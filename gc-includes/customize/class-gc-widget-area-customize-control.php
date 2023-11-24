<?php
/**
 * Customize API: GC_Widget_Area_Customize_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Widget Area Customize Control class.
 *
 * @since 3.9.0
 *
 * @see GC_Customize_Control
 */
class GC_Widget_Area_Customize_Control extends GC_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @var string
	 */
	public $type = 'sidebar_widgets';

	/**
	 * Sidebar ID.
	 *
	 * @var int|string
	 */
	public $sidebar_id;

	/**
	 * Refreshes the parameters passed to the JavaScript via JSON.
	 *
	 */
	public function to_json() {
		parent::to_json();
		$exported_properties = array( 'sidebar_id' );
		foreach ( $exported_properties as $key ) {
			$this->json[ $key ] = $this->$key;
		}
	}

	/**
	 * Renders the control's content.
	 *
	 */
	public function render_content() {
		$id = 'reorder-widgets-desc-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		?>
		<button type="button" class="button add-new-widget" aria-expanded="false" aria-controls="available-widgets">
			<?php _e( '添加小工具' ); ?>
		</button>
		<button type="button" class="button-link reorder-toggle" aria-label="<?php esc_attr_e( '重排小工具' ); ?>" aria-describedby="<?php echo esc_attr( $id ); ?>">
			<span class="reorder"><?php _e( '重新排序' ); ?></span>
			<span class="reorder-done"><?php _e( '完成' ); ?></span>
		</button>
		<p class="screen-reader-text" id="<?php echo esc_attr( $id ); ?>">
			<?php
			/* translators: Hidden accessibility text. */
			_e( '在重排模式中，小工具列表顶部将会出现额外的控件来让您重排小工具。' );
			?>
		</p>
		<?php
	}
}
