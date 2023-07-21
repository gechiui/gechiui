<?php
/**
 * Server-side rendering of the `core/gcoa-post-id` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-id` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post id for the current post wrapped inside "time" tags.
 */
function render_block_core_gcoa_post_id( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	return $block->context['postId'];
}

/**
 * Registers the `core/gcoa-post-id` block on the server.
 */
function register_block_core_gcoa_post_id() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-id',
		array(
			'render_callback' => 'render_block_core_gcoa_post_id',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_id' );
