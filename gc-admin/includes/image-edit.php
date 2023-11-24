<?php
/**
 * GeChiUI Image Editor
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Loads the GC image-editing interface.
 *
 * @param int          $post_id Attachment post ID.
 * @param false|object $msg     Optional. Message to display for image editor updates or errors.
 *                              Default false.
 */
function gc_image_editor( $post_id, $msg = false ) {
	$nonce     = gc_create_nonce( "image_editor-$post_id" );
	$meta      = gc_get_attachment_metadata( $post_id );
	$thumb     = image_get_intermediate_size( $post_id, 'thumbnail' );
	$sub_sizes = isset( $meta['sizes'] ) && is_array( $meta['sizes'] );
	$note      = '';

	if ( isset( $meta['width'], $meta['height'] ) ) {
		$big = max( $meta['width'], $meta['height'] );
	} else {
		die( __( '图片数据不存在，请重新上传图片。' ) );
	}

	$sizer = $big > 600 ? 600 / $big : 1;

	$backup_sizes = get_post_meta( $post_id, '_gc_attachment_backup_sizes', true );
	$can_restore  = false;
	if ( ! empty( $backup_sizes ) && isset( $backup_sizes['full-orig'], $meta['file'] ) ) {
		$can_restore = gc_basename( $meta['file'] ) !== $backup_sizes['full-orig']['file'];
	}

	if ( $msg ) {
		if ( isset( $msg->error ) ) {
			$note = setting_error( $msg->error, 'danger', '', 'tabindex="-1" role="alert"');
		} elseif ( isset( $msg->msg ) ) {
			$note = setting_error( $msg->msg, 'success', '', 'tabindex="-1" role="alert"');
		}
	}

	/**
	 * Shows the settings in the Image Editor that allow selecting to edit only the thumbnail of an image.
	 *
	 * @since 6.3.0
	 *
	 * @param bool $show Whether to show the settings in the Image Editor. Default false.
	 */
	$edit_thumbnails_separately = (bool) apply_filters( 'image_edit_thumbnails_separately', false );

	?>
	<div class="imgedit-wrap gc-clearfix">
	<div id="imgedit-panel-<?php echo $post_id; ?>">
	<?php echo $note; ?>
	<div class="imgedit-panel-content imgedit-panel-tools gc-clearfix">
		<div class="imgedit-menu gc-clearfix">
			<button type="button" onclick="imageEdit.toggleCropTool( <?php echo "$post_id, '$nonce'"; ?>, this );" aria-expanded="false" aria-controls="imgedit-crop" class="imgedit-crop button disabled" disabled><?php esc_html_e( '裁剪' ); ?></button>
			<button type="button" class="imgedit-scale button" onclick="imageEdit.toggleControls(this);" aria-expanded="false" aria-controls="imgedit-scale"><?php esc_html_e( '缩放' ); ?></button>
			<div class="imgedit-rotate-menu-container">
				<button type="button" aria-controls="imgedit-rotate-menu" class="imgedit-rotate button" aria-expanded="false" onclick="imageEdit.togglePopup(this)"><?php esc_html_e( '图像旋转' ); ?></button>
				<div id="imgedit-rotate-menu" class="imgedit-popup-menu">
			<?php
			// On some setups GD library does not provide imagerotate() - Ticket #11536.
			if ( gc_image_editor_supports(
				array(
					'mime_type' => get_post_mime_type( $post_id ),
					'methods'   => array( 'rotate' ),
				)
			) ) {
				$note_no_rotate = '';
				?>
					<button type="button" class="imgedit-rleft button" onkeyup="imageEdit.browsePopup(this)" onclick="imageEdit.rotate( 90, <?php echo "$post_id, '$nonce'"; ?>, this)"><?php esc_html_e( '向左旋转90°' ); ?></button>
					<button type="button" class="imgedit-rright button" onkeyup="imageEdit.browsePopup(this)" onclick="imageEdit.rotate(-90, <?php echo "$post_id, '$nonce'"; ?>, this)"><?php esc_html_e( '向右旋转90°' ); ?></button>
					<button type="button" class="imgedit-rfull button" onkeyup="imageEdit.browsePopup(this)" onclick="imageEdit.rotate(180, <?php echo "$post_id, '$nonce'"; ?>, this)"><?php esc_html_e( '旋转180°' ); ?></button>
				<?php
			} else {
				$note_no_rotate = '<p class="note-no-rotate"><em>' . __( '您的服务器不支持图片旋转功能。' ) . '</em></p>';
				?>
					<button type="button" class="imgedit-rleft button disabled" disabled></button>
					<button type="button" class="imgedit-rright button disabled" disabled></button>
				<?php
			}
			?>
					<hr />
					<button type="button" onkeyup="imageEdit.browsePopup(this)" onclick="imageEdit.flip(1, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-flipv button"><?php esc_html_e( '垂直翻转' ); ?></button>
					<button type="button" onkeyup="imageEdit.browsePopup(this)" onclick="imageEdit.flip(2, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-fliph button"><?php esc_html_e( '水平翻转' ); ?></button>
					<?php echo $note_no_rotate; ?>
				</div>
			</div>
		</div>
		<div class="imgedit-submit imgedit-menu">
			<button type="button" id="image-undo-<?php echo $post_id; ?>" onclick="imageEdit.undo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-undo button disabled" disabled><?php esc_html_e( '撤销' ); ?></button>
			<button type="button" id="image-redo-<?php echo $post_id; ?>" onclick="imageEdit.redo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-redo button disabled" disabled><?php esc_html_e( '重做' ); ?></button>
			<button type="button" onclick="imageEdit.close(<?php echo $post_id; ?>, 1)" class="button imgedit-cancel-btn"><?php esc_html_e( '取消编辑' ); ?></button>
			<button type="button" onclick="imageEdit.save(<?php echo "$post_id, '$nonce'"; ?>)" disabled="disabled" class="btn btn-primary imgedit-submit-btn"><?php esc_html_e( '保存编辑' ); ?></button>
		</div>
	</div>

	<div class="imgedit-panel-content gc-clearfix">
		<div class="imgedit-tools">
			<input type="hidden" id="imgedit-nonce-<?php echo $post_id; ?>" value="<?php echo $nonce; ?>" />
			<input type="hidden" id="imgedit-sizer-<?php echo $post_id; ?>" value="<?php echo $sizer; ?>" />
			<input type="hidden" id="imgedit-history-<?php echo $post_id; ?>" value="" />
			<input type="hidden" id="imgedit-undone-<?php echo $post_id; ?>" value="0" />
			<input type="hidden" id="imgedit-selection-<?php echo $post_id; ?>" value="" />
			<input type="hidden" id="imgedit-x-<?php echo $post_id; ?>" value="<?php echo isset( $meta['width'] ) ? $meta['width'] : 0; ?>" />
			<input type="hidden" id="imgedit-y-<?php echo $post_id; ?>" value="<?php echo isset( $meta['height'] ) ? $meta['height'] : 0; ?>" />

			<div id="imgedit-crop-<?php echo $post_id; ?>" class="imgedit-crop-wrap">
			<div class="imgedit-crop-grid"></div>
			<img id="image-preview-<?php echo $post_id; ?>" onload="imageEdit.imgLoaded('<?php echo $post_id; ?>')"
				src="<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ) . '?action=imgedit-preview&amp;_ajax_nonce=' . $nonce . '&amp;postid=' . $post_id . '&amp;rand=' . rand( 1, 99999 ); ?>" alt="" />
			</div>
		</div>
		<div class="imgedit-settings">
			<div class="imgedit-tool-active">
				<div class="imgedit-group">
				<div id="imgedit-scale" tabindex="-1" class="imgedit-group-controls">
					<div class="imgedit-group-top">
						<h4><?php _e( '拉伸图片' ); ?></h4>
						<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);" aria-expanded="false"><span class="screen-reader-text">
						<?php
						/* translators: Hidden accessibility text. */
						esc_html_e( '图片缩放帮助' );
						?>
						</span></button>
						<div class="imgedit-help">
						<p><?php _e( '您可以成比例地拉伸原始图片，在诸如裁切、旋转的编辑操作之前，最好先伸缩调整好您的图片尺寸。图片仅能被缩小，不能被放大。' ); ?></p>
						</div>
						<?php if ( isset( $meta['width'], $meta['height'] ) ) : ?>
						<p>
							<?php
							printf(
								/* translators: %s: Image width and height in pixels. */
								__( '原始尺寸 %s' ),
								'<span class="imgedit-original-dimensions">' . $meta['width'] . ' &times; ' . $meta['height'] . '</span>'
							);
							?>
						</p>
						<?php endif; ?>
						<div class="imgedit-submit">
						<fieldset class="imgedit-scale-controls">
							<legend><?php _e( '新尺寸：' ); ?></legend>
							<div class="nowrap">
							<label for="imgedit-scale-width-<?php echo $post_id; ?>" class="screen-reader-text">
							<?php
							/* translators: Hidden accessibility text. */
							_e( '缩放高度' );
							?>
							</label>
							<input type="number" step="1" min="0" max="<?php echo isset( $meta['width'] ) ? $meta['width'] : ''; ?>" aria-describedby="imgedit-scale-warn-<?php echo $post_id; ?>"  id="imgedit-scale-width-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1, this)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1, this)" value="<?php echo isset( $meta['width'] ) ? $meta['width'] : 0; ?>" />
							<span class="imgedit-separator" aria-hidden="true">&times;</span>
							<label for="imgedit-scale-height-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( '缩放高度' ); ?></label>
							<input type="number" step="1" min="0" max="<?php echo isset( $meta['height'] ) ? $meta['height'] : ''; ?>" aria-describedby="imgedit-scale-warn-<?php echo $post_id; ?>" id="imgedit-scale-height-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0, this)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0, this)" value="<?php echo isset( $meta['height'] ) ? $meta['height'] : 0; ?>" />
							<button id="imgedit-scale-button" type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'scale')" class="btn btn-primary"><?php esc_html_e( '缩放' ); ?></button>
							<span class="imgedit-scale-warn" id="imgedit-scale-warn-<?php echo $post_id; ?>"><span class="dashicons dashicons-warning" aria-hidden="true"></span><?php esc_html_e( '图像无法缩放至大于原始尺寸。' ); ?></span>
							</div>
						</fieldset>
						</div>
					</div>
				</div>
			</div>

		<?php if ( $can_restore ) { ?>
				<div class="imgedit-group">
				<div class="imgedit-group-top">
					<h2><button type="button" onclick="imageEdit.toggleHelp(this);" class="button-link" aria-expanded="false"><?php _e( '恢复原始图片' ); ?> <span class="dashicons dashicons-arrow-down imgedit-help-toggle"></span></button></h2>
					<div class="imgedit-help imgedit-restore">
					<p>
					<?php
					_e( '放弃所有变更，并恢复到原始图片。' );
					if ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) {
						echo ' ' . __( '先前编辑过的图片副本不会被删除。' );
					}
					?>
					</p>
					<div class="imgedit-submit">
						<input type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'restore')" class="btn btn-primary" value="<?php esc_attr_e( '还原图片' ); ?>" <?php echo $can_restore; ?> />
					</div>
				</div>
			</div>
			</div>
		<?php } ?>
			<div class="imgedit-group">
				<div id="imgedit-crop" tabindex="-1" class="imgedit-group-controls">
				<div class="imgedit-group-top">
					<h4><?php _e( '裁剪图像' ); ?></h4>
					<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);" aria-expanded="false"><span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '图片裁切帮助' );
					?>
					</span></button>
					<div class="imgedit-help">
						<p><?php _e( '要裁切此图片，点击并拖动来确认您的选择。' ); ?></p>
						<p><strong><?php _e( '按比例裁切' ); ?></strong><br />
						<?php _e( '纵横比是宽与高之间的比值。您可以通过在修改选区时按住Shift键来固定纵横比。使用输入框来指定纵横比，如1:1（方形）、4:3、16:9等。' ); ?></p>

						<p><strong><?php _e( '裁切选区' ); ?></strong><br />
						<?php _e( '您做出选择后，选区大小可以通过输入像素值值来调整。最小值为媒体设置中指定的缩略图大小。' ); ?></p>
					</div>
				</div>
				<fieldset class="imgedit-crop-ratio">
					<legend><?php _e( '长宽比：' ); ?></legend>
					<div class="nowrap">
					<label for="imgedit-crop-width-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '裁切比例宽' );
					?>
					</label>
					<input type="number" step="1" min="1" id="imgedit-crop-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 0, this)" onblur="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 0, this)" />
					<span class="imgedit-separator" aria-hidden="true">:</span>
					<label for="imgedit-crop-height-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '裁切比例高' );
					?>
					</label>
					<input  type="number" step="1" min="0" id="imgedit-crop-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 1, this)" onblur="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 1, this)" />
					</div>
				</fieldset>
				<fieldset id="imgedit-crop-sel-<?php echo $post_id; ?>" class="imgedit-crop-sel">
					<legend><?php _e( '选区：' ); ?></legend>
					<div class="nowrap">
					<label for="imgedit-sel-width-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '选区宽度' );
					?>
					</label>
					<input  type="number" step="1" min="0" id="imgedit-sel-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" />
					<span class="imgedit-separator" aria-hidden="true">&times;</span>
					<label for="imgedit-sel-height-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '选区高度' );
					?>
					</label>
					<input  type="number" step="1" min="0" id="imgedit-sel-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" />
					</div>
				</fieldset>
				<fieldset id="imgedit-crop-sel-<?php echo $post_id; ?>" class="imgedit-crop-sel">
					<legend><?php _e( '起始坐标：' ); ?></legend>
					<div class="nowrap">
					<label for="imgedit-start-x-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '水平起始位置' );
					?>
					</label>
					<input  type="number" step="1" min="0" id="imgedit-start-x-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" value="0" />
					<span class="imgedit-separator" aria-hidden="true">&times;</span>
					<label for="imgedit-start-y-<?php echo $post_id; ?>" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '垂直起始位置' );
					?>
					</label>
					<input  type="number" step="1" min="0" id="imgedit-start-y-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" value="0" />
					</div>
				</fieldset>
				<div class="imgedit-crop-apply imgedit-menu container">
					<button class="button-primary" type="button" onclick="imageEdit.handleCropToolClick( <?php echo "$post_id, '$nonce'"; ?>, this );" class="imgedit-crop-apply button"><?php esc_html_e( '应用裁剪' ); ?></button> <button type="button" onclick="imageEdit.handleCropToolClick( <?php echo "$post_id, '$nonce'"; ?>, this );" class="imgedit-crop-clear button" disabled="disabled"><?php esc_html_e( '清除裁剪' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<?php
	if ( $edit_thumbnails_separately && $thumb && $sub_sizes ) {
		$thumb_img = gc_constrain_dimensions( $thumb['width'], $thumb['height'], 160, 120 );
		?>

	<div class="imgedit-group imgedit-applyto">
		<div class="imgedit-group-top">
			<h4><?php _e( '缩略图设置' ); ?></h4>
			<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);" aria-expanded="false"><span class="screen-reader-text">
			<?php
			/* translators: Hidden accessibility text. */
			esc_html_e( '缩略图设置帮助' );
			?>
			</span></button>
			<div class="imgedit-help">
			<p><?php _e( '您可以编辑图片，并无需影响缩略图。比如，您可能希望有一张只展示图片一部分的方形缩略图。' ); ?></p>
			</div>
		</div>
		<div class="imgedit-thumbnail-preview-group">
			<figure class="imgedit-thumbnail-preview">
				<img src="<?php echo $thumb['url']; ?>" width="<?php echo $thumb_img[0]; ?>" height="<?php echo $thumb_img[1]; ?>" class="imgedit-size-preview" alt="" draggable="false" />
				<figcaption class="imgedit-thumbnail-preview-caption"><?php _e( '当前缩略图' ); ?></figcaption>
			</figure>
			<div id="imgedit-save-target-<?php echo $post_id; ?>" class="imgedit-save-target">
			<fieldset>
				<legend><?php _e( '将更改应用于：' ); ?></legend>

				<span class="imgedit-label">
					<input type="radio" id="imgedit-target-all" name="imgedit-target-<?php echo $post_id; ?>" value="all" checked="checked" />
					<label for="imgedit-target-all"><?php _e( '所有图片大小' ); ?></label>
				</span>

				<span class="imgedit-label">
					<input type="radio" id="imgedit-target-thumbnail" name="imgedit-target-<?php echo $post_id; ?>" value="thumbnail" />
					<label for="imgedit-target-thumbnail"><?php _e( '缩略图' ); ?></label>
				</span>

				<span class="imgedit-label">
					<input type="radio" id="imgedit-target-nothumb" name="imgedit-target-<?php echo $post_id; ?>" value="nothumb" />
					<label for="imgedit-target-nothumb"><?php _e( '除缩略图外所有尺寸' ); ?></label>
				</span>

				</fieldset>
			</div>
		</div>
	</div>
	<?php } ?>
		</div>
	</div>

	</div>

	<div class="imgedit-wait" id="imgedit-wait-<?php echo $post_id; ?>"></div>
	<div class="hidden" id="imgedit-leaving-<?php echo $post_id; ?>"><?php _e( "未保存的更改将丢失。按“确定”以继续，按“取消”可返回“图片编辑器”。" ); ?></div>
	</div>
	<?php
}

