<?php
/**
 * Customize API: GC_Customize_Header_Image_Control class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customize Header Image Control class.
 *
 *
 *
 * @see GC_Customize_Image_Control
 */
class GC_Customize_Header_Image_Control extends GC_Customize_Image_Control {
	/**
	 * Customize control type.
	 *
	 * @var string
	 */
	public $type = 'header';

	/**
	 * Uploaded header images.
	 *
	 * @var string
	 */
	public $uploaded_headers;

	/**
	 * Default header images.
	 *
	 * @var string
	 */
	public $default_headers;

	/**
	 * Constructor.
	 *
	 *
	 * @param GC_Customize_Manager $manager Customizer bootstrap instance.
	 */
	public function __construct( $manager ) {
		parent::__construct(
			$manager,
			'header_image',
			array(
				'label'    => __( '页眉图片' ),
				'settings' => array(
					'default' => 'header_image',
					'data'    => 'header_image_data',
				),
				'section'  => 'header_image',
				'removed'  => 'remove-header',
				'get_url'  => 'get_header_image',
			)
		);

	}

	/**
	 */
	public function enqueue() {
		gc_enqueue_media();
		gc_enqueue_script( 'customize-views' );

		$this->prepare_control();

		gc_localize_script(
			'customize-views',
			'_gcCustomizeHeader',
			array(
				'data'     => array(
					'width'         => absint( get_theme_support( 'custom-header', 'width' ) ),
					'height'        => absint( get_theme_support( 'custom-header', 'height' ) ),
					'flex-width'    => absint( get_theme_support( 'custom-header', 'flex-width' ) ),
					'flex-height'   => absint( get_theme_support( 'custom-header', 'flex-height' ) ),
					'currentImgSrc' => $this->get_current_image_src(),
				),
				'nonces'   => array(
					'add'    => gc_create_nonce( 'header-add' ),
					'remove' => gc_create_nonce( 'header-remove' ),
				),
				'uploads'  => $this->uploaded_headers,
				'defaults' => $this->default_headers,
			)
		);

		parent::enqueue();
	}

	/**
	 * @global Custom_Image_Header $custom_image_header
	 */
	public function prepare_control() {
		global $custom_image_header;
		if ( empty( $custom_image_header ) ) {
			return;
		}

		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_header_image_template' ) );

		// Process default headers and uploaded headers.
		$custom_image_header->process_default_headers();
		$this->default_headers  = $custom_image_header->get_default_header_images();
		$this->uploaded_headers = $custom_image_header->get_uploaded_header_images();
	}

	/**
	 */
	public function print_header_image_template() {
		?>
		<script type="text/template" id="tmpl-header-choice">
			<# if (data.random) { #>
			<button type="button" class="button display-options random">
				<span class="dashicons dashicons-randomize dice"></span>
				<# if ( data.type === 'uploaded' ) { #>
					<?php _e( '随机化上传的页眉图片' ); ?>
				<# } else if ( data.type === 'default' ) { #>
					<?php _e( '随机化推荐的页眉图片' ); ?>
				<# } #>
			</button>

			<# } else { #>

			<button type="button" class="choice thumbnail"
				data-customize-image-value="{{{data.header.url}}}"
				data-customize-header-image-data="{{JSON.stringify(data.header)}}">
				<span class="screen-reader-text"><?php _e( '设置图片' ); ?></span>
				<img src="{{{data.header.thumbnail_url}}}" alt="{{{data.header.alt_text || data.header.description}}}" />
			</button>

			<# if ( data.type === 'uploaded' ) { #>
				<button type="button" class="dashicons dashicons-no close"><span class="screen-reader-text"><?php _e( '移除图片' ); ?></span></button>
			<# } #>

			<# } #>
		</script>

		<script type="text/template" id="tmpl-header-current">
			<# if (data.choice) { #>
				<# if (data.random) { #>

			<div class="placeholder">
				<span class="dashicons dashicons-randomize dice"></span>
				<# if ( data.type === 'uploaded' ) { #>
					<?php _e( '正在随机化上传的页眉图片' ); ?>
				<# } else if ( data.type === 'default' ) { #>
					<?php _e( '正在随机化推荐的页眉图片' ); ?>
				<# } #>
			</div>

				<# } else { #>

			<img src="{{{data.header.thumbnail_url}}}" alt="{{{data.header.alt_text || data.header.description}}}" />

				<# } #>
			<# } else { #>

			<div class="placeholder">
				<?php _e( '未设置图片' ); ?>
			</div>

			<# } #>
		</script>
		<?php
	}

