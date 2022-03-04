<?php
/**
 * Action handler for Multisite administration panels.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

require_once __DIR__ . '/admin.php';

gc_redirect( network_admin_url() );
exit;
