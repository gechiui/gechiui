<?php
/**
 * Plugins administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'activate_plugins' ) ) {
	gc_die( __( '抱歉，您不能在此站点上管理插件。' ) );
}

$gc_list_table = _get_list_table( 'GC_Plugins_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

$action = $gc_list_table->current_action();

$plugin = isset( $_REQUEST['plugin'] ) ? gc_unslash( $_REQUEST['plugin'] ) : '';
$s      = isset( $_REQUEST['s'] ) ? urlencode( gc_unslash( $_REQUEST['s'] ) ) : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$query_args_to_remove = array(
	'error',
	'deleted',
	'activate',
	'activate-multi',
	'deactivate',
	'deactivate-multi',
	'enabled-auto-update',
	'disabled-auto-update',
	'enabled-auto-update-multi',
	'disabled-auto-update-multi',
	'_error_nonce',
);

$_SERVER['REQUEST_URI'] = remove_query_arg( $query_args_to_remove, $_SERVER['REQUEST_URI'] );

gc_enqueue_script( 'updates' );

if ( $action ) {

	switch ( $action ) {
		case 'activate':
			if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
				gc_die( __( '抱歉，您不能启用此插件。' ) );
			}

			if ( is_multisite() && ! is_network_admin() && is_network_only_plugin( $plugin ) ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			check_admin_referer( 'activate-plugin_' . $plugin );

			$result = activate_plugin( $plugin, self_admin_url( 'plugins.php?error=true&plugin=' . urlencode( $plugin ) ), is_network_admin() );
			if ( is_gc_error( $result ) ) {
				if ( 'unexpected_output' === $result->get_error_code() ) {
					$redirect = self_admin_url( 'plugins.php?error=true&charsout=' . strlen( $result->get_error_data() ) . '&plugin=' . urlencode( $plugin ) . "&plugin_status=$status&paged=$page&s=$s" );
					gc_redirect( add_query_arg( '_error_nonce', gc_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) );
					exit;
				} else {
					gc_die( $result );
				}
			}

			if ( ! is_network_admin() ) {
				$recent = (array) get_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_option( 'recently_activated', $recent );
			} else {
				$recent = (array) get_site_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_site_option( 'recently_activated', $recent );
			}

			if ( isset( $_GET['from'] ) && 'import' === $_GET['from'] ) {
				// Overrides the ?error=true one above and redirects to the Imports page, stripping the -importer suffix.
				gc_redirect( self_admin_url( 'import.php?import=' . str_replace( '-importer', '', dirname( $plugin ) ) ) );
			} elseif ( isset( $_GET['from'] ) && 'press-this' === $_GET['from'] ) {
				gc_redirect( self_admin_url( 'press-this.php' ) );
			} else {
				// Overrides the ?error=true one above.
				gc_redirect( self_admin_url( "plugins.php?activate=true&plugin_status=$status&paged=$page&s=$s" ) );
			}
			exit;

		case 'activate-selected':
			if ( ! current_user_can( 'activate_plugins' ) ) {
				gc_die( __( '抱歉，您不能在在此站点上启用插件。' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			$plugins = isset( $_POST['checked'] ) ? (array) gc_unslash( $_POST['checked'] ) : array();

			if ( is_network_admin() ) {
				foreach ( $plugins as $i => $plugin ) {
					// Only activate plugins which are not already network activated.
					if ( is_plugin_active_for_network( $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			} else {
				foreach ( $plugins as $i => $plugin ) {
					// Only activate plugins which are not already active and are not network-only when on Multisite.
					if ( is_plugin_active( $plugin ) || ( is_multisite() && is_network_only_plugin( $plugin ) ) ) {
						unset( $plugins[ $i ] );
					}
					// Only activate plugins which the user can activate.
					if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			}

			if ( empty( $plugins ) ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			activate_plugins( $plugins, self_admin_url( 'plugins.php?error=true' ), is_network_admin() );

			if ( ! is_network_admin() ) {
				$recent = (array) get_option( 'recently_activated' );
			} else {
				$recent = (array) get_site_option( 'recently_activated' );
			}

			foreach ( $plugins as $plugin ) {
				unset( $recent[ $plugin ] );
			}

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', $recent );
			} else {
				update_site_option( 'recently_activated', $recent );
			}

			gc_redirect( self_admin_url( "plugins.php?activate-multi=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;

		case 'update-selected':
			check_admin_referer( 'bulk-plugins' );

			if ( isset( $_GET['plugins'] ) ) {
				$plugins = explode( ',', gc_unslash( $_GET['plugins'] ) );
			} elseif ( isset( $_POST['checked'] ) ) {
				$plugins = (array) gc_unslash( $_POST['checked'] );
			} else {
				$plugins = array();
			}

			// Used in the HTML title tag.
			$title       = __( '升级插件' );
			$parent_file = 'plugins.php';

			gc_enqueue_script( 'updates' );
			require_once ABSPATH . 'gc-admin/admin-header.php';

			echo '<div class="wrap">';
			echo '<h1>' . esc_html( $title ) . '</h1>';

			$url = self_admin_url( 'update.php?action=update-selected&amp;plugins=' . urlencode( implode( ',', $plugins ) ) );
			$url = gc_nonce_url( $url, 'bulk-update-plugins' );

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once ABSPATH . 'gc-admin/admin-footer.php';
			exit;

		case 'error_scrape':
			if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
				gc_die( __( '抱歉，您不能启用此插件。' ) );
			}

			check_admin_referer( 'plugin-activation-error_' . $plugin );

			$valid = validate_plugin( $plugin );
			if ( is_gc_error( $valid ) ) {
				gc_die( $valid );
			}

			if ( ! GC_DEBUG ) {
				error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
			}

			ini_set( 'display_errors', true ); // Ensure that fatal errors are displayed.
			// Go back to "sandbox" scope so we get the same errors as before.
			plugin_sandbox_scrape( $plugin );
			/** This action is documented in gc-admin/includes/plugin.php */
			do_action( "activate_{$plugin}" );
			exit;

		case 'deactivate':
			if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
				gc_die( __( '抱歉，您不能禁用此插件。' ) );
			}

			check_admin_referer( 'deactivate-plugin_' . $plugin );

			if ( ! is_network_admin() && is_plugin_active_for_network( $plugin ) ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			deactivate_plugins( $plugin, false, is_network_admin() );

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );
			} else {
				update_site_option( 'recently_activated', array( $plugin => time() ) + (array) get_site_option( 'recently_activated' ) );
			}

			if ( headers_sent() ) {
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s" ) . "' />";
			} else {
				gc_redirect( self_admin_url( "plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s" ) );
			}
			exit;

		case 'deactivate-selected':
			if ( ! current_user_can( 'deactivate_plugins' ) ) {
				gc_die( __( '抱歉，您不能在此站点上禁用插件。' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			$plugins = isset( $_POST['checked'] ) ? (array) gc_unslash( $_POST['checked'] ) : array();
			// Do not deactivate plugins which are already deactivated.
			if ( is_network_admin() ) {
				$plugins = array_filter( $plugins, 'is_plugin_active_for_network' );
			} else {
				$plugins = array_filter( $plugins, 'is_plugin_active' );
				$plugins = array_diff( $plugins, array_filter( $plugins, 'is_plugin_active_for_network' ) );

				foreach ( $plugins as $i => $plugin ) {
					// Only deactivate plugins which the user can deactivate.
					if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			}
			if ( empty( $plugins ) ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			deactivate_plugins( $plugins, false, is_network_admin() );

			$deactivated = array();
			foreach ( $plugins as $plugin ) {
				$deactivated[ $plugin ] = time();
			}

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', $deactivated + (array) get_option( 'recently_activated' ) );
			} else {
				update_site_option( 'recently_activated', $deactivated + (array) get_site_option( 'recently_activated' ) );
			}

			gc_redirect( self_admin_url( "plugins.php?deactivate-multi=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;

		case 'delete-selected':
			if ( ! current_user_can( 'delete_plugins' ) ) {
				gc_die( __( '抱歉，您不能在此站点上删除插件。' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			// $_POST = from the plugin form; $_GET = from the FTP details screen.
			$plugins = isset( $_REQUEST['checked'] ) ? (array) gc_unslash( $_REQUEST['checked'] ) : array();
			if ( empty( $plugins ) ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			$plugins = array_filter( $plugins, 'is_plugin_inactive' ); // Do not allow to delete activated plugins.
			if ( empty( $plugins ) ) {
				gc_redirect( self_admin_url( "plugins.php?error=true&main=true&plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			// Bail on all if any paths are invalid.
			// validate_file() returns truthy for invalid files.
			$invalid_plugin_files = array_filter( $plugins, 'validate_file' );
			if ( $invalid_plugin_files ) {
				gc_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			require ABSPATH . 'gc-admin/update.php';

			$parent_file = 'plugins.php';

			if ( ! isset( $_REQUEST['verify-delete'] ) ) {
				gc_enqueue_script( 'jquery' );
				require_once ABSPATH . 'gc-admin/admin-header.php';

				?>
				<div class="wrap">
				<?php

				$plugin_info              = array();
				$have_non_network_plugins = false;

				foreach ( (array) $plugins as $plugin ) {
					$plugin_slug = dirname( $plugin );

					if ( '.' === $plugin_slug ) {
						$data = get_plugin_data( GC_PLUGIN_DIR . '/' . $plugin );
						if ( $data ) {
							$plugin_info[ $plugin ]                     = $data;
							$plugin_info[ $plugin ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
							if ( ! $plugin_info[ $plugin ]['Network'] ) {
								$have_non_network_plugins = true;
							}
						}
					} else {
						// Get plugins list from that folder.
						$folder_plugins = get_plugins( '/' . $plugin_slug );
						if ( $folder_plugins ) {
							foreach ( $folder_plugins as $plugin_file => $data ) {
								$plugin_info[ $plugin_file ]                     = _get_plugin_data_markup_translate( $plugin_file, $data );
								$plugin_info[ $plugin_file ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
								if ( ! $plugin_info[ $plugin_file ]['Network'] ) {
									$have_non_network_plugins = true;
								}
							}
						}
					}
				}

				$plugins_to_delete = count( $plugin_info );

				?>
				<?php if ( 1 === $plugins_to_delete ) : ?>
					<h1><?php _e( '删除插件' ); ?></h1>
					<?php if ( $have_non_network_plugins && is_network_admin() ) : ?>
						<div class="error"><p><strong><?php _e( '注意：' ); ?></strong> <?php _e( '此插件可能已经在站点网络中的其他站点上启用。' ); ?></p></div>
					<?php endif; ?>
					<p><?php _e( '您将要移除以下插件：' ); ?></p>
				<?php else : ?>
					<h1><?php _e( '删除插件' ); ?></h1>
					<?php if ( $have_non_network_plugins && is_network_admin() ) : ?>
						<div class="error"><p><strong><?php _e( '注意：' ); ?></strong> <?php _e( '这些插件可能已经在站点网络中的其他站点上启用。' ); ?></p></div>
					<?php endif; ?>
					<p><?php _e( '您将要移除以下插件：' ); ?></p>
				<?php endif; ?>
					<ul class="ul-disc">
						<?php

						$data_to_delete = false;

						foreach ( $plugin_info as $plugin ) {
							if ( $plugin['is_uninstallable'] ) {
								/* translators: 1: Plugin name, 2: Plugin author. */
								echo '<li>', sprintf( __( '由%2$s制作的%1$s（<strong>其数据也将被一并删除</strong>）' ), '<strong>' . $plugin['Name'] . '</strong>', '<em>' . $plugin['AuthorName'] . '</em>' ), '</li>';
								$data_to_delete = true;
							} else {
								/* translators: 1: Plugin name, 2: Plugin author. */
								echo '<li>', sprintf( _x( '由%2$s制作的%1$s', 'plugin' ), '<strong>' . $plugin['Name'] . '</strong>', '<em>' . $plugin['AuthorName'] ) . '</em>', '</li>';
							}
						}

						?>
					</ul>
				<p>
				<?php

				if ( $data_to_delete ) {
					_e( '您确定要删除这些文件和数据吗？' );
				} else {
					_e( '您确定要删除这些文件吗？' );
				}

				?>
				</p>
				<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php

					foreach ( (array) $plugins as $plugin ) {
						echo '<input type="hidden" name="checked[]" value="' . esc_attr( $plugin ) . '" />';
					}

					?>
					<?php gc_nonce_field( 'bulk-plugins' ); ?>
					<?php submit_button( $data_to_delete ? __( '是，删除这些文件和数据' ) : __( '是，删除这些文件' ), '', 'submit', false ); ?>
				</form>
				<?php

				$referer = gc_get_referer();

				?>
				<form method="post" action="<?php echo $referer ? esc_url( $referer ) : ''; ?>" style="display:inline;">
					<?php submit_button( __( '不，返回到插件列表' ), '', 'submit', false ); ?>
				</form>
				</div>
				<?php

				require_once ABSPATH . 'gc-admin/admin-footer.php';
				exit;
			} else {
				$plugins_to_delete = count( $plugins );
			} // End if verify-delete.

			$delete_result = delete_plugins( $plugins );

			// Store the result in a cache rather than a URL param due to object type & length.
			set_transient( 'plugins_delete_result_' . $user_ID, $delete_result );
			gc_redirect( self_admin_url( "plugins.php?deleted=$plugins_to_delete&plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		case 'clear-recent-list':
			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', array() );
			} else {
				update_site_option( 'recently_activated', array() );
			}

			break;
		case 'resume':
			if ( is_multisite() ) {
				return;
			}

			if ( ! current_user_can( 'resume_plugin', $plugin ) ) {
				gc_die( __( '抱歉，您不能恢复此插件。' ) );
			}

			check_admin_referer( 'resume-plugin_' . $plugin );

			$result = resume_plugin( $plugin, self_admin_url( "plugins.php?error=resuming&plugin_status=$status&paged=$page&s=$s" ) );

			if ( is_gc_error( $result ) ) {
				gc_die( $result );
			}

			gc_redirect( self_admin_url( "plugins.php?resume=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		case 'enable-auto-update':
		case 'disable-auto-update':
		case 'enable-auto-update-selected':
		case 'disable-auto-update-selected':
			if ( ! current_user_can( 'update_plugins' ) || ! gc_is_auto_update_enabled_for_type( 'plugin' ) ) {
				gc_die( __( '抱歉，您不能管理插件自动更新。' ) );
			}

			if ( is_multisite() && ! is_network_admin() ) {
				gc_die( __( '请联系网络管理员以管理插件的自动更新。' ) );
			}

			$redirect = self_admin_url( "plugins.php?plugin_status={$status}&paged={$page}&s={$s}" );

			if ( 'enable-auto-update' === $action || 'disable-auto-update' === $action ) {
				if ( empty( $plugin ) ) {
					gc_redirect( $redirect );
					exit;
				}

				check_admin_referer( 'updates' );
			} else {
				if ( empty( $_POST['checked'] ) ) {
					gc_redirect( $redirect );
					exit;
				}

				check_admin_referer( 'bulk-plugins' );
			}

			$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

			if ( 'enable-auto-update' === $action ) {
				$auto_updates[] = $plugin;
				$auto_updates   = array_unique( $auto_updates );
				$redirect       = add_query_arg( array( 'enabled-auto-update' => 'true' ), $redirect );
			} elseif ( 'disable-auto-update' === $action ) {
				$auto_updates = array_diff( $auto_updates, array( $plugin ) );
				$redirect     = add_query_arg( array( 'disabled-auto-update' => 'true' ), $redirect );
			} else {
				$plugins = (array) gc_unslash( $_POST['checked'] );

				if ( 'enable-auto-update-selected' === $action ) {
					$new_auto_updates = array_merge( $auto_updates, $plugins );
					$new_auto_updates = array_unique( $new_auto_updates );
					$query_args       = array( 'enabled-auto-update-multi' => 'true' );
				} else {
					$new_auto_updates = array_diff( $auto_updates, $plugins );
					$query_args       = array( 'disabled-auto-update-multi' => 'true' );
				}

				// Return early if all selected plugins already have auto-updates enabled or disabled.
				// Must use non-strict comparison, so that array order is not treated as significant.
				if ( $new_auto_updates == $auto_updates ) { // phpcs:ignore GeChiUI.PHP.StrictComparisons.LooseComparison
					gc_redirect( $redirect );
					exit;
				}

				$auto_updates = $new_auto_updates;
				$redirect     = add_query_arg( $query_args, $redirect );
			}

			/** This filter is documented in gc-admin/includes/class-gc-plugins-list-table.php */
			$all_items = apply_filters( 'all_plugins', get_plugins() );

			// Remove plugins that don't exist or have been deleted since the option was last updated.
			$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

			update_site_option( 'auto_update_plugins', $auto_updates );

			gc_redirect( $redirect );
			exit;
		default:
			if ( isset( $_POST['checked'] ) ) {
				check_admin_referer( 'bulk-plugins' );

				$screen   = get_current_screen()->id;
				$sendback = gc_get_referer();
				$plugins  = isset( $_POST['checked'] ) ? (array) gc_unslash( $_POST['checked'] ) : array();

				/** This action is documented in gc-admin/edit.php */
				$sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $action, $plugins ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
				gc_safe_redirect( $sendback );
				exit;
			}
			break;
	}
}

$gc_list_table->prepare_items();

gc_enqueue_script( 'plugin-install' );
add_thickbox();

add_screen_option( 'per_page', array( 'default' => 999 ) );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '插件拓展、拓充GeChiUI的功能。插件安装后，您可以在这里启用或者禁用它。' ) . '</p>' .
				'<p>' . __( '搜索已安装的插件将在主题的名字、描述、作者和标签中搜索。' ) . ' <span id="live-search-desc" class="hide-if-no-js">' . __( '搜索结果会随着您的输入而不断更新。' ) . '</span></p>' .
				'<p>' . sprintf(
					/* translators: %s: GeChiUI Plugin Directory URL. */
					__( '若您希望从更多插件中选择，请点击“安装插件”按钮。切换到“安装插件”界面后，您就可以在<a href="%s">www.GeChiUI.com插件目录</a>中浏览或搜索插件。GeChiUI插件目录中的主题是由第三方设计开发的，且与GeChiUI所用的版权许可证相兼容。最棒的是，它们都是免费的！' ),
					__( 'https://www.gechiui.com/plugins/' )
				) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'compatibility-problems',
		'title'   => __( '故障排除' ),
		'content' =>
				'<p>' . __( '大部分情况下，每个插件都能和其他插件及GeChiUI核心程序正常配合。但是，有时某些插件会和其他插件产生冲突，进而产生兼容性问题，如果您的站点工作异常，则可能是这个原因导致的。您可尝试禁用所有插件，再逐个启用，以排除问题。' ) . '</p>' .
				'<p>' . sprintf(
					/* translators: %s: GC_PLUGIN_DIR constant value. */
					__( '如果因为插件出现问题而让您无法使用GeChiUI，您可以删除或重命名%s目录中的相应文件，该插件就会被自动禁用。' ),
					'<code>' . GC_PLUGIN_DIR . '</code>'
				) . '</p>',
	)
);

$help_sidebar_autoupdates = '';

if ( current_user_can( 'update_plugins' ) && gc_is_auto_update_enabled_for_type( 'plugin' ) ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'plugins-themes-auto-updates',
			'title'   => __( '自动更新' ),
			'content' =>
					'<p>' . __( '每个插件均可单独启用或禁用自动更新功能。对于已启用自动更新的插件，系统将显示下一次自动更新的预计日期。自动更新功能运行正常与否，取决于GC-Cron任务计划系统。' ) . '</p>' .
					'<p>' . __( '自动更新仅适用于受www.GeChiUI.com认可的插件或兼容的更新系统提供的插件。' ) . '</p>' .
					'<p>' . __( '请注意：第三方主题、插件或自定义代码，都有可能覆盖GeChiUI的计划任务。' ) . '</p>',
		)
	);

	$help_sidebar_autoupdates = '<p>' . __( '<a href="https://www.gechiui.com/support/plugins-themes-auto-updates/">了解更多：自动更新功能文档</a>' ) . '</p>';
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/managing-plugins/">管理插件文档</a>' ) . '</p>' .
	$help_sidebar_autoupdates .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选插件列表' ),
		'heading_pagination' => __( '插件列表导航' ),
		'heading_list'       => __( '插件列表' ),
	)
);

// Used in the HTML title tag.
$title       = __( '插件' );
$parent_file = 'plugins.php';

require_once ABSPATH . 'gc-admin/admin-header.php';

$invalid = validate_active_plugins();
if ( ! empty( $invalid ) ) {
	foreach ( $invalid as $plugin_file => $error ) {
		echo '<div id="message" class="error"><p>';
		printf(
			/* translators: 1: Plugin file, 2: Error message. */
			__( '插件%1$s已被禁用，因为以下错误：%2$s' ),
			'<code>' . esc_html( $plugin_file ) . '</code>',
			$error->get_error_message()
		);
		echo '</p></div>';
	}
}

if ( isset( $_GET['error'] ) ) :

	if ( isset( $_GET['main'] ) ) {
		$errmsg = __( '您不能删除主站点正在使用的插件。' );
	} elseif ( isset( $_GET['charsout'] ) ) {
		$errmsg = sprintf(
			/* translators: %d: Number of characters. */
			_n(
				'这个插件在启用的过程中产生了%d个字符的<strong>异常输出</strong>。',
				'这个插件在启用的过程中产生了%d个字符的<strong>异常输出</strong>。',
				$_GET['charsout']
			),
			$_GET['charsout']
		);
		$errmsg .= ' ' . __( '如果您遇到了“headers already sent”错误、联合feed（如RSS）出错等问题，请尝试禁用或移除本插件。' );
	} elseif ( 'resuming' === $_GET['error'] ) {
		$errmsg = __( '此插件不能被恢复，因其触发了一个<strong>致命错误</strong>。' );
	} else {
		$errmsg = __( '无法启用插件，因为它引起了一个<strong>致命错误</strong>（fatal error）。' );
	}

	?>
	<div id="message" class="error"><p><?php echo $errmsg; ?></p>
	<?php

	if ( ! isset( $_GET['main'] ) && ! isset( $_GET['charsout'] )
		&& isset( $_GET['_error_nonce'] ) && gc_verify_nonce( $_GET['_error_nonce'], 'plugin-activation-error_' . $plugin )
	) {
		$iframe_url = add_query_arg(
			array(
				'action'   => 'error_scrape',
				'plugin'   => urlencode( $plugin ),
				'_gcnonce' => urlencode( $_GET['_error_nonce'] ),
			),
			admin_url( 'plugins.php' )
		);

		?>
		<iframe style="border:0" width="100%" height="70px" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
		<?php
	}

	?>
	</div>
	<?php
elseif ( isset( $_GET['deleted'] ) ) :
	$delete_result = get_transient( 'plugins_delete_result_' . $user_ID );
	// Delete it once we're done.
	delete_transient( 'plugins_delete_result_' . $user_ID );

	if ( is_gc_error( $delete_result ) ) :
		?>
		<div id="message" class="error notice is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: Error message. */
					__( '无法删除插件，因为发生了错误：%s' ),
					$delete_result->get_error_message()
				);
				?>
			</p>
		</div>
		<?php else : ?>
		<div id="message" class="updated notice is-dismissible">
			<p>
				<?php
				if ( 1 === (int) $_GET['deleted'] ) {
					_e( '选择的插件已被删除。' );
				} else {
					_e( '选择的插件已被删除。' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>
<?php elseif ( isset( $_GET['activate'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '插件已启用。' ); ?></p></div>
<?php elseif ( isset( $_GET['activate-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '选择的插件已启用。' ); ?></p></div>
<?php elseif ( isset( $_GET['deactivate'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '插件已禁用。' ); ?></p></div>
<?php elseif ( isset( $_GET['deactivate-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '选择的插件已禁用。' ); ?></p></div>
<?php elseif ( 'update-selected' === $action ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '所有选择的插件都是最新的。' ); ?></p></div>
<?php elseif ( isset( $_GET['resume'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '插件已恢复。' ); ?></p></div>
<?php elseif ( isset( $_GET['enabled-auto-update'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '插件将自动更新。' ); ?></p></div>
<?php elseif ( isset( $_GET['disabled-auto-update'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '插件将不再自动更新。' ); ?></p></div>
<?php elseif ( isset( $_GET['enabled-auto-update-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '选中的插件将会自动升级。' ); ?></p></div>
<?php elseif ( isset( $_GET['disabled-auto-update-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( '所选插件将不再自动更新。' ); ?></p></div>
<?php endif; ?>

<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( ( ! is_multisite() || is_network_admin() ) && current_user_can( 'install_plugins' ) ) {
	?>
	<a href="<?php echo self_admin_url( 'plugin-install.php' ); ?>" class="page-title-action"><?php echo esc_html_x( '安装插件', 'plugin' ); ?></a>
	<?php
}

if ( strlen( $s ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( '搜索结果：%s' ),
		'<strong>' . esc_html( urldecode( $s ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="gc-header-end">

<?php
/**
 * Fires before the plugins list table is rendered.
 *
 * This hook also fires before the plugins list table is rendered in the Network Admin.
 *
 * Please note: The 'active' portion of the hook name does not refer to whether the current
 * view is for active plugins, but rather all plugins actively-installed.
 *
 *
 *
 * @param array[] $plugins_all An array of arrays containing information on all installed plugins.
 */
do_action( 'pre_current_active_plugins', $plugins['all'] );
?>

<?php $gc_list_table->views(); ?>

<form class="search-form search-plugins" method="get">
<?php $gc_list_table->search_box( __( '搜索已安装的插件' ), 'plugin' ); ?>
</form>

<form method="post" id="bulk-action-form">

<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $status ); ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr( $page ); ?>" />

<?php $gc_list_table->display(); ?>
</form>

	<span class="spinner"></span>
</div>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();
gc_print_update_row_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
