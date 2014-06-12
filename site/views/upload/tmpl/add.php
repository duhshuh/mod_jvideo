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

$pageclass_sfx = "";

if (!is_null($this->params))
{
	if (method_exists($this->params, 'get'))
	{
		$pageclass_sfx = $this->params->get( 'pageclass_sfx' );
	}	
}
?>
<script	src="<?php echo JURI::root(); ?>/media/com_jvideo/site/js/form_controls.js"	type="text/javascript" language="javascript"></script>
<div class="jvideo-wrapper<?php echo $pageclass_sfx; ?>">
	<h1><?php echo $this->greeting; ?></h1>
	<h2 class="validation"><?php echo $this->validationMessage; ?></h2>
	<form action="<?php echo JRoute::_('index.php');?>" method="post" name="jvideoForm" id="jvideoForm">
		<input type="hidden" name="view" id="view" value="upload" />
		<input type="hidden" name="task" id="task" value="save" /> 
		<input type="hidden" name="videoGuid" id="videoGuid" value="<?php echo $this->videoGuid; ?>" />
		<div id="addVideoWrapper">
			<div id="previewContainer">
				<div class="content">
					<div id="jvideoPreviewPlaceholder"></div>
					<?php echo JText::_("JV_VIDEO_UPLOADED_NOW_PROCESSING"); ?>
				</div>
			</div>
			<div id="titleContainer">
				<input type="text" name="title" id="title" maxlength="50" value="Untitled" />
			</div>
			
			<div id="leftRightContainer" class="row-fluid">
				<?php
				echo $this->loadTemplate($this->isModerator ? 'moderator' : 'default');
				?>
				<div class="clear">&nbsp;</div>
			</div>
			<div class="clear">&nbsp;</div>
		</div>
	</form>
</div>
