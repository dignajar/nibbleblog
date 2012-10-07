<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_VALIDATION {

	function valid_username($username)
	{
		if( $this->is_empty($username) )
		{
			return false;
		}

		if( $this->is_not_alphanumeric($username) )
		{
			return false;
		}

		if( (strlen($username) < 5) && (strlen($username) > 64) )
		{
			return false;
		}

		return true;
	}

	function valid_password($password)
	{
		if( $this->is_empty($password) )
		{
			return false;
		}

		if( (strlen($username) < 5) && (strlen($username) > 64) )
		{
			return false;
		}

		return true;
	}


	public function valid_mail($mail)
	{
		return ( filter_var($mail, FILTER_VALIDATE_EMAIL) );
	}

	public function valid_int($int)
	{
		if($int === 0)
			return( true );
		elseif (filter_var($int, FILTER_VALIDATE_INT) === false )
			return( false );
		else
			return( true );
	}

	// Remove all characters except digits
	public function sanitize_float($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) );
	}

	public function sanitize_int($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_NUMBER_INT) );
	}

	public function sanitize_email($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_EMAIL) );
	}

	public function sanitize_url($valor)
	{
		return( filter_var($valor, FILTER_SANITIZE_URL) );
	}

	// Convert all applicable characters to HTML entities incluye acentos
	public function sanitize_html($text)
	{
		return(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
	}

}

?>
