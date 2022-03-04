<?php
/**
 * Build User Administration Menu.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

$menu[2] = array( __( '仪表盘' ), 'exist', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'dashicons-dashboard' );

$menu[4] = array( '', 'exist', 'separator1', '', 'gc-menu-separator' );

$menu[70] = array( __( '个人资料' ), 'exist', 'profile.php', '', 'menu-top menu-icon-users', 'menu-users', 'dashicons-admin-users' );

$menu[99] = array( '', 'exist', 'separator-last', '', 'gc-menu-separator' );

$_gc_real_parent_file['users.php'] = 'profile.php';
$compat                            = array();
$submenu                           = array();

require_once ABSPATH . 'gc-admin/includes/menu.php';
