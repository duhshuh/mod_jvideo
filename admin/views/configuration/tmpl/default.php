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
?>
<script type="text/javascript">
jvideo.configuration.initExtensionVersionCheck();
</script>
<form action="index.php?option=com_jvideo" method="post" id="adminForm" name="adminForm">
<input type="hidden" name="view" value="configuration" />
<input type="hidden" name="task" value="do_videoconfig" />
<input type="hidden" name="id" value="<?php echo $this->db_id; ?>" />
<div id="configTabs">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#configTabs-1">Overview</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-2">Global Settings</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-3">Permissions</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-4">Integration</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-5">Synchronization</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-6">Proxy</a></li>
        <li class="ui-state-default ui-corner-top"><a href="#configTabs-7">SEO</a></li>
    </ul>
	<div id="configTabs-1">
        <div class="jvideo-element-container">
            <div class="jvideo_config_header">JVideo Status</div>
        </div>
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_COM_VERSION"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo "<span id=\"com_jvideo_version\">" . $this->version['com_jvideo'] . "</span>"; ?>
			</div>
		</div>
        <!--div class="jvideo-element-container">
            <div class="jvideo-element">
                <?php echo JText::_("JV_MOD_GALLERY_VERSION"); ?>
            </div>
            <div class="jvideo-element-right">
                <?php echo "<span id=\"mod_jvideo_version\">" . $this->version['mod_jvideo'] . "</span>"; ?>
            </div>
        </div>
        <div class="jvideo-element-container">
            <div class="jvideo-element">
                <?php echo JText::_("JV_PLG_SEARCH_VERSION"); ?>
            </div>
            <div class="jvideo-element-right">
                <?php echo "<span id=\"plg_jvideo_search_version\">" . $this->version['plg_jvideo_search'] . "</span>"; ?>
            </div>
        </div>
        <div class="jvideo-element-container">
            <div class="jvideo-element">
                <?php echo JText::_("JV_PLG_CONTENT_VERSION"); ?>
            </div>
            <div class="jvideo-element-right">
                <?php echo "<span id=\"plg_jvideo_content_version\">" . $this->version['plg_jvideo_content'] . "</span>"; ?>
            </div>
        </div-->
        <div class="jvideo-element-container">&nbsp;</div>
        <div class="jvideo-element-container">
            <div class="jvideo-element">
                More Information:
            </div>
            <div class="jvideo-element-right">
                <a href="http://jvideo.warphd.com/download" target="_blank">Downloads</a> -
                <a href="http://jvideo.warphd.com/changelog" target="_blank">Changelog</a> -
                <a href="http://jvideo.warphd.com/support" target="_blank">Support</a> - 
                <a href="http://www.warphd.com/" target="_blank">WarpHD.com</a>
            </div>
        </div>
        <div class="clear"> </div>
    </div>
    <div id="configTabs-2">
        <div id="settingsAccordion">
            <h3 class="settingsHeader"><a href="#">Watch Page Settings</a></h3>
            <div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_INFO_BOX"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showInfoBox']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">&nbsp;</div>
                
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_AUTHOR"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showAuthor']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_JOIN_DATE"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showJoinDate']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_VIDEO_COUNT"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showVideoCount']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_VIDEO_ADDED_DATE"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showDateAdded']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_CATEGORIES"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showCategories']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_TAGS"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showTags']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_VIEWS"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showViews']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_DESCRIPTION"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showDescription']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_ALLOW_RATINGS"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showRatings']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_LINK_TO_VIDEO"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showLinkToVideo']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_EMBED"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showEmbed']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_SHOW_LINKBACK"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showLinkback']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_SHOW_SOCIALBUTTONS"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['showSocialButtons']; ?>
                    </div>
                </div>
            </div>
            <h3 class="settingsHeader"><a href="#">Video Player Settings</a></h3>
            <div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_AUTOPLAY"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['autoPlayOnLoad']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_ARC"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="radio" onchange="javascript:update_player_height('width');" name="aspect_constraint" value="1" <?php echo $this->aspect_16_9; ?> /> <?php echo JText::_("JV_SETTINGS_ARC_16X9") ?>
                        <br />
                        <input type="radio" onchange="javascript:update_player_height('width');" name="aspect_constraint" value="2" <?php echo $this->aspect_4_3; ?> /> <?php echo JText::_("JV_SETTINGS_ARC_4X3"); ?>
                        <br />
                        <input type="radio" name="aspect_constraint" value="0" <?php echo $this->aspect_no; ?> /> <?php echo JText::_("JV_SETTINGS_ARC_NONE"); ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_PLAYER_SIZE"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input onkeyup="javascript:update_player_height('width');" type="text" name="video_player_width" value="<?php echo $this->video_player_width; ?>" maxlength=4 size=5 />
                          x
                        <input onkeyup="javascript:update_player_height('height');" type="text" name="video_player_height" value="<?php echo $this->video_player_height; ?>" maxlength=4 size=5 />
                    </div>
                </div>

                <div class="clear"> </div>
            </div>
            <h3 class="settingsHeader"><a href="#">Video Gallery Settings</a></h3>
            <div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_SHOW_THUMB_FADER") ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['thumbFaderEnabled']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_CACHE_THUMBNAILS") ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['cacheThumbnails']; ?>
                    </div>
                </div>
                <div class="clear"> </div>
            </div>
        </div>
	</div>
	
	<div id="configTabs-3">
        <div id="permissionAccordion">
            <h3 class="permissionHeader"><a href="#"><?php echo JText::_('JV_UPLOAD_PERMS'); ?></a></h3>
            <div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_SETTINGS_MIN_UPLOAD_GROUP"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['gid']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_UPLOAD_SIZE_LIMIT"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="text" name="sizeLimit" value="<?php echo $this->sizeLimit; ?>" maxlength=15 />
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_UPLOAD_RECORDING_LIMIT"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="text" name="recordingLimit" value="<?php echo $this->recordingLimit; ?>" maxlength=15 />
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_MAX_DURATION"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="text" name="maxDuration" value="<?php echo $this->maxDuration; ?>" maxlength=15 />
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_MAX_VIDEOS_BY_USER"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="text" name="maxVideosPerUser" value="<?php echo $this->maxVideosPerUser; ?>" maxlength=15 />
                    </div>
                </div>

            </div>
            <h3 class="permissionHeader"><a href="#"><?php echo JText::_('JV_MANAGEMENT_PERMS'); ?></a></h3>
            <div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_GRP_VID_APPR"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['minApprGroup']; ?>
                    </div>
                </div>

                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_REQ_VID_APPR"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['reqAdminVidAppr']; ?>
                    </div>
                </div>
                <div class="clear"> </div>
            </div>

            <h3 class="permissionHeader"><a href="#"><?php echo JText::_('JV_WATCH_PERMS'); ?></a></h3>
            <div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_BLOCK_ANON_VIEWERS"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['blockAnonViewers']; ?>
                    </div>
                </div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_ENFORCE_ANON_DURATION_LIMIT"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <?php echo $this->lists['enforceAnonDurLimit']; ?>
                    </div>
                </div>
                <div class="jvideo-element-container">
                    <div class="jvideo-element">
                        <?php echo JText::_("JV_ANON_DURATION_LIMIT"); ?>
                    </div>
                    <div class="jvideo-element-right">
                        <input type="text" name="anonDurLimit" value="<?php echo $this->anonDurLimit; ?>" maxlength=5 />
                    </div>
                </div>
                <div class="clear"> </div>
            </div>
        </div>
	</div>
	
    <div id="configTabs-4">
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_SETTINGS_VIDEO_SYSTEM"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists['videoSystem']; ?>
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_SETTINGS_COMMENTS_SYSTEM"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists['commentsSystem']; ?>
			</div>
		</div>
			
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_SETTINGS_PROFILE_SYSTEM"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists['profileSystem']; ?>
			</div>
		</div>

        <div class="jvideo-element-container">
			<div class="jvideo-element">&nbsp;</div>
			<div class="jvideo-element-right">&nbsp;</div>
		</div>

        <div class="jvideo-element-container">
            <div id="mappingAccordion">
                <h3 class="mappingHeader"><a href="#">Custom Mapping</a></h3>
                <div>
                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <small><?php echo JText::_("JV_SETTINGS_CM_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_URL"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_url" id="map_profile_url" value="<?php echo $this->map_profile_url == "" ? "index.php?option=com_jvideo&view=user&user_id=#JV_USERID#" : $this->map_profile_url; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_URL_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_TABLE"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_table" id="map_profile_table" value="<?php echo $this->map_profile_table; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_TABLE_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_ID"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_id" id="map_profile_id" value="<?php echo $this->map_profile_id; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_ID_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_USERID"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_user_id" id="map_profile_user_id" value="<?php echo $this->map_profile_user_id; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_USERID_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_AVATAR"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_avatar" id="map_profile_avatar" value="<?php echo $this->map_profile_avatar; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_AVATAR_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="jvideo-element-container">
                        <div class="jvideo-element">
                            <?php echo JText::_("JV_SETTINGS_CM_AVATAR_PREFIX"); ?>
                        </div>
                        <div class="jvideo-element-right">
                            <input type="text" name="map_profile_avatar_prefix" id="map_profile_avatar_prefix" value="<?php echo $this->map_profile_avatar_prefix; ?>" />
                            <small><?php echo JText::_("JV_SETTINGS_CM_AVATAR_PREFIX_TIP"); ?></small>
                        </div>
                    </div>

                    <div class="clear"> </div>
                </div>
            </div>
        </div>
	</div>
	
	<div id="configTabs-5">

		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_NORMAL_SYNC"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo JText::_("JV_NORMAL_SYNC_DESC"); ?> 
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_DASHBOARD_SYNC") ?>
			</div>
			<div class="jvideo-element-right">
				<?php
				echo JText::_("JV_DASHBOARD_SYNC_PERFORM") . " " 
					. $this->lists['fullSyncInterval'];
								
				if (method_exists('JFactory', 'getDate'))
				{
					$dateAdded = JFactory::getDate($this->lastFullSync);
					$dateAdded = $dateAdded->format(JText::_("JV_DASHBOARD_SYNC_LAST_DATE"));
				}
				else
				{
					$dateAdded = $this->lastFullSync;
				}
				
				echo "<div style=\"margin: 15px 0px;\"><a href=\"#\" onClick=\"javascript: document.getElementById('manualSyncDiv').innerHTML = '".JText::_("JV_DASHBOARD_SYNCHRONIZING")."'; manualSync(); return false;\">" . JText::_("JV_DASHBOARD_SYNC_NOW") . "</a>"
					." <span id=\"manualSyncDiv\" style=\"font-size: smaller;\">" . JText::_("JV_DASHBOARD_SYNC_LAST") . " " . $dateAdded . "</span></div>";
                ?>
			</div>
		</div>
        <div class="jvideo-element-container">
            <?php
            echo "<div>" . JText::_("JV_CRON_JOB_TIP_1") . "</div>"
                ."<div style=\"margin: 15px 0px;\"><i>" . JURI::root() . "index.php?option=com_jvideo&view=jvideo&format=raw&task=consoleSync</i></div>"
                ."<div style=\"margin: 15px 0px;\">" . JText::_("JV_CRON_JOB_TIP_2") . "</div>";
            ?>
		</div>

        <div class="clear"> </div>
	</div>
	
	<div id="configTabs-6">
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_DESCRIPTION"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo JText::_("JV_PROXY_MESSAGE") ?> <a href="http://jvideo.warphd.com/support/faq/#proxySettings" target="_blank"><?php echo JText::_("JV_MORE_INFO") ?></a>
			</div>
		</div>
			
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_ENABLE"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists["proxyEnabled"]; ?>
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_HOST"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="text" name="proxyHost" value="<?php echo $this->proxyHost ?>" maxlength="100" />
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_PORT"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="text" name="proxyPort" value="<?php echo $this->proxyPort ?>" maxlength="6" size="4" />
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_USERNAME"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="text" name="proxyUsername" value="<?php echo $this->proxyUsername ?>" maxlength="25" />
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_PASSWORD"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="password" name="proxyPassword" value="<?php echo $this->proxyPassword ?>" maxlength="25" />
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_CONNECT_TO"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="text" name="proxyTimeout" value="<?php echo $this->proxyTimeout ?>" maxlength="6" size="4" />
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_PROXY_RESPONSE_TO"); ?>
			</div>
			<div class="jvideo-element-right">
				<input type="text" name="proxyResponseTimeout" value="<?php echo $this->proxyResponseTimeout ?>" maxlength="6" size="4" />
			</div>
		</div>

        <div class="clear"> </div>
	</div>
	
	<div id="configTabs-7">
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_DESCRIPTION"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo JText::_("JV_SEO_MESSAGE"); ?>
			</div>
		</div>

		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<strong><?php echo JText::_("JV_SEO_ENABLED"); ?></strong>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists["seoEnabled"]; ?>
			</div>
		</div>
		
		<div class="jvideo-element-container">
			<div class="jvideo-element">
				<?php echo JText::_("JV_SEO_FILEEXTENSION"); ?>
			</div>
			<div class="jvideo-element-right">
				<?php echo $this->lists["seoFileExtension"]; ?>
			</div>
		</div>

        <div class="clear"> </div>
	</div>

</div>
</form>
