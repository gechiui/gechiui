<?php
/**
 * These functions are needed to load GeChiUI.
 *
 * @package GeChiUI
 */

/**
 * Return the HTTP protocol sent by the server.
 *
 *
 *
 * @return string The HTTP protocol. Default: HTTP/1.0.
 */
function gc_get_server_protocol() {
	$protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : '';
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}
	return $protocol;
}

/**
 * Fix `$_SERVER` variables for various setups.
 *
 *
 * @access private
 *
 * @global string $PHP_SELF The filename of the currently executing script,
 *                          relative to the document root.
 */
function gc_fix_server_vars() {
	global $PHP_SELF;

	$default_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI'     => '',
	);

	$_SERVER = array_merge( $default_server_values, $_SERVER );

	// Fix for IIS when running with PHP ISAPI.
	if ( empty( $_SERVER['REQUEST_URI'] ) || ( 'cgi-fcgi' !== PHP_SAPI && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

		if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
			// IIS Mod-Rewrite.
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		} elseif ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
			// IIS Isapi_Rewrite.
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		} else {
			// Use ORIG_PATH_INFO if there is no PATH_INFO.
			if ( ! isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
				$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
			}

			// Some IIS + PHP configurations put the script-name in the path-info (no need to append it twice).
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] ) {
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
				} else {
					$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
				}
			}

			// Append the query string if it exists and isn't null.
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
	}

	// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests.
	if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi' ) == strlen( $_SERVER['SCRIPT_FILENAME'] ) - 7 ) ) {
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	}

	// Fix for Dreamhost and other PHP as CGI hosts.
	if ( isset( $_SERVER['SCRIPT_NAME'] ) && ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) !== false ) ) {
		unset( $_SERVER['PATH_INFO'] );
	}

	// Fix empty PHP_SELF.
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty( $PHP_SELF ) ) {
		$_SERVER['PHP_SELF'] = preg_replace( '/(\?.*)?$/', '', $_SERVER['REQUEST_URI'] );
		$PHP_SELF            = $_SERVER['PHP_SELF'];
	}

	gc_populate_basic_auth_from_authorization_header();
}

/**
 * Populates the Basic Auth server details from the Authorization header.
 *
 * Some servers running in CGI or FastCGI mode don't pass the Authorization
 * header on to GeChiUI.  If it's been rewritten to the `HTTP_AUTHORIZATION` header,
 * fill in the proper $_SERVER variables instead.
 *
 *
 */
function gc_populate_basic_auth_from_authorization_header() {
	// If we don't have anything to pull from, return early.
	if ( ! isset( $_SERVER['HTTP_AUTHORIZATION'] ) && ! isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		return;
	}

	// If either PHP_AUTH key is already set, do nothing.
	if ( isset( $_SERVER['PHP_AUTH_USER'] ) || isset( $_SERVER['PHP_AUTH_PW'] ) ) {
		return;
	}

	// From our prior conditional, one of these must be set.
	$header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? $_SERVER['HTTP_AUTHORIZATION'] : $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

	// Test to make sure the pattern matches expected.
	if ( ! preg_match( '%^Basic [a-z\d/+]*={0,2}$%i', $header ) ) {
		return;
	}

	// Removing `Basic ` the token would start six characters in.
	$token    = substr( $header, 6 );
	$userpass = base64_decode( $token );

	list( $user, $pass ) = explode( ':', $userpass );

	// Now shove them in the proper keys where we're expecting later on.
	$_SERVER['PHP_AUTH_USER'] = $user;
	$_SERVER['PHP_AUTH_PW']   = $pass;
}

/**
 * Check for the required PHP version, and the MySQL extension or
 * a database drop-in.
 *
 * Dies if requirements are not met.
 *
 *
 * @access private
 *
 * @global string $required_php_version The required PHP version string.
 * @global string $gc_version           The GeChiUI version string.
 */
