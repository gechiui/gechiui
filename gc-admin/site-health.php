<?php
/**
 * Tools Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

gc_reset_vars( array( 'action' ) );

$tabs = array(
	/* translators: Tab heading for Site Health Status page. */
	''      => _x( '状态', '系统健康' ),
	/* translators: Tab heading for Site Health Info page. */
	'debug' => _x( '信息', '系统健康' ),
);

/**
 * An associative array of extra tabs for the Site Health navigation bar.
 *
 * Add a custom page to the Site Health screen, based on a tab slug and label.
 * The label you provide will also be used as part of the site title.
 *
 *
 * @param string[] $tabs An associative array of tab labels keyed by their slug.
 */
$tabs = apply_filters( 'site_health_navigation_tabs', $tabs );

$wrapper_classes = array(
	'health-check-tabs-wrapper',
	'hide-if-no-js',
	'tab-count-' . count( $tabs ),
);

$current_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' );

$title = sprintf(
	// translators: %s: The currently displayed tab.
	__( '系统健康 - %s' ),
	( isset( $tabs[ $current_tab ] ) ? esc_html( $tabs[ $current_tab ] ) : esc_html( reset( $tabs ) ) )
);

if ( ! current_user_can( 'view_site_health_checks' ) ) {
	gc_die( __( '抱歉，您不能访问系统健康信息。' ), '', 403 );
}

gc_enqueue_style( 'site-health' );
gc_enqueue_script( 'site-health' );

if ( ! class_exists( 'GC_Site_Health' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
}

if ( 'update_https' === $action ) {
	check_admin_referer( 'gc_update_https' );

	if ( ! current_user_can( 'update_https' ) ) {
		gc_die( __( '抱歉，您不能将该系统更新至HTTPS。' ), 403 );
	}

	if ( ! gc_is_https_supported() ) {
		gc_die( __( '看起来您的系统暂时不支持HTTPS。' ) );
	}

	$result = gc_update_urls_to_https();

	gc_redirect( add_query_arg( 'https_updated', (int) $result, gc_get_referer() ) );
	exit;
}

$health_check_site_status = GC_Site_Health::get_instance();

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '此页面可让您获得系统的健康诊断数据，并显示 GeChiUI 安装状态的总体评级。' ) . '</p>' .
				'<p>' . __( '在“状态”选项卡中，您可以查看有关 GeChiUI 配置的关键信息，以及其它需要您注意的信息。' ) . '</p>' .
				'<p>' . __( '在信息选项卡中，您将找到有关 GeChiUI 系统、服务器和数据库配置的所有详细信息。还有一个导出功能，可让您将系统的所有相关信息复制到剪贴板，方便您在取得支援时解决系统问题。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/site-health-screen/">系统健康工具文档</a>' ) . '</p>'
);

// Start by checking if this is a special request checking for the existence of certain filters.
$health_check_site_status->check_gc_version_check_exists();

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="health-check-header">
	<div class="health-check-title-section">
		<h1>
			<?php _e( '系统健康' ); ?>
		</h1>
	</div>

	<?php
	if ( isset( $_GET['https_updated'] ) ) {
		if ( $_GET['https_updated'] ) {
			echo setting_error( __( '系统URL已切换至HTTPS。' ), 'success' );
		} else {
			echo setting_error( __( '系统URL未能切换至HTTPS。' ), 'danger' );
		}
	}
	?>

	<div class="health-check-title-section site-health-progress-wrapper loading hide-if-no-js">
		<div class="site-health-progress">
			<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
				<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
				<circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
			</svg>
		</div>
		<div class="site-health-progress-label">
			<?php _e( '结果载入中...'  ); ?>
		</div>
	</div>

	<nav class="<?php echo implode( ' ', $wrapper_classes ); ?>" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
		<?php
		$tabs_slice = $tabs;

		/*
		 * If there are more than 4 tabs, only output the first 3 inline,
		 * the remaining links will be added to a sub-navigation.
		 */
		if ( count( $tabs ) > 4 ) {
			$tabs_slice = array_slice( $tabs, 0, 3 );
		}

		foreach ( $tabs_slice as $slug => $label ) {
			printf(
				'<a href="%s" class="health-check-tab %s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'tab' => $slug,
						),
						admin_url( 'site-health.php' )
					)
				),
				( $current_tab === $slug ? 'active' : '' ),
				esc_html( $label )
			);
		}
		?>

		<?php if ( count( $tabs ) > 4 ) : ?>
			<button type="button" class="health-check-tab health-check-offscreen-nav-wrapper" aria-haspopup="true">
				<span class="dashicons dashicons-ellipsis"></span>
				<span class="screen-reader-text"><?php _e( '切换额外菜单项' ); ?></span>

				<div class="health-check-offscreen-nav">
					<?php
					// Remove the first few entries from the array as being already output.
					$tabs_slice = array_slice( $tabs, 3 );
					foreach ( $tabs_slice as $slug => $label ) {
						printf(
							'<a href="%s" class="health-check-tab %s">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'tab' => $slug,
									),
									admin_url( 'site-health.php' )
								)
							),
							( isset( $_GET['tab'] ) && $_GET['tab'] === $slug ? 'active' : '' ),
							esc_html( $label )
						);
					}
					?>
				</div>
			</button>
		<?php endif; ?>
	</nav>
