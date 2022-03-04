<?php
/**
 * Defines constants and global variables that can be overridden, generally in gc-config.php.
 *
 * @package GeChiUI
 */

/**
 * Defines initial GeChiUI constants.
 *
 * @see gc_debug_mode()
 *
 *
 *
 * @global int    $blog_id    The current site ID.
 * @global string $gc_version The GeChiUI version string.
 */
function gc_initial_constants() {
	global $blog_id, $gc_version;

	/**#@+
	 * Constants for expressing human-readable data sizes in their respective number of bytes.
	 *
	 */
	define( 'KB_IN_BYTES', 1024 );
	define( 'MB_IN_BYTES', 1024 * KB_IN_BYTES );
	define( 'GB_IN_BYTES', 1024 * MB_IN_BYTES );
	define( 'TB_IN_BYTES', 1024 * GB_IN_BYTES );
	/**#@-*/

	// Start of run timestamp.
	if ( ! defined( 'GC_START_TIMESTAMP' ) ) {
		define( 'GC_START_TIMESTAMP', microtime( true ) );
	}

	$current_limit     = ini_get( 'memory_limit' );
	$current_limit_int = gc_convert_hr_to_bytes( $current_limit );

	// Define memory limits.
	if ( ! defined( 'GC_MEMORY_LIMIT' ) ) {
		if ( false === gc_is_ini_value_changeable( 'memory_limit' ) ) {
			define( 'GC_MEMORY_LIMIT', $current_limit );
		} elseif ( is_multisite() ) {
			define( 'GC_MEMORY_LIMIT', '64M' );
		} else {
			define( 'GC_MEMORY_LIMIT', '40M' );
		}
	}

	if ( ! defined( 'GC_MAX_MEMORY_LIMIT' ) ) {
		if ( false === gc_is_ini_value_changeable( 'memory_limit' ) ) {
			define( 'GC_MAX_MEMORY_LIMIT', $current_limit );
		} elseif ( -1 === $current_limit_int || $current_limit_int > 268435456 /* = 256M */ ) {
			define( 'GC_MAX_MEMORY_LIMIT', $current_limit );
		} else {
			define( 'GC_MAX_MEMORY_LIMIT', '256M' );
		}
	}

	// Set memory limits.
	$gc_limit_int = gc_convert_hr_to_bytes( GC_MEMORY_LIMIT );
	if ( -1 !== $current_limit_int && ( -1 === $gc_limit_int || $gc_limit_int > $current_limit_int ) ) {
		ini_set( 'memory_limit', GC_MEMORY_LIMIT );
	}

	if ( ! isset( $blog_id ) ) {
		$blog_id = 1;
	}

	if ( ! defined( 'GC_CONTENT_DIR' ) ) {
		define( 'GC_CONTENT_DIR', ABSPATH . 'gc-content' ); // No trailing slash, full paths only - GC_CONTENT_URL is defined further down.
	}

	// Add define( 'GC_DEBUG', true ); to gc-config.php to enable display of notices during development.
	if ( ! defined( 'GC_DEBUG' ) ) {
		if ( 'development' === gc_get_environment_type() ) {
			define( 'GC_DEBUG', true );
		} else {
			define( 'GC_DEBUG', false );
		}
	}

	// Add define( 'GC_DEBUG_DISPLAY', null ); to gc-config.php to use the globally configured setting
	// for 'display_errors' and not force errors to be displayed. Use false to force 'display_errors' off.
	if ( ! defined( 'GC_DEBUG_DISPLAY' ) ) {
		define( 'GC_DEBUG_DISPLAY', true );
	}

	// Add define( 'GC_DEBUG_LOG', true ); to enable error logging to gc-content/debug.log.
	if ( ! defined( 'GC_DEBUG_LOG' ) ) {
		define( 'GC_DEBUG_LOG', false );
	}

	if ( ! defined( 'GC_CACHE' ) ) {
		define( 'GC_CACHE', false );
	}

	// Add define( 'SCRIPT_DEBUG', true ); to gc-config.php to enable loading of non-minified,
	// non-concatenated scripts and stylesheets.
	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		if ( ! empty( $gc_version ) ) {
			$develop_src = false !== strpos( $gc_version, '-src' );
		} else {
			$develop_src = false;
		}

		define( 'SCRIPT_DEBUG', $develop_src );
	}

	/**
	 * Private
	 */
	if ( ! defined( 'MEDIA_TRASH' ) ) {
		define( 'MEDIA_TRASH', false );
	}

	if ( ! defined( 'SHORTINIT' ) ) {
		define( 'SHORTINIT', false );
	}

	// Constants for features added to GC that should short-circuit their plugin implementations.
	define( 'GC_FEATURE_BETTER_PASSWORDS', true );

	/**#@+
	 * Constants for expressing human-readable intervals
	 * in their respective number of seconds.
	 *
	 * Please note that these values are approximate and are provided for convenience.
	 * For example, MONTH_IN_SECONDS wrongly assumes every month has 30 days and
	 * YEAR_IN_SECONDS does not take leap years into account.
	 *
	 * If you need more accuracy please consider using the DateTime class (https://www.php.net/manual/en/class.datetime.php).
	 *
	 */
	define( 'MINUTE_IN_SECONDS', 60 );
	define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
	define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
	define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
	define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
	define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );
	/**#@-*/
}

