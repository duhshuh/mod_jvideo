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
<script	src="<?php echo JURI::root(); ?>/media/com_jvideo/admin/js/form_controls.js"	language="javascript"></script>
<form action="index.php" method="post"	name="adminForm" id="adminForm">
    <input type="hidden" name="option" id="option" value="com_jvideo" />
    <input type="hidden" name="view" id="view" value="videos" />
    <input type="hidden" name="task" id="task" value="save" />
    <input type="hidden" name="videoId" id="videoId" value="<?php echo $this->videoId; ?>" />
    <input type="hidden" name="videoGuid" id="videoGuid" value="<?php echo $this->videoGuid; ?>" />
    <div id="addVideoWrapper">
        <div id="titleContainer">
            <input type="text" name="title" id="title" maxlength="50" value="Untitled" />
        </div>
        <div id="previewContainer">
            <div class="content">
                <div id="jvideoPreviewPlaceholder"></div>
                <?php echo JText::_("JV_VIDEO_UPLOADED_NOW_PROCESSING"); ?>
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
                            <div class="content">
                                <?php echo $this->lists['published']; ?>
                            </div>
                        </div>
                        <div id="publishUpContainer" class="field">
                            <div class="subtitle">
                                <?php echo JText::_("JV_PUBLISH_UP") ?>
                            </div>
                            <div class="content">
                                <a onClick="javascript:document.getElementById('publishUp').value = '';"><?php echo JText::_("JV_PUBLISH_RESET"); ?></a>
                                <?php echo JHTML::calendar($this->publishUp == "0000-00-00 00:00:00" ? "" : $this->publishUp, "publishUp", "publishUp"); ?>
                            </div>
                        </div>
                        <div id="publishDownContainer" class="field">
                            <div class="subtitle">
                                <?php echo JText::_("JV_PUBLISH_DOWN") ?>
                            </div>
                            <div class="content">
                                <a onClick="javascript:document.getElementById('publishDown').value = '';"><?php echo JText::_("JV_PUBLISH_RESET"); ?></a>
                                <?php echo JHTML::calendar($this->publishDown == "0000-00-00 00:00:00" ? "" : $this->publishDown, "publishDown", "publishDown"); ?>
                            </div>
                        </div>
                        <div id="authorContainer" class="field">
                            <div class="subtitle">
                                <?php echo JText::_("JV_AUTHOR") ?>
                            </div>
                            <div class="content">
                                <input type="text" name="authorID" id="authorID" maxlength="255" value="<?php echo $this->userId ?>" />
                            </div>
                        </div>
                        <div id="dateAddedContainer" class="field">
                            <div class="subtitle">
                                <?php echo JText::_("JV_DATE_ADDED") ?>
                            </div>
                            <div class="content">
                                <?php echo JHTML::calendar($this->dateAdded, "dateAdded", "dateAdded"); ?>
                            </div>
                        </div>
                        <div id="featureContainer" class="field">
                            <div class="subtitle">
                                <?php echo JText::_("JV_UPLOAD_ADD_FEATURED") ?>
                            </div>
                            <div class="content">
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
                        <textarea name="desc" id="desc" cols="50" rows="4"></textarea>
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
            <div class="clear">&nbsp;</div>
        </div>
        <div class="clear">&nbsp;</div>
	</div>
</form>
