<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_COMMENTS->delete($safe);
}
elseif( $_POST['action']=='set' )
{
	$data = Text::unserialize($_POST['serial_data']);

	foreach( $data as $name=>$value )
	{
		$safe[$name] = Validation::sanitize_html($value);
	}

	$_DB_COMMENTS->set_settings($safe);

	$error = !$_DB_COMMENTS->savetofile();
}
elseif( $_POST['action']=='approve' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_COMMENTS->approve($safe);
}
elseif( $_POST['action']=='unapprove' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_COMMENTS->unapprove($safe);
}

if( $error )
{
	exit( Text::ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( Text::ajax_header('<success><![CDATA[1]]></success>') );
}
?>