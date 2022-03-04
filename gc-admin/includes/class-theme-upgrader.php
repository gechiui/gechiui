<?php
/**
 * Upgrade API: Theme_Upgrader class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Core class used for upgrading/installing themes.
 *
 * It is designed to upgrade/install themes from a local zip, remote zip URL,
 * or uploaded zip file.
 *
 *
 *
 *
 * @see GC_Upgrader
 */
class Theme_Upgrader extends GC_Upgrader {

	/**
	 * Result of the theme upgrade offer.
	 *
	 * @var array|GC_Error $result
	 * @see GC_Upgrader::$result
	 */
	public $result;

	/**
	 * Whether multiple themes are being upgraded/installed in bulk.
	 *
	 * @var bool $bulk
	 */
	public $bulk = false;

	/**
	 * New theme info.
	 *
	 * @var array $new_theme_data
	 *
	 * @see check_package()
	 */
	public $new_theme_data = array();

	/**
	 * Initialize the upgrade strings.
	 *
	 */
	public function upgrade_strings() {
		$this->strings['up_to_date'] = __( '主题已是最新版。' );
		$this->strings['no_package'] = __( '升级包不可用。' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package'] = sprintf( __( '正在从 %s 下载更新…' ), '<span class="code">%s</span>' );
		$this->strings['unpack_package']      = __( '正在解压缩升级文件&#8230;' );
		$this->strings['remove_old']          = __( '正在移除主题的旧版本&#8230;' );
		$this->strings['remove_old_failed']   = __( '无法移除旧版本主题。' );
		$this->strings['process_failed']      = __( '主题升级失败。' );
		$this->strings['process_success']     = __( '主题升级成功。' );
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
		$this->strings['installing_package']  = __( '正在安装主题&#8230;' );
		$this->strings['remove_old']          = __( '正在移除主题的旧版本&#8230;' );
		$this->strings['remove_old_failed']   = __( '无法移除旧版本主题。' );
		$this->strings['no_files']            = __( '这个主题不包含文件。' );
		$this->strings['process_failed']      = __( '主题安装失败。' );
		$this->strings['process_success']     = __( '主题安装成功。' );
		/* translators: 1: Theme name, 2: Theme version. */
		$this->strings['process_success_specific'] = __( '安装主题<strong>%1$s %2$s</strong>成功。' );
		$this->strings['parent_theme_search']      = __( '该主题需要父主题的支持。正在检查是否安装了正确的父主题&#8230;' );
		/* translators: 1: Theme name, 2: Theme version. */
		$this->strings['parent_theme_prepare_install'] = __( '正准备安装<strong>%1$s %2$s</strong>&#8230;' );
		/* translators: 1: Theme name, 2: Theme version. */
		$this->strings['parent_theme_currently_installed'] = __( '其父主题（<strong>%1$s %2$s</strong>）当前已安装。' );
		/* translators: 1: Theme name, 2: Theme version. */
		$this->strings['parent_theme_install_success'] = __( '已安装好所需的父主题（<strong>%1$s %2$s</strong>）。' );
		/* translators: %s: Theme name. */
		$this->strings['parent_theme_not_found'] = sprintf( __( '<strong>未能找到父主题。</strong>您需要安装父主题%s后才能使用这个子主题。' ), '<strong>%s</strong>' );
		/* translators: %s: Theme error. */
		$this->strings['current_theme_has_errors'] = __( '活动主题有以下错误：“%s”。' );

		if ( ! empty( $this->skin->overwrite ) ) {
			if ( 'update-theme' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( '正在升级主题…' );
				$this->strings['process_failed']     = __( '主题升级失败。' );
				$this->strings['process_success']    = __( '主题升级成功。' );
			}

			if ( 'downgrade-theme' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( '正在降级主题&#8230;' );
				$this->strings['process_failed']     = __( '主题降级失败。' );
				$this->strings['process_success']    = __( '主题降级成功。' );
			}
		}
	}

	/**
	 * Check if a child theme is being installed and we need to install its parent.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Theme_Upgrader::install().
	 *
	 *
	 * @param bool  $install_result
	 * @param array $hook_extra
	 * @param array $child_result
	 * @return bool
	 */
	public function check_parent_theme_filter( $install_result, $hook_extra, $child_result ) {
		// Check to see if we need to install a parent theme.
		$theme_info = $this->theme_info();

		if ( ! $theme_info->parent() ) {
			return $install_result;
		}

		$this->skin->feedback( 'parent_theme_search' );

		if ( ! $theme_info->parent()->errors() ) {
			$this->skin->feedback( 'parent_theme_currently_installed', $theme_info->parent()->display( 'Name' ), $theme_info->parent()->display( 'Version' ) );
			// We already have the theme, fall through.
			return $install_result;
		}

		// We don't have the parent theme, let's install it.
		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $theme_info->get( 'Template' ),
				'fields' => array(
					'sections' => false,
					'tags'     => false,
				),
			)
		); // Save on a bit of bandwidth.

