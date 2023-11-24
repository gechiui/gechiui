<?php
/**
 * Multisite upgrade administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require_once ABSPATH . GCINC . '/http.php';

// Used in the HTML title tag.
$title       = __( '升级SaaS平台' );
$parent_file = 'upgrade.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '请在“更新”或“可用更新”页面（通过“SaaS后台”区域的导航菜单或“工具栏”来进入）升级到最新GeChiUI版本之后再使用本页面。点击“升级SaaS平台”按钮，GeChiUI将自动依次升级SaaS平台中的所有系统（5个一次），并确保所有系统的数据库处于最新结构。' ) . '</p>' .
			'<p>' . __( '若您没有升级GeChiUI核心，点击这个按钮是不会起任何作用的。' ) . '</p>' .
			'<p>' . __( '若更新的过程因故中断或失败，登录系统的用户将被要求继续进行更新。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin-updates-screen/">文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forums/">支持论坛</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( ! current_user_can( 'upgrade_network' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

echo '<div class="wrap">';
echo '<div class="page-header"><h2 class="header-title">' . __( '升级SaaS平台' ) . '</h2></div>';


$action = isset( $_GET['action'] ) ? $_GET['action'] : 'show';

switch ( $action ) {
	case 'upgrade':
		$n = ( isset( $_GET['n'] ) ) ? (int) $_GET['n'] : 0;

		if ( $n < 5 ) {
			/**
			 * @global int $gc_db_version GeChiUI database version.
			 */
			global $gc_db_version;
			update_site_option( 'gcmu_upgrade_site', $gc_db_version );
		}

		$site_ids = get_sites(
			array(
				'spam'                   => 0,
				'deleted'                => 0,
				'archived'               => 0,
				'network_id'             => get_current_network_id(),
				'number'                 => 5,
				'offset'                 => $n,
				'fields'                 => 'ids',
				'order'                  => 'DESC',
				'orderby'                => 'id',
				'update_site_meta_cache' => false,
			)
		);
		if ( empty( $site_ids ) ) {
			echo '<p>' . __( '已全部完成！' ) . '</p>';
			break;
		}
		echo '<ul>';
		foreach ( (array) $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			$siteurl     = site_url();
			$upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
			restore_current_blog();

			echo "<li>$siteurl</li>";

			$response = gc_remote_get(
				$upgrade_url,
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
					'sslverify'   => false,
				)
			);

			if ( is_gc_error( $response ) ) {
				gc_die(
					sprintf(
						/* translators: 1: Site URL, 2: Server error message. */
						__( '警告！升级%1$s时遇到问题，您的服务器或许不能连接到运行的系统。错误信息：%2$s' ),
						$siteurl,
						'<em>' . $response->get_error_message() . '</em>'
					)
				);
			}

			/**
			 * Fires after the Multisite DB upgrade for each site is complete.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param array $response The upgrade response array.
			 */
			do_action( 'after_mu_upgrade', $response );

			/**
			 * Fires after each site has been upgraded.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id The Site ID.
			 */
			do_action( 'gcmu_upgrade_site', $site_id );
		}
		echo '</ul>';
		?><p><?php _e( '若您的浏览器不自动加载下一页，请点击：' ); ?> <a class="btn btn-primary btn-tone" href="upgrade.php?action=upgrade&amp;n=<?php echo ( $n + 5 ); ?>"><?php _e( '继续升级下一批系统' ); ?></a></p>
		<script type="text/javascript">
		<!--
		function nextpage() {
			location.href = "upgrade.php?action=upgrade&n=<?php echo ( $n + 5 ); ?>";
		}
		setTimeout( "nextpage()", 250 );
		//-->
		</script>
		<?php
		break;
	case 'show':
	default:
		?>
		<div class="card">
	    <div class="card-header">
	        <h4 class="card-title"><?php _e( '升级数据库' ); ?></h4>
	    </div>
	    <div class="card-body">
	    <?php
		if ( (int) get_site_option( 'gcmu_upgrade_site' ) !== $GLOBALS['gc_db_version'] ) :
		?>

		<p><?php _e( 'GeChiUI已更新！下一步也是最后一步是分别升级SaaS平台中的子系统。' ); ?></p>
		<?php endif; ?>

		<p><?php _e( '数据库升级过程需要一点时间，请耐心等候。' ); ?></p>
		<p><a class="btn btn-primary" href="upgrade.php?action=upgrade"><?php _e( '升级SaaS平台' ); ?></a></p>
		<?php
		/**
		 * Fires before the footer on the network upgrade screen.
		 *
		 * @since MU (3.0.0)
		 */
		do_action( 'gcmu_upgrade_page' );
		echo '</div>';
		break;
}
?>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
