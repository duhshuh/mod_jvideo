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

jvimport('Factory');
jvimport('UserAllowedToModerateSpecification');
jvimport('VideoRepositoryFactory');

class JVideoController extends JControllerLegacy
{
	public $jvConfig, $jvProfile, $proxyParams;
	private $_repository = null;

	public function __construct()
	{
		parent::__construct();

		$this->_repository = JVideo_VideoRepositoryFactory::create();
		$this->loadConfig();
		$this->initializePlugins();
	}

	protected function loadConfig()
	{
		$this->jvConfig = JVideo_Factory::getConfig();
	}

	protected function initializePlugins()
	{
		$model = $this->getModel( 'jvideo' );
		$model->initializePlugins();

		$this->jvProfile = new JVideo_Profile();
		JRequest::setVar('jvProfile', $this->jvProfile);
	}

	//@todo: Proxy control functions should be library only -- refactor!
	public function proxyControl(InfinovationSoapBase &$base)
	{
		if ($this->jvConfig->proxyEnabled)
		{
			$base->enableProxy();
			$base->setProxyParams(
				$this->jvConfig->proxyHost,
				$this->jvConfig->proxyPort,
				$this->jvConfig->proxyUsername,
				$this->jvConfig->proxyPassword,
				$this->jvConfig->proxyTimeout,
				$this->jvConfig->proxyResponseTimeout
			);
		}
		else
		{
			$base->disableProxy();
		}
	}
	
	public function delete_video()
	{
		$id = (int) JRequest::getVar('id');

		if ($id <= 0) {
			JError::raiseError('403', 'Access Denied');
		}
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$sql = "select infin_vid_id,user_id from #__jvideo_videos where id=".$id;
		$db->setQuery($sql);
		$row = $db->loadObject();
		
		$videoGUID = $row->infin_vid_id;
		$userID = $row->user_id;
		
		if ($id != "")
		{
			if (!$this->isModerator($user) && $userID != $user->id)
			{
				JError::raiseError('403', 'Access Denied');
				return;
			}
			
			// mark as deleted
			$sql = "UPDATE #__jvideo_videos "
			."SET status='deleted' "
			."WHERE id=".$id." "
			."LIMIT 1";
			$db->setQuery($sql);
			$db->execute();
			
			$infinovationVideo = new InfinovationVideo($this->jvConfig->infinoAccountKey, $this->jvConfig->infinoSecretKey);
			$this->proxyControl($infinovationVideo);
			$infinovationVideo->deleteVideo($videoGUID);

			$this->removeFeaturedById($id);

			JRequest::setVar('view', 'watch');

			$this->display();
		}
	}

	private function isModerator($user)
	{
		$spec = new JVideo_UserAllowedToModerateSpecification();
		return $spec->isSatisfiedBy($user);
	}
	
	public function removeFeaturedById($videoId)
	{
		$db = JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$videoId = $inputFilter->clean($videoId, 'INT');

		if ($videoId <= 0) {
			return false;
		}

		$this->_repository->unfeature(
			$this->_repository->getVideoById($videoId)
		);
	}
	
	public function user_allvideos()
	{
		$this->getCompletedVideos($this->jvConfig->fullSync);
		 
		$model = $this->getModel( 'user' );
		 
		// Get data from the model
		$pagination = $model->get_user_pagination($user_id);
		$items = $model->get_userVideos();
		 
		JRequest::setVar('pagination', $pagination);
		JRequest::setVar('items', $items);
		 
		JRequest::setVar('view', 'user_allvideos');
		$this->display();
	}

	public function approve()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getInt('id');
		
