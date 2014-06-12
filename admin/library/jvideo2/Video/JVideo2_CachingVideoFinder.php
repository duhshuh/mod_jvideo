<?php
require_once dirname(__FILE__) . '/JVideo2_IVideoFinder.php';

class JVideo2_CachingVideoFinder implements JVideo2_IVideoFinder
{
	/**
	 * @var JVideo_IVideoFinder
	 */
	private $videoFinder;
	
	public function __construct(JVideo2_IVideoFinder $videoFinder)
	{
		$this->videoFinder = $videoFinder;
	}
	
	public function getCategoryPage($categoryId, $pageNumber, $pageSize)
	{
		$cache = JFactory::getCache('jvideo');
		$cacheId = 'video_finder_getCategoryPage_' . $categoryId . '_' . $pageNumber . '_' . $pageSize;
		
		return $cache->get(array($this->videoFinder, 'getCategoryPage'), array($categoryId, $pageNumber, $pageSize), $cacheId);
	}
}