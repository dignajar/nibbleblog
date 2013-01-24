<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 23/01/2013

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Email {

	public static function send($args)
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$args['from_name'].' <'.$args['from_mail'].'>' . "\r\n";
		$headers .= 'Reply-To: '.$args['from_name'].' <'.$args['from_mail'].'>' . "\r\n";

		return( mail($args['to'], $args['subject'], $args['message'], $headers) );
	}

}

?>