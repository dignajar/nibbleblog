<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// Check installation
if( !file_exists('content/private') )
{
	header('Location:install.php');
	exit('<a href="./install.php">click to install Nibbleblog</a>');
}

// Boot
require('admin/boot/blog.bit');

// Plugins
foreach($plugins as $plugin)
	$plugin->boot();

// Theme init.bit
if(file_exists(THEME_ROOT.'init.bit'))
	include(THEME_ROOT.'init.bit');

// Load the controller if set
if(file_exists(THEME_CONTROLLERS.$layout['controller']))
	require(THEME_CONTROLLERS.$layout['controller']);

// Load the template and view
require(THEME_TEMPLATES.$layout['template']);

?>