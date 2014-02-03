<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_PAGES {

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
	function DB_PAGES($file)
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
	 * Add a new page
	 *
	 * parameters:
	 *  $args = array()
	 *
	 */
	public function add($args)
	{
		// Template
		$template  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$template .= '<page>';
		$template .= '</page>';

		// New object
		$new_obj = new NBXML($template, 0, FALSE, '', FALSE);

		// Time in UTC-0
		$time_unix = Date::unixstamp();

		// Default elements
		$new_obj->addChild('title',				$args['title']);
		$new_obj->addChild('content',			$args['content']);
		$new_obj->addChild('description',		$args['description']);
		$new_obj->addChild('keywords',			$args['keywords']);
		$new_obj->addChild('position',			(int)$args['position']);
		$new_obj->addChild('pub_date',			$time_unix);
		$new_obj->addChild('mod_date',			'0');
		$new_obj->addChild('visits',			'0');

		// Get the last page id
		$new_id = $this->last_insert_id = $this->get_autoinc();

		// Slug
		$slug = $this->slug_generator($args['slug']);
		$this->slug_add($new_id, $slug);

		// Draft, publish
		$mode = 'NULL';

		if(isset($args['mode']))
		{
			if($args['mode']=='draft')
				$mode = 'draft';
		}

		// Time for filename
		$time_filename = Date::format_gmt($time_unix, 'Y.m.d.H.i.s');

		// Filename for the new page
		$filename = $new_id.'.NULL.NULL.'.$mode.'.'.$time_filename.'.xml';

		// Save to file
		if($new_obj->asXml(PATH_PAGES.$filename))
		{
			// Set the next page id
			$this->set_autoinc(1);

			// Save config
			$this->savetofile();

			return $new_id;
		}

		return false;
	}

	/*
	 * Modify a page
	 *
	 * parameters:
	 *  $args = array()
	 *
	 */
	public function set($args)
	{
		if(!$this->set_file($args['id']))
			return false;

		$new_obj = new NBXML(PATH_PAGES.$this->files[0], 0, TRUE, '', FALSE);

		$new_obj->setChild('title', 			$args['title']);
		$new_obj->setChild('content', 			$args['content']);
		$new_obj->setChild('description', 		$args['description']);
		$new_obj->setChild('keywords', 			$args['keywords']);
		$new_obj->setChild('position', 			(int)$args['position']);
		$new_obj->setChild('mod_date', 			Date::unixstamp());

		// ---------------------------------------------------------
		// Filename
		// ---------------------------------------------------------
		$file = explode('.', $this->files[0]);

		// Draft, publish
		$file[3] = 'NULL';

		if(isset($args['mode']))
		{
			if($args['mode']=='draft')
				$file[3] = 'draft';
		}

		// Publish date
		if(isset($args['unixstamp']))
		{
			$new_obj->setChild('pub_date', $args['unixstamp']);

			$file[4] = Date::format_gmt($args['unixstamp'], 'Y');
			$file[5] = Date::format_gmt($args['unixstamp'], 'm');
			$file[6] = Date::format_gmt($args['unixstamp'], 'd');
			$file[7] = Date::format_gmt($args['unixstamp'], 'H');
			$file[8] = Date::format_gmt($args['unixstamp'], 'i');
			$file[9] = Date::format_gmt($args['unixstamp'], 's');
		}

		// Implode the filename
		$filename = implode('.', $file);

		// Delete the old page
		if($this->delete( array('id'=>$args['id']) ))
		{
			// Slug
			$slug = $this->slug_generator($args['slug']);
			$this->slug_add($args['id'], $slug);

			// Save config
			$this->savetofile();

			// Save the new page
			return $new_obj->asXml(PATH_PAGES.$filename);
		}

		return false;
	}

	/*
	 * Get a page by id or slug
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
			$node = $this->xml->xpath('/pages/friendly/url['.$where.']');

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
	 * Delete a page and the slug link
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

			// Save config file pages.xml
			$this->savetofile();

			// Delete the page
			return unlink(PATH_PAGES.$this->files[0]);
		}

		return false;
	}

	/*
	 * Save the config file
	 *
	 */
	public function savetofile()
	{
		return $this->xml->asXML($this->file);
	}

	/*
	 * Return the last page id
	 *
	 */
	public function get_last_insert_id()
	{
		return( $this->last_insert_id );
	}

	/*
	 * Return an array with published pages
	 *
	 * parameters:
	 *  $args = array(page, amount)
	 *
	 */
	public function get_all()
	{
		// Set only published pages
		$this->set_files_by_published();

		if($this->files_count > 0)
			return $this->get_list_by();

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
		$node = $this->xml->xpath('/pages/friendly/url['.$where.']');

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
		$node = $this->xml->xpath('/pages/friendly/url['.$where.']');

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
		$nodes = $this->xml->xpath('/pages/friendly/url['.$where.']');

		foreach($nodes as $node)
		{
			$dom = dom_import_simplexml($node);
			$dom->parentNode->removeChild($dom);
		}

		return true;
	}

	private function set_autoinc($value = 0)
	{
		$this->xml['autoinc'] = $value + $this->get_autoinc();
	}

	/*
	 * Set one page by page id
	 *
	 * parameters:
	 *  (int) $id = Page id
	 *
	 */
	private function set_file($id)
	{
		$this->files = Filesystem::ls(PATH_PAGES, $id.'.*.*.*.*.*.*.*.*.*', 'xml', false, false, true);
		$this->files_count = count( $this->files );

		// Page not found
		if($this->files_count == 0)
			return false;

		return true;
	}

	/*
	 * Set all pages
	 *
	 */
	private function set_files()
	{
		$this->files = Filesystem::ls(PATH_PAGES, '*', 'xml', false, false, true);
		$this->files_count = count( $this->files );
	}

	/*
	 * Set only published pages
	 *
	 */
	private function set_files_by_published()
	{
		$this->files = Filesystem::ls(PATH_PAGES, '*.*.*.NULL.*', 'xml', false, false, true);
		$this->files_count = count( $this->files );
	}

	/*
	 * Set only drafts pages
	 *
	 */
	private function set_files_by_draft()
	{
		$this->files = Filesystem::ls(PATH_PAGES, '*.*.*.draft.*', 'xml', false, false, true);
		$this->files_count = count( $this->files );
	}

	/*
	 * Return all items from a file
	 *
	 * parameters:
	 *  (string) $file = Filename (ID_PAGE.NULL.NULL.NULL.YYYY.MM.DD.HH.mm.ss.xml)
	 *
	 */
	private function get_items($file)
	{
		$xml = new NBXML(PATH_PAGES.$file, 0, TRUE, '', FALSE);

		$file_info = explode('.', $file);

		$tmp_array['content'] = $xml->getChild('content');

		$tmp_array['filename']			= $file;

		$tmp_array['id']				= (int)$file_info[0];
		$tmp_array['draft']				= ($file_info[3]=='draft');
		$tmp_array['visits']			= $xml->getChild('visits');
		$tmp_array['title']				= $xml->getChild('title');
		$tmp_array['description']		= $xml->getChild('description');
		$tmp_array['position']			= $xml->getChild('position');
		$tmp_array['keywords']			= $xml->getChild('keywords');

		$tmp_array['pub_date_unix']		= $xml->getChild('pub_date');
		$tmp_array['mod_date_unix']		= $xml->getChild('mod_date');

		// Slug
		$tmp_array['slug'] = $this->slug_get($tmp_array['id']);

		return $tmp_array;
	}

	private function get_list_by()
	{
		$tmp_array = array();

		foreach($this->files as $file)
		{
			$page = $this->get_items($file);

			$position = $page['position'];

			while(isset($tmp_array[$position]))
				$position++;

			$tmp_array[$position] = $page;
		}

		// Sort low to high
		ksort($tmp_array);

		return $tmp_array;
	}

} // END Class

?>