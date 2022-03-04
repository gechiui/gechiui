<?php
/**
 * The custom background script.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * The custom background class.
 *
 *
 */
class Custom_Background {

	/**
	 * Callback for administration header.
	 *
	 * @var callable
	 */
	public $admin_header_callback;

	/**
	 * Callback for header div.
	 *
	 * @var callable
	 */
	public $admin_image_div_callback;

	/**
	 * Used to trigger a success message when settings updated and set to true.
	 *
	 * @var bool
	 */
	private $updated;

	/**
	 * Constructor - Register administration header callback.
	 *
	 * @param callable $admin_header_callback
	 * @param callable $admin_image_div_callback Optional custom image div output callback.
	 */
	public function __construct( $admin_header_callback = '', $admin_image_div_callback = '' ) {
		$this->admin_header_callback    = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;

		add_action( 'admin_menu', array( $this, 'init' ) );

		add_action( 'gc_ajax_custom-background-add', array( $this, 'ajax_background_add' ) );

		// Unused since 3.5.0.
		add_action( 'gc_ajax_set-background-image', array( $this, 'gc_set_background_image' ) );
	}

	/**
	 * Set up the hooks for the Custom Background admin page.
	 *
	 */
	public function init() {
		$page = add_theme_page( __( '背景' ), __( '背景' ), 'edit_theme_options', 'custom-background', array( $this, 'admin_page' ) );
		if ( ! $page ) {
			return;
		}

		add_action( "load-{$page}", array( $this, 'admin_load' ) );
		add_action( "load-{$page}", array( $this, 'take_action' ), 49 );
		add_action( "load-{$page}", array( $this, 'handle_upload' ), 49 );

		if ( $this->admin_header_callback ) {
			add_action( "admin_head-{$page}", $this->admin_header_callback, 51 );
		}
	}

	/**
	 * Set up the enqueue for the CSS & JavaScript files.
	 *
	 */
	public function admin_load() {
		get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( '概述' ),
				'content' =>
					'<p>' . __( '通过自定义背景，您无须修改模板的任何代码，就可使您的站点外观焕然一新！背景可以是图片或某种色彩。' ) . '</p>' .
					'<p>' . __( '要使用背景图片，您需先上传，或从媒体库中已上传过的图片中选择。您可选择令其单次显示，或平铺多次以充满屏幕。您还可以使背景固定不动，站点的内容在其上滚动。也可让背景与站点内容同步滚动。' ) . '</p>' .
					'<p>' . __( '颜色可以通过点击“选择颜色”来使用选色器选择，或者手工输入HTML十六进制颜色代码（如红色“#ff0000”）。' ) . '</p>' .
					'<p>' . __( '请不要忘记在完成时点击“保存更改”按钮。' ) . '</p>',
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://codex.gechiui.com/Appearance_Background_Screen">自定义背景文档</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
		);

