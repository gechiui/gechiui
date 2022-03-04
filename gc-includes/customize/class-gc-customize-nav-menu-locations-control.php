<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Locations_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Nav Menu Locations Control Class.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Locations_Control extends GC_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'nav_menu_locations';

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 */
	public function content_template() {
		if ( current_theme_supports( 'menus' ) ) :
			?>
			<# var elementId; #>
			<ul class="menu-location-settings">
				<li class="customize-control assigned-menu-locations-title">
					<span class="customize-control-title">{{ gc.customize.Menus.data.l10n.locationsTitle }}</span>
					<# if ( data.isCreating ) { #>
						<p>
							<?php echo _x( '您希望这个菜单出现在哪？', 'menu locations' ); ?>
							<?php
							printf(
								/* translators: 1: Documentation URL, 2: Additional link attributes, 3: Accessibility text. */
								_x( '（如果您希望使用菜单<a href="%1$s" %2$s>小工具%3$s</a>，跳过这一步。）', 'menu locations' ),
								__( 'https://www.gechiui.com/support/gechiui-widgets/' ),
								' class="external-link" target="_blank"',
								sprintf(
									'<span class="screen-reader-text"> %s</span>',
									/* translators: Accessibility text. */
									__( '（在新窗口中打开）' )
								)
							);
							?>
						</p>
					<# } else { #>
						<p><?php echo _x( '这里是菜单将会出现的位置。如果您希望菜单出现在别处，请选择其他位置。', 'menu locations' ); ?></p>
					<# } #>
				</li>

				<?php foreach ( get_registered_nav_menus() as $location => $description ) : ?>
					<# elementId = _.uniqueId( 'customize-nav-menu-control-location-' ); #>
					<li class="customize-control customize-control-checkbox assigned-menu-location">
						<span class="customize-inside-control-row">
							<input id="{{ elementId }}" type="checkbox" data-menu-id="{{ data.menu_id }}" data-location-id="<?php echo esc_attr( $location ); ?>" class="menu-location" />
							<label for="{{ elementId }}">
								<?php echo $description; ?>
								<span class="theme-location-set">
									<?php
									printf(
										/* translators: %s: Menu name. */
										_x( '（当前：%s）', 'menu location' ),
										'<span class="current-menu-location-name-' . esc_attr( $location ) . '"></span>'
									);
									?>
								</span>
							</label>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
		endif;
	}
}
