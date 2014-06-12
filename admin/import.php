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

function jvimport($file)
{
    $parts = array_merge(array('library', 'jvideo'), explode('.', $file));
    $class = array_pop($parts);

    $includePath = implode('/', $parts);

    $path = dirname(__FILE__).'/'.$includePath.'/JVideo_'.$class.'.php';

    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new Exception("Class " . $class . " was not found!");
    }
}


function jvimport2($file)
{
    $parts = array_merge(array('library', 'jvideo2'), explode('.', $file));
    $class = array_pop($parts);

    $includePath = implode('/', $parts);

    $path = dirname(__FILE__).'/'.$includePath.'/JVideo2_'.$class.'.php';

    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new Exception("Class " . $class . " was not found!");
    }
}