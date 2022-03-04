<?php
/**
 * Customize API: GC_Customize_Nav_Menu_Item_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize control to represent the name field for a given menu.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Nav_Menu_Item_Control extends GC_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'nav_menu_item';

	/**
	 * The nav menu item setting.
	 *
	 * @var GC_Customize_Nav_Menu_Item_Setting
	 */
	public $setting;

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_Customize_Control::__construct()
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      The control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 *                                      See GC_Customize_Control::__construct() for information
	 *                                      on accepted arguments. Default empty array.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Don't render the control's content - it's rendered with a JS template.
	 *
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 */
	public function content_template() {
		?>
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-type" aria-hidden="true">{{ data.item_type_label }}</span>
				<span class="item-title" aria-hidden="true">
					<span class="spinner"></span>
					<span class="menu-item-title<# if ( ! data.title && ! data.original_title ) { #> no-title<# } #>">{{ data.title || data.original_title || gc.customize.Menus.data.l10n.untitled }}</span>
				</span>
				<span class="item-controls">
					<button type="button" class="button-link item-edit" aria-expanded="false"><span class="screen-reader-text">
					<?php
						/* translators: 1: Title of a menu item, 2: Type of a menu item. */
						printf( __( '编辑菜单项：%1$s（%2$s）' ), '{{ data.title || gc.customize.Menus.data.l10n.untitled }}', '{{ data.item_type_label }}' );
					?>
					</span><span class="toggle-indicator" aria-hidden="true"></span></button>
					<button type="button" class="button-link item-delete submitdelete deletion"><span class="screen-reader-text">
					<?php
						/* translators: 1: Title of a menu item, 2: Type of a menu item. */
						printf( __( '移除菜单项：%1$s（%2$s）' ), '{{ data.title || gc.customize.Menus.data.l10n.untitled }}', '{{ data.item_type_label }}' );
					?>
					</span></button>
				</span>
			</div>
		</div>

		<div class="menu-item-settings" id="menu-item-settings-{{ data.menu_item_id }}">
			<# if ( 'custom' === data.item_type ) { #>
			<p class="field-url description description-thin">
				<label for="edit-menu-item-url-{{ data.menu_item_id }}">
					<?php _e( 'URL' ); ?><br />
					<input class="widefat code edit-menu-item-url" type="text" id="edit-menu-item-url-{{ data.menu_item_id }}" name="menu-item-url" />
				</label>
			</p>
		<# } #>
			<p class="description description-thin">
				<label for="edit-menu-item-title-{{ data.menu_item_id }}">
					<?php _e( '导航标签' ); ?><br />
					<input type="text" id="edit-menu-item-title-{{ data.menu_item_id }}" placeholder="{{ data.original_title }}" class="widefat edit-menu-item-title" name="menu-item-title" />
				</label>
			</p>
			<p class="field-link-target description description-thin">
				<label for="edit-menu-item-target-{{ data.menu_item_id }}">
					<input type="checkbox" id="edit-menu-item-target-{{ data.menu_item_id }}" class="edit-menu-item-target" value="_blank" name="menu-item-target" />
					<?php _e( '在新标签页中打开链接' ); ?>
				</label>
			</p>
			<p class="field-title-attribute field-attr-title description description-thin">
				<label for="edit-menu-item-attr-title-{{ data.menu_item_id }}">
					<?php _e( 'title属性' ); ?><br />
					<input type="text" id="edit-menu-item-attr-title-{{ data.menu_item_id }}" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title" />
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-{{ data.menu_item_id }}">
					<?php _e( 'CSS类' ); ?><br />
					<input type="text" id="edit-menu-item-classes-{{ data.menu_item_id }}" class="widefat code edit-menu-item-classes" name="menu-item-classes" />
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-{{ data.menu_item_id }}">
					<?php _e( '链接关系（XFN）' ); ?><br />
					<input type="text" id="edit-menu-item-xfn-{{ data.menu_item_id }}" class="widefat code edit-menu-item-xfn" name="menu-item-xfn" />
				</label>
			</p>
			<p class="field-description description description-thin">
				<label for="edit-menu-item-description-{{ data.menu_item_id }}">
					<?php _e( '描述' ); ?><br />
					<textarea id="edit-menu-item-description-{{ data.menu_item_id }}" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description">{{ data.description }}</textarea>
					<span class="描述"><?php _e( '如果活动主题支持，说明将显示在菜单中。' ); ?></span>
				</label>
			</p>

			<?php
			/**
			 * Fires at the end of the form field template for nav menu items in the customizer.
			 *
			 * Additional fields can be rendered here and managed in JavaScript.
			 *
		
			 */
			do_action( 'gc_nav_menu_item_custom_fields_customize_template' );
			?>

			<div class="menu-item-actions description-thin submitbox">
				<# if ( ( 'post_type' === data.item_type || 'taxonomy' === data.item_type ) && '' !== data.original_title ) { #>
				<p class="link-to-original">
					<?php
						/* translators: Nav menu item original title. %s: Original title. */
						printf( __( '原始：%s' ), '<a class="original-link" href="{{ data.url }}">{{ data.original_title }}</a>' );
					?>
				</p>
				<# } #>

				<button type="button" class="button-link button-link-delete item-delete submitdelete deletion"><?php _e( '移除' ); ?></button>
				<span class="spinner"></span>
			</div>
			<input type="hidden" name="menu-item-db-id[{{ data.menu_item_id }}]" class="menu-item-data-db-id" value="{{ data.menu_item_id }}" />
			<input type="hidden" name="menu-item-parent-id[{{ data.menu_item_id }}]" class="menu-item-data-parent-id" value="{{ data.parent }}" />
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
	}

	/**
	 * Return parameters for this control.
	 *
	 *
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported                 = parent::json();
		$exported['menu_item_id'] = $this->setting->post_id;

		return $exported;
	}
}