/**
 * Streams image in GC_Image_Editor to browser.
 *
 * @param GC_Image_Editor $image         The image editor instance.
 * @param string          $mime_type     The mime type of the image.
 * @param int             $attachment_id The image's attachment post ID.
 * @return bool True on success, false on failure.
 */
function gc_stream_image( $image, $mime_type, $attachment_id ) {
	if ( $image instanceof GC_Image_Editor ) {

		/**
		 * Filters the GC_Image_Editor instance for the image to be streamed to the browser.
		 *
		 * @since 3.5.0
		 *
		 * @param GC_Image_Editor $image         The image editor instance.
		 * @param int             $attachment_id The attachment post ID.
		 */
		$image = apply_filters( 'image_editor_save_pre', $image, $attachment_id );

		if ( is_gc_error( $image->stream( $mime_type ) ) ) {
			return false;
		}

		return true;
	} else {
		/* translators: 1: $image, 2: GC_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s得是一个%2$s对象。' ), '$image', 'GC_Image_Editor' ) );

		/**
		 * Filters the GD image resource to be streamed to the browser.
		 *
		 * @deprecated 3.5.0 Use {@see 'image_editor_save_pre'} instead.
		 *
		 * @param resource|GdImage $image         Image resource to be streamed.
		 * @param int              $attachment_id The attachment post ID.
		 */
		$image = apply_filters_deprecated( 'image_save_pre', array( $image, $attachment_id ), '3.5.0', 'image_editor_save_pre' );

		switch ( $mime_type ) {
			case 'image/jpeg':
				header( 'Content-Type: image/jpeg' );
				return imagejpeg( $image, null, 90 );
			case 'image/png':
				header( 'Content-Type: image/png' );
				return imagepng( $image );
			case 'image/gif':
				header( 'Content-Type: image/gif' );
				return imagegif( $image );
			case 'image/webp':
				if ( function_exists( 'imagewebp' ) ) {
					header( 'Content-Type: image/webp' );
					return imagewebp( $image, null, 90 );
				}
				return false;
			default:
				return false;
		}
	}
}

