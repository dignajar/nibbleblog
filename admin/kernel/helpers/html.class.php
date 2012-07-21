<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_HTML {

	private function get_attributes($array = array())
	{
		unset($array['content']);

		$attributes = '';

		if(isset($array['hidden']) && $array['hidden'])
		{
			$attributes .= 'style="display:none" ';
		}

		unset($array['hidden']);

		foreach( $array as $key=>$value )
		{
			$attributes .= $key.'="'.$value.'" ';
		}

		return($attributes);
	}

	public function h1($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<h1 '.$attributes.'>'.$array['content'].'</h1>' );
	}

	public function h2($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<h2 '.$attributes.'>'.$array['content'].'</h2>' );
	}

	public function h3($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<h3 '.$attributes.'>'.$array['content'].'</h3>' );
	}

	public function h4($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<h4 '.$attributes.'>'.$array['content'].'</h4>' );
	}

	public function blockquote($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<blockquote '.$attributes.'>'.$array['content'].'</blockquote>' );
	}

	public function p($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<p '.$attributes.'>'.$array['content'].'</p>' );
	}

	public function separator($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<div class="separator" '.$attributes.'>'.$array['content'].'</div>' );
	}

	public function form_open($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<form '.$attributes.' >' );
	}

	public function form_close()
	{
		return( '</form>' );
	}

	public function input($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<input '.$attributes.'/>' );
	}

	public function checkbox($array = array(), $checked = false)
	{
		$attributes = $this->get_attributes($array);

		if( $checked )
			return( '<input type="checkbox" '.$attributes.' checked="checked" value="1" />' );
		else
			return( '<input type="checkbox" '.$attributes.' value="1"/>' );
	}

	public function radio($array = array(), $checked = false)
	{
		$attributes = $this->get_attributes($array);

		if( $checked )
			return( '<input type="radio" '.$attributes.' checked="checked" />' );
		else
			return( '<input type="radio" '.$attributes.'/>' );
	}

	public function textarea($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<textarea '.$attributes.'>'.$array['content'].'</textarea>' );
	}

	public function label($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<label '.$attributes.'>'.$array['content'].'</label>' );
	}

	public function select($array = array(), $options = array(), $selected)
	{
		$attributes = $this->get_attributes($array);

		$tmp = '<select '.$attributes.'>';
		foreach( $options as $key=>$value )
		{
			if( $key == $selected)
				$attr = 'selected="selected"';
			else
				$attr = '';

			$tmp .= '<option value="'.$key.'" '.$attr.'>'.$value.'</option>';
		}
		$tmp .= '</select>';

		return( $tmp );
	}

	public function div($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<div '.$attributes.'>'.$array['content'].'</div>' );
	}

	public function div_open($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<div '.$attributes.'>' );
	}

	public function div_close()
	{
		return( '</div>' );
	}

	public function article_open($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<article '.$attributes.'>' );
	}

	public function article_close()
	{
		return( '</article>' );
	}

	public function header_open($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<header '.$attributes.'>' );
	}

	public function header_close()
	{
		return( '</header>' );
	}

	public function link($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<a '.$attributes.'>'.$array['content'].'</a>' );
	}

	public function span($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<span '.$attributes.'>'.$array['content'].'</span>' );
	}

	public function img($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<img '.$attributes.'/>' );
	}

	public function ul($array = array())
	{
		$attributes = $this->get_attributes($array);

		return( '<ul '.$attributes.'>'.$array['content'].'</ul>' );
	}

	public function banner($msg, $success, $error)
	{
		if( $success )
			return('<div class="notification_success">'.$msg.'</div>');
		elseif( $error )
			return('<div class="notification_error">'.$msg.'</div>');
	}

} // END class HTML_HELPER

?>
