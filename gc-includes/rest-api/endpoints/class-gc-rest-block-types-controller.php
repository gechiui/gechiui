<?php
/**
 * REST API: GC_REST_Block_Types_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 5.5.0
 */

/**
 * Core class used to access block types via the REST API.
 *
 * @since 5.5.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Block_Types_Controller extends GC_REST_Controller {

	/**
	 * Instance of GC_Block_Type_Registry.
	 *
	 * @since 5.5.0
	 * @var GC_Block_Type_Registry
	 */
	protected $block_registry;

	/**
	 * Instance of GC_Block_Styles_Registry.
	 *
	 * @since 5.5.0
	 * @var GC_Block_Styles_Registry
	 */
	protected $style_registry;

	/**
	 * Constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->namespace      = 'gc/v2';
		$this->rest_base      = 'block-types';
		$this->block_registry = GC_Block_Type_Registry::get_instance();
		$this->style_registry = GC_Block_Styles_Registry::get_instance();
	}

	/**
	 * Registers the routes for block types.
	 *
	 * @since 5.5.0
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
			'/' . $this->rest_base . '/(?P<namespace>[a-zA-Z0-9_-]+)',
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
			'/' . $this->rest_base . '/(?P<namespace>[a-zA-Z0-9_-]+)/(?P<name>[a-zA-Z0-9_-]+)',
			array(
				'args'   => array(
					'name'      => array(
						'description' => __( '区块名称' ),
						'type'        => 'string',
					),
					'namespace' => array(
						'description' => __( '区块命名空间。' ),
						'type'        => 'string',
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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read post block types.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return $this->check_read_permission();
	}

	/**
	 * Retrieves all post block types, depending on user context.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$data        = array();
		$block_types = $this->block_registry->get_all_registered();

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$namespace  = '';
		if ( isset( $registered['namespace'] ) && ! empty( $request['namespace'] ) ) {
			$namespace = $request['namespace'];
		}

		foreach ( $block_types as $slug => $obj ) {
			if ( $namespace ) {
				list ( $block_namespace ) = explode( '/', $obj->name );

				if ( $namespace !== $block_namespace ) {
					continue;
				}
			}
			$block_type = $this->prepare_item_for_response( $obj, $request );
			$data[]     = $this->prepare_response_for_collection( $block_type );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read a block type.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$check = $this->check_read_permission();
		if ( is_gc_error( $check ) ) {
			return $check;
		}
		$block_name = sprintf( '%s/%s', $request['namespace'], $request['name'] );
		$block_type = $this->get_block( $block_name );
		if ( is_gc_error( $block_type ) ) {
			return $block_type;
		}

		return true;
	}

	/**
	 * Checks whether a given block type should be visible.
	 *
	 * @since 5.5.0
	 *
	 * @return true|GC_Error True if the block type is visible, GC_Error otherwise.
	 */
	protected function check_read_permission() {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}
		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new GC_Error( 'rest_block_type_cannot_view', __( '抱歉，您不能管理区块类型。' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * Get the block, if the name is valid.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Block name.
	 * @return GC_Block_Type|GC_Error Block type object if name is valid, GC_Error otherwise.
	 */
	protected function get_block( $name ) {
		$block_type = $this->block_registry->get_registered( $name );
		if ( empty( $block_type ) ) {
			return new GC_Error( 'rest_block_type_invalid', __( '无效的区块类型。' ), array( 'status' => 404 ) );
		}

		return $block_type;
	}

	/**
	 * Retrieves a specific block type.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$block_name = sprintf( '%s/%s', $request['namespace'], $request['name'] );
		$block_type = $this->get_block( $block_name );
		if ( is_gc_error( $block_type ) ) {
			return $block_type;
		}
		$data = $this->prepare_item_for_response( $block_type, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a block type object for serialization.
	 *
	 * @since 5.5.0
	 * @since 5.9.0 Renamed `$block_type` to `$item` to match parent class for PHP 8 named parameter support.
	 * @since 6.3.0 Added `selectors` field.
	 *
	 * @param GC_Block_Type   $item    Block type data.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response Block type data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$block_type = $item;
		$fields     = $this->get_fields_for_response( $request );
		$data       = array();

		if ( rest_is_field_included( 'attributes', $fields ) ) {
			$data['attributes'] = $block_type->get_attributes();
		}

		if ( rest_is_field_included( 'is_dynamic', $fields ) ) {
			$data['is_dynamic'] = $block_type->is_dynamic();
		}

		$schema = $this->get_item_schema();
		// Fields deprecated in GeChiUI 6.1, but left in the schema for backwards compatibility.
		$deprecated_fields = array(
			'editor_script',
			'script',
			'view_script',
			'editor_style',
			'style',
		);
		$extra_fields      = array_merge(
			array(
				'api_version',
				'name',
				'title',
				'description',
				'icon',
				'category',
				'keywords',
				'parent',
				'ancestor',
				'provides_context',
				'uses_context',
				'selectors',
				'supports',
				'styles',
				'textdomain',
				'example',
				'editor_script_handles',
				'script_handles',
				'view_script_handles',
				'editor_style_handles',
				'style_handles',
				'variations',
			),
			$deprecated_fields
		);
		foreach ( $extra_fields as $extra_field ) {
			if ( rest_is_field_included( $extra_field, $fields ) ) {
				if ( isset( $block_type->$extra_field ) ) {
					$field = $block_type->$extra_field;
					if ( in_array( $extra_field, $deprecated_fields, true ) && is_array( $field ) ) {
						// Since the schema only allows strings or null (but no arrays), we return the first array item.
						$field = ! empty( $field ) ? array_shift( $field ) : '';
					}
				} elseif ( array_key_exists( 'default', $schema['properties'][ $extra_field ] ) ) {
					$field = $schema['properties'][ $extra_field ]['default'];
				} else {
					$field = '';
				}
				$data[ $extra_field ] = rest_sanitize_value_from_schema( $field, $schema['properties'][ $extra_field ] );
			}
		}

		if ( rest_is_field_included( 'styles', $fields ) ) {
			$styles         = $this->style_registry->get_registered_styles_for_block( $block_type->name );
			$styles         = array_values( $styles );
			$data['styles'] = gc_parse_args( $styles, $data['styles'] );
			$data['styles'] = array_filter( $data['styles'] );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $block_type ) );
		}

		/**
		 * Filters a block type returned from the REST API.
		 *
		 * Allows modification of the block type data right before it is returned.
		 *
		 * @since 5.5.0
		 *
		 * @param GC_REST_Response $response   The response object.
		 * @param GC_Block_Type    $block_type The original block type object.
		 * @param GC_REST_Request  $request    Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_block_type', $response, $block_type, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_Block_Type $block_type Block type data.
	 * @return array Links for the given block type.
	 */
	protected function prepare_links( $block_type ) {
		list( $namespace ) = explode( '/', $block_type->name );

		$links = array(
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $block_type->name ) ),
			),
			'up'         => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $namespace ) ),
			),
		);

		if ( $block_type->is_dynamic() ) {
			$links['https://api.w.org/render-block'] = array(
				'href' => add_query_arg(
					'context',
					'edit',
					rest_url( sprintf( '%s/%s/%s', 'gc/v2', 'block-renderer', $block_type->name ) )
				),
			);
		}

		return $links;
	}

	/**
	 * Retrieves the block type' schema, conforming to JSON Schema.
	 *
	 * @since 5.5.0
	 * @since 6.3.0 Added `selectors` field.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		// rest_validate_value_from_schema doesn't understand $refs, pull out reused definitions for readability.
		$inner_blocks_definition = array(
			'description' => __( '此范例中使用的内部区块列表。' ),
			'type'        => 'array',
			'items'       => array(
				'type'       => 'object',
				'properties' => array(
					'name'        => array(
						'description' => __( '内部区块的名称。' ),
						'type'        => 'string',
					),
					'attributes'  => array(
						'description' => __( '内部区块的属性。' ),
						'type'        => 'object',
					),
					'innerBlocks' => array(
						'description' => __( "内部区块自身的内部区块清单。这是一个递归定义，遵循父innerBlocks的模式描述。" ),
						'type'        => 'array',
					),
				),
			),
		);

		$example_definition = array(
			'description' => __( '区块示例。' ),
			'type'        => array( 'object', 'null' ),
			'default'     => null,
			'properties'  => array(
				'attributes'  => array(
					'description' => __( '此范例中使用的属性。' ),
					'type'        => 'object',
				),
				'innerBlocks' => $inner_blocks_definition,
			),
			'context'     => array( 'embed', 'view', 'edit' ),
			'readonly'    => true,
		);

		$keywords_definition = array(
			'description' => __( '区块关键字。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
			'default'     => array(),
			'context'     => array( 'embed', 'view', 'edit' ),
			'readonly'    => true,
		);

		$icon_definition = array(
			'description' => __( '区块类型的图标。' ),
			'type'        => array( 'string', 'null' ),
			'default'     => null,
			'context'     => array( 'embed', 'view', 'edit' ),
			'readonly'    => true,
		);

		$category_definition = array(
			'description' => __( '区块分类。' ),
			'type'        => array( 'string', 'null' ),
			'default'     => null,
			'context'     => array( 'embed', 'view', 'edit' ),
			'readonly'    => true,
		);

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'block-type',
			'type'       => 'object',
			'properties' => array(
				'api_version'           => array(
					'description' => __( '区块API的版本。' ),
					'type'        => 'integer',
					'default'     => 1,
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'                 => array(
					'description' => __( '区块类型的标题。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'                  => array(
					'description' => __( '区块类型的唯一名称。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'description'           => array(
					'description' => __( '区块类型的描述。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'icon'                  => $icon_definition,
				'attributes'            => array(
					'description'          => __( '区块属性。' ),
					'type'                 => array( 'object', 'null' ),
					'properties'           => array(),
					'default'              => null,
					'additionalProperties' => array(
						'type' => 'object',
					),
					'context'              => array( 'embed', 'view', 'edit' ),
					'readonly'             => true,
				),
				'provides_context'      => array(
					'description'          => __( '此类区块所提供的上下文。' ),
					'type'                 => 'object',
					'properties'           => array(),
					'additionalProperties' => array(
						'type' => 'string',
					),
					'default'              => array(),
					'context'              => array( 'embed', 'view', 'edit' ),
					'readonly'             => true,
				),
				'uses_context'          => array(
					'description' => __( '此类区块所继承的上下文的值。' ),
					'type'        => 'array',
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'selectors'             => array(
					'description' => __( '自定义CSS选择器。' ),
					'type'        => 'object',
					'default'     => array(),
					'properties'  => array(),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'supports'              => array(
					'description' => __( '区块支持。' ),
					'type'        => 'object',
					'default'     => array(),
					'properties'  => array(),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'category'              => $category_definition,
				'is_dynamic'            => array(
					'description' => __( '区块是动态渲染的？' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'editor_script_handles' => array(
					'description' => __( '编辑器脚本句柄。' ),
					'type'        => array( 'array' ),
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'script_handles'        => array(
					'description' => __( '公开界面和编辑器脚本的句柄。' ),
					'type'        => array( 'array' ),
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'view_script_handles'   => array(
					'description' => __( '公开界面脚本的句柄。' ),
					'type'        => array( 'array' ),
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'editor_style_handles'  => array(
					'description' => __( '编辑器样式句柄。' ),
					'type'        => array( 'array' ),
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'style_handles'         => array(
					'description' => __( '公开界面和编辑器样式的句柄。' ),
					'type'        => array( 'array' ),
					'default'     => array(),
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'styles'                => array(
					'description' => __( '区块样式变体。' ),
					'type'        => 'array',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'         => array(
								'description' => __( '识别样式的唯一名称。' ),
								'type'        => 'string',
								'required'    => true,
							),
							'label'        => array(
								'description' => __( '人类可读的样式标签。' ),
								'type'        => 'string',
							),
							'inline_style' => array(
								'description' => __( '注册样式所需CSS类的内联CSS代码。' ),
								'type'        => 'string',
							),
							'style_handle' => array(
								'description' => __( '包含定义区块样式的句柄。' ),
								'type'        => 'string',
							),
						),
					),
					'default'     => array(),
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'variations'            => array(
					'description' => __( '区块变体。' ),
					'type'        => 'array',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'        => array(
								'description' => __( '唯一且机器可读的名称。' ),
								'type'        => 'string',
								'required'    => true,
							),
							'title'       => array(
								'description' => __( '易读的变体标题。' ),
								'type'        => 'string',
								'required'    => true,
							),
							'description' => array(
								'description' => __( '详细的遍体描述。' ),
								'type'        => 'string',
								'required'    => false,
							),
							'category'    => $category_definition,
							'icon'        => $icon_definition,
							'isDefault'   => array(
								'description' => __( '指明当前的变体是否为默认变体。' ),
								'type'        => 'boolean',
								'required'    => false,
								'default'     => false,
							),
							'attributes'  => array(
								'description' => __( '属性的初始值。' ),
								'type'        => 'object',
							),
							'innerBlocks' => $inner_blocks_definition,
							'example'     => $example_definition,
							'scope'       => array(
								'description' => __( '遍体适用的范围列表。如未提供，则假定所有范围可用。' ),
								'type'        => array( 'array', 'null' ),
								'default'     => null,
								'items'       => array(
									'type' => 'string',
									'enum' => array( 'block', 'inserter', 'transform' ),
								),
								'readonly'    => true,
							),
							'keywords'    => $keywords_definition,
						),
					),
					'readonly'    => true,
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => null,
				),
				'textdomain'            => array(
					'description' => __( '公共文本域。' ),
					'type'        => array( 'string', 'null' ),
					'default'     => null,
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'parent'                => array(
					'description' => __( '上级区块' ),
					'type'        => array( 'array', 'null' ),
					'items'       => array(
						'type' => 'string',
					),
					'default'     => null,
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'ancestor'              => array(
					'description' => __( '上层区块。' ),
					'type'        => array( 'array', 'null' ),
					'items'       => array(
						'type' => 'string',
					),
					'default'     => null,
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'keywords'              => $keywords_definition,
				'example'               => $example_definition,
			),
		);

		// Properties deprecated in GeChiUI 6.1, but left in the schema for backwards compatibility.
		$deprecated_properties      = array(
			'editor_script' => array(
				'description' => __( '编辑器脚本的句柄。已弃用：请使用 `editor_script_handles` 作为代替。' ),
				'type'        => array( 'string', 'null' ),
				'default'     => null,
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			),
			'script'        => array(
				'description' => __( '公开界面和编辑器脚本的句柄。已弃用：请使用 `script_handles` 作为代替。' ),
				'type'        => array( 'string', 'null' ),
				'default'     => null,
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			),
			'view_script'   => array(
				'description' => __( '公开界面脚本的句柄。已弃用：请使用 `view_script_handles` 作为代替。' ),
				'type'        => array( 'string', 'null' ),
				'default'     => null,
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			),
			'editor_style'  => array(
				'description' => __( '编辑器样式的句柄。已弃用：请使用 `editor_style_handles` 作为代替。' ),
				'type'        => array( 'string', 'null' ),
				'default'     => null,
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			),
			'style'         => array(
				'description' => __( '公开界面和编辑器样式的句柄。已弃用：请使用 `style_handles` 作为代替。' ),
				'type'        => array( 'string', 'null' ),
				'default'     => null,
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			),
		);
		$this->schema['properties'] = array_merge( $this->schema['properties'], $deprecated_properties );

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 5.5.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context'   => $this->get_context_param( array( 'default' => 'view' ) ),
			'namespace' => array(
				'description' => __( '区块命名空间。' ),
				'type'        => 'string',
			),
		);
	}

}
