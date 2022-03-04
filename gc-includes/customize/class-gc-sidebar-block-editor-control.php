<?php
/**
 * Customize API: GC_Sidebar_Block_Editor_Control class.
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Core class used to implement the widgets block editor control in the
 * customizer.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Sidebar_Block_Editor_Control extends GC_Customize_Control {
	/**
	 * The control type.
	 *
	 *
	 * @var string
	 */
	public $type = 'sidebar_block_editor';

	/**
	 * Render the widgets block editor container.
	 *
	 */
	public function render_content() {
		// Render an empty control. The JavaScript in
		// @gechiui/customize-widgets will do the rest.
	}
}
