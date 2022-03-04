<?php
/**
 * Update Core administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

gc_enqueue_style( 'plugin-install' );
gc_enqueue_script( 'plugin-install' );
gc_enqueue_script( 'updates' );
add_thickbox();

if ( is_multisite() && ! is_network_admin() ) {
	gc_redirect( network_admin_url( 'update-core.php' ) );
	exit;
}

if ( ! current_user_can( 'update_core' ) && ! current_user_can( 'update_themes' ) && ! current_user_can( 'update_plugins' ) && ! current_user_can( 'update_languages' ) ) {
	gc_die( __( '抱歉，您不能更新此站点。' ) );
}

/**
 * Lists available core updates.
 *
 *
 *
 * @global string $gc_local_package Locale code of the package.
 * @global gcdb   $gcdb             GeChiUI database abstraction object.
 *
 * @param object $update
 */
function list_core_update( $update ) {
	global $gc_local_package, $gcdb;
	static $first_pass = true;

	$gc_version     = get_bloginfo( 'version' );
	$version_string = sprintf( '%s&ndash;%s', $update->current, get_locale() );

	if ( 'zh_CN' === $update->locale && 'zh_CN' === get_locale() ) {
		$version_string = $update->current;
	} elseif ( 'zh_CN' === $update->locale && $update->packages->partial && $gc_version == $update->partial_version ) {
		$updates = get_core_updates();
		if ( $updates && 1 === count( $updates ) ) {
			// If the only available update is a partial builds, it doesn't need a language-specific version string.
			$version_string = $update->current;
		}
	} elseif ( 'zh_CN' === $update->locale && 'zh_CN' !== get_locale() ) {
		$version_string = sprintf( '%s&ndash;%s', $update->current, $update->locale );
	}

	$current = false;
	if ( ! isset( $update->response ) || 'latest' === $update->response ) {
		$current = true;
	}

	$message       = '';
	$form_action   = 'update-core.php?action=do-core-upgrade';
	$php_version   = phpversion();
	$mysql_version = $gcdb->db_version();
	$show_buttons  = true;

	// Nightly build versions have two hyphens and a commit number.
	if ( preg_match( '/-\w+-\d+/', $update->current ) ) {
		// Retrieve the major version number.
		preg_match( '/^\d+.\d+/', $update->current, $update_major );
		/* translators: %s: GeChiUI version. */
		$submit = sprintf( __( '升级至最新的 %s 每日构建版本' ), $update_major[0] );
	} else {
		/* translators: %s: GeChiUI version. */
		$submit = sprintf( __( '更新到%s版本' ), $version_string );
	}

	if ( 'development' === $update->response ) {
		$message = __( '您可以手动更新到最新的每日构建版本：' );
	} else {
		if ( $current ) {
			/* translators: %s: GeChiUI version. */
			$submit      = sprintf( __( '重新安装%s版本' ), $version_string );
			$form_action = 'update-core.php?action=do-core-reinstall';
		} else {
			$php_compat = version_compare( $php_version, $update->php_version, '>=' );
			if ( file_exists( GC_CONTENT_DIR . '/db.php' ) && empty( $gcdb->is_mysql ) ) {
				$mysql_compat = true;
			} else {
				$mysql_compat = version_compare( $mysql_version, $update->mysql_version, '>=' );
			}

			$version_url = sprintf(
				/* translators: %s: GeChiUI version. */
				esc_url( __( 'https://www.gechiui.com/support/gechiui-version/version-%s/' ) ),
				sanitize_title( $update->current )
			);

			$php_update_message = '</p><p>' . sprintf(
				/* translators: %s: URL to Update PHP page. */
				__( '<a href="%s">查阅如何更新PHP</a>。' ),
				esc_url( gc_get_update_php_url() )
			);

			$annotation = gc_get_update_php_annotation();

			if ( $annotation ) {
				$php_update_message .= '</p><p><em>' . $annotation . '</em>';
			}

			if ( ! $mysql_compat && ! $php_compat ) {
				$message = sprintf(
					/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Minimum required MySQL version number, 5: Current PHP version number, 6: Current MySQL version number. */
					__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高及MySQL版本%4$s或更高。您正在运行PHP %5$s和MySQL %6$s。' ),
					$version_url,
					$update->current,
					$update->php_version,
					$update->mysql_version,
					$php_version,
					$mysql_version
				) . $php_update_message;
			} elseif ( ! $php_compat ) {
				$message = sprintf(
					/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Current PHP version number. */
					__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高。您正在运行版本%4$s。' ),
					$version_url,
					$update->current,
					$update->php_version,
					$php_version
				) . $php_update_message;
			} elseif ( ! $mysql_compat ) {
				$message = sprintf(
					/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required MySQL version number, 4: Current MySQL version number. */
					__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要MySQL版本%3$s或更高。您正在运行版本%4$s。' ),
					$version_url,
					$update->current,
					$update->mysql_version,
					$mysql_version
				);
			} else {
				$message = sprintf(
					/* translators: 1: Installed GeChiUI version number, 2: URL to GeChiUI release notes, 3: New GeChiUI version number, including locale if necessary. */
					__( '您可以从GeChiUI %1$s手动更新至<a href="%2$s"> GeChiUI %3$s</a>：' ),
					$gc_version,
					$version_url,
					$version_string
				);
			}

			if ( ! $mysql_compat || ! $php_compat ) {
				$show_buttons = false;
			}
		}
	}

	echo '<p>';
	echo $message;
	echo '</p>';

	echo '<form method="post" action="' . esc_url( $form_action ) . '" name="upgrade" class="upgrade">';
	gc_nonce_field( 'upgrade-core' );

	echo '<p>';
	echo '<input name="version" value="' . esc_attr( $update->current ) . '" type="hidden" />';
	echo '<input name="locale" value="' . esc_attr( $update->locale ) . '" type="hidden" />';
	if ( $show_buttons ) {
		if ( $first_pass ) {
			submit_button( $submit, $current ? '' : 'primary regular', 'upgrade', false );
			$first_pass = false;
		} else {
			submit_button( $submit, '', 'upgrade', false );
		}
	}
	if ( 'zh_CN' !== $update->locale ) {
		if ( ! isset( $update->dismissed ) || ! $update->dismissed ) {
			submit_button( __( '隐藏此更新' ), '', 'dismiss', false );
		} else {
			submit_button( __( '再次显示这个更新' ), '', 'undismiss', false );
		}
	}
	echo '</p>';

	if ( 'zh_CN' !== $update->locale && ( ! isset( $gc_local_package ) || $gc_local_package != $update->locale ) ) {
		echo '<p class="hint">' . __( '当前这个本地化版本包含了翻译及其他本地化修正。' ) . '</p>';
	} elseif ( 'zh_CN' === $update->locale && 'zh_CN' !== get_locale() && ( ! $update->packages->partial && $gc_version == $update->partial_version ) ) {
		// Partial builds don't need language-specific warnings.
		echo '<p class="hint">' . sprintf(
			/* translators: %s: GeChiUI version. */
			__( '您将安装<strong>中文版</strong>的GeChiUI %s，这可能会破坏您当前使用的翻译。您可等待本地化版本发布后再更新。' ),
			'development' !== $update->response ? $update->current : ''
		) . '</p>';
	}

	echo '</form>';

}

