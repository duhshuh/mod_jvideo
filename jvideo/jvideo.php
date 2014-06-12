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

require_once(JPATH_ADMINISTRATOR.'/components/com_jvideo/import.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_jvideo/library/infin-lib.php');
require_once(JPATH_COMPONENT.'/controller.php' );

if($controller = JRequest::getWord('view'))
{
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';

    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

JHTML::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jvideo/helpers/html');

$classname = 'JVideoController'.$controller;

$controller = new $classname( );
$controller->execute(JRequest::getVar( 'task' ));
$controller->redirect();
