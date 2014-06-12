
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

class JVideoControllerCategories extends JVideoController
{
	private $view;

	public function __construct()
	{
		parent::__construct();

		$this->view = $this->getView('categories', 'html');
		$model = $this->getModel('categories');
		$this->view->setModel($model, true);
	}

	public function display($cachable = false, $urlparams = Array())
	{
		switch($this->getTask())
		{
			case "add":
				$this->displayAddView();
				break;
			case "edit":
				$this->displayEditView();
				break;
			case "remove":
				$this->removeCategory();
				$this->displayDefaultView();
				break;
			case "save":
				$this->saveChanges();
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

	private function setupDefaultView()
	{

	}

    private function displayAddView()
    {
        $this->setupAddView();
		$this->view->display('add');
    }

	private function setupAddView()
	{
		JRequest::setVar('active', true);
	}

	private function displayEditView()
    {
        $this->setupEditView();
		$this->view->display('edit');
    }

	private function setupEditView()
	{
		$model = $this->getModel('categories');

		$categoryId = JRequest::getVar('cid');

		if (is_array($categoryId) && count($categoryId) > 0) {
			$categoryId = (int) $categoryId[0];
		} else if ($categoryId == "") {
            throw new JVideo_Exception("Missing category ID");
        } else {
            $categoryId = (int) $categoryId;
        }

		$category = $model->getCategoryById($categoryId);
		
		JRequest::setVar('categoryId', $categoryId);
		JRequest::setVar('categoryName', $category->name);
	}

	public function saveChanges()
	{
		$result = false;

		switch (JRequest::getVar('strategy'))
		{
			case "add":
				$result = $this->saveAddCategory();
				break;
			case "edit":
				$result = $this->saveEditCategory();
				break;
			default:
				break;
		}

		JRequest::setVar("saveResult", $result);
	}

	private function saveAddCategory()
	{
        $model = $this->getModel('categories');
        $inputFilter = JFilterInput::getInstance();

        return $model->addCategory(
            $inputFilter->clean(JRequest::getVar('name')),
            JRequest::getInt('categories'));
	}

	private function saveEditCategory()
	{
        $model = $this->getModel('categories');
        $inputFilter = JFilterInput::getInstance();

        return $model->editCategory(
            JRequest::getInt('id'),
            $inputFilter->clean(JRequest::getVar('name')),
            JRequest::getInt('active'),
            JRequest::getInt('categories'));
	}

	public function removeCategory()
	{
        $model = $this->getModel('categories');

        return $model->removeCategory(JRequest::getVar('cid'));
	}
}
?>