/**
 * Display dismissed updates.
 *
 *
 */
function dismissed_updates() {
	$dismissed = get_core_updates(
		array(
			'dismissed' => true,
			'available' => false,
		)
	);

	if ( $dismissed ) {
		$show_text = esc_js( __( '显示隐藏的更新' ) );
		$hide_text = esc_js( __( '不显示隐藏的更新' ) );
		?>
		<script type="text/javascript">
			jQuery( function( $ ) {
				$( '#show-dismissed' ).on( 'click', function() {
					var isExpanded = ( 'true' === $( this ).attr( 'aria-expanded' ) );

					if ( isExpanded ) {
						$( this ).text( '<?php echo $show_text; ?>' ).attr( 'aria-expanded', 'false' );
					} else {
						$( this ).text( '<?php echo $hide_text; ?>' ).attr( 'aria-expanded', 'true' );
					}

					$( '#dismissed-updates' ).toggle( 'fast' );
				});
			});
		</script>
		<?php
		echo '<p class="hide-if-no-js"><button type="button" class="button" id="show-dismissed" aria-expanded="false">' . __( '显示隐藏的更新' ) . '</button></p>';
		echo '<ul id="dismissed-updates" class="core-updates dismissed">';
		foreach ( (array) $dismissed as $update ) {
			echo '<li>';
			list_core_update( $update );
			echo '</li>';
		}
		echo '</ul>';
	}
}

/**
 * Display upgrade GeChiUI for downloading latest or upgrading automatically form.
 *
 *
 *
 * @global string $required_php_version   The required PHP version string.
 * @global string $required_mysql_version The required MySQL version string.
 */
function core_upgrade_preamble() {
	global $required_php_version, $required_mysql_version;

	$updates = get_core_updates();

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$is_development_version = preg_match( '/alpha|beta|RC/', $gc_version );

	if ( isset( $updates[0]->version ) && version_compare( $updates[0]->version, $gc_version, '>' ) ) {
		echo '<h2 class="response">';
		_e( '有新的GeChiUI版本可供升级。' );
		echo '</h2>';

		echo '<div class="notice notice-warning inline"><p>';
		printf(
			/* translators: 1: Documentation on GeChiUI backups, 2: Documentation on updating GeChiUI. */
			__( '<strong>重要：</strong>在更新前，请<a href="%1$s">备份您的数据库和文件</a>。要获得关于更新的帮助，请访问<a href="%2$s">更新GeChiUI</a>文档页面。' ),
			__( 'https://www.gechiui.com/support/gechiui-backups/' ),
			__( 'https://www.gechiui.com/support/updating-gechiui/' )
		);
		echo '</p></div>';
	} elseif ( $is_development_version ) {
		echo '<h2 class="response">' . __( '您正在使用GeChiUI的开发版本。' ) . '</h2>';
	} else {
		echo '<h2 class="response">' . __( '您使用的GeChiUI是最新版本。' ) . '</h2>';
	}

	echo '<ul class="core-updates">';
	foreach ( (array) $updates as $update ) {
		echo '<li>';
		list_core_update( $update );
		echo '</li>';
	}
	echo '</ul>';

	// Don't show the maintenance mode notice when we are only showing a single re-install option.
	if ( $updates && ( count( $updates ) > 1 || 'latest' !== $updates[0]->response ) ) {
		echo '<p>' . __( '当您升级您的站点时，站点将自动进入维护模式。升级完成后会自动退出。' ) . '</p>';
	} elseif ( ! $updates ) {
		list( $normalized_version ) = explode( '-', $gc_version );
		echo '<p>' . sprintf(
			/* translators: 1: URL to About screen, 2: GeChiUI version. */
			__( '<a href="%1$s">了解GeChiUI %2$s</a>。' ),
			esc_url( self_admin_url( 'about.php' ) ),
			$normalized_version
		) . '</p>';
	}

	dismissed_updates();
}

/**
 * Display GeChiUI auto-updates settings.
 *
 *
 */
