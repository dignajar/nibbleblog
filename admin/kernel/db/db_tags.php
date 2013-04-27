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
	public function add($args)
	{
		$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

		if($node==array())
			return false;

		// Add the table
		$node = $this->xml->addChild('user','');

		// Add the key
		$node->addAttribute('username', $args['username']);

		// Add the registers
		$node->addChild('id', 					$args['id']);
		$node->addChild('session_fail_count',	$args['session_fail_count']);
		$node->addChild('session_date',			$args['session_date']);

		return $this->savetofile();
	}

	public function get($args)
	{
		$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

		if($node==array())
			return false;

		$tmp = array();
		foreach($node[0]->children() as $field=>$n)
			$tmp[$field] = $node[0]->getChild($field);

		return $tmp;
	}

	public function set($args)
	{
		$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

		if($node== array())
			return false;

		unset($args['username']);

		foreach($args as $key=>$value)
			$node[0]->setChild($key, $value);

		return $this->savetofile();
	}

	public function is_valid($args)
	{
		return $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]') != array();
	}

	public function savetofile()
	{
		return $this->xml->asXML($this->file);
	}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/
	// Recive an string $tags and convert this to an array
	private function recondition($tags)
	{
		$tags = $this->strip_spaces($tags);
		$tags = strip_tags($tags);
		$tags = $this->strip_quotes($tags);

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