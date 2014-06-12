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

jimport('joomla.application.component.model');

jvimport('Video');
jvimport('VideoRepositoryFactory');
jvimport('UserRepositoryFactory');

class JVideoModelWatch extends JModelLegacy
{
    public function getMobileUrls($videoGuid)
    {
        try
        {
            $repository = new JVideo_WarpVideoRepository();

            $mobileUrls = $repository->getVideoMobileUrl($videoGuid);

            return $mobileUrls;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function isMobile()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $pattern = '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i';

        return preg_match($pattern, $userAgent) === 1;
    }

    public function  getVideo($id)
    {
        $repository = JVideo_VideoRepositoryFactory::create();

        $video = $repository->getVideoById($id);

        $thumbnails = $this->getThumbnailsByVideoId($id);

        $video->setThumbnails($thumbnails);

        return $video;
    }

    public function getUser($userId)
    {
        $repository = JVideo_UserRepositoryFactory::create();

        return $repository->getUserById($userId);
    }

    public function addHitImpression($videoId)
    {
        $repository = JVideo_VideoRepositoryFactory::create();

        $video = new JVideo_Video();
        $video->setID($videoId);

        $repository->addHit($video);
    }

    public function getRatingIfUserAlreadyVoted($userID, $videoID)
    {
        $db	= JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();
        $userID = $inputFilter->clean($userID, 'INT');
        $videoID = $inputFilter->clean($videoID, 'INT');
        $sql = "SELECT rating FROM #__jvideo_rating WHERE `v_id` = " . $videoID
                ." AND `user_id` = " . $userID . " LIMIT 1;";
        $db->setQuery($sql);
        $row = $db->loadObject();

        if (count($row) > 0) {
            return $row->rating;
        } else {
            return -1;
        }
    }

    public function getThumbnailsByVideoId($videoId)
    {
        $db	= JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();

        $sql = "SELECT imageURL ";
        $sql .= "FROM #__jvideo_thumbnails ";
        $sql .= "WHERE videoID = " . $inputFilter->clean($videoId, 'INT');
        $sql .= " AND width = '120' AND height = '90' ";
        $sql .= "ORDER BY timeIndex ASC, id ASC";

        $db->setQuery($sql);

        return $db->loadResultArray();
    }
}