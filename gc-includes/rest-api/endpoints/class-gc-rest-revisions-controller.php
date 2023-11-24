<?php
/**
 * REST API: GC_REST_Revisions_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 */

/**
 * Core class used to access revisions via the REST API.
 *
 * @see GC_REST_Controller
 */
class GC_REST_Revisions_Controller extends GC_REST_Controller {

	/**
	 * Parent post type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	private $parent_post_type;

	/**
	 * Parent controller.
	 *
	 * @since 4.7.0
	 * @var GC_REST_Controller
	 */
	private $parent_controller;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	private $parent_base;

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct( $parent_post_type ) {
		$this->parent_post_type = $parent_post_type;
		$post_type_object       = get_post_type_object( $parent_post_type );
		$parent_controller      = $post_type_object->get_rest_controller();

		if ( ! $parent_controller ) {
			$parent_controller = new GC_REST_Posts_Controller( $parent_post_type );
		}

		$this->parent_controller = $parent_controller;
		$this->rest_base         = 'revisions';
		$this->parent_base       = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
		$this->namespace         = ! empty( $post_type_object->rest_namespace ) ? $post_type_object->rest_namespace : 'gc/v2';
	}

	/**
	 * Registers the routes for revisions based on post types supporting revisions.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_route()
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
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( '修订版的父级 ID。' ),
						'type'        => 'integer',
					),
					'id'     => array(
						'description' => __( '修订版的唯一标识符。' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => GC_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( '要求为true，因为修订版本不能被移动到回收站。' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get the parent post, if the ID is valid.
	 *
	 * @since 4.7.2
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

	/**
	 * Checks if a given request has access to get revisions.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$parent = $this->get_parent( $request['parent'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		if ( ! current_user_can( 'edit_post', $parent->ID ) ) {
			return new GC_Error(
				'rest_cannot_read',
				__( '抱歉，您不能查阅此文章的修订版本。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Get the revision, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return GC_Post|GC_Error Revision post object if ID is valid, GC_Error otherwise.
	 */
	protected function get_revision( $id ) {
		$error = new GC_Error(
			'rest_post_invalid_id',
			__( '无效的修订版本ID。' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$revision = get_post( (int) $id );
		if ( empty( $revision ) || empty( $revision->ID ) || 'revision' !== $revision->post_type ) {
			return $error;
		}

		return $revision;
	}

	/**
	 * Gets a collection of revisions.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request['parent'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		// Ensure a search string is set in case the orderby is set to 'relevance'.
		if ( ! empty( $request['orderby'] ) && 'relevance' === $request['orderby'] && empty( $request['search'] ) ) {
			return new GC_Error(
				'rest_no_search_term_defined',
				__( '您需要定义搜索条件来按相关性排序。' ),
				array( 'status' => 400 )
			);
		}

		// Ensure an include parameter is set in case the orderby is set to 'include'.
		if ( ! empty( $request['orderby'] ) && 'include' === $request['orderby'] && empty( $request['include'] ) ) {
			return new GC_Error(
				'rest_orderby_include_missing_include',
				__( '您需要定义“include”参数来按包含顺序排序。' ),
				array( 'status' => 400 )
			);
		}

		if ( gc_revisions_enabled( $parent ) ) {
			$registered = $this->get_collection_params();
			$args       = array(
				'post_parent'      => $parent->ID,
				'post_type'        => 'revision',
				'post_status'      => 'inherit',
				'posts_per_page'   => -1,
				'orderby'          => 'date ID',
				'order'            => 'DESC',
				'suppress_filters' => true,
			);

			$parameter_mappings = array(
				'exclude'  => 'post__not_in',
				'include'  => 'post__in',
				'offset'   => 'offset',
				'order'    => 'order',
				'orderby'  => 'orderby',
				'page'     => 'paged',
				'per_page' => 'posts_per_page',
				'search'   => 's',
			);

			foreach ( $parameter_mappings as $api_param => $gc_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$args[ $gc_param ] = $request[ $api_param ];
				}
			}

			// For backward-compatibility, 'date' needs to resolve to 'date ID'.
			if ( isset( $args['orderby'] ) && 'date' === $args['orderby'] ) {
				$args['orderby'] = 'date ID';
			}

			/** This filter is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
			$args       = apply_filters( 'rest_revision_query', $args, $request );
			$query_args = $this->prepare_items_query( $args, $request );

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
	 * Checks if a given request has access to get a specific revision.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Retrieves one revision from the collection.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$parent = $this->get_parent( $request['parent'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		$revision = $this->get_revision( $request['id'] );
		if ( is_gc_error( $revision ) ) {
			return $revision;
		}

		$response = $this->prepare_item_for_response( $revision, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to delete a revision.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$parent = $this->get_parent( $request['parent'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		if ( ! current_user_can( 'delete_post', $parent->ID ) ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '抱歉，您不能删除此文章的修订版本。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$revision = $this->get_revision( $request['id'] );
		if ( is_gc_error( $revision ) ) {
			return $revision;
		}

		$response = $this->get_items_permissions_check( $request );
		if ( ! $response || is_gc_error( $response ) ) {
			return $response;
		}

		if ( ! current_user_can( 'delete_post', $revision->ID ) ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '抱歉，您不能删除此修订版本。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single revision.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		$revision = $this->get_revision( $request['id'] );
		if ( is_gc_error( $revision ) ) {
			return $revision;
		}

		$force = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for revisions.
		if ( ! $force ) {
			return new GC_Error(
				'rest_trash_not_supported',
				/* translators: %s: force=true */
				sprintf( __( "修订版本不能被移动到回收站，设置“%s”来删除。" ), 'force=true' ),
				array( 'status' => 501 )
			);
		}

