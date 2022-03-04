<?php
/**
 * Post advanced form for inclusion in the administration panels.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @global string       $post_type
 * @global GC_Post_Type $post_type_object
 * @global GC_Post      $post             Global post object.
 */
global $post_type, $post_type_object, $post;

// Flag that we're not loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( false );

if ( is_multisite() ) {
	add_action( 'admin_footer', '_admin_notice_post_locked' );
} else {
	$check_users = get_users(
		array(
			'fields' => 'ID',
			'number' => 2,
		)
	);

	if ( count( $check_users ) > 1 ) {
		add_action( 'admin_footer', '_admin_notice_post_locked' );
	}

	unset( $check_users );
}

gc_enqueue_script( 'post' );

$_gc_editor_expand   = false;
$_content_editor_dfw = false;

if ( post_type_supports( $post_type, 'editor' )
	&& ! gc_is_mobile()
	&& ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) )
) {
	/**
	 * Filters whether to enable the 'expand' functionality in the post editor.
	 *
	 *
	 * @param bool   $expand    Whether to enable the 'expand' functionality. Default true.
	 * @param string $post_type Post type.
	 */
	if ( apply_filters( 'gc_editor_expand', true, $post_type ) ) {
		gc_enqueue_script( 'editor-expand' );
		$_content_editor_dfw = true;
		$_gc_editor_expand   = ( 'on' === get_user_setting( 'editor_expand', 'on' ) );
	}
}

if ( gc_is_mobile() ) {
	gc_enqueue_script( 'jquery-touch-punch' );
}

/**
 * Post ID global
 *
 * @name $post_ID
 * @var int
 */
$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;
$user_ID = isset( $user_ID ) ? (int) $user_ID : 0;
$action  = isset( $action ) ? $action : '';

if ( (int) get_option( 'page_for_posts' ) === $post->ID && empty( $post->post_content ) ) {
	add_action( 'edit_form_after_title', '_gc_posts_page_notice' );
	remove_post_type_support( $post_type, 'editor' );
}

$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );
if ( ! $thumbnail_support && 'attachment' === $post_type && $post->post_mime_type ) {
	if ( gc_attachment_is( 'audio', $post ) ) {
		$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
	} elseif ( gc_attachment_is( 'video', $post ) ) {
		$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
	}
}

if ( $thumbnail_support ) {
	add_thickbox();
	gc_enqueue_media( array( 'post' => $post->ID ) );
}

// Add the local autosave notice HTML.
add_action( 'admin_footer', '_local_storage_notice' );

/*
 * @todo Document the $messages array(s).
 */
$permalink = get_permalink( $post->ID );
if ( ! $permalink ) {
	$permalink = '';
}

$messages = array();

$preview_post_link_html   = '';
$scheduled_post_link_html = '';
$view_post_link_html      = '';

$preview_page_link_html   = '';
$scheduled_page_link_html = '';
$view_page_link_html      = '';

$preview_url = get_preview_post_link( $post );

$viewable = is_post_type_viewable( $post_type_object );

if ( $viewable ) {

	// Preview post link.
	$preview_post_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $preview_url ),
		__( '预览文章' )
	);

	// Scheduled post preview link.
	$scheduled_post_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '预览文章' )
	);

	// View post link.
	$view_post_link_html = sprintf(
		' <a href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '查看文章' )
	);

	// Preview page link.
	$preview_page_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $preview_url ),
		__( '预览页面' )
	);

	// Scheduled page preview link.
	$scheduled_page_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '预览页面' )
	);

	// View page link.
	$view_page_link_html = sprintf(
		' <a href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '查看页面' )
	);

}

