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

class JVideo_Exception extends Exception
{
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}

class JVideo_CategoryNotFoundException extends JVideo_Exception
{
	public function __construct($categoryId)
	{
		parent::__construct('CategoryId: ' . $categoryId);
	}
}

class JVideo_InvalidNestedElementParentException extends JVideo_Exception
{
	public function __construct($message)
	{
		parent::__construct($message);
	}
}

class JVideo_InvalidNestedSetStateException extends JVideo_Exception
{
	const CODE_NEST_LEFT_GREATER_THAN_NEST_RIGHT = 1;
	const CODE_MAX_NEST_RIGHT_VALUE_INVALID = 2;

	public function __construct($code)
	{
		parent::__construct('Nested set is in an inconsistent state: ' . $code);
	}
}

class JVideo_SaveCategoryTreeException extends JVideo_Exception
{
	public function __construct($categoryTree)
	{
		parent::__construct('<pre>'.$categoryTree.'</pre>');
	}
}