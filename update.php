<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

/*
 * Versions support
 * v3.5.x
 * v3.6.x
 * v3.7.x
*/

define('UPDATER_VERSION', '4.0');

// =====================================================================
// Require
// =====================================================================
require('admin/boot/rules/1-fs_php.bit');
require('admin/boot/rules/98-constants.bit');

require(PATH_DB . 'nbxml.class.php');
require(PATH_DB . 'db_settings.class.php');
require(PATH_DB . 'db_posts.class.php');

require(PATH_HELPERS . 'html.class.php');
require(PATH_HELPERS . 'date.class.php');
require(PATH_HELPERS . 'text.class.php');
require(PATH_HELPERS . 'filesystem.class.php');

// =====================================================================
// DB
// =====================================================================
$_DB_SETTINGS	= new DB_SETTINGS( FILE_XML_CONFIG );

// =====================================================================
// Variables
// =====================================================================
$domain = getenv('HTTP_HOST');

$base_path = dirname(getenv('SCRIPT_NAME'));
if($base_path!='/')
	$base_path .='/';

$blog_address = 'http://'.$domain.$base_path;

$settings = $_DB_SETTINGS->get();

// =====================================================================
// Language
// =====================================================================
include(PATH_LANGUAGES.'en_US.bit');
include(PATH_LANGUAGES.$settings['language'].'.bit');

Date::set_timezone($settings['timezone']);

Date::set_locale($settings['locale']);

$translit_enable = isset($_LANG['TRANSLIT'])?$_LANG['TRANSLIT']:false;

