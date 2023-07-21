<?php
/**
 * Server-side rendering of the `core/gcoa-post-comment-template` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-comment-template` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post comments for the current post wrapped inside "p" tags.
 */
function render_block_core_gcoa_post_comment_template( $attributes, $content, $block ) {
	global $post;

	$post_id = $block->context['postId'];
	if ( ! isset( $post_id ) ) {
		return '';
	}

	$comment_args = array(
		'orderby' => 'comment_date_gmt',
		'order' => 'ASC',
		'post_id' => $post_id,
		'status'    => 'approve',
		'count'   => false, // 是返回列表还是返回总数
	);
	$comments = get_comments( $comment_args );

	$content = '';

	foreach ($comments as $comment) {
		$block_content = (
			new GC_Block(
				$block->parsed_block,
				array('comment_ID'   => $comment->comment_ID , 'user_id' =>$comment->user_id)
			)
		)->render( array( 'dynamic' => false ) );
		$content .= $block_content;
	}

	return $content;
}

/**
 * Registers the `core/gcoa-post-comment-template` block on the server.
 */
function register_block_core_gcoa_post_comment_template() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comment-template',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comment_template',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comment_template' );

/**
 * Use the button block classes for the form-submit button.
 *
 * @param array $fields The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function post_comment_template_block_form_defaults( $fields ) {
	if ( gc_is_block_theme() ) {
		$fields['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s gc-block-button__link" value="%4$s" />';
		$fields['submit_field']  = '<p class="form-submit gc-block-button">%1$s %2$s</p>';
	}

	return $fields;
}
add_filter( 'comment_form_defaults', 'post_comment_template_block_form_defaults' );
