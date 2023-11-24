<?php
/**
 * REST API: GC_REST_Block_Patterns_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 * @since      6.0.0
 */

/**
 * Core class used to access block patterns via the REST API.
 *
 * @since 6.0.0
 *
 * @see GC_REST_Controller
 */
class GC_REST_Block_Patterns_Controller extends GC_REST_Controller {

	/**
	 * Defines whether remote patterns should be loaded.
	 *
	 * @since 6.0.0
	 * @var bool
	 */
	private $remote_patterns_loaded;

	/**
	 * An array that maps old categories names to new ones.
	 *
	 * @since 6.2.0
	 * @var array
	 */
	protected static $categories_migration = array(
		'buttons' => 'call-to-action',
		'columns' => 'text',
		'query'   => 'posts',
	);

	/**
	 * Constructs the controller.
	 *
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->namespace = 'gc/v2';
		$this->rest_base = 'block-patterns/patterns';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 6.0.0
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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read block patterns.
	 *
	 * @since 6.0.0
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new GC_Error(
			'rest_cannot_view',
			__( '抱歉，您无法查看已注册的区块样板。' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Retrieves all block patterns.
	 *
	 * @since 6.0.0
	 * @since 6.2.0 Added migration for old core pattern categories to the new ones.
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function get_items( $request ) {
		if ( ! $this->remote_patterns_loaded ) {
			// Load block patterns from w.org.
			_load_remote_block_patterns(); // Patterns with the `core` keyword.
			_load_remote_featured_patterns(); // Patterns in the `featured` category.
			_register_remote_theme_patterns(); // Patterns requested by current theme.

			$this->remote_patterns_loaded = true;
		}

		$response = array();
		$patterns = GC_Block_Patterns_Registry::get_instance()->get_all_registered();
		foreach ( $patterns as $pattern ) {
			$migrated_pattern = $this->migrate_pattern_categories( $pattern );
			$prepared_pattern = $this->prepare_item_for_response( $migrated_pattern, $request );
			$response[]       = $this->prepare_response_for_collection( $prepared_pattern );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Migrates old core pattern categories to the new categories.
	 *
	 * Core pattern categories are revamped. Migration is needed to ensure
	 * backwards compatibility.
	 *
	 * @since 6.2.0
	 *
	 * @param array $pattern Raw pattern as registered, before applying any changes.
	 * @return array Migrated pattern.
	 */
	protected function migrate_pattern_categories( $pattern ) {
		// No categories to migrate.
		if (
			! isset( $pattern['categories'] ) ||
			! is_array( $pattern['categories'] )
		) {
			return $pattern;
		}

		foreach ( $pattern['categories'] as $index => $category ) {
			// If the category exists as a key, then it needs migration.
			if ( isset( static::$categories_migration[ $category ] ) ) {
				$pattern['categories'][ $index ] = static::$categories_migration[ $category ];
			}
		}

		return $pattern;
	}

	/**
	 * Prepare a raw block pattern before it gets output in a REST API response.
	 *
	 * @since 6.0.0
	 * @since 6.3.0 Added `source` property.
	 *
	 * @param array           $item    Raw pattern as registered, before any changes.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response|GC_Error Response object on success, or GC_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields = $this->get_fields_for_response( $request );
		$keys   = array(
			'name'          => 'name',
			'title'         => 'title',
			'content'       => 'content',
			'description'   => 'description',
			'viewportWidth' => 'viewport_width',
			'inserter'      => 'inserter',
			'categories'    => 'categories',
			'keywords'      => 'keywords',
			'blockTypes'    => 'block_types',
			'postTypes'     => 'post_types',
			'templateTypes' => 'template_types',
			'source'        => 'source',
		);
		$data   = array();
		foreach ( $keys as $item_key => $rest_key ) {
			if ( isset( $item[ $item_key ] ) && rest_is_field_included( $rest_key, $fields ) ) {
				$data[ $rest_key ] = $item[ $item_key ];
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );
		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the block pattern schema, conforming to JSON Schema.
	 *
	 * @since 6.0.0
	 * @since 6.3.0 Added `source` property.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'block-pattern',
			'type'       => 'object',
			'properties' => array(
				'name'           => array(
					'description' => __( '区块样板名称。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'title'          => array(
					'description' => __( '区块样板标题，人类可读的格式。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'content'        => array(
					'description' => __( '区块样板的内容。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'description'    => array(
					'description' => __( '区块样板详细描述。' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'viewport_width' => array(
					'description' => __( '为区块插入器预览准备的区块样板视图宽度。' ),
					'type'        => 'number',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'inserter'       => array(
					'description' => __( '决定该样板在插入器中是否可见。' ),
					'type'        => 'boolean',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'categories'     => array(
					'description' => __( '区块样板的分类别名。' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'keywords'       => array(
					'description' => __( '区块样板关键字。' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'block_types'    => array(
					'description' => __( '区块样板搭配使用的区块类型。' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'post_types'     => array(
					'description' => __( '该样板被限制于与此组文章类型一起使用。' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'template_types' => array(
					'description' => __( 'An array of template types where the pattern fits.' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'source'         => array(
					'description' => __( '样板来自哪里，例如core' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
					'enum'        => array(
						'core',
						'plugin',
						'theme',
						'pattern-directory/core',
						'pattern-directory/theme',
						'pattern-directory/featured',
					),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
