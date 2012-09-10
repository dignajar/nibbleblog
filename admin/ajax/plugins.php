<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='set' )
{
	parse_str($_POST['serial_data'], $data);

	include_once( PATH_PLUGINS.$data['plugin'].'/plugin.bit');
	$class = 'PLUGIN_'.strtoupper($data['plugin']);
	$plugin = new $class;
	$plugin->init_db();

	unset( $data['plugin'] );

	$error = $plugin->set_fields_db($data);
}

if( $error)
{
	exit( $_TEXT->ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( $_TEXT->ajax_header('<success><![CDATA[1]]></success>') );
}
?>
