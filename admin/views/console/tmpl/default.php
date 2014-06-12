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
<form action="index.php?option=com_jvideo" method="post" id="adminForm" name="adminForm">
    <h2>
        <img src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/images/warp-logo.png" alt="Warp Console" /><br />
    </h2>
    <div>
        <h3 style="padding-left: 18px;">Use the Warp Console to:</h3>
        <ul>
            <li><strong>Brand</strong> your videos with a corporate or personal watermark</li>
            <li>View <strong>analytics</strong> to track how many visitors are watching your videos</li>
            <li>Access publishing features to push your content across the web</li>
            <li>Update account information and upgrade (or downgrade) your account instantly</li>
            <li>Make changes to videos and automatically synchronize with your website</li>
        </ul>
        <h3 style="padding-left: 27px;">
            <strong><a href="index.php?option=com_jvideo&view=console&task=autoLogin" target="_blank">Login to the Console</a></strong>
        </h3>
    </div>
</form>
