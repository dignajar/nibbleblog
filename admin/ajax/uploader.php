<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

// Filename
$filename = false;

if(isset($_SERVER['HTTP_X_FILE_NAME']))
{
	$filename = $_SERVER['HTTP_X_FILE_NAME'];
}
else
{
	if(function_exists('apache_request_headers'))
	{
		$headers = apache_request_headers();

		if(isset($headers['X-FILE-NAME']))
		{
			$filename = $headers['X-FILE-NAME'];
		}
	}
}

if( $filename )
{
	// Ext
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

	if( ($ext!='jpg') && ($ext!='jpeg') && ($ext!='gif') && ($ext!='png') )
	{
		exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA[fail 2]]></alert>') );
	}

	// Stream
	$content = file_get_contents("php://input");

	if( $content == false )
	{
		exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA[fail 3]]></alert>') );
	}

	// Hash
	$hash = Crypt::get_hash(time().$filename);

	if( file_put_contents(PATH_UPLOAD.$hash.'_o.'.$ext, $content) )
	{
		// Resize and/or Crop
		if($settings['img_resize'])
		{
			$Resize->setImage(PATH_UPLOAD.$hash.'_o.'.$ext, $settings['img_resize_width'], $settings['img_resize_height'], $settings['img_resize_option']);
			$Resize->saveImage(PATH_UPLOAD.$hash.'_o.'.$ext, 100);
		}

		// Generate Thumbnail
		if($settings['img_thumbnail'])
		{
			$Resize->setImage(PATH_UPLOAD.$hash.'_o.'.$ext, $settings['img_thumbnail_width'], $settings['img_thumbnail_height'], $settings['img_thumbnail_option']);
			$Resize->saveImage(PATH_UPLOAD.$hash.'_thumb.'.$ext, 100);
		}

		exit( Text::ajax_header('<success><![CDATA[1]]></success><file><![CDATA['.HTML_PATH_UPLOAD.$hash.'_o.'.$ext.']]></file>') );
	}
}

exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA[fail 4]]></alert>') );

?>