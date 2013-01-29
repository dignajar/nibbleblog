
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
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	if(!emailReg.test(email)) {
		return false;
	}

	return true;
}