<?php
/**
 * REST API: GC_REST_Users_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to manage users via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Users_Controller extends GC_REST_Controller {

	/**
	 * Instance of a user meta fields object.
	 *
	 * @var GC_REST_User_Meta_Fields
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'users';

		$this->meta = new GC_REST_User_Meta_Fields();
	}

	/**
	 * Registers the routes for users.
	 *
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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( '用户的唯一标识符。' ),
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
						'force'    => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( '要求为true，因为用户不能被移动到回收站。' ),
						),
						'reassign' => array(
							'type'              => 'integer',
							'description'       => __( '将被删除用户的文章和链接重新指定到此用户ID。' ),
							'required'          => true,
							'sanitize_callback' => array( $this, 'check_reassign' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/me',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'permission_callback' => '__return_true',
					'callback'            => array( $this, 'get_current_item' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => GC_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_current_item' ),
					'permission_callback' => array( $this, 'update_current_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => GC_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_current_item' ),
					'permission_callback' => array( $this, 'delete_current_item_permissions_check' ),
					'args'                => array(
						'force'    => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( '要求为true，因为用户不能被移动到回收站。' ),
						),
						'reassign' => array(
							'type'              => 'integer',
							'description'       => __( '将被删除用户的文章和链接重新指定到此用户ID。' ),
							'required'          => true,
							'sanitize_callback' => array( $this, 'check_reassign' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks for a valid value for the reassign parameter when deleting users.
	 *
	 * The value can be an integer, 'false', false, or ''.
	 *
	 *
	 * @param int|bool        $value   The value passed to the reassign parameter.
	 * @param GC_REST_Request $request Full details about the request.
	 * @param string          $param   The parameter that is being sanitized.
	 * @return int|bool|GC_Error
	 */
	public function check_reassign( $value, $request, $param ) {
		if ( is_numeric( $value ) ) {
			return $value;
		}

		if ( empty( $value ) || false === $value || 'false' === $value ) {
			return false;
		}

		return new GC_Error(
			'rest_invalid_param',
			__( '无效的用户参数。' ),
			array( 'status' => 400 )
		);
	}

	/**
	 * Permissions check for getting all users.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, otherwise GC_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		// Check if roles is specified in GET request and if user can list users.
		if ( ! empty( $request['roles'] ) && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_user_cannot_view',
				__( '抱歉，您无法按角色筛选用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		// Check if capabilities is specified in GET request and if user can list users.
		if ( ! empty( $request['capabilities'] ) && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_user_cannot_view',
				__( '抱歉，您无法按权限筛选用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'edit' === $request['context'] && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能列出用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( in_array( $request['orderby'], array( 'email', 'registered_date' ), true ) && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_forbidden_orderby',
				__( '抱歉，您不能依此参数排序用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'authors' === $request['who'] ) {
			$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

			foreach ( $types as $type ) {
				if ( post_type_supports( $type->name, 'author' )
					&& current_user_can( $type->cap->edit_posts ) ) {
					return true;
				}
			}

			return new GC_Error(
				'rest_forbidden_who',
				__( '抱歉，您不能用此参数查询用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all users.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal GC_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array(
			'exclude'      => 'exclude',
			'include'      => 'include',
			'order'        => 'order',
			'per_page'     => 'number',
			'search'       => 'search',
			'roles'        => 'role__in',
			'capabilities' => 'capability__in',
			'slug'         => 'nicename__in',
		);

		$prepared_args = array();

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $prepared_args.
		 */
		foreach ( $parameter_mappings as $api_param => $gc_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $gc_param ] = $request[ $api_param ];
			}
		}

		if ( isset( $registered['offset'] ) && ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}

		if ( isset( $registered['orderby'] ) ) {
			$orderby_possibles        = array(
				'id'              => 'ID',
				'include'         => 'include',
				'name'            => 'display_name',
				'registered_date' => 'registered',
				'slug'            => 'user_nicename',
				'include_slugs'   => 'nicename__in',
				'email'           => 'user_email',
				'url'             => 'user_url',
			);
			$prepared_args['orderby'] = $orderby_possibles[ $request['orderby'] ];
		}

		if ( isset( $registered['who'] ) && ! empty( $request['who'] ) && 'authors' === $request['who'] ) {
			$prepared_args['who'] = 'authors';
		} elseif ( ! current_user_can( 'list_users' ) ) {
			$prepared_args['has_published_posts'] = get_post_types( array( 'show_in_rest' => true ), 'names' );
		}

		if ( ! empty( $request['has_published_posts'] ) ) {
			$prepared_args['has_published_posts'] = ( true === $request['has_published_posts'] )
				? get_post_types( array( 'show_in_rest' => true ), 'names' )
				: (array) $request['has_published_posts'];
		}

		if ( ! empty( $prepared_args['search'] ) ) {
			$prepared_args['search'] = '*' . $prepared_args['search'] . '*';
		}
		/**
		 * Filters GC_User_Query arguments when querying users via the REST API.
		 *
		 * @link https://developer.gechiui.com/reference/classes/gc_user_query/
		 *
		 *
		 * @param array           $prepared_args Array of arguments for GC_User_Query.
		 * @param GC_REST_Request $request       The REST API request.
		 */
		$prepared_args = apply_filters( 'rest_user_query', $prepared_args, $request );

		$query = new GC_User_Query( $prepared_args );

		$users = array();

		foreach ( $query->results as $user ) {
			$data    = $this->prepare_item_for_response( $user, $request );
			$users[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $users );

		// Store pagination values for headers then unset for count query.
		$per_page = (int) $prepared_args['number'];
		$page     = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		$prepared_args['fields'] = 'ID';

		$total_users = $query->get_total();

		if ( $total_users < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $prepared_args['number'], $prepared_args['offset'] );
			$count_query = new GC_User_Query( $prepared_args );
			$total_users = $count_query->get_total();
		}

		$response->header( 'X-GC-Total', (int) $total_users );

		$max_pages = ceil( $total_users / $per_page );

		$response->header( 'X-GC-TotalPages', (int) $max_pages );

		$base = add_query_arg( urlencode_deep( $request->get_query_params() ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );
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
	 * Get the user, if the ID is valid.
	 *
	 *
	 * @param int $id Supplied ID.
	 * @return GC_User|GC_Error True if ID is valid, GC_Error otherwise.
	 */
	protected function get_user( $id ) {
		$error = new GC_Error(
			'rest_user_invalid_id',
			__( '用户ID无效。' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$user = get_userdata( (int) $id );
		if ( empty( $user ) || ! $user->exists() ) {
			return $error;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user->ID ) ) {
			return $error;
		}

		return $user;
	}

	/**
	 * Checks if a given request has access to read a user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, otherwise GC_Error object.
	 */
	public function get_item_permissions_check( $request ) {
		$user = $this->get_user( $request['id'] );
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$types = get_post_types( array( 'show_in_rest' => true ), 'names' );

		if ( get_current_user_id() === $user->ID ) {
			return true;
		}

		if ( 'edit' === $request['context'] && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_user_cannot_view',
				__( '抱歉，您不能列出用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		} elseif ( ! count_user_posts( $user->ID, $types ) && ! current_user_can( 'edit_user', $user->ID ) && ! current_user_can( 'list_users' ) ) {
			return new GC_Error(
				'rest_user_cannot_view',
				__( '抱歉，您不能列出用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a single user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$user = $this->get_user( $request['id'] );
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$user     = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $user );

		return $response;
	}

	/**
	 * Retrieves the current user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_current_item( $request ) {
		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new GC_Error(
				'rest_not_logged_in',
				__( '您目前没有登录。' ),
				array( 'status' => 401 )
			);
		}

		$user     = gc_get_current_user();
		$response = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Checks if a given request has access create users.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! current_user_can( 'create_users' ) ) {
			return new GC_Error(
				'rest_cannot_create_user',
				__( '抱歉，您不能创建新用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new GC_Error(
				'rest_user_exists',
				__( '不能创建新用户。' ),
				array( 'status' => 400 )
			);
		}

		$schema = $this->get_item_schema();

		if ( ! empty( $request['roles'] ) && ! empty( $schema['properties']['roles'] ) ) {
			$check_permission = $this->check_role_update( $request['id'], $request['roles'] );

			if ( is_gc_error( $check_permission ) ) {
				return $check_permission;
			}
		}

		$user = $this->prepare_item_for_database( $request );

		if ( is_multisite() ) {
			$ret = gcmu_validate_user_signup( $user->user_login, $user->user_email );

			if ( is_gc_error( $ret['errors'] ) && $ret['errors']->has_errors() ) {
				$error = new GC_Error(
					'rest_invalid_param',
					__( '无效的用户参数。' ),
					array( 'status' => 400 )
				);

				foreach ( $ret['errors']->errors as $code => $messages ) {
					foreach ( $messages as $message ) {
						$error->add( $code, $message );
					}

					$error_data = $error->get_error_data( $code );

					if ( $error_data ) {
						$error->add_data( $error_data, $code );
					}
				}
				return $error;
			}
		}

		if ( is_multisite() ) {
			$user_id = gcmu_create_user( $user->user_login, $user->user_pass, $user->user_email );

			if ( ! $user_id ) {
				return new GC_Error(
					'rest_user_create',
					__( '创建新用户时出错。' ),
					array( 'status' => 500 )
				);
			}

			$user->ID = $user_id;
			$user_id  = gc_update_user( gc_slash( (array) $user ) );

			if ( is_gc_error( $user_id ) ) {
				return $user_id;
			}

			$result = add_user_to_blog( get_site()->id, $user_id, '' );
			if ( is_gc_error( $result ) ) {
				return $result;
			}
		} else {
			$user_id = gc_insert_user( gc_slash( (array) $user ) );

			if ( is_gc_error( $user_id ) ) {
				return $user_id;
			}
		}

		$user = get_user_by( 'id', $user_id );

		/**
		 * Fires immediately after a user is created or updated via the REST API.
		 *
		 *
		 * @param GC_User         $user     Inserted or updated user object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a user, false when updating.
		 */
		do_action( 'rest_insert_user', $user, $request, true );

		if ( ! empty( $request['roles'] ) && ! empty( $schema['properties']['roles'] ) ) {
			array_map( array( $user, 'add_role' ), $request['roles'] );
		}

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $user_id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$user          = get_user_by( 'id', $user_id );
		$fields_update = $this->update_additional_fields_for_object( $user, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/**
		 * Fires after a user is completely created or updated via the REST API.
		 *
		 *
		 * @param GC_User         $user     Inserted or updated user object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a user, false when updating.
		 */
		do_action( 'rest_after_insert_user', $user, $request, true );

		$response = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $user_id ) ) );

		return $response;
	}

	/**
	 * Checks if a given request has access to update a user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$user = $this->get_user( $request['id'] );
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! empty( $request['roles'] ) ) {
			if ( ! current_user_can( 'promote_user', $user->ID ) ) {
				return new GC_Error(
					'rest_cannot_edit_roles',
					__( '抱歉，您不能编辑该用户的角色。' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}

			$request_params = array_keys( $request->get_params() );
			sort( $request_params );
			// If only 'id' and 'roles' are specified (we are only trying to
			// edit roles), then only the 'promote_user' cap is required.
			if ( array( 'id', 'roles' ) === $request_params ) {
				return true;
			}
		}

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return new GC_Error(
				'rest_cannot_edit',
				__( '抱歉，您不能编辑此用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates a single user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$user = $this->get_user( $request['id'] );
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$id = $user->ID;

		if ( ! $user ) {
			return new GC_Error(
				'rest_user_invalid_id',
				__( '用户ID无效。' ),
				array( 'status' => 404 )
			);
		}

		$owner_id = email_exists( $request['email'] );

		if ( $owner_id && $owner_id !== $id ) {
			return new GC_Error(
				'rest_user_invalid_email',
				__( '电子邮箱无效。' ),
				array( 'status' => 400 )
			);
		}

		if ( ! empty( $request['username'] ) && $request['username'] !== $user->user_login ) {
			return new GC_Error(
				'rest_user_invalid_argument',
				__( "用户名不能被编辑。" ),
				array( 'status' => 400 )
			);
		}

		if ( ! empty( $request['slug'] ) && $request['slug'] !== $user->user_nicename && get_user_by( 'slug', $request['slug'] ) ) {
			return new GC_Error(
				'rest_user_invalid_slug',
				__( '无效的别名。' ),
				array( 'status' => 400 )
			);
		}

		if ( ! empty( $request['roles'] ) ) {
			$check_permission = $this->check_role_update( $id, $request['roles'] );

			if ( is_gc_error( $check_permission ) ) {
				return $check_permission;
			}
		}

		$user = $this->prepare_item_for_database( $request );

		// Ensure we're operating on the same user we already checked.
		$user->ID = $id;

		$user_id = gc_update_user( gc_slash( (array) $user ) );

		if ( is_gc_error( $user_id ) ) {
			return $user_id;
		}

		$user = get_user_by( 'id', $user_id );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-users-controller.php */
		do_action( 'rest_insert_user', $user, $request, false );

		if ( ! empty( $request['roles'] ) ) {
			array_map( array( $user, 'add_role' ), $request['roles'] );
		}

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$user          = get_user_by( 'id', $user_id );
		$fields_update = $this->update_additional_fields_for_object( $user, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-users-controller.php */
		do_action( 'rest_after_insert_user', $user, $request, false );

		$response = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Checks if a given request has access to update the current user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function update_current_item_permissions_check( $request ) {
		$request['id'] = get_current_user_id();

		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Updates the current user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_current_item( $request ) {
		$request['id'] = get_current_user_id();

		return $this->update_item( $request );
	}

	/**
	 * Checks if a given request has access delete a user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$user = $this->get_user( $request['id'] );
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'delete_user', $user->ID ) ) {
			return new GC_Error(
				'rest_user_cannot_delete',
				__( '抱歉，您不能删除这个用户。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		// We don't support delete requests in multisite.
		if ( is_multisite() ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '此用户不能被删除。' ),
				array( 'status' => 501 )
			);
		}

		$user = $this->get_user( $request['id'] );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$id       = $user->ID;
		$reassign = false === $request['reassign'] ? null : absint( $request['reassign'] );
		$force    = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for users.
		if ( ! $force ) {
			return new GC_Error(
				'rest_trash_not_supported',
				/* translators: %s: force=true */
				sprintf( __( "用户不能被移动到回收站，设置“%s”来删除。" ), 'force=true' ),
				array( 'status' => 501 )
			);
		}

		if ( ! empty( $reassign ) ) {
			if ( $reassign === $id || ! get_userdata( $reassign ) ) {
				return new GC_Error(
					'rest_user_invalid_reassign',
					__( '重新指定了无效的用户ID。' ),
					array( 'status' => 400 )
				);
			}
		}

		$request->set_param( 'context', 'edit' );

		$previous = $this->prepare_item_for_response( $user, $request );

		// Include user admin functions to get access to gc_delete_user().
		require_once ABSPATH . 'gc-admin/includes/user.php';

		$result = gc_delete_user( $id, $reassign );

		if ( ! $result ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '此用户不能被删除。' ),
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

		/**
		 * Fires immediately after a user is deleted via the REST API.
		 *
		 *
		 * @param GC_User          $user     The user data.
		 * @param GC_REST_Response $response The response returned from the API.
		 * @param GC_REST_Request  $request  The request sent to the API.
		 */
		do_action( 'rest_delete_user', $user, $response, $request );

		return $response;
	}

	/**
	 * Checks if a given request has access to delete the current user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_current_item_permissions_check( $request ) {
		$request['id'] = get_current_user_id();

		return $this->delete_item_permissions_check( $request );
	}

	/**
	 * Deletes the current user.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_current_item( $request ) {
		$request['id'] = get_current_user_id();

		return $this->delete_item( $request );
	}

	/**
	 * Prepares a single user output for response.
	 *
	 *
	 * @param GC_User         $item    User object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$user   = $item;
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = $user->ID;
		}

		if ( in_array( 'username', $fields, true ) ) {
			$data['username'] = $user->user_login;
		}

		if ( in_array( 'name', $fields, true ) ) {
			$data['name'] = $user->display_name;
		}

		if ( in_array( 'first_name', $fields, true ) ) {
			$data['first_name'] = $user->first_name;
		}

		if ( in_array( 'last_name', $fields, true ) ) {
			$data['last_name'] = $user->last_name;
		}

		if ( in_array( 'email', $fields, true ) ) {
			$data['email'] = $user->user_email;
		}

		if ( in_array( 'url', $fields, true ) ) {
			$data['url'] = $user->user_url;
		}

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = $user->description;
		}

		if ( in_array( 'link', $fields, true ) ) {
			$data['link'] = get_author_posts_url( $user->ID, $user->user_nicename );
		}

		if ( in_array( 'locale', $fields, true ) ) {
			$data['locale'] = get_user_locale( $user );
		}

		if ( in_array( 'nickname', $fields, true ) ) {
			$data['nickname'] = $user->nickname;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $user->user_nicename;
		}

		if ( in_array( 'roles', $fields, true ) ) {
			// Defensively call array_values() to ensure an array is returned.
			$data['roles'] = array_values( $user->roles );
		}

		if ( in_array( 'registered_date', $fields, true ) ) {
			$data['registered_date'] = gmdate( 'c', strtotime( $user->user_registered ) );
		}

		if ( in_array( 'capabilities', $fields, true ) ) {
			$data['capabilities'] = (object) $user->allcaps;
		}

		if ( in_array( 'extra_capabilities', $fields, true ) ) {
			$data['extra_capabilities'] = (object) $user->caps;
		}

		if ( in_array( 'avatar_urls', $fields, true ) ) {
			$data['avatar_urls'] = rest_get_avatar_urls( $user );
		}

		if ( in_array( 'meta', $fields, true ) ) {
			$data['meta'] = $this->meta->get_value( $user->ID, $request );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'embed';

		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $user ) );

		/**
		 * Filters user data returned from the REST API.
		 *
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_User          $user     User object used to create response.
		 * @param GC_REST_Request  $request  Request object.
		 */
		return apply_filters( 'rest_prepare_user', $response, $user, $request );
	}

	/**
	 * Prepares links for the user request.
	 *
	 *
	 * @param GC_User $user User object.
	 * @return array Links for the given user.
	 */
	protected function prepare_links( $user ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $user->ID ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Prepares a single user for creation or update.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return object User object.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_user = new stdClass;

		$schema = $this->get_item_schema();

		// Required arguments.
		if ( isset( $request['email'] ) && ! empty( $schema['properties']['email'] ) ) {
			$prepared_user->user_email = $request['email'];
		}

		if ( isset( $request['username'] ) && ! empty( $schema['properties']['username'] ) ) {
			$prepared_user->user_login = $request['username'];
		}

		if ( isset( $request['password'] ) && ! empty( $schema['properties']['password'] ) ) {
			$prepared_user->user_pass = $request['password'];
		}

		// Optional arguments.
		if ( isset( $request['id'] ) ) {
			$prepared_user->ID = absint( $request['id'] );
		}

		if ( isset( $request['name'] ) && ! empty( $schema['properties']['name'] ) ) {
			$prepared_user->display_name = $request['name'];
		}

		if ( isset( $request['first_name'] ) && ! empty( $schema['properties']['first_name'] ) ) {
			$prepared_user->first_name = $request['first_name'];
		}

		if ( isset( $request['last_name'] ) && ! empty( $schema['properties']['last_name'] ) ) {
			$prepared_user->last_name = $request['last_name'];
		}

		if ( isset( $request['nickname'] ) && ! empty( $schema['properties']['nickname'] ) ) {
			$prepared_user->nickname = $request['nickname'];
		}

		if ( isset( $request['slug'] ) && ! empty( $schema['properties']['slug'] ) ) {
			$prepared_user->user_nicename = $request['slug'];
		}

		if ( isset( $request['description'] ) && ! empty( $schema['properties']['description'] ) ) {
			$prepared_user->description = $request['description'];
		}

		if ( isset( $request['url'] ) && ! empty( $schema['properties']['url'] ) ) {
			$prepared_user->user_url = $request['url'];
		}

		if ( isset( $request['locale'] ) && ! empty( $schema['properties']['locale'] ) ) {
			$prepared_user->locale = $request['locale'];
		}

		// Setting roles will be handled outside of this function.
		if ( isset( $request['roles'] ) ) {
			$prepared_user->role = false;
		}

		/**
		 * Filters user data before insertion via the REST API.
		 *
		 *
		 * @param object          $prepared_user User object.
		 * @param GC_REST_Request $request       Request object.
		 */
		return apply_filters( 'rest_pre_insert_user', $prepared_user, $request );
	}

	/**
	 * Determines if the current user is allowed to make the desired roles change.
	 *
	 *
	 * @global GC_Roles $gc_roles GeChiUI role management object.
	 *
	 * @param int   $user_id User ID.
	 * @param array $roles   New user roles.
	 * @return true|GC_Error True if the current user is allowed to make the role change,
	 *                       otherwise a GC_Error object.
	 */
	protected function check_role_update( $user_id, $roles ) {
		global $gc_roles;

		foreach ( $roles as $role ) {

			if ( ! isset( $gc_roles->role_objects[ $role ] ) ) {
				return new GC_Error(
					'rest_user_invalid_role',
					/* translators: %s: Role key. */
					sprintf( __( '角色%s不存在。' ), $role ),
					array( 'status' => 400 )
				);
			}

			$potential_role = $gc_roles->role_objects[ $role ];

			/*
			 * Don't let anyone with 'edit_users' (admins) edit their own role to something without it.
			 * Multisite super admins can freely edit their blog roles -- they possess all caps.
			 */
			if ( ! ( is_multisite()
				&& current_user_can( 'manage_sites' ) )
				&& get_current_user_id() === $user_id
				&& ! $potential_role->has_cap( 'edit_users' )
			) {
				return new GC_Error(
					'rest_user_invalid_role',
					__( '抱歉，您不能将此角色给予用户。' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}

			// Include user admin functions to get access to get_editable_roles().
			require_once ABSPATH . 'gc-admin/includes/user.php';

			// The new role must be editable by the logged-in user.
			$editable_roles = get_editable_roles();

			if ( empty( $editable_roles[ $role ] ) ) {
				return new GC_Error(
					'rest_user_invalid_role',
					__( '抱歉，您不能将此角色给予用户。' ),
					array( 'status' => 403 )
				);
			}
		}

		return true;
	}

	/**
	 * Check a username for the REST API.
	 *
	 * Performs a couple of checks like edit_user() in gc-admin/includes/user.php.
	 *
	 *
	 * @param string          $value   The username submitted in the request.
	 * @param GC_REST_Request $request Full details about the request.
	 * @param string          $param   The parameter name.
	 * @return string|GC_Error The sanitized username, if valid, otherwise an error.
	 */
	public function check_username( $value, $request, $param ) {
		$username = (string) $value;

		if ( ! validate_username( $username ) ) {
			return new GC_Error(
				'rest_user_invalid_username',
				__( '此用户名无效，因为它使用了非法字符。请输入有效的用户名。' ),
				array( 'status' => 400 )
			);
		}

		/** This filter is documented in gc-includes/user.php */
		$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

		if ( in_array( strtolower( $username ), array_map( 'strtolower', $illegal_logins ), true ) ) {
			return new GC_Error(
				'rest_user_invalid_username',
				__( '抱歉，该用户名不可用。' ),
				array( 'status' => 400 )
			);
		}

		return $username;
	}

	/**
	 * Check a user password for the REST API.
	 *
	 * Performs a couple of checks like edit_user() in gc-admin/includes/user.php.
	 *
	 *
	 * @param string          $value   The password submitted in the request.
	 * @param GC_REST_Request $request Full details about the request.
	 * @param string          $param   The parameter name.
	 * @return string|GC_Error The sanitized password, if valid, otherwise an error.
	 */
	public function check_user_password( $value, $request, $param ) {
		$password = (string) $value;

		if ( empty( $password ) ) {
			return new GC_Error(
				'rest_user_invalid_password',
				__( '密码不能为空。' ),
				array( 'status' => 400 )
			);
		}

		if ( false !== strpos( $password, '\\' ) ) {
			return new GC_Error(
				'rest_user_invalid_password',
				sprintf(
					/* translators: %s: The '\' character. */
					__( '密码不能包含“%s”字符。' ),
					'\\'
				),
				array( 'status' => 400 )
			);
		}

		return $password;
	}

	/**
	 * Retrieves the user's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'user',
			'type'       => 'object',
			'properties' => array(
				'id'                 => array(
					'description' => __( '用户的唯一标识符。' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'username'           => array(
					'description' => __( '用户的登录名。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'check_username' ),
					),
				),
				'name'               => array(
					'description' => __( '用户的显示名。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'first_name'         => array(
					'description' => __( '用户的名字。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'last_name'          => array(
					'description' => __( '用户的姓氏。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'email'              => array(
					'description' => __( '用户的电子邮箱。' ),
					'type'        => 'string',
					'format'      => 'email',
					'context'     => array( 'edit' ),
					'required'    => true,
				),
				'url'                => array(
					'description' => __( '用户的URL。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'description'        => array(
					'description' => __( '用户的描述。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'link'               => array(
					'description' => __( '用户的作者URL。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'locale'             => array(
					'description' => __( '用户的地区语言。' ),
					'type'        => 'string',
					'enum'        => array_merge( array( '', 'zh_CN' ), get_available_languages() ),
					'context'     => array( 'edit' ),
				),
				'nickname'           => array(
					'description' => __( '用户的昵称。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'slug'               => array(
					'description' => __( '用户的英数字标识符。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'sanitize_slug' ),
					),
				),
				'registered_date'    => array(
					'description' => __( '用户的注册日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'roles'              => array(
					'description' => __( '用户被赋予的角色。' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'edit' ),
				),
				'password'           => array(
					'description' => __( '用户的密码（从不被包含）。' ),
					'type'        => 'string',
					'context'     => array(), // Password is never displayed.
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'check_user_password' ),
					),
				),
				'capabilities'       => array(
					'description' => __( '用户所有的权限。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'extra_capabilities' => array(
					'description' => __( '用户包含的额外权限。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
			),
		);

		if ( get_option( 'show_avatars' ) ) {
			$avatar_properties = array();

			$avatar_sizes = rest_get_avatar_sizes();

			foreach ( $avatar_sizes as $size ) {
				$avatar_properties[ $size ] = array(
					/* translators: %d: Avatar image size in pixels. */
					'description' => sprintf( __( '头像URL，图片尺寸%d像素。' ), $size ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
				);
			}

			$schema['properties']['avatar_urls'] = array(
				'description' => __( '用户的头像URL。' ),
				'type'        => 'object',
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
				'properties'  => $avatar_properties,
			);
		}

		$schema['properties']['meta'] = $this->meta->get_field_schema();

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

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
			'default'     => 'asc',
			'description' => __( '设置排序字段升序或降序。' ),
			'enum'        => array( 'asc', 'desc' ),
			'type'        => 'string',
		);

		$query_params['orderby'] = array(
			'default'     => 'name',
			'description' => __( '按用户属性对集合进行排序。' ),
			'enum'        => array(
				'id',
				'include',
				'name',
				'registered_date',
				'slug',
				'include_slugs',
				'email',
				'url',
			),
			'type'        => 'string',
		);

		$query_params['slug'] = array(
			'description' => __( '将结果集限制为具有一个或多个别名的用户。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		$query_params['roles'] = array(
			'description' => __( '将结果集限制为匹配至少一个角色的用户，接受.csv格式列表或单个角色。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		$query_params['capabilities'] = array(
			'description' => __( '将结果集限制为匹配至少一项提供的特定功能的用户。接受 csv 列表或单个功能。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		$query_params['who'] = array(
			'description' => __( '将结果集限制为用户中的所有作者。' ),
			'type'        => 'string',
			'enum'        => array(
				'authors',
			),
		);

		$query_params['has_published_posts'] = array(
			'description' => __( '将结果限制于已发布文章的用户。' ),
			'type'        => array( 'boolean', 'array' ),
			'items'       => array(
				'type' => 'string',
				'enum' => get_post_types( array( 'show_in_rest' => true ), 'names' ),
			),
		);

		/**
		 * Filters REST API collection parameters for the users controller.
		 *
		 * This filter registers the collection parameter, but does not map the
		 * collection parameter to an internal GC_User_Query parameter.  Use the
		 * `rest_user_query` filter to set GC_User_Query arguments.
		 *
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_user_collection_params', $query_params );
	}
}
