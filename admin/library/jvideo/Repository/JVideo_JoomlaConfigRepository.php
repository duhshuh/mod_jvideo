<?php
/*
 *    @package    JVideo
 *    @subpackage Library
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
defined('_JEXEC') or die("Cannot use JVideo Joomla repository outside of Joomla");

require_once(dirname(__FILE__) . '/../JVideo_Config.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');

class JVideo_JoomlaConfigRepository
{
	public function getConfig() 
	{
        $db = JFactory::getDBO();

        $sql = "SELECT jvc.* "
            .", AES_DECRYPT(infino_uname,'47e73c7d96e85') as infino_uname_DEC "
            .", AES_DECRYPT(infino_pass,'47e73c7d96e85') as infino_pass_DEC "
            ." FROM #__jvideo_config AS jvc ";
        $db->setQuery($sql);

		return $db->loadObject();
	}
	
	public function update(JVideo_Config $config)
	{
		$db = JFactory::getDBO();
        $inputFilter = JFilterInput::getInstance();
		
		$sql = "UPDATE #__jvideo_config SET "
			."min_gid = " . $inputFilter->clean($config->minGID, 'INT') . ","
			."show_embeded = " . $inputFilter->clean($config->showEmbedded, 'INT') . ","
			."auto_play = " . $inputFilter->clean($config->autoPlay, 'INT') . ","
			."has_ratings = " . $inputFilter->clean($config->hasRatings, 'INT') . ","
			."video_player_height = " . $inputFilter->clean($config->videoPlayerHeight, 'INT') . ","
			."video_player_width = " . $inputFilter->clean($config->videoPlayerWidth, 'INT') . ","
			."aspect_constraint = " . $inputFilter->clean($config->aspectConstraint, 'INT') . ","
			."profile_system = " . $db->quote($config->profileSystem) . ","
			."video_system = " . $db->quote($config->videoSystem) . ","
			."comments_system = " . $db->quote($config->commentsSystem) . ","
			."map_profile_url = " . $db->quote($config->mapProfileURL) . ","
			."map_profile_table = " . $db->quote($config->mapProfileTable) . ","
			."map_profile_id = " . $db->quote($config->mapProfileID) . ","
			."map_profile_user_id = " . $db->quote($config->mapProfileUserID) . ","
			."map_profile_avatar = " . $db->quote($config->mapProfileAvatar) . ","
			."map_profile_avatar_prefix = " . $db->quote($config->mapProfileAvatarPrefix) . ","
			."proxyEnabled = " . $inputFilter->clean($config->proxyEnabled, 'INT') . ","
			."proxyHost = " . $db->quote($config->proxyHost) . ","
			."proxyPort = " . $inputFilter->clean($config->proxyPort, 'INT') . ","
			."proxyUsername = " . $db->quote($config->proxyUsername) . ","
			."proxyPassword = " . $db->quote($config->proxyPassword) . ","
			."proxyTimeout = " . $inputFilter->clean($config->proxyTimeout, 'INT') . ","
			."proxyResponseTimeout = " . $inputFilter->clean($config->proxyResponseTimeout, 'INT') . ","
			."require_admin_vid_appr = " . $inputFilter->clean($config->requireAdminVidAppr, 'INT') . ","
			."min_appr_gid = " . $inputFilter->clean($config->minApprGID, 'INT') . ","
			."cacheThumbnails = " . $inputFilter->clean($config->cacheThumbnails, 'INT') . ","
			."seoEnabled = " . $inputFilter->clean($config->seoEnabled, 'INT') . ","
			."seoFileExtension = " . $db->quote($config->seoFileExtension) . ","
			."fullSyncInterval = " . $inputFilter->clean($config->fullSyncInterval, 'INT') . ","
			."thumbFaderEnabled = " . $inputFilter->clean($config->thumbFaderEnabled, 'INT') . ","
			."sizeLimit = " . $inputFilter->clean($config->sizeLimit, 'INT') . ","
			."recordingLimit = " . $inputFilter->clean($config->recordingLimit, 'INT') . ","
			."maxVideosPerUser = " . $inputFilter->clean($config->maxVideosPerUser, 'INT') . ","
			."maxDuration = " . $inputFilter->clean($config->maxDuration, 'INT') . ","
            ."showLinkback = " . $inputFilter->clean($config->showLinkback, 'INT') . ","
            ."anonDurLimit = " . $inputFilter->clean($config->anonDurLimit, 'INT') . ","
            ."enforceAnonDurLimit = " . $inputFilter->clean($config->enforceAnonDurLimit, 'INT') . ","
            ."blockAnonViewers = " . $inputFilter->clean($config->blockAnonViewers, 'INT') . ","
            ."showInfoBox = " . $inputFilter->clean($config->showInfoBox, 'INT') . ","
            ."showAuthor = " . $inputFilter->clean($config->showAuthor, 'INT') . ","
            ."showJoinDate = " . $inputFilter->clean($config->showJoinDate, 'INT') . ","
            ."showVideoCount = " . $inputFilter->clean($config->showVideoCount, 'INT') . ","
            ."showDateAdded = " . $inputFilter->clean($config->showDateAdded, 'INT') . ","
            ."showCategories = " . $inputFilter->clean($config->showCategories, 'INT') . ","
            ."showTags = " . $inputFilter->clean($config->showTags, 'INT') . ","
            ."showLinkToVideo = " . $inputFilter->clean($config->showLinkToVideo, 'INT') . ","
            ."showViews = " . $inputFilter->clean($config->showViews, 'INT') . ","
            ."showDescription = " . $inputFilter->clean($config->showDescription, 'INT') . ","
            ."showSocialButtons = " . $inputFilter->clean($config->showSocialButtons, 'INT') . ","
            ."installStatus = " . $db->quote($config->installStatus) . ";";
		
		$db->setQuery($sql);
		$db->execute();
	}
}