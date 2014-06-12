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

jvimport('ProfileBase');

class JVideo_Profile extends JVideo_ProfileBase {

	// extended public members
	public function __construct($userID = -1)
	{
		if ($userID != -1)
		{
			$db	= JFactory::getDBO();
			if (!$db)
			{
				return false;
			}
			
			$sql = $this->sqlSelectUserByUserID($userID);
			
			if ($sql != "")
			{
				$db->setQuery($sql);
				
				$result = $db->loadObjectList();

				if (is_array($result) && count($result) == 0)
				{
					return false;
				}
				
				$result = $result[0];
				
				$this->setID($result->id);
				$this->setUserID($result->user_id);
				$this->setUsername($result->username);
				$this->setAvatarFilename($result->avatar);
				
				// Enable if desired/required by your profile system
				// But if you have to do so, you really should just add your own plugin.
				//$this->setDisplayName($result->display_name);
				//$this->setBirthdate($result->birthdate);
				//$this->setLocation($result->location);
				//$this->setDescription($result->description);
				//$this->setOccupation($result->occupation);
				//$this->setInterests($result->interests);
				//$this->setWebsiteURL($result->website);
			}
		}
	}
	
	public function getProfileURL($userID)
	{
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
		$jvConfig = JRequest::getVar("jvConfig");
		$profileURL = str_replace("#JV_USERID#", $userID, $jvConfig->mapProfileURL);
		return JRoute::_($profileURL);		
	}
	
	public function getAvatarURL()
	{
		$avatarFilename = $this->getAvatarFilename();
		$jvConfig = JRequest::getVar("jvConfig");
		if ($avatarFilename)
		{
			return $jvConfig->mapProfileAvatarPrefix.$avatarFilename;
		}
		else
		{
			return null;
		}
	}
	
	public function sqlSelectUserByUserID($userID)
	{
		$sql = "";
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
				
		if ($userID)
		{
			$jvConfig = JRequest::getVar("jvConfig");
			
			$sql = "SELECT "
				."p.`".$jvConfig->mapProfileID."` as `id`,"
				."p.`".$jvConfig->mapProfileUserID."` as `user_id`,"
				."p.`".$jvConfig->mapProfileAvatar."` as `avatar`,"
				."u.`username` as `username` "
				."FROM ".$jvConfig->mapProfileTable." AS p "
				."JOIN #__users AS u ON p.`".$jvConfig->mapProfileUserID."` = u.`id` "
				."WHERE p.`".$jvConfig->mapProfileUserID."` = " . $userID . " "
				."LIMIT 1";
		}
		
		return $sql;
	}
}
?>