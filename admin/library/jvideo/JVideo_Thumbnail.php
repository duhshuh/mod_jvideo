<?php

require_once dirname(__FILE__) . '/JVideo_ThumbnailCacher.php';
require_once dirname(__FILE__) . '/JVideo_ThumbnailResizer.php';

class JVideo_Thumbnail
{
	private $videoId;
	private $thumbnailId;
	private $remoteUrl;
	private $timeIndex;
	private $width;
	private $height;
	
	public function __construct($videoId, $thumbnailId, $remoteUrl, $timeIndex, $width, $height)
	{
		$this->videoId = $videoId;
		$this->thumbnailId = $thumbnailId;
		$this->remoteUrl = $remoteUrl;
		$this->timeIndex = $timeIndex;
		$this->width = $width;
		$this->height = $height;
	}
	
	public function getUrl()
	{
		$url = $this->remoteUrl;
		
		if ($this->isCachingEnabled())
		{
			$cacher = new JVideo_ThumbnailCacher();
			$url = $cacher->cache($this->videoId, $this->remoteUrl, $this->thumbnailId);
		}
		
		return $url;
	}
	
	public function getTimeIndex()
	{
		return $this->timeIndex;
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function getHeight()
	{
		return $this->height;
	}
	
	public function resize($maxWidth, $maxHeight, $constrainAspect = false)
	{
		$resizer = new JVideo_ThumbnailResizer();
		return $resizer->resize($this->videoId, $this->thumbnailId, $this->remoteUrl, $maxWidth, $maxHeight, $constrainAspect);
	}
	
	private function isCachingEnabled()
	{
        $config = JVideo_Factory::getConfig();
		return (bool)$config->cacheThumbnails;
	}
}