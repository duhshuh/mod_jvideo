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

jvimport('CommentsBase');

class JVideo_Comments extends JVideo_CommentsBase
{
	public function displayComments()
	{
		$option = JRequest::getCmd('option');
		
		if (!@include_once(JPATH_ADMINISTRATOR.'/components/com_comment/plugin/'.$option.'/josc_com_jvideo.php'))
		{
			echo "!JoomlaComment could not be found. It must be installed separately from JVideo.";
		}
	}
}
