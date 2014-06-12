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

require_once dirname(__FILE__) . '/JVideo_NestedElement.php';
require_once dirname(__FILE__) . '/JVideo_Exception.php';

class JVideo_NestedSet
{
	public $elements;

	public function __construct($elements = array())
	{
		$this->elements = $elements;

		$this->validateNestValues();
	}

	public function addRoot(JVideo_NestedElement $element)
	{
		list($nestLeft, $nestRight) = $this->getNextRootNestValues();

		$element->nestLeft = $nestLeft;
		$element->nestRight = $nestRight;
		
		return $this->insertElement($element);
	}

	public function addChild(JVideo_NestedElement $parent, JVideo_NestedElement $child)
	{
		list($nestLeft, $nestRight) = $this->getNextChildNestValues($parent);

		$child->nestLeft = $nestLeft;
		$child->nestRight = $nestRight;

		return $this->insertElement($child);
	}

	public function getRootElements()
	{
		$rootElements = array();

		foreach ($this->elements as $element)
		{
			if ($this->isRootElement($element))
				$rootElements[] = $element;
		}

		return $rootElements;
	}

	public function getChildElements(JVideo_NestedElement $parent)
	{
		$children = array();

		foreach ($this->elements as $element)
		{
			if ($this->isDirectParentOf($parent, $element))
				$children[] = $element;
		}

		return $children;
	}

    public function getNestedNodes(JVideo_NestedElement $node)
    {
        $nodes = array();

        foreach ($this->elements as $element)
        {
            if (  ($element->nestLeft >= $node->nestLeft)
                &&($element->nestRight <= $node->nestRight))
            {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

	public function getAllElements()
	{
		return $this->elements;
	}

	public function setParent(JVideo_NestedElement $child, JVideo_NestedElement $newParent)
	{
		$this->throwExceptionIfInvalidParent($child, $newParent);

		$this->removeElement($child);

		list($nestLeft, $nestRight) = $this->getNextChildNestValues($newParent);
		$child->nestLeft = $nestLeft;
		$child->nestRight = $nestRight;

		return $this->insertElement($child);
	}

	public function moveParent(JVideo_NestedElement $child, JVideo_NestedElement $newParent)
	{
		$grandchildren = $this->getChildElements($child);

		$movedChild = $this->setParent($child, $newParent);

		foreach ($grandchildren as $grandchild) {
			$this->moveParent($grandchild, $movedChild);
		}
	}

	public function moveParentToRoot(JVideo_NestedElement $parent)
	{
		$children = $this->getChildElements($parent);

		$this->removeElement($parent);
		$newParent = $this->addRoot($parent);

		foreach ($children as $child) {
			$this->moveParent($child, $newParent);
		}
	}

	public function getDirectParentOf(JVideo_NestedElement $child)
	{
		if ($this->isRootElement($child)) return null;

		foreach ($this->elements as $element) {
			if ($this->isDirectParentOf($element, $child)) {
				return $element;
			}
		}
	}

	public function isRootElement(JVideo_NestedElement $element)
	{
		foreach ($this->elements as $c)
		{
			if ($this->isParentOf($c, $element))
				return false;
		}
		return true;
	}

	public function isParentOf(JVideo_NestedElement $parent, JVideo_NestedElement $child)
	{
		return $parent->nestLeft < $child->nestLeft
				&& $parent->nestRight > $child->nestRight;
	}

	public function isDirectParentOf(JVideo_NestedElement $parent, JVideo_NestedElement $child)
	{
		if (!$this->isParentOf($parent, $child)) return false;

		foreach ($this->elements as $element)
		{
			if ($this->isParentOf($parent, $element) && $this->isParentOf($element, $child))
				return false;
		}

		return true;
	}

	public function getNextRootNestValues()
	{
		$maxNestValue = 0;
		foreach ($this->elements as $element)
		{
			if ($element->nestRight > $maxNestValue)
				$maxNestValue = $element->nestRight;
		}
		return array($maxNestValue + 1, $maxNestValue + 2);
	}

	public function getNextChildNestValues(JVideo_NestedElement $parent)
	{
		return array($parent->nestRight, $parent->nestRight + 1);
	}

	private function insertElement(JVideo_NestedElement $newElement)
	{
		$nestDelta = $newElement->nestRight - $newElement->nestLeft + 1;
		foreach ($this->elements as $element)
		{
			if ($element->nestLeft >= $newElement->nestLeft)
				$element->nestLeft += $nestDelta;
			if ($element->nestRight >= $newElement->nestLeft)
				$element->nestRight += $nestDelta;
		}
		$this->elements[] = $newElement;
		
		return $newElement;
	}

	public function removeElementAndChildren(JVideo_NestedElement $remove)
	{
		$removed = array($remove);

		$children = $this->getChildElements($remove);

		foreach ($children as $child)
		{
			$removed = array_merge($removed, $this->removeElementAndChildren($child));
		}

		$this->removeElement($remove);

		return $removed;
	}

	public function removeElement(JVideo_NestedElement $remove)
	{
		foreach ($this->elements as $element)
		{
			if ($element != $remove)
			{
				$this->updateNestValuesToRemoveElement($element, $remove);
			}
		}
		$this->elements = $this->removeArrayElement($this->elements, $remove);
	}

	
	private function updateNestValuesToRemoveElement(JVideo_NestedElement $elementToStay, JVideo_NestedElement $elementToRemove)
	{
		if ($this->isParentOf($elementToRemove, $elementToStay))
		{
			$elementToStay->nestLeft--;
			$elementToStay->nestRight--;
		}
		else if ($this->isParentOf($elementToStay, $elementToRemove))
		{
			$elementToStay->nestRight -= 2;
		}
		else if ($elementToStay->nestLeft > $elementToRemove->nestRight)
		{
			$elementToStay->nestLeft -= 2;
			$elementToStay->nestRight -= 2;
		}
	}


	/**
	 * @param array $array
	 * @param mixed $elementToRemove
	 * @return array
	 */
	private function removeArrayElement($array, $elementToRemove)
	{
		$newArray = array();
		foreach ($array as $element)
		{
			if ($element != $elementToRemove) $newArray[] = $element;
		}
		return $newArray;
	}

	
	private function throwExceptionIfInvalidParent(JVideo_NestedElement $child, JVideo_NestedElement $parent)
	{
		if ($child == $parent)
			throw new JVideo_InvalidNestedElementParentException('Can\'t set element as own parent');

		if ($this->isParentOf($child, $parent))
			throw new JVideo_InvalidNestedElementParentException('Can\'t set child element as parent');
	}


	private function validateNestValues()
	{
		if (!$this->areNestLeftValuesLessThanNestRightValues())
			throw new JVideo_InvalidNestedSetStateException(JVideo_InvalidNestedSetStateException::CODE_NEST_LEFT_GREATER_THAN_NEST_RIGHT);

		if (!$this->isMaxNestRightValueValid()) {
			throw new JVideo_InvalidNestedSetStateException(JVideo_InvalidNestedSetStateException::CODE_MAX_NEST_RIGHT_VALUE_INVALID);
		}
	}


	private function areNestLeftValuesLessThanNestRightValues()
	{
		foreach ($this->elements as $element)
		{
			if ($element->nestLeft >= $element->nestRight)
				return false;
		}
		return true;
	}


	protected function isMaxNestRightValueValid()
	{
		return $this->getMaxNestRightValue() == (count($this->elements) * 2);
	}


	protected function getMaxNestRightValue()
	{
		$max = 0;
		foreach ($this->elements as $element)
		{
			if ($element->nestRight > $max)
				$max = $element->nestRight;
		}
		return $max;
	}
}