function gc_check_php_mysql_versions() {
	global $required_php_version, $gc_version;
	$php_version = phpversion();

	if ( version_compare( $required_php_version, $php_version, '>' ) ) {
		$protocol = gc_get_server_protocol();
		header( sprintf( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header( 'Content-Type: text/html; charset=utf-8' );
		printf( 'Your server is running PHP version %1$s but GeChiUI %2$s requires at least %3$s.', $php_version, $gc_version, $required_php_version );
		exit( 1 );
	}

	if ( ! extension_loaded( 'mysql' ) && ! extension_loaded( 'mysqli' ) && ! extension_loaded( 'mysqlnd' )
		// This runs before default constants are defined, so we can't assume GC_CONTENT_DIR is set yet.
		&& ( defined( 'GC_CONTENT_DIR' ) && ! file_exists( GC_CONTENT_DIR . '/db.php' )
			|| ! file_exists( ABSPATH . 'gc-content/db.php' ) )
	) {
		require_once ABSPATH . GCINC . '/functions.php';
		gc_load_translations_early();
		$args = array(
			'exit' => false,
			'code' => 'mysql_not_found',
		);
		gc_die(
			__( '您的PHP似乎没有安装运行GeChiUI所必需的MySQL扩展。' ),
			__( '未满足要求' ),
			$args
		);
		exit( 1 );
	}
}

/**
 * Retrieves the current environment type.
 *
 * The type can be set via the `GC_ENVIRONMENT_TYPE` global system variable,
 * or a constant of the same name.
 *
 * Possible values are 'local', 'development', 'staging', and 'production'.
 * If not set, the type defaults to 'production'.
 *
 *
 *
 *
 *
 * @return string The current environment type.
 */
function gc_get_environment_type() {
	static $current_env = '';

	if ( ! defined( 'GC_RUN_CORE_TESTS' ) && $current_env ) {
		return $current_env;
	}

	$gc_environments = array(
		'local',
		'development',
		'staging',
		'production',
	);

	// Add a note about the deprecated GC_ENVIRONMENT_TYPES constant.
	if ( defined( 'GC_ENVIRONMENT_TYPES' ) && function_exists( '_deprecated_argument' ) ) {
		if ( function_exists( '__' ) ) {
			/* translators: %s: GC_ENVIRONMENT_TYPES */
			$message = sprintf( __( '常量%s已不再支持。' ), 'GC_ENVIRONMENT_TYPES' );
		} else {
			$message = sprintf( '常量%s已不再支持。', 'GC_ENVIRONMENT_TYPES' );
		}

		_deprecated_argument(
			'define()',
			'5.5.1',
			$message
		);
	}

	// Check if the environment variable has been set, if `getenv` is available on the system.
	if ( function_exists( 'getenv' ) ) {
		$has_env = getenv( 'GC_ENVIRONMENT_TYPE' );
		if ( false !== $has_env ) {
			$current_env = $has_env;
		}
	}

	// Fetch the environment from a constant, this overrides the global system variable.
	if ( defined( 'GC_ENVIRONMENT_TYPE' ) ) {
		$current_env = GC_ENVIRONMENT_TYPE;
	}

	// Make sure the environment is an allowed one, and not accidentally set to an invalid value.
	if ( ! in_array( $current_env, $gc_environments, true ) ) {
		$current_env = 'production';
	}

	return $current_env;
}

/**
 * Don't load all of GeChiUI when handling a favicon.ico request.
 *
 * Instead, send the headers for a zero-length favicon and bail.
 *
 *
 * @deprecated 5.4.0 Deprecated in favor of do_favicon().
 */
function gc_favicon_request() {
	if ( '/favicon.ico' === $_SERVER['REQUEST_URI'] ) {
		header( 'Content-Type: image/vnd.microsoft.icon' );
		exit;
	}
}

/**
 * Die with a maintenance message when conditions are met.
 *
 * The default message can be replaced by using a drop-in (maintenance.php in
 * the gc-content directory).
 *
 *
 * @access private
 */
function gc_maintenance() {
	// Return if maintenance mode is disabled.
	if ( ! gc_is_maintenance_mode() ) {
		return;
	}

	if ( file_exists( GC_CONTENT_DIR . '/maintenance.php' ) ) {
		require_once GC_CONTENT_DIR . '/maintenance.php';
		die();
	}

	require_once ABSPATH . GCINC . '/functions.php';
	gc_load_translations_early();

	header( 'Retry-After: 600' );

	gc_die(
		__( '正在执行例行维护，请一分钟后回来。' ),
		__( '维护' ),
		503
	);
}

/**
 * Check if maintenance mode is enabled.
 *
 * Checks for a file in the GeChiUI root directory named ".maintenance".
 * This file will contain the variable $upgrading, set to the time the file
 * was created. If the file was created less than 10 minutes ago, GeChiUI
 * is in maintenance mode.
 *
 *
 *
 * @global int $upgrading The Unix timestamp marking when upgrading GeChiUI began.
 *
 * @return bool True if maintenance mode is enabled, false otherwise.
 */
function gc_is_maintenance_mode() {
	global $upgrading;

	if ( ! file_exists( ABSPATH . '.maintenance' ) || gc_installing() ) {
		return false;
	}

	require ABSPATH . '.maintenance';
	// If the $upgrading timestamp is older than 10 minutes, consider maintenance over.
	if ( ( time() - $upgrading ) >= 10 * MINUTE_IN_SECONDS ) {
		return false;
	}

	/**
	 * Filters whether to enable maintenance mode.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web runtimes. If this filter returns true, maintenance mode will be
	 * active and the request will end. If false, the request will be allowed to
	 * continue processing even if maintenance mode should be active.
	 *
	 *
	 * @param bool $enable_checks Whether to enable maintenance mode. Default true.
	 * @param int  $upgrading     The timestamp set in the .maintenance file.
	 */
	if ( ! apply_filters( 'enable_maintenance_mode', true, $upgrading ) ) {
		return false;
	}

	return true;
}

/**
 * Get the time elapsed so far during this PHP script.
 *
 * Uses REQUEST_TIME_FLOAT that appeared in PHP 5.4.0.
 *
 *
 *
 * @return float Seconds since the PHP script started.
 */
function timer_float() {
	return microtime( true ) - $_SERVER['REQUEST_TIME_FLOAT'];
}

/**
 * Start the GeChiUI micro-timer.
 *
 *
 * @access private
 *
 * @global float $timestart Unix timestamp set at the beginning of the page load.
 * @see timer_stop()
 *
 * @return bool Always returns true.
 */
function timer_start() {
	global $timestart;
	$timestart = microtime( true );
	return true;
}

/**
 * Retrieve or display the time from the page start to when function is called.
 *
 *
 *
 * @global float   $timestart Seconds from when timer_start() is called.
 * @global float   $timeend   Seconds from when function is called.
 *
 * @param int|bool $display   Whether to echo or return the results. Accepts 0|false for return,
 *                            1|true for echo. Default 0|false.
 * @param int      $precision The number of digits from the right of the decimal to display.
 *                            Default 3.
 * @return string The "second.microsecond" finished time calculation. The number is formatted
 *                for human consumption, both localized and rounded.
 */
function timer_stop( $display = 0, $precision = 3 ) {
	global $timestart, $timeend;
	$timeend   = microtime( true );
	$timetotal = $timeend - $timestart;
	$r         = ( function_exists( 'number_format_i18n' ) ) ? number_format_i18n( $timetotal, $precision ) : number_format( $timetotal, $precision );
	if ( $display ) {
		echo $r;
	}
	return $r;
}

/**
 * Set PHP error reporting based on GeChiUI debug settings.
 *
 * Uses three constants: `GC_DEBUG`, `GC_DEBUG_DISPLAY`, and `GC_DEBUG_LOG`.
 * All three can be defined in gc-config.php. By default, `GC_DEBUG` and
 * `GC_DEBUG_LOG` are set to false, and `GC_DEBUG_DISPLAY` is set to true.
 *
 * When `GC_DEBUG` is true, all PHP notices are reported. GeChiUI will also
 * display internal notices: when a deprecated GeChiUI function, function
 * argument, or file is used. Deprecated code may be removed from a later
 * version.
 *
 * It is strongly recommended that plugin and theme developers use `GC_DEBUG`
 * in their development environments.
 *
 * `GC_DEBUG_DISPLAY` and `GC_DEBUG_LOG` perform no function unless `GC_DEBUG`
 * is true.
 *
 * When `GC_DEBUG_DISPLAY` is true, GeChiUI will force errors to be displayed.
 * `GC_DEBUG_DISPLAY` defaults to true. Defining it as null prevents GeChiUI
 * from changing the global configuration setting. Defining `GC_DEBUG_DISPLAY`
 * as false will force errors to be hidden.
 *
 * When `GC_DEBUG_LOG` is true, errors will be logged to `gc-content/debug.log`.
 * When `GC_DEBUG_LOG` is a valid path, errors will be logged to the specified file.
 *
 * Errors are never displayed for XML-RPC, REST, `ms-files.php`, and Ajax requests.
 *
 *
 *
 * @access private
 */
function gc_debug_mode() {
	/**
	 * Filters whether to allow the debug mode check to occur.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web runtimes. Returning false causes the `GC_DEBUG` and related
	 * constants to not be checked and the default PHP values for errors
	 * will be used unless you take care to update them yourself.
	 *
	 * To use this filter you must define a `$gc_filter` global before
	 * GeChiUI loads, usually in `gc-config.php`.
	 *
	 * Example:
	 *
	 *     $GLOBALS['gc_filter'] = array(
	 *         'enable_gc_debug_mode_checks' => array(
	 *             10 => array(
	 *                 array(
	 *                     'accepted_args' => 0,
	 *                     'function'      => function() {
	 *                         return false;
	 *                     },
	 *                 ),
	 *             ),
	 *         ),
	 *     );
	 *
	 *
	 * @param bool $enable_debug_mode Whether to enable debug mode checks to occur. Default true.
	 */
	if ( ! apply_filters( 'enable_gc_debug_mode_checks', true ) ) {
		return;
	}

	if ( GC_DEBUG ) {
		error_reporting( E_ALL );

		if ( GC_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 1 );
		} elseif ( null !== GC_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 0 );
		}

		if ( in_array( strtolower( (string) GC_DEBUG_LOG ), array( 'true', '1' ), true ) ) {
			$log_path = GC_CONTENT_DIR . '/debug.log';
		} elseif ( is_string( GC_DEBUG_LOG ) ) {
			$log_path = GC_DEBUG_LOG;
		} else {
			$log_path = false;
		}

		if ( $log_path ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', $log_path );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}

	if (
		defined( 'XMLRPC_REQUEST' ) || defined( 'REST_REQUEST' ) || defined( 'MS_FILES_REQUEST' ) ||
		( defined( 'GC_INSTALLING' ) && GC_INSTALLING ) ||
		gc_doing_ajax() || gc_is_json_request() ) {
		ini_set( 'display_errors', 0 );
	}
}

/**
 * Set the location of the language directory.
 *
 * To set directory manually, define the `GC_LANG_DIR` constant
 * in gc-config.php.
 *
 * If the language directory exists within `GC_CONTENT_DIR`, it
 * is used. Otherwise the language directory is assumed to live
 * in `GCINC`.
 *
 *
 * @access private
 */
function gc_set_lang_dir() {
	if ( ! defined( 'GC_LANG_DIR' ) ) {
		if ( file_exists( GC_CONTENT_DIR . '/languages' ) && @is_dir( GC_CONTENT_DIR . '/languages' ) || ! @is_dir( ABSPATH . GCINC . '/languages' ) ) {
			/**
			 * Server path of the language directory.
			 *
			 * No leading slash, no trailing slash, full path, not relative to ABSPATH
			 *
		
			 */
			define( 'GC_LANG_DIR', GC_CONTENT_DIR . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old static relative path maintained for limited backward compatibility - won't work in some cases.
				define( 'LANGDIR', 'gc-content/languages' );
			}
		} else {
			/**
			 * Server path of the language directory.
			 *
			 * No leading slash, no trailing slash, full path, not relative to `ABSPATH`.
			 *
		
			 */
			define( 'GC_LANG_DIR', ABSPATH . GCINC . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old relative path maintained for backward compatibility.
				define( 'LANGDIR', GCINC . '/languages' );
			}
		}
	}
}

/**
 * Load the database class file and instantiate the `$gcdb` global.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 */
function require_gc_db() {
	global $gcdb;

	require_once ABSPATH . GCINC . '/gc-db.php';
	if ( file_exists( GC_CONTENT_DIR . '/db.php' ) ) {
		require_once GC_CONTENT_DIR . '/db.php';
	}

	if ( isset( $gcdb ) ) {
		return;
	}

	$dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
	$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
	$dbname     = defined( 'DB_NAME' ) ? DB_NAME : '';
	$dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

	$gcdb = new gcdb( $dbuser, $dbpassword, $dbname, $dbhost );
}

/**
 * Set the database table prefix and the format specifiers for database
 * table columns.
 *
 * Columns not listed here default to `%s`.
 *
 *
 * @access private
 *
 * @global gcdb   $gcdb         GeChiUI database abstraction object.
 * @global string $table_prefix The database table prefix.
 */
function gc_set_gcdb_vars() {
	global $gcdb, $table_prefix;
	if ( ! empty( $gcdb->error ) ) {
		dead_db();
	}

	$gcdb->field_types = array(
		'post_author'      => '%d',
		'post_parent'      => '%d',
		'menu_order'       => '%d',
		'term_id'          => '%d',
		'term_group'       => '%d',
		'term_taxonomy_id' => '%d',
		'parent'           => '%d',
		'count'            => '%d',
		'object_id'        => '%d',
		'term_order'       => '%d',
		'ID'               => '%d',
		'comment_ID'       => '%d',
		'comment_post_ID'  => '%d',
		'comment_parent'   => '%d',
		'user_id'          => '%d',
		'link_id'          => '%d',
		'link_owner'       => '%d',
		'link_rating'      => '%d',
		'option_id'        => '%d',
		'blog_id'          => '%d',
		'meta_id'          => '%d',
		'post_id'          => '%d',
		'user_status'      => '%d',
		'umeta_id'         => '%d',
		'comment_karma'    => '%d',
		'comment_count'    => '%d',
		// Multisite:
		'active'           => '%d',
		'cat_id'           => '%d',
		'deleted'          => '%d',
		'lang_id'          => '%d',
		'mature'           => '%d',
		'public'           => '%d',
		'site_id'          => '%d',
		'spam'             => '%d',
	);

	$prefix = $gcdb->set_prefix( $table_prefix );

	if ( is_gc_error( $prefix ) ) {
		gc_load_translations_early();
		gc_die(
			sprintf(
				/* translators: 1: $table_prefix, 2: gc-config.php */
				__( '<strong>错误</strong>：%2$s中的%1$s只能包含数字、字母和下划线。' ),
				'<code>$table_prefix</code>',
				'<code>gc-config.php</code>'
			)
		);
	}
}

/**
 * Toggle `$_gc_using_ext_object_cache` on and off without directly
 * touching global.
 *
 *
 *
 * @global bool $_gc_using_ext_object_cache
 *
 * @param bool $using Whether external object cache is being used.
 * @return bool The current 'using' setting.
 */
function gc_using_ext_object_cache( $using = null ) {
	global $_gc_using_ext_object_cache;
	$current_using = $_gc_using_ext_object_cache;
	if ( null !== $using ) {
		$_gc_using_ext_object_cache = $using;
	}
	return $current_using;
}

/**
 * Start the GeChiUI object cache.
 *
 * If an object-cache.php file exists in the gc-content directory,
 * it uses that drop-in as an external object cache.
 *
 *
 * @access private
 *
 * @global array $gc_filter Stores all of the filters.
 */
function gc_start_object_cache() {
	global $gc_filter;
	static $first_init = true;

	// Only perform the following checks once.

	/**
	 * Filters whether to enable loading of the object-cache.php drop-in.
	 *
	 * This filter runs before it can be used by plugins. It is designed for non-web
	 * runtimes. If false is returned, object-cache.php will never be loaded.
	 *
	 *
	 * @param bool $enable_object_cache Whether to enable loading object-cache.php (if present).
	 *                                  Default true.
	 */
	if ( $first_init && apply_filters( 'enable_loading_object_cache_dropin', true ) ) {
		if ( ! function_exists( 'gc_cache_init' ) ) {
			/*
			 * This is the normal situation. First-run of this function. No
			 * caching backend has been loaded.
			 *
			 * We try to load a custom caching backend, and then, if it
			 * results in a gc_cache_init() function existing, we note
			 * that an external object cache is being used.
			 */
			if ( file_exists( GC_CONTENT_DIR . '/object-cache.php' ) ) {
				require_once GC_CONTENT_DIR . '/object-cache.php';
				if ( function_exists( 'gc_cache_init' ) ) {
					gc_using_ext_object_cache( true );
				}

				// Re-initialize any hooks added manually by object-cache.php.
				if ( $gc_filter ) {
					$gc_filter = GC_Hook::build_preinitialized_hooks( $gc_filter );
				}
			}
		} elseif ( ! gc_using_ext_object_cache() && file_exists( GC_CONTENT_DIR . '/object-cache.php' ) ) {
			/*
			 * Sometimes advanced-cache.php can load object-cache.php before
			 * this function is run. This breaks the function_exists() check
			 * above and can result in gc_using_ext_object_cache() returning
			 * false when actually an external cache is in use.
			 */
			gc_using_ext_object_cache( true );
		}
	}

	if ( ! gc_using_ext_object_cache() ) {
		require_once ABSPATH . GCINC . '/cache.php';
	}

	require_once ABSPATH . GCINC . '/cache-compat.php';

	/*
	 * If cache supports reset, reset instead of init if already
	 * initialized. Reset signals to the cache that global IDs
	 * have changed and it may need to update keys and cleanup caches.
	 */
	if ( ! $first_init && function_exists( 'gc_cache_switch_to_blog' ) ) {
		gc_cache_switch_to_blog( get_current_blog_id() );
	} elseif ( function_exists( 'gc_cache_init' ) ) {
		gc_cache_init();
	}

	if ( function_exists( 'gc_cache_add_global_groups' ) ) {
		gc_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'blog-lookup', 'blog-details', 'site-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'blog_meta' ) );
		gc_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
	}

	$first_init = false;
}

/**
 * Redirect to the installer if GeChiUI is not installed.
 *
 * Dies with an error message when Multisite is enabled.
 *
 *
 * @access private
 */
function gc_not_installed() {
	if ( is_multisite() ) {
		if ( ! is_blog_installed() && ! gc_installing() ) {
			nocache_headers();

			gc_die( __( '您请求的站点未被正确安装。请联系系统管理员。' ) );
		}
	} elseif ( ! is_blog_installed() && ! gc_installing() ) {
		nocache_headers();

		require ABSPATH . GCINC . '/kses.php';
		require ABSPATH . GCINC . '/pluggable.php';

		$link = gc_guess_url() . '/gc-admin/install.php';

		gc_redirect( $link );
		die();
	}
}

/**
 * Retrieve an array of must-use plugin files.
 *
 * The default directory is gc-content/mu-plugins. To change the default
 * directory manually, define `GCMU_PLUGIN_DIR` and `GCMU_PLUGIN_URL`
 * in gc-config.php.
 *
 *
 * @access private
 *
 * @return string[] Array of absolute paths of files to include.
 */
function gc_get_mu_plugins() {
	$mu_plugins = array();
	if ( ! is_dir( GCMU_PLUGIN_DIR ) ) {
		return $mu_plugins;
	}
	$dh = opendir( GCMU_PLUGIN_DIR );
	if ( ! $dh ) {
		return $mu_plugins;
	}
	while ( ( $plugin = readdir( $dh ) ) !== false ) {
		if ( '.php' === substr( $plugin, -4 ) ) {
			$mu_plugins[] = GCMU_PLUGIN_DIR . '/' . $plugin;
		}
	}
	closedir( $dh );
	sort( $mu_plugins );

	return $mu_plugins;
}

/**
 * Retrieve an array of active and valid plugin files.
 *
 * While upgrading or installing GeChiUI, no plugins are returned.
 *
 * The default directory is `gc-content/plugins`. To change the default
 * directory manually, define `GC_PLUGIN_DIR` and `GC_PLUGIN_URL`
 * in `gc-config.php`.
 *
 *
 * @access private
 *
 * @return string[] Array of paths to plugin files relative to the plugins directory.
 */
function gc_get_active_and_valid_plugins() {
	$plugins        = array();
	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Check for hacks file if the option is enabled.
	if ( get_option( 'hack_file' ) && file_exists( ABSPATH . 'my-hacks.php' ) ) {
		_deprecated_file( 'my-hacks.php', '1.5.0' );
		array_unshift( $plugins, ABSPATH . 'my-hacks.php' );
	}

	if ( empty( $active_plugins ) || gc_installing() ) {
		return $plugins;
	}

	$network_plugins = is_multisite() ? gc_get_active_network_plugins() : false;

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin )                     // $plugin must validate as file.
			&& '.php' === substr( $plugin, -4 )             // $plugin must end with '.php'.
			&& file_exists( GC_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist.
			// Not already included as a network plugin.
			&& ( ! $network_plugins || ! in_array( GC_PLUGIN_DIR . '/' . $plugin, $network_plugins, true ) )
			) {
			$plugins[] = GC_PLUGIN_DIR . '/' . $plugin;
		}
	}

	/*
	 * Remove plugins from the list of active plugins when we're on an endpoint
	 * that should be protected against WSODs and the plugin is paused.
	 */
	if ( gc_is_recovery_mode() ) {
		$plugins = gc_skip_paused_plugins( $plugins );
	}

	return $plugins;
}

