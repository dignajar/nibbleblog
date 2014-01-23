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

$installation_complete = false;

$dependencies = true;

$domain = getenv('HTTP_HOST');

$base_path = dirname(getenv('SCRIPT_NAME')).'/';

$blog_address = 'http://'.$domain.$base_path;

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
	$obj->addChild('notification_email_from',		'noreply@'.$domain);

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
	$node->addAttribute('position', 1);
	$node = $obj->addChild('category', '');
	$node->addAttribute('id',1);
	$node->addAttribute('name', $_LANG['MUSIC']);
	$node->addAttribute('slug', 'music');
	$node->addAttribute('position', 2);
	$node = $obj->addChild('category', '');
	$node->addAttribute('id',2);
	$node->addAttribute('name', $_LANG['VIDEOS']);
	$node->addAttribute('slug', 'videos');
	$node->addAttribute('position', 3);
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
	$content  = '<p>'.$_LANG['WELCOME_POST_LINE1'].'</p>';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE2'].'</p>';
	$content .= '<p>'.$_LANG['WELCOME_POST_LINE3'].'</p>';

	$content = Text::replace_assoc(
			array(
				'{{DASHBOARD_LINK}}'=>'<a href="./admin.php">'.$blog_address.'admin.php</a>',
				'{{FACEBOOK_LINK}}'=>'<a target="_blank" href="https://www.facebook.com/nibbleblog">Facebook</a>',
				'{{TWITTER_LINK}}'=>'<a target="_blank" href="https://twitter.com/nibbleblog">Twitter</a>',
				'{{GOOGLEPLUS_LINK}}'=>'<a target="_blank" href="https://plus.google.com/+Nibbleblog">Google+</a>'
			),
			$content
	);

	$_DB_POST = new DB_POSTS(FILE_XML_POSTS);
	$_DB_POST->add( array('id_user'=>0, 'id_cat'=>0, 'type'=>'simple', 'description'=>$_LANG['WELCOME_POST_TITLE'], 'title'=>$_LANG['WELCOME_POST_TITLE'], 'content'=>$content, 'allow_comments'=>'1', 'sticky'=>'0', 'slug'=>'welcome-post') );

	// Plugins
	$plugins = array('pages', 'categories', 'latest_posts');
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
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE3'].' <a href="./admin.php">'.$blog_address.'admin.php</a>') );
				echo Html::p( array('content'=>$_LANG['INSTALLATION_LINE4'].' <a href="./">'.$blog_address.'</a>') );
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
						echo Html::input( array('name'=>'path', 'type'=>'text', 'value'=>$base_path, 'autocomplete'=>'off') );

					echo Html::div_close();

					echo Html::input( array('type'=>'submit', 'value'=>$_LANG['INSTALL']) );

				echo Html::form_close();
			?>
		</section>

		<footer>
			<p><a href="http://nibbleblog.com">Nibbleblog <?php echo NIBBLEBLOG_VERSION ?> "<?php echo NIBBLEBLOG_NAME ?>"</a> ©2009 - 2014 | Developed by Diego Najar | <?php echo Html::link( array('content'=>$_LANG['EXPERT_MODE'], 'href'=>'./install.php?expert=true&language='.$_GET['language']) ) ?></p>
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