<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Language {

	private $lang = array();

	public function get($key)
	{
		$key = strtoupper($key);

		$key = str_replace(' ','_',$key);

		if(isset($this->lang[$key]))
			return $this->lang[$key];

		return '';
	}

	public function set($array)
	{
		$this->lang = $array;

		return true;
	}

	public function add($array)
	{
		$this->lang = array_merge($this->lang, $array);

		return true;
	}

}

?>