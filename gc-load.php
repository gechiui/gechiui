<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the gc-config.php file. The gc-config.php
 * file will then load the gc-settings.php file, which
 * will then set up the GeChiUI environment.
 *
 * If the gc-config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * gc-config.php file.
 *
 * Will also search for gc-config.php in GeChiUI' parent
 * directory to allow the GeChiUI directory to remain
 * untouched.
 *
 * @package GeChiUI
 */

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/*
 * The error_reporting() function can be disabled in php.ini. On systems where that is the case,
 * it's best to add a dummy function to the gc-config.php file, but as this call to the function
 * is run prior to gc-config.php loading, it is wrapped in a function_exists() check.
 */
if ( function_exists( 'error_reporting' ) ) {
	/*
	 * Initialize error reporting to a known set of levels.
	 *
	 * This will be adapted in gc_debug_mode() located in gc-includes/load.php based on GC_DEBUG.
	 * @see https://www.php.net/manual/en/errorfunc.constants.php List of known error levels.
	 */
	error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
}

/*
 * If gc-config.php exists in the GeChiUI root, or if it exists in the root and gc-settings.php
 * doesn't, load gc-config.php. The secondary check for gc-settings.php has the added benefit
 * of avoiding cases where the current directory is a nested installation, e.g. / is GeChiUI(a)
 * and /blog/ is GeChiUI(b).
 *
 * If neither set of conditions is true, initiate loading the setup process.
 */
if ( file_exists( ABSPATH . 'gc-config.php' ) ) {

	/** The config file resides in ABSPATH */
	require_once ABSPATH . 'gc-config.php';

} elseif ( @file_exists( dirname( ABSPATH ) . '/gc-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/gc-settings.php' ) ) {

	/** The config file resides one level above ABSPATH but is not part of another installation */
	require_once dirname( ABSPATH ) . '/gc-config.php';

} else {

	// A config file doesn't exist.

	define( 'GCINC', 'gc-includes' );
	require_once ABSPATH . GCINC . '/version.php';
	require_once ABSPATH . GCINC . '/compat.php';
	require_once ABSPATH . GCINC . '/load.php';

	// Check for the required PHP version and for the MySQL extension or a database drop-in.
	gc_check_php_mysql_versions();

	// Standardize $_SERVER variables across setups.
	gc_fix_server_vars();

	define( 'GC_CONTENT_DIR', ABSPATH . 'gc-content' );
	require_once ABSPATH . GCINC . '/functions.php';

	$path = gc_guess_url() . '/gc-admin/setup-config.php';

	// Redirect to setup-config.php.
	if ( ! str_contains( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
		header( 'Location: ' . $path );
		exit;
	}

	gc_load_translations_early();

	// Die with an error message.
	$die = '<p>' . sprintf(
		/* translators: %s: gc-config.php */
		__( "似乎没有 %s 文件。 在继续安装之前需要这个文件。" ),
		'<code>gc-config.php</code>'
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: 1: Documentation URL, 2: gc-config.php */
		__( '需要帮助？<a href="%1$s">阅读 %2$s 支持文章</a>。' ),
		__( 'https://www.gechiui.com/support/editing-gc-config-php/' ),
		'<code>gc-config.php</code>'
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: %s: gc-config.php */
		__( "您可以通过网页界面创建%s文件，但这并不适合所有的服务器设置。最安全的方法是手动创建该文件。" ),
		'<code>gc-config.php</code>'
	) . '</p>';
	$die .= '<p><a href="' . $path . '" class="btn btn-primary">' . __( '创建配置文件' ) . '</a></p>';

	gc_die( $die, __( 'GeChiUI &rsaquo; 错误' ) );
}
