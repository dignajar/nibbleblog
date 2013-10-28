<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Theme {

	public static function folder_css()
	{
		echo HTML_THEME_CSS;
	}

	public static function url($relative = true)
	{
		if($relative)
			echo HTML_PATH_ROOT;
		else
			echo $settings['url'].HTML_PATH_ROOT;
	}

	public static function css($files, $path=HTML_THEME_CSS)
	{
		if(!is_array($files))
			$files = array($files);

		$tmp = '';
		foreach($files as $file)
			$tmp .= '<link rel="stylesheet" type="text/css" href="'.$path.$file.'" />'.PHP_EOL;

		echo $tmp;
	}

	public static function javascript($files, $path=HTML_THEME_JS)
	{
		if(!is_array($files))
			$files = array($files);

		$tmp = '';
		foreach($files as $file)
			$tmp .= '<script src="'.$path.$file.'"></script>'.PHP_EOL;

		echo $tmp;
	}

	public static function favicon()
	{
		echo '<link rel="shortcut icon" href="'.HTML_THEME_CSS.'img/favicon.ico" type="image/x-icon" />';
	}

	public static function blog_name()
	{
		global $settings;

		echo $settings['name'];
	}

	public static function blog_slogan()
	{
		global $settings;

		echo $settings['slogan'];
	}

	public static function blog_footer()
	{
		global $settings;

		echo $settings['footer'];
	}

	public static function meta_tags($name)
	{
		global $layout;
		global $seo;

		$meta = '<meta charset="utf-8">'.PHP_EOL;

		if(!empty($layout['title']))
			$meta .= '<title>'.$layout['title'].'</title>'.PHP_EOL;

		if(!empty($layout['description']))
			$meta .= '<meta name="description" content="'.$layout['description'].'">'.PHP_EOL;

		if(!empty($layout['generator']))
			$meta .= '<meta name="generator" content="'.$layout['generator'].'">'.PHP_EOL;

		if(!empty($layout['keywords']))
			$meta .= '<meta name="keywords" content="'.$layout['keywords'].'">'.PHP_EOL;

		if(!empty($layout['author']))
		{
			if(filter_var($layout['author'], FILTER_VALIDATE_URL))
				$meta .= '<link rel="author" href="'.$layout['author'].'" />'.PHP_EOL;
			else
				$meta .= '<meta name="author" content="'.$layout['author'].'">'.PHP_EOL;
		}

		if(!empty($layout['robots']))
			$meta .= '<meta name="robots" content="'.$layout['robots'].'">'.PHP_EOL;

		if(!empty($seo['google_code']))
			$meta .= '<meta name="google-site-verification" content="'.$seo['google_code'].'">'.PHP_EOL;

		if(!empty($seo['bing_code']))
			$meta .= '<meta name="msvalidate.01" content="'.$seo['bing_code'].'">'.PHP_EOL;

		$meta .= '<link rel="alternate" type="application/atom+xml" title="ATOM Feed" href="'.$layout['feed'].'" />'.PHP_EOL;

		echo $meta;
	}

}

?>