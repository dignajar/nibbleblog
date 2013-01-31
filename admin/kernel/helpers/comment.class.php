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
	private $comment_settings;

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
		$this->comment_settings = $comment_db->get_settings();
	}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
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
		if($this->check_spam($data['content']))
		{
			return(false);
		}

		// Sanitize
		if($this->comment_settings['sanitize'])
		{
			$data = $this->sanitize($data);
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

			if((float)$defensio[1]['spaminess']>(float)$this->comment_settings['monitor_spaminess'])
			{
				return(true);
			}
		}

		return(false);
	}

} // END Class

?>