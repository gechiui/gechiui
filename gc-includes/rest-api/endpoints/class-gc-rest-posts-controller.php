<?php
/**
 * REST API: GC_REST_Posts_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 */

/**
 * Core class to access posts via the REST API.
 *
 * @see GC_REST_Controller
 */
class GC_REST_Posts_Controller extends GC_REST_Controller {
	/**
	 * Post type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	protected $post_type;

	/**
	 * Instance of a post meta fields object.
	 *
	 * @since 4.7.0
	 * @var GC_REST_Post_Meta_Fields
	 */
	protected $meta;

	/**
	 * Passwordless post access permitted.
	 *
	 * @since 5.7.1
	 * @var int[]
	 */
	protected $password_check_passed = array();

	/**
	 * Whether the controller supports batching.
	 *
	 * @since 5.9.0
	 * @var array
	 */
	protected $allow_batch = array( 'v1' => true );

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
		$obj             = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;
		$this->namespace = ! empty( $obj->rest_namespace ) ? $obj->rest_namespace : 'gc/v2';

		$this->meta = new GC_REST_Post_Meta_Fields( $this->post_type );
	}

	/**
	 * Registers the routes for posts.
	 *
	 * @since 4.7.0
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
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => GC_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::CREATABLE ),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);

		$schema        = $this->get_item_schema();
		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
		if ( isset( $schema['properties']['password'] ) ) {
			$get_item_args['password'] = array(
				'description' => __( '该文章的密码，如文章受密码保护。' ),
				'type'        => 'string',
			);
		}
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'        => array(
					'id' => array(
						'description' => __( '文章的唯一标识符。' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $get_item_args,
				),
				array(
					'methods'             => GC_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => GC_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( '是否绕过回收站并强行删除。' ),
						),
					),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read posts.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {

		$post_type = get_post_type_object( $this->post_type );

		if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能在此文章类型中编辑文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Overrides the result of the post password check for REST requested posts.
	 *
	 * Allow users to read the content of password protected posts if they have
	 * previously passed a permission check or if they have the `edit_post` capability
	 * for the post being checked.
	 *
	 * @since 5.7.1
	 *
	 * @param bool    $required Whether the post requires a password check.
	 * @param GC_Post $post     The post been password checked.
	 * @return bool Result of password check taking in to account REST API considerations.
	 */
	public function check_password_required( $required, $post ) {
		if ( ! $required ) {
			return $required;
		}

		$post = get_post( $post );

		if ( ! $post ) {
			return $required;
		}

		if ( ! empty( $this->password_check_passed[ $post->ID ] ) ) {
			// Password previously checked and approved.
			return false;
		}

		return ! current_user_can( 'edit_post', $post->ID );
	}

	/**
	 * Retrieves a collection of posts.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {

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

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$args       = array();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal GC_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array(
			'author'         => 'author__in',
			'author_exclude' => 'author__not_in',
			'exclude'        => 'post__not_in',
			'include'        => 'post__in',
			'menu_order'     => 'menu_order',
			'offset'         => 'offset',
			'order'          => 'order',
			'orderby'        => 'orderby',
			'page'           => 'paged',
			'parent'         => 'post_parent__in',
			'parent_exclude' => 'post_parent__not_in',
			'search'         => 's',
			'search_columns' => 'search_columns',
			'slug'           => 'post_name__in',
			'status'         => 'post_status',
		);

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 */
		foreach ( $parameter_mappings as $api_param => $gc_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$args[ $gc_param ] = $request[ $api_param ];
			}
		}

		// Check for & assign any parameters which require special handling or setting.
		$args['date_query'] = array();

		if ( isset( $registered['before'], $request['before'] ) ) {
			$args['date_query'][] = array(
				'before' => $request['before'],
				'column' => 'post_date',
			);
		}

		if ( isset( $registered['modified_before'], $request['modified_before'] ) ) {
			$args['date_query'][] = array(
				'before' => $request['modified_before'],
				'column' => 'post_modified',
			);
		}

		if ( isset( $registered['after'], $request['after'] ) ) {
			$args['date_query'][] = array(
				'after'  => $request['after'],
				'column' => 'post_date',
			);
		}

		if ( isset( $registered['modified_after'], $request['modified_after'] ) ) {
			$args['date_query'][] = array(
				'after'  => $request['modified_after'],
				'column' => 'post_modified',
			);
		}

		// Ensure our per_page parameter overrides any provided posts_per_page filter.
		if ( isset( $registered['per_page'] ) ) {
			$args['posts_per_page'] = $request['per_page'];
		}

		if ( isset( $registered['sticky'], $request['sticky'] ) ) {
			$sticky_posts = get_option( 'sticky_posts', array() );
			if ( ! is_array( $sticky_posts ) ) {
				$sticky_posts = array();
			}
			if ( $request['sticky'] ) {
				/*
				 * As post__in will be used to only get sticky posts,
				 * we have to support the case where post__in was already
				 * specified.
				 */
				$args['post__in'] = $args['post__in'] ? array_intersect( $sticky_posts, $args['post__in'] ) : $sticky_posts;

				/*
				 * If we intersected, but there are no post IDs in common,
				 * GC_Query won't return "no posts" for post__in = array()
				 * so we have to fake it a bit.
				 */
				if ( ! $args['post__in'] ) {
					$args['post__in'] = array( 0 );
				}
			} elseif ( $sticky_posts ) {
				/*
				 * As post___not_in will be used to only get posts that
				 * are not sticky, we have to support the case where post__not_in
				 * was already specified.
				 */
				$args['post__not_in'] = array_merge( $args['post__not_in'], $sticky_posts );
			}
		}

		$args = $this->prepare_tax_query( $args, $request );

		// Force the post_type argument, since it's not a user input variable.
		$args['post_type'] = $this->post_type;

		/**
		 * Filters GC_Query arguments when querying posts via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_post_query`
		 *  - `rest_page_query`
		 *  - `rest_attachment_query`
		 *
		 * Enables adding extra arguments or setting defaults for a post collection request.
		 *
		 * @since 4.7.0
		 * @since 5.7.0 Moved after the `tax_query` query arg is generated.
		 *
		 * @link https://developer.gechiui.com/reference/classes/gc_query/
		 *
		 * @param array           $args    Array of arguments for GC_Query.
		 * @param GC_REST_Request $request The REST API request.
		 */
		$args       = apply_filters( "rest_{$this->post_type}_query", $args, $request );
		$query_args = $this->prepare_items_query( $args, $request );

		$posts_query  = new GC_Query();
		$query_result = $posts_query->query( $query_args );

		// Allow access to all password protected posts if the context is edit.
		if ( 'edit' === $request['context'] ) {
			add_filter( 'post_password_required', array( $this, 'check_password_required' ), 10, 2 );
		}

		$posts = array();

		update_post_author_caches( $query_result );
		update_post_parent_caches( $query_result );

		if ( post_type_supports( $this->post_type, 'thumbnail' ) ) {
			update_post_thumbnail_cache( $posts_query );
		}

		foreach ( $query_result as $post ) {
			if ( ! $this->check_read_permission( $post ) ) {
				continue;
			}

			$data    = $this->prepare_item_for_response( $post, $request );
			$posts[] = $this->prepare_response_for_collection( $data );
		}

		// Reset filter.
		if ( 'edit' === $request['context'] ) {
			remove_filter( 'post_password_required', array( $this, 'check_password_required' ) );
		}

		$page        = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 && $page > 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );

			$count_query = new GC_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$max_pages = ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );

		if ( $page > $max_pages && $total_posts > 0 ) {
			return new GC_Error(
				'rest_post_invalid_page_number',
				__( '请求的页码大于总页数。' ),
				array( 'status' => 400 )
			);
		}

		$response = rest_ensure_response( $posts );

		$response->header( 'X-GC-Total', (int) $total_posts );
		$response->header( 'X-GC-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$collection_url = rest_url( rest_get_route_for_post_type_items( $this->post_type ) );
		$base           = add_query_arg( urlencode_deep( $request_params ), $collection_url );

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
	 * Gets the post, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return GC_Post|GC_Error Post object if ID is valid, GC_Error otherwise.
	 */
	protected function get_post( $id ) {
		$error = new GC_Error(
			'rest_post_invalid_id',
			__( '文章ID无效。' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$post = get_post( (int) $id );
		if ( empty( $post ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
			return $error;
		}

		return $post;
	}

	/**
	 * Checks if a given request has access to read a post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能修改这篇文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( $post && ! empty( $request['password'] ) ) {
			// Check post password, and return error if invalid.
			if ( ! hash_equals( $post->post_password, $request['password'] ) ) {
				return new GC_Error(
					'rest_post_incorrect_password',
					__( '错误的文章密码。' ),
					array( 'status' => 403 )
				);
			}
		}

		// Allow access to all password protected posts if the context is edit.
		if ( 'edit' === $request['context'] ) {
			add_filter( 'post_password_required', array( $this, 'check_password_required' ), 10, 2 );
		}

		if ( $post ) {
			return $this->check_read_permission( $post );
		}

		return true;
	}

	/**
	 * Checks if the user can access password-protected content.
	 *
	 * This method determines whether we need to override the regular password
	 * check in core with a filter.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post         $post    Post to check against.
	 * @param GC_REST_Request $request Request data to check.
	 * @return bool True if the user can access password-protected content, otherwise false.
	 */
	public function can_access_password_content( $post, $request ) {
		if ( empty( $post->post_password ) ) {
			// No filter required.
			return false;
		}

		/*
		 * Users always gets access to password protected content in the edit
		 * context if they have the `edit_post` meta capability.
		 */
		if (
			'edit' === $request['context'] &&
			current_user_can( 'edit_post', $post->ID )
		) {
			return true;
		}

		// No password, no auth.
		if ( empty( $request['password'] ) ) {
			return false;
		}

		// Double-check the request password.
		return hash_equals( $post->post_password, $request['password'] );
	}

	/**
	 * Retrieves a single post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		$data     = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		if ( is_post_type_viewable( get_post_type_object( $post->post_type ) ) ) {
			$response->link_header( 'alternate', get_permalink( $post->ID ), array( 'type' => 'text/html' ) );
		}

		return $response;
	}

	/**
	 * Checks if a given request has access to create a post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new GC_Error(
				'rest_post_exists',
				__( '无法创建已存在的文章。' ),
				array( 'status' => 400 )
			);
		}

		$post_type = get_post_type_object( $this->post_type );

		if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new GC_Error(
				'rest_cannot_edit_others',
				__( '抱歉，您不能为此用户创建文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) && ! current_user_can( $post_type->cap->publish_posts ) ) {
			return new GC_Error(
				'rest_cannot_assign_sticky',
				__( '抱歉，您不能将文章置顶。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! current_user_can( $post_type->cap->create_posts ) ) {
			return new GC_Error(
				'rest_cannot_create',
				__( '抱歉，您不能为此用户创建文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->check_assign_terms_permission( $request ) ) {
			return new GC_Error(
				'rest_cannot_assign_term',
				__( '抱歉，您不能指定提供的项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new GC_Error(
				'rest_post_exists',
				__( '无法创建已存在的文章。' ),
				array( 'status' => 400 )
			);
		}

		$prepared_post = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $prepared_post ) ) {
			return $prepared_post;
		}

		$prepared_post->post_type = $this->post_type;

		if ( ! empty( $prepared_post->post_name )
			&& ! empty( $prepared_post->post_status )
			&& in_array( $prepared_post->post_status, array( 'draft', 'pending' ), true )
		) {
			/*
			 * `gc_unique_post_slug()` returns the same slug for 'draft' or 'pending' posts.
			 *
			 * To ensure that a unique slug is generated, pass the post data with the 'publish' status.
			 */
			$prepared_post->post_name = gc_unique_post_slug(
				$prepared_post->post_name,
				$prepared_post->id,
				'publish',
				$prepared_post->post_type,
				$prepared_post->post_parent
			);
		}

		$post_id = gc_insert_post( gc_slash( (array) $prepared_post ), true, false );

		if ( is_gc_error( $post_id ) ) {

			if ( 'db_insert_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}

			return $post_id;
		}

		$post = get_post( $post_id );

		/**
		 * Fires after a single post is created or updated via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_insert_post`
		 *  - `rest_insert_page`
		 *  - `rest_insert_attachment`
		 *
		 * @since 4.7.0
		 *
		 * @param GC_Post         $post     Inserted or updated post object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a post, false when updating.
		 */
		do_action( "rest_insert_{$this->post_type}", $post, $request, true );

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['sticky'] ) ) {
			if ( ! empty( $request['sticky'] ) ) {
				stick_post( $post_id );
			} else {
				unstick_post( $post_id );
			}
		}

		if ( ! empty( $schema['properties']['featured_media'] ) && isset( $request['featured_media'] ) ) {
			$this->handle_featured_media( $request['featured_media'], $post_id );
		}

		if ( ! empty( $schema['properties']['format'] ) && ! empty( $request['format'] ) ) {
			set_post_format( $post, $request['format'] );
		}

		if ( ! empty( $schema['properties']['template'] ) && isset( $request['template'] ) ) {
			$this->handle_template( $request['template'], $post_id, true );
		}

		$terms_update = $this->handle_terms( $post_id, $request );

		if ( is_gc_error( $terms_update ) ) {
			return $terms_update;
		}

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $post_id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$post          = get_post( $post_id );
		$fields_update = $this->update_additional_fields_for_object( $post, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/**
		 * Fires after a single post is completely created or updated via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_after_insert_post`
		 *  - `rest_after_insert_page`
		 *  - `rest_after_insert_attachment`
		 *
		 * @since 5.0.0
		 *
		 * @param GC_Post         $post     Inserted or updated post object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a post, false when updating.
		 */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, true );

		gc_after_insert_post( $post, false, null );

		$response = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( rest_get_route_for_post( $post ) ) );

		return $response;
	}

	/**
	 * Checks if a given request has access to update a post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		$post_type = get_post_type_object( $this->post_type );

		if ( $post && ! $this->check_update_permission( $post ) ) {
			return new GC_Error(
				'rest_cannot_edit',
				__( '抱歉，您不能修改这篇文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new GC_Error(
				'rest_cannot_edit_others',
				__( '抱歉，您不能作为此用户更新文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) && ! current_user_can( $post_type->cap->publish_posts ) ) {
			return new GC_Error(
				'rest_cannot_assign_sticky',
				__( '抱歉，您不能将文章置顶。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->check_assign_terms_permission( $request ) ) {
			return new GC_Error(
				'rest_cannot_assign_term',
				__( '抱歉，您不能指定提供的项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates a single post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$valid_check = $this->get_post( $request['id'] );
		if ( is_gc_error( $valid_check ) ) {
			return $valid_check;
		}

		$post_before = get_post( $request['id'] );
		$post        = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $post ) ) {
			return $post;
		}

		if ( ! empty( $post->post_status ) ) {
			$post_status = $post->post_status;
		} else {
			$post_status = $post_before->post_status;
		}

		/*
		 * `gc_unique_post_slug()` returns the same slug for 'draft' or 'pending' posts.
		 *
		 * To ensure that a unique slug is generated, pass the post data with the 'publish' status.
		 */
		if ( ! empty( $post->post_name ) && in_array( $post_status, array( 'draft', 'pending' ), true ) ) {
			$post_parent     = ! empty( $post->post_parent ) ? $post->post_parent : 0;
			$post->post_name = gc_unique_post_slug(
				$post->post_name,
				$post->ID,
				'publish',
				$post->post_type,
				$post_parent
			);
		}

		// Convert the post object to an array, otherwise gc_update_post() will expect non-escaped input.
		$post_id = gc_update_post( gc_slash( (array) $post ), true, false );

		if ( is_gc_error( $post_id ) ) {
			if ( 'db_update_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}
			return $post_id;
		}

		$post = get_post( $post_id );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
		do_action( "rest_insert_{$this->post_type}", $post, $request, false );

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['format'] ) && ! empty( $request['format'] ) ) {
			set_post_format( $post, $request['format'] );
		}

		if ( ! empty( $schema['properties']['featured_media'] ) && isset( $request['featured_media'] ) ) {
			$this->handle_featured_media( $request['featured_media'], $post_id );
		}

		if ( ! empty( $schema['properties']['sticky'] ) && isset( $request['sticky'] ) ) {
			if ( ! empty( $request['sticky'] ) ) {
				stick_post( $post_id );
			} else {
				unstick_post( $post_id );
			}
		}

		if ( ! empty( $schema['properties']['template'] ) && isset( $request['template'] ) ) {
			$this->handle_template( $request['template'], $post->ID );
		}

		$terms_update = $this->handle_terms( $post->ID, $request );

		if ( is_gc_error( $terms_update ) ) {
			return $terms_update;
		}

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $post->ID );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$post          = get_post( $post_id );
		$fields_update = $this->update_additional_fields_for_object( $post, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		// Filter is fired in GC_REST_Attachments_Controller subclass.
		if ( 'attachment' === $this->post_type ) {
			$response = $this->prepare_item_for_response( $post, $request );
			return rest_ensure_response( $response );
		}

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, false );

		gc_after_insert_post( $post, true, $post_before );

		$response = $this->prepare_item_for_response( $post, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to delete a post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		if ( $post && ! $this->check_delete_permission( $post ) ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '抱歉，您不能删除此文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single post.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		$id    = $post->ID;
		$force = (bool) $request['force'];

		$supports_trash = ( EMPTY_TRASH_DAYS > 0 );

		if ( 'attachment' === $post->post_type ) {
			$supports_trash = $supports_trash && MEDIA_TRASH;
		}

		/**
		 * Filters whether a post is trashable.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_post_trashable`
		 *  - `rest_page_trashable`
		 *  - `rest_attachment_trashable`
		 *
		 * Pass false to disable Trash support for the post.
		 *
		 * @since 4.7.0
		 *
		 * @param bool    $supports_trash Whether the post type support trashing.
		 * @param GC_Post $post           The Post object being considered for trashing support.
		 */
		$supports_trash = apply_filters( "rest_{$this->post_type}_trashable", $supports_trash, $post );

		if ( ! $this->check_delete_permission( $post ) ) {
			return new GC_Error(
				'rest_user_cannot_delete_post',
				__( '抱歉，您不能删除此文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$request->set_param( 'context', 'edit' );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			$previous = $this->prepare_item_for_response( $post, $request );
			$result   = gc_delete_post( $id, true );
			$response = new GC_REST_Response();
			$response->set_data(
				array(
					'deleted'  => true,
					'previous' => $previous->get_data(),
				)
			);
		} else {
			// If we don't support trashing for this type, error out.
			if ( ! $supports_trash ) {
				return new GC_Error(
					'rest_trash_not_supported',
					/* translators: %s: force=true */
					sprintf( __( "此文章不能被移动到回收站，设置“%s”来删除。" ), 'force=true' ),
					array( 'status' => 501 )
				);
			}

			// Otherwise, only trash if we haven't already.
			if ( 'trash' === $post->post_status ) {
				return new GC_Error(
					'rest_already_trashed',
					__( '文章已经被删除。' ),
					array( 'status' => 410 )
				);
			}

			/*
			 * (Note that internally this falls through to `gc_delete_post()`
			 * if the Trash is disabled.)
			 */
			$result   = gc_trash_post( $id );
			$post     = get_post( $id );
			$response = $this->prepare_item_for_response( $post, $request );
		}

		if ( ! $result ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '不能删除这篇文章。' ),
				array( 'status' => 500 )
			);
		}

		/**
		 * Fires immediately after a single post is deleted or trashed via the REST API.
		 *
		 * They dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_delete_post`
		 *  - `rest_delete_page`
		 *  - `rest_delete_attachment`
		 *
		 * @since 4.7.0
		 *
		 * @param GC_Post          $post     The deleted or trashed post.
		 * @param GC_REST_Response $response The response data.
		 * @param GC_REST_Request  $request  The request sent to the API.
		 */
		do_action( "rest_delete_{$this->post_type}", $post, $response, $request );

		return $response;
	}

	/**
	 * Determines the allowed query_vars for a get_items() response and prepares
	 * them for GC_Query.
	 *
	 * @since 4.7.0
	 *
	 * @param array           $prepared_args Optional. Prepared GC_Query arguments. Default empty array.
	 * @param GC_REST_Request $request       Optional. Full details about the request.
	 * @return array Items query arguments.
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		$query_args = array();

		foreach ( $prepared_args as $key => $value ) {
			/**
			 * Filters the query_vars used in get_items() for the constructed query.
			 *
			 * The dynamic portion of the hook name, `$key`, refers to the query_var key.
			 *
			 * @since 4.7.0
			 *
			 * @param string $value The query_var value.
			 */
			$query_args[ $key ] = apply_filters( "rest_query_var-{$key}", $value ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
		}

		if ( 'post' !== $this->post_type || ! isset( $query_args['ignore_sticky_posts'] ) ) {
			$query_args['ignore_sticky_posts'] = true;
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
	 * Checks the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * @since 4.7.0
	 *
	 * @param string      $date_gmt GMT publication time.
	 * @param string|null $date     Optional. Local publication time. Default null.
	 * @return string|null ISO8601/RFC3339 formatted datetime.
	 */
	protected function prepare_date_response( $date_gmt, $date = null ) {
		// Use the date if passed.
		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		// Return null if $date_gmt is empty/zeros.
		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		// Return the formatted datetime.
		return mysql_to_rfc3339( $date_gmt );
	}

	/**
	 * Prepares a single post for create or update.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return stdClass|GC_Error Post object or GC_Error.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_post  = new stdClass();
		$current_status = '';

		// Post ID.
		if ( isset( $request['id'] ) ) {
			$existing_post = $this->get_post( $request['id'] );
			if ( is_gc_error( $existing_post ) ) {
				return $existing_post;
			}

			$prepared_post->ID = $existing_post->ID;
			$current_status    = $existing_post->post_status;
		}

		$schema = $this->get_item_schema();

		// Post title.
		if ( ! empty( $schema['properties']['title'] ) && isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$prepared_post->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$prepared_post->post_title = $request['title']['raw'];
			}
		}

		// Post content.
		if ( ! empty( $schema['properties']['content'] ) && isset( $request['content'] ) ) {
			if ( is_string( $request['content'] ) ) {
				$prepared_post->post_content = $request['content'];
			} elseif ( isset( $request['content']['raw'] ) ) {
				$prepared_post->post_content = $request['content']['raw'];
			}
		}

		// Post excerpt.
		if ( ! empty( $schema['properties']['excerpt'] ) && isset( $request['excerpt'] ) ) {
			if ( is_string( $request['excerpt'] ) ) {
				$prepared_post->post_excerpt = $request['excerpt'];
			} elseif ( isset( $request['excerpt']['raw'] ) ) {
				$prepared_post->post_excerpt = $request['excerpt']['raw'];
			}
		}

		// Post type.
		if ( empty( $request['id'] ) ) {
			// Creating new post, use default type for the controller.
			$prepared_post->post_type = $this->post_type;
		} else {
			// Updating a post, use previous type.
			$prepared_post->post_type = get_post_type( $request['id'] );
		}

		$post_type = get_post_type_object( $prepared_post->post_type );

		// Post status.
		if (
			! empty( $schema['properties']['status'] ) &&
			isset( $request['status'] ) &&
			( ! $current_status || $current_status !== $request['status'] )
		) {
			$status = $this->handle_status_param( $request['status'], $post_type );

			if ( is_gc_error( $status ) ) {
				return $status;
			}

			$prepared_post->post_status = $status;
		}

		// Post date.
		if ( ! empty( $schema['properties']['date'] ) && ! empty( $request['date'] ) ) {
			$current_date = isset( $prepared_post->ID ) ? get_post( $prepared_post->ID )->post_date : false;
			$date_data    = rest_get_date_with_gmt( $request['date'] );

			if ( ! empty( $date_data ) && $current_date !== $date_data[0] ) {
				list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
				$prepared_post->edit_date                                        = true;
			}
		} elseif ( ! empty( $schema['properties']['date_gmt'] ) && ! empty( $request['date_gmt'] ) ) {
			$current_date = isset( $prepared_post->ID ) ? get_post( $prepared_post->ID )->post_date_gmt : false;
			$date_data    = rest_get_date_with_gmt( $request['date_gmt'], true );

			if ( ! empty( $date_data ) && $current_date !== $date_data[1] ) {
				list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
				$prepared_post->edit_date                                        = true;
			}
		}

		/*
		 * Sending a null date or date_gmt value resets date and date_gmt to their
		 * default values (`0000-00-00 00:00:00`).
		 */
		if (
			( ! empty( $schema['properties']['date_gmt'] ) && $request->has_param( 'date_gmt' ) && null === $request['date_gmt'] ) ||
			( ! empty( $schema['properties']['date'] ) && $request->has_param( 'date' ) && null === $request['date'] )
		) {
			$prepared_post->post_date_gmt = null;
			$prepared_post->post_date     = null;
		}

		// Post slug.
		if ( ! empty( $schema['properties']['slug'] ) && isset( $request['slug'] ) ) {
			$prepared_post->post_name = $request['slug'];
		}

		// Author.
		if ( ! empty( $schema['properties']['author'] ) && ! empty( $request['author'] ) ) {
			$post_author = (int) $request['author'];

			if ( get_current_user_id() !== $post_author ) {
				$user_obj = get_userdata( $post_author );

				if ( ! $user_obj ) {
					return new GC_Error(
						'rest_invalid_author',
						__( '作者ID无效。' ),
						array( 'status' => 400 )
					);
				}
			}

			$prepared_post->post_author = $post_author;
		}

		// Post password.
		if ( ! empty( $schema['properties']['password'] ) && isset( $request['password'] ) ) {
			$prepared_post->post_password = $request['password'];

			if ( '' !== $request['password'] ) {
				if ( ! empty( $schema['properties']['sticky'] ) && ! empty( $request['sticky'] ) ) {
					return new GC_Error(
						'rest_invalid_field',
						__( '文章不能既被置顶又受密码保护。' ),
						array( 'status' => 400 )
					);
				}

				if ( ! empty( $prepared_post->ID ) && is_sticky( $prepared_post->ID ) ) {
					return new GC_Error(
						'rest_invalid_field',
						__( '置顶文章不能被密码保护。' ),
						array( 'status' => 400 )
					);
				}
			}
		}

		if ( ! empty( $schema['properties']['sticky'] ) && ! empty( $request['sticky'] ) ) {
			if ( ! empty( $prepared_post->ID ) && post_password_required( $prepared_post->ID ) ) {
				return new GC_Error(
					'rest_invalid_field',
					__( '受密码保护的文章不能被设为置顶。' ),
					array( 'status' => 400 )
				);
			}
		}

		// Parent.
		if ( ! empty( $schema['properties']['parent'] ) && isset( $request['parent'] ) ) {
			if ( 0 === (int) $request['parent'] ) {
				$prepared_post->post_parent = 0;
			} else {
				$parent = get_post( (int) $request['parent'] );

				if ( empty( $parent ) ) {
					return new GC_Error(
						'rest_post_invalid_id',
						__( '文章父 ID 无效。' ),
						array( 'status' => 400 )
					);
				}

				$prepared_post->post_parent = (int) $parent->ID;
			}
		}

		// Menu order.
		if ( ! empty( $schema['properties']['menu_order'] ) && isset( $request['menu_order'] ) ) {
			$prepared_post->menu_order = (int) $request['menu_order'];
		}

		// Comment status.
		if ( ! empty( $schema['properties']['comment_status'] ) && ! empty( $request['comment_status'] ) ) {
			$prepared_post->comment_status = $request['comment_status'];
		}

		// Ping status.
		if ( ! empty( $schema['properties']['ping_status'] ) && ! empty( $request['ping_status'] ) ) {
			$prepared_post->ping_status = $request['ping_status'];
		}

		if ( ! empty( $schema['properties']['template'] ) ) {
			// Force template to null so that it can be handled exclusively by the REST controller.
			$prepared_post->page_template = null;
		}

		/**
		 * Filters a post before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_pre_insert_post`
		 *  - `rest_pre_insert_page`
		 *  - `rest_pre_insert_attachment`
		 *
		 * @since 4.7.0
		 *
		 * @param stdClass        $prepared_post An object representing a single post prepared
		 *                                       for inserting or updating the database.
		 * @param GC_REST_Request $request       Request object.
		 */
		return apply_filters( "rest_pre_insert_{$this->post_type}", $prepared_post, $request );

	}

	/**
	 * Checks whether the status is valid for the given post.
	 *
	 * Allows for sending an update request with the current status, even if that status would not be acceptable.
	 *
	 * @since 5.6.0
	 *
	 * @param string          $status  The provided status.
	 * @param GC_REST_Request $request The request object.
	 * @param string          $param   The parameter name.
	 * @return true|GC_Error True if the status is valid, or GC_Error if not.
	 */
	public function check_status( $status, $request, $param ) {
		if ( $request['id'] ) {
			$post = $this->get_post( $request['id'] );

			if ( ! is_gc_error( $post ) && $post->post_status === $status ) {
				return true;
			}
		}

		$args = $request->get_attributes()['args'][ $param ];

		return rest_validate_value_from_schema( $status, $args, $param );
	}

	/**
	 * Determines validity and normalizes the given status parameter.
	 *
	 * @since 4.7.0
	 *
	 * @param string       $post_status Post status.
	 * @param GC_Post_Type $post_type   Post type.
	 * @return string|GC_Error Post status or GC_Error if lacking the proper permission.
	 */
	protected function handle_status_param( $post_status, $post_type ) {

		switch ( $post_status ) {
			case 'draft':
			case 'pending':
				break;
			case 'private':
				if ( ! current_user_can( $post_type->cap->publish_posts ) ) {
					return new GC_Error(
						'rest_cannot_publish',
						__( '抱歉，您不能在此文章类型中创建私人文章。' ),
						array( 'status' => rest_authorization_required_code() )
					);
				}
				break;
			case 'publish':
			case 'future':
				if ( ! current_user_can( $post_type->cap->publish_posts ) ) {
					return new GC_Error(
						'rest_cannot_publish',
						__( '抱歉，您不能在此文章类型中发布文章。' ),
						array( 'status' => rest_authorization_required_code() )
					);
				}
				break;
			default:
				if ( ! get_post_status_object( $post_status ) ) {
					$post_status = 'draft';
				}
				break;
		}

		return $post_status;
	}

	/**
	 * Determines the featured media based on a request param.
	 *
	 * @since 4.7.0
	 *
	 * @param int $featured_media Featured Media ID.
	 * @param int $post_id        Post ID.
	 * @return bool|GC_Error Whether the post thumbnail was successfully deleted, otherwise GC_Error.
	 */
	protected function handle_featured_media( $featured_media, $post_id ) {

		$featured_media = (int) $featured_media;
		if ( $featured_media ) {
			$result = set_post_thumbnail( $post_id, $featured_media );
			if ( $result ) {
				return true;
			} else {
				return new GC_Error(
					'rest_invalid_featured_media',
					__( '无效的特色媒体ID。' ),
					array( 'status' => 400 )
				);
			}
		} else {
			return delete_post_thumbnail( $post_id );
		}

	}

	/**
	 * Checks whether the template is valid for the given post.
	 *
	 * @since 4.9.0
	 *
	 * @param string          $template Page template filename.
	 * @param GC_REST_Request $request  Request.
	 * @return bool|GC_Error True if template is still valid or if the same as existing value, or false if template not supported.
	 */
	public function check_template( $template, $request ) {

		if ( ! $template ) {
			return true;
		}

		if ( $request['id'] ) {
			$post             = get_post( $request['id'] );
			$current_template = get_page_template_slug( $request['id'] );
		} else {
			$post             = null;
			$current_template = '';
		}

		// Always allow for updating a post to the same template, even if that template is no longer supported.
		if ( $template === $current_template ) {
			return true;
		}

		// If this is a create request, get_post() will return null and gc theme will fallback to the passed post type.
		$allowed_templates = gc_get_theme()->get_page_templates( $post, $this->post_type );

		if ( isset( $allowed_templates[ $template ] ) ) {
			return true;
		}

		return new GC_Error(
			'rest_invalid_param',
			/* translators: 1: Parameter, 2: List of valid values. */
			sprintf( __( '%1$s不是%2$s之一。' ), 'template', implode( ', ', array_keys( $allowed_templates ) ) )
		);
	}

	/**
	 * Sets the template for a post.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 Added the `$validate` parameter.
	 *
	 * @param string $template Page template filename.
	 * @param int    $post_id  Post ID.
	 * @param bool   $validate Whether to validate that the template selected is valid.
	 */
	public function handle_template( $template, $post_id, $validate = false ) {

		if ( $validate && ! array_key_exists( $template, gc_get_theme()->get_page_templates( get_post( $post_id ) ) ) ) {
			$template = '';
		}

		update_post_meta( $post_id, '_gc_page_template', $template );
	}

	/**
	 * Updates the post's terms from a REST request.
	 *
	 * @since 4.7.0
	 *
	 * @param int             $post_id The post ID to update the terms form.
	 * @param GC_REST_Request $request The request object with post and terms data.
	 * @return null|GC_Error GC_Error on an error assigning any of the terms, otherwise null.
	 */
	protected function handle_terms( $post_id, $request ) {
		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( ! isset( $request[ $base ] ) ) {
				continue;
			}

			$result = gc_set_object_terms( $post_id, $request[ $base ], $taxonomy->name );

			if ( is_gc_error( $result ) ) {
				return $result;
			}
		}
	}

	/**
	 * Checks whether current user can assign all terms sent with the current request.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_REST_Request $request The request object with post and terms data.
	 * @return bool Whether the current user can assign the provided terms.
	 */
	protected function check_assign_terms_permission( $request ) {
		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );
		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( ! isset( $request[ $base ] ) ) {
				continue;
			}

			foreach ( (array) $request[ $base ] as $term_id ) {
				// Invalid terms will be rejected later.
				if ( ! get_term( $term_id, $taxonomy->name ) ) {
					continue;
				}

				if ( ! current_user_can( 'assign_term', (int) $term_id ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Checks if a given post type can be viewed or managed.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post_Type|string $post_type Post type name or object.
	 * @return bool Whether the post type is allowed in REST.
	 */
	protected function check_is_post_type_allowed( $post_type ) {
		if ( ! is_object( $post_type ) ) {
			$post_type = get_post_type_object( $post_type );
		}

		if ( ! empty( $post_type ) && ! empty( $post_type->show_in_rest ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a post can be read.
	 *
	 * Correctly handles posts with the inherit status.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be read.
	 */
	public function check_read_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( ! $this->check_is_post_type_allowed( $post_type ) ) {
			return false;
		}

		// Is the post readable?
		if ( 'publish' === $post->post_status || current_user_can( 'read_post', $post->ID ) ) {
			return true;
		}

		$post_status_obj = get_post_status_object( $post->post_status );
		if ( $post_status_obj && $post_status_obj->public ) {
			return true;
		}

		// Can we read the parent if we're inheriting?
		if ( 'inherit' === $post->post_status && $post->post_parent > 0 ) {
			$parent = get_post( $post->post_parent );
			if ( $parent ) {
				return $this->check_read_permission( $parent );
			}
		}

		/*
		 * If there isn't a parent, but the status is set to inherit, assume
		 * it's published (as per get_post_status()).
		 */
		if ( 'inherit' === $post->post_status ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a post can be edited.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be edited.
	 */
	protected function check_update_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		if ( ! $this->check_is_post_type_allowed( $post_type ) ) {
			return false;
		}

		return current_user_can( 'edit_post', $post->ID );
	}

	/**
	 * Checks if a post can be created.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be created.
	 */
	protected function check_create_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		if ( ! $this->check_is_post_type_allowed( $post_type ) ) {
			return false;
		}

		return current_user_can( $post_type->cap->create_posts );
	}

	/**
	 * Checks if a post can be deleted.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be deleted.
	 */
	protected function check_delete_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		if ( ! $this->check_is_post_type_allowed( $post_type ) ) {
			return false;
		}

		return current_user_can( 'delete_post', $post->ID );
	}

	/**
	 * Prepares a single post output for response.
	 *
	 * @since 4.7.0
	 * @since 5.9.0 Renamed `$post` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param GC_Post         $item    Post object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post            = $item;
		$GLOBALS['post'] = $post;

		setup_postdata( $post );

		$fields = $this->get_fields_for_response( $request );

		// Base fields for every post.
		$data = array();

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = $post->ID;
		}

		if ( rest_is_field_included( 'date', $fields ) ) {
			$data['date'] = $this->prepare_date_response( $post->post_date_gmt, $post->post_date );
		}

		if ( rest_is_field_included( 'date_gmt', $fields ) ) {
			/*
			 * For drafts, `post_date_gmt` may not be set, indicating that the date
			 * of the draft should be updated each time it is saved (see #38883).
			 * In this case, shim the value based on the `post_date` field
			 * with the site's timezone offset applied.
			 */
			if ( '0000-00-00 00:00:00' === $post->post_date_gmt ) {
				$post_date_gmt = get_gmt_from_date( $post->post_date );
			} else {
				$post_date_gmt = $post->post_date_gmt;
			}
			$data['date_gmt'] = $this->prepare_date_response( $post_date_gmt );
		}

		if ( rest_is_field_included( 'guid', $fields ) ) {
			$data['guid'] = array(
				/** This filter is documented in gc-includes/post-template.php */
				'rendered' => apply_filters( 'get_the_guid', $post->guid, $post->ID ),
				'raw'      => $post->guid,
			);
		}

		if ( rest_is_field_included( 'modified', $fields ) ) {
			$data['modified'] = $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified );
		}

		if ( rest_is_field_included( 'modified_gmt', $fields ) ) {
			/*
			 * For drafts, `post_modified_gmt` may not be set (see `post_date_gmt` comments
			 * above). In this case, shim the value based on the `post_modified` field
			 * with the site's timezone offset applied.
			 */
			if ( '0000-00-00 00:00:00' === $post->post_modified_gmt ) {
				$post_modified_gmt = gmdate( 'Y-m-d H:i:s', strtotime( $post->post_modified ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			} else {
				$post_modified_gmt = $post->post_modified_gmt;
			}
			$data['modified_gmt'] = $this->prepare_date_response( $post_modified_gmt );
		}

		if ( rest_is_field_included( 'password', $fields ) ) {
			$data['password'] = $post->post_password;
		}

		if ( rest_is_field_included( 'slug', $fields ) ) {
			$data['slug'] = $post->post_name;
		}

		if ( rest_is_field_included( 'status', $fields ) ) {
			$data['status'] = $post->post_status;
		}

		if ( rest_is_field_included( 'type', $fields ) ) {
			$data['type'] = $post->post_type;
		}

		if ( rest_is_field_included( 'link', $fields ) ) {
			$data['link'] = get_permalink( $post->ID );
		}

		if ( rest_is_field_included( 'title', $fields ) ) {
			$data['title'] = array();
		}
		if ( rest_is_field_included( 'title.raw', $fields ) ) {
			$data['title']['raw'] = $post->post_title;
		}
		if ( rest_is_field_included( 'title.rendered', $fields ) ) {
			add_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );

			$data['title']['rendered'] = get_the_title( $post->ID );

			remove_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
		}

		$has_password_filter = false;

		if ( $this->can_access_password_content( $post, $request ) ) {
			$this->password_check_passed[ $post->ID ] = true;
			// Allow access to the post, permissions already checked before.
			add_filter( 'post_password_required', array( $this, 'check_password_required' ), 10, 2 );

			$has_password_filter = true;
		}

		if ( rest_is_field_included( 'content', $fields ) ) {
			$data['content'] = array();
		}
		if ( rest_is_field_included( 'content.raw', $fields ) ) {
			$data['content']['raw'] = $post->post_content;
		}
		if ( rest_is_field_included( 'content.rendered', $fields ) ) {
			/** This filter is documented in gc-includes/post-template.php */
			$data['content']['rendered'] = post_password_required( $post ) ? '' : apply_filters( 'the_content', $post->post_content );
		}
		if ( rest_is_field_included( 'content.protected', $fields ) ) {
			$data['content']['protected'] = (bool) $post->post_password;
		}
		if ( rest_is_field_included( 'content.block_version', $fields ) ) {
			$data['content']['block_version'] = block_version( $post->post_content );
		}

		if ( rest_is_field_included( 'excerpt', $fields ) ) {
			/** This filter is documented in gc-includes/post-template.php */
			$excerpt = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );

			/** This filter is documented in gc-includes/post-template.php */
			$excerpt = apply_filters( 'the_excerpt', $excerpt );

			$data['excerpt'] = array(
				'raw'       => $post->post_excerpt,
				'rendered'  => post_password_required( $post ) ? '' : $excerpt,
				'protected' => (bool) $post->post_password,
			);
		}

		if ( $has_password_filter ) {
			// Reset filter.
			remove_filter( 'post_password_required', array( $this, 'check_password_required' ) );
		}

		if ( rest_is_field_included( 'author', $fields ) ) {
			$data['author'] = (int) $post->post_author;
		}

		if ( rest_is_field_included( 'featured_media', $fields ) ) {
			$data['featured_media'] = (int) get_post_thumbnail_id( $post->ID );
		}

		if ( rest_is_field_included( 'parent', $fields ) ) {
			$data['parent'] = (int) $post->post_parent;
		}

		if ( rest_is_field_included( 'menu_order', $fields ) ) {
			$data['menu_order'] = (int) $post->menu_order;
		}

		if ( rest_is_field_included( 'comment_status', $fields ) ) {
			$data['comment_status'] = $post->comment_status;
		}

		if ( rest_is_field_included( 'ping_status', $fields ) ) {
			$data['ping_status'] = $post->ping_status;
		}

		if ( rest_is_field_included( 'sticky', $fields ) ) {
			$data['sticky'] = is_sticky( $post->ID );
		}

		if ( rest_is_field_included( 'template', $fields ) ) {
			$template = get_page_template_slug( $post->ID );
			if ( $template ) {
				$data['template'] = $template;
			} else {
				$data['template'] = '';
			}
		}

		if ( rest_is_field_included( 'format', $fields ) ) {
			$data['format'] = get_post_format( $post->ID );

			// Fill in blank post format.
			if ( empty( $data['format'] ) ) {
				$data['format'] = 'standard';
			}
		}

		if ( rest_is_field_included( 'meta', $fields ) ) {
			$data['meta'] = $this->meta->get_value( $post->ID, $request );
		}

		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( rest_is_field_included( $base, $fields ) ) {
				$terms         = get_the_terms( $post, $taxonomy->name );
				$data[ $base ] = $terms ? array_values( gc_list_pluck( $terms, 'term_id' ) ) : array();
			}
		}

		$post_type_obj = get_post_type_object( $post->post_type );
		if ( is_post_type_viewable( $post_type_obj ) && $post_type_obj->public ) {
			$permalink_template_requested = rest_is_field_included( 'permalink_template', $fields );
			$generated_slug_requested     = rest_is_field_included( 'generated_slug', $fields );

			if ( $permalink_template_requested || $generated_slug_requested ) {
				if ( ! function_exists( 'get_sample_permalink' ) ) {
					require_once ABSPATH . 'gc-admin/includes/post.php';
				}

				$sample_permalink = get_sample_permalink( $post->ID, $post->post_title, '' );

				if ( $permalink_template_requested ) {
					$data['permalink_template'] = $sample_permalink[0];
				}

				if ( $generated_slug_requested ) {
					$data['generated_slug'] = $sample_permalink[1];
				}
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = $this->prepare_links( $post );
			$response->add_links( $links );

			if ( ! empty( $links['self']['href'] ) ) {
				$actions = $this->get_available_actions( $post, $request );

				$self = $links['self']['href'];

				foreach ( $actions as $rel ) {
					$response->add_link( $rel, $self );
				}
			}
		}

		/**
		 * Filters the post data for a REST API response.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_prepare_post`
		 *  - `rest_prepare_page`
		 *  - `rest_prepare_attachment`
		 *
		 * @since 4.7.0
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_Post          $post     Post object.
		 * @param GC_REST_Request  $request  Request object.
		 */
		return apply_filters( "rest_prepare_{$this->post_type}", $response, $post, $request );
	}

	/**
	 * Overwrites the default protected title format.
	 *
	 * By default, GeChiUI will show password protected posts with a title of
	 * "密码保护：%s", as the REST API communicates the protected status of a post
	 * in a machine readable format, we remove the "Protected: " prefix.
	 *
	 * @since 4.7.0
	 *
	 * @return string Protected title format.
	 */
	public function protected_title_format() {
		return '%s';
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 4.7.0
	 *
	 * @param GC_Post $post Post object.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $post ) {
		// Entity meta.
		$links = array(
			'self'       => array(
				'href' => rest_url( rest_get_route_for_post( $post->ID ) ),
			),
			'collection' => array(
				'href' => rest_url( rest_get_route_for_post_type_items( $this->post_type ) ),
			),
			'about'      => array(
				'href' => rest_url( 'gc/v2/types/' . $this->post_type ),
			),
		);

		if ( ( in_array( $post->post_type, array( 'post', 'page' ), true ) || post_type_supports( $post->post_type, 'author' ) )
			&& ! empty( $post->post_author ) ) {
			$links['author'] = array(
				'href'       => rest_url( 'gc/v2/users/' . $post->post_author ),
				'embeddable' => true,
			);
		}

		if ( in_array( $post->post_type, array( 'post', 'page' ), true ) || post_type_supports( $post->post_type, 'comments' ) ) {
			$replies_url = rest_url( 'gc/v2/comments' );
			$replies_url = add_query_arg( 'post', $post->ID, $replies_url );

			$links['replies'] = array(
				'href'       => $replies_url,
				'embeddable' => true,
			);
		}

		if ( in_array( $post->post_type, array( 'post', 'page' ), true ) || post_type_supports( $post->post_type, 'revisions' ) ) {
			$revisions       = gc_get_latest_revision_id_and_total_count( $post->ID );
			$revisions_count = ! is_gc_error( $revisions ) ? $revisions['count'] : 0;
			$revisions_base  = sprintf( '/%s/%s/%d/revisions', $this->namespace, $this->rest_base, $post->ID );

			$links['version-history'] = array(
				'href'  => rest_url( $revisions_base ),
				'count' => $revisions_count,
			);

			if ( $revisions_count > 0 ) {
				$links['predecessor-version'] = array(
					'href' => rest_url( $revisions_base . '/' . $revisions['latest_id'] ),
					'id'   => $revisions['latest_id'],
				);
			}
		}

		$post_type_obj = get_post_type_object( $post->post_type );

		if ( $post_type_obj->hierarchical && ! empty( $post->post_parent ) ) {
			$links['up'] = array(
				'href'       => rest_url( rest_get_route_for_post( $post->post_parent ) ),
				'embeddable' => true,
			);
		}

		// If we have a featured media, add that.
		$featured_media = get_post_thumbnail_id( $post->ID );
		if ( $featured_media ) {
			$image_url = rest_url( rest_get_route_for_post( $featured_media ) );

			$links['https://api.w.org/featuredmedia'] = array(
				'href'       => $image_url,
				'embeddable' => true,
			);
		}

		if ( ! in_array( $post->post_type, array( 'attachment', 'nav_menu_item', 'revision' ), true ) ) {
			$attachments_url = rest_url( rest_get_route_for_post_type_items( 'attachment' ) );
			$attachments_url = add_query_arg( 'parent', $post->ID, $attachments_url );

			$links['https://api.w.org/attachment'] = array(
				'href' => $attachments_url,
			);
		}

		$taxonomies = get_object_taxonomies( $post->post_type );

		if ( ! empty( $taxonomies ) ) {
			$links['https://api.w.org/term'] = array();

			foreach ( $taxonomies as $tax ) {
				$taxonomy_route = rest_get_route_for_taxonomy_items( $tax );

				// Skip taxonomies that are not public.
				if ( empty( $taxonomy_route ) ) {
					continue;
				}
				$terms_url = add_query_arg(
					'post',
					$post->ID,
					rest_url( $taxonomy_route )
				);

				$links['https://api.w.org/term'][] = array(
					'href'       => $terms_url,
					'taxonomy'   => $tax,
					'embeddable' => true,
				);
			}
		}

		return $links;
	}

	/**
	 * Gets the link relations available for the post and current user.
	 *
	 * @since 4.9.8
	 *
	 * @param GC_Post         $post    Post object.
	 * @param GC_REST_Request $request Request object.
	 * @return array List of link relations.
	 */
	protected function get_available_actions( $post, $request ) {

		if ( 'edit' !== $request['context'] ) {
			return array();
		}

		$rels = array();

		$post_type = get_post_type_object( $post->post_type );

		if ( 'attachment' !== $this->post_type && current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		if ( current_user_can( 'unfiltered_html' ) ) {
			$rels[] = 'https://api.w.org/action-unfiltered-html';
		}

		if ( 'post' === $post_type->name ) {
			if ( current_user_can( $post_type->cap->edit_others_posts ) && current_user_can( $post_type->cap->publish_posts ) ) {
				$rels[] = 'https://api.w.org/action-sticky';
			}
		}

		if ( post_type_supports( $post_type->name, 'author' ) ) {
			if ( current_user_can( $post_type->cap->edit_others_posts ) ) {
				$rels[] = 'https://api.w.org/action-assign-author';
			}
		}

		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $tax ) {
			$tax_base   = ! empty( $tax->rest_base ) ? $tax->rest_base : $tax->name;
			$create_cap = is_taxonomy_hierarchical( $tax->name ) ? $tax->cap->edit_terms : $tax->cap->assign_terms;

			if ( current_user_can( $create_cap ) ) {
				$rels[] = 'https://api.w.org/action-create-' . $tax_base;
			}

			if ( current_user_can( $tax->cap->assign_terms ) ) {
				$rels[] = 'https://api.w.org/action-assign-' . $tax_base;
			}
		}

		return $rels;
	}

	/**
	 * Retrieves the post's schema, conforming to JSON Schema.
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
			'title'      => $this->post_type,
			'type'       => 'object',
			// Base properties for every Post.
			'properties' => array(
				'date'         => array(
					'description' => __( "文章发布的日期（系统时区）。" ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'     => array(
					'description' => __( '该文章发布的 GMT 日期。' ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'guid'         => array(
					'description' => __( '文章的全局唯一标识符。' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '文章的 GUID，存放于数据库中。' ),
							'type'        => 'string',
							'context'     => array( 'edit' ),
							'readonly'    => true,
						),
						'rendered' => array(
							'description' => __( '文章的 GUID，经转换后用于显示。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
					),
				),
				'id'           => array(
					'description' => __( '文章的唯一标识符。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'link'         => array(
					'description' => __( '文章的网址。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'modified'     => array(
					'description' => __( "文章最后修改的日期（系统时区）。" ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'modified_gmt' => array(
					'description' => __( '文章最后一次修改的 GMT 日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'slug'         => array(
					'description' => __( '文章的字母数字标识符，其类型是唯一的。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'sanitize_slug' ),
					),
				),
				'status'       => array(
					'description' => __( '文章的命名状态。' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'validate_callback' => array( $this, 'check_status' ),
					),
				),
				'type'         => array(
					'description' => __( '文章的类型。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'password'     => array(
					'description' => __( '用来保护内容和摘要的密码。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
			),
		);

		$post_type_obj = get_post_type_object( $this->post_type );
		if ( is_post_type_viewable( $post_type_obj ) && $post_type_obj->public ) {
			$schema['properties']['permalink_template'] = array(
				'description' => __( '文章的固定链接模板。' ),
				'type'        => 'string',
				'context'     => array( 'edit' ),
				'readonly'    => true,
			);

			$schema['properties']['generated_slug'] = array(
				'description' => __( '从文章标题自动生成的别名。' ),
				'type'        => 'string',
				'context'     => array( 'edit' ),
				'readonly'    => true,
			);
		}

		if ( $post_type_obj->hierarchical ) {
			$schema['properties']['parent'] = array(
				'description' => __( '文章的父级 ID。' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			);
		}

		$post_type_attributes = array(
			'title',
			'editor',
			'author',
			'excerpt',
			'thumbnail',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
			'custom-fields',
		);
		$fixed_schemas        = array(
			'post'       => array(
				'title',
				'editor',
				'author',
				'excerpt',
				'thumbnail',
				'comments',
				'revisions',
				'post-formats',
				'custom-fields',
			),
			'page'       => array(
				'title',
				'editor',
				'author',
				'excerpt',
				'thumbnail',
				'comments',
				'revisions',
				'page-attributes',
				'custom-fields',
			),
			'attachment' => array(
				'title',
				'author',
				'comments',
				'revisions',
				'custom-fields',
			),
		);

		foreach ( $post_type_attributes as $attribute ) {
			if ( isset( $fixed_schemas[ $this->post_type ] ) && ! in_array( $attribute, $fixed_schemas[ $this->post_type ], true ) ) {
				continue;
			} elseif ( ! isset( $fixed_schemas[ $this->post_type ] ) && ! post_type_supports( $this->post_type, $attribute ) ) {
				continue;
			}

			switch ( $attribute ) {

				case 'title':
					$schema['properties']['title'] = array(
						'description' => __( '文章的标题。' ),
						'type'        => 'object',
						'context'     => array( 'view', 'edit', 'embed' ),
						'arg_options' => array(
							'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
							'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
						),
						'properties'  => array(
							'raw'      => array(
								'description' => __( '文章的标题，存放于数据库中。' ),
								'type'        => 'string',
								'context'     => array( 'edit' ),
							),
							'rendered' => array(
								'description' => __( '文章的 HTML 标题，经转换后用于显示。' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit', 'embed' ),
								'readonly'    => true,
							),
						),
					);
					break;

				case 'editor':
					$schema['properties']['content'] = array(
						'description' => __( '文章的内容。' ),
						'type'        => 'object',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
							'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
						),
						'properties'  => array(
							'raw'           => array(
								'description' => __( '文章的内容，存放于数据库中。' ),
								'type'        => 'string',
								'context'     => array( 'edit' ),
							),
							'rendered'      => array(
								'description' => __( '文章的 HTML 内容，经转换后用于显示。' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'block_version' => array(
								'description' => __( '该文章所使用的内容区块格式版本。' ),
								'type'        => 'integer',
								'context'     => array( 'edit' ),
								'readonly'    => true,
							),
							'protected'     => array(
								'description' => __( '内容是否受到密码保护。' ),
								'type'        => 'boolean',
								'context'     => array( 'view', 'edit', 'embed' ),
								'readonly'    => true,
							),
						),
					);
					break;

				case 'author':
					$schema['properties']['author'] = array(
						'description' => __( '文章作者的ID。' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					);
					break;

				case 'excerpt':
					$schema['properties']['excerpt'] = array(
						'description' => __( '该篇文章的摘要。' ),
						'type'        => 'object',
						'context'     => array( 'view', 'edit', 'embed' ),
						'arg_options' => array(
							'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
							'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
						),
						'properties'  => array(
							'raw'       => array(
								'description' => __( '文章的摘要，存放于数据库中。' ),
								'type'        => 'string',
								'context'     => array( 'edit' ),
							),
							'rendered'  => array(
								'description' => __( '文章的 HTML 摘要，经转换后用于显示。' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit', 'embed' ),
								'readonly'    => true,
							),
							'protected' => array(
								'description' => __( '摘要是否受到密码保护。' ),
								'type'        => 'boolean',
								'context'     => array( 'view', 'edit', 'embed' ),
								'readonly'    => true,
							),
						),
					);
					break;

				case 'thumbnail':
					$schema['properties']['featured_media'] = array(
						'description' => __( '文章的特色媒体 ID。' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					);
					break;

				case 'comments':
					$schema['properties']['comment_status'] = array(
						'description' => __( '文章是否开放评论。' ),
						'type'        => 'string',
						'enum'        => array( 'open', 'closed' ),
						'context'     => array( 'view', 'edit' ),
					);
					$schema['properties']['ping_status']    = array(
						'description' => __( '文章是否接受ping。' ),
						'type'        => 'string',
						'enum'        => array( 'open', 'closed' ),
						'context'     => array( 'view', 'edit' ),
					);
					break;

				case 'page-attributes':
					$schema['properties']['menu_order'] = array(
						'description' => __( '文章与其他文章的顺序。' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit' ),
					);
					break;

				case 'post-formats':
					// Get the native post formats and remove the array keys.
					$formats = array_values( get_post_format_slugs() );

					$schema['properties']['format'] = array(
						'description' => __( '文章的形式。' ),
						'type'        => 'string',
						'enum'        => $formats,
						'context'     => array( 'view', 'edit' ),
					);
					break;

				case 'custom-fields':
					$schema['properties']['meta'] = $this->meta->get_field_schema();
					break;

			}
		}

		if ( 'post' === $this->post_type ) {
			$schema['properties']['sticky'] = array(
				'description' => __( '文章是否为置顶。' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			);
		}

		$schema['properties']['template'] = array(
			'description' => __( '用于显示文章的主题文件。' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit' ),
			'arg_options' => array(
				'validate_callback' => array( $this, 'check_template' ),
			),
		);

		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( array_key_exists( $base, $schema['properties'] ) ) {
				$taxonomy_field_name_with_conflict = ! empty( $taxonomy->rest_base ) ? 'rest_base' : 'name';
				_doing_it_wrong(
					'register_taxonomy',
					sprintf(
						/* translators: 1: The taxonomy name, 2: The property name, either 'rest_base' or 'name', 3: The conflicting value. */
						__( '分类法“%1$s”的“%2$s”属性（%3$s）与REST API文章控制器的现有属性冲突。请在注册分类法时指定自定的“rest_base”来避免此错误。' ),
						$taxonomy->name,
						$taxonomy_field_name_with_conflict,
						$base
					),
					'5.4.0'
				);
			}

			$schema['properties'][ $base ] = array(
				/* translators: %s: Taxonomy name. */
				'description' => sprintf( __( '在 %s 分类法中分配给该文章的项目。' ), $taxonomy->name ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
				),
				'context'     => array( 'view', 'edit' ),
			);
		}

		$schema_links = $this->get_schema_links();

		if ( $schema_links ) {
			$schema['links'] = $schema_links;
		}

		// Take a snapshot of which fields are in the schema pre-filtering.
		$schema_fields = array_keys( $schema['properties'] );

		/**
		 * Filters the post's schema.
		 *
		 * The dynamic portion of the filter, `$this->post_type`, refers to the
		 * post type slug for the controller.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_post_item_schema`
		 *  - `rest_page_item_schema`
		 *  - `rest_attachment_item_schema`
		 *
		 * @since 5.4.0
		 *
		 * @param array $schema Item schema data.
		 */
		$schema = apply_filters( "rest_{$this->post_type}_item_schema", $schema );

		// Emit a _doing_it_wrong warning if user tries to add new properties using this filter.
		$new_fields = array_diff( array_keys( $schema['properties'] ), $schema_fields );
		if ( count( $new_fields ) > 0 ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: register_rest_field */
					__( '请使用%s添加新模式属性。' ),
					'register_rest_field'
				),
				'5.4.0'
			);
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves Link Description Objects that should be added to the Schema for the posts collection.
	 *
	 * @since 4.9.8
	 *
	 * @return array
	 */
	protected function get_schema_links() {

		$href = rest_url( "{$this->namespace}/{$this->rest_base}/{id}" );

		$links = array();

		if ( 'attachment' !== $this->post_type ) {
			$links[] = array(
				'rel'          => 'https://api.w.org/action-publish',
				'title'        => __( '当前用户可以发布此文章。' ),
				'href'         => $href,
				'targetSchema' => array(
					'type'       => 'object',
					'properties' => array(
						'status' => array(
							'type' => 'string',
							'enum' => array( 'publish', 'future' ),
						),
					),
				),
			);
		}

		$links[] = array(
			'rel'          => 'https://api.w.org/action-unfiltered-html',
			'title'        => __( '当前用户可以发布未经过滤的HTML标签和JavaScript。' ),
			'href'         => $href,
			'targetSchema' => array(
				'type'       => 'object',
				'properties' => array(
					'content' => array(
						'raw' => array(
							'type' => 'string',
						),
					),
				),
			),
		);

		if ( 'post' === $this->post_type ) {
			$links[] = array(
				'rel'          => 'https://api.w.org/action-sticky',
				'title'        => __( '当前用户可以置顶此文章。' ),
				'href'         => $href,
				'targetSchema' => array(
					'type'       => 'object',
					'properties' => array(
						'sticky' => array(
							'type' => 'boolean',
						),
					),
				),
			);
		}

		if ( post_type_supports( $this->post_type, 'author' ) ) {
			$links[] = array(
				'rel'          => 'https://api.w.org/action-assign-author',
				'title'        => __( '当前用户可以更改此文章的作者。' ),
				'href'         => $href,
				'targetSchema' => array(
					'type'       => 'object',
					'properties' => array(
						'author' => array(
							'type' => 'integer',
						),
					),
				),
			);
		}

		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $tax ) {
			$tax_base = ! empty( $tax->rest_base ) ? $tax->rest_base : $tax->name;

			/* translators: %s: Taxonomy name. */
			$assign_title = sprintf( __( '当前用户可以指定%s分类法内的项目。' ), $tax->name );
			/* translators: %s: Taxonomy name. */
			$create_title = sprintf( __( '当前用户可以创建%s分类法内的项目。' ), $tax->name );

			$links[] = array(
				'rel'          => 'https://api.w.org/action-assign-' . $tax_base,
				'title'        => $assign_title,
				'href'         => $href,
				'targetSchema' => array(
					'type'       => 'object',
					'properties' => array(
						$tax_base => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'integer',
							),
						),
					),
				),
			);

			$links[] = array(
				'rel'          => 'https://api.w.org/action-create-' . $tax_base,
				'title'        => $create_title,
				'href'         => $href,
				'targetSchema' => array(
					'type'       => 'object',
					'properties' => array(
						$tax_base => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'integer',
							),
						),
					),
				),
			);
		}

		return $links;
	}

	/**
	 * Retrieves the query params for the posts collection.
	 *
	 * @since 4.7.0
	 * @since 5.4.0 The `tax_relation` query parameter was added.
	 * @since 5.7.0 The `modified_after` and `modified_before` query parameters were added.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['after'] = array(
			'description' => __( '将回应限制在一个给定的ISO8601兼容日期之后发布的文章。' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['modified_after'] = array(
			'description' => __( '将回应限制为一个给定的日期（ISO 8601兼容格式）之后修改过的文章。' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		if ( post_type_supports( $this->post_type, 'author' ) ) {
			$query_params['author']         = array(
				'description' => __( '将结果集限制为指定给特定作者的文章。' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
				),
				'default'     => array(),
			);
			$query_params['author_exclude'] = array(
				'description' => __( '确保结果集排除指定给特定作者的文章。' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
				),
				'default'     => array(),
			);
		}

		$query_params['before'] = array(
			'description' => __( '将回应限制在一个给定的ISO8601兼容日期之前发布的文章。' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['modified_before'] = array(
			'description' => __( '将回应限制为一个给定的日期（ISO 8601兼容格式）之前修改过的文章。' ),
			'type'        => 'string',
			'format'      => 'date-time',
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

		if ( 'page' === $this->post_type || post_type_supports( $this->post_type, 'page-attributes' ) ) {
			$query_params['menu_order'] = array(
				'description' => __( '将结果集限制为有特定menu_order的文章。' ),
				'type'        => 'integer',
			);
		}

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
			),
		);

		if ( 'page' === $this->post_type || post_type_supports( $this->post_type, 'page-attributes' ) ) {
			$query_params['orderby']['enum'][] = 'menu_order';
		}

		$post_type = get_post_type_object( $this->post_type );

		if ( $post_type->hierarchical || 'attachment' === $this->post_type ) {
			$query_params['parent']         = array(
				'description' => __( '将结果集限制为具有特定父 ID 的项目。' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
				),
				'default'     => array(),
			);
			$query_params['parent_exclude'] = array(
				'description' => __( '将结果集限制为除特定父ID之外的所有项。' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
				),
				'default'     => array(),
			);
		}

		$query_params['search_columns'] = array(
			'default'     => array(),
			'description' => __( '需要搜索的栏位名称组。' ),
			'type'        => 'array',
			'items'       => array(
				'enum' => array( 'post_title', 'post_content', 'post_excerpt' ),
				'type' => 'string',
			),
		);

		$query_params['slug'] = array(
			'description' => __( '将结果集限制为有一个或多个特定别名的文章。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		$query_params['status'] = array(
			'default'           => 'publish',
			'description'       => __( '将结果集限制为有一个或多个状态的文章。' ),
			'type'              => 'array',
			'items'             => array(
				'enum' => array_merge( array_keys( get_post_stati() ), array( 'any' ) ),
				'type' => 'string',
			),
			'sanitize_callback' => array( $this, 'sanitize_post_statuses' ),
		);

		$query_params = $this->prepare_taxonomy_limit_schema( $query_params );

		if ( 'post' === $this->post_type ) {
			$query_params['sticky'] = array(
				'description' => __( '将结果集限制为置顶项目。' ),
				'type'        => 'boolean',
			);
		}

		/**
		 * Filters collection parameters for the posts controller.
		 *
		 * The dynamic part of the filter `$this->post_type` refers to the post
		 * type slug for the controller.
		 *
		 * This filter registers the collection parameter, but does not map the
		 * collection parameter to an internal GC_Query parameter. Use the
		 * `rest_{$this->post_type}_query` filter to set GC_Query parameters.
		 *
		 * @since 4.7.0
		 *
		 * @param array        $query_params JSON Schema-formatted collection parameters.
		 * @param GC_Post_Type $post_type    Post type object.
		 */
		return apply_filters( "rest_{$this->post_type}_collection_params", $query_params, $post_type );
	}

	/**
	 * Sanitizes and validates the list of post statuses, including whether the
	 * user can query private statuses.
	 *
	 * @since 4.7.0
	 *
	 * @param string|array    $statuses  One or more post statuses.
	 * @param GC_REST_Request $request   Full details about the request.
	 * @param string          $parameter Additional parameter to pass to validation.
	 * @return array|GC_Error A list of valid statuses, otherwise GC_Error object.
	 */
	public function sanitize_post_statuses( $statuses, $request, $parameter ) {
		$statuses = gc_parse_slug_list( $statuses );

		// The default status is different in GC_REST_Attachments_Controller.
		$attributes     = $request->get_attributes();
		$default_status = $attributes['args']['status']['default'];

		foreach ( $statuses as $status ) {
			if ( $status === $default_status ) {
				continue;
			}

			$post_type_obj = get_post_type_object( $this->post_type );

			if ( current_user_can( $post_type_obj->cap->edit_posts ) || 'private' === $status && current_user_can( $post_type_obj->cap->read_private_posts ) ) {
				$result = rest_validate_request_arg( $status, $request, $parameter );
				if ( is_gc_error( $result ) ) {
					return $result;
				}
			} else {
				return new GC_Error(
					'rest_forbidden_status',
					__( '状态被禁止。' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		return $statuses;
	}

	/**
	 * Prepares the 'tax_query' for a collection of posts.
	 *
	 * @since 5.7.0
	 *
	 * @param array           $args    GC_Query arguments.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return array Updated query arguments.
	 */
	private function prepare_tax_query( array $args, GC_REST_Request $request ) {
		$relation = $request['tax_relation'];

		if ( $relation ) {
			$args['tax_query'] = array( 'relation' => $relation );
		}

		$taxonomies = gc_list_filter(
			get_object_taxonomies( $this->post_type, 'objects' ),
			array( 'show_in_rest' => true )
		);

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			$tax_include = $request[ $base ];
			$tax_exclude = $request[ $base . '_exclude' ];

			if ( $tax_include ) {
				$terms            = array();
				$include_children = false;
				$operator         = 'IN';

				if ( rest_is_array( $tax_include ) ) {
					$terms = $tax_include;
				} elseif ( rest_is_object( $tax_include ) ) {
					$terms            = empty( $tax_include['terms'] ) ? array() : $tax_include['terms'];
					$include_children = ! empty( $tax_include['include_children'] );

					if ( isset( $tax_include['operator'] ) && 'AND' === $tax_include['operator'] ) {
						$operator = 'AND';
					}
				}

				if ( $terms ) {
					$args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $terms,
						'include_children' => $include_children,
						'operator'         => $operator,
					);
				}
			}

			if ( $tax_exclude ) {
				$terms            = array();
				$include_children = false;

				if ( rest_is_array( $tax_exclude ) ) {
					$terms = $tax_exclude;
				} elseif ( rest_is_object( $tax_exclude ) ) {
					$terms            = empty( $tax_exclude['terms'] ) ? array() : $tax_exclude['terms'];
					$include_children = ! empty( $tax_exclude['include_children'] );
				}

				if ( $terms ) {
					$args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $terms,
						'include_children' => $include_children,
						'operator'         => 'NOT IN',
					);
				}
			}
		}

		return $args;
	}

	/**
	 * Prepares the collection schema for including and excluding items by terms.
	 *
	 * @since 5.7.0
	 *
	 * @param array $query_params Collection schema.
	 * @return array Updated schema.
	 */
	private function prepare_taxonomy_limit_schema( array $query_params ) {
		$taxonomies = gc_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		if ( ! $taxonomies ) {
			return $query_params;
		}

		$query_params['tax_relation'] = array(
			'description' => __( '基于多个分类法间的关系限制结果集。' ),
			'type'        => 'string',
			'enum'        => array( 'AND', 'OR' ),
		);

		$limit_schema = array(
			'type'  => array( 'object', 'array' ),
			'oneOf' => array(
				array(
					'title'       => __( '项目ID列表' ),
					'description' => __( '将项目与列出的ID相匹配。' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
				),
				array(
					'title'                => __( '项目ID分类法查询。' ),
					'description'          => __( '进行一项高级项目查询。' ),
					'type'                 => 'object',
					'properties'           => array(
						'terms'            => array(
							'description' => __( '项目ID。' ),
							'type'        => 'array',
							'items'       => array(
								'type' => 'integer',
							),
							'default'     => array(),
						),
						'include_children' => array(
							'description' => __( '是否在限制结果集的项目中包含子项目。' ),
							'type'        => 'boolean',
							'default'     => false,
						),
					),
					'additionalProperties' => false,
				),
			),
		);

		$include_schema = array_merge(
			array(
				/* translators: %s: Taxonomy name. */
				'description' => __( '将结果集限制为在%s分类法中指定了特定项目的项目。' ),
			),
			$limit_schema
		);
		// 'operator' is supported only for 'include' queries.
		$include_schema['oneOf'][1]['properties']['operator'] = array(
			'description' => __( '项目是否必须分配所有或任何指定的术语。' ),
			'type'        => 'string',
			'enum'        => array( 'AND', 'OR' ),
			'default'     => 'OR',
		);

		$exclude_schema = array_merge(
			array(
				/* translators: %s: Taxonomy name. */
				'description' => __( '将结果集限制为在%s分类法中未指定特定项目的项目。' ),
			),
			$limit_schema
		);

		foreach ( $taxonomies as $taxonomy ) {
			$base         = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
			$base_exclude = $base . '_exclude';

			$query_params[ $base ]                = $include_schema;
			$query_params[ $base ]['description'] = sprintf( $query_params[ $base ]['description'], $base );

			$query_params[ $base_exclude ]                = $exclude_schema;
			$query_params[ $base_exclude ]['description'] = sprintf( $query_params[ $base_exclude ]['description'], $base );

			if ( ! $taxonomy->hierarchical ) {
				unset( $query_params[ $base ]['oneOf'][1]['properties']['include_children'] );
				unset( $query_params[ $base_exclude ]['oneOf'][1]['properties']['include_children'] );
			}
		}

		return $query_params;
	}
}
