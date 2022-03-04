<?php
/**
 * Upgrade API: Plugin_Upgrader class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Core class used for upgrading/installing plugins.
 *
 * It is designed to upgrade/install plugins from a local zip, remote zip URL,
 * or uploaded zip file.
 *
 *
 *
 *
 * @see GC_Upgrader
 */
class Plugin_Upgrader extends GC_Upgrader {

	/**
	 * Plugin upgrade result.
	 *
	 * @var array|GC_Error $result
	 *
	 * @see GC_Upgrader::$result
	 */
	public $result;

	/**
	 * Whether a bulk upgrade/installation is being performed.
	 *
	 * @var bool $bulk
	 */
	public $bulk = false;

	/**
	 * New plugin info.
	 *
	 * @var array $new_plugin_data
	 *
	 * @see check_package()
	 */
	public $new_plugin_data = array();

	/**
	 * Initialize the upgrade strings.
	 *
	 */
	public function upgrade_strings() {
		$this->strings['up_to_date'] = __( '插件已是最新版。' );
		$this->strings['no_package'] = __( '升级包不可用。' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package']  = sprintf( __( '正在从 %s 下载更新…' ), '<span class="code">%s</span>' );
		$this->strings['unpack_package']       = __( '正在解压缩升级文件&#8230;' );
		$this->strings['remove_old']           = __( '正在移除插件的旧版本&#8230;' );
		$this->strings['remove_old_failed']    = __( '无法移除旧插件。' );
		$this->strings['process_failed']       = __( '插件升级失败。' );
		$this->strings['process_success']      = __( '插件升级成功。' );
		$this->strings['process_bulk_success'] = __( '插件成功升级。' );
	}

	/**
	 * Initialize the installation strings.
	 *
	 */
	public function install_strings() {
		$this->strings['no_package'] = __( '安装包不可用。' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package'] = sprintf( __( '正在从 %s 下载安装包…' ), '<span class="code">%s</span>' );
		$this->strings['unpack_package']      = __( '正在解压缩安装包&#8230;' );
		$this->strings['installing_package']  = __( '正在安装插件&#8230;' );
		$this->strings['remove_old']          = __( '正在删除当前版本插件&#8230;' );
		$this->strings['remove_old_failed']   = __( '无法移除当前版本插件。' );
		$this->strings['no_files']            = __( '这个插件不包含文件。' );
		$this->strings['process_failed']      = __( '插件安装失败。' );
		$this->strings['process_success']     = __( '插件安装成功。' );
		/* translators: 1: Plugin name, 2: Plugin version. */
		$this->strings['process_success_specific'] = __( '成功安装插件<strong>%1$s %2$s</strong>。' );

		if ( ! empty( $this->skin->overwrite ) ) {
			if ( 'update-plugin' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( '正在升级插件…' );
				$this->strings['process_failed']     = __( '插件升级失败。' );
				$this->strings['process_success']    = __( '插件升级成功。' );
			}

			if ( 'downgrade-plugin' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( '正在降级插件&#8230;' );
				$this->strings['process_failed']     = __( '插件降级失败。' );
				$this->strings['process_success']    = __( '插件已成功降级。' );
			}
		}
	}

	/**
	 * Install a plugin package.
	 *
	 *
	 * @param string $package The full local path or URI of the package.
	 * @param array  $args {
	 *     Optional. Other arguments for installing a plugin package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|GC_Error True if the installation was successful, false or a GC_Error otherwise.
	 */
	public function install( $package, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
			'overwrite_package'  => false, // Do not overwrite files.
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );

		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so gc_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'gc_clean_plugins_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $package,
				'destination'       => GC_PLUGIN_DIR,
				'clear_destination' => $parsed_args['overwrite_package'],
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'plugin',
					'action' => 'install',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'gc_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );

		if ( ! $this->result || is_gc_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of plugin update information.
		gc_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		if ( $parsed_args['overwrite_package'] ) {
			/**
			 * Fires when the upgrader has successfully overwritten a currently installed
			 * plugin or theme with an uploaded zip package.
			 *
		
			 *
			 * @param string  $package      The package file.
			 * @param array   $data         The new plugin or theme data.
			 * @param string  $package_type The package type ('plugin' or 'theme').
			 */
			do_action( 'upgrader_overwrote_package', $package, $this->new_plugin_data, 'plugin' );
		}

		return true;
	}

	/**
	 * Upgrade a plugin.
	 *
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 * @param array  $args {
	 *     Optional. Other arguments for upgrading a plugin package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|GC_Error True if the upgrade was successful, false or a GC_Error object otherwise.
	 */
	public function upgrade( $plugin, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );
		if ( ! isset( $current->response[ $plugin ] ) ) {
			$this->skin->before();
			$this->skin->set_result( false );
			$this->skin->error( 'up_to_date' );
			$this->skin->after();
			return false;
		}

		// Get the URL to the zip file.
		$r = $current->response[ $plugin ];

		add_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
		add_filter( 'upgrader_pre_install', array( $this, 'active_before' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );
		add_filter( 'upgrader_post_install', array( $this, 'active_after' ), 10, 2 );
		// There's a Trac ticket to move up the directory for zips which are made a bit differently, useful for non-.org plugins.
		// 'source_selection' => array( $this, 'source_selection' ),
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so gc_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'gc_clean_plugins_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $r->package,
				'destination'       => GC_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin,
					'type'   => 'plugin',
					'action' => 'update',
				),
			)
		);

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_action( 'upgrader_process_complete', 'gc_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
		remove_filter( 'upgrader_pre_install', array( $this, 'active_before' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'active_after' ) );

		if ( ! $this->result || is_gc_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of plugin update information.
		gc_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when plugins update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		if ( isset( $past_failure_emails[ $plugin ] ) ) {
			unset( $past_failure_emails[ $plugin ] );
			update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );
		}

		return true;
	}

