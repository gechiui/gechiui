<?php
/**
 * Customize API: GC_Customize_Background_Image_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Background Image Control class.
 *
 * @see GC_Customize_Image_Control
 */
class GC_Customize_Background_Image_Control extends GC_Customize_Image_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.1.0
	 * @var string
	 */
	public $type = 'background';

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses GC_Customize_Image_Control::__construct()
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 */
	public function __construct( $manager ) {
		parent::__construct(
			$manager,
			'background_image',
			array(
				'label'   => __( '背景图片' ),
				'section' => 'background_image',
			)
		);
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 4.1.0
	 */
	public function enqueue() {
		parent::enqueue();

		$custom_background = get_theme_support( 'custom-background' );
		gc_localize_script(
			'customize-controls',
			'_gcCustomizeBackground',
			array(
				'defaults' => ! empty( $custom_background[0] ) ? $custom_background[0] : array(),
				'nonces'   => array(
					'add' => gc_create_nonce( 'background-add' ),
				),
			)
		);
	}
}
