<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_CAT->delete($safe);
}
elseif( $_POST['action']=='set' )
{
	parse_str($_POST['serial_data'], $data);

	foreach( $data as $id=>$name )
	{
		$safe = array();
		$safe['id'] = $id;
		$safe['name'] = $_VALIDATION->sanitize_html($name);

		if($_TEXT->not_empty($safe['name']))
		{
			$_DB_CAT->set($safe);
		}
	}

	$error = !$_DB_CAT->savetofile();
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