/**
 * Saves image to file.
 * The `$image` parameter expects a `GC_Image_Editor` instance.
 * @since 6.0.0 The `$filesize` value was added to the returned array.
 *
 * @param string          $filename  Name of the file to be saved.
 * @param GC_Image_Editor $image     The image editor instance.
 * @param string          $mime_type The mime type of the image.
 * @param int             $post_id   Attachment post ID.
 * @return array|GC_Error|bool {
 *     Array on success or GC_Error if the file failed to save.
 *     When called with a deprecated value for the `$image` parameter,
 *     i.e. a non-`GC_Image_Editor` image resource or `GdImage` instance,
 *     the function will return true on success, false on failure.
 *
 *     @type string $path      Path to the image file.
 *     @type string $file      Name of the image file.
 *     @type int    $width     Image width.
 *     @type int    $height    Image height.
 *     @type string $mime-type The mime type of the image.
 *     @type int    $filesize  File size of the image.
 * }
 */
function gc_save_image_file( $filename, $image, $mime_type, $post_id ) {
	if ( $image instanceof GC_Image_Editor ) {

		/** This filter is documented in gc-admin/includes/image-edit.php */
		$image = apply_filters( 'image_editor_save_pre', $image, $post_id );

		/**
		 * Filters whether to skip saving the image file.
		 *
		 * Returning a non-null value will short-circuit the save method,
		 * returning that value instead.
		 *
		 * @since 3.5.0
		 *
		 * @param bool|null       $override  Value to return instead of saving. Default null.
		 * @param string          $filename  Name of the file to be saved.
		 * @param GC_Image_Editor $image     The image editor instance.
		 * @param string          $mime_type The mime type of the image.
		 * @param int             $post_id   Attachment post ID.
		 */
		$saved = apply_filters( 'gc_save_image_editor_file', null, $filename, $image, $mime_type, $post_id );

		if ( null !== $saved ) {
			return $saved;
		}

		return $image->save( $filename, $mime_type );
	} else {
		/* translators: 1: $image, 2: GC_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s得是一个%2$s对象。' ), '$image', 'GC_Image_Editor' ) );

		/** This filter is documented in gc-admin/includes/image-edit.php */
		$image = apply_filters_deprecated( 'image_save_pre', array( $image, $post_id ), '3.5.0', 'image_editor_save_pre' );

		/**
		 * Filters whether to skip saving the image file.
		 *
		 * Returning a non-null value will short-circuit the save method,
		 * returning that value instead.
		 *
		 * @deprecated 3.5.0 Use {@see 'gc_save_image_editor_file'} instead.
		 *
		 * @param bool|null        $override  Value to return instead of saving. Default null.
		 * @param string           $filename  Name of the file to be saved.
		 * @param resource|GdImage $image     Image resource or GdImage instance.
		 * @param string           $mime_type The mime type of the image.
		 * @param int              $post_id   Attachment post ID.
		 */
		$saved = apply_filters_deprecated(
			'gc_save_image_file',
			array( null, $filename, $image, $mime_type, $post_id ),
			'3.5.0',
			'gc_save_image_editor_file'
		);

		if ( null !== $saved ) {
			return $saved;
		}

		switch ( $mime_type ) {
			case 'image/jpeg':
				/** This filter is documented in gc-includes/class-gc-image-editor.php */
				return imagejpeg( $image, $filename, apply_filters( 'jpeg_quality', 90, 'edit_image' ) );
			case 'image/png':
				return imagepng( $image, $filename );
			case 'image/gif':
				return imagegif( $image, $filename );
			case 'image/webp':
				if ( function_exists( 'imagewebp' ) ) {
					return imagewebp( $image, $filename );
				}
				return false;
			default:
				return false;
		}
	}
}

