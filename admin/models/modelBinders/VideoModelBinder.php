<?php
class VideoModelBinder
{
	private $videoRepository;

	public function __construct(JVideo_IVideoRepository $videoRepository)
	{
		$this->videoRepository = $videoRepository;
	}

	public function bindModel()
	{
        $videoId = JRequest::getInt('videoId');
        $video = $this->videoRepository->getVideoById($videoId);

        if (is_null($video)) {
            throw new Exception("Invalid video ID: $videoId");
        }

        $video->setVideoTitle(JRequest::getVar('title'));
        $video->setVideoDescription(JRequest::getVar('desc'));
        $video->setDateAdded(method_exists('JFactory', 'getDate')
            ? JFactory::getDate(JRequest::getVar('dateAdded'))
            : JRequest::getVar('dateAdded'));
        $inputFilter = JFilterInput::getInstance();
        $video->setUserID($inputFilter->clean(JRequest::getVar('authorID'), 'INT'));
        $video->setPublished(JRequest::getVar('published'));
        $featured = JRequest::getVar('featured');
        $blankDate = "0000-00-00 00:00:00";
        $publishUp = JRequest::getVar('publishUp');
        $publishDown = JRequest::getVar('publishDown');
        $publishUp = (trim($publishUp) == $blankDate || trim($publishUp) == "") ? $blankDate : $publishUp;
        $publishDown = (trim($publishDown) == $blankDate || trim($publishDown) == "") ? $blankDate : $publishDown;

        if (method_exists('JFactory', 'getDate')) {
            if ($publishUp != $blankDate) {
                $publishUp = JFactory::getDate($publishUp);
                $publishUp = $publishUp->toSql();
            }

            if ($publishDown != $blankDate) {
                $publishDown = JFactory::getDate($publishDown);
                $publishDown = $publishDown->toSql();
            }
        }

        $validInput = "/[^A-Za-z0-9\\040]/i";
        $tags = JRequest::getVar('tags');
        $tags = strtolower(preg_replace($validInput, "", $tags));

        $video->setPublishUp($publishUp);
        $video->setPublishDown($publishDown);
        $video->setTags($tags);

        if (method_exists('JFactory', 'getDate')) {
            $video->setDateAdded($video->getDateAdded()->toSql());
        }

        return $video;
	}
}