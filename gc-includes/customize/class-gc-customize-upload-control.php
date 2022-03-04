<?php
/**
 * Customize API: GC_Customize_Upload_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Upload Control Class.
 *
 *
 *
 * @see GC_Customize_Media_Control
 */
class GC_Customize_Upload_Control extends GC_Customize_Media_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'upload';

	/**
	 * Media control mime type.
	 *
	 * @var string
	 */
	public $mime_type = '';

	/**
	 * Button labels.
	 *
	 * @var array
	 */
	public $button_labels = array();

	public $removed = '';         // Unused.
	public $context;              // Unused.
	public $extensions = array(); // Unused.

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 *
	 * @uses GC_Customize_Media_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$value = $this->value();
		if ( $value ) {
			// Get the attachment model for the existing file.
			$attachment_id = attachment_url_to_postid( $value );
			if ( $attachment_id ) {
				$this->json['attachment'] = gc_prepare_attachment_for_js( $attachment_id );
			}
		}
	}
}
