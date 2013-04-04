<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Social {

	public static function twitter_share($args = array())
	{
		// HTML Code
		$code  = '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		$code .= '<a href="http://twitter.com/share" class="twitter-share-button" data-url="'.$args['url'].'" data-text="'.$args['text'].'">Tweet</a>';

		return $code;
	}

}

?>