?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Nibbleblog Updater <?php echo UPDATER_VERSION ?></title>

	<style type="text/css">
		body {
			font-family: arial,sans-serif;
			margin: 0;
			padding: 0;
			font-size: 62.5%;
			color: #555;
			background: url("admin/templates/easy4/css/img/grey.png") repeat rgba(0, 0, 0, 0);
		}

		#container {
			margin: 50px auto;
			max-width: 700px;
			padding: 20px 30px;
			width: 60%;
			box-shadow: 1px 0px 2px rgba(0, 0, 0, 0.08);

			background: rgba(255, 255, 255, 0.6);
			border: 1px solid #CCC;
			border-radius: 3px 3px 3px 3px;
			font-size: 1.4em;
		}

		h1 {
			margin: 0 0 20px 0;
			text-align: center;
			font-weight: normal;
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

		<section id="configuration">
			<?php

				function add_if_not($obj, $name, $value)
				{
					if(!$obj->is_set($name))
					{
						$obj->addChild($name, $value);
					}
				}


				// =====================================================
				// posts.xml
				// =====================================================
				$filenamepost = Text::replace('posts.xml', 'post.xml', FILE_XML_POSTS);

				if(file_exists($filenamepost))
				{
					$obj = new NBXML($filenamepost, 0, TRUE, '', FALSE);
					add_if_not($obj,'friendly','');

					if($obj->asXml( FILE_XML_POSTS ))
					{
						echo Html::p( array('class'=>'pass', 'content'=>'DB updated: '.FILE_XML_POSTS) );
						@unlink($filenamepost);
					}
					else
						echo Html::p( array('class'=>'pass', 'content'=>'FAIL - DB updated: '.FILE_XML_POSTS) );
				}

				// =====================================================
				// Posts
				// =====================================================
				$posts_files = Filesystem::ls(PATH_POSTS, '*', 'xml', false, false, false);

				$_DB_POST = new DB_POSTS( FILE_XML_POSTS );

				foreach($posts_files as $file_old)
				{
					$explode = explode('.', $file_old);

					$post = new NBXML(PATH_POSTS.$file_old, 0, TRUE, '', FALSE);

					// Generate the slug url
					$id_post = (int) $explode[1];
					$slug = Text::clean_url((string)$post->getChild('title'), '-', $translit_enable); ;
					$_DB_POST->slug($id_post, $slug);
					$_DB_POST->savetofile();

					if(count($explode)==11)
					{
						$unixstamp = (int)$post->getChild('pub_date');

						array_unshift($explode, $unixstamp);

						// Implode the filename
						$filename = implode('.', $explode);

						// Delete the old post
						unlink(PATH_POSTS.$file_old);

						// Save the new post
						$post->asXml(PATH_POSTS.$filename);

						echo Html::p( array('class'=>'pass', 'content'=>'File renamed: '.$file_old.' => '.$filename) );
					}
				}

				// =====================================================
				// notifications.xml
				// =====================================================
				if(!file_exists(FILE_XML_NOTIFICATIONS))
				{
					$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
					$xml .= '<notifications>';
					$xml .= '</notifications>';
					$obj = new NBXML($xml, 0, FALSE, '', FALSE);
					$obj->asXml( FILE_XML_NOTIFICATIONS );

					echo Html::p( array('class'=>'pass', 'content'=>'File created: '.FILE_XML_NOTIFICATIONS) );
				}

				// =====================================================
				// users.xml
				// =====================================================
				if(!file_exists(FILE_XML_USERS))
				{
					require(FILE_SHADOW);

					$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
					$xml .= '<users>';
					$xml .= '</users>';
					$obj = new NBXML($xml, 0, FALSE, '', FALSE);
					$node = $obj->addChild('user', '');
					$node->addAttribute('username', $_USER[0]["username"]);
					$node->addChild('id', 0);
					$node->addChild('session_fail_count', 0);
					$node->addChild('session_date', 0);
					$obj->asXml( FILE_XML_USERS );

					echo Html::p( array('class'=>'pass', 'content'=>'File created: '.FILE_XML_USERS) );
				}

				// =====================================================
				// tags.xml
				// =====================================================
				if(!file_exists(FILE_XML_TAGS))
				{
					$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
					$xml .= '<tags autoinc="0">';
					$xml .= '<list></list>';
					$xml .= '<links></links>';
					$xml .= '</tags>';
					$obj = new NBXML($xml, 0, FALSE, '', FALSE);
					$obj->asXml( FILE_XML_TAGS );

					echo Html::p( array('class'=>'pass', 'content'=>'File created: '.FILE_XML_TAGS) );
				}
				else
				{
    				// upgrade tag names to support spaces
    				$xml = file_get_contents( FILE_XML_TAGS );
    				$obj = new NBXML($xml, 0, FALSE, '', FALSE);
    				$count = 0;
    				foreach ($obj->{'list'}->children() as $tag)
    				{
    				    if ($tag->getAttribute('name_human') === '')
    				    {
        				   $tag->addAttribute('name_human', $tag->getAttribute('name'));
        				   $count = $count + 1;
    				    }
                    }
    				$obj->asXml( FILE_XML_TAGS );
    				if ($count > 0)
    				{
        				echo Html::p( array('class'=>'pass', 'content'=>'DB updated: '.FILE_XML_TAGS) );
    				}
				}

				// =====================================================
				// pages.xml
				// =====================================================
				if(!file_exists(FILE_XML_PAGES))
				{
					$xml  = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
					$xml .= '<pages autoinc="1">';
					$xml .= '<friendly></friendly>';
					$xml .= '</pages>';
					$obj = new NBXML($xml, 0, FALSE, '', FALSE);

					echo Html::p( array('class'=>'pass', 'content'=>'File created: '.FILE_XML_PAGES) );

					$obj->asXml( FILE_XML_PAGES );
				}

				// =====================================================
				// config.xml
				// =====================================================
				$obj = new NBXML(FILE_XML_CONFIG, 0, TRUE, '', FALSE);
				add_if_not($obj,'notification_comments',0);
				add_if_not($obj,'notification_session_fail',0);
				add_if_not($obj,'notification_session_start',0);
				add_if_not($obj,'notification_email_to','');
				add_if_not($obj,'notification_email_from','noreply@'.$domain);

				// SEO Options
				add_if_not($obj,'seo_site_title','');
				add_if_not($obj,'seo_site_description','');
				add_if_not($obj,'seo_keywords','');
				add_if_not($obj,'seo_robots','');
				add_if_not($obj,'seo_google_code','');
				add_if_not($obj,'seo_bing_code','');
				add_if_not($obj,'seo_author','');

				// Images
				add_if_not($obj,'img_resize_quality',100);
				add_if_not($obj,'img_thumbnail_quality',100);

				// Default page
				add_if_not($obj,'default_homepage',0);

				// Default path
				$obj->setChild('url', $blog_address);
				$obj->setChild('path', $base_path);

				if($obj->asXml( FILE_XML_CONFIG ))
					echo Html::p( array('class'=>'pass', 'content'=>'DB updated: '.FILE_XML_CONFIG) );
				else
					echo Html::p( array('class'=>'pass', 'content'=>'FAIL - DB updated: '.FILE_XML_CONFIG) );

				// =====================================================
				// comments.xml
				// =====================================================
				$obj = new NBXML(FILE_XML_COMMENTS, 0, TRUE, '', FALSE);
				add_if_not($obj,'comments_enabled',1);
				add_if_not($obj,'moderate',1);
				add_if_not($obj,'sanitize',1);
				add_if_not($obj,'monitor_enable',0);
				add_if_not($obj,'monitor_api_key','');
				add_if_not($obj,'monitor_spam_control','0.75');
				add_if_not($obj,'monitor_auto_delete',0);
				add_if_not($obj,'disqus_shortname','');
				add_if_not($obj,'facebook_appid','');
				$obj->asXml( FILE_XML_COMMENTS );
				echo Html::p( array('class'=>'pass', 'content'=>'DB updated: '.FILE_XML_COMMENTS) );

				// =====================================================
				// Categories
				// =====================================================
				$obj = new NBXML(FILE_XML_CATEGORIES, 0, TRUE, '', FALSE);
				$position = 0;

				foreach( $obj->children() as $children )
				{
					// Slug
					$name = utf8_decode((string)$children->attributes()->name);
					$slug = Text::clean_url($name, '-', $translit_enable);
					@$children->addAttribute('slug','');
					$children->attributes()->slug = utf8_encode($slug);

					// Position
					@$children->addAttribute('position','');
					$children->attributes()->position = utf8_encode($position);
					$position++;
				}

				$obj->asXml( FILE_XML_CATEGORIES );

				echo Html::p( array('class'=>'pass', 'content'=>'Categories updated...') );

				// =====================================================
				// Uninstall plugins
				// =====================================================
				$plugins = array('last_posts');
				foreach($plugins as $plugin)
				{
					$path = PATH_PLUGINS_DB.$plugin;

					if(file_exists($path.'/db.xml'))
					{
						$files = Filesystem::ls($path.'/', '*', '*');

						foreach($files as $file)
							unlink($path.'/'.$file);

						rmdir($path);

						echo Html::p( array('class'=>'pass', 'content'=>'Plugin '.$plugin.' uninstalled, plugin deprecated.') );
					}
				}

			?>
		</section>

		<footer>
			<p><a href="http://nibbleblog.com">Nibbleblog <?php echo NIBBLEBLOG_VERSION ?> "<?php echo NIBBLEBLOG_NAME ?>"</a> ©2009 - <?php echo date('Y') ?> | Developed by Diego Najar</p>
		</footer>

	</div>

</body>
</html>