/**
 * Defines plugin directory GeChiUI constants.
 *
 * Defines must-use plugin directory constants, which may be overridden in the sunrise.php drop-in.
 *
 *
 */
function gc_plugin_directory_constants() {
	if ( ! defined( 'GC_CONTENT_URL' ) ) {
		define( 'GC_CONTENT_URL', get_option( 'siteurl' ) . '/gc-content' ); // Full URL - GC_CONTENT_DIR is defined further up.
	}

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 */
	if ( ! defined( 'GC_PLUGIN_DIR' ) ) {
		define( 'GC_PLUGIN_DIR', GC_CONTENT_DIR . '/plugins' ); // Full path, no trailing slash.
	}

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 */
	if ( ! defined( 'GC_PLUGIN_URL' ) ) {
		define( 'GC_PLUGIN_URL', GC_CONTENT_URL . '/plugins' ); // Full URL, no trailing slash.
	}

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 * @deprecated
	 */
	if ( ! defined( 'PLUGINDIR' ) ) {
		define( 'PLUGINDIR', 'gc-content/plugins' ); // Relative to ABSPATH. For back compat.
	}

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 */
	if ( ! defined( 'GCMU_PLUGIN_DIR' ) ) {
		define( 'GCMU_PLUGIN_DIR', GC_CONTENT_DIR . '/mu-plugins' ); // Full path, no trailing slash.
	}

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 */
	if ( ! defined( 'GCMU_PLUGIN_URL' ) ) {
		define( 'GCMU_PLUGIN_URL', GC_CONTENT_URL . '/mu-plugins' ); // Full URL, no trailing slash.
	}

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 * @deprecated
	 */
	if ( ! defined( 'MUPLUGINDIR' ) ) {
		define( 'MUPLUGINDIR', 'gc-content/mu-plugins' ); // Relative to ABSPATH. For back compat.
	}
}

/**
 * Defines cookie-related GeChiUI constants.
 *
 * Defines constants after multisite is loaded.
 *
 *
 */
