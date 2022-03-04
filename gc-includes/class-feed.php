<?php
/**
 * Feed API
 *
 * @package GeChiUI
 * @subpackage Feed
 * @deprecated 4.7.0
 */

_deprecated_file( basename( __FILE__ ), '4.7.0', 'fetch_feed()' );

if ( ! class_exists( 'SimplePie', false ) ) {
	require_once ABSPATH . GCINC . '/class-simplepie.php';
}

require_once ABSPATH . GCINC . '/class-gc-feed-cache.php';
require_once ABSPATH . GCINC . '/class-gc-feed-cache-transient.php';
require_once ABSPATH . GCINC . '/class-gc-simplepie-file.php';
require_once ABSPATH . GCINC . '/class-gc-simplepie-sanitize-kses.php';
