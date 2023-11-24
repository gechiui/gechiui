<?php
/**
 * Dependencies API: Scripts functions
 *
 * @package GeChiUI
 * @subpackage Dependencies
 */

/**
 * Initializes $gc_scripts if it has not been set.
 *
 * @global GC_Scripts $gc_scripts
 *
 * @return GC_Scripts GC_Scripts instance.
 */
function gc_scripts() {
	global $gc_scripts;

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		$gc_scripts = new GC_Scripts();
	}

	return $gc_scripts;
}

/**
 * Helper function to output a _doing_it_wrong message when applicable.
 *
 * @ignore
 * @since 5.5.0 Added the `$handle` parameter.
 *
 * @param string $function_name Function name.
 * @param string $handle        Optional. Name of the script or stylesheet that was
 *                              registered or enqueued too early. Default empty.
 */
function _gc_scripts_maybe_doing_it_wrong( $function_name, $handle = '' ) {
	if ( did_action( 'init' ) || did_action( 'gc_enqueue_scripts' )
		|| did_action( 'admin_enqueue_scripts' ) || did_action( 'login_enqueue_scripts' )
	) {
		return;
	}

	$message = sprintf(
		/* translators: 1: gc_enqueue_scripts, 2: admin_enqueue_scripts, 3: login_enqueue_scripts */
		__( '脚本和样式应在%1$s、%2$s和%3$s钩子函数之后再加入加载队列（enqueue）或注册（register）。' ),
		'<code>gc_enqueue_scripts</code>',
		'<code>admin_enqueue_scripts</code>',
		'<code>login_enqueue_scripts</code>'
	);

	if ( $handle ) {
		$message .= ' ' . sprintf(
			/* translators: %s: Name of the script or stylesheet. */
			__( '此通知由%s触发。' ),
			'<code>' . $handle . '</code>'
		);
	}

	_doing_it_wrong(
		$function_name,
		$message,
		'3.3.0'
	);
}

/**
 * Prints scripts in document head that are in the $handles queue.
 *
 * Called by admin-header.php and {@see 'gc_head'} hook. Since it is called by gc_head on every page load,
 * the function does not instantiate the GC_Scripts object unless script names are explicitly passed.
 * Makes use of already-instantiated $gc_scripts global if present. Use provided {@see 'gc_print_scripts'}
 * hook to register/enqueue new scripts.
 *
 * @see GC_Scripts::do_item()
 * @global GC_Scripts $gc_scripts The GC_Scripts object for printing scripts.
 *
 * @param string|string[]|false $handles Optional. Scripts to be printed. Default 'false'.
 * @return string[] On success, an array of handles of processed GC_Dependencies items; otherwise, an empty array.
 */
function gc_print_scripts( $handles = false ) {
	global $gc_scripts;

	/**
	 * Fires before scripts in the $handles queue are printed.
	 *
	 */
	do_action( 'gc_print_scripts' );

	if ( '' === $handles ) { // For 'gc_head'.
		$handles = false;
	}

	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return gc_scripts()->do_items( $handles );
}

/**
 * Adds extra code to a registered script.
 *
 * Code will only be added if the script is already in the queue.
 * Accepts a string $data containing the Code. If two or more code blocks
 * are added to the same script $handle, they will be printed in the order
 * they were added, i.e. the latter added code can redeclare the previous.
 *
 * @see GC_Scripts::add_inline_script()
 *
 * @param string $handle   Name of the script to add the inline script to.
 * @param string $data     String containing the JavaScript to be added.
 * @param string $position Optional. Whether to add the inline script before the handle
 *                         or after. Default 'after'.
 * @return bool True on success, false on failure.
 */
function gc_add_inline_script( $handle, $data, $position = 'after' ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	if ( false !== stripos( $data, '</script>' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: <script>, 2: gc_add_inline_script() */
				__( '不要传递%1$s标签给%2$s。' ),
				'<code>&lt;script&gt;</code>',
				'<code>gc_add_inline_script()</code>'
			),
			'4.5.0'
		);
		$data = trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $data ) );
	}

	return gc_scripts()->add_inline_script( $handle, $data, $position );
}

