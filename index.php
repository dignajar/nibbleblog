<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 12/08/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// ============================================================================
//	BOOT
// ============================================================================
	// If Nibbleblog installed
	if( !file_exists('content/private') )
	{
		header('Location:install.php');
		exit('<a href="./install.php">click to install Nibbleblog</a>');
	}

	require('admin/boot/blog.bit');

// ============================================================================
//	LOAD THEME
// ============================================================================
	require(THEME_ROOT	.	'config.bit');
	require(THEME_ROOT	.	'index.bit');

?>