function gc_cookie_constants() {
	/**
	 * Used to guarantee unique hash cookies.
	 *
	 */
	if ( ! defined( 'COOKIEHASH' ) ) {
		$siteurl = get_site_option( 'siteurl' );
		if ( $siteurl ) {
			define( 'COOKIEHASH', md5( $siteurl ) );
		} else {
			define( 'COOKIEHASH', '' );
		}
	}

	/**
	 */
	if ( ! defined( 'USER_COOKIE' ) ) {
		define( 'USER_COOKIE', 'gechiuiuser_' . COOKIEHASH );
	}

	/**
	 */
	if ( ! defined( 'PASS_COOKIE' ) ) {
		define( 'PASS_COOKIE', 'gechiuipass_' . COOKIEHASH );
	}

	/**
	 */
	if ( ! defined( 'AUTH_COOKIE' ) ) {
		define( 'AUTH_COOKIE', 'gechiui_' . COOKIEHASH );
	}

	/**
	 */
	if ( ! defined( 'SECURE_AUTH_COOKIE' ) ) {
		define( 'SECURE_AUTH_COOKIE', 'gechiui_sec_' . COOKIEHASH );
	}

	/**
	 */
	if ( ! defined( 'LOGGED_IN_COOKIE' ) ) {
		define( 'LOGGED_IN_COOKIE', 'gechiui_logged_in_' . COOKIEHASH );
	}

	/**
	 */
	if ( ! defined( 'TEST_COOKIE' ) ) {
		define( 'TEST_COOKIE', 'gechiui_test_cookie' );
	}

	/**
	 */
	if ( ! defined( 'COOKIEPATH' ) ) {
		define( 'COOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'home' ) . '/' ) );
	}

	/**
	 */
	if ( ! defined( 'SITECOOKIEPATH' ) ) {
		define( 'SITECOOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'siteurl' ) . '/' ) );
	}

	/**
	 */
	if ( ! defined( 'ADMIN_COOKIE_PATH' ) ) {
		define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'gc-admin' );
	}

	/**
	 */
	if ( ! defined( 'PLUGINS_COOKIE_PATH' ) ) {
		define( 'PLUGINS_COOKIE_PATH', preg_replace( '|https?://[^/]+|i', '', GC_PLUGIN_URL ) );
	}

	/**
	 */
	if ( ! defined( 'COOKIE_DOMAIN' ) ) {
		define( 'COOKIE_DOMAIN', false );
	}

	if ( ! defined( 'RECOVERY_MODE_COOKIE' ) ) {
		/**
		 */
		define( 'RECOVERY_MODE_COOKIE', 'gechiui_rec_' . COOKIEHASH );
	}
}

/**
 * Defines SSL-related GeChiUI constants.
 *
 *
 */
function gc_ssl_constants() {
	/**
	 */
	if ( ! defined( 'FORCE_SSL_ADMIN' ) ) {
		if ( 'https' === parse_url( get_option( 'siteurl' ), PHP_URL_SCHEME ) ) {
			define( 'FORCE_SSL_ADMIN', true );
		} else {
			define( 'FORCE_SSL_ADMIN', false );
		}
	}
	force_ssl_admin( FORCE_SSL_ADMIN );

	/**
	 * @deprecated 4.0.0
	 */
	if ( defined( 'FORCE_SSL_LOGIN' ) && FORCE_SSL_LOGIN ) {
		force_ssl_admin( true );
	}
}

/**
 * Defines functionality-related GeChiUI constants.
 *
 *
 */
function gc_functionality_constants() {
	/**
	 */
	if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
		define( 'AUTOSAVE_INTERVAL', MINUTE_IN_SECONDS );
	}

	/**
	 */
	if ( ! defined( 'EMPTY_TRASH_DAYS' ) ) {
		define( 'EMPTY_TRASH_DAYS', 30 );
	}

	if ( ! defined( 'GC_POST_REVISIONS' ) ) {
		define( 'GC_POST_REVISIONS', true );
	}

	/**
	 */
	if ( ! defined( 'GC_CRON_LOCK_TIMEOUT' ) ) {
		define( 'GC_CRON_LOCK_TIMEOUT', MINUTE_IN_SECONDS );
	}
}

/**
 * Defines templating-related GeChiUI constants.
 *
 *
 */
function gc_templating_constants() {
	/**
	 * Filesystem path to the current active template directory.
	 *
	 */
	define( 'TEMPLATEPATH', get_template_directory() );

	/**
	 * Filesystem path to the current active template stylesheet directory.
	 *
	 */
	define( 'STYLESHEETPATH', get_stylesheet_directory() );

	/**
	 * Slug of the default theme for this installation.
	 * Used as the default theme when installing new sites.
	 * It will be used as the fallback if the active theme doesn't exist.
	 *
	 *
	 * @see GC_Theme::get_core_default_theme()
	 */
	if ( ! defined( 'GC_DEFAULT_THEME' ) ) {
		define( 'GC_DEFAULT_THEME', 'defaultbird' );
	}

}
