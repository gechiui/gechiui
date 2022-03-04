<?php
/**
 * Install theme administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';
require ABSPATH . 'gc-admin/includes/theme-install.php';

gc_reset_vars( array( 'tab' ) );

if ( ! current_user_can( 'install_themes' ) ) {
	gc_die( __( '抱歉，您不能在此站点上安装主题。' ) );
}

if ( is_multisite() && ! is_network_admin() ) {
	gc_redirect( network_admin_url( 'theme-install.php' ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( '添加主题' );
$parent_file = 'themes.php';

if ( ! is_network_admin() ) {
	$submenu_file = 'themes.php';
}

$installed_themes = search_theme_directories();

if ( false === $installed_themes ) {
	$installed_themes = array();
}

foreach ( $installed_themes as $k => $v ) {
	if ( false !== strpos( $k, '/' ) ) {
		unset( $installed_themes[ $k ] );
	}
}

gc_localize_script(
	'theme',
	'_gcThemeSettings',
	array(
		'themes'          => false,
		'settings'        => array(
			'isInstall'  => true,
			'canInstall' => current_user_can( 'install_themes' ),
			'installURI' => current_user_can( 'install_themes' ) ? self_admin_url( 'theme-install.php' ) : null,
			'adminUrl'   => parse_url( self_admin_url(), PHP_URL_PATH ),
		),
		'l10n'            => array(
			'addNew'              => __( '添加新主题' ),
			'search'              => __( '搜索主题' ),
			'searchPlaceholder'   => __( '搜索主题…' ), // Placeholder (no ellipsis).
			'upload'              => __( '上传主题' ),
			'back'                => __( '返回' ),
			'error'               => sprintf(
				/* translators: %s: Support forums URL. */
				__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			),
			'tryAgain'            => __( '重试' ),
			/* translators: %d: Number of themes. */
			'themesFound'         => __( '找到的主题数：%d' ),
			'noThemesFound'       => __( '未找到主题，请重新搜索。' ),
			'collapseSidebar'     => __( '折叠边栏' ),
			'expandSidebar'       => __( '展开侧栏' ),
			/* translators: Accessibility text. */
			'selectFeatureFilter' => __( '选择一个或多个主题特征进行筛选' ),
		),
		'installedThemes' => array_keys( $installed_themes ),
		'activeTheme'     => get_stylesheet(),
	)
);

gc_enqueue_script( 'theme' );
gc_enqueue_script( 'updates' );

if ( $tab ) {
	/**
	 * Fires before each of the tabs are rendered on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab.
	 *
	 * Possible hook names include:
	 *
	 *  - `install_themes_pre_dashboard`
	 *  - `install_themes_pre_featured`
	 *  - `install_themes_pre_new`
	 *  - `install_themes_pre_search`
	 *  - `install_themes_pre_updated`
	 *  - `install_themes_pre_upload`
	 *
	 */
	do_action( "install_themes_pre_{$tab}" );
}

$help_overview =
	'<p>' . sprintf(
		/* translators: %s: Theme Directory URL. */
		__( '通过主题浏览/安装器，您可以为您的站点安装其他主题。主题浏览/安装器显示的是来自<a href="%s">www.GeChiUI.com主题目录</a>的主题。这些主题都是由第三方设计开发并免费提供的，且与GeChiUI所用的许可证相兼容。' ),
		__( 'https://www.gechiui.com/themes/' )
	) . '</p>' .
	'<p>' . __( '您可以使用关键词、作者名称或标签搜索主题，也可以更具体地按特性筛选器中所列出的条件进行搜索。' ) . ' <span id="live-search-desc">' . __( '搜索结果会随着您的输入而不断更新。' ) . '</span></p>' .
	'<p>' . __( '除此以外，您可以浏览热门和最新的主题。找到您喜欢的主题后，您可以预览或安装它。' ) . '</p>' .
	'<p>' . sprintf(
		/* translators: %s: /gc-content/themes */
		__( '您可以手动上传从别处下载的.zip主题压缩包文件（请确认其来源可靠）；也可以使用传统方式，将下载的主题文件通过FTP拷贝至您网站的%s目录中。' ),
		'<code>/gc-content/themes</code>'
	) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $help_overview,
	)
);

