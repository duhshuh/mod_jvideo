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
defined('_JEXEC') or die("Cannot use outside of Joomla");

require_once dirname(__FILE__) . '/../../infin-lib.php';
require_once dirname(__FILE__) . '/../JVideo_VideoCategory.php';
require_once dirname(__FILE__) . '/../JVideo_Exception.php';
require_once dirname(__FILE__) . '/../JVideo_ISyncService.php';
require_once dirname(__FILE__) . '/../JVideo_VideoCategoryRepositoryFactory.php';
require_once dirname(__FILE__) . '/../Repository/JVideo_JoomlaVideoCategoryRepository.php';
require_once dirname(__FILE__) . '/../Repository/JVideo_WarpVideoCategoryRepository.php';
require_once dirname(__FILE__) . '/../../jvideo2/Database/JVideo2_DbBatch.php';

class JVideo_JoomlaVideoCategorySyncService implements JVideo_ISyncService
{
    private $localVideoCategories = array();
    private $remoteVideoCategories = array();

    public function sync()
    {
        $localRepository = JVideo_VideoCategoryRepositoryFactory::createJoomlaRepository();
        $this->localVideoCategories = $localRepository->getVideoCategories();

        $remoteRepository = JVideo_VideoCategoryRepositoryFactory::createWarpRepository();
        $this->remoteVideoCategories = $this->replaceVideoGuidWithVideoId($remoteRepository->getVideoCategories());

        $insertCount = $this->addNewVideoCategories();

        $this->localVideoCategories = $localRepository->getVideoCategories();

        $removeCount = $this->removeOldVideoCategories();

        $result = "";

        if ($insertCount > 0)
            $result .= "<p>" . $insertCount . " new video categories inserted</p>";

        if ($removeCount > 0)
            $result .= "<p>" . $removeCount . " old video categories removed</p>";

        return $result;
    }

    private function addNewVideoCategories()
    {
        $videoCategoriesToInsert = $this->getVideoCategoriesToInsert();

        $insertCount = count($videoCategoriesToInsert);

        $this->insertVideoCategories($videoCategoriesToInsert);

        return $insertCount;
    }

    private function removeOldVideoCategories()
    {
        $videoCategoriesToRemove = $this->getVideoCategoriesToRemove();

        $removeCount = count($videoCategoriesToRemove);

        $this->deleteVideoCategories($videoCategoriesToRemove);

        return $removeCount;
    }

    private function getVideoCategoriesToInsert()
    {
        $videoCategoriesToInsert = array();

        if (is_null($this->remoteVideoCategories) || count($this->remoteVideoCategories) == 0)
            return $videoCategoriesToInsert;
        
        reset($this->remoteVideoCategories);

        foreach ($this->remoteVideoCategories as $remoteVideoCategory) {
            reset($this->localVideoCategories);

            foreach ($this->localVideoCategories as $localVideoCategory) {
                if ($localVideoCategory->videoId == $remoteVideoCategory->videoId) {
                    if ($localVideoCategory->categoryId == $remoteVideoCategory->categoryId) {
                        continue 2;
                    }
                }
            }

            $videoCategoriesToInsert[] = $remoteVideoCategory;
        }
        
        return $videoCategoriesToInsert;
    }

    private function getVideoCategoriesToRemove()
    {
        $videoCategoriesToRemove = array();
        reset($this->localVideoCategories);

        foreach ($this->localVideoCategories as $localVideoCategory) {
            reset($this->remoteVideoCategories);

            foreach ($this->remoteVideoCategories as $remoteVideoCategory) {
                if ($remoteVideoCategory->videoId == $localVideoCategory->videoId) {
                    if ($remoteVideoCategory->categoryId == $localVideoCategory->categoryId) {
                        continue 2;
                    }
                }
            }

            $videoCategoriesToRemove[] = $localVideoCategory;
        }

        return $videoCategoriesToRemove;
    }

    private function insertVideoCategories(array $videoCategories)
    {
        $db = JFactory::getDBO();

        $sql = $this->getBatchInsertSql($videoCategories);

        JVideo2_DbBatch::execute($db, $sql);
    }

    private function deleteVideoCategories(array $videoCategories)
    {
        $db = JFactory::getDBO();

        $sql = $this->getBatchRemoveSql($videoCategories);

        JVideo2_DbBatch::execute($db, $sql);
    }

    private function getBatchInsertSql(array $videoCategories, $sql = "")
    {
        if (count($videoCategories) == 0) { return $sql; }

        $limit = min(count($videoCategories), 25);

        $sql .= "insert into #__jvideo_videos_categories values ";

        for ($i = 0; $i < $limit; $i++) {
            $videoCategory = array_pop($videoCategories);

            $sql .= "(" . (int) $videoCategory->videoId . ", " . (int) $videoCategory->categoryId . "),";
        }

        $sql = substr($sql, 0, -1) . ";";

        return $this->getBatchInsertSql($videoCategories, $sql);
    }

    private function getBatchRemoveSql(array $videoCategories, $sql = "")
    {
        if (count($videoCategories) == 0) { return $sql; }

        $limit = min(count($videoCategories), 25);

        $sql .= "delete from #__jvideo_videos_categories where ";

        for ($i = 0; $i < $limit; $i++) {
            $videoCategory = array_pop($videoCategories);

            $sql .= "(video_id = " . (int) $videoCategory->videoId . " " .
                    "and category_id = " . (int) $videoCategory->categoryId . ") or";
        }

        $sql = substr($sql, 0, -3) . " limit " . $limit . ";";

        return $this->getBatchRemoveSql($videoCategories, $sql);
    }

    private function replaceVideoGuidWithVideoId($remoteVideoCategories, $videoCategories = null)
    {
        if (count($remoteVideoCategories) == 0) { return $videoCategories; }

        $db = JFactory::getDBO();
        $tempVideoCategories = array();
        $limit = min(count($remoteVideoCategories), 25);

        $sql = "select distinct v.id as videoId, v.infin_vid_id as videoGuid "
              ."from #__jvideo_videos v "
              ."where v.infin_vid_id in (";

        for ($i = 0, reset($remoteVideoCategories); $i < $limit; $i++)
        {
            $tempVideoCategories[] = array_pop($remoteVideoCategories);

            $sql .= "'" . end($tempVideoCategories)->videoGuid . "',";
        }

        $sql = substr($sql, 0, -1) . ");";
        $db->setQuery($sql);

        $results = $db->loadObjectList();

        foreach ($results as $result)
        {
            reset($tempVideoCategories);

            foreach ($tempVideoCategories as &$tempVideoCategory)
            {
                if ($tempVideoCategory->videoGuid == $result->videoGuid)
                {
                    $videoCategories[] = JVideo_VideoCategoryFactory::create(
                        $result->videoId, $tempVideoCategory->categoryId);

                    unset($tempVideoCategory);
                }
            }
        }

        return $this->replaceVideoGuidWithVideoId($remoteVideoCategories, $videoCategories);
    }
}