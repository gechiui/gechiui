<?php
/**
 * Multisite administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** Load GeChiUI dashboard API */
require_once ABSPATH . 'gc-admin/includes/dashboard.php';

if ( ! current_user_can( 'manage_network' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

// Used in the HTML title tag.
$title       = __( '仪表盘' );
$parent_file = 'index.php';

$overview  = '<p>' . __( '欢迎来到网络管理。这个管理页面可用来管理您的多站点网络的所有方面。' ) . '</p>';
$overview .= '<p>' . __( '从这里您可以：' ) . '</p>';
$overview .= '<ul><li>' . __( '添加和管理站点或用户' ) . '</li>';
$overview .= '<li>' . __( '安装并启用主题或插件' ) . '</li>';
$overview .= '<li>' . __( '升级您的站点网络' ) . '</li>';
$overview .= '<li>' . __( '修改站点网络全局设置' ) . '</li></ul>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $overview,
	)
);

$quick_tasks  = '<p>' . __( '本页中的“Right Now”小工具显示了您站点网络中的用户和站点计数。' ) . '</p>';
$quick_tasks .= '<ul><li>' . __( '要添加新用户，请<strong>点击“添加新用户”</strong>。' ) . '</li>';
$quick_tasks .= '<li>' . __( '要添加新站点，请<strong>点击“添加新站点”</strong>。' ) . '</li></ul>';
$quick_tasks .= '<p>' . __( '要搜索用户或站点，请使用搜索框。' ) . '</p>';
$quick_tasks .= '<ul><li>' . __( '要搜索用户，请<strong>输入电子邮箱或用户名</strong>。用通配符来匹配用户名的一部分，如user&#42;。' ) . '</li>';
$quick_tasks .= '<li>' . __( '要搜索站点，请<strong>输入路径或域名</strong>。' ) . '</li></ul>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'quick-tasks',
		'title'   => __( '快速任务' ),
		'content' => $quick_tasks,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin/">站点网络管理文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forum/issues/multisite/">支持论坛</a>' ) . '</p>'
);

gc_dashboard_setup();

gc_enqueue_script( 'dashboard' );
gc_enqueue_script( 'plugin-install' );
add_thickbox();

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<div id="dashboard-widgets-wrap">

<?php gc_dashboard(); ?>

<div class="clear"></div>
</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php
gc_print_community_events_templates();
require_once ABSPATH . 'gc-admin/admin-footer.php';
