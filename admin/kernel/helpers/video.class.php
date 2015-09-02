<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Video {

	// Get video info on array
	// If the video does not exist or is invalid, returns false
	public static function video_get_info($url, $width = 640, $height = 360)
	{
		if( Text::is_substring($url, 'youtube.com') )
		{
			return( self::video_get_youtube($url, $width, $height) );
		}
		elseif( Text::is_substring($url, 'vimeo.com') )
		{
			return( self::video_get_vimeo($url, $width, $height) );
		}

		return false;
	}

	private static function video_get_youtube($url, $width = 640, $height = 360)
	{
		// Youtube ID
		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
		$video_id = $matches[1];

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = '';
		$info['description'] = '';

		$info['embed'] = '<iframe class="youtube_embed" width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$video_id.'?rel=0" frameborder="0" allowfullscreen></iframe>';

		return($info);
	}

	private static function video_get_vimeo($url, $width = 640, $height = 360)
	{
		preg_match('/vimeo\.com\/([0-9]{1,10})/', $url, $matches);
		$video_id = $matches[1];

		$hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.$video_id.'.php'));

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = $hash[0]['title'];
		$info['description'] = $hash[0]['description'];

		$info['thumb'][0] =  $hash[0]['thumbnail_medium'];
		$info['thumb'][1] =  $hash[0]['thumbnail_small'];

		$info['embed'] = '<iframe class="vimeo_embed" width="'.$width.'" height="'.$height.'" src="https://player.vimeo.com/video/'.$video_id.'"  frameborder="0" allowFullScreen></iframe>';

		return($info);
	}

}

?>