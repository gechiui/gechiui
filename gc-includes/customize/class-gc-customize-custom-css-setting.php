<?php
/**
 * Customize API: GC_Customize_Custom_CSS_Setting class
 *
 * This handles validation, sanitization and saving of the value.
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Custom Setting to handle GC Custom CSS.
 *
 *
 *
 * @see GC_Customize_Setting
 */
final class GC_Customize_Custom_CSS_Setting extends GC_Customize_Setting {

	/**
	 * The setting type.
	 *
	 * @var string
	 */
	public $type = 'custom_css';

	/**
	 * Setting Transport
	 *
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Capability required to edit this setting.
	 *
	 * @var string
	 */
	public $capability = 'edit_css';

	/**
	 * Stylesheet
	 *
	 * @var string
	 */
	public $stylesheet = '';

	/**
	 * GC_Customize_Custom_CSS_Setting constructor.
	 *
	 *
	 * @throws Exception If the setting ID does not match the pattern `custom_css[$stylesheet]`.
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID of the setting.
	 *                                      Can be a theme mod or option name.
	 * @param array                $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( 'custom_css' !== $this->id_data['base'] ) {
			throw new Exception( 'Expected custom_css id_base.' );
		}
		if ( 1 !== count( $this->id_data['keys'] ) || empty( $this->id_data['keys'][0] ) ) {
			throw new Exception( 'Expected single stylesheet key.' );
		}
		$this->stylesheet = $this->id_data['keys'][0];
	}

	/**
	 * Add filter to preview post value.
	 *
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return false;
		}
		$this->is_previewed = true;
		add_filter( 'gc_get_custom_css', array( $this, 'filter_previewed_gc_get_custom_css' ), 9, 2 );
		return true;
	}

	/**
	 * Filters `gc_get_custom_css` for applying the customized value.
	 *
	 * This is used in the preview when `gc_get_custom_css()` is called for rendering the styles.
	 *
	 *
	 * @see gc_get_custom_css()
	 *
	 * @param string $css        Original CSS.
	 * @param string $stylesheet Current stylesheet.
	 * @return string CSS.
	 */
	public function filter_previewed_gc_get_custom_css( $css, $stylesheet ) {
		if ( $stylesheet === $this->stylesheet ) {
			$customized_value = $this->post_value( null );
			if ( ! is_null( $customized_value ) ) {
				$css = $customized_value;
			}
		}
		return $css;
	}

	/**
	 * Fetch the value of the setting. Will return the previewed value when `preview()` is called.
	 *
	 *
	 * @see GC_Customize_Setting::value()
	 *
	 * @return string
	 */
	public function value() {
		if ( $this->is_previewed ) {
			$post_value = $this->post_value( null );
			if ( null !== $post_value ) {
				return $post_value;
			}
		}
		$id_base = $this->id_data['base'];
		$value   = '';
		$post    = gc_get_custom_css_post( $this->stylesheet );
		if ( $post ) {
			$value = $post->post_content;
		}
		if ( empty( $value ) ) {
			$value = $this->default;
		}

		/** This filter is documented in gc-includes/class-gc-customize-setting.php */
		$value = apply_filters( "customize_value_{$id_base}", $value, $this );

		return $value;
	}

	/**
	 * Validate a received value for being valid CSS.
	 *
	 * Checks for imbalanced braces, brackets, and comments.
	 * Notifications are rendered when the customizer state is saved.
	 *
	 *
	 * @param string $value CSS to validate.
	 * @return true|GC_Error True if the input was validated, otherwise GC_Error.
	 */
	public function validate( $value ) {
		// Restores the more descriptive, specific name for use within this method.
		$css = $value;

		$validity = new GC_Error();

		if ( preg_match( '#</?\w+#', $css ) ) {
			$validity->add( 'illegal_markup', __( 'CSS中不能出现标记。' ) );
		}

		if ( ! $validity->has_errors() ) {
			$validity = parent::validate( $css );
		}
		return $validity;
	}

	/**
	 * Store the CSS setting value in the custom_css custom post type for the stylesheet.
	 *
	 *
	 * @param string $value CSS to update.
	 * @return int|false The post ID or false if the value could not be saved.
	 */
	public function update( $value ) {
		// Restores the more descriptive, specific name for use within this method.
		$css = $value;

		if ( empty( $css ) ) {
			$css = '';
		}

		$r = gc_update_custom_css_post(
			$css,
			array(
				'stylesheet' => $this->stylesheet,
			)
		);

		if ( $r instanceof GC_Error ) {
			return false;
		}
		$post_id = $r->ID;

		// Cache post ID in theme mod for performance to avoid additional DB query.
		if ( $this->manager->get_stylesheet() === $this->stylesheet ) {
			set_theme_mod( 'custom_css_post_id', $post_id );
		}

		return $post_id;
	}
}
