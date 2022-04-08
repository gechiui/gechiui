<?php
/**
 * Option API
 *
 * @package GeChiUI
 * @subpackage Option
 */

/**
 * Retrieves an option value based on an option name.
 *
 * If the option does not exist, and a default value is not provided,
 * boolean false is returned. This could be used to check whether you need
 * to initialize an option during installation of a plugin, however that
 * can be done better by using add_option() which will not overwrite
 * existing options.
 *
 * Not initializing an option and using boolean `false` as a return value
 * is a bad practice as it triggers an additional database query.
 *
 * The type of the returned value can be different from the type that was passed
 * when saving or updating the option. If the option value was serialized,
 * then it will be unserialized when it is returned. In this case the type will
 * be the same. For example, storing a non-scalar value like an array will
 * return the same array.
 *
 * In most cases non-string scalar and null values will be converted and returned
 * as string equivalents.
 *
 * Exceptions:
 * 1. When the option has not been saved in the database, the `$default` value
 *    is returned if provided. If not, boolean `false` is returned.
 * 2. When one of the Options API filters is used: {@see 'pre_option_{$option}'},
 *    {@see 'default_option_{$option}'}, or {@see 'option_{$option}'}, the returned
 *    value may not match the expected type.
 * 3. When the option has just been saved in the database, and get_option()
 *    is used right after, non-string scalar and null values are not converted to
 *    string equivalents and the original type is returned.
 *
 * Examples:
 *
 * When adding options like this: `add_option( 'my_option_name', 'value' );`
 * and then retrieving them with `get_option( 'my_option_name' );`, the returned
 * values will be:
 *
 * `false` returns `string(0) ""`
 * `true`  returns `string(1) "1"`
 * `0`     returns `string(1) "0"`
 * `1`     returns `string(1) "1"`
 * `'0'`   returns `string(1) "0"`
 * `'1'`   returns `string(1) "1"`
 * `null`  returns `string(0) ""`
 *
 * When adding options with non-scalar values like
 * `add_option( 'my_array', array( false, 'str', null ) );`, the returned value
 * will be identical to the original as it is serialized before saving
 * it in the database:
 *
 *    array(3) {
 *        [0] => bool(false)
 *        [1] => string(3) "str"
 *        [2] => NULL
 *    }
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $option  Name of the option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @return mixed Value of the option. A value of any type may be returned, including
 *               scalar (string, boolean, float, integer), null, array, object.
 *               Scalar and null values will be returned as strings as long as they originate
 *               from a database stored option value. If there is no option in the database,
 *               boolean `false` is returned.
 */
function get_option( $option, $default = false ) {
	global $gcdb;

	if ( is_scalar( $option ) ) {
		$option = trim( $option );
	}

	if ( empty( $option ) ) {
		return false;
	}

	/*
	 * Until a proper _deprecated_option() function can be introduced,
	 * redirect requests to deprecated keys to the new, correct ones.
	 */
	$deprecated_keys = array(
		'blacklist_keys'    => 'disallowed_keys',
		'comment_whitelist' => 'comment_previously_approved',
	);

	if ( ! gc_installing() && isset( $deprecated_keys[ $option ] ) ) {
		_deprecated_argument(
			__FUNCTION__,
			'5.5.0',
			sprintf(
				/* translators: 1: Deprecated option key, 2: New option key. */
				__( '“%1$s”选项键已被重命名为“%2$s”。' ),
				$option,
				$deprecated_keys[ $option ]
			)
		);
		return get_option( $deprecated_keys[ $option ], $default );
	}

	/**
	 * Filters the value of an existing option before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * Returning a truthy value from the filter will effectively short-circuit retrieval
	 * and return the passed value instead.
	 *
	 *
	 * @param mixed  $pre_option The value to return instead of the option value. This differs
	 *                           from `$default`, which is used as the fallback value in the event
	 *                           the option doesn't exist elsewhere in get_option().
	 *                           Default false (to skip past the short-circuit).
	 * @param string $option     Option name.
	 * @param mixed  $default    The fallback value to return if the option does not exist.
	 *                           Default false.
	 */
	$pre = apply_filters( "pre_option_{$option}", false, $option, $default );

	if ( false !== $pre ) {
		return $pre;
	}

	if ( defined( 'GC_SETUP_CONFIG' ) ) {
		return false;
	}

	// Distinguish between `false` as a default, and not passing one.
	$passed_default = func_num_args() > 1;

	if ( ! gc_installing() ) {
		// Prevent non-existent options from triggering multiple queries.
		$notoptions = gc_cache_get( 'notoptions', 'options' );

		if ( isset( $notoptions[ $option ] ) ) {
			/**
			 * Filters the default value for an option.
			 *
			 * The dynamic portion of the hook name, `$option`, refers to the option name.
			 *
		
		
		
			 *
			 * @param mixed  $default The default value to return if the option does not exist
			 *                        in the database.
			 * @param string $option  Option name.
			 * @param bool   $passed_default Was `get_option()` passed a default value?
			 */
			return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
		}

		$alloptions = gc_load_alloptions();

		if ( isset( $alloptions[ $option ] ) ) {
			$value = $alloptions[ $option ];
		} else {
			$value = gc_cache_get( $option, 'options' );

			if ( false === $value ) {
				$row = $gcdb->get_row( $gcdb->prepare( "SELECT option_value FROM $gcdb->options WHERE option_name = %s LIMIT 1", $option ) );

				// Has to be get_row() instead of get_var() because of funkiness with 0, false, null values.
				if ( is_object( $row ) ) {
					$value = $row->option_value;
					gc_cache_add( $option, $value, 'options' );
				} else { // Option does not exist, so we must cache its non-existence.
					if ( ! is_array( $notoptions ) ) {
						$notoptions = array();
					}

					$notoptions[ $option ] = true;
					gc_cache_set( 'notoptions', $notoptions, 'options' );

					/** This filter is documented in gc-includes/option.php */
					return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
				}
			}
		}
	} else {
		$suppress = $gcdb->suppress_errors();
		$row      = $gcdb->get_row( $gcdb->prepare( "SELECT option_value FROM $gcdb->options WHERE option_name = %s LIMIT 1", $option ) );
		$gcdb->suppress_errors( $suppress );

		if ( is_object( $row ) ) {
			$value = $row->option_value;
		} else {
			/** This filter is documented in gc-includes/option.php */
			return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
		}
	}

	// If home is not set, use siteurl.
	if ( 'home' === $option && '' === $value ) {
		return get_option( 'siteurl' );
	}

	if ( in_array( $option, array( 'siteurl', 'home', 'category_base', 'tag_base' ), true ) ) {
		$value = untrailingslashit( $value );
	}

	/**
	 * Filters the value of an existing option.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $value  Value of the option. If stored serialized, it will be
	 *                       unserialized prior to being returned.
	 * @param string $option Option name.
	 */
	return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
}

/**
 * Protects GeChiUI special option from being modified.
 *
 * Will die if $option is in protected list. Protected options are 'alloptions'
 * and 'notoptions' options.
 *
 *
 *
 * @param string $option Option name.
 */
function gc_protect_special_option( $option ) {
	if ( 'alloptions' === $option || 'notoptions' === $option ) {
		gc_die(
			sprintf(
				/* translators: %s: Option name. */
				__( '%s是一项GC的保护选项，因此无法修改' ),
				esc_html( $option )
			)
		);
	}
}

/**
 * Prints option value after sanitizing for forms.
 *
 *
 *
 * @param string $option Option name.
 */
function form_option( $option ) {
	echo esc_attr( get_option( $option ) );
}

