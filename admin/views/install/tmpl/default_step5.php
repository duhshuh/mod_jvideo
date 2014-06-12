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
jvideo.install.initCountdownTimer(10);
</script>
<form action="index.php" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_jvideo" />
    <input type="hidden" name="view" value="jvideo" />
    
    <h1><?php echo JText::_("JV_INSTALL_STEP_5"); ?></h1>

    <h2><?php echo JText::_("JV_INSTALL_STEP_5_DESC"); ?></h2>
    
    <div class="stepTasksContainer">
      <?php foreach ($this->stepTasks as $description => $result) : ?>
        <?php
        if ($description == "continue") {
            $continue = $result;
            break;
        }
        ?>
        <div class="stepTask">
            <div class="description"><?php echo $description ?></div>
            <div class="divider">...................</div>
            <?php if ($result == "OK") : ?>
                <div class="result good"><?php echo $result; ?></div>
            <?php else : ?>
                <div class="result bad"><?php echo $result; ?></div>
            <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="stepContinue">
        </script>
        <input type="submit" id="adminFormSubmitBtn" value="<?php echo JText::_("JV_FINISH"); ?>" onclick="javascript: jvideo.install.cancelCountdownTimer();" />
        <br /><br />
        <div id="autoNext">
            <small><?php echo JText::_("JV_CONTINUING_IN"); ?> <span id="timer" class="timer">10</span> <?php echo JText::_("JV_SECONDS"); ?>
                (<a href="#" onclick="javascript: jvideo.install.cancelCountdownTimer(); document.getElementById('autoNext').style.display = 'none';"><?php echo JText::_("JV_PAUSE"); ?></a>)</small>
        </div>
    </div>
</form>