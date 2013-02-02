<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Validation {

	public static function mail($mail)
	{
		return ( filter_var($mail, FILTER_VALIDATE_EMAIL) );
	}

	public static function int($int)
	{
		if($int === 0)
			return( true );
		elseif (filter_var($int, FILTER_VALIDATE_INT) === false )
			return( false );
		else
			return( true );
	}

	// Remove all characters except digits
	public static function sanitize_float($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) );
	}

	public static function sanitize_int($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_NUMBER_INT) );
	}

	public static function sanitize_email($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_EMAIL) );
	}

	public static function sanitize_url($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_URL) );
	}

	// Convert all applicable characters to HTML entities incluye acentos
	public static function sanitize_html($text)
	{
		return(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
	}

	public static function captcha($captcha)
	{
		global $_LANG;

		$captcha = self::sanitize_html($captcha);

		if(Session::get_captcha()==$captcha)
		{
			Session::set_error(false);
			Session::set_alert('');
			return(true);
		}

		Session::set_error(true);
		Session::set_alert($_LANG['INVALID_CAPTCHA']);
		return(false);
	}

}

?>