function core_auto_updates_settings() {
	if ( isset( $_GET['core-major-auto-updates-saved'] ) ) {
		if ( 'enabled' === $_GET['core-major-auto-updates-saved'] ) {
			$notice_text = __( '已启用所有GeChiUI版本的自动更新。 谢谢！' );
			echo '<div class="notice notice-success is-dismissible"><p>' . $notice_text . '</p></div>';
		} elseif ( 'disabled' === $_GET['core-major-auto-updates-saved'] ) {
			$notice_text = __( '从现在起，GeChiUI只会自动接收安全性维护版本。' );
			echo '<div class="notice notice-success is-dismissible"><p>' . $notice_text . '</p></div>';
		}
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	$updater = new GC_Automatic_Updater();

	// Defaults:
	$upgrade_dev   = get_site_option( 'auto_update_core_dev', 'enabled' ) === 'enabled';
	$upgrade_minor = get_site_option( 'auto_update_core_minor', 'enabled' ) === 'enabled';
	$upgrade_major = get_site_option( 'auto_update_core_major', 'unset' ) === 'enabled';

	$can_set_update_option = true;
	// GC_AUTO_UPDATE_CORE = true (all), 'beta', 'rc', 'development', 'branch-development', 'minor', false.
	if ( defined( 'GC_AUTO_UPDATE_CORE' ) ) {
		if ( false === GC_AUTO_UPDATE_CORE ) {
			// Defaults to turned off, unless a filter allows it.
			$upgrade_dev   = false;
			$upgrade_minor = false;
			$upgrade_major = false;
		} elseif ( true === GC_AUTO_UPDATE_CORE
			|| in_array( GC_AUTO_UPDATE_CORE, array( 'beta', 'rc', 'development', 'branch-development' ), true )
		) {
			// ALL updates for core.
			$upgrade_dev   = true;
			$upgrade_minor = true;
			$upgrade_major = true;
		} elseif ( 'minor' === GC_AUTO_UPDATE_CORE ) {
			// Only minor updates for core.
			$upgrade_dev   = false;
			$upgrade_minor = true;
			$upgrade_major = false;
		}

		// The UI is overridden by the `GC_AUTO_UPDATE_CORE` constant.
		$can_set_update_option = false;
	}

	if ( $updater->is_disabled() ) {
		$upgrade_dev   = false;
		$upgrade_minor = false;
		$upgrade_major = false;

		/*
		 * The UI is overridden by the `AUTOMATIC_UPDATER_DISABLED` constant
		 * or the `automatic_updater_disabled` filter,
		 * or by `gc_is_file_mod_allowed( 'automatic_updater' )`.
		 * See `GC_Automatic_Updater::is_disabled()`.
		 */
		$can_set_update_option = false;
	}

	// Is the UI overridden by a plugin using the `allow_major_auto_core_updates` filter?
	if ( has_filter( 'allow_major_auto_core_updates' ) ) {
		$can_set_update_option = false;
	}

	/** This filter is documented in gc-admin/includes/class-core-upgrader.php */
	$upgrade_dev = apply_filters( 'allow_dev_auto_core_updates', $upgrade_dev );
	/** This filter is documented in gc-admin/includes/class-core-upgrader.php */
	$upgrade_minor = apply_filters( 'allow_minor_auto_core_updates', $upgrade_minor );
	/** This filter is documented in gc-admin/includes/class-core-upgrader.php */
	$upgrade_major = apply_filters( 'allow_major_auto_core_updates', $upgrade_major );

	$auto_update_settings = array(
		'dev'   => $upgrade_dev,
		'minor' => $upgrade_minor,
		'major' => $upgrade_major,
	);

	if ( $upgrade_major ) {
		$gc_version = get_bloginfo( 'version' );
		$updates    = get_core_updates();

		if ( isset( $updates[0]->version ) && version_compare( $updates[0]->version, $gc_version, '>' ) ) {
			echo '<p>' . gc_get_auto_update_message() . '</p>';
		}
	}

	$action_url = self_admin_url( 'update-core.php?action=core-major-auto-updates-settings' );
	?>

	<p class="auto-update-status">
		<?php

		if ( $updater->is_vcs_checkout( ABSPATH ) ) {
			_e( '此站点似乎受到版本控制功能管理。自动更新被禁用。' );
		} elseif ( $upgrade_major ) {
			_e( '该站点会自动更新至GeChiUI的每个最新版本。' );

			if ( $can_set_update_option ) {
				echo '<br>';
				printf(
					'<a href="%s" class="core-auto-update-settings-link core-auto-update-settings-link-disable">%s</a>',
					gc_nonce_url( add_query_arg( 'value', 'disable', $action_url ), 'core-major-auto-updates-nonce' ),
					__( '切换到仅自动安装维护和安全版本更新。' )
				);
			}
		} elseif ( $upgrade_minor ) {
			_e( '该站点仅自动更新至最新的GeChiUI维护和安全版本。' );

			if ( $can_set_update_option ) {
				echo '<br>';
				printf(
					'<a href="%s" class="core-auto-update-settings-link core-auto-update-settings-link-enable">%s</a>',
					gc_nonce_url( add_query_arg( 'value', 'enable', $action_url ), 'core-major-auto-updates-nonce' ),
					__( '启用所有新版本的GeChiUI自动更新。' )
				);
			}
		} else {
			_e( '该站点不会接收GeChiUI新版本的自动更新。' );
		}
		?>
	</p>

	<?php
	/**
	 * Fires after the major core auto-update settings.
	 *
	 *
	 * @param array $auto_update_settings {
	 *     Array of core auto-update settings.
	 *
	 *     @type bool $dev   Whether to enable automatic updates for development versions.
	 *     @type bool $minor Whether to enable minor automatic core updates.
	 *     @type bool $major Whether to enable major automatic core updates.
	 * }
	 */
	do_action( 'after_core_auto_updates_settings', $auto_update_settings );
}

/**
 * Display the upgrade plugins form.
 *
 *
 */
function list_plugin_updates() {
	$gc_version     = get_bloginfo( 'version' );
	$cur_gc_version = preg_replace( '/-.*$/', '', $gc_version );

	require_once ABSPATH . 'gc-admin/includes/plugin-install.php';
	$plugins = get_plugin_updates();
	if ( empty( $plugins ) ) {
		echo '<h2>' . __( '插件' ) . '</h2>';
		echo '<p>' . __( '您的所有插件均为最新版本。' ) . '</p>';
		return;
	}
	$form_action = 'update-core.php?action=do-plugin-upgrade';

	$core_updates = get_core_updates();
	if ( ! isset( $core_updates[0]->response ) || 'latest' === $core_updates[0]->response || 'development' === $core_updates[0]->response || version_compare( $core_updates[0]->current, $cur_gc_version, '=' ) ) {
		$core_update_version = false;
	} else {
		$core_update_version = $core_updates[0]->current;
	}

	$plugins_count = count( $plugins );
	?>
<h2>
	<?php
	printf(
		'%s <span class="count">(%d)</span>',
		__( '插件' ),
		number_format_i18n( $plugins_count )
	);
	?>
</h2>
<p><?php _e( '以下插件有可用更新，点选需要升级的插件，然后点击“升级插件”。' ); ?></p>
<form method="post" action="<?php echo esc_url( $form_action ); ?>" name="upgrade-plugins" class="upgrade">
	<?php gc_nonce_field( 'upgrade-core' ); ?>
<p><input id="upgrade-plugins" class="button" type="submit" value="<?php esc_attr_e( '升级插件' ); ?>" name="upgrade" /></p>
<table class="widefat updates-table" id="update-plugins-table">
	<thead>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="plugins-select-all" /></td>
		<td class="manage-column"><label for="plugins-select-all"><?php _e( '全选' ); ?></label></td>
	</tr>
	</thead>

	<tbody class="plugins">
	<?php

	$auto_updates = array();
	if ( gc_is_auto_update_enabled_for_type( 'plugin' ) ) {
		$auto_updates       = (array) get_site_option( 'auto_update_plugins', array() );
		$auto_update_notice = ' | ' . gc_get_auto_update_message();
	}

	foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
		$plugin_data = (object) _get_plugin_data_markup_translate( $plugin_file, (array) $plugin_data, false, true );

		$icon            = '<span class="dashicons dashicons-admin-plugins"></span>';
		$preferred_icons = array( 'svg', '2x', '1x', 'default' );
		foreach ( $preferred_icons as $preferred_icon ) {
			if ( ! empty( $plugin_data->update->icons[ $preferred_icon ] ) ) {
				$icon = '<img src="' . esc_url( $plugin_data->update->icons[ $preferred_icon ] ) . '" alt="" />';
				break;
			}
		}

		// Get plugin compat for running version of GeChiUI.
		if ( isset( $plugin_data->update->tested ) && version_compare( $plugin_data->update->tested, $cur_gc_version, '>=' ) ) {
			/* translators: %s: GeChiUI version. */
			$compat = '<br />' . sprintf( __( '与GeChiUI %s的兼容性：100%%（作者自评）' ), $cur_gc_version );
		} else {
			/* translators: %s: GeChiUI version. */
			$compat = '<br />' . sprintf( __( '与GeChiUI %s的兼容性：未知' ), $cur_gc_version );
		}
		// Get plugin compat for updated version of GeChiUI.
		if ( $core_update_version ) {
			if ( isset( $plugin_data->update->tested ) && version_compare( $plugin_data->update->tested, $core_update_version, '>=' ) ) {
				/* translators: %s: GeChiUI version. */
				$compat .= '<br />' . sprintf( __( '与GeChiUI %s的兼容性：100%%（作者自评）' ), $core_update_version );
			} else {
				/* translators: %s: GeChiUI version. */
				$compat .= '<br />' . sprintf( __( '与GeChiUI %s的兼容性：未知' ), $core_update_version );
			}
		}

		$requires_php   = isset( $plugin_data->update->requires_php ) ? $plugin_data->update->requires_php : null;
		$compatible_php = is_php_version_compatible( $requires_php );

		if ( ! $compatible_php && current_user_can( 'update_php' ) ) {
			$compat .= '<br>' . __( '此更新不能与您的PHP版本相兼容。' ) . '&nbsp;';
			$compat .= sprintf(
				/* translators: %s: URL to Update PHP page. */
				__( '<a href="%s">查阅如何更新PHP</a>。' ),
				esc_url( gc_get_update_php_url() )
			);

			$annotation = gc_get_update_php_annotation();

			if ( $annotation ) {
				$compat .= '</p><p><em>' . $annotation . '</em>';
			}
		}

		// Get the upgrade notice for the new plugin version.
		if ( isset( $plugin_data->update->upgrade_notice ) ) {
			$upgrade_notice = '<br />' . strip_tags( $plugin_data->update->upgrade_notice );
		} else {
			$upgrade_notice = '';
		}

		$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data->update->slug . '&section=changelog&TB_iframe=true&width=640&height=662' );
		$details     = sprintf(
			'<a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">%3$s</a>',
			esc_url( $details_url ),
			/* translators: 1: Plugin name, 2: Version number. */
			esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $plugin_data->Name, $plugin_data->update->new_version ) ),
			/* translators: %s: Plugin version. */
			sprintf( __( '查看版本%s详情。' ), $plugin_data->update->new_version )
		);

		$checkbox_id = 'checkbox_' . md5( $plugin_file );
		?>
	<tr>
		<td class="check-column">
			<?php if ( $compatible_php ) : ?>
				<input type="checkbox" name="checked[]" id="<?php echo $checkbox_id; ?>" value="<?php echo esc_attr( $plugin_file ); ?>" />
				<label for="<?php echo $checkbox_id; ?>" class="screen-reader-text">
					<?php
					/* translators: %s: Plugin name. */
					printf( __( '选择%s' ), $plugin_data->Name );
					?>
				</label>
			<?php endif; ?>
		</td>
		<td class="plugin-title"><p>
			<?php echo $icon; ?>
			<strong><?php echo $plugin_data->Name; ?></strong>
			<?php
			printf(
				/* translators: 1: Plugin version, 2: New version. */
				__( '您正在使用的是版本%1$s。升级至%2$s。' ),
				$plugin_data->Version,
				$plugin_data->update->new_version
			);

			echo ' ' . $details . $compat . $upgrade_notice;

			if ( in_array( $plugin_file, $auto_updates, true ) ) {
				echo $auto_update_notice;
			}
			?>
		</p></td>
	</tr>
			<?php
	}
	?>
	</tbody>

	<tfoot>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="plugins-select-all-2" /></td>
		<td class="manage-column"><label for="plugins-select-all-2"><?php _e( '全选' ); ?></label></td>
	</tr>
	</tfoot>
