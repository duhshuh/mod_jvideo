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
defined('_JEXEC') or die("Cannot use JVideo Joomla repository outside of Joomla");

require_once(dirname(__FILE__) . '/../JVideo_IVideoCategoryRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_VideoCategoryFactory.php');
require_once(dirname(__FILE__) . '/../JVideo_VideoRepositoryFactory.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');

class JVideo_JoomlaVideoCategoryRepository implements JVideo_IVideoCategoryRepository
{
	public function getVideoCategoriesByVideoId($videoId)
	{
        $videoCategories = array();

        $rows = $this->getVideoCategoriesObjectListByVideoId($videoId);
        
        foreach ($rows as $row) {
			$videoCategories[] =
                JVideo_VideoCategoryFactory::create(
                    $row->videoId,
                    $row->categoryId,
                    $row->name,
                    $row->nestLeft,
                    $row->nestRight,
                    $row->breadcrumb,
                    $row->videoGuid);
		}

		return $videoCategories;
	}

    private function getVideoCategoriesObjectListByVideoId($videoId)
    {
        $db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();
        $sql = "select vc.`video_id` as videoId, vc.`category_id` as categoryId, "
              ." c.`name`, c.`nestLeft`, c.`nestRight`, ("
                  ."select group_concat(distinct parent.name order by parent.nestRight desc "
                      ."separator ' > ') "
                  ."from #__jvideo_categories AS node "
                  ."join #__jvideo_categories AS parent "
                  ."where node.nestLeft between parent.nestLeft and parent.nestRight "
                  ."and node.name = c.name "
                  ."order by parent.nestLeft asc "
              .") as breadcrumb, "
              ." v.`infin_vid_id` as videoGuid "
              ."from #__jvideo_videos_categories vc "
              ."left join #__jvideo_categories c on c.`id` = vc.`category_id` "
              ."left join #__jvideo_videos v on v.`id` = `video_id` "
              ."where `video_id` = '" . $inputFilter->clean($videoId, 'INT') . "' "
              ."order by breadcrumb asc";
		$db->setQuery($sql);

        return $db->loadObjectList();
    }
    
    public function getVideoCategoriesByVideoGuid($videoGuid)
    {
        $videoRepository = JVideo_VideoRepositoryFactory::create();

        $video = $videoRepository->getVideoByGuid($videoGuid);

        return $this->getVideoCategoriesByVideoId($video->getID());
    }

    public function add(JVideo_VideoCategory $videoCategory)
    {
        $db = JFactory::getDBO();
        $sql = "insert into #__jvideo_videos_categories (`video_id`, `category_id`) "
              ."select '" . (int) $videoCategory->videoId . "', '" . (int) $videoCategory->categoryId . "' "
              ."from dual "
              ."where not exists ("
                  ."select * from #__jvideo_videos_categories "
                  ."where `video_id` = '" . (int) $videoCategory->videoId . "' "
                  ."and `category_id` = '" . (int) $videoCategory->categoryId . "' "
              .")";
        $db->setQuery($sql);
        
        return $db->execute();
    }

    public function remove(JVideo_VideoCategory $videoCategory)
    {
        $db = JFactory::getDBO();
        $sql = "delete from #__jvideo_videos_categories "
              ."where `video_id` = '" . (int) $videoCategory->videoId . "' "
              ."and `category_id` = '" . (int) $videoCategory->categoryId . "' "
              ."limit 1";
        $db->setQuery($sql);

        return $db->execute();
    }

    public function getVideoCategories()
    {
        $videoCategories = array();

        $db = JFactory::getDBO();
        $sql = "select * from #__jvideo_videos_categories";
        $db->setQuery($sql);

        $localVideoCategories = $db->loadObjectList();

        foreach ($localVideoCategories as $localVideoCategory)
        {
            $videoCategories[] = JVideo_VideoCategoryFactory::create(
                                    $localVideoCategory->video_id,
                                    $localVideoCategory->category_id);
        }

        return $videoCategories;
    }

 
}
