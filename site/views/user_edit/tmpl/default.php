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

$pageClassSuffix = $this->params->get('pageclass_sfx');
?>
<script	src="<?php echo JURI::root(); ?>/media/com_jvideo/site/js/form_controls.js"	language="javascript"></script>
<script>JVideo.userProfile.edit.init();</script>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post"	enctype="multipart/form-data" name="jvideoForm" id="jvideoForm">
    <input type="hidden" name="option" value="com_jvideo" />
	<input type="hidden" name="view" value="user_edit" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="user_id"	value="<?php echo $this->user_id; ?>" />
    <input type="hidden" name="changes" value="save" />

<div class="jvideo-wrapper<?php echo $pageClassSuffix; ?>">

	<div class="jvideo-profile-edit<?php echo $pageClassSuffix; ?>">
	
	<table class="jvideo-profile-table<?php echo $pageClassSuffix; ?>">
		<tr>
			<td colspan="2"><strong class="jvideo-title<?php echo $pageClassSuffix; ?>"><?php echo JText::_("JV_PROFILE_EDIT"); ?></strong></td>
		</tr>
		<?php
		if ($this->error != "")
		{
			?>
			<tr>
				<td colspan="2" class="jvideo-error<?php echo $pageClassSuffix; ?>"><?php echo $this->error; ?></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_NAME") ?></strong></td>
			<td><input type="text" name="jvideo_form_display_name"
				value="<?php echo $this->profile->getDisplayName(); ?>" maxlength="50" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_ABOUT_ME"); ?></strong></td>
			<td><textarea name="jvideo_form_description"><?php echo htmlspecialchars($this->profile->getDescription()); ?></textarea>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("Birthdate:"); ?></strong></td>
			<td>
				<select name="jvideo_form_birth_month" class="jvideo-input-birth-month<?php echo $pageClassSuffix; ?>">
					<option value="0"><?php echo JText::_("JV_PROFILE_DDL_MONTH"); ?></option>
					<?php
					$months = array(1 => 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');
							
					for ($i = 1; $i <= count($months); $i++)
					{
						echo "<option value=\"". $i . "\"";
						
						if ( (int) $this->birth_month == $i )
							echo "selected=\"selected\"";
						
						echo ">" . JText::_("JV_PROFILE_DDL_MONTH_" . $months[$i]) . "</option>";
					}
					?>
				</select> 
				<select name="jvideo_form_birth_day" class="jvideo-input-birth-day<?php echo $pageClassSuffix; ?>">
					<option value="0"><?php echo JText::_("JV_PROFILE_DDL_DAY"); ?></option>
					<?php
					for ($i = 1; $i <= 31; $i++)
					{
						echo "<option value=\"".$i."\"";
						
						if ((int)$this->birth_day == $i)
						{
							echo "selected=\"selected\"";
						}
						
						echo ">" . $i . "</option>";
					}
					?>
				</select>
				<input type="number" name="jvideo_form_birth_year" class="jvideo-input-birth-year<?php echo $pageClassSuffix; ?>"
					value="<?php echo $this->birth_year; ?>" size="4" min="1900" max="<?php echo date("Y"); ?>"
					placeholder="<?php echo JText::_("JV_PROFILE_BIRTHYEAR"); ?>" />
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_LOCATION"); ?></strong></td>
			<td><input type="text" name="jvideo_form_location"
				value="<?php echo $this->profile->getLocation(); ?>" maxlength="50" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_OCCUPATION"); ?></strong></td>
			<td><input type="text" name="jvideo_form_occupation"
				value="<?php echo $this->profile->getOccupation(); ?>" maxlength="100" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_INTERESTS"); ?></strong></td>
			<td><input type="text" name="jvideo_form_interests"
				value="<?php echo $this->profile->getInterests(); ?>" maxlength="250" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_("JV_PROFILE_WEBSITE"); ?></strong></td>
			<td><input type="url" name="jvideo_form_website"
				value="<?php echo $this->profile->getWebsiteURL(); ?>"
				maxlength="100" /> <br />
			<small><?php echo JText::_("JV_PROFILE_WEBSITE_EXAMPLE"); ?></small></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php if ($this->hasGD) { ?>
			<tr>
				<td colspan="2">
					<strong><?php echo JText::_("JV_PROFILE_PHOTO"); ?></strong><br />
					<small>
						<?php echo JText::_("JV_PROFILE_PHOTO_UPLOAD_TYPES"); ?><br>
						<?php echo JText::_("JV_PROFILE_PHOTO_RESIZE"); ?><br>
						<?php echo JText::_("JV_PROFILE_PHOTO_MAX_SIZE"); ?>
					</small>
				</td>
			</tr>
			<?php 
			if ($this->profile->getAvatarFilename() != '')
			{
				$avatarUrl = JURI::base() . "/media/com_jvideo/site/images/avatars/" . $this->profile->getAvatarFilename();
				?>
				<tr class="jvideo-profile-image-container<?php echo $pageClassSuffix; ?>">
					<td colspan="2">
						<div><img class="jvideo-profile-image<?php echo $pageClassSuffix; ?>" src="<?php echo $avatarUrl; ?>" height="100" width="100"></div>
						<div>
							<input class="btn jvideo-remove-profile-image-button<?php echo $pageClassSuffix; ?>" type="button"
								value="<?php echo JText::_('JV_PROFILE_PHOTO_REMOVE'); ?>"
								data-confirm-message="<?php echo htmlspecialchars(JText::_("JV_PROFILE_PHOTO_REMOVE_CONFIRM")); ?>">
						</div>
					</td>
				</tr>
				<input type="hidden" name="jvideo_remove_image" value="">
				<?php
			}
			?>
			<tr>
				<td colspan="2"><input type="file" name="jvideo_form_avatar" /></td>
			</tr>
		<?php } else { ?>
			<tr>
				<td colspan="2">
					<strong><?php echo JText::_("JV_PROFILE_PHOTO_DISABLED"); ?></strong>
					<p style="width: 300px;"><?php echo JText::_("JV_PROFILE_PHOTO_REQUIRES_GD"); ?></p>
				</td>
			</tr>
		<?php 
		} ?>
		<tr>
			<td colspan="2" class="jvideo-button-container<?php echo $pageClassSuffix; ?>"><input type="submit" value="<?php echo JText::_("JV_PROFILE_SAVE"); ?>" class="btn" /></td>
		</tr>
	</table>
	</div>
</div>
</form>
<div class="clearfix"></div>
