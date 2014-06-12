<?php
require_once dirname(__FILE__) . '/JVideo2_IVideoFinder.php';
require_once dirname(__FILE__) . '/JVideo2_Video.php';

require_once JPATH_ADMINISTRATOR.'/components/com_jvideo/import.php';
jvimport('Repository.JoomlaVideoRepository');
jvimport('Factory');
jvimport('Thumbnail');
jvimport('Video');
jvimport('VideoFactory');


class JVideo2_DbVideoFinder implements JVideo2_IVideoFinder
{
	public function getCategoryPage($categoryId, $pageNumber, $pageSize)
	{
		$rawVideoData = $this->getRawVideoData($categoryId, $pageNumber, $pageSize);
		$videos = $this->getVideosFromRawData($rawVideoData);
		
		return $videos;
	}
	
	private function getRawVideoData($categoryId, $pageNumber, $pageSize)
	{
		$sql = $this->getSql($categoryId, $pageNumber, $pageSize);
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
	
	private function getVideosFromRawData($rawVideoData)
	{
		$videos = array();

		foreach ($rawVideoData as $videoData)
		{
			$videos[] = new JVideo2_VideoAdapter(JVideo_VideoFactory::create($videoData));
		}
		
		$this->loadThumbnails($videos);
		
		return $videos;
	}
	
	private function getSql($categoryId, $pageNumber, $pageSize)
	{
		$jvvideo = new JVideo_Video();
		$offset = ($pageNumber - 1) * $pageSize;
		return $jvvideo->sqlSelectNewestVideos($categoryId, 'published') . ' limit ' . intval($pageSize) . ' offset ' . intval($offset);
	}
	
	private function loadThumbnails($videos)
	{
		if (count($videos) <= 0) return;
		
		$ids = array();
		foreach ($videos as $video)
		{
			$ids[] = intval($video->getId());
		}
		
		$sql = 'select * from #__jvideo_thumbnails where videoID in (' . implode(',', $ids) . ')';
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		
		$rows = $db->loadObjectList();
		
		if ($rows != null)
		{
			foreach ($rows as $row)
			{
				foreach ($videos as $video)
				{
					if ($row->videoID == $video->getId())
					{
						$video->addThumbnail(new JVideo_Thumbnail($row->videoID, $row->id, $row->imageURL, $row->timeIndex, $row->width, $row->height));
					}
				}
			}
		}
	}
}
