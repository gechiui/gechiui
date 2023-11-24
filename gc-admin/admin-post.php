<?php
/**
 * GeChiUI Generic Request (POST/GET) Handler
 *
 * Intended for form submission handling in themes and plugins.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** We are located in GeChiUI Administration Screens */
if ( ! defined( 'GC_ADMIN' ) ) {
	define( 'GC_ADMIN', true );
}

if ( defined( 'ABSPATH' ) ) {
	require_once ABSPATH . 'gc-load.php';
} else {
	require_once dirname( __DIR__ ) . '/gc-load.php';
}

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

require_once ABSPATH . 'gc-admin/includes/admin.php';

nocache_headers();

/** This action is documented in gc-admin/admin.php */
do_action( 'admin_init' );

$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

// Reject invalid parameters.
if ( ! is_scalar( $action ) ) {
	gc_die( '', 400 );
}

if ( ! is_user_logged_in() ) {
	if ( empty( $action ) ) {
		/**
		 * Fires on a non-authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post_nopriv' );
	} else {
		// If no action is registered, return a Bad Request response.
		if ( ! has_action( "admin_post_nopriv_{$action}" ) ) {
			gc_die( '', 400 );
		}

		/**
		 * Fires on a non-authenticated admin post request for the given action.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the given
		 * request action.
		 *
		 * @since 2.6.0
		 */
		do_action( "admin_post_nopriv_{$action}" );
	}
} else {
	if ( empty( $action ) ) {
		/**
		 * Fires on an authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post' );
	} else {
		// If no action is registered, return a Bad Request response.
		if ( ! has_action( "admin_post_{$action}" ) ) {
			gc_die( '', 400 );
		}

		/**
		 * Fires on an authenticated admin post request for the given action.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the given
		 * request action.
		 *
		 * @since 2.6.0
		 */
		do_action( "admin_post_{$action}" );
	}
}
