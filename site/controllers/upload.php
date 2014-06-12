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

jvimport('UserAllowedToUploadSpecification');
jvimport('MaxVideosPerUserSpecification');

class JVideoControllerUpload extends JVideoController
{
    public function display($cachable = false, $urlparams = Array())
    {
        $videoModel = $this->getModel('video');
        $categoriesModel = $this->getModel('categories');
        
        $view = $this->getView('upload', 'html');
        $view->setModel($videoModel, true);
        $view->setModel($categoriesModel);

        if (!$this->meetsUploadSpecifications())
            $view->setLayout('failure');

        $view->display();
    }

    public function add()
    {
        $videoModel = $this->getModel('video');
        $categoriesModel = $this->getModel('categories');

        $view = $this->getView('upload', 'html');
        $view->setModel($videoModel, 'video');
        $view->setModel($categoriesModel, 'categories');

        if ($this->meetsUploadSpecifications())
            $view->setLayout('add');
        else
            $view->setLayout('failure');

        $view->display();
    }

    public function save()
    {
        $videoModel = $this->getModel('video');
        $categoriesModel = $this->getModel('categories');

        $view = $this->getView('upload', 'html');
        $view->setModel($videoModel, 'video');
        $view->setModel($categoriesModel, 'categories');

        if ($this->meetsUploadSpecifications())
            if ($videoModel->save())
                $view->setLayout('success');
            else
                $view->setLayout('failure');
        else
            $view->setLayout('failure');
        
        $view->display();
    }

    private function meetsUploadSpecifications()
    {
        $userAllowedToUpload = new JVideo_UserAllowedToUploadSpecification();
        $maxVideosPerUser = new JVideo_MaxVideosPerUserSpecification();

        $user = JFactory::getUser();
        $videoCount = $this->getVideoCountByUserId($user->id);

        if ($userAllowedToUpload->isSatisfiedBy($user))
        {
            if ($maxVideosPerUser->isSatisfiedBy($videoCount))
            {
                return true;
            }
            else
            {
                JRequest::setVar('error', JText::_("JV_MAX_VIDEO_LIMIT_MET"));
                return false;
            }
        }
        else
        {
            JRequest::setVar('error', JText::_("JV_UPLOAD_ACCESS_DENIED"));
            return false;
        }
    }
}