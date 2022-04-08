<?php
/**
 * REST API: GC_REST_Global_Styles_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Base Global Styles REST API Controller.
 */
class GC_REST_Global_Styles_Controller extends GC_REST_Controller {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'global-styles';
		$this->post_type = 'gc_global_styles';
	}

	/**
	 * Registers the controllers routes.
	 *
	 *
	 * @return void
	 */
	public function register_routes() {
		// List themes global styles.
		register_rest_route(
			$this->namespace,
			// The route.
			sprintf(
				'/%s/themes/(?P<stylesheet>%s)',
				$this->rest_base,
				// Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
				// Excludes invalid directory name characters: `/:<>*?"|`.
				'[^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?'
			),
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_theme_item' ),
					'permission_callback' => array( $this, 'get_theme_item_permissions_check' ),
					'args'                => array(
						'stylesheet' => array(
							'description'       => __( '主题标识符' ),
							'type'              => 'string',
							'sanitize_callback' => array( $this, '_sanitize_global_styles_callback' ),
						),
					),
				),
			)
		);

		// Lists/updates a single global style variation based on the given id.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\/\w-]+)',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'id' => array(
							'description'       => __( '该模板的ID' ),
							'type'              => 'string',
							'sanitize_callback' => array( $this, '_sanitize_global_styles_callback' ),
						),
					),
				),
				array(
					'methods'             => GC_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Sanitize the global styles ID or stylesheet to decode endpoint.
	 * For example, `gc/v2/global-styles/defaultbird%200.4.0`
	 * would be decoded to `defaultbird 0.4.0`.
	 *
	 *
	 * @param string $id_or_stylesheet Global styles ID or stylesheet.
	 * @return string Sanitized global styles ID or stylesheet.
	 */
	public function _sanitize_global_styles_callback( $id_or_stylesheet ) {
		return urldecode( $id_or_stylesheet );
	}

	/**
	 * Checks if a given request has access to read a single global style.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您无权编辑此全局样式。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->check_read_permission( $post ) ) {
			return new GC_Error(
				'rest_cannot_view',
				__( '抱歉，您无权查看此全局样式。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a global style can be read.
	 *
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be read.
	 */
	protected function check_read_permission( $post ) {
		return current_user_can( 'read_post', $post->ID );
	}

	/**
	 * Returns the given global styles config.
	 *
	 *
	 * @param GC_REST_Request $request The request instance.
	 *
	 * @return GC_REST_Response|GC_Error
	 */
	public function get_item( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		return $this->prepare_item_for_response( $post, $request );
	}

	/**
	 * Checks if a given request has access to write a single global styles config.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has write access for the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_gc_error( $post ) ) {
			return $post;
		}

		if ( $post && ! $this->check_update_permission( $post ) ) {
			return new GC_Error(
				'rest_cannot_edit',
				__( '抱歉，您无权编辑此全局样式。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a global style can be edited.
	 *
	 *
	 * @param GC_Post $post Post object.
	 * @return bool Whether the post can be edited.
	 */
	protected function check_update_permission( $post ) {
		return current_user_can( 'edit_post', $post->ID );
	}

	/**
	 * Updates a single global style config.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$post_before = $this->get_post( $request['id'] );
		if ( is_gc_error( $post_before ) ) {
			return $post_before;
		}

		$changes = $this->prepare_item_for_database( $request );
		$result  = gc_update_post( gc_slash( (array) $changes ), true, false );
		if ( is_gc_error( $result ) ) {
			return $result;
		}

		$post          = get_post( $request['id'] );
		$fields_update = $this->update_additional_fields_for_object( $post, $request );
		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		gc_after_insert_post( $post, true, $post_before );

		$response = $this->prepare_item_for_response( $post, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Prepares a single global styles config for update.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return stdClass Changes to pass to gc_update_post.
	 */
	protected function prepare_item_for_database( $request ) {
		$changes     = new stdClass();
		$changes->ID = $request['id'];

		$post            = get_post( $request['id'] );
		$existing_config = array();
		if ( $post ) {
			$existing_config     = json_decode( $post->post_content, true );
			$json_decoding_error = json_last_error();
			if ( JSON_ERROR_NONE !== $json_decoding_error || ! isset( $existing_config['isGlobalStylesUserThemeJSON'] ) ||
				! $existing_config['isGlobalStylesUserThemeJSON'] ) {
				$existing_config = array();
			}
		}

		if ( isset( $request['styles'] ) || isset( $request['settings'] ) ) {
			$config = array();
			if ( isset( $request['styles'] ) ) {
				$config['styles'] = $request['styles'];
			} elseif ( isset( $existing_config['styles'] ) ) {
				$config['styles'] = $existing_config['styles'];
			}
			if ( isset( $request['settings'] ) ) {
				$config['settings'] = $request['settings'];
			} elseif ( isset( $existing_config['settings'] ) ) {
				$config['settings'] = $existing_config['settings'];
			}
			$config['isGlobalStylesUserThemeJSON'] = true;
			$config['version']                     = GC_Theme_JSON::LATEST_SCHEMA;
			$changes->post_content                 = gc_json_encode( $config );
		}

		// Post title.
		if ( isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$changes->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$changes->post_title = $request['title']['raw'];
			}
		}

		return $changes;
	}

	/**
	 * Prepare a global styles config output for response.
	 *
	 *
	 * @param GC_Post         $post    Global Styles post object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$raw_config                       = json_decode( $post->post_content, true );
		$is_global_styles_user_theme_json = isset( $raw_config['isGlobalStylesUserThemeJSON'] ) && true === $raw_config['isGlobalStylesUserThemeJSON'];
		$config                           = array();
		if ( $is_global_styles_user_theme_json ) {
			$config = ( new GC_Theme_JSON( $raw_config, 'custom' ) )->get_raw_data();
		}

		// Base fields for every post.
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = $post->ID;
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

		if ( rest_is_field_included( 'settings', $fields ) ) {
			$data['settings'] = ! empty( $config['settings'] ) && $is_global_styles_user_theme_json ? $config['settings'] : new stdClass();
		}

		if ( rest_is_field_included( 'styles', $fields ) ) {
			$data['styles'] = ! empty( $config['styles'] ) && $is_global_styles_user_theme_json ? $config['styles'] : new stdClass();
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$links = $this->prepare_links( $post->ID );
		$response->add_links( $links );
		if ( ! empty( $links['self']['href'] ) ) {
			$actions = $this->get_available_actions();
			$self    = $links['self']['href'];
			foreach ( $actions as $rel ) {
				$response->add_link( $rel, $self );
			}
		}

		return $response;
	}

	/**
	 * Get the post, if the ID is valid.
	 *
	 *
	 * @param int $id Supplied ID.
	 * @return GC_Post|GC_Error Post object if ID is valid, GC_Error otherwise.
	 */
	protected function get_post( $id ) {
		$error = new GC_Error(
			'rest_global_styles_not_found',
			__( '不存在具有该 ID 的全局样式配置。' ),
			array( 'status' => 404 )
		);

		$id = (int) $id;
		if ( $id <= 0 ) {
			return $error;
		}

		$post = get_post( $id );
		if ( empty( $post ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
			return $error;
		}

		return $post;
	}


	/**
	 * Prepares links for the request.
	 *
	 *
	 * @param integer $id ID.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $id ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self' => array(
				'href' => rest_url( trailingslashit( $base ) . $id ),
			),
		);

		return $links;
	}

	/**
	 * Get the link relations available for the post and current user.
	 *
	 *
	 * @return array List of link relations.
	 */
	protected function get_available_actions() {
		$rels = array();

		$post_type = get_post_type_object( $this->post_type );
		if ( current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		return $rels;
	}

	/**
	 * Overwrites the default protected title format.
	 *
	 * By default, GeChiUI will show password protected posts with a title of
	 * "密码保护：%s", as the REST API communicates the protected status of a post
	 * in a machine readable format, we remove the "Protected: " prefix.
	 *
	 *
	 * @return string Protected title format.
	 */
	public function protected_title_format() {
		return '%s';
	}

	/**
	 * Retrieves the query params for the global styles collection.
	 *
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array();
	}

	/**
	 * Retrieves the global styles type' schema, conforming to JSON Schema.
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
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'       => array(
					'description' => __( '全局样式配置的 ID。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'styles'   => array(
					'description' => __( '全局样式。' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'settings' => array(
					'description' => __( '全局设置。' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'title'    => array(
					'description' => __( '全局样式变体的标题。' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( '全局样式变体的标题，因为它存在于数据库中。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'rendered' => array(
							'description' => __( '文章的 HTML 标题，经转换后用于显示。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Checks if a given request has access to read a single theme global styles config.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_theme_item_permissions_check( $request ) {
		// Verify if the current user has edit_theme_options capability.
		// This capability is required to edit/view/delete templates.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new GC_Error(
				'rest_cannot_manage_global_styles',
				__( '抱歉，您无权访问本站的全局样式。' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Returns the given theme global styles config.
	 *
	 *
	 * @param GC_REST_Request $request The request instance.
	 * @return GC_REST_Response|GC_Error
	 */
	public function get_theme_item( $request ) {
		if ( gc_get_theme()->get_stylesheet() !== $request['stylesheet'] ) {
			// This endpoint only supports the active theme for now.
			return new GC_Error(
				'rest_theme_not_found',
				__( '未找到主题。' ),
				array( 'status' => 404 )
			);
		}

		$theme  = GC_Theme_JSON_Resolver::get_merged_data( 'theme' );
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( rest_is_field_included( 'settings', $fields ) ) {
			$data['settings'] = $theme->get_settings();
		}

		if ( rest_is_field_included( 'styles', $fields ) ) {
			$raw_data = $theme->get_raw_data();
			if ( isset( $raw_data['styles'] ) ) {
				$data['styles'] = $raw_data['styles'];
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$links = array(
			'self' => array(
				'href' => rest_url( sprintf( '%s/%s/themes/%s', $this->namespace, $this->rest_base, $request['stylesheet'] ) ),
			),
		);

		$response->add_links( $links );

		return $response;
	}
}
