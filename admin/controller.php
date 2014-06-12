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

class JVideoController extends JControllerLegacy
{	
	public $jvConfig;
	
	public function __construct()
	{
		if ($this->checkInstallStatus()) {
			parent::__construct();
			$this->setupMenuItems();
		}
	}
	
	private function setupMenuItems()
	{
		$viewName = JRequest::getVar('view', '');
		
		JToolBarHelper::title( JText::_( 'JV_SETTINGS' ), 'JVideo' );
				
		JSubMenuHelper::addEntry(JText::_('JV_HOME'), 'index.php?option=com_jvideo', $viewName == "");
		JSubMenuHelper::addEntry(JText::_('JV_VIDEOS'), 'index.php?option=com_jvideo&view=videos', $viewName == 'videos');
        JSubMenuHelper::addEntry(JText::_('JV_CATEGORIES'), 'index.php?option=com_jvideo&view=categories', $viewName == 'categories');
        JSubMenuHelper::addEntry(JText::_('JV_FEATURED_VIDEOS'), 'index.php?option=com_jvideo&view=featured_videos', $viewName == 'featured_videos');
        JSubMenuHelper::addEntry(JText::_('JV_CONFIGURATION'), 'index.php?option=com_jvideo&view=configuration', $viewName == 'configuration');
        JSubMenuHelper::addEntry(JText::_('JV_ACCOUNT_SETUP'), 'index.php?option=com_jvideo&view=client_setup', $viewName == 'client_setup');
		JSubMenuHelper::addEntry(JText::_('JV_CONSOLE'), 'index.php?option=com_jvideo&view=console', $viewName == 'console');
        JSubMenuHelper::addEntry(JText::_('JV_HELP'), 'index.php?option=com_jvideo&view=help', $viewName == 'help');
	}

	private function checkInstallStatus()
	{
		if ($this->configTableExists()) {
			$this->setupConfig();
			if ($this->jvConfig->installStatus != 'complete') {
				$view = JRequest::getVar('view', '');
				if ($view != 'install')
				{
					$task = $this->jvConfig->installStatus != '' ? $this->jvConfig->installStatus : 'step1';

					header("Location: " . JURI::base() . "index.php?option=com_jvideo&view=install&task=" . $task);
					exit();
				}
			}
		} else {
			header("Location: " . JURI::base() . "index.php?option=com_jvideo&view=install&task=step1");
			exit();
		}

		return true;
	}

	private function configTableExists()
	{
		$db = JFactory::getDBO();

		$tableList = $db->getTableList();

		foreach ($tableList as $table) {
			if (false !== stristr($table, 'jvideo_config')) {
				return true;
			}
		}

		return false;
	}

	private  function setupConfig()
	{
		$this->jvConfig = JVideo_Factory::getConfig();
		JRequest::setVar('jvConfig', $this->jvConfig);
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
			echo JText::_("JV_DASHBOARD_SYNC_COMPLETE");
		} else {
			echo $result;
		}
	}
}
