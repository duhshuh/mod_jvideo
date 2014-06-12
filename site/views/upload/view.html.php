<?php
/*
 *	@package	JVideo
 *	@subpackage Components
 *	@link http://jvideo.warphd.com
 *	@copyright (C) 2007 - 2010 Warp
 *	@license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

jvimport('Factory');
jvimport('UserAllowedToModerateSpecification');
jvimport2('Web.AssetManager');

class JVideoViewUpload extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->assignCommonVars();

		$this->assignLayoutVars();

		$this->setupDocument();

		parent::display($tpl);
	}

	private function assignCommonVars()
	{
		$app = JFactory::getApplication('site');
		$params = $app->getParams('com_jvideo');

		$this->params = $params;
	}

	private function assignLayoutVars()
	{
		switch ($this->getLayout())
		{
			default:
			case "default":
				$this->assignUploadLayoutVars();
				break;
			case "add":
				$this->assignAddLayoutVars();
				break;
			case "success":
				$this->assignSuccessLayoutVars();
				break;
			case "failure":
				$this->assignFailureLayoutVars();
				break;
		}
	}

	private function assignUploadLayoutVars()
	{
		$user = JFactory::getUser();
		$videoModel = $this->getModel('video');

		$newVideoGuid = $videoModel->getNewVideoGuid();
		
		$this->flashvars = $videoModel->getFlashVarsForUploader($newVideoGuid);

		$this->addVideoStub($newVideoGuid, $user->id);
	}

	private function assignAddLayoutVars()
	{
		$videoModel = $this->getModel('video');
		$categoriesModel = $this->getModel('categories');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$videoGuid = JRequest::getVar('videoGuid');

		$videoModel->setVideoStatusToPending($videoGuid);

		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="jvideo_input"', true, JText::_("JV_YES"), JText::_("JV_NO"));
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="jvideo_input"', false, JText::_("JV_YES"), JText::_("JV_NO"));

		$this->lists = $lists;
		$this->categories = $categoriesModel->getCategories();
		$this->videoId = $this->getVideoStubId($videoGuid);
		$this->videoGuid = $videoGuid;
		$this->authorId = $user->id;
		$this->isModerator = $this->isModerator();
		$this->submitMessage = JText::_("JV_UPLOAD_ADD_SUBMIT");
		$this->greeting = JText::_("JV_UPLOAD_ADD_HEADER");
		$this->title = JText::_("JV_UNTITLED");
		$this->validationMessage = JRequest::getVar('validationMessage');
		$this->dateAdded = $date->toSql();
		$this->videoCategories = null;
		$this->desc = '';
		$this->tags = '';
		$this->published = '';
		$this->publishUp = '';
		$this->publishDown = '';
	}

	private function assignFailureLayoutVars()
	{
		$this->error = JRequest::getVar('error');
	}

	private function assignSuccessLayoutVars()
	{
		$greeting = JText::_("JV_UPLOAD_SUCCESS");
		$user = JFactory::getUser();
		$this->jvProfile = JRequest::getVar('jvProfile');
		$this->greeting = $greeting;
		$this->userId = $user->id;
	}

	private function isModerator()
	{
		$user = JFactory::getUser();
		$spec = new JVideo_UserAllowedToModerateSpecification();
		return $spec->isSatisfiedBy($user);
	}

	private function setupDocument()
	{
		$document = JFactory::getDocument();
		$document->addCustomTag("<meta http-equiv=\"cache-control\" content=\"no-cache\">");
		$document->addCustomTag("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		$document->addCustomTag("<meta http-equiv=\"expires\" content=\"-1\">");

		JVideo2_AssetManager::includeSiteCoreCss();

		$mainframe = JFactory::getApplication();
		$breadcrumbs = $mainframe->getPathWay();

		if (count($breadcrumbs->getPathway()) == 0)
			$breadcrumbs->addItem( JText::_("JV_UPLOAD_BREADCRUMB"), JRoute::_('index.php?option=com_jvideo&view=upload'));
	}

	private function getVideo($videoId)
	{		
		$db	= JFactory::getDBO();
		$jvVideo = new JVideo_Video();
		$sql = $jvVideo->sqlSelectVideo($videoId);
		$db->setQuery($sql);
		$row = $db->loadObject();
		return $row;	
	}
	
	private function addVideoStub($videoGuid, $userId)
	{
		$videoModel = $this->getModel('video');

		return $videoModel->addVideoStub($videoGuid, $userId);

	}

	private function getVideoStubId($videoGuid)
	{
		$videoModel = $this->getModel('video');

		return $videoModel->getVideoStubId($videoGuid);
	}
}