</table>
<p><input id="upgrade-plugins-2" class="button" type="submit" value="<?php esc_attr_e( '升级插件' ); ?>" name="upgrade" /></p>
</form>
	<?php
}

/**
 * Display the upgrade themes form.
 *
 *
 */
function list_theme_updates() {
	$themes = get_theme_updates();
	if ( empty( $themes ) ) {
		echo '<h2>' . __( '主题' ) . '</h2>';
		echo '<p>' . __( '您的所有主题均为最新版本。' ) . '</p>';
		return;
	}

	$form_action = 'update-core.php?action=do-theme-upgrade';

	$themes_count = count( $themes );
	?>
<h2>
	<?php
	printf(
		'%s <span class="count">(%d)</span>',
		__( '主题' ),
		number_format_i18n( $themes_count )
	);
	?>
</h2>
<p><?php _e( '以下主题有可用更新，点选需要升级的主题，然后点击“升级主题”。' ); ?></p>
<p>
	<?php
	printf(
		/* translators: %s: Link to documentation on child themes. */
		__( '<strong>请注意：</strong>所有之前对主题文件的修改都将丢失。请考虑使用<a href="%s">子主题</a>方式来对主题做出修改。' ),
		__( 'https://developer.gechiui.com/themes/advanced-topics/child-themes/' )
	);
	?>
</p>
<form method="post" action="<?php echo esc_url( $form_action ); ?>" name="upgrade-themes" class="upgrade">
	<?php gc_nonce_field( 'upgrade-core' ); ?>
<p><input id="upgrade-themes" class="button" type="submit" value="<?php esc_attr_e( '升级主题' ); ?>" name="upgrade" /></p>
<table class="widefat updates-table" id="update-themes-table">
	<thead>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="themes-select-all" /></td>
		<td class="manage-column"><label for="themes-select-all"><?php _e( '全选' ); ?></label></td>
	</tr>
	</thead>

	<tbody class="plugins">
	<?php
	$auto_updates = array();
	if ( gc_is_auto_update_enabled_for_type( 'theme' ) ) {
		$auto_updates       = (array) get_site_option( 'auto_update_themes', array() );
		$auto_update_notice = ' | ' . gc_get_auto_update_message();
	}

	foreach ( $themes as $stylesheet => $theme ) {
		$requires_gc  = isset( $theme->update['requires'] ) ? $theme->update['requires'] : null;
		$requires_php = isset( $theme->update['requires_php'] ) ? $theme->update['requires_php'] : null;

		$compatible_gc  = is_gc_version_compatible( $requires_gc );
		$compatible_php = is_php_version_compatible( $requires_php );

		$compat = '';

		if ( ! $compatible_gc && ! $compatible_php ) {
			$compat .= '<br>' . __( '此更新不能与您的GeChiUI和PHP版本相兼容。' ) . '&nbsp;';
			if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
				$compat .= sprintf(
					/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
					__( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
					self_admin_url( 'update-core.php' ),
					esc_url( gc_get_update_php_url() )
				);

				$annotation = gc_get_update_php_annotation();

				if ( $annotation ) {
					$compat .= '</p><p><em>' . $annotation . '</em>';
				}
			} elseif ( current_user_can( 'update_core' ) ) {
				$compat .= sprintf(
					/* translators: %s: URL to GeChiUI Updates screen. */
					__( '<a href="%s">请更新GeChiUI</a>。' ),
					self_admin_url( 'update-core.php' )
				);
			} elseif ( current_user_can( 'update_php' ) ) {
				$compat .= sprintf(
					/* translators: %s: URL to Update PHP page. */
					__( '<a href="%s">查阅如何更新PHP</a>。' ),
					esc_url( gc_get_update_php_url() )
				);

				$annotation = gc_get_update_php_annotation();

				if ( $annotation ) {
					$compat .= '</p><p><em>' . $annotation . '</em>';
				}
			}
		} elseif ( ! $compatible_gc ) {
			$compat .= '<br>' . __( '此更新不能与您的GeChiUI版本相兼容。' ) . '&nbsp;';
			if ( current_user_can( 'update_core' ) ) {
				$compat .= sprintf(
					/* translators: %s: URL to GeChiUI Updates screen. */
					__( '<a href="%s">请更新GeChiUI</a>。' ),
					self_admin_url( 'update-core.php' )
				);
			}
		} elseif ( ! $compatible_php ) {
			$compat .= '<br>' . __( '此更新不能与您的PHP版本相兼容。' ) . '&nbsp;';
			if ( current_user_can( 'update_php' ) ) {
				$compat .= sprintf(
					/* translators: %s: URL to Update PHP page. */
					__( '<a href="%s">查阅如何更新PHP</a>。' ),
					esc_url( gc_get_update_php_url() )
				);

				$annotation = gc_get_update_php_annotation();

				if ( $annotation ) {
					$compat .= '</p><p><em>' . $annotation . '</em>';
				}
			}
		}

		$checkbox_id = 'checkbox_' . md5( $theme->get( 'Name' ) );
		?>
	<tr>
		<td class="check-column">
			<?php if ( $compatible_gc && $compatible_php ) : ?>
				<input type="checkbox" name="checked[]" id="<?php echo $checkbox_id; ?>" value="<?php echo esc_attr( $stylesheet ); ?>" />
				<label for="<?php echo $checkbox_id; ?>" class="screen-reader-text">
					<?php
					/* translators: %s: Theme name. */
					printf( __( '选择%s' ), $theme->display( 'Name' ) );
					?>
				</label>
			<?php endif; ?>
		</td>
		<td class="plugin-title"><p>
			<img src="<?php echo esc_url( $theme->get_screenshot() ); ?>" width="85" height="64" class="updates-table-screenshot" alt="" />
			<strong><?php echo $theme->display( 'Name' ); ?></strong>
			<?php
			printf(
				/* translators: 1: Theme version, 2: New version. */
				__( '您正在使用的是版本%1$s。升级至%2$s。' ),
				$theme->display( 'Version' ),
				$theme->update['new_version']
			);

			echo ' ' . $compat;

			if ( in_array( $stylesheet, $auto_updates, true ) ) {
				echo $auto_update_notice;
			}
			?>
		</p></td>
	</tr>
			<?php
	}
	?>
	</tbody>

	<tfoot>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="themes-select-all-2" /></td>
		<td class="manage-column"><label for="themes-select-all-2"><?php _e( '全选' ); ?></label></td>
	</tr>
	</tfoot>
</table>
<p><input id="upgrade-themes-2" class="button" type="submit" value="<?php esc_attr_e( '升级主题' ); ?>" name="upgrade" /></p>
</form>
	<?php
}

