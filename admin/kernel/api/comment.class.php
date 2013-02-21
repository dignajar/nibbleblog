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
	private $db;
	private $db_notification;

	private $settings;
	private $comment_settings;

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
	function __construct($db, $db_notification, $settings)
	{
		$this->db = $db;
		$this->db_notification = $db_notification;

		$this->settings = $settings;
		$this->comment_settings = $db->get_settings();
	}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
	// Return TRUE if the comment is inserted
	// Return FALSE if the comment is spam or need moderation
	public function add()
	{
		// Sleep
		sleep($this->comment_settings['sleep']);

		// Comment data from session
		$data = Session::get_comment_array();

		// If the post allow comments
		if(!$data['post_allow_comments'])
		{
			return(false);
		}

		// Anti-spam
		$spam_level = $this->get_spam_level($data['content']);

		// Set type
		if($spam_level>(float)$this->comment_settings['monitor_spaminess'])
		{
			$data['type'] = 'spam';
		}
		elseif($this->comment_settings['moderate'])
		{
			$data['type'] = 'unapproved';
		}
		else
		{
			$data['type'] = 'NULL';
		}

		// Sanitize
		if($this->comment_settings['sanitize'])
		{
			$data = $this->sanitize($data);
		}

		if( ($data['type']!='spam') || !$this->comment_settings['monitor_auto_delete'] )
		{
			// Add comment
			$this->db->add($data);

			// Add notification
			$this->db_notification->add('comment', $this->settings['notification_comments'], 'YOU_HAVE_A_NEW_COMMENT');
		}

		// Clean session
		Session::init();

		return($data['type']=='NULL');
	}

	// Return array with the comment if exist
	// Return empty array if not exist
	public function get($id)
	{
		$comment = $this->db->get( array('id'=>$id) );

		if($comment==false)
		{
			return(array());
		}

		return($comment);
	}

	// Returns an array if there are comments in the system
	// Returns an empty array if there are not comment on the system
	public function get_by_page($page, $amount)
	{
		$comments = $this->db->get_list_by_page( array('page_number'=>$page, 'amount'=>$amount) );

		if($comments==false)
		{
			return(array());
		}

		return($comments);
	}

	// Returns an array if there are comments in the post
	// Returns an empty array if there are not comment on the post
	public function get_by_post($id_post)
	{
		$comments = $this->db->get_list_by_post( array('id_post'=>$id_post) );

		if($comments==false)
		{
			return(array());
		}

		return($comments);
	}

	// Returns an array if there are comments
	// Returns an empty array if there are not comment
	public function get_last($amount)
	{
		$comments = $this->db->get_last( array('amount'=>$amount) );

		if($comments==false)
		{
			return(array());
		}

		return($comments);
	}

	// Return an array with the comments settings
	public function get_settings()
	{
		return( $this->db->get_settings() );
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

	private function get_spam_level($content)
	{
		if($this->comment_settings['monitor_enable'])
		{
			$defensio = new Defensio($this->comment_settings['monitor_api_key']);

			$document = array(
							'type'=>'comment',
							'content'=>$content,
							'platform'=>'Nibbleblog',
							'client' => 'Nibbleblog',
							'async' => 'false'
			);

			$defensio_result = $defensio->postDocument($document);

			return( (float)$defensio_result[1]->spaminess );
		}

		return(0);
	}

} // END Class

?>