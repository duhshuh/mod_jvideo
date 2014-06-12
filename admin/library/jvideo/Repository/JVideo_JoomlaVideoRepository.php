<?php
/*
 *	@package	JVideo
 *	@subpackage Library
 *	@link http://jvideo.warphd.com
 *	@copyright (C) 2007 - 2010 Warp
 *	@license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die("Cannot use JVideo Joomla repository outside of Joomla");

require_once(dirname(__FILE__) . '/../JVideo_IVideoRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_VideoFactory.php');

class JVideo_JoomlaVideoRepository implements JVideo_IVideoRepository
{
	public function update(JVideo_Video $video)
	{
		$db = JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();

		$query = "UPDATE #__jvideo_videos "
				."SET "
				."`user_id` = " . $inputFilter->clean($video->getUserID(), 'INT') . ", "
				."`video_title` = " . $db->Quote($video->getVideoTitle()) . ", "
				."`video_desc` = " . $db->Quote($video->getVideoDescription()) . ", "
				."`hits` = " . $inputFilter->clean($video->getHits(), 'INT') . ", "
				."`duration` = " . $inputFilter->clean($video->getDuration(), 'FLOAT') . ", "
				."`tags` = " . $db->Quote($video->getTags()) . ", "
				."`date_added` = " . $db->Quote($video->getDateAdded()) . ", "
				."`published` = " . $db->Quote($video->published) . ", "
				."`publish_up` = "  . $db->Quote($video->getPublishUp()) .", "
				."`publish_down` = ". $db->Quote($video->getPublishDown()) .", "
				."`admin_approved` = " . $inputFilter->clean($video->getAdminApproved(), 'INT') . " "
				." WHERE `id` = " . $inputFilter->clean($video->getID(), 'INT') . " "
				." LIMIT 1";

		$db->setQuery($query);
		return $db->execute();
	}

	public function delete(JVideo_Video $video)
	{
		$db = JFactory::getDBO();

		if ((int) $video->getID() <= 0)
			throw new JVideo_Exception("Cannot delete video with invalid ID#" . $video->getID());

		$query = "DELETE FROM #__jvideo_videos "
				."WHERE id=" . (int) $video->getID() . " "
				."LIMIT 1";

		$db->setQuery($query);
		return $db->execute();
	}

	public function insertStub($videoGuid, $userId)
	{
		$db	= JFactory::getDBO();

		$query = "INSERT INTO #__jvideo_videos (user_id, infin_vid_id, date_added) "
				."SELECT '" . (int) $userId . "', " . $db->Quote($videoGuid) . ", NOW() "
				."FROM DUAL "
				."WHERE NOT EXISTS ("
					."SELECT * "
					."FROM #__jvideo_videos "
					."WHERE `infin_vid_id` = " . $db->Quote($videoGuid) . ");";

		$db->setQuery($query);
		
		if ($db->execute())
		{
			return (int) $db->insertid();
		}
		else
		{
			$query = "SELECT `id` FROM #__jvideo_videos "
					."WHERE `infin_vid_id` = " . $db->Quote($videoGuid) . " "
					."LIMIT 1;";

			$db->setQuery($query);
			return (int) $db->loadResult();
		}
	}

	public function approve(JVideo_Video $video)
	{
		$db	= JFactory::getDBO();

		$query = "UPDATE #__jvideo_videos "
				."SET admin_approved = 1 "
				."WHERE `id` = " . (int) $video->getID() . " "
				."LIMIT 1;";

		$db->setQuery($query);
		return $db->execute();
	}

	public function unapprove(JVideo_Video $video)
	{
		$db	= JFactory::getDBO();

		$query = "UPDATE #__jvideo_videos "
				."SET admin_approved = 0 "
				."WHERE `id` = " . (int) $video->getID() . " "
				."LIMIT 1;";

		$db->setQuery($query);
		return $db->execute();
	}

	public function feature(JVideo_Video $video)
	{
		$db = JFactory::getDBO();

		$sql = "INSERT INTO #__jvideo_featured (v_id, feature_rank) "
			  ."SELECT " . (int) $video->getID() . ", (select coalesce(max(feature_rank) + 1, 1) from #__jvideo_featured) "
			  ."FROM DUAL "
			  ."WHERE NOT EXISTS("
				."SELECT * FROM #__jvideo_featured "
				."WHERE `v_id` = " . (int) $video->getID() . ")";

		$db->setQuery($sql);
		return $db->execute();

		$this->normalizeFeaturedVideoOrder();
	}

	public function unfeature(JVideo_Video $video)
	{
		$db = JFactory::getDBO();

		$sql = "DELETE FROM #__jvideo_featured "
			  ."WHERE v_id = " . (int) $video->getID() . " "
			  ."LIMIT 1;";

		$db->setQuery($sql);
		$db->execute();

		$this->normalizeFeaturedVideoOrder();
	}

	public function normalizeFeaturedVideoOrder()
	{
		$db = JFactory::getDBO();

		$db->setQuery('select id from #__jvideo_featured order by feature_rank');
		$result = $db->loadObjectList();

		if ($result)
		{
			$currentRank = 1;
			foreach ($result as $row)
			{
				$db->setQuery('update #__jvideo_featured set feature_rank = ' . $currentRank . ' where id = ' . $row->id);
				$db->execute();
				$currentRank++;
			}
		}
	}

	public function publish(JVideo_Video $video)
	{
		$db	= JFactory::getDBO();

		$query = "UPDATE #__jvideo_videos "
				."SET published = 1 "
				."WHERE `id` = " . (int) $video->getID() . " "
				."LIMIT 1;";

		$db->setQuery($query);
		return $db->execute();
	}

	public function unpublish(JVideo_Video $video)
	{
		$db	= JFactory::getDBO();

		$query = "UPDATE #__jvideo_videos "
				."SET published = 0 "
				."WHERE `id` = " . (int) $video->getID() . " "
				."LIMIT 1;";

		$db->setQuery($query);
		return $db->execute();
	}

	public function addHit(JVideo_Video $video)
	{
		$db	= JFactory::getDBO();

		$sql = "UPDATE #__jvideo_videos "
			  ."SET `hits` = `hits` + 1 "
			  ."WHERE `id` = " . (int) $video->getID() . " "
			  ."LIMIT 1;";

		$db->setQuery($sql);
		return $db->execute();
	}

	public function getVideos()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__jvideo_videos WHERE status <> 'deleted';";
		$db->setQuery($sql);

		$videoRows = $db->loadObjectList();

		$videos = array();

		foreach ($videoRows as $videoRow) {
			$videos[] = JVideo_VideoFactory::create($videoRow);
		}
		
		return $videos;
	}

	public function getVideoById($videoId)
	{
		$db = JFactory::getDBO();
		
		$lookup = new JVideo_Video();
		$query = $lookup->sqlSelectVideo($videoId);

		$db->setQuery($query);
		$video = $db->loadObject();

		return JVideo_VideoFactory::create($video);
	}

	public function getVideoByGuid($videoGuid)
	{
		return $this->getVideoById(
			$this->getVideoIdByVideoGuid($videoGuid));
	}

	public function getVideoIdByVideoGuid($videoGuid)
	{
		$db = JFactory::getDBO();
		
		$sql = "SELECT `id` FROM #__jvideo_videos "
			  ."WHERE `infin_vid_id` = " . $db->quote($videoGuid);

		$db->setQuery($sql);
		return $db->loadResult();
	}

	public function getNewVideoGuid()
	{
		return null;
	}

	public function getFlashVarsForUploader($videoGuid, $uri)
	{
		return null;
	}
}