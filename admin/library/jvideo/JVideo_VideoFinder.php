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

require_once(dirname(__FILE__) . '/JVideo_IFinder.php');

class JVideo_VideoFinder implements JVideo_IFinder
{
	public function findBy( &$params )
	{
		$result = '';

		if (!is_null($params)) {
			foreach ( $params as $key => $val ) {
				if ($result != '') {
					$result .= ' AND ';
				}
				
				$result .= $this->getSqlByParam($key, $val) . ' ';
			}
		}
		
		return $result;
	}
	
	private function getSqlByParam($key, $val)
	{
		switch ($key) 
		{
			case "title":
				return "video_title LIKE '%" . $val . "%'";
			case "description":
				return "video_desc LIKE '%" . $val . "%'";
			case "tags":
				return "tags LIKE '%" . $val . "%'";
			case "exclude_deleted":
				return "status <> 'deleted'";
            case "exclude_waiting_for_upload":
                return "status <> 'waiting for upload'";
			default:
				return $key . " = " . $val;
		}
	}
}