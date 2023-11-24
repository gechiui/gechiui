<?php
/**
 * Themes administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此系统上编辑主题选项。' ) . '</p>',
		403
	);
}

if ( current_user_can( 'switch_themes' ) && isset( $_GET['action'] ) ) {
	if ( 'activate' === $_GET['action'] ) {
		check_admin_referer( 'switch-theme_' . $_GET['stylesheet'] );
		$theme = gc_get_theme( $_GET['stylesheet'] );

		if ( ! $theme->exists() || ! $theme->is_allowed() ) {
			gc_die(
				'<h1>' . __( '出现了问题。' ) . '</h1>' .
				'<p>' . __( '请求的主题不存在。' ) . '</p>',
				403
			);
		}

		switch_theme( $theme->get_stylesheet() );
		gc_redirect( admin_url( 'themes.php?activated=true' ) );
		exit;
	} elseif ( 'resume' === $_GET['action'] ) {
		check_admin_referer( 'resume-theme_' . $_GET['stylesheet'] );
		$theme = gc_get_theme( $_GET['stylesheet'] );

		if ( ! current_user_can( 'resume_theme', $_GET['stylesheet'] ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '抱歉，您不能恢复此主题。' ) . '</p>',
				403
			);
		}

		$result = resume_theme( $theme->get_stylesheet(), self_admin_url( 'themes.php?error=resuming' ) );

		if ( is_gc_error( $result ) ) {
			gc_die( $result );
		}

		gc_redirect( admin_url( 'themes.php?resumed=true' ) );
		exit;
	} elseif ( 'delete' === $_GET['action'] ) {
		check_admin_referer( 'delete-theme_' . $_GET['stylesheet'] );
		$theme = gc_get_theme( $_GET['stylesheet'] );

		if ( ! current_user_can( 'delete_themes' ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '抱歉，您不能删除此项目。' ) . '</p>',
				403
			);
		}

		if ( ! $theme->exists() ) {
			gc_die(
				'<h1>' . __( '出现了问题。' ) . '</h1>' .
				'<p>' . __( '请求的主题不存在。' ) . '</p>',
				403
			);
		}

		$active = gc_get_theme();
		if ( $active->get( 'Template' ) === $_GET['stylesheet'] ) {
			gc_redirect( admin_url( 'themes.php?delete-active-child=true' ) );
		} else {
			delete_theme( $_GET['stylesheet'] );
			gc_redirect( admin_url( 'themes.php?deleted=true' ) );
		}
		exit;
	} elseif ( 'enable-auto-update' === $_GET['action'] ) {
		if ( ! ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) ) {
			gc_die( __( '抱歉，您不能启用主题自动更新。' ) );
		}

		check_admin_referer( 'updates' );

		$all_items    = gc_get_themes();
		$auto_updates = (array) get_site_option( 'auto_update_themes', array() );

		$auto_updates[] = $_GET['stylesheet'];
		$auto_updates   = array_unique( $auto_updates );
		// Remove themes that have been deleted since the site option was last updated.
		$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

		update_site_option( 'auto_update_themes', $auto_updates );

		gc_redirect( admin_url( 'themes.php?enabled-auto-update=true' ) );

		exit;
	} elseif ( 'disable-auto-update' === $_GET['action'] ) {
		if ( ! ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) ) {
			gc_die( __( '抱歉，您不能禁用主题自动更新。' ) );
		}

		check_admin_referer( 'updates' );

		$all_items    = gc_get_themes();
		$auto_updates = (array) get_site_option( 'auto_update_themes', array() );

		$auto_updates = array_diff( $auto_updates, array( $_GET['stylesheet'] ) );
		// Remove themes that have been deleted since the site option was last updated.
		$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

		update_site_option( 'auto_update_themes', $auto_updates );

		gc_redirect( admin_url( 'themes.php?disabled-auto-update=true' ) );

		exit;
	}
}

// Used in the HTML title tag.
$title       = __( '主题' );
$parent_file = 'themes.php';

// Help tab: Overview.
if ( current_user_can( 'switch_themes' ) ) {
	$help_overview = '<p>' . __( '此界面用于管理您已安装的主题。除了GeChiUI自带的默认主题之外，其他主题均是由第三方设计及开发的。' ) . '</p>' .
		'<p>' . __( '在此界面中，您可以：' ) . '</p>' .
		'<ul><li>' . __( '悬浮或轻触以查看启用和实时预览按钮' ) . '</li>' .
		'<li>' . __( '点击主题以查看主题名称、版本、作者、说明、标签和删除链接' ) . '</li>' .
		'<li>' . __( '单击活动主题的“自定义”或任何其他主题的“实时预览”以查看实时预览' ) . '</li></ul>' .
		'<p>' . __( '活动主题将突出显示为第一个主题。' ) . '</p>' .
		'<p>' . __( '搜索已安装的主题将根据主题的名字、描述、作者或标签来搜索。' ) . ' <span id="live-search-desc">' . __( '搜索结果会随着您的输入而不断更新。' ) . '</span></p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' => $help_overview,
		)
	);
} // End if 'switch_themes'.

// Help tab: Adding Themes.
if ( current_user_can( 'install_themes' ) ) {
	if ( is_multisite() ) {
		$help_install = '<p>' . __( '只能在“SaaS后台”中为多系统安装主题。' ) . '</p>';
	} else {
		$help_install = '<p>' . sprintf(
			/* translators: %s: https://www.gechiui.com/themes/ */
			__( '若您希望从更多主题中选择，请点击“安装主题”按钮。切换到“安装主题”界面后，您就可以在<a href="%s">www.GeChiUI.com主题目录</a>中浏览或搜索主题。GeChiUI主题目录中的主题是由第三方设计开发的，且与GeChiUI所用的版权许可证相兼容。最棒的是，它们都是免费的！' ),
			__( 'https://www.gechiui.com/themes/' )
		) . '</p>';
	}

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'adding-themes',
			'title'   => __( '安装主题' ),
			'content' => $help_install,
		)
	);
} // End if 'install_themes'.

