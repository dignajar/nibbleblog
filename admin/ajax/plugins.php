<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='set' )
{
	$data = Text::unserialize($_POST['serial_data']);

	include_once( PATH_PLUGINS.$data['plugin'].'/plugin.bit');
	$class = 'PLUGIN_'.strtoupper($data['plugin']);
	$plugin = new $class;
	$plugin->init_db();

	unset( $data['plugin'] );

	$error = !$plugin->set_fields_db($data);
}

if($error)
	exit( Text::ajax_header('<error><![CDATA[1]]></error><alert><![CDATA['.$_LANG['FAIL'].']]></alert>') );
else
	exit( Text::ajax_header('<success><![CDATA[1]]></success><alert><![CDATA['.$_LANG['CHANGES_HAS_BEEN_SAVED_SUCCESSFULLY'].']]></alert>') );

?>