		gc_enqueue_media();
		gc_enqueue_script( 'custom-background' );
		gc_enqueue_style( 'gc-color-picker' );
	}

	/**
	 * Execute custom background modification.
	 *
	 */
	public function take_action() {
		if ( empty( $_POST ) ) {
			return;
		}

		if ( isset( $_POST['reset-background'] ) ) {
			check_admin_referer( 'custom-background-reset', '_gcnonce-custom-background-reset' );

			remove_theme_mod( 'background_image' );
			remove_theme_mod( 'background_image_thumb' );

			$this->updated = true;
			return;
		}

		if ( isset( $_POST['remove-background'] ) ) {
			// @todo Uploaded files are not removed here.
			check_admin_referer( 'custom-background-remove', '_gcnonce-custom-background-remove' );

			set_theme_mod( 'background_image', '' );
			set_theme_mod( 'background_image_thumb', '' );

			$this->updated = true;
			gc_safe_redirect( $_POST['_gc_http_referer'] );
			return;
		}

		if ( isset( $_POST['background-preset'] ) ) {
			check_admin_referer( 'custom-background' );

			if ( in_array( $_POST['background-preset'], array( 'default', 'fill', 'fit', 'repeat', 'custom' ), true ) ) {
				$preset = $_POST['background-preset'];
			} else {
				$preset = 'default';
			}

			set_theme_mod( 'background_preset', $preset );
		}

		if ( isset( $_POST['background-position'] ) ) {
			check_admin_referer( 'custom-background' );

			$position = explode( ' ', $_POST['background-position'] );

			if ( in_array( $position[0], array( 'left', 'center', 'right' ), true ) ) {
				$position_x = $position[0];
			} else {
				$position_x = 'left';
			}

			if ( in_array( $position[1], array( 'top', 'center', 'bottom' ), true ) ) {
				$position_y = $position[1];
			} else {
				$position_y = 'top';
			}

			set_theme_mod( 'background_position_x', $position_x );
			set_theme_mod( 'background_position_y', $position_y );
		}

		if ( isset( $_POST['background-size'] ) ) {
			check_admin_referer( 'custom-background' );

			if ( in_array( $_POST['background-size'], array( 'auto', 'contain', 'cover' ), true ) ) {
				$size = $_POST['background-size'];
			} else {
				$size = 'auto';
			}

			set_theme_mod( 'background_size', $size );
		}

		if ( isset( $_POST['background-repeat'] ) ) {
			check_admin_referer( 'custom-background' );

			$repeat = $_POST['background-repeat'];

			if ( 'no-repeat' !== $repeat ) {
				$repeat = 'repeat';
			}

			set_theme_mod( 'background_repeat', $repeat );
		}

		if ( isset( $_POST['background-attachment'] ) ) {
			check_admin_referer( 'custom-background' );

			$attachment = $_POST['background-attachment'];

			if ( 'fixed' !== $attachment ) {
				$attachment = 'scroll';
			}

			set_theme_mod( 'background_attachment', $attachment );
		}

		if ( isset( $_POST['background-color'] ) ) {
			check_admin_referer( 'custom-background' );

			$color = preg_replace( '/[^0-9a-fA-F]/', '', $_POST['background-color'] );

			if ( strlen( $color ) === 6 || strlen( $color ) === 3 ) {
				set_theme_mod( 'background_color', $color );
			} else {
				set_theme_mod( 'background_color', '' );
			}
		}

		$this->updated = true;
	}

	/**
	 * Display the custom background page.
	 *
	 */
	public function admin_page() {
		?>
<div class="wrap" id="custom-background">
<h1><?php _e( '自定义背景' ); ?></h1>

		<?php if ( current_user_can( 'customize' ) ) { ?>
<div class="notice notice-info hide-if-no-customize">
	<p>
			<?php
			printf(
				/* translators: %s: URL to background image configuration in Customizer. */
				__( '您现在可在<a href="%s">定制器</a>中管理并实时预览自定义背景。' ),
				admin_url( 'customize.php?autofocus[control]=background_image' )
			);
			?>
	</p>
</div>
		<?php } ?>

		<?php if ( ! empty( $this->updated ) ) { ?>
<div id="message" class="updated">
	<p>
			<?php
			/* translators: %s: Home URL. */
			printf( __( '背景已更新。<a href="%s">访问您的站点</a>来看看效果。' ), home_url( '/' ) );
			?>
	</p>
</div>
		<?php } ?>

<h2><?php _e( '背景图片' ); ?></h2>

<table class="form-table" role="presentation">
<tbody>
<tr>
<th scope="row"><?php _e( '预览' ); ?></th>
<td>
		<?php
		if ( $this->admin_image_div_callback ) {
			call_user_func( $this->admin_image_div_callback );
		} else {
			$background_styles = '';
			$bgcolor           = get_background_color();
			if ( $bgcolor ) {
				$background_styles .= 'background-color: #' . $bgcolor . ';';
			}

			$background_image_thumb = get_background_image();
			if ( $background_image_thumb ) {
				$background_image_thumb = esc_url( set_url_scheme( get_theme_mod( 'background_image_thumb', str_replace( '%', '%%', $background_image_thumb ) ) ) );
				$background_position_x  = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
				$background_position_y  = get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) );
				$background_size        = get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) );
				$background_repeat      = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
				$background_attachment  = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );

				// Background-image URL must be single quote, see below.
				$background_styles .= " background-image: url('$background_image_thumb');"
				. " background-size: $background_size;"
				. " background-position: $background_position_x $background_position_y;"
				. " background-repeat: $background_repeat;"
				. " background-attachment: $background_attachment;";
			}
			?>
	<div id="custom-background-image" style="<?php echo $background_styles; ?>"><?php // Must be double quote, see above. ?>
			<?php if ( $background_image_thumb ) { ?>
		<img class="custom-background-image" src="<?php echo $background_image_thumb; ?>" style="visibility:hidden;" alt="" /><br />
		<img class="custom-background-image" src="<?php echo $background_image_thumb; ?>" style="visibility:hidden;" alt="" />
		<?php } ?>
	</div>
	<?php } ?>