		$previous = $this->prepare_item_for_response( $revision, $request );

		$result = gc_delete_post( $request['id'], true );

		/**
		 * Fires after a revision is deleted via the REST API.
		 *
		 * @since 4.7.0
		 *
		 * @param GC_Post|false|null $result The revision object (if it was deleted or moved to the Trash successfully)
		 *                                   or false or null (failure). If the revision was moved to the Trash, $result represents
		 *                                   its new state; if it was deleted, $result represents its state before deletion.
		 * @param GC_REST_Request $request The request sent to the API.
		 */
		do_action( 'rest_delete_revision', $result, $request );

		if ( ! $result ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '不能删除这篇文章。' ),
				array( 'status' => 500 )
			);
		}

		$response = new GC_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);
		return $response;
	}

	/**
	 * Determines the allowed query_vars for a get_items() response and prepares
	 * them for GC_Query.
	 *
	 * @since 5.0.0
	 *
	 * @param array           $prepared_args Optional. Prepared GC_Query arguments. Default empty array.
	 * @param GC_REST_Request $request       Optional. Full details about the request.
	 * @return array Items query arguments.
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		$query_args = array();

		foreach ( $prepared_args as $key => $value ) {
			/** This filter is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
			$query_args[ $key ] = apply_filters( "rest_query_var-{$key}", $value ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
		}

		// Map to proper GC_Query orderby param.
		if ( isset( $query_args['orderby'] ) && isset( $request['orderby'] ) ) {
			$orderby_mappings = array(
				'id'            => 'ID',
				'include'       => 'post__in',
				'slug'          => 'post_name',
				'include_slugs' => 'post_name__in',
			);

			if ( isset( $orderby_mappings[ $request['orderby'] ] ) ) {
				$query_args['orderby'] = $orderby_mappings[ $request['orderby'] ];
			}
		}

		return $query_args;
	}

	/**
	 * Prepares the revision for the REST response.
	 *
	 * @since 4.7.0
	 * @since 5.9.0 Renamed `$post` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param GC_Post         $item    Post revision object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post            = $item;
		$GLOBALS['post'] = $post;

		setup_postdata( $post );

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'author', $fields, true ) ) {
			$data['author'] = (int) $post->post_author;
		}

		if ( in_array( 'date', $fields, true ) ) {
			$data['date'] = $this->prepare_date_response( $post->post_date_gmt, $post->post_date );
		}

		if ( in_array( 'date_gmt', $fields, true ) ) {
			$data['date_gmt'] = $this->prepare_date_response( $post->post_date_gmt );
		}

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = $post->ID;
		}

		if ( in_array( 'modified', $fields, true ) ) {
			$data['modified'] = $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified );
		}

		if ( in_array( 'modified_gmt', $fields, true ) ) {
			$data['modified_gmt'] = $this->prepare_date_response( $post->post_modified_gmt );
		}

		if ( in_array( 'parent', $fields, true ) ) {
			$data['parent'] = (int) $post->post_parent;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $post->post_name;
		}

		if ( in_array( 'guid', $fields, true ) ) {
			$data['guid'] = array(
				/** This filter is documented in gc-includes/post-template.php */
				'rendered' => apply_filters( 'get_the_guid', $post->guid, $post->ID ),
				'raw'      => $post->guid,
			);
		}

		if ( in_array( 'title', $fields, true ) ) {
			$data['title'] = array(
				'raw'      => $post->post_title,
				'rendered' => get_the_title( $post->ID ),
			);
		}

		if ( in_array( 'content', $fields, true ) ) {

			$data['content'] = array(
				'raw'      => $post->post_content,
				/** This filter is documented in gc-includes/post-template.php */
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		if ( in_array( 'excerpt', $fields, true ) ) {
			$data['excerpt'] = array(
				'raw'      => $post->post_excerpt,
				'rendered' => $this->prepare_excerpt_response( $post->post_excerpt, $post ),
			);
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );

		if ( ! empty( $data['parent'] ) ) {
			$response->add_link( 'parent', rest_url( rest_get_route_for_post( $data['parent'] ) ) );
		}

		/**
		 * Filters a revision returned from the REST API.
		 *
		 * Allows modification of the revision right before it is returned.
		 *
		 * @since 4.7.0
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_Post          $post     The original revision object.
		 * @param GC_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_revision', $response, $post, $request );
	}

	/**
	 * Checks the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * @since 4.7.0
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
	 * Retrieves the revision's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
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
			// Base properties for every Revision.
			'properties' => array(
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
				'guid'         => array(
					'description' => __( '修订版本的 GUID，存放于数据库中。' ),
					'type'        => 'string',
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
				'slug'         => array(
					'description' => __( '修订版本的字母数字标识符，其类型是唯一的。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		$parent_schema = $this->parent_controller->get_item_schema();

		if ( ! empty( $parent_schema['properties']['title'] ) ) {
			$schema['properties']['title'] = $parent_schema['properties']['title'];
		}

		if ( ! empty( $parent_schema['properties']['content'] ) ) {
			$schema['properties']['content'] = $parent_schema['properties']['content'];
		}

		if ( ! empty( $parent_schema['properties']['excerpt'] ) ) {
			$schema['properties']['excerpt'] = $parent_schema['properties']['excerpt'];
		}

		if ( ! empty( $parent_schema['properties']['guid'] ) ) {
			$schema['properties']['guid'] = $parent_schema['properties']['guid'];
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 4.7.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		unset( $query_params['per_page']['default'] );

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
			'description' => __( '按对象属性排序集合。' ),
			'type'        => 'string',
			'default'     => 'date',
			'enum'        => array(
				'date',
				'id',
				'include',
				'relevance',
				'slug',
				'include_slugs',
				'title',
			),
		);

		return $query_params;
	}

	/**
	 * Checks the post excerpt and prepare it for single post output.
	 *
	 * @since 4.7.0
	 *
	 * @param string  $excerpt The post excerpt.
	 * @param GC_Post $post    Post revision object.
	 * @return string Prepared excerpt or empty string.
	 */
	protected function prepare_excerpt_response( $excerpt, $post ) {

		/** This filter is documented in gc-includes/post-template.php */
		$excerpt = apply_filters( 'the_excerpt', $excerpt, $post );

		if ( empty( $excerpt ) ) {
			return '';
		}

		return $excerpt;
	}
}
