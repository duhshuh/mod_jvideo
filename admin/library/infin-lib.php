<?php
/*
 *    @package    JVideo
 *    @subpackage Components
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
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).'/nusoap/nusoap.php');

define('INFIN_VIDEO_SERVICE_URL', 'http://manage.warphd.com/soap/v2/video/');
define('INFIN_VIDEO_SERVICE_TRANSITION_URL', 'http://manage.warphd.com/soap/v3/video/');
define('INFIN_ACCOUNT_SERVICE_URL', 'https://secure.warphd.com/soap/v1/account/');
define('INFIN_AUTHTOKEN_SERVICE_URL', 'https://secure.warphd.com/soap/v1/authtoken/');
define('INFIN_COMMON_SERVICE_URL', 'https://secure.warphd.com/soap/v1/common/');
define('INFIN_PROJECT_SERVICE_URL', 'http://manage.warphd.com/soap/v1/project/');

class InfinovationAuthToken extends InfinovationSoapBase
{
	private $accountKey;
	private $authTokenKey;

	public function __construct($accountKey, $secretKey)
	{
		$this->accountKey = $accountKey;
		parent::__construct(INFIN_AUTHTOKEN_SERVICE_URL, $secretKey);
	}

	public function createAuthToken()
	{
        $remoteAddress = $this->isValidIPAddress($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";

		$params = array('accountKey' => $this->accountKey, 'clientIP' => $remoteAddress, 'expires' => $this->getExpireDate());
		$result = $this->doSoapCall('CreateAuthToken', $params);
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);

		$resultNode = $xmlDoc->getElementsByTagName('authToken')->item(0);

		if ($resultNode->getAttribute('authTokenKey') == '-1')
		{
			throw new Exception($resultNode->getAttribute('msg'));
		}

		$this->authTokenKey = $resultNode->getAttribute('authTokenKey');

		$authToken = array('accountKey' => $resultNode->getAttribute('accountKey'), 'authTokenKey' => $resultNode->getAttribute('authTokenKey'));

		return $authToken;
	}

	public function getCredentials()
	{
		$params = array('authTokenKey' => $this->authTokenKey);
		$result = $this->doSoapCall('GetCredentials', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);

		$resultNode = $xmlDoc->getElementsByTagName('account')->item(0);

		if ($resultNode->getAttribute('accountKey') == '-1')
		{
			throw new Exception($resultNode->getAttribute('msg'));
		}

		$acctInfo = array('accountKey' => $resultNode->getAttribute('accountKey'), 'secretKey' => $resultNode->getAttribute('secretKey'));

		return $acctInfo;
	}

    private function isValidIPAddress($ip)
    {
        if (!empty($ip) && ip2long($ip) != -1)
        {
            $reserved_ips = array (
                array('0.0.0.0','2.255.255.255'),
                array('10.0.0.0','10.255.255.255'),
                array('127.0.0.0','127.255.255.255'),
                array('169.254.0.0','169.254.255.255'),
                array('172.16.0.0','172.31.255.255'),
                array('192.0.2.0','192.0.2.255'),
                array('192.168.0.0','192.168.255.255'),
                array('255.255.255.0','255.255.255.255')
            );

            foreach ($reserved_ips as $r)
            {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

class InfinovationAccount extends InfinovationSoapBase
{
	private $domainName;
	private $username;

	public function __construct($domainName, $username, $password)
	{
		$this->domainName = $domainName;
		$this->username = $username;
		parent::__construct(INFIN_ACCOUNT_SERVICE_URL, sha1($password));
	}

	/**
	 * Gets accountKey and secretKey given valid account login info
	 *
	 * @return array	Array with keys 'accountKey' and 'secretKey'
	 */
	public function getCredentials()
	{
		$params = array('domainName' => $this->domainName, 'username' => $this->username, 'expires' => $this->getExpireDate());
		$result = $this->doSoapCall('GetCredentials', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);
		$resultNode = $xmlDoc->getElementsByTagName('account')->item(0);

		if ($resultNode->getAttribute('accountKey') == '-1')
		{
			throw new Exception($resultNode->getAttribute('msg'));
		}

		$acctInfo = array('accountKey' => $resultNode->getAttribute('accountKey'), 'secretKey' => $resultNode->getAttribute('secretKey'));

		return $acctInfo;
	}
}

