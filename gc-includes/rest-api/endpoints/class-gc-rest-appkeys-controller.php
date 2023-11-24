<?php
/**
 * REST API: GC_REST_AppKeys_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 * @since      5.6.0
 */

/**
 * Core class to access a user's application passwords via the REST API.
 *
 * @see   GC_REST_Controller
 */
class GC_REST_AppKeys_Controller extends GC_REST_Controller {

	/**
	 * Application Passwords controller constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'users/(?P<user_id>(?:[\d]+|me))/appkeys';
	}

	/**
	 * Registers the REST API routes for the application passwords controller.
	 *
	 * @since 5.6.0
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
					'args'                => $this->get_endpoint_args_for_item_schema(),
				),
				array(
					'methods'             => GC_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_items' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/introspect',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_current_item' ),
					'permission_callback' => array( $this, 'get_current_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<uuid>[\w\-]+)',
			array(
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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to get application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'list_app_passwords', $user->ID ) ) {
			return new GC_Error(
				'rest_cannot_list_appkeys',
				__( '抱歉，您不能查看该用户的所有Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a collection of application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$passwords = GC_AppKeys::get_user_appkeys( $user->ID );
		$response  = array();

		foreach ( $passwords as $password ) {
			$response[] = $this->prepare_response_for_collection(
				$this->prepare_item_for_response( $password, $request )
			);
		}

		return new GC_REST_Response( $response );
	}

	/**
	 * Checks if a given request has access to get a specific application password.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'read_app_password', $user->ID, $request['uuid'] ) ) {
			return new GC_Error(
				'rest_cannot_read_appkey',
				__( '抱歉，您不能查看此Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves one application password from the collection.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$password = $this->get_appkey( $request );

		if ( is_gc_error( $password ) ) {
			return $password;
		}

		return $this->prepare_item_for_response( $password, $request );
	}

	/**
	 * Checks if a given request has access to create application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'create_app_password', $user->ID ) ) {
			return new GC_Error(
				'rest_cannot_create_appkeys',
				__( '抱歉，您不能为该用户创建Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$prepared = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $prepared ) ) {
			return $prepared;
		}

		$created = GC_AppKeys::create_new_appkey( $user->ID, gc_slash( (array) $prepared ) );

		if ( is_gc_error( $created ) ) {
			return $created;
		}

		$password = $created[0];
		$item     = GC_AppKeys::get_user_appkey( $user->ID, $created[1]['uuid'] );

		$item['new_password'] = GC_AppKeys::chunk_password( $password );
		$fields_update        = $this->update_additional_fields_for_object( $item, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		/**
		 * Fires after a single application password is completely created or updated via the REST API.
		 *
		 * @since 5.6.0
		 *
		 * @param array           $item     Inserted or updated password item.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating an application password, false when updating.
		 */
		do_action( 'rest_after_insert_appkey', $item, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $item, $request );

		$response->set_status( 201 );
		$response->header( 'Location', $response->get_links()['self'][0]['href'] );

