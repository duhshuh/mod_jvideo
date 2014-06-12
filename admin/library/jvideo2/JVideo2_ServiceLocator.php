<?php
require_once dirname(__FILE__) . '/../jvideo/JVideo_ConfigFactory.php';

require_once dirname(__FILE__) . '/Category/JVideo2_CachingCategoryFinder.php';
require_once dirname(__FILE__) . '/Category/JVideo2_DbCategoryFinder.php';
require_once dirname(__FILE__) . '/Database/JVideo2_MySqlInfo.php';
require_once dirname(__FILE__) . '/Install/JVideo2_RequirementsChecker.php';
require_once dirname(__FILE__) . '/Video/JVideo2_CachingVideoFinder.php';
require_once dirname(__FILE__) . '/Video/JVideo2_DbVideoFinder.php';
require_once dirname(__FILE__) . '/Video/JVideo2_PlayerRenderer.php';
require_once dirname(__FILE__) . '/Web/JVideo2_PhpInfo.php';
require_once dirname(__FILE__) . '/Web/JVideo2_PhpIni.php';

class JVideo2_ServiceLocator
{
	private static $instance = null;
	
	private function __construct()
	{
	}
	
	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new JVideo2_ServiceLocator();
		}
		return self::$instance;
	}
	
	/**
	 * @return JVideo2_CachingVideoFinder 
	 */
	public function getVideoFinder()
	{
		return new JVideo2_CachingVideoFinder(new JVideo2_DbVideoFinder());
	}
	
	/**
	 * @return JVideo2_CachingCategoryFinder 
	 */
	public function getCategoryFinder()
	{
		return new JVideo2_CachingCategoryFinder(new JVideo2_DbCategoryFinder());
	}
	
	/**
	 * @return JVideo2_RequirementsChecker 
	 */
	public function getRequirementsChecker()
	{
		return new JVideo2_RequirementsChecker($this->getPhpInfo(), new JVideo2_MySqlInfo());
	}
	
	/**
	 * @return JVideo2_PhpInfo 
	 */
	public function getPhpInfo()
	{
		return new JVideo2_PhpInfo(new JVideo2_PhpIni());
	}
	
	/**
	 * @return JVideo2_PlayerRenderer 
	 */
	public function getPlayerRenderer()
	{
		$config = JVideo_ConfigFactory::create();
		$renderer = new JVideo2_PlayerRenderer($config);
		return $renderer;
	}
}