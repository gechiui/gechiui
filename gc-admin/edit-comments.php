<?php
/**
 * Edit Comments Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';
if ( ! current_user_can( 'edit_posts' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能编辑评论。' ) . '</p>',
		403
	);
}

$gc_list_table = _get_list_table( 'GC_Comments_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

$doaction = $gc_list_table->current_action();

if ( $doaction ) {
	check_admin_referer( 'bulk-comments' );

	if ( 'delete_all' === $doaction && ! empty( $_REQUEST['pagegen_timestamp'] ) ) {
		$comment_status = gc_unslash( $_REQUEST['comment_status'] );
		$delete_time    = gc_unslash( $_REQUEST['pagegen_timestamp'] );
		$comment_ids    = $gcdb->get_col( $gcdb->prepare( "SELECT comment_ID FROM $gcdb->comments WHERE comment_approved = %s AND %s > comment_date_gmt", $comment_status, $delete_time ) );
		$doaction       = 'delete';
	} elseif ( isset( $_REQUEST['delete_comments'] ) ) {
		$comment_ids = $_REQUEST['delete_comments'];
		$doaction    = $_REQUEST['action'];
	} elseif ( isset( $_REQUEST['ids'] ) ) {
		$comment_ids = array_map( 'absint', explode( ',', $_REQUEST['ids'] ) );
	} elseif ( gc_get_referer() ) {
		gc_safe_redirect( gc_get_referer() );
		exit;
	}

	$approved   = 0;
	$unapproved = 0;
	$spammed    = 0;
	$unspammed  = 0;
	$trashed    = 0;
	$untrashed  = 0;
	$deleted    = 0;

	$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), gc_get_referer() );
	$redirect_to = add_query_arg( 'paged', $pagenum, $redirect_to );

	gc_defer_comment_counting( true );

	foreach ( $comment_ids as $comment_id ) { // Check the permissions on each.
		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			continue;
		}

		switch ( $doaction ) {
			case 'approve':
				gc_set_comment_status( $comment_id, 'approve' );
				$approved++;
				break;
			case 'unapprove':
				gc_set_comment_status( $comment_id, 'hold' );
				$unapproved++;
				break;
			case 'spam':
				gc_spam_comment( $comment_id );
				$spammed++;
				break;
			case 'unspam':
				gc_unspam_comment( $comment_id );
				$unspammed++;
				break;
			case 'trash':
				gc_trash_comment( $comment_id );
				$trashed++;
				break;
			case 'untrash':
				gc_untrash_comment( $comment_id );
				$untrashed++;
				break;
			case 'delete':
				gc_delete_comment( $comment_id );
				$deleted++;
				break;
		}
	}

	if ( ! in_array( $doaction, array( 'approve', 'unapprove', 'spam', 'unspam', 'trash', 'delete' ), true ) ) {
		$screen = get_current_screen()->id;

		/** This action is documented in gc-admin/edit.php */
		$redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $comment_ids ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	}

	gc_defer_comment_counting( false );

	if ( $approved ) {
		$redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
	}
	if ( $unapproved ) {
		$redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
	}
	if ( $spammed ) {
		$redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
	}
	if ( $unspammed ) {
		$redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
	}
	if ( $trashed ) {
		$redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
	}
	if ( $untrashed ) {
		$redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
	}
	if ( $deleted ) {
		$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
	}
	if ( $trashed || $spammed ) {
		$redirect_to = add_query_arg( 'ids', implode( ',', $comment_ids ), $redirect_to );
	}

	gc_safe_redirect( $redirect_to );
	exit;
} elseif ( ! empty( $_GET['_gc_http_referer'] ) ) {
	gc_redirect( remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$gc_list_table->prepare_items();

gc_enqueue_script( 'admin-comments' );
enqueue_comment_hotkeys_js();

if ( $post_id ) {
	$comments_count      = gc_count_comments( $post_id );
	$draft_or_post_title = gc_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' );

	if ( $comments_count->moderated > 0 ) {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: 1: Comments count, 2: Post title. */
			__( '“%2$s”的评论（%1$s）' ),
			number_format_i18n( $comments_count->moderated ),
			$draft_or_post_title
		);
	} else {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: %s: Post title. */
			__( '《%s》上的评论' ),
			$draft_or_post_title
		);
	}
} else {
	$comments_count = gc_count_comments();

	if ( $comments_count->moderated > 0 ) {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: %s: Comments count. */
			__( '评论（%s）' ),
			number_format_i18n( $comments_count->moderated )
		);
	} else {
		// Used in the HTML title tag.
		$title = __( '评论' );
	}
}

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '您可以使用与管理文章相同的方式来管理评论。与其他管理页面一样，您可以用相同方法随意自定义本界面。将鼠标光标悬停在某条评论上，可以快速管理评论；使用批量管理功能也是十分有效的方法。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'moderating-comments',
		'title'   => __( '评论的审核' ),
		'content' =>
					'<p>' . __( '评论左侧的红条提示代表着该条评论正等待您的审核。' ) . '</p>' .
					'<p>' . __( '在<strong>作者</strong>一栏中，评论者的电子邮箱、博客URL、IP地址连同评论者的名称一并显示。点击链接即可显示发自该IP地址的所有评论。' ) . '</p>' .
					'<p>' . __( '在<strong>评论</strong>一栏中，悬浮在任何评论上将给您批准、回复（并批准）、快速编辑、编辑、标记为垃圾、或删除该评论的选项。' ) . '</p>' .
					'<p>' . __( '在<strong>回复至</strong>一栏中，包含三项内容。文章标题代表评论发布于何处，并链接到该文章的编辑器界面。“查看文章”链接则可查看该文章在您的系统上的实时页面。带有数字的小气泡代表该文章收到的已批准评论的数量。如果有待处理的评论，则会显示一个含有待处理评论数量的红色圆圈。单击该圆圈，则将仅显示发表于该文章的待处理评论。' ) . '</p>' .
					'<p>' . __( '在<strong>提交于</strong>一栏中，显示了此评论被留在您的系统上的日期和时间。点击此日期/时间链接，则可前往系统上该条评论的对应位置。' ) . '</p>' .
					'<p>' . __( '许多用户使用键盘快捷键来提高审核效率。点击右侧的链接可以了解更多。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/comments-screen/">评论文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/comment-spam/">垃圾评论文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/keyboard-shortcuts/">键盘快捷键文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选评论列表' ),
		'heading_pagination' => __( '评论列表导航' ),
		'heading_list'       => __( '评论列表' ),
	)
);

