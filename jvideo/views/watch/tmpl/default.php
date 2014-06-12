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

$showVideo = true;

if ($this->jvVideo->getStatus() == "deleted")
{
		$showVideo = false;
		include("error_deleted.php");
}
else if (!$this->is_approved)
{
	if ($this->requires_approval)
	{
		if ($this->isModerator)
		{
			echo "<div id=\"jvideo-video-requires-approval\">"
				."<a"
				." onClick=\"JVideoAJAX.approveVideo(".$this->id."); document.getElementById('jvideo-video-requires-approval').innerHTML = 'Video has been approved!<br /><br />'; return false;\""
				." href=\"#jvideo\">".JText::_("JV_VID_REQUIRES_APPROVAL")
				."</a><br /><br />"
				."</div>";
			$showVideo = true;
		}
		else
		{
			echo "<p>" . JText::_("JV_CANNOT_APPROVE") . "</p><p>&nbsp;</p>";		

			$showVideo = $this->isOwner;
		}
	}
}

if ($showVideo)
{
	if (!$this->jvVideo->getPublished() && !$this->isOwner && !$this->isModerator)
	{
		include("error_unavailable.php");

		$showVideo = false;
	}	
}
	
if (!$showVideo) return;

$playerAspectClass = 'aspect-16x9';
if ($this->aspectConstraint === JVIDEO_ASPECT_CONSTRAINT_4x3)
{
	$playerAspectClass = 'aspect-4x3';
}
?>
<div class="jvideo-wrapper jvideo-watch">
	<div class="jvideo-container" name="jvideo">
		<div class="jvideo-title row-fluid">
			<div class="span12"><?php echo $this->jvVideo->getVideoTitle(); ?></div>
		</div>


		<div class="jvideo-player row-fluid">
			<div class="span12">
				<div class="jvideo-player-container <?php echo $playerAspectClass; ?>">
					<?php
					if ($this->jvVideo->getStatus() == "complete")
					{
						if ($this->isMobile)
						{
							include("player_mobile.php");
						} else {
							include("player_standard.php");
						}
					}
					else if ($this->jvVideo->getStatus() == "pending")
					{
						echo '<div class="jvideo-error">' .  JText::_("JV_ERROR_VIDEO_NOT_YET_CONVERTED") . '</div>';
					}
					else
					{
						echo '<div class="jvideo-error">' . JText::_("JV_ERROR_VIDEO_UNKNOWN") . '</div>';
					}
					?>
				</div>
			</div>
		</div>
		
		
		<?php if ($this->showSocialButtons) : ?>
			<div class="jvideo-social row-fluid">
				<?php echo $this->socialButtons; ?>
			</div>
		<?php endif; ?>

		
		<?php if ($this->showInfoBox) : ?>
		<div class="row-fluid">
			<div class="jvideo-watch-info">
				<div class="row-fluid">
					<div class="span6">
						<?php
						if ($this->showAuthor) :
							$profileUrl = JRoute::_($this->jvProfile->getProfileURL($this->jvVideo->getUserID(), $this->params->get('profile_target_itemid')));
							$avatarUrl = $this->jvProfile->getAvatarURL();
							?>
							<div class="row-fluid">
									<?php
									if ($avatarUrl)
									{
										?>
										<div class="span4">
											<div class="jvideo-video-author-avatar">
												<a href="<?php echo $profileUrl; ?>"><img id="jvideo_video_author_avatar" src="<?php echo $avatarUrl; ?>" border="0" /></a>
											</div>
										</div>
										<div class="span8">
										<?php
									}
									else
									{
										?>
										<div class="span12">
										<?php
									}
									?>
									<div>
										<strong><?php echo JText::_("JV_VIDEO_AUTHOR"); ?></strong>
										<a href="<?php echo $profileUrl; ?>"><?php echo $this->jvVideo->getUserFullName(); ?></a>
									</div>
									<?php

									if ($this->showJoinDate) :
										?>
										<div>
											<strong><?php echo JText::_("JV_VIDEO_AUTHOR_JOINED"); ?></strong>
											<?php echo $this->date_joined; ?>
										</div>
										<?php
									endif;

									if ($this->showVideoCount) :
										?>
										<div>
											<strong><?php echo JText::_("JV_VIDEO_AUTHOR_VIDEOS"); ?></strong>
											<?php echo $this->video_count; ?>
										</div>
										<?php
									endif;

									if ($this->showDateAdded) :
										$dateAdded = JFactory::getDate($this->jvVideo->getDateAdded());
										$dateAdded = $dateAdded->format(JText::_("JV_VIDEO_DATE_ADDED_FORMAT"));
										?>
										<div>
											<strong><?php echo JText::_("JV_VIDEO_DATE_ADDED"); ?></strong>
											<?php echo $dateAdded; ?>
										</div>
										<?php
									endif;
									?>
								</div>
							</div>
						<?php

						elseif ($this->showDateAdded) :
							$dateAdded = JFactory::getDate($this->jvVideo->getDateAdded());
							$dateAdded = $dateAdded->format(JText::_("JV_VIDEO_DATE_ADDED_FORMAT"));
							?>
							<div>
								<strong><?php echo JText::_("JV_VIDEO_DATE_ADDED"); ?></strong>
								<?php echo $dateAdded; ?>
							</div>
							<?php
						endif;


						if ($this->showCategories) :

							$categories = trim($this->jvVideo->getCategoryName());

							if ($categories != '')
							{
								$array = explode(',', $categories);
								$uniqueArray = array_unique($array);
								$categoryArray = array_values($uniqueArray);
								$cleanCategoryCount = count($categoryArray);
								$cleanCategories = implode(', ', $categoryArray);
								?>
								<div>
									<strong>
										<?php
										if ($cleanCategoryCount == 1)
											echo JText::_("JV_VIDEO_CATEGORY");
										else if ($cleanCategoryCount > 1)
											echo JText::_("JV_VIDEO_CATEGORIES");
										else
											echo JText::_("JV_VIDEO_CATEGORIES_NONE");
										?>
									</strong>
									<?php echo $this->jvVideo->getCategoryName(); ?>
								</div>
								<?php
							}
						endif;

						if ($this->showTags && $this->jvVideo->getTags() != '') :
							?>
							<div>
								<strong><?php echo JText::_("JV_VIDEO_TAGS"); ?></strong>
								<?php echo $this->jvVideo->getTags(); ?>
							</div>
							<?php
						endif;

						if ($this->showEmbedded || $this->showLinkToVideo) :
							?>
							<div class="jvideo-embed">
								<?php
								if ($this->showLinkToVideo) :
									?>
									<p>
										<div id="jvideo_link_to_this_page"><strong><?php echo JText::_("JV_VIDEO_LINK_TO_THIS_PAGE"); ?></strong></div>
										<input type="text" name="link_to_page" value="<?php echo $this->jvVideo->getVideoAbsoluteURL($this->params->get('video_target_itemid')); ?>" onClick="javascript:this.select();" />
									</p>
									<?php
								endif;

								if ($this->showEmbedded) :
									$embedPlayerURL = 'http://manage.warphd.com/assets/player.swf?a=' . $this->infinoAccountKey . '&amp;v=1';
									?>
									<p>
										<div><strong><?php echo JText::_("JV_VIDEO_EMBED"); ?></strong></div>
										<input type="text" name="embed_video" value="&lt;object width=&quot;<?php echo $this->videoPlayerWidth; ?>&quot; height=&quot;<?php echo $this->videoPlayerHeight; ?>&quot;&gt;&lt;param name=&quot;allowFullScreen&quot; value=&quot;true&quot; /&gt;&lt;param name=&quot;src&quot; value=&quot;<?php echo $embedPlayerURL; ?>&quot;/&gt;&lt;param name=&quot;allowScriptAccess&quot; value=&quot;always&quot; /&gt;&lt;param name=&quot;flashvars&quot; value=&quot;<?php echo $this->flashvars; ?>&quot; /&gt;&lt;param name=&quot;wmode&quot; value=&quot;transparent&quot;&gt;&lt;/param&gt;&lt;embed src=&quot;<?php echo $embedPlayerURL ?>&quot; type=&quot;application/x-shockwave-flash&quot; wmode=&quot;transparent&quot; width=&quot;<?php echo $this->videoPlayerWidth; ?>&quot; height=&quot;<?php echo $this->videoPlayerHeight; ?>&quot; allowFullScreen=&quot;true&quot; flashVars=&quot;<?php echo $this->flashvars; ?>&quot;&gt;&lt;/embed&gt;&lt;/object&gt;" onClick="javascript:this.select();" />
									</p>
									<?php
								endif;
								?>
							</div>
							<?php
						endif;

						if ($this->isOwner || $this->isModerator)
						{
							if ($this->jvVideo->getStatus() != "deleted")
							{
								?>
								<p>
								<?php 
								if (!$this->is_approved && $this->requires_approval) 
								{
									?>
									<span class="publish-video-notapproved"><?php echo JText::_("JV_VIDEO_NOT_APPROVED"); ?></span>
									<?php
								}		
								else if ($this->jvVideo->getPublished())
								{
									?>
									<span class="publish-video-published"><?php echo JText::_("JV_VIDEO_PUBLISHED"); ?></span>
									<?php						
								}				
								else
								{
									if (method_exists('JFactory', 'getDate'))
									{
										$today = JFactory::getDate(time());
									}
									else
									{
										$today = date('Y-m-d H:i:s');
									}
									if (($this->jvVideo->getPublishDown() != "0000-00-00 00:00:00")
										&& ($this->jvVideo->getPublishDown() <= $today->toSql()))
									{
										?>
										<span class="publish-video-expired"><?php echo JText::_("JV_VIDEO_EXPIRED"); ?></span>
										<?php
									}
									else
									{
										?>
										<span class="publish-video-unpublished"><?php echo JText::_("JV_VIDEO_UNPUBLISHED"); ?></span>
										<?php
									}
								}
								?>
								- <a href="<?php echo JRoute::_('index.php?option=com_jvideo&view=edit&id='.$this->jvVideo->getID().($this->params->get('video_target_itemid') != "" ? "&Itemid=".$this->params->get("video_target_itemid") : "")); ?>"><?php echo JText::_("JV_VIDEO_EDIT"); ?></a> 
								- <a href="<?php echo JRoute::_('index.php?option=com_jvideo&task=delete_video&id='.$this->jvVideo->getID().($this->params->get('video_target_itemid') != "" ? "&Itemid=".$this->params->get("video_target_itemid") : "")); ?>" onClick="return confirm('<?php echo JText::_("JV_VIDEO_DELETE_CONFIRM"); ?>');"><?php echo JText::_("JV_VIDEO_DELETE"); ?></a> 
								
								<?php
								if ($this->isModerator)
								{
									if ($this->isFeatured)
									{
										?>
										- <span id="markFeatured"><a href="#jvideo" title="<?php echo JText::_("JV_VIDEO_UNFEATURE_TITLE") ?>" onclick="JVideoAJAX.featureVideo(<?php echo $this->jvVideo->getID(); ?>, 'false'); return false;"><?php echo JText::_("JV_VIDEO_FEATURED"); ?></a></span>
										<?php
									}
									else
									{
										?>
										- <span id="markFeatured"><a href="#jvideo" title="<?php echo JText::_("JV_VIDEO_FEATURE_TITLE"); ?>" onclick="JVideoAJAX.featureVideo(<?php echo $this->jvVideo->getID(); ?>, 'true'); return false;" /><?php echo JText::_("JV_VIDEO_NOT_FEATURED"); ?></a></span>
										<?php					
									}
								}
								?>
								</p>
								<?php
							}
						}
						?>
					</div>				
					<div class="span6">
						<div class="row-fluid">
							<?php

							if ($this->showViews) :
								?>
								<div class="jvideo-views span6">
									<strong><?php echo JText::_("JV_VIDEO_VIEWS"); ?></strong>
									<?php echo $this->jvVideo->getHits() == 0 ? 1 : $this->jvVideo->getHits(); ?>
								</div>
								<?php
							endif;

							if ($this->hasRatings) :
								?>
								<div id="ajaxRating" class="jvideo-rating span6">
									<strong><?php echo JText::_("JV_VIDEO_RATE"); ?></strong>
									<?php
									$blankImg = JURI::base() . "/media/com_jvideo/site/images/blank.gif";
									$emptyStar = "jvideo-stars-large-empty";
									$halfStar = "jvideo-stars-large-half";
									$filledStar = "jvideo-stars-large-full";

									$starMap = '00000';
									for ($i = 1; $i <= 5; $i++)
									{
										$starMap[$i - 1] = floor($this->jvVideo->getRatingAvg()) >= $i ? '2' : (round($this->jvVideo->getRatingAvg()) == $i ? '1' : '0');
									}

									if ($this->jvVideo->getRatingCount() == 0) {
										echo '<span class="jvideo-rating-stars" title="' . JText::_("JV_VIDEO_NOT_YET_RATED") . '">';
									} else {
										echo '<span class="jvideo-rating-stars" title="' . round($this->jvVideo->getRatingAvg(), 1) . ' ' . JText::_("JV_VIDEO_OUT_OF_5_STARS") . '">';
									}

									$starMapRate = '00000';
									for ($i = 1; $i <= 5; $i++)
									{
										$star = floor($this->jvVideo->getRatingAvg()) >= $i ? $filledStar : (round($this->jvVideo->getRatingAvg()) == $i ? $halfStar : $emptyStar);
										$starMapRate[$i - 1] = '2';

										if ($this->isLoggedIn)
										{
											echo '<a'
												.' onclick="JVideoAJAX.rateVideo(' . $this->jvVideo->getID() . ', ' . $this->user_id . ',' . $i . '); return false;"'
												." onmouseover=\"JVideoAJAX.highlightStars('" . $starMapRate . "', '" . $filledStar . "', '" . $halfStar . "', '" . $emptyStar . "');\" "
												." onmouseout=\"JVideoAJAX.highlightStars('" . $starMap . "', '" . $filledStar . "', '" . $halfStar . "', '" . $emptyStar . "');\" "
												.' href="#">'
												.'<img src="' . $blankImg . '" class="' . $star . '" id="videoStar' . $i . '" vspace="0" hspace="0" height="16" width="16" border="0">'
												.'</a>';
										}
										else
										{
											echo '<img src="' . $blankImg . '" class="' . $star . '" id="videoStar' . $i . '" vspace="0" ' .
												 'hspace="0" height="16" width="16" border="0" />';
										}
									}
									echo '</span>';

									if (!$this->isLoggedIn)
										echo "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\"></span><span class=\"jvideo-rating-count\">". JText::_("JV_VIDEO_SIGN_IN_TO_RATE") ."</span>";
									else if ($this->jvVideo->getRatingCount() == 1)
										echo "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\"></span><span class=\"jvideo-rating-count\">". JText::_("JV_VIDEO_1_RATING") ."</span>";
									else if ($this->jvVideo->getRatingCount() == 0)
										echo "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\"></span><span class=\"jvideo-rating-count\">". JText::_("JV_VIDEO_NOT_YET_RATED") ."</span>";
									else
										echo "<br /><span id=\"jvideo-rating-thanks\" class=\"jvideo-rating-thanks\"></span><span class=\"jvideo-rating-count\">".$this->jvVideo->getRatingCount()." ". JText::_("ratings") ."</span>";
									?>
								</div>
								<?php

							endif;
							?>
						</div>

						<?php

						if ($this->showDescription) :
							?>
							<div class="row-fluid">
								<div id="span12">
									<?php
									echo "<strong><u>" . JText::_("JV_VIDEO_ABOUT_THIS_VIDEO") . "</u></strong> ";
									echo "<br />";
									echo nl2br($this->jvVideo->getVideoDescription());
									?>
								</div>
							</div>
							<?php
						endif;

						?>
					</div>
					
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>
		<?php endif; //showInfoBox ?>

		<?php if ($this->showLinkback) : ?>
			<div class="row-fluid">
				<div class="jvideo-link-back span12">
					<a href="http://jvideo.warphd.com" target="_blank">JVideo</a> powered by <a href="http://www.warphd.com" target="_blank">Warp</a>
				</div>
			</div>
		<?php endif; ?>

		<div class="row-fluid">
			<div class="jvideo-comments span12">
				<?php $this->jvComments->displayComments(); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
JVideoAJAX.normalSync();
</script>
