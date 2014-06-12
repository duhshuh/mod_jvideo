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

class JVideoControllerVideos extends JVideoController
{
    private $params;
    private $videoOrder;
    private $videoCategories;
    private $videoFilter;
    private $videosPerPage;
    private $userId;
    private $limit;
    private $limitstart;

	public function display($cachable = false, $urlparams = Array())
	{
        $cache = JFactory::getCache('com_jvideo');
        $model = $this->getModel('videos');
        $viewLayout = JRequest::getCmd( 'layout', 'default' );

        $this->assignMenuParametersToPrivateMembers();

        $this->setupPaginationLimitAndLimitStart();

        $baseCacheId = $this->buildBaseCacheId();
        
		$items = $cache->get(array($model, 'getVideos')
            , array($this->videoOrder, $this->videoCategories, $this->videoFilter
                , $this->limitstart, $this->limit, $this->userId)
			,$baseCacheId . "_items");

        $total = $cache->get(array($model, 'getVideosTotal')
            , array($this->videoOrder, $this->videoCategories, $this->videoFilter
                , $this->userId)
			,$baseCacheId . "_total");
		
        $pagination = $model->getVideosPagination($total, $this->limitstart
            , $this->limit);

		JRequest::setVar('user_id', $this->userId);
		JRequest::setVar('video_order', $this->videoOrder);
		JRequest::setVar('video_filter', $this->videoFilter);
		JRequest::setVar('videos_per_page', $this->videosPerPage);
		JRequest::setVar('video_categories', $this->videoCategories);
		JRequest::setVar('limitstart', $this->limitstart);
		JRequest::setVar('limit', $this->limit);
		JRequest::setVar('layout', $viewLayout);
		JRequest::setVar('items', $items);
		JRequest::setVar('pagination', $pagination);
		
		parent::display();
	}

    private function assignMenuParametersToPrivateMembers()
    {
		$app = JFactory::getApplication('site');
		$this->params = $app->getParams('com_jvideo');

        $this->videoOrder = $this->params->get('video_order', JRequest::getVar('video_order', 'newestvideos'));
		$this->videoCategories = $this->params->get('video_categories', JRequest::getVar('video_categories', -1));
		$this->videoFilter = $this->params->get('video_filter', JRequest::getVar('video_filter', 'published'));
		$this->videosPerPage = $this->params->get('videos_per_page', JRequest::getVar('videos_per_page', 12));
        $this->userId = JRequest::getInt('user_id', 0);
    }

    private function setupPaginationLimitAndLimitStart()
    {
        $this->setupPaginationLimit();
        $this->setupPaginationLimitStart();
    }

    private function setupPaginationLimit()
    {
        if ($this->videosPerPage > 0) {
            $this->limit = $this->videosPerPage;
        } else {
			$mainframe = JFactory::getApplication();
            $this->limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        }
    }

    private function setupPaginationLimitStart()
    {
        if ($this->videosPerPage > 0) {
            $this->limitstart = JRequest::getVar('limitstart', 0);
        } else {
			$mainframe = JFactory::getApplication();
			$option = JRequest::getCmd('option');
			
            $this->limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', JRequest::getVar('limitstart'), 'int');
        	$this->limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        }
    }

    private function buildBaseCacheId()
    {
        $baseCacheId = "videos_"
            .$this->videoOrder
            ."_".implode(',', is_array($this->videoCategories) ? $this->videoCategories : array($this->videoCategories))
            ."_".$this->videoFilter
            ."_".$this->videosPerPage
            ."_".$this->limit
            ."_".$this->limitstart
            ."_".$this->userId;

        return $baseCacheId;
    }
}