// Help tab: Previewing and Customizing.
if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
	$help_customize =
		'<p>' . __( '轻触或悬浮在任何已安装的主题上，然后点击下方的“实时预览”链接来查看实时预览，并在单独的界面预览并调整主题设置。您也可在主题详情界面的最底找到“实时预览”按钮。任何已安装的主题都可通过此方式预览及自定义。' ) . '</p>' .
		'<p>' . __( '当前预览的主题是可互动的——您可以点击不同的页面来查看主题如何显示文章、存档和其他页面模板。根据主题所支持功能不同，您可以调整的项目也不同。要应用您在实时预览界面的设置，请点击左上角的“启用并发布”按钮。' ) . '</p>' .
		'<p>' . __( '在小屏幕设备上预览时，您可点击左下方的“隐藏控制区”图标，便可收起边栏，以便留出更多空间显示新主题的外观。再次点击该图标，边栏将展开。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'customize-preview-themes',
			'title'   => __( '预览和自定义' ),
			'content' => $help_customize,
		)
	);
} // End if 'edit_theme_options' && 'customize'.

$help_sidebar_autoupdates = '';

// Help tab: Auto-updates.
if ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) {
	$help_tab_autoupdates =
		'<p>' . __( '每个主题均可单独启用或禁用自动更新功能。对于已启用自动更新的主题，系统将显示下一次自动更新的预计日期。自动更新功能运行正常与否，取决于GC-Cron任务计划系统。' ) . '</p>' .
		'<p>' . __( '请注意：第三方主题、插件或自定义代码，都有可能覆盖GeChiUI的计划任务。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'plugins-themes-auto-updates',
			'title'   => __( '自动更新' ),
			'content' => $help_tab_autoupdates,
		)
	);

	$help_sidebar_autoupdates = '<p>' . __( '<a href="https://www.gechiui.com/support/plugins-themes-auto-updates/">了解更多：自动更新功能文档</a>' ) . '</p>';
} // End if 'update_themes' && 'gc_is_auto_update_enabled_for_type'.

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/using-themes/">主题使用文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/appearance-themes-screen/">Documentation on Managing Themes</a>' ) . '</p>' .
	$help_sidebar_autoupdates .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

if ( current_user_can( 'switch_themes' ) ) {
	$themes = gc_prepare_themes_for_js();
} else {
	$themes = gc_prepare_themes_for_js( array( gc_get_theme() ) );
}
gc_reset_vars( array( 'theme', 'search' ) );

gc_localize_script(
	'theme',
	'_gcThemeSettings',
	array(
		'themes'   => $themes,
		'settings' => array(
			'canInstall'    => ( ! is_multisite() && current_user_can( 'install_themes' ) ),
			'installURI'    => ( ! is_multisite() && current_user_can( 'install_themes' ) ) ? admin_url( 'theme-install.php' ) : null,
			'confirmDelete' => __( "您确定要删除该主题吗？\n\nClick 'Cancel' to go back, 'OK' to confirm the delete." ),
			'adminUrl'      => parse_url( admin_url(), PHP_URL_PATH ),
		),
		'l10n'     => array(
			'addNew'            => __( '添加新主题' ),
			'search'            => __( '搜索已安装的主题' ),
			'searchPlaceholder' => __( '搜索已安装的主题...'  ), // Placeholder (no ellipsis).
			/* translators: %d: Number of themes. */
			'themesFound'       => __( '找到的主题数：%d' ),
			'noThemesFound'     => __( '未找到主题，请重新搜索。' ),
		),
	)
);

