<?php
header("Content-type: text/xml; charset=utf-8");

require('admin/boot/feed.bit');

// Get the last update (the date of the last published post)
$updated = Date::atom(time());
if(isset($posts[0]))
{
	$last_post = $posts[0];
	$updated = Date::atom($last_post['pub_date_unix']);
}

// Get the domain name
$domain = parse_url($settings['url']);
$domain = 'http://'.$domain['host'];

// =====================================================================
// ATOM Feed
// =====================================================================
$rss = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
$rss.= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
$rss.= '<title>'.$settings['name'].'</title>' . PHP_EOL;
$rss.= '<subtitle>'.$settings['slogan'].'</subtitle>' . PHP_EOL;
$rss.= '<link href="'.Url::atom().'" rel="self" />' . PHP_EOL;
$rss.= '<id>'.Url::atom().'</id>'. PHP_EOL;
$rss.= '<updated>'.$updated.'</updated>' . PHP_EOL;

foreach($posts as $post)
{
	// Post, absolute permalink
	$permalink = Post::permalink(true);

	// Post, publish date on atom format
	$date = Date::atom($post['pub_date_unix']);

	// Post, category name
	$category = Post::category();

	// Post, full content
	$content = Post::content(true);

	// Absolute images src
	$content = preg_replace("/(src)\=\"([^(http|data:image)])(\/)?/", "$1=\"$domain$2", $content);

	// Post, title
	$title = Post::title();

	// Entry
	$rss.= '<entry>' . PHP_EOL;
		$rss.= '<title type="html">'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</title>' . PHP_EOL;
		$rss.= '<content type="html">'.htmlspecialchars($content, ENT_QUOTES, 'UTF-8').'</content>' . PHP_EOL;
		$rss.= '<link href="'.htmlspecialchars($permalink, ENT_QUOTES, 'UTF-8').'" />' . PHP_EOL;
		$rss.= '<id>'.htmlspecialchars($permalink, ENT_QUOTES, 'UTF-8').'</id>' . PHP_EOL;
		$rss.= '<updated>'.htmlspecialchars($date, ENT_QUOTES, 'UTF-8').'</updated>' . PHP_EOL;
		$rss.= '<category term="'.htmlspecialchars($category, ENT_QUOTES, 'UTF-8').'"/>' . PHP_EOL;
	$rss.= '</entry>' . PHP_EOL;
}

$rss.= '</feed>';

echo $rss;

?>