/**
 * Filters a given list of plugins, removing any paused plugins from it.
 *
 *
 *
 * @param string[] $plugins Array of absolute plugin main file paths.
 * @return string[] Filtered array of plugins, without any paused plugins.
 */
function gc_skip_paused_plugins( array $plugins ) {
	$paused_plugins = gc_paused_plugins()->get_all();

	if ( empty( $paused_plugins ) ) {
		return $plugins;
	}

	foreach ( $plugins as $index => $plugin ) {
		list( $plugin ) = explode( '/', plugin_basename( $plugin ) );

		if ( array_key_exists( $plugin, $paused_plugins ) ) {
			unset( $plugins[ $index ] );

			// Store list of paused plugins for displaying an admin notice.
			$GLOBALS['_paused_plugins'][ $plugin ] = $paused_plugins[ $plugin ];
		}
	}

	return $plugins;
}

/**
 * Retrieves an array of active and valid themes.
 *
 * While upgrading or installing GeChiUI, no themes are returned.
 *
 *
 * @access private
 *
 * @return string[] Array of absolute paths to theme directories.
 */
function gc_get_active_and_valid_themes() {
	global $pagenow;

	$themes = array();

	if ( gc_installing() && 'gc-activate.php' !== $pagenow ) {
		return $themes;
	}

	if ( TEMPLATEPATH !== STYLESHEETPATH ) {
		$themes[] = STYLESHEETPATH;
	}

	$themes[] = TEMPLATEPATH;

	/*
	 * Remove themes from the list of active themes when we're on an endpoint
	 * that should be protected against WSODs and the theme is paused.
	 */
	if ( gc_is_recovery_mode() ) {
		$themes = gc_skip_paused_themes( $themes );

		// If no active and valid themes exist, skip loading themes.
		if ( empty( $themes ) ) {
			add_filter( 'gc_using_themes', '__return_false' );
		}
	}

	return $themes;
}

