<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Text {

	public static function unserialize($string)
	{
		parse_str($string, $data);

		// Clean magic quotes if this enabled
		if(get_magic_quotes_gpc())
		{
			$data = self::clean_magic_quotes($data);
		}

		return($data);
	}

	public static function ajax_header($tmp)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xml .= '<ajax>';
		$xml .= $tmp;
		$xml .= '</ajax>';
		return( $xml );
	}

	// Clean magic quotes
	public static function clean_magic_quotes($args)
	{
		$tmp_array = array();
		foreach($args as $key => $arg)
		{
			$tmp_array[$key] = stripslashes($arg);
		}

		return($tmp_array);
	}

	public static function cut_text($text, $maxlength)
	{
		return( substr($text,0,strrpos(substr($text,0,$maxlength)," ")) );
	}

	public static function cut_words($text, $count)
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
	public static function replace($search, $replace, $string)
	{
		return( str_replace($search,$replace,$string) );
	}

	// Strip spaces
	public static function strip_spaces($string)
	{
		return( str_replace(' ','',$string) );
	}

	// Strip quotes ' and "
	public static function strip_quotes($text)
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
	public static function is_substring($string, $substring)
	{
		return( strpos($string, $substring) !== false );
	}

	// RETURN
	// TRUE - is not empty
	// FALSE - is empty
	public static function not_empty($string)
	{
		return( !self::is_empty($string) );
	}

	public static function is_empty($string)
	{
		$string = self::strip_spaces($string);
		return( empty($string) );
	}

	// Compara 2 cadenas
	// Retorna TRUE si son iguales, FALSE caso contrario
	public static function compare($value1, $value2)
	{
		return( strcmp($value1, $value2) == 0 );
	}

	// Clean text for URL
	public static function clean_url($text)
	{
		$text = str_replace(array("!", "*", "&#039;", "&quot;", "(", ")", ";", ":", "@", "&amp", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "|"),'',$text);
		$text = str_replace(" ","-",$text);
		return($text);
	}

	public static function random_text($length)
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