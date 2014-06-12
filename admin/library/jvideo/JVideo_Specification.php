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

abstract class JVideo_Specification
{
	public abstract function isSatisfiedBy($object);

	public function and_(JVideo_Specification $spec)
	{
		return new JVideo_AndSpecification($this, $spec);
	}

	public function or_(JVideo_Specification $spec)
	{
		return new JVideo_OrSpecification($this, $spec);
	}

	public function not_()
	{
		return new JVideo_NotSpecification($this);
	}
}

class JVideo_AndSpecification extends JVideo_Specification
{
	private $spec1;
	private $spec2;

	public function __construct(JVideo_Specification $spec1, JVideo_Specification $spec2)
	{
		$this->spec1 = $spec1;
		$this->spec2 = $spec2;
	}

	public function isSatisfiedBy($object)
	{
		return $this->spec1->isSatisfiedBy($object) && $this->spec2->isSatisfiedBy($object);
	}
}

class JVideo_OrSpecification extends JVideo_Specification
{
	private $spec1;
	private $spec2;

	public function __construct(JVideo_Specification $spec1, JVideo_Specification $spec2)
	{
		$this->spec1 = $spec1;
		$this->spec2 = $spec2;
	}

	public function isSatisfiedBy($object)
	{
		return $this->spec1->isSatisfiedBy($object) || $this->spec2->isSatisfiedBy($object);
	}
}

class JVideo_NotSpecification extends JVideo_Specification
{
	private $spec;

	public function __construct(JVideo_Specification $spec)
	{
		$this->spec = $spec;
	}

	public function isSatisfiedBy($object)
	{
		return !$this->spec->isSatisfiedBy($object);
	}
}