$scheduled_date = sprintf(
	/* translators: Publish box date string. 1: Date, 2: Time. */
	__( '%1$s %2$s' ),
	/* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'Y年n月j日', 'publish box date format' ), strtotime( $post->post_date ) ),
	/* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'H:i', 'publish box time format' ), strtotime( $post->post_date ) )
);

$messages['post']       = array(
	0  => '', // Unused. Messages start at index 1.
	1  => __( '文章已更新。' ) . $view_post_link_html,
	2  => __( '已更新自定义字段。' ),
	3  => __( '已删除自定义字段。' ),
	4  => __( '文章已更新。' ),
	/* translators: %s: Date and time of the revision. */
	5  => isset( $_GET['revision'] ) ? sprintf( __( '文章已还原到在%s的版本。' ), gc_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6  => __( '文章已发布。' ) . $view_post_link_html,
	7  => __( '文章已保存。' ),
	8  => __( '文章已提交。' ) . $preview_post_link_html,
	/* translators: %s: Scheduled date for the post. */
	9  => sprintf( __( '文章已计划至：%s。' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
	10 => __( '文章草稿已更新。' ) . $preview_post_link_html,
);
$messages['page']       = array(
	0  => '', // Unused. Messages start at index 1.
	1  => __( '页面已更新。' ) . $view_page_link_html,
	2  => __( '已更新自定义字段。' ),
	3  => __( '已删除自定义字段。' ),
	4  => __( '页面已更新。' ),
	/* translators: %s: Date and time of the revision. */
	5  => isset( $_GET['revision'] ) ? sprintf( __( '页面已从%s的修订版本中还原。' ), gc_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6  => __( '页面已发布。' ) . $view_page_link_html,
	7  => __( '页面已保存。' ),
	8  => __( '页面已提交。' ) . $preview_page_link_html,
	/* translators: %s: Scheduled date for the page. */
	9  => sprintf( __( '页面已计划至：%s。' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_page_link_html,
	10 => __( '页面草稿已更新。' ) . $preview_page_link_html,
);
$messages['attachment'] = array_fill( 1, 10, __( '媒体文件已更新。' ) ); // Hack, for now.

/**
 * Filters the post updated messages.
 *
 *
 *
 * @param array[] $messages Post updated messages. For defaults see `$messages` declarations above.
 */
$messages = apply_filters( 'post_updated_messages', $messages );

$message = false;
if ( isset( $_GET['message'] ) ) {
	$_GET['message'] = absint( $_GET['message'] );
	if ( isset( $messages[ $post_type ][ $_GET['message'] ] ) ) {
		$message = $messages[ $post_type ][ $_GET['message'] ];
	} elseif ( ! isset( $messages[ $post_type ] ) && isset( $messages['post'][ $_GET['message'] ] ) ) {
		$message = $messages['post'][ $_GET['message'] ];
	}
}

$notice     = false;
$form_extra = '';
if ( 'auto-draft' === $post->post_status ) {
	if ( 'edit' === $action ) {
		$post->post_title = '';
	}
	$autosave    = false;
	$form_extra .= "<input type='hidden' id='auto_draft' name='auto_draft' value='1' />";
} else {
	$autosave = gc_get_post_autosave( $post->ID );
}

$form_action  = 'editpost';
$nonce_action = 'update-post_' . $post->ID;
$form_extra  .= "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr( $post->ID ) . "' />";

// Detect if there exists an autosave newer than the post and if that autosave is different than the post.
if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
	foreach ( _gc_post_revision_fields( $post ) as $autosave_field => $_autosave_field ) {
		if ( normalize_whitespace( $autosave->$autosave_field ) !== normalize_whitespace( $post->$autosave_field ) ) {
			$notice = sprintf(
				/* translators: %s: URL to view the autosave. */
				__( '此文章存在比以下修订版本更新的自动保存。 <a href="%s">查看自动保存</a>' ),
				get_edit_post_link( $autosave->ID )
			);
			break;
		}
	}
	// If this autosave isn't different from the current post, begone.
	if ( ! $notice ) {
		gc_delete_post_revision( $autosave->ID );
	}
	unset( $autosave_field, $_autosave_field );
}

$post_type_object = get_post_type_object( $post_type );

// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).
require_once ABSPATH . 'gc-admin/includes/meta-boxes.php';

register_and_do_post_meta_boxes( $post );

add_screen_option(
	'layout_columns',
	array(
		'max'     => 2,
		'default' => 2,
	)
);

if ( 'post' === $post_type ) {
	$customize_display = '<p>' . __( '标题区域和文章编辑区域的位置是固定的，但您可以通过拖拽功能重新排列其他所有模块。点击模块标题可以最小化或展开模块。一些模块默认隐藏，您也可以在“显示选项”中取消隐藏这些模块（摘要、发送Trackback、自定义字段、讨论、别名、作者）。您还可以在单栏布局和双栏布局中切换。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'customize-display',
			'title'   => __( '自定义显示方式' ),
			'content' => $customize_display,
		)
	);

	$title_and_editor  = '<p>' . __( '<strong>标题</strong>——为您的文章选择标题。在输入完标题后，您可以在下方看到固定链接，您也可以对此加以编辑。' ) . '</p>';
	$title_and_editor .= '<p>' . __( '<strong>文章编辑器</strong>——输入您文章的内容。有两种编辑模式：可视化和文本。通过点击对应的标签来切换模式。' ) . '</p>';
	$title_and_editor .= '<p>' . __( '可视化模式给您类似字处理工具的编辑器。点击最后一个按钮将展开第二行控制按钮。' ) . '</p>';
	$title_and_editor .= '<p>' . __( '文本模式使您可以向您的文章中加入HTML。请注意，为了让编辑器更简洁，在切换到文本模式时，&lt;p&gt;和&lt;br&gt;标签会自动被转换为换行。在您打字时，您可以使用一个换行来代替&lt;br&gt;标签，使用两个换行来代替&lt;p&gt;标签。在切换回可视化模式后，这些换行将自动被转换为对应的标签。' ) . '</p>';
	$title_and_editor .= '<p>' . __( '您可通过点击编辑器之上的按钮并遵循指引来插入媒体文件，您可以使用可视化模式中的格式化工具栏来对齐或编辑您的图片。' ) . '</p>';
	$title_and_editor .= '<p>' . __( '您可以通过右侧的图标启用免打扰写作模式。此功能在较旧的浏览器或小屏幕设备上不可用，并且需要您在屏幕选项中启用了全高度编辑器。' ) . '</p>';
	$title_and_editor .= '<p>' . sprintf(
		/* translators: %s: Alt + F10 */
		__( '致键盘用户：当您在可视化模式下编辑内容时，您可以按下%s来访问工具栏。' ),
		'<kbd>Alt + F10</kbd>'
	) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'title-post-editor',
			'title'   => __( '标题和文章编辑器' ),
			'content' => $title_and_editor,
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p>' . sprintf(
			/* translators: %s: URL to Press This bookmarklet. */
			__( '您亦可通过<a href="%s">快速发布书签</a>来创建文章。' ),
			'tools.php'
		) . '</p>' .
			'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/gechiui-editor/">撰写和编辑文章文档</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);
} elseif ( 'page' === $post_type ) {
	$about_pages = '<p>' . __( '页面和文章类似——它们都有标题、正文以及附带的相关信息；但与文章不同的是，它们类似永久的文章，而往往不像一般的博客文章那样，随着时间流逝逐渐淡出人们的视线。页面不属于任何一个分类，亦不能拥有标签，但是页面之间可以有层级关系。您可将一个页面附属在另一个“父级页面”之下，构建一个页面群组。' ) . '</p>' .
		'<p>' . __( '创建一个页面与创建一篇文章非常类似，画面亦可使用相同的方式来自定义，包括拖曳、显示选项页以及展开/折叠模块。这个画面也有免打扰写作模式，于可视化及文本方式均可通过全屏按钮调出。页面编辑器与文章编辑器大同小异，差别在于页面有它自己的属性模块。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'about-pages',
			'title'   => __( '关于页面' ),
			'content' => $about_pages,
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/pages-add-new-screen/">添加新文章文档</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/pages-screen/">管理页面文档</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);
} elseif ( 'attachment' === $post_type ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' =>
				'<p>' . __( '在此页面，您可编辑媒体库中文件的属性。' ) . '</p>' .
				'<p>' . __( '对于图片，您可点击缩略图下方的“编辑图片”，之后就会弹出一个快捷图片编辑器——您可以裁切、旋转、翻转图片。您还可以撤销或重做操作。在编辑器的右侧，您可以对图片剪裁等进行更详尽的设置。您可以点击“帮助”以了解更多。' ) . '</p>' .
				'<p>' . __( '裁切图片：请点击图片，并将裁切选区调整至您希望裁下的区域。然后点击“保存”以保存图片。' ) . '</p>' .
				'<p>' . __( '在完成后请不要忘记点击“更新媒体”。' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/edit-media/">编辑媒体文档</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
	);
}

if ( 'post' === $post_type || 'page' === $post_type ) {
	$inserting_media  = '<p>' . __( '您可以通过点击“添加媒体”按钮上传或插入多媒体文件（图片、音频、文档等）。您可以从已经上传到媒体库的文件中选择，并直接插入文章，或者上传新的文件，然后再插入。要创建相册，选择要添加的图片，并点击“创建新相册”按钮。' ) . '</p>';
	$inserting_media .= '<p>' . __( '您也可以从许多知名网站（如Twitter、YouTube、Flickr）嵌入媒体内容，只需将媒体URL粘贴在您文章或页面中的单独一行即可。<a href="https://www.gechiui.com/support/embeds/">了解更多关于嵌入的信息</a>。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'inserting-media',
			'title'   => __( '插入多媒体' ),
			'content' => $inserting_media,
		)
	);
}

if ( 'post' === $post_type ) {
	$publish_box  = '<p>' . __( '本页几个模块控制内容发布方式：' ) . '</p>';
	$publish_box .= '<ul><li>' .
		__( '<strong>发布</strong>——您可以在“发布”区域设置文章的属性。点击“状态”、“可见性”、“发布”右侧的“编辑”按钮，可以调整更多设置。可见性设置包括密码保护和文章置顶；通过设置发布选项，可实现定时发布功能。' ) .
	'</li>';

	if ( current_theme_supports( 'post-formats' ) && post_type_supports( 'post', 'post-formats' ) ) {
		$publish_box .= '<li>' . __( '<strong>形式</strong>——文章形式规定了您的主题将显示一篇文章的方式。比如，您可以有一篇“标准”的文章，其包括标题和几个段落；也可以有一篇短小的“旁白”，略去标题而只有短短一段文字。您的主题可以启用10种可用的文章形式的全部或一部分。<a href="https://www.gechiui.com/support/post-formats/#supported-formats">了解更多有关各种文章形式的信息</a>。' ) . '</li>';
	}

	if ( current_theme_supports( 'post-thumbnails' ) && post_type_supports( 'post', 'thumbnail' ) ) {
		$publish_box .= '<li>' . sprintf(
			/* translators: %s: Featured image. */
			__( '<strong>%s</strong>——这让您可以使用文章里的图片而不用重新插入它。通常您的主题在首页或自定义页首时使用特色图片功能作为缩略图时才用得到。' ),
			esc_html( $post_type_object->labels->featured_image )
		) . '</li>';
	}

	$publish_box .= '</ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'publish-box',
			'title'   => __( '发布设置' ),
			'content' => $publish_box,
		)
	);

	$discussion_settings  = '<p>' . __( '<strong>发送Trackback</strong>——Trackback是通知旧博客系统您已链接至它们的一种方式。请输入您所希望发送Trackback的URL。如果您链接到的是其他GeChiUI站点，则无须填写此栏，这些站点将自动通过Pingback方式通知。' ) . '</p>';
	$discussion_settings .= '<p>' . __( '<strong>讨论</strong>——您可以开启或关闭评论和ping功能。若该篇文章有评论，您可以在这里浏览、审核评论。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'discussion-settings',
			'title'   => __( '讨论设置' ),
			'content' => $discussion_settings,
		)
	);
} elseif ( 'page' === $post_type ) {
	$page_attributes = '<p>' . __( '<strong>父级</strong>——您可以以层级的方式组织您的页面。例如，您可以创建一个“关于”页面，它的下级有“人生”和“我的宠物”。层级深度不限。' ) . '</p>' .
		'<p>' . __( '<strong>模板</strong>——某些主题有定制的模板，您可以用在一些您想添加新功能或者自定义布局的页面上。您可以在下拉菜单中看到这些模板。' ) . '</p>' .
		'<p>' . __( '<strong>排序</strong>——页面默认按照字母表顺序进行排序。您也可以通过>为页面指定数字（1代表在最前，2代表其次……）来自定义页面的顺序。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'page-attributes',
			'title'   => __( '页面属性' ),
			'content' => $page_attributes,
		)
	);
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) {
	echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
}
?>

