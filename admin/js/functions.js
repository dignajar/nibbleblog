
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
	form.children("input:checkbox:not(:checked)").each(function() { notchecked += $(this).attr("name") + "=0&" });

	return( notchecked + form.serialize() );
}
