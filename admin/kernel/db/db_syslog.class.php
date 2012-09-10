<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar
 
 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_SYSLOG {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to the blog_config.xml file
		public $obj_xml; 				// Contains the object of the blog_config.xml file

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_SYSLOG($file)
		{
			$this->file_xml = $file;

			if (file_exists($this->file_xml))
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

		public function add_session()
		{
			global $_NET;
			global $_DATE;
			global $_CRYPT;

			if( count( $this->obj_xml->login->session ) >= AMOUNT_OF_SESSION )
				unset( $this->obj_xml->login->session[0] );


			// For encrypt the user IP
			include(FILE_KEYS);
			$user_ip = $_CRYPT->encrypt($_NET->get_user_ip(), $_KEYS[0]);

			$node = $this->obj_xml->login->addChild('session');

			$node->addAttribute('ip',		$user_ip);
			$node->addAttribute('date',		$_DATE->unixstamp());

			$this->savetofile();

			return(true);
		}

		public function get_all_sessions()
		{
			global $_CRYPT;

			include(FILE_KEYS);

			$tmp_array = array();
			foreach( $this->obj_xml->login->session as $session )
			{
				$user_ip = $_CRYPT->decrypt((string) $session->getAttribute('ip'), $_KEYS[0]);

				$row = array();
				$row['ip']		= $user_ip;
				$row['date']	= (string) $session->getAttribute('date');
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
