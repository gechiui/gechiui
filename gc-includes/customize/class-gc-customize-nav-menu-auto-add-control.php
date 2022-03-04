<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Auto_Add_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize control to represent the auto_add field for a given menu.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Auto_Add_Control extends GC_Customize_Control {

	/**
	 * Type of control, used by JS.
	 *
	 * @var string
	 */
	public $type = 'nav_menu_auto_add';

	/**
	 * No-op since we're using JS template.
	 *
	 */
	protected function render_content() {}

	/**
	 * Render the Underscore template for this control.
	 *
	 */
	protected function content_template() {
		?>
		<# var elementId = _.uniqueId( 'customize-nav-menu-auto-add-control-' ); #>
		<span class="customize-control-title"><?php _e( '菜单选项' ); ?></span>
		<span class="customize-inside-control-row">
			<input id="{{ elementId }}" type="checkbox" class="auto_add" />
			<label for="{{ elementId }}">
				<?php _e( '自动将新的顶级页面添加至此菜单' ); ?>
			</label>
		</span>
		<?php
	}
}
