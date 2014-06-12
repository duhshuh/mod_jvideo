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

require_once(dirname(__FILE__) . '/../../infin-lib.php');
require_once(dirname(__FILE__) . '/../JVideo_Category.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryTree.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');
require_once(dirname(__FILE__) . '/../Repository/JVideo_JoomlaCategoryTreeRepository.php');
require_once(dirname(__FILE__) . '/../Repository/JVideo_WarpCategoryTreeRepository.php');

class JVideo_CategoryTreeService
{
    private $localRepository = null;
    private $remoteRepository = null;

    public function __construct()
    {
        $this->localRepository = new JVideo_JoomlaCategoryTreeRepository();
        $this->remoteRepository = new JVideo_WarpCategoryTreeRepository();
    }

    public function addCategory(JVideo_Category $category, JVideo_Category $parentCategory = null)
    {
        $categoryTree = $this->localRepository->getCategoryTree();

		if (!is_null($parentCategory)) {
			$categoryTree->addChild($parentCategory, $category);
		} else {
			$categoryTree->addRoot($category);
		}

        $categoryTree = $this->remoteRepository->update($categoryTree);
        
        return $this->localRepository->update($categoryTree);
    }

    public function editCategory($categoryId, $name, $active, $parentCategoryId)
    {
        $categoryTree = $this->localRepository->getCategoryTree();
        $newCategory = $this->getCategoryFromCategoryTreeById($categoryTree, $categoryId);
        
        $oldCategory = $newCategory;

        $inputFilter = JFilterInput::getInstance();
        $newCategory->name = $inputFilter->clean($name);

        if (!$categoryTree->updateCategory($oldCategory, $newCategory)) {
            throw new JVideo_Exception("Could not update category!");
        }

		if ($parentCategoryId > 0 ) {
            $newParent = $this->getCategoryFromCategoryTreeById($categoryTree, $parentCategoryId);

            if (!$categoryTree->isDirectParentOf($newParent, $newCategory)) {
                $categoryTree->moveParent($newCategory, $newParent);
            }
		} else {
            if (!$categoryTree->isRootElement($newCategory)) {
                $categoryTree->moveParentToRoot($newCategory);
            }
		}

		$categoryTree = $this->remoteRepository->update($categoryTree);

        return $this->localRepository->update($categoryTree);
    }

    public function removeCategory(JVideo_Category $category)
    {
        $categoryTree = $this->localRepository->getCategoryTree();

		$categoryTreeIterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($categoryTreeIterator as $categoryTreeNode) {
            if ($categoryTreeNode->id == $category->id) {
                $categoryTree->removeElementAndChildren($categoryTreeNode);
                break;
            }
        }

        $categoryTree = $this->remoteRepository->update($categoryTree);

        return $this->localRepository->update($categoryTree);
    }

    public function removeCategories(JVideo_CategoryCollection $categories)
    {
        $categoryTree = $this->localRepository->getCategoryTree();
		$categoryTreeIterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($categories->getCollection() as $category) {
            foreach ($categoryTreeIterator as $categoryTreeNode) {
                if ($categoryTreeNode->id == $category->id) {
                    $categoryTree->removeElementAndChildren($categoryTreeNode);
                    break;
                }
            }
        }

		$categoryTree = $this->remoteRepository->update($categoryTree);

        return $this->localRepository->update($categoryTree);
    }

    private function getCategoryFromCategoryTreeById(JVideo_CategoryTree $categoryTree, $categoryId)
    {
        $iterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($iterator as $node) {
            if ($node->id == $categoryId) {
                return $node;
            }
        }

        return null;
    }
}