		$sql = "update #__jvideo_videos set admin_approved = 1 where id = " . (int) $id . " limit 1";
		$db->setQuery($sql);
		$db->execute();
	}

	public function rate_video()
	{
		$model = $this->getModel( 'JVideo' );
		$rateResult = $model->rate_video();

		$id = JRequest::getInt('id');
		$video = $model->get_video($id);
		if (count($video) > 0)
		{
			$user = JFactory::getUser();
			$jvConfig = JVideo_Factory::getConfig();

			$jvVideo = new JVideo_Video();
			$jvVideo->setID($video->id);
			$jvVideo->setUserID($video->user_id);
			$jvVideo->setRatingAvg($video->rating_avg);
			$jvVideo->setRatingCount($video->rating_count);
			
			$ajaxReturn = "Rate: ";
			
			$blankImg = JURI::base() . "/media/com_jvideo/site/images/blank.gif";
			$emptyStar = "jvideo-stars-large-empty";
			$halfStar = "jvideo-stars-large-half";
			$filledStar = "jvideo-stars-large-full";
			
			$starMap = '00000';
			for ($i = 1; $i <= 5; $i++)
			{
				$starMap[$i - 1] = floor($jvVideo->getRatingAvg()) >= $i ? '2' : (round($jvVideo->getRatingAvg()) == $i ? '1' : '0');
			}
			
			$ajaxReturn .= '<span class="jvideo-rating-stars" title="' . round($jvVideo->getRatingAvg(), 1) . ' ' . JText::_("out of 5 stars") . '">';
			
			$starMapRate = '00000';
			for ($i = 1; $i <= 5; $i++)
			{
				$star = floor($jvVideo->getRatingAvg()) >= $i ? $filledStar : (round($jvVideo->getRatingAvg()) == $i ? $halfStar : $emptyStar);
				$starMapRate[$i - 1] = '2';
				$ajaxReturn .= "<img src=\"".$blankImg."\" class=\"".$star."\" id=\"videoStar".$i."\" vspace=\"0\" "
					."hspace=\"0\" height=\"16\" width=\"16\" border=\"0\" />";
					
			}
			$ajaxReturn .= "</span>";
			
			if ($jvVideo->getRatingCount() == 1)
			{
				$ajaxReturn .= "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\">". JText::_("JV_VIDEO_THANKS_FOR_VOTING") ."</span> <span class=\"jvideo-rating-count\">". JText::_("JV_VIDEO_1_RATING") ."</span>";
			}
			else if ($jvVideo->getRatingCount() == 0)
			{
				$ajaxReturn .= "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\">". JText::_("JV_VIDEO_THANKS_FOR_VOTING") ."</span> <span class=\"jvideo-rating-count\">". JText::_("JV_VIDEO_NOT_YET_RATED") ."</span>";	
			}
			else
			{
				$ajaxReturn .= "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\">". JText::_("JV_VIDEO_THANKS_FOR_VOTING") ."</span> <span class=\"jvideo-rating-count\">".$jvVideo->getRatingCount()." ". JText::_("JV_VIDEO_RATINGS") ."</span>";
			}
			
			echo $ajaxReturn;
			
		}
	}

	public function removeFeatured()
	{
		$videoId = JRequest::getInt('videoId');
		
		if ($videoId > 0) {
			$model = $this->getModel('jvideo');
			$model->removeFeatured($videoId);
		}
		
		echo JHTML::_('jvideo.video.removeFeatured', $videoId);
	}

	public function addFeatured()
	{
		$videoId = JRequest::getInt('videoId');

		$model = $this->getModel('jvideo');
		$model->addFeatured($videoId);

		echo JHTML::_('jvideo.video.addFeatured', $videoId);
	}

	
	public function publishVideo()
	{
		$id = JRequest::getInt('id');
		
		if ($id > 0)
		{
			$model = $this->getModel( 'JVideo' );
			$model->publishVideo($id);
		}
		
		echo "<a href=\"#jvideo\" title=\"". JText::_("Click here to unpublish this video") ."\" onClick=\"javascript: JVideoAJAX.publishVideo(".$id.", 'false'); return false;\">". JText::_("Published") ."</a>";
	}
	
	public function unpublishVideo()
	{
		$id = JRequest::getInt('id');
		
		if ($id > 0)
		{
			$model = $this->getModel( 'JVideo' );
			$model->unpublishVideo($id);
		}
		
		echo "<a href=\"#jvideo\" title=\"". JText::_("JV_VIDEO_PUBLISH_TITLE") ."\" onClick=\"javascript: JVideoAJAX.publishVideo(".$id.", 'true'); return false;\">". JText::_("JV_VIDEO_UNPUBLISHED") ."</a>";
	}
	
	public function addVideoToCategory()
	{
		$categoryId = JRequest::getInt('categoryId');
		$videoId = JRequest::getInt('videoId');
		
		$model = $this->getModel( 'categories' );
		$model->addVideoCategory($videoId, $categoryId);

		$categories = $model->getCategoriesByVideoId($videoId);
		
		echo JHTML::_('jvideo.videoCategory.videoCategoryList', $categories);
	}
	
	public function removeVideoFromCategory()
	{
		$categoryId = JRequest::getInt('categoryId');
		$videoId = JRequest::getInt('videoId');
		
		$model = $this->getModel( 'categories' );
		$model->removeVideoCategory($videoId, $categoryId);

		$categories = $model->getCategoriesByVideoId($videoId);

		echo JHTML::_('jvideo.videoCategory.videoCategoryList', $categories);
	}

	public function getVideoCountByUserId($userId)
	{
		$model = $this->getModel('JVideo');
		$cache = JFactory::getCache('com_jvideo');
		return $cache->call(array($model, 'getVideoCountByUserId'), $userId);
	}
	
	public function getCompletedVideos($fullSync = false)
	{
		$model = $this->getModel('jvideo');
		$model->synchronize($fullSync);
	}
	
	public function normalSync()
	{
		$model = $this->getModel('jvideo');
		$result = $model->synchronize(false);
	}

	public function consoleSync()
	{
		$model = $this->getModel('jvideo');
		$result = $model->synchronize(true);

		if (JRequest::getInt('manual') == 1) {
			echo "document.getElementById('manualSyncDiv').innerHTML = 'Sync complete';";
		} else {
			echo $result;
		}
	}

	//@deprecated
	public function dashboardSync()
	{
		$this->consoleSync();
	}


}