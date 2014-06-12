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

class JVideoModelConfiguration  extends JModelLegacy
{
    function get_configuration()
    {
        return JVideo_ConfigFactory::create();
    }

    function do_configuration()
    {
        $config = JVideo_ConfigFactory::create();

        if (JRequest::getVar('gid') > 0)
        {
            $config->minGID = JRequest::getInt('gid');
        }
        else
        {
            $config->minGID = 18;
        }

        $config->showEmbedded = JRequest::getInt('showEmbed');
        $config->autoPlay = JRequest::getInt('autoPlayOnLoad');
        $config->hasRatings = JRequest::getInt('showRatings');
        $config->requireAdminVidAppr = JRequest::getInt('reqAdminVidAppr');
        $config->minApprGID = JRequest::getInt('minApprGroup');
        $config->videoPlayerHeight = JRequest::getInt('video_player_height');
        $config->videoPlayerWidth = JRequest::getInt('video_player_width');
        $config->aspectConstraint = JRequest::getInt('aspect_constraint');
        $config->proxyHost = JRequest::getVar('proxyHost');
        $config->proxyUsername = JRequest::getVar('proxyUsername');
        $config->proxyPassword = JRequest::getVar('proxyPassword');
        $config->proxyEnabled = JRequest::getInt('proxyEnabled');
        $config->proxyPort = JRequest::getInt('proxyPort');
        $config->proxyTimeout = JRequest::getInt('proxyTimeout');
        $config->proxyResponseTimeout = JRequest::getInt('proxyResponseTimeout');
        $config->cacheThumbnails = JRequest::getInt('cacheThumbnails');
        $config->seoEnabled = JRequest::getInt('seoEnabled');
        $config->seoFileExtension = JRequest::getVar('seoFileExtension');
        $config->fullSyncInterval = JRequest::getVar('fullSyncInterval');
        $config->thumbFaderEnabled = JRequest::getInt('thumbFaderEnabled');
        $config->sizeLimit = JRequest::getInt('sizeLimit');
        $config->recordingLimit = JRequest::getInt('recordingLimit');
        $config->maxVideosPerUser = JRequest::getInt('maxVideosPerUser');
        $config->maxDuration = JRequest::getInt('maxDuration');
        $config->showInfoBox = JRequest::getInt('showInfoBox');
        $config->showAuthor = JRequest::getInt('showAuthor');
        $config->showJoinDate = JRequest::getInt('showJoinDate');
        $config->showVideoCount = JRequest::getInt('showVideoCount');
        $config->showDateAdded = JRequest::getInt('showDateAdded');
        $config->showCategories = JRequest::getInt('showCategories');
        $config->showTags = JRequest::getInt('showTags');
        $config->showLinkToVideo = JRequest::getInt('showLinkToVideo');
        $config->showViews = JRequest::getInt('showViews');
        $config->showDescription = JRequest::getInt('showDescription');
        $config->showSocialButtons = JRequest::getInt('showSocialButtons');
        $config->showLinkback = JRequest::getInt('showLinkback');
        $config->anonDurLimit = JRequest::getInt('anonDurLimit');
        $config->enforceAnonDurLimit = JRequest::getInt('enforceAnonDurLimit');
        $config->blockAnonViewers = JRequest::getInt('blockAnonViewers');
        $config->profileSystem = JRequest::getVar('profileSystem') == "" ? "default" : JRequest::getVar('profileSystem');
        $config->videoSystem = JRequest::getVar('videoSystem') == "" ? "default" : JRequest::getVar('videoSystem');
        $config->commentsSystem = JRequest::getVar('commentsSystem') == "" ? "default" : JRequest::getVar('commentsSystem');

        $repository = JVideo_ConfigRepositoryFactory::create();
        $repository->update($config);
    }

    function getVersion()
    {
        $version['com_jvideo'] = $this->getComponentVersion();
        $version['mod_jvideo'] = $this->getModuleVersion();
        $version['plg_jvideo_search'] = $this->getPlgSearchVersion();
        $version['plg_jvideo_content'] = $this->getPlgContentVersion();

        return $version;
    }

    private function getComponentVersion()
    {
        return $this->getVersionFromManifest('component', 'jvideo');
    }

    private function getModuleVersion()
    {
        return $this->getVersionFromManifest('module', 'mod_jvideo');
    }

    private function getPlgSearchVersion()
    {
        return $this->getVersionFromManifest('plugin', 'jvideo_search', 'search');
    }

    private function getPlgContentVersion()
    {
        return $this->getVersionFromManifest('plugin', 'jvideo_content', 'content');
    }

    private function getVersionFromManifest($type, $name, $folder = null)
    {
        $basePath = $this->getBasePathByType($type, $name, $folder);

        $xmlFile = $basePath.'/'.$name.".xml";

        if (file_exists($xmlFile))
            return $this->parseVersionFromXml($xmlFile);
        else
            return "Not Installed";
    }

    private function getBasePathByType($type, $name, $folder = null)
    {
        switch ($type)
        {
            case 'component':
                return JPATH_ADMINISTRATOR.'/components/com_'.$name;
            case 'module':
                return JPATH_ROOT.'/modules/'.$name;
            case 'plugin':
                return JPATH_ROOT.'/plugins/'.$folder;
            default:
                return JPATH_ROOT;
        }
    }

    private function parseVersionFromXml($xmlFile)
    {
        if ($data = JApplicationHelper::parseXMLInstallFile($xmlFile)) {
            foreach($data as $key => $value) {
                if ($key == "version")
                    return $value;
            }
        }

        return "Not Installed";
    }


}
