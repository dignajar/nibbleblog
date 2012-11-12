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
	public function add($data, $delay = 0, $sanitize = true)
	{
		// Sleep
		sleep($delay);

		// Sanitize
		if($sanitize)
		{
			$data = $this->sanitize($data);
		}

		// Add on database
		$this->db->add($data);
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