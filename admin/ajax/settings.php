<?php header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('security.bit');

if($_POST['action']=='set')
{
	$data = Text::unserialize($_POST['serial_data']);

	foreach( $data as $name=>$value )
		$safe[$name] = Validation::sanitize_html($value);

	$_DB_SETTINGS->set($safe);

	$error = !$_DB_SETTINGS->savetofile();
}

if($error)
	exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA['.$_LANG['FAIL'].']]></alert>') );
else
	exit( Text::ajax_header('<success><![CDATA[1]]></success><alert><![CDATA['.$_LANG['CHANGES_HAS_BEEN_SAVED_SUCCESSFULLY'].']]></alert>') );

?>