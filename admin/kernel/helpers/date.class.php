<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Date {

	public static function set_locale($string)
	{
		if(setlocale(LC_ALL,$string.'.UTF-8')!==false)
			return true;

		if(setlocale(LC_ALL,$string.'.UTF8')!==false)
			return true;

		return setlocale(LC_ALL,$string);
	}

	public static function set_timezone($string)
	{
		return(date_default_timezone_set($string));
	}

	// Return array('Africa/Abidjan'=>'Africa/Abidjan (GMT+0)', ..., 'Pacific/Wallis'=>'Pacific/Wallis (GMT+12)');
	// PHP supported list. http://php.net/manual/en/timezones.php
	public static function get_timezones()
	{
		$tmp = array();

		$timezone_identifiers_list = timezone_identifiers_list();

		foreach($timezone_identifiers_list as $timezone_identifier)
		{
			$date_time_zone = new DateTimeZone($timezone_identifier);
			$date_time = new DateTime('now', $date_time_zone);

			$hours = floor($date_time_zone->getOffset($date_time) / 3600);
			$mins = floor(($date_time_zone->getOffset($date_time) - ($hours*3600)) / 60);

			$hours = 'GMT' . ($hours < 0 ? $hours : '+'.$hours);
			$mins = ($mins > 0 ? $mins : '0'.$mins);

			$text = str_replace("_"," ",$timezone_identifier);

			$tmp[$timezone_identifier]=$text.' ('.$hours.':'.$mins.')';
		}

		return($tmp);
	}

	// Time GMT
	public static function unixstamp()
	{
		return( time() );
	}

	// Format a local time/date according to locale settings
	public static function format($time, $format)
	{
		$date = strftime($format, $time);

		return( $date );
	}

	// Format a GMT/UTC+0 date/time
	public static function format_gmt($time, $format)
	{
		$date = gmdate($format, $time);

		return( $date );
	}

	public static function atom($time)
	{
		$date = date(DATE_ATOM, $time);

		return( $date );
	}

}

?>