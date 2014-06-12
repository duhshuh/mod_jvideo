<?php
/*
 *    @package    JVideo
 *    @subpackage Library
 *    @link http://jvideo.warphd.com
 *    @copyright (C) 2007 - 2010 Warp
 *    @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die("Cannot use outside of Joomla");

require_once dirname(__FILE__) . '/../../infin-lib.php';
require_once dirname(__FILE__) . '/../JVideo_Exception.php';
require_once dirname(__FILE__) . '/../JVideo_ISyncService.php';
require_once dirname(__FILE__) . '/../JVideo_VideoBase.php';
require_once dirname(__FILE__) . '/../Repository/JVideo_JoomlaVideoRepository.php';
require_once dirname(__FILE__) . '/../Repository/JVideo_WarpVideoRepository.php';
require_once dirname(__FILE__) . '/../../jvideo2/Database/JVideo2_DbBatch.php';

class JVideo_JoomlaVideoSyncService implements JVideo_ISyncService
{
    const SYNC_NONE = 0;
    const SYNC_INCR = 1;
    const SYNC_FULL = 2;
    
    private $localVideos = null;
    private $remoteVideos = null;
    private $statusCounter = array('complete' => 0, 'pending' => 0, 'aborted' => 0, 'error' => 0, 'deleted' => 0);

    public function sync($forceFullSync = false)
    {
        $localRepository = new JVideo_JoomlaVideoRepository();
        $remoteRepository = new JVideo_WarpVideoRepository();

        $this->localVideos = $localRepository->getVideos();
        $this->remoteVideos = $this->getVideosBySyncType($this->getSyncType($forceFullSync));

        if (is_null($this->remoteVideos)) return;

        $newVideos = array();

        foreach ($this->remoteVideos as $remoteVideo)
        {
            $remoteVideoClone = $this->getClonedVideo($remoteVideo);
            $localVideo = $this->getVideoByVideoGuid($remoteVideoClone->infinoVideoID);
            $oldStatus = "";
            
            $this->incrementStatusCounter($remoteVideoClone);

            if ("abort" == $remoteVideoClone->status)
            {
                if (!is_null($localVideo)) {
                    $this->setErrorStatusForVideo((int) $localVideo->id);
                }

                continue;
            }
            else if (!is_null($localVideo))
            {
                $oldStatus = $localVideo->status;

                $remoteVideoClone->id = (int) $localVideo->id;
                
                $this->updateVideo($localVideo, $remoteVideoClone);
            }
            else if ("deleted" == $remoteVideoClone->status)
            {
                continue;
            }
            else
            {
                $oldStatus = "inserted";

                $remoteVideoClone->id = $this->insertVideo($remoteVideoClone);
            }

            $this->setVideoUrlIfNotSet($remoteVideoClone);
            $this->addThumbnailsToVideo($remoteVideoClone->id, $remoteVideo->thumbs);

            if ($this->isNewVideoByStatus($oldStatus, $remoteVideoClone->status)) {
                $newVideo = new stdClass();
                $newVideo->user_id = $remoteVideoClone->userID;
                $newVideo->id = $remoteVideoClone->id;
                $newVideo->title = $remoteVideoClone->videoTitle;
                $newVideos[] = $newVideo;
            }
        }

        $this->onAfterSync($newVideos);

        return "<p>Videos synchronized (" . $this->statusCounter['complete'] . " complete "
                                  ."and " . $this->statusCounter['pending'] . " pending)</p>";
    }

    private function getVideosBySyncType($syncType)
    {
        switch ($syncType) {
            case self::SYNC_INCR:
                return $this->getVideosByIncrSync();
            case self::SYNC_FULL:
                return $this->getVideosByFullSync();
            case self::SYNC_NONE:
                return null;
            default:
                throw new Exception("Sync type not implemented");
        }
    }

    private function getVideosByFullSync()
    {
        $this->updateLastFullSyncToNow();
        $this->removeExistingThumbnails();

        return $this->getRemoteVideo()->getVideos();
    }

    private function getVideosByIncrSync()
    {
        $this->updateLastIncrSyncToNow();

        if ($this->getPendingVideosCount() > 0)
            return $this->getRemoteVideo()->getCompletedVideos($this->getLastVideoGuid());
        else
            return null;
    }

    private function updateLastFullSyncToNow()
    {
        $db = JFactory::getDBO();
        $sql = "UPDATE #__jvideo_config "
                ."SET lastFullSync = NOW()";
        $db->setQuery($sql);
        return $db->execute() !== false;
    }

    private function updateLastIncrSyncToNow()
    {
        $db = JFactory::getDBO();
        $sql = "UPDATE #__jvideo_config "
                ."SET lastIncrSync = NOW()";
        $db->setQuery($sql);
        return $db->execute() !== false;
    }

    private function getLastVideoGuid()
    {
        $db = JFactory::getDBO();

        $sql = "SELECT infin_vid_id "
                ."FROM #__jvideo_videos "
                ."WHERE status <> 'pending' "
                ."AND infin_vid_id IS NOT NULL "
                ."AND id < ( "
                ."		SELECT MIN(id) "
                ."		FROM #__jvideo_videos "
                ."		WHERE status = 'pending' "
                ."		AND infin_vid_id IS NOT NULL "
                ."		) "
                ."ORDER BY id DESC "
                ."LIMIT 1";
        $db->setQuery($sql);

        return $db->loadResult();
    }

    private function getPendingVideosCount()
    {
        $db = JFactory::getDBO();

        $sql = "SELECT COUNT(*) "
                ."FROM #__jvideo_videos "
                ."WHERE status = 'pending' "
                ."AND infin_vid_id IS NOT NULL ";
        $db->setQuery($sql);

        return (int) $db->loadResult();
    }

    private function removeExistingThumbnails()
    {
        $db = JFactory::getDBO();
        $sql = "TRUNCATE TABLE #__jvideo_thumbnails;";
        $db->setQuery($sql);
        return $db->execute() !== false;
    }

    private function incrementStatusCounter($video)
    {
        switch ($video->status)
        {
            case 'complete': @$this->statusCounter['complete']++; break;
            case 'pending' : @$this->statusCounter['pending']++; break;
            case 'deleted' : @$this->statusCounter['deleted']++; break;
            case 'error' : @$this->statusCounter['error']++; break;
            case 'abort' : @$this->statusCounter['abort']++; break;
        }
    }
    
    private function getSyncType($override)
    {
        if ($override) {
            return self::SYNC_FULL;
        } else {
            $syncType = $this->getSyncTypeByInterval();

            if ($syncType->doFullSync || $syncType->doIncrSync) {
                if ($syncType->fullSyncInterval > 0 && $syncType->doFullSync) {
                    return self::SYNC_FULL;
                } else {
                    return self::SYNC_INCR;
                }
            } else {
                return self::SYNC_NONE;
            }
        }
    }

    private function getClonedVideo($video)
    {
        $clonedVideo = new JVideo_VideoBase();
        $clonedVideo->id = 0;
        $clonedVideo->infinoVideoID = $video->videoGuid;
        $clonedVideo->videoTitle = $video->title;
        $clonedVideo->videoDescription = $video->description;
        $clonedVideo->tags = $video->tags;
        $clonedVideo->duration = $video->duration == "" ? "0" : $video->duration;
        $clonedVideo->url = $video->url;
        $clonedVideo->dateAdded = JFactory::getDate($video->conversionEndDate);
        $clonedVideo->dateAdded = $clonedVideo->dateAdded->toSql();
        $clonedVideo->status = $this->getStatusByStatusId((int) $video->status);
        $clonedVideo->userID = strlen($video->ownerRef) == 0 ? 62 : (int) $video->ownerRef;
        $clonedVideo->thumbnail = "";

        return $clonedVideo;
    }

    private function getVideoByVideoGuid($videoGuid)
    {
        if (!is_array($this->localVideos)) {
            return null;
        }
        
        reset($this->localVideos);

        foreach ($this->localVideos as $localVideo) {
            if ($localVideo->getInfinoVideoID() == $videoGuid) {
                return $localVideo;
            }
        }

        return null;
    }

    private function getSyncTypeByInterval()
    {
        $db = JFactory::getDBO();

        $sql = "SELECT fullSyncInterval, incrSyncInterval, "
                ."(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(lastFullSync) > (fullSyncInterval * 60 * 60)) AS doFullSync, "
                ."(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(lastIncrSync) > (incrSyncInterval)) as doIncrSync "
                ."FROM `#__jvideo_config` "
                ."WHERE 1";
        $db->setQuery($sql);

        return $db->loadObject();
    }

    private function getStatusByStatusId($statusId)
    {
        switch ($statusId)
        {
            case 1:
                return "abort";
            case 2: case 3:
                return "pending";
            case 4:
                return "complete";
            case 5:
                return "deleted";
            case 6:
            default:
                return "error";
        }
    }

    private function isNewVideoByStatus($oldStatus, $newStatus)
    {
        return $newStatus == "complete" && ($oldStatus == "inserted" || $oldStatus == "pending");
    }

    private function triggerNewVideoNotifications($newVideos)
    {
        if (count($newVideos) > 0) {
            $this->addActivityNotificationForJomSocial($newVideos);
        }
    }

    private function insertVideo(&$video)
    {
        $db = JFactory::getDBO();

        $sql = "INSERT INTO #__jvideo_videos "
                ."(user_id, video_title, video_desc, tags, status, infin_vid_id, duration, date_added) VALUES ("
                .(int) $video->userID.", "
                .$db->quote($video->videoTitle).", "
                .$db->quote($video->videoDescription).", "
                .$db->quote($video->tags).", "
                .$db->quote($video->status).", "
                .$db->quote($video->infinoVideoID).", "
                .$db->quote($video->duration).", "
                .$db->quote($video->dateAdded)
                .")";
        $db->setQuery($sql);

        if ($db->execute() !== false) {
            return (int) $db->insertid();
        } else {
            return false;
        }
    }

    private function updateVideo(&$localVideo, &$tmpVideo)
    {
        if ($localVideo->status == "deleted" && $tmpVideo->status == "deleted") 
            return true;

        $db = JFactory::getDBO();

        $sql = "UPDATE #__jvideo_videos SET "
                .(!isset($localVideo->user_id) || $localVideo->user_id == ""
                    ? "user_id = " . $tmpVideo->userID . ", " : "")
                .(!isset($localVideo->video_title) || $localVideo->video_title == ""
                    ? "video_title = " . $db->quote($tmpVideo->videoTitle) . ", " : "")
                .(!isset($localVideo->video_desc) || $localVideo->video_desc == ""
                    ? "video_desc = " . $db->quote($tmpVideo->videoDescription) . ", " : "")
                .(!isset($localVideo->tags) || $localVideo->tags == ""
                    ? "tags = " . $db->quote($tmpVideo->tags) . ", " : "")
                ."status = " . $db->quote($tmpVideo->status) . ", "
                ."duration = " . $db->quote($tmpVideo->duration) . ", "
                ."date_added = IF(ISNULL(date_added), "
                    .(!isset($localVideo->date_added) || $localVideo->date_added != ""
                        ? $db->quote($tmpVideo->dateAdded) : "CURDATE()")
                    . ", date_added) "
                ."WHERE id = " . $tmpVideo->id . " LIMIT 1";
        $db->setQuery($sql);

        return $db->execute() !== false;

    }

    private function setVideoUrlIfNotSet(&$tmpVideo)
    {
        if ($tmpVideo->url == "" && ($tmpVideo->status == "complete" || $tmpVideo->status == "pending")) {
            $this->getRemoteVideo()->updateVideo(
                    $tmpVideo->infinoVideoID, $tmpVideo->videoTitle, $tmpVideo->videoDescription, $tmpVideo->userID,
                    $tmpVideo->tags, JURI::base() . 'index.php?option=com_jvideo&view=watch&id=' . $tmpVideo->id);
        }
    }

    private function setErrorStatusForVideo($videoId)
    {
        $db = JFactory::getDBO();

        $sql = "UPDATE #__jvideo_videos "
                ."SET status = 'error' "
                ."WHERE id = " . $videoId . " "
                ."AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(transaction_dt) > 86400) "
                ."LIMIT 1";
        $db->setQuery($sql);

        return $db->execute() !== false;
    }

    private function addThumbnailsToVideo($videoId, &$thumbnails)
    {
        $db = JFactory::getDBO();

        if (count($thumbnails) > 0)
        {
            $sql = "INSERT INTO #__jvideo_thumbnails "
                  ." (videoID, imageURL, timeIndex, width, height) "
                  ." VALUES ";

            foreach ($thumbnails as $thumbnail)
            {
                $sql .= "(". (int) $videoId . ", "
                        ." ". $db->quote($thumbnail->url) . ", "
                        ." ". (int)$thumbnail->timeIndex . ", "
                        ." ". (int)$thumbnail->width . ", "
                        ." ". (int)$thumbnail->height ."),";
            }

            $sql = substr($sql, 0, -1) . ';';

            JVideo2_DbBatch::execute($db, $sql);
            //$db->setQuery($sql);
            //return $db->queryBatch($sql) !== false;
        }
        else
        {
            return true;
        }
    }

    private function addActivityNotificationForJomSocial($videos)
    {
        $config = JVideo_Factory::getConfig();

        if ($config->profileSystem == 'JomSocial')
        {
            $jomSocialLibraryPath = JPATH_ROOT.'/components/com_community/libraries/core.php';

            if (file_exists($jomSocialLibraryPath))
            {
                include_once($jomSocialLibraryPath);

                foreach ($videos as $video)
                {
                    $plugin = JPluginHelper::getPlugin('community', 'jvideo');
                    $pluginParams = new JParameter( $plugin->params );
                    $useEmbeddedPlayer = $pluginParams->get('js_embedded_player', 0);
                    $targetItemId = $pluginParams->get('js_target_id', 0);
                    $charLimitTitle = $pluginParams->get('js_char_limit_title', 32);
                    $playerHeight = $pluginParams->get('js_player_height', 300);
                    $playerWidth = $pluginParams->get('js_player_width', 400);

                    if ($useEmbeddedPlayer) {
                        $url = CRoute::_('index.php?option=com_community&view=profile&userid='.$video->user_id.'#app-jvideo');
                    } else {
                        $routeUrl = "index.php?option=com_jvideo&view=watch&id=" . $video->id;

                        if ($targetItemId > 0) {
                            $routeUrl .= "&Itemid=" . $targetItemId;
                        }

                        $url = JRoute::_($routeUrl);
                    }

                    if ($charLimitTitle > 0) {
                        if (strlen($video->title) > $charLimitTitle) {
                            $title = substr($video->title, 0, $charLimitTitle - 1) . "...";
                        } else {
                            $title = $video->title;
                        }
                    } else {
                        $title = $video->title;
                    }

                    $link = "<a href=\"".$url."\">".$title."</a>";

                    $act = new stdClass();
                    $act->cmd 		= 'jvideo.upload';
                    $act->actor 	= $video->user_id;
                    $act->target 	= 0; // no target
                    $act->title 	= JText::_('{actor} uploaded a new video: ') . $link;
                    $act->content 	= '';
                    $act->app 		= 'jvideo';
                    $act->cid 		= 'video_id_' . $video->id;
                    $act->params	= '';

                    CFactory::load('libraries', 'activities');
                    CActivityStream::add($act);
                }
            }
        }
    }

    private function getRemoteVideo()
    {
        $config = JVideo_Factory::getConfig();
        
        $remoteVideo = new InfinovationVideo(
                $config->infinoAccountKey,
                $config->infinoSecretKey);

        $this->proxyControl($remoteVideo);

        return $remoteVideo;
    }

    private function onAfterSync($videos)
    {
        $this->removeDeletedVideos();
        $this->purgeAbandonedVideos();
        $this->triggerNewVideoNotifications($videos);
    }

    private function removeDeletedVideos()
    {
        $db = JFactory::getDBO();

        $sql = "DELETE FROM #__jvideo_videos WHERE status = 'deleted';";

        $db->setQuery($sql);
        return false !== $db->execute();
    }

    private function purgeAbandonedVideos()
    {
        $db = JFactory::getDBO();

        $sql = "DELETE FROM #__jvideo_videos WHERE status = 'waiting for upload' AND "
              ." AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(transaction_dt) > 86400);";
        
        $db->setQuery($sql);
        return false !== $db->execute();
    }

    //@todo: Refactor/remove
	public function proxyControl(InfinovationSoapBase &$base)
	{
        $config = JVideo_Factory::getConfig();

		if ($config->proxyEnabled) {
			$base->enableProxy();
			$base->setProxyParams($config->proxyHost, $config->proxyPort, $config->proxyUsername,
                    $config->proxyPassword, $config->proxyTimeout, $config->proxyResponseTimeout);
		} else {
			$base->disableProxy();
		}
	}
}