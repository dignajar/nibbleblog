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
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to XML file
		public $obj_xml; 			// Contains the object
		public $settings;

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_NOTIFICATIONS($file, $settings)
		{
			$this->file_xml = $file;

			if( file_exists($this->file_xml) )
			{
				$this->obj_xml = new NBXML($this->file_xml, 0, TRUE, '', FALSE);
				$this->settings = $settings;
			}
			else
			{
				return(false);
			}

			return(true);
		}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
		public function savetofile()
		{
			return( $this->obj_xml->asXML($this->file_xml) );
		}

		public function add($type, $send_email, $message_key, $email_message = '')
		{
			global $_LANG;

			if( count( $this->obj_xml->notification ) >= NOTIFICATIONS_AMOUNT )
				unset( $this->obj_xml->notification[0] );

			// Email
			if($send_email)
			{
				$sent = Email::send(array(
									'from_name'=>$this->settings['notification_email_from'],
									'from_mail'=>$this->settings['notification_email_from'],
									'to'=>$this->settings['notification_email_to'],
									'subject'=>$_LANG[$message_key],
									'message'=>EMAIL_NOTIFICATIONS.$email_message
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
			$node = $this->obj_xml->addChild('notification');
			$node->addAttribute('type',			$type);
			$node->addAttribute('mail',			$sent);
			$node->addAttribute('message_key',	$message_key);
			$node->addAttribute('ip',			$user_ip);
			$node->addAttribute('date',			Date::unixstamp());

			$this->savetofile();

			return(true);
		}

		public function get_all()
		{
			include(FILE_KEYS);

			$tmp_array = array();
			foreach( $this->obj_xml->notification as $notification )
			{
				// Decrypt the user IP
				$user_ip = Crypt::decrypt((string) $notification->getAttribute('ip'), $_KEYS[0]);

				$row = array();
				$row['type']			= (string) $notification->getAttribute('type');
				$row['mail']			= (bool) $notification->getAttribute('mail');
				$row['message_key']		= (string) $notification->getAttribute('message_key');
				$row['date']			= (string) $notification->getAttribute('date');
				$row['ip'] 				= $user_ip;

				array_push($tmp_array, $row);
			}
			return( array_reverse($tmp_array) );
		}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/


} // END Class

?>