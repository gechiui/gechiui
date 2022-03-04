<?php
/**
 * Edit Posts Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! $typenow ) {
	gc_die( __( '无效的文章类型。' ) );
}

if ( ! in_array( $typenow, get_post_types( array( 'show_ui' => true ) ), true ) ) {
	gc_die( __( '抱歉，您不能在此文章类型中编辑文章。' ) );
}

if ( 'attachment' === $typenow ) {
	if ( gc_redirect( admin_url( 'upload.php' ) ) ) {
		exit;
	}
}

/**
 * @global string       $post_type
 * @global GC_Post_Type $post_type_object
 */
global $post_type, $post_type_object;

$post_type        = $typenow;
$post_type_object = get_post_type_object( $post_type );

if ( ! $post_type_object ) {
	gc_die( __( '无效的文章类型。' ) );
}

if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此文章类型中编辑文章。' ) . '</p>',
		403
	);
}

$gc_list_table = _get_list_table( 'GC_Posts_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

// Back-compat for viewing comments of an entry.
foreach ( array( 'p', 'attachment_id', 'page_id' ) as $_redirect ) {
	if ( ! empty( $_REQUEST[ $_redirect ] ) ) {
		gc_redirect( admin_url( 'edit-comments.php?p=' . absint( $_REQUEST[ $_redirect ] ) ) );
		exit;
	}
}
unset( $_redirect );

if ( 'post' !== $post_type ) {
	$parent_file   = "edit.php?post_type=$post_type";
	$submenu_file  = "edit.php?post_type=$post_type";
	$post_new_file = "post-new.php?post_type=$post_type";
} else {
	$parent_file   = 'edit.php';
	$submenu_file  = 'edit.php';
	$post_new_file = 'post-new.php';
}

$doaction = $gc_list_table->current_action();

if ( $doaction ) {
	check_admin_referer( 'bulk-posts' );

	$sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'locked', 'ids' ), gc_get_referer() );
	if ( ! $sendback ) {
		$sendback = admin_url( $parent_file );
	}
	$sendback = add_query_arg( 'paged', $pagenum, $sendback );
	if ( strpos( $sendback, 'post.php' ) !== false ) {
		$sendback = admin_url( $post_new_file );
	}

	$post_ids = array();

	if ( 'delete_all' === $doaction ) {
		// Prepare for deletion of all posts with a specified post status (i.e. Empty Trash).
		$post_status = preg_replace( '/[^a-z0-9_-]+/i', '', $_REQUEST['post_status'] );
		// Validate the post status exists.
		if ( get_post_status_object( $post_status ) ) {
			$post_ids = $gcdb->get_col( $gcdb->prepare( "SELECT ID FROM $gcdb->posts WHERE post_type=%s AND post_status = %s", $post_type, $post_status ) );
		}
		$doaction = 'delete';
	} elseif ( isset( $_REQUEST['media'] ) ) {
		$post_ids = $_REQUEST['media'];
	} elseif ( isset( $_REQUEST['ids'] ) ) {
		$post_ids = explode( ',', $_REQUEST['ids'] );
	} elseif ( ! empty( $_REQUEST['post'] ) ) {
		$post_ids = array_map( 'intval', $_REQUEST['post'] );
	}

	if ( empty( $post_ids ) ) {
		gc_redirect( $sendback );
		exit;
	}

	switch ( $doaction ) {
		case 'trash':
			$trashed = 0;
			$locked  = 0;

			foreach ( (array) $post_ids as $post_id ) {
				if ( ! current_user_can( 'delete_post', $post_id ) ) {
					gc_die( __( '抱歉，您不能移动此项目到回收站。' ) );
				}

				if ( gc_check_post_lock( $post_id ) ) {
					$locked++;
					continue;
				}

				if ( ! gc_trash_post( $post_id ) ) {
					gc_die( __( '将项目移至回收站时发生错误。' ) );
				}

				$trashed++;
			}

			$sendback = add_query_arg(
				array(
					'trashed' => $trashed,
					'ids'     => implode( ',', $post_ids ),
					'locked'  => $locked,
				),
				$sendback
			);
			break;
		case 'untrash':
			$untrashed = 0;

			if ( isset( $_GET['doaction'] ) && ( 'undo' === $_GET['doaction'] ) ) {
				add_filter( 'gc_untrash_post_status', 'gc_untrash_post_set_previous_status', 10, 3 );
			}

			foreach ( (array) $post_ids as $post_id ) {
				if ( ! current_user_can( 'delete_post', $post_id ) ) {
					gc_die( __( '抱歉，您不能从回收站还原此项目。' ) );
				}

				if ( ! gc_untrash_post( $post_id ) ) {
					gc_die( __( '从回收站恢复时发生错误。' ) );
				}

				$untrashed++;
			}
			$sendback = add_query_arg( 'untrashed', $untrashed, $sendback );

			remove_filter( 'gc_untrash_post_status', 'gc_untrash_post_set_previous_status', 10 );

			break;
		case 'delete':
			$deleted = 0;
			foreach ( (array) $post_ids as $post_id ) {
				$post_del = get_post( $post_id );

				if ( ! current_user_can( 'delete_post', $post_id ) ) {
					gc_die( __( '抱歉，您不能删除此项目。' ) );
				}

				if ( 'attachment' === $post_del->post_type ) {
					if ( ! gc_delete_attachment( $post_id ) ) {
						gc_die( __( '删除附件时发生错误。' ) );
					}
				} else {
					if ( ! gc_delete_post( $post_id ) ) {
						gc_die( __( '删除项目时发生错误。' ) );
					}
				}
				$deleted++;
			}
			$sendback = add_query_arg( 'deleted', $deleted, $sendback );
			break;
		case 'edit':
			if ( isset( $_REQUEST['bulk_edit'] ) ) {
				$done = bulk_edit_posts( $_REQUEST );

				if ( is_array( $done ) ) {
					$done['updated'] = count( $done['updated'] );
					$done['skipped'] = count( $done['skipped'] );
					$done['locked']  = count( $done['locked'] );
					$sendback        = add_query_arg( $done, $sendback );
				}
			}
			break;
		default:
			$screen = get_current_screen()->id;

			/**
			 * Fires when a custom bulk action should be handled.
			 *
			 * The redirect link should be modified with success or failure feedback
			 * from the action to be used to display feedback to the user.
			 *
			 * The dynamic portion of the hook name, `$screen`, refers to the current screen ID.
			 *
		
			 *
			 * @param string $sendback The redirect URL.
			 * @param string $doaction The action being taken.
			 * @param array  $items    The items to take the action on. Accepts an array of IDs of posts,
			 *                         comments, terms, links, plugins, attachments, or users.
			 */
			$sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $doaction, $post_ids ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
			break;
	}

	$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

	gc_redirect( $sendback );
	exit;
} elseif ( ! empty( $_REQUEST['_gc_http_referer'] ) ) {
	gc_redirect( remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$gc_list_table->prepare_items();

gc_enqueue_script( 'inline-edit-post' );
gc_enqueue_script( 'heartbeat' );

if ( 'gc_block' === $post_type ) {
	gc_enqueue_script( 'gc-list-reusable-blocks' );
	gc_enqueue_style( 'gc-list-reusable-blocks' );
}

// Used in the HTML title tag.
$title = $post_type_object->labels->name;

if ( 'post' === $post_type ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' =>
					'<p>' . __( '本页面提供文章相关的所有功能。您可以自定义页面的样式来使工作更顺手。' ) . '</p>',
		)
	);
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'screen-content',
			'title'   => __( '页面内容' ),
			'content' =>
					'<p>' . __( '您可以通过以下方法来自定义本页面内容的显示方式：' ) . '</p>' .
					'<ul>' .
						'<li>' . __( '您可在“显示选项”中依据您的需要隐藏或显示每页显示的文章数量。' ) . '</li>' .
						'<li>' . __( '您可以通过点击列表上方的文字链接来筛选列表显示的项目——全部、已发布、草稿、回收站。默认视图中，显示所有文章。' ) . '</li>' .
						'<li>' . __( '您可以使用简单标题列表来查看文章，或是在显示选项面板种加入摘要。' ) . '</li>' .
						'<li>' . __( '通过在文章列表上方的下拉菜单中选择，您可单独查看显示某一分类中的文章，或是某月发布的文章。点击列表中作者、分类，或标签也可令列表只显示那些内容。' ) . '</li>' .
					'</ul>',
		)
	);
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'action-links',
			'title'   => __( '可进行的操作' ),
			'content' =>
					'<p>' . __( '将鼠标光标悬停在文章列表中的某一行，操作链接将会显示出来，您可以通过它们快速管理文章。您可进行下列操作：' ) . '</p>' .
					'<ul>' .
						'<li>' . __( '点击<strong>编辑</strong>可在编辑器中编辑该文章。直接点击文章标题也可以达到同样的效果。' ) . '</li>' .
						'<li>' . __( '点击<strong>快速编辑</strong>，您无须跳转到其他页面，在本页内就能对文章属性进行更改。' ) . '</li>' .
						'<li>' . __( '点击<strong>移至回收站</strong>，该文章将会从列表中移除，并自动移至回收站。在回收站中，您可以将其永久删除。' ) . '</li>' .
						'<li>' . __( '点击<strong>预览/查看</strong>，您的浏览器将跳转到前台，为您展示文章发布后的效果，或访问已经发布的这篇文章。' ) . '</li>' .
					'</ul>',
		)
	);
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'bulk-actions',
			'title'   => __( '批量操作' ),
			'content' =>
					'<p>' . __( '您也可以一次编辑或移动多篇文章到回收站。使用复选框选择你要操作的文章，然后从批量操作菜单中选择你要采取的操作，然后点击“应用”按钮。' ) . '</p>' .
							'<p>' . __( '在使用“批量编辑”时，您可以使用复选框一次编辑这些文章的多个属性（分类、作者等）。要将某篇文章从批量编辑中移除，请在“批量编辑”区域中点击其标题旁边的“×”按钮。' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/posts-screen/">管理文章文档</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);

} elseif ( 'page' === $post_type ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' =>
					'<p>' . __( '页面和文章类似——它们都有标题、正文以及附带的相关信息；但与文章不同的是，它们类似永久的文章，而往往不像一般的博客文章那样，随着时间流逝逐渐淡出人们的视线。页面不属于任何一个分类，亦不能拥有标签，但是页面之间可以有层级关系。您可将一个页面附属在另一个“父级页面”之下，构建一个页面群组。' ) . '</p>',
		)
	);
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'managing-pages',
			'title'   => __( '页面的管理' ),
			'content' =>
					'<p>' . __( '管理页面的方法和管理文章的方法类似，本界面也可以用相同的方式自定义。' ) . '</p>' .
					'<p>' . __( '您可以进行同样的操作，比如使用过滤器筛选列表项、使用鼠标悬停的方式进行管理，或使用“批量操作”功能来同时编辑多个文章的属性。' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/pages-screen/">管理页面文档</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);

}

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => $post_type_object->labels->filter_items_list,
		'heading_pagination' => $post_type_object->labels->items_list_navigation,
		'heading_list'       => $post_type_object->labels->items_list,
	)
);

