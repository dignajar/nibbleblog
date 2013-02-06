<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Email {

	public static function send($args)
	{
		if( Validation::mail($args['from_mail']) && Validation::mail($args['to']) )
		{
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.$args['from_mail']. "\r\n";

			return( mail($args['to'], $args['subject'], $args['message'], $headers) );
		}

		return(false);
	}

}

?>