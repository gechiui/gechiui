<?php
/**
 * GeChiUI media templates.
 *
 * @package GeChiUI
 * @subpackage Media
 *
 */

/**
 * Output the markup for a audio tag to be used in an Underscore template
 * when data.model is passed.
 *
 *
 */
function gc_underscore_audio_template() {
	$audio_types = gc_get_audio_extensions();
	?>
<audio style="visibility: hidden"
	controls
	class="gc-audio-shortcode"
	width="{{ _.isUndefined( data.model.width ) ? 400 : data.model.width }}"
	preload="{{ _.isUndefined( data.model.preload ) ? 'none' : data.model.preload }}"
	<#
	<?php
	foreach ( array( 'autoplay', 'loop' ) as $attr ) :
		?>
	if ( ! _.isUndefined( data.model.<?php echo $attr; ?> ) && data.model.<?php echo $attr; ?> ) {
		#> <?php echo $attr; ?><#
	}
	<?php endforeach; ?>#>
>
	<# if ( ! _.isEmpty( data.model.src ) ) { #>
	<source src="{{ data.model.src }}" type="{{ gc.media.view.settings.embedMimes[ data.model.src.split('.').pop() ] }}" />
	<# } #>

	<?php
	foreach ( $audio_types as $type ) :
		?>
	<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) { #>
	<source src="{{ data.model.<?php echo $type; ?> }}" type="{{ gc.media.view.settings.embedMimes[ '<?php echo $type; ?>' ] }}" />
	<# } #>
		<?php
	endforeach;
	?>
</audio>
	<?php
}

/**
 * Output the markup for a video tag to be used in an Underscore template
 * when data.model is passed.
 *
 *
 */
function gc_underscore_video_template() {
	$video_types = gc_get_video_extensions();
	?>
<#  var w_rule = '', classes = [],
		w, h, settings = gc.media.view.settings,
		isYouTube = isVimeo = false;

	if ( ! _.isEmpty( data.model.src ) ) {
		isYouTube = data.model.src.match(/youtube|youtu\.be/);
		isVimeo = -1 !== data.model.src.indexOf('vimeo');
	}

	if ( settings.contentWidth && data.model.width >= settings.contentWidth ) {
		w = settings.contentWidth;
	} else {
		w = data.model.width;
	}

	if ( w !== data.model.width ) {
		h = Math.ceil( ( data.model.height * w ) / data.model.width );
	} else {
		h = data.model.height;
	}

	if ( w ) {
		w_rule = 'width: ' + w + 'px; ';
	}

	if ( isYouTube ) {
		classes.push( 'youtube-video' );
	}

	if ( isVimeo ) {
		classes.push( 'vimeo-video' );
	}

#>
<div style="{{ w_rule }}" class="gc-video">
<video controls
	class="gc-video-shortcode {{ classes.join( ' ' ) }}"
	<# if ( w ) { #>width="{{ w }}"<# } #>
	<# if ( h ) { #>height="{{ h }}"<# } #>
	<?php
	$props = array(
		'poster'  => '',
		'preload' => 'metadata',
	);
	foreach ( $props as $key => $value ) :
		if ( empty( $value ) ) {
			?>
		<#
		if ( ! _.isUndefined( data.model.<?php echo $key; ?> ) && data.model.<?php echo $key; ?> ) {
			#> <?php echo $key; ?>="{{ data.model.<?php echo $key; ?> }}"<#
		} #>
			<?php
		} else {
			echo $key
			?>
			="{{ _.isUndefined( data.model.<?php echo $key; ?> ) ? '<?php echo $value; ?>' : data.model.<?php echo $key; ?> }}"
			<?php
		}
	endforeach;
	?>
	<#
	<?php
	foreach ( array( 'autoplay', 'loop' ) as $attr ) :
		?>
	if ( ! _.isUndefined( data.model.<?php echo $attr; ?> ) && data.model.<?php echo $attr; ?> ) {
		#> <?php echo $attr; ?><#
	}
	<?php endforeach; ?>#>
>
	<# if ( ! _.isEmpty( data.model.src ) ) {
		if ( isYouTube ) { #>
		<source src="{{ data.model.src }}" type="video/youtube" />
		<# } else if ( isVimeo ) { #>
		<source src="{{ data.model.src }}" type="video/vimeo" />
		<# } else { #>
		<source src="{{ data.model.src }}" type="{{ settings.embedMimes[ data.model.src.split('.').pop() ] }}" />
		<# }
	} #>

	<?php
	foreach ( $video_types as $type ) :
		?>
	<# if ( data.model.<?php echo $type; ?> ) { #>
	<source src="{{ data.model.<?php echo $type; ?> }}" type="{{ settings.embedMimes[ '<?php echo $type; ?>' ] }}" />
	<# } #>
	<?php endforeach; ?>
	{{{ data.model.content }}}
</video>
</div>
	<?php
}

/**
 * Prints the templates used in the media manager.
 *
 *
 */
