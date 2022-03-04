<?php
/**
 * REST API: GC_REST_Post_Search_Handler class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class representing a search handler for posts in the REST API.
 *
 *
 *
 * @see GC_REST_Search_Handler
 */
class GC_REST_Post_Search_Handler extends GC_REST_Search_Handler {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->type = 'post';

		// Support all public post types except attachments.
		$this->subtypes = array_diff(
			array_values(
				get_post_types(
					array(
						'public'       => true,
						'show_in_rest' => true,
					),
					'names'
				)
			),
			array( 'attachment' )
		);
	}

	/**
	 * Searches the object type content for a given search request.
	 *
	 *
	 * @param GC_REST_Request $request Full REST request.
	 * @return array Associative array containing an `GC_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `GC_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	public function search_items( GC_REST_Request $request ) {

		// Get the post types to search for the current request.
		$post_types = $request[ GC_REST_Search_Controller::PROP_SUBTYPE ];
		if ( in_array( GC_REST_Search_Controller::TYPE_ANY, $post_types, true ) ) {
			$post_types = $this->subtypes;
		}

		$query_args = array(
			'post_type'           => $post_types,
			'post_status'         => 'publish',
			'paged'               => (int) $request['page'],
			'posts_per_page'      => (int) $request['per_page'],
			'ignore_sticky_posts' => true,
			'fields'              => 'ids',
		);

		if ( ! empty( $request['search'] ) ) {
			$query_args['s'] = $request['search'];
		}

		/**
		 * Filters the query arguments for a REST API search request.
		 *
		 * Enables adding extra arguments or setting defaults for a post search request.
		 *
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param GC_REST_Request $request    The request used.
		 */
		$query_args = apply_filters( 'rest_post_search_query', $query_args, $request );

		$query     = new GC_Query();
		$found_ids = $query->query( $query_args );
		$total     = $query->found_posts;

		return array(
			self::RESULT_IDS   => $found_ids,
			self::RESULT_TOTAL => $total,
		);
	}

	/**
	 * Prepares the search result for a given ID.
	 *
	 *
	 * @param int   $id     Item ID.
	 * @param array $fields Fields to include for the item.
	 * @return array Associative array containing all fields for the item.
	 */
	public function prepare_item( $id, array $fields ) {
		$post = get_post( $id );

		$data = array();

		if ( in_array( GC_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_ID ] = (int) $post->ID;
		}

		if ( in_array( GC_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			if ( post_type_supports( $post->post_type, 'title' ) ) {
				add_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
				$data[ GC_REST_Search_Controller::PROP_TITLE ] = get_the_title( $post->ID );
				remove_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
			} else {
				$data[ GC_REST_Search_Controller::PROP_TITLE ] = '';
			}
		}

		if ( in_array( GC_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_URL ] = get_permalink( $post->ID );
		}

		if ( in_array( GC_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}

		if ( in_array( GC_REST_Search_Controller::PROP_SUBTYPE, $fields, true ) ) {
			$data[ GC_REST_Search_Controller::PROP_SUBTYPE ] = $post->post_type;
		}

		return $data;
	}

	/**
	 * Prepares links for the search result of a given ID.
	 *
	 *
	 * @param int $id Item ID.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {
		$post = get_post( $id );

		$links = array();

		$item_route = rest_get_route_for_post( $post );
		if ( ! empty( $item_route ) ) {
			$links['self'] = array(
				'href'       => rest_url( $item_route ),
				'embeddable' => true,
			);
		}

		$links['about'] = array(
			'href' => rest_url( 'gc/v2/types/' . $post->post_type ),
		);

		return $links;
	}

	/**
	 * Overwrites the default protected title format.
	 *
	 * By default, GeChiUI will show password protected posts with a title of
	 * "密码保护：%s". As the REST API communicates the protected status of a post
	 * in a machine readable format, we remove the "Protected: " prefix.
	 *
	 *
	 * @return string Protected title format.
	 */
	public function protected_title_format() {
		return '%s';
	}

	/**
	 * Attempts to detect the route to access a single item.
	 *
	 * @deprecated 5.5.0 Use rest_get_route_for_post()
	 * @see rest_get_route_for_post()
	 *
	 * @param GC_Post $post Post object.
	 * @return string REST route relative to the REST base URI, or empty string if unknown.
	 */
	protected function detect_rest_item_route( $post ) {
		_deprecated_function( __METHOD__, '5.5.0', 'rest_get_route_for_post()' );

		return rest_get_route_for_post( $post );
	}

}
