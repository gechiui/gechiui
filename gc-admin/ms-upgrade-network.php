<?php
/**
 * Multisite upgrade administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

require_once __DIR__ . '/admin.php';

gc_redirect( network_admin_url( 'upgrade.php' ) );
exit;
