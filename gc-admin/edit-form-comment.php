<?php
/**
 * Edit comment form for inclusion in another file.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<form name="post" action="comment.php" method="post" id="post">
<?php gc_nonce_field( 'update-comment_' . $comment->comment_ID ); ?>
<div class="wrap">
<h1><?php _e( '编辑评论' ); ?></h1>

<div id="poststuff">
<input type="hidden" name="action" value="editedcomment" />
<input type="hidden" name="comment_ID" value="<?php echo esc_attr( $comment->comment_ID ); ?>" />
<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( $comment->comment_post_ID ); ?>" />

<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content" class="edit-form-section edit-comment-section">
<?php
if ( 'approved' === gc_get_comment_status( $comment ) && $comment->comment_post_ID > 0 ) :
	$comment_link = get_comment_link( $comment );
	?>
<div class="inside">
	<div id="comment-link-box">
		<strong><?php _ex( '固定链接：', 'comment' ); ?></strong>
		<span id="sample-permalink">
			<a href="<?php echo esc_url( $comment_link ); ?>">
				<?php echo esc_html( $comment_link ); ?>
			</a>
		</span>
	</div>
</div>
<?php endif; ?>
<div id="namediv" class="stuffbox">
<div class="inside">
<h2 class="edit-comment-author"><?php _e( '作者' ); ?></h2>
<fieldset>
<legend class="screen-reader-text"><?php _e( '评论者' ); ?></legend>
<table class="form-table editcomment" role="presentation">
<tbody>
<tr>
	<td class="first"><label for="name"><?php _e( '显示名称' ); ?></label></td>
	<td><input type="text" name="newcomment_author" size="30" value="<?php echo esc_attr( $comment->comment_author ); ?>" id="name" /></td>
</tr>
<tr>
	<td class="first"><label for="email"><?php _e( '电子邮箱' ); ?></label></td>
	<td>
		<input type="text" name="newcomment_author_email" size="30" value="<?php echo esc_attr( $comment->comment_author_email ); ?>" id="email" />
	</td>
</tr>
<tr>
	<td class="first"><label for="newcomment_author_url"><?php _e( 'URL' ); ?></label></td>
	<td>
		<input type="text" id="newcomment_author_url" name="newcomment_author_url" size="30" class="code" value="<?php echo esc_attr( $comment->comment_author_url ); ?>" />
	</td>
</tr>
</tbody>
</table>
</fieldset>
</div>
</div>

<div id="postdiv" class="postarea">
<?php
	echo '<label for="content" class="screen-reader-text">' . __( '评论' ) . '</label>';
	$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	gc_editor(
		$comment->comment_content,
		'content',
		array(
			'media_buttons' => false,
			'tinymce'       => false,
			'quicktags'     => $quicktags_settings,
		)
	);
	gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	?>
</div>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<div id="submitdiv" class="stuffbox" >
<h2><?php _e( '保存' ); ?></h2>
<div class="inside">
<div class="submitbox" id="submitcomment">
<div id="minor-publishing">

<div id="misc-publishing-actions">

<div class="misc-pub-section misc-pub-comment-status" id="comment-status">
<?php _e( '状态：' ); ?> <span id="comment-status-display">
<?php
switch ( $comment->comment_approved ) {
	case '1':
		_e( '已批准' );
		break;
	case '0':
		_e( '待审' );
		break;
	case 'spam':
		_e( '垃圾' );
		break;
}
?>
</span>

<fieldset id="comment-status-radio">
<legend class="screen-reader-text"><?php _e( '评论状态' ); ?></legend>
<label><input type="radio"<?php checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /><?php _ex( '已批准', 'comment status' ); ?></label><br />
<label><input type="radio"<?php checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /><?php _ex( '待审', 'comment status' ); ?></label><br />
<label><input type="radio"<?php checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /><?php _ex( '垃圾', 'comment status' ); ?></label>
</fieldset>
</div><!-- .misc-pub-section -->

<div class="misc-pub-section curtime misc-pub-curtime">
<?php
$submitted = sprintf(
	/* translators: 1: Comment date, 2: Comment time. */
	__( '%1$s %2$s' ),
	/* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'Y年n月j日', 'publish box date format' ), strtotime( $comment->comment_date ) ),
	/* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'H:i', 'publish box time format' ), strtotime( $comment->comment_date ) )
);
?>
<span id="timestamp">
<?php
/* translators: %s: Comment date. */
printf( __( '提交于：%s' ), '<b>' . $submitted . '</b>' );
?>
</span>
<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><span aria-hidden="true"><?php _e( '编辑' ); ?></span> <span class="screen-reader-text"><?php _e( '编辑日期和时间' ); ?></span></a>
<fieldset id='timestampdiv' class='hide-if-js'>
<legend class="screen-reader-text"><?php _e( '日期和时间' ); ?></legend>
<?php touch_time( ( 'editcomment' === $action ), 0 ); ?>
</fieldset>
</div>

