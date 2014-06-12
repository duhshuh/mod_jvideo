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

jimport('joomla.application.component.controller');

class JVideoControllerVideos extends JVideoController
{
	private $view;

	public function __construct()
	{	
		parent::__construct();

		$this->view = $this->getView('videos', 'html');
	}

	public function display($cachable = false, $urlparams = Array())
	{
		$task = $this->getTask();
		
		switch($task)
		{
			case "apply":
				$this->saveChanges();
			case "edit":
				$this->displayEditView();
				break;
			case "add":
			case "add_step1":
				$this->displayAddStep1View();
				break;
			case "add_step2":
				$this->displayAddStep2View();
				break;
			case "add_success":
				$this->displayAddSuccessView();
				break;
			case "add_failure":
				$this->displayAddFailureView();
				break;
			case "publish":
				$this->publishVideos();
				$this->displayDefaultView();
				break;
			case "unpublish":
				$this->unpublishVideos();
				$this->displayDefaultView();
				break;
			case "approve":
				$this->approveVideos();
				$this->displayDefaultView();
				break;
			case "unapprove":
				$this->unapproveVideos();
				$this->displayDefaultView();
				break;
			case "feature":
				$this->featureVideos();
				$this->displayDefaultView();
				break;
			case "unfeature":
				$this->unfeatureVideos();
				$this->displayDefaultView();
				break;
			case "remove":
			case "delete":
				$this->deleteVideos();
				$this->displayDefaultView();
				break;
			case "ajax_root_categories":
				$this->displayAjaxRootCategories();
				break;
			case "ajax_child_categories":
				$this->displayAjaxChildCategories();
				break;
			case "save":
				$this->saveChanges();
			case "cancel":
			default:
				$this->displayDefaultView();
				break;
		}
	}

	private function displayDefaultView()
	{
		$this->setupDefaultView();
		$this->view->display();
	}
	
	private function displayEditView()
	{
		$this->setupEditView();
		$this->view->display('edit');
	}

	private function displayAddStep1View()
	{
		$this->setupAddStep1View();
		$this->view->display('add_step1');
	}

	private function displayAddStep2View()
	{
		$this->setupAddStep2View();
		$this->view->display('add_step2');
	}

	private function displayAddSuccessView()
	{
		$this->setupAddSuccessView();
		$this->view->display('add_success');
	}

	private function displayAddFailureView()
	{
		$this->setupAddFailureView();
		$this->view->display('add_failure');
	}

	private function displayAjaxRootCategories()
	{
		$rootCategories = $this->getRootCategories();

		$this->printDropDownListCategories($rootCategories);
	}

	private function displayAjaxChildCategories()
	{
		$childCategories = $this->getChildCategories();

		$this->printDropDownListCategories($childCategories);
	}

	public function setupEditView()
	{
		$vidModel = $this->getModel('videos');
		$catModel = $this->getModel('categories');

		$videoId = $this->getRequestedVideoId();
		JRequest::setVar('videoId', $videoId);
		
		$video = $vidModel->getVideo($videoId);
		JRequest::setVar('video', $video);
		
		$categories = $catModel->getCategories();
		JRequest::setVar('categories', $categories);
		
		$videoCategories = $catModel->getCategoriesByVideoId($videoId);
		JRequest::setVar('videoCategories', $videoCategories);
	}

	private function getRequestedVideoId()
	{
		$videoId = JRequest::getVar('cid');
		
		if (is_array($videoId) && count($videoId) > 0)
		{
			$videoId = (int) $videoId[0];
		}
		else if ($videoId == "")
		{
			throw new JVideo_Exception("Missing Video ID");
		}
		else
		{
			$videoId = (int) $videoId;
		}

		return $videoId;
	}
	
	public function setupAddStep1View()
	{
		$user = JFactory::getUser();
		
		$newVideoGuid = $this->getNewVideoGuid();
		$flashVars = $this->buildFlashVarsForUploader($user->id, $newVideoGuid);

		JRequest::setVar('flashvars', $flashVars);
	}
	
	public function setupAddStep2View()
	{
		$model = $this->getModel('videos');
		
		$videoGuid = JRequest::getVar('videoGuid');
		$userId = JRequest::getInt('userId');

		$videoId = $model->insertVideoSkeleton($videoGuid, $userId);
		$categories = $this->getCategoriesForNewVideo();
		 
		JRequest::setVar('videoId', $videoId);
		JRequest::setVar('categories', $categories);
	}
	
	public function setupDefaultView()
	{
		$this->getVideosUsingRequestVars();
	}
	
