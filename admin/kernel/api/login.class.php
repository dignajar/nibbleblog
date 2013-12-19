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
	private $db_users;

	function Login($started, $db_users)
	{
		$this->session_started = $started;
		$this->db_users = $db_users;
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
		$_SESSION['session_user']['id']			= $args['id_user'];
		$_SESSION['session_user']['username']	= $args['username'];
		$_SESSION['session_login']['key']		= $this->get_key();
	}

	/*
	 * Check if the user is logued
	*/
	public function is_logued()
	{
		if($this->session_started)
		{
			if(isset($_SESSION['session_user']['id']) && isset($_SESSION['session_login']['key']))
			{
				if(Text::compare($_SESSION['session_login']['key'], $this->get_key()))
				{
					return true;
				}
			}
		}

		return false;
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
		// Check the file FILE_SHADOW=shadow.php
		if(!file_exists(FILE_SHADOW))
			return false;

		require(FILE_SHADOW);

		// Check username
		if(Text::compare($args['username'], $_USER[0]['username']))
		{
			// Generate the password hash
			$hash = Crypt::get_hash($args['password'], $_USER[0]['salt']);

			// Check password
			if(Text::compare($hash, $_USER[0]['password']))
			{
				$this->db_users->set(array('username'=>$args['username'], 'session_fail_count'=>0, 'session_date'=>time()));

				$this->set_login( array('id_user'=>0, 'username'=>$args['username']) );

				return true;
			}
		}

		// Set brute force
		$this->db_users->set_blacklist();

		// Increment the failed count and last failed session date
		$user = $this->db_users->get(array('username'=>$args['username']));
		$count = $user['session_fail_count'] + 1;
		$this->db_users->set(array('username'=>$args['username'], 'session_fail_count'=>$count, 'session_date'=>time()));

		return false;
	}

	public function logout()
	{
		// Unset all of the session variables.
		$_SESSION = array();

		if(ini_get("session.use_cookies"))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}

		session_destroy();

		$this->session_started = false;

		// Clean remember me
		setcookie('nibbleblog_hash', '', time()-42000);
		setcookie('nibbleblog_id', '', time()-42000);
	}

	public function remember_me()
	{
		// Check the file FILE_SHADOW=shadow.php
		if(!file_exists(FILE_SHADOW))
			return false;

		require(FILE_SHADOW);

		// Check the file FILE_KEYS=keys.php
		if(!file_exists(FILE_KEYS))
			return false;

		require(FILE_KEYS);

		// Check cookies
		if( !isset($_COOKIE['nibbleblog_hash']) || !isset($_COOKIE['nibbleblog_id']) )
			return false;

		// Sanitize cookies
		$cookie_hash	= Validation::sanitize_html($_COOKIE['nibbleblog_hash']);
		$cookie_id		= Validation::sanitize_int($_COOKIE['nibbleblog_id']);

		// Check user id
		if(!isset($_USER[$cookie_id]))
		{
			// Set brute force
			$this->db_users->set_blacklist();

			// Clean cookies
			setcookie('nibbleblog_hash', '', time()-42000);
			setcookie('nibbleblog_id', '', time()-42000);

			return false;
		}

		// Generate tmp hash
		$tmp_hash = Crypt::get_hash($_USER[$cookie_id]['username'].$this->get_key(), $_KEYS[2]);

		// Check hash
		if($tmp_hash!=$cookie_hash)
		{
			// Set brute force
			$this->db_users->set_blacklist();

			// Clean cookies
			setcookie('nibbleblog_hash', '', time()-42000);
			setcookie('nibbleblog_id', '', time()-42000);

			return false;
		}

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

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/
	/*
	 * Return a key, with user agent and user IP
	*/
	private function get_key()
	{
		return Crypt::get_hash( Net::get_user_agent() . Net::get_user_ip() );
	}

} // END class LOGIN

?>