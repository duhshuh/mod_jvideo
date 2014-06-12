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
$inputFilter = JFilterInput::getInstance();

$pageClassSuffix = $this->params->get('pageclass_sfx');
?>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="GET" name="jvideoForm" id="jvideoForm" >
<input type="hidden" name="option" value="com_jvideo" />
<input type="hidden" name="view" value="videos" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />

<div class="jvideo-wrapper<?php echo $pageClassSuffix; ?>">

	<?php if ($this->params->get('show_page_title')) : ?>
	<div class="jvideo-header<?php echo $pageClassSuffix; ?>">
		<h1><?php echo $this->header_title; ?></h1>
	</div>
	<?php endif; ?>
		
	<?php
	$video_layout = $this->params->get('video_layout');
	$video_layout_columns = $inputFilter->clean($this->params->get('video_layout_columns'), 'INT');

	if ($video_layout == "list")
	{
		$video_layout_columns = 1;
	}

	$spanClass = 'span' . (12 / $video_layout_columns);
	?>
	
	<div class="jvideo-videos-<?php echo $video_layout . $pageClassSuffix; ?> container-fluid">
		<div class="row-fluid">
			<?php
			$itemcount = 0;

			if (!is_null($this->items))
			{			
				$thumbnailCrossfade = array();
				
				foreach ($this->items as $item)
				{
					$thumbnailCrossfade[] = array(
						'id' => $item->getID(),
						'thumbs' => $item->getThumbnails($this->cacheThumbnails, $this->cacheProxyParams)
					);
					
					if ($itemcount > 0 && 0 == ($itemcount % $video_layout_columns)) {
						echo '</div><div class="row-fluid">';
					}
					
					$itemcount++;
					?>
					<div class="<?php echo $spanClass; ?>">
					<div class="jvideo-videos-video<?php echo $pageClassSuffix; ?>">
						<?php if ($this->params->get('show_thumbnail') && $this->params->get('show_video_title')) : ?>
							
							<div class="jvideo-videos-thumb<?php echo $pageClassSuffix; ?>">
								<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>">
								<img id="<?php echo "jvthumb_" . $item->getID() ?>" 
									src="<?php echo $item->getThumbURL($this->cacheThumbnails, $this->cacheProxyParams); ?>" 
									border="0" height="90" width="120" />
								<br />
								<span id="<?php echo "jvthumb_" . $item->getID() . "_title"; ?>" 
									class="jvideo-videos-title<?php echo $pageClassSuffix; ?>" 
									title="<?php echo $item->getVideoTitle(); ?>">	
								<?php if ($video_layout == "list") : ?>
									</span>
									</a>
									</div>
									<div class="jvideo-videos-title<?php echo $pageClassSuffix; ?>">
										<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>" title="<?php echo $item->getVideoTitle(); ?>">
								<?php endif; ?>
								<?php
								$title_limit = $inputFilter->clean($this->params->get('char_limit_title'), 'INT');
								echo $this->truncateText($item->getVideoTitle(), $title_limit);
								?>
								<?php if ($video_layout == "grid") : ?>
									</span>
								<?php endif; ?>
								</a>						
							</div>

						<?php elseif ($this->params->get('show_thumbnail')) : ?>

							<div class="jvideo-videos-thumb<?php echo $pageClassSuffix; ?>">
								<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>">
								<img id="<?php echo "jvthumb_" . $item->getID() ?>" 
									src="<?php echo $item->getThumbURL($this->cacheThumbnails, $this->cacheProxyParams); ?>" 
									border="0" height="90" width="120" />
								</a>
							</div>
						
						<?php else : ?>

							<div class="jvideo-videos-title<?php echo $pageClassSuffix; ?>">
								<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>" title="<?php echo $item->getVideoTitle(); ?>">
								<?php 
									$title_limit = $inputFilter->clean($this->params->get('char_limit_title'), 'INT');
									echo $this->truncateText($item->getVideoTitle(), $title_limit);
								?>
								</a>
							</div>
							
						<?php endif; ?>
						
						<?php if ($this->params->get('show_video_description')) : ?>
							<div class="jvideo-videos-desc<?php echo $pageClassSuffix; ?>">
								<?php
								$desc_limit = $inputFilter->clean($this->params->get('char_limit_description'), 'INT');
								echo $this->truncateText($item->getVideoDescription(), $desc_limit);
								?>
							</div>
						<?php endif; ?>
						
						<?php if ($this->params->get('show_video_author') && !is_null($item->getUsername())) : ?>
							<div class="jvideo-videos-author<?php echo $pageClassSuffix; ?>">
								<?php
								echo JText::_("JV_VIDEO_AUTHOR")." ";
								echo "<a href=\"".$this->jvProfile->getProfileURL($item->getUserID(), $this->params->get('profile_target_itemid'))."\">";
								echo htmlspecialchars($item->getUsername());
								echo "</a>";
								?>
							</div>
						<?php endif; ?>
						
						<?php if ($this->params->get('show_video_views')) : ?>
							<div class="jvideo-videos-views<?php echo $pageClassSuffix; ?>">
								<?php echo JText::_("JV_VIDEO_VIEWS"); ?> <?php echo $item->getHits(); ?>
							</div>
						<?php endif; ?>
						
						<?php if ($this->params->get('show_video_dateadded') && $item->getDateAdded() != "") : ?>
							<div class="jvideo-videos-dateadded<?php echo $pageClassSuffix; ?>">
								<?php echo JText::_("JV_VIDEO_ADDED"); ?>
								<?php
								if (method_exists('JFactory', 'getDate'))
								{
									$dateAdded = JFactory::getDate($item->getDateAdded());
									$dateAdded = $dateAdded->format(JText::_("JV_VIDEO_DATE_ADDED_FORMAT"));
									echo $dateAdded;
								}
								else
								{
									echo $item->getDateAdded();
								}
								?>
							</div>
						<?php endif; ?>
		
						<?php if ($this->params->get('show_video_rating')) : ?>
							<div class="jvideo-videos-rating<?php echo $pageClassSuffix; ?>">
							<?php
							
								if ($item->getRatingCount() > 0)
								{
									$blankImg = JURI::base() . "/media/com_jvideo/site/images/blank.gif";
									$emptyStar = "jvideo-stars-small-empty";
									$halfStar = "jvideo-stars-small-half";
									$fullStar = "jvideo-stars-small-full";
							
									for ($i_rating = 1; $i_rating <= 5; $i_rating++)
									{
										$star = floor($item->getRatingAvg()) >= $i_rating ? $fullStar : (round($item->getRatingAvg()) == $i_rating ? $halfStar : $emptyStar);
										echo "<img src=\"".$blankImg."\" class=\"".$star."\" height=\"12\" width=\"12\" "
											."alt=\"". JText::_("JV_VIDEO_RATED") ." ".round($item->getRatingAvg(), 1)." ". JText::_("JV_VIDEO_OF_5")."\" "
											."title=\"". JText::_("JV_VIDEO_RATED") ." ".round($item->getRatingAvg(), 1)." ". JText::_("JV_VIDEO_OF_5")."\" />";
									}
								}
								else
								{
									echo JText::_("JV_VIDEO_NO_RATING");
								}
							?>
							</div>
						<?php endif; ?>
		
						<?php if ($this->params->get('show_video_duration') && $item->getDuration() > 0) : ?>
							<div class="jvideo-videos-duration<?php echo $pageClassSuffix; ?>">
								<?php
								if ($item->getDuration() > 0)
								{
									$duration = round($item->getDuration());
									$mins = floor ($duration / 60);
									$secs = $duration % 60;
									if ($secs < 10)
										$secs = "0".$secs;
									echo $mins . ":" . $secs;
								}
								?>
							</div>
						<?php endif; ?>
						
						<div class="jvideo-videos-clear">
							<img src="<?php echo $this->baseurl . "/media/com_jvideo/site/images/blank.gif"; ?>" height="1" width="1" />
						</div>
					</div>
					</div>
					<?php
				}
			}
			
			if ($itemcount == 0)
			{
				echo "<p>" . JText::_("JV_VIDEO_LIST_NO_VIDEOS") ." <a href=\"". JRoute::_("index.php?option=com_jvideo&view=upload") ."\">". JText::_("JV_VIDEO_LIST_NO_VIDEOS_UPLOAD") . "</a></p>";
			}
			?>
		</div>
	</div>
	
	
	<div class="jvideo-footer<?php echo $pageClassSuffix; ?>">
		<?php
		if ($itemcount > 0)
		{ 
			if ($this->params->get('videos_per_page') == 0) {
				echo $this->pagination->getListFooter();
			} else {
				// custom jvideo footer
				echo "<div class=\"list-footer pagination\">";
				echo $this->pagination->getPagesLinks();
				echo "</div>";
			}
		}
        ?>
	</div>

    <?php if ($this->showLinkback) : ?>
        <div class="jvideo-link-back">
            <a href="http://jvideo.warphd.com" target="_blank">JVideo</a> powered by <a href="http://www.warphd.com" target="_blank">Warp</a>
        </div>
    <?php endif; ?>
	
	<div class="jvideo-videos-clear">
		<img src="<?php echo $this->baseurl . "/media/com_jvideo/site/images/blank.gif"; ?>" height="1" width="1" />
	</div>

	<?php
	// Thumbnail Animation
	if ($this->thumbFaderEnabled) :
	?>
	<script type="text/javascript">
	(function() {
		var PageCrossFades = [
		<?php
		$firstRun1 = true;
				
		foreach ($thumbnailCrossfade as $cf)
		{
			echo $firstRun1 ? '' : ',';
			$firstRun1 = false;
			
			echo "['jvthumb_" . $cf['id'] . "'," . '[';
			
				$firstRun2 = true;
				foreach ($cf['thumbs'] as $cfthumb)
				{
					echo $firstRun2 ? '' : ',';
					$firstRun2 = false;
					
					echo "'" . $cfthumb . "'";
				}
				
			echo ']]';
		}
		?>	
		];
		
		JVideo.imageCrossFade.setup(PageCrossFades);
	}());
	</script>
	<?php endif; ?>
	
</div>

<script type="text/javascript">
JVideoAJAX.normalSync();
</script>
</form>