add_thickbox();
gc_enqueue_script( 'theme' );
gc_enqueue_script( 'updates' );

if ( ! validate_current_theme() || isset( $_GET['broken'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '当前启用的主题已受损。自动切换回默认主题。' ), 'danger' );
} elseif ( isset( $_GET['activated'] ) ) {
	if ( isset( $_GET['previewed'] ) ) {
		$message = __( '设置已保存，主题已启用。' ). '<a href="'. home_url( '/' ) .'">'. __( '访问系统' ) .'</a>';
		add_settings_error( 'general', 'settings_updated', $message, 'success' );
	} else {
		$message = __( '新主题已启用。' ). '<a href="'. home_url( '/' ) .'">'. __( '访问系统' ) .'</a>';
		add_settings_error( 'general', 'settings_updated', $message, 'success' );
	}
} elseif ( isset( $_GET['deleted'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '主题已删除。' ), 'success' );
} elseif ( isset( $_GET['delete-active-child'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '您不能删除有已启用子主题的主题。' ), 'warning' );
} elseif ( isset( $_GET['resumed'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '主题已恢复。' ), 'success' );
} elseif ( isset( $_GET['error'] ) && 'resuming' === $_GET['error'] ) {
	add_settings_error( 'general', 'settings_updated', __( '此主题不能被恢复，因其触发了一个<strong>致命错误</strong>。' ), 'danger' );
} elseif ( isset( $_GET['enabled-auto-update'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '主题将自动更新。' ), 'success' );
} elseif ( isset( $_GET['disabled-auto-update'] ) ) {
	add_settings_error( 'general', 'settings_updated', __( '主题将不再自动更新。' ), 'warning' );
}

$current_theme = gc_get_theme();

