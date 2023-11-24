<?php
/**
 * Add Link Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_links' ) ) {
	gc_die( __( '抱歉，您不能添加链接到此系统。' ) );
}

// Used in the HTML title tag.
$title       = __( '添加链接' );
$parent_file = 'link-manager.php';

gc_reset_vars( array( 'action', 'cat_id', 'link_id' ) );

gc_enqueue_script( 'link' );

if ( gc_is_mobile() ) {
	gc_enqueue_script( 'jquery-touch-punch' );
}

$link = get_default_link_to_edit();
require ABSPATH . 'gc-admin/edit-link-form.php';

require_once ABSPATH . 'gc-admin/admin-footer.php';
