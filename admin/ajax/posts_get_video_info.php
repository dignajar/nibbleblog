<?php header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('security.bit');

$error = Video::video_get_info($_POST['url']);

if( $error == false )
{
	exit( Text::ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( Text::ajax_header('<success><![CDATA[1]]></success><title><![CDATA['.$error['title'].']]></title>') );
}
?>