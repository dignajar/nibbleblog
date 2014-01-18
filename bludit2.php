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
// VARIABLES
// =====================================================================
$POSTS_TO_SYNC = 10;

// =====================================================================
// FUNCTIONS
// =====================================================================

// This function make a post recondition, and convert to json
// If you don't want to convert to json, then call the function with false
function post_to_json($post, $tojson=true)
{
	global $settings;
	global $_DB_TAGS;
	global $_DB_CATEGORIES;

	// Permalink
	$post['permalink'] = Url::post($post, true);

	// Get tags
	$post['tags'] = $_DB_TAGS->get_by_idpost( array('id_post'=>$post['id']) );

	// Category
	$category = $_DB_CATEGORIES->get( array('id'=>$post['id_cat']) );
	array_push($post['tags'], $category['name']);

	// Content
	// Src images relatives to absolute
	$domain = $settings['url'];
	$post['content'] = preg_replace("/(src)\=\"([^(http|data:image)])(\/)?/", "$1=\"$domain$2", $post['content'][0]);

	// Unset
	unset($post['read_more']);
	unset($post['filename']);
	unset($post['id_cat']);
	unset($post['id_user']);
	unset($post['mode']);
	unset($post['draft']);
	unset($post['visits']);
	unset($post['allow_comments']);

	if($tojson)
		return json_encode($post);

	return $post;
}

// =====================================================================
// MAIN
// =====================================================================

// Boot
require('admin/boot/blog.bit');

// Blog Keys
require(FILE_KEYS);

if(!isset($_KEYS[0]))
	exit(json_encode(array('error'=>'Nibbleblog: Error key 0')));

if(!isset($_KEYS[1]))
	exit(json_encode(array('error'=>'Nibbleblog: Error key 1')));

// This hash represent your blog on Bludit
$mark = Crypt::get_hash($_KEYS[0]);

// This hash is the key for sync
$key_for_sync = Crypt::get_hash($_KEYS[1]);

if($url['sync']!=$key_for_sync)
	exit(json_encode(array('error'=>'Nibbleblog: Error key for sync')));

// Prevent flood requests
// $_DB_USERS->set_blacklist();

if($url['other']=='status')
{
	$posts = $_DB_POST->get_list_by_page(array('page'=>0, 'amount'=>$POSTS_TO_SYNC));

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
		$sync['post'] = post_to_json($post,false);

		array_push($tmp['posts'], $sync);
	}

	echo json_encode($tmp);
}
elseif($url['other']=='post')
{
	// Get the post
	$post = $_DB_POST->get(array('id'=>$url['id_post']));

	// Post to Json
	echo post_to_json($post);
}
elseif($url['other']=='latest')
{
	$list = $_DB_POST->get_list_by_page(array('page'=>0, 'amount'=>5));

	$tmp = array();

	foreach($list as $post)
		array_push($tmp, post_to_json($post, false));

	echo json_encode($tmp);
}

?>