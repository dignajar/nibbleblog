<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

/*
 * For 1-Click Setup
 * What you need to install Nibbleblog without the default form.
 * You can delete/replace this file, is independent from the blog, it only for install.
 *
 * $_POST = array('name', 'slogan', 'url', 'path', 'email', 'username', 'password')
 *
 * $_POST['name'] = Blog name
 * $_POST['slogan'] = Blog slogan
 * $_POST['url'] = Complete URL of your blog, example: http://www.mysite.com/blog/
 * $_POST['path'] = Relative directory of your blog on the server, example: /blog/
 * $_POST['email'] = Admin's email
 * $_POST['username'] = Admin's username
 * $_POST['password'] = Admin's password in plain text
 *
*/

// =====================================================================
//	VARIABLES
// =====================================================================
$permissions_dir = 0755;
$php_modules = array();
$dependencies = true;
$blog_domain = getenv('HTTP_HOST');
$blog_base_path = '/';
$blog_address = 'http://'.$blog_domain;
$installation_complete = false;
$languages = array(
	'ar_MA'=>'العربية',
	'cs_CZ'=>'čeština',
	'de_DE'=>'Deutsch',
	'en_US'=>'English',
	'es_ES'=>'Español',
	'fr_FR'=>'Français',
	'fr_IR'=>'فارسی-فارسی',
	'hu_HU'=>'Magyar',
	'it_IT'=>'Italiano',
	'pl_PL'=>'Polski',
	'pt_PT'=>'Português',
	'ru_RU'=>'Pyccĸий',
	'tr_TR'=>'Tϋrkçe',
	'vi_VI'=>'Tiếng Việt',
	'zh_TW'=>'繁體中文'
);

$languages_html = array();
foreach($languages as $raw=>$lang)
	$languages_html[$raw] = $lang;

// =====================================================================
//	FILES
// =====================================================================
if( file_exists('content/private') || file_exists('content/public') )
	exit('Blog already installed... May be you want to <a href="update.php">update</a> ?');

// Boot
require('admin/boot/rules/1-fs_php.bit');
require('admin/boot/rules/4-remove_magic.bit');
require('admin/boot/rules/98-constants.bit');

// CLASS
require(PATH_DB . 'nbxml.class.php');
require(PATH_DB . 'db_posts.class.php');
require(PATH_KERNEL . 'plugin.class.php');
require(PATH_HELPERS . 'crypt.class.php');
require(PATH_HELPERS . 'date.class.php');
require(PATH_HELPERS . 'filesystem.class.php');
require(PATH_HELPERS . 'html.class.php');
require(PATH_HELPERS . 'net.class.php');
require(PATH_HELPERS . 'number.class.php');
require(PATH_HELPERS . 'redirect.class.php');
require(PATH_HELPERS . 'text.class.php');
require(PATH_HELPERS . 'validation.class.php');

// ============================================================================
//	SYSTEM
// ============================================================================

// PHP MODULES
if(function_exists('get_loaded_extensions'))
{
	$php_modules = get_loaded_extensions();
}

// WRITING TEST
// Try to give permissions to the directory content
if(!file_exists('content'))
{
	@mkdir('content', $permissions_dir, true);
}
@chmod('content', $permissions_dir);
@rmdir('content/tmp');
$writing_test = @mkdir('content/tmp');

// BLOG BASE PATH
if( dirname(getenv('REQUEST_URI')) != '/' )
{
	$blog_base_path = dirname(getenv('REQUEST_URI')).'/';
}

// REGIONAL
if( !@include( 'languages/'. $_GET['language'] . '.bit' ) )
{
	$_GET['language'] = 'en_US';
	require( 'languages/en_US.bit' );
}

Date::set_timezone('UTC');

