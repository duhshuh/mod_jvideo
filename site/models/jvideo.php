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

jimport( 'joomla.application.component.model' );

jvimport('Services.SyncServiceManager');
jvimport('Services.JoomlaCategorySyncService');
jvimport('Services.JoomlaVideoSyncService');
jvimport('Services.JoomlaVideoCategorySyncService');
jvimport('VideoRepositoryFactory');

class JVideoModelJVideo extends JModelLegacy
{
	private $_repository = null;

	public function __construct()
	{
		parent::__construct();

		$this->_repository = JVideo_VideoRepositoryFactory::create();
	}

	public function getConfig()
	{
		return JVideo_Factory::getConfig();
	}

	public function initializePlugins()
	{
		$config = JVideo_Factory::getConfig();

		if ($config->profileSystem == "custom_mapping")
		{
			if (($config->mapProfileURL != "")
					&& ($config->mapProfileTable != "")
					&& ($config->mapProfileID != "")
					&& ($config->mapProfileUserID != "")
					&& ($config->mapProfileAvatar != ""))
			{
				include_once(JPATH_COMPONENT.'/plugins/'.$config->profileSystem.'/profile.php');
			}
			else
			{
				include_once(JPATH_COMPONENT.'/plugins/default/profile.php');
			}
		}
		else
		{
			include_once(JPATH_COMPONENT.'/plugins/'.$config->profileSystem.'/profile.php');
		}

		include_once(JPATH_COMPONENT.'/plugins/'.$config->commentsSystem.'/comments.php');
	}

	public function removeFeatured($videoId)
	{
		$inputFilter = JFilterInput::getInstance();
		$videoId = $inputFilter->clean($videoId, 'INT');

		$this->_repository->unfeature(
			$this->_repository->getVideoById($videoId)
		);
	}

	public function addFeatured($videoId)
	{
		$inputFilter = JFilterInput::getInstance();
		$videoId = $inputFilter->clean($videoId, 'INT');

		$this->_repository->feature(
			$this->_repository->getVideoById($videoId)
		);
	}

	public function publishVideo($id)
	{
		$db = JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$id = $inputFilter->clean($id, 'INT');

		$sql = "UPDATE #__jvideo_videos SET published = 1 WHERE id = ".$id." LIMIT 1;";
		$db->setQuery($sql);
		$db->execute();
	}

	public function unpublishVideo($id)
	{
		$db = JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$id = $inputFilter->clean($id, 'INT');

		$sql = "UPDATE #__jvideo_videos SET published = 0 WHERE id = ".$id." LIMIT 1;";
		$db->setQuery($sql);
		$db->execute();
	}

	//@todo: Refactor
	public function rate_video()
	{
		$db	= JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$id = $inputFilter->clean(JRequest::getVar('id'), 'INT');
		$user_id = $inputFilter->clean(JRequest::getVar('user_id'), 'INT');
		$rating = $inputFilter->clean(JRequest::getVar('rating'), 'INT');

		if ($id && $user_id && $rating)
		{
			$sql = "SELECT COUNT(*) FROM #__jvideo_rating v "
					."WHERE v.`user_id` = " . JRequest::getInt('user_id')
					." AND v.`v_id` = " . JRequest::getInt('id');
			$db->setQuery($sql);
			$db->execute();

			$count = $db->loadResult();

			if (!$count)
			{
				$sql = "INSERT INTO #__jvideo_rating (`v_id`,`user_id`,`rating`) "
						."VALUES ("
						. JRequest::getInt('id') . ","
						. JRequest::getInt('user_id') . ","
						. JRequest::getInt('rating') . ")";

				$db->setQuery( $sql );
				$db->execute();

				return true;
			}
			else
			{
				$sql = "UPDATE #__jvideo_rating "
						."SET `rating` = " . JRequest::getVar('rating') . " "
						."WHERE `v_id` = " . JRequest::getVar('id') . " "
						."AND `user_id` = " . JRequest::getVar('user_id') . " "
						."LIMIT 1";

				$db->setQuery( $sql );
				$db->execute();

				return true;
			}
		}
		else
		{
			return false;
		}
	}

