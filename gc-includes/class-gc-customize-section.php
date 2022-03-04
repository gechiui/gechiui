<?php
/**
 * GeChiUI Customize Section classes
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Section class.
 *
 * A UI container for controls, managed by the GC_Customize_Manager class.
 *
 *
 *
 * @see GC_Customize_Manager
 */
class GC_Customize_Section {

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
	 * Priority of the section which informs load order of sections.
	 *
	 * @var int
	 */
	public $priority = 160;

	/**
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @var string
	 */
	public $panel = '';

	/**
	 * Capability required for the section.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme features required to support the section.
	 *
	 * @var string|string[]
	 */
	public $theme_supports = '';

	/**
	 * Title of the section to show in UI.
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
	 * Customizer controls for this section.
	 *
	 * @var array
	 */
	public $controls;

	/**
	 * Type of this section.
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
	 * Show the description or hide it behind the help icon.
	 *
	 *
	 * @var bool Indicates whether the Section's description should be
	 *           hidden behind a help icon ("?") in the Section header,
	 *           similar to how help icons are displayed on Panels.
	 */
	public $description_hidden = false;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID of the section.
	 * @param array                $args    {
	 *     Optional. Array of properties for the new Section object. Default empty array.
	 *
	 *     @type int             $priority           Priority of the section, defining the display order
	 *                                               of panels and sections. Default 160.
	 *     @type string          $panel              The panel this section belongs to (if any).
	 *                                               Default empty.
	 *     @type string          $capability         Capability required for the section.
	 *                                               Default 'edit_theme_options'
	 *     @type string|string[] $theme_supports     Theme features required to support the section.
	 *     @type string          $title              Title of the section to show in UI.
	 *     @type string          $description        Description to show in the UI.
	 *     @type string          $type               Type of the section.
	 *     @type callable        $active_callback    Active callback.
	 *     @type bool            $description_hidden Hide the description behind a help icon,
	 *                                               instead of inline above the first control.
	 *                                               Default false.
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

		$this->controls = array(); // Users cannot customize the $controls array.
	}

	/**
	 * Check whether section is active to current Customizer preview.
	 *
	 *
	 * @return bool Whether the section is active to the current preview.
	 */
	final public function active() {
		$section = $this;
		$active  = call_user_func( $this->active_callback, $this );

		/**
		 * Filters response of GC_Customize_Section::active().
		 *
		 *
		 * @param bool                 $active  Whether the Customizer section is active.
		 * @param GC_Customize_Section $section GC_Customize_Section instance.
		 */
		$active = apply_filters( 'customize_section_active', $active, $section );

		return $active;
	}

	/**
	 * Default callback used when invoking GC_Customize_Section::active().
	 *
	 * Subclasses can override this with their specific logic, or they may provide
	 * an 'active_callback' argument to the constructor.
	 *
	 *
	 * @return true Always true.
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
		$array                   = gc_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'panel', 'type', 'description_hidden' ) );
		$array['title']          = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['content']        = $this->get_content();
		$array['active']         = $this->active();
		$array['instanceNumber'] = $this->instance_number;

		if ( $this->panel ) {
			/* translators: &#9656; is the unicode right-pointing triangle. %s: Section title in the Customizer. */
			$array['customizeAction'] = sprintf( __( '自定义 &#9656; %s' ), esc_html( $this->manager->get_panel( $this->panel )->title ) );
		} else {
			$array['customizeAction'] = __( '正在自定义' );
		}

		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the section.
	 *
	 *
	 * @return bool False if theme doesn't support the section or user doesn't have the capability.
	 */
	final public function check_capabilities() {
		if ( $this->capability && ! current_user_can( $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! current_theme_supports( ... (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the section's content for insertion into the Customizer pane.
	 *
	 *
	 * @return string Contents of the section.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the section.
	 *
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer section.
		 *
		 *
		 * @param GC_Customize_Section $section GC_Customize_Section instance.
		 */
		do_action( 'customize_render_section', $this );
		/**
		 * Fires before rendering a specific Customizer section.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to the ID
		 * of the specific Customizer section to be rendered.
		 *
		 */
		do_action( "customize_render_section_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the section UI in a subclass.
	 *
	 * Sections are now rendered in JS by default, see GC_Customize_Section::print_template().
	 *
	 */
	protected function render() {}

	/**
	 * Render the section's JS template.
	 *
	 * This function is only run for section types that have been registered with
	 * GC_Customize_Manager::register_section_type().
	 *
	 *
	 * @see GC_Customize_Manager::render_template()
	 */
	public function print_template() {
		?>
		<script type="text/html" id="tmpl-customize-section-<?php echo $this->type; ?>">
			<?php $this->render_template(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for rendering this section.
	 *
	 * Class variables for this section class are available in the `data` JS object;
	 * export custom variables by overriding GC_Customize_Section::json().
	 *
	 *
	 * @see GC_Customize_Section::print_template()
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text"><?php _e( '按回车来打开此小节' ); ?></span>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container section-meta <# if ( data.description_hidden ) { #>customize-info<# } #>">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
							<span class="screen-reader-text"><?php _e( '返回' ); ?></span>
						</button>
						<h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span>
							{{ data.title }}
						</h3>
						<# if ( data.description && data.description_hidden ) { #>
							<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( '帮助' ); ?></span></button>
							<div class="description customize-section-description">
								{{{ data.description }}}
							</div>
						<# } #>

						<div class="customize-control-notifications-container"></div>
					</div>

					<# if ( data.description && ! data.description_hidden ) { #>
						<div class="description customize-section-description">
							{{{ data.description }}}
						</div>
					<# } #>
				</li>
			</ul>
		</li>
		<?php
	}
}

/** GC_Customize_Themes_Section class */
require_once ABSPATH . GCINC . '/customize/class-gc-customize-themes-section.php';

/** GC_Customize_Sidebar_Section class */
require_once ABSPATH . GCINC . '/customize/class-gc-customize-sidebar-section.php';

/** GC_Customize_Nav_Menu_Section class */
require_once ABSPATH . GCINC . '/customize/class-gc-customize-nav-menu-section.php';