/**
 * Loads and caches all autoloaded options, if available or all options.
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param bool $force_cache Optional. Whether to force an update of the local cache
 *                          from the persistent cache. Default false.
 * @return array List of all options.
 */
function gc_load_alloptions( $force_cache = false ) {
	global $gcdb;

	if ( ! gc_installing() || ! is_multisite() ) {
		$alloptions = gc_cache_get( 'alloptions', 'options', $force_cache );
	} else {
		$alloptions = false;
	}

	if ( ! $alloptions ) {
		$suppress      = $gcdb->suppress_errors();
		$alloptions_db = $gcdb->get_results( "SELECT option_name, option_value FROM $gcdb->options WHERE autoload = 'yes'" );
		if ( ! $alloptions_db ) {
			$alloptions_db = $gcdb->get_results( "SELECT option_name, option_value FROM $gcdb->options" );
		}
		$gcdb->suppress_errors( $suppress );

		$alloptions = array();
		foreach ( (array) $alloptions_db as $o ) {
			$alloptions[ $o->option_name ] = $o->option_value;
		}

		if ( ! gc_installing() || ! is_multisite() ) {
			/**
			 * Filters all options before caching them.
			 *
		
			 *
			 * @param array $alloptions Array with all options.
			 */
			$alloptions = apply_filters( 'pre_cache_alloptions', $alloptions );

			gc_cache_add( 'alloptions', $alloptions, 'options' );
		}
	}

	/**
	 * Filters all options after retrieving them.
	 *
	 *
	 * @param array $alloptions Array with all options.
	 */
	return apply_filters( 'alloptions', $alloptions );
}

/**
 * Loads and caches certain often requested site options if is_multisite() and a persistent cache is not being used.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $network_id Optional site ID for which to query the options. Defaults to the current site.
 */
function gc_load_core_site_options( $network_id = null ) {
	global $gcdb;

	if ( ! is_multisite() || gc_using_ext_object_cache() || gc_installing() ) {
		return;
	}

	if ( empty( $network_id ) ) {
		$network_id = get_current_network_id();
	}

	$core_options = array( 'site_name', 'siteurl', 'active_sitewide_plugins', '_site_transient_timeout_theme_roots', '_site_transient_theme_roots', 'site_admins', 'can_compress_scripts', 'global_terms_enabled', 'ms_files_rewriting' );

	$core_options_in = "'" . implode( "', '", $core_options ) . "'";
	$options         = $gcdb->get_results( $gcdb->prepare( "SELECT meta_key, meta_value FROM $gcdb->sitemeta WHERE meta_key IN ($core_options_in) AND site_id = %d", $network_id ) );

	$data = array();
	foreach ( $options as $option ) {
		$key                = $option->meta_key;
		$cache_key          = "{$network_id}:$key";
		$option->meta_value = maybe_unserialize( $option->meta_value );

		$data[ $cache_key ] = $option->meta_value;
	}
	gc_cache_set_multiple( $data, 'site-options' );
}

/**
 * Updates the value of an option that was already added.
 *
 * You do not need to serialize values. If the value needs to be serialized,
 * then it will be serialized before it is inserted into the database.
 * Remember, resources cannot be serialized or added as an option.
 *
 * If the option does not exist, it will be created.

 * This function is designed to work with or without a logged-in user. In terms of security,
 * plugin developers should check the current user's capabilities before updating any options.
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string      $option   Name of the option to update. Expected to not be SQL-escaped.
 * @param mixed       $value    Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @param string|bool $autoload Optional. Whether to load the option when GeChiUI starts up. For existing options,
 *                              `$autoload` can only be updated using `update_option()` if `$value` is also changed.
 *                              Accepts 'yes'|true to enable or 'no'|false to disable. For non-existent options,
 *                              the default value is 'yes'. Default null.
 * @return bool True if the value was updated, false otherwise.
 */
