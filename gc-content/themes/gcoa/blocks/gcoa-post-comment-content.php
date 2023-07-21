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
function render_block_core_gcoa_post_comment_content( $attributes, $content, $block ) {
	if ( ! isset( $block->context['comment_ID'] ) ) {
		return '';
	}

	$text = "";
	$comment_parent_text = "";
	$comment_ID = $block->context['comment_ID'];
	$comment = get_comment($comment_ID);
	if ($comment->comment_parent > 0){
		$comment_parent = get_comment($comment->comment_parent);
		$comment_parent_author =get_comment_author($comment->comment_parent);
		$comment_parent_text = '<div class="col-sm-12"><div class="border-bottom p-v-20"><p class="text-opacity"><small>'. $comment_parent->comment_content .'<br> -- '. $comment_parent_author .'</small></p></div></div>';
		$text .= '回复 @'.$comment_parent_author . ' ';
	}
	$text .= get_comment_text($comment_ID) . $comment_parent_text ;
	return $text;

}

/**
 * Registers the `core/gcoa-post-date` block on the server.
 */
function register_block_core_gcoa_post_comment_content() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comment-content',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comment_content',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comment_content' );

