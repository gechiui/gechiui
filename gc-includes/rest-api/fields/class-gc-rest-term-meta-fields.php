<?php
/**
 * REST API: GC_REST_Term_Meta_Fields class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to manage meta values for terms via the REST API.
 *
 *
 *
 * @see GC_REST_Meta_Fields
 */
class GC_REST_Term_Meta_Fields extends GC_REST_Meta_Fields {

	/**
	 * Taxonomy to register fields for.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $taxonomy Taxonomy to register fields for.
	 */
	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	/**
	 * Retrieves the term meta type.
	 *
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return 'term';
	}

	/**
	 * Retrieves the term meta subtype.
	 *
	 *
	 * @return string Subtype for the meta type, or empty string if no specific subtype.
	 */
	protected function get_meta_subtype() {
		return $this->taxonomy;
	}

	/**
	 * Retrieves the type for register_rest_field().
	 *
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {
		return 'post_tag' === $this->taxonomy ? 'tag' : $this->taxonomy;
	}
}