add_screen_option(
	'per_page',
	array(
		'default' => 20,
		'option'  => 'edit_' . $post_type . '_per_page',
	)
);

$bulk_counts = array(
	'updated'   => isset( $_REQUEST['updated'] ) ? absint( $_REQUEST['updated'] ) : 0,
	'locked'    => isset( $_REQUEST['locked'] ) ? absint( $_REQUEST['locked'] ) : 0,
	'deleted'   => isset( $_REQUEST['deleted'] ) ? absint( $_REQUEST['deleted'] ) : 0,
	'trashed'   => isset( $_REQUEST['trashed'] ) ? absint( $_REQUEST['trashed'] ) : 0,
	'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
);

$bulk_messages             = array();
$bulk_messages['post']     = array(
	/* translators: %s: Number of posts. */
	'updated'   => _n( '%s篇文章已更新。', '%s篇文章已更新。', $bulk_counts['updated'] ),
	'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '一篇文章未更新，有其他人正在编辑。' ) :
					/* translators: %s: Number of posts. */
					_n( '%s篇文章未被更新，因为有人正在编辑它们。', '%s篇文章未被更新，因为有人正在编辑它们。', $bulk_counts['locked'] ),
	/* translators: %s: Number of posts. */
	'deleted'   => _n( '已永久删除%s篇文章。', '已永久删除%s篇文章。', $bulk_counts['deleted'] ),
	/* translators: %s: Number of posts. */
	'trashed'   => _n( '已移动%s篇文章到回收站。', '已移动%s篇文章到回收站。', $bulk_counts['trashed'] ),
	/* translators: %s: Number of posts. */
	'untrashed' => _n( '%s篇文章已从回收站中恢复。', '%s篇文章已从回收站中恢复。', $bulk_counts['untrashed'] ),
);
$bulk_messages['page']     = array(
	/* translators: %s: Number of pages. */
	'updated'   => _n( '%s篇文章已更新。', '%s篇文章已更新。', $bulk_counts['updated'] ),
	'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '一个页面未更新，有其他人正在编辑。' ) :
					/* translators: %s: Number of pages. */
					_n( '%s篇文章未被更新，因为有人正在编辑它们。', '%s篇文章未被更新，因为有人正在编辑它们。', $bulk_counts['locked'] ),
	/* translators: %s: Number of pages. */
	'deleted'   => _n( '已永久删除%s个页面。', '已永久删除%s个页面。', $bulk_counts['deleted'] ),
	/* translators: %s: Number of pages. */
	'trashed'   => _n( '已移动 %s 个页面到回收站。', '已移动 %s 个页面到回收站。', $bulk_counts['trashed'] ),
	/* translators: %s: Number of pages. */
	'untrashed' => _n( '%s篇文章已从回收站中恢复。', '%s篇文章已从回收站中恢复。', $bulk_counts['untrashed'] ),
);
$bulk_messages['gc_block'] = array(
	/* translators: %s: Number of blocks. */
	'updated'   => _n( '已更新%s个区块。', '已更新%s个区块。', $bulk_counts['updated'] ),
	'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '未更新1个区块，有人正在编辑。' ) :
					/* translators: %s: Number of blocks. */
					_n( '未更新%s个区块，有人正在编辑。', '未更新%s个区块，有人正在编辑。', $bulk_counts['locked'] ),
	/* translators: %s: Number of blocks. */
	'deleted'   => _n( '已永久删除%s个区块。', '已永久删除%s个区块。', $bulk_counts['deleted'] ),
	/* translators: %s: Number of blocks. */
	'trashed'   => _n( '已将%s个区块移动到回收站。', '已将%s个区块移动到回收站。', $bulk_counts['trashed'] ),
	/* translators: %s: Number of blocks. */
	'untrashed' => _n( '已从回收站还原%s个区块。', '已从回收站还原%s个区块。', $bulk_counts['untrashed'] ),
);

