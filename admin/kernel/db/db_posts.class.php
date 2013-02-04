<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_POSTS {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to XML file
		public $obj_xml; 			// Contains the object

		private $files;
		private $files_count;

		private $last_insert_id;

		private $settings;

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_POSTS($file, $settings)
		{
			$this->file_xml = $file;

			if(file_exists($this->file_xml))
			{
				$this->settings = $settings;

				$this->last_insert_id = max($this->get_autoinc() - 1, 0);

				$this->files = array();
				$this->files_count = 0;

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

		public function get_last_insert_id()
		{
			return( $this->last_insert_id );
		}

		// Return the POST ID
		public function add($args)
		{
			// Template
			$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			$xml .= '<post>';
			$xml .= '</post>';

			// Object
			$new_obj = new NBXML($xml, 0, FALSE, '', FALSE);

			// Time - UTC=0
			$time_unix = Date::unixstamp();

			// Time for Filename
			$time_filename = Date::format_gmt($time_unix, 'Y.m.d.H.i.s');

			// Default elements
			$new_obj->addChild('type',				$args['type']);
			$new_obj->addChild('title',				$args['title']);
			$new_obj->addChild('content',			$args['content']);
			$new_obj->addChild('description',		$args['description']);
			$new_obj->addChild('allow_comments',	$args['allow_comments']);
			$new_obj->addChild('slug',				$args['slug']);

			$new_obj->addChild('pub_date',			$time_unix);
			$new_obj->addChild('mod_date',			'0');
			$new_obj->addChild('visits',			'0');

			// Video post
			if(isset($args['video']))
			{
				$new_obj->addChild('video', $args['video']);
			}
			// Quote post
			elseif(isset($args['quote']))
			{
				$new_obj->addChild('quote', $args['quote']);
			}

			// Last insert ID
			$new_id = $this->last_insert_id = $this->get_autoinc();

			// Mode, draft, published
			if(isset($args['mode']) && ($args['mode']=='draft'))
			{
				$mode = 'draft';
			}
			else
			{
				$mode = 'NULL';
			}

			// Filename for new post
			$filename = $new_id . '.' . $args['id_cat'] . '.' . $args['id_user'] . '.' . $mode . '.' . $time_filename . '.xml';

			// Save to file
			if( $new_obj->asXml(PATH_POSTS.$filename) )
			{
				// Increment the AutoINC
				$this->set_autoinc(1);

				// Save config file post.xml
				$this->savetofile();
			}
			else
			{
				return(false);
			}

			return($new_id);
		}

		public function set($args)
		{
			if(!$this->set_file($args['id']))
			{
				return(false);
			}

			$new_obj = new NBXML(PATH_POSTS.$this->files[0], 0, TRUE, '', FALSE);

			$new_obj->setChild('title', 			$args['title']);
			$new_obj->setChild('content', 			$args['content']);
			$new_obj->setChild('description', 		$args['description']);
			$new_obj->setChild('allow_comments', 	$args['allow_comments']);
			$new_obj->setChild('slug',				$args['slug']);
			$new_obj->setChild('mod_date', 			Date::unixstamp());

			if(isset($args['quote']))
			{
				$new_obj->setChild('quote', $args['quote']);
			}

			// ------------------------------------
			// Filename
			// ------------------------------------
			$file = explode('.', $this->files[0]);

			// Category
			$file[1] = $args['id_cat'];

			// Draft / Published
			if(isset($args['mode']) && ($args['mode']=='draft'))
			{
				$file[3] = 'draft';
			}
			else
			{
				$file[3] = 'NULL';
			}

			// Publish date
			$file[4] = Date::format_gmt($args['unixstamp'], 'Y');
			$file[5] = Date::format_gmt($args['unixstamp'], 'm');
			$file[6] = Date::format_gmt($args['unixstamp'], 'd');
			$file[7] = Date::format_gmt($args['unixstamp'], 'H');
			$file[8] = Date::format_gmt($args['unixstamp'], 'i');
			$file[9] = Date::format_gmt($args['unixstamp'], 's');

			// Implode the filename
			$filename = implode(".", $file);

			// Delete the old post
			$this->remove( array('id'=>$args['id']) );

			// Save the new post
			return($new_obj->asXml(PATH_POSTS.$filename));
		}

		public function change_category($args)
		{
			return( $this->rename_by_position($args['id'], 1, $args['id_cat']) );
		}

		public function remove($args)
		{
			$this->set_file($args['id']);

			if($this->files_count > 0)
			{
				return(unlink( PATH_POSTS . $this->files[0] ));
			}
			else
			{
				return(false);
			}

			return(true);
		}

		public function get($args)
		{
			$this->set_file($args['id']);

			if($this->files_count > 0)
				return( $this->get_items( $this->files[0] ) );
			else
				return( array() );
		}

		public function get_list_by_page($args)
		{
			// Set list of posts published
			$this->set_files_by_published();

			if($this->files_count > 0)
				return( $this->get_list_by($args['page'], $args['amount']) );
			else
				return( array() );
		}

		public function get_list_by_page_more_drafts($args)
		{
			// Set list of posts drafts and published
			$this->set_files();

			if($this->files_count > 0)
				return( $this->get_list_by($args['page'], $args['amount']) );
			else
				return( array() );
		}

		public function get_list_by_category($args)
		{
			// Set list of posts by category
			$this->set_files_by_category($args['id_cat']);

			if($this->files_count > 0)
				return( $this->get_list_by($args['page'], $args['amount']) );
			else
				return( array() );
		}

		public function get_count()
		{
			return( $this->files_count );
		}

		public function get_autoinc()
		{
			return( (int) $this->obj_xml['autoinc'] );
		}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/
		private function rename($id, $rename)
		{
			$this->set_file($id);

			// File not found
			if($this->files_count == 0)
			{
				return(false);
			}

			$filename = $this->files[0];

			return( rename(PATH_POSTS.$filename, PATH_POSTS.$rename) );
		}

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

			return( rename(PATH_POSTS.$filename, PATH_POSTS.$implode) );
		}

		private function set_autoinc($value = 0)
		{
			$this->obj_xml['autoinc'] = $value + $this->get_autoinc();
		}

		// Get only the post file
		private function set_file($id)
		{
			$this->files = Filesystem::ls(PATH_POSTS, $id.'.*.*.*.*.*.*.*.*.*', 'xml', false, false, false);
			$this->files_count = count( $this->files );

			// Post not found
			if($this->files_count == 0)
			{
				return(false);
			}

			return(true);
		}

		// Get all files, drafts and published
		private function set_files()
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Get all files, only published
		private function set_files_by_published()
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*.*.*.NULL.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Get all files, by category
		private function set_files_by_category($id_cat)
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*.'.$id_cat.'.*.NULL.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Devuelve los items de un post
		// File name: ID_POST.ID_CATEGORY.ID_USER.NULL.YYYY.MM.DD.HH.mm.ss.xml
		private function get_items($file)
		{
			$obj_xml = new NBXML(PATH_POSTS . $file, 0, TRUE, '', FALSE);

			$file_info = explode('.', $file);

			$content = (string) $obj_xml->getChild('content');
			$tmp_content = explode("<!-- pagebreak -->", $content);

			$tmp_array = array('read_more'=>false);

			$tmp_array['filename']			= (string) $file;

			$tmp_array['id']				= (int) $file_info[0];
			$tmp_array['id_cat']			= (int) $file_info[1];
			$tmp_array['id_user']			= (int) $file_info[2];
			$tmp_array['mode']				= (string) $file_info[3];
			$tmp_array['draft']				= (bool) ($file_info[3]=='draft');
			$tmp_array['visits']			= (int) $obj_xml->getChild('visits');

			$tmp_array['type']				= (string) $obj_xml->getChild('type');
			$tmp_array['title']				= (string) $obj_xml->getChild('title');
			$tmp_array['description']		= (string) $obj_xml->getChild('description');
			$tmp_array['slug']				= (string) $obj_xml->getChild('slug');

			$tmp_array['pub_date_unix']		= (string) $obj_xml->getChild('pub_date');
			$tmp_array['mod_date_unix']		= (string) $obj_xml->getChild('mod_date');

			$tmp_array['allow_comments']	= (bool) ((int)$obj_xml->getChild('allow_comments'))==1;

			// DATE
			$tmp_array['pub_date'] = Date::format($tmp_array['pub_date_unix'], $this->settings['timestamp_format']);
			$tmp_array['mod_date'] = Date::format($tmp_array['mod_date_unix'], $this->settings['timestamp_format']);

			// CONTENT
			$tmp_array['content'][0] = $content;

			$tmp_array['content'][1] = $tmp_content[0];

			if( isset($tmp_content[1]) )
			{
				$tmp_array['content'][2] = $tmp_content[1];
				$tmp_array['read_more'] = true;
			}

			// POST TYPE
			if($tmp_array['type']=='video')
			{
				$tmp_array['video']			= (string) $obj_xml->getChild('video');
			}
			elseif($tmp_array['type']=='quote')
			{
				$tmp_array['quote']			= (string) $obj_xml->getChild('quote');
			}

			// FRIENDLY URLS
			if( $this->settings['friendly_urls'] )
			{
				if(  Text::not_empty($tmp_array['slug']) )
				{
					$slug = $tmp_array['slug'];
				}
				else
				{
					if( Text::not_empty($tmp_array['title']))
					{
						$slug = Text::clean_url($tmp_array['title']);
					}
					else
					{
						$slug = $tmp_array['type'];
					}
				}

				$tmp_array['permalink'] = HTML_PATH_ROOT.'post-'.$tmp_array['id'].'/'.$slug;
			}
			else
			{
				$tmp_array['permalink'] = HTML_PATH_ROOT.'index.php?controller=post&action=view&id_post='.$tmp_array['id'];
			}

			return( $tmp_array );
		}

		private function get_list_by($page_number, $post_per_page)
		{
			$init = (int) $post_per_page * $page_number;
			$end  = (int) min( ($init + $post_per_page - 1), $this->files_count - 1 );
			$outrange = $init > $end;

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

} // END Class

?>
