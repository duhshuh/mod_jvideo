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

jvimport('VideoRepositoryFactory');

class JVideoModelFeatured_Videos  extends JModelLegacy
{
	var $_repository = null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();

		$this->_repository = JVideo_VideoRepositoryFactory::create();

		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function do_remove_featured_video()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getInt('featured_id');

		$db->setQuery('select v_id from #__jvideo_featured where id = ' . $db->escape((int)$id));
		$videoId = $db->loadResult();

		if ($videoId)
		{
			$this->_repository->unfeature(
				$this->_repository->getVideoById($videoId)
			);
		}
	}

	function do_reorder_featured_video()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getInt('featured_id');
		$order_val = JRequest::getVar('featured_order');
		$order_method = JRequest::getvar('order_method');

		$new_order = 0;
		$go = 0;

		if ($order_method == "up") {
			$new_order = $order_val - 1;
		} else {
			$new_order = $order_val + 1;
		}

		/* Determine the maximum number in the database */
		$sql_max = "select count(*) from #__jvideo_featured";
		$db->setQuery($sql_max);
		$max_count = $db->loadResult();

		/* sanity check */
		if ($new_order > 0 && $new_order <= $max_count) {
			$go = 1;
		}

		if ($go == 1) {
			$sql_last = "select id from #__jvideo_featured where feature_rank = " . $db->escape((int)$new_order);
			$db->setQuery($sql_last);
			$old_id = (int) $db->loadResult();
				
			$sql_update_last = "update #__jvideo_featured set feature_rank = " . $db->escape((int)$order_val) . " where id=" . $db->escape((int)$old_id);
			$db->setQuery($sql_update_last);
			$db->execute();
				
			$sql_update_current = "update #__jvideo_featured set feature_rank=" . $db->escape((int)$new_order) . " where id=" . $db->escape((int)$id);
			$db->setQuery($sql_update_current);
			$db->execute();
		}

		return $go;
	}
	function get_featured_videos()
	{
		// if data hasn't already been obtained, load it
		if (empty($this->_data)) {
			$query = $this->get_featured_video_sql();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	function get_featured_video_total()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->get_featured_video_sql();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}


	function get_featured_video_pagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->get_featured_video_total(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function get_featured_video_sql()
	{
		$db = JFactory::getDBO();
		$sql = "select v.video_title, f.id, f.feature_rank, t.imageURL "
			."from #__jvideo_featured f, #__jvideo_videos v "
			."left join #__jvideo_thumbnails t on t.videoID = v.id "
			."where f.v_id = v.id "
			."group by t.videoID "
			."order by f.feature_rank DESC";
		return $sql;
	}

	function get_video_display_settings()
	{
		$db = JFactory::getDBO();
		$sql = "select id, show_embeded, auto_play, has_ratings, min_gid from #__jvideo_config";
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();

		return $rows;
	}

	function do_video_display_settings()
	{
		$db = JFactory::getDBO();

		/* determine if this is the first save */
		if (JRequest::getVar('id') == '' || JRequest::getVar('id') < 0) {
			$sql = "insert into #__jvideo_config (min_gid, show_embeded, auto_play, has_ratings) " .
					"values(".
						$db->quote(JRequest::getVar('gid')) . ", " .
						$db->quote(JRequest::getVar('show_embed_code')) . ", " .
						$db->quote(JRequest::getVar('auto_play_onload')) . ", " .
						$db->quote(JRequest::getVar('allow_ratings')) . ")";
		} else {
			$sql = "update #__jvideo_config set " .
					"min_gid = " . $db->quote(JRequest::getVar('gid')) . ", " .
					"show_embeded = " . $db->quote(JRequest::getVar('show_embed_code')) . ", " .
					"auto_play = " . $db->quote(JRequest::getVar('auto_play_onload')) . ", " .
					"has_ratings = " . $db->quote(JRequest::getVar('allow_ratings'));
		}

		$db->setQuery($sql);
		$db->execute();
	}

	function get_video_categories()
	{
		$db = JFactory::getDBO();
		$sql = "select id, category_name from #__jvideo_categories order by category_name";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		return $rows;
	}

	function add_category($category_name)
	{
		$db = JFactory::getDBO();

		/* Check for an existing record */
		$sql = "select id from #__jvideo_categories where category_name = ".$db->quote($category_name);
		$db->setQuery($sql);
		$id = $db->loadResult();
		$valid = 0;

		if ($id < 0 || $id == '') {
			/* We're good, add it */
			$sql = "insert into #__jvideo_categories (category_name) values(".$db->quote($category_name).")";
			$db->setQuery($sql);
			$db->execute();
			$valid = 1;
		}

		return $valid;
	}
	
	function edit_category($category_name, $category_id)
	{
		$db = JFactory::getDBO();

		/* Check for an existing record */
		$sql = "select id from #__jvideo_categories where category_name = ".$db->quote($category_name)." and id != ".(int)$category_id;
		$db->setQuery($sql);
		$id = $db->loadResult();
		$valid = 0;

		if ($id < 0 || $id == '') {
			/* We're good, edit it */
			$sql = "update #__jvideo_categories set category_name = ".$db->quote($category_name)." where id = ".(int)$category_id;
			$db->setQuery($sql);
			$db->execute();
			$valid = 1;
		}

		return $valid;
	}

	function delete_category($category_id)
	{
		$db = JFactory::getDBO();

		/* Check for attached videos */
		$sql = "select count(id) from #__jvideo_videos_categories where vc_id=".(int)$category_id;
		$db->setQuery($sql);
		$count = $db->loadResult();
		$valid = 0;

		if ($count == 0 || $count == '') {
			/* We're good, delete it */
			$sql = "delete from #__jvideo_categories where id = ".(int)$category_id;
			$db->setQuery($sql);
			$db->execute();
			$valid = 1;
		}

		return $valid;
	}
}
?>
