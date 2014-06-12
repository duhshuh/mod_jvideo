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
<form action="index.php" method="post" id="adminForm" name="adminForm">

	<?php
	if ( ! $this->verifyAccountExists)
	{
		?>
		<h2 align="center">
            <img src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/images/warp-logo.png" alt="Warp Logo" /><br />
            Account Not Found
        </h2>
		<h3 align="center"><a href="index.php?option=com_jvideo&view=client_setup">Click here to setup your account</a></h3>
		<?php
		return;
	}
	?>

	<div id="editcell">
		<table class="adminlist">
			<thead>
				<?php
				$saveResult = JRequest::getVar("saveResult", "");

				if ($saveResult != "") {
					?>
					<tr>
						<th colspan="3" align="center" class="jvideo_saveResult">
							<?php
							if ( (bool) $saveResult ) {
								echo "Save Successful";
							} else {
								echo "Error occurred during save!";
							}
							?>
						</th>
					</tr>
					<?php
				}
				?>
				<tr>
					<th width="1%"><?php echo JText::_( '#' ); ?></th>
					<th width="2%">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->categoriesCount; ?>);" />
					</th>
					<th><?php echo JText::_( 'Category' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php //echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<?php

			$k = $i = 0;
			$indentPrefix = '.';
			$indentSeparator = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$indentSuffix = '<sup>|_</sup>&nbsp;&nbsp;';
			
            $categoryTreeIterator = new JVideo_CategoryTreeIterator($this->categories);
            
            foreach ($categoryTreeIterator as $categoryTreeItem)
			{
				$checked = JHTML::_( 'grid.id', $i, $categoryTreeItem->id );
				?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center" width="5">
						<?php echo $i + 1; ?>
				</td>
				<td align="center" width="20">
						<?php echo $checked; ?>
				</td>
				<td>
						<?php
						echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=categories&task=edit&cid[]=".$categoryTreeItem->id)."\">";

						if ( (int) $categoryTreeItem->level > 1 ) {
							echo $indentPrefix;
							for ($indentIterator = 1; $indentIterator < $categoryTreeItem->level; $indentIterator++) {
								echo $indentSeparator;
							}
							echo $indentSuffix;
						}

						echo $categoryTreeItem->name;
						echo "</a>\n";
						?>
				</td>
			</tr>
				<?php
                $i++;
				$k = 1 - $k;
			}
			?>
		</table>
	</div>

	<input type="hidden" name="option" value="com_jvideo" />
	<input type="hidden" name="view" value="categories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
