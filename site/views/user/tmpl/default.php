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

if ($this->user_id == "")
{
	echo "<p>".JText::_("JV_PROFILE_LOGIN_REQUIRED")."</p>";
	return;
}
?>
<div class="jvideo-wrapper<?php echo $pageClassSuffix; ?> container-fluid">
	<form action="<?php echo JRoute::_('index.php');?>" method="get" name="jvideoForm" id="jvideoForm" >
		<input type="hidden" name="option" value="com_jvideo" />
		<input type="hidden" name="view" value="user" />

		<div class="jvideo-profile<?php echo $pageClassSuffix; ?> row-fluid">
			<div class="span3">
				<?php
				$avatarUrl = htmlspecialchars($this->profile->getAvatarURL());
				if (!$avatarUrl)
				{
					$avatarUrl = JURI::base() . '/media/com_jvideo/site/images/avatar-default.gif';
				}
				?>
				<div>
					<img class="jvideo-profile-avatar<?php echo $pageClassSuffix; ?>" src="<?php echo $avatarUrl; ?>" height="100" width="100" />
				</div>
				<?php

				if ($this->user_id == $this->user->id)
				{
					$uploadUrl = JRoute::_('index.php?option=com_jvideo&view=upload');
					$profileEditUrl = JRoute::_( 'index.php?option=com_jvideo&task=user_edit&user_id=' . $this->user_id);
					$linktext = $this->profile ? JText::_("JV_PROFILE_EDIT_YOUR_PROFILE") : JText::_("JV_PROFILE_CREATE_YOUR_PROFILE");
					?>
					<div class="jvideo-profile-button-container<?php echo $pageClassSuffix; ?>">
						<a href="<?php echo $profileEditUrl; ?>" class="btn jvideo-profile-btn<?php echo $pageClassSuffix; ?>"><?php echo $linktext; ?></a>
						<a href="<?php echo $uploadUrl; ?>" class="btn jvideo-profile-btn<?php echo $pageClassSuffix; ?>"><?php echo JText::_("JV_PROFILE_UPLOAD_A_VIDEO"); ?></a>
					</div>
					<?php
				}
				?>
			</div>
					
			<div class="span9">
				<h2 class="jvideo-profile-displayname">
					<?php echo htmlspecialchars($this->getDisplayName()); ?>
				</h2>
				<?php
				$profileFields = array(
						'JV_PROFILE_AGE' => htmlspecialchars($this->getAge()),
						'JV_PROFILE_LOCATION' => htmlspecialchars($this->profile->getLocation()),
						'JV_PROFILE_OCCUPATION' => htmlspecialchars($this->profile->getOccupation()),
						'JV_PROFILE_INTERESTS' => htmlspecialchars($this->profile->getInterests())
					);

				if ($this->profile->getWebsiteURL())
				{
					$profileFields['JV_PROFILE_WEBSITE'] = '<a href="' . htmlspecialchars($this->profile->getWebsiteURL()) . '">' . htmlspecialchars($this->profile->getWebsiteURL()) . '</a>';
				}

				foreach ($profileFields as $label => $value)
				{
					if ($value)
					{
						?>
						<div class="jvideo-profile-field-row<?php echo $pageClassSuffix; ?>">
							<span class="jvideo-profile-field-label<?php echo $pageClassSuffix; ?>"><?php echo JText::_($label); ?></span>
							<span class="jvideo-profile-field-value<?php echo $pageClassSuffix; ?>"><?php echo $value; ?></span>
						</div>
						<div class="clearfix"></div>
						<?php
					}
				}
				?>

				<?php if ($this->profile->getDescription()) : ?>
					<div class="jvideo-profile-description<?php echo $pageClassSuffix; ?>"><?php echo htmlspecialchars($this->profile->getDescription()); ?></div>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="clearfix"></div>
		<hr>
		
		<div class="jvideo-videos-grid<?php $pageClassSuffix; ?>">
	
			<h3><?php echo JText::_("JV_PROFILE_UPLOADED_VIDEOS"); ?></h3>
			
			<div class="row-fluid">
			<?php
	        $inputFilter = JFilterInput::getInstance();
			$itemcount = 0;
			$thumbnailCrossfade = array();
			$video_layout_columns = 4;
			$spanClass = 'span' . (12 / $video_layout_columns);
			
			foreach ($this->items as $item)
			{ 
				if ($item->getStatus() == 'pending')
				{
					continue;
				}
				
				$thumbnailCrossfade[] = array(
					 "id" => $item->getID()
					,"thumbs" => $item->getThumbnails($this->cacheThumbnails, $this->cacheProxyParams)
				);
				
				if ($itemcount > 0 && 0 == ($itemcount % $video_layout_columns)) {
					echo '</div><div class="row-fluid">';
				}

				$itemcount++;	
				?>
				<div class="<?php echo $spanClass; ?>">
					<div class="jvideo-videos-video<?php echo $pageClassSuffix; ?>">
				
					<?php if (($this->params->get('show_thumbnail')) && ($this->params->get('show_video_title'))) : ?>
						<div class="jvideo-videos-thumb<?php echo $pageClassSuffix; ?>">
							<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>">
								<img src="<?php echo $item->getThumbURL($this->cacheThumbnails, $this->cacheProxyParams); ?>"
									id="<?php echo "jvthumb_" . $item->getID() ?>"  
									border="0" height="90" width="120" />
								<br />
								<span id="<?php echo "jvthumb_" . $item->getID() . "_title"; ?>" 
									class="jvideo-videos-title<?php echo $pageClassSuffix; ?>" 
									title="<?php echo $item->getVideoTitle(); ?>">	

									<?php 
									$title_limit = $inputFilter->clean($this->params->get('char_limit_title'), 'INT');
									
									if ($title_limit > 0 && strlen($item->getVideoTitle()) > $title_limit)
									{
										echo substr($item->getVideoTitle(), 0, $title_limit - 1) . "...";
									}
									else
									{
										echo $item->getVideoTitle();
									}
									?>
								</span>
							</a>						
						</div>

					<?php elseif ($this->params->get('show_thumbnail')) : ?>
						
						<div class="jvideo-videos-thumb<?php echo $pageClassSuffix; ?>">
							<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>">
							<img src="<?php echo $item->getThumbURL($this->cacheThumbnails, $this->cacheProxyParams); ?>" 
								id="<?php echo "jvthumb_" . $item->getID() ?>"  
								border="0" height="90" width="120" />
							</a>
						</div>
					
					<?php else : ?>
						
						<div class="jvideo-videos-title<?php echo $pageClassSuffix; ?>">
							<a href="<?php echo $item->getVideoURL($this->params->get('video_target_itemid')); ?>" title="<?php echo $item->getVideoTitle(); ?>">
							<?php 
								$title_limit = $inputFilter->clean($this->params->get('char_limit_title'), 'INT');
								
								if ($title_limit > 0 && strlen($item->getVideoTitle()) > $title_limit)
								{
									echo substr($item->getVideoTitle(), 0, $title_limit - 1) . "...";
								}
								else
								{
									echo $item->getVideoTitle();
								}
							?>
							</a>
						</div>
						
					<?php endif; ?>


					<?php if ($this->params->get('show_video_views')) : ?>
						<div class="jvideo-videos-views<?php echo $pageClassSuffix; ?>">
							<?php echo JText::_("JV_VIDEO_VIEWS") . " " . $item->getHits(); ?>
						</div>
					<?php endif; ?>
					
					
					<?php if ($this->params->get('show_video_dateadded') && $item->getDateAdded() != "") : ?>
						<div class="jvideo-videos-dateadded<?php echo $pageClassSuffix; ?>">
							<?php
							$dateAdded = JFactory::getDate($item->getDateAdded());
							$dateAdded = $dateAdded->format(JText::_("JV_VIDEO_DATE_ADDED_FORMAT"));
							echo JText::_("JV_VIDEO_ADDED") . " " . $dateAdded;
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
									."alt=\"" . JText::_("JV_VIDEO_RATED") . " ".round($item->getRatingAvg(), 1)." " . JText::_("JV_VIDEO_OF_5") ."\" "
									."title=\"" . JText::_("JV_VIDEO_RATED") . " ".round($item->getRatingAvg(), 1)." " . JText::_("JV_VIDEO_OF_5") . "\" />";
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
					
					</div>
				</div>
				<?php
			}
			
			
			if ($itemcount == 0)
			{
				echo "<p>" . JText::_("JV_PROFILE_NO_USER_VIDEOS") . "</p>";
			}	
			?>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="jvideo-footer<?php echo $pageClassSuffix; ?>">
			<?php
			if ($itemcount > 0)
			{ 
				if ($this->params->get('videos_per_page') == 0) {
					echo $this->pagination->getListFooter();
				} else {
					// custom jvideo footer
					echo "<div class=\"list-footer\">";
					echo $this->pagination->getPagesLinks();
					echo "</div>";
				}
			}
			?>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="jvideo-profile-pending<?php echo $pageClassSuffix; ?>">
			<?php
			$firstrun = true;
			reset($this->items);
			foreach($this->items as $item)
			{
				
				if ((($this->user_id == $item->getUserID()) && ($item->getStatus() == "pending" || $item->getVideoTitle() == "")) && $this->user_id == $this->logged_uid)
				{
					if ($firstrun)
					{
						echo "<h1>" . JText::_("JV_PROFILE_VIDEOS_PENDING_PREFIX") . $this->getDisplayName() . JText::_("JV_PROFILE_VIDEOS_PENDING_SUFFIX") . "</h1>";
						echo "<ul>";
						$firstrun = false;
					}
					
					if ($item->getStatus() == "pending")
					{
						echo "<li><i><a href=\"" . $item->getVideoURL($this->params->get('video_target_itemid')) . "\">" . ($item->getVideoTitle() == "" ? JText::_("JV_VIDEO_UNTITLED") : $item->getVideoTitle()) . "</a></i> " . JText::_("JV_VIDEO_PENDING") . "</p>";
					} 
					else if ($item->getVideoTitle() == "")
					{
						echo "<li><i>" . JText::_("JV_VIDEO_UNTITLED") . "</i> (<a href=\"". JRoute::_('index.php?option=com_jvideo&view=watch&id='.$item->getID()) ."\">" . JText::_("JV_VIDEO_TITLE_NEEDED") . "</a>)";
					}
				}
			}
			
			if (!$firstrun)
			{
				echo "</ul>";
			}
			?>
		</div>
		<?php
		// Thumbnail Animation
		if ($this->thumbFaderEnabled) :
			?>
			<script type="text/javascript">
			(function() {
				var videoCrossFades = [
				<?php
				$firstRun1 = true;
						
				foreach ($thumbnailCrossfade as $cf)
				{
					echo $firstRun1 ? "" : ",";
					$firstRun1 = false;
					
					echo "['jvthumb_".$cf["id"]."'," ."[";
					
						$firstRun2 = true;
						foreach ($cf["thumbs"] as $cfthumb)
						{
							echo $firstRun2 ? "" : ",";
							$firstRun2 = false;
							
							echo "'".$cfthumb."'";
						}
						
					echo "]]";
				}
				?>	
				];
				
				JVideo.imageCrossFade.setup(videoCrossFades);
			}());
			</script>
		<?php endif; ?>

	</form>
	<script type="text/javascript">
	JVideoAJAX.normalSync();
	</script>
</div>