class InfinovationCommon extends InfinovationSoapBase
{
	public function __construct()
	{
		parent::__construct(INFIN_COMMON_SERVICE_URL, null);
	}


	/**
	 * Generates an expiration date string for SOAP calls
	 *
	 * @param int $seconds	Number of seconds until expiration
	 *
	 * @return Date string
	 */
	public function getExpireDate($seconds = 30)
	{
		$result = $this->doSoapCall('GetServerTime');

		return gmdate('c', $result + $seconds);
	}
}

class InfinovationProject extends InfinovationSoapBase
{
	private $projectName;

	public function __construct($projectName)
	{
		$this->projectName = $projectName;
		parent::__construct(INFIN_PROJECT_SERVICE_URL, null);
	}

	/**
	 * Return project version
	 *
	 * @return version
	 */
	public function getProjectVersion()
	{
		$params = array('projectName' => $this->projectName);
		$result = $this->doSoapCall('GetProjectVersion', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);
		$resultNode = $xmlDoc->getElementsByTagName('project')->item(0);

		if ($resultNode->getAttribute('success') != '1')
		{
			throw new Exception($resultNode->getAttribute('msg'));
		}

		$version = $resultNode->getAttribute('version');

		return $version;
	}
}


class InfinovationVideo extends InfinovationSoapBase
{
	private $accountKey;

	public function __construct($accountKey, $secretKey)
	{
		$this->accountKey = $accountKey;
		parent::__construct(INFIN_VIDEO_SERVICE_URL, $secretKey);
	}

	/**
	 * Gets a new video identifier
	 *
	 * @return string	New video identifier
	 */
	public function getNewVideoGuid()
	{
		$params = array('accountKey' => $this->accountKey, 'expires' => $this->getExpireDate());
		$result = $this->doSoapCall('GetNewVideoGuid', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);
		$resultNode = $xmlDoc->getElementsByTagName('result')->item(0);

		if ($resultNode->getAttribute('success') != '1')
		{
			throw new Exception($resultNode->getAttribute('msg'));
		}

		return $resultNode->getAttribute('videoGuid');
	}

