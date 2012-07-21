<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

$error = $_VIDEO->video_get_info($_POST['url']);

if( $error == false )
{
	exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( $_TEXT->ajax_header('<success><![CDATA[1]]></success><title><![CDATA['.$error['title'].']]></title>') );
}
?>
