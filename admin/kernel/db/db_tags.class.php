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
		}
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
		$id = $this->get_autoinc();
		$this->set_autoinc();

		$node = $this->xml->list->addGodChild('tag', array('id'=>$id, 'name'=>$args['name'], 'name_human'=>$args['name_human']));

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
		$node = $this->xml->links->addGodChild('link', array('id_tag'=>$args['id_tag'], 'id_post'=>$args['id_post']));

		return true;
	}

	// Get tag information, by ID or by name
	public function get($args)
	{
		if(isset($args['name']))
			$where = '@name="'.utf8_encode($args['name']).'"';
		elseif(isset($args['id']))
			$where = '@id="'.utf8_encode($args['id']).'"';

		$node = $this->xml->xpath('/tags/list/tag['.$where.']');

		if($node==array())
			return false;

		$tmp = array();
		$tmp['id'] = $node[0]->getAttribute('id');
		$tmp['name'] = $node[0]->getAttribute('name');
		$tmp['name_human'] = $node[0]->getAttribute('name_human');

		return $tmp;
	}

	// Get tag ID
	public function get_id($args)
	{
		$tag = $this->get($args);

		if($tag===false)
			return false;

		return $tag['id'];
	}

	// Get tags by post ID
	public function get_by_idpost($args)
	{
		$nodes = $this->xml->xpath('/tags/links/link[@id_post="'.utf8_encode($args['id_post']).'"]');

		$tmp = array();

		foreach($nodes as $node)
		{
			$id_tag = $node->getAttribute('id_tag');
			$tag = $this->get(array('id'=>$id_tag));

			array_push($tmp, array('name'=>$tag['name'], 'name_human'=>$tag['name_human']));
		}

		return $tmp;
	}

	// Add tags and link this with a id post
	public function add_tags($args)
	{
		$tmp = $this->recondition($args['tags']);

		foreach($tmp as $tag)
		{
			$id = $this->add(array('name'=>$tag['name'], 'name_human'=>$tag['name_human']));

			$this->link(array('id_tag'=>$id, 'id_post'=>$args['id_post']));
		}

		return true;
	}

	// Delete all links
	public function delete_links($args)
	{
		$nodes = $this->xml->xpath('/tags/links/link[@id_post="'.utf8_encode($args['id_post']).'"]');

		foreach($nodes as $node)
		{
			$dom = dom_import_simplexml($node);
			$dom->parentNode->removeChild($dom);
		}

		return true;
	}

	// Get all id post by tag name
	public function get_all_posts($args)
	{
		$id_tag = $this->get_id(array('name'=>$args['name']));

		if($id_tag===false)
			return false;

		$nodes = $this->xml->xpath('/tags/links/link[@id_tag="'.utf8_encode($id_tag).'"]');

		$tmp = array();

		foreach($nodes as $node)
		{
			$id_post = (int)$node->getAttribute('id_post');
			array_push($tmp, $id_post);
		}

		return $tmp;
	}

	// Get cloud
	public function get_cloud()
	{
		$tags = $this->xml->xpath('/tags/list/tag');

		$tmp = array();

		foreach($tags as $tag)
		{
			$id = (int)$tag->getAttribute('id');
			$name = (string)$tag->getAttribute('name');
			$name_human = (string)$tag->getAttribute('name_human');

			$where = '@id_tag="'.$id.'"';
			$nodes = $this->xml->xpath('/tags/links/link['.$where.']');

			$tmp[$name] = array('amount'=>count($nodes), 'name_human'=>$name_human);
		}

		return $tmp;
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
		    $tag = trim($tag);
			if(!empty($tag))
				array_push($tmp_array, array('name'=>Text::strip_spaces($tag), 'name_human'=>$tag));
		}

		return( $tmp_array );
	}


} // END Class

?>