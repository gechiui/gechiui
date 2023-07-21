<?php
/**
 * Server-side rendering of the `core/gcoa-post-author` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-author` block on the server.
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  GC_Block $block      Block instance.
 * @return string Returns the rendered author block.
 */
function render_block_core_gcoa_post_author( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$author_id = get_post_field( 'post_author', $block->context['postId'] );
	if ( empty( $author_id ) ) {
		return '';
	}

	return get_the_author_meta( 'display_name', $author_id );
}

/**
 * Registers the `core/gcoa-post-author` block on the server.
 */
function register_block_core_gcoa_post_author() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-author',
		array(
			'render_callback' => 'render_block_core_gcoa_post_author',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_author' );
