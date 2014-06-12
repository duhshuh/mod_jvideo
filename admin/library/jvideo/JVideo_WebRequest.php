<?php
require_once dirname(__FILE__) . '/JVideo_Factory.php';

class JVideo_WebRequest
{
	public function get($url, $timeout = 5)
	{
		$ch = $this->getCurlHandle($url, $timeout);
		$this->setProxySettingIfNecessary($ch);

		$data = curl_exec($ch);
		
		curl_close($ch);
		
		return $data;
	}
	
	private function getCurlHandle($url, $timeout)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		//disabled for security: curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		return $ch;
	}
	
	private function setProxySettingIfNecessary($ch)
	{
        $config = JVideo_Factory::getConfig();
		
		if ($config->proxyEnabled)
		{
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			$this->setProxyHostAndPort($ch, $config);
			$this->setProxyUsernameAndPassword($ch, $config);
			$this->setProxyTimeout($ch, $config);
		}
	}
	
	private function setProxyHostAndPort($ch, $config)
	{
		if (stripos($config->proxyHost, "http://") === false)
		{
			curl_setopt($ch, CURLOPT_PROXY, "http://" . $config->proxyHost . ":" . $config->proxyPort);
		}
		else
		{
			curl_setopt($ch, CURLOPT_PROXY, $config->proxyHost);
		}
	}
	
	private function setProxyUsernameAndPassword($ch, $config)
	{
		if ($config->proxyUsername != "" && $config->proxyPassword != "")
		{
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $config->proxyUsername . ":" . $config->proxyPassword);
		}
	}
	
	private function setProxyTimeout($ch, $config)
	{
		if ((int)$config->proxyTimeout > 0)
		{
			curl_setopt($ch, CURLOPT_TIMEOUT, $config->proxyTimeout);
		}
	}
}