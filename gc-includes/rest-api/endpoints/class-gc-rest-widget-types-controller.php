<?php
/**
 * REST API: GC_REST_Widget_Types_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class to access widget types via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Widget_Types_Controller extends GC_REST_Controller {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'widget-types';
	}

	/**
	 * Registers the widget type routes.
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
			'/' . $this->rest_base . '/(?P<id>[a-zA-Z0-9_-]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( '小工具类型id。' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-zA-Z0-9_-]+)/encode',
			array(
				'args' => array(
					'id'        => array(
						'description' => __( '小工具类型id。' ),
						'type'        => 'string',
						'required'    => true,
					),
					'instance'  => array(
						'description' => __( '小工具的当前设置实例。' ),
						'type'        => 'object',
					),
					'form_data' => array(
						'description'       => __( '序列化小工具表单数据，以编码为设置实例。' ),
						'type'              => 'string',
						'sanitize_callback' => static function( $string ) {
							$array = array();
							gc_parse_str( $string, $array );
							return $array;
						},
					),
				),
				array(
					'methods'             => GC_REST_Server::CREATABLE,
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'callback'            => array( $this, 'encode_form_data' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-zA-Z0-9_-]+)/render',
			array(
				array(
					'methods'             => GC_REST_Server::CREATABLE,
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'callback'            => array( $this, 'render' ),
					'args'                => array(
						'id'       => array(
							'description' => __( '小工具类型id。' ),
							'type'        => 'string',
							'required'    => true,
						),
						'instance' => array(
							'description' => __( '小工具的当前设置实例。' ),
							'type'        => 'object',
						),
					),
				),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read widget types.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return $this->check_read_permission();
	}

	/**
	 * Retrieves the list of all widget types.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$data = array();
		foreach ( $this->get_widgets() as $widget ) {
			$widget_type = $this->prepare_item_for_response( $widget, $request );
			$data[]      = $this->prepare_response_for_collection( $widget_type );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read a widget type.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$check = $this->check_read_permission();
		if ( is_gc_error( $check ) ) {
			return $check;
		}
		$widget_id   = $request['id'];
		$widget_type = $this->get_widget( $widget_id );
		if ( is_gc_error( $widget_type ) ) {
			return $widget_type;
		}

		return true;
	}

	/**
	 * Checks whether the user can read widget types.
	 *
	 *
	 * @return true|GC_Error True if the widget type is visible, GC_Error otherwise.
	 */
	protected function check_read_permission() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new GC_Error(
				'rest_cannot_manage_widgets',
				__( '抱歉，您不能在本站管理小工具。' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Gets the details about the requested widget.
	 *
	 *
	 * @param string $id The widget type id.
	 * @return array|GC_Error The array of widget data if the name is valid, GC_Error otherwise.
	 */
	public function get_widget( $id ) {
		foreach ( $this->get_widgets() as $widget ) {
			if ( $id === $widget['id'] ) {
				return $widget;
			}
		}

		return new GC_Error( 'rest_widget_type_invalid', __( '无效的小工具类型' ), array( 'status' => 404 ) );
	}

	/**
	 * Normalize array of widgets.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 * @global array             $gc_registered_widgets The list of registered widgets.
	 *
	 * @return array Array of widgets.
	 */
	protected function get_widgets() {
		global $gc_widget_factory, $gc_registered_widgets;

		$widgets = array();

		foreach ( $gc_registered_widgets as $widget ) {
			$parsed_id     = gc_parse_widget_id( $widget['id'] );
			$widget_object = $gc_widget_factory->get_widget_object( $parsed_id['id_base'] );

			$widget['id']       = $parsed_id['id_base'];
			$widget['is_multi'] = (bool) $widget_object;

			if ( isset( $widget['name'] ) ) {
				$widget['name'] = html_entity_decode( $widget['name'], ENT_QUOTES, get_bloginfo( 'charset' ) );
			}

			if ( isset( $widget['description'] ) ) {
				$widget['description'] = html_entity_decode( $widget['description'], ENT_QUOTES, get_bloginfo( 'charset' ) );
			}

			unset( $widget['callback'] );

			$classname = '';
			foreach ( (array) $widget['classname'] as $cn ) {
				if ( is_string( $cn ) ) {
					$classname .= '_' . $cn;
				} elseif ( is_object( $cn ) ) {
					$classname .= '_' . get_class( $cn );
				}
			}
			$widget['classname'] = ltrim( $classname, '_' );

			$widgets[ $widget['id'] ] = $widget;
		}

		ksort( $widgets );

		return $widgets;
	}

	/**
	 * Retrieves a single widget type from the collection.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$widget_id   = $request['id'];
		$widget_type = $this->get_widget( $widget_id );
		if ( is_gc_error( $widget_type ) ) {
			return $widget_type;
		}
		$data = $this->prepare_item_for_response( $widget_type, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a widget type object for serialization.
	 *
	 *
	 * @param array           $item    Widget type data.
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response Widget type data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$widget_type = $item;
		$fields      = $this->get_fields_for_response( $request );
		$data        = array(
			'id' => $widget_type['id'],
		);

		$schema       = $this->get_item_schema();
		$extra_fields = array(
			'name',
			'description',
			'is_multi',
			'classname',
			'widget_class',
			'option_name',
			'customize_selective_refresh',
		);

		foreach ( $extra_fields as $extra_field ) {
			if ( ! rest_is_field_included( $extra_field, $fields ) ) {
				continue;
			}

			if ( isset( $widget_type[ $extra_field ] ) ) {
				$field = $widget_type[ $extra_field ];
			} elseif ( array_key_exists( 'default', $schema['properties'][ $extra_field ] ) ) {
				$field = $schema['properties'][ $extra_field ]['default'];
			} else {
				$field = '';
			}

			$data[ $extra_field ] = rest_sanitize_value_from_schema( $field, $schema['properties'][ $extra_field ] );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $widget_type ) );

		/**
		 * Filters the REST API response for a widget type.
		 *
		 *
		 * @param GC_REST_Response $response    The response object.
		 * @param array            $widget_type The array of widget data.
		 * @param GC_REST_Request  $request     The request object.
		 */
		return apply_filters( 'rest_prepare_widget_type', $response, $widget_type, $request );
	}

	/**
	 * Prepares links for the widget type.
	 *
	 *
	 * @param array $widget_type Widget type data.
	 * @return array Links for the given widget type.
	 */
	protected function prepare_links( $widget_type ) {
		return array(
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $widget_type['id'] ) ),
			),
		);
	}

	/**
	 * Retrieves the widget type's schema, conforming to JSON Schema.
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
			'title'      => 'widget-type',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( '标识小工具类型的唯一别名。' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'        => array(
					'description' => __( '辨别小工具类型的可读名称。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( '小工具的描述。' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'is_multi'    => array(
					'description' => __( '小工具是否支持多个实例' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'classname'   => array(
					'description' => __( '类名' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * An RPC-style endpoint which can be used by clients to turn user input in
	 * a widget admin form into an encoded instance object.
	 *
	 * Accepts:
	 *
	 * - id:        A widget type ID.
	 * - instance:  A widget's encoded instance object. Optional.
	 * - form_data: Form data from submitting a widget's admin form. Optional.
	 *
	 * Returns:
	 * - instance: The encoded instance object after updating the widget with
	 *             the given form data.
	 * - form:     The widget's admin form after updating the widget with the
	 *             given form data.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function encode_form_data( $request ) {
		global $gc_widget_factory;

		$id            = $request['id'];
		$widget_object = $gc_widget_factory->get_widget_object( $id );

		if ( ! $widget_object ) {
			return new GC_Error(
				'rest_invalid_widget',
				__( '无法预览没有扩展GC_Widget的小工具。' ),
				array( 'status' => 400 )
			);
		}

		// Set the widget's number so that the id attributes in the HTML that we
		// return are predictable.
		if ( isset( $request['number'] ) && is_numeric( $request['number'] ) ) {
			$widget_object->_set( (int) $request['number'] );
		} else {
			$widget_object->_set( -1 );
		}

		if ( isset( $request['instance']['encoded'], $request['instance']['hash'] ) ) {
			$serialized_instance = base64_decode( $request['instance']['encoded'] );
			if ( ! hash_equals( gc_hash( $serialized_instance ), $request['instance']['hash'] ) ) {
				return new GC_Error(
					'rest_invalid_widget',
					__( '提供的实例格式不正确。' ),
					array( 'status' => 400 )
				);
			}
			$instance = unserialize( $serialized_instance );
		} else {
			$instance = array();
		}

		if (
			isset( $request['form_data'][ "widget-$id" ] ) &&
			is_array( $request['form_data'][ "widget-$id" ] )
		) {
			$new_instance = array_values( $request['form_data'][ "widget-$id" ] )[0];
			$old_instance = $instance;

			$instance = $widget_object->update( $new_instance, $old_instance );

			/** This filter is documented in gc-includes/class-gc-widget.php */
			$instance = apply_filters(
				'widget_update_callback',
				$instance,
				$new_instance,
				$old_instance,
				$widget_object
			);
		}

		$serialized_instance = serialize( $instance );
		$widget_key          = $gc_widget_factory->get_widget_key( $id );

		$response = array(
			'form'     => trim(
				$this->get_widget_form(
					$widget_object,
					$instance
				)
			),
			'preview'  => trim(
				$this->get_widget_preview(
					$widget_key,
					$instance
				)
			),
			'instance' => array(
				'encoded' => base64_encode( $serialized_instance ),
				'hash'    => gc_hash( $serialized_instance ),
			),
		);

		if ( ! empty( $widget_object->widget_options['show_instance_in_rest'] ) ) {
			// Use new stdClass so that JSON result is {} and not [].
			$response['instance']['raw'] = empty( $instance ) ? new stdClass : $instance;
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Returns the output of GC_Widget::widget() when called with the provided
	 * instance. Used by encode_form_data() to preview a widget.

	 *
	 * @param string    $widget   The widget's PHP class name (see class-gc-widget.php).
	 * @param array     $instance Widget instance settings.
	 * @return string
	 */
	private function get_widget_preview( $widget, $instance ) {
		ob_start();
		the_widget( $widget, $instance );
		return ob_get_clean();
	}

	/**
	 * Returns the output of GC_Widget::form() when called with the provided
	 * instance. Used by encode_form_data() to preview a widget's form.
	 *
	 *
	 * @param GC_Widget $widget_object Widget object to call widget() on.
	 * @param array     $instance Widget instance settings.
	 * @return string
	 */
	private function get_widget_form( $widget_object, $instance ) {
		ob_start();

		/** This filter is documented in gc-includes/class-gc-widget.php */
		$instance = apply_filters(
			'widget_form_callback',
			$instance,
			$widget_object
		);

		if ( false !== $instance ) {
			$return = $widget_object->form( $instance );

			/** This filter is documented in gc-includes/class-gc-widget.php */
			do_action_ref_array(
				'in_widget_form',
				array( &$widget_object, &$return, $instance )
			);
		}

		return ob_get_clean();
	}

	/**
	 * Renders a single Legacy Widget and wraps it in a JSON-encodable array.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 *
	 * @return array An array with rendered Legacy Widget HTML.
	 */
	public function render( $request ) {
		return array(
			'preview' => $this->render_legacy_widget_preview_iframe(
				$request['id'],
				isset( $request['instance'] ) ? $request['instance'] : null
			),
		);
	}

	/**
	 * Renders a page containing a preview of the requested Legacy Widget block.
	 *
	 *
	 * @param string $id_base The id base of the requested widget.
	 * @param array  $instance The widget instance attributes.
	 *
	 * @return string Rendered Legacy Widget block preview.
	 */
	private function render_legacy_widget_preview_iframe( $id_base, $instance ) {
		if ( ! defined( 'IFRAME_REQUEST' ) ) {
			define( 'IFRAME_REQUEST', true );
		}

		ob_start();
		?>
		<!doctype html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			<?php gc_head(); ?>
			<style>
				/* Reset theme styles */
				html, body, #page, #content {
					padding: 0 !important;
					margin: 0 !important;
				}
			</style>
		</head>
		<body <?php body_class(); ?>>
		<div id="page" class="site">
			<div id="content" class="site-content">
				<?php
				$registry = GC_Block_Type_Registry::get_instance();
				$block    = $registry->get_registered( 'core/legacy-widget' );
				echo $block->render(
					array(
						'idBase'   => $id_base,
						'instance' => $instance,
					)
				);
				?>
			</div><!-- #content -->
		</div><!-- #page -->
		<?php gc_footer(); ?>
		</body>
		</html>
		<?php
		return ob_get_clean();
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