if ( isset( $_REQUEST['error'] ) ) {
	$error     = (int) $_REQUEST['error'];
	$error_msg = '';
	switch ( $error ) {
		case 1:
			$message = __( '评论ID无效。' );
			add_settings_error( 'general', 'settings_updated', $message, 'danger' );
			break;
		case 2:
			$message = __( '抱歉，您不能编辑此文章的评论。' );
			add_settings_error( 'general', 'settings_updated', $message, 'danger' );
			break;
	}
}

if ( isset( $_REQUEST['approved'] ) || isset( $_REQUEST['deleted'] ) || isset( $_REQUEST['trashed'] ) || isset( $_REQUEST['untrashed'] ) || isset( $_REQUEST['spammed'] ) || isset( $_REQUEST['unspammed'] ) || isset( $_REQUEST['same'] ) ) {
	$approved  = isset( $_REQUEST['approved'] ) ? (int) $_REQUEST['approved'] : 0;
	$deleted   = isset( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;
	$trashed   = isset( $_REQUEST['trashed'] ) ? (int) $_REQUEST['trashed'] : 0;
	$untrashed = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
	$spammed   = isset( $_REQUEST['spammed'] ) ? (int) $_REQUEST['spammed'] : 0;
	$unspammed = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;
	$same      = isset( $_REQUEST['same'] ) ? (int) $_REQUEST['same'] : 0;

	if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spammed > 0 || $unspammed > 0 || $same > 0 ) {
		if ( $approved > 0 ) {
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '已批准%s条评论。', '已批准%s条评论。', $approved ), $approved );
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $spammed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '已标记%s条评论为垃圾评论。', '已标记%s条评论为垃圾评论。', $spammed ), $spammed ) . ' <a href="' . esc_url( gc_nonce_url( "edit-comments.php?doaction=undo&action=unspam&ids=$ids", 'bulk-comments' ) ) . '">' . __( '撤销' ) . '</a><br />';
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $unspammed > 0 ) {
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '已从垃圾评论中恢复%s条评论。', '已从垃圾评论中恢复%s条评论。', $unspammed ), $unspammed );
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $trashed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '已移动%s条评论到回收站。', '已移动%s条评论到回收站。', $trashed ), $trashed ) . ' <a href="' . esc_url( gc_nonce_url( "edit-comments.php?doaction=undo&action=untrash&ids=$ids", 'bulk-comments' ) ) . '">' . __( '撤销' ) . '</a><br />';
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $untrashed > 0 ) {
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '%s条评论已从回收站中恢复。', '%s条评论已从回收站中恢复。', $untrashed ), $untrashed );
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $deleted > 0 ) {
			/* translators: %s: Number of comments. */
			$message = sprintf( _n( '已永久删除%s条评论。', '已永久删除%s条评论。', $deleted ), $deleted );
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( $same > 0 ) {
			$comment = get_comment( $same );
			if ( $comment ) {
				switch ( $comment->comment_approved ) {
					case '1':
						$message = __( '此条评论已获准过了。' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( '编辑评论' ) . '</a>';
						add_settings_error( 'general', 'settings_updated', $message, 'success' );
						break;
					case 'trash':
						$message = __( '此条评论已在回收站中。' ) . ' <a href="' . esc_url( admin_url( 'edit-comments.php?comment_status=trash' ) ) . '"> ' . __( '查看回收站' ) . '</a>';
						add_settings_error( 'general', 'settings_updated', $message, 'success' );
						break;
					case 'spam':
						$message = __( '此条评论已被标记为垃圾评论。' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( '编辑评论' ) . '</a>';
						add_settings_error( 'general', 'settings_updated', $message, 'success' );
						break;
				}
			}
		}
	}
}

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
	<div class="page-header">
		<h2 class="header-title">
		<?php
		if ( $post_id ) {
			printf(
				/* translators: %s: Link to post. */
				__( '《%s》上的评论' ),
				sprintf(
					'<a href="%1$s">%2$s</a>',
					get_edit_post_link( $post_id ),
					gc_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' )
				)
			);
		} else {
			_e( '评论' );
		}
		?>
		</h2>
	</div>

<?php
if ( $post_id ) {
	$post_type_object = get_post_type_object( get_post_type( $post_id ) );

	if ( $post_type_object ) {
		printf(
			'<a href="%1$s" class="comments-view-item-link">%2$s</a>',
			get_permalink( $post_id ),
			$post_type_object->labels->view_item
		);
	}
}

if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( '搜索词：%s' ),
		'<strong>' . esc_html( gc_unslash( $_REQUEST['s'] ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<?php $gc_list_table->views(); ?>

<form id="comments-form" method="get">

<?php $gc_list_table->search_box( __( '搜索评论' ), 'comment' ); ?>

<?php if ( $post_id ) : ?>
<input type="hidden" name="p" value="<?php echo esc_attr( (int) $post_id ); ?>" />
<?php endif; ?>
<input type="hidden" name="comment_status" value="<?php echo esc_attr( $comment_status ); ?>" />
<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', 1 ) ); ?>" />

<input type="hidden" name="_total" value="<?php echo esc_attr( $gc_list_table->get_pagination_arg( 'total_items' ) ); ?>" />
<input type="hidden" name="_per_page" value="<?php echo esc_attr( $gc_list_table->get_pagination_arg( 'per_page' ) ); ?>" />
<input type="hidden" name="_page" value="<?php echo esc_attr( $gc_list_table->get_pagination_arg( 'page' ) ); ?>" />

<?php if ( isset( $_REQUEST['paged'] ) ) { ?>
	<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
<?php } ?>

<?php $gc_list_table->display(); ?>
</form>
</div>

<div id="ajax-response"></div>

<?php
gc_comment_reply( '-1', true, 'detail' );
gc_comment_trashnotice();
require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
