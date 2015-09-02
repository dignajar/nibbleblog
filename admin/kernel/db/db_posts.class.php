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
========================================================================
	VARIABLES
========================================================================
*/
		public $file; 			// Contains the link to XML file
		public $xml; 			// Contains the object

		private $files;
		private $files_count;

		private $last_insert_id;

/*
========================================================================
	CONSTRUCTORS
========================================================================
*/
		function DB_POSTS($file)
		{
			if(file_exists($file))
			{
				$this->file = $file;

				$this->last_insert_id = max($this->get_autoinc() - 1, 0);

				$this->files = array();
				$this->files_count = 0;

				$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);
			}
		}

/*
========================================================================
	PUBLIC METHODS
========================================================================
*/
		/*
		 * Save the config file
		 *
		 */
		public function savetofile()
		{
			return $this->xml->asXML($this->file);
		}

		/*
		 * Return the last post id
		 *
		 */
		public function get_last_insert_id()
		{
			return( $this->last_insert_id );
		}

		/*
		 * Add a new post
		 *
		 * parameters:
		 *  $args = array()
		 *
		 */
		public function add($args)
		{
			// Template
			$template  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			$template .= '<post>';
			$template .= '</post>';

			// New object
			$new_obj = new NBXML($template, 0, FALSE, '', FALSE);

			// Time in UTC-0
			$time_unix = Date::unixstamp();

			// Default elements
			$new_obj->addChild('type',				$args['type']);
			$new_obj->addChild('title',				$args['title']);
			$new_obj->addChild('content',			$args['content']);
			$new_obj->addChild('description',		$args['description']);
			$new_obj->addChild('allow_comments',	$args['allow_comments']);
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

			// Get the last post id
			$new_id = $this->last_insert_id = $this->get_autoinc();

			// Slug
			$this->slug($new_id, $args['slug']);


			// Draft, publish
			$mode = 'NULL';

			if(isset($args['mode']))
			{
				if($args['mode']=='draft')
					$mode = 'draft';
			}

			// Time for filename
			$time_filename = Date::format_gmt($time_unix, 'Y.m.d.H.i.s');

			// Filename for the new post
			$filename = $time_unix.'.'.$new_id.'.'.$args['id_cat'].'.'.$args['id_user'].'.'.$mode.'.'.$time_filename.'.xml';

			// Save to file
			if($new_obj->asXml(PATH_POSTS.$filename))
			{
				// Set the next post id
				$this->set_autoinc(1);

				// Save config
				$this->savetofile();

				return $new_id;
			}

			return false;
		}

		/*
		 * Modify a post
		 *
		 * parameters:
		 *  $args = array()
		 *
		 */
		public function set($args)
		{
			if(!$this->set_file($args['id']))
				return false;

			$new_obj = new NBXML(PATH_POSTS.$this->files[0], 0, TRUE, '', FALSE);

			$new_obj->setChild('title', 			$args['title']);
			$new_obj->setChild('content', 			$args['content']);
			$new_obj->setChild('description', 		$args['description']);
			$new_obj->setChild('allow_comments', 	$args['allow_comments']);
			$new_obj->setChild('mod_date', 			Date::unixstamp());

			// Quote
			if(isset($args['quote']))
			{
				$new_obj->setChild('quote', $args['quote']);
			}

			// ---------------------------------------------------------
			// Filename
			// ---------------------------------------------------------
			$file = explode('.', $this->files[0]);

			// Category
			if(isset($args['id_cat']))
				$file[2] = $args['id_cat'];

			// Draft, publish
			$file[4] = 'NULL';

			if(isset($args['mode']))
			{
				if($args['mode']=='draft')
					$file[4] = 'draft';
			}

			// Publish date
			if(isset($args['unixstamp']))
			{
				$file[0] = $args['unixstamp'];

				$new_obj->setChild('pub_date', $args['unixstamp']);

				$file[5] = Date::format_gmt($args['unixstamp'], 'Y');
				$file[6] = Date::format_gmt($args['unixstamp'], 'm');
				$file[7] = Date::format_gmt($args['unixstamp'], 'd');
				$file[8] = Date::format_gmt($args['unixstamp'], 'H');
				$file[9] = Date::format_gmt($args['unixstamp'], 'i');
				$file[10] = Date::format_gmt($args['unixstamp'], 's');
			}

			// Implode the filename
			$filename = implode('.', $file);

			// Delete the old post
			if($this->delete( array('id'=>$args['id']) ))
			{
				// Slug
				$this->slug($args['id'], $args['slug']);

				// Save config
				$this->savetofile();

				// Save the new post
				return $new_obj->asXml(PATH_POSTS.$filename);
			}

			return false;
		}

		/*
		 * Get a post by id or slug
		 *
		 * parameters:
		 *  $args = array(id, slug)
		 *
		 */
		public function get($args)
		{
			if(isset($args['slug']))
			{
				$where = '@slug="'.utf8_encode($args['slug']).'"';
				$node = $this->xml->xpath('/post/friendly/url['.$where.']');

				if($node==array())
					return false;

				$id = $node[0]->getAttribute('id');
			}
			elseif(isset($args['id']))
			{
				$id = $args['id'];
			}
			else
			{
				return false;
			}

			$this->set_file($id);

			if($this->files_count > 0)
				return $this->get_items($this->files[0]);

			return false;
		}

		/*
		 * Delete a post and this slug link
		 *
		 * parameters:
		 *  $args = array(id)
		 *
		 */
		public function delete($args)
		{
			$this->set_file($args['id']);

			if($this->files_count > 0)
			{
				// Delete the slug
				$this->slug_delete($args['id']);

				// Save config file post.xml
				$this->savetofile();

				// Delete the post
				return unlink(PATH_POSTS.$this->files[0]);
			}

			return false;
		}

		/*
		 * Return an array with all published posts
		 *
		 */
		public function get_all()
		{
			// Set only published posts
			$this->set_files_by_published();

			if($this->files_count > 0)
			return $this->get_full_list();

			return array();
		}

		/*
		 * Return an array with published posts filter by page and amount
		 *
		 * parameters:
		 *  $args = array(page, amount)
		 *
		 */
		public function get_list_by_page($args)
		{
			// Set only published post
			$this->set_files_by_published();

			if($this->files_count > 0)
				return $this->get_list_by($args['page'], $args['amount']);

			return array();
		}

		/*
		 * Return an array with published and drafts posts filter by page and amount
		 *
		 * parameters:
		 *  $args = array(page, amount)
		 *
		 */
		public function get_list_by_page_more_drafts($args)
		{
			// Set list of posts drafts and published
			$this->set_files();

			if($this->files_count > 0)
				return $this->get_list_by($args['page'], $args['amount']);

			return array();
		}

		/*
		 * Return an array with drafts posts filter by page and amount
		 *
		 * parameters:
		 *  $args = array(page, amount)
		 *
		 */
		public function get_drafts($args)
		{
			// Set only drafts posts
			$this->set_files_by_draft();

			if($this->files_count > 0)
				return $this->get_list_by($args['page'], $args['amount']);

			return array();
		}

		/*
		 * Return an array with published post filter by category, page and amount
		 *
		 * parameters:
		 *  $args = array(id_cat, page, amount)
		 *
		 */
		public function get_list_by_category($args)
		{
			// Set posts by category
			$this->set_files_by_category($args['id_cat']);

			if($this->files_count > 0)
				return $this->get_list_by($args['page'], $args['amount']);

			return array();
		}

		/*
		 * Return the amount of posts
		 *
		 */
		public function get_count()
		{
			return $this->files_count;
		}

		/*
		 * Return the next post id
		 *
		 */
		public function get_autoinc()
		{
			return (int) $this->xml['autoinc'];
		}

		public function slug($id_post, $slug)
		{
			$this->slug_delete($id_post);
			$slug = $this->slug_generator($slug);
			$this->slug_add($id_post, $slug);
		}

		public function prev_next_post($id_post)
		{
			// Set only published post
			$this->set_files_by_published();

			$filename['prev'] = false;
			$filename['next'] = false;

			$i = 0;

			while($this->files_count>$i)
			{
				$explode = explode(".",$this->files[$i]);

				$id = (int)$explode[1];

				if($id==$id_post)
				{
					$filename['prev'] = isset($this->files[$i+1])?$this->files[$i+1]:false;
					$filename['next'] = isset($this->files[$i-1])?$this->files[$i-1]:false;
				}

				$i = $i + 1;
			}

			$tmp['prev'] = $filename['prev']==false?false:$this->get_items($filename['prev']);
			$tmp['next'] = $filename['next']==false?false:$this->get_items($filename['next']);

			return $tmp;
		}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/
		/*
		 * Get slug by post id
		 *
		 * parameters:
		 *  (int) $id = Post id
		 *
		 */
		private function slug_get($id)
		{
			$where = '@id="'.utf8_encode($id).'"';
			$node = $this->xml->xpath('/post/friendly/url['.$where.']');

			if($node==array())
				return false;

			return $node[0]->getAttribute('slug');
		}

		/*
		 * Generate a new slug, unique
		 *
		 * parameters:
		 *  (string) $slug
		 *
		 */
		private function slug_generator($slug)
		{
			if(!$this->slug_exists($slug))
				return $slug;

			$slug = $slug.'-0';

			while($this->slug_exists($slug))
				$slug++;

			return $slug;
		}

		/*
		 * Check if exists an slug
		 *
		 * parameters:
		 *  (string) $slug
		 *
		 */
		private function slug_exists($slug)
		{
			$where = '@slug="'.utf8_encode($slug).'"';
			$node = $this->xml->xpath('/post/friendly/url['.$where.']');

			if($node==array())
				return false;

			return true;
		}

		/*
		 * Add slug of a post
		 *
		 * parameters:
		 *  (int) $id = Post id
		 *  (string) $slug
		 *
		 */
		private function slug_add($id, $slug)
		{
			return $this->xml->friendly->addGodChild('url', array('id'=>$id, 'slug'=>$slug));
		}

		/*
		 * Delete slug of a post
		 *
		 * parameters:
		 *  (int) $id = Post id
		 *
		 */
		private function slug_delete($id)
		{
			$where = '@id="'.utf8_encode($id).'"';
			$nodes = $this->xml->xpath('/post/friendly/url['.$where.']');

			foreach($nodes as $node)
			{
				$dom = dom_import_simplexml($node);
				$dom->parentNode->removeChild($dom);
			}

			return true;
		}

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
			$this->xml['autoinc'] = $value + $this->get_autoinc();
		}

		// Set the post
		private function set_file($id)
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*.'.$id.'.*.*.*.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );

			// Post not found
			if($this->files_count == 0)
				return false;

			return true;
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
			$this->files = Filesystem::ls(PATH_POSTS, '*.*.*.*.NULL.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Get all files, only drafts
		private function set_files_by_draft()
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*.*.*.*.draft.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Get all files, by category
		private function set_files_by_category($id_cat)
		{
			$this->files = Filesystem::ls(PATH_POSTS, '*.*.'.$id_cat.'.*.NULL.*.*.*.*.*.*', 'xml', false, false, true);
			$this->files_count = count( $this->files );
		}

		// Devuelve los items de un post
		// File name: UNIXSTAMP.ID_POST.ID_CATEGORY.ID_USER.NULL.YYYY.MM.DD.HH.mm.ss.xml
		private function get_items($file)
		{
			$xml = new NBXML(PATH_POSTS . $file, 0, TRUE, '', FALSE);

			$file_info = explode('.', $file);

			$content = (string) $xml->getChild('content');
			$tmp_content = explode("<!-- pagebreak -->", $content);

			$tmp_array = array('read_more'=>false);

			$tmp_array['filename']			= (string) $file;

			$tmp_array['id']				= (int) $file_info[1];
			$tmp_array['id_cat']			= (int) $file_info[2];
			$tmp_array['id_user']			= (int) $file_info[3];
			$tmp_array['mode']				= (string) $file_info[4];
			$tmp_array['draft']				= (bool) ($file_info[4]=='draft');
			$tmp_array['visits']			= (int) $xml->getChild('visits');

			$tmp_array['type']				= (string) $xml->getChild('type');
			$tmp_array['title']				= (string) $xml->getChild('title');
			$tmp_array['description']		= (string) $xml->getChild('description');

			$tmp_array['pub_date_unix']		= (string) $xml->getChild('pub_date');
			$tmp_array['mod_date_unix']		= (string) $xml->getChild('mod_date');

			$tmp_array['allow_comments']	= (bool) ((int)$xml->getChild('allow_comments'))==1;

			// Slug
			$tmp_array['slug'] = $this->slug_get($tmp_array['id']);

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
				$tmp_array['video']			= (string) $xml->getChild('video');
			}
			elseif($tmp_array['type']=='quote')
			{
				$tmp_array['quote']			= (string) $xml->getChild('quote');
			}

			return( $tmp_array );
		}

		private function get_list_by($page_number, $post_per_page)
		{
			$init = (int) $post_per_page * $page_number;
			$end  = (int) min( ($init + $post_per_page - 1), $this->files_count - 1 );

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

		/*
		 * Get a full list of posts
		 *
		 */
		private function get_full_list()
		{
			$tmp_array = array();

			foreach($this->files as $file)
			{
			$post = $this->get_items($file);

			$position = $post['position'];

			while(isset($tmp_array[$position]))
				$position++;

				$tmp_array[$position] = $post;
			}

			// Sort low to high
			ksort($tmp_array);

			return $tmp_array;
		}

} // END Class

?>
