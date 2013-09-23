<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

// =====================================================================
//	BOOT
// =====================================================================
require('admin/boot/blog.bit');

require(FILE_KEYS);

if(!isset($_KEYS[0]))
	exit('Nibbleblog: Error');

$mark = Crypt::get_hash($_KEYS[0]);

if($_GET['other']=='status')
{
	$posts = $Post->get_by_page(0,10);

	$tmp = array(
		'posts'=>array(),
		'mark'=>$mark
	);

	foreach($posts as $post)
	{
		$time = max($post['pub_date_unix'], $post['mod_date_unix']);

		$sync = array();
		$sync['id'] = $post['id'];
		$sync['time'] = $time;
		$sync['hash'] = Crypt::get_hash(json_encode($post));

		array_push($tmp['posts'], $sync);
	}

	echo json_encode($tmp);
}


?>