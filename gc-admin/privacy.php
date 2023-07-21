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

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<?php _e( '隐私' ); ?>
			</h1>
		</div>

		<div class="about__header-text">
			<?php _e( '我们非常重视隐私和透明度' ); ?>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper gc-clearfix" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
			<a href="about.php" class="nav-tab"><?php _e( '更新内容' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( '鸣谢' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( '您的自由' ); ?></a>
			<a href="privacy.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( '隐私' ); ?></a>
		</nav>
	</div>

	<div class="about__section has-2-columns is-wider-right">
		<div class="column about__image">
			<img class="privacy-image" src="<?php echo esc_url( assets_url( '/images/privacy.svg' ) ); ?>" alt="" />
		</div>
		<div class="column is-vertically-aligned-center">
			<p><?php _e( '您的GeChiUI站点可能需要不时将数据发送到www.GeChiUI.com，数据内容包括但不限于您正在使用的GeChiUI版本、已安装的插件及主题列表等。' ); ?></p>

			<p>
				<?php
				printf(
					/* translators: %s: https://www.gechiui.com/about/stats/ */
					__( '这些数据会被用来改善GeChiUI，包括通过为您寻找并自动安装更新来保护您的站点。这些数据也会被用来计算统计，比如显示在<a href="%s">www.GeChiUI.com统计页面</a>上的数据。' ),
					__( 'https://www.gechiui.com/about/stats/' )
				);
				?>
			</p>

			<p>
				<?php
				printf(
					/* translators: %s: https://www.gechiui.com/about/privacy/ */
					__( '我们非常重视隐私和透明度. To learn more about what data we collect, and how we use it, please visit <a href="%s">our Privacy Policy</a>.' ),
					__( 'https://www.gechiui.com/about/privacy/' )
				);
				?>
			</p>
		</div>
	</div>

</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
