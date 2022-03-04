<?php
/**
 * REST API: GC_REST_Post_Types_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class to access post types via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Post_Types_Controller extends GC_REST_Controller {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'types';
	}

	/**
	 * Registers the routes for post types.
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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<type>[\w-]+)',
			array(
				'args'   => array(
					'type' => array(
						'description' => __( '文章类型的英数字标识符。' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read types.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( 'edit' === $request['context'] ) {
			$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

			foreach ( $types as $type ) {
				if ( current_user_can( $type->cap->edit_posts ) ) {
					return true;
				}
			}

			return new GC_Error(
				'rest_cannot_view',
				__( '抱歉，您不能在此文章类型中编辑文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all public post types.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$data  = array();
		$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

		foreach ( $types as $type ) {
			if ( 'edit' === $request['context'] && ! current_user_can( $type->cap->edit_posts ) ) {
				continue;
			}

			$post_type           = $this->prepare_item_for_response( $type, $request );
			$data[ $type->name ] = $this->prepare_response_for_collection( $post_type );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a specific post type.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$obj = get_post_type_object( $request['type'] );

		if ( empty( $obj ) ) {
			return new GC_Error(
				'rest_type_invalid',
				__( '无效的文章类型。' ),
				array( 'status' => 404 )
			);
		}

		if ( empty( $obj->show_in_rest ) ) {
			return new GC_Error(
				'rest_cannot_read_type',
				__( '不能预览文章类型。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'edit' === $request['context'] && ! current_user_can( $obj->cap->edit_posts ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能在此文章类型中编辑文章。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$data = $this->prepare_item_for_response( $obj, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a post type object for serialization.
	 *
	 *
	 * @param GC_Post_Type    $item    Post type object.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post_type  = $item;
		$taxonomies = gc_list_filter( get_object_taxonomies( $post_type->name, 'objects' ), array( 'show_in_rest' => true ) );
		$taxonomies = gc_list_pluck( $taxonomies, 'name' );
		$base       = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;
		$namespace  = ! empty( $post_type->rest_namespace ) ? $post_type->rest_namespace : 'gc/v2';
		$supports   = get_all_post_type_supports( $post_type->name );

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'capabilities', $fields, true ) ) {
			$data['capabilities'] = $post_type->cap;
		}

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = $post_type->description;
		}

		if ( in_array( 'hierarchical', $fields, true ) ) {
			$data['hierarchical'] = $post_type->hierarchical;
		}

		if ( in_array( 'visibility', $fields, true ) ) {
			$data['visibility'] = array(
				'show_in_nav_menus' => (bool) $post_type->show_in_nav_menus,
				'show_ui'           => (bool) $post_type->show_ui,
			);
		}

		if ( in_array( 'viewable', $fields, true ) ) {
			$data['viewable'] = is_post_type_viewable( $post_type );
		}

		if ( in_array( 'labels', $fields, true ) ) {
			$data['labels'] = $post_type->labels;
		}

		if ( in_array( 'name', $fields, true ) ) {
			$data['name'] = $post_type->label;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $post_type->name;
		}

		if ( in_array( 'supports', $fields, true ) ) {
			$data['supports'] = $supports;
		}

		if ( in_array( 'taxonomies', $fields, true ) ) {
			$data['taxonomies'] = array_values( $taxonomies );
		}

		if ( in_array( 'rest_base', $fields, true ) ) {
			$data['rest_base'] = $base;
		}

		if ( in_array( 'rest_namespace', $fields, true ) ) {
			$data['rest_namespace'] = $namespace;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links(
			array(
				'collection'              => array(
					'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
				),
				'https://api.w.org/items' => array(
					'href' => rest_url( rest_get_route_for_post_type_items( $post_type->name ) ),
				),
			)
		);

		/**
		 * Filters a post type returned from the REST API.
		 *
		 * Allows modification of the post type data right before it is returned.
		 *
		 *
		 * @param GC_REST_Response $response  The response object.
		 * @param GC_Post_Type     $post_type The original post type object.
		 * @param GC_REST_Request  $request   Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_post_type', $response, $post_type, $request );
	}

	/**
	 * Retrieves the post type's schema, conforming to JSON Schema.
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
			'title'      => 'type',
			'type'       => 'object',
			'properties' => array(
				'capabilities'   => array(
					'description' => __( '文章类型使用的所有权限。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'description'    => array(
					'description' => __( '文章类型的人类可读描述。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'hierarchical'   => array(
					'description' => __( '该文章类型是否拥有子类型。' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'viewable'       => array(
					'description' => __( '此文章类型是否可被查看。' ),
					'type'        => 'boolean',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'labels'         => array(
					'description' => __( '文章类型不同上下文中的人类可读标签。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'name'           => array(
					'description' => __( '文章类型的标题。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'slug'           => array(
					'description' => __( '文章类型的英数字标识符。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'supports'       => array(
					'description' => __( '文章类型支持的所有功能。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'taxonomies'     => array(
					'description' => __( '与文章类型关联的分类法。' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'rest_base'      => array(
					'description' => __( '与文章类型关联的REST base路由。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'rest_namespace' => array(
					'description' => __( '文章类型的 REST 路由命名空间。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'visibility'     => array(
					'description' => __( '文章类型的可见性设置。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
					'properties'  => array(
						'show_ui'           => array(
							'description' => __( '是否生成用于管理此文章类型的默认 UI。' ),
							'type'        => 'boolean',
						),
						'show_in_nav_menus' => array(
							'description' => __( '是否使文章类型可在导航菜单中选择。' ),
							'type'        => 'boolean',
						),
					),
				),
			),
		);

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
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

}
