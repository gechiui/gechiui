<?php
/**
 * GeChiUI database access abstraction class.
 *
 * This file is deprecated, use 'gc-includes/class-gcdb.php' instead.
 *
 * @deprecated 6.1.0
 * @package GeChiUI
 */

if ( function_exists( '_deprecated_file' ) ) {
	// Note: GCINC may not be defined yet, so 'gc-includes' is used here.
	_deprecated_file( basename( __FILE__ ), '6.1.0', 'gc-includes/class-gcdb.php' );
}

/** gcdb class */
require_once __DIR__ . '/class-gcdb.php';
