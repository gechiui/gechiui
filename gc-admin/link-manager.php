<?php
/**
 * Link Management Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';
if ( ! current_user_can( 'manage_links' ) ) {
	gc_die( __( '抱歉，您不能在此系统上编辑链接。' ) );
}

$gc_list_table = _get_list_table( 'GC_Links_List_Table' );

// Handle bulk deletes.
$doaction = $gc_list_table->current_action();

if ( $doaction && isset( $_REQUEST['linkcheck'] ) ) {
	check_admin_referer( 'bulk-bookmarks' );

	$redirect_to = admin_url( 'link-manager.php' );
	$bulklinks   = (array) $_REQUEST['linkcheck'];

	if ( 'delete' === $doaction ) {
		foreach ( $bulklinks as $link_id ) {
			$link_id = (int) $link_id;

			gc_delete_link( $link_id );
		}

		$redirect_to = add_query_arg( 'deleted', count( $bulklinks ), $redirect_to );
	} else {
		$screen = get_current_screen()->id;

		/** This action is documented in gc-admin/edit.php */
		$redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $bulklinks ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	}
	gc_redirect( $redirect_to );
	exit;
} elseif ( ! empty( $_GET['_gc_http_referer'] ) ) {
	gc_redirect( remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$gc_list_table->prepare_items();

// Used in the HTML title tag.
$title       = __( '链接' );
$this_file   = 'link-manager.php';
$parent_file = $this_file;

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . sprintf(
				/* translators: %s: URL to Widgets screen. */
				__( '您可以在这里添加在您系统中显示的链接（通常在<a href="%s">小工具</a>中显示）。我们预置了几个链接至GeChiUI社区的链接作为例子。' ),
				'widgets.php'
			) . '</p>' .
			'<p>' . __( '可以使用“链接分类”来组织链接。链接分类和文章分类不大相同。' ) . '</p>' .
			'<p>' . __( '您可以通过“显示选项”或链接列表上方的下拉菜单过滤器来自定义本页面的显示。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'deleting-links',
		'title'   => __( '链接的删除' ),
		'content' =>
			'<p>' . __( '若您删除一个链接，它将被永久移除。目前，链接不具备回收站功能。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Links_Screen">管理链接文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_list' => __( '链接列表' ),
	)
);

if ( isset( $_REQUEST['deleted'] ) ) {
	$deleted = (int) $_REQUEST['deleted'];
	$message = sprintf( _n( '已删除%s个链接。', '已删除%s个链接。', $deleted ), $deleted );
	add_settings_error( 'general', 'settings_updated', $message, 'success' );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] );
}

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( ! current_user_can( 'manage_links' ) ) {
	gc_die( __( '抱歉，您不能在此系统上编辑链接。' ) );
}

?>

<div class="wrap nosubsub">
	<div class="page-header">
		<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
		<a href="link-add.php" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加新链接', 'link' ); ?></a>
	</div>
<?php
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

<form id="posts-filter" method="get">

<?php $gc_list_table->search_box( __( '搜索链接' ), 'link' ); ?>

<?php $gc_list_table->display(); ?>

<div id="ajax-response"></div>
</form>

</div>

<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
