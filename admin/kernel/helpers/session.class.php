<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Session {

	public static function init()
	{
		$comment = array(
			'author_name'=>'',
			'author_email'=>'',
			'content'=>'',
			'hash'=>'',
			'post_allow_comments'=>false,
			'id_post'=>0
		);

		$_SESSION['nibbleblog'] = array(
			'error'=>false,
			'alert'=>'',
			'comment'=>$comment,
			'last_comment_at'=>0,
			'last_session_at'=>0,
			'fail_session'=>0
		);
	}

	public static function reset()
	{
		$last_comment_at = $_SESSION['nibbleblog']['last_comment_at'];
		$last_session_at = $_SESSION['nibbleblog']['last_session_at'];
		$fail_session = $_SESSION['nibbleblog']['fail_session'];

		self::init();

		$_SESSION['nibbleblog']['last_comment_at'] = $last_comment_at;
		$_SESSION['nibbleblog']['last_session_at'] = $last_session_at;
		$_SESSION['nibbleblog']['fail_session'] = $fail_session;
	}

	public static function get($name)
	{
		if(isset($_SESSION['nibbleblog'][$name]))
			return $_SESSION['nibbleblog'][$name];
		else
			return false;
	}

	public static function get_error()
	{
		if(isset($_SESSION['nibbleblog']['error']))
		{
			return($_SESSION['nibbleblog']['error']);
		}

		return false;
	}

	public static function get_last_comment_at()
	{
		return($_SESSION['nibbleblog']['last_comment_at']);
	}

	public static function get_last_session_at()
	{
		return($_SESSION['nibbleblog']['last_session_at']);
	}

	public static function get_fail_session()
	{
		return($_SESSION['nibbleblog']['fail_session']);
	}

	public static function get_alert()
	{
		self::set_error(false);
		return($_SESSION['nibbleblog']['alert']);
	}

	public static function get_comment($key)
	{
		if(isset($_SESSION['nibbleblog']['comment'][$key]))
		{
			return($_SESSION['nibbleblog']['comment'][$key]);
		}

		return false;
	}

	public static function get_comment_array()
	{
		return($_SESSION['nibbleblog']['comment']);
	}

	public static function set_error($boolean = true)
	{
		$_SESSION['nibbleblog']['error'] = $boolean;
	}

	public static function set_last_comment_at($time)
	{
		$_SESSION['nibbleblog']['last_comment_at'] = $time;
	}

	public static function set_last_session_at($time)
	{
		$_SESSION['nibbleblog']['last_session_at'] = $time;
	}

	public static function set_fail_session($amount)
	{
		$_SESSION['nibbleblog']['fail_session'] = $amount;
	}

	public static function set_alert($text = '')
	{
		self::set_error(true);
		$_SESSION['nibbleblog']['alert'] = $text;
	}

	public static function set_comment($comment)
	{
		$_SESSION['nibbleblog']['comment']['author_name'] = $comment['author_name'];
		$_SESSION['nibbleblog']['comment']['author_email'] = $comment['author_email'];
		$_SESSION['nibbleblog']['comment']['content'] = $comment['content'];
		$_SESSION['nibbleblog']['comment']['post_allow_comments'] = $comment['post_allow_comments'];
		$_SESSION['nibbleblog']['comment']['id_post'] = $comment['id_post'];
	}

	public static function set($key, $value)
	{
		$_SESSION['nibbleblog'][$key] = $value;
	}

}

?>