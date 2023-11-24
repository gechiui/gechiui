<?php
/**
 * Widget API: GC_Widget_Media_Video class
 *
 * @package GeChiUI
 * @subpackage Widgets
 */

/**
 * Core class that implements a video widget.
 *
 * @see GC_Widget_Media
 * @see GC_Widget
 */
class GC_Widget_Media_Video extends GC_Widget_Media {

	/**
	 * Constructor.
	 *
	 * @since 4.8.0
	 */
	public function __construct() {
		parent::__construct(
			'media_video',
			__( '视频' ),
			array(
				'description' => __( '显示存在媒体库中的视频。' ),
				'mime_type'   => 'video',
			)
		);

		$this->l10n = array_merge(
			$this->l10n,
			array(
				'no_media_selected'          => __( '未选择视频' ),
				'add_media'                  => _x( '添加视频', 'label for button in the video widget' ),
				'replace_media'              => _x( '替换视频', 'label for button in the video widget; should preferably not be longer than ~13 characters long' ),
				'edit_media'                 => _x( '编辑视频', 'label for button in the video widget; should preferably not be longer than ~13 characters long' ),
				'missing_attachment'         => sprintf(
					/* translators: %s: URL to media library. */
					__( '找不到指定视频。检查您的<a href="%s">媒体库</a>并确保其未被删除。' ),
					esc_url( admin_url( 'upload.php' ) )
				),
				/* translators: %d: Widget count. */
				'media_library_state_multi'  => _n_noop( '视频小工具（%d）', '视频小工具（%d）' ),
				'media_library_state_single' => __( '视频小工具' ),
				/* translators: %s: A list of valid video file extensions. */
				'unsupported_file_type'      => sprintf( __( '抱歉，未能从提供的URL载入视频。请检查该URL是否为一个支持的视频文件（%s）或视频流。' ), '<code>.' . implode( '</code>, <code>.', gc_get_video_extensions() ) . '</code>' ),
			)
		);
	}

	/**
	 * Get schema for properties of a widget instance (item).
	 *
	 * @since 4.8.0
	 *
	 * @see GC_REST_Controller::get_item_schema()
	 * @see GC_REST_Controller::get_additional_fields()
	 * @link https://core.trac.gechiui.com/ticket/35574
	 *
	 * @return array Schema for properties.
	 */
	public function get_instance_schema() {

		$schema = array(
			'preload' => array(
				'type'                  => 'string',
				'enum'                  => array( 'none', 'auto', 'metadata' ),
				'default'               => 'metadata',
				'description'           => __( '预加载' ),
				'should_preview_update' => false,
			),
			'loop'    => array(
				'type'                  => 'boolean',
				'default'               => false,
				'description'           => __( '循环' ),
				'should_preview_update' => false,
			),
			'content' => array(
				'type'                  => 'string',
				'default'               => '',
				'sanitize_callback'     => 'gc_kses_post',
				'description'           => __( '“音轨”（字幕、说明文字、内容描述、章节或元数据）' ),
				'should_preview_update' => false,
			),
		);

		foreach ( gc_get_video_extensions() as $video_extension ) {
			$schema[ $video_extension ] = array(
				'type'        => 'string',
				'default'     => '',
				'format'      => 'uri',
				/* translators: %s: Video extension. */
				'description' => sprintf( __( '%s格式的视频源文件URL' ), $video_extension ),
			);
		}

		return array_merge( $schema, parent::get_instance_schema() );
	}

	/**
	 * Render the media on the frontend.
	 *
	 * @since 4.8.0
	 *
	 * @param array $instance Widget instance props.
	 */
	public function render_media( $instance ) {
		$instance   = array_merge( gc_list_pluck( $this->get_instance_schema(), 'default' ), $instance );
		$attachment = null;

		if ( $this->is_attachment_with_mime_type( $instance['attachment_id'], $this->widget_options['mime_type'] ) ) {
			$attachment = get_post( $instance['attachment_id'] );
		}

		$src = $instance['url'];
		if ( $attachment ) {
			$src = gc_get_attachment_url( $attachment->ID );
		}

		if ( empty( $src ) ) {
			return;
		}

		$youku_pattern = '#^https?://(?:www\.)?(?:youku\.com/watch|youtu\.be/)#';
		$vimeo_pattern   = '#^https?://(.+\.)?vimeo\.com/.*#';

		if ( $attachment || preg_match( $youku_pattern, $src ) || preg_match( $vimeo_pattern, $src ) ) {
			add_filter( 'gc_video_shortcode', array( $this, 'inject_video_max_width_style' ) );

			echo gc_video_shortcode(
				array_merge(
					$instance,
					compact( 'src' )
				),
				$instance['content']
			);

			remove_filter( 'gc_video_shortcode', array( $this, 'inject_video_max_width_style' ) );
		} else {
			echo $this->inject_video_max_width_style( gc_oembed_get( $src ) );
		}
	}

