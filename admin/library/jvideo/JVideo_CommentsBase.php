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

require_once(dirname(__FILE__).'/JVideo_DTO.php');

class JVideo_CommentsBase implements JVideo_DTO
{
	private $id;
	private $videoID;
	private $userID;
	private $date;
	private $notify;
	private $title;
	private $comment;
	private $published;
	private $parentID;
	
	public function __construct($id = -1, $videoID = -1, $userID = -1
	, $date = "", $notify = -1, $title = "", $comment = "", $published = -1
	, $parentID = -1)
	{
		$this->setID($id);
		$this->setVideoID($videoID);
		$this->setUserID($userID);
		$this->setDate($date);
		$this->setNotify($date);
		$this->setTitle($title);
		$this->setComment($comment);
		$this->setPublished($published);
		$this->setParentID($parentID);
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function setID($id) {
		$this->id = $id;
	}
	
	public function getVideoID() {
		return $this->videoID;
	}
	
	public function setVideoID($videoID) {
		$this->videoID = $videoID;
	}
	
	public function getUserID() {
		return $this->userID;
	}
	
	public function setUserID($userID) {
		$this->userID = $userID;
	}
	
	public function getNotify() {
		return $this->notify;
	}
	
	public function setNotify($notify) {
		$this->notify = $notify;
	}
	
	public function getDate($date) {
		return $this->date;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getComment() {
		return $this->comment;
	}
	
	public function setComment($comment) {
		$this->comment = $comment;
	}
	
	public function getPublished() {
		return $this->published;
	}
	
	public function setPublished($published) {
		$this->published = $published;
	}
	
	public function getParentID() {
		return $this->parentID;
	}
	
	public function setParentID($parentID) {
		$this->parentID = $parentID;
	}
	
}

?>