/**
 * Registers a new script.
 *
 * Registers a script to be enqueued later using the gc_enqueue_script() function.
 *
 * @see GC_Dependencies::add()
 * @see GC_Dependencies::add_data()
 *
 * @since 4.3.0 A return value was added.
 * @since 6.3.0 The $in_footer parameter of type boolean was overloaded to be an $args parameter of type array.
 *
 * @param string           $handle    Name of the script. Should be unique.
 * @param string|false     $src       Full URL of the script, or path of the script relative to the GeChiUI root directory.
 *                                    If source is set to false, script is an alias of other scripts it depends on.
 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
 *                                    as a query string for cache busting purposes. If version is set to false, a version
 *                                    number is automatically added equal to current installed GeChiUI version.
 *                                    If set to null, no version is added.
 * @param array|bool       $args     {
 *      Optional. An array of additional script loading strategies. Default empty array.
 *      Otherwise, it may be a boolean in which case it determines whether the script is printed in the footer. Default false.
 *
 *      @type string    $strategy     Optional. If provided, may be either 'defer' or 'async'.
 *      @type bool      $in_footer    Optional. Whether to print the script in the footer. Default 'false'.
 * }
 * @return bool Whether the script has been registered. True on success, false on failure.
 */
function gc_register_script( $handle, $src, $deps = array(), $ver = false, $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = array(
			'in_footer' => (bool) $args,
		);
	}
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	$gc_scripts = gc_scripts();

	$registered = $gc_scripts->add( $handle, $src, $deps, $ver );
	if ( ! empty( $args['in_footer'] ) ) {
		$gc_scripts->add_data( $handle, 'group', 1 );
	}
	if ( ! empty( $args['strategy'] ) ) {
		$gc_scripts->add_data( $handle, 'strategy', $args['strategy'] );
	}
	return $registered;
}

/**
 * Localizes a script.
 *
 * Works only if the script has already been registered.
 *
 * Accepts an associative array $l10n and creates a JavaScript object:
 *
 *     "$object_name" = {
 *         key: value,
 *         key: value,
 *         ...
 *     }
 *
 * @see GC_Scripts::localize()
 * @link https://core.trac.gechiui.com/ticket/11520
 * @global GC_Scripts $gc_scripts The GC_Scripts object for printing scripts.
 *
 * @todo Documentation cleanup
 *
 * @param string $handle      Script handle the data will be attached to.
 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
 *                            Example: '/[a-zA-Z0-9_]+/'.
 * @param array  $l10n        The data itself. The data can be either a single or multi-dimensional array.
 * @return bool True if the script was successfully localized, false otherwise.
 */
function gc_localize_script( $handle, $object_name, $l10n ) {
	global $gc_scripts;

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );
		return false;
	}

	return $gc_scripts->localize( $handle, $object_name, $l10n );
}

/**
 * Sets translated strings for a script.
 *
 * Works only if the script has already been registered.
 *
 * @see GC_Scripts::set_translations()
 * @global GC_Scripts $gc_scripts The GC_Scripts object for printing scripts.
 *
 * @since 5.0.0
 * @since 5.1.0 The `$domain` parameter was made optional.
 *
 * @param string $handle Script handle the textdomain will be attached to.
 * @param string $domain Optional. Text domain. Default 'default'.
 * @param string $path   Optional. The full file path to the directory containing translation files.
 * @return bool True if the text domain was successfully localized, false otherwise.
 */
function gc_set_script_translations( $handle, $domain = 'default', $path = '' ) {
	global $gc_scripts;

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );
		return false;
	}

	return $gc_scripts->set_translations( $handle, $domain, $path );
}

/**
 * Removes a registered script.
 *
 * Note: there are intentional safeguards in place to prevent critical admin scripts,
 * such as jQuery core, from being unregistered.
 *
 * @see GC_Dependencies::remove()
 *
 * @global string $pagenow The filename of the current screen.
 *
 * @param string $handle Name of the script to be removed.
 */
