<?php
class JVideo2_AssetManager
{
	private static $loadedFiles = array();
	
	public static function includeJQuery()
	{
		self::includeScript('/media/com_jvideo/site/js/jquery-1.7.1.min.js');
	}
	
	public static function includeJQueryUI()
	{
		self::includeScript('/media/com_jvideo/admin/js/jquery-ui-1.8.21.min.js');
		self::includeStyleSheet('/media/com_jvideo/admin/css/jquery/redmond/redmond.css');
	}

	public static function includeAdminCoreJs()
	{
		self::includeScript('/media/com_jvideo/admin/js/core.js.php');
	}

	public static function includeAdminCoreCss()
	{
		self::includeStyleSheet('/media/com_jvideo/admin/css/styles.css');
	}

	public static function includeSiteCoreJs()
	{
		self::includeScript('/media/com_jvideo/site/js/core.js.php');
	}

	public static function includeSiteCoreCss()
	{
		self::includeStyleSheet('/media/com_jvideo/site/css/styles.css');
	}

	public static function includeCrossFadeJs()
	{
        self::includeScript('/index.php?option=com_jvideo&amp;view=jsbaseurl&amp;format=raw');
        self::includeScript('/media/com_jvideo/site/js/crossfade.js');
	}

	private static function includeScript($scriptPath)
	{
		if (!isset(self::$loadedFiles[$scriptPath]))
		{
			self::$loadedFiles[$scriptPath] = 1;

			$doc = JFactory::getDocument();
			$doc->addScript(JURI::root(true) . $scriptPath);
		}
	}

	private static function includeStyleSheet($styleSheetPath)
	{
		if (!isset(self::$loadedFiles[$styleSheetPath]))
		{
			self::$loadedFiles[$styleSheetPath] = 1;

			$doc = JFactory::getDocument();
			$doc->addStyleSheet(JURI::root(true) . $styleSheetPath);
		}
	}
}