function update_option( $option, $value, $autoload = null ) {
	global $gcdb;

	if ( is_scalar( $option ) ) {
		$option = trim( $option );
	}

	if ( empty( $option ) ) {
		return false;
	}

	/*
	 * Until a proper _deprecated_option() function can be introduced,
	 * redirect requests to deprecated keys to the new, correct ones.
	 */
	$deprecated_keys = array(
		'blacklist_keys'    => 'disallowed_keys',
		'comment_whitelist' => 'comment_previously_approved',
	);

	if ( ! gc_installing() && isset( $deprecated_keys[ $option ] ) ) {
		_deprecated_argument(
			__FUNCTION__,
			'5.5.0',
			sprintf(
				/* translators: 1: Deprecated option key, 2: New option key. */
				__( '“%1$s”选项键已被重命名为“%2$s”。' ),
				$option,
				$deprecated_keys[ $option ]
			)
		);
		return update_option( $deprecated_keys[ $option ], $value, $autoload );
	}

	gc_protect_special_option( $option );

	if ( is_object( $value ) ) {
		$value = clone $value;
	}

	$value     = sanitize_option( $option, $value );
	$old_value = get_option( $option );

	/**
	 * Filters a specific option before its value is (maybe) serialized and updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param mixed  $old_value The old option value.
	 * @param string $option    Option name.
	 */
	$value = apply_filters( "pre_update_option_{$option}", $value, $old_value, $option );

	/**
	 * Filters an option before its value is (maybe) serialized and updated.
	 *
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param string $option    Name of the option.
	 * @param mixed  $old_value The old option value.
	 */
	$value = apply_filters( 'pre_update_option', $value, $option, $old_value );

	/*
	 * If the new and old values are the same, no need to update.
	 *
	 * Unserialized values will be adequate in most cases. If the unserialized
	 * data differs, the (maybe) serialized data is checked to avoid
	 * unnecessary database calls for otherwise identical object instances.
	 *
	 * See https://core.trac.gechiui.com/ticket/38903
	 */
	if ( $value === $old_value || maybe_serialize( $value ) === maybe_serialize( $old_value ) ) {
		return false;
	}

	/** This filter is documented in gc-includes/option.php */
	if ( apply_filters( "default_option_{$option}", false, $option, false ) === $old_value ) {
		// Default setting for new options is 'yes'.
		if ( null === $autoload ) {
			$autoload = 'yes';
		}

		return add_option( $option, $value, '', $autoload );
	}

	$serialized_value = maybe_serialize( $value );

	/**
	 * Fires immediately before an option value is updated.
	 *
	 *
	 * @param string $option    Name of the option to update.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	do_action( 'update_option', $option, $old_value, $value );

	$update_args = array(
		'option_value' => $serialized_value,
	);

	if ( null !== $autoload ) {
		$update_args['autoload'] = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';
	}

	$result = $gcdb->update( $gcdb->options, $update_args, array( 'option_name' => $option ) );
	if ( ! $result ) {
		return false;
	}

	$notoptions = gc_cache_get( 'notoptions', 'options' );

	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		gc_cache_set( 'notoptions', $notoptions, 'options' );
	}

	if ( ! gc_installing() ) {
		$alloptions = gc_load_alloptions( true );
		if ( isset( $alloptions[ $option ] ) ) {
			$alloptions[ $option ] = $serialized_value;
			gc_cache_set( 'alloptions', $alloptions, 'options' );
		} else {
			gc_cache_set( $option, $serialized_value, 'options' );
		}
	}

	/**
	 * Fires after the value of a specific option has been successfully updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	do_action( "update_option_{$option}", $old_value, $value, $option );

	/**
	 * Fires after the value of an option has been successfully updated.
	 *
	 *
	 * @param string $option    Name of the updated option.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	do_action( 'updated_option', $option, $old_value, $value );

	return true;
}

/**
 * Adds a new option.
 *
 * You do not need to serialize values. If the value needs to be serialized,
 * then it will be serialized before it is inserted into the database.
 * Remember, resources cannot be serialized or added as an option.
 *
 * You can create options without values and then update the values later.
 * Existing options will not be updated and checks are performed to ensure that you
 * aren't adding a protected GeChiUI option. Care should be taken to not name
 * options the same as the ones which are protected.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string      $option     Name of the option to add. Expected to not be SQL-escaped.
 * @param mixed       $value      Optional. Option value. Must be serializable if non-scalar.
 *                                Expected to not be SQL-escaped.
 * @param string      $deprecated Optional. Description. Not used anymore.
 * @param string|bool $autoload   Optional. Whether to load the option when GeChiUI starts up.
 *                                Default is enabled. Accepts 'no' to disable for legacy reasons.
 * @return bool True if the option was added, false otherwise.
 */
function add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
	global $gcdb;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.3.0' );
	}

	if ( is_scalar( $option ) ) {
		$option = trim( $option );
	}

	if ( empty( $option ) ) {
		return false;
	}

	/*
	 * Until a proper _deprecated_option() function can be introduced,
	 * redirect requests to deprecated keys to the new, correct ones.
	 */
	$deprecated_keys = array(
		'blacklist_keys'    => 'disallowed_keys',
		'comment_whitelist' => 'comment_previously_approved',
	);

	if ( ! gc_installing() && isset( $deprecated_keys[ $option ] ) ) {
		_deprecated_argument(
			__FUNCTION__,
			'5.5.0',
			sprintf(
				/* translators: 1: Deprecated option key, 2: New option key. */
				__( '“%1$s”选项键已被重命名为“%2$s”。' ),
				$option,
				$deprecated_keys[ $option ]
			)
		);
		return add_option( $deprecated_keys[ $option ], $value, $deprecated, $autoload );
	}

	gc_protect_special_option( $option );

	if ( is_object( $value ) ) {
		$value = clone $value;
	}

	$value = sanitize_option( $option, $value );

	// Make sure the option doesn't already exist.
	// We can check the 'notoptions' cache before we ask for a DB query.
	$notoptions = gc_cache_get( 'notoptions', 'options' );

	if ( ! is_array( $notoptions ) || ! isset( $notoptions[ $option ] ) ) {
		/** This filter is documented in gc-includes/option.php */
		if ( apply_filters( "default_option_{$option}", false, $option, false ) !== get_option( $option ) ) {
			return false;
		}
	}

	$serialized_value = maybe_serialize( $value );
	$autoload         = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';

	/**
	 * Fires before an option is added.
	 *
	 *
	 * @param string $option Name of the option to add.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( 'add_option', $option, $value );

	$result = $gcdb->query( $gcdb->prepare( "INSERT INTO `$gcdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option, $serialized_value, $autoload ) );
	if ( ! $result ) {
		return false;
	}

	if ( ! gc_installing() ) {
		if ( 'yes' === $autoload ) {
			$alloptions            = gc_load_alloptions( true );
			$alloptions[ $option ] = $serialized_value;
			gc_cache_set( 'alloptions', $alloptions, 'options' );
		} else {
			gc_cache_set( $option, $serialized_value, 'options' );
		}
	}

	// This option exists now.
	$notoptions = gc_cache_get( 'notoptions', 'options' ); // Yes, again... we need it to be fresh.

	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		gc_cache_set( 'notoptions', $notoptions, 'options' );
	}

	/**
	 * Fires after a specific option has been added.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param string $option Name of the option to add.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( "add_option_{$option}", $option, $value );

	/**
	 * Fires after an option has been added.
	 *
	 *
	 * @param string $option Name of the added option.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( 'added_option', $option, $value );

	return true;
}

/**
 * Removes option by name. Prevents removal of protected GeChiUI options.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $option Name of the option to delete. Expected to not be SQL-escaped.
 * @return bool True if the option was deleted, false otherwise.
 */
function delete_option( $option ) {
	global $gcdb;

	if ( is_scalar( $option ) ) {
		$option = trim( $option );
	}

	if ( empty( $option ) ) {
		return false;
	}

	gc_protect_special_option( $option );

	// Get the ID, if no ID then return.
	$row = $gcdb->get_row( $gcdb->prepare( "SELECT autoload FROM $gcdb->options WHERE option_name = %s", $option ) );
	if ( is_null( $row ) ) {
		return false;
	}

	/**
	 * Fires immediately before an option is deleted.
	 *
	 *
	 * @param string $option Name of the option to delete.
	 */
	do_action( 'delete_option', $option );

	$result = $gcdb->delete( $gcdb->options, array( 'option_name' => $option ) );

	if ( ! gc_installing() ) {
		if ( 'yes' === $row->autoload ) {
			$alloptions = gc_load_alloptions( true );
			if ( is_array( $alloptions ) && isset( $alloptions[ $option ] ) ) {
				unset( $alloptions[ $option ] );
				gc_cache_set( 'alloptions', $alloptions, 'options' );
			}
		} else {
			gc_cache_delete( $option, 'options' );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a specific option has been deleted.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 *
		 * @param string $option Name of the deleted option.
		 */
		do_action( "delete_option_{$option}", $option );

		/**
		 * Fires after an option has been deleted.
		 *
		 *
		 * @param string $option Name of the deleted option.
		 */
		do_action( 'deleted_option', $option );

		return true;
	}

	return false;
}

/**
 * Deletes a transient.
 *
 *
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return bool True if the transient was deleted, false otherwise.
 */
function delete_transient( $transient ) {

	/**
	 * Fires immediately before a specific transient is deleted.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param string $transient Transient name.
	 */
	do_action( "delete_transient_{$transient}", $transient );

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$result = gc_cache_delete( $transient, 'transient' );
	} else {
		$option_timeout = '_transient_timeout_' . $transient;
		$option         = '_transient_' . $transient;
		$result         = delete_option( $option );

		if ( $result ) {
			delete_option( $option_timeout );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a transient is deleted.
		 *
		 *
		 * @param string $transient Deleted transient name.
		 */
		do_action( 'deleted_transient', $transient );
	}

	return $result;
}

/**
 * Retrieves the value of a transient.
 *
 * If the transient does not exist, does not have a value, or has expired,
 * then the return value will be false.
 *
 *
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return mixed Value of transient.
 */
function get_transient( $transient ) {

	/**
	 * Filters the value of an existing transient before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * Returning a truthy value from the filter will effectively short-circuit retrieval
	 * and return the passed value instead.
	 *
	 *
	 * @param mixed  $pre_transient The default value to return if the transient does not exist.
	 *                              Any value other than false will short-circuit the retrieval
	 *                              of the transient, and return that value.
	 * @param string $transient     Transient name.
	 */
	$pre = apply_filters( "pre_transient_{$transient}", false, $transient );

	if ( false !== $pre ) {
		return $pre;
	}

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$value = gc_cache_get( $transient, 'transient' );
	} else {
		$transient_option = '_transient_' . $transient;
		if ( ! gc_installing() ) {
			// If option is not in alloptions, it is not autoloaded and thus has a timeout.
			$alloptions = gc_load_alloptions();
			if ( ! isset( $alloptions[ $transient_option ] ) ) {
				$transient_timeout = '_transient_timeout_' . $transient;
				$timeout           = get_option( $transient_timeout );
				if ( false !== $timeout && $timeout < time() ) {
					delete_option( $transient_option );
					delete_option( $transient_timeout );
					$value = false;
				}
			}
		}

		if ( ! isset( $value ) ) {
			$value = get_option( $transient_option );
		}
	}

	/**
	 * Filters an existing transient's value.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param mixed  $value     Value of transient.
	 * @param string $transient Transient name.
	 */
	return apply_filters( "transient_{$transient}", $value, $transient );
}

