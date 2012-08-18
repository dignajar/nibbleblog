<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_TEXT {

	public function ajax_header($tmp)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xml .= '<ajax>';
		$xml .= $tmp;
		$xml .= '</ajax>';
		return( $xml );
	}

	// Clean magic quotes
	public function clean_magic_quotes($args)
	{
		$tmp_array = array();
		foreach($args as $key => $arg)
		{
			$tmp_array[$key] = stripslashes($arg);
		}

		return($tmp_array);
	}

	public function cut_text($text, $maxlength)
	{
		return( substr($text,0,strrpos(substr($text,0,$maxlength)," ")) );
	}

	public function cut_words($text, $count)
	{
		$explode = explode(" ", $text);

		if(count($explode) > $count)
		{
			array_splice($explode, $count);
			$text = implode(' ', $explode);
		}

		return($text);
	}

	// Strip spaces
	public function replace($search, $replace, $string)
	{
		return( str_replace($search,$replace,$string) );
	}

	// Strip spaces
	public function strip_spaces($string)
	{
		return( str_replace(' ','',$string) );
	}

	// Strip quotes ' and "
	public function strip_quotes($text)
	{
		$text = str_replace('\'', '', $text);
		$text = str_replace('"', '', $text);
		return( $text );
	}

	function clean_non_alphanumeric($string)
	{
		$string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);

		return $string;
	}

	// RETURN
	// TRUE - si contiene el substring
	// FALSE - caso contrario
	public function is_substring($string, $substring)
	{
		return( strpos($string, $substring) !== false );
	}

	// RETURN
	// TRUE - is not empty
	// FALSE - is empty
	public function not_empty($string)
	{
		return( !$this->is_empty($string) );
	}

	public function is_empty($string)
	{
		$string = $this->strip_spaces($string);
		return( empty($string) );
	}

	// Compara 2 cadenas
	// Retorna TRUE si son iguales, FALSE caso contrario
	public function compare($value1, $value2)
	{
		return( strcmp($value1, $value2) == 0 );
	}

	// Clean text for URL
	public function clean_url($text)
	{
		$text = str_replace(array("!", "*", "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]"),"",$text);
		$text = str_replace(" ","_",$text);

		return($text);
	}

	function get_slug_url($string)
	{
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower($string);
		//Strip any unwanted characters
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);

		return $string;
	}

	function random_text($length)
	{
		 $characteres = "1234567890abcdefghijklmnopqrstuvwxyz!@#%^&*";
		 $text = '';
		 for($i=0; $i<$length; $i++)
		 {
			$text .= $characteres{rand(0,41)};
		 }
		 return $text;
	}

}

?>
