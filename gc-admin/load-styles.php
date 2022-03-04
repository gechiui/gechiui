<?php

/*
 * Disable error reporting.
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting( 0 );

// Set ABSPATH for execution.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

define( 'GCINC', 'gc-includes' );
define( 'GC_CONTENT_DIR', ABSPATH . 'gc-content' );

require ABSPATH . 'gc-admin/includes/noop.php';
require ABSPATH . GCINC . '/theme.php';
require ABSPATH . GCINC . '/class-gc-theme-json-resolver.php';
require ABSPATH . GCINC . '/global-styles-and-settings.php';
require ABSPATH . GCINC . '/script-loader.php';
require ABSPATH . GCINC . '/version.php';

$protocol = $_SERVER['SERVER_PROTOCOL'];
if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
	$protocol = 'HTTP/1.0';
}

$load = $_GET['load'];
if ( is_array( $load ) ) {
	ksort( $load );
	$load = implode( '', $load );
}

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = array_unique( explode( ',', $load ) );

if ( empty( $load ) ) {
	header( "$protocol 400 Bad Request" );
	exit;
}

$rtl            = ( isset( $_GET['dir'] ) && 'rtl' === $_GET['dir'] );
$expires_offset = 31536000; // 1 year.
$out            = '';

$gc_styles = new GC_Styles();
gc_default_styles( $gc_styles );

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $gc_version ) {
	header( "$protocol 304 Not Modified" );
	exit;
}

foreach ( $load as $handle ) {
	if ( ! array_key_exists( $handle, $gc_styles->registered ) ) {
		continue;
	}

	$style = $gc_styles->registered[ $handle ];

	if ( empty( $style->src ) ) {
		continue;
	}

	$path = ABSPATH . $style->src;

	if ( $rtl && ! empty( $style->extra['rtl'] ) ) {
		// All default styles have fully independent RTL files.
		$path = str_replace( '.min.css', '-rtl.min.css', $path );
	}

	$content = get_file( $path ) . "\n";

	if ( strpos( $style->src, '/' . GCINC . '/css/' ) === 0 ) {
		$content = str_replace( '../images/', '../' . GCINC . '/images/', $content );
		$content = str_replace( '../js/tinymce/', '/assets/vendors/tinymce/', $content );
		$content = str_replace( '../fonts/', '../' . GCINC . '/fonts/', $content );
		$out    .= $content;
	} else {
		$out .= str_replace( '../images/', 'images/', $content );
	}
}

header( "Etag: $gc_version" );
header( 'Content-Type: text/css; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

echo $out;
exit;
