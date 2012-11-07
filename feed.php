<?php
header("Content-type: text/xml");

require('admin/boot/feed.bit');

$settings = $_DB_SETTINGS->get();

$posts = $_DB_POST->get_list_by_page( array('page'=>0, 'amount'=>$settings['items_rss']) );

if($settings['friendly_urls'])
{
	$feed_link = $settings['url'].$settings['path'].'feed';
}
else
{
	$feed_link = $settings['url'].$settings['path'].'feed.php';
}

$last_post = $posts[0];
$updated = $_DATE->atom($last_post['pub_date']);

// ============================================================================
// ATOM Feed
// ============================================================================
$rss = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$rss.= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
$rss.= '<title>'.$settings['name'].'</title>' . PHP_EOL;
$rss.= '<subtitle>'.$settings['slogan'].'</subtitle>' . PHP_EOL;
$rss.= '<link href="'.$feed_link.'" rel="self" />' . PHP_EOL;
$rss.= '<id>'.$feed_link.'</id>'. PHP_EOL;
$rss.= '<updated>'.$updated.'</updated>' . PHP_EOL;

foreach($posts as $post)
{
	if($post['type']=='quote')
	{
		$title = 'quote';
		$content = htmlspecialchars($post['quote'], ENT_QUOTES, 'UTF-8');
	}
	else
	{
		if($_TEXT->not_empty($post['title']))
		{
			$title = htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');
		}
		else
		{
			$title = htmlspecialchars($post['type'], ENT_QUOTES, 'UTF-8');
		}

		$content = htmlspecialchars($post['content'][1], ENT_QUOTES, 'UTF-8');
	}

	$full_link = htmlspecialchars($settings['url'].$post['permalink']);

	$date = $_DATE->atom($post['pub_date']);

	// Entry
	$rss.= '<entry>' . PHP_EOL;
		$rss.= '<title type="html">'.$title.'</title>' . PHP_EOL;
		$rss.= '<content type="html">'.$content.'</content>' . PHP_EOL;
		$rss.= '<link href="'.$full_link.'" />' . PHP_EOL;
		$rss.= '<id>'.$full_link.'</id>' . PHP_EOL;
		$rss.= '<updated>'.$date.'</updated>' . PHP_EOL;
	$rss.= '</entry>' . PHP_EOL;
}

$rss.= '</feed>';

echo $rss;

?>
