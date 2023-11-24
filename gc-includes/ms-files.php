<?php
/**
 * Multisite upload handler.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

define( 'MS_FILES_REQUEST', true );
define( 'SHORTINIT', true );
require_once dirname( __DIR__ ) . '/gc-load.php';

if ( ! is_multisite() ) {
	die( 'Multisite support not enabled' );
}

ms_file_constants();

if ( '1' == $current_blog->archived || '1' == $current_blog->spam || '1' == $current_blog->deleted ) {
	status_header( 404 );
	die( '404 &#8212; File not found.' );
}

$file = rtrim( BLOGUPLOADDIR, '/' ) . '/' . str_replace( '..', '', $_GET['file'] );
if ( ! is_file( $file ) ) {
	status_header( 404 );
	die( '404 &#8212; File not found.' );
}

$mime = gc_check_filetype( $file );
if ( false === $mime['type'] && function_exists( 'mime_content_type' ) ) {
	$mime['type'] = mime_content_type( $file );
}

if ( $mime['type'] ) {
	$mimetype = $mime['type'];
} else {
	$mimetype = 'image/' . substr( $file, strrpos( $file, '.' ) + 1 );
}

header( 'Content-Type: ' . $mimetype ); // Always send this.
if ( ! str_contains( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) ) {
	header( 'Content-Length: ' . filesize( $file ) );
}

// Optional support for X-Sendfile and X-Accel-Redirect.
if ( GCMU_ACCEL_REDIRECT ) {
	header( 'X-Accel-Redirect: ' . str_replace( GC_CONTENT_DIR, '', $file ) );
	exit;
} elseif ( GCMU_SENDFILE ) {
	header( 'X-Sendfile: ' . $file );
	exit;
}

$last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file ) );
$etag          = '"' . md5( $last_modified ) . '"';
header( "Last-Modified: $last_modified GMT" );
header( 'ETag: ' . $etag );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );

// Support for conditional GET - use stripslashes() to avoid formatting.php dependency.
$client_etag = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;

if ( ! isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {
	$_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;
}

$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
// If string is empty, return 0. If not, attempt to parse into a timestamp.
$client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;

// Make a timestamp for our most recent modification...
$modified_timestamp = strtotime( $last_modified );

if ( ( $client_last_modified && $client_etag )
	? ( ( $client_modified_timestamp >= $modified_timestamp ) && ( $client_etag == $etag ) )
	: ( ( $client_modified_timestamp >= $modified_timestamp ) || ( $client_etag == $etag ) )
	) {
	status_header( 304 );
	exit;
}

// If we made it this far, just serve the file.
readfile( $file );
flush();
