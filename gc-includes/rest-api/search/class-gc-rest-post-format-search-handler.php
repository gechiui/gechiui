<?php
/**
 * REST API: GC_REST_Post_Format_Search_Handler class
 *
 * @package GeChiUI
 * @subpackage REST_API
 */

/**
 * Core class representing a search handler for post formats in the REST API.
 *
 * @see GC_REST_Search_Handler
 */
class GC_REST_Post_Format_Search_Handler extends GC_REST_Search_Handler {

	/**
	 * Constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->type = 'post-format';
	}

	/**
	 * Searches the object type content for a given search request.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full REST request.
	 * @return array Associative array containing an `GC_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `GC_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	public function search_items( GC_REST_Request $request ) {
		$format_strings = get_post_format_strings();
		$format_slugs   = array_keys( $format_strings );

		$query_args = array();

		if ( ! empty( $request['search'] ) ) {
			$query_args['search'] = $request['search'];
		}

		/**
		 * Filters the query arguments for a REST API search request.
		 *
		 * Enables adding extra arguments or setting defaults for a post format search request.
		 *
		 * @since 5.6.0
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param GC_REST_Request $request    The request used.
		 */
		$query_args = apply_filters( 'rest_post_format_search_query', $query_args, $request );

		$found_ids = array();
		foreach ( $format_slugs as $index => $format_slug ) {
			if ( ! empty( $query_args['search'] ) ) {
				$format_string       = get_post_format_string( $format_slug );
				$format_slug_match   = stripos( $format_slug, $query_args['search'] ) !== false;
				$format_string_match = stripos( $format_string, $query_args['search'] ) !== false;
				if ( ! $format_slug_match && ! $format_string_match ) {
					continue;
				}
			}

			$format_link = get_post_format_link( $format_slug );
			if ( $format_link ) {
				$found_ids[] = $format_slug;
			}
		}

		$page     = (int) $request['page'];
		$per_page = (int) $request['per_page'];

		return array(
			self::RESULT_IDS   => array_slice( $found_ids, ( $page - 1 ) * $per_page, $per_page ),
			self::RESULT_TOTAL => count( $found_ids ),
		);
	}

	/**
	 * Prepares the search result for a given ID.
	 *
	 * @since 5.6.0
	 *
	 * @param string $id     Item ID, the post format slug.
	 * @param array  $fields Fields to include for the item.
	 * @return array Associative array containing all fields for the item.
	 */
	public function prepare_item( $id, array $fields ) {
		$data = array();

		if ( in_array( GC_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_ID ] = $id;
		}

		if ( in_array( GC_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_TITLE ] = get_post_format_string( $id );
		}

		if ( in_array( GC_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_URL ] = get_post_format_link( $id );
		}

		if ( in_array( GC_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}

		return $data;
	}

	/**
	 * Prepares links for the search result.
	 *
	 * @since 5.6.0
	 *
	 * @param string $id Item ID, the post format slug.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {
		return array();
	}
}