	/**
	 * @return string|void
	 */
	public function get_current_image_src() {
		$src = $this->value();
		if ( isset( $this->get_url ) ) {
			$src = call_user_func( $this->get_url, $src );
			return $src;
		}
	}

	/**
	 */
	public function render_content() {
		$visibility = $this->get_current_image_src() ? '' : ' style="display:none" ';
		$width      = absint( get_theme_support( 'custom-header', 'width' ) );
		$height     = absint( get_theme_support( 'custom-header', 'height' ) );
		?>
		<div class="customize-control-content">
			<?php
			if ( current_theme_supports( 'custom-header', 'video' ) ) {
				echo '<span class="customize-control-title">' . $this->label . '</span>';
			}
			?>
			<div class="customize-control-notifications-container"></div>
			<p class="customizer-section-intro customize-control-description">
				<?php
				if ( current_theme_supports( 'custom-header', 'video' ) ) {
					_e( '点击“新增图片”以从您的计算机上传图片文件。对应您的视频尺寸的图片最适合您的主题，您可以在上传后对图片进行裁剪以适应尺寸。' );
				} elseif ( $width && $height ) {
					printf(
						/* translators: %s: Header size in pixels. */
						__( '点击“新增图片”以从您的计算机上传图片文件。页眉图片尺寸为%s像素时最适合您的主题，您可以在上传后对图片进行裁剪以获得最佳尺寸。' ),
						sprintf( '<strong>%s &times; %s</strong>', $width, $height )
					);
				} elseif ( $width ) {
					printf(
						/* translators: %s: Header width in pixels. */
						__( '点击“新增图片”以从您的计算机上传图片文件。页眉图片宽度为%s像素时最适合您的主题，您可以在上传后对图片进行裁剪以获得最佳尺寸。' ),
						sprintf( '<strong>%s</strong>', $width )
					);
				} else {
					printf(
						/* translators: %s: Header height in pixels. */
						__( '点击“新增图片”以从您的计算机上传图片文件。页眉图片高度为%s像素时最适合您的主题，您可以在上传后对图片进行裁剪以获得最佳尺寸。' ),
						sprintf( '<strong>%s</strong>', $height )
					);
				}
				?>
			</p>
			<div class="current">
				<label for="header_image-button">
					<span class="customize-control-title">
						<?php _e( '当前页眉图片' ); ?>
					</span>
				</label>
				<div class="container">
				</div>
			</div>
			<div class="actions">
				<?php if ( current_user_can( 'upload_files' ) ) : ?>
				<button type="button"<?php echo $visibility; ?> class="button remove" aria-label="<?php esc_attr_e( '隐藏页眉图片' ); ?>"><?php _e( '隐藏图片' ); ?></button>
				<button type="button" class="button new" id="header_image-button" aria-label="<?php esc_attr_e( '添加新页眉图片' ); ?>"><?php _e( '新增图片' ); ?></button>
				<?php endif; ?>
			</div>
			<div class="choices">
				<span class="customize-control-title header-previously-uploaded">
					<?php _ex( '已经上传', 'custom headers' ); ?>
				</span>
				<div class="uploaded">
					<div class="list">
					</div>
				</div>
				<span class="customize-control-title header-default">
					<?php _ex( '建议的', 'custom headers' ); ?>
				</span>
				<div class="default">
					<div class="list">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
