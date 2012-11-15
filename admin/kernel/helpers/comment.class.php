<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 05/11/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Comment {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
	private $db;

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
	function __construct($db)
	{
		$this->db = $db;
	}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
	public function add($delay = 0, $sanitize = true)
	{
		// Sleep
		sleep($delay);

		$data = Session::get_comment_array();

		// Sanitize
		if($sanitize)
		{
			$data = $this->sanitize($data);
		}

		// If the post allow comments
		if(!$data['post_allow_comments'])
		{
			return(false);
		}

		// Add to database
		$this->db->add($data);

		// Clean session
		Session::init();

		return(true);
	}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/
	private function sanitize($args)
	{
		$safe = array();
		foreach($args as $key=>$value)
		{
			$safe[$key] = Validation::sanitize_html($value);
		}

		return($safe);
	}

} // END Class

?>