<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

if(!isset($Login))
	exit('Nibbleblog security error');

if(!$Login->is_logued())
	exit('Nibbleblog security error');

?>