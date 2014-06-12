<?php

require_once dirname(__FILE__) . '/../Image/JVideo2_ThumbnailGenerator.php';
require_once dirname(__FILE__) . '/../Image/JVideo2_ImageResizeProfile.php';

interface JVideo2_IVideo
{
	function getId();
	function getVideoGuid();
	function getTitle();
	function getUrl();
	function getThumbnails();
	function getThumbnailUrl(JVideo2_ImageResizeProfile $profile);
}

class JVideo2_VideoAdapter implements JVideo2_IVideo
{
	/**
	 * @var JVideo_Video
	 */
	private $video;
	private $thumbnails = array();
	
	public function __construct(JVideo_Video $video)
	{
		$this->video = $video;
	}
	
	public function getId()
	{
		return $this->video->getId();
	}
	
	public function getVideoGuid()
	{
		return $this->video->getInfinoVideoID();
	}
	
	public function getTitle()
	{
		return $this->video->getVideoTitle();
	}
	
	public function getUrl()
	{
		return $this->video->getVideoUrl();
	}
	
	public function getThumbnails()
	{
		return $this->thumbnails;
	}
	
	public function getThumbnailUrl(JVideo2_ImageResizeProfile $profile)
	{
		$generator = new JVideo2_ThumbnailGenerator();
		return $generator->generate($this, $profile);
	}
	
	public function addThumbnail(JVideo_Thumbnail $thumbnail)
	{
		$this->thumbnails[] = $thumbnail;
	}
	
	public function getDuration()
	{
		return $this->video->getDuration();
	}
	
	public function getDescription() {
		return $this->video->getVideoDescription();
	}
}