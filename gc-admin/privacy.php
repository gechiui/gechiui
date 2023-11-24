<?php
/**
 * Privacy administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
$title = __( '隐私' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap about__container">

	<div class="about__section has-2-columns is-wider-right">
		<div class="column about__image">
			<img class="privacy-image" src="<?php echo esc_url( assets_url( '/images/privacy.svg' ) ); ?>" alt="" />
		</div>
		<div class="column is-vertically-aligned-center">
			<p><?php _e( '您的GeChiUI系统可能需要不时将数据发送到www.GeChiUI.com，数据内容包括但不限于您正在使用的GeChiUI版本、已安装的插件及主题列表等。' ); ?></p>

			<p>
				<?php _e( '这些数据会被用来改善GeChiUI，包括通过为您寻找并自动安装更新来保护您的系统。这些数据也会被用来计算统计，比如显示在<a href="%s">www.GeChiUI.com统计页面</a>上的数据。' ); ?>
			</p>
		</div>
	</div>

</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