		if ( ! $api || is_gc_error( $api ) ) {
			$this->skin->feedback( 'parent_theme_not_found', $theme_info->get( 'Template' ) );
			// Don't show activate or preview actions after installation.
			add_filter( 'install_theme_complete_actions', array( $this, 'hide_activate_preview_actions' ) );
			return $install_result;
		}

		// Backup required data we're going to override:
		$child_api             = $this->skin->api;
		$child_success_message = $this->strings['process_success'];

		// Override them.
		$this->skin->api = $api;

		$this->strings['process_success_specific'] = $this->strings['parent_theme_install_success'];

		$this->skin->feedback( 'parent_theme_prepare_install', $api->name, $api->version );

		add_filter( 'install_theme_complete_actions', '__return_false', 999 ); // Don't show any actions after installing the theme.

		// Install the parent theme.
		$parent_result = $this->run(
			array(
				'package'           => $api->download_link,
				'destination'       => get_theme_root(),
				'clear_destination' => false, // Do not overwrite files.
				'clear_working'     => true,
			)
		);

		if ( is_gc_error( $parent_result ) ) {
			add_filter( 'install_theme_complete_actions', array( $this, 'hide_activate_preview_actions' ) );
		}

		// Start cleaning up after the parent's installation.
		remove_filter( 'install_theme_complete_actions', '__return_false', 999 );

		// Reset child's result and data.
		$this->result                     = $child_result;
		$this->skin->api                  = $child_api;
		$this->strings['process_success'] = $child_success_message;

