<?php


class JVideo2_PlayerRenderer
{
	/**
	 * @var JVideo_Config
	 */
	private $config;
	
	public function __construct(JVideo_Config $config)
	{
		$this->config = $config;
	}
	
	public function generateSwfUrl(JVideo2_IVideo $video)
	{
		/*
		$swfUrl = 'http://files.warphd.com/FlashPlayer.swf' .
					'?AccountKey=' . urlencode($this->config->infinoAccountKey) .
					'&VideoGuid=' . urlencode($video->getVideoGuid()) .
					'&AutoPlay=' . ($this->config->autoPlay ? '1' : '0');
		*/

		$uri = JFactory::getURI();
		$swfUrl = ($uri->isSSL() ? 'https://secure' : 'http://manage') . '.warphd.com/assets/player.swf?v=1&a=' . urlencode($this->config->infinoAccountKey);

		return $swfUrl;
	}
}