/**
 * Filters a given list of themes, removing any paused themes from it.
 *
 *
 *
 * @param string[] $themes Array of absolute theme directory paths.
 * @return string[] Filtered array of absolute paths to themes, without any paused themes.
 */
function gc_skip_paused_themes( array $themes ) {
	$paused_themes = gc_paused_themes()->get_all();

	if ( empty( $paused_themes ) ) {
		return $themes;
	}

	foreach ( $themes as $index => $theme ) {
		$theme = basename( $theme );

		if ( array_key_exists( $theme, $paused_themes ) ) {
			unset( $themes[ $index ] );

			// Store list of paused themes for displaying an admin notice.
			$GLOBALS['_paused_themes'][ $theme ] = $paused_themes[ $theme ];
		}
	}

	return $themes;
}

/**
 * Is GeChiUI in Recovery Mode.
 *
 * In this mode, plugins or themes that cause WSODs will be paused.
 *
 *
 *
 * @return bool
 */
function gc_is_recovery_mode() {
	return gc_recovery_mode()->is_active();
}

/**
 * Determines whether we are currently on an endpoint that should be protected against WSODs.
 *
 *
 *
 * @global string $pagenow
 *
 * @return bool True if the current endpoint should be protected.
 */
function is_protected_endpoint() {
	// Protect login pages.
	if ( isset( $GLOBALS['pagenow'] ) && 'gc-login.php' === $GLOBALS['pagenow'] ) {
		return true;
	}

	// Protect the admin backend.
	if ( is_admin() && ! gc_doing_ajax() ) {
		return true;
	}

	// Protect Ajax actions that could help resolve a fatal error should be available.
	if ( is_protected_ajax_action() ) {
		return true;
	}

	/**
	 * Filters whether the current request is against a protected endpoint.
	 *
	 * This filter is only fired when an endpoint is requested which is not already protected by
	 * GeChiUI core. As such, it exclusively allows providing further protected endpoints in
	 * addition to the admin backend, login pages and protected Ajax actions.
	 *
	 *
	 * @param bool $is_protected_endpoint Whether the currently requested endpoint is protected.
	 *                                    Default false.
	 */
	return (bool) apply_filters( 'is_protected_endpoint', false );
}

