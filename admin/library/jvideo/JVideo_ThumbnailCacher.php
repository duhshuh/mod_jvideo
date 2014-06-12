<?php
require_once dirname(__FILE__) . '/JVideo_WebRequest.php';

class JVideo_ThumbnailCacher
{
	private $videoId;
	private $thumbnailId;
	private $remoteThumbnailUrl;
	
	public function cache($videoId, $remoteThumbnailUrl, $thumbnailId)
	{
		$this->videoId = $videoId;
		$this->thumbnailId = $thumbnailId;
		$this->remoteThumbnailUrl = $remoteThumbnailUrl;

		if (!$this->isCached())
		{
			$this->cacheImage();
		}

		if ($this->isCachedImageValid())
		{
			return $this->getCachedWebPath();
		}
		else
		{
			return $remoteThumbnailUrl;
		}
	}
	
	private function isCached()
	{
		$filePath = $this->getCachedFilePath();
		return file_exists($filePath); // && filesize($filePath) > 0;
	}
	
	private function isCachedImageValid()
	{
		$filePath = $this->getCachedFilePath();
		return file_exists($filePath) && filesize($filePath) > 0;
	}
	
	private function cacheImage()
	{
		$request = new JVideo_WebRequest();
		$img = $request->get($this->remoteThumbnailUrl);

		if (strlen($img) > 0)
		{
			$this->createCacheDirectoryIfNecessary();
			if (($fp = $this->openCacheFile()))
			{
				$write = @fputs($fp, $img);
				fclose($fp);
			}
		}
	}

	private function createCacheDirectoryIfNecessary()
	{
		$cacheDirectory = $this->getCacheDirectory();
		if (!is_dir($cacheDirectory))
		{
			if (!mkdir($cacheDirectory, 0775, true))
			{
				trigger_error('Unable to create thumbnail cache directory: ' . $cacheDirectory, E_USER_WARNING);
			}
		}
	}

	private function openCacheFile()
	{
		$cacheFilePath = $this->getCachedFilePath();
		$fp = @fopen($cacheFilePath, "wb");
		if ($fp === false)
		{
			trigger_error('Unable to open thumbnail cache file for writing: ' . $cacheFilePath, E_USER_WARNING);
		}
		return $fp;
	}
	
	private function getCachedFileName()
	{
		return 'V' . $this->videoId . '_T' . $this->thumbnailId . '.jpg';
	}
	
	private function getCachedFilePath()
	{
		return $this->getCacheDirectory() . '/' . $this->getCachedFileName();
	}

	private function getCacheDirectory()
	{
		return JPATH_ROOT.'/media/com_jvideo/site/images/thumbnails';
	}
	
	private function getCachedWebPath()
	{
		return JURI::root() . '/media/com_jvideo/site/images/thumbnails/' . $this->getCachedFileName();
	}
}