</div>

<?php
if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
	/**
	 * Output content of a custom Site Health tab.
	 *
	 * This action fires right after the Site Health header, and users are still subject to
	 * the capability checks for the Site Health page to view any custom tabs and their contents.
	 *
	 *
	 * @param string $tab The slug of the tab that was requested.
	 */
	do_action( 'site_health_tab_content', $_GET['tab'] );

	require_once ABSPATH . 'gc-admin/admin-footer.php';
	return;
} else {
	echo setting_error( __( '系统健康检查需要JavaScript支持。' ), 'danger hide-if-js' );
	?>

<div class="health-check-body health-check-status-tab hide-if-no-js">
	<div class="site-status-all-clear hide">
		<p class="icon">
			<span class="dashicons dashicons-smiley" aria-hidden="true"></span>
		</p>

		<p class="encouragement">
			<?php _e( '好样的！' ); ?>
		</p>

		<p>
			<?php _e( '一切都在平缓运行。' ); ?>
		</p>
	</div>

	<div class="site-status-has-issues">
		<h2>
			<?php _e( '系统健康状态' ); ?>
		</h2>

		<p><?php _e( '系统健康检查向您显示关于您的GeChiUI配置的关键问题，及需要您的注意的项目。' ); ?></p>

		<div class="site-health-issues-wrapper" id="health-check-issues-critical">
			<h3 class="site-health-issue-count-title">
				<?php
					/* translators: %s: Number of critical issues found. */
					printf( _n( '%s个关键问题', '%s个关键问题', 0 ), '<span class="issue-count">0</span>' );
				?>
			</h3>

			<div id="health-check-site-status-critical" class="health-check-accordion issues"></div>
		</div>

		<div class="site-health-issues-wrapper" id="health-check-issues-recommended">
			<h3 class="site-health-issue-count-title">
				<?php
					/* translators: %s: Number of recommended improvements. */
					printf( _n( '%s个推荐的改进', '%s个推荐的改进', 0 ), '<span class="issue-count">0</span>' );
				?>
			</h3>

			<div id="health-check-site-status-recommended" class="health-check-accordion issues"></div>
		</div>
	</div>

	<div class="site-health-view-more">
		<button type="button" class="button site-health-view-passed" aria-expanded="false" aria-controls="health-check-issues-good">
			<?php _e( '通过测试' ); ?>
			<span class="icon"></span>
		</button>
	</div>

	<div class="site-health-issues-wrapper hidden" id="health-check-issues-good">
		<h3 class="site-health-issue-count-title">
			<?php
				/* translators: %s: Number of items with no issues. */
				printf( _n( '%s个没有问题的项目', '%s个没有问题的项目', 0 ), '<span class="issue-count">0</span>' );
			?>
		</h3>

		<div id="health-check-site-status-good" class="health-check-accordion issues"></div>
	</div>
</div>

<script id="tmpl-health-check-issue" type="text/template">
	<h4 class="health-check-accordion-heading">
		<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-{{ data.test }}" type="button">
			<span class="title">{{ data.label }}</span>
			<# if ( data.badge ) { #>
				<span class="badge {{ data.badge.color }}">{{ data.badge.label }}</span>
			<# } #>
			<span class="icon"></span>
		</button>
	</h4>
	<div id="health-check-accordion-block-{{ data.test }}" class="health-check-accordion-panel" hidden="hidden">
		{{{ data.description }}}
		<# if ( data.actions ) { #>
			<div class="actions">
				{{{ data.actions }}}
			</div>
		<# } #>
	</div>
</script>

	<?php
}
require_once ABSPATH . 'gc-admin/admin-footer.php';