// ============================================================================
//	POST
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	mkdir('content/private',		$permissions_dir, true);
	mkdir('content/private/plugins',$permissions_dir, true);
	mkdir('content/public',			$permissions_dir, true);
	mkdir('content/public/upload',	$permissions_dir, true);
	mkdir('content/public/posts',	$permissions_dir, true);
	mkdir('content/public/pages',	$permissions_dir, true);
	mkdir('content/public/comments',$permissions_dir, true);

	// Config.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<config>';
	$xml .= '</config>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);

	// General
	$obj->addChild('name',					$_POST['name']);
	$obj->addChild('slogan',				$_POST['slogan']);
	$obj->addChild('footer',				$_LANG['POWERED_BY_NIBBLEBLOG']);
	$obj->addChild('advanced_post_options', 0);

	// Advanced
	$obj->addChild('url',					$_POST['url']);
	$obj->addChild('path',					$_POST['path']);
	$obj->addChild('items_rss',				4);
	$obj->addChild('items_page',			6);

	// Regional
	$obj->addChild('language',				$_GET['language']);
	$obj->addChild('timezone',				'UTC');
	$obj->addChild('timestamp_format',		'%d %B, %Y');
	$obj->addChild('locale',				$_GET['language']);

	// Images
	$obj->addChild('img_resize',			1);
	$obj->addChild('img_resize_width',		1000);
	$obj->addChild('img_resize_height',		600);
	$obj->addChild('img_resize_quality',	100);
	$obj->addChild('img_resize_option',		'auto');
	$obj->addChild('img_thumbnail',			1);
	$obj->addChild('img_thumbnail_width',	190);
	$obj->addChild('img_thumbnail_height',	190);
	$obj->addChild('img_thumbnail_quality',	100);
	$obj->addChild('img_thumbnail_option',	'landscape');

	// Theme
	$obj->addChild('theme',					'simpler');

	// Notifications
	$obj->addChild('notification_comments',			1);
	$obj->addChild('notification_session_fail',		0);
	$obj->addChild('notification_session_start',	0);
	$obj->addChild('notification_email_to',			$_POST['email']);
	$obj->addChild('notification_email_from',		'noreply@'.$blog_domain);

	// SEO
	$obj->addChild('seo_site_title',		$_POST['name'].' - '.$_POST['slogan']);
	$obj->addChild('seo_site_description',	'');
	$obj->addChild('seo_keywords',			'');
	$obj->addChild('seo_robots',			'');
	$obj->addChild('seo_google_code',		'');
	$obj->addChild('seo_bing_code',			'');
	$obj->addChild('seo_author',			'');
	$obj->addChild('friendly_urls',			0);

	// Default Homepage
	$obj->addChild('default_homepage',		0);

	$obj->asXml( FILE_XML_CONFIG );

	// categories.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<categories autoinc="3">';
	$xml .= '</categories>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);
	$node = $obj->addChild('category', '');
	$node->addAttribute('id',0);
	$node->addAttribute('name', $_LANG['UNCATEGORIZED']);
	$node->addAttribute('slug', 'uncategorized');
	$node = $obj->addChild('category', '');
	$node->addAttribute('id',1);
	$node->addAttribute('name', $_LANG['MUSIC']);
	$node->addAttribute('slug', 'music');
	$node = $obj->addChild('category', '');
	$node->addAttribute('id',2);
	$node->addAttribute('name', $_LANG['VIDEOS']);
	$node->addAttribute('slug', 'videos');
	$obj->asXml( FILE_XML_CATEGORIES );

	// tags.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<tags autoinc="0">';
	$xml .= '<list></list>';
	$xml .= '<links></links>';
	$xml .= '</tags>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);
	$obj->asXml( FILE_XML_TAGS );

	// comments.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<comments autoinc="0">';
	$xml .= '</comments>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);
	$obj->addChild('moderate', 1);
	$obj->addChild('sanitize', 1);
	$obj->addChild('monitor_enable', 0);
	$obj->addChild('monitor_api_key', '');
	$obj->addChild('monitor_spam_control', '0.75');
	$obj->addChild('monitor_auto_delete', 0);
	$obj->addChild('disqus_shortname', '');
	$obj->addChild('facebook_appid', '');
	$obj->asXml( FILE_XML_COMMENTS );

	// posts.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<post autoinc="1">';
	$xml .= '<friendly></friendly>';
	$xml .= '</post>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);

	$obj->asXml( FILE_XML_POSTS );

	// pages.xml
	$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	$xml .= '<pages autoinc="1">';
	$xml .= '<friendly></friendly>';
	$xml .= '</pages>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);

	$obj->asXml( FILE_XML_PAGES );

	// notifications.xml
	$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$xml .= '<notifications>';
	$xml .= '</notifications>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);
	$obj->asXml( FILE_XML_NOTIFICATIONS );

	// users.xml
	$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$xml .= '<users>';
	$xml .= '</users>';
	$obj = new NBXML($xml, 0, FALSE, '', FALSE);
	$node = $obj->addGodChild('user', array('username'=>$_POST['username']));
	$node->addChild('id', 0);
	$node->addChild('session_fail_count', 0);
	$node->addChild('session_date', 0);
	$obj->asXml( FILE_XML_USERS );

	// shadow.php
	$new_salt = Text::random_text(11);
	$new_hash = Crypt::get_hash($_POST['password'],$new_salt);
	$text = '<?php $_USER[0]["uid"] = "0"; $_USER[0]["username"] = "'.$_POST['username'].'"; $_USER[0]["password"] = "'.$new_hash.'"; $_USER[0]["salt"] = "'.$new_salt.'"; $_USER[0]["email"] = "'.$_POST['email'].'"; ?>';
	$file = fopen(FILE_SHADOW, 'w');
	fputs($file, $text);
	fclose($file);

	// keys.php
	$key1 = Crypt::get_hash(Text::random_text(11));
	$key2 = Crypt::get_hash(Text::random_text(11));
	$key3 = Crypt::get_hash(Text::random_text(11));
	$text = '<?php $_KEYS[0] = "nibbl'.$key1.'"; $_KEYS[1] = "eblog'.$key2.'"; $_KEYS[2] = "rulez'.$key3.'"; ?>';
	$file = fopen(FILE_KEYS, 'w');
	fputs($file, $text);
	fclose($file);

	// welcome post
	$content  = '<img alt="Mr Nibbler" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHYAAAB2CAYAAAAdp2cRAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA3XAAAN1wFCKJt4AAAAB3RJTUUH3QoaFRccHA+zSQAAFgFJREFUeNrtXXl4lOW1/533myUYSNjEDXAJKKisqUi01akksySZmYQy6KPUUu3VLmrFtlfber2RK7bWem9r1bbWS6lVS001K8wSkLQqoICitogQLBUQr0CVCUtm+d5z/5hBZsJkMpPZ05znmT/mm2++5fzes77vOS8wRIOSKF8exGisH6fVYoyqYiTAegBQVXyq05Hv6NHSv3d2rugZgqsAgDUYDBq9vsQEwAzQ5wGcH+d0BrAXQBfA7zDjZaBnvcfjOToEYZ4A63A4hnV3+25jxk0AnTXgBycEmPkvALX4fNTa2dn86RCcOQLWZLJXAvwIQOem9SWIjzDTCkUJPLZ69eqPhmDNErDl5eXasWPPeRigxZm9E/cAaHC7W3/1rw6sknmnyFhcXDz6WYDmZ2GcagCqnDx5yriurvc8Q8BmEFSioiaAvpDl95o1adLU03ft2u4ZAjb9TpLi94vfAXR1jt5t9uTJF+3v6nrvrX9FYEWmLuz1+peFQpmc0jKTaf5ZQ8Cmzfut+xKAW3P9csw0nCjYMOQVp8Wu1l9KpHYANCxP3pFVNVi+Zs2q94ckdoBkMBiKiNTleQRqyFXWKF8dUsUpkF4/8nsAXZhvL8mMGywWiz7V61gsdqvZbOs0GusvzXdgNemzq7ZJgLw9j+YVIoV2NLPOAMA9QE2k0elKfigl7gSIiOS9AK77l5BYItwDkC5fX1RKHlDYZbVaTysqKn2RiJZE+CQmo9E6ZdADa7HUlTFTfV57iURzk5fUxUWBgFjJjKtOvZz490EPrJTyNmQhPZkizaisdJQmMxb0+k9+HQPUEzTfaKyzDFpgDQbHcIAWFICjqAjRU5542Ga7HoA9LvME/6ympmbUoARWp/MtBDCiIIJ2EglNFzocDp0QdF8C3vYZqqp5YFACS4SaQontmHF2IucdOeI3MeOMBK95vcVinTuogA2pYXyhUIAVgosSBCuZdyIpacmgAlav930+fSEOSyJsJsLTROgA4Eu/xNLxBIG9KMlLf9HhcORTti21BAURGZnToiJf1WiUJatXN+04cay2tvacYFC8xkzD0whsd4Jn6pJLtJDu8OHAxQC2DApgmdmYaqaJiJtLSvQ3NzY2qpHH29vb9xmN9reIcGUaVfGuBJ9qD4CKpBipYf+pnnV9LSC/IQQXA+RlxgdE8k2AXnG5Wt/LS2AtFvvFUmJ8iqBuJQrc2tjYqvbxO6czRamq6s4EB+xmIlqYxKUPHjt2+N0YEX4VEa5kpohri0UAYDLZdjOjSatVnl+1qundvLGxUpIxxXsfJRI3O51OXxxVPzGN7/rJyJHD3k9sAKAFQDCJay/v7OwMxhiYZXGG9XlEtCQYlBvMZrs7JN3pG8UiBWm7JkW7epfT2dynaqyqsp3NjLQBS8Sv9lb3fdHata3/B2Blgpd+u6RE99M+7npmgry4nEg+Yzbb1lkstlk5A9ZgMGiYUZ7CfX/k8bT8MW6aSCFrOlWTlHgpufP5QYAP96eCiejGxsbGU+xreXm5FuCJyQ12mikldZjNtrtyAqxeP+oSAMUDlNUVbnfLQ/1kfpRQpUDaKKjXc2syf+joaP0Q4DsAln2c4hNCLnK5mnfH+nHMmLPLBhgKapjpPrPZ/pNUVPMAVbG8bICgri4p0X+nv7O6u32LgKRjyThqGOva2toOJvs/t7uthUjcdaq95ePMvMjpbNsY565XpGiqbjGb7Q9lGViaOoBHfcXnG31Tf3bOaLSez0xpzb9KSc/GOt7Q0CDM5rrz4v3X5WpeQSTqAGwP2+otQtA8j6e1o5/B1GtihP0APgSwCeDO8OdlZn63L5XPjFtMJvvdWQx35NTktAS/7PONdvRXClldXX2mqlIz0jup8KHf/2n7qQPIWLxhwxvLAVIALIgPbtMrDQ0NV7zyyt9GrFnTeDghiRHidikxk0juJArsdzqdB+KZniNHfNOZycTMtwA0OoJ3d5vN9q0uV0tSqz8GpMNNJtv2RD2+cKxqczqd3viSaiwmGtYGYHY6pZUIP3a5Wn7ci5HDvF5fC0BzAHzidrdcgFCpZs7JarWO9fvF87348PeDB/fO2bJlSyCTqpiIaEyCTN2sKGp9f6CWl5driYqeTjeoALw9PXRKgZbX63skDCoAjDIarechTyjkC+gWALwj4vD5Y8dOsGVUFVsslhFSQpvAqX+W8vj1Lle/xcl0+unnPMZM89LNJCI80btu1my2XcdM1/c67zIAf88WeEajdQoR3UqEWcy0AxCPu91Nb5102hr/aTRabyRCJ0DhGSm2A3ghYxI7fPjwRNRBkxD+hYlUnJvNtqXMdG0GQP2UyP9E9KCsK2OmR2KwYXY2AK2urj7TZLL9jEi8CtBXmWkmgIWAfMlstt8Rea7H07adGcsjDs0zGAxFGQO2sbGxJ1yHGtuRY/zY7W65KV6q8KSttn+LmW7PBBOlxGORJqChoUGoKj/aR/w9pz/7X1NTP9VgqBuZyjOpqsYarhHuvT5MYcZSo9G+IDpJE/gfACd4XazXlxgy6RUzgI0Aet2E9xPRnW53Yt6bxWK3Ssn/laF1yAf9fl2Ubd2wYesdcWaKZtTU1IxatWrVJ7E9XP3FwaDs0OvBJpN9B4B1RLxeSrFPUaQEaDwzzgd4NELzyC/Gnr2Jn7AQgh8yGBa3n4genE7nAbPZ/gIzbgg7NzcDcGUsjhUC9wM4wYSPAPy3EIHLE3XJq6rqZkrJTwKUoWo/ur+zs/FIZGwMyHvi/EEJBLRXx5H+7ogo4iIAX2emp4l4rZS0Tkr8nhlLmelOZrqbmdabTLZvx0gZvt9PSnGMTne4stex30Wow3mJ5pIHxFins/VNn2/UVECZ6vMdvtTtblnan+d7Uv3OP0sI+YdM1fcQ8ZaKipnP9vLiHz/phPT5vz7XRZeWFu0EkEyHGgWg+81m21ciD/r9n64LC0I8hT0uOvvV/DozXg0/pZCSnqmtrT0nQ5knoLNzRY/b/eL+WNNVcTzqEkBtSqVbTH9mDMCShoYGGeGcLUgsvcdGq9V6Wh9+hQpg7QDSgg+ZTNZLTvKss4dIfpmID8Vw9j4g4mVardp0qsaQ3wDQFT7PJ6UyOiMJioGQw+FQurv9K5lRlbm78FNud+t3T3wzGOpG6vXydYDGJXiBO9zulqdjO1D2y4ngSp5n/E5JiX5e5AyQweAYrtP5aonoQoQmE/7sdLa91k+ShCorHSWJZr6yBqzJZL8PwF2Zuj4RHwoElDlr1zYdOimt9l8xJ1U81VVRMWtOpMRHh2b2R5lx4wAe73G3u+WH2Ux0ZKUsw2i03UREDZm8BxF9q6Oj+Y2Ie14F0ANJDt7Re/Z8/M6uXdt3xPpx1qzpnX6/agASW58cGU5NnnxRUWnp8Ff3798v06H9Jk688Lquru1/yxmwFov1aiLxmwzfa5Xb3bLsxBebzTZCVdEI0KjkB4gsnzDhrBW7d+8+xXfYtm1bsKxsejuROhdIdr0XzS0uLrlu0qQpRRdccPGR2bOndW/bti04kJedOLHsJmZ6tKxs6tFdu7a/nnVVHKqZpQ4AGaxv4cOKErw8siOb2Wx7ijmVeiJ+1O1u7bPEw2BYXKTXf7oM4JtS5OHBcPx/jBnHAOwDeCdAb5SU6NbHmuKsqakZparKZmYaA/Aut7u1PK1ecX9ks9lGAPRMZkEFiOjBXqBelxqoAEC3h9oDxosImr9DRAsA3p/CjcYCNI0ZlwP4IoBFAN0PoM3r9f8m1h+CQeWeEKhAvOgiI8A6HA7F56OnAWS6OHjj3LmzPmOA1Wody4wH0zFeAH6yv5YELlfzWlXVzyWSvyRCII1aaC8znjrVV7FOAejmiHDqzawC293t/154BGaSfFKqt0d6sIGAeCB6kjolbEcLobb0B+6aNY2HXa627zNzBTN+B2BP6MMvMNNiRRFzmOVcQH4F4OdC6jYaRQD7iNhJhPuZUV1Sop/h8bSsP1U7iTnhlKUKYJ1Go3wza+FOKAeMpzNtv4n4IZer9Ucn7XndHIAz0WKvh4jucbmaV6TrgvPm1Y8pKsK4QCDIfn/37s7OzrQ32U4r86ur55+rqsF16ZOavkDFBz09o+ZELrUxm+0vMuOaDN7zyZ6ew/dlAoRMUNpUsdVqPS0YDK7MNKhh7XVvNKjWyzIJatie3aLXl24KT61RvgObtnZAfj8tIxrI6sWkaZPL1doWzXTxzSzxawIRnjKb7V9n5p+WlOg7Eq0uSMyMWfRERecyq6cz42wpxVgh5BnMOENKburoaPNkFViz2V7NjGx1P1uKiJyqxTJ/vJSqNZvSwIzPAbTS6/XtNRrtKzSawDOpdDY3m+vOY5aPMNNVUkrtCYVAxDhR0CUEdACyB6zRaJ/AzL/MhnYi4q0uV+vLkcekDF4bakCdC6LxRLhXVTU/MJlsm4nIKaV0ejxt2xNP4jhGM/tcAJ0Zr9aYiE7Pmo11OBwKET8JUGl2JIWWx3jlhcg5kQBoDjP+k0hsNJttf3A4HEpi7+T/z0SW8jLjk6wB6/X6vgFQRZa459Xp5J+ibZJtFtJYCpLGAWjxev3396/trOcThZa9JOCVb80KsOEsyH9kkV3tbW1tx6LVcPpXN6aRbjOZ6r4cHyz6XmLmkP2BAD+XcWAbGhoEkXgCgD5bXCKilt5mAOD5yGvin5nN9vrYDlN9BZDYwGTGknDNbmaBXb/+jcVI/6r9eNRN5O+MNgM9M5JYGZErUpjxlMlkuy3yYFVV3Uxm+Vv0P5XpJeKvezytz2Y8jp03r34MkXpvdm0Wd566Tlm5Kk/KbfoFF6AHTCb7IgA7Qvv2saEfbedlxq8Vxf9kvGKutAKr0cgfZSe7FEUvxwh9ruKCwPUzmhL6UH8h3Z8CAeXuyCU+A6GkVLHJVD8DgCPbHNFoaEPvDA0zV2CQERFemzt39i2pgpo0sETqPch+ntRbXKzbFn1I+7k823cgXSbn0b4W0mVMFZtMtknMlIN9dPjt3vlYZiofhNIa0Gp5XbquJxIfTXQtcjOrESM9x9MHn7Tind5xelaAJYItR2N5e4xBdikGHfGmdF4tIWCrqx1nAsjJtitE1BX53WBYXARg0uADFuuyDqyq+q/JkRoGs9wb+V2vP3Qx0jiPnB/2lQ+VlOhfyjqwyPzCtD5Jp+MPe0lw2SBUw8/H6u6WaWCJqM+dLDIe6sRwKM4bZKD6VZV+ke6r9gus0Vh/SaL98TPw0v+M4T1OHEywEuHXofZ+WQaWSM1hhodiFFPTxEEE6l8OHNi3NBPXTgBYmpZDNRWjFpTHDwJM3yPipSNG6BYk05QrGUpkknd6DldbxgjYk1v7k2MKArwPoHeZ8Y6i4G1VFa95PE0fZ/rG/QLLnEsvlKLKDB0Oh87r9efT5k3dAO9npgNE+JCZ9wqB3YD4B4DdBw7s2ZcpiUwJWIvFUiJlTnfBigoBjh7FaOROfTwvBNoAeYBZ+ainZ+RH/TX9zCVp4kurNtcb1wei7X0wl/vI7XY6W9oKxQaI+MBycS4frndpYiDAp+XuWfhcFBCJfhib061DmTkKWCFyNwfLnH9blKcU7uQWWKLo76o2h08zw2i0TxgkwCoHcvlwRKyLllj4c/g0Qgj83OFw6AoeWK1W/Ti3wEb3RWZmf241CK7xev3rqqrqZhY0sOEE/Ee5Y2T0hoXMGn8e8OwSIXit2Wx/2Gg0FhcksGF2vpFDVXx6r++H84RvCjP+jWjYWoulrqwggSXquzNJFqCNAnbECO2HCDXWyBeaIqV8yWKxXl1wwEqpOHNo08Y1NDR89oyhyeiU+iplYvCVSkl/DNXiFBCwHk/TXwG8l6PnK3r99bd7hxh78k/xURGz+myo4XUBxbFEaMrVA0opey9cezs/3RUaTURPIE8ajyQErBCBFeEtvHKgjmWvhiXi9fwNMqjCaLR/qWCAXb169UdE1JgrZkV+U1V1E/KYiPjbBQMsAGg06oNxtmXJJKPmRqq3NWvaPgB4bx5DOy1U7V8gwLa3t+8jws9zkKQYU1VVN6MX89rzW2rFwoIBFgB6erwPE+G1rGcDFO5VDEYvIr/pioICtrOzM0hE30xgi+t0x7PGyO8VFTM35188GxVFnF1QwAKA09m8Swi+Nrv2lmeG64cAAA0NDZKZ/jdfgWVmbcEBGwK3baOUfCNO7ruWaRkQwaB/ceQRvV6uyN79k37enoIEFgBCDRtldazV+hlSb1+L3I0xtM8qr8xTmd1bsMACgNvd9gag1GfpRcbqdKW1vYKwh4j4SP7ZWIrZS7G62j7NYrFdabVax+Y1sCFwm94SIvBFgNdnQWpvjb73i/ulFMvyz8bSxljHVZUflJJW+f20zWSy/7ay0joxb4EN2VznAbe7tYaI7kTM1ftpo8tMJltU6HPo0J6niPiveYSrykyntC9yOBzDTm4fTjoA9YpCLxuNtqq8BfbEQHW5mlcwCyPA72RQbn8Q2Vl0y5YtASLxFeCzrUBzLa8bYpVweL3+K3FK0y4qJcLvjUb75fkMLIDQNF9FxeyriejODNm/6aGuq9EhGCBvA1jmGlYiLO/jpz6Kx6lICKTdnGR0iqmysuYCRdH8BEBlepnHRwDxeZereXfk8fBmSo8DuVoPzbt9Pu/nYm29ajLZtgB910Fpteol7e3t+/JWYiNpzZpV77vdLQuIZBXAaZtuY6bhzPJ5g8ERtdjN5WpdyUw3A3w8RwL7w1ighvbuiVvcph4/rk1r7JuVBeMuV9umkhK9BcASgNO0pJUu1Ov9TxgMBk20KWhu1miUawDekWVQW9zu1lWxf5L2+AMVD6ejzV4kZU1lbdu2jXftem/rhAnTf6vRqAB4Vhp6+V+k1eovmDz5AndXV9dni9x27tx+sKzs3OeE0JwL0MVZsKvvC+G/tquryxfr90mTLnoEOLEfXTRbAPldj6c17enRnC3jqK2tPScYVL7DzIvC7n8qNncrIBb3trkhu2u/lRnLkLEWQnyYCEaXqzXmurDwTtc2ZkwlYp2UYCGwm4g2OZ0t2zI22HLtRdbW1p4TCGjuAngRUutY7mXm719xxew/9G40aTTariKiZwCUpBtUZlro8bS8hjyjvNnxKQSwWILQVptFqWh9Zvy8tFTXFNk7qbraPk1K/Cl9HXD4H8zKDeFVnBgCth8yGOpGFhXxteG90C9JgfFvSSm/1tHRvvOk5FqvIRJpmKTnF3w+/bc7OxuPIE8pr/doMxrryoXgGwDUDFDSjhGJL7lcTRtOxpP2NwEMcP0vH2cWD3o8zY8hz/vW53V9rMfTvMXlarlr7txZU4WQZiL+BYC3k9iE9zRm9WvRocWA8spMhJVSotzjaf4FCmAzgoJoNhl2hjaGPzAYDEVFRSOmAVQmJY0n4jPD21qPIcI4AKPDJZfbhKBHokaygJrEXgKfAHhOCFoeSlsWDhVkF9HwHq6bwp9krY83LHExzBB/DGAbETZLSS8dOrR3U67a+QxqG5spqqx0lCpKz3gpFS0AaLV0TKNR97e2tnZjiIYon+n/ASwszMjbr4P/AAAAAElFTkSuQmCC" style="float: left; margin-top: 15px;" class="align_left" />';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE1'].'</p>';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE2'].'<br/><a href="./admin.php">'.$blog_address.$blog_base_path.'admin.php</a></p>';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE3'].'<br/><a target="_blank" href="http://forum.nibbleblog.com">http://forum.nibbleblog.com</a></p>';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE4'].'  <a target="_blank" href="http://www.facebook.com/nibbleblog">https://www.facebook.com/nibbleblog</a></p>';
	$_DB_POST = new DB_POSTS(FILE_XML_POSTS);
	$_DB_POST->add( array('id_user'=>0, 'id_cat'=>0, 'type'=>'simple', 'description'=>$_LANG['WELCOME_POST_TITLE'], 'title'=>$_LANG['WELCOME_POST_TITLE'], 'content'=>$content, 'allow_comments'=>'1', 'sticky'=>'0', 'slug'=>'welcome-post') );

	// Plugins
	$plugins = array('pages', 'categories', 'last_posts');
	foreach($plugins as $plugin)
	{
		include_once(PATH_PLUGINS.$plugin.'/plugin.bit');
		$class = 'PLUGIN_'.strtoupper($plugin);
		$obj = new $class;

		if( @!include(PATH_PLUGINS.$plugin.'/languages/'.$_GET['language'].'.bit') )
			include(PATH_PLUGINS.$plugin.'/languages/en_US.bit');

		$merge = array_merge($_LANG, $_PLUGIN_CONFIG['LANG']);

		$obj->set_lang($merge);

		$obj->set_attributes(
		array(
			'name'=>$_PLUGIN_CONFIG['LANG']['NAME'],
			'description'=>$_PLUGIN_CONFIG['LANG']['DESCRIPTION'],
			'author'=>$_PLUGIN_CONFIG['DATA']['author'],
			'version'=>$_PLUGIN_CONFIG['DATA']['version'],
			'url'=>$_PLUGIN_CONFIG['DATA']['url'],
			'display'=>isset($_PLUGIN_CONFIG['DATA']['display'])?false:true
		));

		include(PATH_PLUGINS.$plugin.'/languages/en_US.bit');
		$obj->set_slug_name($_PLUGIN_CONFIG['LANG']['NAME']);

		$obj->install(0);
	}

	$installation_complete = true;
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name='robots' content='noindex,nofollow' />
	<title>Nibbleblog Installer</title>

	<script src="./admin/js/jquery/jquery.js"></script>
	<script src="./admin/js/functions.js"></script>

	<style type="text/css">
		body {
			font-family: arial,sans-serif;
			background-color: #FAFAFA;
			margin: 0;
			padding: 0;
			font-size: 62.5%;
			color: #555;
		}

		#container {
			margin: 50px auto;
			max-width: 700px;
			padding: 20px 30px;
			width: 60%;
			box-shadow: 1px 0px 2px rgba(0, 0, 0, 0.08);

			background: #FFFFFF;
			border: 1px solid #CCC;
			border-radius: 3px 3px 3px 3px;
		}

		h1 {
			margin: 0 0 20px 0;
			text-align: center;
			color: #2986D2;
			font-size: 2.6em;
			font-weight: normal;
		}

		h2 {
			color: #6C7479;
			font-size: 2em;
		}

		p {
			font-size: 1.3em;
		}

		a {
			color: #2361D3;
			cursor: pointer;
			text-decoration: none;
		}

		a:hover {
			text-decoration: underline;
		}

		a.lang {
			display: inline-block;
		}

		div.dependency {
			background: #f1f1f1;
			padding: 10px;
			overflow: auto;
			margin-bottom: 5px;
			font-size: 1.3em;
		}

		div.status_pass {
			float:right;
			color: green;
		}

		div.status_fail {
			float:right;
			color: red;
		}

		#configuration,
		#dependencies,
		#complete {
			display: none;
		}

		input[type="text"],
		input[type="password"],
		textarea {
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
			width: 100%;
			border: 1px solid #ccc;
			border-radius: 2px;
			color: #858585;
			padding: 10px 8px 10px 8px;
			outline:none; /* not focus border on chrome */
			resize: none;
		}

		input[type="submit"] {
			border: 1px solid rgba(0, 0, 0, 0.1);
			border-radius: 2px 2px 2px 2px;
			box-shadow: 0 0 1px rgba(0, 0, 0, 0.05);
			color: #444444;
			cursor: pointer;
			display: inline-block;
			font-size: 1.3em;
			padding: 7px 33px;
			background: #F1F1F1;
			background: -moz-linear-gradient(center top , #F5F5F5, #F1F1F1);
			background: -webkit-gradient(linear, left top, left bottom, from(#F5F5F5), to(#F1F1F1));
			margin-top: 20px;
		}

		input[type="submit"]:hover {
			background: #E1E1E1;
			background: -moz-linear-gradient(center top , #EFEFEF, #E1E1E1);
			background: -webkit-gradient(linear, left top, left bottom, from(#EFEFEF), to(#E1E1E1));
		}

		select {
			width: 100%;
			padding: 6px;
		}

		label {
			color: #333;
			margin-bottom:3px;
			display:block;
			font-size: 1.3em;
			margin-top: 16px;
		}

		footer {
			margin: 30px 0;
			border-top: 1px solid #f1f1f1;
			text-align: center;
		}

		#head {
			margin-bottom: 20px;
		}
</style>

</head>
<body>

	<div id="container">

		<header id="head">
			<?php
				echo Html::h1( array('content'=>$_LANG['WELCOME_TO_NIBBLEBLOG']) );
			?>
		</header>

		<noscript>
		<section id="javascript_fail">
			<h2>Javascript</h2>
			<p><?php echo $_LANG['PLEASE_ENABLE_JAVASCRIPT_IN_YOUR_BROWSER'] ?></p>
		</section>
		</noscript>

		<section id="complete">
			<?php
				echo Html::h2( array('content'=>$_LANG['INSTALLATION_COMPLETE']) );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE1']) );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE2']) );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE3'].' <a href="./admin.php">'.$blog_address.$blog_base_path.'admin.php</a>') );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE4'].' <a href="./">'.$blog_address.$blog_base_path.'</a>') );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE5'].' <a href="http://forum.nibbleblog.com">http://forum.nibbleblog.com</a>') );
			?>
		</section>

		<section id="dependencies">
			<h2><?php echo $_LANG['DEPENDENCIES'] ?></h2>
			<?php
				// PHP MODULE DOM
				echo Html::div_open( array('class'=>'dependency') );
					echo Html::link( array('class'=>'description', 'content'=>$_LANG['PHP_VERSION'].' > 5.2', 'href'=>'http://www.php.net', 'target'=>'_blank') );

					if( version_compare(phpversion(), '5.2', '>') )
					{
						echo Html::div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo Html::div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo Html::div_close();

				echo Html::div_open( array('class'=>'dependency') );
					echo Html::link( array('class'=>'description', 'content'=>$_LANG['PHP_MODULE'].' - DOM', 'href'=>'http://www.php.net/manual/en/book.dom.php', 'target'=>'_blank') );

					if( in_array('dom', $php_modules) )
					{
						echo Html::div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo Html::div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo Html::div_close();

				echo Html::div_open( array('class'=>'dependency') );
					echo Html::link( array('class'=>'description', 'content'=>$_LANG['PHP_MODULE'].' - GD', 'href'=>'http://www.php.net/manual/en/book.image.php', 'target'=>'_blank') );

					if( in_array('gd', $php_modules) )
					{
						echo Html::div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo Html::div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo Html::div_close();

				echo Html::div_open( array('class'=>'dependency') );
					echo Html::link( array('class'=>'description', 'content'=>$_LANG['PHP_MODULE'].' - SimpleXML', 'href'=>'http://www.php.net/manual/en/book.simplexml.php', 'target'=>'_blank') );

					if( in_array('SimpleXML', $php_modules) )
					{
						echo Html::div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo Html::div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo Html::div_close();

				echo Html::div_open( array('class'=>'dependency') );
					echo Html::link( array('class'=>'description', 'content'=>$_LANG['WRITING_TEST_ON_CONTENT_DIRECTORY'], 'href'=>'http://wiki.nibbleblog.com/doku.php?id=how_to_set_up_permissions', 'target'=>'_blank') );

					if( $writing_test )
					{
						echo Html::div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo Html::div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo Html::div_close();
			?>
		</section>

		<section id="configuration">

			<?php
				echo Html::form_open( array('id'=>'js_form', 'name'=>'form', 'method'=>'post') );

					// LANGUAGE
					echo Html::label( array('content'=>$_LANG['LANGUAGE'], 'class'=>'blocked') );
					echo Html::select( array('id'=>'js_language', 'name'=>'language'), $languages_html, isset($_GET['language'])?$_GET['language']:'en_US');

					echo Html::label( array('content'=>$_LANG['BLOG_TITLE']) );
					echo Html::input( array('id'=>'js_name', 'name'=>'name', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254', 'value'=>'') );

					echo Html::label( array('content'=>$_LANG['BLOG_SLOGAN']) );
					echo Html::input( array('id'=>'js_slogan', 'name'=>'slogan', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254', 'value'=>'') );

					echo Html::label( array('content'=>$_LANG['ADMINISTRATOR_USERNAME'].'*') );
					echo Html::input( array('id'=>'js_username', 'name'=>'username', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254', 'value'=>'') );

					echo Html::label( array('content'=>$_LANG['ADMINISTRATOR_PASSWORD'].'*') );
					echo Html::input( array('id'=>'js_password', 'name'=>'password', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254', 'value'=>'') );

					echo Html::label( array('content'=>$_LANG['ADMINISTRATOR_EMAIL'].'*') );
					echo Html::input( array('id'=>'js_email', 'name'=>'email', 'type'=>'text', 'autocomplete'=>'off', 'value'=>'', 'placeholder'=>'Enter a valid e-mail address') );

					echo Html::div_open( array('hidden'=>!isset($_GET['expert'])) );

						echo Html::label( array('content'=>$_LANG['BLOG_ADDRESS']) );
						echo Html::input( array('name'=>'url', 'type'=>'text', 'value'=>$blog_address, 'autocomplete'=>'off') );

						echo Html::label( array('content'=>$_LANG['BLOG_BASE_PATH']) );
						echo Html::input( array('name'=>'path', 'type'=>'text', 'value'=>$blog_base_path, 'autocomplete'=>'off') );

					echo Html::div_close();

					echo Html::input( array('type'=>'submit', 'value'=>$_LANG['INSTALL']) );

				echo Html::form_close();
			?>
		</section>

		<footer>
			<p><a href="http://nibbleblog.com">Nibbleblog <?php echo NIBBLEBLOG_VERSION ?> "<?php echo NIBBLEBLOG_NAME ?>"</a> ©2010 - 2014 | Developed by Diego Najar | <?php echo Html::link( array('content'=>$_LANG['EXPERT_MODE'], 'href'=>'./install.php?expert=true&language='.$_GET['language']) ) ?></p>
		</footer>

	</div>

	<script>
	$(document).ready(function(){

		<?php
			if($installation_complete)
				echo '$("#complete").show()';
			elseif($dependencies)
				echo '$("#configuration").show()';
			else
				echo '$("#dependencies").show()';
		?>

		$("#js_language").change(function () {
			var locale = $("#js_language option:selected").val();
			var url = location.pathname+"?language="+locale;
			console.log("Nibbleblog: Url="+url);
			location.replace(url);
		});

		$("form").submit(function(e){
			var username = $("#js_username");
			var password = $("#js_password");
			var email = $("#js_email");

			username.css("background-color", "");
			password.css("background-color", "");
			email.css("background-color", "");

			if(empty(username.val()))
			{
				username.css("background-color", "#D8F0F0");
				return false;
			}

			if(empty(password.val()))
			{
				password.css("background-color", "#D8F0F0");
				return false;
			}

			if(!validate_email(email.val()))
			{
				email.css("background-color", "#D8F0F0");
				return false;
			}

			return true;
		});

	});
	</script>

</body>
</html>