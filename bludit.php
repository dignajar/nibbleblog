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
	$post = $Post->get($url['id_post']);

	//$content = Text::replace('src="', 'src="'.BLOG_URL, $post['content'][0]);
	//$post['content'] = $content;

	$post['content'] = $post['content'][0];

	echo json_encode($post);
}

?>