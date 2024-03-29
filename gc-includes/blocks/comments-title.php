<?php
/**
 * Server-side rendering of the `core/comments-title` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/comments-title` block on the server.
 *
 * @param array $attributes Block attributes.
 *
 * @return string Return the post comments title.
 */
function render_block_core_comments_title( $attributes ) {

	if ( post_password_required() ) {
		return;
	}

	$align_class_name    = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$show_post_title     = ! empty( $attributes['showPostTitle'] ) && $attributes['showPostTitle'];
	$show_comments_count = ! empty( $attributes['showCommentsCount'] ) && $attributes['showCommentsCount'];
	$wrapper_attributes  = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );
	$comments_count      = get_comments_number();
	/* translators: %s: Post title. */
	$post_title = sprintf( __( '“%s”' ), get_the_title() );
	$tag_name   = 'h2';
	if ( isset( $attributes['level'] ) ) {
		$tag_name = 'h' . $attributes['level'];
	}

	if ( '0' === $comments_count ) {
		return;
	}

	if ( $show_comments_count ) {
		if ( $show_post_title ) {
			if ( '1' === $comments_count ) {
				/* translators: %s: Post title. */
				$comments_title = sprintf( __( '《%s》 有 1 条评论' ), $post_title );
			} else {
				$comments_title = sprintf(
					/* translators: 1: Number of comments, 2: Post title. */
					_n(
						'《%2$s》 有 %1$s 条评论',
						'《%2$s》 有 %1$s 条评论',
						$comments_count
					),
					number_format_i18n( $comments_count ),
					$post_title
				);
			}
		} elseif ( '1' === $comments_count ) {
			$comments_title = __( '1 条回复' );
		} else {
			$comments_title = sprintf(
				/* translators: %s: Number of comments. */
				_n( '%s 条回复', '%s 条回复', $comments_count ),
				number_format_i18n( $comments_count )
			);
		}
	} elseif ( $show_post_title ) {
		if ( '1' === $comments_count ) {
			/* translators: %s: Post title. */
			$comments_title = sprintf( __( '%s 的回复' ), $post_title );
		} else {
			/* translators: %s: Post title. */
			$comments_title = sprintf( __( '%s 的回复' ), $post_title );
		}
	} elseif ( '1' === $comments_count ) {
		$comments_title = __( '回复' );
	} else {
		$comments_title = __( '回复' );
	}

	return sprintf(
		'<%1$s id="comments" %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$comments_title
	);
}

/**
 * Registers the `core/comments-title` block on the server.
 */
function register_block_core_comments_title() {
	register_block_type_from_metadata(
		__DIR__ . '/comments-title',
		array(
			'render_callback' => 'render_block_core_comments_title',
		)
	);
}

add_action( 'init', 'register_block_core_comments_title' );
