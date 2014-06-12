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

class JVideoControllerEdit extends JVideoController
{
    public function display($cachable = false, $urlparams = array())
    {
        $videoModel = $this->getModel('video');
        $categoriesModel = $this->getModel('categories');

        $view = $this->getView('edit', 'html');
        $view->setModel($videoModel, 'video');
        $view->setModel($categoriesModel, 'categories');
        $view->setLayout('default');

        $view->display();
    }

    public function save()
    {
        $videoModel = $this->getModel('video');
        $categoriesModel = $this->getModel('categories');

        $result = $videoModel->save();
        JRequest::setVar('resultHeader', $result ? JText::_("JV_EDIT_VIDEO") : JText::_("JV_EDIT_VIDEO_ERROR"));
        JRequest::setVar('resultMessage', $result ? JText::_("JV_EDIT_SAVED") : JText::_("JV_EDIT_VIDEO_ERROR_MSG"));

        $view = $this->getView('edit', 'html');
        $view->setModel($videoModel, 'video');
        $view->setModel($categoriesModel, 'categories');
        $view->setLayout('result');
        
        $view->display();
    }
}