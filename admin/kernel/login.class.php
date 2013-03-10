<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Login {

	private $session_started;

	function __construct()
	{
		// Set HTTPOnly
		session_set_cookie_params(0, NULL, NULL, NULL, TRUE);

		// Session start
		$this->session_started = session_start();

		// Regenerate the SESSION ID, this for prevent session hijacking "man-in-the-middle attack"
		//session_regenerate_id(true);
	}

	/*
	 * Return a key, with user agent and user IP
	*/
	private function get_key()
	{
		return( Crypt::get_hash( Net::get_user_agent() . Net::get_user_ip() ) );
	}

	/*
	 * Set session variables
	 *
	 * Parameters
	 ** id_user
	 ** username
	*/
	public function set_login($args)
	{
		$_SESSION = array();

		$_SESSION['session_user']['id'] = $args['id_user'];
		$_SESSION['session_user']['username'] = $args['username'];

		$_SESSION['session_login']['at'] = Date::unixstamp();
		$_SESSION['session_login']['key'] = $this->get_key();

		$_SESSION['session_alert']['active'] = false;
		$_SESSION['session_alert']['msg'] = '';
	}

	/*
	 * Check the user is logued
	*/
	public function is_logued()
	{
		if( $this->session_started )
		{
			if( isset($_SESSION['session_user']['id']) && isset($_SESSION['session_login']['key']) )
			{
				if( Text::compare($_SESSION['session_login']['key'], $this->get_key()) )
				{
					return(true);
				}
			}
		}

		return(false);
	}

	/*
	 * Verify the username and password are correct
	 *
	 * Parameters
	 ** username
	 ** password
	*/
	public function verify_login($args)
	{
		require( FILE_SHADOW );

		if( Text::compare($args['username'], $_USER[0]['username']) )
		{
			$hash = Crypt::get_hash($args['password'], $_USER[0]['salt']);

			if( Text::compare($hash, $_USER[0]['password']) )
			{
				$this->set_login( array('id_user'=>0, 'username'=>$args['username']) );
				return(true);
			}
		}

		return(false);
	}

	public function logout()
	{
		$_SESSION = array();

		if(ini_get("session.use_cookies"))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}

		session_destroy();

		$this->session_started = false;

		// Clean remember me
		setcookie('nibbleblog_hash', '', time()-42000, '/');
		setcookie('nibbleblog_id', '', time()-42000, '/');
	}

	public function remember_me()
	{
		require(FILE_KEYS);
		require(FILE_SHADOW);

		// Check cookies
		if( !isset($_COOKIE['nibbleblog_hash']) || !isset($_COOKIE['nibbleblog_id']) )
			return false;

		// Sanitize cookies
		$cookie_hash = Validation::sanitize_html($_COOKIE['nibbleblog_hash']);
		$cookie_id = Validation::sanitize_int($_COOKIE['nibbleblog_id']);

		// Check user id
		if(!isset($_USER[$cookie_id]))
			return false;

		// Generate tmp hash
		$tmp_hash = Crypt::get_hash($_USER[$cookie_id]['username'].$this->get_key(), $_KEYS[2]);

		// Check hash
		if($tmp_hash!=$cookie_hash)
			return false;

		$this->set_login( array('id_user'=>$cookie_id, 'username'=>$_USER[$cookie_id]['username']) );

		return true;
	}

	public function set_remember_me()
	{
		if(!$this->is_logued())
			return false;

		require(FILE_KEYS);

		// Generate tmp hash
		$tmp_hash = Crypt::get_hash($this->get_username().$this->get_key(), $_KEYS[2]);

		// Set cookies
		setcookie('nibbleblog_hash', $tmp_hash, time()+(3600*24*15));
		setcookie('nibbleblog_id', $this->get_user_id(), time()+(3600*24*15));

		return true;
	}

// =================================================================
// Methods for return the session parameters
// =================================================================
	public function get_user_id()
	{
		if( isset($_SESSION['session_user']['id']) )
		{
			return($_SESSION['session_user']['id']);
		}
		else
		{
			return(false);
		}
	}

	public function get_username()
	{
		if( isset($_SESSION['session_user']['username']) )
		{
			return($_SESSION['session_user']['username']);
		}
		else
		{
			return(false);
		}
	}

	public function get_time_user_logued()
	{
		if( isset($_SESSION['session_login']['at']) )
		{
			return($_SESSION['session_login']['at']);
		}
		else
		{
			return(false);
		}
	}

} // END class LOGIN

?>