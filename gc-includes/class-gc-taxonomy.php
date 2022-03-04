<?php
/**
 * Taxonomy API: GC_Taxonomy class
 *
 * @package GeChiUI
 * @subpackage Taxonomy
 *
 */

/**
 * Core class used for interacting with taxonomies.
 *
 *
 */
final class GC_Taxonomy {
	/**
	 * Taxonomy key.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Name of the taxonomy shown in the menu. Usually plural.
	 *
	 * @var string
	 */
	public $label;

	/**
	 * Labels object for this taxonomy.
	 *
	 * If not set, tag labels are inherited for non-hierarchical types
	 * and category labels for hierarchical ones.
	 *
	 * @see get_taxonomy_labels()
	 *
	 * @var stdClass
	 */
	public $labels;

	/**
	 * A short descriptive summary of what the taxonomy is for.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * 此分类法是否可供公开使用，或是通过管理界面，或是由前端用户。
	 *
	 * @var bool
	 */
	public $public = true;

	/**
	 * 此分类法是否可公开查询。
	 *
	 * @var bool
	 */
	public $publicly_queryable = true;

	/**
	 * Whether the taxonomy is hierarchical.
	 *
	 * @var bool
	 */
	public $hierarchical = false;

	/**
	 * Whether to generate and allow a UI for managing terms in this taxonomy in the admin.
	 *
	 * @var bool
	 */
	public $show_ui = true;

	/**
	 * Whether to show the taxonomy in the admin menu.
	 *
	 * If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown.
	 *
	 * @var bool
	 */
	public $show_in_menu = true;

	/**
	 * Whether the taxonomy is available for selection in navigation menus.
	 *
	 * @var bool
	 */
	public $show_in_nav_menus = true;

	/**
	 * Whether to list the taxonomy in the tag cloud widget controls.
	 *
	 * @var bool
	 */
	public $show_tagcloud = true;

	/**
	 * 是否在快速/批量编辑面板显示分类法。
	 *
	 * @var bool
	 */
	public $show_in_quick_edit = true;

	/**
	 * Whether to display a column for the taxonomy on its post type listing screens.
	 *
	 * @var bool
	 */
	public $show_admin_column = false;

	/**
	 * The callback function for the meta box display.
	 *
	 * @var bool|callable
	 */
	public $meta_box_cb = null;

	/**
	 * The callback function for sanitizing taxonomy data saved from a meta box.
	 *
	 * @var callable
	 */
	public $meta_box_sanitize_cb = null;

	/**
	 * An array of object types this taxonomy is registered for.
	 *
	 * @var string[]
	 */
	public $object_type = null;

	/**
	 * Capabilities for this taxonomy.
	 *
	 * @var stdClass
	 */
	public $cap;

	/**
	 * Rewrites information for this taxonomy.
	 *
	 * @var array|false
	 */
	public $rewrite;

	/**
	 * Query var string for this taxonomy.
	 *
	 * @var string|false
	 */
	public $query_var;

	/**
	 * Function that will be called when the count is updated.
	 *
	 * @var callable
	 */
	public $update_count_callback;

	/**
	 * Whether this taxonomy should appear in the REST API.
	 *
	 * Default false. If true, standard endpoints will be registered with
	 * respect to $rest_base and $rest_controller_class.
	 *
	 * @var bool $show_in_rest
	 */
	public $show_in_rest;

	/**
	 * The base path for this taxonomy's REST API endpoints.
	 *
	 * @var string|bool $rest_base
	 */
	public $rest_base;

	/**
	 * The namespace for this taxonomy's REST API endpoints.
	 *
	 * @var string|bool $rest_namespace
	 */
	public $rest_namespace;

	/**
	 * The controller for this taxonomy's REST API endpoints.
	 *
	 * Custom controllers must extend GC_REST_Controller.
	 *
	 * @var string|bool $rest_controller_class
	 */
	public $rest_controller_class;

	/**
	 * The controller instance for this taxonomy's REST API endpoints.
	 *
	 * Lazily computed. Should be accessed using {@see GC_Taxonomy::get_rest_controller()}.
	 *
	 * @var GC_REST_Controller $rest_controller
	 */
	public $rest_controller;

	/**
	 * The default term name for this taxonomy. If you pass an array you have
	 * to set 'name' and optionally 'slug' and 'description'.
	 *
	 * @var array|string
	 */
	public $default_term;

	/**
	 * Whether terms in this taxonomy should be sorted in the order they are provided to `gc_set_object_terms()`.
	 *
	 * Use this in combination with `'orderby' => 'term_order'` when fetching terms.
	 *
	 * @var bool|null
	 */
	public $sort = null;

