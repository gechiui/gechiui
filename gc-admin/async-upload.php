<?php
/**
 * Server-side file upload handler from gc-plupload or other asynchronous upload methods.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( isset( $_REQUEST['action'] ) && 'upload-attachment' === $_REQUEST['action'] ) {
	define( 'DOING_AJAX', true );
}

if ( ! defined( 'GC_ADMIN' ) ) {
	define( 'GC_ADMIN', true );
}

if ( defined( 'ABSPATH' ) ) {
	require_once ABSPATH . 'gc-load.php';
} else {
	require_once dirname( __DIR__ ) . '/gc-load.php';
}

require_once ABSPATH . 'gc-admin/admin.php';

header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );

if ( isset( $_REQUEST['action'] ) && 'upload-attachment' === $_REQUEST['action'] ) {
	require ABSPATH . 'gc-admin/includes/ajax-actions.php';

	send_nosniff_header();
	nocache_headers();

	gc_ajax_upload_attachment();
	die( '0' );
}

if ( ! current_user_can( 'upload_files' ) ) {
	gc_die( __( '抱歉，您不能上传文件。' ) );
}

// Just fetch the detail form for that attachment.
if ( isset( $_REQUEST['attachment_id'] ) && (int) $_REQUEST['attachment_id'] && $_REQUEST['fetch'] ) {
	$id   = (int) $_REQUEST['attachment_id'];
	$post = get_post( $id );
	if ( 'attachment' !== $post->post_type ) {
		gc_die( __( '无效的文章类型。' ) );
	}

	switch ( $_REQUEST['fetch'] ) {
		case 3:
			?>
			<div class="media-item-wrapper">
				<div class="attachment-details">
					<?php
					$thumb_url = gc_get_attachment_image_src( $id, 'thumbnail', true );
					if ( $thumb_url ) {
						echo '<img class="pinkynail" src="' . esc_url( $thumb_url[0] ) . '" alt="" />';
					}

					// Title shouldn't ever be empty, but use filename just in case.
					$file     = get_attached_file( $post->ID );
					$file_url = gc_get_attachment_url( $post->ID );
					$title    = $post->post_title ? $post->post_title : gc_basename( $file );
					?>
					<div class="filename new">
						<span class="media-list-title"><strong><?php echo esc_html( gc_html_excerpt( $title, 60, '&hellip;' ) ); ?></strong></span>
						<span class="media-list-subtitle"><?php echo gc_basename( $file ); ?></span>
					</div>
				</div>
				<div class="attachment-tools">
					<span class="media-item-copy-container copy-to-clipboard-container edit-attachment">
						<button type="button" class="button button-small copy-attachment-url" data-clipboard-text="<?php echo $file_url; ?>"><?php _e( '复制网址至剪贴板' ); ?></button>
						<span class="success hidden" aria-hidden="true"><?php _e( '已复制！' ); ?></span>
					</span>
					<?php
					if ( current_user_can( 'edit_post', $id ) ) {
						echo '<a class="edit-attachment" href="' . esc_url( get_edit_post_link( $id ) ) . '">' . _x( '编辑', 'media item' ) . '</a>';
					} else {
						echo '<span class="edit-attachment">' . _x( '成功', 'media item' ) . '</span>';
					}
					?>
				</div>
			</div>
			<?php
			break;
		case 2:
			add_filter( 'attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2 );
			echo get_media_item(
				$id,
				array(
					'send'   => false,
					'delete' => true,
				)
			);
			break;
		default:
			add_filter( 'attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2 );
			echo get_media_item( $id );
			break;
	}
	exit;
}

check_admin_referer( 'media-form' );

$post_id = 0;
if ( isset( $_REQUEST['post_id'] ) ) {
	$post_id = absint( $_REQUEST['post_id'] );
	if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		$post_id = 0;
	}
}

$id = media_handle_upload( 'async-upload', $post_id );
if ( is_gc_error( $id ) ) {
	printf(
		'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
		sprintf(
			'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
			__( '不再显示' )
		),
		sprintf(
			/* translators: %s: Name of the file that failed to upload. */
			__( '“%s”上传失败。' ),
			esc_html( $_FILES['async-upload']['name'] )
		),
		esc_html( $id->get_error_message() )
	);
	exit;
}

if ( $_REQUEST['short'] ) {
	// Short form response - attachment ID only.
	echo $id;
} else {
	// Long form response - big chunk of HTML.
	$type = $_REQUEST['type'];

	/**
	 * Filters the returned ID of an uploaded attachment.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the attachment type.
	 *
	 * Possible hook names include:
	 *
	 *  - `async_upload_audio`
	 *  - `async_upload_file`
	 *  - `async_upload_image`
	 *  - `async_upload_video`
	 *
	 *
	 * @param int $id Uploaded attachment ID.
	 */
	echo apply_filters( "async_upload_{$type}", $id );
}
