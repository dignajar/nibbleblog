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
require('admin/boot/admin.bit');

// ============================================================================
//	CONTROLLER, VIEW and TEMPLATE
// ============================================================================
if($_URL['controller'] === 'dashboard')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'view')
	{
		define('LAYOUT_TITLE',		$_LANG['DASHBOARD']);
		define('LAYOUT_CONTROLLER',	'dashboard/view.bit');
		define('LAYOUT_VIEW',		'dashboard/view.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'post')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'new_simple')
	{
		define('LAYOUT_TITLE',		$_LANG['NEW_SIMPLE_POST']);
		define('LAYOUT_CONTROLLER',	'post/new.bit');
		define('LAYOUT_VIEW',		'post/new_simple.bit');
	}
	elseif($_URL['action'] === 'new_video')
	{
		define('LAYOUT_TITLE',		$_LANG['NEW_VIDEO_POST']);
		define('LAYOUT_CONTROLLER',	'post/new.bit');
		define('LAYOUT_VIEW',		'post/new_video.bit');
	}
	elseif($_URL['action'] === 'new_quote')
	{
		define('LAYOUT_TITLE',		$_LANG['NEW_QUOTE_POST']);
		define('LAYOUT_CONTROLLER',	'post/new.bit');
		define('LAYOUT_VIEW',		'post/new_quote.bit');
	}
	elseif(($_URL['action'] === 'edit_simple') || ($_URL['action'] === 'edit_video'))
	{
		define('LAYOUT_TITLE',		$_LANG['EDIT_POST']);
		define('LAYOUT_CONTROLLER',	'post/edit.bit');
		define('LAYOUT_VIEW',		'post/edit.bit');
	}
	elseif($_URL['action'] === 'edit_quote')
	{
		define('LAYOUT_TITLE',		$_LANG['EDIT_POST']);
		define('LAYOUT_CONTROLLER',	'post/edit.bit');
		define('LAYOUT_VIEW',		'post/edit_quote.bit');
	}
	elseif($_URL['action'] === 'list')
	{
		define('LAYOUT_TITLE',		$_LANG['POSTS']);
		define('LAYOUT_CONTROLLER',	'post/list.bit');
		define('LAYOUT_VIEW',		'post/list.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'categories')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'list')
	{
		define('LAYOUT_TITLE',		$_LANG['MANAGE_CATEGORIES']);
		define('LAYOUT_CONTROLLER',	'categories/list.bit');
		define('LAYOUT_VIEW',		'categories/list.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'comments')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'list')
	{
		define('LAYOUT_TITLE',		$_LANG['COMMENTS']);
		define('LAYOUT_CONTROLLER',	'comments/list.bit');
		define('LAYOUT_VIEW',		'comments/list.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'settings')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'general')
	{
		define('LAYOUT_TITLE',		$_LANG['GENERAL_SETTINGS']);
		define('LAYOUT_CONTROLLER',	'settings/general.bit');
		define('LAYOUT_VIEW',		'settings/general.bit');
	}
	elseif($_URL['action'] === 'advanced')
	{
		define('LAYOUT_TITLE',		$_LANG['ADVANCED_SETTINGS']);
		define('LAYOUT_CONTROLLER',	'settings/advanced.bit');
		define('LAYOUT_VIEW',		'settings/advanced.bit');
	}
	elseif($_URL['action'] === 'themes')
	{
		define('LAYOUT_TITLE',		$_LANG['CHANGE_THEME']);
		define('LAYOUT_CONTROLLER',	'settings/themes.bit');
		define('LAYOUT_VIEW',		'settings/themes.bit');
	}
	elseif($_URL['action'] === 'username')
	{
		define('LAYOUT_TITLE',		$_LANG['USERNAME_AND_PASSWORD']);
		define('LAYOUT_CONTROLLER',	'settings/username.bit');
		define('LAYOUT_VIEW',		'settings/username.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'plugins')
{
	// This controller requires user logged
	require('admin/kernel/security.bit');

	if($_URL['action'] === 'list')
	{
		define('LAYOUT_TITLE',		$_LANG['PLUGINS']);
		define('LAYOUT_CONTROLLER',	'plugins/list.bit');
		define('LAYOUT_VIEW',		'plugins/list.bit');
	}
	elseif($_URL['action'] === 'install')
	{
		define('LAYOUT_TITLE',		$_LANG['PLUGINS']);
		define('LAYOUT_CONTROLLER',	'plugins/install.bit');
		define('LAYOUT_VIEW',		'plugins/install.bit');
	}
	elseif($_URL['action'] === 'uninstall')
	{
		define('LAYOUT_TITLE',		$_LANG['PLUGINS']);
		define('LAYOUT_CONTROLLER',	'plugins/uninstall.bit');
		define('LAYOUT_VIEW',		'plugins/uninstall.bit');
	}
	elseif($_URL['action'] === 'config')
	{
		define('LAYOUT_TITLE',		$_LANG['PLUGINS']);
		define('LAYOUT_CONTROLLER',	'plugins/config.bit');
		define('LAYOUT_VIEW',		'plugins/config.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'default/index.bit');
}
elseif($_URL['controller'] === 'user')
{
	if($_URL['action'] === 'logout')
	{
		define('LAYOUT_TITLE',		$_LANG['LOGOUT']);
		define('LAYOUT_CONTROLLER',	'user/logout.bit');
		define('LAYOUT_VIEW',		'user/logout.bit');
	}
	elseif($_URL['action'] === 'login')
	{
		define('LAYOUT_TITLE',		$_LANG['SIGN_IN_TO_NIBBLEBLOG_ADMIN_AREA']);
		define('LAYOUT_CONTROLLER',	'user/login.bit');
		define('LAYOUT_VIEW',		'user/login.bit');
	}

	// Template for this controller
	define('LAYOUT_TEMPLATE',	'login/index.bit');
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
