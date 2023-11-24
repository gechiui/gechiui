<?php
/**
 * Manage media uploaded file.
 *
 * There are many filters in here for media. Plugins can extend functionality
 * by hooking into the filters.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'upload_files' ) ) {
	gc_die( __( '抱歉，您不能上传文件。' ) );
}

gc_enqueue_script( 'plupload-handlers' );

$post_id = 0;
if ( isset( $_REQUEST['post_id'] ) ) {
	$post_id = absint( $_REQUEST['post_id'] );
	if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		$post_id = 0;
	}
}

if ( $_POST ) {
	if ( isset( $_POST['html-upload'] ) && ! empty( $_FILES ) ) {
		check_admin_referer( 'media-form' );
		// Upload File button was clicked.
		$upload_id = media_handle_upload( 'async-upload', $post_id );
		if ( is_gc_error( $upload_id ) ) {
			gc_die( $upload_id );
		}
	}
	gc_redirect( admin_url( 'upload.php' ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( '上传新媒体文件' );
$parent_file = 'upload.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '您可以不创建文章，直接上传媒体文件。这样，您就可以日后将这些媒体文件用在文章或页面中；与此同时，您也得到了一个可用于分享的网络链接。您有如下三种上传文件方式可供选择：' ) . '</p>' .
				'<ul>' .
					'<li>' . __( '将文件<strong>拖放</strong>到下方的区域。您可以同时拖放多个文件。' ) . '</li>' .
					'<li>' . __( '点击<strong>选择文件</strong>后将弹出您操作系统的文件选择窗口。选择您需要上传的文件，单击<strong>打开</strong>按钮后，文件开始上传。进度条会自动出现，指示上传状态。' ) . '</li>' .
					'<li>' . __( '点击拖拽框下方的链接可恢复使用<strong>浏览器上传工具</strong>。' ) . '</li>' .
				'</ul>',
	)
);
get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/media-add-new-screen/">上传媒体文件文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

$form_class = 'media-upload-form type-form validate';

if ( get_user_setting( 'uploader' ) || isset( $_GET['browser-uploader'] ) ) {
	$form_class .= ' html-uploader';
}
?>
<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

	<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>" class="<?php echo esc_attr( $form_class ); ?>" id="file-form">

	<?php media_upload_form(); ?>

	<script type="text/javascript">
	var post_id = <?php echo absint( $post_id ); ?>, shortform = 3;
	</script>
	<input type="hidden" name="post_id" id="post_id" value="<?php echo absint( $post_id ); ?>" />
	<?php gc_nonce_field( 'media-form' ); ?>
	<div id="media-items" class="hide-if-no-js"></div>
	</form>
</div>

<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
