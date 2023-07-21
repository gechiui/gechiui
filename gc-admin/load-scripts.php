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

require ABSPATH . 'gc-admin/includes/noop.php';
require ABSPATH . GCINC . '/script-loader.php';
require ABSPATH . GCINC . '/version.php';

$expires_offset = 31536000; // 1 year.
$out            = '';

$gc_scripts = new GC_Scripts();
gc_default_scripts( $gc_scripts );
gc_default_packages_vendor( $gc_scripts );
gc_default_packages_scripts( $gc_scripts );

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $gc_version ) {
	header( "$protocol 304 Not Modified" );
	exit;
}

foreach ( $load as $handle ) {
	if ( ! array_key_exists( $handle, $gc_scripts->registered ) ) {
		continue;
	}

	$path = ABSPATH . 'assets' . $gc_scripts->registered[ $handle ]->src;
	$out .= get_file( $path ) . "\n";
}

header( "Etag: $gc_version" );
header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

echo $out;
exit;