/**
 * Display the update translations form.
 *
 *
 */
function list_translation_updates() {
	$updates = gc_get_translation_updates();
	if ( ! $updates ) {
		if ( 'zh_CN' !== get_locale() ) {
			echo '<h2>' . __( '翻译' ) . '</h2>';
			echo '<p>' . __( '您的所有翻译均为最新版本。' ) . '</p>';
		}
		return;
	}

	$form_action = 'update-core.php?action=do-translation-upgrade';
	?>
	<h2><?php _e( '翻译' ); ?></h2>
	<form method="post" action="<?php echo esc_url( $form_action ); ?>" name="upgrade-translations" class="upgrade">
		<p><?php _e( '有新的翻译可用。' ); ?></p>
		<?php gc_nonce_field( 'upgrade-translations' ); ?>
		<p><input class="button" type="submit" value="<?php esc_attr_e( '更新翻译' ); ?>" name="upgrade" /></p>
	</form>
	<?php
}

/**
 * Upgrade GeChiUI core display.
 *
 *
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param bool $reinstall
 */
function do_core_upgrade( $reinstall = false ) {
	global $gc_filesystem;

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	if ( $reinstall ) {
		$url = 'update-core.php?action=do-core-reinstall';
	} else {
		$url = 'update-core.php?action=do-core-upgrade';
	}
	$url = gc_nonce_url( $url, 'upgrade-core' );

	$version = isset( $_POST['version'] ) ? $_POST['version'] : false;
	$locale  = isset( $_POST['locale'] ) ? $_POST['locale'] : 'zh_CN';
	$update  = find_core_update( $version, $locale );
	if ( ! $update ) {
		return;
	}

	// Allow relaxed file ownership writes for User-initiated upgrades when the API specifies
	// that it's safe to do so. This only happens when there are no new files to create.
	$allow_relaxed_file_ownership = ! $reinstall && isset( $update->new_files ) && ! $update->new_files;

	?>
	<div class="wrap">
	<h1><?php _e( '升级GeChiUI' ); ?></h1>
	<?php

	$credentials = request_filesystem_credentials( $url, '', false, ABSPATH, array( 'version', 'locale' ), $allow_relaxed_file_ownership );
	if ( false === $credentials ) {
		echo '</div>';
		return;
	}

	if ( ! GC_Filesystem( $credentials, ABSPATH, $allow_relaxed_file_ownership ) ) {
		// Failed to connect. Error and request again.
		request_filesystem_credentials( $url, '', true, ABSPATH, array( 'version', 'locale' ), $allow_relaxed_file_ownership );
		echo '</div>';
		return;
	}

	if ( $gc_filesystem->errors->has_errors() ) {
		foreach ( $gc_filesystem->errors->get_error_messages() as $message ) {
			show_message( $message );
		}
		echo '</div>';
		return;
	}

	if ( $reinstall ) {
		$update->response = 'reinstall';
	}

	add_filter( 'update_feedback', 'show_message' );

	$upgrader = new Core_Upgrader();
	$result   = $upgrader->upgrade(
		$update,
		array(
			'allow_relaxed_file_ownership' => $allow_relaxed_file_ownership,
		)
	);

	if ( is_gc_error( $result ) ) {
		show_message( $result );
		if ( 'up_to_date' != $result->get_error_code() && 'locked' != $result->get_error_code() ) {
			show_message( __( '安装失败' ) );
		}
		echo '</div>';
		return;
	}

	show_message( __( 'GeChiUI升级成功。' ) );
	show_message(
		'<span class="hide-if-no-js">' . sprintf(
			/* translators: 1: GeChiUI version, 2: URL to About screen. */
			__( '欢迎使用GeChiUI %1$s。我们将带您到“关于GeChiUI”页面。如果没有自动跳转，请<a href="%2$s">点击这里</a>。' ),
			$result,
			esc_url( self_admin_url( 'about.php?updated' ) )
		) . '</span>'
	);
	show_message(
		'<span class="hide-if-js">' . sprintf(
			/* translators: 1: GeChiUI version, 2: URL to About screen. */
			__( '欢迎使用GeChiUI %1$s。<a href="%2$s">了解更多</a>。' ),
			$result,
			esc_url( self_admin_url( 'about.php?updated' ) )
		) . '</span>'
	);
	?>
	</div>
	<script type="text/javascript">
	window.location = '<?php echo self_admin_url( 'about.php?updated' ); ?>';
	</script>
	<?php
}

