<?php
header("Content-type: text/xml; charset=utf-8");

require('admin/boot/feed.bit');

// =====================================================================
// Sitemap
// =====================================================================
$rss = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
$rss.= '<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

$rss.='<url>' . PHP_EOL;
$rss.='<loc>'.$settings['url'].'</loc>' . PHP_EOL;
$rss.='<changefreq>always</changefreq>' . PHP_EOL;
$rss.='</url>' . PHP_EOL;

foreach($pages as $page)
{
	$permalink = Page::permalink(true);

	$rss.='<url>' . PHP_EOL;
	$rss.='<loc>'.$permalink.'</loc>' . PHP_EOL;
	$rss.='<changefreq>always</changefreq>' . PHP_EOL;
	$rss.='</url>' . PHP_EOL;
}

foreach($posts as $post)
{
	$permalink = Post::permalink(true);

	$rss.='<url>' . PHP_EOL;
	$rss.='<loc>'.$permalink.'</loc>' . PHP_EOL;
	$rss.='<changefreq>always</changefreq>' . PHP_EOL;
	$rss.='</url>' . PHP_EOL;
}

$rss.= '</urlset>';

echo $rss;

?>