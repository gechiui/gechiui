<?php
/**
 * GeChiUI Network Administration Bootstrap
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

define( 'GC_NETWORK_ADMIN', true );

/** Load GeChiUI Administration Bootstrap */
require_once dirname( __DIR__ ) . '/admin.php';

// Do not remove this check. It is required by individual network admin pages.
if ( ! is_multisite() ) {
	gc_die( __( '未启用SaaS支持。' ) );
}

$redirect_network_admin_request = ( 0 !== strcasecmp( $current_blog->domain, $current_site->domain ) || 0 !== strcasecmp( $current_blog->path, $current_site->path ) );

/**
 * Filters whether to redirect the request to the Network Admin.
 *
 * @param bool $redirect_network_admin_request Whether the request should be redirected.
 */
$redirect_network_admin_request = apply_filters( 'redirect_network_admin_request', $redirect_network_admin_request );

if ( $redirect_network_admin_request ) {
	gc_redirect( network_admin_url() );
	exit;
}

unset( $redirect_network_admin_request );
