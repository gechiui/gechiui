<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Location_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Menu Location Control Class.
 *
 * This custom control is only needed for JS.
 *
 * @since 4.3.0
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Location_Control extends GC_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'nav_menu_location';

	/**
	 * Location ID.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $location_id = '';

	/**
	 * Refresh the parameters passed to JavaScript via JSON.
	 *
	 * @since 4.3.0
	 *
	 * @see GC_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['locationId'] = $this->location_id;
	}

	/**
	 * Render content just like a normal select control.
	 *
	 * @since 4.3.0
	 * @since 4.9.0 Added a button to create menus.
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$value_hidden_class    = '';
		$no_value_hidden_class = '';
		if ( $this->value() ) {
			$value_hidden_class = ' hidden';
		} else {
			$no_value_hidden_class = ' hidden';
		}
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $label ) :
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				endforeach;
				?>
			</select>
		</label>
		<button type="button" class="button-link create-menu<?php echo $value_hidden_class; ?>" data-location-id="<?php echo esc_attr( $this->location_id ); ?>" aria-label="<?php esc_attr_e( '为此位置创建菜单' ); ?>"><?php _e( '+ 创建新菜单' ); ?></button>
		<button type="button" class="button-link edit-menu<?php echo $no_value_hidden_class; ?>" aria-label="<?php esc_attr_e( '编辑选中的菜单' ); ?>"><?php _e( '编辑菜单' ); ?></button>
		<?php
	}
}
