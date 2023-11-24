<?php
/**
 * Media Library administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'upload_files' ) ) {
	gc_die( __( '抱歉，您不能上传文件。' ) );
}

$mode  = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';
$modes = array( 'grid', 'list' );

if ( isset( $_GET['mode'] ) && in_array( $_GET['mode'], $modes, true ) ) {
	$mode = $_GET['mode'];
	update_user_option( get_current_user_id(), 'media_library_mode', $mode );
}

if ( 'grid' === $mode ) {
	gc_enqueue_media();
	gc_enqueue_script( 'media-grid' );
	gc_enqueue_script( 'media' );

	remove_action( 'admin_head', 'gc_admin_canonical_url' );

	$q = $_GET;
	// Let JS handle this.
	unset( $q['s'] );
	$vars   = gc_edit_attachments_query_vars( $q );
	$ignore = array( 'mode', 'post_type', 'post_status', 'posts_per_page' );
	foreach ( $vars as $key => $value ) {
		if ( ! $value || in_array( $key, $ignore, true ) ) {
			unset( $vars[ $key ] );
		}
	}

	gc_localize_script(
		'media-grid',
		'_gcMediaGridSettings',
		array(
			'adminUrl'  => parse_url( self_admin_url(), PHP_URL_PATH ),
			'queryVars' => (object) $vars,
		)
	);

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' =>
				'<p>' . __( '您上传的所有文件都在“媒体库”界面中按上传时间顺序列出，最新上传的显示在最前面。' ) . '</p>' .
				'<p>' . __( '您可以简单的网格视图或列表视图两种方式来查阅您的媒体文件。您可以使用媒体文件列表左上侧的图标来切换这些视图。' ) . '</p>' .
				'<p>' . __( '要删除媒体项目，点击顶部的“批量选择”按钮，选择您想要删除的项目，再点击“删除所选”按钮。如您想返回查阅媒体，请点击“取消选择”按钮。' ) . '</p>',
		)
	);

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'attachment-details',
			'title'   => __( '附件详情' ),
			'content' =>
				'<p>' . __( '点选一个项目将会带出“附件详情”对话框，您可在其中预览媒体并快速做出修改。您在“附件详情”对话框中做出的所有修改都会自动保存。' ) . '</p>' .
				'<p>' . __( '使用对话框顶部的箭头按钮或键盘上的左右键，便能快速浏览媒体项目。' ) . '</p>' .
				'<p>' . __( '您也可以在此详情对话框中删除单个项目或访问扩展编辑界面。' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/media-library-screen/">媒体库文档</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);

	// Used in the HTML title tag.
	$title       = __( '媒体库' );
	$parent_file = 'upload.php';

	require_once ABSPATH . 'gc-admin/admin-header.php';
	?>
	<div class="wrap" id="gc-media-grid" data-search="<?php _admin_search_query(); ?>">
		<div class="page-header">
			<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
			<?php
			if ( current_user_can( 'upload_files' ) ) {
				?>
				<a href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>" class="btn btn-primary btn-tone btn-sm aria-button-if-js"><?php echo esc_html_x( '新增文件', 'file' ); ?></a>
				<?php
			}
			?>
		</div>

		<div class="error hide-if-js">
			<p>
			<?php
			printf(
				/* translators: %s: List view URL. */
				__( '媒体库的网格视图需要JavaScript支持。<a href="%s">切换回列表视图</a>。' ),
				'upload.php?mode=list'
			);
			?>
			</p>
		</div>
	</div>
	<?php
	require_once ABSPATH . 'gc-admin/admin-footer.php';
	exit;
}

