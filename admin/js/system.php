<?php

Header("content-type: application/x-javascript");

require('../boot/includes/fs_php.bit');
require('../boot/includes/objects.bit');
require('../boot/includes/fs_html.bit');

// ============================================================================
//	PATHS
// ============================================================================

echo 'var HTML_PATH_ROOT = "'.HTML_PATH_ROOT.'";';
echo 'var HTML_PATH_ADMIN = "'.HTML_PATH_ADMIN.'";';
echo 'var HTML_PATH_ADMIN_AJAX = "'.HTML_PATH_ADMIN_AJAX.'";';
echo 'var HTML_PATH_ADMIN_JS = "'.HTML_PATH_ADMIN_JS.'";';
echo 'var HTML_PATH_ADMIN_TEMPLATES = "'.HTML_PATH_ADMIN_TEMPLATES.'";';

// ============================================================================
//	VARS
// ============================================================================

echo 'var _MAX_FILE_SIZE = 1024 * 3000;';


?>
