<?php
/**
 * Customize API: GC_Customize_Themes_Panel class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Themes Panel Class
 *
 *
 *
 * @see GC_Customize_Panel
 */
class GC_Customize_Themes_Panel extends GC_Customize_Panel {

	/**
	 * Panel type.
	 *
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * An Underscore (JS) template for rendering this panel's container.
	 *
	 * The themes panel renders a custom panel heading with the active theme and a switch themes button.
	 *
	 * @see GC_Customize_Panel::print_template()
	 *
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-panel-themes">
			<h3 class="accordion-section-title">
				<?php
				if ( $this->manager->is_theme_active() ) {
					echo '<span class="customize-action">' . __( '启用主题' ) . '</span> {{ data.title }}';
				} else {
					echo '<span class="customize-action">' . __( '正在预览主题' ) . '</span> {{ data.title }}';
				}
				?>

				<?php if ( current_user_can( 'switch_themes' ) ) : ?>
					<button type="button" class="button change-theme" aria-label="<?php esc_attr_e( '更改主题' ); ?>"><?php _ex( '更改', 'theme' ); ?></button>
				<?php endif; ?>
			</h3>
			<ul class="accordion-sub-container control-panel-content"></ul>
		</li>
		<?php
	}

	/**
	 * An Underscore (JS) template for this panel's content (but not its container).
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding GC_Customize_Panel::json().
	 *
	 *
	 * @see GC_Customize_Panel::print_template()
	 */
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button class="customize-panel-back" tabindex="-1" type="button"><span class="screen-reader-text"><?php _e( '返回' ); ?></span></button>
			<div class="accordion-section-title">
				<span class="preview-notice">
					<?php
					printf(
						/* translators: %s: Themes panel title in the Customizer. */
						__( '您正在浏览%s' ),
						'<strong class="panel-title">' . __( '主题' ) . '</strong>'
					); // Separate strings for consistency with other panels.
					?>
				</span>
				<?php if ( current_user_can( 'install_themes' ) && ! is_multisite() ) : ?>
					<# if ( data.description ) { #>
						<button class="customize-help-toggle dashicons dashicons-editor-help" type="button" aria-expanded="false"><span class="screen-reader-text"><?php _e( '帮助' ); ?></span></button>
					<# } #>
				<?php endif; ?>
			</div>
			<?php if ( current_user_can( 'install_themes' ) && ! is_multisite() ) : ?>
				<# if ( data.description ) { #>
					<div class="description customize-panel-description">
						{{{ data.description }}}
					</div>
				<# } #>
			<?php endif; ?>

			<div class="customize-control-notifications-container"></div>
		</li>
		<li class="customize-themes-full-container-container">
			<div class="customize-themes-full-container">
				<div class="customize-themes-notifications"></div>
			</div>
		</li>
		<?php
	}
}