	/**
	 * Inject max-width and remove height for videos too constrained to fit inside sidebars on frontend.
	 *
	 * @since 4.8.0
	 *
	 * @param string $html Video shortcode HTML output.
	 * @return string HTML Output.
	 */
	public function inject_video_max_width_style( $html ) {
		$html = preg_replace( '/\sheight="\d+"/', '', $html );
		$html = preg_replace( '/\swidth="\d+"/', '', $html );
		$html = preg_replace( '/(?<=width:)\s*\d+px(?=;?)/', '100%', $html );
		return $html;
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * These scripts normally are enqueued just-in-time when a video shortcode is used.
	 * In the customizer, however, widgets can be dynamically added and rendered via
	 * selective refresh, and so it is important to unconditionally enqueue them in
	 * case a widget does get added.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_preview_scripts() {
		/** This filter is documented in gc-includes/media.php */
		if ( 'mediaelement' === apply_filters( 'gc_video_shortcode_library', 'mediaelement' ) ) {
			gc_enqueue_style( 'gc-mediaelement' );
			gc_enqueue_script( 'mediaelement-vimeo' );
			gc_enqueue_script( 'gc-mediaelement' );
		}
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_scripts() {
		parent::enqueue_admin_scripts();

		$handle = 'media-video-widget';
		gc_enqueue_script( $handle );

		$exported_schema = array();
		foreach ( $this->get_instance_schema() as $field => $field_schema ) {
			$exported_schema[ $field ] = gc_array_slice_assoc( $field_schema, array( 'type', 'default', 'enum', 'minimum', 'format', 'media_prop', 'should_preview_update' ) );
		}
		gc_add_inline_script(
			$handle,
			sprintf(
				'gc.mediaWidgets.modelConstructors[ %s ].prototype.schema = %s;',
				gc_json_encode( $this->id_base ),
				gc_json_encode( $exported_schema )
			)
		);

		gc_add_inline_script(
			$handle,
			sprintf(
				'
					gc.mediaWidgets.controlConstructors[ %1$s ].prototype.mime_type = %2$s;
					gc.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n = _.extend( {}, gc.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n, %3$s );
				',
				gc_json_encode( $this->id_base ),
				gc_json_encode( $this->widget_options['mime_type'] ),
				gc_json_encode( $this->l10n )
			)
		);
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.8.0
	 */
	public function render_control_template_scripts() {
		parent::render_control_template_scripts()
		?>
		<script type="text/html" id="tmpl-gc-media-widget-video-preview">
			<# if ( data.error && 'missing_attachment' === data.error ) { #>
				<div class="alert alert-danger notice-alt notice-missing-attachment">
					<p><?php echo $this->l10n['missing_attachment']; ?></p>
				</div>
			<# } else if ( data.error && 'unsupported_file_type' === data.error ) { #>
				<div class="alert alert-danger notice-alt notice-missing-attachment">
					<p><?php echo $this->l10n['unsupported_file_type']; ?></p>
				</div>
			<# } else if ( data.error ) { #>
				<div class="alert alert-danger notice-alt">
					<p><?php _e( '发生了未知错误，无法预览媒体。' ); ?></p>
				</div>
			<# } else if ( data.is_oembed && data.model.poster ) { #>
				<a href="{{ data.model.src }}" target="_blank" class="media-widget-video-link">
					<img src="{{ data.model.poster }}" />
				</a>
			<# } else if ( data.is_oembed ) { #>
				<a href="{{ data.model.src }}" target="_blank" class="media-widget-video-link no-poster">
					<span class="dashicons dashicons-format-video"></span>
				</a>
			<# } else if ( data.model.src ) { #>
				<?php gc_underscore_video_template(); ?>
			<# } #>
		</script>
		<?php
	}
}
