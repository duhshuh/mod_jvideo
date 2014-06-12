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

jimport('joomla.application.component.view');
jvimport2('Web.AssetManager');

class JVideoViewfeatured_videos extends JViewLegacy
{
	function __construct($config = null)
	{
		parent::__construct($config);
	}
	
	function display($tpl = null)
	{
        JVideo2_AssetManager::includeAdminCoreCss();
		
		$this->setToolBar();

        if ($this->checkIfAccountHasBeenSetup()) {
            $this->items = JRequest::getVar('items');
            $this->pagination = JRequest::getVar('pagination');
        }
        
		parent::display($tpl);
	}

    private function checkIfAccountHasBeenSetup()
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

	function setToolBar()
	{
		JToolBarHelper::title( JText::_( 'Featured Videos' ), 'featured_videos' );
	}
	
	
}