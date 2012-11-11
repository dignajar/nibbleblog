<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 07/10/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

if( file_exists('content/private') || file_exists('content/public') )
	exit('Blog already installed');

require('admin/boot/init/1-fs_php.bit');
require('admin/boot/init/10-constants.bit');

// DB
require(PATH_DB . 'nbxml.class.php');
require(PATH_DB . 'db_posts.class.php');

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
//	VARIABLES
// ============================================================================
$php_modules = array();
$dependencies = true;
$blog_base_path = '/';
$blog_address = 'http://'.getenv('HTTP_HOST');
$installation_complete = false;
$languagues = array(
	'de_DE'=>'Deutsch',
	'en_US'=>'English',
	'es_ES'=>'Español',
	'fr_FR'=>'Français',
	'hu_HU'=>'Magyar',
	'pl_PL'=>'Polski',
	'pt_PT'=>'Português',
	'ru_RU'=>'Pyccĸий',
	'vi_VN'=>'Tiếng Việt',
	'zh_TW'=>'繁體中文'
);

// ============================================================================
//	SYSTEM
// ============================================================================

// PHP MODULES
if(function_exists('get_loaded_extensions'))
	$php_modules = get_loaded_extensions();

// WRITING TEST
// Try to give permissions to the directory content
if(!file_exists('content'))
	@mkdir('content');
@chmod('content',0777);
@rmdir('content/tmp');
$writing_test = @mkdir('content/tmp');

// BLOG BASE PATH
if( dirname(getenv('REQUEST_URI')) != '/' )
{
	$blog_base_path = dirname(getenv('REQUEST_URI')).'/';
}

// LANGUAGES
if( !@include( 'languages/'. $_GET['language'] . '.bit' ) )
{
	$_GET['language'] = 'en_US';
	require( 'languages/en_US.bit' );
}

// ============================================================================
//	POST
// ============================================================================

	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		mkdir('content/private',		0777, true);
		mkdir('content/private/plugins',0777, true);
		mkdir('content/public',			0777, true);
		mkdir('content/public/upload',	0777, true);
		mkdir('content/public/posts',	0777, true);
		mkdir('content/public/comments',0777, true);

		// Config.xml
		$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
		$xml .= '<config>';
		$xml .= '</config>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$obj->addChild('name',					$_POST['name']);
		$obj->addChild('slogan',				$_POST['slogan']);
		$obj->addChild('footer',				$_LANG['POWERED_BY_NIBBLEBLOG']);
		$obj->addChild('about',					'');
		$obj->addChild('language',				$_GET['language']);
		$obj->addChild('timezone',				'UTC');
		$obj->addChild('theme',					'clean');
		$obj->addChild('url',					$_POST['url']);
		$obj->addChild('path',					$_POST['path']);
		$obj->addChild('items_rss',				'8');
		$obj->addChild('items_page',			'4');
		$obj->addChild('timestamp_format',		'%d %B, %Y');
		$obj->addChild('advanced_post_options',	'0');
		$obj->addChild('locale',				$_GET['language']);
		$obj->addChild('friendly_urls',			0);
		$obj->addChild('enable_wysiwyg',		1);

		$obj->addChild('img_resize',			1);
		$obj->addChild('img_resize_width',		880);
		$obj->addChild('img_resize_height',		600);
		$obj->addChild('img_resize_option',		'auto');

		$obj->addChild('img_thumbnail',			1);
		$obj->addChild('img_thumbnail_width',	190);
		$obj->addChild('img_thumbnail_height',	190);
		$obj->addChild('img_thumbnail_option',	'landscape');

		$obj->asXml( FILE_XML_CONFIG );

		// categories.xml
		$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
		$xml .= '<categories autoinc="3">';
		$xml .= '</categories>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$node = $obj->addChild('category', '');
		$node->addAttribute('id',0);
		$node->addAttribute('name', $_LANG['UNCATEGORIZED']);
		$node = $obj->addChild('category', '');
		$node->addAttribute('id',1);
		$node->addAttribute('name', $_LANG['MUSIC']);
		$node = $obj->addChild('category', '');
		$node->addAttribute('id',2);
		$node->addAttribute('name', $_LANG['VIDEOS']);
		$obj->asXml( FILE_XML_CATEGORIES );

		// comments.xml
		$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
		$xml .= '<comments autoinc="0">';
		$xml .= '</comments>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$node = $obj->addChild('spam', '');
		$obj->asXml( FILE_XML_COMMENTS );

		// post.xml
		$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
		$xml .= '<post autoinc="1">';
		$xml .= '</post>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$node = $obj->addChild('sticky', '');
		$obj->asXml( FILE_XML_POST );

		// syslog.xml
		$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xml .= '<syslog>';
		$xml .= '</syslog>';
		$obj = new NBXML($xml, 0, FALSE, '', FALSE);
		$obj->addChild('login', '');
		$obj->asXml( FILE_XML_SYSLOG );

		// shadow.php
		$new_salt = $_TEXT->random_text(11);
		$new_hash = $_CRYPT->get_hash($_POST['password'],$new_salt);
		$text = '<?php $_USER[0]["uid"] = "0"; $_USER[0]["username"] = "'.$_POST['username'].'"; $_USER[0]["password"] = "'.$new_hash.'"; $_USER[0]["salt"] = "'.$new_salt.'"; $_USER[0]["email"] = "'.$_POST['email'].'"; ?>';
		$file = fopen(FILE_SHADOW, 'w');
		fputs($file, $text);
		fclose($file);

		// keys.php
		$key1 = $_CRYPT->get_hash($_TEXT->random_text(11));
		$key2 = $_CRYPT->get_hash($_TEXT->random_text(11));
		$key3 = $_CRYPT->get_hash($_TEXT->random_text(11));
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
		$_DB_POST->add( array('id_user'=>0, 'id_cat'=>0, 'type'=>'simple', 'description'=>$_LANG['WELCOME_POST_TITLE'], 'title'=>$_LANG['WELCOME_POST_TITLE'], 'content'=>$content, 'allow_comments'=>'1', 'sticky'=>'0') );

		$installation_complete = true;
	}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Nibbleblog Installer</title>

	<script src="./admin/js/jquery/jquery.js"></script>

	<style type="text/css">
		body {
			font-family: arial,sans-serif;
			background-color: #FFF;
			margin: 0;
			padding: 0;
			font-size: 0.875em;
			color: #616161;
		}

		#container {
			background: none repeat scroll 0 0 #F9F9F9;
			border: 1px solid #EBEBEB;
			border-radius: 3px 3px 3px 3px;
			margin: 50px auto;
			max-width: 800px;
			padding: 20px 30px;
			width: 60%;
		}

		h1 {

		}

		h2 {
			color: #339900;
		}

		a {
			color: #3C6EB4;
			cursor: pointer;
			text-decoration: none;
		}

		a:hover {
			text-decoration: underline;
		}

		a.lang {
			float: right;
			font-size: 12px;
			margin-left: 8px;
			text-decoration:underline;
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
			padding: 8px;
			outline:none;
			resize: none;
			margin-bottom: 10px;
		}

		label {
			color: #333;
			margin-bottom:2px;
			display:block;

		}

		input[type="submit"] {
			padding: 3px 20px;
		}

		footer {
			margin: 30px 0;
			border-top: 1px dotted #ccc;
			font-size:13px;
		}
		div.lang {
			margin-right: -20px;
			margin-top: -10px;
			overflow: auto;
		}