		return $install_result;
	}

	/**
	 * Don't display the activate and preview actions to the user.
	 *
	 * Hooked to the {@see 'install_theme_complete_actions'} filter by
	 * Theme_Upgrader::check_parent_theme_filter() when installing
	 * a child theme and installing the parent theme fails.
	 *
	 *
	 * @param array $actions Preview actions.
	 * @return array
	 */
	public function hide_activate_preview_actions( $actions ) {
		unset( $actions['activate'], $actions['preview'] );
		return $actions;
	}

	/**
	 * Install a theme package.
	 *
	 *
	 * @param string $package The full local path or URI of the package.
	 * @param array  $args {
	 *     Optional. Other arguments for installing a theme package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the updates cache if successful.
	 *                                    Default true.
	 * }
	 *
	 * @return bool|GC_Error True if the installation was successful, false or a GC_Error object otherwise.
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
		add_filter( 'upgrader_post_install', array( $this, 'check_parent_theme_filter' ), 10, 3 );

		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so gc_update_themes() knows about the new theme.
			add_action( 'upgrader_process_complete', 'gc_clean_themes_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $package,
				'destination'       => get_theme_root(),
				'clear_destination' => $parsed_args['overwrite_package'],
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'theme',
					'action' => 'install',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'gc_clean_themes_cache', 9 );
		remove_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'check_parent_theme_filter' ) );

		if ( ! $this->result || is_gc_error( $this->result ) ) {
			return $this->result;
		}

		// Refresh the Theme Update information.
		gc_clean_themes_cache( $parsed_args['clear_update_cache'] );

		if ( $parsed_args['overwrite_package'] ) {
			/** This action is documented in gc-admin/includes/class-plugin-upgrader.php */
			do_action( 'upgrader_overwrote_package', $package, $this->new_theme_data, 'theme' );
		}

		return true;
	}

	/**
	 * Upgrade a theme.
	 *
	 *
	 * @param string $theme The theme slug.
	 * @param array  $args {
	 *     Optional. Other arguments for upgrading a theme. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the update cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|GC_Error True if the upgrade was successful, false or a GC_Error object otherwise.
	 */
	public function upgrade( $theme, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		// Is an update available?
		$current = get_site_transient( 'update_themes' );
		if ( ! isset( $current->response[ $theme ] ) ) {
			$this->skin->before();
			$this->skin->set_result( false );
			$this->skin->error( 'up_to_date' );
			$this->skin->after();
			return false;
		}

		$r = $current->response[ $theme ];

		add_filter( 'upgrader_pre_install', array( $this, 'current_before' ), 10, 2 );
		add_filter( 'upgrader_post_install', array( $this, 'current_after' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ), 10, 4 );
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so gc_update_themes() knows about the new theme.
			add_action( 'upgrader_process_complete', 'gc_clean_themes_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $r['package'],
				'destination'       => get_theme_root( $theme ),
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'theme'  => $theme,
					'type'   => 'theme',
					'action' => 'update',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'gc_clean_themes_cache', 9 );
		remove_filter( 'upgrader_pre_install', array( $this, 'current_before' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'current_after' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ) );

		if ( ! $this->result || is_gc_error( $this->result ) ) {
			return $this->result;
		}

		gc_clean_themes_cache( $parsed_args['clear_update_cache'] );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when themes update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		if ( isset( $past_failure_emails[ $theme ] ) ) {
			unset( $past_failure_emails[ $theme ] );
			update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );
		}

		return true;
	}

	/**
	 * Upgrade several themes at once.
	 *
	 *
	 * @param string[] $themes Array of the theme slugs.
	 * @param array    $args {
	 *     Optional. Other arguments for upgrading several themes at once. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the update cache if successful.
	 *                                    Default true.
	 * }
	 * @return array[]|false An array of results, or false if unable to connect to the filesystem.
	 */
	public function bulk_upgrade( $themes, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_themes' );

		add_filter( 'upgrader_pre_install', array( $this, 'current_before' ), 10, 2 );
		add_filter( 'upgrader_post_install', array( $this, 'current_after' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ), 10, 4 );

		$this->skin->header();

		// Connect to the filesystem first.
		$res = $this->fs_connect( array( GC_CONTENT_DIR ) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		/*
		 * Only start maintenance mode if:
		 * - running Multisite and there are one or more themes specified, OR
		 * - a theme with an update available is currently in use.
		 * @todo For multisite, maintenance mode should only kick in for individual sites if at all possible.
		 */
		$maintenance = ( is_multisite() && ! empty( $themes ) );
		foreach ( $themes as $theme ) {
			$maintenance = $maintenance || get_stylesheet() === $theme || get_template() === $theme;
		}
		if ( $maintenance ) {
			$this->maintenance_mode( true );
		}

		$results = array();

		$this->update_count   = count( $themes );
		$this->update_current = 0;
		foreach ( $themes as $theme ) {
			$this->update_current++;

			$this->skin->theme_info = $this->theme_info( $theme );

			if ( ! isset( $current->response[ $theme ] ) ) {
				$this->skin->set_result( true );
				$this->skin->before();
				$this->skin->feedback( 'up_to_date' );
				$this->skin->after();
				$results[ $theme ] = true;
				continue;
			}

			// Get the URL to the zip file.
			$r = $current->response[ $theme ];

			$result = $this->run(
				array(
					'package'           => $r['package'],
					'destination'       => get_theme_root( $theme ),
					'clear_destination' => true,
					'clear_working'     => true,
					'is_multi'          => true,
					'hook_extra'        => array(
						'theme' => $theme,
					),
				)
			);

			$results[ $theme ] = $result;

			// Prevent credentials auth screen from displaying multiple times.
			if ( false === $result ) {
				break;
			}
		} // End foreach $themes.

		$this->maintenance_mode( false );

		// Refresh the Theme Update information.
		gc_clean_themes_cache( $parsed_args['clear_update_cache'] );

		/** This action is documented in gc-admin/includes/class-gc-upgrader.php */
		do_action(
			'upgrader_process_complete',
			$this,
			array(
				'action' => 'update',
				'type'   => 'theme',
				'bulk'   => true,
				'themes' => $themes,
			)
		);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter( 'upgrader_pre_install', array( $this, 'current_before' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'current_after' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ) );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when themes update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		foreach ( $results as $theme => $result ) {
			// Maintain last failure notification when themes failed to update manually.
			if ( ! $result || is_gc_error( $result ) || ! isset( $past_failure_emails[ $theme ] ) ) {
				continue;
			}

			unset( $past_failure_emails[ $theme ] );
		}

		update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );

		return $results;
	}

	/**
	 * Checks that the package source contains a valid theme.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by Theme_Upgrader::install().
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

		$this->new_theme_data = array();

		if ( is_gc_error( $source ) ) {
			return $source;
		}

		// Check that the folder contains a valid theme.
		$working_directory = str_replace( $gc_filesystem->gc_content_dir(), trailingslashit( GC_CONTENT_DIR ), $source );
		if ( ! is_dir( $working_directory ) ) { // Sanity check, if the above fails, let's not prevent installation.
			return $source;
		}

		// A proper archive should have a style.css file in the single subdirectory.
		if ( ! file_exists( $working_directory . 'style.css' ) ) {
			return new GC_Error(
				'incompatible_archive_theme_no_style',
				$this->strings['incompatible_archive'],
				sprintf(
					/* translators: %s: style.css */
					__( '主题缺少%s样式表。' ),
					'<code>style.css</code>'
				)
			);
		}

		// All these headers are needed on Theme_Installer_Skin::do_overwrite().
		$info = get_file_data(
			$working_directory . 'style.css',
			array(
				'Name'        => 'Theme Name',
				'Version'     => 'Version',
				'Author'      => 'Author',
				'Template'    => 'Template',
				'RequiresGC'  => 'Requires at least',
				'RequiresPHP' => 'Requires PHP',
			)
		);

		if ( empty( $info['Name'] ) ) {
			return new GC_Error(
				'incompatible_archive_theme_no_name',
				$this->strings['incompatible_archive'],
				sprintf(
					/* translators: %s: style.css */
					__( '%s样式表未包含合法的主题头部。' ),
					'<code>style.css</code>'
				)
			);
		}

		// If it's not a child theme, it must have at least an index.php to be legit.
		if ( empty( $info['Template'] ) && ! file_exists( $working_directory . 'index.php' ) ) {
			return new GC_Error(
				'incompatible_archive_theme_no_index',
				$this->strings['incompatible_archive'],
				sprintf(
					/* translators: %s: index.php */
					__( '主题缺少%s文件。' ),
					'<code>index.php</code>'
				)
			);
		}

		$requires_php = isset( $info['RequiresPHP'] ) ? $info['RequiresPHP'] : null;
		$requires_gc  = isset( $info['RequiresGC'] ) ? $info['RequiresGC'] : null;

		if ( ! is_php_version_compatible( $requires_php ) ) {
			$error = sprintf(
				/* translators: 1: Current PHP version, 2: Version required by the uploaded theme. */
				__( '您的服务器PHP版本为%1$s，然而上传的主题要求版本为%2$s。' ),
				phpversion(),
				$requires_php
			);

			return new GC_Error( 'incompatible_php_required_version', $this->strings['incompatible_archive'], $error );
		}
		if ( ! is_gc_version_compatible( $requires_gc ) ) {
			$error = sprintf(
				/* translators: 1: Current GeChiUI version, 2: Version required by the uploaded theme. */
				__( '当前GeChiUI版本为%1$s，但是该上传主题要求版本为%2$s。' ),
				$gc_version,
				$requires_gc
			);

			return new GC_Error( 'incompatible_gc_required_version', $this->strings['incompatible_archive'], $error );
		}

		$this->new_theme_data = $info;

		return $source;
	}

	/**
	 * Turn on maintenance mode before attempting to upgrade the active theme.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Theme_Upgrader::upgrade() and
	 * Theme_Upgrader::bulk_upgrade().
	 *
	 *
	 * @param bool|GC_Error $return Upgrade offer return.
	 * @param array         $theme  Theme arguments.
	 * @return bool|GC_Error The passed in $return param or GC_Error.
	 */
	public function current_before( $return, $theme ) {
		if ( is_gc_error( $return ) ) {
			return $return;
		}

		$theme = isset( $theme['theme'] ) ? $theme['theme'] : '';

		// Only run if active theme
		if ( get_stylesheet() !== $theme ) {
			return $return;
		}

		// Change to maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( true );
		}

		return $return;
	}

	/**
	 * Turn off maintenance mode after upgrading the active theme.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Theme_Upgrader::upgrade()
	 * and Theme_Upgrader::bulk_upgrade().
	 *
	 *
	 * @param bool|GC_Error $return Upgrade offer return.
	 * @param array         $theme  Theme arguments.
	 * @return bool|GC_Error The passed in $return param or GC_Error.
	 */
	public function current_after( $return, $theme ) {
		if ( is_gc_error( $return ) ) {
			return $return;
		}

		$theme = isset( $theme['theme'] ) ? $theme['theme'] : '';

		// Only run if active theme.
		if ( get_stylesheet() !== $theme ) {
			return $return;
		}

		// Ensure stylesheet name hasn't changed after the upgrade:
		if ( get_stylesheet() === $theme && $theme !== $this->result['destination_name'] ) {
			gc_clean_themes_cache();
			$stylesheet = $this->result['destination_name'];
			switch_theme( $stylesheet );
		}

		// Time to remove maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( false );
		}
		return $return;
	}

	/**
	 * Delete the old theme during an upgrade.
	 *
	 * Hooked to the {@see 'upgrader_clear_destination'} filter by Theme_Upgrader::upgrade()
	 * and Theme_Upgrader::bulk_upgrade().
	 *
	 *
	 * @global GC_Filesystem_Base $gc_filesystem Subclass
	 *
	 * @param bool   $removed
	 * @param string $local_destination
	 * @param string $remote_destination
	 * @param array  $theme
	 * @return bool
	 */
	public function delete_old_theme( $removed, $local_destination, $remote_destination, $theme ) {
		global $gc_filesystem;

		if ( is_gc_error( $removed ) ) {
			return $removed; // Pass errors through.
		}

		if ( ! isset( $theme['theme'] ) ) {
			return $removed;
		}

		$theme      = $theme['theme'];
		$themes_dir = trailingslashit( $gc_filesystem->gc_themes_dir( $theme ) );
		if ( $gc_filesystem->exists( $themes_dir . $theme ) ) {
			if ( ! $gc_filesystem->delete( $themes_dir . $theme, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the GC_Theme object for a theme.
	 *
	 *
	 * @param string $theme The directory name of the theme. This is optional, and if not supplied,
	 *                      the directory name from the last result will be used.
	 * @return GC_Theme|false The theme's info object, or false `$theme` is not supplied
	 *                        and the last result isn't set.
	 */
	public function theme_info( $theme = null ) {
		if ( empty( $theme ) ) {
			if ( ! empty( $this->result['destination_name'] ) ) {
				$theme = $this->result['destination_name'];
			} else {
				return false;
			}
		}

		$theme = gc_get_theme( $theme );
		$theme->cache_delete();

		return $theme;
	}

}