	public function getVideosUsingRequestVars()
	{
		$videoModel = $this->getModel('videos');
		$categoryModel = $this->getModel('categories');
		$categories = $categoryModel->getCategories();

		$category = JRequest::getVar('category', null);
		
		if (!empty($category) && $category != -1)
		{
			$nestedCategories = $categoryModel->getNestedCategoryArray($category);
		}
		else
		{
			$nestedCategories = $category;
		}

		$orderBy = JRequest::getVar('orderBy', 'id desc');
		$filter = $videoModel->getFilterForSearch(JRequest::getVar('search', ''));
		$filter->exclude_deleted = true;
		$filter->exclude_waiting_for_upload = true;
		
		$items = $videoModel->getVideos($orderBy, $nestedCategories, $filter);

		JRequest::setVar('orderBy', $orderBy);
		JRequest::setVar('category', $category);
		JRequest::setVar('categories', $categories);
		JRequest::setVar('items', $items);
		JRequest::setVar('total', $videoModel->_total);
		JRequest::setVar('pagination', $videoModel->_pagination);
	}
	
	public function saveChanges()
	{
		$model = $this->getModel('videos');
		$model->save();
	}
	
	public function attachVideoToCategory()
	{
		$catmodel = $this->getModel( 'categories' );
		$video_guid = JRequest::getVar('videoGuid');
		$category_id = JRequest::getInt('category_id');
		$viewLayout = "default_add";
		
		$catmodel->add_video_category($category_id, $video_guid, "");
		
		$video_categories = $catmodel->get_videos_categories("", $video_guid);
		JRequest::setVar('attached_category_list', $video_categories);
	}
	
	public function detachVideoFromCategory()
	{
		$catmodel = $this->getModel( 'categories' );
		$video_guid = JRequest::getVar('videoGuid');
		$category_id = JRequest::getInt('attached_category_id');
		$viewLayout = "default_add";
		$catmodel->del_video_category($category_id, $video_guid, "");
		
		$video_categories = $catmodel->get_videos_categories("", $video_guid);
		JRequest::setVar('attached_category_list', $video_categories);
	}
	
	public function publishVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->publishVideo($videoId);
		}
	}
	
	public function publishVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->publishVideo((int)$videoId);
	}
	
	public function unpublishVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->unpublishVideo($videoId);
		}
	}
	
	public function unpublishVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->unpublishVideo((int)$videoId);
	}
	
	public function approveVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->approveVideo($videoId);
		}

	}
	
	public function approveVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->approveVideo((int)$videoId);
	}
	
	public function unapproveVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->unapproveVideo($videoId);
		}
	}
	
	public function unapproveVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->unapproveVideo((int)$videoId);
	}

	public function featureVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->featureVideo($videoId);
		}
	}

	public function featureVideo($videoId)
{
		$model = $this->getModel('videos');
		$model->featureVideo((int)$videoId);
	}

	public function unfeatureVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->unfeatureVideo($videoId);
		}
	}

   	public function unfeatureVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->unfeatureVideo((int)$videoId);
	}


	public function deleteVideos()
	{
		$videoIdList = JRequest::getVar('cid');

		foreach ($videoIdList as $videoId) {
			$this->deleteVideo($videoId);
		}
	}
	
	public function deleteVideo($videoId)
	{
		$model = $this->getModel('videos');
		$model->deleteVideo((int)$videoId);
	}
	
	public function getNewVideoGuid()
	{
		$model = $this->getModel('videos');
		return $model->getNewVideoGuid(); 	
	}
	
	public function buildFlashVarsForUploader($userId, $newVideoGuid)
	{
		$model = $this->getModel('videos');
		return $model->getFlashVarsForUploader($userId, $newVideoGuid);
	}
		
	private function getCategoriesForNewVideo()
	{
		$model = $this->getModel('categories');
		return $model->getCategories();
	}
	
	public function getCategoriesByVideoId($videoId)
	{
		$model = $this->getModel( 'categories' );
		return $model->getCategoriesByVideoId($videoId);
	}
	
	public function getCategoriesByVideoGuid($videoGuid)
	{
		$model = $this->getModel( 'categories' );
		return $model->getCategoriesByVideoGuid($videoGuid);
	}
	
	public function addVideoToCategory() {
		$categoryId = JRequest::getInt('categoryID');
		$videoId = JRequest::getInt('videoID');

		$model = $this->getModel('categories');
		$model->addVideoCategory($videoId, $categoryId);

		$categories = $model->getCategoriesByVideoId($videoId);

		echo JHTML::_('jvideo.videoCategory.videoCategoryList', $categories);
	}
	
	public function removeVideoFromCategory() {
		$categoryId = JRequest::getInt('categoryID');
		$videoId = JRequest::getInt('videoID');
		$videoCategoriesList = "";

		$model = $this->getModel('categories');
		$model->removeVideoCategory($videoId, $categoryId);

		$categories = $model->getCategoriesByVideoID($videoId);
		
		echo JHTML::_('jvideo.videoCategory.videoCategoryList', $categories);
	}

	public function getRootCategories()
	{
		$model = $this->getModel( 'categories' );
		return $model->getRootCategories();
	}

	public function getChildCategories()
	{
		$model = $this->getModel( 'categories' );

		$parentId = JRequest::getInt('parentId');

		return $model->getChildCategoriesByCategoryId($parentId);
	}

	private function printDropDownListCategories($categories)
	{
		$output = "";

		foreach ($categories as $category) {
			$output .= "<option value='" . $category->id . "'>" . htmlentities($category->name) . "</option>\n";
		}

		echo $output;
	}
}