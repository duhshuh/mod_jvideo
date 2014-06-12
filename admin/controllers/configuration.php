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

class JVideoControllerConfiguration extends JVideoController
{
	function display($cachable = false, $urlparams = Array())
	{
		switch($this->getTask())
		{
			case "do_videoconfig":
				$model = $this->getModel('configuration');
				$model->do_configuration();
				$this->setup_video_display_variables();
				JToolBarHelper::save( 'do_videoconfig' );
				JRequest::setVar('saved', '1');
				break;
			default:
				$model = $this->getModel('configuration');
				$this->setup_video_display_variables();
				JToolBarHelper::save( 'do_videoconfig' );
				break;
		}
		
		parent::display();
	}
	
	function setup_video_display_variables()
	{
		$model = $this->getModel('configuration');
		$config = $model->get_configuration();

        JRequest::setVar('version', $model->getVersion());
		JRequest::setVar('reqAdminVidAppr', $config->requireAdminVidAppr);
		JRequest::setVar('showEmbed', $config->showEmbedded);
		JRequest::setVar('showRatings', $config->hasRatings);
		JRequest::setVar('autoPlayOnLoad', $config->autoPlay);
		JRequest::setVar('video_player_height', $config->videoPlayerHeight);
		JRequest::setVar('video_player_width', $config->videoPlayerWidth);
		JRequest::setVar('aspect_constraint', $config->aspectConstraint);
		JRequest::setVar('profileSystem', $config->profileSystem);
		JRequest::setVar('videoSystem', $config->videoSystem);
		JRequest::setVar('commentsSystem', $config->commentsSystem);
		JRequest::setVar('map_profile_url', $config->mapProfileURL);
		JRequest::setVar('map_profile_table', $config->mapProfileTable);
		JRequest::setVar('map_profile_id', $config->mapProfileID);
		JRequest::setVar('map_profile_user_id', $config->mapProfileUserID);
		JRequest::setVar('map_profile_avatar', $config->mapProfileAvatar);
		JRequest::setVar('map_profile_avatar_prefix', $config->mapProfileAvatarPrefix);
		JRequest::setVar('proxyEnabled', $config->proxyEnabled);
		JRequest::setVar('proxyHost', $config->proxyHost);
		JRequest::setVar('proxyPort', $config->proxyPort);
		JRequest::setVar('proxyUsername', $config->proxyUsername);
		JRequest::setVar('proxyPassword', $config->proxyPassword);
		JRequest::setVar('proxyTimeout', $config->proxyTimeout);
		JRequest::setVar('proxyResponseTimeout', $config->proxyResponseTimeout);
		JRequest::setVar('cacheThumbnails', $config->cacheThumbnails);
		JRequest::setVar('seoEnabled', $config->seoEnabled);
        JRequest::setVar('showInfoBox', $config->showInfoBox);
        JRequest::setVar('showAuthor', $config->showAuthor);
        JRequest::setVar('showJoinDate', $config->showJoinDate);
        JRequest::setVar('showVideoCount', $config->showVideoCount);
        JRequest::setVar('showDateAdded', $config->showDateAdded);
        JRequest::setVar('showCategories', $config->showCategories);
        JRequest::setVar('showTags', $config->showTags);
        JRequest::setVar('showLinkToVideo', $config->showLinkToVideo);
        JRequest::setVar('showViews', $config->showViews);
        JRequest::setVar('showDescription', $config->showDescription);
        JRequest::setVar('showSocialButtons', $config->showSocialButtons);
        JRequest::setVar('showLinkback', $config->showLinkback);
		JRequest::setVar('seoFileExtension', $config->seoFileExtension);
		JRequest::setVar('fullSyncInterval', $config->fullSyncInterval);
		JRequest::setVar('lastFullSync', $config->lastFullSync);
		JRequest::setVar('thumbFaderEnabled', $config->thumbFaderEnabled);
		JRequest::setVar('sizeLimit', $config->sizeLimit);
		JRequest::setVar('recordingLimit', $config->recordingLimit);
		JRequest::setVar('maxVideosPerUser', $config->maxVideosPerUser);
		JRequest::setVar('maxDuration', $config->maxDuration);
        JRequest::setVar('anonDurLimit', $config->anonDurLimit);
        JRequest::setVar('enforceAnonDurLimit', $config->enforceAnonDurLimit);
        JRequest::setVar('blockAnonViewers', $config->blockAnonViewers);

		if ($config->minGID != '') {
			JRequest::setVar('min_gid', $config->minGID);
		}

		if ($config->minApprGID != '') {
			JRequest::setVar('minApprGroup', $config->minApprGID);
		}
	}
	
	function newestVersion()
	{
		$project = new InfinovationProject("com_jvideo");
		$projectVersion = $project->getProjectVersion();
		
		echo $projectVersion;
	}
}