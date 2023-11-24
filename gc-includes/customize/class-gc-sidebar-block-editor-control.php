<?php
/**
 * Customize API: GC_Sidebar_Block_Editor_Control class.
 *
 * @package GeChiUI
 * @subpackage Customize
 * @since 5.8.0
 */

/**
 * Core class used to implement the widgets block editor control in the
 * customizer.
 *
 * @since 5.8.0
 *
 * @see GC_Customize_Control
 */
class GC_Sidebar_Block_Editor_Control extends GC_Customize_Control {
	/**
	 * The control type.
	 *
	 * @since 5.8.0
	 *
	 * @var string
	 */
	public $type = 'sidebar_block_editor';

	/**
	 * Render the widgets block editor container.
	 *
	 * @since 5.8.0
	 */
	public function render_content() {
		// Render an empty control. The JavaScript in
		// @gechiui/customize-widgets will do the rest.
	}
}
