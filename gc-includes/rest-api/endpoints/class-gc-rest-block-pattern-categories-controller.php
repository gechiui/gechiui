<?php
/**
 * REST API: GC_REST_Block_Pattern_Categories_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 * @since      6.0.0
 */

/**
 * Core class used to access block pattern categories via the REST API.
 *
 * @since 6.0.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Block_Pattern_Categories_Controller extends GC_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'block-patterns/categories';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 6.0.0
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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read block patterns.
	 *
	 * @since 6.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
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
			'rest_cannot_view',
			__( '抱歉，您无法查看已注册的区块样板分类。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Retrieves all block pattern categories.
	 *
	 * @since 6.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_Error|GC_REST_Response Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$response   = array();
		$categories = GC_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
		foreach ( $categories as $category ) {
			$prepared_category = $this->prepare_item_for_response( $category, $request );
			$response[]        = $this->prepare_response_for_collection( $prepared_category );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Prepare a raw block pattern category before it gets output in a REST API response.
	 *
	 * @since 6.0.0
	 *
	 * @param array           $item    Raw category as registered, before any changes.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields = $this->get_fields_for_response( $request );
		$keys   = array( 'name', 'label', 'description' );
		$data   = array();
		foreach ( $keys as $key ) {
			if ( isset( $item[ $key ] ) && rest_is_field_included( $key, $fields ) ) {
				$data[ $key ] = $item[ $key ];
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the block pattern category schema, conforming to JSON Schema.
	 *
	 * @since 6.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'block-pattern-category',
			'type'       => 'object',
			'properties' => array(
				'name'        => array(
					'description' => __( '分类名称。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'label'       => array(
					'description' => __( '采用人类可读格式显示的分类标签。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'description' => array(
					'description' => __( '采用人类可读的格式的分类描述。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