/**
 * Sets/updates the value of a transient.
 *
 * You do not need to serialize values. If the value needs to be serialized,
 * then it will be serialized before it is set.
 *
 *
 *
 * @param string $transient  Transient name. Expected to not be SQL-escaped.
 *                           Must be 172 characters or fewer in length.
 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
 *                           Expected to not be SQL-escaped.
 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
 * @return bool True if the value was set, false otherwise.
 */
function set_transient( $transient, $value, $expiration = 0 ) {

	$expiration = (int) $expiration;

	/**
	 * Filters a specific transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param mixed  $value      New value of transient.
	 * @param int    $expiration Time until expiration in seconds.
	 * @param string $transient  Transient name.
	 */
	$value = apply_filters( "pre_set_transient_{$transient}", $value, $expiration, $transient );

	/**
	 * Filters the expiration for a transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
	 * @param mixed  $value      New value of transient.
	 * @param string $transient  Transient name.
	 */
	$expiration = apply_filters( "expiration_of_transient_{$transient}", $expiration, $value, $transient );

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$result = gc_cache_set( $transient, $value, 'transient', $expiration );
	} else {
		$transient_timeout = '_transient_timeout_' . $transient;
		$transient_option  = '_transient_' . $transient;

		if ( false === get_option( $transient_option ) ) {
			$autoload = 'yes';
			if ( $expiration ) {
				$autoload = 'no';
				add_option( $transient_timeout, time() + $expiration, '', 'no' );
			}
			$result = add_option( $transient_option, $value, '', $autoload );
		} else {
			// If expiration is requested, but the transient has no timeout option,
			// delete, then re-create transient rather than update.
			$update = true;

			if ( $expiration ) {
				if ( false === get_option( $transient_timeout ) ) {
					delete_option( $transient_option );
					add_option( $transient_timeout, time() + $expiration, '', 'no' );
					$result = add_option( $transient_option, $value, '', 'no' );
					$update = false;
				} else {
					update_option( $transient_timeout, time() + $expiration );
				}
			}

			if ( $update ) {
				$result = update_option( $transient_option, $value );
			}
		}
	}

	if ( $result ) {

		/**
		 * Fires after the value for a specific transient has been set.
		 *
		 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
		 *
		 *
		 * @param mixed  $value      Transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 * @param string $transient  The name of the transient.
		 */
		do_action( "set_transient_{$transient}", $value, $expiration, $transient );

		/**
		 * Fires after the value for a transient has been set.
		 *
		 *
		 * @param string $transient  The name of the transient.
		 * @param mixed  $value      Transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 */
		do_action( 'setted_transient', $transient, $value, $expiration );
	}

	return $result;
}

/**
 * Deletes all expired transients.
 *
 * The multi-table delete syntax is used to delete the transient record
 * from table a, and the corresponding transient_timeout record from table b.
 *
 *
 *
 * @param bool $force_db Optional. Force cleanup to run against the database even when an external object cache is used.
 */
function delete_expired_transients( $force_db = false ) {
	global $gcdb;

	if ( ! $force_db && gc_using_ext_object_cache() ) {
		return;
	}

	$gcdb->query(
		$gcdb->prepare(
			"DELETE a, b FROM {$gcdb->options} a, {$gcdb->options} b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d",
			$gcdb->esc_like( '_transient_' ) . '%',
			$gcdb->esc_like( '_transient_timeout_' ) . '%',
			time()
		)
	);

	if ( ! is_multisite() ) {
		// Single site stores site transients in the options table.
		$gcdb->query(
			$gcdb->prepare(
				"DELETE a, b FROM {$gcdb->options} a, {$gcdb->options} b
				WHERE a.option_name LIKE %s
				AND a.option_name NOT LIKE %s
				AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
				AND b.option_value < %d",
				$gcdb->esc_like( '_site_transient_' ) . '%',
				$gcdb->esc_like( '_site_transient_timeout_' ) . '%',
				time()
			)
		);
	} elseif ( is_multisite() && is_main_site() && is_main_network() ) {
		// Multisite stores site transients in the sitemeta table.
		$gcdb->query(
			$gcdb->prepare(
				"DELETE a, b FROM {$gcdb->sitemeta} a, {$gcdb->sitemeta} b
				WHERE a.meta_key LIKE %s
				AND a.meta_key NOT LIKE %s
				AND b.meta_key = CONCAT( '_site_transient_timeout_', SUBSTRING( a.meta_key, 17 ) )
				AND b.meta_value < %d",
				$gcdb->esc_like( '_site_transient_' ) . '%',
				$gcdb->esc_like( '_site_transient_timeout_' ) . '%',
				time()
			)
		);
	}
}

/**
 * Saves and restores user interface settings stored in a cookie.
 *
 * Checks if the current user-settings cookie is updated and stores it. When no
 * cookie exists (different browser used), adds the last saved cookie restoring
 * the settings.
 *
 *
 */
