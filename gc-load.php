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
	 * @see http://php.net/manual/en/errorfunc.constants.php List of known error levels.
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
	require_once ABSPATH . GCINC . '/load.php';

	// Standardize $_SERVER variables across setups.
	gc_fix_server_vars();

	require_once ABSPATH . GCINC . '/functions.php';

	$path = gc_guess_url() . '/gc-admin/setup-config.php';

	/*
	 * We're going to redirect to setup-config.php. While this shouldn't result
	 * in an infinite loop, that's a silly thing to assume, don't you think? If
	 * we're traveling in circles, our last-ditch effort is "Need more help?"
	 */
	if ( false === strpos( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
		header( 'Location: ' . $path );
		exit;
	}

	define( 'GC_CONTENT_DIR', ABSPATH . 'gc-content' );
	require_once ABSPATH . GCINC . '/version.php';

	gc_check_php_mysql_versions();
	gc_load_translations_early();

	// Die with an error message.
	$die = '<p>' . sprintf(
		/* translators: %s: gc-config.php */
		__( "%s文件似乎不存在，我需要这文件来开始运行。" ),
		'<code>gc-config.php</code>'
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: %s: Documentation URL. */
		__( "需要更多帮助？<a href='%s'>看这里</a>。" ),
		__( 'https://www.gechiui.com/support/editing-gc-config-php/' )
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: %s: gc-config.php */
		__( "您可以通过网页界面创建%s文件，但这并不适合所有的服务器设置。最安全的方法是手动创建该文件。" ),
		'<code>gc-config.php</code>'
	) . '</p>';
	$die .= '<p><a href="' . $path . '" class="button button-large">' . __( '创建配置文件' ) . '</a></p>';

	gc_die( $die, __( 'GeChiUI &rsaquo; 错误' ) );
}
