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
	
	if (aspect_constraint == 0) {
		return;
	}
	
	/* Determine our multiplier based on our aspect ratio */
	if (method == 'width') {
		var width = theForm.video_player_width.value;
		width = width.replace(/[^0-9]/g, '');
		theForm.video_player_width.value = width;

		if (aspect_constraint == '1') {
			var height = Math.round(width / 16 * 9);
			height = height - (height % 16); //ffmpeg optimization
			theForm.video_player_height.value = height;
		} else {
			var height = Math.round(width / 4 * 3);
			theForm.video_player_height.value = height;
		}		
	} else if (method == 'height') {
		var height = theForm.video_player_height.value;
		height = height.replace(/[^0-9]/g, '');
		theForm.video_player_height.value = height;
			
		if (aspect_constraint == '1') {
			var width = Math.round(height / 9 * 16);
			width = width - (width % 9); //ffmpeg optimization
			theForm.video_player_width.value = width;
		} else {
			var width = Math.round(height / 3 * 4);
			theForm.video_player_width.value = width;
		}		
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
    var x=document.getElementById("parent_categories");

    if (x.options[x.selectedIndex].value != 0) {
    	document.getElementById("catcrumb").value = x.options[x.selectedIndex].text;
   	}

    return true;
}

function orderItemsBy(column)
{
    var orderByValue = document.getElementById('orderBy').value;
    var prevColumn = removeDesc(orderByValue);

    if (column != prevColumn) {
        if (!isSpecialColumn(column)) {
            column = addDesc(column);
        }
    } else {
        if (isSpecialColumn(column)) {
            column = addDesc(column);
        } else if (column == orderByValue) {
            column = addDesc(column)
        }
    }

    document.getElementById('orderBy').value = column;
    document.forms[0].submit();
}

function addDesc(column)
{
    return column + ' DESC';
}

function removeDesc(column)
{
    return column.replace(' DESC', '');
}

function isSpecialColumn(column)
{
    switch (column)
    {
        case 'video_title':
        case 'admin_approved':
            return true;
        default:
            return false;
    }
}