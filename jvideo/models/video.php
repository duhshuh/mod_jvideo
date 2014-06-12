<?php
/*
 *    @package    JVideo
 *    @subpackage Components
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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

jvimport('UserAllowedToModerateSpecification');
jvimport('VideoRepositoryFactory');
jvimport('VideoCategoryRepositoryFactory');

class JVideoModelVideo extends JModelLegacy
{
    var $_config = null;
    var $_repository = null;

	public function __construct()
    {
    	parent::__construct();

        $this->_config = JVideo_Factory::getConfig();
        $this->_repository = JVideo_VideoRepositoryFactory::create();
	}

    public function save()
    {
        $user = JFactory::getUser();
        $videoGuid = JRequest::getVar('videoGuid');

        if (!$videoGuid) return false;

        $video = $this->_repository->getVideoByGuid($videoGuid);
            
        if ($this->isAuthenticated($user, $video))
        {
            if (is_null($video))
                throw new Exception("Invalid video ID. Changes could not be saved!");

            $video->setVideoTitle(JRequest::getVar('title'));
            $video->setVideoDescription(JRequest::getVar('description'));
            $video->setTags($this->getCleanTags(JRequest::getVar('tags')));

            if ($this->isModerator($user))
            {
                $inputFilter = JFilterInput::getInstance();
                $video->setUserID($inputFilter->clean(JRequest::getVar('authorId'), 'INT'));
                $video->setPublished(JRequest::getVar('published'));
                $video->setDateAdded($this->getMySQLFormattedDate(JRequest::getVar('dateAdded')));
                $video->setPublishUp($this->getMySQLFormattedDate(JRequest::getVar('publishUp')));
                $video->setPublishDown($this->getMySQLFormattedDate(JRequest::getVar('publishDown')));

                $featured = JRequest::getInt('featured');

                if ($featured && !$video->isFeatured()) {
                    $this->_repository->feature($video);
                } else if (!$featured && $video->isFeatured()) {
                    $this->_repository->unfeature($video);
                }
            }

            $this->_repository->update($video);

            $videoCategories = JRequest::getVar('videoCategory');
            $this->updateVideoCategories($video, $videoCategories);

            return true;
        }
        else
        {
            $this->raiseErrorAccessDenied();
            return false;
        }
    }

	public function get_video_categories()
	{
		$model = $this->getModel( 'categories' );
		
		return $model->get_jvideo_categories();
	}	
	
	public function getVideoIdByVideoGuid($videoGuid)
	{
		$db	= JFactory::getDBO();
		
		$sql = "SELECT id FROM #__jvideo_videos "
			  ."WHERE infin_vid_id = " . $db->quote($videoGuid) . " "
			  ."LIMIT 1;";
	
		$db->setQuery($sql);
		
		return (int) $db->loadResult();
	}

    private function isAuthenticated(&$user, &$video)
    {
        if ($this->isModerator($user))
            return true;
        else if ($this->isOwner($user, $video))
            return true;
        else
            return false;
    }

    private function isModerator($user)
    {
        $spec = new JVideo_UserAllowedToModerateSpecification();
        return $spec->isSatisfiedBy($user);
    }

    private function isOwner(&$user, &$video)
    {
        return !is_null($video) && $user->id == $video->userID;
    }

    private function getCleanTags($tags)
    {
        $validInput = "/[^A-Za-z0-9\\040]/i";
        
        return strtolower(preg_replace($validInput, "", addslashes($tags)));
    }

    private function getMySQLFormattedDate($publishDate)
    {
        $blankDate = "0000-00-00 00:00:00";
        $publishDate = (trim($publishDate) == $blankDate || trim($publishDate) == "") ? $blankDate : $publishDate;

        if ($publishDate != $blankDate) {
            $publishDate = JFactory::getDate($publishDate);
            $publishDate = $publishDate->toSql();
        }

        return $publishDate;
    }

    private function updateVideoCategories($video, $categoryIds)
    {
        if (is_null($video) || is_null($categoryIds))
            return;

        $videoCategories = $this->getVideoCategoriesByVideoId($video->id);
        
        $this->addNewVideoCategories($video, $categoryIds, $videoCategories);
        $this->removeOldVideoCategories($video, $categoryIds, $videoCategories);
    }

    private function getVideoCategoriesByVideoId($videoId)
    {
        $videoCategoryRepo = JVideo_VideoCategoryRepositoryFactory::create();

        $videoCategories = $videoCategoryRepo->getVideoCategoriesByVideoId($videoId);

        if (is_null($videoCategories))
            $videoCategories = array();

        return $videoCategories;
    }

    private function addNewVideoCategories(&$video, &$categoryIds, &$videoCategories)
    {
        $videoCategoryRepo = JVideo_VideoCategoryRepositoryFactory::create();
        reset($categoryIds);

        foreach ($categoryIds as $categoryId) {
            reset($videoCategories);

            foreach ($videoCategories as $videoCategory) {
                if ($categoryId == $videoCategory->categoryId) {
                    continue 2;
                }
            }

            $newVideoCategory = JVideo_VideoCategoryFactory::create($video->id, $categoryId, "", "", "", "",
                    $video->infinoVideoID);
            $videoCategoryRepo->add($newVideoCategory);
        }
    }

    private function removeOldVideoCategories(&$video, &$categoryIds, &$videoCategories)
    {
        $videoCategoryRepo = JVideo_VideoCategoryRepositoryFactory::create();
        reset($videoCategories);

        foreach ($videoCategories as $videoCategory) {
            reset($categoryIds);

            foreach ($categoryIds as $categoryId) {
                if ($categoryId == $videoCategory->categoryId) {
                    continue 2;
                }
            }

            $videoCategoryRepo->remove($videoCategory);
        }
    }


    private function raiseErrorAccessDenied()
    {
        JError::raiseError('403', 'Access denied');
    }

    public function getNewVideoGuid()
    {
        $repository = JVideo_VideoRepositoryFactory::create();

        return $repository->getNewVideoGuid();
    }

    public function getFlashVarsForUploader($newVideoGuid)
    {
        $user = JFactory::getUser();
        $repository = JVideo_VideoRepositoryFactory::create();

        $uri = JRoute::_("index.php?option=com_jvideo&view=upload"
                        ."&task=add"
                        ."&uid=" . ($user->id == "" ? "62" : $user->id)
                        ."&videoGuid=" . $newVideoGuid);

        return $repository->getFlashVarsForUploader($newVideoGuid, $uri);
    }

    public function getFlashVarsByVideoGuid($videoGuid)
    {
     	$infinovationVideo = new InfinovationVideo(
            $this->_config->infinoAccountKey,
            $this->_config->infinoSecretKey);
        
		return $infinovationVideo->getPlayerFlashVars($videoGuid, false);
    }

    public function addVideoStub($videoGuid, $userId)
    {
    	$db	= JFactory::getDBO();

    	$sql = "SELECT id FROM #__jvideo_videos "
    		  ."WHERE infin_vid_id = " . $db->quote($videoGuid);
		$db->setQuery($sql);

		$videoID = (int)$db->loadResult();

		if (!$videoID)
		{
	    	$sql = "INSERT INTO #__jvideo_videos (user_id, infin_vid_id, date_added, video_title) "
                  ."VALUES (" . (int) $userId . ", " . $db->Quote($videoGuid) . ", NOW(), '" . JText::_('JV_UNTITLED') . "')";

			$db->setQuery($sql);
			$db->query($sql);
		}
    }

    public function getVideoStubId($videoGuid)
    {
        $db	= JFactory::getDBO();

    	$sql = "SELECT id FROM #__jvideo_videos "
    		  ."WHERE infin_vid_id = " . $db->quote($videoGuid);
		$db->setQuery($sql);

		return (int) $db->loadResult();
    }

    public function setVideoStatusToPending($videoGuid)
    {
        $db	= JFactory::getDBO();

    	$sql = "UPDATE #__jvideo_videos "
              ."SET status = 'pending' WHERE infin_vid_id = " . $db->quote($videoGuid) . " LIMIT 1";
		$db->setQuery($sql);

        return $db->execute();
    }
}