function gc_user_settings() {

	if ( ! is_admin() || gc_doing_ajax() ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	if ( ! is_user_member_of_blog() ) {
		return;
	}

	$settings = (string) get_user_option( 'user-settings', $user_id );

	if ( isset( $_COOKIE[ 'gc-settings-' . $user_id ] ) ) {
		$cookie = preg_replace( '/[^A-Za-z0-9=&_]/', '', $_COOKIE[ 'gc-settings-' . $user_id ] );

		// No change or both empty.
		if ( $cookie == $settings ) {
			return;
		}

		$last_saved = (int) get_user_option( 'user-settings-time', $user_id );
		$current    = isset( $_COOKIE[ 'gc-settings-time-' . $user_id ] ) ? preg_replace( '/[^0-9]/', '', $_COOKIE[ 'gc-settings-time-' . $user_id ] ) : 0;

		// The cookie is newer than the saved value. Update the user_option and leave the cookie as-is.
		if ( $current > $last_saved ) {
			update_user_option( $user_id, 'user-settings', $cookie, false );
			update_user_option( $user_id, 'user-settings-time', time() - 5, false );
			return;
		}
	}

	// The cookie is not set in the current browser or the saved value is newer.
	$secure = ( 'https' === parse_url( admin_url(), PHP_URL_SCHEME ) );
	setcookie( 'gc-settings-' . $user_id, $settings, time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, $secure );
	setcookie( 'gc-settings-time-' . $user_id, time(), time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, $secure );
	$_COOKIE[ 'gc-settings-' . $user_id ] = $settings;
}

/**
 * Retrieves user interface setting value based on setting name.
 *
 *
 *
 * @param string       $name    The name of the setting.
 * @param string|false $default Optional. Default value to return when $name is not set. Default false.
 * @return mixed The last saved user setting or the default value/false if it doesn't exist.
 */
function get_user_setting( $name, $default = false ) {
	$all_user_settings = get_all_user_settings();

	return isset( $all_user_settings[ $name ] ) ? $all_user_settings[ $name ] : $default;
}

/**
 * Adds or updates user interface setting.
 *
 * Both $name and $value can contain only ASCII letters, numbers, hyphens, and underscores.
 *
 * This function has to be used before any output has started as it calls setcookie().
 *
 *
 *
 * @param string $name  The name of the setting.
 * @param string $value The value for the setting.
 * @return bool|null True if set successfully, false otherwise.
 *                   Null if the current user is not a member of the site.
 */
function set_user_setting( $name, $value ) {
	if ( headers_sent() ) {
		return false;
	}

	$all_user_settings          = get_all_user_settings();
	$all_user_settings[ $name ] = $value;

	return gc_set_all_user_settings( $all_user_settings );
}

/**
 * Deletes user interface settings.
 *
 * Deleting settings would reset them to the defaults.
 *
 * This function has to be used before any output has started as it calls setcookie().
 *
 *
 *
 * @param string $names The name or array of names of the setting to be deleted.
 * @return bool|null True if deleted successfully, false otherwise.
 *                   Null if the current user is not a member of the site.
 */
function delete_user_setting( $names ) {
	if ( headers_sent() ) {
		return false;
	}

	$all_user_settings = get_all_user_settings();
	$names             = (array) $names;
	$deleted           = false;

	foreach ( $names as $name ) {
		if ( isset( $all_user_settings[ $name ] ) ) {
			unset( $all_user_settings[ $name ] );
			$deleted = true;
		}
	}

	if ( $deleted ) {
		return gc_set_all_user_settings( $all_user_settings );
	}

	return false;
}

/**
 * Retrieves all user interface settings.
 *
 *
 *
 * @global array $_updated_user_settings
 *
 * @return array The last saved user settings or empty array.
 */
function get_all_user_settings() {
	global $_updated_user_settings;

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return array();
	}

	if ( isset( $_updated_user_settings ) && is_array( $_updated_user_settings ) ) {
		return $_updated_user_settings;
	}

	$user_settings = array();

	if ( isset( $_COOKIE[ 'gc-settings-' . $user_id ] ) ) {
		$cookie = preg_replace( '/[^A-Za-z0-9=&_-]/', '', $_COOKIE[ 'gc-settings-' . $user_id ] );

		if ( strpos( $cookie, '=' ) ) { // '=' cannot be 1st char.
			parse_str( $cookie, $user_settings );
		}
	} else {
		$option = get_user_option( 'user-settings', $user_id );

		if ( $option && is_string( $option ) ) {
			parse_str( $option, $user_settings );
		}
	}

	$_updated_user_settings = $user_settings;
	return $user_settings;
}

/**
 * Private. Sets all user interface settings.
 *
 *
 * @access private
 *
 * @global array $_updated_user_settings
 *
 * @param array $user_settings User settings.
 * @return bool|null True if set successfully, false if the current user could not be found.
 *                   Null if the current user is not a member of the site.
 */
function gc_set_all_user_settings( $user_settings ) {
	global $_updated_user_settings;

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	if ( ! is_user_member_of_blog() ) {
		return;
	}

	$settings = '';
	foreach ( $user_settings as $name => $value ) {
		$_name  = preg_replace( '/[^A-Za-z0-9_-]+/', '', $name );
		$_value = preg_replace( '/[^A-Za-z0-9_-]+/', '', $value );

		if ( ! empty( $_name ) ) {
			$settings .= $_name . '=' . $_value . '&';
		}
	}

	$settings = rtrim( $settings, '&' );
	parse_str( $settings, $_updated_user_settings );

	update_user_option( $user_id, 'user-settings', $settings, false );
	update_user_option( $user_id, 'user-settings-time', time(), false );

	return true;
}

/**
 * Deletes the user settings of the current user.
 *
 *
 */
function delete_all_user_settings() {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	update_user_option( $user_id, 'user-settings', '', false );
	setcookie( 'gc-settings-' . $user_id, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH );
}

/**
 * Retrieve an option value for the current network based on name of option.
 *
 *
 *
 *
 *
 * @see get_network_option()
 *
 * @param string $option     Name of the option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default    Optional. Value to return if the option doesn't exist. Default false.
 * @param bool   $deprecated Whether to use cache. Multisite only. Always set to true.
 * @return mixed Value set for the option.
 */
function get_site_option( $option, $default = false, $deprecated = true ) {
	return get_network_option( null, $option, $default );
}

/**
 * Adds a new option for the current network.
 *
 * Existing options will not be updated. Note that prior to 3.3 this wasn't the case.
 *
 *
 *
 *
 * @see add_network_option()
 *
 * @param string $option Name of the option to add. Expected to not be SQL-escaped.
 * @param mixed  $value  Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool True if the option was added, false otherwise.
 */
function add_site_option( $option, $value ) {
	return add_network_option( null, $option, $value );
}

/**
 * Removes a option by name for the current network.
 *
 *
 *
 *
 * @see delete_network_option()
 *
 * @param string $option Name of the option to delete. Expected to not be SQL-escaped.
 * @return bool True if the option was deleted, false otherwise.
 */
function delete_site_option( $option ) {
	return delete_network_option( null, $option );
}

/**
 * Updates the value of an option that was already added for the current network.
 *
 *
 *
 *
 * @see update_network_option()
 *
 * @param string $option Name of the option. Expected to not be SQL-escaped.
 * @param mixed  $value  Option value. Expected to not be SQL-escaped.
 * @return bool True if the value was updated, false otherwise.
 */
function update_site_option( $option, $value ) {
	return update_network_option( null, $option, $value );
}

/**
 * Retrieves a network's option value based on the option name.
 *
 *
 *
 * @see get_option()
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of the option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default    Optional. Value to return if the option doesn't exist. Default false.
 * @return mixed Value set for the option.
 */
