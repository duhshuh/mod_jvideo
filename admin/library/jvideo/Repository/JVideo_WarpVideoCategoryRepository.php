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

require_once(dirname(__FILE__) . '/../../infin-lib.php');
require_once(dirname(__FILE__) . '/../JVideo_IVideoCategoryRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_RemoteService.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');

class JVideo_WarpVideoCategoryRepository extends JVideo_RemoteService implements JVideo_IVideoCategoryRepository
{
    public function __construct()
    {
        parent::__construct();

        $this->addRemoteService(
            new InfinovationVideo(
                $this->config->infinoAccountKey,
                $this->config->infinoSecretKey));
    }
    
    public function add(JVideo_VideoCategory $videoCategory)
    {
        $this->remoteService->addToCategory(
            $videoCategory->videoGuid,
            $videoCategory->categoryId);
    }

    public function remove(JVideo_VideoCategory $videoCategory)
    {
        $this->remoteService->removeFromCategory(
            $videoCategory->videoGuid,
            $videoCategory->categoryId);
    }

    public function getVideoCategories()
    {
        return $this->remoteService->getVideoCategories();
    }
    
	public function getVideoCategoriesByVideoId($videoId)
    {
        return;
    }

    public function getVideoCategoriesByVideoGuid($videoGuid)
    {
        return;
    }
}
