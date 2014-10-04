<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Url {

	public static function category($slug, $absolute=false)
	{
		global $settings;

		$url = $absolute?BLOG_URL:HTML_PATH_ROOT;

		if($settings['friendly_urls'])
		{
			return $url.'category/'.$slug.'/';
		}
		else
		{
			return $url.'index.php?controller=blog&amp;action=view&amp;category='.$slug;
		}
	}

	public static function tag($slug, $absolute=false)
	{
		global $settings;

		$url = $absolute?BLOG_URL:HTML_PATH_ROOT;

		if($settings['friendly_urls'])
		{
			return $url.'tag/'.$slug.'/';
		}
		else
		{
			return $url.'index.php?controller=blog&amp;action=view&amp;tag='.$slug;
		}
	}

	public static function post($post, $absolute=false)
	{
		global $settings;

		$url = $absolute?BLOG_URL:HTML_PATH_ROOT;

		if($settings['friendly_urls'])
		{
			return $url.'post/'.$post['slug'].'/';
		}
		else
		{
			return $url.'index.php?controller=post&amp;action=view&amp;id_post='.$post['id'];
		}
	}

	public static function page($page, $absolute=false)
	{
		global $settings;

		$url = $absolute?BLOG_URL:HTML_PATH_ROOT;

		if($settings['friendly_urls'])
		{
			return $url.'page/'.$page['slug'].'/';
		}
		else
		{
			return $url.'index.php?controller=page&amp;action=view&amp;id_page='.$page['id'];
		}
	}

	public static function atom()
	{
		global $settings;

		if($settings['friendly_urls'])
		{
			return BLOG_URL.'feed';
		}
		else
		{
			return BLOG_URL.'feed.php';
		}
	}

	public static function sitemap()
	{
		global $settings;

		if($settings['friendly_urls'])
		{
			return BLOG_URL.'sitemap';
		}
		else
		{
			return BLOG_URL.'sitemap.php';
		}
	}
	
}

?>
