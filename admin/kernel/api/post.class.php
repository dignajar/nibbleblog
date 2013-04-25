<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Post {

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

	// Return array with the post if exist
	// Return FALSE if not exist
	public function get($id)
	{
		$post = $this->db->get( array('id'=>$id) );

		return($post);
	}

	// Returns an array if there are posts in the system
	// Returns an empty array if there are not posts on the system
	public function get_by_page($page, $amount)
	{
		$posts = $this->db->get_list_by_page( array('page_number'=>$page, 'amount'=>$amount) );

		return($posts);
	}

	// Returns an array if there are posts in the category
	// Returns an empty array if there are not posts on the category
	public function get_by_category($id_category, $page, $amount)
	{
		$posts = $this->db->get_list_by_category( array('id_cat'=>$id_category, 'page_number'=>$page, 'amount'=>$amount) );

		return($posts);
	}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/


} // END Class

?>