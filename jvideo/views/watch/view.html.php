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

jimport( 'joomla.application.component.view');
jvimport('UserAllowedToModerateSpecification');
jvimport2('ServiceLocator');
jvimport2('Video.Video');
jvimport2('Web.AssetManager');

class JVideoViewWatch extends JViewLegacy
{
	private $videoGuid = null;

	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();

		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		$cache = JFactory::getCache('com_jvideo');
		$config = JVideo_Factory::getConfig();
		$params = clone($mainframe->getParams('com_jvideo'));
		$id = JRequest::getInt('id');

		if (!$id) {
			echo "<p>" . JText::_("JV_ERROR_MISSING_VIDEO_ID") ."</p>";
			return;
		}

		if ($user->guest && $config->blockAnonViewers) {
			echo "<div>" . JText::_("JV_ANONYMOUS_VIEWERS_BLOCKED") . "</div>";
			return;
		}

		$jvVideo = $this->getVideo($id);
		$jvUser = $this->getUser($jvVideo->getUserID());

		if (is_null($jvVideo)) {
			echo "<p>". JText::_("JV_ERROR_VIDEO_DOES_NOT_EXIST") ."</p>";
			return;
		}

		$this->videoGuid = $jvVideo->getInfinoVideoID();

		$this->addHitImpression($id);

		$date_joined = JFactory::getDate($jvUser->registerDate);
		$date_joined = $date_joined->format(JText::_("JV_JOIN_DATE_FORMAT"));

		$usersVote = $cache->get(array($this, 'getRatingIfUserAlreadyVoted'), array($user->id, $jvVideo->id)
				, "video_rating_" . $jvVideo->id . "_" . $user->id);

		$warpVideo = new InfinovationVideo($config->infinoAccountKey, $config->infinoSecretKey);
		$flashvars = $warpVideo->getPlayerFlashVars($jvVideo->getInfinoVideoID()
				, $config->autoPlay, $config->anonDurLimit, $config->enforceAnonDurLimit && $user->guest);

		$breadcrumbs = $mainframe->getPathWay();
		$breadcrumbs->addItem($jvVideo->getVideoTitle(), $jvVideo->getVideoURL());

		$this->addScripts();
		$this->addStylesheets();

		$doc->setMetaData("title", $jvVideo->getVideoTitle());
		$doc->addHeadLink($jvVideo->getThumbURL(), "image_src");

		if ($config->seoEnabled)
		{
			$doc->setTitle($doc->getTitle() . " - " . $jvVideo->getVideoTitle());
			$doc->setMetaData("keywords", $jvVideo->getTags());
			$doc->setDescription($jvVideo->getVideoDescription());
		}

		$this->prepareMobileView();

		$this->addSocialPlugins($jvVideo);

		$this->id = $id;
		$this->isOwner = $this->isOwner($user, $jvVideo);
		$this->isModerator = $this->isModerator($user);
		$this->isLoggedIn = $user->id != "";
		$this->requires_approval = $config->requireAdminVidAppr;
		$this->is_approved = $jvVideo->getAdminApproved();
		$this->isFeatured = $jvVideo->isFeatured();
		$this->user_id = $user->id;
		$this->video_count = $jvUser->videoCount ? $jvUser->videoCount : 0;
		$this->videoPlayerHeight = $config->videoPlayerHeight;
		$this->videoPlayerWidth = $config->videoPlayerWidth;
		$this->aspectConstraint = $config->aspectConstraint;
		$this->hasRatings = $config->hasRatings;
		$this->showLinkback = $config->showLinkback;
		$this->showEmbedded = $config->showEmbedded;
		$this->showInfoBox = $config->showInfoBox;
		$this->showAuthor = $config->showAuthor;
		$this->showJoinDate = $config->showJoinDate;
		$this->showVideoCount = $config->showVideoCount;
		$this->showDateAdded = $config->showDateAdded;
		$this->showCategories = $config->showCategories;
		$this->showTags = $config->showTags;
		$this->showLinkToVideo = $config->showLinkToVideo;
		$this->showViews = $config->showViews;
		$this->showDescription = $config->showDescription;
		$this->showSocialButtons = $config->showSocialButtons;
		$this->infinoAccountKey = $config->infinoAccountKey;
		$this->playerSwfUrl = $this->getPlayerSwfUrl($jvVideo);
		$this->date_joined = $date_joined;
		$this->jvProfile = new JVideo_Profile($jvVideo->getUserID());
		$this->jvComments = new JVideo_Comments();
		$this->jvVideo = $jvVideo;
		$this->flashvars = $flashvars;
		$this->usersVote = $usersVote;
		$this->params = $params;

