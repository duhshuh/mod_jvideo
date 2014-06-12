var JVideo = JVideo || {};
JVideo.userProfile = JVideo.userProfile || {};

JVideo.userProfile.edit = (function($, document) {
	var MODULE = {};

	MODULE.init = function() {
		$(document).ready(function() {
			$('.jvideo-remove-profile-image-button').click(function(e) {
				e.preventDefault();

				var confirmMessage = $(this).attr('data-confirm-message');
				if (confirm(confirmMessage)) {
					$('.jvideo-profile-image-container').remove();
					$('input[name="jvideo_remove_image"]').val('on');
				}
			});
		});
	};

	return MODULE;
}(jQuery, document));


/**
 * Everything below should be refactored into the JVideo namespace
 */

function embedSetAutoPlay(objId, val)
{
    obj = document.getElementById(objId);

    if (false == val) {
        obj.value = obj.value.replace(/AutoPlay=1/g, 'AutoPlay=0');
    } else {
        obj.value = obj.value.replace(/AutoPlay=0/g, 'AutoPlay=1');
    }

    obj.select();
}

function embedSetFullScreen(objId, val)
{
    obj = document.getElementById(objId);

    if (false == val) {
        obj.value = obj.value.replace(/allowFullScreen\"\ value=\"1/g, 'allowFullScreen" value="0');
        obj.value = obj.value.replace(/allowFullScreen=1/g, 'allowFullScreen=0');
    } else {
        obj.value = obj.value.replace(/allowFullScreen\"\ value=\"0/g, 'allowFullScreen" value="1');
        obj.value = obj.value.replace(/allowFullScreen=0/g, 'allowFullScreen=1');
    }

    obj.select();
}

function embedSetHeightWidth(objId, height, width)
{
    obj = document.getElementById(objId);
    obj.value = obj.value.replace(/height=\"[0-9]*\"/g, 'height="' + height + '"');
    obj.value = obj.value.replace(/width=\"[0-9]*\"/g, 'width="' + width + '"');
    obj.select();
}

function update_player_height(method)
{
	var theForm = document.getElementById('adminForm');
	var radioObj = theForm.aspect_constraint;
	var radioLength = radioObj.length;
	
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			var aspect_constraint = radioObj[i].value;
		}
	}
	
	/* Determine our multiplier based on our aspect ratio */
		if (method == 'width') {
			var multiplier = 0.75;
			
			if (aspect_constraint == '16x9') {
				multiplier = 0.5625;
			}
			
			var width = theForm.video_player_width.value;
			width = width.replace(/[^0-9]/g, '');
			theForm.video_player_width.value = width;
			
			var height = Math.round(width *  multiplier);
			
			theForm.video_player_height.value = height;
		} 
		if (method == 'height') {
			var multiplier = 1.3;
			
			if (aspect_constraint == '16x9') {
				multiplier = 1.8;
			}
			
			
			var height = theForm.video_player_height.value;
			height = height.replace(/[^0-9]/g, '');
			theForm.video_player_height.value = height;
			
			var width = Math.round(height *  multiplier);
			
			theForm.video_player_width.value = width;
		}
}
function edit_category()
{
	var theForm = document.getElementById('adminForm');
	theForm.task.value = 'edit_category';
}

function delete_category()
{
	if (confirm("Are you sure you want to delete this category?")) {
		/* Do something */
		var theForm = document.getElementById('adminForm');
		theForm.task.value = 'do_delete_category';
		theForm.submit();
	}
}

function jvideo_verify_admin_client_form()
{
	var theForm = document.getElementById('adminForm');
	
	var isValid = true;
	var message = "";
	
	if ( (theForm.pass.value != theForm.passConfirm.value) || theForm.pass.value == "")
	{
		message += 'Your passwords do not match.\n';
		isValid = false;
	}
	
	if (theForm.user_name.value == "")
	{
		message += 'You must enter a user name.\n';
		isValid = false;
	}
	
	if (theForm.infindomain.value == "")
	{
		message += 'You must enter a domain.\n';
		isValid = false;
	}
	
	if (message != "")
	{
		alert(message);
	}
	
	return isValid;
}

function featured_reorder_item(id, rank, updown)
{
	var theForm = document.getElementById('adminForm');
	
	theForm.featured_id.value = id;
	theForm.featured_order.value = rank;
	theForm.order_method.value = updown;
	theForm.task.value = 'do_reorder_featured_video';
	theForm.submit();
}

function featured_remove_item(id)
{
	var theForm = document.getElementById('adminForm');
	
	if (confirm('Are you sure you want to remove this featured video?')) {
		theForm.featured_id.value = id;
		theForm.task.value = 'do_remove_featured_video';
		theForm.submit();		
	}
}

function set_cat_crumb()
{
    var x=document.getElementById("parent_categories")
    alert(x.options[x.selectedIndex].text);
}

function attach_video_to_category_from_add()
{
	var theForm = document.getElementById('jvideoForm');
	theForm.task.value = 'attach_video_to_category_from_add';
	theForm.submit();
}

function detach_video_to_category_from_add()
{
	var theForm = document.getElementById('jvideoForm');
	theForm.task.value = 'detach_video_from_category_from_add';
	theForm.submit();
}
function attach_video_to_category_from_edit()
{
	var theForm = document.getElementById('jvideoForm');
	theForm.task.value = 'attach_video_to_category_from_edit';
	theForm.submit();
}

function detach_video_to_category_from_edit()
{
	var theForm = document.getElementById('jvideoForm');
	theForm.task.value = 'detach_video_from_category_from_edit';
	theForm.submit();
}