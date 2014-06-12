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

jimport( 'joomla.application.component.view');
jvimport2('Web.AssetManager');

class JVideoViewuser_edit extends JViewLegacy
{
    function display($tpl = null)
    {
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
    	$params = clone($mainframe->getParams('com_jvideo'));
    	
    	$user = JFactory::getUser();
    	if (!$user->id)
		{
			JError::raiseError('403', 'Access denied');
		}
		
    	if (JRequest::getVar('Itemid') == '')
    	{
    		$document->setTitle($document->getTitle() . ' - ' . JText::_("JV_PROFILE_EDIT"));
    	}
    	
    	// 
    	// Validate ID
    	//
		$user_id = JRequest::getInt('user_id');
		if (!$user_id)
		{
			$mainframe->redirect("index.php?option=com_jvideo");
		} 

		/* Break apart birth date */
		$profile = JRequest::getVar('profile');
		$this->birth_month = null;
		$this->birth_day = null;
		$this->birth_year = null;
		$this->age = null;
		
		if (!is_null($profile) && $profile != "" && $profile->getID())
		{
			if (strlen($profile->getBirthdate()) > 0)
			{
				$birthstamp = strtotime($profile->getBirthdate());
				
				$birth_day = date('j', $birthstamp);
				$birth_month = date('n', $birthstamp);
				$birth_year = date('Y', $birthstamp);
			
				$this->birth_month = $birth_month;
				$this->birth_day = $birth_day;
				$this->birth_year = $birth_year;
				$this->age = $this->getAge($profile->getBirthdate());
			}
		}
		else
		{
			$profile = new JVideo_Profile();
		}
		
		$this->profile = $profile;
		$this->user_id = $user_id;
		$this->error = JRequest::getVar('error');
		$this->hasGD = JRequest::getVar('hasGD');
		$this->params = $params;
		$breadcrumbs = $mainframe->getPathWay();
		$breadcrumbs->addItem(JText::_("JV_PROFILE_EDIT"), JRoute::_("index.php?option=com_jvideo&view=user_edit&user_id=".JRequest::getVar("user_id")));
		
		JVideo2_AssetManager::includeSiteCoreCss();
		
		parent::display($tpl);
    }
    
    function getAge($_dob)
    {
    	$dob = date("Y-m-d",strtotime($_dob));
    	$ageparts = explode("-",$dob);
	    $age = date("Y-m-d")-$dob;

    	return (date("nd") < $ageparts[1].str_pad($ageparts[2],2,'0',STR_PAD_LEFT)) ? $age-=1 : $age; 
    }
}