if ( $current_theme->errors() && ( ! is_multisite() || current_user_can( 'manage_network_themes' ) ) ) {
	$message = __( '错误：' ) . ' ' . $current_theme->errors()->get_error_message();
	add_settings_error( 'general', 'settings_updated', $message, 'danger' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header">
		<h2 class="header-title">
			<?php esc_html_e( '主题' ); ?>
			<span class="title-count theme-count"><?php echo ! empty( $_GET['search'] ) ? __( '&hellip;' ) : count( $themes ); ?></span>
		</h2>
		<?php if ( ! is_multisite() && current_user_can( 'install_themes' ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'theme-install.php' ) ); ?>" class="hide-if-no-js btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '安装主题', 'theme' ); ?></a>
		<?php endif; ?>
		<form class="search-form"></form>
	</div>

<?php

$current_theme_actions = array();

if ( is_array( $submenu ) && isset( $submenu['themes.php'] ) ) {
	$forbidden_paths = array(
		'themes.php',
		'theme-editor.php',
		'site-editor.php',
		'edit.php?post_type=gc_navigation',
	);

	foreach ( (array) $submenu['themes.php'] as $item ) {
		$class = '';

		if ( in_array( $item[2], $forbidden_paths, true ) || str_starts_with( $item[2], 'customize.php' ) ) {
			continue;
		}

		// 0 = name, 1 = capability, 2 = file.
		if ( 0 === strcmp( $self, $item[2] ) && empty( $parent_file )
			|| $parent_file && $item[2] === $parent_file
		) {
			$class = ' current';
		}

		if ( ! empty( $submenu[ $item[2] ] ) ) {
			$submenu[ $item[2] ] = array_values( $submenu[ $item[2] ] ); // Re-index.
			$menu_hook           = get_plugin_page_hook( $submenu[ $item[2] ][0][2], $item[2] );

			if ( file_exists( GC_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}" ) || ! empty( $menu_hook ) ) {
				$current_theme_actions[] = "<a class='btn btn-primary btn-tone$class' href='admin.php?page={$submenu[$item[2]][0][2]}'>{$item[0]}</a>";
			} else {
				$current_theme_actions[] = "<a class='btn btn-primary btn-tone$class' href='{$submenu[$item[2]][0][2]}'>{$item[0]}</a>";
			}
		} elseif ( ! empty( $item[2] ) && current_user_can( $item[1] ) ) {
			$menu_file = $item[2];

			if ( current_user_can( 'customize' ) ) {
				if ( 'custom-header' === $menu_file ) {
					$current_theme_actions[] = "<a class='btn btn-primary btn-tone hide-if-no-customize$class' href='customize.php?autofocus[control]=header_image'>{$item[0]}</a>";
				} elseif ( 'custom-background' === $menu_file ) {
					$current_theme_actions[] = "<a class='btn btn-primary btn-tone hide-if-no-customize$class' href='customize.php?autofocus[control]=background_image'>{$item[0]}</a>";
				}
			}

			$pos = strpos( $menu_file, '?' );
			if ( false !== $pos ) {
				$menu_file = substr( $menu_file, 0, $pos );
			}

			if ( file_exists( ABSPATH . "gc-admin/$menu_file" ) ) {
				$current_theme_actions[] = "<a class='btn btn-primary btn-tone$class' href='{$item[2]}'>{$item[0]}</a>";
			} else {
				$current_theme_actions[] = "<a class='btn btn-primary btn-tone$class' href='themes.php?page={$item[2]}'>{$item[0]}</a>";
			}
		}
	}
}

$class_name = 'theme-browser';
if ( ! empty( $_GET['search'] ) ) {
	$class_name .= ' search-loading';
}
?>
<div class="<?php echo esc_attr( $class_name ); ?>">
	<div class="themes gc-clearfix">

<?php
/*
 * This PHP is synchronized with the tmpl-theme template below!
 */

foreach ( $themes as $theme ) :
	$aria_action = $theme['id'] . '-action';
	$aria_name   = $theme['id'] . '-name';

	$active_class = '';
	if ( $theme['active'] ) {
		$active_class = ' active';
	}
	?>
<div class="theme<?php echo $active_class; ?>">
	<?php if ( ! empty( $theme['screenshot'][0] ) ) { ?>
		<div class="theme-screenshot">
			<img src="<?php echo esc_attr( $theme['screenshot'][0] ); ?>" alt="" />
		</div>
	<?php } else { ?>
		<div class="theme-screenshot blank"></div>
	<?php } ?>

	<?php if ( $theme['hasUpdate'] ) : ?>
		<?php if ( $theme['updateResponse']['compatibleGC'] && $theme['updateResponse']['compatiblePHP'] ) : ?>
			<div class="update-message notice inline notice-warning notice-alt"><p>
				<?php if ( $theme['hasPackage'] ) : ?>
					<?php _e( '有新版本可用。<button class="button-link" type="button">立即更新</button>' ); ?>
				<?php else : ?>
					<?php _e( '新版本现在可用。' ); ?>
				<?php endif; ?>
			</p></div>
		<?php else : ?>
			<div class="update-message notice inline notice-error notice-alt"><p>
				<?php
				if ( ! $theme['updateResponse']['compatibleGC'] && ! $theme['updateResponse']['compatiblePHP'] ) {
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						$theme['name']
					);
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
				} elseif ( ! $theme['updateResponse']['compatibleGC'] ) {
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						$theme['name']
					);
					if ( current_user_can( 'update_core' ) ) {
						printf(
							/* translators: %s: URL to GeChiUI Updates screen. */
							' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
							self_admin_url( 'update-core.php' )
						);
					}
				} elseif ( ! $theme['updateResponse']['compatiblePHP'] ) {
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						$theme['name']
					);
					if ( current_user_can( 'update_php' ) ) {
						printf(
							/* translators: %s: URL to Update PHP page. */
							' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
							esc_url( gc_get_update_php_url() )
						);
						gc_update_php_annotation( '</p><p><em>', '</em>' );
					}
				}
				?>
			</p></div>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( ! $theme['compatibleGC'] || ! $theme['compatiblePHP'] ) {
		echo '<div class="notice inline notice-error notice-alt"><p>';
		if ( ! $theme['compatibleGC'] && ! $theme['compatiblePHP'] ) {
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
		} elseif ( ! $theme['compatibleGC'] ) {
			_e( '此主题未适配当前GeChiUI版本。' );
			if ( current_user_can( 'update_core' ) ) {
				printf(
					/* translators: %s: URL to GeChiUI Updates screen. */
					' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
					self_admin_url( 'update-core.php' )
				);
			}
		} elseif ( ! $theme['compatiblePHP'] ) {
			_e( '此主题未适配当前PHP版本。' );
			if ( current_user_can( 'update_php' ) ) {
				printf(
					/* translators: %s: URL to Update PHP page. */
					' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
					esc_url( gc_get_update_php_url() )
				);
				gc_update_php_annotation( '</p><p><em>', '</em>' );
			}
		}
		echo '</p></div>';
	}
	?>

	<?php
	/* translators: %s: Theme name. */
	$details_aria_label = sprintf( _x( '查看 %s 的主题详情', 'theme' ), $theme['name'] );
	?>
	<button type="button" aria-label="<?php echo esc_attr( $details_aria_label ); ?>" class="more-details" id="<?php echo esc_attr( $aria_action ); ?>"><?php _e( '主题详情' ); ?></button>
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name. */
		printf( __( '作者：%s' ), $theme['author'] );
		?>
	</div>

	<div class="theme-id-container">
		<?php if ( $theme['active'] ) { ?>
			<h2 class="theme-name" id="<?php echo esc_attr( $aria_name ); ?>">
				<span><?php _ex( '启用:', 'theme' ); ?></span> <?php echo $theme['name']; ?>
			</h2>
		<?php } else { ?>
			<h2 class="theme-name" id="<?php echo esc_attr( $aria_name ); ?>"><?php echo $theme['name']; ?></h2>
		<?php } ?>

		<div class="theme-actions">
		<?php if ( $theme['active'] ) { ?>
			<?php
			if ( $theme['actions']['customize'] && current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
				/* translators: %s: Theme name. */
				$customize_aria_label = sprintf( _x( '自定义 %s', 'theme' ), $theme['name'] );
				?>
				<a aria-label="<?php echo esc_attr( $customize_aria_label ); ?>" class="btn btn-primary btn-sm customize load-customize hide-if-no-customize" href="<?php echo $theme['actions']['customize']; ?>"><?php _e( '自定义' ); ?></a>
			<?php } ?>
		<?php } elseif ( $theme['compatibleGC'] && $theme['compatiblePHP'] ) { ?>
			<?php
			/* translators: %s: Theme name. */
			$aria_label = sprintf( _x( '启用%s', 'theme' ), '{{ data.name }}' );
			?>
			<a class="button activate" href="<?php echo $theme['actions']['activate']; ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
			<?php
			if ( ! $theme['blockTheme'] && current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
				/* translators: %s: Theme name. */
				$live_preview_aria_label = sprintf( _x( '实时预览 %s', 'theme' ), '{{ data.name }}' );
				?>
				<a aria-label="<?php echo esc_attr( $live_preview_aria_label ); ?>" class="btn btn-primary btn-sm load-customize hide-if-no-customize" href="<?php echo $theme['actions']['customize']; ?>"><?php _e( '实时预览' ); ?></a>
			<?php } ?>
		<?php } else { ?>
			<?php
			/* translators: %s: Theme name. */
			$aria_label = sprintf( _x( '无法启用%s', 'theme' ), '{{ data.name }}' );
			?>
			<a class="button disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( '无法启用', 'theme' ); ?></a>
			<?php if ( ! $theme['blockTheme'] && current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) { ?>
				<a class="btn btn-primary hide-if-no-customize disabled"><?php _e( '实时预览' ); ?></a>
			<?php } ?>
		<?php } ?>

		</div>
	</div>
