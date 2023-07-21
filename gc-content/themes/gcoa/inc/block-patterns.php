<?php
/**
 * Twenty Twenty-Two: Block Patterns
 *
 * @since Twenty Twenty-Two 1.0
 */

/**
 * Registers block patterns and categories.
 *
 * @since Twenty Twenty-Two 1.0
 *
 * @return void
 */
function gcoa_register_block_patterns() {
	$block_pattern_categories = array(
		'featured' => array( 'label' => __( '特色', 'gcoa' ) ),
		'footer'   => array( 'label' => __( '页脚', 'gcoa' ) ),
		'header'   => array( 'label' => __( '页眉', 'gcoa' ) ),
		'query'    => array( 'label' => __( '查询', 'gcoa' ) ),
		'pages'    => array( 'label' => __( '页面', 'gcoa' ) ),
		'button'    => array( 'label' => __( '按钮', 'gcoa' ) ),
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'gcoa_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! GC_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

	$block_patterns = array(
		'footer-default',
		'header-default',
	);

	/**
	 * Filters the theme block patterns.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @param array $block_patterns List of block patterns by name.
	 */
	$block_patterns = apply_filters( 'gcoa_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		$pattern_file = get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' );

		register_block_pattern(
			'gcoa/' . $block_pattern,
			require $pattern_file
		);
	}
}
add_action( 'init', 'gcoa_register_block_patterns', 9 );
