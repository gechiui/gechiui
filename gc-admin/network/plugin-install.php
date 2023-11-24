<?php
/**
 * Install plugin network administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

if ( isset( $_GET['tab'] ) && ( 'plugin-information' === $_GET['tab'] ) ) {
	define( 'IFRAME_REQUEST', true );
}

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require ABSPATH . 'gc-admin/plugin-install.php';
