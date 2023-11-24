<?php
/**
 * Server-side rendering of the `core/avatar` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/avatar` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Return the avatar.
 */
function render_block_gcoa_core_avatar( $attributes, $content, $block ) {
	if ( ! get_option( 'show_avatars' ) ) {
		return '';
	}
	$size               = isset( $attributes['size'] ) ? $attributes['size'] : 96;
	$wrapper_attributes = get_block_wrapper_attributes();
	$border_attributes  = get_block_core_avatar_border_attributes( $attributes );

	// Class gets passed through `esc_attr` via `get_avatar`.
	$image_classes = ! empty( $border_attributes['class'] )
		? "avatar avatar-icon {$border_attributes['class']}"
		: 'avatar avatar-icon';


	// Unlike class, `get_avatar` doesn't filter the styles via `esc_attr`.
	// The style engine does pass the border styles through
	// `safecss_filter_attr` however.
	$image_styles = ! empty( $border_attributes['style'] )
		? sprintf( ' style="%s"', esc_attr( $border_attributes['style'] ) )
		: '';

	if ( ! isset( $block->context['commentId'] ) ) {
		$author_id   = isset( $attributes['userId'] ) ? $attributes['userId'] : get_post_field( 'post_author', $block->context['postId'] );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		// translators: %s is the Author name.
		$alt          = sprintf( __( '%s 的头像' ), $author_name );
		$avatar_block = get_avatar(
			$author_id,
			$size,
			'',
			$alt,
			array(
				'extra_attr' => $image_styles,
				'class'      => $image_classes,
			)
		);
		if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
			$label = '';
			if ( '_blank' === $attributes['linkTarget'] ) {
				// translators: %s is the Author name.
				$label = 'aria-label="' . sprintf( esc_attr__( '（%s 的作者归档，在新标签页中打开）' ), $author_name ) . '"';
			}
			// translators: %1$s: Author archive link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
			$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="gc-block-avatar__link">%4$s</a>', esc_url( get_author_posts_url( $author_id ) ), esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
		}
		return sprintf( '<div class="avatar avatar-image bg-primary %1s" %2s>%3s<i class="anticon anticon-user"></i></div>', $attributes['className'], $wrapper_attributes, $avatar_block );
	}
	$comment = get_comment( $block->context['commentId'] );
	if ( ! $comment ) {
		return '';
	}
	/* translators: %s is the Comment Author name */
	$alt          = sprintf( __( '%s 的头像' ), $comment->comment_author );
	$avatar_block = get_avatar(
		$comment,
		$size,
		'',
		$alt,
		array(
			'extra_attr' => $image_styles,
			'class'      => $image_classes,
		)
	);
	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] && isset( $comment->comment_author_url ) && '' !== $comment->comment_author_url ) {
		$label = '';
		if ( '_blank' === $attributes['linkTarget'] ) {
			// translators: %s is the Comment Author name.
			$label = 'aria-label="' . sprintf( esc_attr__( '（%s 链接，在新标签页中打开）' ), $comment->comment_author ) . '"';
		}
		// translators: %1$s: Comment Author website link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
		$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="gc-block-avatar__link">%4$s</a>', esc_url( $comment->comment_author_url ), esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
	}
	return sprintf( '<div class="avatar avatar-image bg-primary %1s" %2s>%3s<i class="anticon anticon-user"></i></div>', $attributes['className'], $wrapper_attributes, $avatar_block );
}

/**
 * Registers the `core/avatar` block on the server.
 */
function register_block_gcoa_core_avatar() {
	// 删除原有的区块
	unregister_block_type('core/avatar');
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/avatar',
		array(
			'render_callback' => 'render_block_gcoa_core_avatar',
		)
	);
}
add_action( 'init', 'register_block_gcoa_core_avatar' );
