<?php
/**
 * Customize API: GC_Customize_Code_Editor_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Code Editor Control class.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Code_Editor_Control extends GC_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @var string
	 */
	public $type = 'code_editor';

	/**
	 * Type of code that is being edited.
	 *
	 * @var string
	 */
	public $code_type = '';

	/**
	 * Code editor settings.
	 *
	 * @see gc_enqueue_code_editor()
	 * @var array|false
	 */
	public $editor_settings = array();

	/**
	 * Enqueue control related scripts/styles.
	 *
	 */
	public function enqueue() {
		$this->editor_settings = gc_enqueue_code_editor(
			array_merge(
				array(
					'type'       => $this->code_type,
					'codemirror' => array(
						'indentUnit' => 2,
						'tabSize'    => 2,
					),
				),
				$this->editor_settings
			)
		);
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 *
	 * @see GC_Customize_Control::json()
	 *
	 * @return array Array of parameters passed to the JavaScript.
	 */
	public function json() {
		$json                    = parent::json();
		$json['editor_settings'] = $this->editor_settings;
		$json['input_attrs']     = $this->input_attrs;
		return $json;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 */
	public function render_content() {}

	/**
	 * Render a JS template for control display.
	 *
	 */
	public function content_template() {
		?>
		<# var elementIdPrefix = 'el' + String( Math.random() ); #>
		<# if ( data.label ) { #>
			<label for="{{ elementIdPrefix }}_editor" class="customize-control-title">
				{{ data.label }}
			</label>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-notifications-container"></div>
		<textarea id="{{ elementIdPrefix }}_editor"
			<# _.each( _.extend( { 'class': 'code' }, data.input_attrs ), function( value, key ) { #>
				{{{ key }}}="{{ value }}"
			<# }); #>
			></textarea>
		<?php
	}
}
