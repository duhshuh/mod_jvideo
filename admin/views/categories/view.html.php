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

class JVideoViewCategories extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->setupInterface();
		
		$this->setupLayout($tpl);

		parent::display($tpl);
	}

	public function setupInterface()
	{
		JToolbarHelper::title( JText::_( 'Categories' ), 'categories' );
		
		$this->verifyAccountExists();
		$this->addStyles();
		$this->addScripts();
	}

	private function setupLayout($tpl)
	{
		switch ($tpl)
		{
			case "add":
				$this->displayAddLayout();
				break;
			case "edit":
				$this->displayEditLayout();
				break;
			case "save":
			default:
				$this->displayDefaultLayout();
				break;
		}
	}

	public function displayDefaultLayout()
	{
		$this->setupDefaultToolbar();
		$this->setupCategories();
	}

	public function displayAddLayout()
	{
		$this->setupAddToolbar();
		$this->setupCategories();
	}

	public function displayEditLayout()
	{
		$this->setupEditToolbar();
		$this->setupExistingValues();
	}

	public function setupCategories()
	{
		$categories = $this->getCategories();

		if (!is_null($categories)) {
			$this->assign('categoriesCount', $categories->count());
		} else {
			$this->assign('categoriesCount', 0);
		}
        
		$this->assignRef('categories', $categories);
	}

	private function getCategories()
	{
		$model = $this->getModel("categories");
		
		return $model->getCategories();
	}

	private function addStyles()
	{
        JVideo2_AssetManager::includeAdminCoreCss();
	}

	private function addScripts()
	{

	}

	public function setupDefaultToolbar()
	{
    	JToolbarHelper::addNew('add');

		JToolbarHelper::deleteList(JText::_('JV_CATEGORIES_CONFIRM_DELETE'));
	}

	public function setupAddToolbar()
	{
		JToolbarHelper::save();
		JToolbarHelper::cancel();
	}

	public function setupEditToolbar()
	{
		JToolbarHelper::save();
		JToolbarHelper::cancel();
	}

	private function verifyAccountExists()
	{
		$model = $this->getModel("categories");

		$verifyAccountExists = $model->verifyAccountExists();
		
		$this->assignRef('verifyAccountExists', $verifyAccountExists);
	}

	private function setupExistingValues()
	{
        $model = $this->getModel("categories");
        $inputFilter = JFilterInput::getInstance();

		$categoryId = JRequest::getInt('categoryId');
		$categoryName = $inputFilter->clean(JRequest::getVar('categoryName'));
		$categories = $model->getCategories();

		$this->assignRef('categoryId', $categoryId);
		$this->assignRef('categoryName', $categoryName);
        $this->assignRef('categories', $categories);
	}

	private function getParentCategoryIdByCategoryId($categoryId)
	{
		$model = $this->getModel('categories');

		return $model->getParentCategoryIdByCategoryId($categoryId);
	}
}