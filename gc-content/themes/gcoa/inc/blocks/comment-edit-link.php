<?php
/**
 * Server-side rendering of the `core/comment-edit-link` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/comment-edit-link` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Return the post comment's date.
 */
function render_block_gcoa_core_comment_edit_link( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) || ! current_user_can( 'edit_comment', $block->context['commentId'] ) ) {
		return '';
	}

	$edit_comment_link = get_edit_comment_link( $block->context['commentId'] );

	$link_atts = '';

	if ( ! empty( $attributes['linkTarget'] ) ) {
		$link_atts .= sprintf( 'target="%s"', esc_attr( $attributes['linkTarget'] ) );
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
		'<div %1$s><a href="%2$s" class="text-dark" %3$s><i class="anticon m-r-5 anticon-edit"></i>%4$s</a></div>',
		$wrapper_attributes,
		esc_url( $edit_comment_link ),
		$link_atts,
		esc_html__( '编辑' )
	);
}

/**
 * Registers the `core/comment-edit-link` block on the server.
 */
function register_block_gcoa_core_comment_edit_link() {
	// 删除原有的区块
	unregister_block_type('core/comment-edit-link');
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/comment-edit-link',
		array(
			'render_callback' => 'render_block_gcoa_core_comment_edit_link',
		)
	);
}

add_action( 'init', 'register_block_gcoa_core_comment_edit_link' );
