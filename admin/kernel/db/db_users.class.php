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

	// Return true if the IP is blocked
	// Return false if the IP is not blocked
	// The IP expires at BRUTEFORCE_TIME + Current time
	public function bruteforce()
	{
		$ip = Net::get_user_ip();
		$current_time = time();

		$node = $this->xml->xpath('/users/bruteforce[@ip="'.utf8_encode($ip).'"]');

		// IP dosen't exist
		if(empty($node))
		{
			if( count( $this->xml->users->bruteforce ) >= BRUTEFORCE_SAVED_REQUESTS )
				unset( $this->xml->users->bruteforce[0] );

			// Add the table
			$node = $this->xml->addChild('bruteforce','');

			// Add the key
			$node->addAttribute('ip', $ip);

			// Add the registers
			$node->addChild('date', $current_time);
			$node->addChild('fail_count', 1);

			// Save the database
			$this->savetofile();

			// The IP is new and there is not brute force
			return false;
		}
		else
		{
			$date = $node[0]->getChild('date');
			$fail_count = $node[0]->getChild('fail_count');

			// The IP expired, so the renewed
			if($current_time > $date + BRUTEFORCE_TIME)
			{
				$node[0]->setChild('date', $current_time);
				$node[0]->setChild('fail_count', 1);

				return false;
			}

			if($fail_count > BRUTEFORCE_LOCKING_AMOUNT)
			{
				return true;
			}
		}

	}


	public function add($args)
	{
		$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

		if(empty($node))
			return false;

		// Add the table
		$node = $this->xml->addChild('user','');

		// Add the key
		$node->addAttribute('username', $args['username']);

		// Add the registers
		$node->addChild('id', 					$args['id']);
		$node->addChild('session_fail_count',	$args['session_fail_count']);
		$node->addChild('session_date',			$args['session_date']);

		return $this->savetofile();
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

	public function set($args)
	{
		$node = $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]');

		if($node== array())
			return false;

		unset($args['username']);

		foreach($args as $key=>$value)
			$node[0]->setChild($key, $value);

		return $this->savetofile();
	}

	public function is_valid($args)
	{
		return $this->xml->xpath('/users/user[@username="'.utf8_encode($args['username']).'"]') != array();
	}

	public function savetofile()
	{
		return $this->xml->asXML($this->file);
	}

/*
========================================================================
	PRIVATE METHODS
========================================================================
*/


} // END Class

?>