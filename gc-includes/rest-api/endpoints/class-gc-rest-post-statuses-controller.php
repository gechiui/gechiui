<?php
/**
 * REST API: GC_REST_Post_Statuses_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to access post statuses via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Post_Statuses_Controller extends GC_REST_Controller {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'statuses';
	}

	/**
	 * Registers the routes for post statuses.
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
			'/' . $this->rest_base . '/(?P<status>[\w-]+)',
			array(
				'args'   => array(
					'status' => array(
						'description' => __( '状态的英数字标识符。' ),
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
	 * Checks whether a given request has permission to read post statuses.
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
				__( '抱歉，您不能管理文章状态。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all post statuses, depending on user context.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$data              = array();
		$statuses          = get_post_stati( array( 'internal' => false ), 'object' );
		$statuses['trash'] = get_post_status_object( 'trash' );

		foreach ( $statuses as $slug => $obj ) {
			$ret = $this->check_read_permission( $obj );

			if ( ! $ret ) {
				continue;
			}

			$status             = $this->prepare_item_for_response( $obj, $request );
			$data[ $obj->name ] = $this->prepare_response_for_collection( $status );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read a post status.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$status = get_post_status_object( $request['status'] );

		if ( empty( $status ) ) {
			return new GC_Error(
				'rest_status_invalid',
				__( '无效状态。' ),
				array( 'status' => 404 )
			);
		}

		$check = $this->check_read_permission( $status );

		if ( ! $check ) {
			return new GC_Error(
				'rest_cannot_read_status',
				__( '不能查看状态。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks whether a given post status should be visible.
	 *
	 *
	 * @param object $status Post status.
	 * @return bool True if the post status is visible, otherwise false.
	 */
	protected function check_read_permission( $status ) {
		if ( true === $status->public ) {
			return true;
		}

		if ( false === $status->internal || 'trash' === $status->name ) {
			$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

			foreach ( $types as $type ) {
				if ( current_user_can( $type->cap->edit_posts ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Retrieves a specific post status.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$obj = get_post_status_object( $request['status'] );

		if ( empty( $obj ) ) {
			return new GC_Error(
				'rest_status_invalid',
				__( '无效状态。' ),
				array( 'status' => 404 )
			);
		}

		$data = $this->prepare_item_for_response( $obj, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a post status object for serialization.
	 *
	 *
	 * @param stdClass        $item    Post status data.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response Post status data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$status = $item;
		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'name', $fields, true ) ) {
			$data['name'] = $status->label;
		}

		if ( in_array( 'private', $fields, true ) ) {
			$data['private'] = (bool) $status->private;
		}

		if ( in_array( 'protected', $fields, true ) ) {
			$data['protected'] = (bool) $status->protected;
		}

		if ( in_array( 'public', $fields, true ) ) {
			$data['public'] = (bool) $status->public;
		}

		if ( in_array( 'queryable', $fields, true ) ) {
			$data['queryable'] = (bool) $status->publicly_queryable;
		}

		if ( in_array( 'show_in_list', $fields, true ) ) {
			$data['show_in_list'] = (bool) $status->show_in_admin_all_list;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $status->name;
		}

		if ( in_array( 'date_floating', $fields, true ) ) {
			$data['date_floating'] = $status->date_floating;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$rest_url = rest_url( rest_get_route_for_post_type_items( 'post' ) );
		if ( 'publish' === $status->name ) {
			$response->add_link( 'archives', $rest_url );
		} else {
			$response->add_link( 'archives', add_query_arg( 'status', $status->name, $rest_url ) );
		}

		/**
		 * Filters a post status returned from the REST API.
		 *
		 * Allows modification of the status data right before it is returned.
		 *
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param object           $status   The original post status object.
		 * @param GC_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_status', $response, $status, $request );
	}

	/**
	 * Retrieves the post status' schema, conforming to JSON Schema.
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
			'title'      => 'status',
			'type'       => 'object',
			'properties' => array(
				'name'          => array(
					'description' => __( '状态标题。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'private'       => array(
					'description' => __( '有此状态的文章是否应为私人的。' ),
					'type'        => 'boolean',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'protected'     => array(
					'description' => __( '有此状态的文章是否应为受保护的。' ),
					'type'        => 'boolean',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'public'        => array(
					'description' => __( '此状态的文章是否应于前端显示。' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'queryable'     => array(
					'description' => __( '有此状态的文章是否可被公开查询。' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'show_in_list'  => array(
					'description' => __( '是否在编辑文章类型时包含文章列表。' ),
					'type'        => 'boolean',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'slug'          => array(
					'description' => __( '状态的英数字标识符。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_floating' => array(
					'description' => __( '有此状态的文章可否设置浮动发布日期。' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
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
