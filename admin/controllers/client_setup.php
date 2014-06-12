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

jimport('joomla.application.component.controller');

class JVideoControllerClient_Setup extends JVideoController
{	
	function display($cachable = false, $urlparams = Array())
	{
		switch($this->getTask())
		{
			case "do_client_setup":
				$model = $this->getModel( 'client_setup' );
				$uname = JRequest::getVar('user_name');
				$pass = JRequest::getVar('pass');
				$infindomain = JRequest::getVar('infindomain');
				$success = $model->do_client_setup($uname, $pass, $infindomain);
				JToolBarHelper::save( 'do_client_setup' );
				JRequest::setVar('success', $success);
				break;
			default:
				JToolBarHelper::save( 'do_client_setup' );
				break;
		}
		
		parent::display();
	}
	
	function check_for_guid()
	{
		$model = $this->getModel('client_setup');
		$model->check_for_guid();
	}
}
?>