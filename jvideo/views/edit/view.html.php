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

jimport('joomla.application.component.view');
jvimport('Factory');
jvimport('UserAllowedToModerateSpecification');
jvimport('VideoRepositoryFactory');
jvimport2('Web.AssetManager');

class JVideoViewEdit extends JViewLegacy
{
	private $_videoId = null;

	public function display($tpl = null)
	{
		$this->_videoId = JRequest::getInt('id');

		$this->assignCommonVars();
		$this->assignLayoutVars();
		$this->setupDocument();

		parent::display($tpl);
	}

	private function assignCommonVars()
	{
		$app = JFactory::getApplication('site');
		$params = $app->getParams('com_jvideo');

		$this->assignRef('params', $params);
	}

	private function assignLayoutVars()
	{
		switch ($this->getLayout())
		{
			default:
			case "default":
				$this->assignEditLayoutVars();
				break;
			case "result":
				$this->assignResultLayoutVars();
				break;
		}
	}

	private function assignEditLayoutVars()
	{
		$videoModel = $this->getModel('video');
		$categoriesModel = $this->getModel('categories');
		$config = JVideo_Factory::getConfig();
		$user = JFactory::getUser();

		$video = $this->getVideo($this->_videoId);

		if (!$this->isOwner($user, $video) && !$this->isModerator($user))
			JError::raiseError('403', 'Access Denied');

		$categories = $categoriesModel->getCategories();
		$videoCategories = $categoriesModel->getCategoriesByVideoId($this->_videoId);

		$title = htmlspecialchars($video->videoTitle);
		$tags = htmlspecialchars($video->tags);
		$dateAdded = JFactory::getDate($video->dateAdded);

		$featured = (is_null($video->isFeatured) || $video->isFeatured == "") ? "0" : "1";
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="jvideo_input"', $featured, JText::_("JV_YES"), JText::_("JV_NO"));
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="jvideo_input"', $video->published, JText::_("JV_YES"), JText::_("JV_NO"));

		$this->setupBreadcrumbs($title);
		
		$embedCode = $video->getEmbedCodeByFlashvars($config->infinoAccountKey
			, $videoModel->getFlashVarsByVideoGuid($video->infinoVideoID)
			, $config->videoPlayerHeight, $config->videoPlayerWidth, true);

		$this->videoId = $this->_videoId;
		$this->videoGuid = $video->infinoVideoID;
		$this->title = $video->videoTitle;
		$this->description = $video->videoDescription;
		$this->tags = $tags;
		$this->authorId = $video->userID;
		$this->dateAdded = $dateAdded->format('Y-m-d');
		$this->published = $video->published;
		$this->publishUp = $video->publishUp;
		$this->publishDown = $video->publishDown;
		$this->videoStatus = $video->status;
		$this->videoPlayerHeight = $config->videoPlayerHeight;
		$this->videoPlayerWidth = $config->videoPlayerWidth;
		$this->embedCode = $embedCode;
		$this->lists =	$lists;
		$this->categories = $categories;
		$this->videoCategories = $videoCategories;
		$this->validationMessage = JRequest::getVar('validationMessage');
		$this->isModerator = $this->isModerator($user);
		$this->greeting = JText::_("JV_EDIT_VIDEO");
		$this->submitMessage = JText::_("JV_EDIT_SAVE");
	}

	private function assignResultLayoutVars()
	{
		$title = JRequest::getVar("title");
		$this->setupBreadcrumbs($title);

		//@refactor
		$jvVideo = new JVideo_Video();
		$jvVideo->setID($this->_videoId);
		
		$this->title = $title;
		$this->resultHeader = JRequest::getVar("resultHeader");
		$this->resultMessage = JRequest::getVar("resultMessage");
		$this->resultReturn = JRequest::getVar("goBackWhere", $jvVideo->getVideoURL());
	}

	private function setupDocument()
	{
		$this->addScripts();
		$this->addStylesheets();
	}

	private function addScripts()
	{
		$config = JVideo_Factory::getConfig();

		JVideo2_AssetManager::includeJQuery();
		JVideo2_AssetManager::includeJQueryUI();
		JVideo2_AssetManager::includeSiteCoreJs();

		if ($config->thumbFaderEnabled)
			JVideo2_AssetManager::includeCrossFadeJs();
	}

	private function addStylesheets()
	{
		JVideo2_AssetManager::includeSiteCoreCss();
	}

	private function setupBreadcrumbs($title)
	{
		$mainframe = JFactory::getApplication();
		$breadcrumbs = $mainframe->getPathWay();
		$breadcrumbs->addItem( $title, JRoute::_('index.php?option=com_jvideo&view=watch&id='.$this->_videoId));
		$breadcrumbs->addItem( JText::_("JV_EDIT_VIDEO"), JRoute::_('index.php?option=com_jvideo&view=edit&id='.$this->_videoId));
	}

	private function getVideo($videoId)
	{
		$repository = JVideo_VideoRepositoryFactory::create();

		return $repository->getVideoById($videoId);
	}

	private function isAuthenticated(&$user, &$video)
	{
		if ($this->isModerator($user))
			return true;
		else if ($this->isOwner($user, $video))
			return true;
		else
			return false;
	}

	private function isModerator($user)
	{
		$spec = new JVideo_UserAllowedToModerateSpecification();
		return $spec->isSatisfiedBy($user);
	}

	private function isOwner(&$user, &$video)
	{
		return $user->id == $video->userID;
	}
}