</td>
</tr>

		<?php if ( get_background_image() ) : ?>
<tr>
<th scope="row"><?php _e( '移除图片' ); ?></th>
<td>
<form method="post">
			<?php gc_nonce_field( 'custom-background-remove', '_gcnonce-custom-background-remove' ); ?>
			<?php submit_button( __( '移除背景图片' ), '', 'remove-background', false ); ?><br/>
			<?php _e( '这将移除背景图片。之后，您将无法还原任何自定义元素。' ); ?>
</form>
</td>
</tr>
		<?php endif; ?>

		<?php $default_image = get_theme_support( 'custom-background', 'default-image' ); ?>
		<?php if ( $default_image && get_background_image() !== $default_image ) : ?>
<tr>
<th scope="row"><?php _e( '还原原始图片' ); ?></th>
<td>
<form method="post">
			<?php gc_nonce_field( 'custom-background-reset', '_gcnonce-custom-background-reset' ); ?>
			<?php submit_button( __( '还原原始图片' ), '', 'reset-background', false ); ?><br/>
			<?php _e( '这将恢复原始背景图图片。您将无法还原任何自定义元素。' ); ?>
</form>
</td>
</tr>
		<?php endif; ?>

		<?php if ( current_user_can( 'upload_files' ) ) : ?>
<tr>
<th scope="row"><?php _e( '选择图片' ); ?></th>
<td><form enctype="multipart/form-data" id="upload-form" class="gc-upload-form" method="post">
	<p>
		<label for="upload"><?php _e( '从您的计算机中选择图片：' ); ?></label><br />
		<input type="file" id="upload" name="import" />
		<input type="hidden" name="action" value="save" />
			<?php gc_nonce_field( 'custom-background-upload', '_gcnonce-custom-background-upload' ); ?>
			<?php submit_button( __( '上传' ), '', 'submit', false ); ?>
	</p>
	<p>
		<label for="choose-from-library-link"><?php _e( '或从您的媒体库中选择图片：' ); ?></label><br />
		<button id="choose-from-library-link" class="button"
			data-choose="<?php esc_attr_e( '选择背景图片' ); ?>"
			data-update="<?php esc_attr_e( '设为背景' ); ?>"><?php _e( '选择图片' ); ?></button>
	</p>
	</form>
</td>
</tr>
		<?php endif; ?>
</tbody>
</table>

<h2><?php _e( '显示选项' ); ?></h2>
<form method="post">
<table class="form-table" role="presentation">
<tbody>
		<?php if ( get_background_image() ) : ?>
