<?php

/*
* Nibbleblog -
* http://www.nibbleblog.com
* Author Diego Najar

* All Nibbleblog code is released under the GNU General Public License.
* See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_COMMENTS {

/*
======================================================================================
VARIABLES
======================================================================================
*/
	public $file; 				// Contains the link to XML file
	public $xml; 				// Contains the object

	private $files;
	private $files_count;

	private $last_insert_id;

	private $settings;

/*
======================================================================================
CONSTRUCTORS
======================================================================================
*/
	function DB_COMMENTS($file, $settings)
	{
		if(file_exists($file))
		{
			$this->file = $file;

			$this->settings = $settings;

			$this->last_insert_id = max($this->get_autoinc() - 1, 0);

			$this->files = array();

			$this->files_count = 0;

			$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);
		}
	}
/*
======================================================================================
PUBLIC METHODS
======================================================================================
*/
	public function savetofile()
	{
		return( $this->xml->asXML($this->file) );
	}

	public function get_last_insert_id()
	{
		return( $this->last_insert_id );
	}

	/*
	 * Return the COMMENT ID
	 *
	 * $args = array('author_ip', 'author_email', 'author_name', 'content', 'id_post', 'type')
	 *
	 */
	public function add($args)
	{
		global $Login;

		// Template
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xml .= '<comment>';
		$xml .= '</comment>';

		// Object
		$new_obj = new NBXML($xml, 0, FALSE, '', FALSE);

		// Time - UTC=0
		$time_unix = Date::unixstamp();

		// Time for Filename
		$time_filename = Date::format_gmt($time_unix, 'Y.m.d.H.i.s');

		// Encrypt the user IP and Email
		include(FILE_KEYS);
		$user_ip = Crypt::encrypt($args['author_ip'], $_KEYS[1]);
		$user_email = Crypt::encrypt($args['author_email'], $_KEYS[1]);

		$new_obj->addChild('author_name',	$args['author_name']);
		$new_obj->addChild('content',		$args['content']);

		$new_obj->addChild('author_email',	$user_email);
		$new_obj->addChild('author_ip',		$user_ip);

		$new_obj->addChild('pub_date',		$time_unix);
		$new_obj->addChild('highlight',		'0');

		// Last insert ID
		$new_id = $this->last_insert_id = $this->get_autoinc();

		// User ID
		if($Login->is_logged())
		{
			$id_user = $Login->get_user_id();
		}
		else
		{
			$id_user = 'NULL';
		}

		// Filename for new post
		$filename = $new_id . '.' . $args['id_post'] . '.' . $id_user . '.' . $args['type'] . '.' . $time_filename . '.xml';

		// Save to file
		if( $new_obj->asXml( PATH_COMMENTS . $filename ) )
		{
			// Increment the AutoINC
			$this->set_autoinc(1);

			// Save config file
			$this->savetofile();

			return $new_id;
		}

		return false;
	}

	public function get($args)
	{
		$this->set_file($args['id']);

		if($this->files_count > 0)
		{
			return( $this->get_items( $this->files[0] ) );
		}

		return false;
	}

	public function get_list_by_post($args)
	{
		$this->set_files_by_post($args['id_post']);

		$tmp_array = array();
		foreach($this->files as $file)
		{
			array_push( $tmp_array, $this->get_items( $file ) );
		}

		return($tmp_array);
	}

	public function get_list_by_page($args)
	{
		// Set the list of comments
		$this->set_files();

		if($this->files_count > 0)
		{
			return( $this->get_list_by($args['page_number'], $args['amount']) );
		}

		return array();
	}

	public function get_last($amount)
	{
		$this->set_files();

		$tmp_array = array();

		$total = min($amount, $this->files_count);

		for($i = 0; $i < $total; $i++)
		{
			array_push( $tmp_array, $this->get_items( $this->files[$i] ) );
		}

		return($tmp_array);
	}

	public function delete($args)
	{
		$this->set_file( $args['id'] );

		if($this->files_count > 0)
		{
			return(unlink( PATH_COMMENTS . $this->files[0] ));
		}

		return(false);
	}

	public function delete_all_by_post($args)
	{
		$this->set_files_by_post($args['id_post'], '*');

		foreach($this->files as $file)
		{
			unlink( PATH_COMMENTS . $file );
		}
	}

	public function get_count()
	{
		return($this->files_count);
	}

	public function get_settings()
	{
		$tmp_array = array();
		$tmp_array['comments_enabled'] 		= (int) $this->xml->getChild('comments_enabled');
		$tmp_array['monitor_enable'] 		= (int) $this->xml->getChild('monitor_enable');
		$tmp_array['monitor_api_key'] 		= (string) $this->xml->getChild('monitor_api_key');
		$tmp_array['monitor_spam_control'] 	= (float) $this->xml->getChild('monitor_spam_control');
		$tmp_array['monitor_auto_delete'] 	= (float) $this->xml->getChild('monitor_auto_delete');
		$tmp_array['sanitize'] 				= (int) $this->xml->getChild('sanitize');
		$tmp_array['moderate'] 				= (int) $this->xml->getChild('moderate');
		$tmp_array['disqus_shortname']		= $this->xml->getChild('disqus_shortname');
		$tmp_array['facebook_appid']		= $this->xml->getChild('facebook_appid');

		return($tmp_array);
	}

	public function set_settings($args)
	{
		foreach($args as $name=>$value)
		{
			$this->xml->setChild($name, $value);
		}

		return(true);
	}

	public function approve($args)
	{
		return($this->rename_by_position($args['id'], 3, 'NULL'));
	}

	public function unapprove($args)
	{
		return($this->rename_by_position($args['id'], 3, 'unapprove'));
	}

	public function spam($args)
	{
		return($this->rename_by_position($args['id'], 3, 'spam'));
	}