</div>
<?php endforeach; ?>
	</div>
</div>
<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( '主题详情' ); ?>"></div>

<p class="no-themes"><?php _e( '未找到主题，请重新搜索。' ); ?></p>

<?php
// List broken themes, if any.
$broken_themes = gc_get_themes( array( 'errors' => true ) );
if ( ! is_multisite() && $broken_themes ) {
	?>

<div class="broken-themes">
<h3><?php _e( '损坏的主题' ); ?></h3>
<p><?php _e( '下列主题已安装但不完整。' ); ?></p>

	<?php
	$can_resume  = current_user_can( 'resume_themes' );
	$can_delete  = current_user_can( 'delete_themes' );
	$can_install = current_user_can( 'install_themes' );
	?>
<table>
	<tr>
		<th><?php _ex( '名称', 'theme name' ); ?></th>
		<th><?php _e( '描述' ); ?></th>
		<?php if ( $can_resume ) { ?>
			<td></td>
		<?php } ?>
		<?php if ( $can_delete ) { ?>
			<td></td>
		<?php } ?>
		<?php if ( $can_install ) { ?>
			<td></td>
		<?php } ?>
	</tr>
	<?php foreach ( $broken_themes as $broken_theme ) : ?>
		<tr>
			<td><?php echo $broken_theme->get( 'Name' ) ? $broken_theme->display( 'Name' ) : esc_html( $broken_theme->get_stylesheet() ); ?></td>
			<td><?php echo $broken_theme->errors()->get_error_message(); ?></td>
			<?php
			if ( $can_resume ) {
				if ( 'theme_paused' === $broken_theme->errors()->get_error_code() ) {
					$stylesheet = $broken_theme->get_stylesheet();
					$resume_url = add_query_arg(
						array(
							'action'     => 'resume',
							'stylesheet' => urlencode( $stylesheet ),
						),
						admin_url( 'themes.php' )
					);
					$resume_url = gc_nonce_url( $resume_url, 'resume-theme_' . $stylesheet );
					?>
					<td><a href="<?php echo esc_url( $resume_url ); ?>" class="button resume-theme"><?php _e( '恢复' ); ?></a></td>
					<?php
				} else {
					?>
					<td></td>
					<?php
				}
			}

			if ( $can_delete ) {
				$stylesheet = $broken_theme->get_stylesheet();
				$delete_url = add_query_arg(
					array(
						'action'     => 'delete',
						'stylesheet' => urlencode( $stylesheet ),
					),
					admin_url( 'themes.php' )
				);
				$delete_url = gc_nonce_url( $delete_url, 'delete-theme_' . $stylesheet );
				?>
				<td><a href="<?php echo esc_url( $delete_url ); ?>" class="button delete-theme"><?php _e( '删除' ); ?></a></td>
				<?php
			}

			if ( $can_install && 'theme_no_parent' === $broken_theme->errors()->get_error_code() ) {
				$parent_theme_name = $broken_theme->get( 'Template' );
				$parent_theme      = themes_api( 'theme_information', array( 'slug' => urlencode( $parent_theme_name ) ) );

				if ( ! is_gc_error( $parent_theme ) ) {
					$install_url = add_query_arg(
						array(
							'action' => 'install-theme',
							'theme'  => urlencode( $parent_theme_name ),
						),
						admin_url( 'update.php' )
					);
					$install_url = gc_nonce_url( $install_url, 'install-theme_' . $parent_theme_name );
					?>
					<td><a href="<?php echo esc_url( $install_url ); ?>" class="button install-theme"><?php _e( '安装父主题' ); ?></a></td>
					<?php
				}
			}
			?>
		</tr>
	<?php endforeach; ?>
</table>
</div>

	<?php
}
?>
</div><!-- .wrap -->

