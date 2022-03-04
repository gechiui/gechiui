<?php
/**
 * Customize API: GC_Customize_Media_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Media Control class.
 *
 *
 *
 * @see GC_Customize_Control
 */
class GC_Customize_Media_Control extends GC_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'media';

	/**
	 * Media control mime type.
	 *
	 * @var string
	 */
	public $mime_type = '';

	/**
	 * Button labels.
	 *
	 * @var array
	 */
	public $button_labels = array();

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_Customize_Control::__construct()
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 *                                      See GC_Customize_Control::__construct() for information
	 *                                      on accepted arguments. Default empty array.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = gc_parse_args( $this->button_labels, $this->get_default_button_labels() );
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 */
	public function enqueue() {
		gc_enqueue_media();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 *
	 * @see GC_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['label']         = html_entity_decode( $this->label, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$this->json['mime_type']     = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;
		$this->json['canUpload']     = current_user_can( 'upload_files' );

		$value = $this->value();

		if ( is_object( $this->setting ) ) {
			if ( $this->setting->default ) {
				// Fake an attachment model - needs all fields used by template.
				// Note that the default value must be a URL, NOT an attachment ID.
				$ext  = substr( $this->setting->default, -3 );
				$type = in_array( $ext, array( 'jpg', 'png', 'gif', 'bmp', 'webp' ), true ) ? 'image' : 'document';

				$default_attachment = array(
					'id'    => 1,
					'url'   => $this->setting->default,
					'type'  => $type,
					'icon'  => gc_mime_type_icon( $type ),
					'title' => gc_basename( $this->setting->default ),
				);

				if ( 'image' === $type ) {
					$default_attachment['sizes'] = array(
						'full' => array( 'url' => $this->setting->default ),
					);
				}

				$this->json['defaultAttachment'] = $default_attachment;
			}

			if ( $value && $this->setting->default && $value === $this->setting->default ) {
				// Set the default as the attachment.
				$this->json['attachment'] = $this->json['defaultAttachment'];
			} elseif ( $value ) {
				$this->json['attachment'] = gc_prepare_attachment_for_js( $value );
			}
		}
	}

	/**
	 * Don't render any content for this control from PHP.
	 *
	 *
	 * @see GC_Customize_Media_Control::content_template()
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 */
	public function content_template() {
		?>
		<#
		var descriptionId = _.uniqueId( 'customize-media-control-description-' );
		var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
		#>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<div class="customize-control-notifications-container"></div>
		<# if ( data.description ) { #>
			<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view attachment-media-view-{{ data.attachment.type }} {{ data.attachment.orientation }}">
				<div class="thumbnail thumbnail-{{ data.attachment.type }}">
					<# if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.medium ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.medium.url }}" draggable="false" alt="" />
					<# } else if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.full ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.full.url }}" draggable="false" alt="" />
					<# } else if ( 'audio' === data.attachment.type ) { #>
						<# if ( data.attachment.image && data.attachment.image.src && data.attachment.image.src !== data.attachment.icon ) { #>
							<img src="{{ data.attachment.image.src }}" class="thumbnail" draggable="false" alt="" />
						<# } else { #>
							<img src="{{ data.attachment.icon }}" class="attachment-thumb type-icon" draggable="false" alt="" />
						<# } #>
						<p class="attachment-meta attachment-meta-title">&#8220;{{ data.attachment.title }}&#8221;</p>
						<# if ( data.attachment.album || data.attachment.meta.album ) { #>
						<p class="attachment-meta"><em>{{ data.attachment.album || data.attachment.meta.album }}</em></p>
						<# } #>
						<# if ( data.attachment.artist || data.attachment.meta.artist ) { #>
						<p class="attachment-meta">{{ data.attachment.artist || data.attachment.meta.artist }}</p>
						<# } #>
						<audio style="visibility: hidden" controls class="gc-audio-shortcode" width="100%" preload="none">
							<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}" />
						</audio>
					<# } else if ( 'video' === data.attachment.type ) { #>
						<div class="gc-media-wrapper gc-video">
							<video controls="controls" class="gc-video-shortcode" preload="metadata"
								<# if ( data.attachment.image && data.attachment.image.src !== data.attachment.icon ) { #>poster="{{ data.attachment.image.src }}"<# } #>>
								<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}" />
							</video>
						</div>
					<# } else { #>
						<img class="attachment-thumb type-icon icon" src="{{ data.attachment.icon }}" draggable="false" alt="" />
						<p class="attachment-title">{{ data.attachment.title }}</p>
					<# } #>
				</div>
				<div class="actions">
					<# if ( data.canUpload ) { #>
					<button type="button" class="button remove-button">{{ data.button_labels.remove }}</button>
					<button type="button" class="button upload-button control-focus" {{{ describedByAttr }}}>{{ data.button_labels.change }}</button>
					<# } #>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<# if ( data.canUpload ) { #>
					<button type="button" class="upload-button button-add-media" {{{ describedByAttr }}}>{{ data.button_labels.select }}</button>
				<# } #>
				<div class="actions">
					<# if ( data.defaultAttachment ) { #>
						<button type="button" class="button default-button">{{ data.button_labels['default'] }}</button>
					<# } #>
				</div>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Get default button labels.
	 *
	 * Provides an array of the default button labels based on the mime type of the current control.
	 *
	 *
	 * @return string[] An associative array of default button labels keyed by the button name.
	 */
	public function get_default_button_labels() {
		// Get just the mime type and strip the mime subtype if present.
		$mime_type = ! empty( $this->mime_type ) ? strtok( ltrim( $this->mime_type, '/' ), '/' ) : 'default';

		switch ( $mime_type ) {
			case 'video':
				return array(
					'select'       => __( '选择视频' ),
					'change'       => __( '更换视频' ),
					'default'      => __( '默认' ),
					'remove'       => __( '移除' ),
					'placeholder'  => __( '未选择视频' ),
					'frame_title'  => __( '选择视频' ),
					'frame_button' => __( '选择视频' ),
				);
			case 'audio':
				return array(
					'select'       => __( '选择音频' ),
					'change'       => __( '更换音频' ),
					'default'      => __( '默认' ),
					'remove'       => __( '移除' ),
					'placeholder'  => __( '未选择音频' ),
					'frame_title'  => __( '选择音频' ),
					'frame_button' => __( '选择音频' ),
				);
			case 'image':
				return array(
					'select'       => __( '选择图片' ),
					'site_icon'    => __( '选择站点图标' ),
					'change'       => __( '更换图片' ),
					'default'      => __( '默认' ),
					'remove'       => __( '移除' ),
					'placeholder'  => __( '未选择图片' ),
					'frame_title'  => __( '选择图片' ),
					'frame_button' => __( '选择图片' ),
				);
			default:
				return array(
					'select'       => __( '选择文件' ),
					'change'       => __( '更换文件' ),
					'default'      => __( '默认' ),
					'remove'       => __( '移除' ),
					'placeholder'  => __( '未选择文件' ),
					'frame_title'  => __( '选择文件' ),
					'frame_button' => __( '选择文件' ),
				);
		} // End switch().
	}
}