<?php
$post_id = $comment->comment_post_ID;
if ( current_user_can( 'edit_post', $post_id ) ) {
	$post_link  = "<a href='" . esc_url( get_edit_post_link( $post_id ) ) . "'>";
	$post_link .= esc_html( get_the_title( $post_id ) ) . '</a>';
} else {
	$post_link = esc_html( get_the_title( $post_id ) );
}
?>

<div class="misc-pub-section misc-pub-response-to">
	<?php
	printf(
		/* translators: %s: Post link. */
		__( '回应给：%s' ),
		'<b>' . $post_link . '</b>'
	);
	?>
</div>

<?php
if ( $comment->comment_parent ) :
	$parent = get_comment( $comment->comment_parent );
	if ( $parent ) :
		$parent_link = esc_url( get_comment_link( $parent ) );
		$name        = get_comment_author( $parent );
		?>
	<div class="misc-pub-section misc-pub-reply-to">
		<?php
		printf(
			/* translators: %s: Comment link. */
			__( '回复给：%s' ),
			'<b><a href="' . $parent_link . '">' . $name . '</a></b>'
		);
		?>
	</div>
		<?php
endif;
endif;
?>

<?php
	/**
	 * Filters miscellaneous actions for the edit comment form sidebar.
	 *
	 *
	 * @param string     $html    Output HTML to display miscellaneous action.
	 * @param GC_Comment $comment Current comment object.
	 */
	echo apply_filters( 'edit_comment_misc_actions', '', $comment );
?>

</div> <!-- misc actions -->
<div class="clear"></div>
</div>

<div id="major-publishing-actions">
<div id="delete-action">
<?php echo "<a class='submitdelete deletion' href='" . gc_nonce_url( 'comment.php?action=' . ( ! EMPTY_TRASH_DAYS ? 'deletecomment' : 'trashcomment' ) . "&amp;c=$comment->comment_ID&amp;_gc_original_http_referer=" . urlencode( gc_get_referer() ), 'delete-comment_' . $comment->comment_ID ) . "'>" . ( ! EMPTY_TRASH_DAYS ? __( '永久删除' ) : __( '移动至回收站' ) ) . "</a>\n"; ?>
</div>
<div id="publishing-action">
<?php submit_button( __( '更新' ), 'primary large', 'save', false ); ?>
</div>
<div class="clear"></div>
</div>
</div>
</div>
</div><!-- /submitdiv -->
</div>

<div id="postbox-container-2" class="postbox-container">
<?php
/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'add_meta_boxes', 'comment', $comment );

/**
 * Fires when comment-specific meta boxes are added.
 *
 *
 *
 * @param GC_Comment $comment Comment object.
 */
do_action( 'add_meta_boxes_comment', $comment );

do_meta_boxes( null, 'normal', $comment );

$referer = gc_get_referer();
?>
</div>

<input type="hidden" name="c" value="<?php echo esc_attr( $comment->comment_ID ); ?>" />
<input type="hidden" name="p" value="<?php echo esc_attr( $comment->comment_post_ID ); ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />
<?php gc_original_referer_field( true, 'previous' ); ?>
<input type="hidden" name="noredir" value="1" />

</div><!-- /post-body -->
</div>
</div>
</form>

<?php if ( ! gc_is_mobile() ) : ?>
<script type="text/javascript">
try{document.post.name.focus();}catch(e){}
</script>
	<?php
endif;