/**
 * Image preview ratio. Internal use only.
 *
 * @ignore
 * @param int $w Image width in pixels.
 * @param int $h Image height in pixels.
 * @return float|int Image preview ratio.
 */
function _image_get_preview_ratio( $w, $h ) {
	$max = max( $w, $h );
	return $max > 600 ? ( 600 / $max ) : 1;
}

/**
 * Returns an image resource. Internal use only.
 *
 * @deprecated 3.5.0 Use GC_Image_Editor::rotate()
 * @see GC_Image_Editor::rotate()
 *
 * @ignore
 * @param resource|GdImage  $img   Image resource.
 * @param float|int         $angle Image rotation angle, in degrees.
 * @return resource|GdImage|false GD image resource or GdImage instance, false otherwise.
 */
function _rotate_image_resource( $img, $angle ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'GC_Image_Editor::rotate()' );

	if ( function_exists( 'imagerotate' ) ) {
		$rotated = imagerotate( $img, $angle, 0 );

		if ( is_gd_image( $rotated ) ) {
			imagedestroy( $img );
			$img = $rotated;
		}
	}

	return $img;
}

/**
 * Flips an image resource. Internal use only.
 *
 * @deprecated 3.5.0 Use GC_Image_Editor::flip()
 * @see GC_Image_Editor::flip()
 *
 * @ignore
 * @param resource|GdImage $img  Image resource or GdImage instance.
 * @param bool             $horz Whether to flip horizontally.
 * @param bool             $vert Whether to flip vertically.
 * @return resource|GdImage (maybe) flipped image resource or GdImage instance.
 */