function get_network_option( $network_id, $option, $default = false ) {
	global $gcdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	/**
	 * Filters the value of an existing network option before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * Returning a truthy value from the filter will effectively short-circuit retrieval
	 * and return the passed value instead.
	 *
	 *
	 * @param mixed  $pre_option The value to return instead of the option value. This differs
	 *                           from `$default`, which is used as the fallback value in the event
	 *                           the option doesn't exist elsewhere in get_network_option().
	 *                           Default false (to skip past the short-circuit).
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 * @param mixed  $default    The fallback value to return if the option does not exist.
	 *                           Default false.
	 */
	$pre = apply_filters( "pre_site_option_{$option}", false, $option, $network_id, $default );

	if ( false !== $pre ) {
		return $pre;
	}

	// Prevent non-existent options from triggering multiple queries.
	$notoptions_key = "$network_id:notoptions";
	$notoptions     = gc_cache_get( $notoptions_key, 'site-options' );

	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {

		/**
		 * Filters a specific default network option.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 *
		 * @param mixed  $default    The value to return if the site option does not exist
		 *                           in the database.
		 * @param string $option     Option name.
		 * @param int    $network_id ID of the network.
		 */
		return apply_filters( "default_site_option_{$option}", $default, $option, $network_id );
	}

	if ( ! is_multisite() ) {
		/** This filter is documented in gc-includes/option.php */
		$default = apply_filters( 'default_site_option_' . $option, $default, $option, $network_id );
		$value   = get_option( $option, $default );
	} else {
		$cache_key = "$network_id:$option";
		$value     = gc_cache_get( $cache_key, 'site-options' );

		if ( ! isset( $value ) || false === $value ) {
			$row = $gcdb->get_row( $gcdb->prepare( "SELECT meta_value FROM $gcdb->sitemeta WHERE meta_key = %s AND site_id = %d", $option, $network_id ) );

			// Has to be get_row() instead of get_var() because of funkiness with 0, false, null values.
			if ( is_object( $row ) ) {
				$value = $row->meta_value;
				$value = maybe_unserialize( $value );
				gc_cache_set( $cache_key, $value, 'site-options' );
			} else {
				if ( ! is_array( $notoptions ) ) {
					$notoptions = array();
				}

				$notoptions[ $option ] = true;
				gc_cache_set( $notoptions_key, $notoptions, 'site-options' );

				/** This filter is documented in gc-includes/option.php */
				$value = apply_filters( 'default_site_option_' . $option, $default, $option, $network_id );
			}
		}
	}

	if ( ! is_array( $notoptions ) ) {
		$notoptions = array();
		gc_cache_set( $notoptions_key, $notoptions, 'site-options' );
	}

	/**
	 * Filters the value of an existing network option.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $value      Value of network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	return apply_filters( "site_option_{$option}", $value, $option, $network_id );
}

/**
 * Adds a new network option.
 *
 * Existing options will not be updated.
 *
 *
 *
 * @see add_option()
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of the option to add. Expected to not be SQL-escaped.
 * @param mixed  $value      Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool True if the option was added, false otherwise.
 */
function add_network_option( $network_id, $option, $value ) {
	global $gcdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	gc_protect_special_option( $option );

	/**
	 * Filters the value of a specific network option before it is added.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $value      Value of network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	$value = apply_filters( "pre_add_site_option_{$option}", $value, $option, $network_id );

	$notoptions_key = "$network_id:notoptions";

	if ( ! is_multisite() ) {
		$result = add_option( $option, $value, '', 'no' );
	} else {
		$cache_key = "$network_id:$option";

		// Make sure the option doesn't already exist.
		// We can check the 'notoptions' cache before we ask for a DB query.
		$notoptions = gc_cache_get( $notoptions_key, 'site-options' );

		if ( ! is_array( $notoptions ) || ! isset( $notoptions[ $option ] ) ) {
			if ( false !== get_network_option( $network_id, $option, false ) ) {
				return false;
			}
		}

		$value = sanitize_option( $option, $value );

		$serialized_value = maybe_serialize( $value );
		$result           = $gcdb->insert(
			$gcdb->sitemeta,
			array(
				'site_id'    => $network_id,
				'meta_key'   => $option,
				'meta_value' => $serialized_value,
			)
		);

		if ( ! $result ) {
			return false;
		}

		gc_cache_set( $cache_key, $value, 'site-options' );

		// This option exists now.
		$notoptions = gc_cache_get( $notoptions_key, 'site-options' ); // Yes, again... we need it to be fresh.

		if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
			unset( $notoptions[ $option ] );
			gc_cache_set( $notoptions_key, $notoptions, 'site-options' );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a specific network option has been successfully added.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "add_site_option_{$option}", $option, $value, $network_id );

		/**
		 * Fires after a network option has been successfully added.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'add_site_option', $option, $value, $network_id );

		return true;
	}

	return false;
}

/**
 * Removes a network option by name.
 *
 *
 *
 * @see delete_option()
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of the option to delete. Expected to not be SQL-escaped.
 * @return bool True if the option was deleted, false otherwise.
 */
function delete_network_option( $network_id, $option ) {
	global $gcdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	/**
	 * Fires immediately before a specific network option is deleted.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	do_action( "pre_delete_site_option_{$option}", $option, $network_id );

	if ( ! is_multisite() ) {
		$result = delete_option( $option );
	} else {
		$row = $gcdb->get_row( $gcdb->prepare( "SELECT meta_id FROM {$gcdb->sitemeta} WHERE meta_key = %s AND site_id = %d", $option, $network_id ) );
		if ( is_null( $row ) || ! $row->meta_id ) {
			return false;
		}
		$cache_key = "$network_id:$option";
		gc_cache_delete( $cache_key, 'site-options' );

		$result = $gcdb->delete(
			$gcdb->sitemeta,
			array(
				'meta_key' => $option,
				'site_id'  => $network_id,
			)
		);
	}

	if ( $result ) {

		/**
		 * Fires after a specific network option has been deleted.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "delete_site_option_{$option}", $option, $network_id );

		/**
		 * Fires after a network option has been deleted.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'delete_site_option', $option, $network_id );

		return true;
	}

	return false;
}

/**
 * Updates the value of a network option that was already added.
 *
 *
 *
 * @see update_option()
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of the option. Expected to not be SQL-escaped.
 * @param mixed  $value      Option value. Expected to not be SQL-escaped.
 * @return bool True if the value was updated, false otherwise.
 */
function update_network_option( $network_id, $option, $value ) {
	global $gcdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	gc_protect_special_option( $option );

	$old_value = get_network_option( $network_id, $option, false );

	/**
	 * Filters a specific network option before its value is updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 *
	 * @param mixed  $value      New value of the network option.
	 * @param mixed  $old_value  Old value of the network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	$value = apply_filters( "pre_update_site_option_{$option}", $value, $old_value, $option, $network_id );

	/*
	 * If the new and old values are the same, no need to update.
	 *
	 * Unserialized values will be adequate in most cases. If the unserialized
	 * data differs, the (maybe) serialized data is checked to avoid
	 * unnecessary database calls for otherwise identical object instances.
	 *
	 * See https://core.trac.gechiui.com/ticket/44956
	 */
	if ( $value === $old_value || maybe_serialize( $value ) === maybe_serialize( $old_value ) ) {
		return false;
	}

	if ( false === $old_value ) {
		return add_network_option( $network_id, $option, $value );
	}

	$notoptions_key = "$network_id:notoptions";
	$notoptions     = gc_cache_get( $notoptions_key, 'site-options' );

	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		gc_cache_set( $notoptions_key, $notoptions, 'site-options' );
	}

	if ( ! is_multisite() ) {
		$result = update_option( $option, $value, 'no' );
	} else {
		$value = sanitize_option( $option, $value );

		$serialized_value = maybe_serialize( $value );
		$result           = $gcdb->update(
			$gcdb->sitemeta,
			array( 'meta_value' => $serialized_value ),
			array(
				'site_id'  => $network_id,
				'meta_key' => $option,
			)
		);

		if ( $result ) {
			$cache_key = "$network_id:$option";
			gc_cache_set( $cache_key, $value, 'site-options' );
		}
	}

	if ( $result ) {

		/**
		 * Fires after the value of a specific network option has been successfully updated.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Current value of the network option.
		 * @param mixed  $old_value  Old value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "update_site_option_{$option}", $option, $value, $old_value, $network_id );

		/**
		 * Fires after the value of a network option has been successfully updated.
		 *
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Current value of the network option.
		 * @param mixed  $old_value  Old value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'update_site_option', $option, $value, $old_value, $network_id );

		return true;
	}

	return false;
}

/**
 * Deletes a site transient.
 *
 *
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return bool True if the transient was deleted, false otherwise.
 */
function delete_site_transient( $transient ) {

	/**
	 * Fires immediately before a specific site transient is deleted.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param string $transient Transient name.
	 */
	do_action( "delete_site_transient_{$transient}", $transient );

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$result = gc_cache_delete( $transient, 'site-transient' );
	} else {
		$option_timeout = '_site_transient_timeout_' . $transient;
		$option         = '_site_transient_' . $transient;
		$result         = delete_site_option( $option );

		if ( $result ) {
			delete_site_option( $option_timeout );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a transient is deleted.
		 *
		 *
		 * @param string $transient Deleted transient name.
		 */
		do_action( 'deleted_site_transient', $transient );
	}

	return $result;
}

/**
 * Retrieves the value of a site transient.
 *
 * If the transient does not exist, does not have a value, or has expired,
 * then the return value will be false.
 *
 *
 *
 * @see get_transient()
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return mixed Value of transient.
 */
function get_site_transient( $transient ) {

	/**
	 * Filters the value of an existing site transient before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * Returning a truthy value from the filter will effectively short-circuit retrieval
	 * and return the passed value instead.
	 *
	 *
	 * @param mixed  $pre_site_transient The default value to return if the site transient does not exist.
	 *                                   Any value other than false will short-circuit the retrieval
	 *                                   of the transient, and return that value.
	 * @param string $transient          Transient name.
	 */
	$pre = apply_filters( "pre_site_transient_{$transient}", false, $transient );

	if ( false !== $pre ) {
		return $pre;
	}

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$value = gc_cache_get( $transient, 'site-transient' );
	} else {
		// Core transients that do not have a timeout. Listed here so querying timeouts can be avoided.
		$no_timeout       = array( 'update_core', 'update_plugins', 'update_themes' );
		$transient_option = '_site_transient_' . $transient;
		if ( ! in_array( $transient, $no_timeout, true ) ) {
			$transient_timeout = '_site_transient_timeout_' . $transient;
			$timeout           = get_site_option( $transient_timeout );
			if ( false !== $timeout && $timeout < time() ) {
				delete_site_option( $transient_option );
				delete_site_option( $transient_timeout );
				$value = false;
			}
		}

		if ( ! isset( $value ) ) {
			$value = get_site_option( $transient_option );
		}
	}

	/**
	 * Filters the value of an existing site transient.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param mixed  $value     Value of site transient.
	 * @param string $transient Transient name.
	 */
	return apply_filters( "site_transient_{$transient}", $value, $transient );
}

/**
 * Sets/updates the value of a site transient.
 *
 * You do not need to serialize values. If the value needs to be serialized,
 * then it will be serialized before it is set.
 *
 *
 *
 * @see set_transient()
 *
 * @param string $transient  Transient name. Expected to not be SQL-escaped. Must be
 *                           167 characters or fewer in length.
 * @param mixed  $value      Transient value. Expected to not be SQL-escaped.
 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
 * @return bool True if the value was set, false otherwise.
 */
function set_site_transient( $transient, $value, $expiration = 0 ) {

	/**
	 * Filters the value of a specific site transient before it is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param mixed  $value     New value of site transient.
	 * @param string $transient Transient name.
	 */
	$value = apply_filters( "pre_set_site_transient_{$transient}", $value, $transient );

	$expiration = (int) $expiration;

	/**
	 * Filters the expiration for a site transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 *
	 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
	 * @param mixed  $value      New value of site transient.
	 * @param string $transient  Transient name.
	 */
	$expiration = apply_filters( "expiration_of_site_transient_{$transient}", $expiration, $value, $transient );

	if ( gc_using_ext_object_cache() || gc_installing() ) {
		$result = gc_cache_set( $transient, $value, 'site-transient', $expiration );
	} else {
		$transient_timeout = '_site_transient_timeout_' . $transient;
		$option            = '_site_transient_' . $transient;

		if ( false === get_site_option( $option ) ) {
			if ( $expiration ) {
				add_site_option( $transient_timeout, time() + $expiration );
			}
			$result = add_site_option( $option, $value );
		} else {
			if ( $expiration ) {
				update_site_option( $transient_timeout, time() + $expiration );
			}
			$result = update_site_option( $option, $value );
		}
	}

	if ( $result ) {

		/**
		 * Fires after the value for a specific site transient has been set.
		 *
		 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
		 *
		 *
		 * @param mixed  $value      Site transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 * @param string $transient  Transient name.
		 */
		do_action( "set_site_transient_{$transient}", $value, $expiration, $transient );

		/**
		 * Fires after the value for a site transient has been set.
		 *
		 *
		 * @param string $transient  The name of the site transient.
		 * @param mixed  $value      Site transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 */
		do_action( 'setted_site_transient', $transient, $value, $expiration );
	}

	return $result;
}

/**
 * Registers default settings available in GeChiUI.
 *
 * The settings registered here are primarily useful for the REST API, so this
 * does not encompass all settings available in GeChiUI.
 *
 *
 */
function register_initial_settings() {
	register_setting(
		'general',
		'blogname',
		array(
			'show_in_rest' => array(
				'name' => 'title',
			),
			'type'         => 'string',
			'description'  => __( '站点标题。' ),
		)
	);

	register_setting(
		'general',
		'blogdescription',
		array(
			'show_in_rest' => array(
				'name' => 'description',
			),
			'type'         => 'string',
			'description'  => __( '站点副标题。' ),
		)
	);

	if ( ! is_multisite() ) {
		register_setting(
			'general',
			'siteurl',
			array(
				'show_in_rest' => array(
					'name'   => 'url',
					'schema' => array(
						'format' => 'uri',
					),
				),
				'type'         => 'string',
				'description'  => __( '站点URL。' ),
			)
		);
	}

	if ( ! is_multisite() ) {
		register_setting(
			'general',
			'admin_email',
			array(
				'show_in_rest' => array(
					'name'   => 'email',
					'schema' => array(
						'format' => 'email',
					),
				),
				'type'         => 'string',
				'description'  => __( '此地址被用作管理用途，如新用户通知。' ),
			)
		);
	}

	register_setting(
		'general',
		'timezone_string',
		array(
			'show_in_rest' => array(
				'name' => 'timezone',
			),
			'type'         => 'string',
			'description'  => __( '和您在同一个时区的城市。' ),
		)
	);

	register_setting(
		'general',
		'date_format',
		array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( '对所有日期字符串适用的日期格式。' ),
		)
	);

	register_setting(
		'general',
		'time_format',
		array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( '对所有时间字符串适用的时间格式。' ),
		)
	);

	register_setting(
		'general',
		'start_of_week',
		array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( '一周从周几开始。' ),
		)
	);

	register_setting(
		'general',
		'GCLANG',
		array(
			'show_in_rest' => array(
				'name' => 'language',
			),
			'type'         => 'string',
			'description'  => __( 'GeChiUI地区语言代码。' ),
			'default'      => 'zh_CN',
		)
	);

	register_setting(
		'writing',
		'use_smilies',
		array(
			'show_in_rest' => true,
			'type'         => 'boolean',
			'description'  => __( '将表情符号如:-)和:-P转换为图片显示。' ),
			'default'      => true,
		)
	);

	register_setting(
		'writing',
		'default_category',
		array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( '默认文章分类。' ),
		)
	);

	register_setting(
		'writing',
		'default_post_format',
		array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( '默认文章形式。' ),
		)
	);

	register_setting(
		'reading',
		'posts_per_page',
		array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( '最多显示的博客页面数量。' ),
			'default'      => 10,
		)
	);

	register_setting(
		'discussion',
		'default_ping_status',
		array(
			'show_in_rest' => array(
				'schema' => array(
					'enum' => array( 'open', 'closed' ),
				),
			),
			'type'         => 'string',
			'description'  => __( '允许其他博客发送链接通知（pingback和trackback）到新文章。' ),
		)
	);

	register_setting(
		'discussion',
		'default_comment_status',
		array(
			'show_in_rest' => array(
				'schema' => array(
					'enum' => array( 'open', 'closed' ),
				),
			),
			'type'         => 'string',
			'description'  => __( '允许他人在新文章上发表评论。' ),
		)
	);
}

