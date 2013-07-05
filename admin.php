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
require('admin/boot/admin.bit');

// =====================================================================
//	CONTROLLER, VIEW and TEMPLATE
// =====================================================================
$controllers['dashboard']['view'] 		= array('security'=>true, 'title'=>$_LANG['DASHBOARD'], 'controller'=>'view', 'view'=>'view', 'template'=>'default');

$controllers['page']['new']		 		= array('security'=>true, 'title'=>$_LANG['NEW_PAGE'], 'controller'=>'new', 'view'=>'new', 'template'=>'default');
$controllers['page']['edit']	 		= array('security'=>true, 'title'=>$_LANG['NEW_PAGE'], 'controller'=>'edit', 'view'=>'new', 'template'=>'default');
$controllers['page']['list']	 		= array('security'=>true, 'title'=>$_LANG['NEW_PAGE'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');

$controllers['post']['new_simple'] 		= array('security'=>true, 'title'=>$_LANG['NEW_SIMPLE_POST'], 'controller'=>'new', 'view'=>'new_simple', 'template'=>'default');
$controllers['post']['new_video'] 		= array('security'=>true, 'title'=>$_LANG['NEW_VIDEO_POST'], 'controller'=>'new', 'view'=>'new_video', 'template'=>'default');
$controllers['post']['new_quote'] 		= array('security'=>true, 'title'=>$_LANG['NEW_QUOTE_POST'], 'controller'=>'new', 'view'=>'new_quote', 'template'=>'default');

$controllers['post']['edit_simple'] 	= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'edit', 'template'=>'default');
$controllers['post']['edit_video'] 		= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'edit', 'template'=>'default');
$controllers['post']['edit_quote'] 		= array('security'=>true, 'title'=>$_LANG['EDIT_POST'], 'controller'=>'edit', 'view'=>'new_quote', 'template'=>'default');

$controllers['post']['list'] 			= array('security'=>true, 'title'=>$_LANG['POSTS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');

$controllers['categories']['list']		= array('security'=>true, 'title'=>$_LANG['MANAGE_CATEGORIES'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');
$controllers['categories']['edit']		= array('security'=>true, 'title'=>$_LANG['MANAGE_CATEGORIES'], 'controller'=>'edit', 'view'=>'edit', 'template'=>'default');

$controllers['comments']['list']		= array('security'=>true, 'title'=>$_LANG['COMMENTS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');
$controllers['comments']['settings']	= array('security'=>true, 'title'=>$_LANG['COMMENT_SETTINGS'], 'controller'=>'settings', 'view'=>'settings', 'template'=>'default');

$controllers['settings']['general']		= array('security'=>true, 'title'=>$_LANG['GENERAL_SETTINGS'], 'controller'=>'general', 'view'=>'general', 'template'=>'default');
$controllers['settings']['advanced']	= array('security'=>true, 'title'=>$_LANG['ADVANCED_SETTINGS'], 'controller'=>'advanced', 'view'=>'advanced', 'template'=>'default');
$controllers['settings']['regional']	= array('security'=>true, 'title'=>$_LANG['REGIONAL_SETTINGS'], 'controller'=>'regional', 'view'=>'regional', 'template'=>'default');
$controllers['settings']['image']		= array('security'=>true, 'title'=>$_LANG['IMAGE_SETTINGS'], 'controller'=>'image', 'view'=>'image', 'template'=>'default');
$controllers['settings']['themes']		= array('security'=>true, 'title'=>$_LANG['CHANGE_THEME'], 'controller'=>'themes', 'view'=>'themes', 'template'=>'default');
$controllers['settings']['username']	= array('security'=>true, 'title'=>$_LANG['USERNAME_AND_PASSWORD'], 'controller'=>'username', 'view'=>'username', 'template'=>'default');
$controllers['settings']['notifications']= array('security'=>true, 'title'=>$_LANG['NOTIFICATIONS'], 'controller'=>'notifications', 'view'=>'notifications', 'template'=>'default');
$controllers['settings']['seo'] 		 = array('security'=>true, 'title'=>$_LANG['SEO_OPTIONS'], 'controller'=>'seo', 'view'=>'seo', 'template'=>'default');

$controllers['plugins']['list']			= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'list', 'view'=>'list', 'template'=>'default');
$controllers['plugins']['install']		= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'install', 'view'=>'config', 'template'=>'default');
$controllers['plugins']['uninstall']	= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'uninstall', 'view'=>'list', 'template'=>'default');
$controllers['plugins']['config']		= array('security'=>true, 'title'=>$_LANG['PLUGINS'], 'controller'=>'config', 'view'=>'config', 'template'=>'default');

$controllers['user']['logout']			= array('security'=>true, 'title'=>$_LANG['LOGOUT'], 'controller'=>'logout', 'view'=>'logout', 'template'=>'login');
$controllers['user']['login']			= array('security'=>false, 'title'=>$_LANG['SIGN_IN_TO_NIBBLEBLOG_ADMIN_AREA'], 'controller'=>'login', 'view'=>'login', 'template'=>'login');
$controllers['user']['forgot']			= array('security'=>false, 'title'=>$_LANG['CHANGE_PASSWORD'], 'controller'=>'forgot', 'view'=>'forgot', 'template'=>'default');

$layout = array(
	'controller'=>'user/login.bit',
	'view'=>'user/login.bit',
	'template'=>'login/index.bit',
	'title'=>$_LANG['SIGN_IN_TO_NIBBLEBLOG_ADMIN_AREA']
);
/*
require_once(FILE_SHADOW);
require_once(FILE_KEYS);
$hash = Crypt::get_hash($_USER[0]['salt'].$_KEYS[2]);
var_dump($hash);
exit;
*/
if(isset($controllers[$url['controller']][$url['action']]))
{
	$dirname = $url['controller'].'/';
	$parameters = $controllers[$url['controller']][$url['action']];

	if($parameters['security'])
	{
		if(!isset($Login))
			exit('Nibbleblog security error');

		if(!$Login->is_logued())
			exit('Nibbleblog security error');
	}

	$layout['controller'] 	= $dirname.$parameters['controller'].'.bit';
	$layout['view'] 		= $dirname.$parameters['view'].'.bit';
	$layout['template'] 	= $parameters['template'].'/index.bit';
	$layout['title'] 		= $parameters['title'];
}

// Plugins
foreach($plugins as $plugin)
	$plugin->boot();

// Load the controller and template
@require(PATH_ADMIN_CONTROLLER.$layout['controller']);
@require(PATH_ADMIN_TEMPLATES.$layout['template']);

?>