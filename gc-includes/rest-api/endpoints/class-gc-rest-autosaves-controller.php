<?php
/**
 * REST API: GC_REST_Autosaves_Controller class.
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to access autosaves via the REST API.
 *
 *
 *
 * @see GC_REST_Revisions_Controller
 * @see GC_REST_Controller
 */
class GC_REST_Autosaves_Controller extends GC_REST_Revisions_Controller {

	/**
	 * Parent post type.
	 *
	 * @var string
	 */
	private $parent_post_type;

	/**
	 * Parent post controller.
	 *
	 * @var GC_REST_Controller
	 */
	private $parent_controller;

	/**
	 * Revision controller.
	 *
	 * @var GC_REST_Revisions_Controller
	 */
	private $revisions_controller;

	/**
	 * The base of the parent controller's route.
	 *
	 * @var string
	 */
	private $parent_base;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct( $parent_post_type ) {
		$this->parent_post_type = $parent_post_type;
		$post_type_object       = get_post_type_object( $parent_post_type );
		$parent_controller      = $post_type_object->get_rest_controller();

		if ( ! $parent_controller ) {
			$parent_controller = new GC_REST_Posts_Controller( $parent_post_type );
		}

		$this->parent_controller    = $parent_controller;
		$this->revisions_controller = new GC_REST_Revisions_Controller( $parent_post_type );
		$this->rest_base            = 'autosaves';
		$this->namespace            = ! empty( $post_type_object->rest_namespace ) ? $post_type_object->rest_namespace : 'gc/v2';
		$this->parent_base          = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
	}

	/**
	 * Registers the routes for autosaves.
	 *
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<id>[\d]+)/' . $this->rest_base,
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( '自动保存的父级ID。' ),
						'type'        => 'integer',
					),
				),
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
					'args'                => $this->parent_controller->get_endpoint_args_for_item_schema( GC_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( '自动保存的父级ID。' ),
						'type'        => 'integer',
					),
					'id'     => array(
						'description' => __( '自动保存的 ID。' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this->revisions_controller, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get the parent post.
	 *
	 *
	 * @param int $parent_id Supplied ID.
	 * @return GC_Post|GC_Error Post object if ID is valid, GC_Error otherwise.
	 */
	protected function get_parent( $parent_id ) {
		return $this->revisions_controller->get_parent( $parent_id );
	}

	/**
	 * Checks if a given request has access to get autosaves.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$parent = $this->get_parent( $request['id'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		if ( ! current_user_can( 'edit_post', $parent->ID ) ) {
			return new GC_Error(
				'rest_cannot_read',
				__( '抱歉，您不能查看此文章的自动保存。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create an autosave revision.
	 *
	 * Autosave revisions inherit permissions from the parent post,
	 * check if the current user has permission to edit the post.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create the item, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$id = $request->get_param( 'id' );

		if ( empty( $id ) ) {
			return new GC_Error(
				'rest_post_invalid_id',
				__( '无效的项目ID。' ),
				array( 'status' => 404 )
			);
		}

		return $this->parent_controller->update_item_permissions_check( $request );
	}

	/**
	 * Creates, updates or deletes an autosave revision.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {

		if ( ! defined( 'DOING_AUTOSAVE' ) ) {
			define( 'DOING_AUTOSAVE', true );
		}

		$post = get_post( $request['id'] );

		if ( is_gc_error( $post ) ) {
			return $post;
		}

		$prepared_post     = $this->parent_controller->prepare_item_for_database( $request );
		$prepared_post->ID = $post->ID;
		$user_id           = get_current_user_id();

		if ( ( 'draft' === $post->post_status || 'auto-draft' === $post->post_status ) && $post->post_author == $user_id ) {
			// Draft posts for the same author: autosaving updates the post and does not create a revision.
			// Convert the post object to an array and add slashes, gc_update_post() expects escaped array.
			$autosave_id = gc_update_post( gc_slash( (array) $prepared_post ), true );
		} else {
			// Non-draft posts: create or update the post autosave.
			$autosave_id = $this->create_post_autosave( (array) $prepared_post );
		}

		if ( is_gc_error( $autosave_id ) ) {
			return $autosave_id;
		}

		$autosave = get_post( $autosave_id );
		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $autosave, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Get the autosave, if the ID is valid.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_Post|GC_Error Revision post object if ID is valid, GC_Error otherwise.
	 */
	public function get_item( $request ) {
		$parent_id = (int) $request->get_param( 'parent' );

		if ( $parent_id <= 0 ) {
			return new GC_Error(
				'rest_post_invalid_id',
				__( '文章父 ID 无效。' ),
				array( 'status' => 404 )
			);
		}

		$autosave = gc_get_post_autosave( $parent_id );

		if ( ! $autosave ) {
			return new GC_Error(
				'rest_post_no_autosave',
				__( '没有此文章的自动保存修订版本。' ),
				array( 'status' => 404 )
			);
		}

		$response = $this->prepare_item_for_response( $autosave, $request );
		return $response;
	}

