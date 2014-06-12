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
<div class="jvideo-wrapper<?php echo $this->params->get('pageclass_sfx'); ?>">
    <h1><?php echo JText::_("JV_UPLOAD_HEADER"); ?></h1>
	<p><?php echo JText::_("JV_UPLOAD_HEADER_DESC"); ?></p>
	<script type="text/javascript" language="javascript">
	<!--
	var requiredMajorVersion = 9;
	var requiredMinorVersion = 0;
	var requiredRevision = 115;
	var hasProductInstall = DetectFlashVer(6, 0, 65);
	var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

	if ( hasProductInstall && !hasRequestedVersion ) {
		var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
		var MMredirectURL = window.location;
	    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
	    var MMdoctitle = document.title;
	
		AC_FL_RunContent(
			"src", "<?php echo JURI::root(true); ?>/media/com_jvideo/site/swf/playerProductInstall.swf",
			"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
			"width", "420",
			"height", "390",
			"align", "middle",
			"id", "InfinovationVideoUploader",
			"quality", "high",
			"bgcolor", "#ffffff",
			"name", "InfinovationVideoUploader",
			"allowScriptAccess","always",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer"
		);
	} else if (hasRequestedVersion) {
		AC_FL_RunContent(
				"src", "//infinovision.s3.amazonaws.com/VideoUploaderRecorder.swf",
				"width", "420",
				"height", "390",
				"align", "middle",
				"id", "VideoUploaderRecorder",
				"quality", "high",
				"name", "VideoUploaderRecorder",
				"allowScriptAccess","always",
				"type", "application/x-shockwave-flash",
				"pluginspage", "http://www.adobe.com/go/getflashplayer",
				"wmode", "transparent",
				"flashvars", "<?php echo $this->flashvars; ?>"
		);
	  } else {  // flash is too old or we can't detect the plugin
	    var alternateContent = 'This content requires the Adobe Flash Player. '
	   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
	    document.write(alternateContent);  // insert non-flash content
	  }
	// -->
	</script>
	<noscript>
	  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
				id="VideoUploaderRecorder" name="VideoUploaderRecorder" width="420" height="390"
				codebase="//fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
				<param name="movie" value="http://infinovision.s3.amazonaws.com/VideoUploaderRecorder.swf" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="allowScriptAccess" value="always" />
				<param name="wmode" value="transparent" />
				<param name="flashvars" value="<?php echo $this->flashvars; ?>" />
				<embed src="http://infinovision.s3.amazonaws.com/VideoUploaderRecorder.swf" 
					width="420" 
					height="390"
                    id="VideoUploaderRecorder"
					name="VideoUploaderRecorder"
					align="middle"
					play="true"
					loop="false"
					quality="high"
					wmode="transparent" 
					allowScriptAccess="always"
					type="application/x-shockwave-flash"
					pluginspage="http://www.adobe.com/go/getflashplayer">
				</embed>
		</object>
	</noscript>
</div>