<?php
require_once dirname(__FILE__) . '/JVideo2_ImageResizeProfile.php';

class JVideo2_ImageResizer
{
	/**
	 * @var JVideo2_ThumbailProfile
	 */
	private $profile;
	
	public function resize($image, JVideo2_ImageResizeProfile $profile)
	{
		$this->profile = $profile;

		return $this->createResizedImage($image);
	}

	private function createResizedImage($original)
	{
		list($oldWidth, $oldHeight) = $this->getImageDimensions($original);
		list($newWidth, $newHeight, $copyWidth, $copyHeight, $offsetX, $offsetY) = $this->getNewImageDimentions($original);

		$new = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($new, $original, 0, 0, $offsetX, $offsetY, $copyWidth, $copyHeight, $oldWidth, $oldHeight);

		return $new;
	}

	private function getNewImageDimentions($sourceImage)
	{
		list($oldWidth, $oldHeight) = $this->getImageDimensions($sourceImage);

		if ($oldWidth / $oldHeight > $this->profile->maxWidth / $this->profile->maxHeight)
		{
			if ($this->profile->constrainAspect)
			{
				return $this->getLeftRightCroppedDimensions($oldWidth, $oldHeight);
			} else
			{
				return $this->getMaxWidthDimensions($oldWidth, $oldHeight);
			}
		} else
		{
			if ($this->profile->constrainAspect)
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
		$copyHeight = $this->profile->maxHeight;
		$copyWidth = $oldWidth * $copyHeight / $oldHeight;
		$newHeight = $this->profile->maxHeight;
		$newWidth = $this->profile->maxWidth;
		$offsetX = $copyWidth - $this->profile->maxWidth;
		$offsetY = 0;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), intval($offsetX), intval($offsetY));
	}

	private function getMaxWidthDimensions($oldWidth, $oldHeight)
	{
		$newWidth = $this->profile->maxWidth;
		$newHeight = $oldHeight * $newWidth / $oldWidth;
		$copyWidth = $newWidth;
		$copyHeight = $newHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), 0, 0);
	}

	private function getTopBottomCroppedDimensions($oldWidth, $oldHeight)
	{
		$copyWidth = $this->profile->maxWidth;
		$copyHeight = $oldHeight * $copyWidth / $oldWidth;
		$newHeight = $this->profile->maxHeight;
		$newWidth = $this->profile->maxWidth;
		$offsetX = 0;
		$offsetY = $copyHeight - $this->profile->maxHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), intval($offsetX), intval($offsetY));
	}

	private function getMaxHeightDimensions($oldWidth, $oldHeight)
	{
		$newHeight = $this->profile->maxHeight;
		$newWidth = $oldWidth * $newHeight / $oldHeight;
		$copyWidth = $newWidth;
		$copyHeight = $newHeight;
		return array(intval($newWidth), intval($newHeight), intval($copyWidth), intval($copyHeight), 0, 0);
	}

	private function getImageDimensions($image)
	{
		return array(imagesx($image), imagesy($image));
	}
}