<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Page {

/*
========================================================================
	VARIABLES
========================================================================
*/
	private $db;

/*
========================================================================
	CONSTRUCTORS
========================================================================
*/
	function __construct($db)
	{
		$this->db = $db;
	}

/*
========================================================================
	PUBLIC METHODS
========================================================================
*/

	public function get($id, $slug=false)
	{
		if($slug!==false)
			return $this->db->get( array('slug'=>$slug) );

		return $this->db->get( array('id'=>$id) );
	}

	public function get_all()
	{
		$pages = $this->db->get_all();

		usort($pages, array('Page','sort'));

		return $pages;
	}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/
	private function sort($a, $b)
	{
		return $a['position'] - $b['position'];
	}

} // END Class

?>