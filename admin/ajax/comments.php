<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_COMMENTS->delete($safe);
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