$help_installing =
	'<p>' . __( '在主题列表生成后，您可以预览或安装其中任意一个。点击您感兴趣主题的缩略图，可打开新页面全屏预览，让您更了解启用该主题后的实际外观。' ) . '</p>' .
	'<p>' . __( '要安装的主题，以便您可以使用网站内容预览它，并自定义其主题选项，请单击左侧窗格顶部的“安装”按钮。主题文件将自动下载到您的网站。完成后，主题可以立即激活，您可以通过单击“激活”链接，或转到管理主题屏幕并单击“实时预览”任何已安装主题的缩略图下的链接。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'installing',
		'title'   => __( '预览并安装' ),
		'content' => $help_installing,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/appearance-themes-screen/#install-themes">Documentation on Adding New Themes</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<div class="wrap">
	<h1 class="gc-heading-inline"><?php echo esc_html( $title ); ?></h1>

	<?php

	/**
	 * Filters the tabs shown on the Add Themes screen.
	 *
	 * This filter is for backward compatibility only, for the suppression of the upload tab.
	 *
	 *
	 * @param string[] $tabs Associative array of the tabs shown on the Add Themes screen. Default is 'upload'.
	 */
	$tabs = apply_filters( 'install_themes_tabs', array( 'upload' => __( '上传主题' ) ) );
	if ( ! empty( $tabs['upload'] ) && current_user_can( 'upload_themes' ) ) {
		echo ' <button type="button" class="upload-view-toggle page-title-action hide-if-no-js" aria-expanded="false">' . __( '上传主题' ) . '</button>';
	}
	?>

	<hr class="gc-header-end">

	<div class="error hide-if-js">
		<p><?php _e( '主题安装器需要JavaScript支持。' ); ?></p>
	</div>

	<div class="upload-theme">
	<?php install_themes_upload(); ?>
	</div>

	<h2 class="screen-reader-text hide-if-no-js"><?php _e( '筛选主题列表' ); ?></h2>

	<div class="gc-filter hide-if-no-js">
		<div class="filter-count">
			<span class="count theme-count"></span>
		</div>

		<ul class="filter-links">
			<li><a href="#" data-sort="popular"><?php _ex( '热门', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="new"><?php _ex( '最新', 'themes' ); ?></a></li>
		</ul>


		<form class="search-form"></form>

	</div>
	<h2 class="screen-reader-text hide-if-no-js"><?php _e( '主题列表' ); ?></h2>
	<div class="theme-browser content-filterable"></div>
	<div class="theme-install-overlay gc-full-overlay expanded"></div>

	<p class="no-themes"><?php _e( '未找到主题，请重新搜索。' ); ?></p>
	<span class="spinner"></span>

<?php
if ( $tab ) {
	/**
	 * Fires at the top of each of the tabs on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab.
	 *
	 * Possible hook names include:
	 *
	 *  - `install_themes_dashboard`
	 *  - `install_themes_featured`
	 *  - `install_themes_new`
	 *  - `install_themes_search`
	 *  - `install_themes_updated`
	 *  - `install_themes_upload`
	 *
	 *
	 * @param int $paged Number of the current page of results being viewed.
	 */
	do_action( "install_themes_{$tab}", $paged );
}
?>
</div>

<script id="tmpl-theme" type="text/template">
	<# if ( data.screenshot_url ) { #>
		<div class="theme-screenshot">
			<img src="{{ data.screenshot_url }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>

	<# if ( data.installed ) { #>
		<div class="notice notice-success notice-alt"><p><?php _ex( '已安装', 'theme' ); ?></p></div>
	<# } #>

	<# if ( ! data.compatible_gc || ! data.compatible_php ) { #>
		<div class="notice notice-error notice-alt"><p>
			<# if ( ! data.compatible_gc && ! data.compatible_php ) { #>
				<?php
				_e( '此主题不能与您的GeChiUI和PHP版本一同工作。' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					printf(
						/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
						' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
						self_admin_url( 'update-core.php' ),
						esc_url( gc_get_update_php_url() )
					);
					gc_update_php_annotation( '</p><p><em>', '</em>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to GeChiUI Updates screen. */
						' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
						self_admin_url( 'update-core.php' )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
						esc_url( gc_get_update_php_url() )
					);
					gc_update_php_annotation( '</p><p><em>', '</em>' );
				}
				?>
			<# } else if ( ! data.compatible_gc ) { #>
				<?php
				_e( '此主题未适配当前GeChiUI版本。' );
				if ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to GeChiUI Updates screen. */
						' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
						self_admin_url( 'update-core.php' )
					);
				}
				?>
			<# } else if ( ! data.compatible_php ) { #>
				<?php
				_e( '此主题未适配当前PHP版本。' );
				if ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
						esc_url( gc_get_update_php_url() )
					);
					gc_update_php_annotation( '</p><p><em>', '</em>' );
				}
				?>
			<# } #>
		</p></div>
	<# } #>

	<span class="more-details"><?php _ex( '详情及预览', 'theme' ); ?></span>
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name. */
		printf( __( '作者：%s' ), '{{ data.author }}' );
		?>
	</div>

	<div class="theme-id-container">
		<h3 class="theme-name">{{ data.name }}</h3>

		<div class="theme-actions">
			<# if ( data.installed ) { #>
				<# if ( data.compatible_gc && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.activate_url ) { #>
						<# if ( ! data.active ) { #>
							<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
						<# } else { #>
							<button class="button button-primary disabled"><?php _ex( '已启用', 'theme' ); ?></button>
						<# } #>
					<# } #>
					<# if ( data.customize_url ) { #>
						<# if ( ! data.active ) { #>
							<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( '实时预览' ); ?></a>
						<# } else { #>
							<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( '自定义' ); ?></a>
						<# } #>
					<# } else { #>
						<button class="button preview install-theme-preview"><?php _e( '预览' ); ?></button>
					<# } #>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '无法启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.activate_url ) { #>
						<a class="button button-primary disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( '无法启用', 'theme' ); ?></a>
					<# } #>
					<# if ( data.customize_url ) { #>
						<a class="button disabled"><?php _e( '实时预览' ); ?></a>
					<# } else { #>
						<button class="button disabled"><?php _e( '预览' ); ?></button>
					<# } #>
				<# } #>
			<# } else { #>
				<# if ( data.compatible_gc && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '安装%s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}" href="{{ data.install_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '安装' ); ?></a>
					<button class="button preview install-theme-preview"><?php _e( '预览' ); ?></button>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '无法安装%s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button button-primary disabled" data-name="{{ data.name }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( '无法安装', 'theme' ); ?></a>
					<button class="button disabled"><?php _e( '预览' ); ?></button>
				<# } #>
			<# } #>
		</div>
	</div>
