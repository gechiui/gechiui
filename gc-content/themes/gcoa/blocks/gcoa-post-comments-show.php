<?php
/**
 * Server-side rendering of the `core/gcoa-post-comments-show` block.
 *
 * @package GeChiUI
 */
function render_block_core_gcoa_post_comments_show( $attributes, $content, $block ) {

	$post_id = $block->context['postId'];

	
	if ( ! isset( $post_id ) ) {
		return '';
	}

	if(comments_open()) {
		return (
			new GC_Block(
				$block->parsed_block,
				array('postId'   => $post_id , )
			)
		)->render( array( 'dynamic' => false ) );
	}
}


/**
 * Registers the `core/gcoa-post-comments-show` block on the server.
 */

function register_block_core_gcoa_post_comments_show() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comments-show',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comments_show',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comments_show' );