/**
 * Registers a setting and its data.
 *
 *
 *
 *
 *
 *
 *              请考虑编写更具包容性的代码。
 *
 * @global array $new_allowed_options
 * @global array $gc_registered_settings
 *
 * @param string $option_group A settings group name. Should correspond to an allowed option key name.
 *                             Default allowed option key names include 'general', 'discussion', 'media',
 *                             'reading', 'writing', and 'options'.
 * @param string $option_name The name of an option to sanitize and save.
 * @param array  $args {
 *     Data used to describe the setting when registered.
 *
 *     @type string     $type              The type of data associated with this setting.
 *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
 *     @type string     $description       A description of the data attached to this setting.
 *     @type callable   $sanitize_callback A callback function that sanitizes the option's value.
 *     @type bool|array $show_in_rest      Whether data associated with this setting should be included in the REST API.
 *                                         When registering complex settings, this argument may optionally be an
 *                                         array with a 'schema' key.
 *     @type mixed      $default           Default value when calling `get_option()`.
 * }
 */
function register_setting( $option_group, $option_name, $args = array() ) {
	global $new_allowed_options, $gc_registered_settings;

	/*
	 * In 5.5.0, the `$new_whitelist_options` global variable was renamed to `$new_allowed_options`.
	 * 请考虑编写更具包容性的代码。
	 */
	$GLOBALS['new_whitelist_options'] = &$new_allowed_options;

	$defaults = array(
		'type'              => 'string',
		'group'             => $option_group,
		'description'       => '',
		'sanitize_callback' => null,
		'show_in_rest'      => false,
	);

	// Back-compat: old sanitize callback is added.
	if ( is_callable( $args ) ) {
		$args = array(
			'sanitize_callback' => $args,
		);
	}

	/**
	 * Filters the registration arguments when registering a setting.
	 *
	 *
	 * @param array  $args         Array of setting registration arguments.
	 * @param array  $defaults     Array of default arguments.
	 * @param string $option_group Setting group.
	 * @param string $option_name  Setting name.
	 */
	$args = apply_filters( 'register_setting_args', $args, $defaults, $option_group, $option_name );

	$args = gc_parse_args( $args, $defaults );

	// Require an item schema when registering settings with an array type.
	if ( false !== $args['show_in_rest'] && 'array' === $args['type'] && ( ! is_array( $args['show_in_rest'] ) || ! isset( $args['show_in_rest']['schema']['items'] ) ) ) {
		_doing_it_wrong( __FUNCTION__, __( '当您注册“array”设置来在REST API中显示时，您必须在“show_in_rest.schema.items”中对每个数组项目指定模式。' ), '5.4.0' );
	}

	if ( ! is_array( $gc_registered_settings ) ) {
		$gc_registered_settings = array();
	}

	if ( 'misc' === $option_group ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			sprintf(
				/* translators: %s: misc */
				__( '“%s”设置组已被移除，请使用其他设置组。' ),
				'misc'
			)
		);
		$option_group = 'general';
	}

	if ( 'privacy' === $option_group ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.5.0',
			sprintf(
				/* translators: %s: privacy */
				__( '“%s”设置组已被移除，请使用其他设置组。' ),
				'privacy'
			)
		);
		$option_group = 'reading';
	}

	$new_allowed_options[ $option_group ][] = $option_name;

	if ( ! empty( $args['sanitize_callback'] ) ) {
		add_filter( "sanitize_option_{$option_name}", $args['sanitize_callback'] );
	}
	if ( array_key_exists( 'default', $args ) ) {
		add_filter( "default_option_{$option_name}", 'filter_default_option', 10, 3 );
	}

	/**
	 * Fires immediately before the setting is registered but after its filters are in place.
	 *
	 *
	 * @param string $option_group Setting group.
	 * @param string $option_name  Setting name.
	 * @param array  $args         Array of setting registration arguments.
	 */
	do_action( 'register_setting', $option_group, $option_name, $args );

	$gc_registered_settings[ $option_name ] = $args;
}

