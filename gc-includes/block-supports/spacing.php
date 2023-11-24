<?php
/**
 * Spacing block support flag.
 *
 * For backwards compatibility, this remains separate to the dimensions.php
 * block support despite both belonging under a single panel in the editor.
 *
 * @package GeChiUI
 * @since 5.8.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 5.8.0
 * @access private
 *
 * @param GC_Block_Type $block_type Block Type.
 */
function gc_register_spacing_support( $block_type ) {
	$has_spacing_support = block_has_support( $block_type, array( 'spacing' ), false );

	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_spacing_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Adds CSS classes for block spacing to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.8.0
 * @since 6.1.0 Implemented the style engine to generate CSS and classnames.
 * @access private
 *
 * @param GC_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block spacing CSS classes and inline styles.
 */
function gc_apply_spacing_support( $block_type, $block_attributes ) {
	if ( gc_should_skip_block_supports_serialization( $block_type, 'spacing' ) ) {
		return array();
	}

	$attributes          = array();
	$has_padding_support = block_has_support( $block_type, array( 'spacing', 'padding' ), false );
	$has_margin_support  = block_has_support( $block_type, array( 'spacing', 'margin' ), false );
	$block_styles        = isset( $block_attributes['style'] ) ? $block_attributes['style'] : null;

	if ( ! $block_styles ) {
		return $attributes;
	}

	$skip_padding                    = gc_should_skip_block_supports_serialization( $block_type, 'spacing', 'padding' );
	$skip_margin                     = gc_should_skip_block_supports_serialization( $block_type, 'spacing', 'margin' );
	$spacing_block_styles            = array();
	$spacing_block_styles['padding'] = $has_padding_support && ! $skip_padding ? _gc_array_get( $block_styles, array( 'spacing', 'padding' ), null ) : null;
	$spacing_block_styles['margin']  = $has_margin_support && ! $skip_margin ? _gc_array_get( $block_styles, array( 'spacing', 'margin' ), null ) : null;
	$styles                          = gc_style_engine_get_styles( array( 'spacing' => $spacing_block_styles ) );

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

// Register the block support.
GC_Block_Supports::get_instance()->register(
	'spacing',
	array(
		'register_attribute' => 'gc_register_spacing_support',
		'apply'              => 'gc_apply_spacing_support',
	)
);
