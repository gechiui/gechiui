<?php
/**
 * Block Renderer REST API: GC_REST_Block_Renderer_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Controller which provides REST endpoint for rendering a block.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Block_Renderer_Controller extends GC_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'block-renderer';
	}

	/**
	 * Registers the necessary REST API routes, one for each dynamic block.
	 *
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<name>[a-z0-9-]+/[a-z0-9-]+)',
			array(
				'args'   => array(
					'name' => array(
						'description' => __( '此区块的唯一注册名。' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => array( GC_REST_Server::READABLE, GC_REST_Server::CREATABLE ),
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context'    => $this->get_context_param( array( 'default' => 'view' ) ),
						'attributes' => array(
							'description'       => __( '此区块的属性。' ),
							'type'              => 'object',
							'default'           => array(),
							'validate_callback' => static function ( $value, $request ) {
								$block = GC_Block_Type_Registry::get_instance()->get_registered( $request['name'] );

								if ( ! $block ) {
									// This will get rejected in ::get_item().
									return true;
								}

								$schema = array(
									'type'                 => 'object',
									'properties'           => $block->get_attributes(),
									'additionalProperties' => false,
								);

								return rest_validate_value_from_schema( $value, $schema );
							},
							'sanitize_callback' => static function ( $value, $request ) {
								$block = GC_Block_Type_Registry::get_instance()->get_registered( $request['name'] );

								if ( ! $block ) {
									// This will get rejected in ::get_item().
									return true;
								}

								$schema = array(
									'type'                 => 'object',
									'properties'           => $block->get_attributes(),
									'additionalProperties' => false,
								);

								return rest_sanitize_value_from_schema( $value, $schema );
							},
						),
						'post_id'    => array(
							'description' => __( '文章上下文的ID。' ),
							'type'        => 'integer',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read blocks.
	 *
	 *
	 * @global GC_Post $post Global post object.
	 *
	 * @param GC_REST_Request $request Request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		global $post;

		$post_id = isset( $request['post_id'] ) ? (int) $request['post_id'] : 0;

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );

			if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
				return new GC_Error(
					'block_cannot_read',
					__( '抱歉，您不能阅读此文章的区块。' ),
					array(
						'status' => rest_authorization_required_code(),
					)
				);
			}
		} else {
			if ( ! current_user_can( 'edit_posts' ) ) {
				return new GC_Error(
					'block_cannot_read',
					__( '抱歉，您不能作为此用户阅读区块。' ),
					array(
						'status' => rest_authorization_required_code(),
					)
				);
			}
		}

		return true;
	}

	/**
	 * Returns block output from block's registered render_callback.
	 *
	 *
	 * @global GC_Post $post Global post object.
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		global $post;

		$post_id = isset( $request['post_id'] ) ? (int) $request['post_id'] : 0;

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );

			// Set up postdata since this will be needed if post_id was set.
			setup_postdata( $post );
		}

		$registry   = GC_Block_Type_Registry::get_instance();
		$registered = $registry->get_registered( $request['name'] );

		if ( null === $registered || ! $registered->is_dynamic() ) {
			return new GC_Error(
				'block_invalid',
				__( '无效区块。' ),
				array(
					'status' => 404,
				)
			);
		}

		$attributes = $request->get_param( 'attributes' );

		// Create an array representation simulating the output of parse_blocks.
		$block = array(
			'blockName'    => $request['name'],
			'attrs'        => $attributes,
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		// Render using render_block to ensure all relevant filters are used.
		$data = array(
			'rendered' => render_block( $block ),
		);

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves block's output schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => 'rendered-block',
			'type'       => 'object',
			'properties' => array(
				'rendered' => array(
					'description' => __( '渲染的区块。' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'edit' ),
				),
			),
		);

		return $this->schema;
	}
}
