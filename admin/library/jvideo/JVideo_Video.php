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

require_once(dirname(__FILE__). '/JVideo_VideoBase.php');
require_once(dirname(__FILE__). '/JVideo_VideoFinder.php');
require_once(dirname(__FILE__). '/JVideo_ThumbnailCacher.php');

class JVideo_Video extends JVideo_VideoBase
{
	public function getVideoURL($targetItemid = "", $returnRawUrl = false) {
		if ($this->getID() != "") {
			$routeUrl = "index.php?option=com_jvideo&view=watch&id=" . $this->getID();
            
            if ($returnRawUrl == false)
            {
		        $inputFilter = JFilterInput::getInstance();
                $targetItemid = $inputFilter->clean($targetItemid, 'INT');
                if ($targetItemid > 0)
                {
                    $routeUrl .= "&Itemid=" . $targetItemid;
                }
                return JRoute::_($routeUrl);
            }
            else
            {
                return JURI::base() . $routeUrl;
            }
		} else {
			return "#";
		}
	}
	
	public function getVideoAbsoluteURL($targetItemid = "") {
		if ($this->getID() != "") {
			$uri = JURI::getInstance();
			$baseUrl = $uri->toString( array('scheme', 'host', 'port'));
			$routeUrl = "index.php?option=com_jvideo&view=watch&id=" . $this->getID();
			if ($targetItemid != "")
			{
				$routeUrl .= "&Itemid=" . $targetItemid;
			}
			return $baseUrl . JRoute::_($routeUrl);
		} else {
			return "#";
		}
	}
	
	public function getThumbURL($cacheEnabled = false, $proxyParams = null, $index = 0) {
		if ($cacheEnabled) {
			return $this->cacheThumbnail($this->getID(),  $this->getThumbnail($index), $this->getThumbnailKey($index), $proxyParams);
		} else {
			return $this->getThumbnail($index);
		}
	}
	
	public function getThumbnails($cacheEnabled = false, $proxyParams = null) {
		if ($cacheEnabled) {
			$parentThumbs = parent::getThumbnails();
			$cachedThumbs = array();
						
			while (list($key, $val) = each($parentThumbs)) {
				$cachedThumbs[] = $this->cacheThumbnail(
					$this->getID()
					, $val
					, $key
					, $proxyParams);
			}
						
			return $cachedThumbs;
		} else {
			return parent::getThumbnails();
		}
	}
	
	public function getEmbedCode($accountKey, $signature, $autoPlay = false, $height = 360, $width = 640, $allowFullScreen = true )
	{
		
		$videoGuid = $this->getInfinoVideoID();
		
		$code = "<object width=\"". (int) $width ."\" height=\"" . (int) $height."\">\n"
				."<param name=\"allowFullScreen\" value=\"".(bool)$allowFullScreen."\" />"
				."<param name=\"src\" value=\"http://manage.warphd.com/assets/player.swf?a=".$accountKey."&v=1\"/>"
				."<param name=\"allowScriptAccess\" value=\"always\" />"
				."<param name=\"flashvars\" value=\"AccountKey=".$accountKey."&VideoGuid=".$videoGuid."&Signature=".$signature."&AutoPlay=".$autoPlay."\" />"
				."<param name=\"wmode\" value=\"transparent\"></param>"
				."<embed src=\"http://manage.warphd.com/assets/player.swf?a=".$accountKey."&v=1\" "
				."type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"".(int)$width."\" height=\"".(int)$height."\" allowFullScreen=\"".(bool)$allowFullScreen."\" "
				."flashVars=\"AccountKey=".$accountKey."&VideoGuid=".$videoGuid."&Signature=".$signature."&AutoPlay=".(bool)$autoPlay."\"></embed>"
				."</object>";
				
		return $code;
	}	
	
	//@todo: Merge both embedding functions into one getEmbedCode function
	public function getEmbedCodeByFlashvars($accountKey, $flashVars, $height = 360, $width = 640, $allowFullScreen = true)
	{
		$videoGuid = $this->getInfinoVideoID();
		
		$code = "<object width=\"". (int) $width ."\" height=\"" . (int) $height."\">\n"
				."<param name=\"allowFullScreen\" value=\"".(bool)$allowFullScreen."\" />"
				."<param name=\"src\" value=\"http://manage.warphd.com/assets/player.swf?a=".$accountKey."&v=1\"/>"
				."<param name=\"allowScriptAccess\" value=\"always\" />"
				."<param name=\"flashvars\" value=\"".$flashVars."\" />"
				."<param name=\"wmode\" value=\"transparent\"></param>"
				."<embed src=\"http://manage.warphd.com/assets/player.swf?a=".$accountKey."&v=1\" "
				."type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"".(int)$width."\" height=\"".(int)$height."\" allowFullScreen=\"".(bool)$allowFullScreen."\" "
				."flashVars=\"".$flashVars."\"></embed>"
				."</object>";
				
		return $code;
	}

    //@todo Ugh... what was I thinking... worst code design ever below. Refactor!
	public function sqlSelectVideo($videoID) {
        $inputFilter = JFilterInput::getInstance();
		$videoID = $inputFilter->clean($videoID, 'INT');
		$where = "v.`id` = ".$videoID." AND v.`infin_vid_id` IS NOT NULL AND v.`user_id` IS NOT NULL";
		$orderBy = "v.`id` LIMIT 1";
		$groupBy = "";
		$categories = null;
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}

