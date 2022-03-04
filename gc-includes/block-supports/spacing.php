<?php
/**
 * Spacing block support flag.

 * For backwards compatibility, this remains separate to the dimensions.php
 * block support despite both belonging under a single panel in the editor.
 *
 * @package GeChiUI
 *
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 *
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
 * Add CSS classes for block spacing to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 *
 * @access private
 *
 * @param GC_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block spacing CSS classes and inline styles.
 */
function gc_apply_spacing_support( $block_type, $block_attributes ) {
	if ( gc_skip_spacing_serialization( $block_type ) ) {
		return array();
	}

	$has_padding_support = block_has_support( $block_type, array( 'spacing', 'padding' ), false );
	$has_margin_support  = block_has_support( $block_type, array( 'spacing', 'margin' ), false );
	$styles              = array();

	if ( $has_padding_support ) {
		$padding_value = _gc_array_get( $block_attributes, array( 'style', 'spacing', 'padding' ), null );
		if ( is_array( $padding_value ) ) {
			foreach ( $padding_value as $key => $value ) {
				$styles[] = sprintf( 'padding-%s: %s;', $key, $value );
			}
		} elseif ( null !== $padding_value ) {
			$styles[] = sprintf( 'padding: %s;', $padding_value );
		}
	}

	if ( $has_margin_support ) {
		$margin_value = _gc_array_get( $block_attributes, array( 'style', 'spacing', 'margin' ), null );
		if ( is_array( $margin_value ) ) {
			foreach ( $margin_value as $key => $value ) {
				$styles[] = sprintf( 'margin-%s: %s;', $key, $value );
			}
		} elseif ( null !== $margin_value ) {
			$styles[] = sprintf( 'margin: %s;', $margin_value );
		}
	}

	return empty( $styles ) ? array() : array( 'style' => implode( ' ', $styles ) );
}

/**
 * Checks whether serialization of the current block's spacing properties should
 * occur.
 *
 *
 * @access private
 *
 * @param GC_Block_Type $block_type Block type.
 * @return bool Whether to serialize spacing support styles & classes.
 */
function gc_skip_spacing_serialization( $block_type ) {
	$spacing_support = _gc_array_get( $block_type->supports, array( 'spacing' ), false );

	return is_array( $spacing_support ) &&
		array_key_exists( '__experimentalSkipSerialization', $spacing_support ) &&
		$spacing_support['__experimentalSkipSerialization'];
}

// Register the block support.
GC_Block_Supports::get_instance()->register(
	'spacing',
	array(
		'register_attribute' => 'gc_register_spacing_support',
		'apply'              => 'gc_apply_spacing_support',
	)
);
