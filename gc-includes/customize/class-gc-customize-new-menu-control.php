<?php
/**
 * Customize API: GC_Customize_New_Menu_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 * @deprecated 4.9.0 This file is no longer used as of the menu creation UX introduced in #40104.
 */

_deprecated_file( basename( __FILE__ ), '4.9.0' );

/**
 * Customize control class for new menus.
 *
 *
 * @deprecated 4.9.0 This class is no longer used as of the menu creation UX introduced in #40104.
 *
 * @see GC_Customize_Control
 */
class GC_Customize_New_Menu_Control extends GC_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'new_menu';

	/**
	 * Constructor.
	 *
	 * @deprecated 4.9.0
	 *
	 * @see GC_Customize_Control::__construct()
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      The control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 *                                      See GC_Customize_Control::__construct() for information
	 *                                      on accepted arguments. Default empty array.
	 */
	public function __construct( GC_Customize_Manager $manager, $id, array $args = array() ) {
		_deprecated_function( __METHOD__, '4.9.0' );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the control's content.
	 *
	 * @deprecated 4.9.0
	 */
	public function render_content() {
		_deprecated_function( __METHOD__, '4.9.0' );
		?>
		<button type="button" class="button button-primary" id="create-new-menu-submit"><?php _e( '创建菜单' ); ?></button>
		<span class="spinner"></span>
		<?php
	}
}
