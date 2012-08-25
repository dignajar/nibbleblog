<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 21/08/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// ============================================================================
//	BOOT
// ============================================================================
	if( !file_exists('content/private') )
	{
		header('Location:install.php');
		exit('<a href="./install.php">click to install Nibbleblog</a>');
	}

	require('admin/boot/blog.bit');

// ============================================================================
//	THEME CONFIG
// ============================================================================
	@require(THEME_ROOT.'config.bit');

// ============================================================================
//	CONTROLLER & ACTION
// ============================================================================
	$url = $_URL;

	$layout = array('controller'=>'blog/view.bit', 'view'=>'blog/view.bit', 'template'=>'default.bit', 'title'=>'Blog powered by Nibbleblog');

	if( ($url['controller']!=null) && ($url['action']!=null) )
	{
		$layout['controller']	= $url['controller'].'/'.$url['action'].'.bit';
		$layout['view']			= $url['controller'].'/'.$url['action'].'.bit';
	}

	if(isset($theme['template'][$url['controller']]))
	{
		$layout['template'] = $theme['template'][$url['controller']];
	}

	// Load the controller and template
	@require(THEME_CONTROLLERS.$layout['controller']);
	@require(THEME_TEMPLATES.$layout['template']);

?>
