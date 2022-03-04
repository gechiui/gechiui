<?php
/**
 * REST API: GC_REST_Settings_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class used to manage a site's settings via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Settings_Controller extends GC_REST_Controller {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'settings';
	}

	/**
	 * Registers the routes for the site's settings.
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
					'callback'            => array( $this, 'get_item' ),
					'args'                => array(),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => GC_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'args'                => $this->get_endpoint_args_for_item_schema( GC_REST_Server::EDITABLE ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Checks if a given request has access to read and manage settings.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return bool True if the request has read access for the item, otherwise false.
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Retrieves the settings.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return array|GC_Error Array on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$options  = $this->get_registered_options();
		$response = array();

		foreach ( $options as $name => $args ) {
			/**
			 * Filters the value of a setting recognized by the REST API.
			 *
			 * Allow hijacking the setting value and overriding the built-in behavior by returning a
			 * non-null value.  The returned value will be presented as the setting value instead.
			 *
		
			 *
			 * @param mixed  $result Value to use for the requested setting. Can be a scalar
			 *                       matching the registered schema for the setting, or null to
			 *                       follow the default get_option() behavior.
			 * @param string $name   Setting name (as shown in REST API responses).
			 * @param array  $args   Arguments passed to register_setting() for this setting.
			 */
			$response[ $name ] = apply_filters( 'rest_pre_get_setting', null, $name, $args );

			if ( is_null( $response[ $name ] ) ) {
				// Default to a null value as "null" in the response means "not set".
				$response[ $name ] = get_option( $args['option_name'], $args['schema']['default'] );
			}

			/*
			 * Because get_option() is lossy, we have to
			 * cast values to the type they are registered with.
			 */
			$response[ $name ] = $this->prepare_value( $response[ $name ], $args['schema'] );
		}

		return $response;
	}

	/**
	 * Prepares a value for output based off a schema array.
	 *
	 *
	 * @param mixed $value  Value to prepare.
	 * @param array $schema Schema to match.
	 * @return mixed The prepared value.
	 */
	protected function prepare_value( $value, $schema ) {
		/*
		 * If the value is not valid by the schema, set the value to null.
		 * Null values are specifically non-destructive, so this will not cause
		 * overwriting the current invalid value to null.
		 */
		if ( is_gc_error( rest_validate_value_from_schema( $value, $schema ) ) ) {
			return null;
		}

		return rest_sanitize_value_from_schema( $value, $schema );
	}

	/**
	 * Updates settings for the settings object.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return array|GC_Error Array on success, or error object on failure.
	 */
	public function update_item( $request ) {
		$options = $this->get_registered_options();

		$params = $request->get_params();

		foreach ( $options as $name => $args ) {
			if ( ! array_key_exists( $name, $params ) ) {
				continue;
			}

			/**
			 * Filters whether to preempt a setting value update via the REST API.
			 *
			 * Allows hijacking the setting update logic and overriding the built-in behavior by
			 * returning true.
			 *
		
			 *
			 * @param bool   $result Whether to override the default behavior for updating the
			 *                       value of a setting.
			 * @param string $name   Setting name (as shown in REST API responses).
			 * @param mixed  $value  Updated setting value.
			 * @param array  $args   Arguments passed to register_setting() for this setting.
			 */
			$updated = apply_filters( 'rest_pre_update_setting', false, $name, $request[ $name ], $args );

			if ( $updated ) {
				continue;
			}

			/*
			 * A null value for an option would have the same effect as
			 * deleting the option from the database, and relying on the
			 * default value.
			 */
			if ( is_null( $request[ $name ] ) ) {
				/*
				 * A null value is returned in the response for any option
				 * that has a non-scalar value.
				 *
				 * To protect clients from accidentally including the null
				 * values from a response object in a request, we do not allow
				 * options with values that don't pass validation to be updated to null.
				 * Without this added protection a client could mistakenly
				 * delete all options that have invalid values from the
				 * database.
				 */
				if ( is_gc_error( rest_validate_value_from_schema( get_option( $args['option_name'], false ), $args['schema'] ) ) ) {
					return new GC_Error(
						'rest_invalid_stored_value',
						/* translators: %s: Property name. */
						sprintf( __( '%s属性存储了无效的值，且不能被更新为null。' ), $name ),
						array( 'status' => 500 )
					);
				}

				delete_option( $args['option_name'] );
			} else {
				update_option( $args['option_name'], $request[ $name ] );
			}
		}

		return $this->get_item( $request );
	}

	/**
	 * Retrieves all of the registered options for the Settings API.
	 *
	 *
	 * @return array Array of registered options.
	 */
	protected function get_registered_options() {
		$rest_options = array();

		foreach ( get_registered_settings() as $name => $args ) {
			if ( empty( $args['show_in_rest'] ) ) {
				continue;
			}

			$rest_args = array();

			if ( is_array( $args['show_in_rest'] ) ) {
				$rest_args = $args['show_in_rest'];
			}

			$defaults = array(
				'name'   => ! empty( $rest_args['name'] ) ? $rest_args['name'] : $name,
				'schema' => array(),
			);

			$rest_args = array_merge( $defaults, $rest_args );

			$default_schema = array(
				'type'        => empty( $args['type'] ) ? null : $args['type'],
				'description' => empty( $args['description'] ) ? '' : $args['description'],
				'default'     => isset( $args['default'] ) ? $args['default'] : null,
			);

			$rest_args['schema']      = array_merge( $default_schema, $rest_args['schema'] );
			$rest_args['option_name'] = $name;

			// Skip over settings that don't have a defined type in the schema.
			if ( empty( $rest_args['schema']['type'] ) ) {
				continue;
			}

			/*
			 * Allow the supported types for settings, as we don't want invalid types
			 * to be updated with arbitrary values that we can't do decent sanitizing for.
			 */
			if ( ! in_array( $rest_args['schema']['type'], array( 'number', 'integer', 'string', 'boolean', 'array', 'object' ), true ) ) {
				continue;
			}

			$rest_args['schema'] = $this->set_additional_properties_to_false( $rest_args['schema'] );

			$rest_options[ $rest_args['name'] ] = $rest_args;
		}

		return $rest_options;
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$options = $this->get_registered_options();

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(),
		);

		foreach ( $options as $option_name => $option ) {
			$schema['properties'][ $option_name ]                = $option['schema'];
			$schema['properties'][ $option_name ]['arg_options'] = array(
				'sanitize_callback' => array( $this, 'sanitize_callback' ),
			);
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Custom sanitize callback used for all options to allow the use of 'null'.
	 *
	 * By default, the schema of settings will throw an error if a value is set to
	 * `null` as it's not a valid value for something like "type => string". We
	 * provide a wrapper sanitizer to allow the use of `null`.
	 *
	 *
	 * @param mixed           $value   The value for the setting.
	 * @param GC_REST_Request $request The request object.
	 * @param string          $param   The parameter name.
	 * @return mixed|GC_Error
	 */
	public function sanitize_callback( $value, $request, $param ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		return rest_parse_request_arg( $value, $request, $param );
	}

	/**
	 * Recursively add additionalProperties = false to all objects in a schema.
	 *
	 * This is need to restrict properties of objects in settings values to only
	 * registered items, as the REST API will allow additional properties by
	 * default.
	 *
	 *
	 * @param array $schema The schema array.
	 * @return array
	 */
	protected function set_additional_properties_to_false( $schema ) {
		switch ( $schema['type'] ) {
			case 'object':
				foreach ( $schema['properties'] as $key => $child_schema ) {
					$schema['properties'][ $key ] = $this->set_additional_properties_to_false( $child_schema );
				}

				$schema['additionalProperties'] = false;
				break;
			case 'array':
				$schema['items'] = $this->set_additional_properties_to_false( $schema['items'] );
				break;
		}

		return $schema;
	}
}