<hr class="gc-header-end">

<?php if ( $notice ) : ?>
<div id="notice" class="notice notice-warning"><p id="has-newer-autosave"><?php echo $notice; ?></p></div>
<?php endif; ?>
<?php if ( $message ) : ?>
<div id="message" class="updated notice notice-success is-dismissible"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<div id="lost-connection-notice" class="error hidden">
	<p><span class="spinner"></span> <?php _e( '<strong>连接丢失。</strong>保存已被禁用，直到您重新连接。' ); ?>
	<span class="hide-if-no-sessionstorage"><?php _e( '我们正在您的浏览器中备份此文章，以防不测。' ); ?></span>
	</p>
</div>
<form name="post" action="post.php" method="post" id="post"
<?php
/**
 * Fires inside the post editor form tag.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'post_edit_form_tag', $post );

$referer = gc_get_referer();
?>
>
<?php gc_nonce_field( $nonce_action ); ?>
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID; ?>" />
<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ); ?>" />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ); ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status ); ?>" />
<input type="hidden" id="referredby" name="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />
<?php if ( ! empty( $active_post_lock ) ) { ?>
<input type="hidden" id="active_post_lock" value="<?php echo esc_attr( implode( ':', $active_post_lock ) ); ?>" />
	<?php
}
if ( 'draft' !== get_post_status( $post ) ) {
	gc_original_referer_field( true, 'previous' );
}

echo $form_extra;

gc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>

<?php
/**
 * Fires at the beginning of the edit form.
 *
 * At this point, the required hidden fields and nonces have already been output.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_top', $post );
?>

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-<?php echo ( 1 === get_current_screen()->get_columns() ) ? '1' : '2'; ?>">
<div id="post-body-content">

<?php if ( post_type_supports( $post_type, 'title' ) ) { ?>
<div id="titlediv">
<div id="titlewrap">
	<?php
	/**
	 * Filters the title field placeholder text.
	 *
	 *
	 * @param string  $text Placeholder text. Default '添加标题'.
	 * @param GC_Post $post Post object.
	 */
	$title_placeholder = apply_filters( 'enter_title_here', __( '添加标题' ), $post );
	?>
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo $title_placeholder; ?></label>
	<input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" spellcheck="true" autocomplete="off" />
