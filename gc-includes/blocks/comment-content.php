<?php
/**
 * Server-side rendering of the `core/comment-content` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/comment-content` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Return the post comment's content.
 */
function render_block_core_comment_content( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	$comment            = get_comment( $block->context['commentId'] );
	$commenter          = gc_get_current_commenter();
	$show_pending_links = isset( $commenter['comment_author'] ) && $commenter['comment_author'];
	if ( empty( $comment ) ) {
		return '';
	}

	$args         = array();
	$comment_text = get_comment_text( $comment, $args );
	if ( ! $comment_text ) {
		return '';
	}

	/** This filter is documented in gc-includes/comment-template.php */
	$comment_text = apply_filters( 'comment_text', $comment_text, $comment, $args );

	$moderation_note = '';
	if ( '0' === $comment->comment_approved ) {
		$commenter = gc_get_current_commenter();

		if ( $commenter['comment_author_email'] ) {
			$moderation_note = __( '您的评论正在等待审核。' );
		} else {
			$moderation_note = __( '您的评论正在等待审核。此为预览，您的评论将在获得批准后显示。' );
		}
		$moderation_note = '<p><em class="comment-awaiting-moderation">' . $moderation_note . '</em></p>';
		if ( ! $show_pending_links ) {
			$comment_text = gc_kses( $comment_text, array() );
		}
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf(
		'<div %1$s>%2$s%3$s</div>',
		$wrapper_attributes,
		$moderation_note,
		$comment_text
	);
}

/**
 * Registers the `core/comment-content` block on the server.
 */
function register_block_core_comment_content() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-content',
		array(
			'render_callback' => 'render_block_core_comment_content',
		)
	);
}
add_action( 'init', 'register_block_core_comment_content' );
