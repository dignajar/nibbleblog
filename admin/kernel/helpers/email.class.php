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
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$args['from']. "\r\n";

		return mail($args['to'], $args['subject'], $args['message'], $headers);
	}

}

?>