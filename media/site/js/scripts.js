var JVideoAJAX = (function($) {
	var that = {};
	var normalSyncComplete = false;

	that.normalSync = function() {
		if (normalSyncComplete) return;
		normalSyncComplete = true;
		
		$.get('index.php', {
			option: "com_jvideo",
			view: "jvideo",
			task: "normalSync",
			format: "raw"
		});
	};

	that.consoleSync = function() {
		$.get('index.php', {
			option: "com_jvideo",
			view: "jvideo",
			task: "consoleSync",
			format: "raw"
		});
	};

	that.rateVideo = function(videoId, userId, rating) {
		$('#ajaxRating').load('index.php', {
		   option: 'com_jvideo',
		   task: 'rate_video',
		   format: 'raw',
		   id: videoId,
		   user_id: userId,
		   rating: rating
		});
	};

	that.approveVideo = function(videoId) {
		$.get('index.php', {
			option: 'com_jvideo',
			task: 'approve',
			format: 'raw',
			id: videoId
		});
	};

	that.deleteVideo = function(videoId) {
		$.get('index.php', {
			option: 'com_jvideo',
			task: 'delete_video',
			format: 'raw',
			id: videoId
		});

		locate.reload();
	};

	that.featureVideo = function(videoId, addFeatured) {
		$('#markFeatured').load('index.php', {
			option: 'com_jvideo',
			task: addFeatured == 'true' ? 'addFeatured' : 'removeFeatured',
			format: 'raw',
			videoId: videoId
		});
	};

	that.publishVideo = function(videoId, publishVideo) {
		$('#publishVideo').load('index.php', {
			option: 'com_jvideo',
			task: publishVideo == 'true' ? 'publishVideo' : 'unpublishVideo',
			format: 'raw',
			id: videoId
		});
	};

	that.updateVideoCategories = function(videoId, categoryId, isAdding) {
		$('#categoryList').load('index.php', {
			option: 'com_jvideo',
			task: isAdding == 'true' ? 'addVideoToCategory' : 'removeVideoFromCategory',
			format: 'raw',
			videoId: parseInt(videoId),
			categoryId: parseInt(categoryId)
		});
	};

	that.getVideosByParams = function(videoParams, placeholderId) {
		displayLoadingImage(placeholderId);

		var paramUrl = '?option=com_jvideo&view=videos&format=ajax';
		
		for (var videoKey in videoParams) {
			paramUrl += '&' + videoKey + '=' + videoParams[videoKey];

			if (videoKey == 'player_placeholder_id')
				break;
		}

		$('#' + placeholderId).load('index.php' + paramUrl);
	};

	that.getVideoPlayer = function(id, playerHeight, playerWidth, placeholderId) {
		displayLoadingImage(placeholderId);

		$('#' + placeholderId).load('index.php', {
			option: 'com_jvideo',
			view: 'watch',
			format: 'ajax',
			id: id,
			playerHeight: playerHeight,
			playerWidth: playerWidth
		});
	};

	that.highlightStars = function(starMap, fullStar, halfStar, emptyStar) {
		// starmap: 0 = empty, 1 = half, 2 = full
		for (var i = 0; i < 5; i++) {
			var star = document.getElementById('videoStar' + (i + 1));
			star.className = starMap.charAt(i) == "2" ? fullStar : starMap.charAt(i) == "1" ? halfStar : emptyStar;
		}
	};

	function displayLoadingImage(placeholderId) {
		var contents = "";
		var contentHeight = document.getElementById(placeholderId).clientHeight;
		
		if (contentHeight < 16) {
			contentHeight = 16;
		}
		
		contents += '<div style="width: 100%; height: '+contentHeight+'px; vertical-align: middle; text-align: center; position: relative;">';
		contents += '   <img src="' + jsBaseUrl + '/media/com_jvideo/site/images/wait.gif" border="0" height="11" width="43" alt="Loading..." style="position: absolute; top: '+(contentHeight / 2)+'px;" />';
		contents += '</div>';
		
		document.getElementById(placeholderId).innerHTML = contents; 
	}

	return that;
}(jQuery));
