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

require_once(dirname(__FILE__).'/JVideo_Config.php');
require_once(dirname(__FILE__).'/JVideo_ConfigRepositoryFactory.php');
require_once(dirname(__FILE__).'/JVideo_Exception.php');

class JVideo_ConfigFactory
{
    private static $config;

	/**
	 * @return JVideo_Config
	 */
	public static function &create()
	{
        if (!is_object(self::$config))
        {
            $repository = JVideo_ConfigRepositoryFactory::create();
            $configData = $repository->getConfig();

            self::$config = new JVideo_Config();
            self::$config->aspectConstraint = intval(@$configData->aspect_constraint);
            self::$config->autoPlay = @$configData->auto_play;
            self::$config->bradPos = @$configData->brad_pos;
            self::$config->clientGUID = @$configData->client_guid;
            self::$config->hasPNG = @$configData->has_png;
            self::$config->hasRatings = @$configData->has_ratings;
            self::$config->infinoAccountKey = @$configData->infino_acctKey;
            self::$config->infinoSecretKey = @$configData->infino_secretKey;
            self::$config->infinoAccountID = @$configData->infino_accountId;
            self::$config->infinoDomain = @$configData->infino_domain;
            self::$config->infinoPassword = @$configData->infino_pass_DEC;
            self::$config->infinoUserID = @$configData->infino_userId;
            self::$config->infinoUsername = @$configData->infino_uname_DEC;
            self::$config->minGID = @$configData->min_gid;
            self::$config->profileSystem = @$configData->profile_system;
            self::$config->regLevel = @$configData->reg_level;
            self::$config->showEmbedded = @$configData->show_embeded;
            self::$config->videoPlayerHeight = @$configData->video_player_height;
            self::$config->videoPlayerWidth = @$configData->video_player_width;
            self::$config->profileSystem = @$configData->profile_system;
            self::$config->videoSystem = @$configData->video_system;
            self::$config->commentsSystem = @$configData->comments_system;
            self::$config->mapProfileURL = @$configData->map_profile_url;
            self::$config->mapProfileTable = @$configData->map_profile_table;
            self::$config->mapProfileID = @$configData->map_profile_id;
            self::$config->mapProfileUserID = @$configData->map_profile_user_id;
            self::$config->mapProfileAvatar = @$configData->map_profile_avatar;
            self::$config->mapProfileAvatarPrefix = @$configData->map_profile_avatar_prefix;
            self::$config->proxyEnabled = @$configData->proxyEnabled;
            self::$config->proxyHost = @$configData->proxyHost;
            self::$config->proxyPort = @$configData->proxyPort;
            self::$config->proxyUsername = @$configData->proxyUsername;
            self::$config->proxyPassword = @$configData->proxyPassword;
            self::$config->proxyTimeout = @$configData->proxyTimeout;
            self::$config->proxyResponseTimeout = @$configData->proxyResponseTimeout;
            self::$config->minApprGID = @$configData->min_appr_gid;
            self::$config->requireAdminVidAppr = @$configData->require_admin_vid_appr;
            self::$config->cacheThumbnails = @$configData->cacheThumbnails;
            self::$config->seoEnabled = @$configData->seoEnabled;
            self::$config->seoFileExtension = @$configData->seoFileExtension;
            self::$config->version = @$configData->version;
            self::$config->lastFullSync = @$configData->lastFullSync;
            self::$config->lastIncrSync = @$configData->lastIncrSync;
            self::$config->fullSyncInterval = @$configData->fullSyncInterval;
            self::$config->incrSyncInterval = @$configData->incrSyncInterval;
            self::$config->thumbFaderEnabled = @$configData->thumbFaderEnabled;
            self::$config->sizeLimit = @$configData->sizeLimit;
            self::$config->recordingLimit = @$configData->recordingLimit;
            self::$config->maxVideosPerUser = @$configData->maxVideosPerUser;
            self::$config->maxDuration = @$configData->maxDuration;
            self::$config->showLinkback = @$configData->showLinkback;
            self::$config->installStatus = @$configData->installStatus;
            self::$config->anonDurLimit = @$configData->anonDurLimit;
            self::$config->enforceAnonDurLimit = @$configData->enforceAnonDurLimit;
            self::$config->blockAnonViewers = @$configData->blockAnonViewers;
            self::$config->showInfoBox = @$configData->showInfoBox;
            self::$config->showAuthor = @$configData->showAuthor;
            self::$config->showJoinDate = @$configData->showJoinDate;
            self::$config->showVideoCount = @$configData->showVideoCount;
            self::$config->showDateAdded = @$configData->showDateAdded;
            self::$config->showCategories = @$configData->showCategories;
            self::$config->showTags = @$configData->showTags;
            self::$config->showLinkToVideo = @$configData->showLinkToVideo;
            self::$config->showViews = @$configData->showViews;
            self::$config->showDescription = @$configData->showDescription;
            self::$config->showSocialButtons = @$configData->showSocialButtons;
        }

		return self::$config;
	}

    public static function destroy()
    {
        self::$config = null;
    }
}