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

	// extended private members
	private $m_avatarapproved = 0;
	
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

				if (!is_array($result) || count($result) == 0)
				{
					return false;
				}
				
				$result = $result[0];
				
				$this->setID($result->id);
				$this->setUserID($result->user_id);
				$this->setUsername($result->username);
				$this->setDisplayName($result->firstname . " " . $result->lastname);
				$this->setBirthdate("");
				$this->setLocation("");
				$this->setDescription("");
				$this->setOccupation("");
				$this->setInterests("");
				$this->setWebsiteURL("");
				$this->setAvatarFilename($result->avatar);
				$this->setAvatarApproved($result->avatarapproved);
			}
		}
	}
	
	public function getProfileURL($userID, $targetItemID = "")
	{
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
		$targetItemID = $inputFilter->clean($targetItemID, 'INT');
		
		$routeUrl = "index.php?option=com_comprofiler&task=userProfile&user=" . $userID;
		
		if ($targetItemID != 0)
		{
			$routeUrl .= "&Itemid=" . $targetItemID;
		}
		
		return JRoute::_($routeUrl);		
	}
	
	public function getAvatarURL()
	{
		$avatarFilename = $this->getAvatarFilename();
		
		if ($this->m_avatarApproved)
		{
	    	if ($avatarFilename != '')
            {
                $imgpath = JURI::base() . 'images/comprofiler/';
                if (eregi("gallery/", $avatarFilename) == false)
					$imgpath .= "tn" . $avatarFilename;
                else
    				$imgpath .= $avatar;
    			
    			return $imgpath;
            }
		}
		return null;
	}
	
	public function sqlSelectUserByUserID($userID)
	{
		$sql = "";
        $inputFilter = JFilterInput::getInstance();
		$userID = $inputFilter->clean($userID, 'INT');
				
		if ($userID)
		{
			$sql = "SELECT c.`id`, c.`user_id`, u.`username`, c.`firstname`, c.`lastname`, c.`avatar`, c.`avatarapproved` "
				."FROM #__users u "
				."INNER JOIN #__comprofiler c ON u.`id` = c.`user_id` "
				."WHERE c.`user_id` = " . $userID . " "
				."LIMIT 1";
		}
		
		return $sql;
	}

	public function getAvatarApproved() {
		return $this->m_avatarApproved;
	}
	
	public function setAvatarApproved($approved) {
        $inputFilter = JFilterInput::getInstance();
		$approved = $inputFilter->clean($approved, 'INT');
		$this->m_avatarApproved = $approved;
	}
}
?>