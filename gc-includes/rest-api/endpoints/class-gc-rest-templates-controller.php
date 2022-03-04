<?php
/**
 * REST API: GC_REST_Templates_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Base Templates REST API Controller.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Templates_Controller extends GC_REST_Controller {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
		$obj             = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;
		$this->namespace = ! empty( $obj->rest_namespace ) ? $obj->rest_namespace : 'gc/v2';
	}

	/**
	 * Registers the controllers routes.
	 *
	 */
	public function register_routes() {
		// Lists all templates.
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

		// Lists/updates a single template based on the given id.
		register_rest_route(
			$this->namespace,
			// The route.
			sprintf(
				'/%s/(?P<id>%s%s)',
				$this->rest_base,
				// Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
				// Excludes invalid directory name characters: `/:<>*?"|`.
				'([^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?)',
				// Matches the template name.
				'[\/\w-]+'
			),
			array(
				'args'   => array(
					'id' => array(
						'description'       => __( '该模板的ID' ),
						'type'              => 'string',
						'sanitize_callback' => array( $this, '_sanitize_template_id' ),
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
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( '是否绕过回收站并强行删除。' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if the user has permissions to make the request.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	protected function permissions_check( $request ) {
		// Verify if the current user has edit_theme_options capability.
		// This capability is required to edit/view/delete templates.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new GC_Error(
				'rest_cannot_manage_templates',
				__( '抱歉，您无法访问此站点的模板。' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Requesting this endpoint for a template like 'defaultbird//home'
	 * requires using a path like /gc/v2/templates/defaultbird//home. There
	 * are special cases when GeChiUI routing corrects the name to contain
	 * only a single slash like 'defaultbird/home'.
	 *
	 * This method doubles the last slash if it's not already doubled. It relies
	 * on the template ID format {theme_name}//{template_slug} and the fact that
	 * slugs cannot contain slashes.
	 *
	 * @see https://core.trac.gechiui.com/ticket/54507
	 *
	 * @param string $id Template ID.
	 * @return string Sanitized template ID.
	 */
	public function _sanitize_template_id( $id ) {
		$id = urldecode( $id );

		$last_slash_pos = strrpos( $id, '/' );
		if ( false === $last_slash_pos ) {
			return $id;
		}

		$is_double_slashed = substr( $id, $last_slash_pos - 1, 1 ) === '/';
		if ( $is_double_slashed ) {
			return $id;
		}
		return (
			substr( $id, 0, $last_slash_pos )
			. '/'
			. substr( $id, $last_slash_pos )
		);
	}

	/**
	 * Checks if a given request has access to read templates.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Returns a list of templates.
	 *
	 *
	 * @param GC_REST_Request $request The request instance.
	 * @return GC_REST_Response
	 */
	public function get_items( $request ) {
		$query = array();
		if ( isset( $request['gc_id'] ) ) {
			$query['gc_id'] = $request['gc_id'];
		}
		if ( isset( $request['area'] ) ) {
			$query['area'] = $request['area'];
		}
		if ( isset( $request['post_type'] ) ) {
			$query['post_type'] = $request['post_type'];
		}

		$templates = array();
		foreach ( get_block_templates( $query, $this->post_type ) as $template ) {
			$data        = $this->prepare_item_for_response( $template, $request );
			$templates[] = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $templates );
	}

	/**
	 * Checks if a given request has access to read a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Returns the given template
	 *
	 *
	 * @param GC_REST_Request $request The request instance.
	 * @return GC_REST_Response|GC_Error
	 */
	public function get_item( $request ) {
		if ( isset( $request['source'] ) && 'theme' === $request['source'] ) {
			$template = get_block_file_template( $request['id'], $this->post_type );
		} else {
			$template = get_block_template( $request['id'], $this->post_type );
		}

		if ( ! $template ) {
			return new GC_Error( 'rest_template_not_found', __( '不存在具有该id的模板。' ), array( 'status' => 404 ) );
		}

		return $this->prepare_item_for_response( $template, $request );
	}

	/**
	 * Checks if a given request has access to write a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has write access for the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Updates a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$template = get_block_template( $request['id'], $this->post_type );
		if ( ! $template ) {
			return new GC_Error( 'rest_template_not_found', __( '不存在具有该id的模板。' ), array( 'status' => 404 ) );
		}

		$post_before = get_post( $template->gc_id );

		if ( isset( $request['source'] ) && 'theme' === $request['source'] ) {
			gc_delete_post( $template->gc_id, true );
			$request->set_param( 'context', 'edit' );

			$template = get_block_template( $request['id'], $this->post_type );
			$response = $this->prepare_item_for_response( $template, $request );

			return rest_ensure_response( $response );
		}

		$changes = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $changes ) ) {
			return $changes;
		}

		if ( 'custom' === $template->source ) {
			$update = true;
			$result = gc_update_post( gc_slash( (array) $changes ), false );
		} else {
			$update      = false;
			$post_before = null;
			$result      = gc_insert_post( gc_slash( (array) $changes ), false );
		}

		if ( is_gc_error( $result ) ) {
			if ( 'db_update_error' === $result->get_error_code() ) {
				$result->add_data( array( 'status' => 500 ) );
			} else {
				$result->add_data( array( 'status' => 400 ) );
			}
			return $result;
		}

		$template      = get_block_template( $request['id'], $this->post_type );
		$fields_update = $this->update_additional_fields_for_object( $template, $request );
		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		$post = get_post( $template->gc_id );
		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, false );

		gc_after_insert_post( $post, $update, $post_before );

		$response = $this->prepare_item_for_response( $template, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to create a template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Creates a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		$prepared_post = $this->prepare_item_for_database( $request );

		if ( is_gc_error( $prepared_post ) ) {
			return $prepared_post;
		}

		$prepared_post->post_name = $request['slug'];
		$post_id                  = gc_insert_post( gc_slash( (array) $prepared_post ), true );
		if ( is_gc_error( $post_id ) ) {
			if ( 'db_insert_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}

			return $post_id;
		}
		$posts = get_block_templates( array( 'gc_id' => $post_id ), $this->post_type );
		if ( ! count( $posts ) ) {
			return new GC_Error( 'rest_template_insert_error', __( '不存在具有该id的模板。' ), array( 'status' => 400 ) );
		}
		$id            = $posts[0]->id;
		$post          = get_post( $post_id );
		$template      = get_block_template( $id, $this->post_type );
		$fields_update = $this->update_additional_fields_for_object( $template, $request );
		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-posts-controller.php */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, true );

		gc_after_insert_post( $post, false, null );

		$response = $this->prepare_item_for_response( $template, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $template->id ) ) );

		return $response;
	}

	/**
	 * Checks if a given request has access to delete a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has delete access for the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Deletes a single template.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		$template = get_block_template( $request['id'], $this->post_type );
		if ( ! $template ) {
			return new GC_Error( 'rest_template_not_found', __( '不存在具有该id的模板。' ), array( 'status' => 404 ) );
		}
		if ( 'custom' !== $template->source ) {
			return new GC_Error( 'rest_invalid_template', __( '无法移除基于主题文件的模板。' ), array( 'status' => 400 ) );
		}

		$id    = $template->gc_id;
		$force = (bool) $request['force'];

		$request->set_param( 'context', 'edit' );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			$previous = $this->prepare_item_for_response( $template, $request );
			$result   = gc_delete_post( $id, true );
			$response = new GC_REST_Response();
			$response->set_data(
				array(
					'deleted'  => true,
					'previous' => $previous->get_data(),
				)
			);
		} else {
			// Otherwise, only trash if we haven't already.
			if ( 'trash' === $template->status ) {
				return new GC_Error(
					'rest_template_already_trashed',
					__( '该模板已经被删除。' ),
					array( 'status' => 410 )
				);
			}

			// (Note that internally this falls through to `gc_delete_post()`
			// if the Trash is disabled.)
			$result           = gc_trash_post( $id );
			$template->status = 'trash';
			$response         = $this->prepare_item_for_response( $template, $request );
		}

		if ( ! $result ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '无法删除该模板。' ),
				array( 'status' => 500 )
			);
		}

		return $response;
	}

	/**
	 * Prepares a single template for create or update.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return stdClass Changes to pass to gc_update_post.
	 */
	protected function prepare_item_for_database( $request ) {
		$template = $request['id'] ? get_block_template( $request['id'], $this->post_type ) : null;
		$changes  = new stdClass();
		if ( null === $template ) {
			$changes->post_type   = $this->post_type;
			$changes->post_status = 'publish';
			$changes->tax_input   = array(
				'gc_theme' => isset( $request['theme'] ) ? $request['theme'] : gc_get_theme()->get_stylesheet(),
			);
		} elseif ( 'custom' !== $template->source ) {
			$changes->post_name   = $template->slug;
			$changes->post_type   = $this->post_type;
			$changes->post_status = 'publish';
			$changes->tax_input   = array(
				'gc_theme' => $template->theme,
			);
			$changes->meta_input  = array(
				'origin' => $template->source,
			);
		} else {
			$changes->post_name   = $template->slug;
			$changes->ID          = $template->gc_id;
			$changes->post_status = 'publish';
		}
		if ( isset( $request['content'] ) ) {
			if ( is_string( $request['content'] ) ) {
				$changes->post_content = $request['content'];
			} elseif ( isset( $request['content']['raw'] ) ) {
				$changes->post_content = $request['content']['raw'];
			}
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_content = $template->content;
		}
		if ( isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$changes->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$changes->post_title = $request['title']['raw'];
			}
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_title = $template->title;
		}
		if ( isset( $request['description'] ) ) {
			$changes->post_excerpt = $request['description'];
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_excerpt = $template->description;
		}

		if ( 'gc_template_part' === $this->post_type ) {
			if ( isset( $request['area'] ) ) {
				$changes->tax_input['gc_template_part_area'] = _filter_block_template_part_area( $request['area'] );
			} elseif ( null !== $template && 'custom' !== $template->source && $template->area ) {
				$changes->tax_input['gc_template_part_area'] = _filter_block_template_part_area( $template->area );
			} elseif ( ! $template->area ) {
				$changes->tax_input['gc_template_part_area'] = GC_TEMPLATE_PART_AREA_UNCATEGORIZED;
			}
		}

		if ( ! empty( $request['author'] ) ) {
			$post_author = (int) $request['author'];

			if ( get_current_user_id() !== $post_author ) {
				$user_obj = get_userdata( $post_author );

				if ( ! $user_obj ) {
					return new GC_Error(
						'rest_invalid_author',
						__( '作者ID无效。' ),
						array( 'status' => 400 )
					);
				}
			}

			$changes->post_author = $post_author;
		}

		return $changes;
	}

	/**
	 * Prepare a single template output for response
	 *
	 *
	 * @param GC_Block_Template $item    Template instance.
	 * @param GC_REST_Request   $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// Restores the more descriptive, specific name for use within this method.
		$template = $item;

		$fields = $this->get_fields_for_response( $request );

		// Base fields for every template.
		$data = array();

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = $template->id;
		}

		if ( rest_is_field_included( 'theme', $fields ) ) {
			$data['theme'] = $template->theme;
		}

		if ( rest_is_field_included( 'content', $fields ) ) {
			$data['content'] = array();
		}
		if ( rest_is_field_included( 'content.raw', $fields ) ) {
			$data['content']['raw'] = $template->content;
		}

		if ( rest_is_field_included( 'content.block_version', $fields ) ) {
			$data['content']['block_version'] = block_version( $template->content );
		}

		if ( rest_is_field_included( 'slug', $fields ) ) {
			$data['slug'] = $template->slug;
		}

		if ( rest_is_field_included( 'source', $fields ) ) {
			$data['source'] = $template->source;
		}

		if ( rest_is_field_included( 'origin', $fields ) ) {
			$data['origin'] = $template->origin;
		}

		if ( rest_is_field_included( 'type', $fields ) ) {
			$data['type'] = $template->type;
		}

		if ( rest_is_field_included( 'description', $fields ) ) {
			$data['description'] = $template->description;
		}

		if ( rest_is_field_included( 'title', $fields ) ) {
			$data['title'] = array();
		}

		if ( rest_is_field_included( 'title.raw', $fields ) ) {
			$data['title']['raw'] = $template->title;
		}

		if ( rest_is_field_included( 'title.rendered', $fields ) ) {
			if ( $template->gc_id ) {
				/** This filter is documented in gc-includes/post-template.php */
				$data['title']['rendered'] = apply_filters( 'the_title', $template->title, $template->gc_id );
			} else {
				$data['title']['rendered'] = $template->title;
			}
		}

		if ( rest_is_field_included( 'status', $fields ) ) {
			$data['status'] = $template->status;
		}

		if ( rest_is_field_included( 'gc_id', $fields ) ) {
			$data['gc_id'] = (int) $template->gc_id;
		}

		if ( rest_is_field_included( 'has_theme_file', $fields ) ) {
			$data['has_theme_file'] = (bool) $template->has_theme_file;
		}

		if ( rest_is_field_included( 'is_custom', $fields ) && 'gc_template' === $template->type ) {
			$data['is_custom'] = $template->is_custom;
		}

		if ( rest_is_field_included( 'author', $fields ) ) {
			$data['author'] = (int) $template->author;
		}

		if ( rest_is_field_included( 'area', $fields ) && 'gc_template_part' === $template->type ) {
			$data['area'] = $template->area;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$links = $this->prepare_links( $template->id );
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
	 * Prepares links for the request.
	 *
	 *
	 * @param integer $id ID.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $id ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
			'about'      => array(
				'href' => rest_url( 'gc/v2/types/' . $this->post_type ),
			),
		);

		return $links;
	}

	/**
	 * Get the link relations available for the post and current user.
	 *
	 *
	 * @return string[] List of link relations.
	 */
	protected function get_available_actions() {
		$rels = array();

		$post_type = get_post_type_object( $this->post_type );

		if ( current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		if ( current_user_can( 'unfiltered_html' ) ) {
			$rels[] = 'https://api.w.org/action-unfiltered-html';
		}

		return $rels;
	}

	/**
	 * Retrieves the query params for the posts collection.
	 *
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context'   => $this->get_context_param( array( 'default' => 'view' ) ),
			'gc_id'     => array(
				'description' => __( '限制为指定的文章 id。' ),
				'type'        => 'integer',
			),
			'area'      => array(
				'description' => __( '限制为指定的模板组件区。' ),
				'type'        => 'string',
			),
			'post_type' => array(
				'description' => __( '要获取模板的文章类型。' ),
				'type'        => 'string',
			),
		);
	}

	/**
	 * Retrieves the block type' schema, conforming to JSON Schema.
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
				'id'             => array(
					'description' => __( '模板的ID。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'slug'           => array(
					'description' => __( '标识模板的唯一别名。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'minLength'   => 1,
					'pattern'     => '[a-zA-Z0-9_\-]+',
				),
				'theme'          => array(
					'description' => __( '模板的主题标识符。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'type'           => array(
					'description' => __( '模板的类型。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'source'         => array(
					'description' => __( '模板来源' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'origin'         => array(
					'description' => __( '自定义模板源' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'content'        => array(
					'description' => __( '模板的内容。' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'           => array(
							'description' => __( '模板的内容，因为它存在于数据库中。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'block_version' => array(
							'description' => __( '模板使用的内容块格式的版本。' ),
							'type'        => 'integer',
							'context'     => array( 'edit' ),
							'readonly'    => true,
						),
					),
				),
				'title'          => array(
					'description' => __( '模板的标题。' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( '模板的标题，因为它存在于数据库中。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'rendered' => array(
							'description' => __( '模板的 HTML 标题，已转换以供显示。' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
				'description'    => array(
					'description' => __( '模板的描述。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'status'         => array(
					'description' => __( '模板的状态。' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'default'     => 'publish',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'gc_id'          => array(
					'description' => __( '文章ID。' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'has_theme_file' => array(
					'description' => __( '主题文件存在。' ),
					'type'        => 'bool',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'author'         => array(
					'description' => __( '模板作者的 ID。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		if ( 'gc_template' === $this->post_type ) {
			$schema['properties']['is_custom'] = array(
				'description' => __( '模板是否为自定义模板。' ),
				'type'        => 'bool',
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			);
		}

		if ( 'gc_template_part' === $this->post_type ) {
			$schema['properties']['area'] = array(
				'description' => __( '模板组件用途（页眉、页脚等）。' ),
				'type'        => 'string',
				'context'     => array( 'embed', 'view', 'edit' ),
			);
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
