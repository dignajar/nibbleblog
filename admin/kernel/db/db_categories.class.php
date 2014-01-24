<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_CATEGORIES {

/*
========================================================================
VARIABLES
========================================================================
*/
	public $file;	// File db
	public $xml;	// Simplexml Obj

/*
========================================================================
CONSTRUCTORS
========================================================================
*/
	function DB_CATEGORIES($file)
	{
		if(file_exists($file))
		{
			$this->file = $file;

			$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);
		}
	}

/*
========================================================================
PUBLIC METHODS
========================================================================
*/
	public function savetofile()
	{
		return $this->xml->asXML($this->file);
	}

	public function add($args)
	{
		$tmp_node = $this->xml->xpath('/categories/category[@name="'.utf8_encode($args['name']).'"]');

		if( $tmp_node == array() )
		{
			$new_node = $this->xml->addChild('category','');
			$new_node->addAttribute('id', $this->get_autoinc());
			$new_node->addAttribute('name', $args['name'] );
			$new_node->addAttribute('slug', $args['slug'] );
			$new_node->addAttribute('position', $args['position'] );
			$this->set_autoinc(1);

			return $this->savetofile();
		}

		return false;
	}

	public function set($args)
	{
		$node = $this->xml->xpath('/categories/category[@id="'.$args['id'].'"]');

		// Category not found
		if( $node == array() )
			return false;

		$node[0]->attributes()->name = utf8_encode($args['name']);
		$node[0]->attributes()->slug = utf8_encode($args['slug']);
		$node[0]->attributes()->position = utf8_encode($args['position']);

		return $this->savetofile();
	}

	public function delete($args)
	{
		$tmp_node = $this->xml->xpath('/categories/category[@id="'.$args['id'].'"]');

		// Category not found
		if( $tmp_node == array() )
			return false;

		// Need at least 1 category
		if( $this->get_count() == 1 )
			return false;

		// Check if the category have some post assoc
		if( $this->get_post_count($args['id']) > 0)
			return false;

		$dom = dom_import_simplexml($tmp_node[0]);
		$dom->parentNode->removeChild($dom);

		return $this->savetofile();
	}

	public function get($args)
	{
		$node = $this->xml->xpath('/categories/category[@id="'.$args['id'].'"]');

		// Category not found
		if( $node == array() )
			return false;

		return $this->get_items($node[0]);
	}

	public function get_by_slug($args)
	{
		$node = $this->xml->xpath('/categories/category[@slug="'.utf8_encode($args['slug']).'"]');

		// Category not found
		if( $node == array() )
			return false;

		return $this->get_items($node[0]);
	}

	public function get_all()
	{
		$tmp_array = array();
		foreach( $this->xml->children() as $children )
		{
			$row = $this->get_items($children);

			$position = $row['position'];

			while(isset($tmp_array[$position]))
				$position++;

			$tmp_array[$position] = $row;
		}

		// Sort low to high
		ksort($tmp_array);

		return $tmp_array;
	}

	public function get_count()
	{
		return count($this->xml);
	}

	public function get_post_count($id)
	{
		return count(Filesystem::ls(PATH_POSTS, '*.*.'.$id.'.*.*.*.*.*.*.*.*', 'xml', false, false, false));
	}

/*
========================================================================
PRIVATE METHODS
========================================================================
*/
	private function get_autoinc()
	{
		return (int)$this->xml['autoinc'];
	}

	private function set_autoinc($value = 0)
	{
		$this->xml['autoinc'] = $value + $this->get_autoinc();
	}

	private function get_items($node)
	{
		$tmp_array			= array();
		$tmp_array['id']	= (int) $node->getAttribute('id');
		$tmp_array['name']	= $node->getAttribute('name');
		$tmp_array['slug']	= $node->getAttribute('slug');
		$tmp_array['position']	= $node->getAttribute('position');

		return $tmp_array;
	}

} // END Class

?>