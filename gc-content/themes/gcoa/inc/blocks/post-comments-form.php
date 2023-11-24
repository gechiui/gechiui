<?php
/**
 * Server-side rendering of the `core/post-comments-form` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/post-comments-form` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post comments form for the current post.
 */
function render_block_gcoa_core_post_comments_form( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	if ( post_password_required( $block->context['postId'] ) ) {
		return;
	}

	$classes = array( 'comment-respond' ); // See comment further below.
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	add_filter( 'comment_form_defaults', 'gcoa_post_comments_form_block_form_defaults' );

	ob_start();
	gcoa_comment_form( array(), $block->context['postId'] );
	$form = ob_get_clean();

	remove_filter( 'comment_form_defaults', 'gcoa_post_comments_form_block_form_defaults' );

	// We use the outermost wrapping `<div />` returned by `comment_form()`
	// which is identified by its default classname `comment-respond` to inject
	// our wrapper attributes. This way, it is guaranteed that all styling applied
	// to the block is carried along when the comment form is moved to the location
	// of the 'Reply' link that the user clicked by Core's `comment-reply.js` script.
	$form = str_replace( 'class="comment-respond"', $wrapper_attributes, $form );

	// Enqueue the comment-reply script.
	gc_enqueue_script( 'comment-reply' );

	return $form;
}

/**
 * Registers the `core/post-comments-form` block on the server.
 */
function register_block_gcoa_core_post_comments_form() {
	// 删除原有的区块
	unregister_block_type('core/post-comments-form');
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/post-comments-form',
		array(
			'render_callback' => 'render_block_gcoa_core_post_comments_form',
		)
	);
}
add_action( 'init', 'register_block_gcoa_core_post_comments_form' );

