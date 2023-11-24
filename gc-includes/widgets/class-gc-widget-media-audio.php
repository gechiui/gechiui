<?php
/**
 * Widget API: GC_Widget_Media_Audio class
 *
 * @package GeChiUI
 * @subpackage Widgets
 */

/**
 * Core class that implements an audio widget.
 *
 * @see GC_Widget_Media
 * @see GC_Widget
 */
class GC_Widget_Media_Audio extends GC_Widget_Media {

	/**
	 * Constructor.
	 *
	 * @since 4.8.0
	 */
	public function __construct() {
		parent::__construct(
			'media_audio',
			__( '音频' ),
			array(
				'description' => __( '显示一个音频播放器。' ),
				'mime_type'   => 'audio',
			)
		);

		$this->l10n = array_merge(
			$this->l10n,
			array(
				'no_media_selected'          => __( '未选择音频' ),
				'add_media'                  => _x( '添加音频', 'label for button in the audio widget' ),
				'replace_media'              => _x( '替换音频', 'label for button in the audio widget; should preferably not be longer than ~13 characters long' ),
				'edit_media'                 => _x( '编辑音频', 'label for button in the audio widget; should preferably not be longer than ~13 characters long' ),
				'missing_attachment'         => sprintf(
					/* translators: %s: URL to media library. */
					__( '找不到指定音频。检查您的<a href="%s">媒体库</a>并确保其未被删除。' ),
					esc_url( admin_url( 'upload.php' ) )
				),
				/* translators: %d: Widget count. */
				'media_library_state_multi'  => _n_noop( '音频小工具（%d）', '音频小工具（%d）' ),
				'media_library_state_single' => __( '音频小工具' ),
				'unsupported_file_type'      => __( '文件格式可能不正确。请链接到正确的音频文件。' ),
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
				'type'        => 'string',
				'enum'        => array( 'none', 'auto', 'metadata' ),
				'default'     => 'none',
				'description' => __( '预加载' ),
			),
			'loop'    => array(
				'type'        => 'boolean',
				'default'     => false,
				'description' => __( '循环' ),
			),
		);

		foreach ( gc_get_audio_extensions() as $audio_extension ) {
			$schema[ $audio_extension ] = array(
				'type'        => 'string',
				'default'     => '',
				'format'      => 'uri',
				/* translators: %s: Audio extension. */
				'description' => sprintf( __( '%s格式的音频源文件URL' ), $audio_extension ),
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

		if ( $attachment ) {
			$src = gc_get_attachment_url( $attachment->ID );
		} else {
			$src = $instance['url'];
		}

		echo gc_audio_shortcode(
			array_merge(
				$instance,
				compact( 'src' )
			)
		);
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * These scripts normally are enqueued just-in-time when an audio shortcode is used.
	 * In the customizer, however, widgets can be dynamically added and rendered via
	 * selective refresh, and so it is important to unconditionally enqueue them in
	 * case a widget does get added.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_preview_scripts() {
		/** This filter is documented in gc-includes/media.php */
		if ( 'mediaelement' === apply_filters( 'gc_audio_shortcode_library', 'mediaelement' ) ) {
			gc_enqueue_style( 'gc-mediaelement' );
			gc_enqueue_script( 'gc-mediaelement' );
		}
	}

	/**
	 * Loads the required media files for the media manager and scripts for media widgets.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_scripts() {
		parent::enqueue_admin_scripts();

		gc_enqueue_style( 'gc-mediaelement' );
		gc_enqueue_script( 'gc-mediaelement' );

		$handle = 'media-audio-widget';
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
		<script type="text/html" id="tmpl-gc-media-widget-audio-preview">
			<# if ( data.error && 'missing_attachment' === data.error ) { #>
				<div class="alert alert-danger notice-alt notice-missing-attachment">
					<p><?php echo $this->l10n['missing_attachment']; ?></p>
				</div>
			<# } else if ( data.error ) { #>
				<div class="alert alert-danger notice-alt">
					<p><?php _e( '发生了未知错误，无法预览媒体。' ); ?></p>
				</div>
			<# } else if ( data.model && data.model.src ) { #>
				<?php gc_underscore_audio_template(); ?>
			<# } #>
		</script>
		<?php
	}
}
