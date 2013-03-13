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
		$name    = utf8_encode($name);
		$value   = utf8_encode($value);

		$node = parent::addChild($name);
		$node[0] = $value; // (BUG) Con esta forma escapamos el & que no escapa el addChild

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

}

?>