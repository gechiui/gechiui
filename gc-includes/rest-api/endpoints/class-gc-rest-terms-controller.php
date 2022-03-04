<?php
/**
 * REST API: GC_REST_Terms_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to managed terms associated with a taxonomy via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Terms_Controller extends GC_REST_Controller {

	/**
	 * Taxonomy key.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Instance of a term meta fields object.
	 *
	 * @var GC_REST_Term_Meta_Fields
	 */
	protected $meta;

	/**
	 * Column to have the terms be sorted by.
	 *
	 * @var string
	 */
	protected $sort_column;

	/**
	 * Number of terms that were found.
	 *
	 * @var int
	 */
	protected $total_terms;

	/**
	 * Whether the controller supports batching.
	 *
	 * @var array
	 */
	protected $allow_batch = array( 'v1' => true );

	/**
	 * Constructor.
	 *
	 *
	 * @param string $taxonomy Taxonomy key.
	 */
	public function __construct( $taxonomy ) {
		$this->taxonomy  = $taxonomy;
		$tax_obj         = get_taxonomy( $taxonomy );
		$this->rest_base = ! empty( $tax_obj->rest_base ) ? $tax_obj->rest_base : $tax_obj->name;
		$this->namespace = ! empty( $tax_obj->rest_namespace ) ? $tax_obj->rest_namespace : 'gc/v2';

		$this->meta = new GC_REST_Term_Meta_Fields( $taxonomy );
	}

	/**
	 * Registers the routes for terms.
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
				array(
					'methods'             => GC_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::CREATABLE ),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'        => array(
					'id' => array(
						'description' => __( '项目的唯一标识符。' ),
						'type'        => 'integer',
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
							'description' => __( '要求为true，因为项目不能被移动到回收站。' ),
						),
					),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a request has access to read terms in the specified taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, otherwise false or GC_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		$tax_obj = get_taxonomy( $this->taxonomy );

		if ( ! $tax_obj || ! $this->check_is_taxonomy_allowed( $this->taxonomy ) ) {
			return false;
		}

		if ( 'edit' === $request['context'] && ! current_user_can( $tax_obj->cap->edit_terms ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能编辑此分类法中的项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves terms associated with a taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal GC_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array(
			'exclude'    => 'exclude',
			'include'    => 'include',
			'order'      => 'order',
			'orderby'    => 'orderby',
			'post'       => 'post',
			'hide_empty' => 'hide_empty',
			'per_page'   => 'number',
			'search'     => 'search',
			'slug'       => 'slug',
		);

		$prepared_args = array( 'taxonomy' => $this->taxonomy );

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $prepared_args.
		 */
		foreach ( $parameter_mappings as $api_param => $gc_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $gc_param ] = $request[ $api_param ];
			}
		}

		if ( isset( $prepared_args['orderby'] ) && isset( $request['orderby'] ) ) {
			$orderby_mappings = array(
				'include_slugs' => 'slug__in',
			);

			if ( isset( $orderby_mappings[ $request['orderby'] ] ) ) {
				$prepared_args['orderby'] = $orderby_mappings[ $request['orderby'] ];
			}
		}

		if ( isset( $registered['offset'] ) && ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}

		$taxonomy_obj = get_taxonomy( $this->taxonomy );

		if ( $taxonomy_obj->hierarchical && isset( $registered['parent'], $request['parent'] ) ) {
			if ( 0 === $request['parent'] ) {
				// Only query top-level terms.
				$prepared_args['parent'] = 0;
			} else {
				if ( $request['parent'] ) {
					$prepared_args['parent'] = $request['parent'];
				}
			}
		}

		/**
		 * Filters get_terms() arguments when querying terms via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_category_query`
		 *  - `rest_post_tag_query`
		 *
		 * Enables adding extra arguments or setting defaults for a terms
		 * collection request.
		 *
		 *
		 * @link https://developer.gechiui.com/reference/functions/get_terms/
		 *
		 * @param array           $prepared_args Array of arguments for get_terms().
		 * @param GC_REST_Request $request       The REST API request.
		 */
		$prepared_args = apply_filters( "rest_{$this->taxonomy}_query", $prepared_args, $request );

		if ( ! empty( $prepared_args['post'] ) ) {
			$query_result = gc_get_object_terms( $prepared_args['post'], $this->taxonomy, $prepared_args );

			// Used when calling gc_count_terms() below.
			$prepared_args['object_ids'] = $prepared_args['post'];
		} else {
			$query_result = get_terms( $prepared_args );
		}

		$count_args = $prepared_args;

		unset( $count_args['number'], $count_args['offset'] );

		$total_terms = gc_count_terms( $count_args );

		// gc_count_terms() can return a falsey value when the term has no children.
		if ( ! $total_terms ) {
			$total_terms = 0;
		}

		$response = array();

		foreach ( $query_result as $term ) {
			$data       = $this->prepare_item_for_response( $term, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		// Store pagination values for headers.
		$per_page = (int) $prepared_args['number'];
		$page     = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		$response->header( 'X-GC-Total', (int) $total_terms );

		$max_pages = ceil( $total_terms / $per_page );

		$response->header( 'X-GC-TotalPages', (int) $max_pages );

		$base = add_query_arg( urlencode_deep( $request->get_query_params() ), rest_url( $this->namespace . '/' . $this->rest_base ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Get the term, if the ID is valid.
	 *
	 *
	 * @param int $id Supplied ID.
	 * @return GC_Term|GC_Error Term object if ID is valid, GC_Error otherwise.
	 */
	protected function get_term( $id ) {
		$error = new GC_Error(
			'rest_term_invalid',
			__( '项目不存在。' ),
			array( 'status' => 404 )
		);

		if ( ! $this->check_is_taxonomy_allowed( $this->taxonomy ) ) {
			return $error;
		}

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$term = get_term( (int) $id, $this->taxonomy );
		if ( empty( $term ) || $term->taxonomy !== $this->taxonomy ) {
			return $error;
		}

		return $term;
	}

	/**
	 * Checks if a request has access to read or edit the specified term.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, otherwise false or GC_Error object.
	 */
	public function get_item_permissions_check( $request ) {
		$term = $this->get_term( $request['id'] );

		if ( is_gc_error( $term ) ) {
			return $term;
		}

		if ( 'edit' === $request['context'] && ! current_user_can( 'edit_term', $term->term_id ) ) {
			return new GC_Error(
				'rest_forbidden_context',
				__( '抱歉，您不能编辑此项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Gets a single term from a taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$term = $this->get_term( $request['id'] );
		if ( is_gc_error( $term ) ) {
			return $term;
		}

		$response = $this->prepare_item_for_response( $term, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a request has access to create a term.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, false or GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! $this->check_is_taxonomy_allowed( $this->taxonomy ) ) {
			return false;
		}

		$taxonomy_obj = get_taxonomy( $this->taxonomy );

		if ( ( is_taxonomy_hierarchical( $this->taxonomy )
				&& ! current_user_can( $taxonomy_obj->cap->edit_terms ) )
			|| ( ! is_taxonomy_hierarchical( $this->taxonomy )
				&& ! current_user_can( $taxonomy_obj->cap->assign_terms ) ) ) {
			return new GC_Error(
				'rest_cannot_create',
				__( '抱歉，您不能在此分类法中创建项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single term in a taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $this->taxonomy ) ) {
				return new GC_Error(
					'rest_taxonomy_not_hierarchical',
					__( '不能设置父项目。分类法不分层级。' ),
					array( 'status' => 400 )
				);
			}

			$parent = get_term( (int) $request['parent'], $this->taxonomy );

			if ( ! $parent ) {
				return new GC_Error(
					'rest_term_invalid',
					__( '父项目不存在。' ),
					array( 'status' => 400 )
				);
			}
		}

		$prepared_term = $this->prepare_item_for_database( $request );

		$term = gc_insert_term( gc_slash( $prepared_term->name ), $this->taxonomy, gc_slash( (array) $prepared_term ) );
		if ( is_gc_error( $term ) ) {
			/*
			 * If we're going to inform the client that the term already exists,
			 * give them the identifier for future use.
			 */
			$term_id = $term->get_error_data( 'term_exists' );
			if ( $term_id ) {
				$existing_term = get_term( $term_id, $this->taxonomy );
				$term->add_data( $existing_term->term_id, 'term_exists' );
				$term->add_data(
					array(
						'status'  => 400,
						'term_id' => $term_id,
					)
				);
			}

			return $term;
		}

		$term = get_term( $term['term_id'], $this->taxonomy );

		/**
		 * Fires after a single term is created or updated via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_insert_category`
		 *  - `rest_insert_post_tag`
		 *
		 *
		 * @param GC_Term         $term     Inserted or updated term object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a term, false when updating.
		 */
		do_action( "rest_insert_{$this->taxonomy}", $term, $request, true );

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $term->term_id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$fields_update = $this->update_additional_fields_for_object( $term, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/**
		 * Fires after a single term is completely created or updated via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_after_insert_category`
		 *  - `rest_after_insert_post_tag`
		 *
		 *
		 * @param GC_Term         $term     Inserted or updated term object.
		 * @param GC_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a term, false when updating.
		 */
		do_action( "rest_after_insert_{$this->taxonomy}", $term, $request, true );

		$response = $this->prepare_item_for_response( $term, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( $this->namespace . '/' . $this->rest_base . '/' . $term->term_id ) );

		return $response;
	}

	/**
	 * Checks if a request has access to update the specified term.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, false or GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$term = $this->get_term( $request['id'] );

		if ( is_gc_error( $term ) ) {
			return $term;
		}

		if ( ! current_user_can( 'edit_term', $term->term_id ) ) {
			return new GC_Error(
				'rest_cannot_update',
				__( '抱歉，您不能编辑此项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates a single term from a taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		$term = $this->get_term( $request['id'] );
		if ( is_gc_error( $term ) ) {
			return $term;
		}

		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $this->taxonomy ) ) {
				return new GC_Error(
					'rest_taxonomy_not_hierarchical',
					__( '不能设置父项目。分类法不分层级。' ),
					array( 'status' => 400 )
				);
			}

			$parent = get_term( (int) $request['parent'], $this->taxonomy );

			if ( ! $parent ) {
				return new GC_Error(
					'rest_term_invalid',
					__( '父项目不存在。' ),
					array( 'status' => 400 )
				);
			}
		}

		$prepared_term = $this->prepare_item_for_database( $request );

		// Only update the term if we have something to update.
		if ( ! empty( $prepared_term ) ) {
			$update = gc_update_term( $term->term_id, $term->taxonomy, gc_slash( (array) $prepared_term ) );

			if ( is_gc_error( $update ) ) {
				return $update;
			}
		}

		$term = get_term( $term->term_id, $this->taxonomy );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-terms-controller.php */
		do_action( "rest_insert_{$this->taxonomy}", $term, $request, false );

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $term->term_id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$fields_update = $this->update_additional_fields_for_object( $term, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-terms-controller.php */
		do_action( "rest_after_insert_{$this->taxonomy}", $term, $request, false );

		$response = $this->prepare_item_for_response( $term, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a request has access to delete the specified term.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, otherwise false or GC_Error object.
	 */
	public function delete_item_permissions_check( $request ) {
		$term = $this->get_term( $request['id'] );

		if ( is_gc_error( $term ) ) {
			return $term;
		}

		if ( ! current_user_can( 'delete_term', $term->term_id ) ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '抱歉，您不能删除此项目。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single term from a taxonomy.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		$term = $this->get_term( $request['id'] );
		if ( is_gc_error( $term ) ) {
			return $term;
		}

		$force = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for terms.
		if ( ! $force ) {
			return new GC_Error(
				'rest_trash_not_supported',
				/* translators: %s: force=true */
				sprintf( __( "项目不能被移动到回收站，设置“%s”来删除。" ), 'force=true' ),
				array( 'status' => 501 )
			);
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $term, $request );

		$retval = gc_delete_term( $term->term_id, $term->taxonomy );

		if ( ! $retval ) {
			return new GC_Error(
				'rest_cannot_delete',
				__( '项目不能被删除。' ),
				array( 'status' => 500 )
			);
		}

		$response = new GC_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);

		/**
		 * Fires after a single term is deleted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_delete_category`
		 *  - `rest_delete_post_tag`
		 *
		 *
		 * @param GC_Term          $term     The deleted term.
		 * @param GC_REST_Response $response The response data.
		 * @param GC_REST_Request  $request  The request sent to the API.
		 */
		do_action( "rest_delete_{$this->taxonomy}", $term, $response, $request );

		return $response;
	}

	/**
	 * Prepares a single term for create or update.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return object Term object.
	 */
	public function prepare_item_for_database( $request ) {
		$prepared_term = new stdClass;

		$schema = $this->get_item_schema();
		if ( isset( $request['name'] ) && ! empty( $schema['properties']['name'] ) ) {
			$prepared_term->name = $request['name'];
		}

		if ( isset( $request['slug'] ) && ! empty( $schema['properties']['slug'] ) ) {
			$prepared_term->slug = $request['slug'];
		}

		if ( isset( $request['taxonomy'] ) && ! empty( $schema['properties']['taxonomy'] ) ) {
			$prepared_term->taxonomy = $request['taxonomy'];
		}

		if ( isset( $request['description'] ) && ! empty( $schema['properties']['description'] ) ) {
			$prepared_term->description = $request['description'];
		}

		if ( isset( $request['parent'] ) && ! empty( $schema['properties']['parent'] ) ) {
			$parent_term_id   = 0;
			$requested_parent = (int) $request['parent'];

			if ( $requested_parent ) {
				$parent_term = get_term( $requested_parent, $this->taxonomy );

				if ( $parent_term instanceof GC_Term ) {
					$parent_term_id = $parent_term->term_id;
				}
			}

			$prepared_term->parent = $parent_term_id;
		}

		/**
		 * Filters term data before inserting term via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_pre_insert_category`
		 *  - `rest_pre_insert_post_tag`
		 *
		 *
		 * @param object          $prepared_term Term object.
		 * @param GC_REST_Request $request       Request object.
		 */
		return apply_filters( "rest_pre_insert_{$this->taxonomy}", $prepared_term, $request );
	}

	/**
	 * Prepares a single term output for response.
	 *
	 *
	 * @param GC_Term         $item    Term object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = (int) $item->term_id;
		}

		if ( in_array( 'count', $fields, true ) ) {
			$data['count'] = (int) $item->count;
		}

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = $item->description;
		}

		if ( in_array( 'link', $fields, true ) ) {
			$data['link'] = get_term_link( $item );
		}

		if ( in_array( 'name', $fields, true ) ) {
			$data['name'] = $item->name;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $item->slug;
		}

		if ( in_array( 'taxonomy', $fields, true ) ) {
			$data['taxonomy'] = $item->taxonomy;
		}

		if ( in_array( 'parent', $fields, true ) ) {
			$data['parent'] = (int) $item->parent;
		}

		if ( in_array( 'meta', $fields, true ) ) {
			$data['meta'] = $this->meta->get_value( $item->term_id, $request );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $item ) );

		/**
		 * Filters the term data for a REST API response.
		 *
		 * The dynamic portion of the hook name, `$this->taxonomy`, refers to the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `rest_prepare_category`
		 *  - `rest_prepare_post_tag`
		 *
		 * Allows modification of the term data right before it is returned.
		 *
		 *
		 * @param GC_REST_Response  $response  The response object.
		 * @param GC_Term           $item      The original term object.
		 * @param GC_REST_Request   $request   Request used to generate the response.
		 */
		return apply_filters( "rest_prepare_{$this->taxonomy}", $response, $item, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 *
	 * @param GC_Term $term Term object.
	 * @return array Links for the given term.
	 */
	protected function prepare_links( $term ) {
		$base  = $this->namespace . '/' . $this->rest_base;
		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $term->term_id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
			'about'      => array(
				'href' => rest_url( sprintf( 'gc/v2/taxonomies/%s', $this->taxonomy ) ),
			),
		);

		if ( $term->parent ) {
			$parent_term = get_term( (int) $term->parent, $term->taxonomy );

			if ( $parent_term ) {
				$links['up'] = array(
					'href'       => rest_url( trailingslashit( $base ) . $parent_term->term_id ),
					'embeddable' => true,
				);
			}
		}

		$taxonomy_obj = get_taxonomy( $term->taxonomy );

		if ( empty( $taxonomy_obj->object_type ) ) {
			return $links;
		}

		$post_type_links = array();

		foreach ( $taxonomy_obj->object_type as $type ) {
			$rest_path = rest_get_route_for_post_type_items( $type );

			if ( empty( $rest_path ) ) {
				continue;
			}

			$post_type_links[] = array(
				'href' => add_query_arg( $this->rest_base, $term->term_id, rest_url( $rest_path ) ),
			);
		}

		if ( ! empty( $post_type_links ) ) {
			$links['https://api.w.org/post_type'] = $post_type_links;
		}

		return $links;
	}

	/**
	 * Retrieves the term's schema, conforming to JSON Schema.
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
			'title'      => 'post_tag' === $this->taxonomy ? 'tag' : $this->taxonomy,
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( '项目的唯一标识符。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'count'       => array(
					'description' => __( '项目中已发布文章的数量。' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( '项目的HTML描述。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'link'        => array(
					'description' => __( '项目的URL。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'name'        => array(
					'description' => __( '项目的HTML标题。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'slug'        => array(
					'description' => __( '项目类型而言的英数字标识符。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'sanitize_slug' ),
					),
				),
				'taxonomy'    => array(
					'description' => __( '项目的类型属性。' ),
					'type'        => 'string',
					'enum'        => array( $this->taxonomy ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$taxonomy = get_taxonomy( $this->taxonomy );

		if ( $taxonomy->hierarchical ) {
			$schema['properties']['parent'] = array(
				'description' => __( '父项目ID。' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			);
		}

		$schema['properties']['meta'] = $this->meta->get_field_schema();

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
		$query_params = parent::get_collection_params();
		$taxonomy     = get_taxonomy( $this->taxonomy );

		$query_params['context']['default'] = 'view';

		$query_params['exclude'] = array(
			'description' => __( '确保结果集排除指定ID。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( '将结果集限制为指定ID。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		if ( ! $taxonomy->hierarchical ) {
			$query_params['offset'] = array(
				'description' => __( '将结果集移位特定数量。' ),
				'type'        => 'integer',
			);
		}

		$query_params['order'] = array(
			'description' => __( '设置排序字段升序或降序。' ),
			'type'        => 'string',
			'default'     => 'asc',
			'enum'        => array(
				'asc',
				'desc',
			),
		);

		$query_params['orderby'] = array(
			'description' => __( '按项目属性排序集合。' ),
			'type'        => 'string',
			'default'     => 'name',
			'enum'        => array(
				'id',
				'include',
				'name',
				'slug',
				'include_slugs',
				'term_group',
				'description',
				'count',
			),
		);

		$query_params['hide_empty'] = array(
			'description' => __( '是否隐藏未被指定到任何文章的项目。' ),
			'type'        => 'boolean',
			'default'     => false,
		);

		if ( $taxonomy->hierarchical ) {
			$query_params['parent'] = array(
				'description' => __( '将结果集限制为指定给特定父项目的项目。' ),
				'type'        => 'integer',
			);
		}

		$query_params['post'] = array(
			'description' => __( '将结果集限制为指定给特定文章的项目。' ),
			'type'        => 'integer',
			'default'     => null,
		);

		$query_params['slug'] = array(
			'description' => __( '将结果集限制为具有一个或多个别名的分类法项目。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		/**
		 * Filters collection parameters for the terms controller.
		 *
		 * The dynamic part of the filter `$this->taxonomy` refers to the taxonomy
		 * slug for the controller.
		 *
		 * This filter registers the collection parameter, but does not map the
		 * collection parameter to an internal GC_Term_Query parameter.  Use the
		 * `rest_{$this->taxonomy}_query` filter to set GC_Term_Query parameters.
		 *
		 *
		 * @param array       $query_params JSON Schema-formatted collection parameters.
		 * @param GC_Taxonomy $taxonomy     Taxonomy object.
		 */
		return apply_filters( "rest_{$this->taxonomy}_collection_params", $query_params, $taxonomy );
	}

	/**
	 * Checks that the taxonomy is valid.
	 *
	 *
	 * @param string $taxonomy Taxonomy to check.
	 * @return bool Whether the taxonomy is allowed for REST management.
	 */
	protected function check_is_taxonomy_allowed( $taxonomy ) {
		$taxonomy_obj = get_taxonomy( $taxonomy );
		if ( $taxonomy_obj && ! empty( $taxonomy_obj->show_in_rest ) ) {
			return true;
		}
		return false;
	}
}