function _flip_image_resource( $img, $horz, $vert ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'GC_Image_Editor::flip()' );

	$w   = imagesx( $img );
	$h   = imagesy( $img );
	$dst = gc_imagecreatetruecolor( $w, $h );

	if ( is_gd_image( $dst ) ) {
		$sx = $vert ? ( $w - 1 ) : 0;
		$sy = $horz ? ( $h - 1 ) : 0;
		$sw = $vert ? -$w : $w;
		$sh = $horz ? -$h : $h;

		if ( imagecopyresampled( $dst, $img, 0, 0, $sx, $sy, $w, $h, $sw, $sh ) ) {
			imagedestroy( $img );
			$img = $dst;
		}
	}

	return $img;
}

/**
 * Crops an image resource. Internal use only.
 *
 * @ignore
 * @param resource|GdImage $img Image resource or GdImage instance.
 * @param float            $x   Source point x-coordinate.
 * @param float            $y   Source point y-coordinate.
 * @param float            $w   Source width.
 * @param float            $h   Source height.
 * @return resource|GdImage (maybe) cropped image resource or GdImage instance.
 */
function _crop_image_resource( $img, $x, $y, $w, $h ) {
	$dst = gc_imagecreatetruecolor( $w, $h );

	if ( is_gd_image( $dst ) ) {
		if ( imagecopy( $dst, $img, 0, 0, $x, $y, $w, $h ) ) {
			imagedestroy( $img );
			$img = $dst;
		}
	}

	return $img;
}

/**
 * Performs group of changes on Editor specified.
 *
 * @param GC_Image_Editor $image   GC_Image_Editor instance.
 * @param array           $changes Array of change operations.
 * @return GC_Image_Editor GC_Image_Editor instance with changes applied.
 */
