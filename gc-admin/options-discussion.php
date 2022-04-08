<?php
/**
 * Discussion settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */
/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	gc_die( __( '抱歉，您不能管理此站点的选项。' ) );
}

// Used in the HTML title tag.
$title       = __( '讨论设置' );
$parent_file = 'options-general.php';

add_action( 'admin_print_footer_scripts', 'options_discussion_add_js' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '此界面提供了许多选项，包括控制评论的管理和显示，以及引用通告的显示选项等。若您需要了解此界面中每个选项的作用，请访问讨论设置文档。' ) . '</p>' .
			'<p>' . __( '调整完成后，记得点击页面下方“保存更改”按钮使设置生效。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-discussion-screen/">讨论设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form method="post" action="options.php">
<?php settings_fields( 'discussion' ); ?>

<table class="form-table" role="presentation">
<tr>
<th scope="row"><?php _e( '默认文章设置' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '默认文章设置' ); ?></span></legend>
<label for="default_pingback_flag">
<input name="default_pingback_flag" type="checkbox" id="default_pingback_flag" value="1" <?php checked( '1', get_option( 'default_pingback_flag' ) ); ?> />
<?php _e( '尝试通知文章中链接的博客' ); ?></label>
<br />
<label for="default_ping_status">
<input name="default_ping_status" type="checkbox" id="default_ping_status" value="open" <?php checked( 'open', get_option( 'default_ping_status' ) ); ?> />
<?php _e( '允许其他博客发送链接通知（Pingback和Trackback）到新文章' ); ?></label>
<br />
<label for="default_comment_status">
<input name="default_comment_status" type="checkbox" id="default_comment_status" value="open" <?php checked( 'open', get_option( 'default_comment_status' ) ); ?> />
<?php _e( '允许他人在新文章上发表评论' ); ?></label>
<br />
<p class="description"><?php _e( '个别文章可能会覆盖这些设置。此处进行的更改仅适用于新文章。' ); ?></p>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( '其他评论设置' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '其他评论设置' ); ?></span></legend>
<label for="require_name_email"><input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php checked( '1', get_option( 'require_name_email' ) ); ?> /> <?php _e( '评论者必须填入名字和电子邮箱' ); ?></label>
<br />
<label for="comment_registration">
<input name="comment_registration" type="checkbox" id="comment_registration" value="1" <?php checked( '1', get_option( 'comment_registration' ) ); ?> />
<?php _e( '用户必须注册并登录才可以发表评论' ); ?>
<?php
if ( ! get_option( 'users_can_register' ) && is_multisite() ) {
	echo ' ' . __( '（已禁止注册新用户。只有该站点的成员可以进行评论。）' );}
?>
</label>
<br />

<label for="close_comments_for_old_posts">
<input name="close_comments_for_old_posts" type="checkbox" id="close_comments_for_old_posts" value="1" <?php checked( '1', get_option( 'close_comments_for_old_posts' ) ); ?> />
<?php
printf(
	/* translators: %s: Number of days. */
	__( '自动关闭发布%s天后的文章上的评论功能' ),
	'</label> <label for="close_comments_days_old"><input name="close_comments_days_old" type="number" min="0" step="1" id="close_comments_days_old" value="' . esc_attr( get_option( 'close_comments_days_old' ) ) . '" class="small-text" />'
);
?>
</label>
<br />

<label for="show_comments_cookies_opt_in">
<input name="show_comments_cookies_opt_in" type="checkbox" id="show_comments_cookies_opt_in" value="1" <?php checked( '1', get_option( 'show_comments_cookies_opt_in' ) ); ?> />
<?php _e( '显示评论cookies复选框，允许设置评论者cookies。' ); ?>
</label>
<br />

<label for="thread_comments">
<input name="thread_comments" type="checkbox" id="thread_comments" value="1" <?php checked( '1', get_option( 'thread_comments' ) ); ?> />
<?php
/**
 * Filters the maximum depth of threaded/nested comments.
 *
 *
 *
 * @param int $max_depth The maximum depth of threaded comments. Default 10.
 */
$maxdeep = (int) apply_filters( 'thread_comments_depth_max', 10 );

$thread_comments_depth = '</label> <label for="thread_comments_depth"><select name="thread_comments_depth" id="thread_comments_depth">';
for ( $i = 2; $i <= $maxdeep; $i++ ) {
	$thread_comments_depth .= "<option value='" . esc_attr( $i ) . "'";
	if ( (int) get_option( 'thread_comments_depth' ) === $i ) {
		$thread_comments_depth .= " selected='selected'";
	}
	$thread_comments_depth .= ">$i</option>";
}
$thread_comments_depth .= '</select>';

/* translators: %s: Number of levels. */
printf( __( '启用评论嵌套，最多嵌套%s层' ), $thread_comments_depth );

?>
</label>
<br />
<label for="page_comments">
<input name="page_comments" type="checkbox" id="page_comments" value="1" <?php checked( '1', get_option( 'page_comments' ) ); ?> />
<?php
$default_comments_page = '</label> <label for="default_comments_page"><select name="default_comments_page" id="default_comments_page"><option value="newest"';
if ( 'newest' === get_option( 'default_comments_page' ) ) {
	$default_comments_page .= ' selected="selected"';
}
$default_comments_page .= '>' . __( '最后' ) . '</option><option value="oldest"';
if ( 'oldest' === get_option( 'default_comments_page' ) ) {
	$default_comments_page .= ' selected="selected"';
}
$default_comments_page .= '>' . __( '最前' ) . '</option></select>';
printf(
	/* translators: 1: Form field control for number of top level comments per page, 2: Form field control for the 'first' or 'last' page. */
	__( '分页显示评论，每页显示%1$s条评论，默认显示%2$s一页' ),
	'</label> <label for="comments_per_page"><input name="comments_per_page" type="number" step="1" min="0" id="comments_per_page" value="' . esc_attr( get_option( 'comments_per_page' ) ) . '" class="small-text" />',
	$default_comments_page
);
?>
</label>
<br />
<label for="comment_order">
<?php

$comment_order = '<select name="comment_order" id="comment_order"><option value="asc"';
if ( 'asc' === get_option( 'comment_order' ) ) {
	$comment_order .= ' selected="selected"';
}
$comment_order .= '>' . __( '旧的' ) . '</option><option value="desc"';
if ( 'desc' === get_option( 'comment_order' ) ) {
	$comment_order .= ' selected="selected"';
}
$comment_order .= '>' . __( '新的' ) . '</option></select>';

/* translators: %s: Form field control for 'older' or 'newer' comments. */
printf( __( '在每个页面顶部显示%s评论' ), $comment_order );

?>
</label>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( '发送邮件通知我' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '发送邮件通知我' ); ?></span></legend>
<label for="comments_notify">
<input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php checked( '1', get_option( 'comments_notify' ) ); ?> />
<?php _e( '有人发表评论时' ); ?> </label>
<br />
<label for="moderation_notify">
<input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php checked( '1', get_option( 'moderation_notify' ) ); ?> />
<?php _e( '有评论等待审核时' ); ?> </label>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( '在评论显示之前' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '在评论显示之前' ); ?></span></legend>
<label for="comment_moderation">
<input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php checked( '1', get_option( 'comment_moderation' ) ); ?> />
<?php _e( '评论必须经人工批准' ); ?> </label>
<br />
<label for="comment_previously_approved"><input type="checkbox" name="comment_previously_approved" id="comment_previously_approved" value="1" <?php checked( '1', get_option( 'comment_previously_approved' ) ); ?> /> <?php _e( '评论者先前须有评论通过了审核' ); ?></label>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( '评论审核' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '评论审核' ); ?></span></legend>
<p><label for="comment_max_links">
<?php
printf(
	/* translators: %s: Number of links. */
	__( '当某条评论包含超过%s个超链接时，将其放入待审队列（垃圾评论通常含有许多超链接）。' ),
	'<input name="comment_max_links" type="number" step="1" min="0" id="comment_max_links" value="' . esc_attr( get_option( 'comment_max_links' ) ) . '" class="small-text" />'
);
?>
</label></p>

<p><label for="moderation_keys"><?php _e( '当评论者的内容、名称、网址、电邮、IP或浏览器用户代理字串中包含以下关键词，这则评论将被设为<a href="edit-comments.php?comment_status=moderated">待审</a>。每行输入一个词或IP地址。WordPrss也将在单词的内部进行匹配，所以“GeChiUI”将与关键词“press”相匹配。' ); ?></label></p>
<p>
<textarea name="moderation_keys" rows="10" cols="50" id="moderation_keys" class="large-text code"><?php echo esc_textarea( get_option( 'moderation_keys' ) ); ?></textarea>
</p>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( '禁止使用的评论关键字' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '禁止使用的评论关键字' ); ?></span></legend>
<p><label for="disallowed_keys"><?php _e( '当评论者的内容、名称、网址、电邮、IP或浏览器用户代理字串中包含以下关键词，这则评论将被移入回收站。每行输入一个词或IP地址。WordPrss也将在单词的内部进行匹配，所以“GeChiUI”将与关键词“press”相匹配。' ); ?></label></p>
<p>
<textarea name="disallowed_keys" rows="10" cols="50" id="disallowed_keys" class="large-text code"><?php echo esc_textarea( get_option( 'disallowed_keys' ) ); ?></textarea>
</p>
</fieldset></td>
</tr>
<?php do_settings_fields( 'discussion', 'default' ); ?>
</table>

<h2 class="title"><?php _e( '头像' ); ?></h2>

<p><?php _e( '头像是您在各个博客见通用的图片。在每个启用了头像功能的站点上，它将显示在您的名字旁边。在这里您可以启用您站点上的读者评论头像显示功能。' ); ?></p>

<?php
// The above would be a good place to link to the documentation on the Gravatar functions, for putting it in themes. Anything like that?

$show_avatars       = get_option( 'show_avatars' );
$show_avatars_class = '';
if ( ! $show_avatars ) {
	$show_avatars_class = ' hide-if-js';
}
?>

<table class="form-table" role="presentation">
<tr>
<th scope="row"><?php _e( '头像显示' ); ?></th>
<td>
	<label for="show_avatars">
		<input type="checkbox" id="show_avatars" name="show_avatars" value="1" <?php checked( $show_avatars, 1 ); ?> />
		<?php _e( '显示头像' ); ?>
	</label>
</td>
</tr>
<tr class="avatar-settings<?php echo $show_avatars_class; ?>">
<th scope="row"><?php _e( '默认头像' ); ?></th>
<td class="defaultavatarpicker"><fieldset><legend class="screen-reader-text"><span><?php _e( '默认头像' ); ?></span></legend>

<p>
<?php _e( '如用户没有自定义头像，您可以显示一个通用标志或用他们的电子邮箱生成一个。' ); ?><br />
</p>

<?php
$avatar_defaults = array(
	'1.jpg'          => __( '神秘人士' ),
	'2.jpg'            => __( '空白' ),
	'3.jpg' => __( 'Gravatar标志' ),
	'4.jpg'        => __( '抽象图形（自动生成）' ),
	'5.jpg'          => __( 'Wavatar（自动生成）' ),
	'6.jpg'        => __( '小怪物（自动生成）' ),
	'7.jpg'            => __( '复古（自动生成）' ),
);
/**
 * Filters the default avatars.
 *
 * Avatars are stored in key/value pairs, where the key is option value,
 * and the name is the displayed avatar name.
 *
 *
 * @param string[] $avatar_defaults Associative array of default avatars.
 */
$avatar_defaults = apply_filters( 'avatar_defaults', $avatar_defaults );
$default         = get_option( 'avatar_default', 'mystery' );
$avatar_list     = '';

// Force avatars on to display these choices.
add_filter( 'pre_option_show_avatars', '__return_true', 100 );

foreach ( $avatar_defaults as $default_key => $default_name ) {
	$selected     = ( $default == $default_key ) ? 'checked="checked" ' : '';
	$avatar_list .= "\n\t<label><input type='radio' name='avatar_default' id='avatar_{$default_key}' value='" . esc_attr( $default_key ) . "' {$selected}/> ";
	$avatar_list .= get_avatar( $user_email, 32, $default_key, '', array( 'force_default' => true ) );
	$avatar_list .= ' ' . $default_name . '</label>';
	$avatar_list .= '<br />';
}

remove_filter( 'pre_option_show_avatars', '__return_true', 100 );

/**
 * Filters the HTML output of the default avatar list.
 *
 *
 * @param string $avatar_list HTML markup of the avatar list.
 */
echo apply_filters( 'default_avatar_select', $avatar_list );
?>

</fieldset></td>
</tr>
<?php do_settings_fields( 'discussion', 'avatars' ); ?>
</table>

<?php do_settings_sections( 'discussion' ); ?>

<?php submit_button(); ?>
</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
