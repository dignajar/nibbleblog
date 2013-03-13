<?php
header("Content-Type: text/xml");

require('../boot/ajax.bit');
require('../kernel/security.bit');

if( $_POST['action']=='delete' )
{
	$safe['id'] = $_POST['id'];

	$error = !$_DB_CATEGORIES->delete($safe);
}
elseif( $_POST['action']=='set' )
{
	$data = Text::unserialize($_POST['serial_data']);

	foreach( $data as $id=>$name )
	{
		$safe = array();
		$safe['id'] = $id;
		$safe['name'] = Validation::sanitize_html($name);

		if(Text::not_empty($safe['name']))
		{
			$_DB_CATEGORIES->set($safe);
		}
	}

	$error = !$_DB_CATEGORIES->savetofile();
}

if( $error )
{
	exit( Text::ajax_header('<error><![CDATA[1]]></error>') );
}
else
{
	exit( Text::ajax_header('<success><![CDATA[1]]></success>') );
}
?>