/**
 * Dismiss a core update.
 *
 *
 */
function do_dismiss_core_update() {
	$version = isset( $_POST['version'] ) ? $_POST['version'] : false;
	$locale  = isset( $_POST['locale'] ) ? $_POST['locale'] : 'zh_CN';
	$update  = find_core_update( $version, $locale );
	if ( ! $update ) {
		return;
	}
	dismiss_core_update( $update );
	gc_redirect( gc_nonce_url( 'update-core.php?action=upgrade-core', 'upgrade-core' ) );
	exit;
}

/**
 * Undismiss a core update.
 *
 *
 */
function do_undismiss_core_update() {
	$version = isset( $_POST['version'] ) ? $_POST['version'] : false;
	$locale  = isset( $_POST['locale'] ) ? $_POST['locale'] : 'zh_CN';
	$update  = find_core_update( $version, $locale );
	if ( ! $update ) {
		return;
	}
	undismiss_core_update( $version, $locale );
	gc_redirect( gc_nonce_url( 'update-core.php?action=upgrade-core', 'upgrade-core' ) );
	exit;
}

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'upgrade-core';

$upgrade_error = false;
if ( ( 'do-theme-upgrade' === $action || ( 'do-plugin-upgrade' === $action && ! isset( $_GET['plugins'] ) ) )
	&& ! isset( $_POST['checked'] ) ) {
	$upgrade_error = ( 'do-theme-upgrade' === $action ) ? 'themes' : 'plugins';
	$action        = 'upgrade-core';
}

