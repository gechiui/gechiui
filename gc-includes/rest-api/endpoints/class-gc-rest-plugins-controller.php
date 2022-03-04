<?php
/**
 * REST API: GC_REST_Plugins_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core class to access plugins via the REST API.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Plugins_Controller extends GC_REST_Controller {

	const PATTERN = '[^.\/]+(?:\/[^.\/]+)?';

	/**
	 * Plugins controller constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'plugins';
	}

	/**
	 * Registers the routes for the plugins controller.
	 *
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
					'args'                => array(
						'slug'   => array(
							'type'        => 'string',
							'required'    => true,
							'description' => __( 'www.GeChiUI.com插件目录别名。' ),
							'pattern'     => '[\w\-]+',
						),
						'status' => array(
							'description' => __( '插件启用状态。' ),
							'type'        => 'string',
							'enum'        => is_multisite() ? array( 'inactive', 'active', 'network-active' ) : array( 'inactive', 'active' ),
							'default'     => 'inactive',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<plugin>' . self::PATTERN . ')',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
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
				),
				'args'   => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					'plugin'  => array(
						'type'              => 'string',
						'pattern'           => self::PATTERN,
						'validate_callback' => array( $this, 'validate_plugin_param' ),
						'sanitize_callback' => array( $this, 'sanitize_plugin_param' ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to get plugins.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_view_plugins',
				__( '抱歉，您不能在此站点上管理插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a collection of plugins.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$plugins = array();

		foreach ( get_plugins() as $file => $data ) {
			if ( is_gc_error( $this->check_read_permission( $file ) ) ) {
				continue;
			}

			$data['_file'] = $file;

			if ( ! $this->does_plugin_match_request( $request, $data ) ) {
				continue;
			}

			$plugins[] = $this->prepare_response_for_collection( $this->prepare_item_for_response( $data, $request ) );
		}

		return new GC_REST_Response( $plugins );
	}

	/**
	 * Checks if a given request has access to get a specific plugin.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, GC_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_view_plugin',
				__( '抱歉，您不能在此站点上管理插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$can_read = $this->check_read_permission( $request['plugin'] );

		if ( is_gc_error( $can_read ) ) {
			return $can_read;
		}

		return true;
	}

	/**
	 * Retrieves one plugin from the site.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$data = $this->get_plugin_data( $request['plugin'] );

		if ( is_gc_error( $data ) ) {
			return $data;
		}

		return $this->prepare_item_for_response( $data, $request );
	}

	/**
	 * Checks if the given plugin can be viewed by the current user.
	 *
	 * On multisite, this hides non-active network only plugins if the user does not have permission
	 * to manage network plugins.
	 *
	 *
	 * @param string $plugin The plugin file to check.
	 * @return true|GC_Error True if can read, a GC_Error instance otherwise.
	 */
	protected function check_read_permission( $plugin ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		if ( ! $this->is_plugin_installed( $plugin ) ) {
			return new GC_Error( 'rest_plugin_not_found', __( '找不到插件。' ), array( 'status' => 404 ) );
		}

		if ( ! is_multisite() ) {
			return true;
		}

		if ( ! is_network_only_plugin( $plugin ) || is_plugin_active( $plugin ) || current_user_can( 'manage_network_plugins' ) ) {
			return true;
		}

		return new GC_Error(
			'rest_cannot_view_plugin',
			__( '抱歉，您不能管理此插件。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a given request has access to upload plugins.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to create items, GC_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_install_plugin',
				__( '抱歉，您不能在此站点上安装插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'inactive' !== $request['status'] && ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_activate_plugin',
				__( '抱歉，您不能启用插件。' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Uploads a plugin and optionally activates it.
	 *
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function create_item( $request ) {
		global $gc_filesystem;

		require_once ABSPATH . 'gc-admin/includes/file.php';
		require_once ABSPATH . 'gc-admin/includes/plugin.php';
		require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
		require_once ABSPATH . 'gc-admin/includes/plugin-install.php';

		$slug = $request['slug'];

		// Verify filesystem is accessible first.
		$filesystem_available = $this->is_filesystem_available();
		if ( is_gc_error( $filesystem_available ) ) {
			return $filesystem_available;
		}

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array(
					'sections'       => false,
					'language_packs' => true,
				),
			)
		);

		if ( is_gc_error( $api ) ) {
			if ( false !== strpos( $api->get_error_message(), '找不到插件。' ) ) {
				$api->add_data( array( 'status' => 404 ) );
			} else {
				$api->add_data( array( 'status' => 500 ) );
			}

			return $api;
		}

		$skin     = new GC_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->install( $api->download_link );

		if ( is_gc_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		// This should be the same as $result above.
		if ( is_gc_error( $skin->result ) ) {
			$skin->result->add_data( array( 'status' => 500 ) );

			return $skin->result;
		}

		if ( $skin->get_errors()->has_errors() ) {
			$error = $skin->get_errors();
			$error->add_data( array( 'status' => 500 ) );

			return $error;
		}

		if ( is_null( $result ) ) {
			// Pass through the error from GC_Filesystem if one was raised.
			if ( $gc_filesystem instanceof GC_Filesystem_Base
				&& is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors()
			) {
				return new GC_Error(
					'unable_to_connect_to_filesystem',
					$gc_filesystem->errors->get_error_message(),
					array( 'status' => 500 )
				);
			}

			return new GC_Error(
				'unable_to_connect_to_filesystem',
				__( '无法连接至文件系统。 请确认您的凭据。' ),
				array( 'status' => 500 )
			);
		}

		$file = $upgrader->plugin_info();

		if ( ! $file ) {
			return new GC_Error(
				'unable_to_determine_installed_plugin',
				__( '无法确定安装了什么插件。' ),
				array( 'status' => 500 )
			);
		}

		if ( 'inactive' !== $request['status'] ) {
			$can_change_status = $this->plugin_status_permission_check( $file, $request['status'], 'inactive' );

			if ( is_gc_error( $can_change_status ) ) {
				return $can_change_status;
			}

			$changed_status = $this->handle_plugin_status( $file, $request['status'], 'inactive' );

			if ( is_gc_error( $changed_status ) ) {
				return $changed_status;
			}
		}

		// Install translations.
		$installed_locales = array_values( get_available_languages() );
		/** This filter is documented in gc-includes/update.php */
		$installed_locales = apply_filters( 'plugins_update_check_locales', $installed_locales );

		$language_packs = array_map(
			static function( $item ) {
				return (object) $item;
			},
			$api->language_packs
		);

		$language_packs = array_filter(
			$language_packs,
			static function( $pack ) use ( $installed_locales ) {
				return in_array( $pack->language, $installed_locales, true );
			}
		);

		if ( $language_packs ) {
			$lp_upgrader = new Language_Pack_Upgrader( $skin );

			// Install all applicable language packs for the plugin.
			$lp_upgrader->bulk_upgrade( $language_packs );
		}

		$path          = GC_PLUGIN_DIR . '/' . $file;
		$data          = get_plugin_data( $path, false, false );
		$data['_file'] = $file;

		$response = $this->prepare_item_for_response( $data, $request );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, substr( $file, 0, - 4 ) ) ) );

		return $response;
	}

	/**
	 * Checks if a given request has access to update a specific plugin.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_manage_plugins',
				__( '抱歉，您不能在此站点上管理插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$can_read = $this->check_read_permission( $request['plugin'] );

		if ( is_gc_error( $can_read ) ) {
			return $can_read;
		}

		$status = $this->get_plugin_status( $request['plugin'] );

		if ( $request['status'] && $status !== $request['status'] ) {
			$can_change_status = $this->plugin_status_permission_check( $request['plugin'], $request['status'], $status );

			if ( is_gc_error( $can_change_status ) ) {
				return $can_change_status;
			}
		}

		return true;
	}

	/**
	 * Updates one plugin.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function update_item( $request ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$data = $this->get_plugin_data( $request['plugin'] );

		if ( is_gc_error( $data ) ) {
			return $data;
		}

		$status = $this->get_plugin_status( $request['plugin'] );

		if ( $request['status'] && $status !== $request['status'] ) {
			$handled = $this->handle_plugin_status( $request['plugin'], $request['status'], $status );

			if ( is_gc_error( $handled ) ) {
				return $handled;
			}
		}

		$this->update_additional_fields_for_object( $data, $request );

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( $data, $request );
	}

	/**
	 * Checks if a given request has access to delete a specific plugin.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to delete the item, GC_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_manage_plugins',
				__( '抱歉，您不能在此站点上管理插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! current_user_can( 'delete_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_manage_plugins',
				__( '抱歉，您不能在此站点上删除插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$can_read = $this->check_read_permission( $request['plugin'] );

		if ( is_gc_error( $can_read ) ) {
			return $can_read;
		}

		return true;
	}

	/**
	 * Deletes one plugin from the site.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function delete_item( $request ) {
		require_once ABSPATH . 'gc-admin/includes/file.php';
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$data = $this->get_plugin_data( $request['plugin'] );

		if ( is_gc_error( $data ) ) {
			return $data;
		}

		if ( is_plugin_active( $request['plugin'] ) ) {
			return new GC_Error(
				'rest_cannot_delete_active_plugin',
				__( '无法删除已启用的插件。 请先将其禁用。' ),
				array( 'status' => 400 )
			);
		}

		$filesystem_available = $this->is_filesystem_available();
		if ( is_gc_error( $filesystem_available ) ) {
			return $filesystem_available;
		}

		$prepared = $this->prepare_item_for_response( $data, $request );
		$deleted  = delete_plugins( array( $request['plugin'] ) );

		if ( is_gc_error( $deleted ) ) {
			$deleted->add_data( array( 'status' => 500 ) );

			return $deleted;
		}

		return new GC_REST_Response(
			array(
				'deleted'  => true,
				'previous' => $prepared->get_data(),
			)
		);
	}

	/**
	 * Prepares the plugin for the REST response.
	 *
	 *
	 * @param mixed           $item    Unmarked up and untranslated plugin data from {@see get_plugin_data()}.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$item   = _get_plugin_data_markup_translate( $item['_file'], $item, false );
		$marked = _get_plugin_data_markup_translate( $item['_file'], $item, true );

		$data = array(
			'plugin'       => substr( $item['_file'], 0, - 4 ),
			'status'       => $this->get_plugin_status( $item['_file'] ),
			'name'         => $item['Name'],
			'plugin_uri'   => $item['PluginURI'],
			'author'       => $item['Author'],
			'author_uri'   => $item['AuthorURI'],
			'description'  => array(
				'raw'      => $item['description'],
				'rendered' => $marked['description'],
			),
			'version'      => $item['Version'],
			'network_only' => $item['Network'],
			'requires_gc'  => $item['RequiresGC'],
			'requires_php' => $item['RequiresPHP'],
			'textdomain'   => $item['TextDomain'],
		);

		$data = $this->add_additional_fields_to_object( $data, $request );

		$response = new GC_REST_Response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		/**
		 * Filters plugin data for a REST API response.
		 *
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param array            $item     The plugin item from {@see get_plugin_data()}.
		 * @param GC_REST_Request  $request  The request object.
		 */
		return apply_filters( 'rest_prepare_plugin', $response, $item, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 *
	 * @param array $item The plugin item.
	 * @return array[]
	 */
	protected function prepare_links( $item ) {
		return array(
			'self' => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, substr( $item['_file'], 0, - 4 ) ) ),
			),
		);
	}

	/**
	 * Gets the plugin header data for a plugin.
	 *
	 *
	 * @param string $plugin The plugin file to get data for.
	 * @return array|GC_Error The plugin data, or a GC_Error if the plugin is not installed.
	 */
	protected function get_plugin_data( $plugin ) {
		$plugins = get_plugins();

		if ( ! isset( $plugins[ $plugin ] ) ) {
			return new GC_Error( 'rest_plugin_not_found', __( '找不到插件。' ), array( 'status' => 404 ) );
		}

		$data          = $plugins[ $plugin ];
		$data['_file'] = $plugin;

		return $data;
	}

	/**
	 * Get's the activation status for a plugin.
	 *
	 *
	 * @param string $plugin The plugin file to check.
	 * @return string Either 'network-active', 'active' or 'inactive'.
	 */
	protected function get_plugin_status( $plugin ) {
		if ( is_plugin_active_for_network( $plugin ) ) {
			return 'network-active';
		}

		if ( is_plugin_active( $plugin ) ) {
			return 'active';
		}

		return 'inactive';
	}

	/**
	 * Handle updating a plugin's status.
	 *
	 *
	 * @param string $plugin         The plugin file to update.
	 * @param string $new_status     The plugin's new status.
	 * @param string $current_status The plugin's current status.
	 * @return true|GC_Error
	 */
	protected function plugin_status_permission_check( $plugin, $new_status, $current_status ) {
		if ( is_multisite() && ( 'network-active' === $current_status || 'network-active' === $new_status ) && ! current_user_can( 'manage_network_plugins' ) ) {
			return new GC_Error(
				'rest_cannot_manage_network_plugins',
				__( '抱歉，您不能管理站点网络插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ( 'active' === $new_status || 'network-active' === $new_status ) && ! current_user_can( 'activate_plugin', $plugin ) ) {
			return new GC_Error(
				'rest_cannot_activate_plugin',
				__( '抱歉，您不能启用此插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'inactive' === $new_status && ! current_user_can( 'deactivate_plugin', $plugin ) ) {
			return new GC_Error(
				'rest_cannot_deactivate_plugin',
				__( '抱歉，您不能禁用此插件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Handle updating a plugin's status.
	 *
	 *
	 * @param string $plugin         The plugin file to update.
	 * @param string $new_status     The plugin's new status.
	 * @param string $current_status The plugin's current status.
	 * @return true|GC_Error
	 */
	protected function handle_plugin_status( $plugin, $new_status, $current_status ) {
		if ( 'inactive' === $new_status ) {
			deactivate_plugins( $plugin, false, 'network-active' === $current_status );

			return true;
		}

		if ( 'active' === $new_status && 'network-active' === $current_status ) {
			return true;
		}

		$network_activate = 'network-active' === $new_status;

		if ( is_multisite() && ! $network_activate && is_network_only_plugin( $plugin ) ) {
			return new GC_Error(
				'rest_network_only_plugin',
				__( '仅供站点网络使用的插件，必须于站点网络启用。' ),
				array( 'status' => 400 )
			);
		}

		$activated = activate_plugin( $plugin, '', $network_activate );

		if ( is_gc_error( $activated ) ) {
			$activated->add_data( array( 'status' => 500 ) );

			return $activated;
		}

		return true;
	}

	/**
	 * Checks that the "plugin" parameter is a valid path.
	 *
	 *
	 * @param string $file The plugin file parameter.
	 * @return bool
	 */
	public function validate_plugin_param( $file ) {
		if ( ! is_string( $file ) || ! preg_match( '/' . self::PATTERN . '/u', $file ) ) {
			return false;
		}

		$validated = validate_file( plugin_basename( $file ) );

		return 0 === $validated;
	}

	/**
	 * Sanitizes the "plugin" parameter to be a proper plugin file with ".php" appended.
	 *
	 *
	 * @param string $file The plugin file parameter.
	 * @return string
	 */
	public function sanitize_plugin_param( $file ) {
		return plugin_basename( sanitize_text_field( $file . '.php' ) );
	}

	/**
	 * Checks if the plugin matches the requested parameters.
	 *
	 *
	 * @param GC_REST_Request $request The request to require the plugin matches against.
	 * @param array           $item    The plugin item.
	 * @return bool
	 */
	protected function does_plugin_match_request( $request, $item ) {
		$search = $request['search'];

		if ( $search ) {
			$matched_search = false;

			foreach ( $item as $field ) {
				if ( is_string( $field ) && false !== strpos( strip_tags( $field ), $search ) ) {
					$matched_search = true;
					break;
				}
			}

			if ( ! $matched_search ) {
				return false;
			}
		}

		$status = $request['status'];

		if ( $status && ! in_array( $this->get_plugin_status( $item['_file'] ), $status, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the plugin is installed.
	 *
	 *
	 * @param string $plugin The plugin file.
	 * @return bool
	 */
	protected function is_plugin_installed( $plugin ) {
		return file_exists( GC_PLUGIN_DIR . '/' . $plugin );
	}

	/**
	 * Determine if the endpoints are available.
	 *
	 * Only the 'Direct' filesystem transport, and SSH/FTP when credentials are stored are supported at present.
	 *
	 *
	 * @return true|GC_Error True if filesystem is available, GC_Error otherwise.
	 */
	protected function is_filesystem_available() {
		$filesystem_method = get_filesystem_method();

		if ( 'direct' === $filesystem_method ) {
			return true;
		}

		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
		ob_end_clean();

		if ( $filesystem_credentials_are_stored ) {
			return true;
		}

		return new GC_Error( 'fs_unavailable', __( '该文件系统当前不可用于管理插件。' ), array( 'status' => 500 ) );
	}

	/**
	 * Retrieves the plugin's schema, conforming to JSON Schema.
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
			'title'      => 'plugin',
			'type'       => 'object',
			'properties' => array(
				'plugin'       => array(
					'description' => __( '插件文件。' ),
					'type'        => 'string',
					'pattern'     => self::PATTERN,
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'status'       => array(
					'description' => __( '插件启用状态。' ),
					'type'        => 'string',
					'enum'        => is_multisite() ? array( 'inactive', 'active', 'network-active' ) : array( 'inactive', 'active' ),
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'name'         => array(
					'description' => __( '插件名称。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'plugin_uri'   => array(
					'description' => __( '插件的网站地址。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'author'       => array(
					'description' => __( '插件作者。' ),
					'type'        => 'object',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'author_uri'   => array(
					'description' => __( '插件作者的网站地址。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'description'  => array(
					'description' => __( '插件说明。' ),
					'type'        => 'object',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( '原始插件说明。' ),
							'type'        => 'string',
						),
						'rendered' => array(
							'description' => __( '插件说明已格式化以供显示。' ),
							'type'        => 'string',
						),
					),
				),
				'version'      => array(
					'description' => __( '插件版本号。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'network_only' => array(
					'description' => __( '插件是否只能在站点网络中启用。' ),
					'type'        => 'boolean',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'requires_gc'  => array(
					'description' => __( '最低要求的GeChiUI版本。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'requires_php' => array(
					'description' => __( '最低要求的PHP版本。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'textdomain'   => array(
					'description' => __( '插件的文本域。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['status'] = array(
			'description' => __( '限制结果集为具有给定状态的插件。' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
				'enum' => is_multisite() ? array( 'inactive', 'active', 'network-active' ) : array( 'inactive', 'active' ),
			),
		);

		unset( $query_params['page'], $query_params['per_page'] );

		return $query_params;
	}
}
