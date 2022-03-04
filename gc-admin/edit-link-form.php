<?php
/**
 * Edit links form for inclusion in administration panels.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! empty( $link_id ) ) {
	/* translators: %s: URL to Links screen. */
	$heading      = sprintf( __( '<a href="%s">链接</a> / 编辑链接' ), 'link-manager.php' );
	$submit_text  = __( '更新链接' );
	$form_name    = 'editlink';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	/* translators: %s: URL to Links screen. */
	$heading      = sprintf( __( '<a href="%s">链接</a> / 添加新链接' ), 'link-manager.php' );
	$submit_text  = __( '添加链接' );
	$form_name    = 'addlink';
	$nonce_action = 'add-bookmark';
}

require_once ABSPATH . 'gc-admin/includes/meta-boxes.php';

add_meta_box( 'linksubmitdiv', __( '保存' ), 'link_submit_meta_box', null, 'side', 'core' );
add_meta_box( 'linkcategorydiv', __( '分类' ), 'link_categories_meta_box', null, 'normal', 'core' );
add_meta_box( 'linktargetdiv', __( '打开方式' ), 'link_target_meta_box', null, 'normal', 'core' );
add_meta_box( 'linkxfndiv', __( '链接关系（XFN）' ), 'link_xfn_meta_box', null, 'normal', 'core' );
add_meta_box( 'linkadvanceddiv', __( '高级' ), 'link_advanced_meta_box', null, 'normal', 'core' );

/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'add_meta_boxes', 'link', $link );

/**
 * Fires when link-specific meta boxes are added.
 *
 *
 *
 * @param object $link Link object.
 */
do_action( 'add_meta_boxes_link', $link );

/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'normal', $link );
/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'advanced', $link );
/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'side', $link );

add_screen_option(
	'layout_columns',
	array(
		'max'     => 2,
		'default' => 2,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
		'<p>' . __( '您可以在相关模块中添加或编辑链接。只有链接的地址和名称是必填项。' ) . '</p>' .
		'<p>' . __( '链接名称、网络地址及描述是固定不动的，您可通过拖放来移动其他模块。您也可以在“显示选项”选项卡中隐藏它们，或是点击部件的标题栏来最小化。' ) . '</p>' .
		'<p>' . __( 'XFN是可选项，其含义为<a href="https://gmpg.org/xfn/">XHTML社交网络</a>。GeChiUI允许通过创建XFN属性来标识您与您的站点所链接站点作者/拥有者的关系。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Links_Add_New_Screen">创建链接文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<a href="link-add.php" class="page-title-action"><?php echo esc_html_x( '添加新链接', 'link' ); ?></a>

<hr class="gc-header-end">

<?php if ( isset( $_GET['added'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php _e( '链接已添加。' ); ?></p></div>
<?php endif; ?>

<form name="<?php echo esc_attr( $form_name ); ?>" id="<?php echo esc_attr( $form_name ); ?>" method="post" action="link.php">
<?php
if ( ! empty( $link_added ) ) {
	echo $link_added;
}

gc_nonce_field( $nonce_action );
gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
gc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>

<div id="poststuff">

<div id="post-body" class="metabox-holder columns-<?php echo ( 1 === get_current_screen()->get_columns() ) ? '1' : '2'; ?>">
<div id="post-body-content">
<div id="namediv" class="postbox">
<h2 class="postbox-header"><label for="link_name"><?php _ex( '名称', 'link name' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_name" size="30" maxlength="255" value="<?php echo esc_attr( $link->link_name ); ?>" id="link_name" />
	<p><?php _e( '例如：实用的博客软件' ); ?></p>
</div>
</div>

<div id="addressdiv" class="postbox">
<h2 class="postbox-header"><label for="link_url"><?php _e( 'Web地址' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_url" size="30" maxlength="255" class="code" value="<?php echo esc_attr( $link->link_url ); ?>" id="link_url" />
	<p><?php _e( '例如：<code>https://www.gechiui.com/</code> &#8212; 不要忘记输入<code>https://</code>' ); ?></p>
</div>
</div>

<div id="descriptiondiv" class="postbox">
<h2 class="postbox-header"><label for="link_description"><?php _e( '描述' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_description" size="30" maxlength="255" value="<?php echo isset( $link->link_description ) ? esc_attr( $link->link_description ) : ''; ?>" id="link_description" />
	<p><?php _e( '通常，当访客将鼠标光标悬停在链接表链接的上方时，它会显示出来。根据主题的不同，也可能显示在链接下方。' ); ?></p>
</div>
</div>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php

/** This action is documented in gc-admin/includes/meta-boxes.php */
do_action( 'submitlink_box' );
$side_meta_boxes = do_meta_boxes( 'link', 'side', $link );

?>
</div>
<div id="postbox-container-2" class="postbox-container">
<?php

do_meta_boxes( null, 'normal', $link );

do_meta_boxes( null, 'advanced', $link );

?>
</div>
<?php

if ( $link_id ) :
	?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id; ?>" />
<?php else : ?>
<input type="hidden" name="action" value="add" />
<?php endif; ?>

</div>
</div>

</form>
</div>
