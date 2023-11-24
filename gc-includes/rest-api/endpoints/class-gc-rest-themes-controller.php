<?php
/**
 * REST API: GC_REST_Themes_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class used to manage themes via the REST API.
 *
 * @since 5.0.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Themes_Controller extends GC_REST_Controller {

	/**
	 * Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
	 * Excludes invalid directory name characters: `/:<>*?"|`.
	 */
	const PATTERN = '[^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?';

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'themes';
	}

	/**
	 * Registers the routes for themes.
	 *
	 * @since 5.0.0
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
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			sprintf( '/%s/(?P<stylesheet>%s)', $this->rest_base, self::PATTERN ),
			array(
				'args'   => array(
					'stylesheet' => array(
						'description'       => __( "主题的样式表。这是主题的唯一标识。" ),
						'type'              => 'string',
						'sanitize_callback' => array( $this, '_sanitize_stylesheet_callback' ),
					),
				),
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Sanitize the stylesheet to decode endpoint.
	 *
	 * @since 5.9.0
	 *
	 * @param string $stylesheet The stylesheet name.
	 * @return string Sanitized stylesheet.
	 */
	public function _sanitize_stylesheet_callback( $stylesheet ) {
		return urldecode( $stylesheet );
	}

	/**
	 * Checks if a given request has access to read the theme.
	 *
	 * @since 5.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access for the item, otherwise GC_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		if ( current_user_can( 'switch_themes' ) || current_user_can( 'manage_network_themes' ) ) {
			return true;
		}

		$registered = $this->get_collection_params();
		if ( isset( $registered['status'], $request['status'] ) && is_array( $request['status'] ) && array( 'active' ) === $request['status'] ) {
			return $this->check_read_active_theme_permission();
		}

		return new GC_Error(
			'rest_cannot_view_themes',
			__( '抱歉，您不能查看主题。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a given request has access to read the theme.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return bool|GC_Error True if the request has read access for the item, otherwise GC_Error object.
	 */
	public function get_item_permissions_check( $request ) {
		if ( current_user_can( 'switch_themes' ) || current_user_can( 'manage_network_themes' ) ) {
			return true;
		}

		$gc_theme      = gc_get_theme( $request['stylesheet'] );
		$current_theme = gc_get_theme();

		if ( $this->is_same_theme( $gc_theme, $current_theme ) ) {
			return $this->check_read_active_theme_permission();
		}

		return new GC_Error(
			'rest_cannot_view_themes',
			__( '抱歉，您不能查看主题。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a theme can be read.
	 *
	 * @since 5.7.0
	 *
	 * @return bool|GC_Error Whether the theme can be read.
	 */
	protected function check_read_active_theme_permission() {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new GC_Error(
			'rest_cannot_view_active_theme',
			__( '抱歉，您不能查看已启用主题。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Retrieves a single theme.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_item( $request ) {
		$gc_theme = gc_get_theme( $request['stylesheet'] );
		if ( ! $gc_theme->exists() ) {
			return new GC_Error(
				'rest_theme_not_found',
				__( '未找到主题。' ),
				array( 'status' => 404 )
			);
		}
		$data = $this->prepare_item_for_response( $gc_theme, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a collection of themes.
	 *
	 * @since 5.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		$themes = array();

		$active_themes = gc_get_themes();
		$current_theme = gc_get_theme();
		$status        = $request['status'];

		foreach ( $active_themes as $theme_name => $theme ) {
			$theme_status = ( $this->is_same_theme( $theme, $current_theme ) ) ? 'active' : 'inactive';
			if ( is_array( $status ) && ! in_array( $theme_status, $status, true ) ) {
				continue;
			}

			$prepared = $this->prepare_item_for_response( $theme, $request );
			$themes[] = $this->prepare_response_for_collection( $prepared );
		}

		$response = rest_ensure_response( $themes );

		$response->header( 'X-GC-Total', count( $themes ) );
		$response->header( 'X-GC-TotalPages', 1 );

		return $response;
	}

	/**
	 * Prepares a single theme output for response.
	 *
	 * @since 5.0.0
	 * @since 5.9.0 Renamed `$theme` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param GC_Theme        $item    Theme object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$theme  = $item;
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( rest_is_field_included( 'stylesheet', $fields ) ) {
			$data['stylesheet'] = $theme->get_stylesheet();
		}

		if ( rest_is_field_included( 'template', $fields ) ) {
			/**
			 * Use the get_template() method, not the 'Template' header, for finding the template.
			 * The 'Template' header is only good for what was written in the style.css, while
			 * get_template() takes into account where GeChiUI actually located the theme and
			 * whether it is actually valid.
			 */
			$data['template'] = $theme->get_template();
		}

		$plain_field_mappings = array(
			'requires_php' => 'RequiresPHP',
			'requires_gc'  => 'RequiresGC',
			'textdomain'   => 'TextDomain',
			'version'      => 'Version',
		);

		foreach ( $plain_field_mappings as $field => $header ) {
			if ( rest_is_field_included( $field, $fields ) ) {
				$data[ $field ] = $theme->get( $header );
			}
		}

		if ( rest_is_field_included( 'screenshot', $fields ) ) {
			// Using $theme->get_screenshot() with no args to get absolute URL.
			$data['screenshot'] = $theme->get_screenshot() ? $theme->get_screenshot() : '';
		}

		$rich_field_mappings = array(
			'author'      => 'Author',
			'author_uri'  => 'AuthorURI',
			'description' => 'Description',
			'name'        => 'Name',
			'tags'        => 'Tags',
			'theme_uri'   => 'ThemeURI',
		);

		foreach ( $rich_field_mappings as $field => $header ) {
			if ( rest_is_field_included( "{$field}.raw", $fields ) ) {
				$data[ $field ]['raw'] = $theme->display( $header, false, true );
			}

			if ( rest_is_field_included( "{$field}.rendered", $fields ) ) {
				$data[ $field ]['rendered'] = $theme->display( $header );
			}
		}

		$current_theme = gc_get_theme();
		if ( rest_is_field_included( 'status', $fields ) ) {
			$data['status'] = ( $this->is_same_theme( $theme, $current_theme ) ) ? 'active' : 'inactive';
		}

		if ( rest_is_field_included( 'theme_supports', $fields ) && $this->is_same_theme( $theme, $current_theme ) ) {
			foreach ( get_registered_theme_features() as $feature => $config ) {
				if ( ! is_array( $config['show_in_rest'] ) ) {
					continue;
				}

				$name = $config['show_in_rest']['name'];

				if ( ! rest_is_field_included( "theme_supports.{$name}", $fields ) ) {
					continue;
				}

				if ( ! current_theme_supports( $feature ) ) {
					$data['theme_supports'][ $name ] = $config['show_in_rest']['schema']['default'];
					continue;
				}

				$support = get_theme_support( $feature );

				if ( isset( $config['show_in_rest']['prepare_callback'] ) ) {
					$prepare = $config['show_in_rest']['prepare_callback'];
				} else {
					$prepare = array( $this, 'prepare_theme_support' );
				}

				$prepared = $prepare( $support, $config, $feature, $request );

				if ( is_gc_error( $prepared ) ) {
					continue;
				}

				$data['theme_supports'][ $name ] = $prepared;
			}
		}

		if ( rest_is_field_included( 'is_block_theme', $fields ) ) {
			$data['is_block_theme'] = $theme->is_block_theme();
		}

		$data = $this->add_additional_fields_to_object( $data, $request );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $theme ) );
		}

		/**
		 * Filters theme data returned from the REST API.
		 *
		 * @since 5.0.0
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_Theme         $theme    Theme object used to create response.
		 * @param GC_REST_Request  $request  Request object.
		 */
		return apply_filters( 'rest_prepare_theme', $response, $theme, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_Theme $theme Theme data.
	 * @return array Links for the given block type.
	 */
	protected function prepare_links( $theme ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $theme->get_stylesheet() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		if ( $this->is_same_theme( $theme, gc_get_theme() ) ) {
			// This creates a record for the active theme if not existent.
			$id = GC_Theme_JSON_Resolver::get_user_global_styles_post_id();
		} else {
			$user_cpt = GC_Theme_JSON_Resolver::get_user_data_from_gc_global_styles( $theme );
			$id       = isset( $user_cpt['ID'] ) ? $user_cpt['ID'] : null;
		}

		if ( $id ) {
			$links['https://api.w.org/user-global-styles'] = array(
				'href' => rest_url( 'gc/v2/global-styles/' . $id ),
			);
		}

		return $links;
	}

	/**
	 * Helper function to compare two themes.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_Theme $theme_a First theme to compare.
	 * @param GC_Theme $theme_b Second theme to compare.
	 * @return bool
	 */
	protected function is_same_theme( $theme_a, $theme_b ) {
		return $theme_a->get_stylesheet() === $theme_b->get_stylesheet();
	}

	/**
	 * Prepares the theme support value for inclusion in the REST API response.
	 *
	 * @since 5.5.0
	 *
	 * @param mixed           $support The raw value from get_theme_support().
	 * @param array           $args    The feature's registration args.
	 * @param string          $feature The feature name.
	 * @param GC_REST_Request $request The request object.
	 * @return mixed The prepared support value.
	 */
	protected function prepare_theme_support( $support, $args, $feature, $request ) {
		$schema = $args['show_in_rest']['schema'];

		if ( 'boolean' === $schema['type'] ) {
			return true;
		}

		if ( is_array( $support ) && ! $args['variadic'] ) {
			$support = $support[0];
		}

		return rest_sanitize_value_from_schema( $support, $schema );
	}

	/**
	 * Retrieves the theme's schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'theme',
			'type'       => 'object',
			'properties' => array(
				'stylesheet'     => array(
					'description' => __( '主题的样式表。这是主题的唯一标识。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'template'       => array(
					'description' => __( '主题的模板。如果这是一个子主题，则其指代父主题；否则，其与此主题的样式表相同。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'author'         => array(
					'description' => __( '主题作者。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题作者名称，在主题标头中找到。' ),
							'type'        => 'string',
						),
						'rendered' => array(
							'description' => __( '主题作者的HTML信息，经转换后用于显示。' ),
							'type'        => 'string',
						),
					),
				),
				'author_uri'     => array(
					'description' => __( '主题作者的网站。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题作者的网站，在主题标头中找到。' ),
							'type'        => 'string',
							'format'      => 'uri',
						),
						'rendered' => array(
							'description' => __( '主题作者的网站，经转换后用于显示。' ),
							'type'        => 'string',
							'format'      => 'uri',
						),
					),
				),
				'description'    => array(
					'description' => __( '主题的描述。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题描述，在主题标头中找到。' ),
							'type'        => 'string',
						),
						'rendered' => array(
							'description' => __( '主题描述，经转换后用于显示。' ),
							'type'        => 'string',
						),
					),
				),
				'is_block_theme' => array(
					'description' => __( '主题是否是基于块（block-based）的主题。' ),
					'type'        => 'boolean',
					'readonly'    => true,
				),
				'name'           => array(
					'description' => __( '主题名称。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题名称，在主题标头中找到。' ),
							'type'        => 'string',
						),
						'rendered' => array(
							'description' => __( '主题名称，经转换后用于显示。' ),
							'type'        => 'string',
						),
					),
				),
				'requires_php'   => array(
					'description' => __( '主题工作所需的最低PHP版本。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'requires_gc'    => array(
					'description' => __( '主题工作所需的最低GeChiUI版本。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'screenshot'     => array(
					'description' => __( '主题的截屏URL。' ),
					'type'        => 'string',
					'format'      => 'uri',
					'readonly'    => true,
				),
				'tags'           => array(
					'description' => __( '描述主题样式和功能的标签。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题标签，在主题标头中找到。' ),
							'type'        => 'array',
							'items'       => array(
								'type' => 'string',
							),
						),
						'rendered' => array(
							'description' => __( '主题标签，经转换后用于显示。' ),
							'type'        => 'string',
						),
					),
				),
				'textdomain'     => array(
					'description' => __( '主题的文本域。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'theme_supports' => array(
					'description' => __( '此主题支持的功能。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(),
				),
				'theme_uri'      => array(
					'description' => __( '主题页面URI链接。' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'raw'      => array(
							'description' => __( '主题网页的URI，在主题标头中找到。' ),
							'type'        => 'string',
							'format'      => 'uri',
						),
						'rendered' => array(
							'description' => __( '主题网页的URI，经转换后用于显示。' ),
							'type'        => 'string',
							'format'      => 'uri',
						),
					),
				),
				'version'        => array(
					'description' => __( '主题当前版本。' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'status'         => array(
					'description' => __( '主题的命名状态。' ),
					'type'        => 'string',
					'enum'        => array( 'inactive', 'active' ),
				),
			),
		);

		foreach ( get_registered_theme_features() as $feature => $config ) {
			if ( ! is_array( $config['show_in_rest'] ) ) {
				continue;
			}

			$name = $config['show_in_rest']['name'];

			$schema['properties']['theme_supports']['properties'][ $name ] = $config['show_in_rest']['schema'];
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search params for the themes collection.
	 *
	 * @since 5.0.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = array(
			'status' => array(
				'description' => __( '将结果集限制为指定了一个或多个状态的主题。' ),
				'type'        => 'array',
				'items'       => array(
					'enum' => array( 'active', 'inactive' ),
					'type' => 'string',
				),
			),
		);

		/**
		 * Filters REST API collection parameters for the themes controller.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_themes_collection_params', $query_params );
	}

	/**
	 * Sanitizes and validates the list of theme status.
	 *
	 * @since 5.0.0
	 * @deprecated 5.7.0
	 *
	 * @param string|array    $statuses  One or more theme statuses.
	 * @param GC_REST_Request $request   Full details about the request.
	 * @param string          $parameter Additional parameter to pass to validation.
	 * @return array|GC_Error A list of valid statuses, otherwise GC_Error object.
	 */
	public function sanitize_theme_status( $statuses, $request, $parameter ) {
		_deprecated_function( __METHOD__, '5.7.0' );

		$statuses = gc_parse_slug_list( $statuses );

		foreach ( $statuses as $status ) {
			$result = rest_validate_request_arg( $status, $request, $parameter );

			if ( is_gc_error( $result ) ) {
				return $result;
			}
		}

		return $statuses;
	}
}
