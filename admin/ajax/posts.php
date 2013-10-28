<?php header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	// Delete all comments from a post
	$_DB_COMMENTS->delete_all_by_post( array('id_post'=>$safe['id']) );

	// Delete links to tags
	$_DB_TAGS->delete_links(array('id_post'=>$safe['id']));
	$_DB_TAGS->savetofile();

	// Delete the post
	$error = !$_DB_POST->delete($safe);
}

if($error)
	exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA['.$_LANG['FAIL'].']]></alert>') );
else
	exit( Text::ajax_header('<success><![CDATA[1]]></success><alert><![CDATA['.$_LANG['CHANGES_HAS_BEEN_SAVED_SUCCESSFULLY'].']]></alert>') );

?>