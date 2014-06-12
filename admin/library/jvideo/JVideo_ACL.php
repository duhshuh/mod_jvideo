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

class JVideo_Access
{
	public function checkAccess(JVideo_AccessVideo &$video, JVideo_AccessType $type, JVideo_AccessGroup $group)
	{
		
	}
}

class JVideo_AccessVideo
{
	public $id;
	public $level = array();
	public $group = array();
	
	public function getLevel($group)
	{
		if (array_key_exists($group, $this->group))
		{
			return $this->group[$group];
		}
		else
		{
			return JVideo_AccessLevel::None;
		}
	}
}

class JVideo_AccessLevel
{
	const None = 0;
	const View = 1;
	const Upload = 2;
	const Edit = 3;
	const Delete = 4;
	const Move = 5;
	const Feature = 6;
	const Admin = 7;
}

class JVideo_AccessGroup
{
	const Viewer = 0;
	const Uploader = 1;
	const Editor = 2;
	const Admin = 3;
}
?>