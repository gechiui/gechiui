<?php
/**
 * Theme Customize Screen.
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

define( 'IFRAME_REQUEST', true );

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'customize' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能自定义此站点。' ) . '</p>',
		403
	);
}

/**
 * @global GC_Scripts           $gc_scripts
 * @global GC_Customize_Manager $gc_customize
 */
global $gc_scripts, $gc_customize;

if ( $gc_customize->changeset_post_id() ) {
	$changeset_post = get_post( $gc_customize->changeset_post_id() );

	if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $changeset_post->ID ) ) {
		gc_die(
			'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
			'<p>' . __( '抱歉，您不能编辑此变更集。' ) . '</p>',
			403
		);
	}

	$missed_schedule = (
		'future' === $changeset_post->post_status &&
		get_post_time( 'G', true, $changeset_post ) < time()
	);
	if ( $missed_schedule ) {
		/*
		 * Note that an Ajax request spawns here instead of just calling `gc_publish_post( $changeset_post->ID )`.
		 *
		 * Because GC_Customize_Manager is not instantiated for customize.php with the `settings_previewed=false`
		 * argument, settings cannot be reliably saved. Some logic short-circuits if the current value is the
		 * same as the value being saved. This is particularly true for options via `update_option()`.
		 *
		 * By opening an Ajax request, this is avoided and the changeset is published. See #39221.
		 */
		$nonces       = $gc_customize->get_nonces();
		$request_args = array(
			'nonce'                      => $nonces['save'],
			'customize_changeset_uuid'   => $gc_customize->changeset_uuid(),
			'gc_customize'               => 'on',
			'customize_changeset_status' => 'publish',
		);
		ob_start();
		?>
		<?php gc_print_scripts( array( 'gc-util' ) ); ?>
		<script>
			gc.ajax.post( 'customize_save', <?php echo gc_json_encode( $request_args ); ?> );
		</script>
		<?php
		$script = ob_get_clean();

		gc_die(
			'<h1>' . __( '您计划的修改刚才发布了' ) . '</h1>' .
			'<p><a href="' . esc_url( remove_query_arg( 'changeset_uuid' ) ) . '">' . __( '定制器全新变化' ) . '</a></p>' . $script,
			200
		);
	}

	if ( in_array( get_post_status( $changeset_post->ID ), array( 'publish', 'trash' ), true ) ) {
		gc_die(
			'<h1>' . __( '出现了问题。' ) . '</h1>' .
			'<p>' . __( '此变更集不能被进一步修改。' ) . '</p>' .
			'<p><a href="' . esc_url( remove_query_arg( 'changeset_uuid' ) ) . '">' . __( '定制器全新变化' ) . '</a></p>',
			403
		);
	}
}


gc_reset_vars( array( 'url', 'return', 'autofocus' ) );
if ( ! empty( $url ) ) {
	$gc_customize->set_preview_url( gc_unslash( $url ) );
}
if ( ! empty( $return ) ) {
	$gc_customize->set_return_url( gc_unslash( $return ) );
}
if ( ! empty( $autofocus ) && is_array( $autofocus ) ) {
	$gc_customize->set_autofocus( gc_unslash( $autofocus ) );
}

$registered             = $gc_scripts->registered;
$gc_scripts             = new GC_Scripts;
$gc_scripts->registered = $registered;

add_action( 'customize_controls_print_scripts', 'print_head_scripts', 20 );
add_action( 'customize_controls_print_footer_scripts', '_gc_footer_scripts' );
add_action( 'customize_controls_print_styles', 'print_admin_styles', 20 );

/**
 * Fires when Customizer controls are initialized, before scripts are enqueued.
 *
 *
 */
do_action( 'customize_controls_init' );

gc_enqueue_script( 'heartbeat' );
gc_enqueue_script( 'customize-controls' );
gc_enqueue_style( 'customize-controls' );

/**
 * Enqueue Customizer control scripts.
 *
 *
 */
do_action( 'customize_controls_enqueue_scripts' );

// Let's roll.
header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

gc_user_settings();
_gc_admin_html_begin();

$body_class = 'gc-core-ui gc-customizer js';

if ( gc_is_mobile() ) :
	$body_class .= ' mobile';
	add_filter( 'admin_viewport_meta', '_customizer_mobile_viewport_meta' );
endif;

if ( $gc_customize->is_ios() ) {
	$body_class .= ' ios';
}

if ( is_rtl() ) {
	$body_class .= ' rtl';
}
$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

if ( gc_use_widgets_block_editor() ) {
	$body_class .= ' gc-embed-responsive';
}

$admin_title = sprintf( $gc_customize->get_document_title_template(), __( '载入中&hellip;' ) );

?>
<title><?php echo esc_html( $admin_title ); ?></title>

<script type="text/javascript">
var ajaxurl = <?php echo gc_json_encode( admin_url( 'admin-ajax.php', 'relative' ) ); ?>,
	pagenow = 'customize';
</script>

<?php
/**
 * Fires when Customizer control styles are printed.
 *
 *
 */
do_action( 'customize_controls_print_styles' );

/**
 * Fires when Customizer control scripts are printed.
 *
 *
 */
do_action( 'customize_controls_print_scripts' );

