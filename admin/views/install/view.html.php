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

jimport( 'joomla.application.component.view' );
jvimport2('Web.AssetManager');

class JVideoViewInstall extends JViewLegacy
{
    public function display($step = "step1")
    {        
        $this->assignStepTasksVar();
        $this->$step();

        $this->setupDisplay($step);

        parent::display($step);
    }

    public function assignStepTasksVar()
    {
        $stepTasks = JRequest::getVar('stepTasks');

        $this->assignRef('stepTasks', $stepTasks);
    }

    private function setupDisplay($step)
    {
        JVideo2_AssetManager::includeAdminCoreCss();
        JVideo2_AssetManager::includeJQuery();
        JVideo2_AssetManager::includeAdminCoreJs();

        $this->replaceSubMenuWithInstallSubMenu($step);

        JToolBarHelper::title( JText::_( 'JVideo Installation' ), 'jvideo' );
    }
    
    public function step1()
    {
    }

    public function step2()
    {
    }

    public function step3()
    {
    }

    public function step4()
    {
    }

    public function step5()
    {
    }

    private function replaceSubMenuWithInstallSubMenu($step)
    {
        $submenu = JToolBar::getInstance('submenu');
        //$submenu->_buttons = array();

        $submenu->appendButton(JText::_('JV_INSTALL_STEP_1_DESC'), '#', $step == 'step1');
        $submenu->appendButton(JText::_('JV_INSTALL_STEP_2_DESC'), '#', $step == 'step2');
        $submenu->appendButton(JText::_('JV_INSTALL_STEP_3_DESC'), '#', $step == 'step3');
        $submenu->appendButton(JText::_('JV_INSTALL_STEP_4_DESC'), '#', $step == 'step4');
        $submenu->appendButton(JText::_('JV_INSTALL_STEP_5_DESC'), '#', $step == 'step5');
    }
}