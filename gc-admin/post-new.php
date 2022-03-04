<?php
/**
 * New Post Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/**
 * @global string  $post_type
 * @global object  $post_type_object
 * @global GC_Post $post             Global post object.
 */
global $post_type, $post_type_object, $post;

if ( ! isset( $_GET['post_type'] ) ) {
	$post_type = 'post';
} elseif ( in_array( $_GET['post_type'], get_post_types( array( 'show_ui' => true ) ), true ) ) {
	$post_type = $_GET['post_type'];
} else {
	gc_die( __( '无效的文章类型。' ) );
}
$post_type_object = get_post_type_object( $post_type );

if ( 'post' === $post_type ) {
	$parent_file  = 'edit.php';
	$submenu_file = 'post-new.php';
} elseif ( 'attachment' === $post_type ) {
	if ( gc_redirect( admin_url( 'media-new.php' ) ) ) {
		exit;
	}
} else {
	$submenu_file = "post-new.php?post_type=$post_type";
	if ( isset( $post_type_object ) && $post_type_object->show_in_menu && true !== $post_type_object->show_in_menu ) {
		$parent_file = $post_type_object->show_in_menu;
		// What if there isn't a post-new.php item for this post type?
		if ( ! isset( $_registered_pages[ get_plugin_page_hookname( "post-new.php?post_type=$post_type", $post_type_object->show_in_menu ) ] ) ) {
			if ( isset( $_registered_pages[ get_plugin_page_hookname( "edit.php?post_type=$post_type", $post_type_object->show_in_menu ) ] ) ) {
				// Fall back to edit.php for that post type, if it exists.
				$submenu_file = "edit.php?post_type=$post_type";
			} else {
				// Otherwise, give up and highlight the parent.
				$submenu_file = $parent_file;
			}
		}
	} else {
		$parent_file = "edit.php?post_type=$post_type";
	}
}

$title = $post_type_object->labels->add_new_item;

$editing = true;

if ( ! current_user_can( $post_type_object->cap->edit_posts ) || ! current_user_can( $post_type_object->cap->create_posts ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能为此用户创建文章。' ) . '</p>',
		403
	);
}

$post    = get_default_post_to_edit( $post_type, true );
$post_ID = $post->ID;

/** This filter is documented in gc-admin/post.php */
if ( apply_filters( 'replace_editor', false, $post ) !== true ) {
	if ( use_block_editor_for_post( $post ) ) {
		require ABSPATH . 'gc-admin/edit-form-blocks.php';
	} else {
		gc_enqueue_script( 'autosave' );
		require ABSPATH . 'gc-admin/edit-form-advanced.php';
	}
} else {
	// Flag that we're not loading the block editor.
	$current_screen = get_current_screen();
	$current_screen->is_block_editor( false );
}

require_once ABSPATH . 'gc-admin/admin-footer.php';
