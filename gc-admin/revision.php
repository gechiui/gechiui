<?php
/**
 * Revisions administration panel
 *
 * Requires gc-admin/includes/revision.php.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require ABSPATH . 'gc-admin/includes/revision.php';

/**
 * @global int    $revision Optional. The revision ID.
 * @global string $action   The action to take.
 *                          Accepts 'restore', 'view' or 'edit'.
 * @global int    $from     The revision to compare from.
 * @global int    $to       Optional, required if revision missing. The revision to compare to.
 */
gc_reset_vars( array( 'revision', 'action', 'from', 'to' ) );

$revision_id = absint( $revision );

$from = is_numeric( $from ) ? absint( $from ) : null;
if ( ! $revision_id ) {
	$revision_id = absint( $to );
}
$redirect = 'edit.php';

switch ( $action ) {
	case 'restore':
		$revision = gc_get_post_revision( $revision_id );
		if ( ! $revision ) {
			break;
		}

		if ( ! current_user_can( 'edit_post', $revision->post_parent ) ) {
			break;
		}

		$post = get_post( $revision->post_parent );
		if ( ! $post ) {
			break;
		}

		// Don't restore if revisions are disabled and this is not an autosave.
		if ( ! gc_revisions_enabled( $post ) && ! gc_is_post_autosave( $revision ) ) {
			$redirect = 'edit.php?post_type=' . $post->post_type;
			break;
		}

		// Don't restore if the post is locked.
		if ( gc_check_post_lock( $post->ID ) ) {
			break;
		}

		check_admin_referer( "restore-post_{$revision->ID}" );

		/*
		 * Ensure the global $post remains the same after revision is restored.
		 * Because gc_insert_post() and gc_transition_post_status() are called
		 * during the process, plugins can unexpectedly modify $post.
		 */
		$backup_global_post = clone $post;

		gc_restore_post_revision( $revision->ID );

		// Restore the global $post as it was before.
		$post = $backup_global_post;

		$redirect = add_query_arg(
			array(
				'message'  => 5,
				'revision' => $revision->ID,
			),
			get_edit_post_link( $post->ID, 'url' )
		);
		break;
	case 'view':
	case 'edit':
	default:
		$revision = gc_get_post_revision( $revision_id );
		if ( ! $revision ) {
			break;
		}

		$post = get_post( $revision->post_parent );
		if ( ! $post ) {
			break;
		}

		if ( ! current_user_can( 'read_post', $revision->ID ) || ! current_user_can( 'edit_post', $revision->post_parent ) ) {
			break;
		}

		// Bail if revisions are disabled and this is not an autosave.
		if ( ! gc_revisions_enabled( $post ) && ! gc_is_post_autosave( $revision ) ) {
			$redirect = 'edit.php?post_type=' . $post->post_type;
			break;
		}

		$post_edit_link = get_edit_post_link();
		$post_title     = '<a href="' . $post_edit_link . '">' . _draft_or_post_title() . '</a>';
		/* translators: %s: Post title. */
		$h1             = sprintf( __( '比较《%s》的修订版本' ), $post_title );
		$return_to_post = '<a href="' . $post_edit_link . '">' . __( '&larr; 转到编辑器' ) . '</a>';
		// Used in the HTML title tag.
		$title = __( '修订版本' );

		$redirect = false;
		break;
}

// Empty post_type means either malformed object found, or no valid parent was found.
if ( ! $redirect && empty( $post->post_type ) ) {
	$redirect = 'edit.php';
}

if ( ! empty( $redirect ) ) {
	gc_redirect( $redirect );
	exit;
}

// This is so that the correct "Edit" menu item is selected.
if ( ! empty( $post->post_type ) && 'post' !== $post->post_type ) {
	$parent_file = 'edit.php?post_type=' . $post->post_type;
} else {
	$parent_file = 'edit.php';
}
$submenu_file = $parent_file;

gc_enqueue_script( 'revisions' );
gc_localize_script( 'revisions', '_gcRevisionsSettings', gc_prepare_revisions_for_js( $post, $revision_id, $from ) );

/* Revisions Help Tab */

$revisions_overview  = '<p>' . __( '在此页您可管理内容的修订版本。' ) . '</p>';
$revisions_overview .= '<p>' . __( '修订版本是在您编修您的文章和页面时，定期创建的拷贝。左边的红字表记了移除的内容，右边的绿字表记了加入的内容。' ) . '</p>';
$revisions_overview .= '<p>' . __( '在此页您可审阅、比较和恢复修订版本：' ) . '</p>';
$revisions_overview .= '<ul><li>' . __( '要在修订版本间移动，请<strong>将滑块向左右拖动</strong>或</strong>使用“上一个”或“下一个”按钮</strong>。' ) . '</li>';
$revisions_overview .= '<li>' . __( '通过在侧面<strong>选择“比较两个版本”模块</strong>来比较两个不同的修订版本。' ) . '</li>';
$revisions_overview .= '<li>' . __( '要恢复一个修订版本，<strong>点击“恢复此版本”</strong>。' ) . '</li></ul>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'revisions-overview',
		'title'   => __( '概述' ),
		'content' => $revisions_overview,
	)
);

$revisions_sidebar  = '<p><strong>' . __( '更多信息：' ) . '</strong></p>';
$revisions_sidebar .= '<p>' . __( '<a href="https://www.gechiui.com/support/revisions/">版本管理</a>' ) . '</p>';
$revisions_sidebar .= '<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>';

get_current_screen()->set_help_sidebar( $revisions_sidebar );

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo $h1; ?></h2></div>
	<?php echo $return_to_post; ?>
</div>
<?php
gc_print_revision_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
