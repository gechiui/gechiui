<?php
/**
 * A simple set of functions to check our version 1.0 update service.
 *
 * @package GeChiUI
 *
 */

/**
 * Check GeChiUI version against the newest version.
 *
 * The GeChiUI version, PHP version, and locale is sent.
 *
 * Checks against the GeChiUI server at api.gechiui.com. Will only check
 * if GeChiUI isn't installing.
 *
 *
 *
 * @global string $gc_version       Used to check against the newest GeChiUI version.
 * @global gcdb   $gcdb             GeChiUI database abstraction object.
 * @global string $gc_local_package Locale code of the package.
 *
 * @param array $extra_stats Extra statistics to report to the www.GeChiUI.com API.
 * @param bool  $force_check Whether to bypass the transient cache and force a fresh update check. Defaults to false, true if $extra_stats is set.
 */
function gc_version_check( $extra_stats = array(), $force_check = false ) {
	global $gcdb, $gc_local_package;

	if ( gc_installing() ) {
		return;
	}

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';
	$php_version = phpversion();

	$current      = get_site_transient( 'update_core' );
	$translations = gc_get_installed_translations( 'core' );

	// Invalidate the transient when $gc_version changes.
	if ( is_object( $current ) && $gc_version !== $current->version_checked ) {
		$current = false;
	}

	if ( ! is_object( $current ) ) {
		$current                  = new stdClass;
		$current->updates         = array();
		$current->version_checked = $gc_version;
	}

	if ( ! empty( $extra_stats ) ) {
		$force_check = true;
	}

	// Wait 1 minute between multiple version check requests.
	$timeout          = MINUTE_IN_SECONDS;
	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( ! $force_check && $time_not_changed ) {
		return;
	}

	/**
	 * Filters the locale requested for GeChiUI core translations.
	 *
	 *
	 * @param string $locale Current locale.
	 */
	$locale = apply_filters( 'core_version_check_locale', get_locale() );

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$current->last_checked = time();
	set_site_transient( 'update_core', $current );

	if ( method_exists( $gcdb, 'db_version' ) ) {
		$mysql_version = preg_replace( '/[^0-9.].*/', '', $gcdb->db_version() );
	} else {
		$mysql_version = 'N/A';
	}

	if ( is_multisite() ) {
		$user_count        = get_user_count();
		$num_blogs         = get_blog_count();
		$gc_install        = network_site_url();
		$multisite_enabled = 1;
	} else {
		$user_count        = count_users();
		$user_count        = $user_count['total_users'];
		$multisite_enabled = 0;
		$num_blogs         = 1;
		$gc_install        = home_url( '/' );
	}

	$query = array(
		'version'            => $gc_version,
		'php'                => $php_version,
		'locale'             => $locale,
		'mysql'              => $mysql_version,
		'local_package'      => isset( $gc_local_package ) ? $gc_local_package : '',
		'blogs'              => $num_blogs,
		'users'              => $user_count,
		'multisite_enabled'  => $multisite_enabled,
		'initial_db_version' => get_site_option( 'initial_db_version' ),
	);

	/**
	 * Filters the query arguments sent as part of the core version check.
	 *
	 * WARNING: Changing this data may result in your site not receiving security updates.
	 * Please exercise extreme caution.
	 *
	 *
	 * @param array $query {
	 *     Version check query arguments.
	 *
	 *     @type string $version            GeChiUI version number.
	 *     @type string $php                PHP version number.
	 *     @type string $locale             The locale to retrieve updates for.
	 *     @type string $mysql              MySQL version number.
	 *     @type string $local_package      The value of the $gc_local_package global, when set.
	 *     @type int    $blogs              Number of sites on this GeChiUI installation.
	 *     @type int    $users              Number of users on this GeChiUI installation.
	 *     @type int    $multisite_enabled  Whether this GeChiUI installation uses Multisite.
	 *     @type int    $initial_db_version Database version of GeChiUI at time of installation.
	 * }
	 */
	$query = apply_filters( 'core_version_check_query_args', $query );

	$post_body = array(
		'translations' => gc_json_encode( $translations ),
	);

	if ( is_array( $extra_stats ) ) {
		$post_body = array_merge( $post_body, $extra_stats );
	}

	// Allow for GC_AUTO_UPDATE_CORE to specify beta/RC/development releases.
	if ( defined( 'GC_AUTO_UPDATE_CORE' )
		&& in_array( GC_AUTO_UPDATE_CORE, array( 'beta', 'rc', 'development', 'branch-development' ), true )
	) {
		$query['channel'] = GC_AUTO_UPDATE_CORE;
	}

	$url      = 'http://api.gechiui.com/core/version-check/1.7/?' . http_build_query( $query, '', '&' );
	$http_url = $url;
	$ssl      = gc_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$doing_cron = gc_doing_cron();

	$options = array(
		'timeout'    => $doing_cron ? 30 : 3,
		'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ),
		'headers'    => array(
			'gc_install' => $gc_install,
			'gc_blog'    => home_url( '/' ),
		),
		'body'       => $post_body,
	);

	$response = gc_remote_post( $url, $options );

	if ( $ssl && is_gc_error( $response ) ) {
		trigger_error(
			sprintf(
				/* translators: %s: Support forums URL. */
				__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
			headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$response = gc_remote_post( $http_url, $options );
	}

	if ( is_gc_error( $response ) || 200 !== gc_remote_retrieve_response_code( $response ) ) {
		return;
	}

	$body = trim( gc_remote_retrieve_body( $response ) );
	$body = json_decode( $body, true );

	if ( ! is_array( $body ) || ! isset( $body['offers'] ) ) {
		return;
	}

	$offers = $body['offers'];

	foreach ( $offers as &$offer ) {
		foreach ( $offer as $offer_key => $value ) {
			if ( 'packages' === $offer_key ) {
				$offer['packages'] = (object) array_intersect_key(
					array_map( 'esc_url', $offer['packages'] ),
					array_fill_keys( array( 'full', 'no_content', 'new_bundled', 'partial', 'rollback' ), '' )
				);
			} elseif ( 'download' === $offer_key ) {
				$offer['download'] = esc_url( $value );
			} else {
				$offer[ $offer_key ] = esc_html( $value );
			}
		}
		$offer = (object) array_intersect_key(
			$offer,
			array_fill_keys(
				array(
					'response',
					'download',
					'locale',
					'packages',
					'current',
					'version',
					'php_version',
					'mysql_version',
					'new_bundled',
					'partial_version',
					'notify_email',
					'support_email',
					'new_files',
				),
				''
			)
		);
	}

	$updates                  = new stdClass();
	$updates->updates         = $offers;
	$updates->last_checked    = time();
	$updates->version_checked = $gc_version;

	if ( isset( $body['translations'] ) ) {
		$updates->translations = $body['translations'];
	}

	set_site_transient( 'update_core', $updates );

	if ( ! empty( $body['ttl'] ) ) {
		$ttl = (int) $body['ttl'];

		if ( $ttl && ( time() + $ttl < gc_next_scheduled( 'gc_version_check' ) ) ) {
			// Queue an event to re-run the update check in $ttl seconds.
			gc_schedule_single_event( time() + $ttl, 'gc_version_check' );
		}
	}

	// Trigger background updates if running non-interactively, and we weren't called from the update handler.
	if ( $doing_cron && ! doing_action( 'gc_maybe_auto_update' ) ) {
		/**
		 * Fires during gc_cron, starting the auto-update process.
		 *
		 */
		do_action( 'gc_maybe_auto_update' );
	}
}

/**
 * Checks for available updates to plugins based on the latest versions hosted on www.GeChiUI.com.
 *
 * Despite its name this function does not actually perform any updates, it only checks for available updates.
 *
 * A list of all plugins installed is sent to GC, along with the site locale.
 *
 * Checks against the GeChiUI server at api.gechiui.com. Will only check
 * if GeChiUI isn't installing.
 *
 *
 *
 * @global string $gc_version The GeChiUI version string.
 *
 * @param array $extra_stats Extra statistics to report to the www.GeChiUI.com API.
 */
function gc_update_plugins( $extra_stats = array() ) {
	if ( gc_installing() ) {
		return;
	}

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	// If running blog-side, bail unless we've not checked in the last 12 hours.
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'gc-admin/includes/plugin.php';
	}

	$plugins      = get_plugins();
	$translations = gc_get_installed_translations( 'plugins' );

	$active  = get_option( 'active_plugins', array() );
	$current = get_site_transient( 'update_plugins' );

	if ( ! is_object( $current ) ) {
		$current = new stdClass;
	}

	$updates               = new stdClass;
	$updates->last_checked = time();
	$updates->response     = array();
	$updates->translations = array();
	$updates->no_update    = array();

	$doing_cron = gc_doing_cron();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete':
			$timeout = 0;
			break;
		case 'load-update-core.php':
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-plugins.php':
		case 'load-update.php':
			$timeout = HOUR_IN_SECONDS;
			break;
		default:
			if ( $doing_cron ) {
				$timeout = 2 * HOUR_IN_SECONDS;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
		$plugin_changed = false;

		foreach ( $plugins as $file => $p ) {
			$updates->checked[ $file ] = $p['Version'];

			if ( ! isset( $current->checked[ $file ] ) || (string) $current->checked[ $file ] !== (string) $p['Version'] ) {
				$plugin_changed = true;
			}
		}

		if ( isset( $current->response ) && is_array( $current->response ) ) {
			foreach ( $current->response as $plugin_file => $update_details ) {
				if ( ! isset( $plugins[ $plugin_file ] ) ) {
					$plugin_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed.
		if ( ! $plugin_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$current->last_checked = time();
	set_site_transient( 'update_plugins', $current );

	$to_send = compact( 'plugins', 'active' );

	$locales = array_values( get_available_languages() );

	/**
	 * Filters the locales requested for plugin translations.
	 *
	 *
	 * @param array $locales Plugin locales. Default is all available locales of the site.
	 */
	$locales = apply_filters( 'plugins_update_check_locales', $locales );
	/**
	 * 补充一个默认的中文，这样主题和插件就不必一定使用英文，任何语言都可以，只要下载语言包即可
	 */
	$locales[] = 'zh_CN';
	$locales = array_unique( $locales );

	if ( $doing_cron ) {
		$timeout = 30;
	} else {
		// Three seconds, plus one extra second for every 10 plugins.
		$timeout = 3 + (int) ( count( $plugins ) / 10 );
	}

	$options = array(
		'timeout'    => $timeout,
		'body'       => array(
			'plugins'      => gc_json_encode( $to_send ),
			'translations' => gc_json_encode( $translations ),
			'locale'       => gc_json_encode( $locales ),
			'all'          => gc_json_encode( true ),
		),
		'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ),
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = gc_json_encode( $extra_stats );
	}

	//添加appkey身份识别
    $gcorg_professionals = get_user_option( 'gcorg_professionals' );
    if ( $gcorg_professionals ) {
		$options['body']['username'] = $gcorg_professionals['username'];
        $options['body']['appkey'] = $gcorg_professionals['appkey'];
	}

	$url      = 'http://api.gechiui.com/plugins/update-check/1.1/';
	$http_url = $url;
	$ssl      = gc_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$raw_response = gc_remote_post( $url, $options );

	if ( $ssl && is_gc_error( $raw_response ) ) {
		trigger_error(
			sprintf(
				/* translators: %s: Support forums URL. */
				__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
			headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$raw_response = gc_remote_post( $http_url, $options );
	}

	if ( is_gc_error( $raw_response ) || 200 !== gc_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$response = json_decode( gc_remote_retrieve_body( $raw_response ), true );

	if ( $response && is_array( $response ) ) {
		$updates->response     = $response['plugins'];
		$updates->translations = $response['translations'];
		$updates->no_update    = $response['no_update'];
	}

	// Support updates for any plugins using the `Update URI` header field.
	foreach ( $plugins as $plugin_file => $plugin_data ) {
		if ( ! $plugin_data['UpdateURI'] || isset( $updates->response[ $plugin_file ] ) ) {
			continue;
		}

		$hostname = gc_parse_url( esc_url_raw( $plugin_data['UpdateURI'] ), PHP_URL_HOST );

		/**
		 * Filters the update response for a given plugin hostname.
		 *
		 * The dynamic portion of the hook name, `$hostname`, refers to the hostname
		 * of the URI specified in the `Update URI` header field.
		 *
		 *
		 * @param array|false $update {
		 *     The plugin update data with the latest details. Default false.
		 *
		 *     @type string $id           Optional. ID of the plugin for update purposes, should be a URI
		 *                                specified in the `Update URI` header field.
		 *     @type string $slug         Slug of the plugin.
		 *     @type string $version      The version of the plugin.
		 *     @type string $url          The URL for details of the plugin.
		 *     @type string $package      Optional. The update ZIP for the plugin.
		 *     @type string $tested       Optional. The version of GeChiUI the plugin is tested against.
		 *     @type string $requires_php Optional. The version of PHP which the plugin requires.
		 *     @type bool   $autoupdate   Optional. Whether the plugin should automatically update.
		 *     @type array  $icons        Optional. Array of plugin icons.
		 *     @type array  $banners      Optional. Array of plugin banners.
		 *     @type array  $banners_rtl  Optional. Array of plugin RTL banners.
		 *     @type array  $translations {
		 *         Optional. List of translation updates for the plugin.
		 *
		 *         @type string $language   The language the translation update is for.
		 *         @type string $version    The version of the plugin this translation is for.
		 *                                  This is not the version of the language file.
		 *         @type string $updated    The update timestamp of the translation file.
		 *                                  Should be a date in the `YYYY-MM-DD HH:MM:SS` format.
		 *         @type string $package    The ZIP location containing the translation update.
		 *         @type string $autoupdate Whether the translation should be automatically installed.
		 *     }
		 * }
		 * @param array       $plugin_data      Plugin headers.
		 * @param string      $plugin_file      Plugin filename.
		 * @param array       $locales          Installed locales to look translations for.
		 */
		$update = apply_filters( "update_plugins_{$hostname}", false, $plugin_data, $plugin_file, $locales );

		if ( ! $update ) {
			continue;
		}

		$update = (object) $update;

		// Is it valid? We require at least a version.
		if ( ! isset( $update->version ) ) {
			continue;
		}

		// These should remain constant.
		$update->id     = $plugin_data['UpdateURI'];
		$update->plugin = $plugin_file;

		// GeChiUI needs the version field specified as 'new_version'.
		if ( ! isset( $update->new_version ) ) {
			$update->new_version = $update->version;
		}

		// Handle any translation updates.
		if ( ! empty( $update->translations ) ) {
			foreach ( $update->translations as $translation ) {
				if ( isset( $translation['language'], $translation['package'] ) ) {
					$translation['type'] = 'plugin';
					$translation['slug'] = isset( $update->slug ) ? $update->slug : $update->id;

					$updates->translations[] = $translation;
				}
			}
		}

		unset( $updates->no_update[ $plugin_file ], $updates->response[ $plugin_file ] );

		if ( version_compare( $update->new_version, $plugin_data['Version'], '>' ) ) {
			$updates->response[ $plugin_file ] = $update;
		} else {
			$updates->no_update[ $plugin_file ] = $update;
		}
	}

	$sanitize_plugin_update_payload = static function( &$item ) {
		$item = (object) $item;

		unset( $item->translations, $item->compatibility );

		return $item;
	};

	array_walk( $updates->response, $sanitize_plugin_update_payload );
	array_walk( $updates->no_update, $sanitize_plugin_update_payload );

	set_site_transient( 'update_plugins', $updates );
}

/**
 * Checks for available updates to themes based on the latest versions hosted on www.GeChiUI.com.
 *
 * Despite its name this function does not actually perform any updates, it only checks for available updates.
 *
 * A list of all themes installed is sent to GC, along with the site locale.
 *
 * Checks against the GeChiUI server at api.gechiui.com. Will only check
 * if GeChiUI isn't installing.
 *
 *
 *
 * @global string $gc_version The GeChiUI version string.
 *
 * @param array $extra_stats Extra statistics to report to the www.GeChiUI.com API.
 */
function gc_update_themes( $extra_stats = array() ) {
	if ( gc_installing() ) {
		return;
	}

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$installed_themes = gc_get_themes();
	$translations     = gc_get_installed_translations( 'themes' );

	$last_update = get_site_transient( 'update_themes' );

	if ( ! is_object( $last_update ) ) {
		$last_update = new stdClass;
	}

	$themes  = array();
	$checked = array();
	$request = array();

	// Put slug of active theme into request.
	$request['active'] = get_option( 'stylesheet' );

	foreach ( $installed_themes as $theme ) {
		$checked[ $theme->get_stylesheet() ] = $theme->get( 'Version' );

		$themes[ $theme->get_stylesheet() ] = array(
			'Name'       => $theme->get( 'Name' ),
			'Title'      => $theme->get( 'Name' ),
			'Version'    => $theme->get( 'Version' ),
			'Author'     => $theme->get( 'Author' ),
			'Author URI' => $theme->get( 'AuthorURI' ),
			'Template'   => $theme->get_template(),
			'样式表' => $theme->get_stylesheet(),
		);
	}

	$doing_cron = gc_doing_cron();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete':
			$timeout = 0;
			break;
		case 'load-update-core.php':
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-themes.php':
		case 'load-update.php':
			$timeout = HOUR_IN_SECONDS;
			break;
		default:
			if ( $doing_cron ) {
				$timeout = 2 * HOUR_IN_SECONDS;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $last_update->last_checked ) && $timeout > ( time() - $last_update->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
		$theme_changed = false;

		foreach ( $checked as $slug => $v ) {
			if ( ! isset( $last_update->checked[ $slug ] ) || (string) $last_update->checked[ $slug ] !== (string) $v ) {
				$theme_changed = true;
			}
		}

		if ( isset( $last_update->response ) && is_array( $last_update->response ) ) {
			foreach ( $last_update->response as $slug => $update_details ) {
				if ( ! isset( $checked[ $slug ] ) ) {
					$theme_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed.
		if ( ! $theme_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$last_update->last_checked = time();
	set_site_transient( 'update_themes', $last_update );

	$request['themes'] = $themes;

	$locales = array_values( get_available_languages() );

	/**
	 * Filters the locales requested for theme translations.
	 *
	 *
	 * @param array $locales Theme locales. Default is all available locales of the site.
	 */
	$locales = apply_filters( 'themes_update_check_locales', $locales );
	/**
	 * 补充一个默认的中文，这样主题和插件就不必一定使用英文，任何语言都可以，只要下载语言包即可
	 */
	$locales[] = 'zh_CN';
	$locales = array_unique( $locales );

	if ( $doing_cron ) {
		$timeout = 30;
	} else {
		// Three seconds, plus one extra second for every 10 themes.
		$timeout = 3 + (int) ( count( $themes ) / 10 );
	}

	$options = array(
		'timeout'    => $timeout,
		'body'       => array(
			'themes'       => gc_json_encode( $request ),
			'translations' => gc_json_encode( $translations ),
			'locale'       => gc_json_encode( $locales ),
		),
		'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ),
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = gc_json_encode( $extra_stats );
	}

	$url      = 'http://api.gechiui.com/themes/update-check/1.1/';
	$http_url = $url;
	$ssl      = gc_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$raw_response = gc_remote_post( $url, $options );

	if ( $ssl && is_gc_error( $raw_response ) ) {
		trigger_error(
			sprintf(
				/* translators: %s: Support forums URL. */
				__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
			headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$raw_response = gc_remote_post( $http_url, $options );
	}

	if ( is_gc_error( $raw_response ) || 200 !== gc_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$new_update               = new stdClass;
	$new_update->last_checked = time();
	$new_update->checked      = $checked;

	$response = json_decode( gc_remote_retrieve_body( $raw_response ), true );

	if ( is_array( $response ) ) {
		$new_update->response     = $response['themes'];
		$new_update->no_update    = $response['no_update'];
		$new_update->translations = $response['translations'];
	}

	set_site_transient( 'update_themes', $new_update );
}

/**
 * Performs GeChiUI automatic background updates.
 *
 * Updates GeChiUI core plus any plugins and themes that have automatic updates enabled.
 *
 *
 */
function gc_maybe_auto_update() {
	include_once ABSPATH . 'gc-admin/includes/admin.php';
	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	$upgrader = new GC_Automatic_Updater;
	$upgrader->run();
}

/**
 * Retrieves a list of all language updates available.
 *
 *
 *
 * @return object[] Array of translation objects that have available updates.
 */
function gc_get_translation_updates() {
	$updates    = array();
	$transients = array(
		'update_core'    => 'core',
		'update_plugins' => 'plugin',
		'update_themes'  => 'theme',
	);

	foreach ( $transients as $transient => $type ) {
		$transient = get_site_transient( $transient );

		if ( empty( $transient->translations ) ) {
			continue;
		}

		foreach ( $transient->translations as $translation ) {
			$updates[] = (object) $translation;
		}
	}

	return $updates;
}

/**
 * Collect counts and UI strings for available updates
 *
 *
 *
 * @return array
 */
function gc_get_update_data() {
	$counts = array(
		'plugins'      => 0,
		'themes'       => 0,
		'gechiui'    => 0,
		'translations' => 0,
	);

	$plugins = current_user_can( 'update_plugins' );

	if ( $plugins ) {
		$update_plugins = get_site_transient( 'update_plugins' );

		if ( ! empty( $update_plugins->response ) ) {
			$counts['plugins'] = count( $update_plugins->response );
		}
	}

	$themes = current_user_can( 'update_themes' );

	if ( $themes ) {
		$update_themes = get_site_transient( 'update_themes' );

		if ( ! empty( $update_themes->response ) ) {
			$counts['themes'] = count( $update_themes->response );
		}
	}

	$core = current_user_can( 'update_core' );

	if ( $core && function_exists( 'get_core_updates' ) ) {
		$update_gechiui = get_core_updates( array( 'dismissed' => false ) );

		if ( ! empty( $update_gechiui )
			&& ! in_array( $update_gechiui[0]->response, array( 'development', 'latest' ), true )
			&& current_user_can( 'update_core' )
		) {
			$counts['gechiui'] = 1;
		}
	}

	if ( ( $core || $plugins || $themes ) && gc_get_translation_updates() ) {
		$counts['translations'] = 1;
	}

	$counts['total'] = $counts['plugins'] + $counts['themes'] + $counts['gechiui'] + $counts['translations'];
	$titles          = array();

	if ( $counts['gechiui'] ) {
		/* translators: %d: Number of available GeChiUI updates. */
		$titles['gechiui'] = sprintf( __( '%d个GeChiUI更新' ), $counts['gechiui'] );
	}

	if ( $counts['plugins'] ) {
		/* translators: %d: Number of available plugin updates. */
		$titles['plugins'] = sprintf( _n( '%d个插件更新', '%d个插件更新', $counts['plugins'] ), $counts['plugins'] );
	}

	if ( $counts['themes'] ) {
		/* translators: %d: Number of available theme updates. */
		$titles['themes'] = sprintf( _n( '%d个主题更新', '%d个主题更新', $counts['themes'] ), $counts['themes'] );
	}

	if ( $counts['translations'] ) {
		$titles['translations'] = __( '翻译更新' );
	}

	$update_title = $titles ? esc_attr( implode( ', ', $titles ) ) : '';

	$update_data = array(
		'counts' => $counts,
		'title'  => $update_title,
	);
	/**
	 * Filters the returned array of update data for plugins, themes, and GeChiUI core.
	 *
	 *
	 * @param array $update_data {
	 *     Fetched update data.
	 *
	 *     @type array   $counts       An array of counts for available plugin, theme, and GeChiUI updates.
	 *     @type string  $update_title Titles of available updates.
	 * }
	 * @param array $titles An array of update counts and UI strings for available updates.
	 */
	return apply_filters( 'gc_get_update_data', $update_data, $titles );
}

/**
 * Determines whether core should be updated.
 *
 *
 *
 * @global string $gc_version The GeChiUI version string.
 */
function _maybe_update_core() {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$current = get_site_transient( 'update_core' );

	if ( isset( $current->last_checked, $current->version_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
		&& $current->version_checked === $gc_version
	) {
		return;
	}

	gc_version_check();
}
/**
 * Check the last time plugins were run before checking plugin versions.
 *
 * This might have been backported to GeChiUI 2.6.1 for performance reasons.
 * This is used for the gc-admin to check only so often instead of every page
 * load.
 *
 *
 * @access private
 */
function _maybe_update_plugins() {
	$current = get_site_transient( 'update_plugins' );

	if ( isset( $current->last_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
	) {
		return;
	}

	gc_update_plugins();
}

/**
 * Check themes versions only after a duration of time.
 *
 * This is for performance reasons to make sure that on the theme version
 * checker is not run on every page load.
 *
 *
 * @access private
 */
function _maybe_update_themes() {
	$current = get_site_transient( 'update_themes' );

	if ( isset( $current->last_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
	) {
		return;
	}

	gc_update_themes();
}

/**
 * Schedule core, theme, and plugin update checks.
 *
 *
 */
function gc_schedule_update_checks() {
	if ( ! gc_next_scheduled( 'gc_version_check' ) && ! gc_installing() ) {
		gc_schedule_event( time(), 'twicedaily', 'gc_version_check' );
	}

	if ( ! gc_next_scheduled( 'gc_update_plugins' ) && ! gc_installing() ) {
		gc_schedule_event( time(), 'twicedaily', 'gc_update_plugins' );
	}

	if ( ! gc_next_scheduled( 'gc_update_themes' ) && ! gc_installing() ) {
		gc_schedule_event( time(), 'twicedaily', 'gc_update_themes' );
	}
}

/**
 * Clear existing update caches for plugins, themes, and core.
 *
 *
 */
function gc_clean_update_cache() {
	if ( function_exists( 'gc_clean_plugins_cache' ) ) {
		gc_clean_plugins_cache();
	} else {
		delete_site_transient( 'update_plugins' );
	}

	gc_clean_themes_cache();

	delete_site_transient( 'update_core' );
}

if ( ( ! is_main_site() && ! is_network_admin() ) || gc_doing_ajax() ) {
	return;
}

add_action( 'admin_init', '_maybe_update_core' );
add_action( 'gc_version_check', 'gc_version_check' );

add_action( 'load-plugins.php', 'gc_update_plugins' );
add_action( 'load-update.php', 'gc_update_plugins' );
add_action( 'load-update-core.php', 'gc_update_plugins' );
add_action( 'admin_init', '_maybe_update_plugins' );
add_action( 'gc_update_plugins', 'gc_update_plugins' );

add_action( 'load-themes.php', 'gc_update_themes' );
add_action( 'load-update.php', 'gc_update_themes' );
add_action( 'load-update-core.php', 'gc_update_themes' );
add_action( 'admin_init', '_maybe_update_themes' );
add_action( 'gc_update_themes', 'gc_update_themes' );

add_action( 'update_option_GCLANG', 'gc_clean_update_cache', 10, 0 );

add_action( 'gc_maybe_auto_update', 'gc_maybe_auto_update' );

add_action( 'init', 'gc_schedule_update_checks' );
