<?php
/**
 * Media management action handler.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

$parent_file  = 'upload.php';
$submenu_file = 'upload.php';

gc_reset_vars( array( 'action' ) );

switch ( $action ) {
	case 'editattachment':
		$attachment_id = (int) $_POST['attachment_id'];
		check_admin_referer( 'media-form' );

		if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
			gc_die( __( '抱歉，您不能编辑此附件。' ) );
		}

		$errors = media_upload_form_handler();

		if ( empty( $errors ) ) {
			$location = 'media.php';
			$referer  = gc_get_original_referer();
			if ( $referer ) {
				if ( false !== strpos( $referer, 'upload.php' ) || ( url_to_postid( $referer ) === $attachment_id ) ) {
					$location = $referer;
				}
			}
			if ( false !== strpos( $location, 'upload.php' ) ) {
				$location = remove_query_arg( 'message', $location );
				$location = add_query_arg( 'posted', $attachment_id, $location );
			} elseif ( false !== strpos( $location, 'media.php' ) ) {
				$location = add_query_arg( 'message', 'updated', $location );
			}
			gc_redirect( $location );
			exit;
		}

		// No break.
	case 'edit':
		// Used in the HTML title tag.
		$title = __( '编辑媒体' );

		if ( empty( $errors ) ) {
			$errors = null;
		}

		if ( empty( $_GET['attachment_id'] ) ) {
			gc_redirect( admin_url( 'upload.php' ) );
			exit;
		}
		$att_id = (int) $_GET['attachment_id'];

		if ( ! current_user_can( 'edit_post', $att_id ) ) {
			gc_die( __( '抱歉，您不能编辑此附件。' ) );
		}

		$att = get_post( $att_id );

		if ( empty( $att->ID ) ) {
			gc_die( __( '您正在试图编辑一个不存在的附件。该文件可能已被删除？' ) );
		}
		if ( 'attachment' !== $att->post_type ) {
			gc_die( __( '您试图编辑的项目不是附件，请返回重试。' ) );
		}
		if ( 'trash' === $att->post_status ) {
			gc_die( __( '您无法编辑该附件，因为它当前在回收站中。请将其移出回收站，然后重试。' ) );
		}

		add_filter( 'attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2 );

		gc_enqueue_script( 'gc-ajax-response' );
		gc_enqueue_script( 'image-edit' );
		gc_enqueue_style( 'imgareaselect' );

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

		require_once ABSPATH . 'gc-admin/admin-header.php';

		$parent_file = 'upload.php';
		$message     = '';
		$class       = '';
		if ( isset( $_GET['message'] ) ) {
			switch ( $_GET['message'] ) {
				case 'updated':
					$message = __( '媒体文件已更新。' );
					$class   = 'updated';
					break;
			}
		}
		if ( $message ) {
			echo "<div id='message' class='$class'><p>$message</p></div>\n";
		}

		?>

	<div class="wrap">
	<h1 class="gc-heading-inline">
		<?php
		echo esc_html( $title );
		?>
</h1>

		<?php
		if ( current_user_can( 'upload_files' ) ) {
			?>
	<a href="media-new.php" class="page-title-action"><?php echo esc_html_x( '添加新文件', 'file' ); ?></a>
<?php } ?>

	<hr class="gc-header-end">

	<form method="post" class="media-upload-form" id="media-single-form">
	<p class="submit" style="padding-bottom: 0;">
		<?php submit_button( __( '更新媒体' ), 'primary', 'save', false ); ?>
	</p>

	<div class="media-single">
	<div id="media-item-<?php echo $att_id; ?>" class="media-item">
		<?php
		echo get_media_item(
			$att_id,
			array(
				'toggle'     => false,
				'send'       => false,
				'delete'     => false,
				'show_title' => false,
				'errors'     => ! empty( $errors[ $att_id ] ) ? $errors[ $att_id ] : null,
			)
		);
		?>
	</div>
	</div>

		<?php submit_button( __( '更新媒体' ), 'primary', 'save' ); ?>
	<input type="hidden" name="post_id" id="post_id" value="<?php echo isset( $post_id ) ? esc_attr( $post_id ) : ''; ?>" />
	<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr( $att_id ); ?>" />
	<input type="hidden" name="action" value="editattachment" />
		<?php gc_original_referer_field( true, 'previous' ); ?>
		<?php gc_nonce_field( 'media-form' ); ?>

	</form>

	</div>

		<?php

		require_once ABSPATH . 'gc-admin/admin-footer.php';

		exit;

	default:
		gc_redirect( admin_url( 'upload.php' ) );
		exit;

}
