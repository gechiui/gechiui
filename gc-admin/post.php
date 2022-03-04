<?php
/**
 * Edit post administration panel.
 *
 * Manage Post actions: post, edit, delete, etc.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

$parent_file  = 'edit.php';
$submenu_file = 'edit.php';

gc_reset_vars( array( 'action' ) );

if ( isset( $_GET['post'] ) && isset( $_POST['post_ID'] ) && (int) $_GET['post'] !== (int) $_POST['post_ID'] ) {
	gc_die( __( '检测到不匹配的文章ID。' ), __( '抱歉，您不能编辑此项目。' ), 400 );
} elseif ( isset( $_GET['post'] ) ) {
	$post_id = (int) $_GET['post'];
} elseif ( isset( $_POST['post_ID'] ) ) {
	$post_id = (int) $_POST['post_ID'];
} else {
	$post_id = 0;
}
$post_ID = $post_id;

/**
 * @global string  $post_type
 * @global object  $post_type_object
 * @global GC_Post $post             Global post object.
 */
global $post_type, $post_type_object, $post;

if ( $post_id ) {
	$post = get_post( $post_id );
}

if ( $post ) {
	$post_type        = $post->post_type;
	$post_type_object = get_post_type_object( $post_type );
}

if ( isset( $_POST['post_type'] ) && $post && $post_type !== $_POST['post_type'] ) {
	gc_die( __( '检测到文章类型不匹配。' ), __( '抱歉，您不能编辑此项目。' ), 400 );
}

if ( isset( $_POST['deletepost'] ) ) {
	$action = 'delete';
} elseif ( isset( $_POST['gc-preview'] ) && 'dopreview' === $_POST['gc-preview'] ) {
	$action = 'preview';
}

$sendback = gc_get_referer();
if ( ! $sendback ||
	false !== strpos( $sendback, 'post.php' ) ||
	false !== strpos( $sendback, 'post-new.php' ) ) {
	if ( 'attachment' === $post_type ) {
		$sendback = admin_url( 'upload.php' );
	} else {
		$sendback = admin_url( 'edit.php' );
		if ( ! empty( $post_type ) ) {
			$sendback = add_query_arg( 'post_type', $post_type, $sendback );
		}
	}
} else {
	$sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), $sendback );
}

