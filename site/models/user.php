<?php
/*
 *	@package	JVideo
 *	@subpackage Components
 *	@link http://jvideo.warphd.com
 *	@copyright (C) 2007 - 2010 Warp
 *	@license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

jvimport('Video');

class JVideoModelUser  extends JModelLegacy
{
	var $_total = null;
	var $_pagination = null;
	var $_order = '';
	var $_filter = '';
	var $_customlimit = 0;
	var $_categories = -1;
	var $_user_id = '';
	
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$this->_order = JRequest::getVar('video_order');
		$this->_filter = JRequest::getVar('video_filter');
		$this->_customlimit = JRequest::getVar('videos_per_page');
		$this->_categories = JRequest::getVar('video_categories');
		
		if ($this->_customlimit != 0) {
			$limit = $this->_customlimit;
			$limitstart = JRequest::getVar('limitstart');
		} else {
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', JRequest::getVar('limitstart'), 'int');
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		}
		
		if ($this->_user_id == '')
		{
			$user = JFactory::getUser();
			if ($user->guest == 0)
			{
				$this->_user_id = $user->id;
			}
		}
		
		JRequest::setVar('limit', $limit);
		JRequest::setVar('limitstart', $limitstart);
	}

	public function getUserVideos($userId, $order, $categories, $filter, $limitstart, $limit)
	{
		$cache = JFactory::getCache('com_jvideo');
		$this->_user_id = $userId;
		
		// if data hasn't already been obtained, load it
		if (empty($this->_data)) {
			
			$query = $this->getUserVideosSql($order, $categories, $filter);
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
					$jvItem->setHits($item->hits);
					$jvItem->setCategoryName($item->category_name);
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
	
	public function getUserVideosTotal($userId, $order, $categories, $filter)
	{
		if (empty($this->_total)) {
			$query = $this->getUserVideosSql($order, $categories, $filter);
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	
	public function getUserVideosPagination($userId, $total, $limitstart, $limit)
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($total, $limitstart, $limit );
		}
		return $this->_pagination;
	}

	public function getUserVideosSql($order, $categories, $filter)
	{
		$user = JFactory::getUser();
		$jvVideo = new JVideo_Video();
			
		if (($this->_user_id == $user->id) || ($this->_user_id == ""))
		{
			return $jvVideo->sqlSelectMyVideos($user->id);
		}
		else
		{
			return $jvVideo->sqlSelectUsersVideos($this->_user_id);	
		}
	}	
	
	
	public function getUserProfile($user_id)
	{
		if ($user_id != '')
		{
			$db = JFactory::getDBO();
			$inputFilter = JFilterInput::getInstance();
			$user_id = $inputFilter->clean($user_id, 'INT');
			$profile = new JVideo_Profile($user_id);
			return $profile;
		}
		else
		{
			return false;
		}
	}
	
	public function saveUserProfile($user_id)
	{
		if ($user_id)
		{
			$user = JFactory::getUser();
			
			if ($user_id != $user->id)
			{
				return 1;
			}
			
			$db = JFactory::getDBO();
			$inputFilter = JFilterInput::getInstance();
			
			$user_id = $inputFilter->clean($user_id, 'INT');

			$fieldsToUpdate = array(
					'`display_name`' => JRequest::getVar('jvideo_form_display_name'),
					'`birthdate`' => $this->buildBirthdateSqlValue(),
					'`location`' => JRequest::getVar('jvideo_form_location'),
					'`description`' => JRequest::getVar('jvideo_form_description'),
					'`occupation`' => JRequest::getVar('jvideo_form_occupation'),
					'`interests`' => JRequest::getVar('jvideo_form_interests'),
					'`website`' => JRequest::getVar('jvideo_form_website')
				);

			$avatar = JRequest::getVar('jvideo_form_avatar_name');
			if (isset($avatar))
			{
				$fieldsToUpdate['avatar'] = $avatar;
			}
			elseif (JRequest::getVar('jvideo_remove_image') === 'on')
			{
				$fieldsToUpdate['avatar'] = null;
			}

			$db->setQuery($this->getProfileSaveSql($user_id, $fieldsToUpdate));
			$db->execute();
						
			return 0;
		}
	}

	private function getProfileSaveSql($userId, $fieldsToUpdate)
	{
		$db = JFactory::getDBO();

		if ($this->doesProfileExist($userId))
		{
			$fieldPairs = array();
			foreach ($fieldsToUpdate as $key => $value)
			{
				$fieldPairs[] = $key . ' = ' . $this->dbQuote($db, $value);
			}

			return 'update #__jvideo_users set ' . implode(',', $fieldPairs) . ' where `user_id` = ' . (int)$userId;
		}
		else
		{
			$fieldNames = array('`user_id`');
			$fieldValues = array((int)$userId);
			foreach ($fieldsToUpdate as $key => $value)
			{
				$fieldNames[] = $key;
				$fieldValues[] = $this->dbQuote($db, $value);
			}

			return 'insert into #__jvideo_users (' . implode(',', $fieldNames) . ') values (' . implode(',', $fieldValues) . ')';
		}
	}

	private function dbQuote($db, $value)
	{
		if ($value == null || strlen($value) <= 0)
		{
			return 'NULL';
		}

		return $db->quote($value);
	}

	private function doesProfileExist($userId)
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT COUNT(*) FROM #__jvideo_users WHERE `user_id` = ' . (int)$userId;
		$db->setQuery($sql);
		$obj = $db->loadResult();
		return !!$obj;
	}

	private function buildBirthdateSqlValue()
	{
		$year = intval(JRequest::getVar('jvideo_form_birth_year'));
		$month = intval(JRequest::getVar('jvideo_form_birth_month'));
		$day = intval(JRequest::getVar('jvideo_form_birth_day'));

		if ($year > 1900 && $month >= 1 && $month <= 12 && $day >= 1 && $day <= 31)
		{
			return $year . '-' . $month . '-' . $day;
		}

		return null;
	}
	
	function getUsername($user_id)
	{		
		$db = JFactory::getDBO();
		$sql = "SELECT `username` FROM #__users WHERE `id` = " . (int) $user_id . " LIMIT 1";
		$db->setQuery($sql);
		$row = $db->loadObjectList();
		if (count($row) == 1)
		{
			return $row[0]->username;
		}
		else
		{
			return false;
		}
	}
	
   	function getThumbnailsFromVideoIdArray($videoIdArray)
	{
		$db	= JFactory::getDBO();
		$sql = "SELECT id, videoID, imageURL ";
		$sql .= "FROM #__jvideo_thumbnails ";
		$sql .= "WHERE videoID IN (";
		
		foreach ($videoIdArray as $videoId)
		{
			$sql .= (int) $videoId . ",";
		}
		
		$sql .= "-1) AND width = 120 AND height = 90 ";
		$sql .= "ORDER BY timeIndex ASC, id ASC"; 
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
}
?>