/**
 * Fires in head section of Customizer controls.
 *
 *
 */
do_action( 'customize_controls_head' );
?>
</head>
<body class="<?php echo esc_attr( $body_class ); ?>">
<div class="gc-full-overlay expanded">
	<form id="customize-controls" class="wrap gc-full-overlay-sidebar">
		<div id="customize-header-actions" class="gc-full-overlay-header">
			<?php
			$compatible_gc  = is_gc_version_compatible( $gc_customize->theme()->get( 'RequiresGC' ) );
			$compatible_php = is_php_version_compatible( $gc_customize->theme()->get( 'RequiresPHP' ) );
			?>
			<?php if ( $compatible_gc && $compatible_php ) : ?>
				<?php $save_text = $gc_customize->is_theme_active() ? __( '发布' ) : __( '启用并发布' ); ?>
				<div id="customize-save-button-wrapper" class="customize-save-button-wrapper" >
					<?php submit_button( $save_text, 'primary save', 'save', false ); ?>
					<button id="publish-settings" class="publish-settings button-primary button dashicons dashicons-admin-generic" aria-label="<?php esc_attr_e( '发布设置' ); ?>" aria-expanded="false" disabled></button>
				</div>
			<?php else : ?>
				<?php $save_text = _x( '无法启用', 'theme' ); ?>
				<div id="customize-save-button-wrapper" class="customize-save-button-wrapper disabled" >
					<button class="button button-primary disabled" aria-label="<?php esc_attr_e( '发布设置' ); ?>" aria-expanded="false" disabled><?php echo $save_text; ?></button>
				</div>
			<?php endif; ?>
			<span class="spinner"></span>
			<button type="button" class="customize-controls-preview-toggle">
				<span class="controls"><?php _e( '自定义' ); ?></span>
				<span class="preview"><?php _e( '预览' ); ?></span>
			</button>
			<a class="customize-controls-close" href="<?php echo esc_url( $gc_customize->get_return_url() ); ?>">
				<span class="screen-reader-text"><?php _e( '关闭定制器并返回到前一页' ); ?></span>
			</a>
		</div>

		<div id="customize-sidebar-outer-content">
			<div id="customize-outer-theme-controls">
				<ul class="customize-outer-pane-parent"><?php // Outer panel and sections are not implemented, but its here as a placeholder to avoid any side-effect in api.Section. ?></ul>
			</div>
		</div>

		<div id="widgets-right" class="gc-clearfix"><!-- For Widget Customizer, many widgets try to look for instances under div#widgets-right, so we have to add that ID to a container div in the Customizer for compat -->
			<div id="customize-notifications-area" class="customize-control-notifications-container">
				<ul></ul>
			</div>
			<div class="gc-full-overlay-sidebar-content" tabindex="-1">
				<div id="customize-info" class="accordion-section customize-info" data-block-theme="<?php echo (int) gc_is_block_theme(); ?>">
					<div class="accordion-section-title">
						<span class="preview-notice">
						<?php
							/* translators: %s: The site/panel title in the Customizer. */
							printf( __( '您正在自定义%s' ), '<strong class="panel-title site-title">' . get_bloginfo( 'name', 'display' ) . '</strong>' );
						?>
						</span>
						<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( '帮助' ); ?></span></button>
					</div>
					<div class="customize-panel-description">
						<p>
							<?php
							_e( '定制器允许您在发布前对站点的更改进行预览。您可以在预览中查看站点的不同页面。对于一些可编辑元素，会显示编辑快捷方式。' );
							?>
						</p>
						<p>
							<?php
							_e( '<a href="https://www.gechiui.com/support/appearance-customize-screen/">定制器文档</a>' );
							?>
						</p>
					</div>
				</div>

				<div id="customize-theme-controls">
					<ul class="customize-pane-parent"><?php // Panels and sections are managed here via JavaScript ?></ul>
				</div>
			</div>
		</div>

		<div id="customize-footer-actions" class="gc-full-overlay-footer">
			<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="<?php echo esc_attr_x( '隐藏控制区', 'label for hide controls button without length constraints' ); ?>">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _ex( '隐藏控制区', 'short (~12 characters) label for hide controls button' ); ?></span>
			</button>
			<?php $previewable_devices = $gc_customize->get_previewable_devices(); ?>
			<?php if ( ! empty( $previewable_devices ) ) : ?>
			<div class="devices-wrapper">
				<div class="devices">
					<?php foreach ( (array) $previewable_devices as $device => $settings ) : ?>
						<?php
						if ( empty( $settings['label'] ) ) {
							continue;
						}
						$active = ! empty( $settings['default'] );
						$class  = 'preview-' . $device;
						if ( $active ) {
							$class .= ' active';
						}
						?>
						<button type="button" class="<?php echo esc_attr( $class ); ?>" aria-pressed="<?php echo esc_attr( $active ); ?>" data-device="<?php echo esc_attr( $device ); ?>">
							<span class="screen-reader-text"><?php echo esc_html( $settings['label'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</form>
	<div id="customize-preview" class="gc-full-overlay-main"></div>
	<?php

	/**
	 * Prints templates, control scripts, and settings in the footer.
	 *
	 */
	do_action( 'customize_controls_print_footer_scripts' );
	?>
</div>
</body>
</html>
