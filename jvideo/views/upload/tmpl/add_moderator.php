<?php
defined('_JEXEC') or die('Restricted access');
?>
<div class="span6">
	<div id="publishingContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_PUBLISH_CONTAINER"); ?>
		</div>
		<div class="content">
			<div id="publishedContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_PUBLISHED") ?>
				</div>
				<div>
					<?php echo $this->lists['published']; ?>
				</div>
			</div>
			<div id="publishUpContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_PUBLISH_UP") ?>
				</div>
				<div>
					<a onClick="javascript:document.getElementById('publishUp').value = '';"><?php echo JText::_("JV_PUBLISH_RESET"); ?></a>
					<?php echo JHTML::calendar($this->publishUp == "0000-00-00 00:00:00" ? "" : $this->publishUp, "publishUp", "publishUp"); ?>
				</div>
			</div>
			<div id="publishDownContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_PUBLISH_DOWN") ?>
				</div>
				<div>
					<a onClick="javascript:document.getElementById('publishDown').value = '';"><?php echo JText::_("JV_PUBLISH_RESET"); ?></a>
					<?php echo JHTML::calendar($this->publishDown == "0000-00-00 00:00:00" ? "" : $this->publishDown, "publishDown", "publishDown"); ?>
				</div>
			</div>
			<div id="authorContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_AUTHOR") ?>
				</div>
				<div>
					<input type="text" name="authorId" id="authorId" maxlength="255" value="<?php echo $this->authorId ?>" />
				</div>
			</div>
			<div id="dateAddedContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_DATE_ADDED") ?>
				</div>
				<div>
					<?php echo JHTML::calendar($this->dateAdded, "dateAdded", "dateAdded"); ?>
				</div>
			</div>
			<div id="featureContainer" class="field">
				<div class="subtitle">
					<?php echo JText::_("JV_UPLOAD_ADD_FEATURED") ?>
				</div>
				<div>
					<?php echo $this->lists['featured']; ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="clear">&nbsp;</div>
</div>
<div class="span6">
	<div id="descriptionContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_EDIT_DESC"); ?>
		</div>
		<div class="content">
			<textarea name="description" id="description" cols="50" rows="4"></textarea>
		</div>
	</div>
	<div id="categoriesContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_ALL_CATEGORIES"); ?>
		</div>
		<div class="content">
			<?php echo JHTML::_('jvideo.videoCategory.videoCategoryList', $this->categories); ?>
		</div>
	</div>
	<div id="tagsContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_EDIT_TAGS") ?>
		</div>
		<div class="content">
			<input type="text" name="tags" id="tags" maxlength="255" value="" />
			<div class="example">
				<?php echo JText::_("JV_EDIT_TAGS_EXAMPLE"); ?>
			</div>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
</div>

<div class="jvideo-submit-container span12">
    <input type="submit" value="<?php echo $this->submitMessage; ?>" class="btn" />
</div>
