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
function render_block_core_gcoa_post_comment_replytocom_link( $attributes, $content, $block ) {
	if ( ! isset( $block->context['comment_ID'] ) ) {
		return '';
	}
	$comment_ID = $block->context['comment_ID'];

	$permalink = str_replace( '#comment-' . $comment_ID, '', get_comment_link( $comment ) );

	
	return esc_url(
				add_query_arg(
					array(
						'replytocom'      => $comment_ID,
						'unapproved'      => false,
						'moderation-hash' => false,
					),
					get_permalink()
				)
			) . '#respond';

	return esc_html( remove_query_arg( array( 'replytocom', 'unapproved', 'moderation-hash' ) ) ) . '#respond';
}

/**
 * Registers the `core/gcoa-post-date` block on the server.
 */
function register_block_core_gcoa_post_comment_replytocom_link() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comment-replytocom-link',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comment_replytocom_link',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comment_replytocom_link' );
