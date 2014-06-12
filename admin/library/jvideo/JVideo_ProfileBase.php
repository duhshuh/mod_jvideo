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

class JVideo_ProfileBase implements JVideo_DTO
{
	private $id;
	private $userID;
	private $username;
	private $fullName;
	private $displayName;
	private $birthdate;
	private $location;
	private $description;
	private $occupation;
	private $interests;
	private $websiteURL;
	private $avatarFilename;

	public function getID() {
		return $this->id; 
	}
	
	public function setID($id) {
		$this->id = $id;
	}
	
	public function getUserID() {
		return $this->userID; 
	}
	
	public function setUserID($userID) {
		$this->userID = $userID;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	public function getFullName() {
		return $this->fullName;
	}
	
	public function setFullName($fullName) {
		$this->fullName = $fullName;
	}
	
	public function getDisplayName() { 
		return $this->displayName;
	}
	
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
	
	public function getBirthdate() { 
		return $this->birthdate; 
	}
	
	public function setBirthdate($birthdate) {
		$this->birthdate = $birthdate;
	}
	
	public function getLocation() {
		return $this->location; 
	}
	
	public function setLocation($location) {
		$this->location = $location;
	}
	
	public function getDescription() {
		return $this->description; 
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}

	public function getOccupation() {
		return $this->occupation; 
	}
	
	public function setOccupation($occupation) {
		$this->occupation = $occupation;
	}

	public function getInterests() {
		return $this->interests; 
	}
	
	public function setInterests($interests) {
		$this->interests = $interests;
	}
	
	public function getWebsiteURL() {
		return $this->websiteURL; 
	}
	
	public function setWebsiteURL($websiteURL) {
		$this->websiteURL = $websiteURL;
	}

	public function getAvatarFilename() {
		return $this->avatarFilename; 
	}
	
	public function setAvatarFilename($avatarFilename) {
		$this->avatarFilename = $avatarFilename;
	}
}

?>