<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

if( file_exists('content/private') || file_exists('content/public') )
	exit('Blog already installed... May be you want to <a href="update.php">update</a> ?');

require('admin/boot/rules/1-fs_php.bit');
require('admin/boot/rules/4-remove_magic.bit');
require('admin/boot/rules/98-constants.bit');

// DB
require(PATH_DB . 'nbxml.class.php');
require(PATH_DB . 'db_posts.class.php');

// HELPERS
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
//	VARIABLES
// ============================================================================
$permissions_dir = 0755;
$php_modules = array();
$dependencies = true;
$blog_domain = getenv('HTTP_HOST');
$blog_base_path = '/';
$blog_address = 'http://'.$blog_domain;
$installation_complete = false;
$languages = array(
	'cs_CZ'=>'čeština',
	'de_DE'=>'Deutsch',
	'en_US'=>'English',
	'es_ES'=>'Español',
	'fr_FR'=>'Français',
	'fa_IR'=>'فارسی',
	'hu_HU'=>'Magyar',
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
		$obj->addChild('img_resize_option',		'auto');
		$obj->addChild('img_thumbnail',			1);
		$obj->addChild('img_thumbnail_width',	190);
		$obj->addChild('img_thumbnail_height',	190);
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
		$obj->asXml( FILE_XML_COMMENTS );

		// post.xml
		$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
		$xml .= '<post autoinc="1">';
		$xml .= '</post>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$node = $obj->addChild('sticky', '');
		$obj->asXml( FILE_XML_POST );

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
		$content = '<p>'.$_LANG['WELCOME_POST_LINE1'].'</p>';
		$content .= '<p>'.$_LANG['WELCOME_POST_LINE2'].'  <a href="./admin.php">'.$blog_address.$blog_base_path.'admin.php</a></p>';
		$content .= '<p>'.$_LANG['WELCOME_POST_LINE3'].'  <a target="_blank" href="http://forum.nibbleblog.com">http://forum.nibbleblog.com</a></p>';
		$content .= '<p>'.$_LANG['WELCOME_POST_LINE4'].'  <a target="_blank" href="http://www.facebook.com/nibbleblog">https://www.facebook.com/nibbleblog</a></p>';
		$_DB_POST = new DB_POSTS(FILE_XML_POST, null);
		$_DB_POST->add( array('id_user'=>0, 'id_cat'=>0, 'type'=>'simple', 'description'=>$_LANG['WELCOME_POST_TITLE'], 'title'=>$_LANG['WELCOME_POST_TITLE'], 'content'=>$content, 'allow_comments'=>'1', 'sticky'=>'0', 'slug'=>'welcome-post') );

		$installation_complete = true;
	}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Nibbleblog Installer</title>

	<script src="./admin/js/jquery/jquery.js"></script>
	<script src="./admin/js/functions.js"></script>

	<style type="text/css">
		body {
			font-family: arial,sans-serif;
			background-color: #FFF;
			margin: 0;
			padding: 0;
			font-size: 0.875em;
			color: #555;
		}

		#container {
			background: none repeat scroll 0 0 #F9F9F9;
			border: 1px solid #EBEBEB;
			border-radius: 3px 3px 3px 3px;
			margin: 50px auto;
			max-width: 700px;
			padding: 20px 30px;
			width: 60%;
			box-shadow: 0 0 4px rgba(0, 0, 0, 0.05);
		}

		h1 {
			margin: 0 0 20px 0;
			text-align: center;
		}

		h2 {
			color: #6C7479;
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
			font-size: 0.9em;
			display: inline-block;
		}

		div.dependency {
			background: #f1f1f1;
			padding: 10px;
			overflow: auto;
			margin-bottom: 5px;
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

		input[type="text"] {
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
			width: 100%;
			border: 1px solid #C4C4C4;
			border-radius: 2px;
			color: #858585;
			padding: 10px 8px;
			outline:none; /* not focus border on chrome */
			resize: none;
			margin-bottom: 17px;
		}

		select {
			width: 100%;
			padding: 6px;
			margin-bottom: 17px;
		}

		label {
			color: #333;
			margin-bottom:2px;
			display:block;
			font-size: 0.9em;
		}

		input[type="submit"] {
			padding: 3px 20px;
		}

		footer {
			margin: 30px 0;
			border-top: 1px solid #f1f1f1;
			text-align: center;
			font-size: 0.9em;
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
			<p><a href="http://nibbleblog.com">Nibbleblog <?php echo NIBBLEBLOG_VERSION ?> "<?php echo NIBBLEBLOG_NAME ?>"</a> | Copyright (2009 - 2013) + GPL v3 | Developed by Diego Najar | <?php echo Html::link( array('content'=>$_LANG['EXPERT_MODE'], 'href'=>'./install.php?expert=true&language='.$_GET['language']) ) ?></p>
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
