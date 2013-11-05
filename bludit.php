<?php
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

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

if($url['other']=='status')
{
	$posts = $Post->get_by_page(0,10);

	$posts = array_reverse($posts);

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
elseif($url['other']=='post')
{
	// Get the post
	$post = $Post->get($url['id_post']);

	// Get tags
	$post['tags'] = $_DB_TAGS->get_by_idpost( array('id_post'=>$post['id']) );

	// Src images relatives to absoluts
	$domain = $settings['url'];
	$post['content'] = preg_replace("/(src)\=\"([^(http)])(\/)?/", "$1=\"$domain$2", $post['content'][0]);

	// Unset
	unset($post['read_more']);
	unset($post['filename']);
	unset($post['id_cat']);
	unset($post['id_user']);
	unset($post['mode']);
	unset($post['draft']);
	unset($post['visits']);
	unset($post['allow_comments']);

	// JSON
	echo json_encode($post);
}
elseif($url['other']=='latest')
{
	$list = $Post->get_by_page(0, 5);
	$tmp = array();

	foreach($list as $post)
	{
		// Permalink
		$post['permalink'] = Url::post($post, true);

		// Get tags
		$post['tags'] = $_DB_TAGS->get_by_idpost( array('id_post'=>$post['id']) );

		// Content
		// Src images relatives to absoluts
		$domain = $settings['url'];
		$post['content'] = preg_replace("/(src)\=\"([^(http)])(\/)?/", "$1=\"$domain$2", $post['content'][0]);

		// Unset
		unset($post['read_more']);
		unset($post['filename']);
		unset($post['id_cat']);
		unset($post['id_user']);
		unset($post['mode']);
		unset($post['draft']);
		unset($post['visits']);
		unset($post['allow_comments']);

		array_push($tmp, $post);
	}

	echo json_encode($tmp);
}

?>