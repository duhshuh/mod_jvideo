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

if ($this->accountIsNotSetup) {
    ?>

    <h2 align="center">
        <img src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/images/warp-logo.png" alt="Warp Logo" /><br />
        Account Not Found
    </h2>
    <h3 align="center"><a href="index.php?option=com_jvideo&view=client_setup">Click here to setup your account</a></h3>
    <?php
    return;
}

$app = JFactory::getApplication();
$template = $app->getTemplate();
$iconFolderWebPath = 'templates/' . $template. '/images/admin';
?>
<style type="text/css">
    .videos-header .videos-header-search, .videos-header .videos-header-thumbs {
        float: left;
        white-space: nowrap;
    }

    .videos-header input, .videos-header button, .videos-header select {
        margin-top: 0;
        margin-bottom: 0;
    }

    .videos-header .videos-header-thumbs {
        padding-left: 15px;
    }

    .videos-header .videos-header-thumbs label {
        display: inline;
    }

    .videos-header * {
        vertical-align: middle;
    }

    .videos-header .videos-header-categories {
        float: right;
    }
</style>
<div id="j-main-container">
<form action="index.php" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="category" id="category" value="<?php echo JRequest::getVar('category', ''); ?>" />
    <input type="hidden" name="orderBy" id="orderBy" value="<?php echo JRequest::getVar('orderBy', 'id'); ?>" />
    <?php
    $videoCollection = $this->items->getCollection();
    
    $imgBase = JURI::root(true) . "/media/com_jvideo/admin/images";
    ?>
    <div id="editcell">
        <table class="videos-header" width="100%">
            <tr valign="bottom">
                <td>
                    <div class="videos-header-search">
                        <span><?php echo JText::_( 'Search' ); ?>:</span>
                        <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by title' );?>" />
                        <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                        <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
                    </div>

                    <div class="videos-header-thumbs">
                        <input type="hidden" name="enableThumbs" id="enableThumbs" value="<?php echo $this->enableThumbs; ?>" />
                        <input type="checkbox" name="chkEnableThumbs" id="chkEnableThumbs" <?php if ($this->enableThumbs != "false") { echo "checked=\"checked\""; } ?>  onclick="document.getElementById('enableThumbs').value = this.checked; this.form.submit();">
                        <label for="chkEnableThumbs"><?php echo JText::_(" Show Thumbnails"); ?></label>
                    </div>

                    <div class="videos-header-categories">
                        <?php
                        echo JHTML::_('jvideo.category.videoManagerDropDownList', $this->lists['categories'], JRequest::getVar('category', null));
                        ?>
                    </div>

                    
                </td>
            </tr>
        </table>

        <table class="adminlist table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_( '#' ); ?></th>
                    <th>
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $videoCollection ); ?>);" />
                    </th>
                    <th><a href="#" onclick="orderItemsBy('status')"><?php echo JText::_('Status'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('video_title')"><?php echo JText::_('Video Title'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('published')"><?php echo JText::_('Published'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('feature_id')"><?php echo JText::_('Featured'); ?></a>
                        <br /><small>[<a href="index.php?option=com_jvideo&view=featured_videos"><?php echo JText::_(' order '); ?></a>]</small></th>
                    <th><a href="#" onclick="orderItemsBy('duration')"><?php echo JText::_('Duration'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('date_added')"><?php echo JText::_('Date Added'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('hits')"><?php echo JText::_('Views' ); ?></a></th>
                    <th width="125"><a href="#" onclick="orderItemsBy('category_name')"><?php echo JText::_('Categories'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('username')"><?php echo JText::_('Author'); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('id')"><?php echo JText::_('Video&nbsp;ID' ); ?></a></th>
                    <th><a href="#" onclick="orderItemsBy('admin_approved')"><?php echo JText::_('Approved'); ?></a></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <?php
            function truncateString($string, $length, $stopanywhere=false) {
                if (strlen($string) > $length) {
                    $string = substr($string,0,($length -3));
                    if ($stopanywhere) {
                        $string .= '...';
                    } else{
                        $string = substr($string,0,strrpos($string,' ')).'...';
                    }
                }
                return $string;
            }

            $thumbnailCrossfade = array();
            $k = 0;
            for ($i = 0, $n = count($videoCollection); $i < $n; $i++)
            {
                $video = $videoCollection[$i];

                $thumbnailCrossfade[] = array(
                    "id" => $video->getID()
                    ,"thumbs" => $video->getThumbnails()
                );

                $checked = JHTML::_( 'grid.id', $i, $video->getID() );
                ?>
            <tr class="<?php echo "row$k"; ?>">
                <td align="center" width="5">
                        <?php echo $i + 1 + $this->pagination->limitstart; ?>
                </td>
                <td align="center" width="20">
                        <?php echo $checked; ?>
                </td>
                <td align="center" width="20">
                        <?php
                        
                        switch ($video->getStatus())
                        {
                            case "error":
                                echo "<img src=\"".$imgBase."/icon_error.png\" alt=\"".JText::_("Error")."\" title=\"".JText::_("Error: Video could not be converted")."\" />";
                                break;
                            case "deleted":
                                echo "<img src=\"$iconFolderWebPath/publish_x.png\" alt=\"".JText::_("Deleted")."\" title=\"".JText::_("Deleted")."\" />";
                                break;
                            case "pending":
                            case "waiting for upload":
                                echo "<img src=\"".$imgBase."/icon_wait.png\" alt=\"".JText::_("Processing")."\" title=\"".JText::_("Processing")."\" />";
                                break;
                            case "complete":
                            default:
                                if ($this->enableThumbs != "false") {
                                    echo "<div height=\"67\" width=\"90\" style=\"background-image: url('". $video->getThumbURL() . "'); vertical-align: middle; text-align: center; background-position: cover; width: 90px\">"
                                        ."<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=edit&cid[]=".$video->getID())."\" "
                                        ." height=\"67\" width=\"90\">"
                                        ."<img id=\"jvthumb_" . $video->getID() . "\" "
                                        ."src=\"" . $imgBase . "/blank.gif\" "
                                        ."border=\"0\" height=\"67\" width=\"90\" style=\"background-image: url('". $imgBase . "/icon_24x24_play.png'); background-repeat: no-repeat; background-position: right;\" />"
                                        ."</a>"
                                        ."</div>";
                                } else {
                                    echo "<img src=\"".$imgBase."/icon_play.png\" alt=\"".JText::_("Completed")."\" title=\"".JText::_("Completed")."\" />";
                                }
                                break;
                        }
                        ?>
                </td>
                <td>
                        <?php
                        echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=edit&cid[]=".$video->getID())
                            ."\" title=\"" . $video->getVideoTitle() . "\" >";
                        echo str_replace(' ', '&nbsp;', truncateString($video->getVideoTitle(), 35));
                        echo "</a>\n";
                        ?>
                </td>

                <?php
                $sortVars = "&orderBy=" . JRequest::getVar('orderBy') . "&category=" . JRequest::getVar('category');
                ?>

                <td align="center">
                    <?php if ($video->getPublished()) : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=unpublish&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $iconFolderWebPath; ?>/publish_g.png" alt="Published" title="<?php echo JText::_("Published"); ?>" />
                            <?php echo "</a>"; ?>
                        <?php elseif ($video->isExpired() && $video->isPublished()) : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=unpublish&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $iconFolderWebPath; ?>/publish_r.png" alt="Expired" title="<?php echo JText::_("Expired"); ?>" />
                            <?php echo "</a>"; ?>
                        <?php elseif ($video->isPending() && $video->isPublished()) : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=unpublish&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $iconFolderWebPath; ?>/publish_y.png" alt="Pending" title="<?php echo JText::_("Pending"); ?>" />
                            <?php echo "</a>"; ?>
                        <?php else : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=publish&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $iconFolderWebPath; ?>/publish_x.png" alt="Not Published" title="<?php echo JText::_("Not Published"); ?>" />
                            <?php echo "</a>"; ?>
                    <?php endif; ?>
                </td>
                <td align="center" width="1%">
                        <?php if ($video->isFeatured()) : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=unfeature&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $imgBase ?>/icon_16x16_star.png" />
                            <?php echo "</a>"; ?>
                        <?php else : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=feature&cid[]=".$video->getID().$sortVars)."\">"; ?>
                            <img src="<?php echo $imgBase ?>/icon_16x16_nostar.png" />
                            <?php echo "</a>"; ?>
                        <?php endif; ?>
                </td>
                <td align="right">
                        <?php
                        $duration = floatval($video->getDuration());
                        echo floor($duration / 60) . ':' . sprintf("%02s", round($duration % 60).'');
                        ?>
                </td>
                <td align="center">
                        <?php
                        if ($video->getDateAdded() !== null)
                        {
                            $dateAdded = JFactory::getDate($video->getDateAdded());
                            echo $dateAdded->format(JText::_('JV_FORMAT_DATE'));
                        }
                        ?>
                </td>
                <td align="right">
                        <?php echo $video->getHits(); ?>
                </td>
                <td align="left">
                        <?php
                        $categories = truncateString($video->getCategoryName(), 80);
                        $categories = "<u>" . str_replace(',', '</u>, <u>', $categories) . "</u>";
                        echo $categories;
                        ?>
                </td>
                <td align="center">
                    <span title="ID #<?php echo $video->getUserID();?>">
                        <?php if (!is_null($video->getUsername())) { ?>
                            <?php echo htmlspecialchars($video->getUsername()); ?>
                        <?php } else { ?>
                            <i>unknown</i>
                        <?php } ?>
                    </span>
                </td>
                <td align="center">
                        <?php echo $video->getID(); ?>
                </td>
                <td align="center" width="1%">
                        <?php if ($video->getAdminApproved()) : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=unapprove&cid[]=".$video->getID().$sortVars)."\">"; ?>
                    <img src="<?php echo $imgBase ?>/icon_16x16_approved.png" alt="Approved" title="<?php echo JText::_("Approved"); ?>" />
                            <?php echo "</a>"; ?>
                        <?php else : ?>
                            <?php echo "<a href=\"".JRoute::_("index.php?option=com_jvideo&view=videos&task=approve&cid[]=".$video->getID().$sortVars)."\">"; ?>
                    <img src="<?php echo $imgBase ?>/icon_16x16_notapproved.png" alt="Not Approved" title="<?php echo JText::_("Not Approved"); ?>" />
                            <?php echo "</a>"; ?>
                        <?php endif; ?>
                </td>
            </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </table>

        <table cellspacing="0" cellpadding="6" border="0" align="center">
            <tr align="center">
                <td><img src="<?php echo $imgBase ?>/icon_play.png"></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_CONVERSION_SUCCESS"); ?></td>
                <td><img src="<?php echo $imgBase ?>/icon_wait.png"></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_CONVERSION_PENDING"); ?></td>
                <td><img src="<?php echo $imgBase ?>/icon_error.png" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_CONVERSION_FAILED"); ?></td>
                <td><img src="<?php echo $iconFolderWebPath; ?>/publish_x.png" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_DELETED"); ?></td>
            </tr>
            <tr align="center">
                <td><img src="<?php echo $iconFolderWebPath; ?>/publish_y.png" width="16" height="16" border="0" alt="Pending" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_PUBLISHED_PENDING"); ?></td>
                <td><img src="<?php echo $iconFolderWebPath; ?>/publish_g.png" width="16" height="16" border="0" alt="Visible" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_PUBLISHED_CURRENT"); ?></td>
                <td><img src="<?php echo $iconFolderWebPath; ?>/publish_r.png" width="16" height="16" border="0" alt="Finished" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_PUBLISHED_EXPIRED"); ?></td>
                <td><img src="<?php echo $iconFolderWebPath; ?>/publish_x.png" width="16" height="16" border="0" alt="Finished" /></td>
                <td><?php echo JText::_("JV_LEGEND_VIDEO_NOT_PUBLISHED"); ?></td>
            </tr>
            <tr align="center">
                <td><img src="<?php echo $imgBase ?>/icon_16x16_star.png"></td>
                <td><?php echo JText::_("JV_LEGEND_FEATURED"); ?></td>
                <td><img src="<?php echo $imgBase ?>/icon_16x16_nostar.png"></td>
                <td><?php echo JText::_("JV_LEGEND_NOT_FEATURED"); ?></td>
                <td><img src="<?php echo $imgBase ?>/icon_16x16_approved.png" /></td>
                <td><?php echo JText::_("JV_LEGEND_APPROVED"); ?></td>
                <td><img src="<?php echo $imgBase ?>/icon_16x16_notapproved.png" /></td>
                <td><?php echo JText::_("JV_LEGEND_NOT_APPROVED"); ?></td>
            </tr>
        </table>
    </div>

    <input type="hidden" name="option" value="com_jvideo" />
    <input type="hidden" name="view" value="videos" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
</form>
</div>

<script type="text/javascript">
<?php
if (count($videoCollection) == 0) {
    echo "consoleSync()";
} else {
    echo "normalSync()";
}
?>
</script>
