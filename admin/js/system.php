<?php

Header("content-type: application/x-javascript");

require('../boot/init/1-fs_php.bit');
require('../boot/init/2-objects.bit');
require('../boot/init/3-variables.bit');

// =====================================================================
//	PATHS
// =====================================================================

echo 'var HTML_PATH_ROOT = "'.HTML_PATH_ROOT.'";';
echo 'var HTML_PATH_ADMIN = "'.HTML_PATH_ADMIN.'";';
echo 'var HTML_PATH_ADMIN_AJAX = "'.HTML_PATH_ADMIN_AJAX.'";';
echo 'var HTML_PATH_ADMIN_JS = "'.HTML_PATH_ADMIN_JS.'";';
echo 'var HTML_PATH_ADMIN_TEMPLATES = "'.HTML_PATH_ADMIN_TEMPLATES.'";';

// =====================================================================
//	VARS
// =====================================================================

if( $_DB_SETTINGS->get_wysiwyg() )
{
	echo 'var _WYSIWYG = true;';
}
else
{
	echo 'var _WYSIWYG = false;';
}

echo 'var _MAX_FILE_SIZE = 1024 * 3000;';

?>