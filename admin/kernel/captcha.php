<?php
/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/
header("Content-Type: image/gif");
session_start();

$text = substr(md5(time()), 0, rand(5,8));

$_SESSION['nibbleblog']['captcha'] = $text;

// Parameters
$_URL = array('w'=>90, 'h'=>20, 'bgcolor'=>'255,255,255', 'txcolor'=>'200,25,110');

if(isset($_GET['w']))	$_URL['w'] = (int)$_GET['w'];
if(isset($_GET['h']))	$_URL['h'] = (int)$_GET['h'];
if(isset($_GET['bgcolor'])) $_URL['bgcolor'] = $_GET['bgcolor'];
if(isset($_GET['txcolor'])) $_URL['txcolor'] = $_GET['txcolor'];

// Size 100x20
$captcha = @imagecreate($_URL['w'], $_URL['h']);

// Background color
$exp = explode(',',$_URL['bgcolor']);
$bgcolor = imagecolorallocate($captcha, $exp[0], $exp[1], $exp[2]);

// Text color
$exp = explode(',',$_URL['txcolor']);
$textcolor = imagecolorallocate($captcha, $exp[0], $exp[1], $exp[2]);

// Create GIF
imagestring($captcha, 5, 5, $_URL['h']/2 - 8, $text, $textcolor);
imagegif($captcha);
imagedestroy($captcha);
?>
