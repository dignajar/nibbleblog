<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class NBXML extends SimpleXMLElement
{
	public function addChild($name, $value='', $namespace='')
	{
		//$type	= gettype($value);
		$name	= utf8_encode($name);
		$value	= utf8_encode($value);

		$node = parent::addChild($name);
		$node[0] = $value; // (BUG) Con esta forma escapamos el & que no escapa el addChild

		// Add type
		//$node->addAttribute('type', $type);

      return $node;
	}

	public function addAttribute($name, $value='', $namespace='')
	{
		$name    = utf8_encode($name);
		$value   = utf8_encode($value);

		return parent::addAttribute($name, $value);
	}

	public function getAttribute($name)
	{
		return( utf8_decode((string)$this->attributes()->{$name}) );
	}

	public function setChild($name, $value)
	{
		$this->{$name} = utf8_encode($value);
	}

	public function getChild($name)
	{
		return( utf8_decode((string)$this->{$name}) );
	}

	public function is_set($name)
	{
		return isset($this->{$name});
	}

	public function cast($type, $data)
	{
		if($type=='string')
			return (string) $data;
		elseif(($type=='int') || ($type=='integer'))
			return (int) $data;
		elseif(($type=='bool') || ($type=='boolean'))
			return (bool) $data;
		elseif($type=='float')
			return (float) $data;
		elseif($type=='array')
			return (array) $data;
		elseif($type=='object')
			return (object) $data;

		return $data;
	}

}

?>