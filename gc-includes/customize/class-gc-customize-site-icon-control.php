<?php
/**
 * Customize API: GC_Customize_Site_Icon_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Site Icon control class.
 *
 * Used only for custom functionality in JavaScript.
 *
 * @since 4.3.0
 *
 * @see GC_Customize_Cropped_Image_Control
 */
class GC_Customize_Site_Icon_Control extends GC_Customize_Cropped_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'site_icon';

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
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
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_styles', 'gc_site_icon', 99 );
	}

	/**
	 * Renders a JS template for the content of the site icon control.
	 *
	 * @since 4.5.0
	 */
	public function content_template() {
		?>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view">
				<# if ( data.attachment.sizes ) { #>
					<div class="site-icon-preview gc-clearfix">
						<div class="favicon-preview">
							<img src="<?php echo esc_url( assets_url( 'images/' . ( is_rtl() ? 'browser-rtl.png' : 'browser.png' ) ) ); ?>" class="browser-preview" width="182" alt="" />

							<div class="favicon">
								<img src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" alt="<?php esc_attr_e( '作为浏览器图标预览' ); ?>" />
							</div>
							<span class="browser-title" aria-hidden="true"><# print( '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>' ) #></span>
						</div>
						<img class="app-icon-preview" src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" alt="<?php esc_attr_e( '作为app图标预览' ); ?>" />
					</div>
				<# } #>
				<div class="actions">
					<# if ( data.canUpload ) { #>
						<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
						<button type="button" class="button upload-button"><?php echo $this->button_labels['change']; ?></button>
					<# } #>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<# if ( data.canUpload ) { #>
					<button type="button" class="upload-button button-add-media"><?php echo $this->button_labels['site_icon']; ?></button>
				<# } #>
				<div class="actions">
					<# if ( data.defaultAttachment ) { #>
						<button type="button" class="button default-button"><?php echo $this->button_labels['default']; ?></button>
					<# } #>
				</div>
			</div>
		<# } #>
		<?php
	}
}