/**
 * Determines whether we are currently handling an Ajax action that should be protected against WSODs.
 *
 *
 *
 * @return bool True if the current Ajax action should be protected.
 */
function is_protected_ajax_action() {
	if ( ! gc_doing_ajax() ) {
		return false;
	}

	if ( ! isset( $_REQUEST['action'] ) ) {
		return false;
	}

	$actions_to_protect = array(
		'edit-theme-plugin-file', // Saving changes in the core code editor.
		'heartbeat',              // Keep the heart beating.
		'install-plugin',         // Installing a new plugin.
		'install-theme',          // Installing a new theme.
		'search-plugins',         // Searching in the list of plugins.
		'search-install-plugins', // Searching for a plugin in the plugin install screen.
		'update-plugin',          // Update an existing plugin.
		'update-theme',           // Update an existing theme.
	);

	/**
	 * Filters the array of protected Ajax actions.
	 *
	 * This filter is only fired when doing Ajax and the Ajax request has an 'action' property.
	 *
	 *
	 * @param string[] $actions_to_protect Array of strings with Ajax actions to protect.
	 */
	$actions_to_protect = (array) apply_filters( 'gc_protected_ajax_actions', $actions_to_protect );

	if ( ! in_array( $_REQUEST['action'], $actions_to_protect, true ) ) {
		return false;
	}

	return true;
}