/**
 * Unregisters a setting.
 *
 *
 *
 *
 *              请考虑编写更具包容性的代码。
 *
 * @global array $new_allowed_options
 * @global array $gc_registered_settings
 *
 * @param string   $option_group The settings group name used during registration.
 * @param string   $option_name  The name of the option to unregister.
 * @param callable $deprecated   Optional. Deprecated.
 */
function unregister_setting( $option_group, $option_name, $deprecated = '' ) {
	global $new_allowed_options, $gc_registered_settings;

	/*
	 * In 5.5.0, the `$new_whitelist_options` global variable was renamed to `$new_allowed_options`.
	 * 请考虑编写更具包容性的代码。
	 */
	$GLOBALS['new_whitelist_options'] = &$new_allowed_options;

	if ( 'misc' === $option_group ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			sprintf(
				/* translators: %s: misc */
				__( '“%s”设置组已被移除，请使用其他设置组。' ),
				'misc'
			)
		);
		$option_group = 'general';
	}

	if ( 'privacy' === $option_group ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.5.0',
			sprintf(
				/* translators: %s: privacy */
				__( '“%s”设置组已被移除，请使用其他设置组。' ),
				'privacy'
			)
		);
		$option_group = 'reading';
	}

	$pos = array_search( $option_name, (array) $new_allowed_options[ $option_group ], true );

	if ( false !== $pos ) {
		unset( $new_allowed_options[ $option_group ][ $pos ] );
	}

	if ( '' !== $deprecated ) {
		_deprecated_argument(
			__FUNCTION__,
			'4.7.0',
			sprintf(
				/* translators: 1: $sanitize_callback, 2: register_setting() */
				__( '%1$s已被弃用。请给%2$s提供一个回调函数。' ),
				'<code>$sanitize_callback</code>',
				'<code>register_setting()</code>'
			)
		);
		remove_filter( "sanitize_option_{$option_name}", $deprecated );
	}

	if ( isset( $gc_registered_settings[ $option_name ] ) ) {
		// Remove the sanitize callback if one was set during registration.
		if ( ! empty( $gc_registered_settings[ $option_name ]['sanitize_callback'] ) ) {
			remove_filter( "sanitize_option_{$option_name}", $gc_registered_settings[ $option_name ]['sanitize_callback'] );
		}

		// Remove the default filter if a default was provided during registration.
		if ( array_key_exists( 'default', $gc_registered_settings[ $option_name ] ) ) {
			remove_filter( "default_option_{$option_name}", 'filter_default_option', 10 );
		}

		/**
		 * Fires immediately before the setting is unregistered and after its filters have been removed.
		 *
		 *
		 * @param string $option_group Setting group.
		 * @param string $option_name  Setting name.
		 */
		do_action( 'unregister_setting', $option_group, $option_name );

		unset( $gc_registered_settings[ $option_name ] );
	}
}

/**
 * Retrieves an array of registered settings.
 *
 *
 *
 * @global array $gc_registered_settings
 *
 * @return array List of registered settings, keyed by option name.
 */
function get_registered_settings() {
	global $gc_registered_settings;

	if ( ! is_array( $gc_registered_settings ) ) {
		return array();
	}

	return $gc_registered_settings;
}

/**
 * Filters the default value for the option.
 *
 * For settings which register a default setting in `register_setting()`, this
 * function is added as a filter to `default_option_{$option}`.
 *
 *
 *
 * @param mixed  $default        Existing default value to return.
 * @param string $option         Option name.
 * @param bool   $passed_default Was `get_option()` passed a default value?
 * @return mixed Filtered default value.
 */
function filter_default_option( $default, $option, $passed_default ) {
	if ( $passed_default ) {
		return $default;
	}

	$registered = get_registered_settings();
	if ( empty( $registered[ $option ] ) ) {
		return $default;
	}

	return $registered[ $option ]['default'];
}
