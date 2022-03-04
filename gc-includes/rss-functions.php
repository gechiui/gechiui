<?php
/**
 * Deprecated. Use rss.php instead.
 *
 * @package GeChiUI
 * @deprecated 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

_deprecated_file( basename( __FILE__ ), '2.1.0', GCINC . '/rss.php' );
require_once ABSPATH . GCINC . '/rss.php';
