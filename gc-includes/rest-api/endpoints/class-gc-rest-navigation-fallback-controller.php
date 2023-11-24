<?php
/**
 * GC_REST_Navigation_Fallback_Controller class
 *
 * REST Controller to create/fetch a fallback Navigation Menu.
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 6.3.0
 */

/**
 * REST Controller to fetch a fallback Navigation Block Menu. If needed it creates one.
 *
 * @since 6.3.0
 */
class GC_REST_Navigation_Fallback_Controller extends GC_REST_Controller {

	/**
	 * The Post Type for the Controller
	 *
	 * @since 6.3.0
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * Constructs the controller.
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		$this->namespace = 'gc-block-editor/v1';
		$this->rest_base = 'navigation-fallback';
		$this->post_type = 'gc_navigation';
	}

	/**
	 * Registers the controllers routes.
	 *
	 * @since 6.3.0
	 *
	 * @return void
	 */
	public function register_routes() {

		// Lists a single nav item based on the given id or slug.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::READABLE ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read fallbacks.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {

		$post_type = get_post_type_object( $this->post_type );

		// Getting fallbacks requires creating and reading `gc_navigation` posts.
		if ( ! current_user_can( $post_type->cap->create_posts ) || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_posts' ) ) {
			return new GC_Error(
				'rest_cannot_create',
				__( '很抱歉，您不允许以此用户身份创建导航菜单。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '很抱歉，您不允许以此用户身份编辑导航菜单。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Gets the most appropriate fallback Navigation Menu.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$post = GC_Navigation_Fallback::get_fallback();

		if ( empty( $post ) ) {
			return rest_ensure_response( new GC_Error( 'no_fallback_menu', __( '未找到回退菜单。' ), array( 'status' => 404 ) ) );
		}

		$response = $this->prepare_item_for_response( $post, $request );

		return $response;
	}

	/**
	 * Retrieves the fallbacks' schema, conforming to JSON Schema.
	 *
	 * @since 6.3.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'navigation-fallback',
			'type'       => 'object',
			'properties' => array(
				'id' => array(
					'description' => __( '导航菜单的唯一标识符。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_Post         $item    The gc_navigation Post object whose response is being prepared.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response $response The response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array();

		$fields = $this->get_fields_for_response( $request );

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = (int) $item->ID;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = $this->prepare_links( $item );
			$response->add_links( $links );
		}

		return $response;
	}

	/**
	 * Prepares the links for the request.
	 *
	 * @since 6.3.0
	 *
	 * @param GC_Post $post the Navigation Menu post object.
	 * @return array Links for the given request.
	 */
	private function prepare_links( $post ) {
		return array(
			'self' => array(
				'href'       => rest_url( rest_get_route_for_post( $post->ID ) ),
				'embeddable' => true,
			),
		);
	}
}