	/**
	 * Locates and starts (if necessary) a valid upload server for a specified videoguid
	 *
	 * @param string $videoGuid	Video guid
	 *
	 * @return string	Full url to the upload page
	 */
	public function getUploadUrl($videoGuid)
	{
		$params = array('accountKey' => $this->accountKey, 'videoGuid' => $videoGuid);
		$result = $this->doSoapCall('GetUploadUrl', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($result);
		$resultNode = $xmlDoc->getElementsByTagName('result')->item(0);

		$status = intval($resultNode->getAttribute('status'));

		switch ($status)
		{
			case InfinovationServerStatus::Pending:
				throw new InfinovationServerPendingException();
				break;

			case InfinovationServerStatus::Error:
				throw new Exception($resultNode->getAttribute('msg'));
				break;
		}

		return $resultNode->getAttribute('msg');
	}

	/**
	 * Gets all videos that have been completed since the supplied videoguid
	 *
	 * @param string $lastVideoGuid	Last successful video guid
	 *
	 * @return array	Array of InfinovationVideoInfo objects
	 */
	public function getCompletedVideos($lastVideoGuid = null)
	{
		$params = array('accountKey' => $this->accountKey, 'lastVideoGuid' => $lastVideoGuid, 'expires' => $this->getExpireDate());
		$resultXml = $this->doSoapCall('GetCompletedVideos', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);
		$videosNode = $xmlDoc->getElementsByTagName('videos');

		if ($videosNode->length > 0)
		{
			$videosNode = $videosNode->item(0);

			if ($videosNode->getAttribute('success') == '1')
			{
				$videoNodes = $xmlDoc->getElementsByTagName('video');

				$videos = array();

				for ($i = 0; $i < $videoNodes->length; $i++)
				{
					$videoNode = $videoNodes->item($i);

					$video = new InfinovationVideoInfo();
					$video->videoGuid = $videoNode->getAttribute('videoGuid');
					$video->duration = $videoNode->getAttribute('duration');
					$video->conversionEndDate = $videoNode->getAttribute('conversionEndDate');
					$video->title = $videoNode->getAttribute('title');
					$video->description = $videoNode->getAttribute('description');
					$video->ownerRef = $videoNode->getAttribute('ownerRef');
					$video->tags = $videoNode->getAttribute('tags');
					$video->status = $videoNode->getAttribute('status');
					$video->url = $videoNode->getAttribute('url');

					if ($videoNode->hasChildNodes())
					{
						for ($j = 0; $j < $videoNode->childNodes->length; $j++)
						{
							$thumbNode = $videoNode->childNodes->item($j);
							if ($thumbNode->nodeName == 'thumbnail')
							{
								$thumb = new InfinovationThumbInfo();
								$thumb->url = $thumbNode->getAttribute('url');
								$thumb->timeIndex = $thumbNode->getAttribute('timeIndex');
								$thumb->width = $thumbNode->getAttribute('width');
								$thumb->height = $thumbNode->getAttribute('height');
								$video->thumbs[] = $thumb;
							}
						}
					}

					$videos[] = $video;
				}

				return $videos;
			}
			else
			{
				throw new Exception($videosNode->getAttribute('msg'));
			}
		}

		throw new Exception('Unexpected response: ' . $resultXml);
	}


	/**
	 * Gets all videos
	 *
	 * @return array	Array of InfinovationVideoInfo objects
	 */
	public function getVideos()
	{
		$params = array('accountKey' => $this->accountKey, 'expires' => $this->getExpireDate());
		$resultXml = $this->doSoapCall('GetVideos', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);
		$videosNode = $xmlDoc->getElementsByTagName('videos');

		if ($videosNode->length > 0)
		{
			$videosNode = $videosNode->item(0);

			if ($videosNode->getAttribute('success') == '1')
			{
				$videoNodes = $xmlDoc->getElementsByTagName('video');

				$videos = array();

				for ($i = 0; $i < $videoNodes->length; $i++)
				{
					$videoNode = $videoNodes->item($i);

					$video = new InfinovationVideoInfo();
					$video->videoGuid = $videoNode->getAttribute('videoGuid');
					$video->duration = $videoNode->getAttribute('duration');
					$video->conversionEndDate = $videoNode->getAttribute('conversionEndDate');
					$video->title = $videoNode->getAttribute('title');
					$video->description = $videoNode->getAttribute('description');
					$video->ownerRef = $videoNode->getAttribute('ownerRef');
					$video->tags = $videoNode->getAttribute('tags');
					$video->status = $videoNode->getAttribute('status');
					$video->url = $videoNode->getAttribute('url');

					if ($videoNode->hasChildNodes())
					{
						for ($j = 0; $j < $videoNode->childNodes->length; $j++)
						{
							$thumbNode = $videoNode->childNodes->item($j);
							if ($thumbNode->nodeName == 'thumbnail')
							{
								$thumb = new InfinovationThumbInfo();
								$thumb->url = $thumbNode->getAttribute('url');
								$thumb->timeIndex = $thumbNode->getAttribute('timeIndex');
								$thumb->width = $thumbNode->getAttribute('width');
								$thumb->height = $thumbNode->getAttribute('height');
								$video->thumbs[] = $thumb;
							}
						}
					}

					$videos[] = $video;
				}

				return $videos;
			}
			else
			{
				throw new Exception($videosNode->getAttribute('msg'));
			}
		}

		throw new Exception('Unexpected response: ' . $resultXml);
	}

	/**
	 * Gets all the video data for a specific video
	 *
	 * @param string $videoGuid	videoGuid
	 *
	 * @return InfinovationVideoInfo	Video info object
	 */
	public function getVideoInfo($videoGuid)
	{
		$params = array('accountKey' => $this->accountKey, 'videoGuid' => $videoGuid); // , 'expires' => $this->getExpireDate()
		$resultXml = $this->doSoapCall('GetVideoInfo', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$videoNode = $xmlDoc->getElementsByTagName('video');

		if ($videoNode->length > 0)
		{
			$videoNode = $videoNode->item(0);

			if ($videoNode->getAttribute('status') != '0')
			{
				$video = new InfinovationVideoInfo();
				$video->videoGuid = $videoNode->getAttribute('videoGuid');
				$video->duration = $videoNode->getAttribute('duration');
				$video->conversionEndDate = $videoNode->getAttribute('conversionEndDate');
				$video->title = $videoNode->getAttribute('title');
				$video->description = $videoNode->getAttribute('description');
				$video->ownerRef = $videoNode->getAttribute('ownerRef');
				$video->tags = $videoNode->getAttribute('tags');
				$video->status = $videoNode->getAttribute('status');
				$video->url = $videoNode->getAttribute('url');

				if ($videoNode->hasChildNodes())
				{
					for ($j = 0; $j < $videoNode->childNodes->length; $j++)
					{
						$thumbNode = $videoNode->childNodes->item($j);
						if ($thumbNode->nodeName == 'thumbnail')
						{
							$thumb = new InfinovationThumbInfo();
							$thumb->url = $thumbNode->getAttribute('url');
							$thumb->timeIndex = $thumbNode->getAttribute('timeIndex');
							$thumb->width = $thumbNode->getAttribute('width');
							$thumb->height = $thumbNode->getAttribute('height');
							$video->thumbs[] = $thumb;
						}
					}
				}

				return $video;
			}
			else
			{
				throw new Exception($videoNode->getAttribute('msg'));
			}
		}

		throw new Exception('Unexpected response: ' . $resultXml);
	}

    /**
	 * Gets all the video play data for a specific video
	 *
	 * @param string $videoGuid	videoGuid
	 *
	 * @return InfinovationVideoInfo	Video info object
	 */
	public function getVideoPlayInfo($videoGuid)
	{
        //@todo: Remove when V3 migration complete
        $this->setServiceUrl(INFIN_VIDEO_SERVICE_TRANSITION_URL);

		$params = array('accountKey' => $this->accountKey, 'videoGuid' => $videoGuid);
		$resultXml = $this->doSoapCall('GetVideoPlayInfo', $params);
        
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$videoNode = $xmlDoc->getElementsByTagName('video');

		if ($videoNode->length > 0)
		{
			$videoNode = $videoNode->item(0);

			if ($videoNode->getAttribute('status') != '0')
			{
				$video = new InfinovationVideoInfo();
				$video->videoGuid = $videoNode->getAttribute('videoGuid');
				$video->duration = $videoNode->getAttribute('duration');
				$video->conversionEndDate = $videoNode->getAttribute('conversionEndDate');
				$video->title = $videoNode->getAttribute('title');
				$video->description = $videoNode->getAttribute('description');
				$video->ownerRef = $videoNode->getAttribute('ownerRef');
				$video->tags = $videoNode->getAttribute('tags');
				$video->status = $videoNode->getAttribute('status');
				$video->url = $videoNode->getAttribute('url');

				if ($videoNode->hasChildNodes())
				{
					for ($j = 0; $j < $videoNode->childNodes->length; $j++)
					{
						$videoNodeChild = $videoNode->childNodes->item($j);

                        switch ($videoNodeChild->nodeName)
                        {
                            case 'thumbnail':
                                $thumb = new InfinovationThumbInfo();
                                $thumb->url = $videoNodeChild->getAttribute('url');
                                $thumb->timeIndex = $videoNodeChild->getAttribute('timeIndex');
                                $thumb->width = $videoNodeChild->getAttribute('width');
                                $thumb->height = $videoNodeChild->getAttribute('height');
                                $video->thumbs[] = $thumb;
                                break;
                            case 'instance':
                                $instance = new InfinovationVideoInstance();
                                $instance->videoConversionProfileId = $videoNodeChild->getAttribute('conversionProfileId');
                                $instance->videoConversionProfileName = $videoNodeChild->getAttribute('conversionProfileName');
                                $instance->height = $videoNodeChild->getAttribute('height');
                                $instance->width = $videoNodeChild->getAttribute('width');
                                $instance->videoBitrate = $videoNodeChild->getAttribute('videoBitrate');
                                $instance->audioBitrate = $videoNodeChild->getAttribute('audioBitrate');
                                $video->instances[] = $instance;
                                break;
                            case 'category':
                                break;
                            case 'playerLogo':
                                break;
                            default:
                                break;
                        }
					}
				}

				return $video;
			}
			else
			{
				throw new Exception($videoNode->getAttribute('msg'));
			}
		}

		throw new Exception('Unexpected response: ' . $resultXml);
	}

    /**
	 * Get the IPhone/IPod, Android, etc URL for compatible videos
	 *
	 * @param string $videoGuid	videoGuid
	 *
	 * @return Url string
	 */
	public function getVideoMobileUrl($videoGuid)
	{
        //@todo: Remove when V3 migration complete
        $this->setServiceUrl(INFIN_VIDEO_SERVICE_TRANSITION_URL);

		$params = array('accountKey' => $this->accountKey, 'videoGuid' => $videoGuid);
		$resultXml = $this->doSoapCall('GetVideoIPhoneUrl', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('result');

		if ($resultNode->length > 0)
		{
            $resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '1')
			{
                if ($resultNode->hasChildNodes())
                {
                    $videoNode = $resultNode->childNodes->item(0);

                    $iphoneUrls = new stdClass();
                    $iphoneUrls->url = $videoNode->getAttribute('url');
                    $iphoneUrls->smallThumbnailUrl = $videoNode->getAttribute('smallThumbnailUrl');
                    $iphoneUrls->largeThumbnailUrl = $videoNode->getAttribute('largeThumbnailUrl');
                    
                    return $iphoneUrls;
                }
                else
                {
                    throw new Exception('Unexpected response: ' . $resultXml);
                }
			}
			else
			{
				throw new Exception($resultNode->getAttribute('message'));
			}
		}

		throw new Exception('Unexpected response: ' . $resultXml);
	}

	/**
	 * Updates the properties of a video
	 *
	 * @param string $videoGuid
	 * @param string $title
	 * @param string $description
	 * @param string $ownerRef
	 * @param string $tags
	 */
	public function updateVideo($videoGuid, $title, $description, $ownerRef, $tags, $url)
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid,
			'title' => $title,
			'description' => preg_replace('#\r#', '', $description),
			'ownerRef' => (int) $ownerRef,
			'tags' => $tags,
			'url' => $url,
			'expires' => $this->getExpireDate());

		$resultXml = $this->doSoapCall('UpdateVideo', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('result');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}

	/**
	 * Deletes a video
	 *
	 * @param string $videoGuid
	 */
	public function deleteVideo($videoGuid)
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid,
			'expires' => $this->getExpireDate());
		$resultXml = $this->doSoapCall('DeleteVideo', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('result');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}