</div>
	<?php
	/**
	 * Fires before the permalink field in the edit form.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_form_before_permalink', $post );
	?>
<div class="inside">
	<?php
	if ( $viewable ) :
		$sample_permalink_html = $post_type_object->public ? get_sample_permalink_html( $post->ID ) : '';

		// As of 4.4, the Get Shortlink button is hidden by default.
		if ( has_filter( 'pre_get_shortlink' ) || has_filter( 'get_shortlink' ) ) {
			$shortlink = gc_get_shortlink( $post->ID, 'post' );

			if ( ! empty( $shortlink ) && $shortlink !== $permalink && home_url( '?page_id=' . $post->ID ) !== $permalink ) {
				$sample_permalink_html .= '<input id="shortlink" type="hidden" value="' . esc_attr( $shortlink ) . '" />' .
					'<button type="button" class="button button-small" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val());">' .
					__( '获取短链接地址' ) .
					'</button>';
			}
		}

		if ( $post_type_object->public
			&& ! ( 'pending' === get_post_status( $post ) && ! current_user_can( $post_type_object->cap->publish_posts ) )
		) {
			$has_sample_permalink = $sample_permalink_html && 'auto-draft' !== $post->post_status;
			?>
	<div id="edit-slug-box" class="hide-if-no-js">
			<?php
			if ( $has_sample_permalink ) {
				echo $sample_permalink_html;
			}
			?>
	</div>
			<?php
		}
endif;
	?>
</div>
	<?php
	gc_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
	?>
</div><!-- /titlediv -->
	<?php
}
/**
 * Fires after the title field.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_after_title', $post );

if ( post_type_supports( $post_type, 'editor' ) ) {
	$_gc_editor_expand_class = '';
	if ( $_gc_editor_expand ) {
		$_gc_editor_expand_class = ' gc-editor-expand';
	}
	?>
<div id="postdivrich" class="postarea<?php echo $_gc_editor_expand_class; ?>">

	<?php
	gc_editor(
		$post->post_content,
		'content',
		array(
			'_content_editor_dfw' => $_content_editor_dfw,
			'drag_drop_upload'    => true,
			'tabfocus_elements'   => 'content-html,save-post',
			'editor_height'       => 300,
			'tinymce'             => array(
				'resize'                  => false,
				'gc_autoresize_on'        => $_gc_editor_expand,
				'add_unload_trigger'      => false,
				'gc_keep_scroll_position' => ! $is_IE,
			),
		)
	);
	?>
<table id="post-status-info"><tbody><tr>
	<td id="gc-word-count" class="hide-if-no-js">
	<?php
	printf(
		/* translators: %s: Number of words. */
		__( '字数统计：%s' ),
		'<span class="word-count">0</span>'
	);
	?>
	</td>
	<td class="autosave-info">
	<span class="autosave-message">&nbsp;</span>
	<?php
	if ( 'auto-draft' !== $post->post_status ) {
		echo '<span id="last-edit">';
		$last_user = get_userdata( get_post_meta( $post->ID, '_edit_last', true ) );
		if ( $last_user ) {
			/* translators: 1: Name of most recent post author, 2: Post edited date, 3: Post edited time. */
			printf( __( '最后由%1$s编辑于%2$s%3$s' ), esc_html( $last_user->display_name ), mysql2date( __( 'Y年n月j日' ), $post->post_modified ), mysql2date( __( 'ag:i' ), $post->post_modified ) );
		} else {
			/* translators: 1: Post edited date, 2: Post edited time. */
			printf( __( '最后编辑于%1$s %2$s' ), mysql2date( __( 'Y年n月j日' ), $post->post_modified ), mysql2date( __( 'ag:i' ), $post->post_modified ) );
		}
		echo '</span>';
	}
	?>
	</td>
	<td id="content-resize-handle" class="hide-if-no-js"><br /></td>
