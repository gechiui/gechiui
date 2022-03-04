<?php
/**
 * REST API: GC_REST_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core base controller for managing and interacting with REST API items.
 *
 *
 */
abstract class GC_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Cached results of get_item_schema.
	 *
	 * @var array
	 */
	protected $schema;

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		_doing_it_wrong(
			'GC_REST_Controller::register_routes',
			/* translators: %s: register_routes() */
			sprintf( __( "方法“%s”必须被重写。" ), __METHOD__ ),
			'4.7.0'
		);
	}

	/**
	 * Checks if a given request has access to get items.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Retrieves a collection of items.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Checks if a given request has access to get a specific item.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Retrieves one item from the collection.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Checks if a given request has access to create items.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Creates one item from the collection.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Checks if a given request has access to update a specific item.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Updates one item from the collection.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Checks if a given request has access to delete a specific item.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Deletes one item from the collection.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Prepares one item for create or update operation.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return object|GC_Error The prepared item, or GC_Error object on failure.
	 */
	protected function prepare_item_for_database( $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 *
	 * @param mixed           $item    GeChiUI representation of the item.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		return new GC_Error(
			'invalid-method',
			/* translators: %s: Method name. */
			sprintf( __( "方法‘%s’未实现，必须在子类中被覆盖。" ), __METHOD__ ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Prepares a response for insertion into a collection.
	 *
	 *
	 * @param GC_REST_Response $response Response object.
	 * @return array|mixed Response data, ready for insertion into collection data.
	 */
	public function prepare_response_for_collection( $response ) {
		if ( ! ( $response instanceof GC_REST_Response ) ) {
			return $response;
		}

		$data   = (array) $response->get_data();
		$server = rest_get_server();
		$links  = $server::get_compact_response_links( $response );

		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Filters a response based on the context defined in the schema.
	 *
	 *
	 * @param array  $data    Response data to filter.
	 * @param string $context Context defined in the schema.
	 * @return array Filtered response.
	 */
	public function filter_response_by_context( $data, $context ) {

		$schema = $this->get_item_schema();

		return rest_filter_response_by_context( $data, $schema, $context );
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		return $this->add_additional_fields_schema( array() );
	}

	/**
	 * Retrieves the item's schema for display / public consumption purposes.
	 *
	 *
	 * @return array Public item schema data.
	 */
	public function get_public_item_schema() {

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties'] ) ) {
			foreach ( $schema['properties'] as &$property ) {
				unset( $property['arg_options'] );
			}
		}

		return $schema;
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'context'  => $this->get_context_param(),
			'page'     => array(
				'description'       => __( '集合的当前页。' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( '结果集包含的最大项目数量。' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'search'   => array(
				'description'       => __( '将结果限制为匹配字符串的。' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * Ensures consistent descriptions between endpoints, and populates enum from schema.
	 *
	 *
	 * @param array $args Optional. Additional arguments for context parameter. Default empty array.
	 * @return array Context parameter details.
	 */
	public function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( '请求提出的范围，用于决定回应包含的字段。' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$schema = $this->get_item_schema();

		if ( empty( $schema['properties'] ) ) {
			return array_merge( $param_details, $args );
		}

		$contexts = array();

		foreach ( $schema['properties'] as $attributes ) {
			if ( ! empty( $attributes['context'] ) ) {
				$contexts = array_merge( $contexts, $attributes['context'] );
			}
		}

		if ( ! empty( $contexts ) ) {
			$param_details['enum'] = array_unique( $contexts );
			rsort( $param_details['enum'] );
		}

		return array_merge( $param_details, $args );
	}

	/**
	 * Adds the values from additional fields to a data object.
	 *
	 *
	 * @param array           $prepared Prepared response array.
	 * @param GC_REST_Request $request  Full details about the request.
	 * @return array Modified data object with additional fields.
	 */
	protected function add_additional_fields_to_object( $prepared, $request ) {

		$additional_fields = $this->get_additional_fields();

		$requested_fields = $this->get_fields_for_response( $request );

		foreach ( $additional_fields as $field_name => $field_options ) {
			if ( ! $field_options['get_callback'] ) {
				continue;
			}

			if ( ! rest_is_field_included( $field_name, $requested_fields ) ) {
				continue;
			}

			$prepared[ $field_name ] = call_user_func( $field_options['get_callback'], $prepared, $field_name, $request, $this->get_object_type() );
		}

		return $prepared;
	}

	/**
	 * Updates the values of additional fields added to a data object.
	 *
	 *
	 * @param object          $object  Data model like GC_Term or GC_Post.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True on success, GC_Error object if a field cannot be updated.
	 */
	protected function update_additional_fields_for_object( $object, $request ) {
		$additional_fields = $this->get_additional_fields();

		foreach ( $additional_fields as $field_name => $field_options ) {
			if ( ! $field_options['update_callback'] ) {
				continue;
			}

			// Don't run the update callbacks if the data wasn't passed in the request.
			if ( ! isset( $request[ $field_name ] ) ) {
				continue;
			}

			$result = call_user_func( $field_options['update_callback'], $request[ $field_name ], $object, $field_name, $request, $this->get_object_type() );

			if ( is_gc_error( $result ) ) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * Adds the schema from additional fields to a schema array.
	 *
	 * The type of object is inferred from the passed schema.
	 *
	 *
	 * @param array $schema Schema array.
	 * @return array Modified Schema array.
	 */
	protected function add_additional_fields_schema( $schema ) {
		if ( empty( $schema['title'] ) ) {
			return $schema;
		}

		// Can't use $this->get_object_type otherwise we cause an inf loop.
		$object_type = $schema['title'];

		$additional_fields = $this->get_additional_fields( $object_type );

		foreach ( $additional_fields as $field_name => $field_options ) {
			if ( ! $field_options['schema'] ) {
				continue;
			}

			$schema['properties'][ $field_name ] = $field_options['schema'];
		}

		return $schema;
	}

	/**
	 * Retrieves all of the registered additional fields for a given object-type.
	 *
	 *
	 * @global array $gc_rest_additional_fields Holds registered fields, organized by object type.
	 *
	 * @param string $object_type Optional. The object type.
	 * @return array Registered additional fields (if any), empty array if none or if the object type
	 *               could not be inferred.
	 */
	protected function get_additional_fields( $object_type = null ) {
		global $gc_rest_additional_fields;

		if ( ! $object_type ) {
			$object_type = $this->get_object_type();
		}

		if ( ! $object_type ) {
			return array();
		}

		if ( ! $gc_rest_additional_fields || ! isset( $gc_rest_additional_fields[ $object_type ] ) ) {
			return array();
		}

		return $gc_rest_additional_fields[ $object_type ];
	}

	/**
	 * Retrieves the object type this controller is responsible for managing.
	 *
	 *
	 * @return string Object type for the controller.
	 */
	protected function get_object_type() {
		$schema = $this->get_item_schema();

		if ( ! $schema || ! isset( $schema['title'] ) ) {
			return null;
		}

		return $schema['title'];
	}

	/**
	 * Gets an array of fields to be included on the response.
	 *
	 * Included fields are based on item schema and `_fields=` request argument.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return string[] Fields to be included in the response.
	 */
	public function get_fields_for_response( $request ) {
		$schema     = $this->get_item_schema();
		$properties = isset( $schema['properties'] ) ? $schema['properties'] : array();

		$additional_fields = $this->get_additional_fields();

		foreach ( $additional_fields as $field_name => $field_options ) {
			// For back-compat, include any field with an empty schema
			// because it won't be present in $this->get_item_schema().
			if ( is_null( $field_options['schema'] ) ) {
				$properties[ $field_name ] = $field_options;
			}
		}

		// Exclude fields that specify a different context than the request context.
		$context = $request['context'];
		if ( $context ) {
			foreach ( $properties as $name => $options ) {
				if ( ! empty( $options['context'] ) && ! in_array( $context, $options['context'], true ) ) {
					unset( $properties[ $name ] );
				}
			}
		}

		$fields = array_keys( $properties );

		if ( ! isset( $request['_fields'] ) ) {
			return $fields;
		}
		$requested_fields = gc_parse_list( $request['_fields'] );
		if ( 0 === count( $requested_fields ) ) {
			return $fields;
		}
		// Trim off outside whitespace from the comma delimited list.
		$requested_fields = array_map( 'trim', $requested_fields );
		// Always persist 'id', because it can be needed for add_additional_fields_to_object().
		if ( in_array( 'id', $fields, true ) ) {
			$requested_fields[] = 'id';
		}
		// Return the list of all requested fields which appear in the schema.
		return array_reduce(
			$requested_fields,
			static function( $response_fields, $field ) use ( $fields ) {
				if ( in_array( $field, $fields, true ) ) {
					$response_fields[] = $field;
					return $response_fields;
				}
				// Check for nested fields if $field is not a direct match.
				$nested_fields = explode( '.', $field );
				// A nested field is included so long as its top-level property
				// is present in the schema.
				if ( in_array( $nested_fields[0], $fields, true ) ) {
					$response_fields[] = $field;
				}
				return $response_fields;
			},
			array()
		);
	}

	/**
	 * Retrieves an array of endpoint arguments from the item schema for the controller.
	 *
	 *
	 * @param string $method Optional. HTTP method of the request. The arguments for `CREATABLE` requests are
	 *                       checked for required values and may fall-back to a given default, this is not done
	 *                       on `EDITABLE` requests. Default GC_REST_Server::CREATABLE.
	 * @return array Endpoint arguments.
	 */
	public function get_endpoint_args_for_item_schema( $method = GC_REST_Server::CREATABLE ) {
		return rest_get_endpoint_args_for_schema( $this->get_item_schema(), $method );
	}

	/**
	 * Sanitizes the slug value.
	 *
	 *
	 * @internal We can't use sanitize_title() directly, as the second
	 * parameter is the fallback title, which would end up being set to the
	 * request object.
	 *
	 * @see https://github.com/GC-API/GC-API/issues/1585
	 *
	 * @todo Remove this in favour of https://core.trac.gechiui.com/ticket/34659
	 *
	 * @param string $slug Slug value passed in request.
	 * @return string Sanitized value for the slug.
	 */
	public function sanitize_slug( $slug ) {
		return sanitize_title( $slug );
	}
}
