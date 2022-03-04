<?php
/**
 * Comment Management Screen
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Bootstrap */
require_once __DIR__ . '/admin.php';

$parent_file  = 'edit-comments.php';
$submenu_file = 'edit-comments.php';

/**
 * @global string $action
 */
global $action;
gc_reset_vars( array( 'action' ) );

if ( isset( $_POST['deletecomment'] ) ) {
	$action = 'deletecomment';
}

if ( 'cdc' === $action ) {
	$action = 'delete';
} elseif ( 'mac' === $action ) {
	$action = 'approve';
}

if ( isset( $_GET['dt'] ) ) {
	if ( 'spam' === $_GET['dt'] ) {
		$action = 'spam';
	} elseif ( 'trash' === $_GET['dt'] ) {
		$action = 'trash';
	}
}

if ( isset( $_REQUEST['c'] ) ) {
	$comment_id = absint( $_REQUEST['c'] );
	$comment    = get_comment( $comment_id );

	// Prevent actions on a comment associated with a trashed post.
	if ( $comment && 'trash' === get_post_status( $comment->comment_post_ID ) ) {
		gc_die(
			__( '您不能编辑此评论，因为与评论关联的文章已被移至回收站。请先恢复文章后重试。' )
		);
	}
} else {
	$comment = null;
}

switch ( $action ) {

	case 'editcomment':
		// Used in the HTML title tag.
		$title = __( '编辑评论' );

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( '概述' ),
				'content' =>
					'<p>' . __( '若有需要，您可以编辑一条评论中的任何内容，特别是当评论者打错字的时候，这一功能十分有用。' ) . '</p>' .
					'<p>' . __( '您可以使用页面上的状态栏来审核评论，并可以修改该评论的发布时间。' ) . '</p>',
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/comments-screen/">评论文档</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
		);

		gc_enqueue_script( 'comment' );
		require_once ABSPATH . 'gc-admin/admin-header.php';

		if ( ! $comment ) {
			comment_footer_die( __( '评论ID无效。' ) . sprintf( ' <a href="%s">' . __( '返回' ) . '</a>.', 'javascript:history.go(-1)' ) );
		}

		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			comment_footer_die( __( '抱歉，您不能编辑此评论。' ) );
		}

		if ( 'trash' === $comment->comment_approved ) {
			comment_footer_die( __( '评论在回收站里，如要编辑请移出回收站。' ) );
		}

		$comment = get_comment_to_edit( $comment_id );

		require ABSPATH . 'gc-admin/edit-form-comment.php';

		break;

	case 'delete':
	case 'approve':
	case 'trash':
	case 'spam':
		// Used in the HTML title tag.
		$title = __( '审核评论' );

		if ( ! $comment ) {
			gc_redirect( admin_url( 'edit-comments.php?error=1' ) );
			die();
		}

		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			gc_redirect( admin_url( 'edit-comments.php?error=2' ) );
			die();
		}

		// No need to re-approve/re-trash/re-spam a comment.
		if ( str_replace( '1', 'approve', $comment->comment_approved ) === $action ) {
			gc_redirect( admin_url( 'edit-comments.php?same=' . $comment_id ) );
			die();
		}

		require_once ABSPATH . 'gc-admin/admin-header.php';

		$formaction    = $action . 'comment';
		$nonce_action  = ( 'approve' === $action ) ? 'approve-comment_' : 'delete-comment_';
		$nonce_action .= $comment_id;

		?>
	<div class="wrap">

	<h1><?php echo esc_html( $title ); ?></h1>

		<?php
		switch ( $action ) {
			case 'spam':
				$caution_msg = __( '您将标记以下评论为垃圾评论：' );
				$button      = _x( '标记为垃圾评论', 'comment' );
				break;
			case 'trash':
				$caution_msg = __( '您将移动以下评论到回收站：' );
				$button      = __( '移动至回收站' );
				break;
			case 'delete':
				$caution_msg = __( '您将删除以下评论：' );
				$button      = __( '永久删除评论' );
				break;
			default:
				$caution_msg = __( '您正在批准以下评论：' );
				$button      = __( '批准评论' );
				break;
		}

		if ( '0' !== $comment->comment_approved ) { // If not unapproved.
			$message = '';
			switch ( $comment->comment_approved ) {
				case '1':
					$message = __( '此评论当前已获准。' );
					break;
				case 'spam':
					$message = __( '此评论当前已被标记为垃圾评论。' );
					break;
				case 'trash':
					$message = __( '此评论当前在回收站中。' );
					break;
			}
			if ( $message ) {
				echo '<div id="message" class="notice notice-info"><p>' . $message . '</p></div>';
			}
		}
		?>