</tr></tbody></table>

</div>
	<?php
}
/**
 * Fires after the content editor.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_after_editor', $post );
?>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php

if ( 'page' === $post_type ) {
	/**
	 * Fires before meta boxes with 'side' context are output for the 'page' post type.
	 *
	 * The submitpage box is a meta box with 'side' context, so this hook fires just before it is output.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'submitpage_box', $post );
} else {
	/**
	 * Fires before meta boxes with 'side' context are output for all post types other than 'page'.
	 *
	 * The submitpost box is a meta box with 'side' context, so this hook fires just before it is output.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'submitpost_box', $post );
}


do_meta_boxes( $post_type, 'side', $post );

?>
</div>
<div id="postbox-container-2" class="postbox-container">
<?php

do_meta_boxes( null, 'normal', $post );

if ( 'page' === $post_type ) {
	/**
	 * Fires after 'normal' context meta boxes have been output for the 'page' post type.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_page_form', $post );
} else {
	/**
	 * Fires after 'normal' context meta boxes have been output for all post types other than 'page'.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_form_advanced', $post );
}


do_meta_boxes( null, 'advanced', $post );

?>
</div>
<?php
/**
 * Fires after all meta box sections have been output, before the closing #post-body div.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'dbx_post_sidebar', $post );

?>
</div><!-- /post-body -->
<br class="clear" />
</div><!-- /poststuff -->
</form>
</div>

<?php
if ( post_type_supports( $post_type, 'comments' ) ) {
	gc_comment_reply();
}
?>

<?php if ( ! gc_is_mobile() && post_type_supports( $post_type, 'title' ) && '' === $post->post_title ) : ?>
<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
<?php endif; ?>
