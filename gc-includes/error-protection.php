<?php
/**
 * Error Protection API: Functions
 *
 * @package GeChiUI
 * @since 5.2.0
 */

/**
 * Get the instance for storing paused plugins.
 *
 * @return GC_Paused_Extensions_Storage
 */
function gc_paused_plugins() {
	static $storage = null;

	if ( null === $storage ) {
		$storage = new GC_Paused_Extensions_Storage( 'plugin' );
	}

	return $storage;
}

/**
 * Get the instance for storing paused extensions.
 *
 * @return GC_Paused_Extensions_Storage
 */
function gc_paused_themes() {
	static $storage = null;

	if ( null === $storage ) {
		$storage = new GC_Paused_Extensions_Storage( 'theme' );
	}

	return $storage;
}

/**
 * Get a human readable description of an extension's error.
 *
 * @since 5.2.0
 *
 * @param array $error Error details from `error_get_last()`.
 * @return string Formatted error description.
 */
function gc_get_extension_error_description( $error ) {
	$constants   = get_defined_constants( true );
	$constants   = isset( $constants['Core'] ) ? $constants['Core'] : $constants['internal'];
	$core_errors = array();

	foreach ( $constants as $constant => $value ) {
		if ( str_starts_with( $constant, 'E_' ) ) {
			$core_errors[ $value ] = $constant;
		}
	}

	if ( isset( $core_errors[ $error['type'] ] ) ) {
		$error['type'] = $core_errors[ $error['type'] ];
	}

	/* translators: 1: Error type, 2: Error line number, 3: Error file name, 4: Error message. */
	$error_message = __( '错误类型%1$s发生在文件%3$s的%2$s行。错误信息：%4$s' );

	return sprintf(
		$error_message,
		"<code>{$error['type']}</code>",
		"<code>{$error['line']}</code>",
		"<code>{$error['file']}</code>",
		"<code>{$error['message']}</code>"
	);
}

/**
 * Registers the shutdown handler for fatal errors.
 *
 * The handler will only be registered if {@see gc_is_fatal_error_handler_enabled()} returns true.
 *
 * @since 5.2.0
 */
function gc_register_fatal_error_handler() {
	if ( ! gc_is_fatal_error_handler_enabled() ) {
		return;
	}

	$handler = null;
	if ( defined( 'GC_CONTENT_DIR' ) && is_readable( GC_CONTENT_DIR . '/fatal-error-handler.php' ) ) {
		$handler = include GC_CONTENT_DIR . '/fatal-error-handler.php';
	}

	if ( ! is_object( $handler ) || ! is_callable( array( $handler, 'handle' ) ) ) {
		$handler = new GC_Fatal_Error_Handler();
	}

	register_shutdown_function( array( $handler, 'handle' ) );
}

/**
 * Checks whether the fatal error handler is enabled.
 *
 * A constant `GC_DISABLE_FATAL_ERROR_HANDLER` can be set in `gc-config.php` to disable it, or alternatively the
 * {@see 'gc_fatal_error_handler_enabled'} filter can be used to modify the return value.
 *
 * @since 5.2.0
 *
 * @return bool True if the fatal error handler is enabled, false otherwise.
 */
function gc_is_fatal_error_handler_enabled() {
	$enabled = ! defined( 'GC_DISABLE_FATAL_ERROR_HANDLER' ) || ! GC_DISABLE_FATAL_ERROR_HANDLER;

	/**
	 * Filters whether the fatal error handler is enabled.
	 *
	 * **Important:** This filter runs before it can be used by plugins. It cannot
	 * be used by plugins, mu-plugins, or themes. To use this filter you must define
	 * a `$gc_filter` global before GeChiUI loads, usually in `gc-config.php`.
	 *
	 * Example:
	 *
	 *     $GLOBALS['gc_filter'] = array(
	 *         'gc_fatal_error_handler_enabled' => array(
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
	 * Alternatively you can use the `GC_DISABLE_FATAL_ERROR_HANDLER` constant.
	 *
	 * @since 5.2.0
	 *
	 * @param bool $enabled True if the fatal error handler is enabled, false otherwise.
	 */
	return apply_filters( 'gc_fatal_error_handler_enabled', $enabled );
}

/**
 * Access the GeChiUI Recovery Mode instance.
 *
 * @since 5.2.0
 *
 * @return GC_Recovery_Mode
 */
function gc_recovery_mode() {
	static $gc_recovery_mode;

	if ( ! $gc_recovery_mode ) {
		$gc_recovery_mode = new GC_Recovery_Mode();
	}

	return $gc_recovery_mode;
}
