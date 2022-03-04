<?php
/**
 * Manage link administration actions.
 *
 * This page is accessed by the link management pages and handles the forms and
 * Ajax processes for link actions.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

gc_reset_vars( array( 'action', 'cat_id', 'link_id' ) );

if ( ! current_user_can( 'manage_links' ) ) {
	gc_link_manager_disabled_message();
}

if ( ! empty( $_POST['deletebookmarks'] ) ) {
	$action = 'deletebookmarks';
}
if ( ! empty( $_POST['move'] ) ) {
	$action = 'move';
}
if ( ! empty( $_POST['linkcheck'] ) ) {
	$linkcheck = $_POST['linkcheck'];
}

$this_file = admin_url( 'link-manager.php' );

switch ( $action ) {
	case 'deletebookmarks':
		check_admin_referer( 'bulk-bookmarks' );

		// For each link id (in $linkcheck[]) change category to selected value.
		if ( count( $linkcheck ) === 0 ) {
			gc_redirect( $this_file );
			exit;
		}

		$deleted = 0;
		foreach ( $linkcheck as $link_id ) {
			$link_id = (int) $link_id;

			if ( gc_delete_link( $link_id ) ) {
				$deleted++;
			}
		}

		gc_redirect( "$this_file?deleted=$deleted" );
		exit;

	case 'move':
		check_admin_referer( 'bulk-bookmarks' );

		// For each link id (in $linkcheck[]) change category to selected value.
		if ( count( $linkcheck ) === 0 ) {
			gc_redirect( $this_file );
			exit;
		}
		$all_links = implode( ',', $linkcheck );
		/*
		 * Should now have an array of links we can change:
		 *     $q = $gcdb->query("update $gcdb->links SET link_category='$category' WHERE link_id IN ($all_links)");
		 */

		gc_redirect( $this_file );
		exit;

	case 'add':
		check_admin_referer( 'add-bookmark' );

		$redir = gc_get_referer();
		if ( add_link() ) {
			$redir = add_query_arg( 'added', 'true', $redir );
		}

		gc_redirect( $redir );
		exit;

	case 'save':
		$link_id = (int) $_POST['link_id'];
		check_admin_referer( 'update-bookmark_' . $link_id );

		edit_link( $link_id );

		gc_redirect( $this_file );
		exit;

	case 'delete':
		$link_id = (int) $_GET['link_id'];
		check_admin_referer( 'delete-bookmark_' . $link_id );

		gc_delete_link( $link_id );

		gc_redirect( $this_file );
		exit;

	case 'edit':
		gc_enqueue_script( 'link' );
		gc_enqueue_script( 'xfn' );

		if ( gc_is_mobile() ) {
			gc_enqueue_script( 'jquery-touch-punch' );
		}

		$parent_file  = 'link-manager.php';
		$submenu_file = 'link-manager.php';
		// Used in the HTML title tag.
		$title = __( '编辑链接' );

		$link_id = (int) $_GET['link_id'];

		$link = get_link_to_edit( $link_id );
		if ( ! $link ) {
			gc_die( __( '未找到链接。' ) );
		}

		require ABSPATH . 'gc-admin/edit-link-form.php';
		require_once ABSPATH . 'gc-admin/admin-footer.php';
		break;

	default:
		break;
}
