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

require_once dirname(__FILE__) . '/JVideo_NestedSet.php';

class JVideo_CategoryTree extends JVideo_NestedSet
{
	public function __construct($categories = array())
	{
		parent::__construct($this->removeInactiveCategories($categories));
	}

	public function updateCategory(JVideo_Category $old, JVideo_Category $new)
	{
		foreach ($this->elements as &$node) {
			if ($node == $old) {
				$node->name = $new->name;
				$node->active = $new->active;
				return true;
			}
		}

		return false;
	}

    public function getRootCategories()
    {
        $rootCategories = array();

		foreach ($this->elements as $element)
		{
			if ($this->isRootElement($element))
				$rootCategories[] = $element;
		}

		return $rootCategories;
    }

    public function getChildCategories(JVideo_Category $category)
    {
        return $this->getChildElements($category);
    }

    public function getNestedCategoryIds(JVideo_Category $category)
    {
        $nodes = array();
        
        $nodes[] = $category->id;

        foreach ($this->elements as $element)
        {
            if (  ($element->nestLeft > $category->nestLeft)
                &&($element->nestRight < $category->nestRight))
            {
                $nodes[] = $element->id;
            }
        }

        return $nodes;
    }

    public function getSiblingCategories(JVideo_Category $category)
    {
        if ($this->isRootElement($category))
            return $this->getRootCategories();
        else
            return $this->getChildCategories($this->getDirectParentOf($category));
    }

    public function count()
    {
        return count($this->elements);
    }

    public function getAncestry(JVideo_Category $category, array &$ancestry = null)
    {
        if (is_null($category))
            return array();
        
        if (!$this->isRootElement($category))
            $this->getAncestry($this->getDirectParentOf($category), $ancestry);

        $ancestry[] = $category;

        return $ancestry;
    }

    private function removeInactiveCategories(&$categories)
    {
        foreach ($categories as $key => $category)
            if ($category->active == 0)
                unset($categories[$key]);

        return $categories;
    }
}
