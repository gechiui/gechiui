<?php
/**
 * REST API: GC_REST_Global_Styles_Revisions_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 6.3.0
 */

/**
 * Core class used to access global styles revisions via the REST API.
 *
 * @since 6.3.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Global_Styles_Revisions_Controller extends GC_REST_Controller {
	/**
	 * Parent post type.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $parent_post_type;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $parent_base;

	/**
	 * Constructor.
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		$this->parent_post_type = 'gc_global_styles';
		$this->rest_base        = 'revisions';
		$this->parent_base      = 'global-styles';
		$this->namespace        = 'gc/v2';
	}

	/**
	 * Registers the controller's routes.
	 *
	 * @since 6.3.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base,
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( '修订版的父级 ID。' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * Inherits from GC_REST_Controller::get_collection_params(),
	 * also reflects changes to return value GC_REST_Revisions_Controller::get_collection_params().
	 *
	 * @since 6.3.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$collection_params                       = parent::get_collection_params();
		$collection_params['context']['default'] = 'view';
		$collection_params['offset']             = array(
			'description' => __( '将结果集移位特定数量。' ),
			'type'        => 'integer',
		);
		unset( $collection_params['search'] );
		unset( $collection_params['per_page']['default'] );

		return $collection_params;
	}

	/**
	 * Returns decoded JSON from post content string,
	 * or a 404 if not found.
	 *
	 * @since 6.3.0
	 *
	 * @param string $raw_json Encoded JSON from global styles custom post content.
	 * @return Array|GC_Error
	 */
	protected function get_decoded_global_styles_json( $raw_json ) {
		$decoded_json = json_decode( $raw_json, true );

		if ( is_array( $decoded_json ) && isset( $decoded_json['isGlobalStylesUserThemeJSON'] ) && true === $decoded_json['isGlobalStylesUserThemeJSON'] ) {
			return $decoded_json;
		}

		return new GC_Error(
			'rest_global_styles_not_found',
			__( '找不到用户全局样式修订。' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Returns paginated revisions of the given global styles config custom post type.
	 *
	 * The bulk of the body is taken from GC_REST_Revisions_Controller->get_items,
	 * but global styles does not require as many parameters.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_REST_Request $request The request instance.
	 * @return GC_REST_Response|GC_Error
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request['parent'] );

		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		$global_styles_config = $this->get_decoded_global_styles_json( $parent->post_content );

		if ( is_gc_error( $global_styles_config ) ) {
			return $global_styles_config;
		}

		if ( gc_revisions_enabled( $parent ) ) {
			$registered = $this->get_collection_params();
			$query_args = array(
				'post_parent'    => $parent->ID,
				'post_type'      => 'revision',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
				'orderby'        => 'date ID',
				'order'          => 'DESC',
			);

			$parameter_mappings = array(
				'offset'   => 'offset',
				'page'     => 'paged',
				'per_page' => 'posts_per_page',
			);

			foreach ( $parameter_mappings as $api_param => $gc_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$query_args[ $gc_param ] = $request[ $api_param ];
				}
			}

			$revisions_query = new GC_Query();
			$revisions       = $revisions_query->query( $query_args );
			$offset          = isset( $query_args['offset'] ) ? (int) $query_args['offset'] : 0;
			$page            = (int) $query_args['paged'];
			$total_revisions = $revisions_query->found_posts;

			if ( $total_revisions < 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count.
				unset( $query_args['paged'], $query_args['offset'] );
				$count_query = new GC_Query();
				$count_query->query( $query_args );

				$total_revisions = $count_query->found_posts;
			}

			if ( $revisions_query->query_vars['posts_per_page'] > 0 ) {
				$max_pages = ceil( $total_revisions / (int) $revisions_query->query_vars['posts_per_page'] );
			} else {
				$max_pages = $total_revisions > 0 ? 1 : 0;
			}
			if ( $total_revisions > 0 ) {
				if ( $offset >= $total_revisions ) {
					return new GC_Error(
						'rest_revision_invalid_offset_number',
						__( '请求的偏移量大于或等于可用修订版本的数量。' ),
						array( 'status' => 400 )
					);
				} elseif ( ! $offset && $page > $max_pages ) {
					return new GC_Error(
						'rest_revision_invalid_page_number',
						__( '请求的页码大于总页数。' ),
						array( 'status' => 400 )
					);
				}
			}
		} else {
			$revisions       = array();
			$total_revisions = 0;
			$max_pages       = 0;
			$page            = (int) $request['page'];
		}

		$response = array();

		foreach ( $revisions as $revision ) {
			$data       = $this->prepare_item_for_response( $revision, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		$response->header( 'X-GC-Total', (int) $total_revisions );
		$response->header( 'X-GC-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base_path      = rest_url( sprintf( '%s/%s/%d/%s', $this->namespace, $this->parent_base, $request['parent'], $this->rest_base ) );
		$base           = add_query_arg( urlencode_deep( $request_params ), $base_path );

		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Checks the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * Duplicate of GC_REST_Revisions_Controller::prepare_date_response.
	 *
	 * @since 6.3.0
	 *
	 * @param string      $date_gmt GMT publication time.
	 * @param string|null $date     Optional. Local publication time. Default null.
	 * @return string|null ISO8601/RFC3339 formatted datetime, otherwise null.
	 */
	protected function prepare_date_response( $date_gmt, $date = null ) {
		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		return mysql_to_rfc3339( $date_gmt );
	}

	/**
	 * Prepares the revision for the REST response.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_Post         $post    Post revision object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$parent               = $this->get_parent( $request['parent'] );
		$global_styles_config = $this->get_decoded_global_styles_json( $post->post_content );

		if ( is_gc_error( $global_styles_config ) ) {
			return $global_styles_config;
		}

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( ! empty( $global_styles_config['styles'] ) || ! empty( $global_styles_config['settings'] ) ) {
			$global_styles_config = ( new GC_Theme_JSON( $global_styles_config, 'custom' ) )->get_raw_data();
			if ( rest_is_field_included( 'settings', $fields ) ) {
				$data['settings'] = ! empty( $global_styles_config['settings'] ) ? $global_styles_config['settings'] : new stdClass();
			}
			if ( rest_is_field_included( 'styles', $fields ) ) {
				$data['styles'] = ! empty( $global_styles_config['styles'] ) ? $global_styles_config['styles'] : new stdClass();
			}
		}

		if ( rest_is_field_included( 'author', $fields ) ) {
			$data['author'] = (int) $post->post_author;
		}

		if ( rest_is_field_included( 'date', $fields ) ) {
			$data['date'] = $this->prepare_date_response( $post->post_date_gmt, $post->post_date );
		}

		if ( rest_is_field_included( 'date_gmt', $fields ) ) {
			$data['date_gmt'] = $this->prepare_date_response( $post->post_date_gmt );
		}

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = (int) $post->ID;
		}

		if ( rest_is_field_included( 'modified', $fields ) ) {
			$data['modified'] = $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified );
		}

		if ( rest_is_field_included( 'modified_gmt', $fields ) ) {
			$data['modified_gmt'] = $this->prepare_date_response( $post->post_modified_gmt );
		}

		if ( rest_is_field_included( 'parent', $fields ) ) {
			$data['parent'] = (int) $parent->ID;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the revision's schema, conforming to JSON Schema.
	 *
	 * @since 6.3.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => "{$this->parent_post_type}-revision",
			'type'       => 'object',
			// Base properties for every revision.
			'properties' => array(

				/*
				 * Adds settings and styles from the GC_REST_Revisions_Controller item fields.
				 * Leaves out GUID as global styles shouldn't be accessible via URL.
				 */
				'author'       => array(
					'description' => __( '修订版本的作者 ID。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date'         => array(
					'description' => __( "修订版本发布的日期“系统时区）。" ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'     => array(
					'description' => __( '修订版本发布的 GMT 日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'id'           => array(
					'description' => __( '修订版的唯一标识符。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'modified'     => array(
					'description' => __( "修订版本的最后修改日期（系统时区）。" ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'modified_gmt' => array(
					'description' => __( '修订版本最后修改的 GMT 日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'parent'       => array(
					'description' => __( '修订版的父级 ID。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				// Adds settings and styles from the GC_REST_Global_Styles_Controller parent schema.
				'styles'       => array(
					'description' => __( '全局样式。' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'settings'     => array(
					'description' => __( '全局设置。' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Checks if a given request has access to read a single global style.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$post = $this->get_parent( $request['parent'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		/*
		 * The same check as GC_REST_Global_Styles_Controller::get_item_permissions_check.
		 */
		if ( ! current_user_can( 'read_post', $post->ID ) ) {
			return new GC_Error(
				'rest_cannot_view',
				__( '很抱歉，不允许您查看此全局样式的修订。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Gets the parent post, if the ID is valid.
	 *
	 * Duplicate of GC_REST_Revisions_Controller::get_parent.
	 *
	 * @since 6.3.0
	 *
	 * @param int $parent_post_id Supplied ID.
	 * @return GC_Post|GC_Error Post object if ID is valid, GC_Error otherwise.
	 */
	protected function get_parent( $parent_post_id ) {
		$error = new GC_Error(
			'rest_post_invalid_parent',
			__( '文章父 ID 无效。' ),
			array( 'status' => 404 )
		);

		if ( (int) $parent_post_id <= 0 ) {
			return $error;
		}

		$parent_post = get_post( (int) $parent_post_id );

		if ( empty( $parent_post ) || empty( $parent_post->ID )
			|| $this->parent_post_type !== $parent_post->post_type
		) {
			return $error;
		}

		return $parent_post;
	}
}
