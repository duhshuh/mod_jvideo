<?php
require_once dirname(__FILE__) . '/JVideo2_PhpIni.php';

class JVideo2_PhpInfo
{
	/** @var Jvideo2_PhpIni */
	private $phpIni;
	
	public function __construct(JVideo2_PhpIni $phpIni)
	{
		$this->phpIni = $phpIni;
	}
	
	public function getVersion()
	{
		return phpversion();
	}
	
    public function isSafeModeEnabled()
    {
        return $this->phpIni->getBool('safe_mode');
    }

    public function isCurlInstalled()
    {
        return extension_loaded("curl");
    }

    public function isLibXmlInstalled()
    {
        return defined('LIBXML_DOTTED_VERSION');
    }
	
	public function getLibXmlVersion()
	{
		return LIBXML_DOTTED_VERSION;
	}
	
	public function isGdInstalled()
	{
		return extension_loaded('gd') && function_exists('gd_info');
	}
	
	public function isGdPngSupportInstalled()
	{
		if (!$this->isGdInstalled()) return false;

		$gdInfo = gd_info();
		return array_key_exists('PNG Support', $gdInfo) && $gdInfo['PNG Support'] == 1;
	}
}