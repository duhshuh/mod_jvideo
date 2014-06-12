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

class JVideoControllerUser extends JVideoController
{
	public function display($cachable = false, $urlparams = Array())
	{	
		$app = JFactory::getApplication('site');
		$params = $app->getParams('com_jvideo');
		
		$user = JFactory::getUser();
		$user_id = JRequest::getInt('user_id') == "" ? $user->id : JRequest::getInt('user_id');
		$order = $params->get('video_order');
		$categories = $params->get('video_categories');
		$filter = $params->get('video_filter');
		$videosPerPage = $params->get('videos_per_page');
		JRequest::setVar('video_order', $order);
		JRequest::setVar('video_filter', $filter);
		JRequest::setVar('videos_per_page', $videosPerPage);
		JRequest::setVar('video_categories', $categories);
		
		if ($user_id == "") {
			parent::display();
			return;
		}

		$cache = JFactory::getCache('com_jvideo');
		$model = $this->getModel('user');
		$limitstart = JRequest::getVar("limitstart");
		$limit = JRequest::getVar("limit");
		
		// Build a repeatable base cache ID
		$baseId = "user_".$user_id."_".$filter."_".$videosPerPage."_".$limit."_".$limitstart; 

		$items = $cache->get(array($model, 'getUserVideos'), array($user_id, $order, $categories, $filter, $limitstart, $limit)
			,$baseId . "_items");
		$total = $cache->get(array($model, 'getUserVideosTotal'), array($user_id, $order, $categories, $filter)
			,$baseId . "_total");
			
		$pagination = $model->getUserVideosPagination($user_id, $total, $limitstart, $limit);

		$profile = $cache->get(array($model, 'getUserProfile'), array($user_id)
			,$baseId . "_profile");

		$username = $cache->get(array($model, 'getUsername'), array($user_id)
			,$baseId . "_username");

		JRequest::setVar('pagination', $pagination);
		JRequest::setVar('items', $items);
		JRequest::setVar('profile', $profile);
		JRequest::setVar('user', $user);
		JRequest::setVar('user_id', $user_id);
		JRequest::setVar('username', $username);
		
		parent::display();
	}
}