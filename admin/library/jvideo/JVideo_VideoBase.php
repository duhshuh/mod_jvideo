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

class JVideo_VideoBase implements JVideo_DTO
{
	public $id;
	public $userID;
	public $username;
	public $user_fullname;
	public $videoTitle;
	public $videoDescription;
	public $categoryName;
	public $tags;
	public $status;
	public $transactionDT;
	public $hits;
	public $infinoVideoID;
	public $duration;
	public $dateAdded;
	public $ratingAvg;
	public $ratingCount;
	public $published;
	public $publishUp;
	public $publishDown;
	public $adminApproved;
	public $thumbnails;
	public $url;
	public $isFeatured;
    public $publisher;

	public function __construct($id = -1, $userID = -1, $username = "", $videoTitle = "", $videoDescription = ""
	, $categoryID = -1, $categoryName = "", $tags = "", $status = "", $transactionDT = "", $hits = -1
	, $infinoVideoID = "", $duration = "", $dateAdded = "", $ratingAvg = -1, $ratingCount = -1, $published = 1
	, $publishUp = '0000-00-00 00:00:00', $publishDown = '0000-00-00 00:00:00', $adminApproved = -1, $thumbnails = array(), $url = "")
	{
		$this->setID($id);
		$this->setUserID($userID);
		$this->setUsername($username);
		$this->setVideoTitle($videoTitle);
		$this->setVideoDescription($videoDescription);
		$this->setCategoryName($categoryName);
		$this->setTags($tags);
		$this->setStatus($status);
		$this->setTransactionDT($transactionDT);
		$this->setHits($hits);
		$this->setInfinoVideoID($infinoVideoID);
		$this->setDuration($duration);
		$this->setDateAdded($dateAdded);
		$this->setRatingAvg($ratingAvg);
		$this->setRatingCount($ratingCount);
		$this->setPublished($published);
		$this->setPublishUp($publishUp);
		$this->setPublishDown($publishDown);
		$this->setAdminApproved($adminApproved);
		$this->setThumbnails($thumbnails);
		$this->setUrl($url);
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function setID($id) {
        $inputFilter = JFilterInput::getInstance();
		$id = $inputFilter->clean($id, 'INT');
		$this->id = $id;
	}
	
	public function getUserID() {
		return $this->userID;
	}
	
	public function setUserID($userID) {
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
		$this->userID = $userID;
	}
	
	public function getUsername() {
		return $this->username;
	}

	public function getUserFullName() {
		return $this->user_fullname;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	public function getVideoTitle() {
		return $this->videoTitle;
	}
	
	public function setVideoTitle($videoTitle) {
		$this->videoTitle = $videoTitle;
	}
	
	public function getVideoDescription() {
		return $this->videoDescription;
	}
	
	public function setVideoDescription($videoDescription) {
		$this->videoDescription = $videoDescription;
	}
	
	public function getCategoryName() {
		return $this->categoryName;
	}
	
	public function setCategoryName($categoryName) {
		if ($categoryName != "") {
			$this->categoryName = $categoryName;
		} else {
			$this->categoryName = "None";
		}
	}
			
	public function getTags() {
		return $this->tags;
	}
	
	public function setTags($tags) {
		$this->tags = $tags;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function getTransactionDT() {
		return $this->transactionDT;
	}
	
	public function setTransactionDT($transactionDT) {
		$this->transactionDT = $transactionDT;
	}
	
	public function getHits() {
		return $this->hits;
	}
	
	public function setHits($hits) {
        $inputFilter = JFilterInput::getInstance();
		$hits = $inputFilter->clean($hits, 'INT');
		$this->hits = $hits;
	}
	
	public function getInfinoVideoID() {
		return $this->infinoVideoID;
	}
	
	public function setInfinoVideoID($infinoVideoID) {
		$this->infinoVideoID = $infinoVideoID;
	}
	
	public function getDuration() {
		return $this->duration;
	}
	
	public function setDuration($duration) {
		$this->duration = $duration;
	}
	
	public function getDateAdded() {
		if ($this->dateAdded === '0000-00-00 00:00:00') return null;
		
		return $this->dateAdded; 
	}
	
	public function setDateAdded($dateAdded) {
		$this->dateAdded = $dateAdded;
	}
	
	public function getRatingAvg() {
		return $this->ratingAvg;
	}
	
	public function setRatingAvg($ratingAvg) {
		$this->ratingAvg = $ratingAvg;
	}
	
	public function getRatingCount() {
		return $this->ratingCount;
	}
	
	public function setRatingCount($ratingCount) {
		$this->ratingCount = intVal($ratingCount);
	}
	
	public function getPublishUp() {
		return $this->publishUp;
	}
	
	public function setPublishUp($publishUp) {
		$this->publishUp = $publishUp;
	}
	
	public function getPublishDown() {
		return $this->publishDown;
	}
	
	public function setPublishDown($publishDown) {
		$this->publishDown = $publishDown;
	}
	
	public function setPublished($published) {
		$this->published = $published;
	}
	
	public function getPublished() {
		if (    ((time() >= strtotime($this->publishUp)) || ($this->publishUp == "0000-00-00 00:00:00"))
			 && ((time() <= strtotime($this->publishDown)) || ($this->publishDown == "0000-00-00 00:00:00"))
			 && ($this->status == 'complete')
			 && ($this->published)
			)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isPublished() {
		return $this->published;
	}
	
	public function isExpired() {
		$publishDownIsSet = $this->publishDown != "0000-00-00 00:00:00";
        $publishHasExpired = $publishDownIsSet && time() >= strtotime($this->publishDown);

		if ($publishHasExpired) {
			return true;
		} else {
			return false;
		}
	}

    public function isPending() {
        $publishUpIsSet = $this->publishUp != "0000-00-00 00:00:00";
		$notYetPublished = $publishUpIsSet && time() <= strtotime($this->publishUp);

		if (!$this->isExpired() && $notYetPublished) {
			return true;
		} else {
			return false;
		}
    }

    public function isFeatured() {
        if ( ($this->isFeatured != "") && ((int)$this->isFeatured > 0) ) {
            return true;
        } else {
            return false;
        }
    }

	public function getAdminApproved() {
		return $this->adminApproved;	
	}
	
	public function setAdminApproved($adminApproved) {
        $inputFilter = JFilterInput::getInstance();
		$adminApproved = $inputFilter->clean($adminApproved, 'INT');
		$this->adminApproved = $adminApproved;		
	}
	
	public function getThumbnails() {
		return $this->thumbnails;
	}
	
	public function getThumbnail($index = 0) {
		try
		{
			if (isset($this->thumbnails))
			{
				reset($this->thumbnails);
			}
			
			if (($index == 0) && is_array($this->thumbnails))
			{
				return current($this->thumbnails);
			}
			elseif ((!$this->getThumbnailCount()) || (!isset($this->thumbnails[$index])))
			{
				return JURI::root() . "/media/com_jvideo/site/images/blank-thumb.gif";
			}
			else
			{
				return $this->thumbnails[$index];
			}
		}
		catch (Exception $ex)
		{
			return JURI::root() . "/media/com_jvideo/site/images/blank-thumb.gif";
		}
	}
	
	public function getThumbnailKey($index = 0) {
		try
		{
			reset($this->thumbnails);
			
			if (($index == 0) && is_array($this->thumbnails))
			{
				return key($this->thumbnails);
			}
			elseif ($index >= count($this->thumbnails) || (!isset($this->thumbnails[$index])))
			{
				return -1;
			}
			else
			{
				return $index;
			}
		}
		catch (Exception $ex)
		{
			return -2;
		}
	}
	
	public function getThumbnailCount() {
		return count($this->thumbnails);
	}
	
	public function setThumbnails($thumbnails) {
		if (isset($thumbnails))
		{
			ksort($thumbnails);
		}
		$this->thumbnails = $thumbnails;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
}