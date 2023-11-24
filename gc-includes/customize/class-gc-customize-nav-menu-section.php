<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Section class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Menu Section Class
 *
 * Custom section only needed in JS.
 *
 * @since 4.3.0
 *
 * @see GC_Customize_Section
 */
class GC_Customize_Nav_Menu_Section extends GC_Customize_Section {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'nav_menu';

	/**
	 * Get section parameters for JS.
	 *
	 * @since 4.3.0
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported            = parent::json();
		$exported['menu_id'] = (int) preg_replace( '/^nav_menu\[(-?\d+)\]/', '$1', $this->id );

		return $exported;
	}
}