/**
 * Filters the bulk action updated messages.
 *
 * By default, custom post types use the messages for the 'post' post type.
 *
 *
 *
 * @param array[] $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                               keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param int[]   $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 */
$bulk_messages = apply_filters( 'bulk_post_updated_messages', $bulk_messages, $bulk_counts );
$bulk_counts   = array_filter( $bulk_counts );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $post_type_object->labels->name );
?>
</h1>

<?php
if ( current_user_can( $post_type_object->cap->create_posts ) ) {
	echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
}

if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( '搜索结果：%s' ),
		'<strong>' . get_search_query() . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="gc-header-end">

<?php
// If we have a bulk message to issue:
$messages = array();
foreach ( $bulk_counts as $message => $count ) {
	if ( isset( $bulk_messages[ $post_type ][ $message ] ) ) {
		$messages[] = sprintf( $bulk_messages[ $post_type ][ $message ], number_format_i18n( $count ) );
	} elseif ( isset( $bulk_messages['post'][ $message ] ) ) {
		$messages[] = sprintf( $bulk_messages['post'][ $message ], number_format_i18n( $count ) );
	}

	if ( 'trashed' === $message && isset( $_REQUEST['ids'] ) ) {
		$ids        = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
		$messages[] = '<a href="' . esc_url( gc_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", 'bulk-posts' ) ) . '">' . __( '撤销' ) . '</a>';
	}

	if ( 'untrashed' === $message && isset( $_REQUEST['ids'] ) ) {
		$ids = explode( ',', $_REQUEST['ids'] );

		if ( 1 === count( $ids ) && current_user_can( 'edit_post', $ids[0] ) ) {
			$messages[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_edit_post_link( $ids[0] ) ),
				esc_html( get_post_type_object( get_post_type( $ids[0] ) )->labels->edit_item )
			);
		}
	}
}

