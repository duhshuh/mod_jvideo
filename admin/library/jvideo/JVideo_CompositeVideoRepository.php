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

require_once(dirname(__FILE__) . '/JVideo_Exception.php');
require_once(dirname(__FILE__) . '/JVideo_IVideoRepository.php');

class JVideo_CompositeVideoRepository implements JVideo_IVideoRepository
{
    private $repositories = array();

    public function add($repository)
    {
        $this->repositories[] = $repository;
    }

    public function update(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->update($video);
        }
    }

    public function delete(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->delete($video);
        }
    }

    public function insertStub($videoGuid, $userId) {
        foreach ($this->repositories as $repository) {
            $videoId = $repository->insertStub($videoGuid, $userId);

            if (null != $videoId) {
                return $videoId;
            }
        }
    }

    public function getVideoById($videoId)
    {
        foreach ($this->repositories as $repository) {
            $video = $repository->getVideoById($videoId);

            if (null != $video) {
                return $video;
            }
        }
    }

    public function getVideoByGuid($videoGuid)
    {
        foreach ($this->repositories as $repository) {
            $video = $repository->getVideoByGuid($videoGuid);

            if (null != $video) {
                return $video;
            }
        }
    }

    public function getNewVideoGuid()
    {
        foreach ($this->repositories as $repository) {
            $videoGuid = $repository->getNewVideoGuid();
            
            if (null != $videoGuid) {
                return $videoGuid;
            }
        }
    }

    public function getFlashVarsForUploader($videoGuid, $uri)
    {
        foreach ($this->repositories as $repository) {
            $flashvars = $repository->getFlashVarsForUploader($videoGuid, $uri);

            if (null != $flashvars) {
                return $flashvars;
            }
        }
    }

    public function approve(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->approve($video);
        }
    }

    public function unapprove(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->unapprove($video);
        }
    }

    public function feature(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->feature($video);
        }
    }

    public function unfeature(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->unfeature($video);
        }
    }

    public function publish(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->publish($video);
        }
    }

    public function unpublish(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->unpublish($video);
        }
    }

    public function addHit(JVideo_Video $video)
    {
        foreach ($this->repositories as $repository) {
            $repository->addHit($video);
        }
    }
}