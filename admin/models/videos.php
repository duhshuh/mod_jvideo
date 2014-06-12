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

jvimport('VideoRepositoryFactory');
jvimport('VideoCollection');
jvimport('VideoCategoryRepositoryFactory');

require_once dirname(__FILE__) . '/modelBinders/VideoModelBinder.php';

class JVideoModelVideos extends JModelLegacy
{
    var $_repository = null;
    
    var $_data;
    var $_limit = 0;
    var $_limitstart = 0;
    var $_total = null;
	var $_pagination = null;
	var $_order = "";
	var $_filter = "";
    var $_query = "";
    var $_thumbnails = null;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_repository = JVideo_VideoRepositoryFactory::create();
    }

    public function getVideos($order = '', $categories = null, $filter = '')
    {
    	$this->buildSqlForVideosBy($order, $categories, $filter);
    	$this->setupLimitsForPagination();
    	$this->fillDataWithVideoQueryResults();
    	$this->buildThumbnailCollection();

    	$videoCollection = new JVideo_VideoCollection();
    	
		foreach ($this->_data as &$dataRow)
		{
			$video = JVideo_VideoFactory::create($dataRow);

			$video->setThumbnails($this->getThumbnailsByVideoId($video->getID()));

			$videoCollection->add($video);
		}

		$this->getVideosTotal($order, $categories, $filter);
		$this->getVideosPagination();

		return $videoCollection;
    }
    
    private function buildSqlForVideosBy($order, $categories, $filter)
    {
    	$jvVideo = new JVideo_Video();
    	$videoFinder = new JVideo_VideoFinder();
    	$filter = $videoFinder->findBy($filter);
    	
    	$this->_query = $jvVideo->sqlSelectVideos($categories, $filter, $order);
    }
    
    private function setupLimitsForPagination()
    {
		$mainframe = JFactory::getApplication();
    	$this->_limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->_limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		$this->_limitstart = ( $this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0 );
    }
    
    private function fillDataWithVideoQueryResults()
    {
    	$this->_data = $this->_getList($this->_query, $this->_limitstart, $this->_limit);
    }
    
    private function buildThumbnailCollection()
    {
    	$this->_thumbnails = array();
    	$videoIds = array();
    
		foreach ($this->_data as &$video) {
			$videoIds[] = $video->id;
		}
		
    	reset($this->_data);
    	
		$this->_thumbnails = $this->getThumbnailsFromVideoIdArray($videoIds);
    }
    
    public function getFilterForSearch($title)
    {
		$filter = new stdClass;
    	if ($title != '') {
		    $filter->title = $title;	    
    	}
	    return $filter;
    }
    
	public function getThumbnailsFromVideoIdArray($videoIdArray)
	{
		$db	= JFactory::getDBO();

		if (!is_array($videoIdArray)) {
			$videoIdArray = array($videoIdArray);
		}
		
		$sql = "SELECT id, videoID, imageURL ";
		$sql .= "FROM #__jvideo_thumbnails ";
		$sql .= "WHERE videoID IN (";
		
		foreach ($videoIdArray as $videoId)
		{
			$sql .= (int) $videoId . ",";
		}
		
		$sql .= "-1) AND width = 120 AND height = 90 ";
		$sql .= "ORDER BY timeIndex ASC, id ASC"; 

		$cacheId = "thumbnails_" . implode(",", $videoIdArray);

		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	private function getThumbnailsByVideoId($videoId)
	{
		reset($this->_thumbnails);
		$thumbnailIDs = "";
		$thumbnailURLs = "";
		$thumbnails = array();
			
		foreach ($this->_thumbnails as $videoThumbnail)
		{
			if ($videoThumbnail->videoID == $videoId)
			{
				$thumbnails[$videoThumbnail->id] = $videoThumbnail->imageURL;
			}
		}
		
		return $thumbnails;			
	}
	
	public function getVideosTotal($order = '', $categories = '', $filter = '')
	{
		if (empty($this->_total)) {
			$jvVideo = new JVideo_Video();
    	   	$query = $jvVideo->sqlSelectVideos(null, "status <> 'deleted'", "");
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	
	public function getVideosPagination()
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->_total, $this->_limitstart, $this->_limit );
		}
		return $this->_pagination;
	}
	
	public function getVideo($videoId)
	{
        return $this->_repository->getVideoById($videoId);
	}	
	
	public function save()
    {
    	$modelBinder = new VideoModelBinder($this->_repository);
    	$video = $modelBinder->bindModel();

        $videoCategories = JRequest::getVar('videoCategory');

        $this->_repository->update($video);

        if ($video->status == 'waiting for upload')
        {
            $this->setVideoStatusToPending($video->infinoVideoID);
        }
        
        if ((int)JRequest::getVar('featured'))
            $this->_repository->feature($video);
        else
            $this->_repository->unfeature($video);

        $this->updateVideoCategories($video, $videoCategories);

        return true;
	}

    public function featureVideo($videoId)
	{
		$this->_repository->feature(
            $this->_repository->getVideoById($videoId)
        );
	}

    public function unfeatureVideo($videoId)
    {
        $this->_repository->unfeature(
            $this->_repository->getVideoById($videoId)
        );
    }

	public function removeFeaturedById($videoId)
	{
        $this->_repository->unfeature(
            $this->_repository->getVideoById($videoId)
        );
	}

	public function removeFeaturedByGuid($videoGuid)
	{
		$this->_repository->unfeature(
            $this->_repository->getVideoByGuid($videoGuid)
        );
	}
	
	public function addFeaturedById($videoId)
	{
        $this->_repository->feature(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function addFeaturedByGuid($videoGuid)
	{
        $this->_repository->feature(
            $this->_repository->getVideoByGuid($videoGuid)
        );
	}
	
	public function addNewVideo()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$title = JRequest::getVar('title');
		$desc = JRequest::getVar('desc');
		$user_id = $user->id;
		$videoGuid = JRequest::getVar('videoGuid');
		$debug = false;
		 
		if (!$videoGuid) return false;
		if ($title == "" || $user_id == "") return false;

        if (JRequest::getVar('dateAdded') != "") {
            $dateAdded = JFactory::getDate(JRequest::getVar('dateAdded'));
            $dateAdded = $dateAdded->toSql();
        } else {
            $dateAdded = "";
        }

        $inputFilter = JFilterInput::getInstance();
		$authorID = $inputFilter->clean(JRequest::getVar('authorID'), 'INT');
		$published = JRequest::getVar('published');
		$blankDate = "0000-00-00 00:00:00";
		$publishUp = JRequest::getVar('publishUp');
		$publishDown = JRequest::getVar('publishDown');
		$publishUp = (trim($publishUp) == $blankDate || trim($publishUp) == "") ? $blankDate : $publishUp;
		$publishDown = (trim($publishDown) == $blankDate || trim($publishDown) == "") ? $blankDate : $publishDown;
		
		if (method_exists('JFactory', 'getDate'))
		{
			if ($publishUp != $blankDate)
			{
				$publishUp = JFactory::getDate($publishUp);
				$publishUp = $publishUp->toSql();
			}
			
			if ($publishDown != $blankDate)
			{
				$publishDown = JFactory::getDate($publishDown);
				$publishDown = $publishDown->toSql();
			}
		}
					
		$tags = JRequest::getVar('tags');
		$allowed = "/[^A-Za-z0-9\\040]/i"; //alphanumeric + space
		$tags = strtolower(preg_replace($allowed,"",$tags));
		
		$sql = "UPDATE #__jvideo_videos "
			."SET "
			."video_title = ".$db->Quote($title).", "
			."video_desc = ".$db->Quote($desc).", "
			."tags = ".$db->Quote($tags).", "
			.($dateAdded == "" ? "date_added=CURDATE(), " : "date_added = " . $db->quote($dateAdded) . ", ")
			."`user_id` = ".(int)$authorID.", "
			."`published` = ".(int)$published.", "
			."`publish_up` = ".$db->Quote($publishUp).", "
			."`publish_down` = ".$db->Quote($publishDown).", "
            ."`status` = 'pending' "
			."WHERE infin_vid_id = ".$db->Quote($videoGuid)." "
			."LIMIT 1";
		
		$db->setQuery( $sql );

		if ($db->execute())
			return true;
		else
			return false;
	}
	
	public function publishVideo($videoId)
	{
        $this->_repository->publish(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function unpublishVideo($videoId)
	{
        $this->_repository->unpublish(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function approveVideo($videoId)
	{
		$this->_repository->approve(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function unapproveVideo($videoId)
	{
		$this->_repository->unapprove(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function deleteVideo($videoId)
	{
        $this->_repository->delete(
            $this->_repository->getVideoById($videoId)
        );
	}
	
	public function getNewVideoGuid()
	{
		return $this->_repository->getNewVideoGuid();
	}
	
	public function getFlashVarsForUploader($userId, $videoGuid)
	{		
		$uri = JRoute::_("index.php?option=com_jvideo"
						."&view=videos"
						."&task=add_step2"
						."&userId=" . ($userId == "" ? "62" : $userId)
						."&videoGuid=" . $videoGuid);

        return $this->_repository->getFlashVarsForUploader($videoGuid, $uri);
	}

	public function insertVideoSkeleton($videoGuid, $userId)
	{
    	return $this->_repository->insertStub($videoGuid, $userId);
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
        reset($videoCategories);
        reset($categoryIds);

        foreach ($categoryIds as $categoryId) {
            foreach ($videoCategories as $videoCategory) {
                if ($categoryId == $videoCategory->categoryId) {
                    continue 2;
                }
            }

            $newVideoCategory = JVideo_VideoCategoryFactory::create($video->id, $categoryId, "", "", "", "",
                    $video->infinoVideoID);
            $videoCategoryRepo->add($newVideoCategory);
            reset($videoCategories);
        }
    }

    private function removeOldVideoCategories(&$video, &$categoryIds, &$videoCategories)
    {
        $videoCategoryRepo = JVideo_VideoCategoryRepositoryFactory::create();

        reset($videoCategories);
        reset($categoryIds);

        foreach ($videoCategories as $videoCategory) {
            foreach ($categoryIds as $categoryId) {
                if ($categoryId == $videoCategory->categoryId) {
                    continue 2;
                }
            }

            $videoCategoryRepo->remove($videoCategory);
            reset($categoryIds);
        }
    }

    public function setVideoStatusToPending($videoGuid)
    {
        $db = JFactory::getDBO();

    	$sql = "UPDATE #__jvideo_videos "
              ."SET status = 'pending' WHERE infin_vid_id = " . $db->quote($videoGuid) . " LIMIT 1";
		$db->setQuery($sql);

        return $db->execute();
    }
}