		parent::display($tpl);
	}

	private function prepareMobileView()
	{
		$model = $this->getModel('watch');
		$this->isMobile = $model->isMobile();

		if ($this->isMobile) {
			$this->setupMobile();
		}
	}

	private function getPlayerSwfUrl(JVideo_Video $video)
	{
		$playerRenderer = JVideo2_ServiceLocator::getInstance()->getPlayerRenderer();
		$video = new JVideo2_VideoAdapter($video);
		return $playerRenderer->generateSwfUrl($video);
	}

	private function setupMobile()
	{
		$model = $this->getModel('watch');

		$mobileUrls = $model->getMobileUrls($this->videoGuid);

		if (false !== $mobileUrls) {
			$mobileVideoUrl = $mobileUrls->url;
			$mobileThumbUrl = $mobileUrls->largeThumbnailUrl;

			$this->mobileThumbUrl = $mobileThumbUrl;
			$this->mobileVideoUrl = $mobileVideoUrl;
			$this->isMobileAvailable = $isMobileAvailable = true;
		} else {
			$this->isMobileAvailable = $isMobileAvailable = false;
		}
	}

	public function getVideo($id)
	{
		$model = $this->getModel('watch');

		return $model->getVideo($id);
	}

	private function getUser($userId)
	{
		$model = $this->getModel('watch');

		return $model->getUser($userId);
	}

	public function addHitImpression($videoId)
	{
		$model = $this->getModel('watch');

		return $model->addHitImpression($videoId);
	}

	public function getRatingIfUserAlreadyVoted($userId, $videoId)
	{
		$model = $this->getModel('watch');

		return $model->getRatingIfUserAlreadyVoted($userId, $videoId);
	}

	public function proxyControl(InfinovationSoapBase &$base)
	{
		$model = $this->getModel('watch');

		return $model->proxyControl($base);
	}

	private function isAuthenticated(&$user, &$video)
	{
		if ($this->isModerator($user))
			return true;
		else if ($this->isOwner($user, $video))
			return true;
		else
			return false;
	}

	private function isModerator(&$user)
	{
		$spec = new JVideo_UserAllowedToModerateSpecification();
		return $spec->isSatisfiedBy($user);
	}

	private function isOwner(&$user, &$video)
	{
		return $user->id == $video->userID;
	}

	private function addScripts()
	{
		$config = JVideo_Factory::getConfig();

		JVideo2_AssetManager::includeJQuery();
		JVideo2_AssetManager::includeJQueryUI();
		JVideo2_AssetManager::includeSiteCoreJs();

		if ($config->thumbFaderEnabled)
			JVideo2_AssetManager::includeCrossFadeJs();
	}

	private function addStylesheets()
	{
		JVideo2_AssetManager::includeSiteCoreCss();
	}

	private function addSocialPlugins($video)
	{
		$this->addSocialTags($video);
		$this->addSocialButtons($video);
	}

	private function addSocialTags($video)
	{
		$this->addFacebookTags($video);
	}
					
	private function addSocialButtons($video)
	{
		$html = "";
		$html .= $this->addFacebookLikeButton($video);
		$html .= $this->addAddThisButton($video);
		
		$this->assign("socialButtons", $html);
	}
	
	private function addFacebookTags($video)
	{
		$config = JVideo_Factory::getConfig();

		$fbTitle = $video->getVideoTitle() == "" ? "Untitled" : $video->getVideoTitle();
		$fbDescription = $video->getVideoDescription() == "" ? $fbTitle : $video->getVideoDescription();
		$fbThumbnail = $video->getThumbnail();

		$this->addMetaProperty("og:video", "http://files.warphd.com/FlashPlayer.swf?AccountKey=" . urlencode($config->infinoAccountKey) . "&VideoGuid=" . urlencode($this->videoGuid) . "&AutoPlay=1");
		$this->addMetaProperty("og:video:height", $config->videoPlayerHeight);
		$this->addMetaProperty("og:video:width", $config->videoPlayerWidth);
		$this->addMetaProperty("og:video:type", "application/x-shockwave-flash");
		$this->addMetaProperty("og:title", htmlspecialchars($fbTitle));
		$this->addMetaProperty("og:description", htmlspecialchars($fbDescription));
		$this->addMetaProperty("og:image", htmlspecialchars($fbThumbnail));
		$this->addMetaProperty("og:type", "article");
	}

	private function addMetaProperty($property, $value)
	{
		$doc = JFactory::getDocument();

		if ($doc->getType() == 'html')
			$doc->addCustomTag("<meta property=\"" . $property . "\" content=\"" . $value . "\" />");
	}

	private function addFacebookLikeButton($video)
	{
		$mainframe = JFactory::getApplication();
		$params = clone($mainframe->getParams('com_jvideo'));

		return "<div class=\"jvideo-social-left span6\"><iframe src=\"http://www.facebook.com/plugins/like.php?href="
			  . urlencode($video->getVideoAbsoluteURL($params->get('video_target_itemid'))) . "&amp;show_faces=false\" "
			  . " scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"height: 30px;\"></iframe></div>";
	}

	private function addAddThisButton($video)
	{
		return "<div class=\"jvideo-social-right span6\"><div class=\"addthis_toolbox addthis_default_style\">"
			  ."<a class=\"jvideo-social-addthis addthis_button_tweet\"></a>"
			  ."<a class=\"jvideo-social-addthis addthis_counter addthis_pill_style\"></a>"
			  ."</div></div>"
			  ."<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js\"></script>";
	}
}