<div id="message" class="notice notice-warning"><p><strong><?php _e( '注意：' ); ?></strong> <?php echo $caution_msg; ?></p></div>

<table class="form-table comment-ays">
<tr>
	<th scope="row"><?php _e( '作者' ); ?></th>
	<td><?php comment_author( $comment ); ?></td>
</tr>
		<?php if ( get_comment_author_email( $comment ) ) { ?>
<tr>
	<th scope="row"><?php _e( '电子邮箱' ); ?></th>
	<td><?php comment_author_email( $comment ); ?></td>
</tr>
<?php } ?>
		<?php if ( get_comment_author_url( $comment ) ) { ?>
<tr>
	<th scope="row"><?php _e( 'URL' ); ?></th>
	<td><a href="<?php comment_author_url( $comment ); ?>"><?php comment_author_url( $comment ); ?></a></td>
</tr>
<?php } ?>
<tr>
	<th scope="row"><?php /* translators: Column name or table row header. */ _e( '回复至' ); ?></th>
	<td>
		<?php
		$post_id = $comment->comment_post_ID;
		if ( current_user_can( 'edit_post', $post_id ) ) {
			$post_link  = "<a href='" . esc_url( get_edit_post_link( $post_id ) ) . "'>";
			$post_link .= esc_html( get_the_title( $post_id ) ) . '</a>';
		} else {
			$post_link = esc_html( get_the_title( $post_id ) );
		}
		echo $post_link;

		if ( $comment->comment_parent ) {
			$parent      = get_comment( $comment->comment_parent );
			$parent_link = esc_url( get_comment_link( $parent ) );
			$name        = get_comment_author( $parent );
			printf(
				/* translators: %s: Comment link. */
				' | ' . __( '回复给%s。' ),
				'<a href="' . $parent_link . '">' . $name . '</a>'
			);
		}
		?>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e( '提交于' ); ?></th>
	<td>
		<?php
		$submitted = sprintf(
			/* translators: 1: Comment date, 2: Comment time. */
			__( '%1$s %2$s' ),
			/* translators: Comment date format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'Y-m-d' ), $comment ),
			/* translators: Comment time format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'ag:i' ), $comment )
		);
		if ( 'approved' === gc_get_comment_status( $comment ) && ! empty( $comment->comment_post_ID ) ) {
			echo '<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . $submitted . '</a>';
		} else {
			echo $submitted;
		}
		?>
	</td>
</tr>
<tr>
	<th scope="row"><?php /* translators: Field name in comment form. */ _ex( '评论', 'noun' ); ?></th>
	<td class="comment-content">
		<?php comment_text( $comment ); ?>
		<p class="edit-comment">
			<a href="<?php echo esc_url( admin_url( "comment.php?action=editcomment&c={$comment->comment_ID}" ) ); ?>"><?php esc_html_e( '编辑' ); ?></a>
		</p>
	</td>
</tr>
</table>

<form action="comment.php" method="get" class="comment-ays-submit">
	<p>
		<?php submit_button( $button, 'primary', 'submit', false ); ?>
		<a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>" class="button-cancel"><?php esc_html_e( '取消' ); ?></a>
	</p>

		<?php gc_nonce_field( $nonce_action ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( $formaction ); ?>" />
	<input type="hidden" name="c" value="<?php echo esc_attr( $comment->comment_ID ); ?>" />
	<input type="hidden" name="noredir" value="1" />
</form>

</div>
		<?php
		break;

	case 'deletecomment':
	case 'trashcomment':
	case 'untrashcomment':
	case 'spamcomment':
	case 'unspamcomment':
	case 'approvecomment':
	case 'unapprovecomment':
		$comment_id = absint( $_REQUEST['c'] );

		if ( in_array( $action, array( 'approvecomment', 'unapprovecomment' ), true ) ) {
			check_admin_referer( 'approve-comment_' . $comment_id );
		} else {
			check_admin_referer( 'delete-comment_' . $comment_id );
		}

		$noredir = isset( $_REQUEST['noredir'] );

		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			comment_footer_die( __( '评论ID无效。' ) . sprintf( ' <a href="%s">' . __( '返回' ) . '</a>.', 'edit-comments.php' ) );
		}
		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			comment_footer_die( __( '抱歉，您不能编辑此文章的评论。' ) );
		}

		if ( gc_get_referer() && ! $noredir && false === strpos( gc_get_referer(), 'comment.php' ) ) {
			$redir = gc_get_referer();
		} elseif ( gc_get_original_referer() && ! $noredir ) {
			$redir = gc_get_original_referer();
		} elseif ( in_array( $action, array( 'approvecomment', 'unapprovecomment' ), true ) ) {
			$redir = admin_url( 'edit-comments.php?p=' . absint( $comment->comment_post_ID ) );
		} else {
			$redir = admin_url( 'edit-comments.php' );
		}

		$redir = remove_query_arg( array( 'spammed', 'unspammed', 'trashed', 'untrashed', 'deleted', 'ids', 'approved', 'unapproved' ), $redir );

		switch ( $action ) {
			case 'deletecomment':
				gc_delete_comment( $comment );
				$redir = add_query_arg( array( 'deleted' => '1' ), $redir );
				break;
			case 'trashcomment':
				gc_trash_comment( $comment );
				$redir = add_query_arg(
					array(
						'trashed' => '1',
						'ids'     => $comment_id,
					),
					$redir
				);
				break;
			case 'untrashcomment':
				gc_untrash_comment( $comment );
				$redir = add_query_arg( array( 'untrashed' => '1' ), $redir );
				break;
			case 'spamcomment':
				gc_spam_comment( $comment );
				$redir = add_query_arg(
					array(
						'spammed' => '1',
						'ids'     => $comment_id,
					),
					$redir
				);
				break;
			case 'unspamcomment':
				gc_unspam_comment( $comment );
				$redir = add_query_arg( array( 'unspammed' => '1' ), $redir );
				break;
			case 'approvecomment':
				gc_set_comment_status( $comment, 'approve' );
				$redir = add_query_arg( array( 'approved' => 1 ), $redir );
				break;
			case 'unapprovecomment':
				gc_set_comment_status( $comment, 'hold' );
				$redir = add_query_arg( array( 'unapproved' => 1 ), $redir );
				break;
		}

		gc_redirect( $redir );
		die;

	case 'editedcomment':
		$comment_id      = absint( $_POST['comment_ID'] );
		$comment_post_id = absint( $_POST['comment_post_ID'] );

		check_admin_referer( 'update-comment_' . $comment_id );

		$updated = edit_comment();
		if ( is_gc_error( $updated ) ) {
			gc_die( $updated->get_error_message() );
		}

		$location = ( empty( $_POST['referredby'] ) ? "edit-comments.php?p=$comment_post_id" : $_POST['referredby'] ) . '#comment-' . $comment_id;

		/**
		 * Filters the URI the user is redirected to after editing a comment in the admin.
		 *
		 *
		 * @param string $location The URI the user will be redirected to.
		 * @param int $comment_id The ID of the comment being edited.
		 */
		$location = apply_filters( 'comment_edit_redirect', $location, $comment_id );

		gc_redirect( $location );
		exit;

	default:
		gc_die( __( '未知操作。' ) );

} // End switch.

require_once ABSPATH . 'gc-admin/admin-footer.php';
