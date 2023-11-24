<?php
/**
 * Upgrade API: Language_Pack_Upgrader class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 */

/**
 * Core class used for updating/installing language packs (translations)
 * for plugins, themes, and core.
 * Moved to its own file from gc-admin/includes/class-gc-upgrader.php.
 *
 * @see GC_Upgrader
 */
class Language_Pack_Upgrader extends GC_Upgrader {

	/**
	 * Result of the language pack upgrade.
	 *
	 * @since 3.7.0
	 * @var array|GC_Error $result
	 * @see GC_Upgrader::$result
	 */
	public $result;

	/**
	 * Whether a bulk upgrade/installation is being performed.
	 *
	 * @since 3.7.0
	 * @var bool $bulk
	 */
	public $bulk = true;

	/**
	 * Asynchronously upgrades language packs after other upgrades have been made.
	 *
	 * Hooked to the {@see 'upgrader_process_complete'} action by default.
	 *
	 * @since 3.7.0
	 *
	 * @param false|GC_Upgrader $upgrader Optional. GC_Upgrader instance or false. If `$upgrader` is
	 *                                    a Language_Pack_Upgrader instance, the method will bail to
	 *                                    avoid recursion. Otherwise unused. Default false.
	 */
	public static function async_upgrade( $upgrader = false ) {
		// Avoid recursion.
		if ( $upgrader && $upgrader instanceof Language_Pack_Upgrader ) {
			return;
		}

		// Nothing to do?
		$language_updates = gc_get_translation_updates();
		if ( ! $language_updates ) {
			return;
		}

		/*
		 * Avoid messing with VCS installations, at least for now.
		 * Noted: this is not the ideal way to accomplish this.
		 */
		$check_vcs = new GC_Automatic_Updater();
		if ( $check_vcs->is_vcs_checkout( GC_CONTENT_DIR ) ) {
			return;
		}

		foreach ( $language_updates as $key => $language_update ) {
			$update = ! empty( $language_update->autoupdate );

			/**
			 * Filters whether to asynchronously update translation for core, a plugin, or a theme.
			 *
			 * @since 4.0.0
			 *
			 * @param bool   $update          Whether to update.
			 * @param object $language_update The update offer.
			 */
			$update = apply_filters( 'async_update_translation', $update, $language_update );

			if ( ! $update ) {
				unset( $language_updates[ $key ] );
			}
		}

		if ( empty( $language_updates ) ) {
			return;
		}

		// Re-use the automatic upgrader skin if the parent upgrader is using it.
		if ( $upgrader && $upgrader->skin instanceof Automatic_Upgrader_Skin ) {
			$skin = $upgrader->skin;
		} else {
			$skin = new Language_Pack_Upgrader_Skin(
				array(
					'skip_header_footer' => true,
				)
			);
		}

		$lp_upgrader = new Language_Pack_Upgrader( $skin );
		$lp_upgrader->bulk_upgrade( $language_updates );
	}

