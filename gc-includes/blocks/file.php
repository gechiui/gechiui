<?php
/**
 * Server-side rendering of the `core/file` block.
 *
 * @package GeChiUI
 */

/**
 * When the `core/file` block is rendering, check if we need to enqueue the `'gc-block-file-view` script.
 *
 * @param array $attributes The block attributes.
 * @param array $content    The block content.
 *
 * @return string Returns the block content.
 */
function render_block_core_file( $attributes, $content ) {
	$should_load_view_script = ! empty( $attributes['displayPreview'] ) && ! gc_script_is( 'gc-block-file-view' );
	if ( $should_load_view_script ) {
		gc_enqueue_script( 'gc-block-file-view' );
	}

	return $content;
}

/**
 * Registers the `core/file` block on server.
 */
function register_block_core_file() {
	register_block_type_from_metadata(
		__DIR__ . '/file',
		array(
			'render_callback' => 'render_block_core_file',
		)
	);
}
add_action( 'init', 'register_block_core_file' );
