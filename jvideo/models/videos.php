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

class JVideoModelVideos extends JModelLegacy
{
	private $_total = null;
	private $_pagination = null;

	public function getVideos($order, $categories, $filter, $limitstart, $limit, $userId = 0)
	{
		$cache = JFactory::getCache('com_jvideo');
		
		// if data hasn't already been obtained, load it
		if (empty($this->_data)) {
			$query = $this->getVideosSql($order, $categories, $filter, $userId);
			
			$this->_data = $this->_getList($query, $limitstart, $limit);
	
			if (!is_null($this->_data))
			{
				// compile list of IDs for thumbnail lookup
				$videoIdArray = array();
				foreach ($this->_data as &$item)
				{
					$videoIdArray[] = $item->id;
				}
				$videoThumbnailArray = $cache->call(array($this, 'getThumbnailsFromVideoIdArray')
					, $videoIdArray, implode(',', $videoIdArray));
				
				reset($this->_data);
				
				foreach ($this->_data as &$item)
				{
					$jvItem = new JVideo_Video();
					$jvItem->setID($item->id);
					$jvItem->setUserID($item->user_id);
					$jvItem->setUsername($item->username);
					$jvItem->setVideoTitle($item->video_title);
					$jvItem->setVideoDescription($item->video_desc);
					$jvItem->setCategoryName($item->category_name);
					$jvItem->setHits($item->hits);
					$jvItem->setStatus($item->status);
					$jvItem->setDuration($item->duration);
					$jvItem->setTags($item->tags);
					$jvItem->setDateAdded($item->date_added);
					$jvItem->setRatingAvg($item->rating_avg);
					$jvItem->setRatingCount($item->rating_count);
					$jvItem->setPublished($item->published);
					$jvItem->setPublishUp($item->publish_up);
					$jvItem->setPublishDown($item->publish_down);
					$jvItem->setAdminApproved($item->admin_approved);

					reset($videoThumbnailArray);
					$thumbnails = array();
					
					foreach ($videoThumbnailArray as $videoThumbnail)
					{
						if ($videoThumbnail->videoID == $item->id)
						{
							$thumbnails[$videoThumbnail->id] = $videoThumbnail->imageURL;
						}
					}
					$jvItem->setThumbnails($thumbnails);
					
					$item = $jvItem;
				}
			}
		}
		return $this->_data;
	}
	
	public function getVideosTotal($order, $categories, $filter, $userId = 0)
	{
		if (empty($this->_total)) {
			$query = $this->getVideosSql($order, $categories, $filter, $userId);
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	
	public function getVideosPagination($total, $limitstart, $limit)
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($total, $limitstart, $limit );
		}
		return $this->_pagination;
	}

	public function getVideosSql($order, $categories, $filter, $userId = 0)
	{
		$jvVideo = new JVideo_Video();

		if ( !$userId )
		{
			switch ($order)
			{
				case "mostwatchedvideos":
					return $jvVideo->sqlSelectMostWatchedVideos($categories, $filter);
				case "toprated":
					return $jvVideo->sqlSelectTopRatedVideos($categories, $filter);
				case "featuredvideos":
					return $jvVideo->sqlSelectFeaturedVideos($categories, $filter);
				case "alphabetical":
					return $jvVideo->sqlSelectAlphabeticalVideos($categories, $filter);
				case "newestvideos":
				default:
					return $jvVideo->sqlSelectNewestVideos($categories, $filter);
			}
		}
		else
		{
			return $jvVideo->sqlSelectUsersVideos($userId);
		}
	}	
	

	public function getThumbnailsFromVideoIdArray($videoIdArray)
	{
		$db	= JFactory::getDBO();
		$sql = "SELECT id, videoID, imageURL ";
		$sql .= "FROM #__jvideo_thumbnails ";
		$sql .= "WHERE videoID IN (";
		
		foreach ($videoIdArray as $videoId)
		{
			$sql .= (int) $videoId . ",";
		}
		
		$sql .= "-1) AND width = '120' AND height = '90' ";
		$sql .= "ORDER BY timeIndex ASC, id ASC"; 
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
}

?>