	/**
	 * Initializes the upgrade strings.
	 *
	 * @since 3.7.0
	 */
	public function upgrade_strings() {
		$this->strings['starting_upgrade'] = __( '一些翻译需要更新，请稍等。' );
		$this->strings['up_to_date']       = __( '您的所有翻译均为最新版本。' );
		$this->strings['no_package']       = __( '升级包不可用。' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package'] = sprintf( __( '正在从 %s 下载翻译...'  ), '<span class="code pre">%s</span>' );
		$this->strings['unpack_package']      = __( '正在解压缩升级文件&#8230;' );
		$this->strings['process_failed']      = __( '翻译升级失败。' );
		$this->strings['process_success']     = __( '翻译升级成功。' );
		$this->strings['remove_old']          = __( '移除旧版本的翻译...'  );
		$this->strings['remove_old_failed']   = __( '未能移除旧翻译。' );
	}

	/**
	 * Upgrades a language pack.
	 *
	 * @since 3.7.0
	 *
	 * @param string|false $update Optional. Whether an update offer is available. Default false.
	 * @param array        $args   Optional. Other optional arguments, see
	 *                             Language_Pack_Upgrader::bulk_upgrade(). Default empty array.
	 * @return array|bool|GC_Error The result of the upgrade, or a GC_Error object instead.
	 */
	public function upgrade( $update = false, $args = array() ) {
		if ( $update ) {
			$update = array( $update );
		}

		$results = $this->bulk_upgrade( $update, $args );

		if ( ! is_array( $results ) ) {
			return $results;
		}

		return $results[0];
	}

	/**
	 * Upgrades several language packs at once.
	 *
	 * @since 3.7.0
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @param object[] $language_updates Optional. Array of language packs to update. @see gc_get_translation_updates().
	 *                                   Default empty array.
	 * @param array    $args {
	 *     Other arguments for upgrading multiple language packs. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the update cache when done.
	 *                                    Default true.
	 * }
	 * @return array|bool|GC_Error Will return an array of results, or true if there are no updates,
	 *                             false or GC_Error for initial errors.
	 */
	public function bulk_upgrade( $language_updates = array(), $args = array() ) {
		global $gc_filesystem;

		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = gc_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		if ( ! $language_updates ) {
			$language_updates = gc_get_translation_updates();
		}

		if ( empty( $language_updates ) ) {
			$this->skin->header();
			$this->skin->set_result( true );
			$this->skin->feedback( 'up_to_date' );
			$this->skin->bulk_footer();
			$this->skin->footer();
			return true;
		}

		if ( 'upgrader_process_complete' === current_filter() ) {
			$this->skin->feedback( 'starting_upgrade' );
		}

		// Remove any existing upgrade filters from the plugin/theme upgraders #GC29425 & #GC29230.
		remove_all_filters( 'upgrader_pre_install' );
		remove_all_filters( 'upgrader_clear_destination' );
		remove_all_filters( 'upgrader_post_install' );
		remove_all_filters( 'upgrader_source_selection' );

		add_filter( 'upgrader_source_selection', array( $this, 'check_package' ), 10, 2 );

		$this->skin->header();

		// Connect to the filesystem first.
		$res = $this->fs_connect( array( GC_CONTENT_DIR, GC_LANG_DIR ) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$results = array();

		$this->update_count   = count( $language_updates );
		$this->update_current = 0;

		/*
		 * The filesystem's mkdir() is not recursive. Make sure GC_LANG_DIR exists,
		 * as we then may need to create a /plugins or /themes directory inside of it.
		 */
		$remote_destination = $gc_filesystem->find_folder( GC_LANG_DIR );
		if ( ! $gc_filesystem->exists( $remote_destination ) ) {
			if ( ! $gc_filesystem->mkdir( $remote_destination, FS_CHMOD_DIR ) ) {
				return new GC_Error( 'mkdir_failed_lang_dir', $this->strings['mkdir_failed'], $remote_destination );
			}
		}

		$language_updates_results = array();

		foreach ( $language_updates as $language_update ) {

			$this->skin->language_update = $language_update;

			$destination = GC_LANG_DIR;
			if ( 'plugin' === $language_update->type ) {
				$destination .= '/plugins';
			} elseif ( 'theme' === $language_update->type ) {
				$destination .= '/themes';
			}

			$this->update_current++;

			$options = array(
				'package'                     => $language_update->package,
				'destination'                 => $destination,
				'clear_destination'           => true,
				'abort_if_destination_exists' => false, // We expect the destination to exist.
				'clear_working'               => true,
				'is_multi'                    => true,
				'hook_extra'                  => array(
					'language_update_type' => $language_update->type,
					'language_update'      => $language_update,
				),
			);

			$result = $this->run( $options );

			$results[] = $this->result;

			// Prevent credentials auth screen from displaying multiple times.
			if ( false === $result ) {
				break;
			}

			$language_updates_results[] = array(
				'language' => $language_update->language,
				'type'     => $language_update->type,
				'slug'     => isset( $language_update->slug ) ? $language_update->slug : 'default',
				'version'  => $language_update->version,
			);
		}

		// Remove upgrade hooks which are not required for translation updates.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		remove_action( 'upgrader_process_complete', 'gc_version_check' );
		remove_action( 'upgrader_process_complete', 'gc_update_plugins' );
		remove_action( 'upgrader_process_complete', 'gc_update_themes' );

		/** This action is documented in gc-admin/includes/class-gc-upgrader.php */
		do_action(
			'upgrader_process_complete',
			$this,
			array(
				'action'       => 'update',
				'type'         => 'translation',
				'bulk'         => true,
				'translations' => $language_updates_results,
			)
		);

		// Re-add upgrade hooks.
		add_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		add_action( 'upgrader_process_complete', 'gc_version_check', 10, 0 );
		add_action( 'upgrader_process_complete', 'gc_update_plugins', 10, 0 );
		add_action( 'upgrader_process_complete', 'gc_update_themes', 10, 0 );

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Clean up our hooks, in case something else does an upgrade on this connection.
		remove_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );

		if ( $parsed_args['clear_update_cache'] ) {
			gc_clean_update_cache();
		}

		return $results;
	}

	/**
	 * Checks that the package source contains .mo and .po files.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by
	 * Language_Pack_Upgrader::bulk_upgrade().
	 *
	 * @since 3.7.0
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @param string|GC_Error $source        The path to the downloaded package source.
	 * @param string          $remote_source Remote file source location.
	 * @return string|GC_Error The source as passed, or a GC_Error object on failure.
	 */
	public function check_package( $source, $remote_source ) {
		global $gc_filesystem;

		if ( is_gc_error( $source ) ) {
			return $source;
		}

		// Check that the folder contains a valid language.
		$files = $gc_filesystem->dirlist( $remote_source );

		// Check to see if a .po and .mo exist in the folder.
		$po = false;
		$mo = false;
		foreach ( (array) $files as $file => $filedata ) {
			if ( str_ends_with( $file, '.po' ) ) {
				$po = true;
			} elseif ( str_ends_with( $file, '.mo' ) ) {
				$mo = true;
			}
		}

		if ( ! $mo || ! $po ) {
			return new GC_Error(
				'incompatible_archive_pomo',
				$this->strings['incompatible_archive'],
				sprintf(
					/* translators: 1: .po, 2: .mo */
					__( '语言包缺少%1$s或%2$s文件。' ),
					'<code>.po</code>',
					'<code>.mo</code>'
				)
			);
		}

		return $source;
	}

	/**
	 * Gets the name of an item being updated.
	 *
	 * @since 3.7.0
	 *
	 * @param object $update The data for an update.
	 * @return string The name of the item being updated.
	 */
	public function get_name_for_update( $update ) {
		switch ( $update->type ) {
			case 'core':
				return 'GeChiUI'; // Not translated.

			case 'theme':
				$theme = gc_get_theme( $update->slug );
				if ( $theme->exists() ) {
					return $theme->Get( 'Name' );
				}
				break;
			case 'plugin':
				$plugin_data = get_plugins( '/' . $update->slug );
				$plugin_data = reset( $plugin_data );
				if ( $plugin_data ) {
					return $plugin_data['Name'];
				}
				break;
		}
		return '';
	}

	/**
	 * Clears existing translations where this item is going to be installed into.
	 *
	 * @since 5.1.0
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @param string $remote_destination The location on the remote filesystem to be cleared.
	 * @return bool|GC_Error True upon success, GC_Error on failure.
	 */
	public function clear_destination( $remote_destination ) {
		global $gc_filesystem;

		$language_update    = $this->skin->language_update;
		$language_directory = GC_LANG_DIR . '/'; // Local path for use with glob().

		if ( 'core' === $language_update->type ) {
			$files = array(
				$remote_destination . $language_update->language . '.po',
				$remote_destination . $language_update->language . '.mo',
				$remote_destination . 'admin-' . $language_update->language . '.po',
				$remote_destination . 'admin-' . $language_update->language . '.mo',
				$remote_destination . 'admin-network-' . $language_update->language . '.po',
				$remote_destination . 'admin-network-' . $language_update->language . '.mo',
				$remote_destination . 'continents-cities-' . $language_update->language . '.po',
				$remote_destination . 'continents-cities-' . $language_update->language . '.mo',
			);

			$json_translation_files = glob( $language_directory . $language_update->language . '-*.json' );
			if ( $json_translation_files ) {
				foreach ( $json_translation_files as $json_translation_file ) {
					$files[] = str_replace( $language_directory, $remote_destination, $json_translation_file );
				}
			}
		} else {
			$files = array(
				$remote_destination . $language_update->slug . '-' . $language_update->language . '.po',
				$remote_destination . $language_update->slug . '-' . $language_update->language . '.mo',
			);

			$language_directory     = $language_directory . $language_update->type . 's/';
			$json_translation_files = glob( $language_directory . $language_update->slug . '-' . $language_update->language . '-*.json' );
			if ( $json_translation_files ) {
				foreach ( $json_translation_files as $json_translation_file ) {
					$files[] = str_replace( $language_directory, $remote_destination, $json_translation_file );
				}
			}
		}

		$files = array_filter( $files, array( $gc_filesystem, 'exists' ) );

		// No files to delete.
		if ( ! $files ) {
			return true;
		}

		// Check all files are writable before attempting to clear the destination.
		$unwritable_files = array();

		// Check writability.
		foreach ( $files as $file ) {
			if ( ! $gc_filesystem->is_writable( $file ) ) {
				// Attempt to alter permissions to allow writes and try again.
				$gc_filesystem->chmod( $file, FS_CHMOD_FILE );
				if ( ! $gc_filesystem->is_writable( $file ) ) {
					$unwritable_files[] = $file;
				}
			}
		}

		if ( ! empty( $unwritable_files ) ) {
			return new GC_Error( 'files_not_writable', $this->strings['files_not_writable'], implode( ', ', $unwritable_files ) );
		}

		foreach ( $files as $file ) {
			if ( ! $gc_filesystem->delete( $file ) ) {
				return new GC_Error( 'remove_old_failed', $this->strings['remove_old_failed'] );
			}
		}

		return true;
	}
}
