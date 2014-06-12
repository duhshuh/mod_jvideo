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

function JVideoBuildRoute( &$query )
{
	$segments = array();
	$jvConfig = JRequest::getVar("jvConfig");
	$seoEnabled = false;
	$seoFileExtension = "";
	$isWatchView = false;
	$videoID = 0;
	
	if (is_null($jvConfig))
	{
		$jvConfig = JRequest::getVar("mod_jvConfig");
	}
	
	if ( !is_null($jvConfig) && array_key_exists('seoEnabled', get_object_vars($jvConfig)))
	{
		$seoEnabled = $jvConfig->seoEnabled;
		$seoFileExtension = $jvConfig->seoFileExtension;
	}
	else
	{
		$db = JFactory::getDBO();
		$sql = "SELECT seoEnabled, seoFileExtension FROM #__jvideo_config LIMIT 1";
		$db->setQuery($sql);
		$seo = $db->loadObject();
		$seoEnabled = (bool) $seo->seoEnabled;
		$seoFileExtension = $seo->seoFileExtension;
	}
	
	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		
		if ($query['view'] == "watch")
		{
			$isWatchView = true;
		}
		
		unset( $query['view'] );
	}
	
	if (isset($query['task']))
	{
		$segments[] = $query['task'];
		unset( $query['task'] );		
	}

	if (isset($query['user_id']))
	{
		$segments[] = $query['user_id'];
		unset( $query['user_id'] );
	}
	
	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		$videoID = (int) $query['id'];
		unset( $query['id'] );
	}
	
	if (isset($query['videoGuid']))
	{
		$segments[] = $query['videoGuid'];
		unset( $query['videoGuid']);
	}
	
	if (isset($query['uid']))
	{
		$segments[] = $query['uid'];
		unset( $query['uid']);
	}
	
	if ($seoEnabled && $isWatchView && $videoID)
	{
		$cache = JFactory::getCache("com_jvideo");
		$videoTitle = $cache->call('getVideoTitleById', $videoID);
		
		if (trim($videoTitle) != "")
		{
			$videoTitle = strtolower($videoTitle);
			$videoTitle = preg_replace('/[^a-z0-9 ]/', '', $videoTitle);
			$videoTitle = str_replace(' ', '-', $videoTitle);
			
			if ($seoFileExtension != "")
				$segments[] = $videoTitle . "." . $seoFileExtension;
			else
				$segments[] = $videoTitle;
		}
		else
		{
			$videoTitle = JText::_("JV_UNTITLED");
			
			if ($seoFileExtension != "")
				$segments[] = $videoTitle . "." . $seoFileExtension;
			else
				$segments[] = $videoTitle;
		}
	}
	
	return $segments;
}

function JVideoParseRoute( $segments )
{
	$vars = array();
	switch($segments[0])
	{
		case "upload":
			$vars['view'] = "upload";
			if (count($segments) > 1)
			{
				if ($segments[1] == "add")
				{
					$vars['task'] = "add";
					$vars['videoGuid'] = str_replace(':','-', $segments[2]);
					
					if (count($segments) > 3)
					{
						$vars['uid'] = $segments[3];
					}				
				}
			}
			break;
		case "watch":
			$vars['view'] = "watch";
			if (count($segments) > 1)
			{
				$id = explode( ':', $segments[1] );
				$vars['id'] = (int) $id[0];
			}
			break;
		case "videos":
			$vars['view'] = "videos";
			if (count($segments) > 1)
			{
				$id = explode( ':', $segments[1] );
				$vars['id'] = (int) $id[0];
			}
			break;
		case "user":
			$vars['view'] = 'user';
			$user_id = explode( ':', $segments[1] );
			$vars['user_id'] = (int) $user_id[0];
			break;
		case 'user_edit':
			$vars['view'] = 'user_edit';
			$user_id = explode( ':', $segments[1] );
			$vars['user_id'] = (int) $user_id[0];
			break;
		case 'user_allvideos':
			$vars['task'] = 'user_allvideos';
			$user_id = explode( ':', $segments[1] );
			$vars['user_id'] = (int) $user_id[0];
			break;
		case 'delete_video':
			$vars['task'] = "delete_video";
			$id = explode( ':', $segments[1] );
			$vars['id'] = (int) $id[0];
			break;
		case "edit":
			if (count($segments) > 1)
			{
				$vars['view'] = $segments[0];
				$vars['id'] = (int) $segments[1];
			}
			else
			{
				$vars['view'] = "edit";
				$vars['id'] = (int) $segments[0];
			}
			break;
		default: //default view
			$vars['view'] = $segments[0];
			$id = explode( ':', $segments[1] );
			$vars['id'] = (int) $id[0];
			break;
	}
	return $vars;
}

function getVideoTitleById($videoID)
{
	$db = JFactory::getDBO();
	$sql = "SELECT video_title FROM #__jvideo_videos "
			."WHERE id = " . (int) $videoID . " "
			."LIMIT 1";
	$db->setQuery($sql);
	return $db->loadResult();
}
