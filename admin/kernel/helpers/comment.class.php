<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Comment {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
	private $comment_db;
	private $notification_db;
	private $settings;

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
	function __construct($comment_db, $notification_db, $settings)
	{
		$this->comment_db = $comment_db;
		$this->notification_db = $notification_db;
		$this->settings = $settings;
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

		// Add comment
		$this->comment_db->add($data);

		// Add notification
		$this->notification_db->add('comment', $this->settings['notification_comments'], 'YOU_HAVE_A_NEW_COMMENT');

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