<?php header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('security.bit');

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

	$filename = strtolower(pathinfo($filename, PATHINFO_FILENAME));
	$filename = Text::replace(' ', '', $filename);
	$filename = Text::replace('_', '', $filename);
	//$filename = Text::cut_text($filename, 20);
	$number = 0;

	while(file_exists(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext))
		$number++;

	if( file_put_contents(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext, $content) )
	{
		// If the gif then don't resize
		if($ext!='gif')
		{
			// Resize and/or Crop
			if($settings['img_resize'])
			{
				$Resize->setImage(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext, $settings['img_resize_width'], $settings['img_resize_height'], $settings['img_resize_option']);
				$Resize->saveImage(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext, $settings['img_resize_quality']);
			}

			// Generate thumbnail
			if($settings['img_thumbnail'])
			{
				$Resize->setImage(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext, $settings['img_thumbnail_width'], $settings['img_thumbnail_height'], $settings['img_thumbnail_option']);
				$Resize->saveImage(PATH_UPLOAD.$filename.'_'.$number.'_thumb.'.$ext, $settings['img_thumbnail_quality']);
			}
		}

		// Generate thumbnail for Nibbleblog media
		$Resize->setImage(PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext, '110', '110', 'crop');
		$Resize->saveImage(PATH_UPLOAD.$filename.'_'.$number.'_nbmedia.jpg', 98, true);

		exit( Text::ajax_header('<success><![CDATA[1]]></success><original><![CDATA['.HTML_PATH_UPLOAD.$filename.'_'.$number.'_o.'.$ext.']]></original><nbmedia><![CDATA['.HTML_PATH_UPLOAD.$filename.'_'.$number.'_nbmedia.jpg]]></nbmedia>') );
	}
}

exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA[fail 4]]></alert>') );

?>