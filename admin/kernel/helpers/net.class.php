<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_NETWORK {

	// return string
	public function get_user_ip()
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
			$realip = getenv('HTTP_X_FORWARDED_FOR');
		elseif (getenv('HTTP_CLIENT_IP'))
			$realip = getenv('HTTP_CLIENT_IP');
		else
			$realip = getenv('REMOTE_ADDR');

		return($realip);
	}

	public function get_user_agent()
	{
		return( getenv('HTTP_USER_AGENT') );
	}

}

?>
