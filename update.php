<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 07/10/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

if( !file_exists('content/private') && !file_exists('content/public') )
	exit('Blog not installed');

require('admin/boot/admin.bit');

// ============================================================================
//	UPDATER
// ============================================================================

	//
	// UPDATE SETTINGS
	//
	$new_settings = array(
						'name'=>'My Blog',
						'slogan'=>'',
						'footer'=>'Powered by Nibbleblog',
						'about'=>'',
						'language'=>'en_EN',
						'timezone'=>'UTC',
						'theme'=>'clean',
						'url'=>'',
						'path'=>'',
						'items_rss'=>'4',
						'items_page'=>'4',
						'timestamp_format'=>'%m/%d/%y',
						'advanced_post_options'=>'0',
						'locale'=>'en_EN',
						'friendly_urls'=>'0',
						'enable_wysiwyg'=>'1',
						'img_resize'=>'1',
						'img_resize_width'=>'800',
						'img_resize_height'=>'600',
						'img_resize_option'=>'auto',
						'img_thumbnail'=>'1',
						'img_thumbnail_width'=>'190',
						'img_thumbnail_height'=>'190',
						'img_thumbnail_option'=>'landscape'
	);

	$settings = $_DB_SETTINGS->get();

	foreach($new_settings as $key=>$value)
	{
		if(!empty($settings[$key]))
		{
			$new_settings[$key] = $settings[$key];
		}
	}

	$_DB_SETTINGS->set($new_settings);
	$_DB_SETTINGS->savetofile();

	exit('Updated to Nibbleblog v3.3.1');

?>