<?php

/**
 * Returns the JavaScript template used to display the auto-update setting for a theme.
 *
 *
 * @return string The template for displaying the auto-update setting link.
 */
function gc_theme_auto_update_setting_template() {
	$template = '
		<div class="theme-autoupdate">
			<# if ( data.autoupdate.supported ) { #>
				<# if ( data.autoupdate.forced === false ) { #>
					' . __( '自动更新已禁用' ) . '
				<# } else if ( data.autoupdate.forced ) { #>
					' . __( '自动更新已启用' ) . '
				<# } else if ( data.autoupdate.enabled ) { #>
					<button type="button" class="toggle-auto-update button-link" data-slug="{{ data.id }}" data-gc-action="disable">
						<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span><span class="label">' . __( '禁用自动更新' ) . '</span>
					</button>
				<# } else { #>
					<button type="button" class="toggle-auto-update button-link" data-slug="{{ data.id }}" data-gc-action="enable">
						<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span><span class="label">' . __( '启用自动更新' ) . '</span>
					</button>
				<# } #>
			<# } #>
			<# if ( data.hasUpdate ) { #>
				<# if ( data.autoupdate.supported && data.autoupdate.enabled ) { #>
					<span class="auto-update-time">
				<# } else { #>
					<span class="auto-update-time hidden">
				<# } #>
				<br />' . gc_get_auto_update_message() . '</span>
			<# } #>
			<div class="alert alert-danger notice-alt inline hidden"><p></p></div>
		</div>
	';

	/**
	 * Filters the JavaScript template used to display the auto-update setting for a theme (in the overlay).
	 *
	 * See {@see gc_prepare_themes_for_js()} for the properties of the `data` object.
	 *
	 *
	 * @param string $template The template for displaying the auto-update setting link.
	 */
	return apply_filters( 'theme_auto_update_setting_template', $template );
}

/*
 * The tmpl-theme template is synchronized with PHP above!
 */
