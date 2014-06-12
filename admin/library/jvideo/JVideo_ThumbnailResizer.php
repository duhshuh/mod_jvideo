<?php
class JVideo_ThumbnailResizer
{
	private $videoId;
	private $thumbnailId;
	private $url;
	private $maxWidth;
	private $maxHeight;
	private $constrainAspect;

	public function resize($videoId, $thumbnailId, $url, $maxWidth, $maxHeight, $constrainAspect)
	{
		$this->videoId = $videoId;
		$this->thumbnailId = $thumbnailId;
		$this->url = $url;
		$this->maxWidth = $maxWidth;
		$this->maxHeight = $maxHeight;
		$this->constrainAspect = $constrainAspect;

		if (!file_exists($this->getNewFilePath()))
		{
			$this->createResizedImage();
		}

		return $this->getNewWebPath();
	}

	private function createResizedImage()
	{
		$original = imagecreatefromstring($this->getImageData());
		
		if ($original === false)
		{
			throw new Exception('Unable to get image');
		}

		list($oldWidth, $oldHeight) = $this->getImageDimensions($original);
		list($newWidth, $newHeight, $copyWidth, $copyHeight, $offsetX, $offsetY) = $this->getNewImageDimentions($original);

		$new = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($new, $original, 0, 0, $offsetX, $offsetY, $copyWidth, $copyHeight, $oldWidth, $oldHeight);

		imagejpeg($new, $this->getNewFilePath(), 80);
	}

	private function getImageData()
	{
		$request = new JVideo_WebRequest();
		return $request->get($this->url);
	}

	private function getNewImageDimentions($sourceImage)
	{
		list($oldWidth, $oldHeight) = $this->getImageDimensions($sourceImage);

		if ($oldWidth / $oldHeight > $this->maxWidth / $this->maxHeight)
		{
			if ($this->constrainAspect)
			{
				return $this->getLeftRightCroppedDimensions($oldWidth, $oldHeight);
			} else
			{
				return $this->getMaxWidthDimensions($oldWidth, $oldHeight);
			}
		} else
		{
			if ($this->constrainAspect)
			{
				return $this->getTopBottomCroppedDimensions($oldWidth, $oldHeight);
			} else
			{
				return $this->getMaxHeightDimensions($oldWidth, $oldHeight);
			}
		}

		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), 0, 0);
	}

	private function getLeftRightCroppedDimensions($oldWidth, $oldHeight)
	{
		$copyHeight = $this->maxHeight;
		$copyWidth = $oldWidth * $copyHeight / $oldHeight;
		$newHeight = $this->maxHeight;
		$newWidth = $this->maxWidth;
		$offsetX = $copyWidth - $this->maxWidth;
		$offsetY = 0;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), intval($offsetX), intval($offsetY));
	}

	private function getMaxWidthDimensions($oldWidth, $oldHeight)
	{
		$newWidth = $this->maxWidth;
		$newHeight = $oldHeight * $newWidth / $oldWidth;
		$copyWidth = $newWidth;
		$copyHeight = $newHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), 0, 0);
	}

	private function getTopBottomCroppedDimensions($oldWidth, $oldHeight)
	{
		$copyWidth = $this->maxWidth;
		$copyHeight = $oldHeight * $copyWidth / $oldWidth;
		$newHeight = $this->maxHeight;
		$newWidth = $this->maxWidth;
		$offsetX = 0;
		$offsetY = $copyHeight - $this->maxHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), intval($offsetX), intval($offsetY));
	}

	private function getMaxHeightDimensions($oldWidth, $oldHeight)
	{
		$newHeight = $this->maxHeight;
		$newWidth = $oldWidth * $newHeight / $oldHeight;
		$copyWidth = $newWidth;
		$copyHeight = $newHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), 0, 0);
	}

	private function getImageDimensions($image)
	{
		return array(imagesx($image), imagesy($image));
	}

	private function getNewFilePath()
	{
		return JPATH_ROOT . '/media/com_jvideo/site/images/thumbnails/' . $this->getNewFileName();
	}

	private function getNewWebPath()
	{
		return JURI::root() . '/media/com_jvideo/site/images/thumbnails/' . $this->getNewFileName();
	}

	private function getNewFileName()
	{
		return 'V' . $this->videoId . '_T' . $this->thumbnailId . '-' . $this->maxWidth . 'x' . $this->maxHeight . '-' . ($this->constrainAspect ? '1' : '0') . '.jpg';
	}

}