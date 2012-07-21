<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_DATE
{
	// Convierte unixstamp a date
	// Devuelve un arreglo y en cada posicion devuelve un dato de la fecha
	// Para ver los datos que devuelve mirar la tabla de la funcion date de php
	// http://php.net/manual/en/function.date.php
	//
	// Parametros
	// $gmt_user -> Zona horaria en la que se encuentra el usuario
	// $time_unix -> Fecha en formato Unix
	public function toarray($time, $gmt = 0)
	{
		$zone = 3600 * $gmt;

		$date = array();

		$date['s'] = date('s', $time + $zone);
		$date['i'] = date('i', $time + $zone);
		$date['h'] = date('h', $time + $zone);
		$date['H'] = date('H', $time + $zone);
		$date['d'] = date('d', $time + $zone);
		$date['N'] = date('N', $time + $zone);
		$date['n'] = date('n', $time + $zone);
		$date['m'] = date('m', $time + $zone);
		$date['Y'] = date('Y', $time + $zone);
		$date['y'] = date('y', $time + $zone);
		$date['a'] = date('a', $time + $zone);
		$date['r'] = date('r', $time + $zone);

		return( $date );
	}

	public function format($time, $format, $gmt = 0)
	{
		$zone = 3600 * $gmt;

		$date = date($format, $time + $zone);

		return( $date );
	}

	public function atom($time, $gmt = 0)
	{
		$zone = 3600 * $gmt;

		$date = date(DATE_ATOM, $time + $zone);

		return( $date );
	}

	// Devuelve la fecha en formato Unixstamp y esta corrida en GMT = 0
	public function unixstamp()
	{
		return( time() - date('Z') );
	}

	public function time_ago($tm, $rcs = 0)
	{
		$cur_tm = time() - date('Z');
		$dif = $cur_tm-$tm;
		$pds = array('seconds','minutes','hours','days','weeks','months','years','decades');
		$lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
		$no = floor($no);
		$x = $no.' '.$pds[$v];
		return($x);
	}

}

?>
