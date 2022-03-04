<?php
/**
 * REST API: GC_REST_Widgets_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class to access widgets via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Widgets_Controller extends GC_REST_Controller {

	/**
	 * Tracks whether {@see retrieve_widgets()} has been called in the current request.
	 *
	 * @var bool
	 */
	protected $widgets_retrieved = false;

	/**
	 * Whether the controller supports batching.
	 *
	 * @var array
	 */
	protected $allow_batch = array( 'v1' => true );

	/**
	 * Widgets controller constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'widgets';
	}

	/**
	 * Registers the widget routes for the controller.
	 *
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->rest_base,
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
					'args'                => $this->get_endpoint_args_for_item_schema(),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/(?P<id>[\w\-]+)',
			array(
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
							'description' => __( '强行移除小工具，货将其移动到未启用的小工具侧边栏。' ),
							'type'        => 'boolean',
						),
					),
				),
				'allow_batch' => $this->allow_batch,
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to get widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$this->retrieve_widgets();
		if ( isset( $request['sidebar'] ) && $this->check_read_sidebar_permission( $request['sidebar'] ) ) {
			return true;
		}

		foreach ( gc_get_sidebars_widgets() as $sidebar_id => $widget_ids ) {
			if ( $this->check_read_sidebar_permission( $sidebar_id ) ) {
				return true;
			}
		}

		return $this->permissions_check( $request );
	}

	/**
	 * Retrieves a collection of widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$this->retrieve_widgets();

		$prepared          = array();
		$permissions_check = $this->permissions_check( $request );

		foreach ( gc_get_sidebars_widgets() as $sidebar_id => $widget_ids ) {
			if ( isset( $request['sidebar'] ) && $sidebar_id !== $request['sidebar'] ) {
				continue;
			}

			if ( is_gc_error( $permissions_check ) && ! $this->check_read_sidebar_permission( $sidebar_id ) ) {
				continue;
			}

			foreach ( $widget_ids as $widget_id ) {
				$response = $this->prepare_item_for_response( compact( 'sidebar_id', 'widget_id' ), $request );

				if ( ! is_gc_error( $response ) ) {
					$prepared[] = $this->prepare_response_for_collection( $response );
				}
			}
		}

		return new GC_REST_Response( $prepared );
	}

	/**
	 * Checks if a given request has access to get a widget.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$this->retrieve_widgets();

		$widget_id  = $request['id'];
		$sidebar_id = gc_find_widgets_sidebar( $widget_id );

		if ( $sidebar_id && $this->check_read_sidebar_permission( $sidebar_id ) ) {
			return true;
		}

		return $this->permissions_check( $request );
	}

	/**
	 * Checks if a sidebar can be read publicly.
	 *
	 *
	 * @param string $sidebar_id The sidebar ID.
	 * @return bool Whether the sidebar can be read.
	 */
	protected function check_read_sidebar_permission( $sidebar_id ) {
		$sidebar = gc_get_sidebar( $sidebar_id );

		return ! empty( $sidebar['show_in_rest'] );
	}

	/**
	 * Gets an individual widget.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$this->retrieve_widgets();

		$widget_id  = $request['id'];
		$sidebar_id = gc_find_widgets_sidebar( $widget_id );

		if ( is_null( $sidebar_id ) ) {
			return new GC_Error(
				'rest_widget_not_found',
				__( '未找到使用该ID的小工具。' ),
				array( 'status' => 404 )
			);
		}

		return $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );
	}

	/**
	 * Checks if a given request has access to create widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Creates a widget.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		$sidebar_id = $request['sidebar'];

		$widget_id = $this->save_widget( $request, $sidebar_id );

		if ( is_gc_error( $widget_id ) ) {
			return $widget_id;
		}

		gc_assign_widget_to_sidebar( $widget_id, $sidebar_id );

		$request['context'] = 'edit';

		$response = $this->prepare_item_for_response( compact( 'sidebar_id', 'widget_id' ), $request );

		if ( is_gc_error( $response ) ) {
			return $response;
		}

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Checks if a given request has access to update widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Updates an existing widget.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		global $gc_widget_factory;

		/*
		 * retrieve_widgets() contains logic to move "hidden" or "lost" widgets to the
		 * gc_inactive_widgets sidebar based on the contents of the $sidebars_widgets global.
		 *
		 * When batch requests are processed, this global is not properly updated by previous
		 * calls, resulting in widgets incorrectly being moved to the gc_inactive_widgets
		 * sidebar.
		 *
		 * See https://core.trac.gechiui.com/ticket/53657.
		 */
		gc_get_sidebars_widgets();
		$this->retrieve_widgets();

		$widget_id  = $request['id'];
		$sidebar_id = gc_find_widgets_sidebar( $widget_id );

		// Allow sidebar to be unset or missing when widget is not a GC_Widget.
		$parsed_id     = gc_parse_widget_id( $widget_id );
		$widget_object = $gc_widget_factory->get_widget_object( $parsed_id['id_base'] );
		if ( is_null( $sidebar_id ) && $widget_object ) {
			return new GC_Error(
				'rest_widget_not_found',
				__( '未找到使用该ID的小工具。' ),
				array( 'status' => 404 )
			);
		}

		if (
			$request->has_param( 'instance' ) ||
			$request->has_param( 'form_data' )
		) {
			$maybe_error = $this->save_widget( $request, $sidebar_id );
			if ( is_gc_error( $maybe_error ) ) {
				return $maybe_error;
			}
		}

		if ( $request->has_param( 'sidebar' ) ) {
			if ( $sidebar_id !== $request['sidebar'] ) {
				$sidebar_id = $request['sidebar'];
				gc_assign_widget_to_sidebar( $widget_id, $sidebar_id );
			}
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );
	}

	/**
	 * Checks if a given request has access to delete widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Deletes a widget.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 * @global array             $gc_registered_widget_updates The registered widget update functions.
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		global $gc_widget_factory, $gc_registered_widget_updates;

		/*
		 * retrieve_widgets() contains logic to move "hidden" or "lost" widgets to the
		 * gc_inactive_widgets sidebar based on the contents of the $sidebars_widgets global.
		 *
		 * When batch requests are processed, this global is not properly updated by previous
		 * calls, resulting in widgets incorrectly being moved to the gc_inactive_widgets
		 * sidebar.
		 *
		 * See https://core.trac.gechiui.com/ticket/53657.
		 */
		gc_get_sidebars_widgets();
		$this->retrieve_widgets();

		$widget_id  = $request['id'];
		$sidebar_id = gc_find_widgets_sidebar( $widget_id );

		if ( is_null( $sidebar_id ) ) {
			return new GC_Error(
				'rest_widget_not_found',
				__( '未找到使用该ID的小工具。' ),
				array( 'status' => 404 )
			);
		}

		$request['context'] = 'edit';

		if ( $request['force'] ) {
			$response = $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );

			$parsed_id = gc_parse_widget_id( $widget_id );
			$id_base   = $parsed_id['id_base'];

			$original_post    = $_POST;
			$original_request = $_REQUEST;

			$_POST    = array(
				'sidebar'         => $sidebar_id,
				"widget-$id_base" => array(),
				'the-widget-id'   => $widget_id,
				'delete_widget'   => '1',
			);
			$_REQUEST = $_POST;

			/** This action is documented in gc-admin/widgets-form.php */
			do_action( 'delete_widget', $widget_id, $sidebar_id, $id_base );

			$callback = $gc_registered_widget_updates[ $id_base ]['callback'];
			$params   = $gc_registered_widget_updates[ $id_base ]['params'];

			if ( is_callable( $callback ) ) {
				ob_start();
				call_user_func_array( $callback, $params );
				ob_end_clean();
			}

			$_POST    = $original_post;
			$_REQUEST = $original_request;

			$widget_object = $gc_widget_factory->get_widget_object( $id_base );

			if ( $widget_object ) {
				/*
				 * GC_Widget sets `updated = true` after an update to prevent more than one widget
				 * from being saved per request. This isn't what we want in the REST API, though,
				 * as we support batch requests.
				 */
				$widget_object->updated = false;
			}

			gc_assign_widget_to_sidebar( $widget_id, '' );

			$response->set_data(
				array(
					'deleted'  => true,
					'previous' => $response->get_data(),
				)
			);
		} else {
			gc_assign_widget_to_sidebar( $widget_id, 'gc_inactive_widgets' );

			$response = $this->prepare_item_for_response(
				array(
					'sidebar_id' => 'gc_inactive_widgets',
					'widget_id'  => $widget_id,
				),
				$request
			);
		}

		/**
		 * Fires after a widget is deleted via the REST API.
		 *
		 *
		 * @param string                    $widget_id  ID of the widget marked for deletion.
		 * @param string                    $sidebar_id ID of the sidebar the widget was deleted from.
		 * @param GC_REST_Response|GC_Error $response   The response data, or GC_Error object on failure.
		 * @param GC_REST_Request           $request    The request sent to the API.
		 */
		do_action( 'rest_delete_widget', $widget_id, $sidebar_id, $response, $request );

		return $response;
	}

	/**
	 * Performs a permissions check for managing widgets.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error
	 */
	protected function permissions_check( $request ) {
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
	 * Looks for "lost" widgets once per request.
	 *
	 *
	 * @see retrieve_widgets()
	 */
	protected function retrieve_widgets() {
		if ( ! $this->widgets_retrieved ) {
			retrieve_widgets();
			$this->widgets_retrieved = true;
		}
	}

	/**
	 * Saves the widget in the request object.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 * @global array             $gc_registered_widget_updates The registered widget update functions.
	 *
	 * @param GC_REST_Request $request    Full details about the request.
	 * @param string          $sidebar_id ID of the sidebar the widget belongs to.
	 * @return string|GC_Error The saved widget ID.
	 */
	protected function save_widget( $request, $sidebar_id ) {
		global $gc_widget_factory, $gc_registered_widget_updates;

		require_once ABSPATH . 'gc-admin/includes/widgets.php'; // For next_widget_id_number().

		if ( isset( $request['id'] ) ) {
			// Saving an existing widget.
			$id            = $request['id'];
			$parsed_id     = gc_parse_widget_id( $id );
			$id_base       = $parsed_id['id_base'];
			$number        = isset( $parsed_id['number'] ) ? $parsed_id['number'] : null;
			$widget_object = $gc_widget_factory->get_widget_object( $id_base );
			$creating      = false;
		} elseif ( $request['id_base'] ) {
			// Saving a new widget.
			$id_base       = $request['id_base'];
			$widget_object = $gc_widget_factory->get_widget_object( $id_base );
			$number        = $widget_object ? next_widget_id_number( $id_base ) : null;
			$id            = $widget_object ? $id_base . '-' . $number : $id_base;
			$creating      = true;
		} else {
			return new GC_Error(
				'rest_invalid_widget',
				__( '小工具类型（id_base）是必需的。' ),
				array( 'status' => 400 )
			);
		}

		if ( ! isset( $gc_registered_widget_updates[ $id_base ] ) ) {
			return new GC_Error(
				'rest_invalid_widget',
				__( '无法更新提供的小工具类型（id_base）。' ),
				array( 'status' => 400 )
			);
		}

		if ( isset( $request['instance'] ) ) {
			if ( ! $widget_object ) {
				return new GC_Error(
					'rest_invalid_widget',
					__( '无法在没有扩展 GC_Widget的 小工具上设置实例。' ),
					array( 'status' => 400 )
				);
			}

			if ( isset( $request['instance']['raw'] ) ) {
				if ( empty( $widget_object->widget_options['show_instance_in_rest'] ) ) {
					return new GC_Error(
						'rest_invalid_widget',
						__( '小工具类型不支持原始实例。' ),
						array( 'status' => 400 )
					);
				}
				$instance = $request['instance']['raw'];
			} elseif ( isset( $request['instance']['encoded'], $request['instance']['hash'] ) ) {
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
				return new GC_Error(
					'rest_invalid_widget',
					__( '提供的实例无效。必须包含原始OR编码和哈希。' ),
					array( 'status' => 400 )
				);
			}

			$form_data = array(
				"widget-$id_base" => array(
					$number => $instance,
				),
				'sidebar'         => $sidebar_id,
			);
		} elseif ( isset( $request['form_data'] ) ) {
			$form_data = $request['form_data'];
		} else {
			$form_data = array();
		}

		$original_post    = $_POST;
		$original_request = $_REQUEST;

		foreach ( $form_data as $key => $value ) {
			$slashed_value    = gc_slash( $value );
			$_POST[ $key ]    = $slashed_value;
			$_REQUEST[ $key ] = $slashed_value;
		}

		$callback = $gc_registered_widget_updates[ $id_base ]['callback'];
		$params   = $gc_registered_widget_updates[ $id_base ]['params'];

		if ( is_callable( $callback ) ) {
			ob_start();
			call_user_func_array( $callback, $params );
			ob_end_clean();
		}

		$_POST    = $original_post;
		$_REQUEST = $original_request;

		if ( $widget_object ) {
			// Register any multi-widget that the update callback just created.
			$widget_object->_set( $number );
			$widget_object->_register_one( $number );

			/*
			 * GC_Widget sets `updated = true` after an update to prevent more than one widget
			 * from being saved per request. This isn't what we want in the REST API, though,
			 * as we support batch requests.
			 */
			$widget_object->updated = false;
		}

		/**
		 * Fires after a widget is created or updated via the REST API.
		 *
		 *
		 * @param string          $id         ID of the widget being saved.
		 * @param string          $sidebar_id ID of the sidebar containing the widget being saved.
		 * @param GC_REST_Request $request    Request object.
		 * @param bool            $creating   True when creating a widget, false when updating.
		 */
		do_action( 'rest_after_save_widget', $id, $sidebar_id, $request, $creating );

		return $id;
	}

	/**
	 * Prepares the widget for the REST response.
	 *
	 *
	 * @global GC_Widget_Factory $gc_widget_factory
	 * @global array             $gc_registered_widgets The registered widgets.
	 *
	 * @param array           $item    An array containing a widget_id and sidebar_id.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		global $gc_widget_factory, $gc_registered_widgets;

		$widget_id  = $item['widget_id'];
		$sidebar_id = $item['sidebar_id'];

		if ( ! isset( $gc_registered_widgets[ $widget_id ] ) ) {
			return new GC_Error(
				'rest_invalid_widget',
				__( '请求的小工具无效。' ),
				array( 'status' => 500 )
			);
		}

		$widget    = $gc_registered_widgets[ $widget_id ];
		$parsed_id = gc_parse_widget_id( $widget_id );
		$fields    = $this->get_fields_for_response( $request );

		$prepared = array(
			'id'            => $widget_id,
			'id_base'       => $parsed_id['id_base'],
			'sidebar'       => $sidebar_id,
			'rendered'      => '',
			'rendered_form' => null,
			'instance'      => null,
		);

		if (
			rest_is_field_included( 'rendered', $fields ) &&
			'gc_inactive_widgets' !== $sidebar_id
		) {
			$prepared['rendered'] = trim( gc_render_widget( $widget_id, $sidebar_id ) );
		}

		if ( rest_is_field_included( 'rendered_form', $fields ) ) {
			$rendered_form = gc_render_widget_control( $widget_id );
			if ( ! is_null( $rendered_form ) ) {
				$prepared['rendered_form'] = trim( $rendered_form );
			}
		}

		if ( rest_is_field_included( 'instance', $fields ) ) {
			$widget_object = $gc_widget_factory->get_widget_object( $parsed_id['id_base'] );
			if ( $widget_object && isset( $parsed_id['number'] ) ) {
				$all_instances                   = $widget_object->get_settings();
				$instance                        = $all_instances[ $parsed_id['number'] ];
				$serialized_instance             = serialize( $instance );
				$prepared['instance']['encoded'] = base64_encode( $serialized_instance );
				$prepared['instance']['hash']    = gc_hash( $serialized_instance );

				if ( ! empty( $widget_object->widget_options['show_instance_in_rest'] ) ) {
					// Use new stdClass so that JSON result is {} and not [].
					$prepared['instance']['raw'] = empty( $instance ) ? new stdClass : $instance;
				}
			}
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$prepared = $this->add_additional_fields_to_object( $prepared, $request );
		$prepared = $this->filter_response_by_context( $prepared, $context );

		$response = rest_ensure_response( $prepared );

		$response->add_links( $this->prepare_links( $prepared ) );

		/**
		 * Filters the REST API response for a widget.
		 *
		 *
		 * @param GC_REST_Response|GC_Error $response The response object, or GC_Error object on failure.
		 * @param array                     $widget   The registered widget data.
		 * @param GC_REST_Request           $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_widget', $response, $widget, $request );
	}

	/**
	 * Prepares links for the widget.
	 *
	 *
	 * @param array $prepared Widget.
	 * @return array Links for the given widget.
	 */
	protected function prepare_links( $prepared ) {
		$id_base = ! empty( $prepared['id_base'] ) ? $prepared['id_base'] : $prepared['id'];

		return array(
			'self'                      => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $prepared['id'] ) ),
			),
			'collection'                => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'about'                     => array(
				'href'       => rest_url( sprintf( 'gc/v2/widget-types/%s', $id_base ) ),
				'embeddable' => true,
			),
			'https://api.w.org/sidebar' => array(
				'href' => rest_url( sprintf( 'gc/v2/sidebars/%s/', $prepared['sidebar'] ) ),
			),
		);
	}

	/**
	 * Gets the list of collection params.
	 *
	 *
	 * @return array[]
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			'sidebar' => array(
				'description' => __( '返回小工具的侧边栏。' ),
				'type'        => 'string',
			),
		);
	}

	/**
	 * Retrieves the widget's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'widget',
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'description' => __( '小工具的唯一标识符。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'id_base'       => array(
					'description' => __( '小工具的类型。对应于 widget-types 端点的 ID。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'sidebar'       => array(
					'description' => __( '小工具所属的侧边栏。' ),
					'type'        => 'string',
					'default'     => 'gc_inactive_widgets',
					'required'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'rendered'      => array(
					'description' => __( '小工具的HTML表示形式。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'rendered_form' => array(
					'description' => __( '小工具管理表单的HTML表示形式。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'instance'      => array(
					'description' => __( '小工具的实例设置（如果支持）。' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'default'     => null,
					'properties'  => array(
						'encoded' => array(
							'description' => __( '实例设置的 Base64 编码表示。' ),
							'type'        => 'string',
							'context'     => array( 'edit' ),
						),
						'hash'    => array(
							'description' => __( '实例设置的加密哈希值。' ),
							'type'        => 'string',
							'context'     => array( 'edit' ),
						),
						'raw'     => array(
							'description' => __( '未编码的实例设置（如果支持）。' ),
							'type'        => 'object',
							'context'     => array( 'edit' ),
						),
					),
				),
				'form_data'     => array(
					'description' => __( '来自小工具管理表单的 URL 编码表单数据。用于更新不支持实例的小工具（指写）。' ),
					'type'        => 'string',
					'context'     => array(),
					'arg_options' => array(
						'sanitize_callback' => static function( $string ) {
							$array = array();
							gc_parse_str( $string, $array );
							return $array;
						},
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}
}
