<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Name_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize control to represent the name field for a given menu.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Name_Control extends GC_Customize_Control {

	/**
	 * Type of control, used by JS.
	 *
	 * @var string
	 */
	public $type = 'nav_menu_name';

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
		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<input type="text" class="menu-name-field live-update-section-title"
				<# if ( data.description ) { #>
					aria-describedby="{{ data.section }}-description"
				<# } #>
				/>
		</label>
		<# if ( data.description ) { #>
			<p id="{{ data.section }}-description">{{ data.description }}</p>
		<# } #>
		<?php
	}
}
