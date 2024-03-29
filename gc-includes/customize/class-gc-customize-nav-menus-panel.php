<?php
/**
 * Customize API: GC_Customize_Nav_Menus_Panel class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Nav Menus Panel Class
 *
 * Needed to add screen options.
 *
 * @since 4.3.0
 *
 * @see GC_Customize_Panel
 */
class GC_Customize_Nav_Menus_Panel extends GC_Customize_Panel {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'nav_menus';

	/**
	 * Render screen options for Menus.
	 *
	 * @since 4.3.0
	 */
	public function render_screen_options() {
		// Adds the screen options.
		require_once ABSPATH . 'gc-admin/includes/nav-menu.php';
		add_filter( 'manage_nav-menus_columns', 'gc_nav_menu_manage_columns' );

		// Display screen options.
		$screen = GC_Screen::get( 'nav-menus.php' );
		$screen->render_screen_options( array( 'wrap' => false ) );
	}

	/**
	 * Returns the advanced options for the nav menus page.
	 *
	 * Link title attribute added as it's a relatively advanced concept for new users.
	 *
	 * @since 4.3.0
	 * @deprecated 4.5.0 Deprecated in favor of gc_nav_menu_manage_columns().
	 */
	public function gc_nav_menu_manage_columns() {
		_deprecated_function( __METHOD__, '4.5.0', 'gc_nav_menu_manage_columns' );
		require_once ABSPATH . 'gc-admin/includes/nav-menu.php';
		return gc_nav_menu_manage_columns();
	}

	/**
	 * An Underscore (JS) template for this panel's content (but not its container).
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding GC_Customize_Panel::json().
	 *
	 * @since 4.3.0
	 *
	 * @see GC_Customize_Panel::print_template()
	 */
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button type="button" class="customize-panel-back" tabindex="-1">
				<span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '返回' );
					?>
				</span>
			</button>
			<div class="accordion-section-title">
				<span class="preview-notice">
					<?php
					/* translators: %s: The site/panel title in the Customizer. */
					printf( __( '您正在自定义%s' ), '<strong class="panel-title">{{ data.title }}</strong>' );
					?>
				</span>
				<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false">
					<span class="screen-reader-text">
						<?php
						/* translators: Hidden accessibility text. */
						_e( '帮助' );
						?>
					</span>
				</button>
				<button type="button" class="customize-screen-options-toggle" aria-expanded="false">
					<span class="screen-reader-text">
						<?php
						/* translators: Hidden accessibility text. */
						_e( '菜单选项' );
						?>
					</span>
				</button>
			</div>
			<# if ( data.description ) { #>
			<div class="description customize-panel-description">{{{ data.description }}}</div>
			<# } #>
			<div id="screen-options-wrap">
				<?php $this->render_screen_options(); ?>
			</div>
		</li>
		<?php
		// NOTE: The following is a workaround for an inability to treat (and thus label) a list of sections as a whole.
		?>
		<li class="customize-control-title customize-section-title-nav_menus-heading"><?php _e( '菜单' ); ?></li>
		<?php
	}
}