    public function sqlSelectActiveVideos($categories = null, $filter = "") {
		$where = "";
		$orderBy = "v.`id` ASC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}

	public function sqlSelectMostWatchedVideos($categories = null, $filter = "") {					
		$where = "";
		$orderBy = "v.`hits` DESC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectFeaturedVideos($categories = null, $filter = "") {	
		$where = "";
		$orderBy = "`feature_rank` DESC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectNewestVideos($categories = null, $filter = "") {
		$where = "";
		$orderBy = "v.`date_added` DESC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectTopRatedVideos($categories = null, $filter = "") {
		$where = "";
		$orderBy = "`rating_avg` DESC, `rating_count` ASC, v.`hits` DESC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectUsersVideos($userID, $filter = "") {
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
		$where = "v.`user_id` = " . $userID;
		$orderBy = "v.`date_added` DESC";
		$groupBy = "";
		$categories = null;
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectMyVideos($userID, $filter = "") {
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
		$where = "v.`status` IN ('complete','pending') AND v.`user_id` = " . $userID;
		$orderBy = "v.`date_added` DESC";
		$groupBy = "";
		$categories = null;
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}
	
	public function sqlSelectAlphabeticalVideos($categories = null, $filter = "") {
		$where = "";
		$orderBy = "v.`video_title` ASC";
		$groupBy = "";
		$this->sqlFilterParams($filter, $where, $orderBy, $groupBy);
		return $this->sqlSelectVideos($categories, $where, $orderBy, $groupBy);
	}

	public function sqlSelectVideos($categories, $where, $orderBy, $groupBy = "") {

		if (trim($where) == "") {
			$where = "true";
		}

		if (trim($groupBy) == "") {
			$groupBy = "v.`id`";
		}

		if (trim($orderBy) == "") {
			$orderBy = "v.`id`";
		}

		if (!is_null($categories)) {
			if (is_array($categories)) {
				$categories = array_map('intval', $categories);
				if (implode(",", $categories) != "" && !in_array(-1, $categories)) {
					$where .= " AND c.`category_id` IN (".implode(",",$categories).") ";
				}
			} else {
				if ((int)$categories > 0) {
					$where .= " AND c.`category_id` = ". (int) $categories ." ";
				}
			}
		}

		$this->m_SQL = ""
				."SELECT v.*, "
                ." u.`username` as username,"
                .' u.`name` as user_fullname,'
				." GROUP_CONCAT(ca.`name`) AS category_name,"
				." rAVG.`rating_avg` AS rating_avg,"
				." rCOUNT.`rating_count` AS rating_count,"
				." f.`feature_rank` AS feature_rank,"
				." f.`id` AS feature_id "
				." FROM #__jvideo_videos v "
				." LEFT JOIN #__users u ON u.`id` = v.`user_id`"
				." LEFT JOIN #__jvideo_videos_categories c ON c.`video_id` = v.`id`"
				." LEFT JOIN #__jvideo_categories ca ON ca.`id` = c.`category_id`"
				." LEFT JOIN ("
				."	SELECT AVG( `rating` ) AS rating_avg, v_id "
				." 	FROM #__jvideo_rating "
				." 	GROUP BY `v_id`"
				." 	) AS rAVG ON rAVG.v_id = v.id"
				." LEFT JOIN ("
				." 	SELECT COUNT( * ) AS rating_count, v_id"
				." 	FROM #__jvideo_rating "
				."	GROUP BY v_id"
				." 	) AS rCOUNT ON rCOUNT.v_id = v.id"
				." LEFT JOIN #__jvideo_featured f ON f.`v_id` = v.`id` "
				." WHERE ".$where." "
				." GROUP BY ".$groupBy." "
				." ORDER BY ".$orderBy;
		return $this->m_SQL;
	}

	public function sqlFilterParams($filter, &$where, &$orderBy, &$groupBy) {
		$jvConfig = JVideo_Factory::getConfig();
		
		switch ($filter) {
			case "featured":
				$groupBy .= (trim($groupBy) != "" ? " AND " : "") 
					. "v.`id` HAVING `feature_id` IS NOT NULL ";
				
				$where .= (trim($where) != "" ? " AND " : "") 
					. "v.`status` = 'complete' "
					." AND ( v.`publish_up` = '0000-00-00 00:00:00' OR v.`publish_up` <= NOW() ) "
					." AND ( v.`publish_down` = '0000-00-00 00:00:00' OR v.`publish_down` >= NOW() ) "
					." AND ( v.`published` = 1 ) ";
					
				if ($jvConfig->requireAdminVidAppr)
				{
					$where .= " AND ( v.`admin_approved` = 1 ) ";
				}
				break;
			case "published":
				$where .= (trim($where) != "" ? " AND " : "") 
					. "v.`status` = 'complete' "
					." AND ( v.`publish_up` = '0000-00-00 00:00:00' OR v.`publish_up` <= NOW() ) "
					." AND ( v.`publish_down` = '0000-00-00 00:00:00' OR v.`publish_down` >= NOW() ) "
					." AND ( v.`published` = 1 ) ";
				
				if ($jvConfig->requireAdminVidAppr)
				{
					$where .= " AND ( v.`admin_approved` = 1 ) ";
				}
				break;
			case "awaitingApproval":
				$where .= (trim($where) != "" ? " AND " : "") . "v.`admin_approved` <> 1"; 
			default: // all except deleted videos; who would want those?
				$where .= (trim($where) != "" ? " AND " : "") . "v.`status` <> 'deleted' AND v.`status` <> 'waiting for upload'";
				break;
		}
	}
	
	
	public function cacheThumbnail($videoID, $thumbnailURL, $thumbnailID, $proxyParams = null)
	{
		try
		{
			$thumbnailCacher = new JVideo_ThumbnailCacher();
			return $thumbnailCacher->cache($videoID, $thumbnailURL, $thumbnailID);
		}
		catch (Exception $ex)
		{ // cache failed... return remote URL
			return $thumbnailURL;
		}
	}

}