?>
<script id="tmpl-theme" type="text/template">
	<# if ( data.screenshot[0] ) { #>
		<div class="theme-screenshot">
			<img src="{{ data.screenshot[0] }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>

	<# if ( data.hasUpdate ) { #>
		<# if ( data.updateResponse.compatibleGC && data.updateResponse.compatiblePHP ) { #>
			<div class="update-message notice inline notice-warning notice-alt"><p>
				<# if ( data.hasPackage ) { #>
					<?php _e( '有新版本可用。<button class="button-link" type="button">立即更新</button>' ); ?>
				<# } else { #>
					<?php _e( '新版本现在可用。' ); ?>
				<# } #>
			</p></div>
		<# } else { #>
			<div class="update-message notice inline notice-error notice-alt"><p>
				<# if ( ! data.updateResponse.compatibleGC && ! data.updateResponse.compatiblePHP ) { #>
					<?php
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						'{{{ data.name }}}'
					);
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
				<# } else if ( ! data.updateResponse.compatibleGC ) { #>
					<?php
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						'{{{ data.name }}}'
					);
					if ( current_user_can( 'update_core' ) ) {
						printf(
							/* translators: %s: URL to GeChiUI Updates screen. */
							' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
							self_admin_url( 'update-core.php' )
						);
					}
					?>
				<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
					<?php
					printf(
						/* translators: %s: Theme name. */
						__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
						'{{{ data.name }}}'
					);
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
	<# } #>

	<# if ( ! data.compatibleGC || ! data.compatiblePHP ) { #>
		<div class="alert alert-danger notice-alt"><p>
			<# if ( ! data.compatibleGC && ! data.compatiblePHP ) { #>
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
			<# } else if ( ! data.compatibleGC ) { #>
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
			<# } else if ( ! data.compatiblePHP ) { #>
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

	<?php
	/* translators: %s: Theme name. */
	$details_aria_label = sprintf( _x( '查看 %s 的主题详情', 'theme' ), '{{ data.name }}' );
	?>
	<button type="button" aria-label="<?php echo esc_attr( $details_aria_label ); ?>" class="more-details" id="{{ data.id }}-action"><?php _e( '主题详情' ); ?></button>
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name. */
		printf( __( '作者：%s' ), '{{{ data.author }}}' );
		?>
	</div>

	<div class="theme-id-container">
		<# if ( data.active ) { #>
			<h2 class="theme-name" id="{{ data.id }}-name">
				<span><?php _ex( '启用:', 'theme' ); ?></span> {{{ data.name }}}
			</h2>
		<# } else { #>
			<h2 class="theme-name" id="{{ data.id }}-name">{{{ data.name }}}</h2>
		<# } #>

		<div class="theme-actions">
			<# if ( data.active ) { #>
				<# if ( data.actions.customize ) { #>
					<?php
					/* translators: %s: Theme name. */
					$customize_aria_label = sprintf( _x( '自定义 %s', 'theme' ), '{{ data.name }}' );
					?>
					<a aria-label="<?php echo esc_attr( $customize_aria_label ); ?>" class="btn btn-primary btn-sm customize load-customize hide-if-no-customize" href="{{{ data.actions.customize }}}"><?php _e( '自定义' ); ?></a>
				<# } #>
			<# } else { #>
				<# if ( data.compatibleGC && data.compatiblePHP ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button activate" href="{{{ data.actions.activate }}}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
					<# if ( ! data.blockTheme ) { #>
						<?php
						/* translators: %s: Theme name. */
						$live_preview_aria_label = sprintf( _x( '实时预览 %s', 'theme' ), '{{ data.name }}' );
						?>
						<a aria-label="<?php echo esc_attr( $live_preview_aria_label ); ?>" class="btn btn-primary btn-sm load-customize hide-if-no-customize" href="{{{ data.actions.customize }}}"><?php _e( '实时预览' ); ?></a>
					<# } #>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '无法启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( '无法启用', 'theme' ); ?></a>
					<# if ( ! data.blockTheme ) { #>
						<a class="btn btn-primary hide-if-no-customize disabled"><?php _e( '实时预览' ); ?></a>
					<# } #>
				<# } #>
			<# } #>
		</div>
	</div>
</script>

