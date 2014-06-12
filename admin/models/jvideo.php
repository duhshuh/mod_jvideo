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

jvimport('Services.SyncServiceManager');
jvimport('Services.JoomlaCategorySyncService');
jvimport('Services.JoomlaVideoSyncService');
jvimport('Services.JoomlaVideoCategorySyncService');

class JVideoModelJVideo  extends JModelLegacy
{
	function generateStatistics()
	{
		$statistics = new stdClass();
		$statistics->totalVideos = $this->getTotalVideos();
		$statistics->totalVideoHits = $this->getTotalVideoHits();
		$statistics->totalVotes = $this->getTotalVotes();

		return $statistics;
	}
	
	private function getTotalVideos()
	{
		return $this->getResultFromQuery("SELECT COUNT(*) FROM #__jvideo_videos WHERE status IN ('complete', 'pending')");
	}
	
	
	private function getTotalVideoHits()
	{
		return $this->getResultFromQuery("SELECT SUM(hits) FROM #__jvideo_videos WHERE status IN ('complete', 'pending')");		
	}
	
	private function getTotalVotes()
	{
		return $this->getResultFromQuery("SELECT COUNT(*) FROM #__jvideo_rating r INNER JOIN #__jvideo_videos v ON r.v_id = v.id  WHERE v.status IN ('complete', 'pending')");	
	}
	
	private function getResultFromQuery($query)
	{
		$db = JFactory::getDBO();
		
		$db->setQuery($query);
		
		return $db->loadResult();
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
}

?>