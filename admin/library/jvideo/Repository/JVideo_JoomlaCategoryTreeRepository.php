<?php
/*
 *    @package    JVideo
 *    @subpackage Library
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
defined('_JEXEC') or die("Cannot use JVideo Joomla repository outside of Joomla");

require_once dirname(__FILE__) . '/../JVideo_ICategoryTreeRepository.php';
require_once dirname(__FILE__) . '/../JVideo_Category.php';
require_once dirname(__FILE__) . '/../JVideo_CategoryCollection.php';
require_once dirname(__FILE__) . '/../JVideo_CategoryFactory.php';
require_once dirname(__FILE__) . '/../JVideo_CategoryTree.php';
require_once dirname(__FILE__) . '/../JVideo_CategoryTreeIterator.php';
require_once dirname(__FILE__) . '/../JVideo_Exception.php';
require_once dirname(__FILE__) . '/../../jvideo2/Database/JVideo2_DbBatch.php';

class JVideo_JoomlaCategoryTreeRepository implements JVideo_ICategoryTreeRepository
{
    public function update(JVideo_CategoryTree $categoryTree)
	{
		$db = JFactory::getDBO();

        // to avoid tree conflicts... prepare SQL statements before executing
		$sql_delete = $this->getCategoryTreeDeleteSql($categoryTree);
        $sql_preinsert = $this->getCategoryTreePreInsertSql($categoryTree);
        $sql_persist = $this->getCategoryArrayPersistSql($categoryTree, $categoryTree->getRootElements());
        
        if (strlen($sql_delete) > 0) {
			$db->setQuery($sql_delete);
			$db->execute();
		}

        if (strlen($sql_preinsert) > 0) {
        	JVideo2_DbBatch::execute($db, $sql_preinsert);
        }

		if (strlen($sql_persist) > 0)
		{
			try {
				JVideo2_DbBatch::execute($db, $sql_persist);
				return true;
			} catch (DbBatchException $e) {
				return false;
			}
		}
		else
		{
			return false;
		}
	}

    public function getCategoryTree()
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT node.`name`, node.`id`, node.nestLeft, node.nestRight, COUNT(`parent`.`id`) AS `level`, `node`.`active` ' .
				'FROM #__jvideo_categories AS node, ' .
				'#__jvideo_categories AS parent ' .
				'WHERE `node`.nestLeft BETWEEN `parent`.nestLeft AND `parent`.nestRight ' .
				'AND `node`.`active` = 1 ' .
				'AND `parent`.`active` = 1 ' .
				'GROUP BY `node`.`id` ' .
				'ORDER BY `node`.nestLeft;';
		$db->setQuery($sql);

		$rows = $db->loadObjectList();

		return $this->getCategoryTreeByDataRows($rows);
	}

	private function getCategoryTreeByDataRows($rows)
	{
		$categories = $this->getCategoryCollectionByDataRows($rows);

		if ($categories->count() > 0) {
			return new JVideo_CategoryTree($categories->getCollection());
		} else {
			return new JVideo_CategoryTree();
		}
	}

    private function getCategoryCollectionByDataRows($rows)
	{
		$categories = new JVideo_CategoryCollection();

        if (count($rows) > 0) {
            foreach ($rows as $row)
            {
                $category = JVideo_CategoryFactory::create($row);

                $categories->add($category);
            }
        }
        
		return $categories;
	}
	
	public function getCategoryById($id)
	{
        $inputFilter = JFilterInput::getInstance();
		$db = JFactory::getDBO();
		$sql = 'select * from #__jvideo_categories ' .
				'where `id` = ' . $inputFilter->clean($id, 'INT') . ' and `active` = 1;';
		$db->setQuery($sql);

		$row = $db->loadObject();

		if (!is_null($row)) {
			return JVideo_CategoryFactory::create($row);
		} else {
			return null;
		}
	}

	public function getParentCategoryById($categoryId)
	{
        $inputFilter = JFilterInput::getInstance();
		$db = JFactory::getDBO();
		$sql = 'select * from #__jvideo_categories AS `node` ' .
				', #__jvideo_categories AS `parent` ' .
				'where `node`.nestLeft BETWEEN `parent`.nestLeft AND `parent`.nestRight ' .
				'AND `node`.`id` = ' . $inputFilter->clean($categoryId, 'INT') . ' ' .
				'AND `node`.`id` <> `parent`.`id` ' .
				'AND `parent`.`active` = 1 ' .
				'AND `node`.`active` = 1 ' .
				'ORDER BY `parent`.nestLeft DESC LIMIT 1';
				
		$db->setQuery($sql);

		$row = $db->loadObject();

		if (!is_null($row)) {
			return JVideo_CategoryFactory::create($row);
		} else {
			return null;
		}
	}

	private function getCategoryArrayPersistSql(JVideo_CategoryTree $categoryTree, array $categories)
	{
		$sql = '';
		foreach ($categories as $category)
		{
			$sql .= $this->getCategoryPersistSql($category);

			$sql .= $this->getCategoryArrayPersistSql($categoryTree, $categoryTree->getChildElements($category));
		}
		return $sql;
	}

	private function getCategoryPersistSql(JVideo_Category $category)
	{
        return $this->getCategoryUpdateSql($category);
	}

	private function getCategoryTreePreInsertSql(JVideo_CategoryTree $categoryTree)
	{
		$db = JFactory::getDBO();
        $sql = "";

        $existingCategoryTree = $this->getCategoryTree();
        $rootCategories = $existingCategoryTree->getRootElements();
		$ids = $this->getCategoryIdArray($existingCategoryTree, $rootCategories);

        $categoryTreeIterator = new JVideo_CategoryTreeIterator($categoryTree);
        $inputFilter = JFilterInput::getInstance();

        foreach ($categoryTreeIterator as $category) {
            reset($ids);

            foreach ($ids as $id) {
                if ($id == $category->id) {
                    continue 2;
                }
            }

            $sql .= 'insert into #__jvideo_categories (`id`, `name`, `nestLeft`, `nestRight`, `active`) ' .
                    'values (' .
                    $inputFilter->clean($category->id, 'INT') . ', ' .
					$db->quote($inputFilter->clean($category->name)) . ', ' .
					$inputFilter->clean($category->nestLeft, 'INT') . ', ' .
					$inputFilter->clean($category->nestRight, 'INT') . ', 1);';
        }

        return $sql;
	}

	private function getCategoryUpdateSql(JVideo_Category $category)
	{
		$db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();
		
		return 'update #__jvideo_categories ' .
				'set `name` = ' . $db->quote($inputFilter->clean($category->name)) . ', ' .
					'`nestLeft` = ' . $inputFilter->clean($category->nestLeft, 'INT') . ', ' .
					'`nestRight` = ' . $inputFilter->clean($category->nestRight, 'INT') . ', ' .
					'`active` = 1 ' .
				'where `id` = ' . $inputFilter->clean($category->id, 'INT') . ';';
	}

	private function getCategoryTreeDeleteSql(JVideo_CategoryTree $categoryTree)
	{
		$db = JFactory::getDBO();

		$rootCategories = $categoryTree->getRootElements();
		$ids = $this->getCategoryIdArray($categoryTree, $rootCategories);

		$sql = 'update #__jvideo_categories ' .
				'set `active` = 0 ' .
				'where `active` = 1 ';

		if (count($ids) > 0)
		{
			$sql .= ' and `id` not in (' . implode(',', $ids) . ')';
		}

		return $sql . ';';
	}

	private function getCategoryIdArray(JVideo_CategoryTree $categoryTree, array $categories)
	{
		$ids = array();
		foreach ($categories as $category)
		{
			if ($category->id != null)
				$ids[] = (int) $category->id;

			$children = $categoryTree->getChildElements($category);
			$ids = array_merge($ids, $this->getCategoryIdArray($categoryTree, $children));
		}
		return $ids;
	}
}
