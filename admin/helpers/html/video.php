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

class JVideoVideo
{
    public static function removeFeatured($videoId)
    {
        return "<a href=\"#jvideo\" title=\"" . JText::_("JV_VIDEO_FEATURE_TITLE") . "\" onClick=\""
            . "javascript: JVideoAJAX.featureVideo("
                . $videoId
            . ",'true'); return false;\">" . JText::_("JV_VIDEO_NOT_FEATURED") ."</a>";
    }

    public static function addFeatured($videoId)
    {
        return "<a href=\"#jvideo\" title=\"" . JText::_("JV_VIDEO_UNFEATURE_TITLE") . "\" onClick=\""
            . "javascript: JVideoAJAX.featureVideo("
                . $videoId
            . ",'false'); return false;\">" . JText::_("JV_VIDEO_FEATURED") ."</a>";
    }
}
