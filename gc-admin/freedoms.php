<?php
/**
 * Your Rights administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// This file was used to also display the Privacy tab on the About screen from 4.9.6 until 5.3.0.
if ( isset( $_GET['privacy-notice'] ) ) {
	gc_redirect( admin_url( 'privacy.php' ), 301 );
	exit;
}

// Used in the HTML title tag.
$title = __( '您的自由' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<?php _e( '四大自由' ); ?>
			</h1>
		</div>

		<div class="about__header-text">
			<?php _e( 'GeChiUI 是可自由使用的开源软件' ); ?>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper gc-clearfix" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
			<a href="about.php" class="nav-tab"><?php _e( '更新内容' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( '鸣谢' ); ?></a>
			<a href="freedoms.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( '您的自由' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( '隐私' ); ?></a>
		</nav>
	</div>

	<div class="about__section is-feature">
		<p class="about-description">
		<?php
		printf(
			/* translators: %s: https://www.gechiui.com/about/license/ */
			__( 'GeChiUI 附带了一些令人惊叹、改变人们对软件许可看法的权利，这要归功于其所使用的<a href="%s">GPL 许可证</a>。' ),
			__( 'https://www.gechiui.com/about/license/' )
		);
		?>
		</p>
	</div>

	<div class="about__section has-2-columns">
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-1.svg' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( '自由之一' ); ?></h2>
			<p><?php _e( '可将程序执行于任何用途。' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-2.svg' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( '自由之二' ); ?></h2>
			<p><?php _e( '研究程序的工作原理，并对其进行更改，以使其按您的意愿执行。' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-3.svg' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( '自由之三' ); ?></h2>
			<p><?php _e( '将程序重新分发。' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-4.svg' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( '自由之四' ); ?></h2>
			<p><?php _e( '将经过您修改的版本重新分发给其他人。' ); ?></p>
		</div>
	</div>

	<div class="about__section has-1-column">
		<div class="column">
			<p>
			<?php
			printf(
				/* translators: %s: https://gechiuifoundation.org/trademark-policy/ */
				__( 'GeChiUI凭借您的力量进行宣传，并发展壮大——每次您和朋友赞扬它，或者一些公司使用GeChiUI来制作公司网站，甚至创建一些服务时。我们喜欢被赞扬的感觉，不过希望您<a href="%s">关注我们的商标使用准则</a>。' ),
				'https://gechiuifoundation.org/trademark-policy/'
			);
			?>
			</p>

			<p>
			<?php
			$plugins_url = current_user_can( 'activate_plugins' ) ? admin_url( 'plugins.php' ) : __( 'https://www.gechiui.com/plugins/' );
			$themes_url  = current_user_can( 'switch_themes' ) ? admin_url( 'themes.php' ) : __( 'https://www.gechiui.com/themes/' );
			printf(
				/* translators: 1: URL to Plugins screen, 2: URL to Themes screen, 3: https://www.gechiui.com/about/license/ */
				__( '在www.GeChiUI.com插件和主题目录中的内容均完全遵循GPL或相似的相兼容的自由许可证发布，因此您可随意使用目录中的<a href="%1$s">插件</a>和<a href="%2$s">主题</a>。若您从别处得到了插件或主题，请先<a href="%3$s">询问它们是否遵循GPL</a>。若它们不遵循GeChiUI使用的许可证，我们不建议您使用。' ),
				$plugins_url,
				$themes_url,
				__( 'https://www.gechiui.com/about/license/' )
			);
			?>
			</p>
		</div>
	</div>

</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