$gc_list_table = _get_list_table( 'GC_Media_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

// Handle bulk actions.
$doaction = $gc_list_table->current_action();

if ( $doaction ) {
	check_admin_referer( 'bulk-media' );

	$post_ids = array();

	if ( 'delete_all' === $doaction ) {
		$post_ids = $gcdb->get_col( "SELECT ID FROM $gcdb->posts WHERE post_type='attachment' AND post_status = 'trash'" );
		$doaction = 'delete';
	} elseif ( isset( $_REQUEST['media'] ) ) {
		$post_ids = $_REQUEST['media'];
	} elseif ( isset( $_REQUEST['ids'] ) ) {
		$post_ids = explode( ',', $_REQUEST['ids'] );
	}

	$location = 'upload.php';
	$referer  = gc_get_referer();
	if ( $referer ) {
		if ( false !== strpos( $referer, 'upload.php' ) ) {
			$location = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted' ), $referer );
		}
	}

	switch ( $doaction ) {
		case 'detach':
			gc_media_attach_action( $_REQUEST['parent_post_id'], 'detach' );
			break;

		case 'attach':
			gc_media_attach_action( $_REQUEST['found_post_id'] );
			break;

		case 'trash':
			if ( empty( $post_ids ) ) {
				break;
			}
			foreach ( (array) $post_ids as $post_id ) {
				if ( ! current_user_can( 'delete_post', $post_id ) ) {
					gc_die( __( '抱歉，您不能移动此项目到回收站。' ) );
				}

				if ( ! gc_trash_post( $post_id ) ) {
					gc_die( __( '将项目移至回收站时发生错误。' ) );
				}
			}
			$location = add_query_arg(
				array(
					'trashed' => count( $post_ids ),
					'ids'     => implode( ',', $post_ids ),
				),
				$location
			);
			break;
		case 'untrash':
			if ( empty( $post_ids ) ) {
				break;
			}
			foreach ( (array) $post_ids as $post_id ) {
				if ( ! current_user_can( 'delete_post', $post_id ) ) {
					gc_die( __( '抱歉，您不能从回收站还原此项目。' ) );
				}

				if ( ! gc_untrash_post( $post_id ) ) {
					gc_die( __( '从回收站恢复时发生错误。' ) );
				}
			}
			$location = add_query_arg( 'untrashed', count( $post_ids ), $location );
			break;
		case 'delete':
			if ( empty( $post_ids ) ) {
				break;
			}
			foreach ( (array) $post_ids as $post_id_del ) {
				if ( ! current_user_can( 'delete_post', $post_id_del ) ) {
					gc_die( __( '抱歉，您不能删除此项目。' ) );
				}

				if ( ! gc_delete_attachment( $post_id_del ) ) {
					gc_die( __( '删除附件时发生错误。' ) );
				}
			}
			$location = add_query_arg( 'deleted', count( $post_ids ), $location );
			break;
		default:
			$screen = get_current_screen()->id;

			/** This action is documented in gc-admin/edit.php */
			$location = apply_filters( "handle_bulk_actions-{$screen}", $location, $doaction, $post_ids ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	}

	gc_redirect( $location );
	exit;
} elseif ( ! empty( $_GET['_gc_http_referer'] ) ) {
	gc_redirect( remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$gc_list_table->prepare_items();

// Used in the HTML title tag.
$title       = __( '媒体库' );
$parent_file = 'upload.php';

gc_enqueue_script( 'media' );

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '您上传的所有文件都在“媒体库”界面中按上传时间顺序列出，最新上传的显示在最前面。您可以在“显示选项”选项卡中自定义此页面。' ) . '</p>' .
				'<p>' . __( '使用列表上方的下拉菜单，您可以通过指定文件类型、状态或日期来筛选列表项。' ) . '</p>' .
				'<p>' . __( '您可以简单的网格视图或列表视图两种方式来查阅您的媒体文件。您可以使用媒体文件列表左上侧的图标来切换这些视图。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'actions-links',
		'title'   => __( '可进行的操作' ),
		'content' =>
				'<p>' . __( '将鼠标移动到某一行上方，将出现几个新的链接：“编辑”、“永久删除”和“查看”。点击“编辑”或文件标题，会出现一个简单的编辑页面，您可用它进行文件属性的编辑；点击“永久删除”将从媒体库中删除该文件（同时，也会从所有包含它的文章中删除）；“查看”将带您到该文件的显示页面。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'attaching-files',
		'title'   => __( '附加文件' ),
		'content' =>
				'<p>' . __( '若某个多媒体文件未被加入任何文章或页面，您将在这个文件的“上传至”一栏看到“现在附加到文章或页面”链接；点击它，将会弹出一个新的窗口，您可搜索现有的内容，并将其加入文章或页面。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/media-library-screen/">媒体库文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选媒体项目列表' ),
		'heading_pagination' => __( '媒体项目列表导航' ),
		'heading_list'       => __( '媒体项目列表' ),
	)
);

$message = '';
if ( ! empty( $_GET['posted'] ) ) {
	$message                = __( '媒体文件已更新。' );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'posted' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $_GET['attached'] ) && absint( $_GET['attached'] ) ) {
	$attached = absint( $_GET['attached'] );
	if ( 1 === $attached ) {
		$message = __( '已附加媒体文件。' );
	} else {
		/* translators: %s: Number of media files. */
		$message = _n( '已附加%s个媒体文件。', '已附加%s个媒体文件。', $attached );
	}
	$message                = sprintf( $message, number_format_i18n( $attached ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'detach', 'attached' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $_GET['detach'] ) && absint( $_GET['detach'] ) ) {
	$detached = absint( $_GET['detach'] );
	if ( 1 === $detached ) {
		$message = __( '已分离媒体文件。' );
	} else {
		/* translators: %s: Number of media files. */
		$message = _n( '已分离 %s 个媒体文件。', '已分离 %s 个媒体文件。', $detached );
	}
	$message                = sprintf( $message, number_format_i18n( $detached ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'detach', 'attached' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $_GET['deleted'] ) && absint( $_GET['deleted'] ) ) {
	$deleted = absint( $_GET['deleted'] );
	if ( 1 === $deleted ) {
		$message = __( '已永久删除媒体文件。' );
	} else {
		/* translators: %s: Number of media files. */
		$message = _n( '已永久删除%s个媒体文件。', '已永久删除%s个媒体文件。', $deleted );
	}
	$message                = sprintf( $message, number_format_i18n( $deleted ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $_GET['trashed'] ) && absint( $_GET['trashed'] ) ) {
	$trashed = absint( $_GET['trashed'] );
	if ( 1 === $trashed ) {
		$message = __( '已移动媒体文件至回收站。' );
	} else {
		/* translators: %s: Number of media files. */
		$message = _n( '已移动%s个媒体文件至回收站。', '已移动%s个媒体文件至回收站。', $trashed );
	}
	$message                = sprintf( $message, number_format_i18n( $trashed ) );
	$message               .= ' <a href="' . esc_url( gc_nonce_url( 'upload.php?doaction=undo&action=untrash&ids=' . ( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), 'bulk-media' ) ) . '">' . __( '撤销' ) . '</a>';
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'trashed' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $_GET['untrashed'] ) && absint( $_GET['untrashed'] ) ) {
	$untrashed = absint( $_GET['untrashed'] );
	if ( 1 === $untrashed ) {
		$message = __( '已从回收站恢复媒体文件。' );
	} else {
		/* translators: %s: Number of media files. */
		$message = _n( '已从回收站恢复%s个媒体文件。', '已从回收站恢复%s个媒体文件。', $untrashed );
	}
	$message                = sprintf( $message, number_format_i18n( $untrashed ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'untrashed' ), $_SERVER['REQUEST_URI'] );
}

$messages[1] = __( '媒体文件已更新。' );
$messages[2] = __( '已永久删除媒体文件。' );
$messages[3] = __( '保存媒体文件时发生错误。' );
$messages[4] = __( '已移动媒体文件至回收站。' ) . ' <a href="' . esc_url( gc_nonce_url( 'upload.php?doaction=undo&action=untrash&ids=' . ( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), 'bulk-media' ) ) . '">' . __( '撤销' ) . '</a>';
$messages[5] = __( '已从回收站恢复媒体文件。' );

if ( ! empty( $_GET['message'] ) && isset( $messages[ $_GET['message'] ] ) ) {
	$message                = $messages[ $_GET['message'] ];
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message' ), $_SERVER['REQUEST_URI'] );
}

if ( ! empty( $message ) ) {
	add_settings_error( 'general', 'settings_updated', $message, 'success' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<div class="page-header">
	<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
	<?php
	if ( current_user_can( 'upload_files' ) ) {
		?>
		<a href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '新增文件', 'file' ); ?></a>
							<?php
	}

	if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
		echo '<span class="subtitle">';
		printf(
			/* translators: %s: Search query. */
			__( '搜索词：%s' ),
			'<strong>' . get_search_query() . '</strong>'
		);
		echo '</span>';
	}
	?>
</div>

<form id="posts-filter" method="get">

<?php $gc_list_table->views(); ?>

<?php $gc_list_table->display(); ?>

<div id="ajax-response"></div>
<?php find_posts_div(); ?>
</form>
</div>

<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