switch ( $action ) {
	case 'post-quickdraft-save':
		// Check nonce and capabilities.
		$nonce     = $_REQUEST['_gcnonce'];
		$error_msg = false;

		// For output of the Quick Draft dashboard widget.
		require_once ABSPATH . 'gc-admin/includes/dashboard.php';

		if ( ! gc_verify_nonce( $nonce, 'add-post' ) ) {
			$error_msg = __( '无法提交此表单，请刷新并重试。' );
		}

		if ( ! current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
			exit;
		}

		if ( $error_msg ) {
			return gc_dashboard_quick_press( $error_msg );
		}

		$post = get_post( $_REQUEST['post_ID'] );
		check_admin_referer( 'add-' . $post->post_type );

		$_POST['comment_status'] = get_default_comment_status( $post->post_type );
		$_POST['ping_status']    = get_default_comment_status( $post->post_type, 'pingback' );

		// Wrap Quick Draft content in the Paragraph block.
		if ( false === strpos( $_POST['content'], '<!-- gc:paragraph -->' ) ) {
			$_POST['content'] = sprintf(
				'<!-- gc:paragraph -->%s<!-- /gc:paragraph -->',
				str_replace( array( "\r\n", "\r", "\n" ), '<br />', $_POST['content'] )
			);
		}

		edit_post();
		gc_dashboard_quick_press();
		exit;

	case 'postajaxpost':
	case 'post':
		check_admin_referer( 'add-' . $post_type );
		$post_id = 'postajaxpost' === $action ? edit_post() : write_post();
		redirect_post( $post_id );
		exit;

	case 'edit':
		$editing = true;

		if ( empty( $post_id ) ) {
			gc_redirect( admin_url( 'post.php' ) );
			exit;
		}

		if ( ! $post ) {
			gc_die( __( '您正在试图编辑一个不存在的条目。它已被删除？' ) );
		}

		if ( ! $post_type_object ) {
			gc_die( __( '无效的文章类型。' ) );
		}

		if ( ! in_array( $typenow, get_post_types( array( 'show_ui' => true ) ), true ) ) {
			gc_die( __( '抱歉，您不能在此文章类型中编辑文章。' ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			gc_die( __( '抱歉，您不能编辑此项目。' ) );
		}

		if ( 'trash' === $post->post_status ) {
			gc_die( __( '您无法编辑该条目，因为它现在在回收站中。请先将其恢复，然后再重试。' ) );
		}

		if ( ! empty( $_GET['get-post-lock'] ) ) {
			check_admin_referer( 'lock-post_' . $post_id );
			gc_set_post_lock( $post_id );
			gc_redirect( get_edit_post_link( $post_id, 'url' ) );
			exit;
		}

		$post_type = $post->post_type;
		if ( 'post' === $post_type ) {
			$parent_file   = 'edit.php';
			$submenu_file  = 'edit.php';
			$post_new_file = 'post-new.php';
		} elseif ( 'attachment' === $post_type ) {
			$parent_file   = 'upload.php';
			$submenu_file  = 'upload.php';
			$post_new_file = 'media-new.php';
		} else {
			if ( isset( $post_type_object ) && $post_type_object->show_in_menu && true !== $post_type_object->show_in_menu ) {
				$parent_file = $post_type_object->show_in_menu;
			} else {
				$parent_file = "edit.php?post_type=$post_type";
			}
			$submenu_file  = "edit.php?post_type=$post_type";
			$post_new_file = "post-new.php?post_type=$post_type";
		}

		$title = $post_type_object->labels->edit_item;

		/**
		 * Allows replacement of the editor.
		 *
		 *
		 * @param bool    $replace Whether to replace the editor. Default false.
		 * @param GC_Post $post    Post object.
		 */
		if ( true === apply_filters( 'replace_editor', false, $post ) ) {
			break;
		}

		if ( use_block_editor_for_post( $post ) ) {
			require ABSPATH . 'gc-admin/edit-form-blocks.php';
			break;
		}

		if ( ! gc_check_post_lock( $post->ID ) ) {
			$active_post_lock = gc_set_post_lock( $post->ID );

			if ( 'attachment' !== $post_type ) {
				gc_enqueue_script( 'autosave' );
			}
		}

		$post = get_post( $post_id, OBJECT, 'edit' );

		if ( post_type_supports( $post_type, 'comments' ) ) {
			gc_enqueue_script( 'admin-comments' );
			enqueue_comment_hotkeys_js();
		}

		require ABSPATH . 'gc-admin/edit-form-advanced.php';

		break;

	case 'editattachment':
		check_admin_referer( 'update-post_' . $post_id );

		// Don't let these be changed.
		unset( $_POST['guid'] );
		$_POST['post_type'] = 'attachment';

		// Update the thumbnail filename.
		$newmeta          = gc_get_attachment_metadata( $post_id, true );
		$newmeta['thumb'] = gc_basename( $_POST['thumb'] );

		gc_update_attachment_metadata( $post_id, $newmeta );

		// Intentional fall-through to trigger the edit_post() call.
	case 'editpost':
		check_admin_referer( 'update-post_' . $post_id );

		$post_id = edit_post();

		// Session cookie flag that the post was saved.
		if ( isset( $_COOKIE['gc-saving-post'] ) && $_COOKIE['gc-saving-post'] === $post_id . '-check' ) {
			setcookie( 'gc-saving-post', $post_id . '-saved', time() + DAY_IN_SECONDS, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, is_ssl() );
		}

		redirect_post( $post_id ); // Send user on their way while we keep working.

		exit;

	case 'trash':
		check_admin_referer( 'trash-post_' . $post_id );

		if ( ! $post ) {
			gc_die( __( '您试图移动到回收站的项目已不存在。' ) );
		}

		if ( ! $post_type_object ) {
			gc_die( __( '无效的文章类型。' ) );
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			gc_die( __( '抱歉，您不能移动此项目到回收站。' ) );
		}

		$user_id = gc_check_post_lock( $post_id );
		if ( $user_id ) {
			$user = get_userdata( $user_id );
			/* translators: %s: User's display name. */
			gc_die( sprintf( __( '您不能移动此项目到回收站。%s正在编辑。' ), $user->display_name ) );
		}

		if ( ! gc_trash_post( $post_id ) ) {
			gc_die( __( '将项目移至回收站时发生错误。' ) );
		}

		gc_redirect(
			add_query_arg(
				array(
					'trashed' => 1,
					'ids'     => $post_id,
				),
				$sendback
			)
		);
		exit;

	case 'untrash':
		check_admin_referer( 'untrash-post_' . $post_id );

		if ( ! $post ) {
			gc_die( __( '您试图从回收站恢复的项目已不存在。' ) );
		}

		if ( ! $post_type_object ) {
			gc_die( __( '无效的文章类型。' ) );
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			gc_die( __( '抱歉，您不能从回收站还原此项目。' ) );
		}

		if ( ! gc_untrash_post( $post_id ) ) {
			gc_die( __( '从回收站恢复时发生错误。' ) );
		}

		$sendback = add_query_arg(
			array(
				'untrashed' => 1,
				'ids'       => $post_id,
			),
			$sendback
		);
		gc_redirect( $sendback );
		exit;

	case 'delete':
		check_admin_referer( 'delete-post_' . $post_id );

		if ( ! $post ) {
			gc_die( __( '此项目已被删除。' ) );
		}

		if ( ! $post_type_object ) {
			gc_die( __( '无效的文章类型。' ) );
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			gc_die( __( '抱歉，您不能删除此项目。' ) );
		}

		if ( 'attachment' === $post->post_type ) {
			$force = ( ! MEDIA_TRASH );
			if ( ! gc_delete_attachment( $post_id, $force ) ) {
				gc_die( __( '删除附件时发生错误。' ) );
			}
		} else {
			if ( ! gc_delete_post( $post_id, true ) ) {
				gc_die( __( '删除项目时发生错误。' ) );
			}
		}

		gc_redirect( add_query_arg( 'deleted', 1, $sendback ) );
		exit;

	case 'preview':
		check_admin_referer( 'update-post_' . $post_id );

		$url = post_preview();

		gc_redirect( $url );
		exit;

	case 'toggle-custom-fields':
		check_admin_referer( 'toggle-custom-fields', 'toggle-custom-fields-nonce' );

		$current_user_id = get_current_user_id();
		if ( $current_user_id ) {
			$enable_custom_fields = (bool) get_user_meta( $current_user_id, 'enable_custom_fields', true );
			update_user_meta( $current_user_id, 'enable_custom_fields', ! $enable_custom_fields );
		}

		gc_safe_redirect( gc_get_referer() );
		exit;

	default:
		/**
		 * Fires for a given custom post action request.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the custom post action.
		 *
		 *
		 * @param int $post_id Post ID sent with the request.
		 */
		do_action( "post_action_{$action}", $post_id );

		gc_redirect( admin_url( 'edit.php' ) );
		exit;
} // End switch.

require_once ABSPATH . 'gc-admin/admin-footer.php';
