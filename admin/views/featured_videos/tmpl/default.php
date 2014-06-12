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

if ($this->accountIsNotSetup)
{
    ?>
    <h2 align="center">
        <img src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/images/warp-logo.png" alt="Warp Logo" /><br />
        Account Not Found
    </h2>
    <h3 align="center"><a href="<?php echo JRoute::_('index.php?option=com_jvideo&view=client_setup'); ?>">Click here to setup your account</a></h3>
    <?php
    return;
}
?>
<script language="javascript" src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/js/form_controls.js"></script>

<form action="index.php?option=com_jvideo" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="view" value="featured_videos" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="featured_id" />
	<input type="hidden" name="featured_order" />
	<input type="hidden" name="order_method" />

	<?php if (count($this->items) <= 0) : ?>
		
		<div class="row-fluid">
			<div class="span12" style="text-align: center;">
				You have no featured videos. Go to your <a href="<?php echo JRoute::_('index.php?option=com_jvideo&view=videos'); ?>">Video Manager</a> to select some.
			</div>
		</div>

	<?php else: ?>

		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="1%"><?php echo JText::_('Reorder'); ?></th>
					<th width="140"><?php echo JText::_('Thumbnail'); ?></th>
					<th><?php echo JText::_('Title'); ?></th>
					<th width="1%"><?php echo JText::_('Remove'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$itemcount = 0;

				foreach ($this->items as $item)
				{
					?>
					<tr>
						<td style="text-align: center">
							<?php if ($itemcount === 0) { ?>
								<span class="icon-arrow-up" style="color: #aaa;"></span>
							<?php } else { ?>
								<a class="icon-arrow-up" onclick="featured_reorder_item(<?php echo $item->id; ?>, <?php echo $item->feature_rank; ?>, 'down'); return false;"></a>
							<?php } ?>

							<?php if ($itemcount === count($this->items) - 1) { ?>
								<span class="icon-arrow-down" style="color: #aaa;"></span>
							<?php } else { ?>
								<a class="icon-arrow-down" onclick="featured_reorder_item(<?php echo $item->id; ?>, <?php echo $item->feature_rank; ?>,  'up'); return false;"></a>
							<?php } ?>
						</td>
						<td>
							<img src="<?php echo $item->imageURL; ?>" />
						</td>
						<td>
							<?php echo htmlspecialchars($item->video_title); ?>
						</td>
						<td style="text-align: center">
							<a class="icon-delete" style="color: #a00;" onclick="featured_remove_item(<?php echo $item->id; ?>); return false;"></a>
						</td>
					</tr>
					<?php
					
					$itemcount++;
				}
				?>
			</tbody>
		</table>

	<?php endif; ?>

	<table align="center">
	<tr><td>	
		<?php  echo $this->pagination->getListFooter(); ?>
	</td></tr>
	</table> 

</form>