<input name="background-preset" type="hidden" value="custom">

			<?php
			$background_position = sprintf(
				'%s %s',
				get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) ),
				get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) )
			);

			$background_position_options = array(
				array(
					'left top'   => array(
						'label' => __( '左上角' ),
						'icon'  => 'dashicons dashicons-arrow-left-alt',
					),
					'center top' => array(
						'label' => __( '顶部' ),
						'icon'  => 'dashicons dashicons-arrow-up-alt',
					),
					'right top'  => array(
						'label' => __( '右上角' ),
						'icon'  => 'dashicons dashicons-arrow-right-alt',
					),
				),
				array(
					'left center'   => array(
						'label' => __( '左' ),
						'icon'  => 'dashicons dashicons-arrow-left-alt',
					),
					'center center' => array(
						'label' => __( '中' ),
						'icon'  => 'background-position-center-icon',
					),
					'right center'  => array(
						'label' => __( '右' ),
						'icon'  => 'dashicons dashicons-arrow-right-alt',
					),
				),
				array(
					'left bottom'   => array(
						'label' => __( '左下角' ),
						'icon'  => 'dashicons dashicons-arrow-left-alt',
					),
					'center bottom' => array(
						'label' => __( '底部' ),
						'icon'  => 'dashicons dashicons-arrow-down-alt',
					),
					'right bottom'  => array(
						'label' => __( '右下角' ),
						'icon'  => 'dashicons dashicons-arrow-right-alt',
					),
				),
			);
			?>
<tr>
<th scope="row"><?php _e( '图片位置' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '图片位置' ); ?></span></legend>
<div class="background-position-control">
			<?php foreach ( $background_position_options as $group ) : ?>
	<div class="button-group">
				<?php foreach ( $group as $value => $input ) : ?>
		<label>
			<input class="ui-helper-hidden-accessible" name="background-position" type="radio" value="<?php echo esc_attr( $value ); ?>"<?php checked( $value, $background_position ); ?>>
			<span class="button display-options position"><span class="<?php echo esc_attr( $input['icon'] ); ?>" aria-hidden="true"></span></span>
			<span class="screen-reader-text"><?php echo $input['label']; ?></span>
		</label>
	<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>
</fieldset></td>
</tr>

<tr>
<th scope="row"><label for="background-size"><?php _e( '图片尺寸' ); ?></label></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '图片尺寸' ); ?></span></legend>
<select id="background-size" name="background-size">
<option value="auto"<?php selected( 'auto', get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) ) ); ?>><?php _ex( '原始', 'Original Size' ); ?></option>
<option value="contain"<?php selected( 'contain', get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) ) ); ?>><?php _e( '适合屏幕' ); ?></option>
<option value="cover"<?php selected( 'cover', get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) ) ); ?>><?php _e( '填满屏幕' ); ?></option>
</select>
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _ex( '重复', 'Background Repeat' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _ex( '重复', 'Background Repeat' ); ?></span></legend>
<input name="background-repeat" type="hidden" value="no-repeat">
<label><input type="checkbox" name="background-repeat" value="repeat"<?php checked( 'repeat', get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) ); ?>> <?php _e( '重复背景图片' ); ?></label>
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _ex( '滚动', 'Background Scroll' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _ex( '滚动', 'Background Scroll' ); ?></span></legend>
<input name="background-attachment" type="hidden" value="fixed">
<label><input name="background-attachment" type="checkbox" value="scroll" <?php checked( 'scroll', get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) ) ); ?>> <?php _e( '随页面滚动' ); ?></label>
</fieldset></td>
</tr>
<?php endif; // get_background_image() ?>
<tr>
<th scope="row"><?php _e( '背景颜色' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '背景颜色' ); ?></span></legend>
		<?php
		$default_color = '';
		if ( current_theme_supports( 'custom-background', 'default-color' ) ) {
			$default_color = ' data-default-color="#' . esc_attr( get_theme_support( 'custom-background', 'default-color' ) ) . '"';
		}
		?>
<input type="text" name="background-color" id="background-color" value="#<?php echo esc_attr( get_background_color() ); ?>"<?php echo $default_color; ?>>
</fieldset></td>
</tr>
</tbody>
</table>

		<?php gc_nonce_field( 'custom-background' ); ?>
		<?php submit_button( null, 'primary', 'save-background-options' ); ?>
</form>