$title       = __( 'GeChiUI更新' );
$parent_file = 'index.php';

$updates_overview  = '<p>' . __( '在这个界面中，您可以将GeChiUI更新至最新版，也可以从www.GeChiUI.com代码库升级您的主题、插件和翻译。' ) . '</p>';
$updates_overview .= '<p>' . __( '如果更新可用，您将会在工具栏和导航菜单处看到通知。' ) . ' ' . __( '保持更新站点对确保安全至关重要，这也会让互联网和您的读者更安全。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $updates_overview,
	)
);

$updates_howto  = '<p>' . __( '<strong>GeChiUI</strong>——更新GeChiUI只需轻点一次鼠标：当有新版本可用时，<strong>点击“立即更新”按钮</strong>。' ) . ' ' . __( '在大多数情况下，GeChiUI会在后台自动为您应用维护和安全更新。' ) . '</p>';
$updates_howto .= '<p>' . __( '<strong>主题和插件</strong>——要在本页面更新主题和插件，使用复选框选择要升级的项目，并<strong>点击对应的“更新”</strong>。选择“主题”区域和“插件”区域最上方的复选框以全选。' ) . '</p>';

if ( 'zh_CN' !== get_locale() ) {
	$updates_howto .= '<p>' . __( '<strong>翻译</strong>——包含GeChiUI翻译的文件将在任何其他升级执行时一并被升级。如果这些文件已过时，您可以<strong>点击“更新翻译”</strong>按钮。' ) . '</p>';
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'how-to-update',
		'title'   => __( '如何更新' ),
		'content' => $updates_howto,
	)
);

$help_sidebar_autoupdates = '';

if ( ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) || ( current_user_can( 'update_plugins' ) && gc_is_auto_update_enabled_for_type( 'plugin' ) ) ) {
	$help_tab_autoupdates  = '<p>' . __( 'GeChiUI大版本及每个主题、插件均可单独启用或禁用自动更新功能。对于已启用自动更新的主题或插件，系统将显示下一次自动更新的预计日期。自动更新功能运行正常与否，取决于GC-Cron任务计划系统。' ) . '</p>';
	$help_tab_autoupdates .= '<p>' . __( '请注意：第三方主题、插件或自定义代码，都有可能覆盖GeChiUI的计划任务。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'plugins-themes-auto-updates',
			'title'   => __( '自动更新' ),
			'content' => $help_tab_autoupdates,
		)
	);

	$help_sidebar_autoupdates = '<p>' . __( '<a href="https://www.gechiui.com/support/plugins-themes-auto-updates/">了解更多：自动更新功能文档</a>' ) . '</p>';
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/dashboard-updates-screen/">升级GeChiUI文档</a>' ) . '</p>' .
	$help_sidebar_autoupdates .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

