<?php
/**
 * Block Pattern Directory REST API: GC_REST_Pattern_Directory_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 5.8.0
 */

/**
 * Controller which provides REST endpoint for block patterns.
 *
 * This simply proxies the endpoint at http://api.gechiui.com/patterns/1.0/. That isn't necessary for
 * functionality, but is desired for privacy. It prevents api.gechiui.com from knowing the user's IP address.
 *
 * @since 5.8.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Pattern_Directory_Controller extends GC_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 * @since 5.8.0
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'pattern-directory';
	}

	/**
	 * Registers the necessary REST API routes.
	 *
	 * @since 5.8.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/patterns',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to view the local block pattern directory.
	 *
	 * @since 5.8.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has permission, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new GC_Error(
			'rest_pattern_directory_cannot_view',
			__( '抱歉，您不能浏览本地区块样板目录。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Search and retrieve block patterns metadata
	 *
	 * @since 5.8.0
	 * @since 6.0.0 Added 'slug' to request.
	 * @since 6.2.0 Added 'per_page', 'page', 'offset', 'order', and 'orderby' to request.
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		/*
		 * Include an unmodified `$gc_version`, so the API can craft a response that's tailored to
		 * it. Some plugins modify the version in a misguided attempt to improve security by
		 * obscuring the version, which can cause invalid requests.
		 */
		require ABSPATH . GCINC . '/version.php';

		$valid_query_args = array(
			'offset'   => true,
			'order'    => true,
			'orderby'  => true,
			'page'     => true,
			'per_page' => true,
			'search'   => true,
			'slug'     => true,
		);
		$query_args       = array_intersect_key( $request->get_params(), $valid_query_args );

		$query_args['locale']             = get_user_locale();
		$query_args['gc-version']         = $gc_version; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- it's defined in `version.php` above.
		$query_args['pattern-categories'] = isset( $request['category'] ) ? $request['category'] : false;
		$query_args['pattern-keywords']   = isset( $request['keyword'] ) ? $request['keyword'] : false;

		$query_args = array_filter( $query_args );

		$transient_key = $this->get_transient_key( $query_args );

		/*
		 * Use network-wide transient to improve performance. The locale is the only site
		 * configuration that affects the response, and it's included in the transient key.
		 */
		$raw_patterns = get_site_transient( $transient_key );

		if ( ! $raw_patterns ) {
			$api_url = 'http://api.gechiui.com/patterns/1.0/?' . build_query( $query_args );
			if ( gc_http_supports( array( 'ssl' ) ) ) {
				$api_url = set_url_scheme( $api_url, 'https' );
			}

			/*
			 * Default to a short TTL, to mitigate cache stampedes on high-traffic sites.
			 * This assumes that most errors will be short-lived, e.g., packet loss that causes the
			 * first request to fail, but a follow-up one will succeed. The value should be high
			 * enough to avoid stampedes, but low enough to not interfere with users manually
			 * re-trying a failed request.
			 */
			$cache_ttl      = 5;
			$gcorg_response = gc_remote_get( $api_url );
			$raw_patterns   = json_decode( gc_remote_retrieve_body( $gcorg_response ) );

			if ( is_gc_error( $gcorg_response ) ) {
				$raw_patterns = $gcorg_response;

			} elseif ( ! is_array( $raw_patterns ) ) {
				// HTTP request succeeded, but response data is invalid.
				$raw_patterns = new GC_Error(
					'pattern_api_failed',
					sprintf(
						/* translators: %s: Support forums URL. */
						__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/forums/' )
					),
					array(
						'response' => gc_remote_retrieve_body( $gcorg_response ),
					)
				);

			} else {
				// Response has valid data.
				$cache_ttl = HOUR_IN_SECONDS;
			}

			set_site_transient( $transient_key, $raw_patterns, $cache_ttl );
		}

		if ( is_gc_error( $raw_patterns ) ) {
			$raw_patterns->add_data( array( 'status' => 500 ) );

			return $raw_patterns;
		}

		$response = array();

		if ( $raw_patterns ) {
			foreach ( $raw_patterns as $pattern ) {
				$response[] = $this->prepare_response_for_collection(
					$this->prepare_item_for_response( $pattern, $request )
				);
			}
		}

		return new GC_REST_Response( $response );
	}

	/**
	 * Prepare a raw block pattern before it gets output in a REST API response.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Renamed `$raw_pattern` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param object          $item    Raw pattern from api.gechiui.com, before any changes.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$raw_pattern      = $item;
		$prepared_pattern = array(
			'id'             => absint( $raw_pattern->id ),
			'title'          => sanitize_text_field( $raw_pattern->title->rendered ),
			'content'        => gc_kses_post( $raw_pattern->pattern_content ),
			'categories'     => array_map( 'sanitize_title', $raw_pattern->category_slugs ),
			'keywords'       => array_map( 'sanitize_text_field', explode( ',', $raw_pattern->meta->gcop_keywords ) ),
			'description'    => sanitize_text_field( $raw_pattern->meta->gcop_description ),
			'viewport_width' => absint( $raw_pattern->meta->gcop_viewport_width ),
			'block_types'    => array_map( 'sanitize_text_field', $raw_pattern->meta->gcop_block_types ),
		);

		$prepared_pattern = $this->add_additional_fields_to_object( $prepared_pattern, $request );

		$response = new GC_REST_Response( $prepared_pattern );

		/**
		 * Filters the REST API response for a block pattern.
		 *
		 * @since 5.8.0
		 *
		 * @param GC_REST_Response $response    The response object.
		 * @param object           $raw_pattern The unprepared block pattern.
		 * @param GC_REST_Request  $request     The request object.
		 */
		return apply_filters( 'rest_prepare_block_pattern', $response, $raw_pattern, $request );
	}

	/**
	 * Retrieves the block pattern's schema, conforming to JSON Schema.
	 *
	 * @since 5.8.0
	 * @since 6.2.0 Added `'block_types'` to schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'pattern-directory-item',
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( '区块样板的 ID。' ),
					'type'        => 'integer',
					'minimum'     => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'title'          => array(
					'description' => __( '区块样板标题，人类可读的格式。' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'content'        => array(
					'description' => __( '区块样板的内容。' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'categories'     => array(
					'description' => __( "区块样板的分类别名。" ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'keywords'       => array(
					'description' => __( "区块样板关键字。" ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'description'    => array(
					'description' => __( '区块样板的描述；' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'viewport_width' => array(
					'description' => __( '预览区块样板时视口的首选宽度（以像素为单位）。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'block_types'    => array(
					'description' => __( '可以使用此模式的块类型。' ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search parameters for the block pattern's collection.
	 *
	 * @since 5.8.0
	 * @since 6.2.0 Added 'per_page', 'page', 'offset', 'order', and 'orderby' to request.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['per_page']['default'] = 100;
		$query_params['search']['minLength'] = 1;
		$query_params['context']['default']  = 'view';

		$query_params['category'] = array(
			'description' => __( '将结果限制为与分类ID匹配的结果。' ),
			'type'        => 'integer',
			'minimum'     => 1,
		);

		$query_params['keyword'] = array(
			'description' => __( '将结果限制为与关键字ID匹配的结果。' ),
			'type'        => 'integer',
			'minimum'     => 1,
		);

		$query_params['slug'] = array(
			'description' => __( '将结果限制为与样板（别名）匹配的结果。' ),
			'type'        => 'array',
		);

		$query_params['offset'] = array(
			'description' => __( '将结果集移位特定数量。' ),
			'type'        => 'integer',
		);

		$query_params['order'] = array(
			'description' => __( '设置排序字段升序或降序。' ),
			'type'        => 'string',
			'default'     => 'desc',
			'enum'        => array( 'asc', 'desc' ),
		);

		$query_params['orderby'] = array(
			'description' => __( '按文章属性对集合进行排序。' ),
			'type'        => 'string',
			'default'     => 'date',
			'enum'        => array(
				'author',
				'date',
				'id',
				'include',
				'modified',
				'parent',
				'relevance',
				'slug',
				'include_slugs',
				'title',
				'favorite_count',
			),
		);

		/**
		 * Filter collection parameters for the block pattern directory controller.
		 *
		 * @since 5.8.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_pattern_directory_collection_params', $query_params );
	}

	/*
	 * Include a hash of the query args, so that different requests are stored in
	 * separate caches.
	 *
	 * MD5 is chosen for its speed, low-collision rate, universal availability, and to stay
	 * under the character limit for `_site_transient_timeout_{...}` keys.
	 *
	 * @link https://stackoverflow.com/questions/3665247/fastest-hash-for-non-cryptographic-uses
	 *
	 * @since 6.0.0
	 *
	 * @param array $query_args Query arguments to generate a transient key from.
	 * @return string Transient key.
	 */
	protected function get_transient_key( $query_args ) {

		if ( isset( $query_args['slug'] ) ) {
			// This is an additional precaution because the "sort" function expects an array.
			$query_args['slug'] = gc_parse_list( $query_args['slug'] );

			// Empty arrays should not affect the transient key.
			if ( empty( $query_args['slug'] ) ) {
				unset( $query_args['slug'] );
			} else {
				// Sort the array so that the transient key doesn't depend on the order of slugs.
				sort( $query_args['slug'] );
			}
		}

		return 'gc_remote_block_patterns_' . md5( serialize( $query_args ) );
	}
}
