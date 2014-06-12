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

jimport( 'joomla.application.component.model' );

class JVideoModelClient_Setup  extends JModelLegacy
{
	function check_for_guid()
	{
		$db = JFactory::getDBO();
		$sql = "select id, client_guid from #__jvideo_config";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		$guid = $rows[0]->client_guid;
		$id = $rows[0]->id;
		
		if ($guid == "");
		{
			$guid = uniqid("infinovation_");

			if ($id == "")
			{
				$sql = "insert into #__jvideo_config (client_guid) values('".$guid.")";
				$db->setQuery($sql);
				$db->execute();
			} else {
				$sql = "update #__jvideo_config set client_guid ='".$guid."'";
				$db->setQuery($sql);
				$db->execute();				
			}
		}
	}
	
	function do_client_setup($uname, $pass, $infin_domain)
	{
		
		$aryCredentials = array();
		$infinAccount = new InfinovationAccount($infin_domain, $uname, $pass);

		$db	= JFactory::getDBO();
		
		$sql = "SELECT proxyEnabled, proxyHost, proxyPort,"
				." proxyUsername, proxyPassword, proxyTimeout, proxyResponseTimeout "
				."FROM #__jvideo_config";
				
		$db->setQuery( $sql );
		
		$row = $db->loadObject();
		
		if ((int)$row->proxyEnabled)
		{			
			$infinAccount->enableProxy();
			$infinAccount->setProxyParams(
				  $row->proxyHost
				, $row->proxyPort
				, $row->proxyUsername
				, $row->proxyPassword
				, $row->proxyTimeout
				, $row->proxyResponseTimeout);
		}
		
		try
		{
			$aryCredentials = $infinAccount->getCredentials();
			$isValid = true;
		}
		catch(Exception $e)
		{
			$isValid = false;
			$errorMsg = $e;
		}
		        
		if ($isValid != true)
		{
            if (stristr($errorMsg, 'stack trace')) {
                $errorMsg = substr($errorMsg, 0, stripos($errorMsg, 'stack trace'));
            }
            
			echo "<h1>Error Setting Up Your Account</h1>";
			echo "<p>A few helpful tips:"
				."	<ol>"
				."		<li>Verify that the username & password you are using are correct</li>"
				."		<li>Make sure PHP cURL is installed</li>"
				."		<li>Are you behind a proxy? Some website hosts use a proxy that prevents your request from getting to our server. Setup your proxy settings on the configuration page.</li>"
				."	</ol>"
				."</p>";
			echo "<p><strong>Error:</strong>&nbsp;&nbsp; " . $errorMsg . "</p>";
			echo "<p>&nbsp;</p>";
			
			return false;
		}
		else
		{
			$db = JFactory::getDBO();
			$sql = "select id from #__jvideo_config";
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
				
			$pass = "";
			$id = $rows[0]->id;
			
			if ($id != "")
			{
				$sql = "update #__jvideo_config set infino_acctKey='".$aryCredentials["accountKey"]."', infino_secretKey='".$aryCredentials["secretKey"]."', infino_domain='".$infin_domain."', infino_uname = AES_ENCRYPT('".$uname."', '47e73c7d96e85'), infino_pass= AES_ENCRYPT('".$pass."', '47e73c7d96e85')";
			}
			else
			{
				$sql = "insert into #__jvideo_config (infino_acctKey, infino_secretKey, infino_domain, infino_uname, infino_pass) values('".$aryCredentials["accountKey"]."', '".$aryCredentials["secretKey"]."', '".$infin_domain."', AES_ENCRYPT('".$uname."', '47e73c7d96e85'), AES_ENCRYPT('".$pass."', '47e73c7d96e85'))";
			}
			
			$db->setQuery($sql);
			$db->execute();			
			
			return true;
		}
	}
}

?>