</style>

</head>
<body>

	<div id="container">

		<header>
			<div class="lang">
			<?php
				if(!$installation_complete)
				{
					foreach( $languagues as $key=>$value)
						echo '<a class="lang" href="./install.php?language='.$key.'">'.$value.'</a>';
				}
			?>
			</div>
			<?php echo $_HTML->h1( array('content'=>$_LANG['WELCOME_TO_NIBBLEBLOG']) ); ?>
		</header>

		<noscript>
		<section id="javascript_fail">
			<h2>Javascript</h2>
			<p><?php echo $_LANG['PLEASE_ENABLE_JAVASCRIPT_IN_YOUR_BROWSER'] ?></p>
		</section>
		</noscript>

		<section id="complete">
			<?php
				echo $_HTML->h2( array('content'=>$_LANG['INSTALLATION_COMPLETE']) );
				echo $_HTML->p( array('content'=>$_LANG['INSTALLATION_LINE1']) );
				echo $_HTML->p( array('content'=>$_LANG['INSTALLATION_LINE2']) );
				echo $_HTML->p( array('content'=>$_LANG['INSTALLATION_LINE3'].' <a href="./admin.php">'.$blog_address.$blog_base_path.'admin.php</a>') );
				echo $_HTML->p( array('content'=>$_LANG['INSTALLATION_LINE4'].' <a href="./">'.$blog_address.$blog_base_path.'</a>') );
				echo $_HTML->p( array('content'=>$_LANG['INSTALLATION_LINE5'].' <a href="http://forum.nibbleblog.com">http://forum.nibbleblog.com</a>') );
			?>
		</section>

		<section id="dependencies">
			<h2><?php echo $_LANG['DEPENDENCIES'] ?></h2>
			<?php
				// PHP MODULE DOM
				echo $_HTML->div_open( array('class'=>'dependency') );
					echo $_HTML->link( array('class'=>'description', 'content'=>$_LANG['PHP_VERSION'].' > 5.2', 'href'=>'http://www.php.net', 'target'=>'_blank') );

					if( version_compare(phpversion(), '5.2', '>') )
					{
						echo $_HTML->div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo $_HTML->div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo $_HTML->div_close();

				echo $_HTML->div_open( array('class'=>'dependency') );
					echo $_HTML->link( array('class'=>'description', 'content'=>$_LANG['PHP_MODULE'].' - DOM', 'href'=>'http://www.php.net/manual/en/book.dom.php', 'target'=>'_blank') );

					if( in_array('dom', $php_modules) )
					{
						echo $_HTML->div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo $_HTML->div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo $_HTML->div_close();

				echo $_HTML->div_open( array('class'=>'dependency') );
					echo $_HTML->link( array('class'=>'description', 'content'=>$_LANG['PHP_MODULE'].' - SimpleXML', 'href'=>'http://ar2.php.net/manual/en/book.simplexml.php', 'target'=>'_blank') );

					if( in_array('SimpleXML', $php_modules) )
					{
						echo $_HTML->div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo $_HTML->div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo $_HTML->div_close();

				echo $_HTML->div_open( array('class'=>'dependency') );
					echo $_HTML->link( array('class'=>'description', 'content'=>$_LANG['WRITING_TEST_ON_CONTENT_DIRECTORY'], 'href'=>'http://forum.nibbleblog.com', 'target'=>'_blank') );

					if( $writing_test )
					{
						echo $_HTML->div( array('class'=>'status_pass', 'content'=>$_LANG['PASS']) );
					}
					else
					{
						$dependencies = false;
						echo $_HTML->div( array('class'=>'status_fail', 'content'=>$_LANG['FAIL']) );
					}

				echo $_HTML->div_close();
			?>
		</section>

		<section id="configuration">
			<h2><?php echo $_LANG['CONFIGURATION'] ?></h2>
			<?php
				echo $_HTML->form_open( array('id'=>'js_form', 'name'=>'form', 'method'=>'post') );

					echo $_HTML->label( array('content'=>$_LANG['BLOG_TITLE']) );
					echo $_HTML->input( array('id'=>'js_name', 'name'=>'name', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254') );

					echo $_HTML->label( array('content'=>$_LANG['BLOG_SLOGAN']) );
					echo $_HTML->input( array('id'=>'js_slogan', 'name'=>'slogan', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254') );

					echo $_HTML->label( array('content'=>$_LANG['ADMINISTRATOR_USERNAME'].'*') );
					echo $_HTML->input( array('id'=>'js_username', 'name'=>'username', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254') );

					echo $_HTML->label( array('content'=>$_LANG['ADMINISTRATOR_PASSWORD'].'*') );
					echo $_HTML->input( array('id'=>'js_password', 'name'=>'password', 'type'=>'text', 'autocomplete'=>'off', 'maxlength'=>'254') );

					echo $_HTML->div_open( array('hidden'=>!isset($_GET['expert'])) );
						echo $_HTML->label( array('content'=>$_LANG['ADMINISTRATOR_EMAIL']) );
						echo $_HTML->input( array('name'=>'email', 'type'=>'text', 'autocomplete'=>'off') );

						echo $_HTML->label( array('content'=>$_LANG['BLOG_ADDRESS']) );
						echo $_HTML->input( array('name'=>'url', 'type'=>'text', 'value'=>$blog_address, 'autocomplete'=>'off') );

						echo $_HTML->label( array('content'=>$_LANG['BLOG_BASE_PATH']) );
						echo $_HTML->input( array('name'=>'path', 'type'=>'text', 'value'=>$blog_base_path, 'autocomplete'=>'off') );
					echo $_HTML->div_close();

					echo $_HTML->input( array('type'=>'submit', 'value'=>$_LANG['INSTALL']) );

				echo $_HTML->form_close();
			?>
		</section>

		<footer>
			<p><a href="http://nibbleblog.com">Nibbleblog <?php echo NIBBLEBLOG_VERSION ?> "<?php echo NIBBLEBLOG_NAME ?>"</a> | Copyright (2009 - 2012) + GPL v3 | Developed by Diego Najar | <?php echo $_HTML->link( array('content'=>$_LANG['EXPERT_MODE'], 'href'=>'./install.php?expert=true&language='.$_GET['language']) ) ?></p>
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

		$("form").submit(function(e){
			var username = $("#js_username");
			var password = $("#js_password");

			username.css("background-color", "");
			password.css("background-color", "");

			if(username.attr("value").length<2)
			{
				username.css("background-color", "#F9EDBE");
				return false;
			}

			if(password.attr("value").length<2)
			{
				password.css("background-color", "#F9EDBE");
				return false;
			}

			return true;
		});

	});
	</script>

</body>
</html>
