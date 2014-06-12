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

require_once(dirname(__FILE__) . '/JVideo_Exception.php');
require_once(dirname(__FILE__) . '/JVideo_IVideoCategoryRepository.php');

class JVideo_CompositeVideoCategoryRepository implements JVideo_IVideoCategoryRepository
{
    private $repositories = array();

    public function addRepository($repository)
    {
        $this->repositories[] = $repository;
    }

    public function add(JVideo_VideoCategory $videoCategory)
    {
        foreach ($this->repositories as $repository) {
            $repository->add($videoCategory);
        }
    }

    public function remove(JVideo_VideoCategory $videoCategory)
    {
        foreach ($this->repositories as $repository) {
            $repository->remove($videoCategory);
        }
    }
    
    public function getVideoCategoriesByVideoId($videoId)
    {
        foreach ($this->repositories as $repository) {
            $videoCategories = $repository->getVideoCategoriesByVideoId($videoId);

            if (null != $videoCategories) {
                return $videoCategories;
            }
        }
    }

    public function getVideoCategoriesByVideoGuid($videoGuid)
    {
        foreach ($this->repositories as $repository) {
            $videoCategories = $repository->getVideoCategoriesByVideoGuid($videoGuid);

            if (null != $videoCategories) {
                return $videoCategories;
            }
        }
    }

    public function getVideoCategories()
    {
        foreach ($this->repositories as $repository) {
            $videoCategories = $repository->getVideoCategories();

            if (null != $videoCategories) {
                return $videoCategories;
            }
        }
    }
}