function gc_deregister_script( $handle ) {
	global $pagenow;

	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	/**
	 * Do not allow accidental or negligent de-registering of critical scripts in the admin.
	 * Show minimal remorse if the correct hook is used.
	 */
	$current_filter = current_filter();
	if ( ( is_admin() && 'admin_enqueue_scripts' !== $current_filter ) ||
		( 'gc-login.php' === $pagenow && 'login_enqueue_scripts' !== $current_filter )
	) {
		$not_allowed = array(
			'jquery',
			'jquery-core',
			'jquery-migrate',
			'jquery-ui-core',
			'jquery-ui-accordion',
			'jquery-ui-autocomplete',
			'jquery-ui-button',
			'jquery-ui-datepicker',
			'jquery-ui-dialog',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-ui-menu',
			'jquery-ui-mouse',
			'jquery-ui-position',
			'jquery-ui-progressbar',
			'jquery-ui-resizable',
			'jquery-ui-selectable',
			'jquery-ui-slider',
			'jquery-ui-sortable',
			'jquery-ui-spinner',
			'jquery-ui-tabs',
			'jquery-ui-tooltip',
			'jquery-ui-widget',
			'underscore',
			'backbone',
		);

		if ( in_array( $handle, $not_allowed, true ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: 1: Script name, 2: gc_enqueue_scripts */
					__( '不在管理区域中反注册%1$s脚本。要将目标定为前端主题，请使用%2$s钩子函数。' ),
					"<code>$handle</code>",
					'<code>gc_enqueue_scripts</code>'
				),
				'3.6.0'
			);
			return;
		}
	}

	gc_scripts()->remove( $handle );
}

/**
 * Enqueues a script.
 *
 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
 *
 * @see GC_Dependencies::add()
 * @see GC_Dependencies::add_data()
 * @see GC_Dependencies::enqueue()
 *
 * @since 6.3.0 The $in_footer parameter of type boolean was overloaded to be an $args parameter of type array.
 *
 * @param string           $handle    Name of the script. Should be unique.
 * @param string           $src       Full URL of the script, or path of the script relative to the GeChiUI root directory.
 *                                    Default empty.
 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
 *                                    as a query string for cache busting purposes. If version is set to false, a version
 *                                    number is automatically added equal to current installed GeChiUI version.
 *                                    If set to null, no version is added.
 * @param array|bool       $args     {
 *      Optional. An array of additional script loading strategies. Default empty array.
 *      Otherwise, it may be a boolean in which case it determines whether the script is printed in the footer. Default false.
 *
 *      @type string    $strategy     Optional. If provided, may be either 'defer' or 'async'.
 *      @type bool      $in_footer    Optional. Whether to print the script in the footer. Default 'false'.
 * }
 */
function gc_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = array() ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	$gc_scripts = gc_scripts();

	if ( $src || ! empty( $args ) ) {
		$_handle = explode( '?', $handle );
		if ( ! is_array( $args ) ) {
			$args = array(
				'in_footer' => (bool) $args,
			);
		}

		if ( $src ) {
			$gc_scripts->add( $_handle[0], $src, $deps, $ver );
		}
		if ( ! empty( $args['in_footer'] ) ) {
			$gc_scripts->add_data( $_handle[0], 'group', 1 );
		}
		if ( ! empty( $args['strategy'] ) ) {
			$gc_scripts->add_data( $_handle[0], 'strategy', $args['strategy'] );
		}
	}

	$gc_scripts->enqueue( $handle );
}

/**
 * Removes a previously enqueued script.
 *
 * @see GC_Dependencies::dequeue()
 *
 * @param string $handle Name of the script to be removed.
 */
function gc_dequeue_script( $handle ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	gc_scripts()->dequeue( $handle );
}

/**
 * Determines whether a script has been added to the queue.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 * 'enqueued' added as an alias of the 'queue' list.
 *
 * @param string $handle Name of the script.
 * @param string $status Optional. Status of the script to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether the script is queued.
 */
function gc_script_is( $handle, $status = 'enqueued' ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	return (bool) gc_scripts()->query( $handle, $status );
}

/**
 * Adds metadata to a script.
 *
 * Works only if the script has already been registered.
 *
 * Possible values for $key and $value:
 * 'conditional' string Comments for IE 6, lte IE 7, etc.
 *
 * @see GC_Dependencies::add_data()
 *
 * @param string $handle Name of the script.
 * @param string $key    Name of data point for which we're storing a value.
 * @param mixed  $value  String containing the data to be added.
 * @return bool True on success, false on failure.
 */
function gc_script_add_data( $handle, $key, $value ) {
	return gc_scripts()->add_data( $handle, $key, $value );
}