</div>
		<?php
	}

	/**
	 * Handle an Image upload for the background image.
	 *
	 */
	public function handle_upload() {
		if ( empty( $_FILES ) ) {
			return;
		}

		check_admin_referer( 'custom-background-upload', '_gcnonce-custom-background-upload' );

		$overrides = array( 'test_form' => false );

		$uploaded_file = $_FILES['import'];
		$gc_filetype   = gc_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'] );
		if ( ! gc_match_mime_types( 'image', $gc_filetype['type'] ) ) {
			gc_die( __( '上传的文件不是有效的图片。请重试。' ) );
		}

		$file = gc_handle_upload( $uploaded_file, $overrides );

		if ( isset( $file['error'] ) ) {
			gc_die( $file['error'] );
		}

		$url      = $file['url'];
		$type     = $file['type'];
		$file     = $file['file'];
		$filename = gc_basename( $file );

		// Construct the object array.
		$object = array(
			'post_title'     => $filename,
			'post_content'   => $url,
			'post_mime_type' => $type,
			'guid'           => $url,
			'context'        => 'custom-background',
		);

		// Save the data.
		$id = gc_insert_attachment( $object, $file );

		// Add the metadata.
		gc_update_attachment_metadata( $id, gc_generate_attachment_metadata( $id, $file ) );
		update_post_meta( $id, '_gc_attachment_is_custom_background', get_option( 'stylesheet' ) );

		set_theme_mod( 'background_image', esc_url_raw( $url ) );

		$thumbnail = gc_get_attachment_image_src( $id, 'thumbnail' );
		set_theme_mod( 'background_image_thumb', esc_url_raw( $thumbnail[0] ) );

		/** This action is documented in gc-admin/includes/class-custom-image-header.php */
		do_action( 'gc_create_file_in_uploads', $file, $id ); // For replication.
		$this->updated = true;
	}

	/**
	 * Ajax handler for adding custom background context to an attachment.
	 *
	 * Triggers when the user adds a new background image from the
	 * Media Manager.
	 *
	 */
	public function ajax_background_add() {
		check_ajax_referer( 'background-add', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			gc_send_json_error();
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		if ( $attachment_id < 1 ) {
			gc_send_json_error();
		}

		update_post_meta( $attachment_id, '_gc_attachment_is_custom_background', get_stylesheet() );

		gc_send_json_success();
	}

	/**
	 * @deprecated 3.5.0
	 *
	 * @param array $form_fields
	 * @return array $form_fields
	 */
	public function attachment_fields_to_edit( $form_fields ) {
		return $form_fields;
	}

	/**
	 * @deprecated 3.5.0
	 *
	 * @param array $tabs
	 * @return array $tabs
	 */
	public function filter_upload_tabs( $tabs ) {
		return $tabs;
	}

	/**
	 * @deprecated 3.5.0
	 */
	public function gc_set_background_image() {
		check_ajax_referer( 'custom-background' );

		if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST['attachment_id'] ) ) {
			exit;
		}

		$attachment_id = absint( $_POST['attachment_id'] );

		$sizes = array_keys(
			/** This filter is documented in gc-admin/includes/media.php */
			apply_filters(
				'image_size_names_choose',
				array(
					'thumbnail' => __( '缩略图' ),
					'medium'    => __( '中等' ),
					'large'     => __( '大' ),
					'full'      => __( '全尺寸' ),
				)
			)
		);

		$size = 'thumbnail';
		if ( in_array( $_POST['size'], $sizes, true ) ) {
			$size = esc_attr( $_POST['size'] );
		}

		update_post_meta( $attachment_id, '_gc_attachment_is_custom_background', get_option( 'stylesheet' ) );

		$url       = gc_get_attachment_image_src( $attachment_id, $size );
		$thumbnail = gc_get_attachment_image_src( $attachment_id, 'thumbnail' );
		set_theme_mod( 'background_image', esc_url_raw( $url[0] ) );
		set_theme_mod( 'background_image_thumb', esc_url_raw( $thumbnail[0] ) );
		exit;
	}
}
