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
<table width="100%" border="0">
	<tr>
		<td width="45%" valign="top">
			<?php
				echo JHtml::_('bootstrap.startTabSet', 'stat-pane', array('active' => 'welcome'));
				echo JHtml::_('bootstrap.addTab', 'stat-pane', 'welcome', JText::_('Welcome to JVideo'));
			?>
			<table class="adminlist">
				<tr>
					<td>
						<div style="font-weight:700;">
							<?php echo JText::_('Official Joomla! Plugin for the Warp Video Network');?>
						</div>
						<p>
							First time using JVideo? Don't forget to <a href="index.php?option=com_jvideo&view=client_setup">setup your account</a>
						</p>
						<p>
							If you are having any problems, please check out our <a href="index.php?option=com_jvideo&view=help">help section</a>
						</p>						
						<p>
							If you found any bugs, just drop us an email at support@warphd.com
						</p>
					</td>
				</tr>
			</table>
			<?php
				echo JHtml::_('bootstrap.endTab');
				echo JHtml::_('bootstrap.addTab', 'stat-pane', 'gettingStarted', JText::_('Getting Started'));

				$uri = JFactory::getUri();
				$playerSwfUrl = ($uri->isSSL() ? 'https://secure' : 'http://manage') . '.warphd.com/assets/player.swf?a=FIamKY772hR1pF0zYDuKsmQMD&v=1';
			?>
				<table class="adminlist">
					<tr>
						<td>
						<object width="540" height="304"><param name="allowFullScreen" value="true" />
						<param name="src" value="<?php echo $playerSwfUrl; ?>"/>
						<param name="allowScriptAccess" value="always" />
						<param name="flashvars" value="AccountKey=FIamKY772hR1pF0zYDuKsmQMD&VideoGuid=9e117444-0c69-102c-8f0b-12313800bcf1&Signature=jQTxbjG57fUlY%2FP19SnX0tgmp9Q%3D&AutoPlay=0" />
						<param name="wmode" value="transparent"></param>
						<embed src="<?php echo $playerSwfUrl; ?>" type="application/x-shockwave-flash" 
							wmode="transparent" width="540" height="304" allowFullScreen="true" 
							flashVars="AccountKey=FIamKY772hR1pF0zYDuKsmQMD&VideoGuid=9e117444-0c69-102c-8f0b-12313800bcf1&Signature=jQTxbjG57fUlY%2FP19SnX0tgmp9Q%3D&AutoPlay=0">
						</embed>
						</object>
						<br /><br />
						If you prefer, you can read our <a href="http://jvideo.warphd.com/support/user-guide" target="_blank">User Guide</a> instead.
						</td>
					</tr>
				</table>
			<?php
				echo JHtml::_('bootstrap.endTab');
				echo JHtml::_('bootstrap.addTab', 'stat-pane', 'statistics', JText::_('Video Statistics'));
			?>
				<table class="adminlist">
					<tr>
						<td>
							<?php echo JText::_( 'Total Active Videos' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->statistics->totalVideos; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'Total Videos Watched' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->statistics->totalVideoHits; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'Total User Votes' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->statistics->totalVotes; ?></strong>
						</td>
					</tr>
				</table>
			<?php
				echo JHtml::_('bootstrap.endTab');

				echo JHtml::_('bootstrap.endTabSet');
			?>
		</td>
	</tr>
</table>