<script id="tmpl-theme-single" type="text/template">
	<div class="theme-backdrop"></div>
	<div class="theme-wrap gc-clearfix" role="document">
		<div class="theme-header">
			<button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php _e( '显示上一个主题' ); ?></span></button>
			<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php _e( '显示下一个主题' ); ?></span></button>
			<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php _e( '关闭详情对话框' ); ?></span></button>
		</div>
		<div class="theme-about gc-clearfix">
			<div class="theme-screenshots">
			<# if ( data.screenshot[0] ) { #>
				<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
			<# } else { #>
				<div class="screenshot blank"></div>
			<# } #>
			</div>

			<div class="theme-info">
				<# if ( data.active ) { #>
					<span class="current-label"><?php _e( '已启用的主题' ); ?></span>
				<# } #>
				<h2 class="theme-name">{{{ data.name }}}<span class="theme-version">
					<?php
					/* translators: %s: Theme version. */
					printf( __( '版本：%s' ), '{{ data.version }}' );
					?>
				</span></h2>
				<p class="theme-author">
					<?php
					/* translators: %s: Theme author link. */
					printf( __( '作者：%s' ), '{{{ data.authorAndUri }}}' );
					?>
				</p>

				<# if ( ! data.compatibleGC || ! data.compatiblePHP ) { #>
					<div class="alert alert-danger notice-alt notice-large"><p>
						<# if ( ! data.compatibleGC && ! data.compatiblePHP ) { #>
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
						<# } else if ( ! data.compatibleGC ) { #>
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
						<# } else if ( ! data.compatiblePHP ) { #>
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

				<# if ( data.hasUpdate ) { #>
					<# if ( data.updateResponse.compatibleGC && data.updateResponse.compatiblePHP ) { #>
						<div class="alert alert-warning notice-alt notice-large">
							<h3 class="notice-title"><?php _e( '更新可用' ); ?></h3>
							{{{ data.update }}}
						</div>
					<# } else { #>
						<div class="alert alert-danger notice-alt notice-large">
							<h3 class="notice-title"><?php _e( '更新不兼容' ); ?></h3>
							<p>
								<# if ( ! data.updateResponse.compatibleGC && ! data.updateResponse.compatiblePHP ) { #>
									<?php
									printf(
										/* translators: %s: Theme name. */
										__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
										'{{{ data.name }}}'
									);
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
								<# } else if ( ! data.updateResponse.compatibleGC ) { #>
									<?php
									printf(
										/* translators: %s: Theme name. */
										__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
										'{{{ data.name }}}'
									);
									if ( current_user_can( 'update_core' ) ) {
										printf(
											/* translators: %s: URL to GeChiUI Updates screen. */
											' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
											self_admin_url( 'update-core.php' )
										);
									}
									?>
								<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
									<?php
									printf(
										/* translators: %s: Theme name. */
										__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
										'{{{ data.name }}}'
									);
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
							</p>
						</div>
					<# } #>
				<# } #>

				<# if ( data.actions.autoupdate ) { #>
					<?php echo gc_theme_auto_update_setting_template(); ?>
				<# } #>

				<p class="theme-description">{{{ data.description }}}</p>

				<# if ( data.parent ) { #>
					<p class="parent-theme">
						<?php
						/* translators: %s: Theme name. */
						printf( __( '这是%s的子主题。' ), '<strong>{{{ data.parent }}}</strong>' );
						?>
					</p>
				<# } #>

				<# if ( data.tags ) { #>
					<p class="theme-tags"><span><?php _e( '标签：' ); ?></span> {{{ data.tags }}}</p>
				<# } #>
			</div>
		</div>

		<div class="theme-actions">
			<div class="active-theme">
				<a href="{{{ data.actions.customize }}}" class="btn btn-primary btn-sm customize load-customize hide-if-no-customize"><?php _e( '自定义' ); ?></a>
				<?php echo implode( ' ', $current_theme_actions ); ?>
			</div>
			<div class="inactive-theme">
				<# if ( data.compatibleGC && data.compatiblePHP ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.actions.activate ) { #>
						<a href="{{{ data.actions.activate }}}" class="button activate" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
					<# } #>
					<# if ( ! data.blockTheme ) { #>
						<a href="{{{ data.actions.customize }}}" class="btn btn-primary btn-sm load-customize hide-if-no-customize"><?php _e( '实时预览' ); ?></a>
					<# } #>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( '无法启用%s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.actions.activate ) { #>
						<a class="button disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( '无法启用', 'theme' ); ?></a>
					<# } #>
					<# if ( ! data.blockTheme ) { #>
						<a class="btn btn-primary hide-if-no-customize disabled"><?php _e( '实时预览' ); ?></a>
					<# } #>
				<# } #>
			</div>

			<# if ( ! data.active && data.actions['delete'] ) { #>
				<?php
				/* translators: %s: Theme name. */
				$aria_label = sprintf( _x( '删除 %s', 'theme' ), '{{ data.name }}' );
				?>
				<a href="{{{ data.actions['delete'] }}}" class="button delete-theme" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '删除' ); ?></a>
			<# } #>
		</div>
	</div>
</script>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();
gc_print_update_row_templates();

gc_localize_script(
	'updates',
	'_gcUpdatesItemCounts',
	array(
		'totals' => gc_get_update_data(),
	)
);

require_once ABSPATH . 'gc-admin/admin-footer.php';
