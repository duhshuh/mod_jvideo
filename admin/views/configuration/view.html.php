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

jimport('joomla.application.component.view');
jvimport2('Web.AssetManager');

class JVideoViewConfiguration extends JViewLegacy
{	
	function display($tpl = null)
	{
		$user = JUser::getInstance();
        $uri = JFactory::getURI();

		$doc = JFactory::getDocument();
        
        JVideo2_AssetManager::includeJQuery();
        JVideo2_AssetManager::includeJQueryUI();
        JVideo2_AssetManager::includeAdminCoreJs();
        JVideo2_AssetManager::includeAdminCoreCss();
        $doc->addScriptDeclaration('jvideo.configuration.setupTabs();');
		
		$this->setToolBar();
		
		$profileTree = $this->getPluginTree("profile");
		$videoTree = $this->getPluginTree("video");
		$commentsTree = $this->getPluginTree("comments");
		
		$profileOpt[] 	= JHtml::_( 'select.option', 'default', JText::_( 'JV_DEFAULT' ), 'value', 'text');
		$profileTree 	= array_merge( $profileOpt, $profileTree );
		
		$videoOpt[] 	= JHtml::_( 'select.option', 'default', JText::_( 'JV_INFINOVISION' ), 'value', 'text');
		$videoTree 		= array_merge( $videoOpt, $videoTree );
		
		$commentsOpt[] 	= JHtml::_( 'select.option', 'default', JText::_( 'JV_DEFAULT_NONE' ), 'value', 'text');
		$commentsTree 	= array_merge( $commentsOpt, $commentsTree );
		
		$seoFileExtTree = array(array("text" => "None", "value" => "", "disable" => ""),
							array("text" => "htm", "value" => "htm", "disable" => ""),
							array("text" => "html", "value" => "html", "disable" => ""),
							array("text" => "php", "value" => "php", "disable" => "")
							);
		
		$fullSyncInterval = array(array("text" => "once per hour", "value" => "1", "disable" => ""),
							array("text" => "every 2 hours", "value" => "2", "disable" => ""),
							array("text" => "every 4 hours", "value" => "4", "disable" => ""),
							array("text" => "every 8 hours", "value" => "8", "disable" => ""),
							array("text" => "every 12 hours", "value" => "12", "disable" => ""),
							array("text" => "every 24 hours", "value" => "24", "disable" => ""),
							array("text" => "manual sync only", "value" => "-1", "disable" => "")
							);
							
		$lists['gid']					= JHtml::_('access.usergroup', 'gid', JRequest::getVar('min_gid'), 'size="10"', false);
		$lists['minApprGroup']			= JHtml::_('access.usergroup', 'minApprGroup', JRequest::getVar('minApprGroup'), 'size="10"', false);
		$lists['profileSystem']			= JHtml::_('select.genericlist', $profileTree, 'profileSystem', '', 'value', 'text', JRequest::getVar('profileSystem'));
		$lists['videoSystem']			= JHtml::_('select.genericlist', $videoTree, 'videoSystem', 'disabled="disabled"', 'value', 'text', JRequest::getVar('videoSystem'));
		$lists['commentsSystem']		= JHtml::_('select.genericlist', $commentsTree, 'commentsSystem', '', 'value', 'text', JRequest::getVar('commentsSystem'));
		$lists['proxyEnabled']			= JHtml::_('select.booleanlist', 'proxyEnabled', 'class="inputbox"', JRequest::getInt("proxyEnabled"));
		$lists['cacheThumbnails']		= JHtml::_('select.booleanlist', 'cacheThumbnails', 'class="inputbox"', JRequest::getInt("cacheThumbnails"));
		$lists['seoEnabled']			= JHtml::_('select.booleanlist', 'seoEnabled', 'class="inputbox"', JRequest::getInt("seoEnabled"));
		$lists['seoFileExtension']		= JHtml::_('select.genericlist', $seoFileExtTree, 'seoFileExtension', '', 'value', 'text', JRequest::getVar('seoFileExtension'));
		$lists['fullSyncInterval']		= JHtml::_('select.genericlist', $fullSyncInterval, 'fullSyncInterval', '', 'value', 'text', JRequest::getVar('fullSyncInterval'));
		$lists['thumbFaderEnabled']		= JHtml::_('select.booleanlist', 'thumbFaderEnabled', 'class="inputbox"', JRequest::getInt("thumbFaderEnabled"));		
		$lists['reqAdminVidAppr']		= JHtml::_('select.booleanlist', 'reqAdminVidAppr', 'class="inputbox"', JRequest::getInt("reqAdminVidAppr"));		
		$lists['showEmbed']				= JHtml::_('select.booleanlist', 'showEmbed', 'class="inputbox"', JRequest::getInt("showEmbed"));
        $lists['showInfoBox']			= JHtml::_('select.booleanlist', 'showInfoBox', 'class="inputbox"', JRequest::getInt("showInfoBox"));
        $lists['showAuthor']			= JHtml::_('select.booleanlist', 'showAuthor', 'class="inputbox"', JRequest::getInt("showAuthor"));
        $lists['showJoinDate']			= JHtml::_('select.booleanlist', 'showJoinDate', 'class="inputbox"', JRequest::getInt("showJoinDate"));
        $lists['showVideoCount']		= JHtml::_('select.booleanlist', 'showVideoCount', 'class="inputbox"', JRequest::getInt("showVideoCount"));
        $lists['showDateAdded']			= JHtml::_('select.booleanlist', 'showDateAdded', 'class="inputbox"', JRequest::getInt("showDateAdded"));
        $lists['showCategories']		= JHtml::_('select.booleanlist', 'showCategories', 'class="inputbox"', JRequest::getInt("showCategories"));
        $lists['showTags']				= JHtml::_('select.booleanlist', 'showTags', 'class="inputbox"', JRequest::getInt("showTags"));
        $lists['showLinkToVideo']		= JHtml::_('select.booleanlist', 'showLinkToVideo', 'class="inputbox"', JRequest::getInt("showLinkToVideo"));
        $lists['showViews']				= JHtml::_('select.booleanlist', 'showViews', 'class="inputbox"', JRequest::getInt("showViews"));
        $lists['showDescription']		= JHtml::_('select.booleanlist', 'showDescription', 'class="inputbox"', JRequest::getInt("showDescription"));
        $lists['showSocialButtons']		= JHtml::_('select.booleanlist', 'showSocialButtons', 'class="inputbox"', JRequest::getInt("showSocialButtons"));
		$lists['showRatings']			= JHtml::_('select.booleanlist', 'showRatings', 'class="inputbox"', JRequest::getInt("showRatings"));
        $lists['showLinkback']			= JHtml::_('select.booleanlist', 'showLinkback', 'class="inputbox"', JRequest::getInt("showLinkback"));
		$lists['autoPlayOnLoad']		= JHtml::_('select.booleanlist', 'autoPlayOnLoad', 'class="inputbox"', JRequest::getInt("autoPlayOnLoad"));		
        $lists['enforceAnonDurLimit']	= JHtml::_('select.booleanlist', 'enforceAnonDurLimit', 'class="inputbox"', JRequest::getInt("enforceAnonDurLimit"));
        $lists['blockAnonViewers']		= JHtml::_('select.booleanlist', 'blockAnonViewers', 'class="inputbox"', JRequest::getInt("blockAnonViewers"));
				
		$checked_var = "CHECKED";
		$notChecked = "";
		switch (JRequest::getVar('aspect_constraint'))
		{
			case "0":
				$this->assignRef('aspect_no', $checked_var);
				$this->assignRef('aspect_16_9', $notChecked);
				$this->assignRef('aspect_4_3', $notChecked);
				break;
			case "1":
				$this->assignRef('aspect_no', $notChecked);
				$this->assignRef('aspect_16_9', $checked_var);
				$this->assignRef('aspect_4_3', $notChecked);
				break;
			case "2":
				$this->assignRef('aspect_no', $notChecked);
				$this->assignRef('aspect_16_9', $notChecked);
				$this->assignRef('aspect_4_3', $checked_var);
				break;
		}
		
		$this->lists = $lists;
		$this->version = JRequest::getVar('version');
		$this->db_id = JRequest::getVar('id') ;
		$this->saved = JRequest::getVar('saved') ;
		$this->video_player_width = JRequest::getVar('video_player_width') ;
		$this->video_player_height = JRequest::getVar('video_player_height') ;
		$this->map_profile_url = JRequest::getVar('map_profile_url');
		$this->map_profile_table = JRequest::getVar('map_profile_table');
		$this->map_profile_id = JRequest::getVar('map_profile_id');
		$this->map_profile_user_id = JRequest::getVar('map_profile_user_id');
		$this->map_profile_avatar = JRequest::getVar('map_profile_avatar');
		$this->map_profile_avatar_prefix = JRequest::getVar('map_profile_avatar_prefix');
		$this->proxyHost = JRequest::getVar('proxyHost');
		$this->proxyPort = JRequest::getInt('proxyPort');
		$this->proxyUsername = JRequest::getVar('proxyUsername');
		$this->proxyPassword = JRequest::getVar('proxyPassword');
		$this->proxyTimeout = JRequest::getInt('proxyTimeout');
		$this->proxyResponseTimeout = JRequest::getInt('proxyResponseTimeout');
		$this->lastFullSync = JRequest::getVar('lastFullSync');
		$this->sizeLimit = JRequest::getInt('sizeLimit');
		$this->recordingLimit = JRequest::getInt('recordingLimit');
		$this->maxVideosPerUser = JRequest::getInt('maxVideosPerUser');
		$this->maxDuration = JRequest::getInt('maxDuration');
        $this->anonDurLimit = JRequest::getInt('anonDurLimit');

        parent::display($tpl);
	}
	
	function setToolBar()
	{
		JToolBarHelper::title( JText::_( 'Configuration' ), 'configuration' );
	}
	
	function getPluginTree($pluginType)
	{
		$rootPath = JPATH_ROOT.'/components/com_jvideo';
		$pluginPath = $rootPath.'/plugins';
		$buildTree = array();
		
		if ($handle = opendir($pluginPath)) {
	    	while (false !== ($dir = readdir($handle))) {
	    		if (is_dir($pluginPath.'/'.$dir) && $dir != "." && $dir != "..") {
	        		$friendlyDir = ucwords(str_replace('_', ' ', $dir));
	        		if (is_file($pluginPath.'/'.$dir.'/'.$pluginType.".php")) {
	        			if ($dir == "default") continue;
		        		$buildTree[] = array(
		        			'text' => $friendlyDir,
	    	    			'value' => $dir,
	        				'disable' => '');
	        		}
	    	    }
	    	}
	    	closedir($handle);
		}
		
		return $buildTree;
	}	
}