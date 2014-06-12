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

require_once(dirname(__FILE__). '/JVideo_DTO.php');
require_once(dirname(__FILE__). '/JVideo_DataAdapter.php');
require_once(dirname(__FILE__). '/JVideo_VideoBase.php');

class JVideo_VideoDataAdapter extends JVideo_DataAdapter
{
	/**
	 * @param JVideo_VideoBase $video
	 */
	public function fill(JVideo_DTO $video, $data)
	{
		$data = self::translateDataFields($data);
		
		parent::fill($video, $data);
	}
	
	protected function lookupPropertyByField($field)
	{
		switch ($field)
		{
			case "user_id": 		return "userID";
			case "video_title":		return "videoTitle";
			case "video_desc": 		return "videoDescription";
			case "category_name": 	return "categoryName";
			case "date_added": 		return "dateAdded";
			case "rating_avg": 		return "ratingAvg";
			case "rating_count": 	return "ratingCount";
			case "publish_up": 		return "publishUp";
			case "publish_down": 	return "publishDown";
			case "admin_approved": 	return "adminApproved";
			case "transaction_dt":	return "transactionDT";
			case "infin_vid_id":	return "infinoVideoID";
            case "feature_id":      return "isFeatured";
			default:			
				return $field;
		}		
	}

}