	/**
	 * Array of arguments to automatically use inside `gc_get_object_terms()` for this taxonomy.
	 *
	 * @var array|null
	 */
	public $args = null;

	/**
	 * Whether it is a built-in taxonomy.
	 *
	 * @var bool
	 */
	public $_builtin;

	/**
	 * Constructor.
	 *
	 * See the register_taxonomy() function for accepted arguments for `$args`.
	 *
	 *
	 * @global GC $gc Current GeChiUI environment instance.
	 *
	 * @param string       $taxonomy    Taxonomy key, must not exceed 32 characters.
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args        Optional. Array or query string of arguments for registering a taxonomy.
	 *                                  Default empty array.
	 */
	public function __construct( $taxonomy, $object_type, $args = array() ) {
		$this->name = $taxonomy;

		$this->set_props( $object_type, $args );
	}

	/**
	 * Sets taxonomy properties.
	 *
	 * See the register_taxonomy() function for accepted arguments for `$args`.
	 *
	 *
	 * @param string|string[] $object_type Name or array of names of the object types for the taxonomy.
	 * @param array|string    $args        Array or query string of arguments for registering a taxonomy.
	 */
	public function set_props( $object_type, $args ) {
		$args = gc_parse_args( $args );

		/**
		 * Filters the arguments for registering a taxonomy.
		 *
		 *
		 * @param array    $args        Array of arguments for registering a taxonomy.
		 *                              See the register_taxonomy() function for accepted arguments.
		 * @param string   $taxonomy    Taxonomy key.
		 * @param string[] $object_type Array of names of object types for the taxonomy.
		 */
		$args = apply_filters( 'register_taxonomy_args', $args, $this->name, (array) $object_type );

		$defaults = array(
			'labels'                => array(),
			'description'           => '',
			'public'                => true,
			'publicly_queryable'    => null,
			'hierarchical'          => false,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => null,
			'show_tagcloud'         => null,
			'show_in_quick_edit'    => null,
			'show_admin_column'     => false,
			'meta_box_cb'           => null,
			'meta_box_sanitize_cb'  => null,
			'capabilities'          => array(),
			'rewrite'               => true,
			'query_var'             => $this->name,
			'update_count_callback' => '',
			'show_in_rest'          => false,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
			'default_term'          => null,
			'sort'                  => null,
			'args'                  => null,
			'_builtin'              => false,
		);

		$args = array_merge( $defaults, $args );

		// If not set, default to the setting for 'public'.
		if ( null === $args['publicly_queryable'] ) {
			$args['publicly_queryable'] = $args['public'];
		}

		if ( false !== $args['query_var'] && ( is_admin() || false !== $args['publicly_queryable'] ) ) {
			if ( true === $args['query_var'] ) {
				$args['query_var'] = $this->name;
			} else {
				$args['query_var'] = sanitize_title_with_dashes( $args['query_var'] );
			}
		} else {
			// Force 'query_var' to false for non-public taxonomies.
			$args['query_var'] = false;
		}

		if ( false !== $args['rewrite'] && ( is_admin() || get_option( 'permalink_structure' ) ) ) {
			$args['rewrite'] = gc_parse_args(
				$args['rewrite'],
				array(
					'with_front'   => true,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE,
				)
			);

			if ( empty( $args['rewrite']['slug'] ) ) {
				$args['rewrite']['slug'] = sanitize_title_with_dashes( $this->name );
			}
		}

		// If not set, default to the setting for 'public'.
		if ( null === $args['show_ui'] ) {
			$args['show_ui'] = $args['public'];
		}

		// If not set, default to the setting for 'show_ui'.
		if ( null === $args['show_in_menu'] || ! $args['show_ui'] ) {
			$args['show_in_menu'] = $args['show_ui'];
		}

		// If not set, default to the setting for 'public'.
		if ( null === $args['show_in_nav_menus'] ) {
			$args['show_in_nav_menus'] = $args['public'];
		}

		// If not set, default to the setting for 'show_ui'.
		if ( null === $args['show_tagcloud'] ) {
			$args['show_tagcloud'] = $args['show_ui'];
		}

		// If not set, default to the setting for 'show_ui'.
		if ( null === $args['show_in_quick_edit'] ) {
			$args['show_in_quick_edit'] = $args['show_ui'];
		}

		// If not set, default rest_namespace to gc/v2 if show_in_rest is true.
		if ( false === $args['rest_namespace'] && ! empty( $args['show_in_rest'] ) ) {
			$args['rest_namespace'] = 'gc/v2';
		}

		$default_caps = array(
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_posts',
		);

		$args['cap'] = (object) array_merge( $default_caps, $args['capabilities'] );
		unset( $args['capabilities'] );

		$args['object_type'] = array_unique( (array) $object_type );

		// If not set, use the default meta box.
		if ( null === $args['meta_box_cb'] ) {
			if ( $args['hierarchical'] ) {
				$args['meta_box_cb'] = 'post_categories_meta_box';
			} else {
				$args['meta_box_cb'] = 'post_tags_meta_box';
			}
		}

		$args['name'] = $this->name;

		// Default meta box sanitization callback depends on the value of 'meta_box_cb'.
		if ( null === $args['meta_box_sanitize_cb'] ) {
			switch ( $args['meta_box_cb'] ) {
				case 'post_categories_meta_box':
					$args['meta_box_sanitize_cb'] = 'taxonomy_meta_box_sanitize_cb_checkboxes';
					break;

				case 'post_tags_meta_box':
				default:
					$args['meta_box_sanitize_cb'] = 'taxonomy_meta_box_sanitize_cb_input';
					break;
			}
		}

		// Default taxonomy term.
		if ( ! empty( $args['default_term'] ) ) {
			if ( ! is_array( $args['default_term'] ) ) {
				$args['default_term'] = array( 'name' => $args['default_term'] );
			}
			$args['default_term'] = gc_parse_args(
				$args['default_term'],
				array(
					'name'        => '',
					'slug'        => '',
					'description' => '',
				)
			);
		}

		foreach ( $args as $property_name => $property_value ) {
			$this->$property_name = $property_value;
		}

		$this->labels = get_taxonomy_labels( $this );
		$this->label  = $this->labels->name;
	}

