<?php
/**
 * Customize API: GC_Customize_New_Menu_Section class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 * @deprecated 4.9.0 This file is no longer used as of the menu creation UX introduced in #40104.
 */

_deprecated_file( basename( __FILE__ ), '4.9.0' );

/**
 * Customize Menu Section Class
 *
 *
 * @deprecated 4.9.0 This class is no longer used as of the menu creation UX introduced in #40104.
 *
 * @see GC_Customize_Section
 */
class GC_Customize_New_Menu_Section extends GC_Customize_Section {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'new_menu';

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @deprecated 4.9.0
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID of the section.
	 * @param array                $args    Section arguments.
	 */
	public function __construct( GC_Customize_Manager $manager, $id, array $args = array() ) {
		_deprecated_function( __METHOD__, '4.9.0' );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the section, and the controls that have been added to it.
	 *
	 * @deprecated 4.9.0
	 */
	protected function render() {
		_deprecated_function( __METHOD__, '4.9.0' );
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="accordion-section-new-menu">
			<button type="button" class="button add-new-menu-item add-menu-toggle" aria-expanded="false">
				<?php echo esc_html( $this->title ); ?>
			</button>
			<ul class="new-menu-section-content"></ul>
		</li>
		<?php
	}
}
