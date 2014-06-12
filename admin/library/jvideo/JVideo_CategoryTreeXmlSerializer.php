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

require_once dirname(__FILE__) . '/JVideo_CategoryFactory.php';
require_once dirname(__FILE__) . '/JVideo_CategoryTree.php';

class JVideo_CategoryTreeXmlSerializer
{
	/**
	 * @param CategoryTree $categoryTree
	 * @return string
	 */
	public function serialize(JVideo_CategoryTree $categoryTree)
	{
		$rootCategories = $categoryTree->getRootElements();

		$document = new DOMDocument();

		$categoriesNode = $document->createElement('categories');
		$document->appendChild($categoriesNode);
		
		$this->addCategoriesToXmlNode($categoryTree, $rootCategories, $document, $categoriesNode);

		return $document->saveXML($categoriesNode);
	}

	private function addCategoriesToXmlNode(JVideo_CategoryTree $categoryTree, array $categories, DOMDocument $document, DOMElement $parentXmlNode)
	{
		foreach ($categories as $category)
		{
			$categoryNode = $this->getCategoryXmlNode($document, $category);
			$parentXmlNode->appendChild($categoryNode);

			$children = $categoryTree->getChildElements($category);
			$this->addCategoriesToXmlNode($categoryTree, $children, $document, $categoryNode);
		}
	}

	private function getCategoryXmlNode(DOMDocument $document, JVideo_Category $category)
	{
		$categoryNode = $document->createElement('category');
        if (!is_null($category->id))
            $categoryNode->setAttribute('id', $category->id);
		$categoryNode->setAttribute('name', $category->name);
		return $categoryNode;
	}


	/**
	 * @param Account $account
	 * @param string $xml
	 * @return CategoryTree
	 */
	public function unserialize($xml)
	{
		$categoryTree = new JVideo_CategoryTree();

		$document = new DOMDocument();
		$document->loadXML($xml);

		$categoriesNodeList = $document->getElementsByTagName('categories');
		$categoriesNode = $categoriesNodeList->item(0);

		$this->addNodeChildrenToCategoryTree($categoryTree, $categoriesNode);

		return $categoryTree;
	}

	private function addNodeChildrenToCategoryTree(JVideo_CategoryTree $categoryTree, DOMElement $node, JVideo_Category $parentCategory = null)
	{
		$childNodes = $this->getChildCategoryNodes($node);

		foreach ($childNodes as $childNode)
		{
			$category = $this->createCategoryFromDomElement($childNode);
			$this->addCategoryToTree($categoryTree, $category, $parentCategory);
			$this->addNodeChildrenToCategoryTree($categoryTree, $childNode, $category);
		}
	}

	private function getChildCategoryNodes(DOMElement $node)
	{
		$childNodes = array();

		if ($node->hasChildNodes())
		{
			$childNode = $node->firstChild;
			do
			{
				if ($this->isCategoryNode($childNode))
				{
					$childNodes[] = $childNode;
				}
			} while (($childNode = $childNode->nextSibling) != null);
		}

		return $childNodes;
	}

	private function isCategoryNode(DOMNode $node)
	{
		return get_class($node) == 'DOMElement' && $node->tagName == 'category';
	}

	private function createCategoryFromDomElement(DOMElement $node)
	{
		$categoryId = intval($node->getAttribute('id'));
		$name = strval($node->getAttribute('name'));
        $nestLeft = intval($node->getAttribute('nestLeft'));
        $nestRight = intval($node->getAttribute('nestRight'));
        $nestRight = intval($node->getAttribute('nestRight'));
        $active = intval($node->getAttribute('active'));

        $category = new JVideo_Category();
        $category->id = $categoryId;
        $category->name = $name;
        $category->nestLeft = $nestLeft;
        $category->nestRight = $nestRight;
        $category->active = $active;
        
		return $category;
	}

	private function addCategoryToTree(JVideo_CategoryTree $categoryTree, JVideo_Category $category, JVideo_Category $parent = null)
	{
		if ($parent == null)
			$categoryTree->addRoot($category);
		else
			$categoryTree->addChild($parent, $category);
	}
}