/**
 * Set internal encoding.
 *
 * In most cases the default internal encoding is latin1, which is
 * of no use, since we want to use the `mb_` functions for `utf-8` strings.
 *
 *
 * @access private
 */
function gc_set_internal_encoding() {
	if ( function_exists( 'mb_internal_encoding' ) ) {
		$charset = get_option( 'blog_charset' );
		// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
		if ( ! $charset || ! @mb_internal_encoding( $charset ) ) {
			mb_internal_encoding( 'UTF-8' );
		}
	}
}

/**
 * Add magic quotes to `$_GET`, `$_POST`, `$_COOKIE`, and `$_SERVER`.
 *
 * Also forces `$_REQUEST` to be `$_GET + $_POST`. If `$_SERVER`,
 * `$_COOKIE`, or `$_ENV` are needed, use those superglobals directly.
 *
 *
 * @access private
 */
function gc_magic_quotes() {
	// Escape with gcdb.
	$_GET    = add_magic_quotes( $_GET );
	$_POST   = add_magic_quotes( $_POST );
	$_COOKIE = add_magic_quotes( $_COOKIE );
	$_SERVER = add_magic_quotes( $_SERVER );

	// Force REQUEST to be GET + POST.
	$_REQUEST = array_merge( $_GET, $_POST );
}

/**
 * Runs just before PHP shuts down execution.
 *
 *
 * @access private
 */
function shutdown_action_hook() {
	/**
	 * Fires just before PHP shuts down execution.
	 *
	 */
	do_action( 'shutdown' );

	gc_cache_close();
}

/**
 * Copy an object.
 *
 *
 * @deprecated 3.2.0
 *
 * @param object $object The object to clone.
 * @return object The cloned object.
 */
function gc_clone( $object ) {
	// Use parens for clone to accommodate PHP 4. See #17880.
	return clone( $object );
}

/**
 * Determines whether the current request is for an administrative interface page.
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return bool True if inside GeChiUI administration interface, false otherwise.
 */
