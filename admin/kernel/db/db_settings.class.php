<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_SETTINGS {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to XML file
		public $obj_xml; 			// Contains the object

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
		// Returns TRUE if the file was written successfully and FALSE otherwise.
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

			// Images
			$tmp_array['img_resize']				= (int) $this->obj_xml->getChild('img_resize') == 1;
			$tmp_array['img_resize_width']			= (int) $this->obj_xml->getChild('img_resize_width');
			$tmp_array['img_resize_height']			= (int) $this->obj_xml->getChild('img_resize_height');
			$tmp_array['img_resize_option']			= (string) $this->obj_xml->getChild('img_resize_option');

			$tmp_array['img_thumbnail']				= (int) $this->obj_xml->getChild('img_thumbnail') == 1;
			$tmp_array['img_thumbnail_width']		= (int) $this->obj_xml->getChild('img_thumbnail_width');
			$tmp_array['img_thumbnail_height']		= (int) $this->obj_xml->getChild('img_thumbnail_height');
			$tmp_array['img_thumbnail_option']		= (string) $this->obj_xml->getChild('img_thumbnail_option');

			// Notifications
			$tmp_array['notification_comments']		= (int) $this->obj_xml->getChild('notification_comments') == 1;
			$tmp_array['notification_session_fail']	= (int) $this->obj_xml->getChild('notification_session_fail') == 1;
			$tmp_array['notification_session_start']= (int) $this->obj_xml->getChild('notification_session_start') == 1;
			$tmp_array['notification_email_to']		= (string) $this->obj_xml->getChild('notification_email_to');
			$tmp_array['notification_email_from']	= (string) $this->obj_xml->getChild('notification_email_from');

			// Regional
			$tmp_array['locale']					= (string) $this->obj_xml->getChild('locale');

			return($tmp_array);
		}

		public function set($args)
		{
			foreach($args as $name=>$value)
			{
				$this->obj_xml->setChild($name, $value);
			}

			return(true);
		}

		public function get_language()
		{
			return((string) $this->obj_xml->getChild('language'));
		}

		public function get_base_path()
		{
			return((string) $this->obj_xml->getChild('path'));
		}

		public function get_languages()
		{
			$tmp_array = array();

			$files = Filesystem::ls(PATH_LANGUAGES, '*', 'bit', false, false, false);

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
			$tmp_array = array();

			$files = Filesystem::ls(PATH_PLUGINS, '*', 'bit', true, false, false);

			return($files);
		}

		public function get_plugins_installed()
		{
			$tmp_array = array();

			$files = Filesystem::ls(PATH_PLUGINS_DB, '*', 'bit', true, false, false);

			return($files);
		}

		public function get_themes()
		{
			$tmp_array = array();

			$files = Filesystem::ls(PATH_THEMES, '*', 'bit', true, false, false);

			return($files);
		}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/


} // END Class

?>