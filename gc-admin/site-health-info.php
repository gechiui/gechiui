<?php
/**
 * Tools Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'GC_Debug_Data' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-debug-data.php';
}
if ( ! class_exists( 'GC_Site_Health' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
}

$health_check_site_status = GC_Site_Health::get_instance();
echo setting_error( __( '系统健康检查需要JavaScript支持。' ), 'danger hide-if-js' );
?>

<div class="health-check-body health-check-debug-tab hide-if-no-js">
	<?php

	GC_Debug_Data::check_for_updates();

	$info = GC_Debug_Data::debug_data();

	?>

	<h2>
		<?php _e( '系统健康信息' ); ?>
	</h2>

	<p>
		<?php
			/* translators: %s: URL to Site Health Status page. */
			printf( __( '此页面能向您显示您的GeChiUI系统的每一个配置详情。想知道可被改善之处，请查阅<a href="%s">系统健康状态</a>页面。' ), esc_url( admin_url( 'site-health.php' ) ) );
		?>
	</p>
	<p>
		<?php _e( '如果您希望导出此页所有信息的列表，您可以使用下方的按钮将其复制到剪贴板。这样您就可以将其粘贴到文本文件并保存到本地，或粘贴到邮件正文并发送给您的支持工程师或是插件/主题开发者。' ); ?>
	</p>

	<div class="site-health-copy-buttons">
		<div class="copy-button-wrapper">
			<button type="button" class="button copy-button" data-clipboard-text="<?php echo esc_attr( GC_Debug_Data::format( $info, 'debug' ) ); ?>">
				<?php _e( '复制系统信息到剪贴板' ); ?>
			</button>
			<span class="success hidden" aria-hidden="true"><?php _e( '已复制！' ); ?></span>
		</div>
	</div>

	<div id="health-check-debug" class="health-check-accordion">

		<?php

		$sizes_fields = array( 'uploads_size', 'themes_size', 'plugins_size', 'gechiui_size', 'database_size', 'total_size' );

		foreach ( $info as $section => $details ) {
			if ( ! isset( $details['fields'] ) || empty( $details['fields'] ) ) {
				continue;
			}

			?>
			<h3 class="health-check-accordion-heading">
				<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" type="button">
					<span class="title">
						<?php echo esc_html( $details['label'] ); ?>
						<?php

						if ( isset( $details['show_count'] ) && $details['show_count'] ) {
							printf(
								'(%s)',
								number_format_i18n( count( $details['fields'] ) )
							);
						}

						?>
					</span>
					<?php

					if ( 'gc-paths-sizes' === $section ) {
						?>
						<span class="health-check-gc-paths-sizes spinner"></span>
						<?php
					}

					?>
					<span class="icon"></span>
				</button>
			</h3>

			<div id="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" class="health-check-accordion-panel" hidden="hidden">
				<?php

				if ( isset( $details['description'] ) && ! empty( $details['description'] ) ) {
					printf( '<p>%s</p>', $details['description'] );
				}

				?>
				<table class="widefat striped health-check-table" role="presentation">
					<tbody>
					<?php

					foreach ( $details['fields'] as $field_name => $field ) {
						if ( is_array( $field['value'] ) ) {
							$values = '<ul>';

							foreach ( $field['value'] as $name => $value ) {
								$values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
							}

							$values .= '</ul>';
						} else {
							$values = esc_html( $field['value'] );
						}

						if ( in_array( $field_name, $sizes_fields, true ) ) {
							printf( '<tr><td>%s</td><td class="%s">%s</td></tr>', esc_html( $field['label'] ), esc_attr( $field_name ), $values );
						} else {
							printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
						}
					}

					?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
</div>
