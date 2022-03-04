<?php
/**
 * Block Pattern Directory REST API: GC_REST_Pattern_Directory_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Controller which provides REST endpoint for block patterns.
 *
 * This simply proxies the endpoint at http://api.gechiui.com/patterns/1.0/. That isn't necessary for
 * functionality, but is desired for privacy. It prevents api.gechiui.com from knowing the user's IP address.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Pattern_Directory_Controller extends GC_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 */
	public function __construct() {
		$this->namespace     = 'gc/v2';
			$this->rest_base = 'pattern-directory';
	}

	/**
	 * Registers the necessary REST API routes.
	 *
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

		$query_args = array(
			'locale'     => get_user_locale(),
			'gc-version' => $gc_version,
		);

		$category_id = $request['category'];
		$keyword_id  = $request['keyword'];
		$search_term = $request['search'];

		if ( $category_id ) {
			$query_args['pattern-categories'] = $category_id;
		}

		if ( $keyword_id ) {
			$query_args['pattern-keywords'] = $keyword_id;
		}

		if ( $search_term ) {
			$query_args['search'] = $search_term;
		}

		/*
		 * Include a hash of the query args, so that different requests are stored in
		 * separate caches.
		 *
		 * MD5 is chosen for its speed, low-collision rate, universal availability, and to stay
		 * under the character limit for `_site_transient_timeout_{...}` keys.
		 *
		 * @link https://stackoverflow.com/questions/3665247/fastest-hash-for-non-cryptographic-uses
		 */
		$transient_key = 'gc_remote_block_patterns_' . md5( implode( '-', $query_args ) );

		/*
		 * Use network-wide transient to improve performance. The locale is the only site
		 * configuration that affects the response, and it's included in the transient key.
		 */
		$raw_patterns = get_site_transient( $transient_key );

		if ( ! $raw_patterns ) {
			$api_url = add_query_arg(
				array_map( 'rawurlencode', $query_args ),
				'http://api.gechiui.com/patterns/1.0/'
			);

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
			'keywords'       => array_map( 'sanitize_title', $raw_pattern->keyword_slugs ),
			'description'    => sanitize_text_field( $raw_pattern->meta->gcop_description ),
			'viewport_width' => absint( $raw_pattern->meta->gcop_viewport_width ),
		);

		$prepared_pattern = $this->add_additional_fields_to_object( $prepared_pattern, $request );

		$response = new GC_REST_Response( $prepared_pattern );

		/**
		 * Filters the REST API response for a block pattern.
		 *
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
					'context'     => array( 'view', 'embed' ),
				),

				'title'          => array(
					'description' => __( '区块样板标题，人类可读的格式。' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'content'        => array(
					'description' => __( '区块样板的内容。' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'categories'     => array(
					'description' => __( "区块样板的分类别名。" ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),

				'keywords'       => array(
					'description' => __( "区块样板的关键字别名。" ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),

				'description'    => array(
					'description' => __( '区块样板的描述；' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'viewport_width' => array(
					'description' => __( '预览区块样板时视口的首选宽度（以像素为单位）。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search parameters for the block pattern's collection.
	 *
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		// Pagination is not supported.
		unset( $query_params['page'] );
		unset( $query_params['per_page'] );

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

		/**
		 * Filter collection parameters for the block pattern directory controller.
		 *
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_pattern_directory_collection_params', $query_params );
	}
}
