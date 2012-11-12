<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 07/10/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// ============================================================================
//	BOOT
// ============================================================================
require('admin/boot/admin.bit');

// ============================================================================
//	CONTROLLER, VIEW and TEMPLATE
// ============================================================================

$controllers['dashboard']['view'] 		= array('security'=>true, 'title'=>$_LANG['DASHBOARD'], 'controller'=>'view', 'view'=>'view', 'template'=>'default');

$controllers['post']['new_simple'] 		= array('security'=>true, 'title'=>$_LANG['NEW_SIMPLE_POST'], 'controller'=>'new', 'view'=>'new_simple', 'template'=>'default');
$controllers['post']['new_video'] 		= array('security'=>true, 'title'=>$_LANG['NEW_VIDEO_POST'], 'controller'=>'new', 'view'=>'new_video', 'template'=>'default');
$controllers['post']['new_quote'] 		= array('security'=>true, 'title'=>$_LANG['NEW_QUOTE_POST'], 'controller'=>'new', 'view'=>'new_quote', 'template'=>'default');

$controllers['post']['edit_simple'] 	= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'edit', 'template'=>'default');
$controllers['post']['edit_video'] 		= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'edit', 'template'=>'default');
$controllers['post']['edit_quote'] 		= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'edit_quote', 'template'=>'default');

$controllers['post']['list'] 			= array('security'=>true, 'title'=>$_LANG['POSTS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');

$controllers['categories']['list']		= array('security'=>true, 'title'=>$_LANG['MANAGE_CATEGORIES'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');

$controllers['comments']['list']		= array('security'=>true, 'title'=>$_LANG['COMMENTS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');

$controllers['settings']['general']		= array('security'=>true, 'title'=>$_LANG['GENERAL_SETTINGS'], 'controller'=>'general', 'view'=>'general', 'template'=>'default');
$controllers['settings']['advanced']	= array('security'=>true, 'title'=>$_LANG['ADVANCED_SETTINGS'], 'controller'=>'advanced', 'view'=>'advanced', 'template'=>'default');
$controllers['settings']['regional']	= array('security'=>true, 'title'=>$_LANG['REGIONAL_SETTINGS'], 'controller'=>'regional', 'view'=>'regional', 'template'=>'default');
$controllers['settings']['image']		= array('security'=>true, 'title'=>$_LANG['IMAGE_SETTINGS'], 'controller'=>'image', 'view'=>'image', 'template'=>'default');
$controllers['settings']['themes']		= array('security'=>true, 'title'=>$_LANG['CHANGE_THEME'], 'controller'=>'themes', 'view'=>'themes', 'template'=>'default');
$controllers['settings']['username']	= array('security'=>true, 'title'=>$_LANG['USERNAME_AND_PASSWORD'], 'controller'=>'username', 'view'=>'username', 'template'=>'default');

$controllers['plugins']['list']			= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');
$controllers['plugins']['install']		= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'install', 'view'=>'config', 'template'=>'default');
$controllers['plugins']['uninstall']	= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'uninstall', 'view'=>'list', 'template'=>'default');
$controllers['plugins']['config']		= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'config', 'view'=>'config', 'template'=>'default');

$controllers['user']['logout']			= array('security'=>false, 'title'=>$_LANG['LOGOUT'], 'controller'=>'logout', 'view'=>'logout', 'template'=>'login');
$controllers['user']['login']			= array('security'=>false, 'title'=>$_LANG['SIGN_IN_TO_NIBBLEBLOG_ADMIN_AREA'], 'controller'=>'login', 'view'=>'login', 'template'=>'login');

if(isset($controllers[$url['controller']][$url['action']]))
{
	$dirname = $url['controller'].'/';
	$parameters = $controllers[$url['controller']][$url['action']];

	define('LAYOUT_TITLE',		$parameters['title']);
	define('LAYOUT_CONTROLLER',	$dirname.$parameters['controller'].'.bit');
	define('LAYOUT_VIEW',		$dirname.$parameters['view'].'.bit');
	define('LAYOUT_TEMPLATE',	$parameters['template'].'/index.bit');

	if($parameters['security'])
	{
		require('admin/kernel/security.bit');
	}
}
else
{
	// Default parameters
	define('LAYOUT_TITLE',		$_LANG['SIGN_IN_TO_NIBBLEBLOG_ADMIN_AREA']);
	define('LAYOUT_CONTROLLER',	'user/login.bit');
	define('LAYOUT_VIEW',		'user/login.bit');

	// Default template
	define('LAYOUT_TEMPLATE',	'login/index.bit');
}

require(PATH_ADMIN_CONTROLLER .	LAYOUT_CONTROLLER);
require(PATH_ADMIN_TEMPLATES  .	LAYOUT_TEMPLATE);

?>
