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

jvimport('CategoryTreeIterator');

class JVideoCategory
{

    private function printCategoriesAsJavaScriptArray($categories)
    {
        if (is_null($categories))
            return null;

        $iterator = new JVideo_CategoryTreeIterator($categories);
        $existingCategoryNestLeft = $existingCategoryNestRight = $parentCategoryId = '';
        $prevCategoryId = $prevCategoryNestLeft = $prevCategoryNestRight = -1;

        $javascript = "<script type=\"text/javascript\">\n";

        foreach ($iterator as $category) {

            if ($existingCategoryId == $category->id) {
				$existingCategoryNestLeft = $category->nestLeft;
				$existingCategoryNestRight = $category->nestRight;
			}

            if ($category->id == $existingCategoryId) {
                if (($category->nestLeft > $prevCategoryNestLeft)
                    && ($category->nestRight < $prevCategoryNestRight)) {
                    $parentCategoryId = $prevCategoryId;
                }
            }

			$dropdownlist[] = array(
                'text' => $indent . $category->name,
                'value' => $category->id,
                'disable' => ($existingCategoryNestLeft <= $category->nestLeft
                           && $existingCategoryNestRight >= $category->nestRight ? 'disable' : ''));

            // Retain for parent check next iteration
            $prevCategoryId = $category->id;
            $prevCategoryNestLeft = $category->nestLeft;
            $prevCategoryNestRight = $category->nestRight;
		}

        $javascript .= "</script>\n";

        return $javascript;
    }

    private function printCategoryAsJavaScriptArray($category)
    {

    }

    private function printCategoriesAsDropDownList($listId, $categories)
    {
        $list = "<select name=\"" . $listId . "\">\n";

        foreach ($categories as $category) {
            $list .= "<option value=\"" . $category->id . "\">" . $category->name . "</option>\n";
        }

        $list .= "</select>\n";

        return $list;
    }

    public static function dropdownlist($categories, $existingCategoryId = -1, $ajaxUpdateId = 0)
	{
        if (is_null($categories))
            return null;

        $dropdownlist = array();
        $existingCategoryNestLeft = $existingCategoryNestRight = $parentCategoryId = '';
        $prevCategoryId = $prevCategoryNestLeft = $prevCategoryNestRight = -1;
        
		$dropdownlist[] = array('text' => JText::_('JV_SELECT_CATEGORY'), 'value' => '-1', 'disable' => '');

        $iterator = new JVideo_CategoryTreeIterator($categories);

		foreach ($iterator as $category) {
            $indent = self::getIndentationByLevel($category->level);

            if ($existingCategoryId == $category->id) {
				$existingCategoryNestLeft = $category->nestLeft;
				$existingCategoryNestRight = $category->nestRight;
			}

            if ($category->id == $existingCategoryId) {
                if (($category->nestLeft > $prevCategoryNestLeft)
                    && ($category->nestRight < $prevCategoryNestRight)) {    
                    $parentCategoryId = $prevCategoryId;
                }
            }

			$dropdownlist[] = array(
                'text' => $indent . $category->name,
                'value' => $category->id,
                'disable' => ($existingCategoryNestLeft <= $category->nestLeft
                           && $existingCategoryNestRight >= $category->nestRight ? 'disable' : ''));

            // Retain for parent check next iteration
            $prevCategoryId = $category->id;
            $prevCategoryNestLeft = $category->nestLeft;
            $prevCategoryNestRight = $category->nestRight;
		}

        if ($ajaxUpdateId)
            $attr = self::jsUpdateVideoCategories($ajaxUpdateId);
        else
            $attr = "";
        
    	return JHTML::_('select.genericlist', $dropdownlist, 'categories', $attr, 'value'
            , 'text', $parentCategoryId);
	}

    public static function videoManagerDropDownList($categories, $selectedCategoryId)
    {
        if (is_null($categories))
            return null;

		$dropdownlist[] = array('text' => JText::_('JV_SELECT_CATEGORY'), 'value' => '-1', 'disable' => '');

        $iterator = new JVideo_CategoryTreeIterator($categories);

		foreach ($iterator as $category) {
            $indent = self::getIndentationByLevel($category->level);

			$dropdownlist[] = array(
                'text' => $indent . $category->name,
                'value' => $category->id,
                'disable' => '');
		}

        $attr = 'onChange="'
                    . 'document.getElementById(\'category\').value = this.value;'
                    . 'this.form.submit();'
              . '" ';

    	return JHTML::_('select.genericlist', $dropdownlist, 'categories', $attr, 'value', 'text', $selectedCategoryId);
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

    private static function jsUpdateVideoCategories($videoId)
    {
        return "onChange=\"javascript:if (this.value != '-1') { JVideoAJAX.updateVideoCategories("
            . $videoId . ", document.getElementById('categories').value, 'true'); "
            . "this.value = -1; return true; }\"";
    }
}
