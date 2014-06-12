var jvideo;

(function(window, document, $) {
	jvideo = jvideo || {};
	jvideo.install = jvideo.install || {};

	var timer = null;

	jvideo.install.initCountdownTimer = function(duration) {
		$(document).ready(function() {
			countDown(duration);
		});
	};

	jvideo.install.cancelCountdownTimer = function() {
		window.clearTimeout(timer);
	};

	var countDown = function(timeRemaining) {
		if (timeRemaining >= 0) {
			$("#timer").text(timeRemaining);
			timer = window.setTimeout(function() { countDown(timeRemaining - 1) }, 1000);
		}
		else {
			document.adminForm.submit();
		}
	};
}(window, document, jvideo.jQuery));