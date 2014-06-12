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

const JVIDEO_ASPECT_CONSTRAINT_NONE = 0;
const JVIDEO_ASPECT_CONSTRAINT_16x9 = 1;
const JVIDEO_ASPECT_CONSTRAINT_4x3 = 2;

class JVideo_Config
{
	public $regLevel;

	public $autoPlay;
	public $hasRatings;
	public $bradPos;
	public $hasPNG;
	public $minGID;
	public $clientGUID;
	public $infinoUsername;
	public $infinoPassword;
	public $infinoDomain;
	public $infinoAccountID;
	public $infinoUserID;
	public $infinoAccountKey;
	public $infinoSecretKey;
	public $videoPlayerHeight;
	public $videoPlayerWidth;
	public $aspectConstraint;
	public $profileSystem;
	public $videoSystem;
	public $commentsSystem;
	public $mapProfileURL;
	public $mapProfileTable;
	public $mapProfileID;
	public $mapProfileUserID;
	public $mapProfileUsername;
	public $mapProfileAvatar;
	public $mapProfileAvatarPrefix;
	public $proxyEnabled;
	public $proxyHost;
	public $proxyPort;
	public $proxyUsername;
	public $proxyPassword;
	public $proxyTimeout;
	public $proxyResponseTimeout;
	public $minApprGID;
	public $requireAdminVidAppr;
	public $cacheThumbnails;
	public $seoEnabled;
	public $seoFileExtension;
	public $version;
	public $lastIncrSync;
	public $lastFullSync;
	public $incrSyncInterval;
	public $fullSyncInterval;
	public $thumbFaderEnabled;
	public $sizeLimit;
	public $recordingLimit;
	public $maxVideosPerUser;
	public $maxDuration;
	public $userProfileMenuItemID;
	public $videosMenuItemID;
    public $showEmbedded;
    public $showInfoBox;
    public $showAuthor;
    public $showJoinDate;
    public $showVideoCount;
    public $showDateAdded;
    public $showCategories;
    public $showTags;
    public $showLinkToVideo;
    public $showViews;
    public $showDescription;
    public $showSocialButtons;
    public $showLinkback;
    public $installStatus;
    public $durationLimit;
    public $anonDurLimit;
    public $enforceAnonDurLimit;
    public $blockAnonViewers;
}