</script>

<script id="tmpl-theme-preview" type="text/template">
	<div class="gc-full-overlay-sidebar">
		<div class="gc-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text"><?php _e( '关闭' ); ?></span></button>
			<button class="previous-theme"><span class="screen-reader-text"><?php _e( '上一个主题' ); ?></span></button>
			<button class="next-theme"><span class="screen-reader-text"><?php _e( '下一个主题' ); ?></span></button>
			<# if ( data.installed ) { #>
				<# if ( data.compatible_gc && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( ! data.active ) { #>
						<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
					<# } else { #>
						<button class="button button-primary disabled"><?php _ex( '已启用', 'theme' ); ?></button>
					<# } #>
				<# } else { #>
					<a class="button button-primary disabled" ><?php _ex( '无法启用', 'theme' ); ?></a>
				<# } #>
			<# } else { #>
				<# if ( data.compatible_gc && data.compatible_php ) { #>
					<a href="{{ data.install_url }}" class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}"><?php _e( '安装' ); ?></a>
				<# } else { #>
					<a class="button button-primary disabled" ><?php _ex( '无法安装', 'theme' ); ?></a>
				<# } #>
			<# } #>
		</div>
		<div class="gc-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.name }}</h3>
					<span class="theme-by">
						<?php
						/* translators: %s: Theme author name. */
						printf( __( '作者：%s' ), '{{ data.author }}' );
						?>
					</span>

					<img class="theme-screenshot" src="{{ data.screenshot_url }}" alt="" />

					<div class="theme-details">
						<# if ( data.rating ) { #>
							<div class="theme-rating">
								{{{ data.stars }}}
								<a class="num-ratings" href="{{ data.reviews_url }}">
									<?php
									/* translators: %s: Number of ratings. */
									printf( __( '（%s个评级）' ), '{{ data.num_ratings }}' );
									?>
								</a>
							</div>
						<# } else { #>
							<span class="no-rating"><?php _e( '此主题未被评级。' ); ?></span>
						<# } #>

						<div class="theme-version">
							<?php
							/* translators: %s: Theme version. */
							printf( __( '版本：%s' ), '{{ data.version }}' );
							?>
						</div>

						<# if ( ! data.compatible_gc || ! data.compatible_php ) { #>
							<div class="notice notice-error notice-alt notice-large"><p>
								<# if ( ! data.compatible_gc && ! data.compatible_php ) { #>
									<?php
									_e( '此主题不能与您的GeChiUI和PHP版本一同工作。' );
									if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
										printf(
											/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
											' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
											self_admin_url( 'update-core.php' ),
											esc_url( gc_get_update_php_url() )
										);
										gc_update_php_annotation( '</p><p><em>', '</em>' );
									} elseif ( current_user_can( 'update_core' ) ) {
										printf(
											/* translators: %s: URL to GeChiUI Updates screen. */
											' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
											self_admin_url( 'update-core.php' )
										);
									} elseif ( current_user_can( 'update_php' ) ) {
										printf(
											/* translators: %s: URL to Update PHP page. */
											' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
											esc_url( gc_get_update_php_url() )
										);
										gc_update_php_annotation( '</p><p><em>', '</em>' );
									}
									?>
								<# } else if ( ! data.compatible_gc ) { #>
									<?php
									_e( '此主题未适配当前GeChiUI版本。' );
									if ( current_user_can( 'update_core' ) ) {
										printf(
											/* translators: %s: URL to GeChiUI Updates screen. */
											' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
											self_admin_url( 'update-core.php' )
										);
									}
									?>
								<# } else if ( ! data.compatible_php ) { #>
									<?php
									_e( '此主题未适配当前PHP版本。' );
									if ( current_user_can( 'update_php' ) ) {
										printf(
											/* translators: %s: URL to Update PHP page. */
											' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
											esc_url( gc_get_update_php_url() )
										);
										gc_update_php_annotation( '</p><p><em>', '</em>' );
									}
									?>
								<# } #>
							</p></div>
						<# } #>

						<div class="theme-description">{{{ data.description }}}</div>
					</div>
				</div>
			</div>
			<div class="gc-full-overlay-footer">
				<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="<?php esc_attr_e( '折叠边栏' ); ?>">
					<span class="collapse-sidebar-arrow"></span>
					<span class="collapse-sidebar-label"><?php _e( '收起' ); ?></span>
				</button>
			</div>
		</div>
		<div class="gc-full-overlay-main">
		<iframe src="{{ data.preview_url }}" title="<?php esc_attr_e( '预览' ); ?>"></iframe>
	</div>
</script>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
