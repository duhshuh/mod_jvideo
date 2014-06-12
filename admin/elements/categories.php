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
 
require_once JPATH_ADMINISTRATOR . '/components/com_jvideo/library/jvideo/JVideo.php';

class JFormFieldCategories extends JFormField
{
    protected $type = 'categories';

    protected function getInput()
    {
		$attributes = self::getAttributes($this->element);
		$categories = self::getCategoryOptions($this->element);
		
		return JHTML::_('select.genericlist', $categories, $this->name . '[]', $attributes, 'id', 'title', $this->value, $this->id);
	}
	
	private static function getAttributes($node)
	{
		$size = self::getOption($node, 'size', 5);
		$class = self::getOption($node, 'class', 'inputbox');
		$multi = self::getOption($node, 'multi', true);
		
		$attributes = ' size="' . $size . '" class="' . $class . '"';
		if ($multi)
		{
			$attributes .= ' multiple="multiple"';
		}
		
		return $attributes;
	}
	
    private static function getCategoryOptions($node)
    {
        $includeAllOption = self::getOption($node, 'includeall', true);
        $categories = array();

        if ($includeAllOption)
        {
            $categories[] = array('id' => -1, 'title' => '- ALL -');
        }

        $categoryTree = self::getCategories();

        $iterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($iterator as $category) {
            $categories[] = array(
                'id' => $category->id,
                'title' => self::getIndentationByLevel($category->level) . $category->name
            );
        }
		
		return $categories;
	}
	
	private static function getOption($node, $name, $default)
	{
		$value = $node->attributes()->$name;
		if (strlen($value) <= 0) return $default;
		return $value;
	}

    private static function getIndentationByLevel($level)
    {
        $indentation = '.....';

        $indent = '';
		for ($i = 1; $i < $level; $i++) {
			$indent .= $indentation;
		}
		return $indent . ' ';
    }

    private static function getCategories()
    {
		$repository = JVideo_CategoryTreeRepositoryFactory::create();
        return $repository->getCategoryTree();
    }
}