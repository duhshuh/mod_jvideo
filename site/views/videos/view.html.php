<?php
/*
 *	@package	JVideo
 *	@subpackage Components
 *	@link http://jvideo.warphd.com
 *	@copyright (C) 2007 - 2010 Warp
 *	@license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jvimport2('Web.AssetManager');

class JVideoViewVideos extends JViewLegacy
{
	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$params = clone($mainframe->getParams('com_jvideo'));

		$document = JFactory::getDocument();
		$this->header_title = $document->getTitle();
		 		
		$params->def( 'video_categories', -1);
		$params->def( 'video_order', 'newestvideos');
		$params->def( 'video_filter', 'published');
		$params->def( 'videos_per_page', 12);
		$params->def( 'video_layout', 'grid' );
		$params->def( 'video_layout_columns', 4);
		$params->def( 'show_thumbnail', 1);
		$params->def( 'show_video_title', 1);
		$params->def( 'show_video_description', 0);
		$params->def( 'show_video_rating', 1);
		$params->def( 'show_video_views', 1);
		$params->def( 'show_video_dateadded', 0);
		$params->def( 'show_video_duration', 1);
		$params->def( 'show_video_author', 1);
		
		$this->params = $params;
		$this->items = JRequest::getVar('items');
		$this->pagination = JRequest::getVar('pagination');
		$this->limitstart = JRequest::getVar('limitstart');
		$this->jvProfile = JRequest::getVar('jvProfile');
		$this->Itemid = JRequest::getVar('Itemid');

		$jvConfig = JVideo_Factory::getConfig();
		$this->cacheThumbnails = $jvConfig->cacheThumbnails;
		$this->thumbFaderEnabled = $jvConfig->thumbFaderEnabled;
		$this->showLinkback = $jvConfig->showLinkback;

		$this->addScripts();
		$this->addStylesheets();

		if ((int)$jvConfig->cacheThumbnails && (int)$jvConfig->proxyEnabled)
		{
			$cacheProxyParams = array('host' => $jvConfig->proxyHost
				, 'port' => $jvConfig->proxyPort 
				, 'username' => $jvConfig->proxyUsername
				, 'password' => $jvConfig->proxyPassword
				, 'timeout' => $jvConfig->proxyTimeout);
			$this->cacheProxyParams = $cacheProxyParams;
		}
		else
		{
			$cacheProxyParams = null;
			$this->cacheProxyParams = $cacheProxyParams;
		}
		
		$this->setBreadCrumb();
		
		parent::display($tpl);
	}

	private function addScripts()
	{
		$config = JVideo_Factory::getConfig();

		JVideo2_AssetManager::includeJQuery();
		JVideo2_AssetManager::includeJQueryUI();
		JVideo2_AssetManager::includeSiteCoreJs();

		if ($config->thumbFaderEnabled)
			JVideo2_AssetManager::includeCrossFadeJs();
	}

	private function addStylesheets()
	{
		JVideo2_AssetManager::includeSiteCoreCss();
	}

	private function setBreadCrumb()
	{
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

		$breadcrumbs = $mainframe->getPathWay();

		if (count($breadcrumbs->getPathway()) == 0)
		{
			$breadcrumbs->addItem($document->getTitle(), JRoute::_("index.php?option=com_jvideo&view=videos&Itemid=". JRequest::getVar("Itemid")));
		}
	}

	protected function truncateText($text, $limit)
	{
		if ($limit > 0 && strlen($text) > $limit)
		{
			return substr($text, 0, $limit - 1) . "...";
		}
		return $text;
	}
}