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

require_once(dirname(__FILE__).'/JVideo_ICollection.php');

class JVideo_VideoCollection implements JVideo_ICollection
{
	private $videoArray;

	public function __construct()
	{
		$this->videoArray = array();
	}
	
	public function add($video)
	{
		try {
			$this->videoArray[] = $video;
		} catch (Exception $ex) {
			throw new JVideo_Exception($ex->getMessage(), $ex->getCode());
		}
	}
	
	public function remove($video)
	{
		try {
			foreach ($this->videoArray as &$videoArrayItem) {
				if ( $videoArrayItem == $video ) {
					unset($videoArrayItem);
					break;
				}
			}
		} catch (Exception $ex) {
			throw new JVideo_Exception($ex->getMessage(), $ex->getCode());
		}
	}
	
	public function getCollection()
	{
		try {
			return $this->videoArray;
		} catch (Exception $ex) {
			throw new JVideo_Exception($ex->getMessage(), $ex->getCode());
		}
		
	}
	
	public function count() {
		try {
			return count($this->videoArray);
		} catch (Exception $ex) {
			throw new JVideo_Exception($ex->getMessage(), $ex->getCode());
		}
	}
}
