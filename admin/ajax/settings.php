<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='set' )
{
	parse_str($_POST['serial_data'], $data);

	foreach( $data as $name=>$value )
	{
		$safe[$name] = $_VALIDATION->sanitize_html($value);
	}

	$_DB_SETTINGS->set($safe);

	$error = !$_DB_SETTINGS->savetofile();
}

if( $error )
{
	exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( $_TEXT->ajax_header('<success><![CDATA[1]]></success>') );
}
?>