function is_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin();
	} elseif ( defined( 'GC_ADMIN' ) ) {
		return GC_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for a site's administrative interface.
 *
 * e.g. `/gc-admin/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return bool True if inside GeChiUI blog administration pages.
 */
function is_blog_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'site' );
	} elseif ( defined( 'GC_BLOG_ADMIN' ) ) {
		return GC_BLOG_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for the network administrative interface.
 *
 * e.g. `/gc-admin/network/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * Does not check if the site is a Multisite network; use is_multisite()
 * for checking if Multisite is enabled.
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return bool True if inside GeChiUI network administration pages.
 */
function is_network_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'network' );
	} elseif ( defined( 'GC_NETWORK_ADMIN' ) ) {
		return GC_NETWORK_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for a user admin screen.
 *
 * e.g. `/gc-admin/user/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return bool True if inside GeChiUI user administration pages.
 */
function is_user_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'user' );
	} elseif ( defined( 'GC_USER_ADMIN' ) ) {
		return GC_USER_ADMIN;
	}

	return false;
}

/**
 * If Multisite is enabled.
 *
 *
 *
 * @return bool True if Multisite is enabled, false otherwise.
 */
function is_multisite() {
	if ( defined( 'MULTISITE' ) ) {
		return MULTISITE;
	}

	if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) ) {
		return true;
	}

	return false;
}

/**
 * Retrieve the current site ID.
 *
 *
 *
 * @global int $blog_id
 *
 * @return int Site ID.
 */
function get_current_blog_id() {
	global $blog_id;
	return absint( $blog_id );
}

/**
 * Retrieves the current network ID.
 *
 *
 *
 * @return int The ID of the current network.
 */
function get_current_network_id() {
	if ( ! is_multisite() ) {
		return 1;
	}

	$current_network = get_network();

	if ( ! isset( $current_network->id ) ) {
		return get_main_network_id();
	}

	return absint( $current_network->id );
}

/**
 * Attempt an early load of translations.
 *
 * Used for errors encountered during the initial loading process, before
 * the locale has been properly detected and loaded.
 *
 * Designed for unusual load sequences (like setup-config.php) or for when
 * the script will then terminate with an error, otherwise there is a risk
 * that a file can be double-included.
 *
 *
 * @access private
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 */
function gc_load_translations_early() {
	global $gc_locale;

	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$loaded = true;

	if ( function_exists( 'did_action' ) && did_action( 'init' ) ) {
		return;
	}

	// We need $gc_local_package.
	require ABSPATH . GCINC . '/version.php';

	// Translation and localization.
	require_once ABSPATH . GCINC . '/pomo/mo.php';
	require_once ABSPATH . GCINC . '/l10n.php';
	require_once ABSPATH . GCINC . '/class-gc-locale.php';
	require_once ABSPATH . GCINC . '/class-gc-locale-switcher.php';

	// General libraries.
	require_once ABSPATH . GCINC . '/plugin.php';

	$locales   = array();
	$locations = array();

	while ( true ) {
		if ( defined( 'GCLANG' ) ) {
			if ( '' === GCLANG ) {
				break;
			}
			$locales[] = GCLANG;
		}

		if ( isset( $gc_local_package ) ) {
			$locales[] = $gc_local_package;
		}

		if ( ! $locales ) {
			break;
		}

		if ( defined( 'GC_LANG_DIR' ) && @is_dir( GC_LANG_DIR ) ) {
			$locations[] = GC_LANG_DIR;
		}

		if ( defined( 'GC_CONTENT_DIR' ) && @is_dir( GC_CONTENT_DIR . '/languages' ) ) {
			$locations[] = GC_CONTENT_DIR . '/languages';
		}

		if ( @is_dir( ABSPATH . 'gc-content/languages' ) ) {
			$locations[] = ABSPATH . 'gc-content/languages';
		}

		if ( @is_dir( ABSPATH . GCINC . '/languages' ) ) {
			$locations[] = ABSPATH . GCINC . '/languages';
		}

		if ( ! $locations ) {
			break;
		}

		$locations = array_unique( $locations );

		foreach ( $locales as $locale ) {
			foreach ( $locations as $location ) {
				if ( file_exists( $location . '/' . $locale . '.mo' ) ) {
					load_textdomain( 'default', $location . '/' . $locale . '.mo' );
					if ( defined( 'GC_SETUP_CONFIG' ) && file_exists( $location . '/admin-' . $locale . '.mo' ) ) {
						load_textdomain( 'default', $location . '/admin-' . $locale . '.mo' );
					}
					break 2;
				}
			}
		}

		break;
	}

	$gc_locale = new GC_Locale();
}

/**
 * Check or set whether GeChiUI is in "installation" mode.
 *
 * If the `GC_INSTALLING` constant is defined during the bootstrap, `gc_installing()` will default to `true`.
 *
 *
 *
 * @param bool $is_installing Optional. True to set GC into Installing mode, false to turn Installing mode off.
 *                            Omit this parameter if you only want to fetch the current status.
 * @return bool True if GC is installing, otherwise false. When a `$is_installing` is passed, the function will
 *              report whether GC was in installing mode prior to the change to `$is_installing`.
 */
function gc_installing( $is_installing = null ) {
	static $installing = null;

	// Support for the `GC_INSTALLING` constant, defined before GC is loaded.
	if ( is_null( $installing ) ) {
		$installing = defined( 'GC_INSTALLING' ) && GC_INSTALLING;
	}

	if ( ! is_null( $is_installing ) ) {
		$old_installing = $installing;
		$installing     = $is_installing;
		return (bool) $old_installing;
	}

	return (bool) $installing;
}

/**
 * Determines if SSL is used.
 *
 *
 *
 *
 * @return bool True if SSL, otherwise false.
 */
function is_ssl() {
	if ( isset( $_SERVER['HTTPS'] ) ) {
		if ( 'on' === strtolower( $_SERVER['HTTPS'] ) ) {
			return true;
		}

		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

/**
 * Converts a shorthand byte value to an integer byte value.
 *
 *
 *
 *
 * @link https://www.php.net/manual/en/function.ini-get.php
 * @link https://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes
 *
 * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
 * @return int An integer byte value.
 */
function gc_convert_hr_to_bytes( $value ) {
	$value = strtolower( trim( $value ) );
	$bytes = (int) $value;

	if ( false !== strpos( $value, 'g' ) ) {
		$bytes *= GB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'm' ) ) {
		$bytes *= MB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'k' ) ) {
		$bytes *= KB_IN_BYTES;
	}

	// Deal with large (float) values which run into the maximum integer size.
	return min( $bytes, PHP_INT_MAX );
}

/**
 * Determines whether a PHP ini value is changeable at runtime.
 *
 *
 *
 * @link https://www.php.net/manual/en/function.ini-get-all.php
 *
 * @param string $setting The name of the ini setting to check.
 * @return bool True if the value is changeable at runtime. False otherwise.
 */
function gc_is_ini_value_changeable( $setting ) {
	static $ini_all;

	if ( ! isset( $ini_all ) ) {
		$ini_all = false;
		// Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
		if ( function_exists( 'ini_get_all' ) ) {
			$ini_all = ini_get_all();
		}
	}

	// Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
	if ( isset( $ini_all[ $setting ]['access'] ) && ( INI_ALL === ( $ini_all[ $setting ]['access'] & 7 ) || INI_USER === ( $ini_all[ $setting ]['access'] & 7 ) ) ) {
		return true;
	}

	// If we were unable to retrieve the details, fail gracefully to assume it's changeable.
	if ( ! is_array( $ini_all ) ) {
		return true;
	}

	return false;
}

/**
 * Determines whether the current request is a GeChiUI Ajax request.
 *
 *
 *
 * @return bool True if it's a GeChiUI Ajax request, false otherwise.
 */
function gc_doing_ajax() {
	/**
	 * Filters whether the current request is a GeChiUI Ajax request.
	 *
	 *
	 * @param bool $gc_doing_ajax Whether the current request is a GeChiUI Ajax request.
	 */
	return apply_filters( 'gc_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}

/**
 * Determines whether the current request should use themes.
 *
 *
 *
 * @return bool True if themes should be used, false otherwise.
 */
function gc_using_themes() {
	/**
	 * Filters whether the current request should use themes.
	 *
	 *
	 * @param bool $gc_using_themes Whether the current request should use themes.
	 */
	return apply_filters( 'gc_using_themes', defined( 'GC_USE_THEMES' ) && GC_USE_THEMES );
}

/**
 * Determines whether the current request is a GeChiUI cron request.
 *
 *
 *
 * @return bool True if it's a GeChiUI cron request, false otherwise.
 */
function gc_doing_cron() {
	/**
	 * Filters whether the current request is a GeChiUI cron request.
	 *
	 *
	 * @param bool $gc_doing_cron Whether the current request is a GeChiUI cron request.
	 */
	return apply_filters( 'gc_doing_cron', defined( 'DOING_CRON' ) && DOING_CRON );
}

/**
 * Checks whether the given variable is a GeChiUI Error.
 *
 * Returns whether `$thing` is an instance of the `GC_Error` class.
 *
 *
 *
 * @param mixed $thing The variable to check.
 * @return bool Whether the variable is an instance of GC_Error.
 */
function is_gc_error( $thing ) {
	$is_gc_error = ( $thing instanceof GC_Error );

	if ( $is_gc_error ) {
		/**
		 * Fires when `is_gc_error()` is called and its parameter is an instance of `GC_Error`.
		 *
		 *
		 * @param GC_Error $thing The error object passed to `is_gc_error()`.
		 */
		do_action( 'is_gc_error_instance', $thing );
	}

	return $is_gc_error;
}

/**
 * Determines whether file modifications are allowed.
 *
 *
 *
 * @param string $context The usage context.
 * @return bool True if file modification is allowed, false otherwise.
 */
function gc_is_file_mod_allowed( $context ) {
	/**
	 * Filters whether file modifications are allowed.
	 *
	 *
	 * @param bool   $file_mod_allowed Whether file modifications are allowed.
	 * @param string $context          The usage context.
	 */
	return apply_filters( 'file_mod_allowed', ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS, $context );
}

/**
 * Start scraping edited file errors.
 *
 *
 */
function gc_start_scraping_edited_file_errors() {
	if ( ! isset( $_REQUEST['gc_scrape_key'] ) || ! isset( $_REQUEST['gc_scrape_nonce'] ) ) {
		return;
	}
	$key   = substr( sanitize_key( gc_unslash( $_REQUEST['gc_scrape_key'] ) ), 0, 32 );
	$nonce = gc_unslash( $_REQUEST['gc_scrape_nonce'] );

	if ( get_transient( 'scrape_key_' . $key ) !== $nonce ) {
		echo "###### gc_scraping_result_start:$key ######";
		echo gc_json_encode(
			array(
				'code'    => 'scrape_nonce_failure',
				'message' => __( 'Scrape key 检查失败，请重试。' ),
			)
		);
		echo "###### gc_scraping_result_end:$key ######";
		die();
	}
	if ( ! defined( 'GC_SANDBOX_SCRAPING' ) ) {
		define( 'GC_SANDBOX_SCRAPING', true );
	}
	register_shutdown_function( 'gc_finalize_scraping_edited_file_errors', $key );
}

/**
 * Finalize scraping for edited file errors.
 *
 *
 *
 * @param string $scrape_key Scrape key.
 */
function gc_finalize_scraping_edited_file_errors( $scrape_key ) {
	$error = error_get_last();
	echo "\n###### gc_scraping_result_start:$scrape_key ######\n";
	if ( ! empty( $error ) && in_array( $error['type'], array( E_CORE_ERROR, E_COMPILE_ERROR, E_ERROR, E_PARSE, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
		$error = str_replace( ABSPATH, '', $error );
		echo gc_json_encode( $error );
	} else {
		echo gc_json_encode( true );
	}
	echo "\n###### gc_scraping_result_end:$scrape_key ######\n";
}

/**
 * Checks whether current request is a JSON request, or is expecting a JSON response.
 *
 *
 *
 * @return bool True if `Accepts` or `Content-Type` headers contain `application/json`.
 *              False otherwise.
 */
function gc_is_json_request() {

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) && gc_is_json_media_type( $_SERVER['HTTP_ACCEPT'] ) ) {
		return true;
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && gc_is_json_media_type( $_SERVER['CONTENT_TYPE'] ) ) {
		return true;
	}

	return false;

}

/**
 * Checks whether current request is a JSONP request, or is expecting a JSONP response.
 *
 *
 *
 * @return bool True if JSONP request, false otherwise.
 */
function gc_is_jsonp_request() {
	if ( ! isset( $_GET['_jsonp'] ) ) {
		return false;
	}

	if ( ! function_exists( 'gc_check_jsonp_callback' ) ) {
		require_once ABSPATH . GCINC . '/functions.php';
	}

	$jsonp_callback = $_GET['_jsonp'];
	if ( ! gc_check_jsonp_callback( $jsonp_callback ) ) {
		return false;
	}

	/** This filter is documented in gc-includes/rest-api/class-gc-rest-server.php */
	$jsonp_enabled = apply_filters( 'rest_jsonp_enabled', true );

	return $jsonp_enabled;

}

/**
 * Checks whether a string is a valid JSON Media Type.
 *
 *
 *
 * @param string $media_type A Media Type string to check.
 * @return bool True if string is a valid JSON Media Type.
 */
function gc_is_json_media_type( $media_type ) {
	static $cache = array();

	if ( ! isset( $cache[ $media_type ] ) ) {
		$cache[ $media_type ] = (bool) preg_match( '/(^|\s|,)application\/([\w!#\$&-\^\.\+]+\+)?json(\+oembed)?($|\s|;|,)/i', $media_type );
	}

	return $cache[ $media_type ];
}

/**
 * Checks whether current request is an XML request, or is expecting an XML response.
 *
 *
 *
 * @return bool True if `Accepts` or `Content-Type` headers contain `text/xml`
 *              or one of the related MIME types. False otherwise.
 */
function gc_is_xml_request() {
	$accepted = array(
		'text/xml',
		'application/rss+xml',
		'application/atom+xml',
		'application/rdf+xml',
		'text/xml+oembed',
		'application/xml+oembed',
	);

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
		foreach ( $accepted as $type ) {
			if ( false !== strpos( $_SERVER['HTTP_ACCEPT'], $type ) ) {
				return true;
			}
		}
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && in_array( $_SERVER['CONTENT_TYPE'], $accepted, true ) ) {
		return true;
	}

	return false;
}

/**
 * Checks if this site is protected by HTTP Basic Auth.
 *
 * At the moment, this merely checks for the present of Basic Auth credentials. Therefore, calling
 * this function with a context different from the current context may give inaccurate results.
 * In a future release, this evaluation may be made more robust.
 *
 * Currently, this is only used by AppKeys to prevent a conflict since it also utilizes
 * Basic Auth.
 *
 *
 *
 * @global string $pagenow The current page.
 *
 * @param string $context The context to check for protection. Accepts 'login', 'admin', and 'front'.
 *                        Defaults to the current context.
 * @return bool Whether the site is protected by Basic Auth.
 */
function gc_is_site_protected_by_basic_auth( $context = '' ) {
	global $pagenow;

	if ( ! $context ) {
		if ( 'gc-login.php' === $pagenow ) {
			$context = 'login';
		} elseif ( is_admin() ) {
			$context = 'admin';
		} else {
			$context = 'front';
		}
	}

	$is_protected = ! empty( $_SERVER['PHP_AUTH_USER'] ) || ! empty( $_SERVER['PHP_AUTH_PW'] );

	/**
	 * Filters whether a site is protected by HTTP Basic Auth.
	 *
	 *
	 * @param bool $is_protected Whether the site is protected by Basic Auth.
	 * @param string $context    The context to check for protection. One of 'login', 'admin', or 'front'.
	 */
	return apply_filters( 'gc_is_site_protected_by_basic_auth', $is_protected, $context );
}
