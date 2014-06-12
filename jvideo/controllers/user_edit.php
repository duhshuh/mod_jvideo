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

jvimport2('Image.AvatarUploadProcessor');

class JVideoControllerUser_Edit extends JVideoController
{
	public function display($cachable = false, $urlparams = Array())
	{	
		$editingUserId = JRequest::getVar('user_id');
		 
		if (!$this->isLoggedInUsersId($editingUserId))
		{
			$this->redirectToUserProfileView($editingUserId);
			return;
		}

		$this->displayUserEditView($editingUserId);
	}

	public function save()
	{
		$editingUserId = JRequest::getVar('user_id');

		$avatarOriginalFileName = null;
		$avatarSavedFileName = null;
		if (strlen($_FILES['jvideo_form_avatar']['name']) > 0)
		{
			$avatarOriginalFileName = $_FILES['jvideo_form_avatar']['name'];
			$avatarSavedFileName = $editingUserId . "_" . date('Ymd_His') . '.jpg';
		}
		JRequest::setVar('jvideo_form_avatar_name', $avatarSavedFileName);

		$model = $this->getModel('user');
		$result = $model->saveUserProfile($editingUserId);

		if (0 == $result)
		{
			if (isset($avatarOriginalFileName))
			{
				$tempFilePath = $_FILES['jvideo_form_avatar']['tmp_name'];
				$avatarProcessor = new JVideo2_AvatarUploadProcessor();
				$avatarProcessor->save($tempFilePath, $avatarOriginalFileName, $avatarSavedFileName, $editingUserId);
			}

			$this->redirectToUserProfileView($editingUserId);
		}
		else if (1 == $result)
		{
			$this->displayUserEditView($editingUserId, JText::_("JV_EDIT_PROFILE_ACCESS_DENIED"));
		}
		else
		{
			$this->displayUserEditView($editingUserId, JText::_("JV_EDIT_PROFILE_ERROR_WITH_FORM"));
		}
	}

	private function isLoggedInUsersId($userId)
	{
		$loggedInUser = JFactory::getUser();
		return $loggedInUser->id != '' && $loggedInUser->id == $userId;
	}

	private function displayUserEditView($userId, $error = null)
	{
		$model = $this->getModel('user');
		$profile = $model->getUserProfile($userId);

		JRequest::setVar('hasGD', extension_loaded("gd"));
		JRequest::setVar('profile', $profile);
		JRequest::setVar('error', $error);
		
		parent::display();
	}

	private function redirectToUserProfileView($userId)
	{
		$userProfileUrl = JRoute::_('index.php?option=com_jvideo&view=user&user_id=' . $userId);
		JFactory::getApplication()->redirect($userProfileUrl);
	}
}