function image_edit_apply_changes( $image, $changes ) {
	if ( is_gd_image( $image ) ) {
		/* translators: 1: $image, 2: GC_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s得是一个%2$s对象。' ), '$image', 'GC_Image_Editor' ) );
	}

	if ( ! is_array( $changes ) ) {
		return $image;
	}

	// Expand change operations.
	foreach ( $changes as $key => $obj ) {
		if ( isset( $obj->r ) ) {
			$obj->type  = 'rotate';
			$obj->angle = $obj->r;
			unset( $obj->r );
		} elseif ( isset( $obj->f ) ) {
			$obj->type = 'flip';
			$obj->axis = $obj->f;
			unset( $obj->f );
		} elseif ( isset( $obj->c ) ) {
			$obj->type = 'crop';
			$obj->sel  = $obj->c;
			unset( $obj->c );
		}
		$changes[ $key ] = $obj;
	}

	// Combine operations.
	if ( count( $changes ) > 1 ) {
		$filtered = array( $changes[0] );
		for ( $i = 0, $j = 1, $c = count( $changes ); $j < $c; $j++ ) {
			$combined = false;
			if ( $filtered[ $i ]->type == $changes[ $j ]->type ) {
				switch ( $filtered[ $i ]->type ) {
					case 'rotate':
						$filtered[ $i ]->angle += $changes[ $j ]->angle;
						$combined               = true;
						break;
					case 'flip':
						$filtered[ $i ]->axis ^= $changes[ $j ]->axis;
						$combined              = true;
						break;
				}
			}
			if ( ! $combined ) {
				$filtered[ ++$i ] = $changes[ $j ];
			}
		}
		$changes = $filtered;
		unset( $filtered );
	}

	// Image resource before applying the changes.
	if ( $image instanceof GC_Image_Editor ) {

		/**
		 * Filters the GC_Image_Editor instance before applying changes to the image.
		 *
		 * @since 3.5.0
		 *
		 * @param GC_Image_Editor $image   GC_Image_Editor instance.
		 * @param array           $changes Array of change operations.
		 */
		$image = apply_filters( 'gc_image_editor_before_change', $image, $changes );
	} elseif ( is_gd_image( $image ) ) {

		/**
		 * Filters the GD image resource before applying changes to the image.
		 *
		 * @deprecated 3.5.0 Use {@see 'gc_image_editor_before_change'} instead.
		 *
		 * @param resource|GdImage $image   GD image resource or GdImage instance.
		 * @param array            $changes Array of change operations.
		 */
		$image = apply_filters_deprecated( 'image_edit_before_change', array( $image, $changes ), '3.5.0', 'gc_image_editor_before_change' );
	}

	foreach ( $changes as $operation ) {
		switch ( $operation->type ) {
			case 'rotate':
				if ( 0 != $operation->angle ) {
					if ( $image instanceof GC_Image_Editor ) {
						$image->rotate( $operation->angle );
					} else {
						$image = _rotate_image_resource( $image, $operation->angle );
					}
				}
				break;
			case 'flip':
				if ( 0 != $operation->axis ) {
					if ( $image instanceof GC_Image_Editor ) {
						$image->flip( ( $operation->axis & 1 ) != 0, ( $operation->axis & 2 ) != 0 );
					} else {
						$image = _flip_image_resource( $image, ( $operation->axis & 1 ) != 0, ( $operation->axis & 2 ) != 0 );
					}
				}
				break;
			case 'crop':
				$sel = $operation->sel;

				if ( $image instanceof GC_Image_Editor ) {
					$size = $image->get_size();
					$w    = $size['width'];
					$h    = $size['height'];

					$scale = 1 / _image_get_preview_ratio( $w, $h ); // Discard preview scaling.
					$image->crop( $sel->x * $scale, $sel->y * $scale, $sel->w * $scale, $sel->h * $scale );
				} else {
					$scale = 1 / _image_get_preview_ratio( imagesx( $image ), imagesy( $image ) ); // Discard preview scaling.
					$image = _crop_image_resource( $image, $sel->x * $scale, $sel->y * $scale, $sel->w * $scale, $sel->h * $scale );
				}
				break;
		}
	}

	return $image;
}


/**
 * Streams image in post to browser, along with enqueued changes
 * in `$_REQUEST['history']`.
 *
 * @param int $post_id Attachment post ID.
 * @return bool True on success, false on failure.
 */
function stream_preview_image( $post_id ) {
	$post = get_post( $post_id );

	gc_raise_memory_limit( 'admin' );

	$img = gc_get_image_editor( _load_image_to_edit_path( $post_id ) );

	if ( is_gc_error( $img ) ) {
		return false;
	}

	$changes = ! empty( $_REQUEST['history'] ) ? json_decode( gc_unslash( $_REQUEST['history'] ) ) : null;
	if ( $changes ) {
		$img = image_edit_apply_changes( $img, $changes );
	}

	// Scale the image.
	$size = $img->get_size();
	$w    = $size['width'];
	$h    = $size['height'];

	$ratio = _image_get_preview_ratio( $w, $h );
	$w2    = max( 1, $w * $ratio );
	$h2    = max( 1, $h * $ratio );

	if ( is_gc_error( $img->resize( $w2, $h2 ) ) ) {
		return false;
	}

	return gc_stream_image( $img, $post->post_mime_type, $post_id );
}

/**
 * Restores the metadata for a given attachment.
 *
 * @param int $post_id Attachment post ID.
 * @return stdClass Image restoration message object.
 */
