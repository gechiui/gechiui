<?php
/**
 * Server-side rendering of the `core/gcoa-post-title` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-title` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block_core_gcoa_post_title( $attributes, $content, $block ) {

	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];
	$title   = get_the_title();

	if ( ! $title ) {
		return '';
	}
	return $title;
}

/**
 * Registers the `core/gcoa-post-title` block on the server.
 */
function register_block_core_gcoa_post_title() {
	register_block_type_from_metadata(
		__DIR__ . '/gcoa-post-title',
		array(
			'render_callback' => 'render_block_core_gcoa_post_title',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_title' );
