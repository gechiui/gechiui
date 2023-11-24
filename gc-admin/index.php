<?php
/**
 * Dashboard Administration Screen
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Bootstrap */
require_once __DIR__ . '/admin.php';

/** Load GeChiUI dashboard API */
require_once ABSPATH . 'gc-admin/includes/dashboard.php';

gc_dashboard_setup();

gc_enqueue_script( 'dashboard' );

if ( current_user_can( 'install_plugins' ) ) {
	gc_enqueue_script( 'plugin-install' );
	gc_enqueue_script( 'updates' );
}
if ( current_user_can( 'upload_files' ) ) {
	gc_enqueue_script( 'media-upload' );
}
add_thickbox();

if ( gc_is_mobile() ) {
	gc_enqueue_script( 'jquery-touch-punch' );
}

// Used in the HTML title tag.
$title       = __( '仪表盘' );
$parent_file = 'index.php';

$help  = '<p>' . __( '欢迎来到您的 GeChiUI 仪表盘！' ) . '</p>';
$help .= '<p>' . __( '仪表盘是您每次登录系统时最先看到的界面。 您可以在这里找到所有的 GeChiUI 工具。 若您需要帮助，只需单击界面标题上方的“帮助”标签即可。' ) . '</p>';

$screen = get_current_screen();

$screen->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $help,
	)
);

// Help tabs.

$help  = '<p>' . __( '左侧的导航菜单提供了所有GeChiUI管理页面的链接。将鼠标移至菜单项目上，子菜单将显示出来。您可以使用最下方的“收起菜单”箭头来收起菜单，菜单项将以小图标的形式显示。' ) . '</p>';
$help .= '<p>' . __( '上方“工具栏”上的链接将仪表盘和系统前端连接起来，默认在系统的所有页面显示，提供您的个人资料信息及有用的GeChiUI信息。' ) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-navigation',
		'title'   => __( '导航' ),
		'content' => $help,
	)
);

$help  = '<p>' . __( '您可以根据您的工作方式，使用下列操作方式安排仪表盘的页面布局。大部分其他管理页面也支持页面布局调整功能。' ) . '</p>';
$help .= '<p>' . __( '<strong>显示选项</strong>——使用“显示选项”选项卡来选择要显示的仪表模块。' ) . '</p>';
$help .= '<p>' . __( '<strong>拖放功能</strong>——要重新排列模块，按住模块的标题栏，将其拖动到您希望的位置，在灰色虚线框出现后放开鼠标按键，即可调整模块的位置。' ) . '</p>';
$help .= '<p>' . __( '<strong>管理模块</strong>——点击模块的标题栏即可展开或收起它。另外，有些模块提供额外的配置选项，在您将鼠标移动到这些模块的标题栏上方时，会出现“配置”链接。' ) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-layout',
		'title'   => __( '布局' ),
		'content' => $help,
	)
);

$help = '<p>' . __( '仪表盘中的模块有：' ) . '</p>';

if ( current_user_can( 'edit_theme_options' ) ) {
	$help .= '<p>' . __( '<strong>欢迎</strong>——显示配置新系统的实用功能。' ) . '</p>';
}

if ( current_user_can( 'view_site_health_checks' ) ) {
	$help .= '<p>' . __( '<strong>系统健康状态</strong>——告知您所需解决的任何潜在问题，以便改善您系统的性能及安全性。' ) . '</p>';
}

if ( current_user_can( 'edit_posts' ) ) {
	$help .= '<p>' . __( '<strong>概览</strong>——显示您系统上的内容概况，以及主题与GeChiUI程序的版本信息。' ) . '</p>';
}

$help .= '<p>' . __( '<strong>动态</strong>——显示即将发布的计划文章、最新发布的文章以及您文章的最新评论，并让您对其进行审阅。' ) . '</p>';

if ( is_blog_admin() && current_user_can( 'edit_posts' ) ) {
	$help .= '<p>' . __( "<strong>快速草稿</strong>——让您创建新文章并保存为草稿，并显示3个指向最近草稿的链接。" ) . '</p>';
}

$help .= '<p>' . sprintf(
	/* translators: %s: GeChiUI Planet URL. */
	__( '<strong>GeChiUI活动及新闻</strong>——您周围的活动、来自GeChiUI项目与<a href="%s">GeChiUI Planet</a>的最新消息。' ),
	__( 'https://planet.gechiui.com/' )
) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-content',
		'title'   => __( '内容' ),
		'content' => $help,
	)
);

unset( $help );

$gc_version = get_bloginfo( 'version', 'display' );
/* translators: %s: GeChiUI version. */
$gc_version_text = sprintf( __( '%s版本' ), $gc_version );
$is_dev_version  = preg_match( '/alpha|beta|RC/', $gc_version );

if ( ! $is_dev_version ) {
	$version_url = sprintf(
		/* translators: %s: GeChiUI version. */
		esc_url( __( 'https://www.gechiui.com/support/gechiui-version/version-%s/' ) ),
		sanitize_title( $gc_version )
	);

	$gc_version_text = sprintf(
		'<a href="%1$s">%2$s</a>',
		$version_url,
		$gc_version_text
	);
}

$screen->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/dashboard-screen/">仪表盘文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>' .
	'<p>' . $gc_version_text . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

	<?php
	if ( ! empty( $_GET['admin_email_remind_later'] ) ) {
		/** This filter is documented in gc-login.php */
		$remind_interval = (int) apply_filters( 'admin_email_remind_interval', 3 * DAY_IN_SECONDS );
		$postponed_time  = get_option( 'admin_email_lifespan' );

		/*
		 * Calculate how many seconds it's been since the reminder was postponed.
		 * This allows us to not show it if the query arg is set, but visited due to caches, bookmarks or similar.
		 */
		$time_passed = time() - ( $postponed_time - $remind_interval );

		// Only show the dashboard notice if it's been less than a minute since the message was postponed.
		if ( $time_passed < MINUTE_IN_SECONDS ) {
			$message = sprintf(
					/* translators: %s: Human-readable time interval. */
					__( '管理员邮件验证页面将会在%s之后重新显示。' ),
					human_time_diff( time() + $remind_interval )
				);
			echo setting_error($message, 'success');
		 }
	}
	?>

	<div id="dashboard-widgets-wrap">
	<?php gc_dashboard(); ?>
	</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php
gc_print_community_events_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
