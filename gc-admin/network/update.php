<?php
/**
 * Update/Install Plugin/Theme network administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ), true ) ) {
	define( 'IFRAME_REQUEST', true );
}

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require ABSPATH . 'gc-admin/update.php';
