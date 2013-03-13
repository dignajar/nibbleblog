<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// =====================================================================
//	BOOT
// =====================================================================
	if( !file_exists('content/private') )
	{
		header('Location:install.php');
		exit('<a href="./install.php">click to install Nibbleblog</a>');
	}

	require('admin/boot/blog.bit');

// =====================================================================
//	THEME CONFIG
// =====================================================================
	require(THEME_ROOT.'config.bit');

// =====================================================================
//	CONTROLLER & ACTION
// =====================================================================
	$layout = array(
		'controller'=>'blog/view.bit',
		'view'=>'blog/view.bit',
		'template'=>'default.bit',
		'title'=>$settings['name'].' - '.$settings['slogan'],
		'description'=>$settings['about'],
		'author'=>'',
		'keywords'=>'',
		'generator'=>'Nibbleblog',
		'feed'=>HTML_PATH_ROOT.'feed.php'
	);

	if( ($url['controller']!=null) && ($url['action']!=null) )
	{
		$layout['controller']	= $url['controller'].'/'.$url['action'].'.bit';
		$layout['view']			= $url['controller'].'/'.$url['action'].'.bit';

		// 404 ?
		if( !file_exists(THEME_CONTROLLERS.$layout['controller']) || !file_exists(THEME_VIEWS.$layout['view']) || $page_not_found )
		{
			$layout['controller']	= 'page/404.bit';
			$layout['view']			= 'page/404.bit';
		}
	}

	if(isset($theme['template'][$url['controller']]))
	{
		$layout['template'] = $theme['template'][$url['controller']];
	}

	if($settings['friendly_urls'])
	{
		$layout['feed'] = HTML_PATH_ROOT.'feed/';
	}

	// Load the controller and template
	@require(THEME_CONTROLLERS.$layout['controller']);
	@require(THEME_TEMPLATES.$layout['template']);

?>