<?php
/*
 *    @package    JVideo
 *    @subpackage Library
 *    @link http://jvideo.warphd.com
 *    @copyright (C) 2007 - 2010 Warp
 *    @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(dirname(__FILE__) . '/../../infin-lib.php');
require_once(dirname(__FILE__) . '/../JVideo_Config.php');
require_once(dirname(__FILE__) . '/../JVideo_IVideoRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_RemoteService.php');
require_once(dirname(__FILE__) . '/../JVideo_Factory.php');

class JVideo_WarpVideoRepository extends JVideo_RemoteService implements JVideo_IVideoRepository
{
    public function __construct()
    {
        parent::__construct();

        $this->addRemoteService(
            new InfinovationVideo(
                $this->config->infinoAccountKey,
                $this->config->infinoSecretKey));
    }

    public function update(JVideo_Video $video)
    {
        $this->remoteService->updateVideo(
            $video->getInfinoVideoID(),
            $video->getVideoTitle(),
            $video->getVideoDescription(),
            $video->getUserID(),
            $video->getTags(),
            $video->getVideoURL("", true));
    }

    public function delete(JVideo_Video $video)
    {
        try
        {
            $this->remoteService->deleteVideo($video->getInfinoVideoID());
        }
        catch (Exception $ex)
        {
            if (!$this->isVideoNotFoundException($ex))
            {
                throw $ex;
            }
        }
    }

    private function isVideoNotFoundException(Exception $ex)
    {
        return strpos($ex->getMessage(), 'Video not found:') !== false;
    }

    public function getNewVideoGuid()
    {
        return $this->remoteService->getNewVideoGuid();
    }

    public function getFlashVarsForUploader($videoGuid, $uri)
    {
        return $this->remoteService->getUploaderFlashVars(
                    $videoGuid, $uri, 1,
					$this->config->sizeLimit,
					$this->config->recordingLimit,
					$this->config->maxDuration);
    }

    public function getVideoPlayInfo($videoGuid)
    {
        return $this->remoteService->getVideoPlayInfo($videoGuid);
    }

    public function getVideoMobileUrl($videoGuid)
    {
        return $this->remoteService->getVideoMobileUrl($videoGuid);
    }

    public function insertStub($videoGuid, $userId) { return null; }
    public function getVideoById($videoId) { return null; }
    public function getVideoByGuid($videoGuid) { return null; }
    public function approve(JVideo_Video $video) { return null; }
    public function unapprove(JVideo_Video $video) { return null; }
    public function feature(JVideo_Video $video) { return null; }
    public function unfeature(JVideo_Video $video) { return null; }
    public function publish(JVideo_Video $video) { return null; }
    public function unpublish(JVideo_Video $video) { return null; }
    public function addHit(JVideo_Video $video) { return null; }
}