	/**
	 * Gets a collection of autosaves using gc_get_post_autosave.
	 *
	 * Contains the user's autosave, for empty if it doesn't exist.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request['id'] );
		if ( is_gc_error( $parent ) ) {
			return $parent;
		}

		$response  = array();
		$parent_id = $parent->ID;
		$revisions = gc_get_post_revisions( $parent_id, array( 'check_enabled' => false ) );

		foreach ( $revisions as $revision ) {
			if ( false !== strpos( $revision->post_name, "{$parent_id}-autosave" ) ) {
				$data       = $this->prepare_item_for_response( $revision, $request );
				$response[] = $this->prepare_response_for_collection( $data );
			}
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Retrieves the autosave's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = $this->revisions_controller->get_item_schema();

		$schema['properties']['preview_link'] = array(
			'description' => __( '此文章的预览链接。' ),
			'type'        => 'string',
			'format'      => 'uri',
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Creates autosave for the specified post.
	 *
	 * From gc-admin/post.php.
	 *
	 *
	 * @param array $post_data Associative array containing the post data.
	 * @return mixed The autosave revision ID or GC_Error.
	 */
	public function create_post_autosave( $post_data ) {

		$post_id = (int) $post_data['ID'];
		$post    = get_post( $post_id );

		if ( is_gc_error( $post ) ) {
			return $post;
		}

		$user_id = get_current_user_id();

		// Store one autosave per author. If there is already an autosave, overwrite it.
		$old_autosave = gc_get_post_autosave( $post_id, $user_id );

		if ( $old_autosave ) {
			$new_autosave                = _gc_post_revision_data( $post_data, true );
			$new_autosave['ID']          = $old_autosave->ID;
			$new_autosave['post_author'] = $user_id;

			// If the new autosave has the same content as the post, delete the autosave.
			$autosave_is_different = false;

			foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _gc_post_revision_fields( $post ) ) ) as $field ) {
				if ( normalize_whitespace( $new_autosave[ $field ] ) !== normalize_whitespace( $post->$field ) ) {
					$autosave_is_different = true;
					break;
				}
			}

			if ( ! $autosave_is_different ) {
				gc_delete_post_revision( $old_autosave->ID );
				return new GC_Error(
					'rest_autosave_no_changes',
					__( '没有什么要保存的。自动保存和当前的文章内容相同。' ),
					array( 'status' => 400 )
				);
			}

			/** This filter is documented in gc-admin/post.php */
			do_action( 'gc_creating_autosave', $new_autosave );

			// gc_update_post() expects escaped array.
			return gc_update_post( gc_slash( $new_autosave ) );
		}

		// Create the new autosave as a special post revision.
		return _gc_put_post_revision( $post_data, true );
	}

	/**
	 * Prepares the revision for the REST response.
	 *
	 *
	 * @param GC_Post         $item    Post revision object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post     = $item;
		$response = $this->revisions_controller->prepare_item_for_response( $post, $request );

		$fields = $this->get_fields_for_response( $request );

		if ( in_array( 'preview_link', $fields, true ) ) {
			$parent_id          = gc_is_post_autosave( $post );
			$preview_post_id    = false === $parent_id ? $post->ID : $parent_id;
			$preview_query_args = array();

			if ( false !== $parent_id ) {
				$preview_query_args['preview_id']    = $parent_id;
				$preview_query_args['preview_nonce'] = gc_create_nonce( 'post_preview_' . $parent_id );
			}

			$response->data['preview_link'] = get_preview_post_link( $preview_post_id, $preview_query_args );
		}

		$context        = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$response->data = $this->add_additional_fields_to_object( $response->data, $request );
		$response->data = $this->filter_response_by_context( $response->data, $context );

		/**
		 * Filters a revision returned from the REST API.
		 *
		 * Allows modification of the revision right before it is returned.
		 *
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_Post          $post     The original revision object.
		 * @param GC_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_autosave', $response, $post, $request );
	}

	/**
	 * Retrieves the query params for the autosaves collection.
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
