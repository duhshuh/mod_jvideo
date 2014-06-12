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
defined('_JEXEC') or die("Cannot use JVideo Joomla repository outside of Joomla");

require_once(dirname(__FILE__) . '/../JVideo_IUserRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_UserFactory.php');

class JVideo_JoomlaUserRepository implements JVideo_IUserRepository
{
    public function insert(JVideo_User $user)
    {
        $db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();

        $query = "INSERT INTO #__jvideo_users "
                ."(`user_id`, `display_name`, `birthdate`, `location`, `description`, `occupation`, "
                ."`interests`, `website`, `avatar`)"
                ."VALUES ("
                .$inputFilter->clean($user->userId, 'INT') . ", "
                .$db->Quote($user->displayName) . ", "
                .$db->Quote($user->birthdate) . ", "
                .$db->Quote($user->location) . ", "
                .$db->Quote($user->description) . ", "
                .$db->Quote($user->occupation) . ", "
                .$db->Quote($user->interests) . ", "
                .$db->Quote($user->website) . ", "
                .$db->Quote($user->avatar) . ") ";

        $db->setQuery($query);

        if ($db->execute()) {
            return $this->getUserById($user->userId);
        } else {
            throw new JVideo_Exception("Could not insert user into JVideo users table");
        }
    }

    public function update(JVideo_User $user)
    {
        $db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();

        $query = "UPDATE #__jvideo_users "
                ."SET "
                ."`display_name` = " . $db->Quote($user->displayName) . ", "
                ."`birthdate` = " . $db->Quote($user->birthdate) . ", "
                ."`location` = " . $db->Quote($user->location) . ", "
                ."`description` = " . $db->Quote($user->description) . ", "
                ."`occupation` = " . $db->Quote($user->occupation) . ", "
                ."`interests` = " . $db->Quote($user->interests) . ", "
                ."`website` = " . $db->Quote($user->website) . ", "
                ."`avatar` = " . $db->Quote($user->avatar) . " "
                ."WHERE `user_id` = " . $inputFilter::clean($user->userId, 'INT') . " "
                ."LIMIT 1";
        
        $db->setQuery($query);

        if ($db->execute()) {
            return $this->getUserById($user->userId);
        } else {
            throw new JVideo_Exception("Could not update JVideo user table");
        }
    }

    public function delete(JVideo_User $user)
    {
        $db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();

        if ((int) $user->userId <= 0)
            throw new JVideo_Exception("Cannot delete user with invalid user ID " . $user->userId);

        $query = "DELETE FROM #__jvideo_users "
                ."WHERE user_id = " . $inputFilter::clean($user->userId, 'INT') . " "
                ."LIMIT 1";

        $db->setQuery($query);
        
        if ($db->execute()) {
            return true;
        } else {
            throw new JVideo_Exception("Could not remove user from JVideo users table");
        }
    }

    public function getUserById($userId)
    {
        $db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();

        $query = "SELECT u1.*, u2.username, u2.registerDate, "
                ."  ("
				." 	SELECT COUNT(*) as videoCount FROM `#__jvideo_videos` "
				."	WHERE `user_id` = " . $inputFilter->clean($userId, 'INT') . " "
                ."  AND status IN ('complete', 'pending') "
				." 	GROUP BY `user_id`"
				." 	) as videoCount "
                ."FROM `#__jvideo_users` AS u1 "
                ."LEFT JOIN `#__users` AS u2 ON u2.`id` = u1.`user_id` "
                ."WHERE u1.`user_id` = " . $inputFilter->clean($userId, 'INT') . " "
                ."LIMIT 1;";

        $db->setQuery($query);
        $user = $db->loadObject();

        return JVideo_UserFactory::create($user);
    }

}