	/**
	 * Adds the necessary rewrite rules for the taxonomy.
	 *
	 *
	 * @global GC $gc Current GeChiUI environment instance.
	 */
	public function add_rewrite_rules() {
		/* @var GC $gc */
		global $gc;

		// Non-publicly queryable taxonomies should not register query vars, except in the admin.
		if ( false !== $this->query_var && $gc ) {
			$gc->add_query_var( $this->query_var );
		}

		if ( false !== $this->rewrite && ( is_admin() || get_option( 'permalink_structure' ) ) ) {
			if ( $this->hierarchical && $this->rewrite['hierarchical'] ) {
				$tag = '(.+?)';
			} else {
				$tag = '([^/]+)';
			}

			add_rewrite_tag( "%$this->name%", $tag, $this->query_var ? "{$this->query_var}=" : "taxonomy=$this->name&term=" );
			add_permastruct( $this->name, "{$this->rewrite['slug']}/%$this->name%", $this->rewrite );
		}
	}

	/**
	 * Removes any rewrite rules, permastructs, and rules for the taxonomy.
	 *
	 *
	 * @global GC $gc Current GeChiUI environment instance.
	 */
	public function remove_rewrite_rules() {
		/* @var GC $gc */
		global $gc;

		// Remove query var.
		if ( false !== $this->query_var ) {
			$gc->remove_query_var( $this->query_var );
		}

		// Remove rewrite tags and permastructs.
		if ( false !== $this->rewrite ) {
			remove_rewrite_tag( "%$this->name%" );
			remove_permastruct( $this->name );
		}
	}

	/**
	 * Registers the ajax callback for the meta box.
	 *
	 */
	public function add_hooks() {
		add_filter( 'gc_ajax_add-' . $this->name, '_gc_ajax_add_hierarchical_term' );
	}

	/**
	 * Removes the ajax callback for the meta box.
	 *
	 */
	public function remove_hooks() {
		remove_filter( 'gc_ajax_add-' . $this->name, '_gc_ajax_add_hierarchical_term' );
	}

	/**
	 * Gets the REST API controller for this taxonomy.
	 *
	 * Will only instantiate the controller class once per request.
	 *
	 *
	 * @return GC_REST_Controller|null The controller instance, or null if the taxonomy
	 *                                 is set not to show in rest.
	 */
	public function get_rest_controller() {
		if ( ! $this->show_in_rest ) {
			return null;
		}

		$class = $this->rest_controller_class ? $this->rest_controller_class : GC_REST_Terms_Controller::class;

		if ( ! class_exists( $class ) ) {
			return null;
		}

		if ( ! is_subclass_of( $class, GC_REST_Controller::class ) ) {
			return null;
		}

		if ( ! $this->rest_controller ) {
			$this->rest_controller = new $class( $this->name );
		}

		if ( ! ( $this->rest_controller instanceof $class ) ) {
			return null;
		}

		return $this->rest_controller;
	}
}
