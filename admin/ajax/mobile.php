<?php header("Content-Type: text/xml");

require('../boot/ajax.bit');


// =====================================================================
//	FUNCTIONS
// =====================================================================

function gen_token($username, $password)
{
	$safe = array();
	$safe['username'] = Validation::sanitize_html($username);
	$safe['password'] = Validation::sanitize_html($password);

	if(!$Login->verify_login( array('username'=>$safe['username'], 'password'=>$safe['password']) ))
		return false;

	if(require(FILE_KEYS)==false)
		return false;

	$token = Crypt::get_hash($safe['username'], $_KEYS[2]);

	return $token;
}

function check_user($username, $password)
{
	global $Login;

	return $Login->verify_login( array('username'=>$safe['username'], 'password'=>$safe['password']) );
}

// =====================================================================
//	MAIN
// =====================================================================

$safe = array();
$safe['username']	= Validation::sanitize_html($_POST['username']);
$safe['password']	= Validation::sanitize_html($_POST['password']);
//$safe['post_type']	= Validation::sanitize_html($_POST['post_type']);

if(check_user($safe['username'], $safe['password']))
{
	$hash = Crypt::get_hash(time());
	$filename = PATH_UPLOAD.$hash.'.jpg';

	if( move_uploaded_file($_FILES["file"]["tmp_name"], $filename) )
	{
		$Resize->setImage($filename, '1024', '720', 'auto');
		$Resize->saveImage($filename, 100);

		exit('OK');
	}

	exit('Fallo subida');
}

exit('Fallo usuario');

?>