if ( 'upgrade-core' === $action ) {
	// Force a update check when requested.
	$force_check = ! empty( $_GET['force-check'] );
	gc_version_check( array(), $force_check );

	require_once ABSPATH . 'gc-admin/admin-header.php';
	?>
	<div class="wrap">
	<h1><?php _e( 'GeChiUI更新' ); ?></h1>
	<p><?php _e( '您可在此查找关于更新功能、设置自动更新及插件和主题是否需要更新的相关信息。' ); ?></p>

	<?php
	if ( $upgrade_error ) {
		echo '<div class="error"><p>';
		if ( 'themes' === $upgrade_error ) {
			_e( '请选择需要升级的主题。' );
		} else {
			_e( '请选择需要升级的插件。' );
		}
		echo '</p></div>';
	}

	$last_update_check = false;
	$current           = get_site_transient( 'update_core' );

	if ( $current && isset( $current->last_checked ) ) {
		$last_update_check = $current->last_checked + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	}

	echo '<h2 class="gc-current-version">';
	/* translators: Current version of GeChiUI. */
	printf( __( '当前版本：%s' ), get_bloginfo( 'version' ) );
	echo '</h2>';

	echo '<p class="update-last-checked">';
	/* translators: 1: Date, 2: Time. */
	printf( __( '最后检查时间：%1$s %2$s。' ), date_i18n( __( 'Y年n月j日' ), $last_update_check ), date_i18n( __( 'a g:i T' ), $last_update_check ) );
	echo ' <a href="' . esc_url( self_admin_url( 'update-core.php?force-check=1' ) ) . '">' . __( '再次检查。' ) . '</a>';
	echo '</p>';

	if ( current_user_can( 'update_core' ) ) {
		core_auto_updates_settings();
		core_upgrade_preamble();
	}
	if ( current_user_can( 'update_plugins' ) ) {
		list_plugin_updates();
	}
	if ( current_user_can( 'update_themes' ) ) {
		list_theme_updates();
	}
	if ( current_user_can( 'update_languages' ) ) {
		list_translation_updates();
	}

	/**
	 * Fires after the core, plugin, and theme update tables.
	 *
	 */
	do_action( 'core_upgrade_preamble' );
	echo '</div>';

	gc_localize_script(
		'updates',
		'_gcUpdatesItemCounts',
		array(
			'totals' => gc_get_update_data(),
		)
	);

	require_once ABSPATH . 'gc-admin/admin-footer.php';

} elseif ( 'do-core-upgrade' === $action || 'do-core-reinstall' === $action ) {

	if ( ! current_user_can( 'update_core' ) ) {
		gc_die( __( '抱歉，您不能更新此站点。' ) );
	}

	check_admin_referer( 'upgrade-core' );

	// Do the (un)dismiss actions before headers, so that they can redirect.
	if ( isset( $_POST['dismiss'] ) ) {
		do_dismiss_core_update();
	} elseif ( isset( $_POST['undismiss'] ) ) {
		do_undismiss_core_update();
	}

	require_once ABSPATH . 'gc-admin/admin-header.php';
	if ( 'do-core-reinstall' === $action ) {
		$reinstall = true;
	} else {
		$reinstall = false;
	}

	if ( isset( $_POST['upgrade'] ) ) {
		do_core_upgrade( $reinstall );
	}

	gc_localize_script(
		'updates',
		'_gcUpdatesItemCounts',
		array(
			'totals' => gc_get_update_data(),
		)
	);

	require_once ABSPATH . 'gc-admin/admin-footer.php';

} elseif ( 'do-plugin-upgrade' === $action ) {

	if ( ! current_user_can( 'update_plugins' ) ) {
		gc_die( __( '抱歉，您不能更新此站点。' ) );
	}

	check_admin_referer( 'upgrade-core' );

	if ( isset( $_GET['plugins'] ) ) {
		$plugins = explode( ',', $_GET['plugins'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$plugins = (array) $_POST['checked'];
	} else {
		gc_redirect( admin_url( 'update-core.php' ) );
		exit;
	}

	$url = 'update.php?action=update-selected&plugins=' . urlencode( implode( ',', $plugins ) );
	$url = gc_nonce_url( $url, 'bulk-update-plugins' );

	// Used in the HTML title tag.
	$title = __( '升级插件' );

	require_once ABSPATH . 'gc-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1><?php _e( '升级插件' ); ?></h1>
		<iframe src="<?php echo $url; ?>" style="width: 100%; height: 100%; min-height: 750px;" frameborder="0" title="<?php esc_attr_e( '更新进度' ); ?>"></iframe>
	</div>
	<?php

	gc_localize_script(
		'updates',
		'_gcUpdatesItemCounts',
		array(
			'totals' => gc_get_update_data(),
		)
	);

	require_once ABSPATH . 'gc-admin/admin-footer.php';

} elseif ( 'do-theme-upgrade' === $action ) {

	if ( ! current_user_can( 'update_themes' ) ) {
		gc_die( __( '抱歉，您不能更新此站点。' ) );
	}

	check_admin_referer( 'upgrade-core' );

	if ( isset( $_GET['themes'] ) ) {
		$themes = explode( ',', $_GET['themes'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$themes = (array) $_POST['checked'];
	} else {
		gc_redirect( admin_url( 'update-core.php' ) );
		exit;
	}

	$url = 'update.php?action=update-selected-themes&themes=' . urlencode( implode( ',', $themes ) );
	$url = gc_nonce_url( $url, 'bulk-update-themes' );

	// Used in the HTML title tag.
	$title = __( '升级主题' );

	require_once ABSPATH . 'gc-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1><?php _e( '升级主题' ); ?></h1>
		<iframe src="<?php echo $url; ?>" style="width: 100%; height: 100%; min-height: 750px;" frameborder="0" title="<?php esc_attr_e( '更新进度' ); ?>"></iframe>
	</div>
	<?php

	gc_localize_script(
		'updates',
		'_gcUpdatesItemCounts',
		array(
			'totals' => gc_get_update_data(),
		)
	);

	require_once ABSPATH . 'gc-admin/admin-footer.php';

} elseif ( 'do-translation-upgrade' === $action ) {

	if ( ! current_user_can( 'update_languages' ) ) {
		gc_die( __( '抱歉，您不能更新此站点。' ) );
	}

	check_admin_referer( 'upgrade-translations' );

	require_once ABSPATH . 'gc-admin/admin-header.php';
	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	$url     = 'update-core.php?action=do-translation-upgrade';
	$nonce   = 'upgrade-translations';
	$title   = __( '更新翻译' );
	$context = GC_LANG_DIR;

	$upgrader = new Language_Pack_Upgrader( new Language_Pack_Upgrader_Skin( compact( 'url', 'nonce', 'title', 'context' ) ) );
	$result   = $upgrader->bulk_upgrade();

	gc_localize_script(
		'updates',
		'_gcUpdatesItemCounts',
		array(
			'totals' => gc_get_update_data(),
		)
	);

	require_once ABSPATH . 'gc-admin/admin-footer.php';

} elseif ( 'core-major-auto-updates-settings' === $action ) {

	if ( ! current_user_can( 'update_core' ) ) {
		gc_die( __( '抱歉，您不能更新此站点。' ) );
	}

	$redirect_url = self_admin_url( 'update-core.php' );

	if ( isset( $_GET['value'] ) ) {
		check_admin_referer( 'core-major-auto-updates-nonce' );

		if ( 'enable' === $_GET['value'] ) {
			update_site_option( 'auto_update_core_major', 'enabled' );
			$redirect_url = add_query_arg( 'core-major-auto-updates-saved', 'enabled', $redirect_url );
		} elseif ( 'disable' === $_GET['value'] ) {
			update_site_option( 'auto_update_core_major', 'disabled' );
			$redirect_url = add_query_arg( 'core-major-auto-updates-saved', 'disabled', $redirect_url );
		}
	}

	gc_redirect( $redirect_url );
	exit;
} else {
	/**
	 * Fires for each custom update action on the GeChiUI Updates screen.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to the
	 * passed update action. The hook fires in lieu of all available
	 * default update actions.
	 *
	 */
	do_action( "update-core-custom_{$action}" );  // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
}
