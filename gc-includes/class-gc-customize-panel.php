<?php
/**
 * GeChiUI Customize Panel classes
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Panel class.
 *
 * A UI container for sections, managed by the GC_Customize_Manager.
 *
 *
 *
 * @see GC_Customize_Manager
 */
class GC_Customize_Panel {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @var int
	 */
	public $instance_number;

	/**
	 * GC_Customize_Manager instance.
	 *
	 * @var GC_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the panel, defining the display order of panels and sections.
	 *
	 * @var int
	 */
	public $priority = 160;

	/**
	 * Capability required for the panel.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme features required to support the panel.
	 *
	 * @var string|string[]
	 */
	public $theme_supports = '';

	/**
	 * Title of the panel to show in UI.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Auto-expand a section in a panel when the panel is expanded when the panel only has the one section.
	 *
	 * @var bool
	 */
	public $auto_expand_sole_section = false;

	/**
	 * Customizer sections for this panel.
	 *
	 * @var array
	 */
	public $sections;

	/**
	 * Type of this panel.
	 *
	 * @var string
	 */
	public $type = 'default';

	/**
	 * Active callback.
	 *
	 *
	 * @see GC_Customize_Section::active()
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               GC_Customize_Section, and returns bool to indicate whether
	 *               the section is active (such as it relates to the URL currently
	 *               being previewed).
	 */
	public $active_callback = '';

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID for the panel.
	 * @param array                $args    {
	 *     Optional. Array of properties for the new Panel object. Default empty array.
	 *
	 *     @type int             $priority        Priority of the panel, defining the display order
	 *                                            of panels and sections. Default 160.
	 *     @type string          $capability      Capability required for the panel.
	 *                                            Default `edit_theme_options`.
	 *     @type string|string[] $theme_supports  Theme features required to support the panel.
	 *     @type string          $title           Title of the panel to show in UI.
	 *     @type string          $description     Description to show in the UI.
	 *     @type string          $type            Type of the panel.
	 *     @type callable        $active_callback Active callback.
	 * }
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id      = $id;
		if ( empty( $this->active_callback ) ) {
			$this->active_callback = array( $this, 'active_callback' );
		}
		self::$instance_count += 1;
		$this->instance_number = self::$instance_count;

		$this->sections = array(); // Users cannot customize the $sections array.
	}

	/**
	 * Check whether panel is active to current Customizer preview.
	 *
	 *
	 * @return bool Whether the panel is active to the current preview.
	 */
	final public function active() {
		$panel  = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filters response of GC_Customize_Panel::active().
		 *
		 *
		 * @param bool               $active Whether the Customizer panel is active.
		 * @param GC_Customize_Panel $panel  GC_Customize_Panel instance.
		 */
		$active = apply_filters( 'customize_panel_active', $active, $panel );

		return $active;
	}

	/**
	 * Default callback used when invoking GC_Customize_Panel::active().
	 *
	 * Subclasses can override this with their specific logic, or they may
	 * provide an 'active_callback' argument to the constructor.
	 *
	 *
	 * @return bool Always true.
	 */
	public function active_callback() {
		return true;
	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$array                          = gc_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type' ) );
		$array['title']                 = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['content']               = $this->get_content();
		$array['active']                = $this->active();
		$array['instanceNumber']        = $this->instance_number;
		$array['autoExpandSoleSection'] = $this->auto_expand_sole_section;
		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the panel.
	 *
	 *
	 * @return bool False if theme doesn't support the panel or the user doesn't have the capability.
	 */
	public function check_capabilities() {
		if ( $this->capability && ! current_user_can( $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! current_theme_supports( ... (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the panel's content template for insertion into the Customizer pane.
	 *
	 *
	 * @return string Content for the panel.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the panel.
	 *
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer panel.
		 *
		 *
		 * @param GC_Customize_Panel $panel GC_Customize_Panel instance.
		 */
		do_action( 'customize_render_panel', $this );

		/**
		 * Fires before rendering a specific Customizer panel.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the ID of the specific Customizer panel to be rendered.
		 *
		 */
		do_action( "customize_render_panel_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the panel container, and then its contents (via `this->render_content()`) in a subclass.
	 *
	 * Panel containers are now rendered in JS by default, see GC_Customize_Panel::print_template().
	 *
	 */
	protected function render() {}

	/**
	 * Render the panel UI in a subclass.
	 *
	 * Panel contents are now rendered in JS by default, see GC_Customize_Panel::print_template().
	 *
	 */
	protected function render_content() {}

	/**
	 * Render the panel's JS templates.
	 *
	 * This function is only run for panel types that have been registered with
	 * GC_Customize_Manager::register_panel_type().
	 *
	 *
	 * @see GC_Customize_Manager::register_panel_type()
	 */
	public function print_template() {
		?>
		<script type="text/html" id="tmpl-customize-panel-<?php echo esc_attr( $this->type ); ?>-content">
			<?php $this->content_template(); ?>
		</script>
		<script type="text/html" id="tmpl-customize-panel-<?php echo esc_attr( $this->type ); ?>">
			<?php $this->render_template(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for rendering this panel's container.
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding GC_Customize_Panel::json().
	 *
	 * @see GC_Customize_Panel::print_template()
	 *
	 */
	protected function render_template() {
		?>
		<li id="accordion-panel-{{ data.id }}" class="accordion-section control-section control-panel control-panel-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text"><?php _e( '按回车键以展开此面板' ); ?></span>
			</h3>
			<ul class="accordion-sub-container control-panel-content"></ul>
		</li>
		<?php
	}

	/**
	 * An Underscore (JS) template for this panel's content (but not its container).
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding GC_Customize_Panel::json().
	 *
	 * @see GC_Customize_Panel::print_template()
	 *
	 */
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button class="customize-panel-back" tabindex="-1"><span class="screen-reader-text"><?php _e( '返回' ); ?></span></button>
			<div class="accordion-section-title">
				<span class="preview-notice">
				<?php
					/* translators: %s: The site/panel title in the Customizer. */
					printf( __( '您正在自定义%s' ), '<strong class="panel-title">{{ data.title }}</strong>' );
				?>
				</span>
				<# if ( data.description ) { #>
					<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( '帮助' ); ?></span></button>
				<# } #>
			</div>
			<# if ( data.description ) { #>
				<div class="description customize-panel-description">
					{{{ data.description }}}
				</div>
			<# } #>

			<div class="customize-control-notifications-container"></div>
		</li>
		<?php
	}
}

/** GC_Customize_Nav_Menus_Panel class */
require_once ABSPATH . GCINC . '/customize/class-gc-customize-nav-menus-panel.php';
