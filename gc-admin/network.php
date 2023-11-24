<?php
/**
 * Network installation administration panel.
 *
 * A multi-step process allowing the user to enable a network of GeChiUI sites.
 *
 *
 * @package GeChiUI
 * @subpackage Administration
 */

define( 'GC_INSTALLING_NETWORK', true );

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'setup_network' ) ) {
	gc_die( __( '抱歉，您不能管理此系统的选项。' ) );
}

if ( is_multisite() ) {
	if ( ! is_network_admin() ) {
		gc_redirect( network_admin_url( 'setup.php' ) );
		exit;
	}

	if ( ! defined( 'MULTISITE' ) ) {
		gc_die( __( 'SaaS平台创建面板不适用于 GeChiUI MU。' ) );
	}
}

require_once __DIR__ . '/includes/network.php';

// We need to create references to ms global tables to enable Network.
foreach ( $gcdb->tables( 'ms_global' ) as $table => $prefixed_table ) {
	$gcdb->$table = $prefixed_table;
}

if ( ! network_domain_check() && ( ! defined( 'GC_ALLOW_MULTISITE' ) || ! GC_ALLOW_MULTISITE ) ) {
	gc_die(
		printf(
			/* translators: 1: GC_ALLOW_MULTISITE, 2: gc-config.php */
			__( '您必须在您的%2$s文件中将%1$s常量设置为“true”（不带中文引号）才能创建SaaS平台。' ),
			'<code>GC_ALLOW_MULTISITE</code>',
			'<code>gc-config.php</code>'
		)
	);
}

if ( is_network_admin() ) {
	// Used in the HTML title tag.
	$title       = __( 'SaaS平台配置' );
	$parent_file = 'settings.php';
} else {
	// Used in the HTML title tag.
	$title       = __( '创建GeChiUISaaS平台' );
	$parent_file = 'tools.php';
}

$network_help = '<p>' . __( '您可以在本页面配置使用子域名（<code>site1.example.com</code>）或子目录（<code>example.com/site1</code>）的SaaS平台。若使用子域名，您需要在Apache和DNS记录中启用泛域名。' ) . '</p>' .
	'<p>' . __( '选择子域名或子目录；此设置只能在事后通过重新配置您的SaaS平台来更改。填写SaaS平台详情，然后点击安装。若不起作用，您可能需要添加一个通配 DNS 记录（对于子域名）或修改固定链接的设置（对于子目录）。' ) . '</p>' .
	'<p>' . __( '在“配置SaaS平台”的下一个页面，GeChiUI将向您提供专为您生成的几行代码，请将它们按要求加入到gc-config.php和.htaccess文件中。请确保您的FTP用户端不隐藏以点（.）开头的文件，这样您才能看到.htaccess文件；若它确实不存在，您需手工创建这个文件。请在对文件作出更改前，备份这两个文件。' ) . '</p>' .
	'<p>' . __( '加入如下内容到gc-config.php（在<code>/*...stop editing...*/或/*...停止编辑...*/</code>上方）和<code>.htaccess</code>（替换GeChiUI原来生成的内容）。' ) . '</p>' .
	'<p>' . __( '在您添加完代码后，请在浏览器刷新页面，之后多系统功能就应该自动启用了。这个页面将仍然保留这段代码，以备日后使用。您可在“SaaS后台”界面的导航菜单中再次访问本页面来查看代码。用户可以通过顶部“工具栏”中的“我的系统”下拉菜单在“SaaS后台”和“管理系统”之间切换。' ) . '</p>' .
	'<p>' . __( '若本SaaS平台配置完成已经超过一个月了。由于主系统“/blog/”固定链接的问题，您不能选择使用子目录。此问题将很快在未来版本中解决。' ) . '</p>' .
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/create-a-network/">SaaS平台创建文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-network-screen/">SaaS平台界面文档</a>' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'network',
		'title'   => __( 'SaaS平台' ),
		'content' => $network_help,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/create-a-network/">SaaS平台创建文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-network-screen/">SaaS平台界面文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

<?php
if ( $_POST ) {

	check_admin_referer( 'install-network-1' );

	require_once ABSPATH . 'gc-admin/includes/upgrade.php';
	// Create network tables.
	install_network();
	$base              = parse_url( trailingslashit( get_option( 'home' ) ), PHP_URL_PATH );
	$subdomain_install = allow_subdomain_install() ? ! empty( $_POST['subdomain_install'] ) : false;
	if ( ! network_domain_check() ) {
		$result = populate_network( 1, get_clean_basedomain(), sanitize_email( $_POST['email'] ), gc_unslash( $_POST['sitename'] ), $base, $subdomain_install );
		if ( is_gc_error( $result ) ) {
			if ( 1 === count( $result->get_error_codes() ) && 'no_wildcard_dns' === $result->get_error_code() ) {
				network_step2( $result );
			} else {
				network_step1( $result );
			}
		} else {
			network_step2();
		}
	} else {
		network_step2();
	}
} elseif ( is_multisite() || network_domain_check() ) {
	network_step2();
} else {
	network_step1();
}
?>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
