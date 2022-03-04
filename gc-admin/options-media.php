<?php
/**
 * Media settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	gc_die( __( '抱歉，您不能管理此站点的选项。' ) );
}

// Used in the HTML title tag.
$title       = __( '媒体设置' );
$parent_file = 'options-general.php';

$media_options_help = '<p>' . __( '您可以为插入文中的图片设置最大尺寸。亦可以“全尺寸”插入图片。' ) . '</p>';

if ( ! is_multisite()
	&& ( get_option( 'upload_url_path' )
		|| get_option( 'upload_path' ) && 'gc-content/uploads' !== get_option( 'upload_path' ) )
) {
	$media_options_help .= '<p>' . __( '“文件上传”的内容决定存放您上传文件的目录和路径。' ) . '</p>';
}

$media_options_help .= '<p>' . __( '调整完成后，记得点击页面下方“保存更改”按钮使设置生效。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $media_options_help,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-media-screen/">媒体设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form action="options.php" method="post">
<?php settings_fields( 'media' ); ?>

<h2 class="title"><?php _e( '图片大小' ); ?></h2>
<p><?php _e( '下面列出来的尺寸决定插入媒体库内的图片之最大尺寸。以像素为单位。' ); ?></p>

<table class="form-table" role="presentation">
<tr>
<th scope="row"><?php _e( '缩略图大小' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '缩略图大小' ); ?></span></legend>
<label for="thumbnail_size_w"><?php _e( '宽度' ); ?></label>
<input name="thumbnail_size_w" type="number" step="1" min="0" id="thumbnail_size_w" value="<?php form_option( 'thumbnail_size_w' ); ?>" class="small-text" />
<br />
<label for="thumbnail_size_h"><?php _e( '高度' ); ?></label>
<input name="thumbnail_size_h" type="number" step="1" min="0" id="thumbnail_size_h" value="<?php form_option( 'thumbnail_size_h' ); ?>" class="small-text" />
</fieldset>
<input name="thumbnail_crop" type="checkbox" id="thumbnail_crop" value="1" <?php checked( '1', get_option( 'thumbnail_crop' ) ); ?>/>
<label for="thumbnail_crop"><?php _e( '总是裁剪缩略图到这个尺寸（一般情况下，缩略图应保持原始比例）' ); ?></label>
</td>
</tr>

<tr>
<th scope="row"><?php _e( '中等大小' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '中等大小' ); ?></span></legend>
<label for="medium_size_w"><?php _e( '最大宽度' ); ?></label>
<input name="medium_size_w" type="number" step="1" min="0" id="medium_size_w" value="<?php form_option( 'medium_size_w' ); ?>" class="small-text" />
<br />
<label for="medium_size_h"><?php _e( '最大高度' ); ?></label>
<input name="medium_size_h" type="number" step="1" min="0" id="medium_size_h" value="<?php form_option( 'medium_size_h' ); ?>" class="small-text" />
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _e( '大尺寸' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '大尺寸' ); ?></span></legend>
<label for="large_size_w"><?php _e( '最大宽度' ); ?></label>
<input name="large_size_w" type="number" step="1" min="0" id="large_size_w" value="<?php form_option( 'large_size_w' ); ?>" class="small-text" />
<br />
<label for="large_size_h"><?php _e( '最大高度' ); ?></label>
<input name="large_size_h" type="number" step="1" min="0" id="large_size_h" value="<?php form_option( 'large_size_h' ); ?>" class="small-text" />
</fieldset></td>
</tr>

<?php do_settings_fields( 'media', 'default' ); ?>
</table>

<?php
/**
 * @global array $gc_settings
 */
if ( isset( $GLOBALS['gc_settings']['media']['embeds'] ) ) :
	?>
<h2 class="title"><?php _e( '嵌入' ); ?></h2>
<table class="form-table" role="presentation">
	<?php do_settings_fields( 'media', 'embeds' ); ?>
</table>
<?php endif; ?>

<?php if ( ! is_multisite() ) : ?>
<h2 class="title"><?php _e( '文件上传' ); ?></h2>
<table class="form-table" role="presentation">
	<?php
	/*
	 * If upload_url_path is not the default (empty),
	 * or upload_path is not the default ('gc-content/uploads' or empty),
	 * they can be edited, otherwise they're locked.
	 */
	if ( get_option( 'upload_url_path' )
		|| get_option( 'upload_path' ) && 'gc-content/uploads' !== get_option( 'upload_path' ) ) :
		?>
<tr>
<th scope="row"><label for="upload_path"><?php _e( '默认上传路径' ); ?></label></th>
<td><input name="upload_path" type="text" id="upload_path" value="<?php echo esc_attr( get_option( 'upload_path' ) ); ?>" class="regular-text code" />
<p class="描述">
		<?php
		/* translators: %s: gc-content/uploads */
		printf( __( '缺省为%s' ), '<code>gc-content/uploads</code>' );
		?>
</p>
</td>
</tr>

<tr>
<th scope="row"><label for="upload_url_path"><?php _e( '文件的完整URL地址' ); ?></label></th>
<td><input name="upload_url_path" type="text" id="upload_url_path" value="<?php echo esc_attr( get_option( 'upload_url_path' ) ); ?>" class="regular-text code" />
<p class="描述"><?php _e( '可选配置，默认留空。' ); ?></p>
</td>
</tr>
<tr>
<td colspan="2" class="td-full">
<?php else : ?>
<tr>
<td class="td-full">
<?php endif; ?>
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1"<?php checked( '1', get_option( 'uploads_use_yearmonth_folders' ) ); ?> />
	<?php _e( '以年—月目录形式组织上传内容' ); ?>
</label>
</td>
</tr>

	<?php do_settings_fields( 'media', 'uploads' ); ?>
</table>
<?php endif; ?>

<?php do_settings_sections( 'media' ); ?>

<?php submit_button(); ?>

</form>

</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
