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
	// Return -1 errors(comment flooding, post doesn't allow comments, others)
	public function add($data)
	{
		// Flood protection
		if( $this->get_last_time() + COMMENT_INTERVAL > time())
		{
			return -1;
		}

		// Anti-spam
		$spam_level = $this->get_spam_level($data['content']);

		if($spam_level===false)
		{
			$data['type'] = 'unapproved';
		}
		else
		{
			if($spam_level>(float)$this->comment_settings['monitor_spam_control'])
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

			if($data['type']!='spam')
			{
				// Add notification
				$this->db_notification->add('comment', $this->settings['notification_comments'], array('ip'=>$data['author_ip'], 'author_name'=>$data['author_name'], 'author_email'=>$data['author_email'], 'comment'=>$data['content']));
			}

			$this->set_last_time();
		}

		return $data['type']=='NULL';
	}

	// Return array with the comment if exist
	// Return FALSE if not exist
	public function get($id)
	{
		$comment = $this->db->get( array('id'=>$id) );

		return($comment);
	}

	// Returns an array if there are comments in the system
	// Returns an empty array if there are not comment on the system
	public function get_by_page($page, $amount)
	{
		$comments = $this->db->get_list_by_page( array('page_number'=>$page, 'amount'=>$amount) );

		return($comments);
	}

	// Returns an array if there are comments in the post
	// Returns an empty array if there are not comment on the post
	public function get_by_post($id_post)
	{
		$comments = $this->db->get_list_by_post( array('id_post'=>$id_post) );

		return($comments);
	}

	// Returns an array if there are comments
	// Returns an empty array if there are not comment
	public function get_last($amount)
	{
		$comments = $this->db->get_last( array('amount'=>$amount) );

		return($comments);
	}

	// Return an array with the comments settings
	public function get_settings()
	{
		return($this->db->get_settings());
	}

	public function disqus_shortname()
	{
		return $this->comment_settings['disqus_shortname'];
	}

	public function facebook_appid()
	{
		return $this->comment_settings['facebook_appid'];
	}

	public function disqus_enabled()
	{
		return !empty($this->comment_settings['disqus_shortname']);
	}

	public function facebook_enabled()
	{
		return !empty($this->comment_settings['facebook_appid']);
	}

	// DEPRACTED
	public function get_hash()
	{
		return Session::get_comment('hash');
	}

	// DEPRACTED
	public function set_hash()
	{
		$hash = Crypt::get_hash(time(),time());
		Session::set('hash', $hash);
	}

	public function get_last_time()
	{
		return Session::get_last_comment_at();
	}

	public function set_last_time()
	{
		Session::set_last_comment_at(time());
	}

	/*
	 * Set comment field
	 */
	public function set_form($field, $text)
	{
		Session::set_comment($field, $text);
	}

	/*
	 * Get comment field
	 */
	public function form($field)
	{
		$data = Session::get_comment($field);
		Session::set_comment($field, '');

		return $data;
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
            try
            {
				$defensio = new Defensio($this->comment_settings['monitor_api_key']);

				// Invalid API KEY
				if(array_shift($defensio->getUser()) != 200)
				{
					return(false);
				}

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
            catch( Exception $e )
            {
				// Something fail, timeout, invalid key, etc...
                return(false);
            }
		}

		// Spam monitor disabled
		return(0);
	}

} // END Class

?>