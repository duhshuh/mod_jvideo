function getVideoPlayer(id, playerHeight, playerWidth, placeholderId, baseUrl) {
	displayLoadingImage(placeholderId, baseUrl);

    jvideo.jQuery('#' + placeholderId).load('index.php', {
        option: "com_jvideo",
        view: "watch",
        format: "raw",
        id: id,
        playerHeight: playerHeight,
        playerWidth: playerWidth
    });
}

function displayLoadingImage(placeholderId, baseUrl) {
	var contents = "";
	var contentHeight = document.getElementById(placeholderId).clientHeight;

	if (contentHeight < 16) {
		contentHeight = 16;
	}

	contents += '<div style="width: 100%; height: '+contentHeight+'px; vertical-align: middle; text-align: center; position: relative;">';
	contents += '	<img src="' + baseUrl + '/media/com_jvideo/site/images/wait.gif" border="0" height="11" width="43" alt="Loading..." style="position: absolute; top: '+(contentHeight / 2)+'px;" />';
	contents += '</div>';

	document.getElementById(placeholderId).innerHTML = contents;
}