function gc_restore_image( $post_id ) {
	$meta             = gc_get_attachment_metadata( $post_id );
	$file             = get_attached_file( $post_id );
	$backup_sizes     = get_post_meta( $post_id, '_gc_attachment_backup_sizes', true );
	$old_backup_sizes = $backup_sizes;
	$restored         = false;
	$msg              = new stdClass();

	if ( ! is_array( $backup_sizes ) ) {
		$msg->error = __( '无法载入图片属性。' );
		return $msg;
	}

	$parts         = pathinfo( $file );
	$suffix        = time() . rand( 100, 999 );
	$default_sizes = get_intermediate_image_sizes();

	if ( isset( $backup_sizes['full-orig'] ) && is_array( $backup_sizes['full-orig'] ) ) {
		$data = $backup_sizes['full-orig'];

		if ( $parts['basename'] != $data['file'] ) {
			if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE ) {

				// Delete only if it's an edited image.
				if ( preg_match( '/-e[0-9]{13}\./', $parts['basename'] ) ) {
					gc_delete_file( $file );
				}
			} elseif ( isset( $meta['width'], $meta['height'] ) ) {
				$backup_sizes[ "full-$suffix" ] = array(
					'width'  => $meta['width'],
					'height' => $meta['height'],
					'file'   => $parts['basename'],
				);
			}
		}

		$restored_file = path_join( $parts['dirname'], $data['file'] );
		$restored      = update_attached_file( $post_id, $restored_file );

		$meta['file']   = _gc_relative_upload_path( $restored_file );
		$meta['width']  = $data['width'];
		$meta['height'] = $data['height'];
	}

	foreach ( $default_sizes as $default_size ) {
		if ( isset( $backup_sizes[ "$default_size-orig" ] ) ) {
			$data = $backup_sizes[ "$default_size-orig" ];
			if ( isset( $meta['sizes'][ $default_size ] ) && $meta['sizes'][ $default_size ]['file'] != $data['file'] ) {
				if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE ) {

					// Delete only if it's an edited image.
					if ( preg_match( '/-e[0-9]{13}-/', $meta['sizes'][ $default_size ]['file'] ) ) {
						$delete_file = path_join( $parts['dirname'], $meta['sizes'][ $default_size ]['file'] );
						gc_delete_file( $delete_file );
					}
				} else {
					$backup_sizes[ "$default_size-{$suffix}" ] = $meta['sizes'][ $default_size ];
				}
			}

			$meta['sizes'][ $default_size ] = $data;
		} else {
			unset( $meta['sizes'][ $default_size ] );
		}
	}

	if ( ! gc_update_attachment_metadata( $post_id, $meta ) ||
		( $old_backup_sizes !== $backup_sizes && ! update_post_meta( $post_id, '_gc_attachment_backup_sizes', $backup_sizes ) ) ) {

		$msg->error = __( '无法保存图片属性。' );
		return $msg;
	}

	if ( ! $restored ) {
		$msg->error = __( '图片属性不正确。' );
	} else {
		$msg->msg = __( '图片还原成功。' );
		if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE ) {
			delete_post_meta( $post_id, '_gc_attachment_backup_sizes' );
		}
	}

	return $msg;
}

/**
 * Saves image to post, along with enqueued changes
 * in `$_REQUEST['history']`.
 *
 * @param int $post_id Attachment post ID.
 * @return stdClass
 */