function gc_print_media_templates() {
	$class = 'media-modal gc-core-ui';

	$alt_text_description = sprintf(
		/* translators: 1: Link to tutorial, 2: Additional link attributes, 3: Accessibility text. */
		__( '<a href="%1$s" %2$s>描述此图片的用途%3$s</a>。如此图片仅作装饰用，请留空。' ),
		esc_url( 'https://www.w3.org/WAI/tutorials/images/decision-tree' ),
		'target="_blank" rel="noopener"',
		sprintf(
			'<span class="screen-reader-text"> %s</span>',
			/* translators: Accessibility text. */
			__( '（在新窗口中打开）' )
		)
	);
	?>

	<?php // Template for the media frame: used both in the media grid and in the media modal. ?>
	<script type="text/html" id="tmpl-media-frame">
		<div class="media-frame-title" id="media-frame-title"></div>
		<h2 class="media-frame-menu-heading"><?php _ex( '操作', 'media modal menu actions' ); ?></h2>
		<button type="button" class="button button-link media-frame-menu-toggle" aria-expanded="false">
			<?php _ex( '菜单', 'media modal menu' ); ?>
			<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span>
		</button>
		<div class="media-frame-menu"></div>
		<div class="media-frame-tab-panel">
			<div class="media-frame-router"></div>
			<div class="media-frame-content"></div>
		</div>
		<h2 class="media-frame-actions-heading screen-reader-text">
		<?php
			/* translators: Accessibility text. */
			_e( '选择媒体动作' );
		?>
		</h2>
		<div class="media-frame-toolbar"></div>
		<div class="media-frame-uploader"></div>
	</script>

	<?php // Template for the media modal. ?>
	<script type="text/html" id="tmpl-media-modal">
		<div tabindex="0" class="<?php echo $class; ?>" role="dialog" aria-labelledby="media-frame-title">
			<# if ( data.hasCloseButton ) { #>
				<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text"><?php _e( '关闭对话框' ); ?></span></span></button>
			<# } #>
			<div class="media-modal-content" role="document"></div>
		</div>
		<div class="media-modal-backdrop"></div>
	</script>

	<?php // Template for the window uploader, used for example in the media grid. ?>
	<script type="text/html" id="tmpl-uploader-window">
		<div class="uploader-window-content">
			<div class="uploader-editor-title"><?php _e( '拖文件至此可上传' ); ?></div>
		</div>
	</script>

	<?php // Template for the editor uploader. ?>
	<script type="text/html" id="tmpl-uploader-editor">
		<div class="uploader-editor-content">
			<div class="uploader-editor-title"><?php _e( '拖文件至此可上传' ); ?></div>
		</div>
	</script>

	<?php // Template for the inline uploader, used for example in the Media Library admin page - Add New. ?>
	<script type="text/html" id="tmpl-uploader-inline">
		<# var messageClass = data.message ? 'has-upload-message' : 'no-upload-message'; #>
		<# if ( data.canClose ) { #>
		<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php _e( '关闭上传器' ); ?></span></button>
		<# } #>
		<div class="uploader-inline-content {{ messageClass }}">
		<# if ( data.message ) { #>
			<h2 class="upload-message">{{ data.message }}</h2>
		<# } #>
		<?php if ( ! _device_can_upload() ) : ?>
			<div class="upload-ui">
				<h2 class="upload-instructions"><?php _e( '您的浏览器不能上传文件' ); ?></h2>
				<p>
				<?php
					printf(
						/* translators: %s: https://apps.gechiui.com/ */
						__( '您设备上的网页浏览器不能上传文件。但是您可以通过<a href="%s">适合您设备的原生app</a>来上传文件。' ),
						'https://apps.gechiui.com/'
					);
				?>
				</p>
			</div>
		<?php elseif ( is_multisite() && ! is_upload_space_available() ) : ?>
			<div class="upload-ui">
				<h2 class="upload-instructions"><?php _e( '已达上传限制' ); ?></h2>
				<?php
				/** This action is documented in gc-admin/includes/media.php */
				do_action( 'upload_ui_over_quota' );
				?>
			</div>
		<?php else : ?>
			<div class="upload-ui">
				<h2 class="upload-instructions drop-instructions"><?php _e( '拖文件至此可上传' ); ?></h2>
				<p class="upload-instructions drop-instructions"><?php _ex( '或', 'Uploader: Drop files here - or - Select Files' ); ?></p>
				<button type="button" class="browser button button-hero" aria-labelledby="post-upload-info"><?php _e( '选择文件' ); ?></button>
			</div>

			<div class="upload-inline-status"></div>

			<div class="post-upload-ui" id="post-upload-info">
				<?php
				/** This action is documented in gc-admin/includes/media.php */
				do_action( 'pre-upload-ui' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
				/** This action is documented in gc-admin/includes/media.php */
				do_action( 'pre-plupload-upload-ui' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

				if ( 10 === remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' ) ) {
					/** This action is documented in gc-admin/includes/media.php */
					do_action( 'post-plupload-upload-ui' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
					add_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
				} else {
					/** This action is documented in gc-admin/includes/media.php */
					do_action( 'post-plupload-upload-ui' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
				}

				$max_upload_size = gc_max_upload_size();
				if ( ! $max_upload_size ) {
					$max_upload_size = 0;
				}
				?>

				<p class="max-upload-size">
				<?php
					printf(
						/* translators: %s: Maximum allowed file size. */
						__( '最大上传文件大小：%s。' ),
						esc_html( size_format( $max_upload_size ) )
					);
				?>
				</p>

				<# if ( data.suggestedWidth && data.suggestedHeight ) { #>
					<p class="suggested-dimensions">
						<?php
							/* translators: 1: Suggested width number, 2: Suggested height number. */
							printf( __( '建议的图片尺寸：%1$s x %2$s像素。' ), '{{data.suggestedWidth}}', '{{data.suggestedHeight}}' );
						?>
					</p>
				<# } #>

				<?php
				/** This action is documented in gc-admin/includes/media.php */
				do_action( 'post-upload-ui' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
				?>
			</div>
		<?php endif; ?>
		</div>
	</script>

	<?php // Template for the view switchers, used for example in the Media Grid. ?>
	<script type="text/html" id="tmpl-media-library-view-switcher">
		<a href="<?php echo esc_url( add_query_arg( 'mode', 'list', admin_url( 'upload.php' ) ) ); ?>" class="view-list">
			<span class="screen-reader-text"><?php _e( '列表视图' ); ?></span>
		</a>
		<a href="<?php echo esc_url( add_query_arg( 'mode', 'grid', admin_url( 'upload.php' ) ) ); ?>" class="view-grid current" aria-current="page">
			<span class="screen-reader-text"><?php _e( '网格视图' ); ?></span>
		</a>
	</script>

	<?php // Template for the uploading status UI. ?>
	<script type="text/html" id="tmpl-uploader-status">
		<h2><?php _e( '正上传' ); ?></h2>

		<div class="media-progress-bar"><div></div></div>
		<div class="upload-details">
			<span class="upload-count">
				<span class="upload-index"></span> / <span class="upload-total"></span>
			</span>
			<span class="upload-detail-separator">&ndash;</span>
			<span class="upload-filename"></span>
		</div>
		<div class="upload-errors"></div>
		<button type="button" class="button upload-dismiss-errors"><?php _e( '忽略错误' ); ?></button>
	</script>

	<?php // Template for the uploading status errors. ?>
	<script type="text/html" id="tmpl-uploader-status-error">
		<span class="upload-error-filename">{{{ data.filename }}}</span>
		<span class="upload-error-message">{{ data.message }}</span>
	</script>

	<?php // Template for the Attachment Details layout in the media browser. ?>
	<script type="text/html" id="tmpl-edit-attachment-frame">
		<div class="edit-media-header">
			<button class="left dashicons"<# if ( ! data.hasPrevious ) { #> disabled<# } #>><span class="screen-reader-text"><?php _e( '编辑上一媒体项目' ); ?></span></button>
			<button class="right dashicons"<# if ( ! data.hasNext ) { #> disabled<# } #>><span class="screen-reader-text"><?php _e( '编辑下一媒体项目' ); ?></span></button>
			<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text"><?php _e( '关闭对话框' ); ?></span></span></button>
		</div>
		<div class="media-frame-title"></div>
		<div class="media-frame-content"></div>
	</script>

	<?php // Template for the Attachment Details two columns layout. ?>
	<script type="text/html" id="tmpl-attachment-details-two-column">
		<div class="attachment-media-view {{ data.orientation }}">
			<h2 class="screen-reader-text"><?php _e( '附件预览' ); ?></h2>
			<div class="thumbnail thumbnail-{{ data.type }}">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div></div></div>
				<# } else if ( data.sizes && data.sizes.large ) { #>
					<img class="details-image" src="{{ data.sizes.large.url }}" draggable="false" alt="" />
				<# } else if ( data.sizes && data.sizes.full ) { #>
					<img class="details-image" src="{{ data.sizes.full.url }}" draggable="false" alt="" />
				<# } else if ( -1 === jQuery.inArray( data.type, [ 'audio', 'video' ] ) ) { #>
					<img class="details-image icon" src="{{ data.icon }}" draggable="false" alt="" />
				<# } #>

				<# if ( 'audio' === data.type ) { #>
				<div class="gc-media-wrapper gc-audio">
					<audio style="visibility: hidden" controls class="gc-audio-shortcode" width="100%" preload="none">
						<source type="{{ data.mime }}" src="{{ data.url }}" />
					</audio>
				</div>
				<# } else if ( 'video' === data.type ) {
					var w_rule = '';
					if ( data.width ) {
						w_rule = 'width: ' + data.width + 'px;';
					} else if ( gc.media.view.settings.contentWidth ) {
						w_rule = 'width: ' + gc.media.view.settings.contentWidth + 'px;';
					}
				#>
				<div style="{{ w_rule }}" class="gc-media-wrapper gc-video">
					<video controls="controls" class="gc-video-shortcode" preload="metadata"
						<# if ( data.width ) { #>width="{{ data.width }}"<# } #>
						<# if ( data.height ) { #>height="{{ data.height }}"<# } #>
						<# if ( data.image && data.image.src !== data.icon ) { #>poster="{{ data.image.src }}"<# } #>>
						<source type="{{ data.mime }}" src="{{ data.url }}" />
					</video>
				</div>
				<# } #>

				<div class="attachment-actions">
					<# if ( 'image' === data.type && ! data.uploading && data.sizes && data.can.save ) { #>
					<button type="button" class="button edit-attachment"><?php _e( '编辑图片' ); ?></button>
					<# } else if ( 'pdf' === data.subtype && data.sizes ) { #>
					<p><?php _e( '文档预览' ); ?></p>
					<# } #>
				</div>
			</div>
		</div>
		<div class="attachment-info">
			<span class="settings-save-status" role="status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e( '已保存。' ); ?></span>
			</span>
			<div class="details">
				<h2 class="screen-reader-text"><?php _e( '详情' ); ?></h2>
				<div class="uploaded"><strong><?php _e( '上传于：' ); ?></strong> {{ data.dateFormatted }}</div>
				<div class="uploaded-by">
					<strong><?php _e( '上传者：' ); ?></strong>
						<# if ( data.authorLink ) { #>
							<a href="{{ data.authorLink }}">{{ data.authorName }}</a>
						<# } else { #>
							{{ data.authorName }}
						<# } #>
				</div>
				<# if ( data.uploadedToTitle ) { #>
					<div class="uploaded-to">
						<strong><?php _e( '上传至：' ); ?></strong>
						<# if ( data.uploadedToLink ) { #>
							<a href="{{ data.uploadedToLink }}">{{ data.uploadedToTitle }}</a>
						<# } else { #>
							{{ data.uploadedToTitle }}
						<# } #>
					</div>
				<# } #>
				<div class="filename"><strong><?php _e( '文件名：' ); ?></strong> {{ data.filename }}</div>
				<div class="file-type"><strong><?php _e( '文件类型：' ); ?></strong> {{ data.mime }}</div>
				<div class="file-size"><strong><?php _e( '文件大小：' ); ?></strong> {{ data.filesizeHumanReadable }}</div>
				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<# if ( data.width && data.height ) { #>
						<div class="dimensions"><strong><?php _e( '分辨率：' ); ?></strong>
							<?php
							/* translators: 1: A number of pixels wide, 2: A number of pixels tall. */
							printf( __( '%1$s×%2$s像素' ), '{{ data.width }}', '{{ data.height }}' );
							?>
						</div>
					<# } #>

					<# if ( data.originalImageURL && data.originalImageName ) { #>
						<?php _e( '原始图片：' ); ?>
						<a href="{{ data.originalImageURL }}">{{data.originalImageName}}</a>
					<# } #>
				<# } #>

				<# if ( data.fileLength && data.fileLengthHumanReadable ) { #>
					<div class="file-length"><strong><?php _e( '长度：' ); ?></strong>
						<span aria-hidden="true">{{ data.fileLength }}</span>
						<span class="screen-reader-text">{{ data.fileLengthHumanReadable }}</span>
					</div>
				<# } #>

				<# if ( 'audio' === data.type && data.meta.bitrate ) { #>
					<div class="bitrate">
						<strong><?php _e( '比特率：' ); ?></strong> {{ Math.round( data.meta.bitrate / 1000 ) }}kb/s
						<# if ( data.meta.bitrate_mode ) { #>
						{{ ' ' + data.meta.bitrate_mode.toUpperCase() }}
						<# } #>
					</div>
				<# } #>

				<# if ( data.mediaStates ) { #>
					<div class="media-states"><strong><?php _e( '使用于：' ); ?></strong> {{ data.mediaStates }}</div>
				<# } #>

				<div class="compat-meta">
					<# if ( data.compat && data.compat.meta ) { #>
						{{{ data.compat.meta }}}
					<# } #>
				</div>
			</div>

			<div class="settings">
				<# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
				<# if ( 'image' === data.type ) { #>
					<span class="setting has-description" data-setting="alt">
						<label for="attachment-details-two-column-alt-text" class="name"><?php _e( '替代文本' ); ?></label>
						<input type="text" id="attachment-details-two-column-alt-text" value="{{ data.alt }}" aria-describedby="alt-text-description" {{ maybeReadOnly }} />
					</span>
					<p class="描述" id="alt-text-description"><?php echo $alt_text_description; ?></p>
				<# } #>
				<?php if ( post_type_supports( 'attachment', 'title' ) ) : ?>
				<span class="setting" data-setting="title">
					<label for="attachment-details-two-column-title" class="name"><?php _e( '标题' ); ?></label>
					<input type="text" id="attachment-details-two-column-title" value="{{ data.title }}" {{ maybeReadOnly }} />
				</span>
				<?php endif; ?>
				<# if ( 'audio' === data.type ) { #>
				<?php
				foreach ( array(
					'artist' => __( '艺术家' ),
					'album'  => __( '相册' ),
				) as $key => $label ) :
					?>
				<span class="setting" data-setting="<?php echo esc_attr( $key ); ?>">
					<label for="attachment-details-two-column-<?php echo esc_attr( $key ); ?>" class="name"><?php echo $label; ?></label>
					<input type="text" id="attachment-details-two-column-<?php echo esc_attr( $key ); ?>" value="{{ data.<?php echo $key; ?> || data.meta.<?php echo $key; ?> || '' }}" />
				</span>
				<?php endforeach; ?>
				<# } #>
				<span class="setting" data-setting="caption">
					<label for="attachment-details-two-column-caption" class="name"><?php _e( '说明文字' ); ?></label>
					<textarea id="attachment-details-two-column-caption" {{ maybeReadOnly }}>{{ data.caption }}</textarea>
				</span>
				<span class="setting" data-setting="描述">
					<label for="attachment-details-two-column-description" class="name"><?php _e( '描述' ); ?></label>
					<textarea id="attachment-details-two-column-description" {{ maybeReadOnly }}>{{ data.description }}</textarea>
				</span>
				<span class="setting" data-setting="url">
					<label for="attachment-details-two-column-copy-link" class="name"><?php _e( '文件URL：' ); ?></label>
					<input type="text" class="attachment-details-copy-link" id="attachment-details-two-column-copy-link" value="{{ data.url }}" readonly />
					<span class="copy-to-clipboard-container">
						<button type="button" class="button button-small copy-attachment-url" data-clipboard-target="#attachment-details-two-column-copy-link"><?php _e( '复制网址至剪贴板' ); ?></button>
						<span class="success hidden" aria-hidden="true"><?php _e( '已复制！' ); ?></span>
					</span>
				</span>
				<div class="attachment-compat"></div>
			</div>

			<div class="actions">
				<# if ( data.link ) { #>
					<a class="view-attachment" href="{{ data.link }}"><?php _e( '查看附件页面' ); ?></a>
				<# } #>
				<# if ( data.can.save ) { #>
					<# if ( data.link ) { #>
						<span class="links-separator">|</span>
					<# } #>
					<a href="{{ data.editLink }}"><?php _e( '编辑详细信息' ); ?></a>
				<# } #>
				<# if ( ! data.uploading && data.can.remove ) { #>
					<# if ( data.link || data.can.save ) { #>
						<span class="links-separator">|</span>
					<# } #>
					<?php if ( MEDIA_TRASH ) : ?>
						<# if ( 'trash' === data.status ) { #>
							<button type="button" class="button-link untrash-attachment"><?php _e( '从回收站中恢复' ); ?></button>
						<# } else { #>
							<button type="button" class="button-link trash-attachment"><?php _e( '移动至回收站' ); ?></button>
						<# } #>
					<?php else : ?>
						<button type="button" class="button-link delete-attachment"><?php _e( '永久删除' ); ?></button>
					<?php endif; ?>
				<# } #>
			</div>
		</div>
	</script>

	<?php // Template for the Attachment "thumbnails" in the Media Grid. ?>
	<script type="text/html" id="tmpl-attachment">
		<div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
			<div class="thumbnail">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
				<# } else if ( 'image' === data.type && data.size && data.size.url ) { #>
					<div class="centered">
						<img src="{{ data.size.url }}" draggable="false" alt="" />
					</div>
				<# } else { #>
					<div class="centered">
						<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
							<img src="{{ data.image.src }}" class="thumbnail" draggable="false" alt="" />
						<# } else if ( data.sizes && data.sizes.medium ) { #>
							<img src="{{ data.sizes.medium.url }}" class="thumbnail" draggable="false" alt="" />
						<# } else { #>
							<img src="{{ data.icon }}" class="icon" draggable="false" alt="" />
						<# } #>
					</div>
					<div class="filename">
						<div>{{ data.filename }}</div>
					</div>
				<# } #>
			</div>
			<# if ( data.buttons.close ) { #>
				<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text"><?php _e( '移除' ); ?></span></button>
			<# } #>
		</div>
		<# if ( data.buttons.check ) { #>
			<button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text"><?php _e( '取消选择' ); ?></span></button>
		<# } #>
		<#
		var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
		if ( data.describe ) {
			if ( 'image' === data.type ) { #>
				<input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
					aria-label="<?php esc_attr_e( '说明文字' ); ?>"
					placeholder="<?php esc_attr_e( '说明文字…' ); ?>" {{ maybeReadOnly }} />
			<# } else { #>
				<input type="text" value="{{ data.title }}" class="describe" data-setting="title"
					<# if ( 'video' === data.type ) { #>
						aria-label="<?php esc_attr_e( '视频标题' ); ?>"
						placeholder="<?php esc_attr_e( '视频标题…' ); ?>"
					<# } else if ( 'audio' === data.type ) { #>
						aria-label="<?php esc_attr_e( '音频标题' ); ?>"
						placeholder="<?php esc_attr_e( '音频标题…' ); ?>"
					<# } else { #>
						aria-label="<?php esc_attr_e( '媒体标题' ); ?>"
						placeholder="<?php esc_attr_e( '媒体标题…' ); ?>"
					<# } #> {{ maybeReadOnly }} />
			<# }
		} #>
	</script>

	<?php // Template for the Attachment details, used for example in the sidebar. ?>
	<script type="text/html" id="tmpl-attachment-details">
		<h2>
			<?php _e( '附件详情' ); ?>
			<span class="settings-save-status" role="status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e( '已保存。' ); ?></span>
			</span>
		</h2>
		<div class="attachment-info">

			<# if ( 'audio' === data.type ) { #>
				<div class="gc-media-wrapper gc-audio">
					<audio style="visibility: hidden" controls class="gc-audio-shortcode" width="100%" preload="none">
						<source type="{{ data.mime }}" src="{{ data.url }}" />
					</audio>
				</div>
			<# } else if ( 'video' === data.type ) {
				var w_rule = '';
				if ( data.width ) {
					w_rule = 'width: ' + data.width + 'px;';
				} else if ( gc.media.view.settings.contentWidth ) {
					w_rule = 'width: ' + gc.media.view.settings.contentWidth + 'px;';
				}
			#>
				<div style="{{ w_rule }}" class="gc-media-wrapper gc-video">
					<video controls="controls" class="gc-video-shortcode" preload="metadata"
						<# if ( data.width ) { #>width="{{ data.width }}"<# } #>
						<# if ( data.height ) { #>height="{{ data.height }}"<# } #>
						<# if ( data.image && data.image.src !== data.icon ) { #>poster="{{ data.image.src }}"<# } #>>
						<source type="{{ data.mime }}" src="{{ data.url }}" />
					</video>
				</div>
			<# } else { #>
				<div class="thumbnail thumbnail-{{ data.type }}">
					<# if ( data.uploading ) { #>
						<div class="media-progress-bar"><div></div></div>
					<# } else if ( 'image' === data.type && data.size && data.size.url ) { #>
						<img src="{{ data.size.url }}" draggable="false" alt="" />
					<# } else { #>
						<img src="{{ data.icon }}" class="icon" draggable="false" alt="" />
					<# } #>
				</div>
			<# } #>

			<div class="details">
				<div class="filename">{{ data.filename }}</div>
				<div class="uploaded">{{ data.dateFormatted }}</div>

				<div class="file-size">{{ data.filesizeHumanReadable }}</div>
				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<# if ( data.width && data.height ) { #>
						<div class="dimensions">
							<?php
							/* translators: 1: A number of pixels wide, 2: A number of pixels tall. */
							printf( __( '%1$s×%2$s像素' ), '{{ data.width }}', '{{ data.height }}' );
							?>
						</div>
					<# } #>

					<# if ( data.originalImageURL && data.originalImageName ) { #>
						<?php _e( '原始图片：' ); ?>
						<a href="{{ data.originalImageURL }}">{{data.originalImageName}}</a>
					<# } #>

					<# if ( data.can.save && data.sizes ) { #>
						<a class="edit-attachment" href="{{ data.editLink }}&amp;image-editor" target="_blank"><?php _e( '编辑图片' ); ?></a>
					<# } #>
				<# } #>

				<# if ( data.fileLength && data.fileLengthHumanReadable ) { #>
					<div class="file-length"><?php _e( '长度：' ); ?>
						<span aria-hidden="true">{{ data.fileLength }}</span>
						<span class="screen-reader-text">{{ data.fileLengthHumanReadable }}</span>
					</div>
				<# } #>

				<# if ( data.mediaStates ) { #>
					<div class="media-states"><strong><?php _e( '使用于：' ); ?></strong> {{ data.mediaStates }}</div>
				<# } #>

				<# if ( ! data.uploading && data.can.remove ) { #>
					<?php if ( MEDIA_TRASH ) : ?>
					<# if ( 'trash' === data.status ) { #>
						<button type="button" class="button-link untrash-attachment"><?php _e( '从回收站中恢复' ); ?></button>
					<# } else { #>
						<button type="button" class="button-link trash-attachment"><?php _e( '移动至回收站' ); ?></button>
					<# } #>
					<?php else : ?>
						<button type="button" class="button-link delete-attachment"><?php _e( '永久删除' ); ?></button>
					<?php endif; ?>
				<# } #>

				<div class="compat-meta">
					<# if ( data.compat && data.compat.meta ) { #>
						{{{ data.compat.meta }}}
					<# } #>
				</div>
			</div>
		</div>
		<# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
		<# if ( 'image' === data.type ) { #>
			<span class="setting has-description" data-setting="alt">
				<label for="attachment-details-alt-text" class="name"><?php _e( '替代文本' ); ?></label>
				<input type="text" id="attachment-details-alt-text" value="{{ data.alt }}" aria-describedby="alt-text-description" {{ maybeReadOnly }} />
			</span>
			<p class="描述" id="alt-text-description"><?php echo $alt_text_description; ?></p>
		<# } #>
		<?php if ( post_type_supports( 'attachment', 'title' ) ) : ?>
		<span class="setting" data-setting="title">
			<label for="attachment-details-title" class="name"><?php _e( '标题' ); ?></label>
			<input type="text" id="attachment-details-title" value="{{ data.title }}" {{ maybeReadOnly }} />
		</span>
		<?php endif; ?>
		<# if ( 'audio' === data.type ) { #>
		<?php
		foreach ( array(
			'artist' => __( '艺术家' ),
			'album'  => __( '相册' ),
		) as $key => $label ) :
			?>
		<span class="setting" data-setting="<?php echo esc_attr( $key ); ?>">
			<label for="attachment-details-<?php echo esc_attr( $key ); ?>" class="name"><?php echo $label; ?></label>
			<input type="text" id="attachment-details-<?php echo esc_attr( $key ); ?>" value="{{ data.<?php echo $key; ?> || data.meta.<?php echo $key; ?> || '' }}" />
		</span>
		<?php endforeach; ?>
		<# } #>
		<span class="setting" data-setting="caption">
			<label for="attachment-details-caption" class="name"><?php _e( '说明文字' ); ?></label>
			<textarea id="attachment-details-caption" {{ maybeReadOnly }}>{{ data.caption }}</textarea>
		</span>
		<span class="setting" data-setting="描述">
			<label for="attachment-details-description" class="name"><?php _e( '描述' ); ?></label>
			<textarea id="attachment-details-description" {{ maybeReadOnly }}>{{ data.description }}</textarea>
		</span>
		<span class="setting" data-setting="url">
			<label for="attachment-details-copy-link" class="name"><?php _e( '文件URL：' ); ?></label>
			<input type="text" class="attachment-details-copy-link" id="attachment-details-copy-link" value="{{ data.url }}" readonly />
			<div class="copy-to-clipboard-container">
				<button type="button" class="button button-small copy-attachment-url" data-clipboard-target="#attachment-details-copy-link"><?php _e( '复制网址至剪贴板' ); ?></button>
				<span class="success hidden" aria-hidden="true"><?php _e( '已复制！' ); ?></span>
			</div>
		</span>
	</script>

	<?php // Template for the Selection status bar. ?>
	<script type="text/html" id="tmpl-media-selection">
		<div class="selection-info">
			<span class="count"></span>
			<# if ( data.editable ) { #>
				<button type="button" class="button-link edit-selection"><?php _e( '编辑所选' ); ?></button>
			<# } #>
			<# if ( data.clearable ) { #>
				<button type="button" class="button-link clear-selection"><?php _e( '清除' ); ?></button>
			<# } #>
		</div>
		<div class="selection-view"></div>
	</script>

	<?php // Template for the Attachment display settings, used for example in the sidebar. ?>
	<script type="text/html" id="tmpl-attachment-display-settings">
		<h2><?php _e( '附件显示设置' ); ?></h2>

		<# if ( 'image' === data.type ) { #>
			<span class="setting align">
				<label for="attachment-display-settings-alignment" class="name"><?php _e( '对齐方式' ); ?></label>
				<select id="attachment-display-settings-alignment" class="alignment"
					data-setting="align"
					<# if ( data.userSettings ) { #>
						data-user-setting="align"
					<# } #>>

					<option value="left">
						<?php esc_html_e( '左' ); ?>
					</option>
					<option value="center">
						<?php esc_html_e( '中' ); ?>
					</option>
					<option value="right">
						<?php esc_html_e( '右' ); ?>
					</option>
					<option value="none" selected>
						<?php esc_html_e( '无' ); ?>
					</option>
				</select>
			</span>
		<# } #>

		<span class="setting">
			<label for="attachment-display-settings-link-to" class="name">
				<# if ( data.model.canEmbed ) { #>
					<?php _e( '嵌入或链接' ); ?>
				<# } else { #>
					<?php _e( '链接到' ); ?>
				<# } #>
			</label>
			<select id="attachment-display-settings-link-to" class="link-to"
				data-setting="link"
				<# if ( data.userSettings && ! data.model.canEmbed ) { #>
					data-user-setting="urlbutton"
				<# } #>>

			<# if ( data.model.canEmbed ) { #>
				<option value="embed" selected>
					<?php esc_html_e( '内嵌媒体播放器' ); ?>
				</option>
				<option value="file">
			<# } else { #>
				<option value="none" selected>
					<?php esc_html_e( '无' ); ?>
				</option>
				<option value="file">
			<# } #>
				<# if ( data.model.canEmbed ) { #>
					<?php esc_html_e( '链接到媒体文件' ); ?>
				<# } else { #>
					<?php esc_html_e( '媒体文件' ); ?>
				<# } #>
				</option>
				<option value="post">
				<# if ( data.model.canEmbed ) { #>
					<?php esc_html_e( '链接到附件页面' ); ?>
				<# } else { #>
					<?php esc_html_e( '附件页面' ); ?>
				<# } #>
				</option>
			<# if ( 'image' === data.type ) { #>
				<option value="custom">
					<?php esc_html_e( '自定义URL' ); ?>
				</option>
			<# } #>
			</select>
		</span>
		<span class="setting">
			<label for="attachment-display-settings-link-to-custom" class="name"><?php _e( 'URL' ); ?></label>
			<input type="text" id="attachment-display-settings-link-to-custom" class="link-to-custom" data-setting="linkUrl" />
		</span>

		<# if ( 'undefined' !== typeof data.sizes ) { #>
			<span class="setting">
				<label for="attachment-display-settings-size" class="name"><?php _e( '尺寸' ); ?></label>
				<select id="attachment-display-settings-size" class="size" name="size"
					data-setting="size"
					<# if ( data.userSettings ) { #>
						data-user-setting="imgsize"
					<# } #>>
					<?php
					/** This filter is documented in gc-admin/includes/media.php */
					$sizes = apply_filters(
						'image_size_names_choose',
						array(
							'thumbnail' => __( '缩略图' ),
							'medium'    => __( '中等' ),
							'large'     => __( '大' ),
							'full'      => __( '全尺寸' ),
						)
					);

					foreach ( $sizes as $value => $name ) :
						?>
						<#
						var size = data.sizes['<?php echo esc_js( $value ); ?>'];
						if ( size ) { #>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'full' ); ?>>
								<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
							</option>
						<# } #>
					<?php endforeach; ?>
				</select>
			</span>
		<# } #>
	</script>

	<?php // Template for the Gallery settings, used for example in the sidebar. ?>
	<script type="text/html" id="tmpl-gallery-settings">
		<h2><?php _e( '相册设置' ); ?></h2>

		<span class="setting">
			<label for="gallery-settings-link-to" class="name"><?php _e( '链接到' ); ?></label>
			<select id="gallery-settings-link-to" class="link-to"
				data-setting="link"
				<# if ( data.userSettings ) { #>
					data-user-setting="urlbutton"
				<# } #>>

				<option value="post" <# if ( ! gc.media.galleryDefaults.link || 'post' === gc.media.galleryDefaults.link ) {
					#>selected="selected"<# }
				#>>
					<?php esc_html_e( '附件页面' ); ?>
				</option>
				<option value="file" <# if ( 'file' === gc.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
					<?php esc_html_e( '媒体文件' ); ?>
				</option>
				<option value="none" <# if ( 'none' === gc.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
					<?php esc_html_e( '无' ); ?>
				</option>
			</select>
		</span>

		<span class="setting">
			<label for="gallery-settings-columns" class="name select-label-inline"><?php _e( '栏目' ); ?></label>
			<select id="gallery-settings-columns" class="columns" name="columns"
				data-setting="columns">
				<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <#
						if ( <?php echo $i; ?> == gc.media.galleryDefaults.columns ) { #>selected="selected"<# }
					#>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</span>

		<span class="setting">
			<input type="checkbox" id="gallery-settings-random-order" data-setting="_orderbyRandom" />
			<label for="gallery-settings-random-order" class="checkbox-label-inline"><?php _e( '随机顺序' ); ?></label>
		</span>

		<span class="setting size">
			<label for="gallery-settings-size" class="name"><?php _e( '尺寸' ); ?></label>
			<select id="gallery-settings-size" class="size" name="size"
				data-setting="size"
				<# if ( data.userSettings ) { #>
					data-user-setting="imgsize"
				<# } #>
				>
				<?php
				/** This filter is documented in gc-admin/includes/media.php */
				$size_names = apply_filters(
					'image_size_names_choose',
					array(
						'thumbnail' => __( '缩略图' ),
						'medium'    => __( '中等' ),
						'large'     => __( '大' ),
						'full'      => __( '全尺寸' ),
					)
				);

				foreach ( $size_names as $size => $label ) :
					?>
					<option value="<?php echo esc_attr( $size ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</span>
	</script>

	<?php // Template for the Playlists settings, used for example in the sidebar. ?>
	<script type="text/html" id="tmpl-playlist-settings">
		<h2><?php _e( '播放列表设置' ); ?></h2>

		<# var emptyModel = _.isEmpty( data.model ),
			isVideo = 'video' === data.controller.get('library').props.get('type'); #>

		<span class="setting">
			<input type="checkbox" id="playlist-settings-show-list" data-setting="tracklist" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<label for="playlist-settings-show-list" class="checkbox-label-inline">
				<# if ( isVideo ) { #>
				<?php _e( '显示视频列表' ); ?>
				<# } else { #>
				<?php _e( '显示曲目列表' ); ?>
				<# } #>
			</label>
		</span>

		<# if ( ! isVideo ) { #>
		<span class="setting">
			<input type="checkbox" id="playlist-settings-show-artist" data-setting="artists" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<label for="playlist-settings-show-artist" class="checkbox-label-inline">
				<?php _e( '在曲目列表中显示艺术家名' ); ?>
			</label>
		</span>
		<# } #>

		<span class="setting">
			<input type="checkbox" id="playlist-settings-show-images" data-setting="images" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<label for="playlist-settings-show-images" class="checkbox-label-inline">
				<?php _e( '显示图片' ); ?>
			</label>
		</span>
	</script>

	<?php // Template for the "从URL插入" layout. ?>
	<script type="text/html" id="tmpl-embed-link-settings">
		<span class="setting link-text">
			<label for="embed-link-settings-link-text" class="name"><?php _e( '链接文字' ); ?></label>
			<input type="text" id="embed-link-settings-link-text" class="alignment" data-setting="linkText" />
		</span>
		<div class="embed-container" style="display: none;">
			<div class="embed-preview"></div>
		</div>
	</script>

	<?php // Template for the "从URL插入" image preview and details. ?>
	<script type="text/html" id="tmpl-embed-image-settings">
		<div class="gc-clearfix">
			<div class="thumbnail">
				<img src="{{ data.model.url }}" draggable="false" alt="" />
			</div>
		</div>

		<span class="setting alt-text has-description">
			<label for="embed-image-settings-alt-text" class="name"><?php _e( '替代文本' ); ?></label>
			<input type="text" id="embed-image-settings-alt-text" data-setting="alt" aria-describedby="alt-text-description" />
		</span>
		<p class="描述" id="alt-text-description"><?php echo $alt_text_description; ?></p>

		<?php
		/** This filter is documented in gc-admin/includes/media.php */
		if ( ! apply_filters( 'disable_captions', '' ) ) :
			?>
			<span class="setting caption">
				<label for="embed-image-settings-caption" class="name"><?php _e( '说明文字' ); ?></label>
				<textarea id="embed-image-settings-caption" data-setting="caption"></textarea>
			</span>
		<?php endif; ?>

		<fieldset class="setting-group">
			<legend class="name"><?php _e( '对齐' ); ?></legend>
			<span class="setting align">
				<span class="button-group button-large" data-setting="align">
					<button class="button" value="left">
						<?php esc_html_e( '左' ); ?>
					</button>
					<button class="button" value="center">
						<?php esc_html_e( '中' ); ?>
					</button>
					<button class="button" value="right">
						<?php esc_html_e( '右' ); ?>
					</button>
					<button class="button active" value="none">
						<?php esc_html_e( '无' ); ?>
					</button>
				</span>
			</span>
		</fieldset>

		<fieldset class="setting-group">
			<legend class="name"><?php _e( '链接到' ); ?></legend>
			<span class="setting link-to">
				<span class="button-group button-large" data-setting="link">
					<button class="button" value="file">
						<?php esc_html_e( '图片URL' ); ?>
					</button>
					<button class="button" value="custom">
						<?php esc_html_e( '自定义URL' ); ?>
					</button>
					<button class="button active" value="none">
						<?php esc_html_e( '无' ); ?>
					</button>
				</span>
			</span>
			<span class="setting">
				<label for="embed-image-settings-link-to-custom" class="name"><?php _e( 'URL' ); ?></label>
				<input type="text" id="embed-image-settings-link-to-custom" class="link-to-custom" data-setting="linkUrl" />
			</span>
		</fieldset>
	</script>

	<?php // Template for the Image details, used for example in the editor. ?>
	<script type="text/html" id="tmpl-image-details">
		<div class="media-embed">
			<div class="embed-media-settings">
				<div class="column-settings">
					<span class="setting alt-text has-description">
						<label for="image-details-alt-text" class="name"><?php _e( '替代文本' ); ?></label>
						<input type="text" id="image-details-alt-text" data-setting="alt" value="{{ data.model.alt }}" aria-describedby="alt-text-description" />
					</span>
					<p class="描述" id="alt-text-description"><?php echo $alt_text_description; ?></p>

					<?php
					/** This filter is documented in gc-admin/includes/media.php */
					if ( ! apply_filters( 'disable_captions', '' ) ) :
						?>
						<span class="setting caption">
							<label for="image-details-caption" class="name"><?php _e( '说明文字' ); ?></label>
							<textarea id="image-details-caption" data-setting="caption">{{ data.model.caption }}</textarea>
						</span>
					<?php endif; ?>

					<h2><?php _e( '显示设置' ); ?></h2>
					<fieldset class="setting-group">
						<legend class="legend-inline"><?php _e( '对齐' ); ?></legend>
						<span class="setting align">
							<span class="button-group button-large" data-setting="align">
								<button class="button" value="left">
									<?php esc_html_e( '左' ); ?>
								</button>
								<button class="button" value="center">
									<?php esc_html_e( '中' ); ?>
								</button>
								<button class="button" value="right">
									<?php esc_html_e( '右' ); ?>
								</button>
								<button class="button active" value="none">
									<?php esc_html_e( '无' ); ?>
								</button>
							</span>
						</span>
					</fieldset>

					<# if ( data.attachment ) { #>
						<# if ( 'undefined' !== typeof data.attachment.sizes ) { #>
							<span class="setting size">
								<label for="image-details-size" class="name"><?php _e( '尺寸' ); ?></label>
								<select id="image-details-size" class="size" name="size"
									data-setting="size"
									<# if ( data.userSettings ) { #>
										data-user-setting="imgsize"
									<# } #>>
									<?php
									/** This filter is documented in gc-admin/includes/media.php */
									$sizes = apply_filters(
										'image_size_names_choose',
										array(
											'thumbnail' => __( '缩略图' ),
											'medium'    => __( '中等' ),
											'large'     => __( '大' ),
											'full'      => __( '全尺寸' ),
										)
									);

									foreach ( $sizes as $value => $name ) :
										?>
										<#
										var size = data.sizes['<?php echo esc_js( $value ); ?>'];
										if ( size ) { #>
											<option value="<?php echo esc_attr( $value ); ?>">
												<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
											</option>
										<# } #>
									<?php endforeach; ?>
									<option value="<?php echo esc_attr( 'custom' ); ?>">
										<?php _e( '自定义尺寸' ); ?>
									</option>
								</select>
							</span>
						<# } #>
							<div class="custom-size gc-clearfix<# if ( data.model.size !== 'custom' ) { #> hidden<# } #>">
								<span class="custom-size-setting">
									<label for="image-details-size-width"><?php _e( '宽度' ); ?></label>
									<input type="number" id="image-details-size-width" aria-describedby="image-size-desc" data-setting="customWidth" step="1" value="{{ data.model.customWidth }}" />
								</span>
								<span class="sep" aria-hidden="true">&times;</span>
								<span class="custom-size-setting">
									<label for="image-details-size-height"><?php _e( '高度' ); ?></label>
									<input type="number" id="image-details-size-height" aria-describedby="image-size-desc" data-setting="customHeight" step="1" value="{{ data.model.customHeight }}" />
								</span>
								<p id="image-size-desc" class="描述"><?php _e( '图片的像素尺寸' ); ?></p>
							</div>
					<# } #>

					<span class="setting link-to">
						<label for="image-details-link-to" class="name"><?php _e( '链接到' ); ?></label>
						<select id="image-details-link-to" data-setting="link">
						<# if ( data.attachment ) { #>
							<option value="file">
								<?php esc_html_e( '媒体文件' ); ?>
							</option>
							<option value="post">
								<?php esc_html_e( '附件页面' ); ?>
							</option>
						<# } else { #>
							<option value="file">
								<?php esc_html_e( '图片URL' ); ?>
							</option>
						<# } #>
							<option value="custom">
								<?php esc_html_e( '自定义URL' ); ?>
							</option>
							<option value="none">
								<?php esc_html_e( '无' ); ?>
							</option>
						</select>
					</span>
					<span class="setting">
						<label for="image-details-link-to-custom" class="name"><?php _e( 'URL' ); ?></label>
						<input type="text" id="image-details-link-to-custom" class="link-to-custom" data-setting="linkUrl" />
					</span>

					<div class="advanced-section">
						<h2><button type="button" class="button-link advanced-toggle"><?php _e( '高级选项' ); ?></button></h2>
						<div class="advanced-settings hidden">
							<div class="advanced-image">
								<span class="setting title-text">
									<label for="image-details-title-attribute" class="name"><?php _e( '图片 title 属性' ); ?></label>
									<input type="text" id="image-details-title-attribute" data-setting="title" value="{{ data.model.title }}" />
								</span>
								<span class="setting extra-classes">
									<label for="image-details-css-class" class="name"><?php _e( '图片CSS类' ); ?></label>
									<input type="text" id="image-details-css-class" data-setting="extraClasses" value="{{ data.model.extraClasses }}" />
								</span>
							</div>
							<div class="advanced-link">
								<span class="setting link-target">
									<input type="checkbox" id="image-details-link-target" data-setting="linkTargetBlank" value="_blank" <# if ( data.model.linkTargetBlank ) { #>checked="checked"<# } #>>
									<label for="image-details-link-target" class="checkbox-label"><?php _e( '在新标签页中打开链接' ); ?></label>
								</span>
								<span class="setting link-rel">
									<label for="image-details-link-rel" class="name"><?php _e( '链接Rel' ); ?></label>
									<input type="text" id="image-details-link-rel" data-setting="linkRel" value="{{ data.model.linkRel }}" />
								</span>
								<span class="setting link-class-name">
									<label for="image-details-link-css-class" class="name"><?php _e( '链接CSS类' ); ?></label>
									<input type="text" id="image-details-link-css-class" data-setting="linkClassName" value="{{ data.model.linkClassName }}" />
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="column-image">
					<div class="image">
						<img src="{{ data.model.url }}" draggable="false" alt="" />
						<# if ( data.attachment && window.imageEdit ) { #>
							<div class="actions">
								<input type="button" class="edit-attachment button" value="<?php esc_attr_e( '编辑原始文件' ); ?>" />
								<input type="button" class="replace-attachment button" value="<?php esc_attr_e( '替换' ); ?>" />
							</div>
						<# } #>
					</div>
				</div>
			</div>
		</div>
	</script>

	<?php // Template for the Image Editor layout. ?>
	<script type="text/html" id="tmpl-image-editor">
		<div id="media-head-{{ data.id }}"></div>
		<div id="image-editor-{{ data.id }}"></div>
	</script>

	<?php // Template for an embedded Audio details. ?>
	<script type="text/html" id="tmpl-audio-details">
		<# var ext, html5types = {
			mp3: gc.media.view.settings.embedMimes.mp3,
			ogg: gc.media.view.settings.embedMimes.ogg
		}; #>

		<?php $audio_types = gc_get_audio_extensions(); ?>
		<div class="media-embed media-embed-details">
			<div class="embed-media-settings embed-audio-settings">
				<?php gc_underscore_audio_template(); ?>

				<# if ( ! _.isEmpty( data.model.src ) ) {
					ext = data.model.src.split('.').pop();
					if ( html5types[ ext ] ) {
						delete html5types[ ext ];
					}
				#>
				<span class="setting">
					<label for="audio-details-source" class="name"><?php _e( 'URL' ); ?></label>
					<input type="text" id="audio-details-source" readonly data-setting="src" value="{{ data.model.src }}" />
					<button type="button" class="button-link remove-setting"><?php _e( '移除音频来源' ); ?></button>
				</span>
				<# } #>
				<?php

				foreach ( $audio_types as $type ) :
					?>
				<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) {
					if ( ! _.isUndefined( html5types.<?php echo $type; ?> ) ) {
						delete html5types.<?php echo $type; ?>;
					}
				#>
				<span class="setting">
					<label for="audio-details-<?php echo $type . '-source'; ?>" class="name"><?php echo strtoupper( $type ); ?></label>
					<input type="text" id="audio-details-<?php echo $type . '-source'; ?>" readonly data-setting="<?php echo $type; ?>" value="{{ data.model.<?php echo $type; ?> }}" />
					<button type="button" class="button-link remove-setting"><?php _e( '移除音频来源' ); ?></button>
				</span>
				<# } #>
				<?php endforeach; ?>

				<# if ( ! _.isEmpty( html5types ) ) { #>
				<fieldset class="setting-group">
					<legend class="name"><?php _e( '添加备用源以增强HTML5播放体验' ); ?></legend>
					<span class="setting">
						<span class="button-large">
						<# _.each( html5types, function (mime, type) { #>
							<button class="button add-media-source" data-mime="{{ mime }}">{{ type }}</button>
						<# } ) #>
						</span>
					</span>
				</fieldset>
				<# } #>

				<fieldset class="setting-group">
					<legend class="name"><?php _e( '预加载' ); ?></legend>
					<span class="setting preload">
						<span class="button-group button-large" data-setting="preload">
							<button class="button" value="auto"><?php _ex( '自动', 'auto preload' ); ?></button>
							<button class="button" value="metadata"><?php _e( '元数据' ); ?></button>
							<button class="button active" value="none"><?php _e( '无' ); ?></button>
						</span>
					</span>
				</fieldset>

				<span class="setting-group">
					<span class="setting checkbox-setting autoplay">
						<input type="checkbox" id="audio-details-autoplay" data-setting="autoplay" />
						<label for="audio-details-autoplay" class="checkbox-label"><?php _e( '自动播放' ); ?></label>
					</span>

					<span class="setting checkbox-setting">
						<input type="checkbox" id="audio-details-loop" data-setting="loop" />
						<label for="audio-details-loop" class="checkbox-label"><?php _e( '循环' ); ?></label>
					</span>
				</span>
			</div>
		</div>
	</script>

	<?php // Template for an embedded Video details. ?>
	<script type="text/html" id="tmpl-video-details">
		<# var ext, html5types = {
			mp4: gc.media.view.settings.embedMimes.mp4,
			ogv: gc.media.view.settings.embedMimes.ogv,
			webm: gc.media.view.settings.embedMimes.webm
		}; #>

		<?php $video_types = gc_get_video_extensions(); ?>
		<div class="media-embed media-embed-details">
			<div class="embed-media-settings embed-video-settings">
				<div class="gc-video-holder">
				<#
				var w = ! data.model.width || data.model.width > 640 ? 640 : data.model.width,
					h = ! data.model.height ? 360 : data.model.height;

				if ( data.model.width && w !== data.model.width ) {
					h = Math.ceil( ( h * w ) / data.model.width );
				}
				#>

				<?php gc_underscore_video_template(); ?>

				<# if ( ! _.isEmpty( data.model.src ) ) {
					ext = data.model.src.split('.').pop();
					if ( html5types[ ext ] ) {
						delete html5types[ ext ];
					}
				#>
				<span class="setting">
					<label for="video-details-source" class="name"><?php _e( 'URL' ); ?></label>
					<input type="text" id="video-details-source" readonly data-setting="src" value="{{ data.model.src }}" />
					<button type="button" class="button-link remove-setting"><?php _e( '移除视频来源' ); ?></button>
				</span>
				<# } #>
				<?php
				foreach ( $video_types as $type ) :
					?>
				<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) {
					if ( ! _.isUndefined( html5types.<?php echo $type; ?> ) ) {
						delete html5types.<?php echo $type; ?>;
					}
				#>
				<span class="setting">
					<label for="video-details-<?php echo $type . '-source'; ?>" class="name"><?php echo strtoupper( $type ); ?></label>
					<input type="text" id="video-details-<?php echo $type . '-source'; ?>" readonly data-setting="<?php echo $type; ?>" value="{{ data.model.<?php echo $type; ?> }}" />
					<button type="button" class="button-link remove-setting"><?php _e( '移除视频来源' ); ?></button>
				</span>
				<# } #>
				<?php endforeach; ?>
				</div>

				<# if ( ! _.isEmpty( html5types ) ) { #>
				<fieldset class="setting-group">
					<legend class="name"><?php _e( '添加备用源以增强HTML5播放体验' ); ?></legend>
					<span class="setting">
						<span class="button-large">
						<# _.each( html5types, function (mime, type) { #>
							<button class="button add-media-source" data-mime="{{ mime }}">{{ type }}</button>
						<# } ) #>
						</span>
					</span>
				</fieldset>
				<# } #>

				<# if ( ! _.isEmpty( data.model.poster ) ) { #>
				<span class="setting">
					<label for="video-details-poster-image" class="name"><?php _e( '海报图片' ); ?></label>
					<input type="text" id="video-details-poster-image" readonly data-setting="poster" value="{{ data.model.poster }}" />
					<button type="button" class="button-link remove-setting"><?php _e( '移除海报图片' ); ?></button>
				</span>
				<# } #>

				<fieldset class="setting-group">
					<legend class="name"><?php _e( '预加载' ); ?></legend>
					<span class="setting preload">
						<span class="button-group button-large" data-setting="preload">
							<button class="button" value="auto"><?php _ex( '自动', 'auto preload' ); ?></button>
							<button class="button" value="metadata"><?php _e( '元数据' ); ?></button>
							<button class="button active" value="none"><?php _e( '无' ); ?></button>
						</span>
					</span>
				</fieldset>

				<span class="setting-group">
					<span class="setting checkbox-setting autoplay">
						<input type="checkbox" id="video-details-autoplay" data-setting="autoplay" />
						<label for="video-details-autoplay" class="checkbox-label"><?php _e( '自动播放' ); ?></label>
					</span>

					<span class="setting checkbox-setting">
						<input type="checkbox" id="video-details-loop" data-setting="loop" />
						<label for="video-details-loop" class="checkbox-label"><?php _e( '循环' ); ?></label>
					</span>
				</span>

				<span class="setting" data-setting="content">
					<#
					var content = '';
					if ( ! _.isEmpty( data.model.content ) ) {
						var tracks = jQuery( data.model.content ).filter( 'track' );
						_.each( tracks.toArray(), function( track, index ) {
							content += track.outerHTML; #>
						<label for="video-details-track-{{ index }}" class="name"><?php _e( '“音轨”（字幕、说明文字、内容描述、章节或元数据）' ); ?></label>
						<input class="content-track" type="text" id="video-details-track-{{ index }}" aria-describedby="video-details-track-desc-{{ index }}" value="{{ track.outerHTML }}" />
						<span class="描述" id="video-details-track-desc-{{ index }}">
						<?php
							printf(
								/* translators: 1: "srclang" HTML attribute, 2: "label" HTML attribute, 3: "kind" HTML attribute. */
								__( '%1$s、%2$s和%3$s可被编辑并设置为视频的语言和种类。' ),
								'srclang',
								'label',
								'kind'
							);
						?>
						</span>
						<button type="button" class="button-link remove-setting remove-track"><?php _ex( '移除视频轨道', 'media' ); ?></button><br/>
						<# } ); #>
					<# } else { #>
					<span class="name"><?php _e( '“音轨”（字幕、说明文字、内容描述、章节或元数据）' ); ?></span><br />
					<em><?php _e( '没有关联的字幕。' ); ?></em>
					<# } #>
					<textarea class="hidden content-setting">{{ content }}</textarea>
				</span>
			</div>
		</div>
	</script>

	<?php // Template for a Gallery within the editor. ?>
	<script type="text/html" id="tmpl-editor-gallery">
		<# if ( data.attachments.length ) { #>
			<div class="gallery gallery-columns-{{ data.columns }}">
				<# _.each( data.attachments, function( attachment, index ) { #>
					<dl class="gallery-item">
						<dt class="gallery-icon">
							<# if ( attachment.thumbnail ) { #>
								<img src="{{ attachment.thumbnail.url }}" width="{{ attachment.thumbnail.width }}" height="{{ attachment.thumbnail.height }}" alt="{{ attachment.alt }}" />
							<# } else { #>
								<img src="{{ attachment.url }}" alt="{{ attachment.alt }}" />
							<# } #>
						</dt>
						<# if ( attachment.caption ) { #>
							<dd class="gc-caption-text gallery-caption">
								{{{ data.verifyHTML( attachment.caption ) }}}
							</dd>
						<# } #>
					</dl>
					<# if ( index % data.columns === data.columns - 1 ) { #>
						<br style="clear: both;">
					<# } #>
				<# } ); #>
			</div>
		<# } else { #>
			<div class="gcview-error">
				<div class="dashicons dashicons-format-gallery"></div><p><?php _e( '找不到条目。' ); ?></p>
			</div>
		<# } #>
	</script>

	<?php // Template for the Crop area layout, used for example in the Customizer. ?>
	<script type="text/html" id="tmpl-crop-content">
		<img class="crop-image" src="{{ data.url }}" alt="<?php esc_attr_e( '图片裁剪区域预览，需要鼠标交互。' ); ?>" />
		<div class="upload-errors"></div>
	</script>

	<?php // Template for the Site Icon preview, used for example in the Customizer. ?>
	<script type="text/html" id="tmpl-site-icon-preview">
		<h2><?php _e( '预览' ); ?></h2>
		<strong aria-hidden="true"><?php _e( '作为浏览器图标' ); ?></strong>
		<div class="favicon-preview">
			<img src="<?php echo esc_url( admin_url( 'images/' . ( is_rtl() ? 'browser-rtl.png' : 'browser.png' ) ) ); ?>" class="browser-preview" width="182" height="" alt="" />

			<div class="favicon">
				<img id="preview-favicon" src="{{ data.url }}" alt="<?php esc_attr_e( '作为浏览器图标预览' ); ?>" />
			</div>
			<span class="browser-title" aria-hidden="true"><# print( '<?php bloginfo( 'name' ); ?>' ) #></span>
		</div>

		<strong aria-hidden="true"><?php _e( '作为app图标' ); ?></strong>
		<div class="app-icon-preview">
			<img id="preview-app-icon" src="{{ data.url }}" alt="<?php esc_attr_e( '作为app图标预览' ); ?>" />
		</div>
	</script>

	<?php

	/**
	 * Fires when the custom Backbone media templates are printed.
	 *
	 */
	do_action( 'print_media_templates' );
}
