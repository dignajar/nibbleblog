<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Url {

	public static function category($slug, $permalink=false)
	{
		if($permalink)
		{
			return HTML_PATH_ROOT.'category/'.$slug.'/';
		}
		else
		{
			return HTML_PATH_ROOT.'index.php?controller=blog&action=view&category='.$slug;
		}
	}

	public static function tag($slug, $permalink=false)
	{
		if($permalink)
		{
			return HTML_PATH_ROOT.'tag/'.$slug.'/';
		}
		else
		{
			return HTML_PATH_ROOT.'index.php?controller=blog&action=view&tag='.$slug;
		}
	}

	public static function post($post, $translit=false, $permalink=false)
	{
		if($permalink)
		{
			if(  Text::not_empty($post['slug']) )
				$slug = $post['slug'];
			elseif( Text::not_empty($post['title']) )
				$slug = Text::clean_url($post['title'], '-', $translit);
			else
				$slug = $post['type'];

			return HTML_PATH_ROOT.'post-'.$post['id'].'/'.$slug;
		}
		else
		{
			return HTML_PATH_ROOT.'index.php?controller=post&action=view&id_post='.$post['id'];
		}
	}

}

?>