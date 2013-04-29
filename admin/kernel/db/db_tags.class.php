<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_TAGS {

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
	function DB_TAGS($file)
	{
		if(file_exists($file))
		{
			$this->file = $file;

			$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);

			return true;
		}

		return false;
	}

/*
========================================================================
	PUBLIC METHODS
========================================================================
*/
	// Add one tag and return the ID, if the tag exist then return the ID
	public function add($args)
	{
		// Get ID tag if this exist
		$id = $this->get_id($args);

		// If the tag name exist, then return the ID
		if($id!==false)
			return $id;

		// Add table
		$node = $this->xml->list->addChild('tag','');

		// Add key
		$node->addAttribute('name', $args['name']);

		// Add registers
		$id = $this->get_autoinc();
		$node->addChild('id', $id);
		$this->set_autoinc();

		return $id;
	}

	// Link a tag with a post
	public function link($args)
	{
		$node = $this->xml->xpath('/tags/links/link[@id_tag="'.utf8_encode($args['id_tag']).'" and @id_post="'.utf8_encode($args['id_post']).'"]');

		// id tag and id post are ready linked
		if($node!=array())
			return false;

		// Add the table
		$node = $this->xml->links->addChild('link','');

		// Add keys
		$node->addAttribute('id_tag', $args['id_tag']);
		$node->addAttribute('id_post', $args['id_post']);

		return true;
	}

	// Get tag information
	private function get($args)
	{
		$node = $this->xml->xpath('/tags/list/tag[@name="'.utf8_encode($args['name']).'"]');

		if($node==array())
			return false;

		$tmp = array();
		foreach($node[0]->children() as $field=>$n)
			$tmp[$field] = $node[0]->getChild($field);

		return $tmp;
	}

	// Get tag ID
	private function get_id($args)
	{
		$tag = $this->get($args);

		if($tag===false)
			return false;

		return $tag['id'];
	}

	// Add tags and link this with a id post
	public function add_tags($args)
	{
		$tmp = $this->recondition($args['tags']);

		foreach($tmp as $tag_name)
		{
			$id = $this->add(array('name'=>$tag_name));

			$this->link(array('id_tag'=>$id, 'id_post'=>$args['id_post']));
		}

		return true;
	}

	// Delete all links
	public function delete_links($args)
	{
		$nodes = $this->xml->xpath('/tags/links/link[@id_post="'.utf8_encode($args['id_post']).'"]');

		if($nodes==array())
			return false;

		foreach($nodes as $node)
		{
			$dom = dom_import_simplexml($node);
			$dom->parentNode->removeChild($dom);
		}

		return true;
	}

	// Save all changes
	public function savetofile()
	{
		return $this->xml->asXML($this->file);
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

	private function set_autoinc($value = 1)
	{
		$this->xml['autoinc'] = $value + $this->get_autoinc();
	}

	// Recive an string $tags and convert this to an array
	private function recondition($tags)
	{
		$explode = explode(',', $tags);

		$tmp_array = array();
		foreach( $explode as $tag )
		{
			if(!empty($tag))
				array_push($tmp_array, $tag);
		}
		return( $tmp_array );
	}


} // END Class

?>