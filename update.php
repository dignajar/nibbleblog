<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 21/08/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

if( !file_exists('content/private') && !file_exists('content/public') )
	exit('Error 1024');

require('admin/boot/init/1-fs_php.bit');

// DB
require(PATH_DB . 'nbxml.class.php');
require(PATH_DB . 'db_posts.class.php');
require(PATH_DB . 'db_settings.class.php');

// HELPERS
require(PATH_HELPERS . 'crypt.class.php');
require(PATH_HELPERS . 'html.class.php');
require(PATH_HELPERS . 'net.class.php');
require(PATH_HELPERS . 'date.class.php');
require(PATH_HELPERS . 'fs.class.php');
require(PATH_HELPERS . 'number.class.php');
require(PATH_HELPERS . 'text.class.php');
require(PATH_HELPERS . 'redirect.class.php');
require(PATH_HELPERS . 'validation.class.php');
require(PATH_HELPERS . 'video.class.php');

// ============================================================================
//	OBJECTS
// ============================================================================
$_CRYPT = new HELPER_CRYPT();
$_DATE = new HELPER_DATE();
$_FS = new HELPER_FS();
$_HTML	= new HELPER_HTML();
$_NET	= new HELPER_NETWORK();
$_NUMBER = new HELPER_NUMBER();
$_TEXT = new HELPER_TEXT();
$_REDIRECT = new HELPER_REDIRECT();
$_VALIDATION = new HELPER_VALIDATION();
$_VIDEO = new HELPER_VIDEO();

// ============================================================================
//	UPDATER
// ============================================================================

	$_DB_SETTINGS	= new DB_SETTINGS( FILE_XML_CONFIG );
	
	$settings = $_DB_SETTINGS->get();
	
	$_DB_SETTINGS->set_general(array(
		'name'=>$settings['name'],
		'slogan'=>$settings['slogan'],
		'about'=>$settings['about'],
		'footer'=>$settings['footer'],
		'language'=>'en_US',
		'locale'=>'en_US'
	));
	
	$_DB_SETTINGS->set_advanced(array(
		'url'=>$settings['url'],
		'path'=>$settings['path'],
		'items_page'=>$settings['items_page'],
		'items_rss'=>$settings['items_rss'],
		'timezone'=>'UTC',
		'timestamp_format'=>'%m/%d/%y',
		'advanced_post_options'=>$settings['advanced_post_options'],
		'enable_wysiwyg'=>'1',
		'friendly_urls'=>$settings['friendly_urls'],
		'locale'=>'en_US'
	));

	$_DB_SETTINGS->savetofile();

	echo '<body><head></head>Updated to v3.3</body>';

?>
