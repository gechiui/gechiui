<?php
/**
 * Server-side rendering of the `core/comments` block.
 *
 * @package GeChiUI
 */
function render_block_gcoa_core_comments( $attributes, $content, $block ) {

	$post_ID = $block->context['postId'];
	$current_user_id = get_current_user_id(); // 当前登录用户ID

	
	if ( ! isset( $post_ID ) ) {
		return '';
	}

	// 删除评论
	$delete_id = isset( $_GET['delete'] ) ? (int) $_GET['delete'] : 0;
	if ($delete_id > 0) {
		gc_delete_comment($delete_id, false);
		gc_redirect(get_permalink($post_ID));
		return;
	}

	// 构造HTML
	$content = '<hr>
    <h5>评论 ('.get_comment_count($post_ID)['approved'].')</h5>
    <div class="m-t-20">
        <ul class="list-group list-group-flush">';
    $comments = get_comments(array( 'post_id' => $post_ID ));

    foreach ($comments as $key => $comment) {
    	// 评论者用户信息
    	$comment_userdata = get_userdata($comment->user_id);
    	$text = "";
		$comment_parent_text = "";
    	if ($comment->comment_parent > 0){
			$comment_parent = get_comment($comment->comment_parent);
			$comment_parent_author =get_comment_author($comment->comment_parent);
			$comment_parent_text = '<div class="col-sm-12"><div class="border-bottom p-v-20"><p class="text-opacity"><small>'. $comment_parent->comment_content .'<br> -- '. $comment_parent_author .'</small></p></div></div>';
			$text .= '回复 @'.$comment_parent_author . ' ';
		}
		$text .= get_comment_text($comment->comment_ID) . $comment_parent_text ;

		$delete_link = esc_url(
				add_query_arg(
					array(
						'delete'      => $comment->comment_ID,
						'unapproved'      => false,
						'moderation-hash' => false,
					),
					get_permalink()
				)
			) . '#respond';

		$replytocom_link = esc_url(
				add_query_arg(
					array(
						'replytocom'      => $comment->comment_ID,
						'unapproved'      => false,
						'moderation-hash' => false,
					),
					get_permalink()
				)
			) . '#respond';

    	$content .= '
    		<li class="list-group-item p-h-0">
                <div class="media m-b-15">
                    <div class="avatar avatar-image">
                        <img src="'.get_avatar_url($comment->user_id).'" alt="">
                    </div>
                    <div class="media-body m-l-20">
                        <h6 class="m-b-0">
                            <a href="'. get_author_posts_url($comment->user_id) .'" class="text-dark">'.$comment_userdata->display_name.'</a>
                        </h6>
                        <span class="font-size-13 text-gray">'.get_comment_date('Y-m-d H:i:s', $comment->comment_ID).'</span>
                    </div>
                </div>
                <span>'.$text.'</span>
                <div class="m-t-15">
                    <ul class="list-inline text-right">';
        if( $current_user_id == $comment->user_id ){
        	$content .= '
                    	<li class="d-inline-block m-r-20">
                            <a class="text-dark" href="'.$delete_link.'">
                                <i class="anticon m-r-5 anticon-delete"></i>
                                <span>删除</span>
                            </a>
                        </li>';
         }
         $content .= '
                        <li class="d-inline-block m-r-30">
                            <a class="text-dark" href="'.$replytocom_link.'">
                                <i class="anticon m-r-5 anticon-message"></i>
                                <span>回复</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>';
    }

    $content .= '</ul></div>';
    

    // 获取回复评论ID
	$replytocom = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;

	// 回复作者
	$placeholder = '';
	if ($replytocom > 0) {
		$placeholder = '回复 @' . get_comment_author($replytocom) . '&nbsp;&nbsp;' .get_comment_text($replytocom) ;
	}

	$content .= '
	<div id="respond" class="m-t-30">
		<h5 class="m-b-20">发表评论</h5>
		<p>'.$placeholder.'</p>
		<form action="/gc-comments-post.php" method="post" id="commentform" novalidate="">
            <div class="form-group">
                <textarea id="comment" name="comment" class="form-control" cols="45" rows="8" maxlength="65525" required="" placeholder="填写评论内容"></textarea>
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

	return $content;
}


/**
 * Registers the `core/comments` block on the server.
 */

function register_block_gcoa_core_comments() {
	// 删除原有的区块
	unregister_block_type('core/comments');
	// 注册新的
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/comments',
		array(
			'render_callback' => 'render_block_gcoa_core_comments',
		)
	);
}
add_action( 'init', 'register_block_gcoa_core_comments' );