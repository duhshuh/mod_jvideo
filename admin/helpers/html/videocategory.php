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

class JVideoVideoCategory
{
    public static function videoCategoryList($categoryTree, $videoCategories = array())
    {
        $currLevel = 0;
        $prevLevel = 0;
        $output = "";
        $indent = "";
        $selected = "";

        $iterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($iterator as $category) {
            $output .= "<div class=\"jvideo-category-checkbox-container\">\n";
            $currLevel = $category->level;
            $selected = '';
            $indent = str_repeat('&nbsp;', 3 * $currLevel);

            if (!is_null($videoCategories)) {
                foreach ($videoCategories as $videoCategory) {
                    if ($videoCategory->categoryId == $category->id) {
                        $selected = "checked=\"checked\"";
                    }
                }
            }

            $output .= $indent . "<label for=\"videoCategory-" . $category->id . "\">"
                    . "<input type=\"checkbox\" value=\"" . $category->id . "\" " . "name=\"videoCategory[]\" "
                    . "id=\"videoCategory-" . $category->id . "\" " . $selected . " />\n"
                    . $category->name
                    . "</label>\n";

            $prevLevel = $currLevel;

            $output .= "</div>\n";
        }

        return $output;

        /*
        foreach ($videoCategories as $videoCategory) {
            $iterator = new JVideo_CategoryTreeIterator($categoryTree);

            foreach ($iterator as $category) {
                if ($category->id == $videoCategory->categoryId) {
                    $categoryAncestry = $categoryTree->getAncestry($category);
                    break;
                }
            }
            
            $iterator->rewind();

            $output .= "<div class=\"jvideo-category-checkbox-container\">\n";

            $ancestryCount = count($categoryAncestry);
            $ancestryCounter = 0;

            foreach ($categoryAncestry as $category) {
                $ancestryCounter++;

                $siblingCategories = $categoryTree->getSiblingCategories($category);

                $dropDownListItems ="<option value=\"\">- Select a Category -</option>\n";

                foreach ($siblingCategories as $siblingCategory) {
                    $dropDownListItems .= "<option value='".$siblingCategory->id."'"
                            .($siblingCategory->id == $category->id ? "selected=\"selected\"" : "")
                            .">" . $siblingCategory->name . "</option>\n";
                }

                $flagDisabled = $ancestryCounter != $ancestryCount ? " disabled=\"disabled\"" : "";

                $dropDownList = "<select name='videoCategory[]'".$flagDisabled.">\n"
                                .$dropDownListItems
                                ."</select>\n&nbsp;";

                $output .= $dropDownList;
            }

            $output .= "</div>\n";
        }

        $output .= "</div>\n";

        return $output;
        */

        /*$videoCategoryList = "";

  		foreach($videoCategories as $videoCategory) {
			$videoCategoryList .= "<strong>" . $videoCategory->breadcrumb . "</strong>"
                                . "&nbsp;<small><a href=\"#\" onClick=\""
                                . "javascript: JVideoAJAX.updateVideoCategories("
                                    . $videoCategory->videoId . ", " . $videoCategory->categoryId
                                    . ", 'false'); return false;\">"
                                . JText::_("JV_DETACH_VIDEO") . "</a></small><br />";
		}

        if ($videoCategoryList == "")
            return JText::_("JV_NO_CATEGORIES_ATTACHED");
        else
            return $videoCategoryList;*/
    }



    public function old_videoCategoryList($videoCategories)
    {
        if (!isset($videoCategories) || $videoCategories == null)
            return JText::_("JV_NO_CATEGORIES_ATTACHED");
        
        $videoCategoryList = "";

  		foreach($videoCategories as $videoCategory) {
			$videoCategoryList .= "<strong>" . $videoCategory->breadcrumb . "</strong>"
                                . "&nbsp;<small><a href=\"#\" onClick=\"" 
                                . "javascript: JVideoAJAX.updateVideoCategories("
                                    . $videoCategory->videoId . ", " . $videoCategory->categoryId
                                    . ", 'false'); return false;\">"
                                . JText::_("JV_DETACH_VIDEO") . "</a></small><br />";
		}

        if ($videoCategoryList == "")
            return JText::_("JV_NO_CATEGORIES_ATTACHED");
        else
            return $videoCategoryList;
    }
}
