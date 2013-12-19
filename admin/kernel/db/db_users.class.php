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
		}
	}

/*
========================================================================
	PUBLIC METHODS
========================================================================
*/

	public function blacklist()
	{
		$ip = Net::get_user_ip();
		$current_time = time();

		$node = $this->xml->xpath('/users/blacklist[@ip="'.utf8_encode($ip).'"]');

		// IP dosen't exist
		if(empty($node))
			return false;

		$date = $node[0]->getChild('date');
		$fail_count = $node[0]->getChild('fail_count');

		// The IP expired, then is not blocked
		if($current_time > $date + (BLACKLIST_TIME*60))
			return false;

		// The IP has more fails than BLACKLIST_LOCKING_AMOUNT, then the IP is blocked
		if($fail_count >= BLACKLIST_LOCKING_AMOUNT)
			return true;

		// Other ways the IP is not blocked
		return false;
	}

	public function set_blacklist()
	{
		$ip = Net::get_user_ip();
		$current_time = time();

		$node = $this->xml->xpath('/users/blacklist[@ip="'.utf8_encode($ip).'"]');

		// IP dosen't exist
		if(empty($node))
		{
			if( count( $this->xml->users->blacklist ) >= BLACKLIST_SAVED_REQUESTS )
				unset( $this->xml->users->blacklist[0] );

			// Add the table
			$node = $this->xml->addChild('blacklist','');

			// Add the key
			$node->addAttribute('ip', $ip);

			// Add the registers
			$node->addChild('date', $current_time);
			$node->addChild('fail_count', 1);

			error_log('Nibbleblog: Blacklist - New IP added - '.$ip);
		}
		else
		{
			$date = $node[0]->getChild('date');
			$fail_count = $node[0]->getChild('fail_count');

			// The IP expired, so renewed
			if($current_time > $date + (BLACKLIST_TIME*60))
			{
				$node[0]->setChild('date', $current_time);
				$node[0]->setChild('fail_count', 1);

				error_log('Nibbleblog: Blacklist - IP renewed because is expired - '.$ip);
			}
			else
			{
				$fail_count += 1;
				$node[0]->setChild('fail_count', $fail_count);

				error_log('Nibbleblog: Blacklist - IP fail count('.$fail_count.') - '.$ip);
			}
		}

		// Save the database
		return $this->savetofile();
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