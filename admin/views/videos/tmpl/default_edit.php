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
defined('_JEXEC') or die('Restricted access'); ?>
<script	src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/js/form_controls.js" language="javascript"></script>
<form action="<?php echo JRoute::_( 'index.php?option=com_jvideo&view=videos');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" id="task" value="save" /> 
	<input type="hidden" name="videoId" id="videoId" value="<?php echo $this->videoId; ?>" />
	<input type="hidden" name="videoGuid" id="videoGuid" value="<?php echo $this->videoGuid; ?>" />
	<input type="hidden" name="goBackWhere" value="<?php echo getenv("HTTP_REFERER"); ?>" />
    <input type="hidden" name="cid" value="<?php echo $this->videoId; ?>" />
    <div id="editVideoWrapper">
        <?php if (isset($this->validation_msg) && $this->validation_msg != ""): ?>
        <h2 class="validation"><?php echo $this->validation_msg; ?></h2>
        <?php endif; ?>
        <div id="titleContainer">
            <input type="text" name="title" id="title" maxlength="50" value="<?php echo $this->title == "" ? "Untitled" : $this->title; ?>" />
        </div>
        <div id="previewContainer">
            <div class="content">
                <div id="jvideoPreviewPlaceholder"></div>
                <?php
                    if ($this->videoStatus == 'complete') {
                        echo $this->embedCode;
                    } else {
                        echo JText::_("JV_VIDEO_UPLOADED_NOW_PROCESSING");
                    }
                ?>
            </div>
        </div>
        <div id="leftRightContainer" class="row-fluid">
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
                                <input type="text" name="authorID" id="authorID" maxlength="255" value="<?php echo $this->authorID ?>" />
                                (<?php echo isset($this->authorName) ? htmlspecialchars($this->authorName) : '<i>unknown</i>'; ?>)
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
                <div id="embedContainer" class="jvideo-edit-section">
                    <div class="title">
                        <?php echo JText::_("JV_EMBED_CODE"); ?>
                    </div>
                    <div class="content">
                        <label for="embedCodeAutoPlay"><input name="embedCodeAutoPlay" id="embedCodeAutoPlay" type="checkbox" onclick="embedSetAutoPlay('embedCode', this.checked)"> AutoPlay</label>
                        &nbsp;&nbsp;
                        <label for="embedCodeFullScreen"><input name="embedCodeFullScreen" id="embedCodeFullScreen" type="checkbox" onclick="embedSetFullScreen('embedCode', this.checked)" checked="checked"> Allow Fullscreen</label>
                        <div class="embed-dimensions-container">
                            <span>W</span>
                            <input class="embed-dimensions-input" type="text" name="embedCodeWidth" id="embedCodeWidth" size="4" maxlength="4" value="<?php echo $this->videoPlayerWidth; ?>"/>
                            <span>x H</span>
                            <input class="embed-dimensions-input" type="text" name="embedCodeHeight" id="embedCodeHeight" size="4" maxlength="4" value="<?php echo $this->videoPlayerHeight; ?>" />
                            <input type="button" class="btn" value="Set" onClick="embedSetHeightWidth('embedCode', document.getElementById('embedCodeHeight').value, document.getElementById('embedCodeWidth').value);">
                        </div>
                        <br />
                        <input type="text" readonly="readonly" onFocus="this.select()" id="embedCode" value="<?php echo htmlentities($this->embedCode); ?>" />
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
                        <textarea name="desc" id="desc" cols="50" rows="4"><?php echo $this->desc; ?></textarea>
                    </div>
                </div>
                <div id="categoriesContainer" class="jvideo-edit-section">
                    <div class="title">
                        <?php echo JText::_("JV_ALL_CATEGORIES"); ?>
                    </div>
                    <div class="content">
                        <?php echo JHTML::_('jvideo.videoCategory.videoCategoryList', $this->categories, $this->videoCategories); ?>
                    </div>
                </div>
                <div id="tagsContainer" class="jvideo-edit-section">
                    <div class="title">
                        <?php echo JText::_("JV_EDIT_TAGS") ?>
                    </div>
                    <div class="content">
                        <input type="text" name="tags" id="tags" maxlength="255" value="<?php echo $this->tags; ?>" />
                        <div class="example">
                            <?php echo JText::_("JV_EDIT_TAGS_EXAMPLE"); ?>
                        </div>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
        <div class="clear">&nbsp;</div>
	</div>
</form>