function gc_save_image( $post_id ) {
	$_gc_additional_image_sizes = gc_get_additional_image_sizes();

	$return  = new stdClass();
	$success = false;
	$delete  = false;
	$scaled  = false;
	$nocrop  = false;
	$post    = get_post( $post_id );

	$img = gc_get_image_editor( _load_image_to_edit_path( $post_id, 'full' ) );
	if ( is_gc_error( $img ) ) {
		$return->error = esc_js( __( '无法创建新图片。' ) );
		return $return;
	}

	$fwidth  = ! empty( $_REQUEST['fwidth'] ) ? (int) $_REQUEST['fwidth'] : 0;
	$fheight = ! empty( $_REQUEST['fheight'] ) ? (int) $_REQUEST['fheight'] : 0;
	$target  = ! empty( $_REQUEST['target'] ) ? preg_replace( '/[^a-z0-9_-]+/i', '', $_REQUEST['target'] ) : '';
	$scale   = ! empty( $_REQUEST['do'] ) && 'scale' === $_REQUEST['do'];

	/** This filter is documented in gc-admin/includes/image-edit.php */
	$edit_thumbnails_separately = (bool) apply_filters( 'image_edit_thumbnails_separately', false );

	if ( $scale ) {
		$size = $img->get_size();
		$sX   = $size['width'];
		$sY   = $size['height'];

		if ( $sX < $fwidth || $sY < $fheight ) {
			$return->error = esc_js( __( '图像无法缩放至大于原始尺寸。' ) );
			return $return;
		}

		if ( $fwidth > 0 && $fheight > 0 ) {
			// Check if it has roughly the same w / h ratio.
			$diff = round( $sX / $sY, 2 ) - round( $fwidth / $fheight, 2 );
			if ( -0.1 < $diff && $diff < 0.1 ) {
				// Scale the full size image.
				if ( $img->resize( $fwidth, $fheight ) ) {
					$scaled = true;
				}
			}

			if ( ! $scaled ) {
				$return->error = esc_js( __( '保存缩放后的图片时发生错误，请刷新后再试。' ) );
				return $return;
			}
		}
	} elseif ( ! empty( $_REQUEST['history'] ) ) {
		$changes = json_decode( gc_unslash( $_REQUEST['history'] ) );
		if ( $changes ) {
			$img = image_edit_apply_changes( $img, $changes );
		}
	} else {
		$return->error = esc_js( __( '无需保存，图片未被更改。' ) );
		return $return;
	}

	$meta         = gc_get_attachment_metadata( $post_id );
	$backup_sizes = get_post_meta( $post->ID, '_gc_attachment_backup_sizes', true );

	if ( ! is_array( $meta ) ) {
		$return->error = esc_js( __( '图片数据不存在，请重新上传图片。' ) );
		return $return;
	}

	if ( ! is_array( $backup_sizes ) ) {
		$backup_sizes = array();
	}

	// Generate new filename.
	$path = get_attached_file( $post_id );

	$basename = pathinfo( $path, PATHINFO_BASENAME );
	$dirname  = pathinfo( $path, PATHINFO_DIRNAME );
	$ext      = pathinfo( $path, PATHINFO_EXTENSION );
	$filename = pathinfo( $path, PATHINFO_FILENAME );
	$suffix   = time() . rand( 100, 999 );

	if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE &&
		isset( $backup_sizes['full-orig'] ) && $backup_sizes['full-orig']['file'] != $basename ) {

		if ( $edit_thumbnails_separately && 'thumbnail' === $target ) {
			$new_path = "{$dirname}/{$filename}-temp.{$ext}";
		} else {
			$new_path = $path;
		}
	} else {
		while ( true ) {
			$filename     = preg_replace( '/-e([0-9]+)$/', '', $filename );
			$filename    .= "-e{$suffix}";
			$new_filename = "{$filename}.{$ext}";
			$new_path     = "{$dirname}/$new_filename";
			if ( file_exists( $new_path ) ) {
				$suffix++;
			} else {
				break;
			}
		}
	}

	// Save the full-size file, also needed to create sub-sizes.
	if ( ! gc_save_image_file( $new_path, $img, $post->post_mime_type, $post_id ) ) {
		$return->error = esc_js( __( '无法保存图片。' ) );
		return $return;
	}

	if ( 'nothumb' === $target || 'all' === $target || 'full' === $target || $scaled ) {
		$tag = false;
		if ( isset( $backup_sizes['full-orig'] ) ) {
			if ( ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) && $backup_sizes['full-orig']['file'] !== $basename ) {
				$tag = "full-$suffix";
			}
		} else {
			$tag = 'full-orig';
		}

		if ( $tag ) {
			$backup_sizes[ $tag ] = array(
				'width'  => $meta['width'],
				'height' => $meta['height'],
				'file'   => $basename,
			);
		}
		$success = ( $path === $new_path ) || update_attached_file( $post_id, $new_path );

		$meta['file'] = _gc_relative_upload_path( $new_path );

		$size           = $img->get_size();
		$meta['width']  = $size['width'];
		$meta['height'] = $size['height'];

		if ( $success && ( 'nothumb' === $target || 'all' === $target ) ) {
			$sizes = get_intermediate_image_sizes();

			if ( $edit_thumbnails_separately && 'nothumb' === $target ) {
				$sizes = array_diff( $sizes, array( 'thumbnail' ) );
			}
		}

		$return->fw = $meta['width'];
		$return->fh = $meta['height'];
	} elseif ( $edit_thumbnails_separately && 'thumbnail' === $target ) {
		$sizes   = array( 'thumbnail' );
		$success = true;
		$delete  = true;
		$nocrop  = true;
	}

	/*
	 * We need to remove any existing resized image files because
	 * a new crop or rotate could generate different sizes (and hence, filenames),
	 * keeping the new resized images from overwriting the existing image files.
	 * https://core.trac.gechiui.com/ticket/32171
	 */
	if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE && ! empty( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size ) {
			if ( ! empty( $size['file'] ) && preg_match( '/-e[0-9]{13}-/', $size['file'] ) ) {
				$delete_file = path_join( $dirname, $size['file'] );
				gc_delete_file( $delete_file );
			}
		}
	}

	if ( isset( $sizes ) ) {
		$_sizes = array();

		foreach ( $sizes as $size ) {
			$tag = false;
			if ( isset( $meta['sizes'][ $size ] ) ) {
				if ( isset( $backup_sizes[ "$size-orig" ] ) ) {
					if ( ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) && $backup_sizes[ "$size-orig" ]['file'] != $meta['sizes'][ $size ]['file'] ) {
						$tag = "$size-$suffix";
					}
				} else {
					$tag = "$size-orig";
				}

				if ( $tag ) {
					$backup_sizes[ $tag ] = $meta['sizes'][ $size ];
				}
			}

			if ( isset( $_gc_additional_image_sizes[ $size ] ) ) {
				$width  = (int) $_gc_additional_image_sizes[ $size ]['width'];
				$height = (int) $_gc_additional_image_sizes[ $size ]['height'];
				$crop   = ( $nocrop ) ? false : $_gc_additional_image_sizes[ $size ]['crop'];
			} else {
				$height = get_option( "{$size}_size_h" );
				$width  = get_option( "{$size}_size_w" );
				$crop   = ( $nocrop ) ? false : get_option( "{$size}_crop" );
			}

			$_sizes[ $size ] = array(
				'width'  => $width,
				'height' => $height,
				'crop'   => $crop,
			);
		}

		$meta['sizes'] = array_merge( $meta['sizes'], $img->multi_resize( $_sizes ) );
	}

	unset( $img );

	if ( $success ) {
		gc_update_attachment_metadata( $post_id, $meta );
		update_post_meta( $post_id, '_gc_attachment_backup_sizes', $backup_sizes );

		if ( 'thumbnail' === $target || 'all' === $target || 'full' === $target ) {
			// Check if it's an image edit from attachment edit screen.
			if ( ! empty( $_REQUEST['context'] ) && 'edit-attachment' === $_REQUEST['context'] ) {
				$thumb_url         = gc_get_attachment_image_src( $post_id, array( 900, 600 ), true );
				$return->thumbnail = $thumb_url[0];
			} else {
				$file_url = gc_get_attachment_url( $post_id );
				if ( ! empty( $meta['sizes']['thumbnail'] ) ) {
					$thumb             = $meta['sizes']['thumbnail'];
					$return->thumbnail = path_join( dirname( $file_url ), $thumb['file'] );
				} else {
					$return->thumbnail = "$file_url?w=128&h=128";
				}
			}
		}
	} else {
		$delete = true;
	}

	if ( $delete ) {
		gc_delete_file( $new_path );
	}

	$return->msg = esc_js( __( '图片已保存' ) );
	return $return;
}
