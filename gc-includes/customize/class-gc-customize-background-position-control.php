<?php
/**
 * Customize API: GC_Customize_Background_Position_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Background Position Control class.
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Background_Position_Control extends GC_Customize_Control {

	/**
	 * Type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $type = 'background_position';

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 4.7.0
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the position control.
	 *
	 * @since 4.7.0
	 */
	public function content_template() {
		$options = array(
			array(
				'left top'   => array(
					'label' => __( '左上角' ),
					'icon'  => 'dashicons dashicons-arrow-left-alt',
				),
				'center top' => array(
					'label' => __( '顶部' ),
					'icon'  => 'dashicons dashicons-arrow-up-alt',
				),
				'right top'  => array(
					'label' => __( '右上角' ),
					'icon'  => 'dashicons dashicons-arrow-right-alt',
				),
			),
			array(
				'left center'   => array(
					'label' => __( '左' ),
					'icon'  => 'dashicons dashicons-arrow-left-alt',
				),
				'center center' => array(
					'label' => __( '中' ),
					'icon'  => 'background-position-center-icon',
				),
				'right center'  => array(
					'label' => __( '右' ),
					'icon'  => 'dashicons dashicons-arrow-right-alt',
				),
			),
			array(
				'left bottom'   => array(
					'label' => __( '左下角' ),
					'icon'  => 'dashicons dashicons-arrow-left-alt',
				),
				'center bottom' => array(
					'label' => __( '底部' ),
					'icon'  => 'dashicons dashicons-arrow-down-alt',
				),
				'right bottom'  => array(
					'label' => __( '右下角' ),
					'icon'  => 'dashicons dashicons-arrow-right-alt',
				),
			),
		);
		?>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-content">
			<fieldset>
				<legend class="screen-reader-text"><span>
					<?php
					/* translators: Hidden accessibility text. */
					_e( '图片位置' );
					?>
				</span></legend>
				<div class="background-position-control">
				<?php foreach ( $options as $group ) : ?>
					<div class="button-group">
					<?php foreach ( $group as $value => $input ) : ?>
						<label>
							<input class="ui-helper-hidden-accessible" name="background-position" type="radio" value="<?php echo esc_attr( $value ); ?>">
							<span class="button display-options position"><span class="<?php echo esc_attr( $input['icon'] ); ?>" aria-hidden="true"></span></span>
							<span class="screen-reader-text"><?php echo $input['label']; ?></span>
						</label>
					<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				</div>
			</fieldset>
		</div>
		<?php
	}
}