	/**
	 * Bulk upgrade several plugins at once.
	 *
	 *
	 * @param string[] $plugins Array of paths to plugin files relative to the plugins directory.
	 * @param array    $args {
	 *     Optional. Other arguments for upgrading several plugins at once.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful. Default true.
	 * }
	 * @return array|false An array of results indexed by plugin file, or false if unable to connect to the filesystem.
	 */
	public function bulk_upgrade( $plugins, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );

		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );

		$this->skin->header();

		// Connect to the filesystem first.
		$res = $this->fs_connect( array( GC_CONTENT_DIR, GC_PLUGIN_DIR ) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		/*
		 * Only start maintenance mode if:
		 * - running Multisite and there are one or more plugins specified, OR
		 * - a plugin with an update available is currently active.
		 * @todo For multisite, maintenance mode should only kick in for individual sites if at all possible.
		 */
		$maintenance = ( is_multisite() && ! empty( $plugins ) );
		foreach ( $plugins as $plugin ) {
			$maintenance = $maintenance || ( is_plugin_active( $plugin ) && isset( $current->response[ $plugin ] ) );
		}
		if ( $maintenance ) {
			$this->maintenance_mode( true );
		}

		$results = array();

		$this->update_count   = count( $plugins );
		$this->update_current = 0;
		foreach ( $plugins as $plugin ) {
			$this->update_current++;
			$this->skin->plugin_info = get_plugin_data( GC_PLUGIN_DIR . '/' . $plugin, false, true );

			if ( ! isset( $current->response[ $plugin ] ) ) {
				$this->skin->set_result( 'up_to_date' );
				$this->skin->before();
				$this->skin->feedback( 'up_to_date' );
				$this->skin->after();
				$results[ $plugin ] = true;
				continue;
			}

			// Get the URL to the zip file.
			$r = $current->response[ $plugin ];

			$this->skin->plugin_active = is_plugin_active( $plugin );

			$result = $this->run(
				array(
					'package'           => $r->package,
					'destination'       => GC_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working'     => true,
					'is_multi'          => true,
					'hook_extra'        => array(
						'plugin' => $plugin,
					),
				)
			);

			$results[ $plugin ] = $result;

			// Prevent credentials auth screen from displaying multiple times.
			if ( false === $result ) {
				break;
			}
		} // End foreach $plugins.

		$this->maintenance_mode( false );

		// Force refresh of plugin update information.
		gc_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		/** This action is documented in gc-admin/includes/class-gc-upgrader.php */
		do_action(
			'upgrader_process_complete',
			$this,
			array(
				'action'  => 'update',
				'type'    => 'plugin',
				'bulk'    => true,
				'plugins' => $plugins,
			)
		);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when plugins update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		foreach ( $results as $plugin => $result ) {
			// Maintain last failure notification when plugins failed to update manually.
			if ( ! $result || is_gc_error( $result ) || ! isset( $past_failure_emails[ $plugin ] ) ) {
				continue;
			}

			unset( $past_failure_emails[ $plugin ] );
		}

		update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );

		return $results;
	}

	/**
	 * Checks that the source package contains a valid plugin.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by Plugin_Upgrader::install().
	 *
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 * @global string             $gc_version    The GeChiUI version string.
	 *
	 * @param string $source The path to the downloaded package source.
	 * @return string|GC_Error The source as passed, or a GC_Error object on failure.
	 */
	public function check_package( $source ) {
		global $gc_filesystem, $gc_version;

		$this->new_plugin_data = array();

		if ( is_gc_error( $source ) ) {
			return $source;
		}

		$working_directory = str_replace( $gc_filesystem->gc_content_dir(), trailingslashit( GC_CONTENT_DIR ), $source );
		if ( ! is_dir( $working_directory ) ) { // Sanity check, if the above fails, let's not prevent installation.
			return $source;
		}

		// Check that the folder contains at least 1 valid plugin.
		$files = glob( $working_directory . '*.php' );
		if ( $files ) {
			foreach ( $files as $file ) {
				$info = get_plugin_data( $file, false, false );
				if ( ! empty( $info['Name'] ) ) {
					$this->new_plugin_data = $info;
					break;
				}
			}
		}

		if ( empty( $this->new_plugin_data ) ) {
			return new GC_Error( 'incompatible_archive_no_plugins', $this->strings['incompatible_archive'], __( '没有找到有效的插件。' ) );
		}

		$requires_php = isset( $info['RequiresPHP'] ) ? $info['RequiresPHP'] : null;
		$requires_gc  = isset( $info['RequiresGC'] ) ? $info['RequiresGC'] : null;

		if ( ! is_php_version_compatible( $requires_php ) ) {
			$error = sprintf(
				/* translators: 1: Current PHP version, 2: Version required by the uploaded plugin. */
				__( '您的服务器PHP版本为%1$s，然而上传的插件要求版本为%2$s。' ),
				phpversion(),
				$requires_php
			);

			return new GC_Error( 'incompatible_php_required_version', $this->strings['incompatible_archive'], $error );
		}

		if ( ! is_gc_version_compatible( $requires_gc ) ) {
			$error = sprintf(
				/* translators: 1: Current GeChiUI version, 2: Version required by the uploaded plugin. */
				__( '您的GeChiUI版本为%1$s，但上传插件的GeChiUI版本要求为%2$s。' ),
				$gc_version,
				$requires_gc
			);

			return new GC_Error( 'incompatible_gc_required_version', $this->strings['incompatible_archive'], $error );
		}

		return $source;
	}

	/**
	 * Retrieve the path to the file that contains the plugin info.
	 *
	 * This isn't used internally in the class, but is called by the skins.
	 *
	 *
	 * @return string|false The full path to the main plugin file, or false.
	 */
	public function plugin_info() {
		if ( ! is_array( $this->result ) ) {
			return false;
		}
		if ( empty( $this->result['destination_name'] ) ) {
			return false;
		}

		// Ensure to pass with leading slash.
		$plugin = get_plugins( '/' . $this->result['destination_name'] );
		if ( empty( $plugin ) ) {
			return false;
		}

		// Assume the requested plugin is the first in the list.
		$pluginfiles = array_keys( $plugin );

		return $this->result['destination_name'] . '/' . $pluginfiles[0];
	}

	/**
	 * Deactivates a plugin before it is upgraded.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 *
	 * @param bool|GC_Error $return Upgrade offer return.
	 * @param array         $plugin Plugin package arguments.
	 * @return bool|GC_Error The passed in $return param or GC_Error.
	 */
	public function deactivate_plugin_before_upgrade( $return, $plugin ) {

		if ( is_gc_error( $return ) ) { // Bypass.
			return $return;
		}

		// When in cron (background updates) don't deactivate the plugin, as we require a browser to reactivate it.
		if ( gc_doing_cron() ) {
			return $return;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if ( empty( $plugin ) ) {
			return new GC_Error( 'bad_request', $this->strings['bad_request'] );
		}

		if ( is_plugin_active( $plugin ) ) {
			// Deactivate the plugin silently, Prevent deactivation hooks from running.
			deactivate_plugins( $plugin, true );
		}

		return $return;
	}

	/**
	 * Turns on maintenance mode before attempting to background update an active plugin.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 *
	 * @param bool|GC_Error $return Upgrade offer return.
	 * @param array         $plugin Plugin package arguments.
	 * @return bool|GC_Error The passed in $return param or GC_Error.
	 */
	public function active_before( $return, $plugin ) {
		if ( is_gc_error( $return ) ) {
			return $return;
		}

		// Only enable maintenance mode when in cron (background update).
		if ( ! gc_doing_cron() ) {
			return $return;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';

		// Only run if plugin is active.
		if ( ! is_plugin_active( $plugin ) ) {
			return $return;
		}

		// Change to maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( true );
		}

		return $return;
	}

	/**
	 * Turns off maintenance mode after upgrading an active plugin.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 *
	 * @param bool|GC_Error $return Upgrade offer return.
	 * @param array         $plugin Plugin package arguments.
	 * @return bool|GC_Error The passed in $return param or GC_Error.
	 */
	public function active_after( $return, $plugin ) {
		if ( is_gc_error( $return ) ) {
			return $return;
		}

		// Only disable maintenance mode when in cron (background update).
		if ( ! gc_doing_cron() ) {
			return $return;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';

		// Only run if plugin is active
		if ( ! is_plugin_active( $plugin ) ) {
			return $return;
		}

		// Time to remove maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( false );
		}

		return $return;
	}

	/**
	 * Deletes the old plugin during an upgrade.
	 *
	 * Hooked to the {@see 'upgrader_clear_destination'} filter by
	 * Plugin_Upgrader::upgrade() and Plugin_Upgrader::bulk_upgrade().
	 *
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @param bool|GC_Error $removed            Whether the destination was cleared.
	 *                                          True on success, GC_Error on failure.
	 * @param string        $local_destination  The local package destination.
	 * @param string        $remote_destination The remote package destination.
	 * @param array         $plugin             Extra arguments passed to hooked filters.
	 * @return bool|GC_Error
	 */
	public function delete_old_plugin( $removed, $local_destination, $remote_destination, $plugin ) {
		global $gc_filesystem;

		if ( is_gc_error( $removed ) ) {
			return $removed; // Pass errors through.
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if ( empty( $plugin ) ) {
			return new GC_Error( 'bad_request', $this->strings['bad_request'] );
		}

		$plugins_dir     = $gc_filesystem->gc_plugins_dir();
		$this_plugin_dir = trailingslashit( dirname( $plugins_dir . $plugin ) );

		if ( ! $gc_filesystem->exists( $this_plugin_dir ) ) { // If it's already vanished.
			return $removed;
		}

		// If plugin is in its own directory, recursively delete the directory.
		// Base check on if plugin includes directory separator AND that it's not the root plugin folder.
		if ( strpos( $plugin, '/' ) && $this_plugin_dir !== $plugins_dir ) {
			$deleted = $gc_filesystem->delete( $this_plugin_dir, true );
		} else {
			$deleted = $gc_filesystem->delete( $plugins_dir . $plugin );
		}

		if ( ! $deleted ) {
			return new GC_Error( 'remove_old_failed', $this->strings['remove_old_failed'] );
		}

		return true;
	}
}
