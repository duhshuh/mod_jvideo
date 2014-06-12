<?php

class JVideo2_ThumbnailGenerator
{
	public function generate(JVideo2_IVideo $video, JVideo2_ImageResizeProfile $profile)
	{
		$thumbnail = $this->getBestThumbnailOrNull($video);
		
		if ($thumbnail == null)
		{
			return null;
		}
		
		return $thumbnail->resize($profile->maxWidth, $profile->maxHeight, $profile->constrainAspect);
	}
	
	private function getBestThumbnailOrNull(JVideo2_IVideo $video)
	{
		$thumbnail = null;
		
		foreach ($video->getThumbnails() as $t)
		{
			if ($thumbnail == null || $this->isBetter($thumbnail, $t))
			{
				$thumbnail = $t;
			}
		}
		
		return $thumbnail;
	}
	
	private function isBetter(JVideo_Thumbnail $current, JVideo_Thumbnail $proposed)
	{
		return $proposed->getWidth() > $current->getWidth()
			|| ($proposed->getWidth() == $current->getWidth() && $proposed->getTimeIndex() < $current->getTimeIndex());
	}
}