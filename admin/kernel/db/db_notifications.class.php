<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_NOTIFICATIONS {

/*
========================================================================
	VARIABLES
========================================================================
*/
	public $file; 			// Contains the link to XML file
	public $xml; 			// Contains the object
	public $settings;

/*
========================================================================
	CONSTRUCTORS
========================================================================
*/
	function DB_NOTIFICATIONS($file, $settings)
	{
		if(file_exists($file))
		{
			$this->file = $file;

			$this->settings = $settings;

			$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);
		}
	}

/*
========================================================================
PUBLIC METHODS
========================================================================
*/
	public function savetofile()
	{
		return( $this->xml->asXML($this->file) );
	}

	public function add($category, $send_email, $args=array())
	{
		global $_LANG;

		if( count( $this->xml->notification ) >= NOTIFICATIONS_AMOUNT )
			unset( $this->xml->notification[0] );

		// Email
		if($send_email)
		{
			if($category=='session_fail')
			{
				// Subject
				$subject = $_LANG['LOGIN_FAILED_ATTEMPT'];
				// Message
				$message = Text::replace_assoc(
						array(
							'{{BLOG_NAME}}'=>$this->settings['name'],
							'{{USERNAME}}'=>$args['username'],
							'{{PASSWORD}}'=>$args['password'],
							'{{IP}}'=>Net::get_user_ip()
						),
						$_LANG['EMAIL_NOTIFICATION_FAIL_LOGIN']
				);
			}
			elseif($category=='session_start')
			{
				// Subject
				$subject = $_LANG['NEW_SESSION_STARTED'];
				// Message
				$message = Text::replace_assoc(
						array(
							'{{BLOG_NAME}}'=>$this->settings['name'],
							'{{USERNAME}}'=>$args['username'],
							'{{IP}}'=>Net::get_user_ip()
						),
						$_LANG['EMAIL_NOTIFICATION_SESSION_STARTED']
				);
			}
			elseif($category=='comment')
			{
				// Subject
				$subject = $_LANG['YOU_HAVE_A_NEW_COMMENT'];
				// Message
				$message = Text::replace_assoc(
						array(
							'{{BLOG_NAME}}'=>$this->settings['name'],
							'{{COMMENT}}'=>$args['comment'],
							'{{AUTHOR_NAME}}'=>$args['author_name'],
							'{{AUTHOR_EMAIL}}'=>$args['author_email'],
							'{{IP}}'=>Net::get_user_ip()
						),
						$_LANG['EMAIL_NOTIFICATION_NEW_COMMENT']
				);
			}

			$sent = Email::send(array(
						'from'=>$this->settings['notification_email_from'],
						'to'=>$this->settings['notification_email_to'],
						'subject'=>$subject,
						'message'=>$message
			));
		}
		else
		{
			$sent = false;
		}

		// Encrypt the user IP
		include(FILE_KEYS);
		$user_ip = Crypt::encrypt(Net::get_user_ip(), $_KEYS[0]);

		// Save the notification
		$node = $this->xml->addChild('notification');
		$node->addAttribute('category',		$category);
		$node->addAttribute('mail',			$sent);
		$node->addAttribute('ip',			$user_ip);
		$node->addAttribute('date',			Date::unixstamp());

		$this->savetofile();

		return true;
	}

	public function get_all()
	{
		include(FILE_KEYS);

		$tmp_array = array();
		foreach( $this->xml->notification as $notification )
		{
			// Decrypt the user IP
			$user_ip = Crypt::decrypt((string) $notification->getAttribute('ip'), $_KEYS[0]);

			$row = array();
			$row['category']		= (string) $notification->getAttribute('category');
			$row['mail']			= (bool) $notification->getAttribute('mail');
			$row['message_key']		= (string) $notification->getAttribute('message_key');
			$row['date']			= (string) $notification->getAttribute('date');
			$row['ip'] 				= $user_ip;

			array_push($tmp_array, $row);
		}
		return( array_reverse($tmp_array) );
	}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/


} // END Class

?>