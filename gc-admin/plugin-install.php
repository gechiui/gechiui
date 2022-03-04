<?php
/**
 * Install plugin administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */
// TODO: Route this page via a specific iframe handler instead of the do_action below.
if ( ! defined( 'IFRAME_REQUEST' ) && isset( $_GET['tab'] ) && ( 'plugin-information' === $_GET['tab'] ) ) {
	define( 'IFRAME_REQUEST', true );
}

/**
 * GeChiUI Administration Bootstrap.
 */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'install_plugins' ) ) {
	gc_die( __( '抱歉，您不能在此站点上安装插件。' ) );
}

if ( is_multisite() && ! is_network_admin() ) {
	gc_redirect( network_admin_url( 'plugin-install.php' ) );
	exit;
}

$gc_list_table = _get_list_table( 'GC_Plugin_Install_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

if ( ! empty( $_REQUEST['_gc_http_referer'] ) ) {
	$location = remove_query_arg( '_gc_http_referer', gc_unslash( $_SERVER['REQUEST_URI'] ) );

	if ( ! empty( $_REQUEST['paged'] ) ) {
		$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
	}

	gc_redirect( $location );
	exit;
}

$gc_list_table->prepare_items();

$total_pages = $gc_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	gc_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( '添加插件' );
$parent_file = 'plugins.php';

gc_enqueue_script( 'plugin-install' );
if ( 'plugin-information' !== $tab ) {
	add_thickbox();
}

$body_id = $tab;

gc_enqueue_script( 'updates' );

/**
 * Fires before each tab on the Install Plugins screen is loaded.
 *
 * The dynamic portion of the hook name, `$tab`, allows for targeting
 * individual tabs.
 *
 * Possible hook names include:
 *
 *  - `install_plugins_pre_beta`
 *  - `install_plugins_pre_professionals`
 *  - `install_plugins_pre_featured`
 *  - `install_plugins_pre_plugin-information`
 *  - `install_plugins_pre_free`
 *  - `install_plugins_pre_all`
 *  - `install_plugins_pre_search`
 *  - `install_plugins_pre_upload`
 *
 *
 */
do_action( "install_plugins_pre_{$tab}" );

/*
 * Call the pre upload action on every non-upload plugin installation screen
 * because the form is always displayed on these screens.
 */
if ( 'upload' !== $tab ) {
	/** This action is documented in gc-admin/plugin-install.php */
	do_action( 'install_plugins_pre_upload' );
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . sprintf(
					/* translators: %s: https://www.gechiui.com/plugins/ */
					__( '插件利用GeChiUI的钩子机制来扩展GeChiUI的功能，同时与GeChiUI核心应用程序相对独立。它们是由全球各地数以千计的开发者开发的。所有在官方<a href="%s">www.GeChiUI.com插件目录</a>中的插件都与GeChiUI所用的许可证相兼容。' ),
					__( 'https://www.gechiui.com/plugins/' )
				) . '</p>' .
				'<p>' . __( '您可以在“安装插件”界面通过搜索或浏览目录来寻找并安装新插件。' ) . ' <span id="live-search-desc" class="hide-if-no-js">' . __( '搜索结果会随着您的输入而不断更新。' ) . '</span></p>',

	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'adding-plugins',
		'title'   => __( '插件的添加' ),
		'content' =>
				'<p>' . __( '若您知道您需要何种插件，请使用“搜索”功能。搜索页面根据您提供的相关短语、作者名称或插件标签在GeChiUI插件目录搜索。您也可以通过选择热门标签来浏览插件目录。标签字体越大，说明使用该标签的插件越多。' ) . '</p>' .
				'<p>' . __( '若您只想知道有什么插件可以选择，您可从列表上方的“特色”、“热门”中浏览。这些内容定期更新。' ) . '</p>' .
				'<p>' . __( '您也可以浏览他人收藏的插件。点击列表上方的“收藏”链接，然后输入一个用户名。' ) . '</p>' .
				'<p>' . __( '如果您想安装从别处下载的插件，请点击列表上方的“上传”。GeChiUI接受.zip格式的压缩包，在上传成功后，您就可以启用该插件了。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/plugins-add-new-screen/">安装插件文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选插件列表' ),
		'heading_pagination' => __( '插件列表导航' ),
		'heading_list'       => __( '插件列表' ),
	)
);

/**
 * GeChiUI Administration Template Header.
 */
require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap <?php echo esc_attr( "plugin-install-tab-$tab" ); ?>">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( ! empty( $tabs['upload'] ) && current_user_can( 'upload_plugins' ) ) {
	printf(
		' <a href="%s" class="upload-view-toggle page-title-action"><span class="upload">%s</span><span class="browse">%s</span></a>',
		( 'upload' === $tab ) ? self_admin_url( 'plugin-install.php' ) : self_admin_url( 'plugin-install.php?tab=upload' ),
		__( '上传插件' ),
		__( '浏览插件' )
	);
}
?>

<hr class="gc-header-end">

<?php
/*
 * Output the upload plugin form on every non-upload plugin installation screen, so it can be
 * displayed via JavaScript rather then opening up the devoted upload plugin page.
 */
if ( 'upload' !== $tab ) {
	?>
	<div class="upload-plugin-wrap">
		<?php
		/** This action is documented in gc-admin/plugin-install.php */
		do_action( 'install_plugins_upload' );
		?>
	</div>
	<?php
	$gc_list_table->views();
}

/**
 * Fires after the plugins list table in each tab of the Install Plugins screen.
 *
 * The dynamic portion of the hook name, `$tab`, allows for targeting
 * individual tabs.
 *
 * Possible hook names include:
 *
 *  - `install_plugins_beta`
 *  - `install_plugins_professionals`
 *  - `install_plugins_featured`
 *  - `install_plugins_plugin-information`
 *  - `install_plugins_free`
 *  - `install_plugins_all`
 *  - `install_plugins_search`
 *  - `install_plugins_upload`
 *
 *
 *
 * @param int $paged The current page number of the plugins list table.
 */
do_action( "install_plugins_{$tab}", $paged );
?>

	<span class="spinner"></span>
</div>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();

/**
 * GeChiUI Administration Template Footer.
 */
require_once ABSPATH . 'gc-admin/admin-footer.php';
