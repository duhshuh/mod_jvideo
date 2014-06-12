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
 
jimport( 'joomla.application.component.view' );

jvimport2('Web.AssetManager');

class JVideoViewVideos extends JViewLegacy
{
	public function display($tpl = null)
	{
		switch ($tpl)
		{
			case "add_step1":
				$this->setupAddStep1Layout();
				break;
			case "add_step2":
				$this->setupAddStep2Layout();
				break;
			case "add_success":
				$this->setupAddSuccessLayout();
				break;
			case "add_failure":
				$this->setupAddFailureLayout();
				break;
			case "edit":
				$this->setupEditLayout();
				break;
			default:
				$this->setupDefaultLayout();
				break;
		}
		
		 parent::display($tpl);
	}
	
	public function setupDefaultLayout()
	{
		$this->setupAssets();

		if ($this->checkIfAccountHasBeenSetup()) {
			$this->setupDefaultLayoutToolbar();
			$this->setupDefaultLayoutVideos();
			$this->setupDefaultLayoutFilters();
			$this->setupDefaultLayoutPagination();
			$this->setupDefaultLayoutHelpers();
		} else {
			JToolbarHelper::title( JText::_( 'JV_VIDEOS' ), 'videos' );
		}
	}
	
	public function checkIfAccountHasBeenSetup()
	{
		$config = JVideo_Factory::getConfig();
   	
		if ($config->infinoAccountKey == "") {
			$this->accountIsNotSetup = $accountIsNotSetup = true;
			return false;
		} else {
			$this->accountIsNotSetup = $accountIsNotSetup = false;
			return true;
		}
	}
	
	
	public function setupDefaultLayoutToolbar()
	{
		JToolbarHelper::title( JText::_( 'JV_VIDEOS' ), 'videos' );

		JToolbarHelper::addNew();
		JToolbarHelper::editList();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::custom('feature', 'jvideo-16-feature', 'jvideo-16-feature', JText::_('JV_FEATURE_VIDEOS'), true, true);
		JToolbarHelper::custom('unfeature', 'jvideo-16-unfeature', 'jvideo-16-unfeature', JText::_('JV_UNFEATURE_VIDEOS'), true, true);
		JToolbarHelper::custom('approve', 'jvideo-16-approve', 'jvideo-16-approve', JText::_('JV_APPROVE_VIDEOS'), true, true);
		JToolbarHelper::custom('unapprove', 'jvideo-16-unapprove', 'jvideo-16-unapprove', JText::_('JV_UNAPPROVE_VIDEOS'), true, true);
		JToolbarHelper::deleteList(JText::_('JV_VIDEO_DELETE_VIDEOS_CONFIRM'), 'delete');
	}

	public function setupDefaultLayoutVideos()
	{
		$videos = JRequest::getVar('items');
		
		$this->items = $videos;
	}
	
	public function setupDefaultLayoutFilters()
	{
		$lists['search'] = $this->setupDefaultLayoutSearchFilter();
		$lists['categories'] = $this->setupCategoriesFilter();

		$this->lists = $lists;
	}
	
	public function setupDefaultLayoutSearchFilter()
	{
		$mainframe = JFactory::getApplication();
		$search = $mainframe->getUserStateFromRequest('search',	'search', '', 'string');
		return JString::strtolower($search);
	}

	public function setupCategoriesFilter()
	{
		return JRequest::getVar('categories', null);
	}
	
	public function setupDefaultLayoutPagination()
	{
		$this->pagination = JRequest::getVar('pagination');
	}
	
	public function setupDefaultLayoutHelpers()
	{
		$config = JVideo_Factory::getConfig();

		if (JRequest::getVar('enableThumbs') != "false") {
			$enableThumbs = "true";
			$this->enableThumbs = $enableThumbs;
		} else {
			$enableThumbs = "false";
			$this->enableThumbs = $enableThumbs;
		}
		$this->thumbFaderEnabled = $config->thumbFaderEnabled;
 		$this->lastFullSync = $config->lastFullSync;
	}
	
	public function setupEditLayout()
	{
		$this->setupAssets();
		$this->setupEditLayoutToolbar();
		$this->setupEditLayoutMessages();
		$this->setupEditLayoutVideoAttributes();
	}

	public function setupEditLayoutToolbar()
	{
		JToolbarHelper::title( JText::_("JV_EDIT_VIDEO") );
		JToolbarHelper::save();
		JToolbarHelper::apply();
		JToolbarHelper::cancel();
	}
	
