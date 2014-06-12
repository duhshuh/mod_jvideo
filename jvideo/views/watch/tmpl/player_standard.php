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

$doc = JFactory::getDocument();
$doc->addScript(JURI::root(true) . '/media/com_jvideo/site/js/AC_OETags.js');
?>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Revision of Flash required
var requiredRevision = 115;
// -----------------------------------------------------------------------------
// -->
</script>
<script language="JavaScript" type="text/javascript">
<!--
// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);


// Check to see if a player with Flash Product Install is available and the version does not meet the requirements for playback
if ( hasProductInstall && !hasRequestedVersion ) {
	// MMdoctitle is the stored document.title value used by the installation process to close the window that started the process
	// This is necessary in order to close browser windows that are still utilizing the older version of the player after installation has completed
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = window.location;
    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
    var MMdoctitle = document.title;

	AC_FL_RunContent(
		"src", "<?php echo JURI::root(true); ?>/media/com_jvideo/site/swf/playerProductInstall.swf",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "<?php echo $this->videoPlayerWidth; ?>",
		"height", "<?php echo $this->videoPlayerHeight; ?>",
		"align", "middle",
		"id", "InfinovisionPlayer",
		"quality", "high",
		"bgcolor", "#ffffff",
		"name", "InfinovisionPlayer",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "<?php echo $this->playerSwfUrl; ?>",
			"width", "<?php echo $this->videoPlayerWidth; ?>",
			"height", "<?php echo $this->videoPlayerHeight; ?>",
			"align", "middle",
			"id", "InfinovisionPlayer",
			"quality", "high",
			"name", "InfinovisionPlayer",
			"allowScriptAccess","always",
			"allowFullScreen","true",
			"wmode","transparent",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer",
			"flashvars", "<?php echo $this->flashvars; ?>"
	);
  } else {  // flash is too old or we can't detect the plugin
    var alternateContent = 'Sorry, but you need to upgrade your system to play our videos! '
  	+ 'This content requires the Adobe Flash Player. '
   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
    document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>
<noscript>
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="InfinovisionPlayer" width="400" height="300"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="<?php echo htmlspecialchars($this->playerSwfUrl); ?>" />
			<param name="quality" value="high" />
			<param name="allowFullScreen" value="true" />
			<param name="allowScriptAccess" value="always" />
			<param name="wmode" value="transparent" />
			<param name="flashvars" value="<?php echo $this->flashvars; ?>" />
			<embed src="<?php echo htmlspecialchars($this->playerSwfUrl); ?>"
				quality="high" 
				width="<?php echo $this->videoPlayerWidth; ?>" 
				height="<?php echo $this->videoPlayerHeight; ?>" 
				name="InfinovisionPlayer"  
				align="middle" 
				play="true" 
				loop="false" 
				wmode="transparent" 
				allowFullScreen="true" 
				allowScriptAccess="always" 
				type="application/x-shockwave-flash" 
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>
