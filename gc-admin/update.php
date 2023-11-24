<?php
/**
 * Update/Install Plugin/Theme administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( ! defined( 'IFRAME_REQUEST' )
	&& isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ), true )
) {
	define( 'IFRAME_REQUEST', true );
}

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

gc_enqueue_script( 'gc-a11y' );

if ( isset( $_GET['action'] ) ) {
	$plugin = isset( $_REQUEST['plugin'] ) ? trim( $_REQUEST['plugin'] ) : '';
	$theme  = isset( $_REQUEST['theme'] ) ? urldecode( $_REQUEST['theme'] ) : '';
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

	if ( 'update-selected' === $action ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上升级插件。' ) );
		}

		check_admin_referer( 'bulk-update-plugins' );

		if ( isset( $_GET['plugins'] ) ) {
			$plugins = explode( ',', stripslashes( $_GET['plugins'] ) );
		} elseif ( isset( $_POST['checked'] ) ) {
			$plugins = (array) $_POST['checked'];
		} else {
			$plugins = array();
		}

		$plugins = array_map( 'urldecode', $plugins );

		$url   = 'update.php?action=update-selected&amp;plugins=' . urlencode( implode( ',', $plugins ) );
		$nonce = 'bulk-update-plugins';

		gc_enqueue_script( 'updates' );
		iframe_header();

		$upgrader = new Plugin_Upgrader( new Bulk_Plugin_Upgrader_Skin( compact( 'nonce', 'url' ) ) );
		$upgrader->bulk_upgrade( $plugins );

		iframe_footer();

	} elseif ( 'upgrade-plugin' === $action ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上升级插件。' ) );
		}

		check_admin_referer( 'upgrade-plugin_' . $plugin );

		// Used in the HTML title tag.
		$title        = __( '升级插件' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugins.php';

		gc_enqueue_script( 'updates' );
		require_once ABSPATH . 'gc-admin/admin-header.php';

		$nonce = 'upgrade-plugin_' . $plugin;
		$url   = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );

		$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin' ) ) );
		$upgrader->upgrade( $plugin );

		require_once ABSPATH . 'gc-admin/admin-footer.php';

	} elseif ( 'activate-plugin' === $action ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上升级插件。' ) );
		}

		check_admin_referer( 'activate-plugin_' . $plugin );
		if ( ! isset( $_GET['failure'] ) && ! isset( $_GET['success'] ) ) {
			gc_redirect( admin_url( 'update.php?action=activate-plugin&failure=true&plugin=' . urlencode( $plugin ) . '&_gcnonce=' . $_GET['_gcnonce'] ) );
			activate_plugin( $plugin, '', ! empty( $_GET['networkwide'] ), true );
			gc_redirect( admin_url( 'update.php?action=activate-plugin&success=true&plugin=' . urlencode( $plugin ) . '&_gcnonce=' . $_GET['_gcnonce'] ) );
			die();
		}
		iframe_header( __( '重新启用插件' ), true );
		if ( isset( $_GET['success'] ) ) {
			echo '<p>' . __( '插件重新启用成功。' ) . '</p>';
		}

		if ( isset( $_GET['failure'] ) ) {
			echo '<p>' . __( '插件重新启用失败，因为发生了一个致命错误（fatal error）。' ) . '</p>';

			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
			ini_set( 'display_errors', true ); // Ensure that fatal errors are displayed.
			gc_register_plugin_realpath( GC_PLUGIN_DIR . '/' . $plugin );
			include GC_PLUGIN_DIR . '/' . $plugin;
		}
		iframe_footer();
	} elseif ( 'install-plugin' === $action ) {

		if ( ! current_user_can( 'install_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装插件。' ) );
		}

		include_once ABSPATH . 'gc-admin/includes/plugin-install.php'; // For plugins_api().

		check_admin_referer( 'install-plugin_' . $plugin );
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		if ( is_gc_error( $api ) ) {
			gc_die( $api );
		}

		// Used in the HTML title tag.
		$title        = __( '安装插件' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugin-install.php';

		require_once ABSPATH . 'gc-admin/admin-header.php';

		/* translators: %s: Plugin name and version. */
		$title = sprintf( __( '正在安装插件：%s' ), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url   = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );
		if ( isset( $_GET['from'] ) ) {
			$url .= '&from=' . urlencode( stripslashes( $_GET['from'] ) );
		}

		$type = 'web'; // Install plugin type, From Web or an Upload.

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
		$upgrader->install( $api->download_link );

		require_once ABSPATH . 'gc-admin/admin-footer.php';

	} elseif ( 'upload-plugin' === $action ) {

		if ( ! current_user_can( 'upload_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装插件。' ) );
		}

		check_admin_referer( 'plugin-upload' );

		$file_upload = new File_Upload_Upgrader( 'pluginzip', 'package' );

		// Used in the HTML title tag.
		$title        = __( '上传插件' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugin-install.php';

		require_once ABSPATH . 'gc-admin/admin-header.php';

		/* translators: %s: File name. */
		$title = sprintf( __( '正在安装您上传的插件：%s' ), esc_html( basename( $file_upload->filename ) ) );
		$nonce = 'plugin-upload';
		$url   = add_query_arg( array( 'package' => $file_upload->id ), 'update.php?action=upload-plugin' );
		$type  = 'upload'; // Install plugin type, From Web or an Upload.

		$overwrite = isset( $_GET['overwrite'] ) ? sanitize_text_field( $_GET['overwrite'] ) : '';
		$overwrite = in_array( $overwrite, array( 'update-plugin', 'downgrade-plugin' ), true ) ? $overwrite : '';

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'type', 'title', 'nonce', 'url', 'overwrite' ) ) );
		$result   = $upgrader->install( $file_upload->package, array( 'overwrite_package' => $overwrite ) );

		if ( $result || is_gc_error( $result ) ) {
			$file_upload->cleanup();
		}

		require_once ABSPATH . 'gc-admin/admin-footer.php';

	} elseif ( 'upload-plugin-cancel-overwrite' === $action ) {
		if ( ! current_user_can( 'upload_plugins' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装插件。' ) );
		}

		check_admin_referer( 'plugin-upload-cancel-overwrite' );

		// Make sure the attachment still exists, or File_Upload_Upgrader will call gc_die()
		// that shows a generic "请选择一个文件" error.
		if ( ! empty( $_GET['package'] ) ) {
			$attachment_id = (int) $_GET['package'];

			if ( get_post( $attachment_id ) ) {
				$file_upload = new File_Upload_Upgrader( 'pluginzip', 'package' );
				$file_upload->cleanup();
			}
		}

		gc_redirect( self_admin_url( 'plugin-install.php' ) );
		exit;
	} elseif ( 'upgrade-theme' === $action ) {

		if ( ! current_user_can( 'update_themes' ) ) {
			gc_die( __( '抱歉，您不能在此系统上升级主题。' ) );
		}

		check_admin_referer( 'upgrade-theme_' . $theme );

		gc_enqueue_script( 'updates' );

		// Used in the HTML title tag.
		$title        = __( '升级主题' );
		$parent_file  = 'themes.php';
		$submenu_file = 'themes.php';

		require_once ABSPATH . 'gc-admin/admin-header.php';

		$nonce = 'upgrade-theme_' . $theme;
		$url   = 'update.php?action=upgrade-theme&theme=' . urlencode( $theme );

		$upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'theme' ) ) );
		$upgrader->upgrade( $theme );

		require_once ABSPATH . 'gc-admin/admin-footer.php';
	} elseif ( 'update-selected-themes' === $action ) {
		if ( ! current_user_can( 'update_themes' ) ) {
			gc_die( __( '抱歉，您不能在此系统上升级主题。' ) );
		}

		check_admin_referer( 'bulk-update-themes' );

		if ( isset( $_GET['themes'] ) ) {
			$themes = explode( ',', stripslashes( $_GET['themes'] ) );
		} elseif ( isset( $_POST['checked'] ) ) {
			$themes = (array) $_POST['checked'];
		} else {
			$themes = array();
		}

		$themes = array_map( 'urldecode', $themes );

		$url   = 'update.php?action=update-selected-themes&amp;themes=' . urlencode( implode( ',', $themes ) );
		$nonce = 'bulk-update-themes';

		gc_enqueue_script( 'updates' );
		iframe_header();

		$upgrader = new Theme_Upgrader( new Bulk_Theme_Upgrader_Skin( compact( 'nonce', 'url' ) ) );
		$upgrader->bulk_upgrade( $themes );

		iframe_footer();
	} elseif ( 'install-theme' === $action ) {

		if ( ! current_user_can( 'install_themes' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装主题。' ) );
		}

		include_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php'; // For themes_api().

		check_admin_referer( 'install-theme_' . $theme );
		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $theme,
				'fields' => array(
					'sections' => false,
					'tags'     => false,
				),
			)
		); // Save on a bit of bandwidth.

		if ( is_gc_error( $api ) ) {
			gc_die( $api );
		}

		// Used in the HTML title tag.
		$title        = __( '安装主题' );
		$parent_file  = 'themes.php';
		$submenu_file = 'themes.php';

		require_once ABSPATH . 'gc-admin/admin-header.php';

		/* translators: %s: Theme name and version. */
		$title = sprintf( __( '正在安装主题：%s' ), $api->name . ' ' . $api->version );
		$nonce = 'install-theme_' . $theme;
		$url   = 'update.php?action=install-theme&theme=' . urlencode( $theme );
		$type  = 'web'; // Install theme type, From Web or an Upload.

		$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
		$upgrader->install( $api->download_link );

		require_once ABSPATH . 'gc-admin/admin-footer.php';

	} elseif ( 'upload-theme' === $action ) {

		if ( ! current_user_can( 'upload_themes' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装主题。' ) );
		}

		check_admin_referer( 'theme-upload' );

		$file_upload = new File_Upload_Upgrader( 'themezip', 'package' );

		// Used in the HTML title tag.
		$title        = __( '上传主题' );
		$parent_file  = 'themes.php';
		$submenu_file = 'theme-install.php';

		require_once ABSPATH . 'gc-admin/admin-header.php';

		/* translators: %s: File name. */
		$title = sprintf( __( '正在安装您上传的主题：%s' ), esc_html( basename( $file_upload->filename ) ) );
		$nonce = 'theme-upload';
		$url   = add_query_arg( array( 'package' => $file_upload->id ), 'update.php?action=upload-theme' );
		$type  = 'upload'; // Install theme type, From Web or an Upload.

		$overwrite = isset( $_GET['overwrite'] ) ? sanitize_text_field( $_GET['overwrite'] ) : '';
		$overwrite = in_array( $overwrite, array( 'update-theme', 'downgrade-theme' ), true ) ? $overwrite : '';

		$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact( 'type', 'title', 'nonce', 'url', 'overwrite' ) ) );
		$result   = $upgrader->install( $file_upload->package, array( 'overwrite_package' => $overwrite ) );

		if ( $result || is_gc_error( $result ) ) {
			$file_upload->cleanup();
		}

		require_once ABSPATH . 'gc-admin/admin-footer.php';

	} elseif ( 'upload-theme-cancel-overwrite' === $action ) {
		if ( ! current_user_can( 'upload_themes' ) ) {
			gc_die( __( '抱歉，您不能在此系统上安装主题。' ) );
		}

		check_admin_referer( 'theme-upload-cancel-overwrite' );

		// Make sure the attachment still exists, or File_Upload_Upgrader will call gc_die()
		// that shows a generic "请选择一个文件" error.
		if ( ! empty( $_GET['package'] ) ) {
			$attachment_id = (int) $_GET['package'];

			if ( get_post( $attachment_id ) ) {
				$file_upload = new File_Upload_Upgrader( 'themezip', 'package' );
				$file_upload->cleanup();
			}
		}

		gc_redirect( self_admin_url( 'theme-install.php' ) );
		exit;
	} else {
		/**
		 * Fires when a custom plugin or theme update request is received.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the action
		 * provided in the request for gc-admin/update.php. Can be used to
		 * provide custom update functionality for themes and plugins.
		 *
		 */
		do_action( "update-custom_{$action}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	}
}
