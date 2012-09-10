<?php
header("Content-type: text/xml");

require('admin/boot/feed.bit');

$settings = $_DB_SETTINGS->get();

$posts = $_DB_POST->get_list_by_page( array('page'=>0, 'amount'=>$settings['items_rss']) );

// ============================================================================
// ATOM Feed
// ============================================================================
$rss = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
$rss.= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
$rss.= '<title>'.$settings['name'].'</title>' . PHP_EOL;
$rss.= '<subtitle>'.$settings['slogan'].'</subtitle>' . PHP_EOL;
$rss.= '<link href="'.$settings['url'].$settings['path'].'" />' . PHP_EOL;
$rss.= '<link href="'.$settings['url'].$settings['path'].'feed.php" rel="self" />' . PHP_EOL;

foreach($posts as $post)
{
	// CHECK TYPE!!
	$rss.= '<entry>' . PHP_EOL;
		$rss.= '<title type="html"><![CDATA['.utf8_encode($post['title']).']]></title>' . PHP_EOL;
		$rss.= '<summary type="html"><![CDATA['.utf8_encode($post['content_part0']).']]></summary>' . PHP_EOL;
		$rss.= '<link href="'.$post['permalink'].'" />' . PHP_EOL;
		$rss.= '<updated>'.$_DATE->atom($post['pub_date']).'</updated>' . PHP_EOL;
	$rss.= '</entry>' . PHP_EOL;
}

echo $rss;

?>
