<?php
/**
 * REST API: GC_REST_Block_Directory_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 * @since 5.5.0
 */

/**
 * Controller which provides REST endpoint for the blocks.
 *
 * @since 5.5.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Block_Directory_Controller extends GC_REST_Controller {

	/**
	 * Constructs the controller.
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'block-directory';
	}

	/**
	 * Registers the necessary REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/search',
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
	}

	/**
	 * Checks whether a given request has permission to install and activate plugins.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has permission, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			return new GC_Error(
				'rest_block_directory_cannot_view',
				__( '抱歉，您不能浏览区块目录。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Search and retrieve blocks metadata
	 *
	 * @since 5.5.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		require_once ABSPATH . 'gc-admin/includes/plugin-install.php';
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$response = plugins_api(
			'query_plugins',
			array(
				'block'    => $request['term'],
				'per_page' => $request['per_page'],
				'page'     => $request['page'],
			)
		);

		if ( is_gc_error( $response ) ) {
			$response->add_data( array( 'status' => 500 ) );

			return $response;
		}

		$result = array();

		foreach ( $response->plugins as $plugin ) {
			// If the API returned a plugin with empty data for 'blocks', skip it.
			if ( empty( $plugin['blocks'] ) ) {
				continue;
			}

			$data     = $this->prepare_item_for_response( $plugin, $request );
			$result[] = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Parse block metadata for a block, and prepare it for an API response.
	 *
	 * @since 5.5.0
	 * @since 5.9.0 Renamed `$plugin` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param array           $item    The plugin metadata.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$plugin = $item;

		$fields = $this->get_fields_for_response( $request );

		// There might be multiple blocks in a plugin. Only the first block is mapped.
		$block_data = reset( $plugin['blocks'] );

		// A data array containing the properties we'll return.
		$block = array(
			'name'                => $block_data['name'],
			'title'               => ( $block_data['title'] ? $block_data['title'] : $plugin['name'] ),
			'description'         => gc_trim_words( $plugin['short_description'], 30, '...' ),
			'id'                  => $plugin['slug'],
			'rating'              => $plugin['rating'] / 20,
			'rating_count'        => (int) $plugin['num_ratings'],
			'active_installs'     => (int) $plugin['active_installs'],
			'author_block_rating' => $plugin['author_block_rating'] / 20,
			'author_block_count'  => (int) $plugin['author_block_count'],
			'author'              => gc_strip_all_tags( $plugin['author'] ),
			'icon'                => ( isset( $plugin['icons']['1x'] ) ? $plugin['icons']['1x'] : 'block-default' ),
			'last_updated'        => gmdate( 'Y-m-d\TH:i:s', strtotime( $plugin['last_updated'] ) ),
			'humanized_updated'   => sprintf(
				/* translators: %s: Human-readable time difference. */
				__( '%s前' ),
				human_time_diff( strtotime( $plugin['last_updated'] ) )
			),
		);

		$this->add_additional_fields_to_object( $block, $request );

		$response = new GC_REST_Response( $block );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $plugin ) );
		}

		return $response;
	}

	/**
	 * Generates a list of links to include in the response for the plugin.
	 *
	 * @since 5.5.0
	 *
	 * @param array $plugin The plugin data from www.GeChiUI.com.
	 * @return array
	 */
	protected function prepare_links( $plugin ) {
		$links = array(
			'https://api.w.org/install-plugin' => array(
				'href' => add_query_arg( 'slug', urlencode( $plugin['slug'] ), rest_url( 'gc/v2/plugins' ) ),
			),
		);

		$plugin_file = $this->find_plugin_for_slug( $plugin['slug'] );

		if ( $plugin_file ) {
			$links['https://api.w.org/plugin'] = array(
				'href'       => rest_url( 'gc/v2/plugins/' . substr( $plugin_file, 0, - 4 ) ),
				'embeddable' => true,
			);
		}

		return $links;
	}

	/**
	 * Finds an installed plugin for the given slug.
	 *
	 * @since 5.5.0
	 *
	 * @param string $slug The www.GeChiUI.com directory slug for a plugin.
	 * @return string The plugin file found matching it.
	 */
	protected function find_plugin_for_slug( $slug ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';

		$plugin_files = get_plugins( '/' . $slug );

		if ( ! $plugin_files ) {
			return '';
		}

		$plugin_files = array_keys( $plugin_files );

		return $slug . '/' . reset( $plugin_files );
	}

	/**
	 * Retrieves the theme's schema, conforming to JSON Schema.
	 *
	 * @since 5.5.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'block-directory-item',
			'type'       => 'object',
			'properties' => array(
				'name'                => array(
					'description' => __( '以命名空间/区块名称的格式来命名区块。' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'title'               => array(
					'description' => __( '以人类可读的格式撰写区块标题。' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'description'         => array(
					'description' => __( '以人类可读的格式撰写该区块的简短描述。' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'id'                  => array(
					'description' => __( '区块别名' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'rating'              => array(
					'description' => __( '区块的星级等级。' ),
					'type'        => 'number',
					'context'     => array( 'view' ),
				),
				'rating_count'        => array(
					'description' => __( '评分数。' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'active_installs'     => array(
					'description' => __( '启用此区块的系统数量。' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'author_block_rating' => array(
					'description' => __( '同一作者发布的区块的平均评级。' ),
					'type'        => 'number',
					'context'     => array( 'view' ),
				),
				'author_block_count'  => array(
					'description' => __( '同一作者发布的区块数。' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'author'              => array(
					'description' => __( '区块作者的www.GeChiUI.com用户名。' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
				'icon'                => array(
					'description' => __( '区块图标' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view' ),
				),
				'last_updated'        => array(
					'description' => __( '区块上次更新的日期。' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
				),
				'humanized_updated'   => array(
					'description' => __( '以模糊的人类可读格式撰写该区块最后一次更新的日期。' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search params for the blocks collection.
	 *
	 * @since 5.5.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['term'] = array(
			'description' => __( '将结果集限制为匹配搜索词的区块。' ),
			'type'        => 'string',
			'required'    => true,
			'minLength'   => 1,
		);

		unset( $query_params['search'] );

		/**
		 * Filters REST API collection parameters for the block directory controller.
		 *
		 * @since 5.5.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_block_directory_collection_params', $query_params );
	}
}