/**
 * Use the button block classes for the form-submit button.
 *
 * @param array $fields The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function gcoa_post_comments_form_block_form_defaults( $fields ) {
	if ( gc_is_block_theme() ) {
		$fields['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="btn btn-primary" value="%4$s" />';
		$fields['submit_field']  = '<div class="text-right m-t-25 m-b-25">%1$s %2$s</div>';
	}

	return $fields;
}

function gcoa_comment_form( $args = array(), $post = null ) {
	$post = get_post( $post );

	// Exit the function if the post is invalid or comments are closed.
	if ( ! $post || ! comments_open( $post ) ) {
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * For backward compatibility, this action also fires if comment_form()
		 * is called with an invalid post object or ID.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );

		return;
	}

	$post_id       = $post->ID;
	$commenter     = gc_get_current_commenter();
	$user          = gc_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = gc_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req   = get_option( 'require_name_email' );
	$html5 = 'html5' === $args['format'];

	// Define attributes in HTML5 or XHTML syntax.
	$required_attribute = ( $html5 ? ' required' : ' required="required"' );
	$checked_attribute  = ( $html5 ? ' checked' : ' checked="checked"' );

	// Identify required fields visually and create a message about the indicator.
	$required_indicator = ' ' . gc_required_field_indicator();
	$required_text      = ' ' . gc_required_field_message();

	if ( has_action( 'set_comment_cookies', 'gc_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
		$consent = empty( $commenter['comment_author_email'] ) ? '' : $checked_attribute;

	}



	$defaults = array(
		'comment_field'        => sprintf(
			'<p class="m-t-10">%s</p>',
			'<textarea id="comment" name="comment" class="form-control" cols="45" rows="8" maxlength="65525" placeholder="填写评论内容" ' . $required_attribute . '></textarea>'
		),
		'must_log_in'          => sprintf(
			'<p class="must-log-in">%s</p>',
			sprintf(
				/* translators: %s: Login URL. */
				__( '要发表评论，您必须先<a href="%s">登录</a>。' ),
				/** This filter is documented in gc-includes/link-template.php */
				gc_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
			)
		),
		'comment_notes_before' => sprintf(
			'<p class="comment-notes">%s%s</p>',
			sprintf(
				'<span id="email-notes">%s</span>',
				__( '您的电子邮箱不会被公开。' )
			),
			$required_text
		),
		'comment_notes_after'  => '',
		'action'               => site_url( '/gc-comments-post.php' ),
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'class_container'      => 'comment-respond',
		'class_form'           => 'comment-form',
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( '评论' ),
		/* translators: %s: Author of the comment being replied to. */
		'title_reply_to'       => __( '回复 %s' ),
		'title_reply_before'   => '<h5 id="reply-title" class="comment-reply-title">',
		'title_reply_after'    => '</h5>',
		'cancel_reply_before'  => ' <small class="m-l-10">',
		'cancel_reply_after'   => '</small>',
		'cancel_reply_link'    => __( '取消回复' ),
		'label_submit'         => __( '发表评论' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filters the comment form default arguments.
	 *
	 * Use {@see 'comment_form_default_fields'} to filter the comment fields.
	 *
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = gc_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered arguments contain all required default values.
	$args = array_merge( $defaults, $args );

	// Remove `aria-describedby` from the email field if there's no associated description.
	if ( isset( $args['fields']['email'] ) && ! str_contains( $args['comment_notes_before'], 'id="email-notes"' ) ) {
		$args['fields']['email'] = str_replace(
			' aria-describedby="email-notes"',
			'',
			$args['fields']['email']
		);
	}

	/**
	 * Fires before the comment form.
	 *
	 */
	do_action( 'comment_form_before' );
	?>
	<div id="respond" class="m-t-10">
		<?php
		echo $args['title_reply_before'];

		comment_form_title( $args['title_reply'], $args['title_reply_to'], true, $post_id );

		if ( get_option( 'thread_comments' ) ) {
			echo $args['cancel_reply_before'];

			cancel_comment_reply_link( $args['cancel_reply_link'] );

			echo $args['cancel_reply_after'];
		}

		echo $args['title_reply_after'];

		if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) :

			echo $args['must_log_in'];
			/**
			 * Fires after the HTML-formatted 'must log in after' message in the comment form.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_must_log_in_after' );

		else :

			printf(
				'<form action="%s" method="post" id="%s" class="%s"%s>',
				esc_url( $args['action'] ),
				esc_attr( $args['id_form'] ),
				esc_attr( $args['class_form'] ),
				( $html5 ? ' novalidate' : '' )
			);

			/**
			 * Fires at the top of the comment form, inside the form tag.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_top' );

			if ( is_user_logged_in() ) :

				/**
				 * Fires after the is_user_logged_in() check in the comment form.
				 *
				 * @since 3.0.0
				 *
				 * @param array  $commenter     An array containing the comment author's
				 *                              username, email, and URL.
				 * @param string $user_identity If the commenter is a registered user,
				 *                              the display name, blank otherwise.
				 */
				do_action( 'comment_form_logged_in_after', $commenter, $user_identity );

			else :

				echo $args['comment_notes_before'];

			endif;

			// Prepare an array of all fields, including the textarea.
			$comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];

			/**
			 * Filters the comment form fields, including the textarea.
			 *
			 * @since 4.4.0
			 *
			 * @param array $comment_fields The comment fields.
			 */
			$comment_fields = apply_filters( 'comment_form_fields', $comment_fields );

			// Get an array of field names, excluding the textarea.
			$comment_field_keys = array_diff( array_keys( $comment_fields ), array( 'comment' ) );

			// Get the first and the last field name, excluding the textarea.
			$first_field = reset( $comment_field_keys );
			$last_field  = end( $comment_field_keys );

			foreach ( $comment_fields as $name => $field ) {

				if ( 'comment' === $name ) {

					/**
					 * Filters the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $field );

					echo $args['comment_notes_after'];

				} elseif ( ! is_user_logged_in() ) {

					if ( $first_field === $name ) {
						/**
						 * Fires before the comment fields in the comment form, excluding the textarea.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
					}

					/**
					 * Filters a comment form field for display.
					 *
					 * The dynamic portion of the hook name, `$name`, refers to the name
					 * of the comment form field.
					 *
					 * Possible hook names include:
					 *
					 *  - `comment_form_field_comment`
					 *  - `comment_form_field_author`
					 *  - `comment_form_field_email`
					 *  - `comment_form_field_url`
					 *  - `comment_form_field_cookies`
					 *
					 * @since 3.0.0
					 *
					 * @param string $field The HTML-formatted output of the comment form field.
					 */
					echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";

					if ( $last_field === $name ) {
						/**
						 * Fires after the comment fields in the comment form, excluding the textarea.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
					}
				}
			}

			$submit_button = sprintf(
				$args['submit_button'],
				esc_attr( $args['name_submit'] ),
				esc_attr( $args['id_submit'] ),
				esc_attr( $args['class_submit'] ),
				esc_attr( $args['label_submit'] )
			);

			$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

			$submit_field = sprintf(
				$args['submit_field'],
				$submit_button,
				get_comment_id_fields( $post_id )
			);

			

			echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

			do_action( 'comment_form', $post_id );

			echo '</form>';

		endif;
		?>
	</div><!-- #respond -->
	<?php

	/**
	 * Fires after the comment form.
	 *
	 */
	do_action( 'comment_form_after' );
}