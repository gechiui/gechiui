<?php
/**
 * REST API: GC_REST_Post_Meta_Fields class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to manage meta values for posts via the REST API.
 *
 *
 *
 * @see GC_REST_Meta_Fields
 */
class GC_REST_Post_Meta_Fields extends GC_REST_Meta_Fields {

	/**
	 * Post type to register fields for.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $post_type Post type to register fields for.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Retrieves the post meta type.
	 *
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return 'post';
	}

	/**
	 * Retrieves the post meta subtype.
	 *
	 *
	 * @return string Subtype for the meta type, or empty string if no specific subtype.
	 */
	protected function get_meta_subtype() {
		return $this->post_type;
	}

	/**
	 * Retrieves the type for register_rest_field().
	 *
	 *
	 * @see register_rest_field()
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {
		return $this->post_type;
	}
}
