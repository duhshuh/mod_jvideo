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

class JVideoLibraryVideoCollectionFixture extends UnitTestCase
{
	public function test_Add_Video_To_Collection()
	{
		$video = new JVideo_Video();
		$collection = new JVideo_VideoCollection();
		
		$collection->add($video);
		
		$this->assertTrue($collection->count());
	}
	
	public function test_Remove_Video_From_Collection()
	{
		$video = new JVideo_Video();
		$collection = new JVideo_VideoCollection();
		
		$collection->add($video);
		$collection->remove($video);
		
		$this->assertTrue($collection->count() == 0);
	}
	
	public function test_Count_Videos_In_Collection()
	{
		$video1 = new JVideo_Video();
		$video2 = new JVideo_Video();
		$video3 = new JVideo_Video();
		$collection = new JVideo_VideoCollection();
		
		$this->assertTrue($collection->count() == 0);
		
		$collection->add($video1);
		$this->assertTrue($collection->count() == 1);
		
		$collection->add($video2);
		$this->assertTrue($collection->count() == 2);
		
		$collection->add($video3);
		$this->assertTrue($collection->count() == 3);
		
		$collection->remove($video3);
		$this->assertTrue($collection->count() == 2);
		
		$collection->remove($video2);
		$this->assertTrue($collection->count() == 1);
		
		$collection->remove($video1);
		$this->assertTrue($collection->count() == 0);
	}
	
	public function test_Remove_Video_That_Does_Not_Exist_In_Collection()
	{
		$video1 = new JVideo_Video();
		$video2 = new JVideo_Video();
		
		$collection = new JVideo_VideoCollection();
		$collection->add($video1);
		$collection->remove($video2);
		
		$this->assertTrue($collection->count() == 1);
	}
	
	public function test_Remove_Video_From_Empty_Collection()
	{
		$video = new JVideo_Video();
		
		$collection = new JVideo_VideoCollection();
		
		$collection->remove($video);
	}
}