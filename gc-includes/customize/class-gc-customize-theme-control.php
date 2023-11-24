<?php
/**
 * Customize API: GC_Customize_Theme_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Theme Control class.
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Theme_Control extends GC_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.2.0
	 * @var string
	 */
	public $type = 'theme';

	/**
	 * Theme object.
	 *
	 * @since 4.2.0
	 * @var GC_Theme
	 */
	public $theme;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 4.2.0
	 *
	 * @see GC_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['theme'] = $this->theme;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 4.2.0
	 */
	public function render_content() {}

	/**
	 * Render a JS template for theme display.
	 *
	 * @since 4.2.0
	 */
	public function content_template() {
		/* translators: %s: Theme name. */
		$details_label = sprintf( __( '主题详情：%s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$customize_label = sprintf( __( '定制主题：%s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$preview_label = sprintf( __( '实时预览主题：%s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$install_label = sprintf( __( '安装并预览主题：%s' ), '{{ data.theme.name }}' );
		?>
		<# if ( data.theme.active ) { #>
			<div class="theme active" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
		<# } else { #>
			<div class="theme" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
		<# } #>

			<# if ( data.theme.screenshot && data.theme.screenshot[0] ) { #>
				<div class="theme-screenshot">
					<img data-src="{{ data.theme.screenshot[0] }}?ver={{ data.theme.version }}" alt="" />
				</div>
			<# } else { #>
				<div class="theme-screenshot blank"></div>
			<# } #>

			<span class="more-details theme-details" id="{{ data.section }}-{{ data.theme.id }}-action" aria-label="<?php echo esc_attr( $details_label ); ?>"><?php _e( '主题详情' ); ?></span>

			<div class="theme-author">
			<?php
				/* translators: Theme author name. */
				printf( _x( '作者：%s', 'theme author' ), '{{ data.theme.author }}' );
			?>
			</div>

			<# if ( 'installed' === data.theme.type && data.theme.hasUpdate ) { #>
				<# if ( data.theme.updateResponse.compatibleGC && data.theme.updateResponse.compatiblePHP ) { #>
					<div class="update-message notice inline notice-warning notice-alt" data-slug="{{ data.theme.id }}">
						<p>
							<?php
							if ( is_multisite() ) {
								_e( '新版本现在可用。' );
							} else {
								printf(
									/* translators: %s: "立即更新" button. */
									__( '有新版本可用。%s' ),
									'<button class="button-link update-theme" type="button">' . __( '立即更新' ) . '</button>'
								);
							}
							?>
						</p>
					</div>
				<# } else { #>
					<div class="update-message notice inline notice-error notice-alt" data-slug="{{ data.theme.id }}">
						<p>
							<# if ( ! data.theme.updateResponse.compatibleGC && ! data.theme.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( '%s 有新版本可用，但与您当前使用的 GeChiUI 和 PHP 版本不兼容。' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
										' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
										self_admin_url( 'update-core.php' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to GeChiUI Updates screen. */
										' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
										self_admin_url( 'update-core.php' )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.theme.updateResponse.compatibleGC ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( '%s 有新版本可用，但与您当前使用的 GeChiUI 版本不兼容。' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to GeChiUI Updates screen. */
										' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
										self_admin_url( 'update-core.php' )
									);
								}
								?>
							<# } else if ( ! data.theme.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( '%s 有新版本可用，但与您当前使用的 PHP 版本不兼容。' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p>
					</div>
				<# } #>
			<# } #>

			<# if ( ! data.theme.compatibleGC || ! data.theme.compatiblePHP ) { #>
				<div class="alert alert-danger notice-alt"><p>
					<# if ( ! data.theme.compatibleGC && ! data.theme.compatiblePHP ) { #>
						<?php
						_e( '此主题未适配您当前的 GeChiUI 和 PHP 版本。' );
						if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
							printf(
								/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
								' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
								self_admin_url( 'update-core.php' ),
								esc_url( gc_get_update_php_url() )
							);
							gc_update_php_annotation( '</p><p><em>', '</em>' );
						} elseif ( current_user_can( 'update_core' ) ) {
							printf(
								/* translators: %s: URL to GeChiUI Updates screen. */
								' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
								self_admin_url( 'update-core.php' )
							);
						} elseif ( current_user_can( 'update_php' ) ) {
							printf(
								/* translators: %s: URL to Update PHP page. */
								' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
								esc_url( gc_get_update_php_url() )
							);
							gc_update_php_annotation( '</p><p><em>', '</em>' );
						}
						?>
					<# } else if ( ! data.theme.compatibleGC ) { #>
						<?php
						_e( '此主题未适配您当前的 GeChiUI 版本。' );
						if ( current_user_can( 'update_core' ) ) {
							printf(
								/* translators: %s: URL to GeChiUI Updates screen. */
								' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
								self_admin_url( 'update-core.php' )
							);
						}
						?>
					<# } else if ( ! data.theme.compatiblePHP ) { #>
						<?php
						_e( '此主题未适配您当前的 PHP 版本。' );
						if ( current_user_can( 'update_php' ) ) {
							printf(
								/* translators: %s: URL to Update PHP page. */
								' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
								esc_url( gc_get_update_php_url() )
							);
							gc_update_php_annotation( '</p><p><em>', '</em>' );
						}
						?>
					<# } #>
				</p></div>
			<# } #>

			<# if ( data.theme.active ) { #>
				<div class="theme-id-container">
					<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">
						<span><?php _ex( 'Previewing:', 'theme' ); ?></span> {{ data.theme.name }}
					</h3>
					<div class="theme-actions">
						<button type="button" class="btn btn-primary customize-theme" aria-label="<?php echo esc_attr( $customize_label ); ?>"><?php _e( '自定义' ); ?></button>
					</div>
				</div>
				<div class="alert alert-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
			<# } else if ( 'installed' === data.theme.type ) { #>
				<# if ( data.theme.blockTheme ) { #>
					<div class="theme-id-container">
						<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
						<div class="theme-actions">
							<# if ( data.theme.actions.activate ) { #>
								<?php
									/* translators: %s: Theme name. */
									$aria_label = sprintf( _x( '启用 %s', 'theme' ), '{{ data.name }}' );
								?>
								<a href="{{{ data.theme.actions.activate }}}" class="btn btn-primary activate" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
							<# } #>
						</div>
					</div>
					<div class="alert alert-danger notice-alt"><p>
					<?php
						_e( '此主题不支持自定义。' );
					?>
					<# if ( data.theme.actions.activate ) { #>
						<?php
							echo ' ';
							printf(
								/* translators: %s: URL to the themes page (also it activates the theme). */
								__( '但您也可以<a href="%s">启用此主题</a>，并使用系统编辑器对其进行自定义。' ),
								'{{{ data.theme.actions.activate }}}'
							);
						?>
					<# } #>
					</p></div>
				<# } else { #>
					<div class="theme-id-container">
						<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
						<div class="theme-actions">
							<# if ( data.theme.compatibleGC && data.theme.compatiblePHP ) { #>
								<button type="button" class="btn btn-primary preview-theme" aria-label="<?php echo esc_attr( $preview_label ); ?>" data-slug="{{ data.theme.id }}"><?php _e( '实时预览' ); ?></button>
							<# } else { #>
								<button type="button" class="btn btn-primary disabled" aria-label="<?php echo esc_attr( $preview_label ); ?>"><?php _e( '实时预览' ); ?></button>
							<# } #>
						</div>
					</div>
					<div class="alert alert-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
				<# } #>
			<# } else { #>
				<div class="theme-id-container">
					<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
					<div class="theme-actions">
						<# if ( data.theme.compatibleGC && data.theme.compatiblePHP ) { #>
							<button type="button" class="btn btn-primary theme-install preview" aria-label="<?php echo esc_attr( $install_label ); ?>" data-slug="{{ data.theme.id }}" data-name="{{ data.theme.name }}"><?php _e( '安装并预览' ); ?></button>
						<# } else { #>
							<button type="button" class="btn btn-primary disabled" aria-label="<?php echo esc_attr( $install_label ); ?>" disabled><?php _e( '安装并预览' ); ?></button>
						<# } #>
					</div>
				</div>
			<# } #>
		</div>
		<?php
	}
}
