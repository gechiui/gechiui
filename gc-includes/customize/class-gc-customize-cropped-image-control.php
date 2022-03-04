<?php
/**
 * Customize API: GC_Customize_Cropped_Image_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Cropped Image Control class.
 *
 *
 *
 * @see GC_Customize_Image_Control
 */
class GC_Customize_Cropped_Image_Control extends GC_Customize_Image_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'cropped_image';

	/**
	 * Suggested width for cropped image.
	 *
	 * @var int
	 */
	public $width = 150;

	/**
	 * Suggested height for cropped image.
	 *
	 * @var int
	 */
	public $height = 150;

	/**
	 * Whether the width is flexible.
	 *
	 * @var bool
	 */
	public $flex_width = false;

	/**
	 * Whether the height is flexible.
	 *
	 * @var bool
	 */
	public $flex_height = false;

	/**
	 * Enqueue control related scripts/styles.
	 *
	 */
	public function enqueue() {
		gc_enqueue_script( 'customize-views' );

		parent::enqueue();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 *
	 * @see GC_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['width']       = absint( $this->width );
		$this->json['height']      = absint( $this->height );
		$this->json['flex_width']  = absint( $this->flex_width );
		$this->json['flex_height'] = absint( $this->flex_height );
	}

}
