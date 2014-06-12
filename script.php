<?php
/*
 *    @package    JVideo
 *    @subpackage Components
 *    @link http://jvideo.warphd.com
 *    @copyright (C) 2007 - 2012 Warp
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

class com_jvideoInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __constructor(JAdapterInstance $adapter)
	{
		
	}
 
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		
	}
 
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		if ($route == 'install' || $route == 'upgrade')
		{
			$result = self::meetsMinimumSystemRequirements();

			if (true !== $result)
			{
				$message = "<div style=\"font-size: 15px; color: #AA2200; font-weight: bold;\">".$result."</div>"
						  ."<div>Installation cannot continue until you resolve the above system requirement!</div>"
						  ."<div>Need help? Visit the <a href=\"http://jvideo.warphd.com\">Support Forums</a> "
						  ."for help or send an email support@warphd.com</div>";

				echo $message;
				return false;
			}
			else
			{
				$installLink = JRoute::_("index.php?option=com_jvideo&view=install");
				$logo = JURI::root(true) . "/media/com_jvideo/admin/images/warp-logo.png";

				$message = <<<INTRO
				<div style="margin: 15px;">
					<img src="{$logo}" alt="Warp Logo" />
				</div>

				<div style="margin: 15px;">
					<input type="button" onClick="window.location='{$installLink}';" value="Continue with Installation"
						style="font-size: larger; background-color: orange; color: white; font-weight: bold; padding: 5px 10px 5px 10px" />
					<noscript><a href="{$installLink}">Continue with Installation</a></noscript>
				</div>

				<div style="margin: 15px;">
					<p>We need to perform a few important tasks to complete the installation.</p>
					<p><span style="color: #d50000; font-weight: bold;">Note:</span>
					If you do not finish the installation, you may experience problems using JVideo</p>
				</div>
INTRO;

				echo $message;

				return true;
			}
		}
	}
 
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
	}
 
	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter)
	{
	}
 
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		$db = JFactory::getDBO();

		$sqlCleanup = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/uninstall/uninstall.mysql.sql');

    	$queries = $db->splitSql($sqlCleanup);
		
    	foreach($queries as $query){
    		$db->setQuery($query);
    		$db->execute();
    	}
	}
	
	
	private static function meetsMinimumSystemRequirements()
	{
		switch(true)
		{
			case self::is_not_valid_PHP():
				return "PHP 5.1.2 or higher is required";
			case self::is_not_valid_MySQL():
				return "MySQL 4.1.22 or higher (MySQL 5 recommended) is required";
			default:
				return true;
		}
	}

	private static function is_not_valid_PHP()
	{
		return version_compare(phpversion(), '5.1.2', '<') === true;
	}

	private static function is_not_valid_MySQL()
	{
		return version_compare(self::getMySQLVersion(), '4.1.22', '<') === true;
	}

	private static function getMySQLVersion()
	{
		$db = JFactory::getDBO();

		$query = "SELECT VERSION() as mysql_version";
		$db->setQuery($query);

		$mysqlVersion = $db->loadResult();

		list($version) = explode('-', $mysqlVersion);
		return $version;
	}
}