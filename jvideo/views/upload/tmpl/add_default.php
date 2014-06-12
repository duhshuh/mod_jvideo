<div class="span6">
	<div id="descriptionContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_EDIT_DESC"); ?>
		</div>
		<div class="content">
			<textarea name="description" id="description" cols="50" rows="4"></textarea>
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
<div class="span6">
	<div id="categoriesContainer" class="jvideo-edit-section">
		<div class="title">
			<?php echo JText::_("JV_ALL_CATEGORIES"); ?>
		</div>
		<div class="content">
			<?php echo JHTML::_('jvideo.videoCategory.videoCategoryList', $this->categories); ?>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
</div>

<div class="jvideo-submit-container span12">
    <input type="submit" value="<?php echo $this->submitMessage; ?>" class="btn" />
</div>
