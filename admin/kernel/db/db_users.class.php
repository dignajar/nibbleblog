<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_USERS {

/*
========================================================================
	VARIABLES
========================================================================
*/
		public $file;	// File db
		public $xml;	// Simplexml Obj

/*
========================================================================
	CONSTRUCTORS
========================================================================
*/
		function DB_USERS($file)
		{
			if(file_exists($file))
			{
				$this->file = $file;

				$this->xml = new NBXML($this->file, 0, TRUE, '', FALSE);

				return true;
			}

			return false;
		}

/*
========================================================================
	PUBLIC METHODS
========================================================================
*/
		public function savetofile()
		{
			return $this->xml->asXML($this->file);
		}

		public function add($args)
		{
			$tmp_node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

			if( $tmp_node == array() )
			{
				$new_node = $this->xml->addChild('user','');
				$new_node->addAttribute('id', $args['id'] );
				$new_node->addAttribute('session_fail_count', 0);
				$new_node->addAttribute('session_date', 0);

				return $this->savetofile();
			}

			return false;
		}

		public function get($args)
		{
			$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

			if($node==array())
				return false;

			$tmp = array();
			foreach($node[0]->children() as $field=>$n)
				$tmp[$field] = $node[0]->getChild($field);

			return $tmp;
		}

		public function set_session_fail($args)
		{
			$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

			if($node== array())
				return false;

			$node[0]->setChild('session_fail_count', $args['value']);

			return true;
		}

		public function is_valid($args)
		{
			return $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]') != array();
		}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/
		private function get_autoinc()
		{
			return (int)$this->xml['autoinc'];
		}

		private function set_autoinc($value = 0)
		{
			$this->xml['autoinc'] = $value + $this->get_autoinc();
		}

} // END Class

?>