<?php
/**
 * Edit Term Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( empty( $_REQUEST['tag_ID'] ) ) {
	$sendback = admin_url( 'edit-tags.php' );
	if ( ! empty( $taxnow ) ) {
		$sendback = add_query_arg( array( 'taxonomy' => $taxnow ), $sendback );
	}

	if ( 'post' !== get_current_screen()->post_type ) {
		$sendback = add_query_arg( 'post_type', get_current_screen()->post_type, $sendback );
	}

	gc_redirect( esc_url_raw( $sendback ) );
	exit;
}

$tag_ID = absint( $_REQUEST['tag_ID'] );
$tag    = get_term( $tag_ID, $taxnow, OBJECT, 'edit' );

if ( ! $tag instanceof GC_Term ) {
	gc_die( __( '您正在试图编辑一个不存在的条目。它已被删除？' ) );
}

$tax      = get_taxonomy( $tag->taxonomy );
$taxonomy = $tax->name;
$title    = $tax->labels->edit_item;

if ( ! in_array( $taxonomy, get_taxonomies( array( 'show_ui' => true ) ), true )
	|| ! current_user_can( 'edit_term', $tag->term_id )
) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能编辑此项目。' ) . '</p>',
		403
	);
}

$post_type = get_current_screen()->post_type;

// Default to the first object_type associated with the taxonomy if no post type was passed.
if ( empty( $post_type ) ) {
	$post_type = reset( $tax->object_type );
}

if ( 'post' !== $post_type ) {
	$parent_file  = ( 'attachment' === $post_type ) ? 'upload.php' : "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} elseif ( 'link_category' === $taxonomy ) {
	$parent_file  = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file  = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

get_current_screen()->set_screen_reader_content(
	array(
		'heading_pagination' => $tax->labels->items_list_navigation,
		'heading_list'       => $tax->labels->items_list,
	)
);
gc_enqueue_script( 'admin-tags' );
require_once ABSPATH . 'gc-admin/admin-header.php';
require ABSPATH . 'gc-admin/edit-tag-form.php';
require_once ABSPATH . 'gc-admin/admin-footer.php';
