<?php
/**
 * REST API: GC_REST_User_Meta_Fields class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to manage meta values for users via the REST API.
 *
 *
 *
 * @see GC_REST_Meta_Fields
 */
class GC_REST_User_Meta_Fields extends GC_REST_Meta_Fields {

	/**
	 * Retrieves the user meta type.
	 *
	 *
	 * @return string The user meta type.
	 */
	protected function get_meta_type() {
		return 'user';
	}

	/**
	 * Retrieves the user meta subtype.
	 *
	 *
	 * @return string 'user' There are no subtypes.
	 */
	protected function get_meta_subtype() {
		return 'user';
	}

	/**
	 * Retrieves the type for register_rest_field().
	 *
	 *
	 * @return string The user REST field type.
	 */
	public function get_rest_field_type() {
		return 'user';
	}
}
