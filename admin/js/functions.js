
function empty(text)
{
	return($.trim(text).length < 1);
}

function not_empty(text)
{
	return( !empty(text) );
}

function serial_form(form)
{
	var notchecked = "";
	form.find('input[type=checkbox]:not(:checked)').each(function() { notchecked += $(this).attr("name") + "=0&" });

	return( notchecked + form.serialize() );
}

function validate_email(email)
{
	var emailReg = /\S+@\S+\.\S+/;

	if(!emailReg.test(email)) {
		return false;
	}

	return true;
}

function set_ajax(id, type, ajax)
{
	var result = false;

	$.ajax({
			url: HTML_PATH_ADMIN_AJAX + ajax, type: 'POST', cache: false, timeout: 15000, dataType: "xml", async: false,
			data: { action: type, id: id },
			success: function(xml)
			{
				result = true;
			}
	});

	return(result);
}

function show_alert(text, hidden_time)
{
	$("#alert").html(text).fadeIn(1000);
	setTimeout(function(){$("#alert").fadeOut(1000);}, hidden_time);
}
