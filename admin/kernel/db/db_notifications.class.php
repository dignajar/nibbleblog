<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 23/01/2013

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

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_NOTIFICATIONS($file)
		{
			$this->file_xml = $file;

			if( file_exists($this->file_xml) )
			{
				$this->obj_xml = new NBXML($this->file_xml, 0, TRUE, '', FALSE);
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

		public function add($type, $send_email, $message_key)
		{
			// Email
			if($send_email)
			{
				$sent = Email::send(array('from_name'=>'', 'from_mail'=>'', 'to'=>'', 'subject'=>'', 'message'=>''));
			}

			// Save the notification
			$node = $this->obj_xml->addChild('notification');
			$node->addAttribute('type',			$type);
			$node->addAttribute('mail',			$sent);
			$node->addAttribute('message_key',	$message_key);
			$node->addAttribute('date',			Date::unixstamp());

			$this->savetofile();

			return(true);
		}

		public function get_all()
		{
			$tmp_array = array();
			foreach( $this->obj_xml->notification as $notification )
			{
				$row = array();
				$row['type']			= (string) $notification->getAttribute('type');
				$row['mail']			= (bool) $notification->getAttribute('mail');
				$row['message_key']		= (bool) $notification->getAttribute('message_key');
				$row['date']			= (string) $notification->getAttribute('date');

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
