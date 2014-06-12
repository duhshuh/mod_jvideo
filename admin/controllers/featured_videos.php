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

jimport('joomla.application.component.controller');

/**
 * JVideo Controller
 *
 * @package		JVideo
 * @subpackage	Component
 * @since 1.5
 */
class JVideoControllerFeatured_Videos extends JVideoController
{
	function __construct()
	{
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = Array())
	{
		$application = JFactory::getApplication();

		switch($this->getTask())
		{
			default:
				$model = $this->getModel('featured_videos');
				$this->setup_featured_video_variables();
				break;
			case "do_remove_featured_video":
				$model = $this->getModel('featured_videos');
				$model->do_remove_featured_video();
				$this->setup_featured_video_variables();
				break;
			case "do_reorder_featured_video":
				$model = $this->getModel('featured_videos');

				if ($model->do_reorder_featured_video())
				{
					$application->enqueueMessage(JText::_('JV_ORDER_SAVED'));
				}
				else
				{
					$application->enqueueMessage(JText::_('JV_INVALID_CHOICE'), 'warning');
				}

				$this->setup_featured_video_variables();
				break;
		}
		
		parent::display();
	}
	
	function setup_featured_video_variables()
	{
		$model = $this->getModel( 'featured_videos' );
	  	// Get data from the model
	 	$pagination = $model->get_featured_video_pagination();
	 	$items = $model->get_featured_videos();
	 	JRequest::setVar('pagination', $pagination);
	 	JRequest::setVar('items', $items);
		JRequest::setVar('view', 'featured_videos');
	}
	
	
}
?>