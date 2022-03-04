<?php
/**
 * REST API: GC_REST_Comment_Meta_Fields class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class to manage comment meta via the REST API.
 *
 *
 *
 * @see GC_REST_Meta_Fields
 */
class GC_REST_Comment_Meta_Fields extends GC_REST_Meta_Fields {

	/**
	 * Retrieves the comment type for comment meta.
	 *
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return 'comment';
	}

	/**
	 * Retrieves the comment meta subtype.
	 *
	 *
	 * @return string 'comment' There are no subtypes.
	 */
	protected function get_meta_subtype() {
		return 'comment';
	}

	/**
	 * Retrieves the type for register_rest_field() in the context of comments.
	 *
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {
		return 'comment';
	}
}
