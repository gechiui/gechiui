<?php
/**
 * Server-side rendering of the `core/gcoa-post-date` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-date` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post date for the current post wrapped inside "time" tags.
 */
function render_block_core_gcoa_post_comment_avatar( $attributes, $content, $block ) {
	if ( ! isset( $block->context['user_id'] ) ) {
		return '';
	}

	$user_ID = $block->context['user_id'];
	return get_avatar_url($user_ID);
}

/**
 * Registers the `core/gcoa-post-date` block on the server.
 */
function register_block_core_gcoa_post_comment_avatar() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comment-avatar',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comment_avatar',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comment_avatar' );
