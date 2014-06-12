<?php
require_once dirname(__FILE__) . '/JVideo2_ImageResizer.php';
require_once dirname(__FILE__) . '/JVideo2_ImageResizeProfile.php';

class JVideo2_AvatarUploadProcessor
{
	const MAX_WIDTH = 100;
	const MAX_HEIGHT = 100;

	public function save($tempFilePath, $originalFileName, $newFileName, $userId)
	{
		$avatarFolderPath = JPATH_ROOT . '/media/com_jvideo/site/images/avatars';
		$newFilePath = $avatarFolderPath . '/' . $newFileName;
		move_uploaded_file($tempFilePath, $newFilePath);

		$image = $this->openImage($originalFileName, $newFilePath);

		$imageProfile = new JVideo2_ImageResizeProfile(self::MAX_WIDTH, self::MAX_HEIGHT, true);
		$imageResizer = new JVideo2_ImageResizer();
		$resizedImage = $imageResizer->resize($image, $imageProfile);

		$this->deleteExistingAvatarImages($avatarFolderPath, $userId);

		imagejpeg($resizedImage, $newFilePath, 80);
	}

	private function openImage($originalFileName, $filePath)
	{
		$ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
		$image = false;

		switch ($ext)
		{
			case 'gif':
				$image = imagecreatefromgif($filePath);
				break;
			case 'jpg':
			case 'jpeg':
				$image = imagecreatefromjpeg($filePath);
				break;
			case 'png':
				$image = imagecreatefrompng($filePath);
				break;
		}

		if ($image === false)
		{
			throw new Exception(JText::_('JV_ERROR_UNABLE_TO_OPEN_IMAGE'));
		}

		return $image;
	}

	private function deleteExistingAvatarImages($avatarFolderPath, $userId)
	{
		$path = $avatarFolderPath . '/';
		$dirHandle = @opendir($path);

		if ($dirHandle === false)
		{
			throw new Exception(JText::_('JV_ERROR_GARBAGE_COLLECTING'));
		}

		while ($file = readdir($dirHandle))
		{
			if (substr($file, 0, strlen(strval($userId)) + 1) == $userId . '_')
			{
				unlink($path . $file);
			}
		}
	}
}