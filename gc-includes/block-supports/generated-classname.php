<?php
/**
 * Generated classname block support flag.
 *
 * @package GeChiUI
 * @since 5.6.0
 */

/**
 * Get the generated classname from a given block name.
 *
 * @since 5.6.0
 *
 * @access private
 *
 * @param  string $block_name Block Name.
 * @return string Generated classname.
 */
function gc_get_block_default_classname( $block_name ) {
	// Generated HTML classes for blocks follow the `gc-block-{name}` nomenclature.
	// Blocks provided by GeChiUI drop the prefixes 'core/' or 'core-' (historically used in 'core-embed/').
	$classname = 'gc-block-' . preg_replace(
		'/^core-/',
		'',
		str_replace( '/', '-', $block_name )
	);

	/**
	 * Filters the default block className for server rendered blocks.
	 *
	 * @since 5.6.0
	 *
	 * @param string     $class_name The current applied classname.
	 * @param string     $block_name The block name.
	 */
	$classname = apply_filters( 'block_default_classname', $classname, $block_name );

	return $classname;
}

/**
 * Add the generated classnames to the output.
 *
 * @since 5.6.0
 *
 * @access private
 *
 * @param  GC_Block_Type $block_type       Block Type.
 *
 * @return array Block CSS classes and inline styles.
 */
function gc_apply_generated_classname_support( $block_type ) {
	$attributes                      = array();
	$has_generated_classname_support = block_has_support( $block_type, array( 'className' ), true );
	if ( $has_generated_classname_support ) {
		$block_classname = gc_get_block_default_classname( $block_type->name );

		if ( $block_classname ) {
			$attributes['class'] = $block_classname;
		}
	}

	return $attributes;
}

// Register the block support.
GC_Block_Supports::get_instance()->register(
	'generated-classname',
	array(
		'apply' => 'gc_apply_generated_classname_support',
	)
);