		return $response;
	}

	/**
	 * Checks if a given request has access to update application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'edit_app_password', $user->ID, $request['uuid'] ) ) {
			return new GC_Error(
				'rest_cannot_edit_appkey',
				__( '抱歉，您不能修改此Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$item = $this->get_appkey( $request );

		if ( is_gc_error( $item ) ) {
			return $item;
		}

		$prepared = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $prepared ) ) {
			return $prepared;
		}

		$saved = GC_AppKeys::update_appkey( $user->ID, $item['uuid'], gc_slash( (array) $prepared ) );

		if ( is_gc_error( $saved ) ) {
			return $saved;
		}

		$fields_update = $this->update_additional_fields_for_object( $item, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$item = GC_AppKeys::get_user_appkey( $user->ID, $item['uuid'] );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-appkeys-controller.php */
		do_action( 'rest_after_insert_appkey', $item, $request, false );

		$request->set_param( 'context', 'edit' );
		return $this->prepare_item_for_response( $item, $request );
	}

	/**
	 * Checks if a given request has access to delete all application passwords for a user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_items_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'delete_app_passwords', $user->ID ) ) {
			return new GC_Error(
				'rest_cannot_delete_appkeys',
				__( '抱歉，您不能为该用户删除Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes all application passwords for a user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_items( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$deleted = GC_AppKeys::delete_all_appkeys( $user->ID );

		if ( is_gc_error( $deleted ) ) {
			return $deleted;
		}

		return new GC_REST_Response(
			array(
				'deleted' => true,
				'count'   => $deleted,
			)
		);
	}

	/**
	 * Checks if a given request has access to delete a specific application password for a user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'delete_app_password', $user->ID, $request['uuid'] ) ) {
			return new GC_Error(
				'rest_cannot_delete_appkey',
				__( '抱歉，您不能删除此Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes an application password for a user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$password = $this->get_appkey( $request );

		if ( is_gc_error( $password ) ) {
			return $password;
		}

		$request->set_param( 'context', 'edit' );
		$previous = $this->prepare_item_for_response( $password, $request );
		$deleted  = GC_AppKeys::delete_appkey( $user->ID, $password['uuid'] );

		if ( is_gc_error( $deleted ) ) {
			return $deleted;
		}

		return new GC_REST_Response(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);
	}

	/**
	 * Checks if a given request has access to get the currently used application password for a user.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_current_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( get_current_user_id() !== $user->ID ) {
			return new GC_Error(
				'rest_cannot_introspect_app_password_for_non_authenticated_user',
				__( '经身份验证的Appkey只能由当前用户进行检验。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves the application password being currently used for authentication of a user.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_current_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$uuid = rest_get_authenticated_app_password();

		if ( ! $uuid ) {
			return new GC_Error(
				'rest_no_authenticated_app_password',
				__( '无法检验Appkey。' ),
				array( 'status' => 404 )
			);
		}

		$password = GC_AppKeys::get_user_appkey( $user->ID, $uuid );

		if ( ! $password ) {
			return new GC_Error(
				'rest_appkey_not_found',
				__( '找不到Appkey。' ),
				array( 'status' => 500 )
			);
		}

		return $this->prepare_item_for_response( $password, $request );
	}

	/**
	 * Performs a permissions check for the request.
	 *
	 * @since 5.6.0
	 * @deprecated 5.7.0 Use `edit_user` directly or one of the specific meta capabilities introduced in 5.7.0.
	 *
	 * @param GC_REST_Request $request
	 * @return true|GC_Error
	 */
	protected function do_permissions_check( $request ) {
		_deprecated_function( __METHOD__, '5.7.0' );

		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return new GC_Error(
				'rest_cannot_manage_appkeys',
				__( '抱歉，您不能为此用户管理Appkey。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Prepares an application password for a create or update operation.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return object|GC_Error The prepared item, or GC_Error object on failure.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared = (object) array(
			'name' => $request['name'],
		);

		if ( $request['app_id'] && ! $request['uuid'] ) {
			$prepared->app_id = $request['app_id'];
		}

		/**
		 * Filters an application password before it is inserted via the REST API.
		 *
		 * @since 5.6.0
		 *
		 * @param stdClass        $prepared An object representing a single application password prepared for inserting or updating the database.
		 * @param GC_REST_Request $request  Request object.
		 */
		return apply_filters( 'rest_pre_insert_appkey', $prepared, $request );
	}

	/**
	 * Prepares the application password for the REST response.
	 *
	 * @since 5.6.0
	 *
	 * @param array           $item    GeChiUI representation of the item.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$fields = $this->get_fields_for_response( $request );

		$prepared = array(
			'uuid'      => $item['uuid'],
			'app_id'    => empty( $item['app_id'] ) ? '' : $item['app_id'],
			'name'      => $item['name'],
			'created'   => gmdate( 'Y-m-d\TH:i:s', $item['created'] ),
			'last_used' => $item['last_used'] ? gmdate( 'Y-m-d\TH:i:s', $item['last_used'] ) : null,
			'last_ip'   => $item['last_ip'] ? $item['last_ip'] : null,
		);

		if ( isset( $item['new_password'] ) ) {
			$prepared['password'] = $item['new_password'];
		}

		$prepared = $this->add_additional_fields_to_object( $prepared, $request );
		$prepared = $this->filter_response_by_context( $prepared, $request['context'] );

		$response = new GC_REST_Response( $prepared );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $user, $item ) );
		}

		/**
		 * Filters the REST API response for an application password.
		 *
		 * @since 5.6.0
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param array            $item     The application password array.
		 * @param GC_REST_Request  $request  The request object.
		 */
		return apply_filters( 'rest_prepare_appkey', $response, $item, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_User $user The requested user.
	 * @param array   $item The application password.
	 * @return array The list of links.
	 */
	protected function prepare_links( GC_User $user, $item ) {
		return array(
			'self' => array(
				'href' => rest_url(
					sprintf(
						'%s/users/%d/appkeys/%s',
						$this->namespace,
						$user->ID,
						$item['uuid']
					)
				),
			),
		);
	}

	/**
	 * Gets the requested user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request The request object.
	 * @return GC_User|GC_Error The GeChiUI user associated with the request, or a GC_Error if none found.
	 */
	protected function get_user( $request ) {
		if ( ! gc_is_appkeys_available() ) {
			return new GC_Error(
				'appkeys_disabled',
				__( 'Appkey不可用。' ),
				array( 'status' => 501 )
			);
		}

		$error = new GC_Error(
			'rest_user_invalid_id',
			__( '用户ID无效。' ),
			array( 'status' => 404 )
		);

		$id = $request['user_id'];

		if ( 'me' === $id ) {
			if ( ! is_user_logged_in() ) {
				return new GC_Error(
					'rest_not_logged_in',
					__( '您目前没有登录。' ),
					array( 'status' => 401 )
				);
			}

			$user = gc_get_current_user();
		} else {
			$id = (int) $id;

			if ( $id <= 0 ) {
				return $error;
			}

			$user = get_userdata( $id );
		}

		if ( empty( $user ) || ! $user->exists() ) {
			return $error;
		}

		if ( is_multisite() && ! user_can( $user->ID, 'manage_sites' ) && ! is_user_member_of_blog( $user->ID ) ) {
			return $error;
		}

		if ( ! gc_is_appkeys_available_for_user( $user ) ) {
			return new GC_Error(
				'appkeys_disabled_for_user',
				__( '您的账户无法使用Appkey。请联系系统管理员以获得协助。' ),
				array( 'status' => 501 )
			);
		}

		return $user;
	}

	/**
	 * Gets the requested application password for a user.
	 *
	 * @since 5.6.0
	 *
	 * @param GC_REST_Request $request The request object.
	 * @return array|GC_Error The application password details if found, a GC_Error otherwise.
	 */
	protected function get_appkey( $request ) {
		$user = $this->get_user( $request );

		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$password = GC_AppKeys::get_user_appkey( $user->ID, $request['uuid'] );

		if ( ! $password ) {
			return new GC_Error(
				'rest_appkey_not_found',
				__( '找不到Appkey。' ),
				array( 'status' => 404 )
			);
		}

		return $password;
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 5.6.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

	/**
	 * Retrieves the application password's schema, conforming to JSON Schema.
	 *
	 * @since 5.6.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'appkey',
			'type'       => 'object',
			'properties' => array(
				'uuid'      => array(
					'description' => __( '该Appkey的唯一标识符。' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'app_id'    => array(
					'description' => __( '由应用程序提供的用于唯一识别的 UUID。建议使用含有 URL 或 DNS 命名空间的 UUID v5。' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'name'      => array(
					'description' => __( 'Appkey名称。' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
					'minLength'   => 1,
					'pattern'     => '.*\S.*',
				),
				'password'  => array(
					'description' => __( '生成的密码。仅在加入应用程序后可用。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'created'   => array(
					'description' => __( '创建该Appkey的GMT日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'last_used' => array(
					'description' => __( '上次使用该Appkey的GMT日期。' ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'last_ip'   => array(
					'description' => __( '上次使用该Appkey的IP地址。' ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'ip',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}
}
