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
	'title'=>$seo['site_title'],
	'description'=>$seo['site_description'],
	'author'=>$seo['author'],
	'robots'=>$seo['robots'],
	'keywords'=>$seo['keywords'],
	'generator'=>$seo['generator'],
	'feed'=>HTML_PATH_ROOT.'feed.php'
);

if( ($url['controller']!=null) && ($url['action']!=null) )
{
	$layout['controller']	= $url['controller'].'/'.$url['action'].'.bit';
	$layout['view']			= $url['controller'].'/'.$url['action'].'.bit';

	// Particular post
	if( (($url['id_post']!==null) || ($url['post']!==null)) && !empty($post) )
	{
		$layout['title'] 		.= ' - '.$post['title'];
		$layout['description']	= $post['description'];
		$layout['keywords']		= implode(',',$post['tags']);

		$where_am_i[1] = 'post';
	}
	elseif( (($url['id_page']!==null) || ($url['page']!==null)) && !empty($page) )
	{
		$layout['title'] 		.= ' - '.$page['title'];
		$layout['description']	= $page['description'];
		$layout['keywords']		= $page['keywords'];

		$where_am_i[1] = 'page';
	}
	elseif( ($url['category']!==null) && !empty($category) )
	{
		$layout['title'] .= ' - '.$category['name'];

		$where_am_i[1] = 'category';
	}
	elseif( ($url['tag']!==null) && !empty($tag) )
	{
		$layout['title'] .= ' - '.$url['tag'];

		$where_am_i[1] = 'tag';
	}

	// Page 404
	if( !file_exists(THEME_CONTROLLERS.$layout['controller']) || !file_exists(THEME_VIEWS.$layout['view']) || $page_not_found )
	{
		$layout['controller']	= 'page/404.bit';
		$layout['view']			= 'page/404.bit';
		$layout['title'] 		.= ' - Error 404';

		$where_am_i[1] = '404';
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

// Plugins
foreach($plugins as $plugin)
	$plugin->boot();

// Load the controller and template
require(THEME_CONTROLLERS.$layout['controller']);
require(THEME_TEMPLATES.$layout['template']);

?>