	public function get_video($id)
	{
		$db	= JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$jvVideo = new JVideo_Video();
		$id = $inputFilter->clean($id, 'INT');

		$sql = $jvVideo->sqlSelectVideo($id);
		$db->setQuery($sql);

		return $db->loadObject();
	}

	public function getVideoCountByUserId($userId)
	{
		$db	= JFactory::getDBO();

		$sql = "SELECT COUNT(*) FROM #__jvideo_videos WHERE `user_id` = " . (int) $userId
			  ." AND status IN ('complete','pending')";
		$db->setQuery($sql);

		return (int) $db->loadResult();
	}

	public function getThumbnailsFromVideoId($videoId)
	{
		$db	= JFactory::getDBO();

		$sql = "SELECT id, videoID, imageURL "
			  ." FROM #__jvideo_thumbnails "
			  ." WHERE videoID = " . ((int) $videoId) . " "
			  ." AND width = '120' AND height = '90' "
			  ." ORDER BY timeIndex ASC, id ASC";
		$db->setQuery($sql);

		return $db->loadObjectList();
	}

	public function synchronize($force = false)
	{
		try
		{
			$serviceManager = new JVideo_SyncServiceManager($force);
			$serviceManager->add(new JVideo_JoomlaCategorySyncService());
			$serviceManager->add(new JVideo_JoomlaVideoSyncService());
			$serviceManager->add(new JVideo_JoomlaVideoCategorySyncService());

			return $serviceManager->sync();
		}
		catch (Exception $ex)
		{
			JError::raiseNotice("500", "A problem occured during synchronization: $ex");
		}
	}

	//@todo: Refactor
	public function do_add_video()
	{
		$db	= JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$user = JFactory::getUser();
		$title = addslashes(JRequest::getVar('title'));
		$desc = addslashes(JRequest::getVar('desc'));
		$category_id = $inputFilter->clean(JRequest::getVar('category_id'), 'INT');
		$tags = addslashes(JRequest::getVar('tags'));
		$user_id = $user->id;
		$videoGuid = JRequest::getVar('videoGuid');
		$debug = false;

		if (!$videoGuid) return false;
		if ($title == "" || $category_id == "" || $user_id == "") return false;

		$sql = "UPDATE #__jvideo_videos "
				."SET video_title = '".$title."', "
				."video_desc = '".$desc."', tags = '".$tags."', "
				."date_added=CURDATE() "
				."WHERE infin_vid_id = '".$videoGuid."' "
				."LIMIT 1";

		$db->setQuery( $sql );

		if ($db->execute())
			return true;
		else
			return false;
	}

	//@todo: Refactor
	public function do_edit_video()
	{
		$db	= JFactory::getDBO();
		$inputFilter = JFilterInput::getInstance();
		$user = JFactory::getUser();
		$title = addslashes(JRequest::getVar('title'));
		$desc = addslashes(JRequest::getVar('desc'));
		$category_id = $inputFilter->clean(JRequest::getVar('category_id'), 'INT');
		$id = $inputFilter->clean(JRequest::getVar('id'), 'INT');
		$user_id = $user->id;

		$tags = addslashes(JRequest::getVar('tags'));
		$allowed = "/[^A-Za-z0-9\\040]/i"; //alphanumeric + space
		$tags = strtolower(preg_replace($allowed,"",$tags));


		if ($file_exists == false)
		{
			$sql = "UPDATE #__jvideo_videos SET ".
					"`video_title` = '".$title."',".
					"`video_desc` = '".$desc."',".
					"`tags` = '".$tags."' ".
					"WHERE `id` = " . $id . " LIMIT 1";

			$db->setQuery( $sql );
			$db->execute();

			return true;
		}
		else
		{
			return false;
		}
	}

	//@todo: refactor
	public function get_video_categories()
	{
		$model = $this->getModel( 'categories' );

		return $model->get_jvideo_categories();
	}	
}