if ( $messages ) {
	echo '<div id="message" class="updated notice is-dismissible"><p>' . implode( ' ', $messages ) . '</p></div>';
}
unset( $messages );

$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed' ), $_SERVER['REQUEST_URI'] );
?>

<?php $gc_list_table->views(); ?>

<form id="posts-filter" method="get">

<?php $gc_list_table->search_box( $post_type_object->labels->search_items, 'post' ); ?>

<input type="hidden" name="post_status" class="post_status_page" value="<?php echo ! empty( $_REQUEST['post_status'] ) ? esc_attr( $_REQUEST['post_status'] ) : 'all'; ?>" />
<input type="hidden" name="post_type" class="post_type_page" value="<?php echo $post_type; ?>" />

<?php if ( ! empty( $_REQUEST['author'] ) ) { ?>
<input type="hidden" name="author" value="<?php echo esc_attr( $_REQUEST['author'] ); ?>" />
<?php } ?>

<?php if ( ! empty( $_REQUEST['show_sticky'] ) ) { ?>
<input type="hidden" name="show_sticky" value="1" />
<?php } ?>

<?php $gc_list_table->display(); ?>

</form>

<?php
if ( $gc_list_table->has_items() ) {
	$gc_list_table->inline_edit();
}
?>

<div id="ajax-response"></div>
<div class="clear"></div>
</div>

<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
