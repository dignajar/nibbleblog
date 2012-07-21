<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_REDIRECT {

	public function url($html_location)
	{
		header("Location:".$html_location);
		exit('<a href="'.$html_location.'">click here to continue</a>');
	}

	public function controller($base, $controller, $action, $parameters = array())
	{
		$url = '';

		foreach( $parameters as $key=>$value )
		{
			$url .= '&'.$key.'='.$value;
		}

		$this->url(HTML_PATH_ROOT.$base.'.php?controller='.$controller.'&action='.$action.$url);
	}
}

?>
