<?php
/**
 * Customize API: GC_Customize_Image_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Image Control class.
 *
 *
 *
 * @see GC_Customize_Upload_Control
 */
class GC_Customize_Image_Control extends GC_Customize_Upload_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'image';

	/**
	 * Media control mime type.
	 *
	 * @var string
	 */
	public $mime_type = 'image';

	/**
	 * @deprecated 4.1.0
	 */
	public function prepare_control() {}

	/**
	 * @deprecated 4.1.0
	 *
	 * @param string $id
	 * @param string $label
	 * @param mixed  $callback
	 */
	public function add_tab( $id, $label, $callback ) {
		_deprecated_function( __METHOD__, '4.1.0' );
	}

	/**
	 * @deprecated 4.1.0
	 *
	 * @param string $id
	 */
	public function remove_tab( $id ) {
		_deprecated_function( __METHOD__, '4.1.0' );
	}

	/**
	 * @deprecated 4.1.0
	 *
	 * @param string $url
	 * @param string $thumbnail_url
	 */
	public function print_tab_image( $url, $thumbnail_url = null ) {
		_deprecated_function( __METHOD__, '4.1.0' );
	}
}
