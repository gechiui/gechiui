<?php
/**
 * Twenty Twenty-Two: Block Patterns
 *
 * @since Twenty Twenty-Two 1.0
 */

/**
 * Registers block shortcodes and categories.
 *
 * @since Twenty Twenty-Two 1.0
 *
 * @return void
 */
function gcoa_register_block_shortcodes() {
	

	$block_shortcodes = array(
		'users',
		'gcforms-entries',
		'profile'
	);

	/**
	 * Filters the theme block shortcodes.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @param array $block_shortcodes List of block shortcodes by name.
	 */
	$block_shortcodes = apply_filters( 'gcoa_block_shortcodes', $block_shortcodes );

	foreach ( $block_shortcodes as $block_shortcode ) {
		$shortcode_file = get_theme_file_path( '/inc/shortcodes/' . $block_shortcode . '.php' );
		GC_Block_Patterns_Registry::get_instance()->register('gcoa/' . $block_shortcode, require $shortcode_file);
	}
}
add_action( 'init', 'gcoa_register_block_shortcodes', 9 );
