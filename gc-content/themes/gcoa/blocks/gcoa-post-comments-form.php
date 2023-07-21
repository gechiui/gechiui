<?php
/**
 * Server-side rendering of the `core/gcoa-post-comments-form` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-post-comments` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 * @return string Returns the filtered post comments for the current post wrapped inside "p" tags.
 */
function render_block_core_gcoa_post_comments_form( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];

	// 获取回复评论ID
	$replytocom = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;

	// 回复作者
	if ($replytocom > 0) {
		$placeholder = '回复 @' . get_comment_author($replytocom);
	}else{
		$placeholder = '填写评论内容';
	}

	return '
	<div id="respond" class="m-t-30">
		<h5 class="m-b-20">发表评论</h5>
		<form action="/gc-comments-post.php" method="post" id="commentform" novalidate="">
            <div class="form-group">
                <textarea id="comment" name="comment" class="form-control" cols="45" rows="8" maxlength="65525" required="" placeholder="'. $placeholder .'"></textarea>
            </div>
            <div class="text-right m-t-25">
                <input name="submit" type="submit" id="submit" class="btn btn-primary" value="发表评论">
                <input type="hidden" name="comment_post_ID" value="'. $post_ID .'" id="comment_post_ID">
                <input type="hidden" name="comment_parent" id="comment_parent" value="'. $replytocom .'">
                '. gc_nonce_field( 'unfiltered-html-comment_' . $post_ID, '_gc_unfiltered_html_comment_disabled', false ) .'
                <script>(function(){if(window===window.parent){document.getElementById(\'_gc_unfiltered_html_comment_disabled\').name=\'_gc_unfiltered_html_comment\';}})();</script>
            </div>
        </form>
	</div>
	';

}

/**
 * Registers the `core/gcoa-post-comments-form` block on the server.
 */
function register_block_core_gcoa_post_comments_form() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comments-form',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comments_form',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comments_form' );