	/**
	 * Generates the flashvars string to be passed to the uploader SWF
	 *
	 * @param string $videoGuid
	 * @param bool $allowWebcam
	 * @param string $postUrl
	 *
	 * @return string
	 */
	public function getUploaderFlashVars($videoGuid, $postUrl, $allowWebcam
		, $sizeLimit = 0, $recordingLimit = 0, $maxDuration = 0)
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid);
		$signature = $this->generateSignature('GetUploadUrl', $params);

		return 'AccountKey=' . urlencode($this->accountKey) .
			'&VideoGuid=' . urlencode($videoGuid) .
			'&Signature=' . urlencode($signature) .
			'&PostURL=' . urlencode($postUrl) .
			'&AllowWebcam=' . ($allowWebcam ? 1 : 0) .
			'&SizeLimit=' . (int) $sizeLimit .
			'&RecordingLimit=' . (int) $recordingLimit .
			'&MaxDuration=' . (int) $maxDuration;
	}
	
	/**
	 * Generates the flashvars string to be passed to the video player SWF
	 *
	 * @param string $videoGuid
	 * @param bool $autoplay
	 *
	 * @return string
	 */
	public function getPlayerFlashVars($videoGuid, $autoplay, $anonDurLimit = 0, $enforceAnonDurLimit = false)
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid);
		$signature = $this->generateSignature('GetVideoPlayInfo', $params);

		$flashvars = 'AccountKey=' . urlencode($this->accountKey) .
			'&VideoGuid=' . urlencode($videoGuid) .
			'&Signature=' . urlencode($signature) .
			'&AutoPlay=' . ($autoplay ? 1 : 0);
        
        if ($enforceAnonDurLimit)
            $flashvars .= '&DurationLimit=' . (int) $anonDurLimit;

        return $flashvars;
	}


	/**
	 * Get Categories
	 *
	 */
	public function getCategories( )
	{
		$params = array('accountKey' => $this->accountKey,
			'expires' => $this->getExpireDate());

		$resultsXml = $this->doSoapCall('GetCategories', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultsXml);

		$resultsNode = $xmlDoc->getElementsByTagName('results');

		if ($resultsNode->length > 0)
		{
			$resultsNode = $resultsNode->item(0);

			if ($resultsNode->getAttribute('success') == '0')
			{
				throw new Exception($resultsNode->getAttribute('msg'));
			}

            return $xmlDoc->saveXML();
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultsXml);
		}
	}

	 /*
	  *  Update Categories
      *
	  * @param string $categoryXml
	  */
	public function updateCategories( $categoryXml )
	{
		$params = array('accountKey' => $this->accountKey,
			'categoryXml' => $categoryXml,
			'expires' => $this->getExpireDate());

		$resultsXml = $this->doSoapCall('UpdateCategories', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultsXml);

		$resultsNode = $xmlDoc->getElementsByTagName('results');

		if ($resultsNode->length > 0)
		{
			$resultsNode = $resultsNode->item(0);

			if ($resultsNode->getAttribute('success') == '0')
			{
				throw new Exception($resultsNode->getAttribute('msg'));
			}

            return $xmlDoc->saveXML();
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultsXml);
		}
	}

	/**
	 * Add Video to Category
	 *
	 * @param string $videoGuid
	 * @param string $categoryId
	 */
	public function addToCategory( $videoGuid, $categoryId )
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid,
			'categoryId' => $categoryId,
			'expires' => $this->getExpireDate());

		$resultXml = $this->doSoapCall('AddToCategory', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('result');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}

	/**
	 * Remove Video from Category
	 *
	 * @param string $videoGuid
	 * @param string $categoryId
	 */
	public function removeFromCategory( $videoGuid, $categoryId )
	{
		$params = array('accountKey' => $this->accountKey,
			'videoGuid' => $videoGuid,
			'categoryId' => $categoryId,
			'expires' => $this->getExpireDate());

		$resultXml = $this->doSoapCall('RemoveFromCategory', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('result');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}

	/**
	 *
	 * Get Category Videos
	 *
	 * @param string $videoGuid
	 * @param string $categoryId
	 */
	public function getCategoryVideos( $categoryId, $page, $count )
	{
		$params = array('accountKey' => $this->accountKey,
			'categoryId' => $categoryId,
			'page' => $page,
			'count' => $count,
			'expires' => $this->getExpireDate());

		$resultXml = $this->doSoapCall('GetCategoryVideos', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('results');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}

  	/**
	 * Get VideoCategories
     *
     * Get a batch list of all video categories in account
	 */
	public function getVideoCategories()
	{
		$params = array('accountKey' => $this->accountKey, 'expires' => $this->getExpireDate());

		$resultXml = $this->doSoapCall('GetAllVideoCategories', $params);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($resultXml);

		$resultNode = $xmlDoc->getElementsByTagName('results');

		if ($resultNode->length > 0)
		{
			$resultNode = $resultNode->item(0);

			if ($resultNode->getAttribute('success') == '0')
			{
				throw new Exception($resultNode->getAttribute('msg'));
			}

            if ($resultNode->hasChildNodes())
            {
                $videoCategories = array();
                $videoCategoriesNode = $resultNode->childNodes->item(0);

                if ($videoCategoriesNode->hasChildNodes())
                {
                    for ($j = 0; $j < $videoCategoriesNode->childNodes->length; $j++)
                    {
                        $videoCategoryNode = $videoCategoriesNode->childNodes->item($j);

                        $videoCategory = new InfinovationVideoCategory();
                        $videoCategory->videoGuid = $videoCategoryNode->getAttribute('videoGuid');
                        $videoCategory->categoryId = $videoCategoryNode->getAttribute('categoryId');

                        $videoCategories[] = $videoCategory;
                    }
                }
                
                return $videoCategories;
            }
            else
            {
                throw new Exception("VideoCategories node is missing!");
            }
		}
		else
		{
			throw new Exception('Unexpected response: ' + $resultXml);
		}
	}
}


class InfinovationSoapBase
{
	private $serviceUrl;
	protected $secretKey;
	private $enableProxy = false;
	private $proxyParams = null;


	public function __construct($serviceUrl, $secretKey)
	{
		$this->serviceUrl = $serviceUrl;
		$this->secretKey = $secretKey;
	}

	public function setServiceUrl($serviceUrl)
	{
		$this->serviceUrl = $serviceUrl;
	}

	public function enableProxy()
	{
		$this->enableProxy = true;
	}

	public function disableProxy()
	{
		$this->enableProxy = false;
	}

	public function setProxyParams($proxyHost = false, $proxyPort = false, $proxyUsername = false
		, $proxyPassword = false, $timeout=0, $responseTimeout = 30)
	{
		$this->proxyParams = array('proxyHost' => $proxyHost
			,'proxyPort' => $proxyPort
			,'proxyUsername' => $proxyUsername
			,'proxyPassword' => $proxyPassword
			,'timeout' => $timeout
			,'responseTimeout' => $responseTimeout);
	}

	/**
	 * Performs the SOAP call
	 *
	 * @param string $method
	 * @param array $params
	 *
	 * @return string	Raw SOAP response
	 */
	protected function doSoapCall($method, array $params = null)
	{
		if ($params != null)
		{
			$params['signature'] = $this->generateSignature($method, $params);
		}
		else
		{
			$params = array();
		}

		if ((!$this->enableProxy && !is_null($this->proxyParams)) || is_null($this->proxyParams)) {
			$soapClient = new nusoap_client($this->serviceUrl);
		} else {
			$soapClient = new nusoap_client(
				$this->serviceUrl
				, null
				, array_key_exists('proxyHost', $this->proxyParams) ? $this->proxyParams['proxyHost'] == "" ? null : $this->proxyParams["proxyHost"] : null
				, array_key_exists('proxyPort', $this->proxyParams) ? $this->proxyParams['proxyPort'] == "" ? null : $this->proxyParams["proxyPort"] : null
				, array_key_exists('proxyUsername', $this->proxyParams) ? $this->proxyParams['proxyUsername'] == "" ? null : $this->proxyParams["proxyUsername"] : null
				, array_key_exists('proxyPassword', $this->proxyParams) ? $this->proxyParams['proxyPassword'] == "" ? null : $this->proxyParams["proxyPassword"] : null
				, array_key_exists('proxyTimeout', $this->proxyParams) ? $this->proxyParams['proxyTimeout']  == "" ? null : $this->proxyParams["proxyTimeout"] : null
				, array_key_exists('proxyResponseTimeout', $this->proxyParams) ? $this->proxyParams['proxyResponseTimeout'] == "" ? null : $this->proxyParams["proxyResponseTimeout"] : null
			);
		}

		$result = $soapClient->call($method, $params);
		$this->checkError($soapClient);

		return $result;
	}

	/**
	 * Generates an expiration date string for SOAP calls
	 *
	 * @param int $seconds	Number of seconds until expiration
	 *
	 * @return Date string
	 */
	protected function getExpireDate($seconds = 30)
	{
		$soapCommon = new InfinovationCommon();
		return $soapCommon->getExpireDate($seconds);
	}

	/**
	 * Generates a SOAP method signature
	 *
	 * @param string	$method	SOAP method being called
	 * @param array		$params Array of parameters to SOAP method
	 */
	protected function generateSignature($method, array $params)
	{
		uksort($params, 'strcasecmp');

		$sigText = $method;
		foreach ($params as $key => $value) $sigText .= $key . $value;
		$sig = base64_encode(hash_hmac('sha1', $sigText, $this->secretKey, true));

		return $sig;
	}

	/**
	 * Checks SOAP client for errors
	 *
	 * @param nusoap_client $soapClient
	 */
	private function checkError(nusoap_client $soapClient)
	{
		if ($error = $soapClient->getError())
		{
			throw new Exception($error . "\n\n" . $soapClient->response);
		}
	}
}


class InfinovationServerStatus
{
	const Running = 1;
	const Pending = 2;
	const Error = 3;
}


class InfinovationVideoInfo
{
	public $videoGuid;
	public $duration;
	public $conversionEndDate;
	public $title;
	public $description;
	public $ownerRef;
	public $tags;
	public $thumbs = array();
    public $instances = array();
	public $status;
}


class InfinovationThumbInfo
{
	public $url;
	public $timeIndex;
	public $width;
	public $height;
}

class InfinovationVideoInstance
{
	public $videoConversionProfileId;
	public $videoConversionProfileName;
	public $s3Path;
	public $width;
	public $height;
	public $videoBitrate;
	public $audioBitrate;
}

class InfinovationVideoCategory
{
    public $videoGuid;
    public $categoryId;
}

class InfinovationServerPendingException extends Exception
{
	public function __construct()
	{
		parent::__construct('Server startup pending');
	}
}