	public function setupEditLayoutVideoAttributes()
	{
		$config = JVideo_Factory::getConfig();

		$video = new JVideo_Video();
		$videoData = JRequest::getVar('video');
		$videoDataAdapter = new JVideo_VideoDataAdapter();
		$videoDataAdapter->fill($video, $videoData);
		
		$videoGuid = $video->infinoVideoID;
		$videoId = $video->id;
		$title = htmlspecialchars($video->videoTitle);
		$desc = $video->videoDescription;
		$tags = htmlspecialchars($video->tags);
		$publishUp = $video->publishUp;
		$publishDown = $video->publishDown;
		$published = $video->published;
		$authorID = $video->userID;
		$authorName = $video->username;
		$featured = $video->isFeatured ? "1" : "0";
		$videoPlayerHeight = $config->videoPlayerHeight;
		$videoPlayerWidth = $config->videoPlayerWidth;
		$videoStatus = $video->status;
		
		$embedCode = $video->getEmbedCodeByFlashvars($config->infinoAccountKey
			, $this->getFlashVarsByVideoGuid($videoGuid)
			, $videoPlayerHeight, $videoPlayerWidth, true);
		
		if (method_exists('JFactory', 'getDate')) {
			$dateAdded = JFactory::getDate($video->getDateAdded());
			$dateAdded = $dateAdded->format('Y-m-d');
		} else {
			$dateAdded = $video->getDateAdded();
		}
		
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="jvideo_input"', $video->published, JText::_("JV_YES"), JText::_("JV_NO"));
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="jvideo_input"', $featured, JText::_("JV_YES"), JText::_("JV_NO"));

		$categories = JRequest::getVar('categories');
		$videoCategories = JRequest::getVar('videoCategories');

		$this->categories = $categories;
		$this->videoCategories = $videoCategories;
		$this->lists = $lists;
		$this->title = $title;
		$this->desc = $desc;
		$this->tags = $tags;
		$this->authorID = $authorID;
		$this->authorName = $authorName;
		$this->dateAdded = $dateAdded;
		$this->published = $published;
		$this->publishUp = $publishUp;
		$this->publishDown = $publishDown;
		$this->videoPlayerHeight = $videoPlayerHeight;
		$this->videoPlayerWidth = $videoPlayerWidth;
		$this->videoStatus = $videoStatus;
		$this->embedCode = $embedCode;
		$this->videoGuid = $videoGuid;
		$this->videoId = $videoId;
	}
	
	public function setupEditLayoutMessages()
	{
		$id = JRequest::getInt('id');
		$validation_msg = JRequest::getVar('validation_msg');
		$uid = JRequest::getInt('uid');
		$greeting = JText::_("JV_EDIT_VIDEO");
		$submit_msg = JText::_("JV_EDIT_SAVE");
		$task_value = "edit";
		
		$this->id = $id;
		$this->task_value = $task_value;
		$this->submit_msg = $submit_msg;
		$this->greeting = $greeting;
		$this->validation_msg = $validation_msg;
	}
	
	public function setupAddStep1Layout()
	{
		$this->setupAddLayoutToolbar();
		$this->setupAssets();
		$this->setupAddUploaderFlashvars();
	}
	
	public function setupAddLayoutToolbar()
	{
		JToolbarHelper::title( JText::_("JV_UPLOAD_HEADER") );
		JToolbarHelper::cancel();
	}
	
	public function setupAddStep2Layout()
	{
		JToolbarHelper::save();
		$this->setupAddLayoutToolbar();
		$this->setupAssets();
		$this->setupAddStep2References();
	}

	public function setupAddUploaderFlashvars()
	{
		$this->flashvars = JRequest::getVar('flashvars');
	}
	
	public function setupAddStep2References()
	{
		$publishUp = "";
		$publishDown = "";
		$published = "";

		$videoGuid = JRequest::getVar('videoGuid');
		$videoId = JRequest::getInt('videoId');
		$userId = JRequest::getInt('userId');
		$categories = JRequest::getVar('categories');

		$dateAdded = JFactory::getDate();
		$dateAdded = $dateAdded->toSql();
		
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="jvideo_input"', true, JText::_("JV_YES"), JText::_("JV_NO"));
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="jvideo_input"', false, JText::_("JV_YES"), JText::_("JV_NO"));

		$this->lists = $lists;
		$this->userId = $userId;
		$this->videoId = $videoId;
		$this->categories = $categories;
		$this->videoGuid = $videoGuid;
		$this->dateAdded = $dateAdded;
		$this->published = $published;
		$this->publishUp = $publishUp;
		$this->publishDown = $publishDown;
	}
	
	public function setupAssets()
	{
		JVideo2_AssetManager::includeJQuery();
		JVideo2_AssetManager::includeJQueryUI();
		JVideo2_AssetManager::includeAdminCoreJs();
		JVideo2_AssetManager::includeAdminCoreCss();
	}
	
	public function getFlashVarsByVideoGuid($videoGuid)
	{
		$config = JVideo_Factory::getConfig();

		$infinovationVideo = new InfinovationVideo(
			$config->infinoAccountKey,
			$config->infinoSecretKey);
		
		return $infinovationVideo->getPlayerFlashVars($videoGuid, false);
	}
}
