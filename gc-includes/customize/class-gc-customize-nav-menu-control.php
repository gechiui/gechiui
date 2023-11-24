<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Nav Menu Control Class.
 *
 * @since 4.3.0
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Control extends GC_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'nav_menu';

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 */
	public function content_template() {
		$add_items = __( '添加项目' );
		?>
		<p class="new-menu-item-invitation">
			<?php
			printf(
				/* translators: %s: "添加项目" button text. */
				__( '是时候加入一些链接了！点击“%s”来将页面、分类和自定义链接加入您的菜单。想加入多少就能加入多少。' ),
				$add_items
			);
			?>
		</p>
		<div class="customize-control-nav_menu-buttons">
			<button type="button" class="button add-new-menu-item" aria-label="<?php esc_attr_e( '添加或移除菜单项' ); ?>" aria-expanded="false" aria-controls="available-menu-items">
				<?php echo $add_items; ?>
			</button>
			<button type="button" class="button-link reorder-toggle" aria-label="<?php esc_attr_e( '重排菜单项' ); ?>" aria-describedby="reorder-items-desc-{{ data.menu_id }}">
				<span class="reorder"><?php _e( '重新排序' ); ?></span>
				<span class="reorder-done"><?php _e( '完成' ); ?></span>
			</button>
		</div>
		<p class="screen-reader-text" id="reorder-items-desc-{{ data.menu_id }}">
			<?php
			/* translators: Hidden accessibility text. */
			_e( '当位于重排模式时，项目列表顶部将会出现额外的控件。' );
			?>
		</p>
		<?php
	}

	/**
	 * Return parameters for this control.
	 *
	 * @since 4.3.0
	 *
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported            = parent::json();
		$exported['menu_id'] = $this->setting->term_id;

		return $exported;
	}
}
