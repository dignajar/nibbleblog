<?php
header("Content-type: text/xml; charset=utf-8");

require('admin/boot/sitemap.bit');

// =====================================================================
// Sitemap
// =====================================================================
$smap = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
$smap.= '<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

$smap.='<url>' . PHP_EOL;
$smap.='<loc>'.$settings['url'].'</loc>' . PHP_EOL;
$smap.='<changefreq>always</changefreq>' . PHP_EOL;
$smap.='</url>' . PHP_EOL;

foreach($pages as $page)
{
	$permalink = Page::permalink(true);

	$smap.='<url>' . PHP_EOL;
	$smap.='<loc>'.$permalink.'</loc>' . PHP_EOL;
	$smap.='<changefreq>always</changefreq>' . PHP_EOL;
	$smap.='</url>' . PHP_EOL;
}

foreach($posts as $post)
{
	$permalink = Post::permalink(true);

	$smap.='<url>' . PHP_EOL;
	$smap.='<loc>'.$permalink.'</loc>' . PHP_EOL;
	$smap.='<changefreq>always</changefreq>' . PHP_EOL;
	$smap.='</url>' . PHP_EOL;
}

$smap.= '</urlset>';

echo $smap;

?>
