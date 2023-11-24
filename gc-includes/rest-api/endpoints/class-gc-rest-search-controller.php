<?php
/**
 * REST API: GC_REST_Search_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class to search through all GeChiUI content via the REST API.
 *
 * @since 5.0.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Search_Controller extends GC_REST_Controller {

	/**
	 * ID property name.
	 */
	const PROP_ID = 'id';

	/**
	 * Title property name.
	 */
	const PROP_TITLE = 'title';

	/**
	 * URL property name.
	 */
	const PROP_URL = 'url';

	/**
	 * Type property name.
	 */
	const PROP_TYPE = 'type';

	/**
	 * Subtype property name.
	 */
	const PROP_SUBTYPE = 'subtype';

	/**
	 * Identifier for the 'any' type.
	 */
	const TYPE_ANY = 'any';

	/**
	 * Search handlers used by the controller.
	 *
	 * @since 5.0.0
	 * @var GC_REST_Search_Handler[]
	 */
	protected $search_handlers = array();

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 *
	 * @param array $search_handlers List of search handlers to use in the controller. Each search
	 *                               handler instance must extend the `GC_REST_Search_Handler` class.
	 */
	public function __construct( array $search_handlers ) {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'search';

		foreach ( $search_handlers as $search_handler ) {
			if ( ! $search_handler instanceof GC_REST_Search_Handler ) {
				_doing_it_wrong(
					__METHOD__,
					/* translators: %s: PHP class name. */
					sprintf( __( 'REST搜索处理器必须继承%s类。' ), 'GC_REST_Search_Handler' ),
					'5.0.0'
				);
				continue;
			}

			$this->search_handlers[ $search_handler->get_type() ] = $search_handler;
		}
	}

	/**
	 * Registers the routes for the search controller.
	 *
	 * @since 5.0.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permission_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to search content.
	 *
	 * @since 5.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has search access, GC_Error object otherwise.
	 */
	public function get_items_permission_check( $request ) {
		return true;
	}

	/**
	 * Retrieves a collection of search results.
	 *
	 * @since 5.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$handler = $this->get_search_handler( $request );
		if ( is_gc_error( $handler ) ) {
			return $handler;
		}

		$result = $handler->search_items( $request );

		if ( ! isset( $result[ GC_REST_Search_Handler::RESULT_IDS ] ) || ! is_array( $result[ GC_REST_Search_Handler::RESULT_IDS ] ) || ! isset( $result[ GC_REST_Search_Handler::RESULT_TOTAL ] ) ) {
			return new GC_Error(
				'rest_search_handler_error',
				__( '内部搜索处理器错误。' ),
				array( 'status' => 500 )
			);
		}

		$ids = $result[ GC_REST_Search_Handler::RESULT_IDS ];

		$results = array();

		foreach ( $ids as $id ) {
			$data      = $this->prepare_item_for_response( $id, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$total     = (int) $result[ GC_REST_Search_Handler::RESULT_TOTAL ];
		$page      = (int) $request['page'];
		$per_page  = (int) $request['per_page'];
		$max_pages = ceil( $total / $per_page );

		if ( $page > $max_pages && $total > 0 ) {
			return new GC_Error(
				'rest_search_invalid_page_number',
				__( '请求的页码大于总页数。' ),
				array( 'status' => 400 )
			);
		}

		$response = rest_ensure_response( $results );
		$response->header( 'X-GC-Total', $total );
		$response->header( 'X-GC-TotalPages', $max_pages );

		$request_params = $request->get_query_params();
		$base           = add_query_arg( urlencode_deep( $request_params ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_link = add_query_arg( 'page', $page - 1, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $page < $max_pages ) {
			$next_link = add_query_arg( 'page', $page + 1, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Prepares a single search result for response.
	 *
	 * @since 5.0.0
	 * @since 5.6.0 The `$id` parameter can accept a string.
	 * @since 5.9.0 Renamed `$id` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param int|string      $item    ID of the item to prepare.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$item_id = $item;
		$handler = $this->get_search_handler( $request );
		if ( is_gc_error( $handler ) ) {
			return new GC_REST_Response();
		}

		$fields = $this->get_fields_for_response( $request );

		$data = $handler->prepare_item( $item_id, $fields );
		$data = $this->add_additional_fields_to_object( $data, $request );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links               = $handler->prepare_item_links( $item_id );
			$links['collection'] = array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			);
			$response->add_links( $links );
		}

		return $response;
	}

	/**
	 * Retrieves the item schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$types    = array();
		$subtypes = array();

		foreach ( $this->search_handlers as $search_handler ) {
			$types[]  = $search_handler->get_type();
			$subtypes = array_merge( $subtypes, $search_handler->get_subtypes() );
		}

		$types    = array_unique( $types );
		$subtypes = array_unique( $subtypes );

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'search-result',
			'type'       => 'object',
			'properties' => array(
				self::PROP_ID      => array(
					'description' => __( '对象的唯一标识符。' ),
					'type'        => array( 'integer', 'string' ),
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_TITLE   => array(
					'description' => __( '对象的标题。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_URL     => array(
					'description' => __( '对象的URL。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_TYPE    => array(
					'description' => __( '对象类型。' ),
					'type'        => 'string',
					'enum'        => $types,
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_SUBTYPE => array(
					'description' => __( '对象子类型。' ),
					'type'        => 'string',
					'enum'        => $subtypes,
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @since 5.0.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$types    = array();
		$subtypes = array();

		foreach ( $this->search_handlers as $search_handler ) {
			$types[]  = $search_handler->get_type();
			$subtypes = array_merge( $subtypes, $search_handler->get_subtypes() );
		}

		$types    = array_unique( $types );
		$subtypes = array_unique( $subtypes );

		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params[ self::PROP_TYPE ] = array(
			'default'     => $types[0],
			'description' => __( '限制结果为一种对象类型。' ),
			'type'        => 'string',
			'enum'        => $types,
		);

		$query_params[ self::PROP_SUBTYPE ] = array(
			'default'           => self::TYPE_ANY,
			'description'       => __( '限制结果为一种或多种对象子类型。' ),
			'type'              => 'array',
			'items'             => array(
				'enum' => array_merge( $subtypes, array( self::TYPE_ANY ) ),
				'type' => 'string',
			),
			'sanitize_callback' => array( $this, 'sanitize_subtypes' ),
		);

		$query_params['exclude'] = array(
			'description' => __( '确保结果集排除指定ID。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( '将结果集限制为指定ID。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		return $query_params;
	}

	/**
	 * Sanitizes the list of subtypes, to ensure only subtypes of the passed type are included.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array    $subtypes  One or more subtypes.
	 * @param GC_REST_Request $request   Full details about the request.
	 * @param string          $parameter Parameter name.
	 * @return string[]|GC_Error List of valid subtypes, or GC_Error object on failure.
	 */
	public function sanitize_subtypes( $subtypes, $request, $parameter ) {
		$subtypes = gc_parse_slug_list( $subtypes );

		$subtypes = rest_parse_request_arg( $subtypes, $request, $parameter );
		if ( is_gc_error( $subtypes ) ) {
			return $subtypes;
		}

		// 'any' overrides any other subtype.
		if ( in_array( self::TYPE_ANY, $subtypes, true ) ) {
			return array( self::TYPE_ANY );
		}

		$handler = $this->get_search_handler( $request );
		if ( is_gc_error( $handler ) ) {
			return $handler;
		}

		return array_intersect( $subtypes, $handler->get_subtypes() );
	}

	/**
	 * Gets the search handler to handle the current request.
	 *
	 * @since 5.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Search_Handler|GC_Error Search handler for the request type, or GC_Error object on failure.
	 */
	protected function get_search_handler( $request ) {
		$type = $request->get_param( self::PROP_TYPE );

		if ( ! $type || ! isset( $this->search_handlers[ $type ] ) ) {
			return new GC_Error(
				'rest_search_invalid_type',
				__( '无效类型参数。' ),
				array( 'status' => 400 )
			);
		}

		return $this->search_handlers[ $type ];
	}
}
