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
		$code  = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$args['url'].'" data-text="'.$args['text'].'">Tweet</a>';
		$code .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

		return $code;
	}

}

?>