var JVideo = JVideo || {};
JVideo.imageCrossFade = (function($, document) {
	var MODULE = {};


	MODULE.setup = function(crossfadeArray) {
		for (var i = 0; i < crossfadeArray.length; i++) {
			new ImageCrossfade(crossfadeArray[i][0], crossfadeArray[i][1]);
		}
	};


	function ImageCrossfade(imageId, images) {
		this.image = document.getElementById(imageId);
		this.imageId = imageId;
		this.imageSource = this.image.src;
		this.imageWidth = parseInt(this.image.getAttribute('width'), 10);
		this.imageHeight = parseInt(this.image.getAttribute('height'), 10);
		this.images = images;
		this.currentImageIndex = 0;
		this.fadeSteps = 36;
		this.fadeDelay = 46;
		this.currentFadeStep = this.fadeSteps;
		this.fadeTimer = null;
		this.mouseOutTimer = null;
			
		var thisRef = this;
		$(document).ready(function() { thisRef.init(); });
	}

	ImageCrossfade.prototype.init = function() {
		var thisRef = this;
		
		this.fadeContainer = document.createElement('span');
		this.fadeContainer.className = 'jvideo-thumb-container';
		
		this.fadeImage = document.createElement('img');
		this.fadeImage.src = this.image.src;
		this.fadeImage.style.cssText = this.image.style.cssText;
		this.fadeImage.className = this.image.className;
		this.fadeImage.style.position = 'absolute';
		this.fadeImage.style.display = 'none';
		this.fadeImage.width = this.imageWidth;
		this.fadeImage.height = this.imageHeight;
		this.fadeImage.border = '0';
		this.fadeImageLink = this.linkImage(this.fadeImage);
		
		this.playImage = this.createTransparentPNG(ImageCrossfade.playImage.src, ImageCrossfade.playImageWidth, ImageCrossfade.playImageHeight);
		this.playImage.id = this.imageId + '_play';
		this.playImage.className = 'jvideo-play-overlay';
		this.playImage.style.display = 'none';
		this.playImage.style.zIndex = '100';
		this.playImageLink = this.linkImage(this.playImage);
		
		this.image.parentNode.insertBefore(this.fadeContainer, this.image);
		this.fadeContainer.appendChild(this.image);
		this.fadeContainer.insertBefore(this.playImageLink, this.image);
		this.fadeContainer.insertBefore(this.fadeImageLink, this.image);
		
		this.fadeContainer.onmouseover = function() { thisRef.startFade(); };
		this.fadeContainer.onmouseout = function() { thisRef.stopFade(false); };
	}

	ImageCrossfade.prototype.isActive = function() {
		return this.fadeImage.style.display == 'block';
	}

	ImageCrossfade.prototype.linkImage = function(image) {
		var link = this.getParentLink();
		var element = image;
		if (link) {
			element = document.createElement('a');
			element.href = link;
			element.appendChild(image);
		}
		return element;
	}

	ImageCrossfade.prototype.getParentLink = function() {
		var parent = this.image;
		var link = null;
		while (parent = parent.parentNode) {
			if (parent.tagName && parent.tagName.toLowerCase() == 'a') {
				link = parent.href;
				break;
			}
		}
		return link;
	}

	ImageCrossfade.prototype.startFade = function() {
		clearTimeout(this.mouseOutTimer);
		if (this.isActive()) return;
		this.fadeImage.style.display = 'block';
		this.playImage.style.display = 'inline-block';
		this.fade();
	}

	ImageCrossfade.prototype.stopFade = function(delay) {
		if (!delay) {
			var thisRef = this;
			this.mouseOutTimer = setTimeout(function() { thisRef.stopFade(true); }, 100);
			return;
		}
		clearTimeout(this.fadeTimer);
		this.image.src = this.imageSource;
		this.playImage.style.display = 'none';
		this.fadeImage.style.display = 'none';
		this.fadeImage.src = this.image.src;
		this.currentImageIndex = 0;
		this.currentFadeStep = this.fadeSteps;
	}

	ImageCrossfade.prototype.fade = function() {
		if (this.currentFadeStep < this.fadeSteps) {
			this.setOpacity(this.fadeImage, Math.pow(++this.currentFadeStep / this.fadeSteps, 3));
		}
		else {
			this.image.src = this.fadeImage.src;
			
			this.currentImageIndex++;
			if (this.currentImageIndex >= this.images.length) this.currentImageIndex = 0;
			this.fadeImage.src = this.images[this.currentImageIndex];
			this.currentFadeStep = 0;
			this.setOpacity(this.fadeImage, 0);
		}
		
		var thisRef = this;
		if (this.isActive()) this.fadeTimer = setTimeout(function() { thisRef.fade(); }, this.fadeDelay);
	}

	ImageCrossfade.prototype.setOpacity = function(obj, percentage) {
		obj.style.opacity = percentage;
		obj.style.filter = 'alpha(opacity=' + Math.round(100 * percentage) + ')';
	}

	ImageCrossfade.prototype.createTransparentPNG = function(imageSrc, width, height) {
		var version = navigator.appVersion.split("MSIE");
		version = parseFloat(version[1]);
		if (version >= 5.5 && version < 7 && document.body.filters)  {
			var span = document.createElement('span');
			span.style.width = width + 'px';
			span.style.height = height + 'px';
			span.style.display = 'inline-block';
			span.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + imageSrc + '\', sizingMethod=\'scale\')';
			return span;
		}
		else {
			var image = document.createElement('img');
			image.src = imageSrc;
			image.width = width;
			image.height = height;
			image.border = 0;
			return image;
		}
	}

	var baseUrl = null;

	if (JVideo.jsBaseUrl) {
	    baseUrl = JVideo.jsBaseUrl;
	} else {
	    baseUrl = '';
	}

	ImageCrossfade.playImage = new Image();
	ImageCrossfade.playImage.src = baseUrl + "/media/com_jvideo/site/images/blank.gif";
	ImageCrossfade.playImageWidth = 120;
	ImageCrossfade.playImageHeight = 90;


	return MODULE;
}(jQuery, document));
