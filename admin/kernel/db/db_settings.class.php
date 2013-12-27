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
	public $file; 			// Contains the link to XML file
	public $xml; 			// Contains the object

/*
======================================================================================
CONSTRUCTORS
======================================================================================
*/
	function DB_SETTINGS($file)
	{
		if(file_exists($file))
		{
			$this->file = $file;

			$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);
		}
	}

/*
======================================================================================
PUBLIC METHODS
======================================================================================
*/
	// Returns TRUE if the file was written successfully and FALSE otherwise.
	public function savetofile()
	{
		return( $this->xml->asXML($this->file) );
	}

	public function get()
	{
		$tmp_array = array();

		// General
		$tmp_array['name']						= (string) $this->xml->getChild('name');
		$tmp_array['slogan']					= (string) $this->xml->getChild('slogan');
		$tmp_array['footer']					= (string) $this->xml->getChild('footer');
		$tmp_array['advanced_post_options']		= (int) $this->xml->getChild('advanced_post_options') == 1;

		// Advanced
		$tmp_array['url']						= (string) $this->xml->getChild('url');
		$tmp_array['path']						= (string) $this->xml->getChild('path');
		$tmp_array['items_page']				= (string) $this->xml->getChild('items_page');
		$tmp_array['items_rss']					= (string) $this->xml->getChild('items_rss');

		// Regional
		$tmp_array['language']					= (string) $this->xml->getChild('language');
		$tmp_array['timezone']					= (string) $this->xml->getChild('timezone');
		$tmp_array['timestamp_format']			= (string) $this->xml->getChild('timestamp_format');
		$tmp_array['locale']					= (string) $this->xml->getChild('locale');

		// Images
		$tmp_array['img_resize']				= (int) $this->xml->getChild('img_resize') == 1;
		$tmp_array['img_resize_width']			= (int) $this->xml->getChild('img_resize_width');
		$tmp_array['img_resize_height']			= (int) $this->xml->getChild('img_resize_height');
		$tmp_array['img_resize_quality']		= $this->xml->getChild('img_resize_quality');
		$tmp_array['img_resize_option']			= (string) $this->xml->getChild('img_resize_option');

		$tmp_array['img_thumbnail']				= (int) $this->xml->getChild('img_thumbnail') == 1;
		$tmp_array['img_thumbnail_width']		= (int) $this->xml->getChild('img_thumbnail_width');
		$tmp_array['img_thumbnail_height']		= (int) $this->xml->getChild('img_thumbnail_height');
		$tmp_array['img_thumbnail_quality']		= $this->xml->getChild('img_thumbnail_quality');
		$tmp_array['img_thumbnail_option']		= (string) $this->xml->getChild('img_thumbnail_option');

		// Theme
		$tmp_array['theme']						= (string) $this->xml->getChild('theme');

		// Notifications
		$tmp_array['notification_comments']		= (int) $this->xml->getChild('notification_comments') == 1;
		$tmp_array['notification_session_fail']	= (int) $this->xml->getChild('notification_session_fail') == 1;
		$tmp_array['notification_session_start']= (int) $this->xml->getChild('notification_session_start') == 1;
		$tmp_array['notification_email_to']		= (string) $this->xml->getChild('notification_email_to');
		$tmp_array['notification_email_from']	= (string) $this->xml->getChild('notification_email_from');

		// SEO
		$tmp_array['seo_site_title']			= (string) $this->xml->getChild('seo_site_title');
		$tmp_array['seo_site_description']		= (string) $this->xml->getChild('seo_site_description');
		$tmp_array['seo_keywords']				= (string) $this->xml->getChild('seo_keywords');
		$tmp_array['seo_robots']				= (string) $this->xml->getChild('seo_robots');
		$tmp_array['seo_google_code']			= (string) $this->xml->getChild('seo_google_code');
		$tmp_array['seo_bing_code']				= (string) $this->xml->getChild('seo_bing_code');
		$tmp_array['seo_author']				= (string) $this->xml->getChild('seo_author');
		$tmp_array['friendly_urls']				= (int) $this->xml->getChild('friendly_urls') == 1;

		// Default homepage
		$tmp_array['default_homepage']			= $this->xml->getChild('default_homepage');

		return($tmp_array);
	}

	public function set($args)
	{
		foreach($args as $name=>$value)
		{
			$this->xml->setChild($name, $value);
		}

		return(true);
	}

	public function get_language()
	{
		return((string) $this->xml->getChild('language'));
	}

	public function get_base_path()
	{
		return((string) $this->xml->getChild('path'));
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

	public function get_themes()
	{
		$tmp_array = array();

		$files = Filesystem::ls(PATH_THEMES, '*', 'bit', true, false, false);

		return $files;
	}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/


} // END Class

?>