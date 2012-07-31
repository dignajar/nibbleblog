<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

$content = file_get_contents("php://input");

if( $content == false )
	exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error><i18n><![CDATA[fail 1]]></i18n>') );

$filename = $_SERVER['HTTP_FILE_NAME'];
$ext = end(explode('.', $filename));

if( file_put_contents(PATH_UPLOAD.$filename, $content) )
{
	$hash = $_CRYPT->get_hash(time().$filename);

	rename(PATH_UPLOAD.$filename, PATH_UPLOAD.$hash.'.'.$ext);

	exit( $_TEXT->ajax_header('<success><![CDATA[1]]></success><file><![CDATA['.HTML_PATH_UPLOAD.$hash.'.'.$ext.']]></file>') );
}

exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error><i18n><![CDATA[fail 2]]></i18n>') );

?>
