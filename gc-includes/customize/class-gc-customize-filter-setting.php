<?php
/**
 * Customize API: GC_Customize_Filter_Setting class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @see GC_Customize_Setting
 */
class GC_Customize_Filter_Setting extends GC_Customize_Setting {

	/**
	 * Saves the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 */
	public function update( $value ) {}
}