/*
======================================================================================
PRIVATE METHODS
======================================================================================
*/
	private function rename_by_position($id, $position, $string)
	{
		$this->set_file($id);

		// File not found
		if($this->files_count == 0)
		{
			return(false);
		}

		$filename = $this->files[0];

		$explode = explode('.', $filename);
		$explode[$position] = $string;
		$implode = implode('.', $explode);

		return( rename(PATH_COMMENTS.$filename, PATH_COMMENTS.$implode) );
	}

	private function get_autoinc()
	{
		return( (int) $this->xml['autoinc'] );
	}

	private function set_autoinc($value = 0)
	{
		$this->xml['autoinc'] = $value + $this->get_autoinc();
	}

	private function set_file($id)
	{
		$this->files = Filesystem::ls(PATH_COMMENTS, $id.'.*.*.*.*.*.*.*.*.*', 'xml', false, false, true);
		$this->files_count = count( $this->files );
	}

	// setea los parametros de la clase
	// obtiene todos los archivos post
	private function set_files()
	{
		$this->files = Filesystem::ls(PATH_COMMENTS, '*', 'xml', false, false, true);
		$this->files_count = count( $this->files );
	}

	// Setea los comentarios de un post en particular
	// File name: IDComment.IDPost.IDUser.IDOther.YYYY.MM.DD.HH.mm.ss.xml
	private function set_files_by_post($id_post, $type='NULL')
	{
		$this->files = Filesystem::ls(PATH_COMMENTS, '*.'.$id_post.'.*.'.$type.'.*.*.*.*.*.*', 'xml', false, true, true);
		$this->files_count = count( $this->files );
	}

	private function get_list_by($page_number, $amount)
	{
		$init = (int) $amount * $page_number;
		$end  = (int) min( ($init + $amount - 1), $this->files_count - 1 );

		$outrange = $init<0 ? true : $init > $end;

		$tmp_array = array();

		if( !$outrange )
		{
			for($init; $init <= $end; $init++)
			{
				array_push( $tmp_array, $this->get_items( $this->files[$init] ) );
			}
		}

		return( $tmp_array );
	}

	// Return the items from a comment
	// File name: IDComment.IDPost.IDUser.NULL.YYYY.MM.DD.HH.mm.ss.xml
	private function get_items($file)
	{
		$xml = new NBXML(PATH_COMMENTS . $file, 0, TRUE, '', FALSE);

		$file_info = explode('.', $file);

		include(FILE_KEYS);
		$user_ip = Crypt::decrypt((string) $xml->getChild('author_ip'), $_KEYS[1]);
		$user_email = Crypt::decrypt((string) $xml->getChild('author_email'), $_KEYS[1]);

		$tmp_array = array();

		$tmp_array['filename']			= (string) $file;

		$tmp_array['id']				= (int) $file_info[0];
		$tmp_array['id_post']			= (int) $file_info[1];
		$tmp_array['id_user']			= (int) $file_info[2];
		$tmp_array['type']				= (string) $file_info[3];

		$tmp_array['author_email']		= $user_email;
		$tmp_array['author_ip']			= $user_ip;

		$tmp_array['author_name']		= (string) $xml->getChild('author_name');
		$tmp_array['content']			= (string) $xml->getChild('content');
		$tmp_array['pub_date_unix']		= (string) $xml->getChild('pub_date');
		$tmp_array['highlight']			= (bool) ((int)$xml->getChild('content')==1);

		$tmp_array['pub_date'] = Date::format($tmp_array['pub_date_unix'], $this->settings['timestamp_format']);

		return( $tmp_array );
	}

} // END Class

?>
