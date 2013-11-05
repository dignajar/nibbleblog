<?php
header("Content-type: text/xml; charset=utf-8");

require('admin/boot/feed.bit');

$feed_link = BLOG_URL.'feed';

if(!$settings['friendly_urls'])
	$feed_link .= '.php';

$last_post = $posts[0];
$updated = Date::atom($last_post['pub_date_unix']);

// ============================================================================
// ATOM Feed
// ============================================================================
$rss = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
$rss.= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
$rss.= '<title>'.$settings['name'].'</title>' . PHP_EOL;
$rss.= '<subtitle>'.$settings['slogan'].'</subtitle>' . PHP_EOL;
$rss.= '<link href="'.$feed_link.'" rel="self" />' . PHP_EOL;
$rss.= '<id>'.$feed_link.'</id>'. PHP_EOL;
$rss.= '<updated>'.$updated.'</updated>' . PHP_EOL;

foreach($posts as $post)
{
	$full_link = htmlspecialchars($settings['url'].$post['permalink']);

	$date = Date::atom($post['pub_date_unix']);

	$category = htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8');

	if($post['type']=='quote')
	{
		$title = 'quote';
		$content = htmlspecialchars($post['quote'], ENT_QUOTES, 'UTF-8');
	}
	else
	{
		if(Text::not_empty($post['title']))
		{
			$title = htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');
		}
		else
		{
			$title = htmlspecialchars($post['type'], ENT_QUOTES, 'UTF-8');
		}

		// Absolute URL for images
		$domain = $settings['url'];
		$content = preg_replace("/(src)\=\"([^(http)])(\/)?/", "$1=\"$domain$2", $post['content'][1]);

		$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

		if(isset($post['content'][2]))
		{
			$content .= htmlspecialchars('<a href="'.$full_link.'">'.$_LANG['READ_MORE'].'</a>', ENT_QUOTES, 'UTF-8');
		}
	}

	// Entry
	$rss.= '<entry>' . PHP_EOL;
		$rss.= '<title type="html">'.$title.'</title>' . PHP_EOL;
		$rss.= '<content type="html">'.$content.'</content>' . PHP_EOL;
		$rss.= '<link href="'.$full_link.'" />' . PHP_EOL;
		$rss.= '<id>'.$full_link.'</id>' . PHP_EOL;
		$rss.= '<updated>'.$date.'</updated>' . PHP_EOL;
		$rss.= '<category term="'.$category.'"/>' . PHP_EOL;
	$rss.= '</entry>' . PHP_EOL;
}

$rss.= '</feed>';

echo $rss;

?>