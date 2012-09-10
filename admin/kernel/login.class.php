<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class LOGIN {

	// Variable que identifica si la session se inicio correctamente
	private $session_started;

	function LOGIN()
	{
		$this->session_started = session_start();
	}

	private function get_key()
	{
		global $_NET;
		global $_CRYPT;

		return( $_CRYPT->get_hash( $_NET->get_user_agent() . $_NET->get_user_ip() ) );
	}

	// Setea todos los parametros que un usuario logueado debe tener
	public function set_login($args)
	{
		global $_NET;
		global $_DATE;

		$this->set_token();

		$_SESSION = array();

		$_SESSION['session_user']['id'] = $args['id_user'];
		$_SESSION['session_user']['username'] = $args['username'];

		$_SESSION['session_login']['at'] = $_DATE->unixstamp();
		$_SESSION['session_login']['key'] = $this->get_key();

		$_SESSION['session_alert']['active'] = false;
		$_SESSION['session_alert']['msg'] = '';
	}

	// Comprueba que el usuario esta logueado
	public function is_logued()
	{
		global $_TEXT;

		if( $this->session_started )
		{
			if( isset($_SESSION['session_user']['id']) && isset($_SESSION['session_login']['key']) )
			{
				if( $_TEXT->compare($_SESSION['session_login']['key'], $this->get_key()) )
				{
					return(true);
				}
			}
		}
		return(false);
	}

	// Comprueba un usuario y contraseña, si es correcto seteo el login
	public function verify_login($args)
	{
		global $_CRYPT;
		global $_TEXT;

		require( FILE_SHADOW );

		if( $_TEXT->compare($args['username'], $_USER[0]['username']) )
		{
			$hash = $_CRYPT->get_hash($args['password'], $_USER[0]['salt']);

			if( $_TEXT->compare($hash, $_USER[0]['password']) )
			{
				$this->set_login( array('id_user'=>0, 'username'=>$args['username']) );
				return(true);
			}
		}

		return(false);
	}

	// Desloguea un usuario
	public function logout()
	{
		$_SESSION = array();

		if (ini_get("session.use_cookies"))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}

		session_destroy();

		$this->session_started = false;
	}


// =================================================================
// GET Parametros del usuario logueado
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

// =================================================================
// TOKEN
// =================================================================

	public function set_token()
	{
		global $_TEXT;

		// expire 5hour
		setcookie('cookie_token', $_TEXT->random_text(10), time()+(3600*5), '/');
	}

	public function get_token()
	{
		if( isset($_COOKIE['cookie_token']) )
		{
			return($_COOKIE['cookie_token']);
		}
		else
		{
			return(false);
		}
	}

	public function valid_token($token)
	{
		return( (strcmp($token, $this->get_token()) == 0) && isset($_COOKIE['cookie_token']) );
	}

// =================================================================
// REMEMBER ME
// =================================================================

	public function remember_me()
	{
		global $_USERS;

		if( isset($_COOKIE['cookie_God_hash']) && isset($_COOKIE['cookie_God_id']) )
		{
			$god_hash = $this->sanitize_html($_COOKIE['cookie_God_hash']);
			$god_id = $this->sanitize_int($_COOKIE['cookie_God_id']);

			$hash = $this->get_hash($god_id.$this->get_user_ip());

			if($hash == $god_hash)
			{
				$user = $_USERS->get_by_id( array('id_user'=>$god_id) );

				if( $user['remember'] == $hash )
				{
					$this->set_login( array('id_user'=>$user['id_user'], 'username'=>$user['username'], 'mail'=>$user['mail']) );

					return(true);
				}
			}

			// Si tenia las cookies de remember me y no tuvo exito, borro las cookies y todo.
			$this->logout();
		}

		return(false);
	}

	public function set_remember_me($args)
	{
		global $_USERS;

		$hash = $this->get_hash($args['id_user'].$this->get_user_ip());

		setcookie('cookie_God_hash', $hash, time()+(3600*24*15), '/');
		setcookie('cookie_God_id', $args['id_user'], time()+(3600*24*15), '/');

		$_USERS->set_remember( array('hash'=>$hash) );
	}

// =================================================================
// FORGOT PASSWORD
// =================================================================

	public function forgot_password($args)
	{
		global $_USERS;

		$user = $_USERS->get_by_username( array('username'=>$args['username']) );

		if($user == false)
		{
			return false;
		}

		if( $this->is_empty($user['forgot']) )
		{
			return false;
		}

		$explode = explode("_",$user['forgot']);

		// Veo si el hash caduco
		if( (($this->unixstamp() - $explode[1]) > (3600*24*2)) )
		{
			$_USERS->set_forgot( array('mail'=>$user['mail'], 'hash'=>'') );

			return false;
		}

		if($args['hash'] == $explode[0])
		{
			$this->set_login( array('id_user'=>$user['id_user'], 'username'=>$user['username'], 'mail'=>$user['mail']) );

			return true;
		}
	}

} // END class LOGIN

?>
