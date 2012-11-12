<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	// Delete all comments from a post
	$_DB_COMMENTS->delete_all_by_post( array('id_post'=>$safe['id']) );

	// Delete the post
	$error = !$_DB_POST->remove($safe);
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
