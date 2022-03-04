<?php
/**
 * Customize API: GC_Customize_Color_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Color Control class.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Color_Control extends GC_Customize_Control {
	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'color';

	/**
	 * Statuses.
	 *
	 * @var array
	 */
	public $statuses;

	/**
	 * Mode.
	 *
	 * @var string
	 */
	public $mode = 'full';

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_Customize_Control::__construct()
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 *                                      See GC_Customize_Control::__construct() for information
	 *                                      on accepted arguments. Default empty array.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$this->statuses = array( '' => __( '默认' ) );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Enqueue scripts/styles for the color picker.
	 *
	 */
	public function enqueue() {
		gc_enqueue_script( 'gc-color-picker' );
		gc_enqueue_style( 'gc-color-picker' );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @uses GC_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['statuses']     = $this->statuses;
		$this->json['defaultValue'] = $this->setting->default;
		$this->json['mode']         = $this->mode;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the color picker control.
	 *
	 */
	public function content_template() {
		?>
		<# var defaultValue = '#RRGGBB', defaultValueAttr = '',
			isHueSlider = data.mode === 'hue';
		if ( data.defaultValue && _.isString( data.defaultValue ) && ! isHueSlider ) {
			if ( '#' !== data.defaultValue.substring( 0, 1 ) ) {
				defaultValue = '#' + data.defaultValue;
			} else {
				defaultValue = data.defaultValue;
			}
			defaultValueAttr = ' data-default-color=' + defaultValue; // Quotes added automatically.
		} #>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-content">
			<label><span class="screen-reader-text">{{{ data.label }}}</span>
			<# if ( isHueSlider ) { #>
				<input class="color-picker-hue" type="text" data-type="hue" />
			<# } else { #>
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="{{ defaultValue }}" {{ defaultValueAttr }} />
			<# } #>
			</label>
		</div>
		<?php
	}
}
