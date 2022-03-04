<?php
/**
 * Customize API: GC_Customize_Background_Image_Setting class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customizer Background Image Setting class.
 *
 *
 *
 * @see GC_Customize_Setting
 */
final class GC_Customize_Background_Image_Setting extends GC_Customize_Setting {
	public $id = 'background_image_thumb';

	/**
	 *
	 * @param mixed $value The value to update. Not used.
	 */
	public function update( $value ) {
		remove_theme_mod( 'background_image_thumb' );
	}
}
