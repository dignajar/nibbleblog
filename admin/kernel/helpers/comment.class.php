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

		// Comment data from session
		$data = Session::get_comment_array();

		// If the post allow comments
		if(!$data['post_allow_comments'])
		{
			return(false);
		}

		// Sanitize
		if($sanitize)
		{
			$data = $this->sanitize($data);
		}

		// Anti-spam
		if($this->check_spam($data['content']))
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

	private function check_spam($content)
	{
		$spam_monitor = $this->comment_db->get_spam_monitor();

		if($spam_monitor['enable'])
		{
			$defensio = new Defensio($spam_monitor['api_key']);

			$document = array(
							'type'=>'comment',
							'content'=>$content,
							'platform'=>'Nibbleblog',
							'client' => 'Nibbleblog',
							'async' => 'false'
			);

			$defensio_result = $defensio->postDocument($document);

			echo 'DEBUG <pre>';
			print_r($defensio_result);
			echo '</pre>';

			if($defensio[1]['spaminess']>$spam_monitor['spaminess'])
			{
				return(true);
			}
		}

		return(false);
	}

} // END Class

?>