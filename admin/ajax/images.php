<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

// Filename
$filename = false; 

if(isset($_SERVER['HTTP_FILE_NAME']))
{
	$filename = $_SERVER['HTTP_FILE_NAME'];
}
else
{
	if(function_exists('apache_request_headers'))
	{
		$headers = apache_request_headers();
		$filename = $headers['FILE_NAME'];
	}
}

if( $filename )
{
	// Ext
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	
	// Hash
	$hash = $_CRYPT->get_hash(time().$filename);

	// Stream
	$content = file_get_contents("php://input");

	if( $content == false )
	{
		exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error><i18n><![CDATA[fail 2]]></i18n>') );
	}

	if( file_put_contents(PATH_UPLOAD.$hash.'.'.$ext, $content) )
	{
		exit( $_TEXT->ajax_header('<success><![CDATA[1]]></success><file><![CDATA['.HTML_PATH_UPLOAD.$hash.'.'.$ext.']]></file>') );
	}
}

exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error><i18n><![CDATA[fail 3]]></i18n>') );

?>
