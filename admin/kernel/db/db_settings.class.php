<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar
 
 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_SETTINGS {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to the blog_config.xml file
		public $obj_xml; 				// Contains the object of the blog_config.xml file

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_SETTINGS($file)
		{
			$this->file_xml = $file;

			if (file_exists($this->file_xml))
			{
				$this->obj_xml = new NBXML($this->file_xml, 0, TRUE, '', FALSE);
			}
			else
			{
				return(false);
			}

			return(true);
		}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
		public function savetofile()
		{
			return( $this->obj_xml->asXML($this->file_xml) );
		}

		public function get()
		{
			$tmp_array = array();

			// General
			$tmp_array['name']						= (string) $this->obj_xml->getChild('name');
			$tmp_array['slogan']					= (string) $this->obj_xml->getChild('slogan');
			$tmp_array['about']						= (string) $this->obj_xml->getChild('about');
			$tmp_array['footer']					= (string) $this->obj_xml->getChild('footer');
			$tmp_array['language']					= (string) $this->obj_xml->getChild('language');

			// Theme
			$tmp_array['theme']						= (string) $this->obj_xml->getChild('theme');

			// Advanced
			$tmp_array['url']						= (string) $this->obj_xml->getChild('url');
			$tmp_array['path']						= (string) $this->obj_xml->getChild('path');
			$tmp_array['items_page']				= (string) $this->obj_xml->getChild('items_page');
			$tmp_array['items_rss']					= (string) $this->obj_xml->getChild('items_rss');
			$tmp_array['timezone']					= (string) $this->obj_xml->getChild('timezone');
			$tmp_array['timestamp_format']			= (string) $this->obj_xml->getChild('timestamp_format');
			$tmp_array['advanced_post_options']		= (int) $this->obj_xml->getChild('advanced_post_options') == 1;
			$tmp_array['friendly_urls']				= (int) $this->obj_xml->getChild('friendly_urls') == 1;
			$tmp_array['enable_wysiwyg']			= (int) $this->obj_xml->getChild('enable_wysiwyg') == 1;
			$tmp_array['locale']					= (string) $this->obj_xml->getChild('locale');

			return($tmp_array);
		}

		public function set_theme($args)
		{
			$this->obj_xml->setChild('theme',			$args['theme']);

			return(true);
		}

		public function set_general($args)
		{
			$this->obj_xml->setChild('name', 			$args['name']);
			$this->obj_xml->setChild('slogan',		 	$args['slogan']);
			$this->obj_xml->setChild('about', 			$args['about']);
			$this->obj_xml->setChild('footer', 			$args['footer']);
			$this->obj_xml->setChild('language', 		$args['language']);
			$this->obj_xml->setChild('locale', 			$args['language']);

			return(true);
		}

		public function set_advanced($args)
		{
			$this->obj_xml->setChild('url', 					$args['url']);
			$this->obj_xml->setChild('path',					$args['path']);
			$this->obj_xml->setChild('items_page', 				$args['items_page']);
			$this->obj_xml->setChild('items_rss', 				$args['items_rss']);
			$this->obj_xml->setChild('timezone', 				$args['timezone']);
			$this->obj_xml->setChild('timestamp_format',		$args['timestamp_format']);
			$this->obj_xml->setChild('advanced_post_options', 	$args['advanced_post_options']);
			$this->obj_xml->setChild('enable_wysiwyg', 			$args['enable_wysiwyg']);
			$this->obj_xml->setChild('friendly_urls', 			$args['friendly_urls']);
			$this->obj_xml->setChild('locale', 					$args['locale']);

			return(true);
		}

		public function get_language()
		{
			return((string) $this->obj_xml->getChild('language'));
		}
		
		public function get_wysiwyg()
		{
			return( (int)$this->obj_xml->getChild('enable_wysiwyg') == 1 );
		}

		public function get_base_path()
		{
			return((string) $this->obj_xml->getChild('path'));
		}

		public function get_languages()
		{
			global $_FS;
			global $_TEXT;

			$tmp_array = array();

			$files = $_FS->ls(PATH_LANGUAGES, '*', 'bit', false, false, false);

			foreach($files as $file)
			{
				include(PATH_LANGUAGES.$file);
				$iso = basename($file, '.bit');
				$native = $_LANG_CONFIG['DATA']['native'];
				$tmp_array[$iso] = ucwords($native);
			}

			return($tmp_array);
		}

		public function get_plugins_on_system()
		{
			global $_FS;

			$tmp_array = array();

			$files = $_FS->ls(PATH_PLUGINS, '*', 'bit', true, false, false);

			return($files);
		}

		public function get_plugins_installed()
		{
			global $_FS;

			$tmp_array = array();

			$files = $_FS->ls(PATH_PLUGINS_DB, '*', 'bit', true, false, false);

			return($files);
		}

		public function get_themes()
		{
			global $_FS;

			$tmp_array = array();

			$files = $_FS->ls(PATH_THEMES, '*', 'bit', true, false, false);

			return($files);
		}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/


} // END Class

?>
