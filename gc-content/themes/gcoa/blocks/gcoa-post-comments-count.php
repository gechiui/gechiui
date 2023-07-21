<?php
/**
 * Server-side rendering of the `core/gcoa-post-comments-count` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-comments` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post comments for the current post wrapped inside "p" tags.
 */
function render_block_core_gcoa_post_comments_count( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return 0;
	}

	$post_ID = $block->context['postId'];

	return get_comment_count($post_ID)['approved'];

}

/**
 * Registers the `core/gcoa-post-comments-count` block on the server.
 */
function register_block_core_gcoa_post_comments_count() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comments-count',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comments_count',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comments_count' );
