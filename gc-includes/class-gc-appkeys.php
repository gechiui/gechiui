<?php
/**
 * GC_AppKeys class
 *
 * @package GeChiUI
 *
 */

/**
 * Class for displaying, modifying, and sanitizing appkeys.
 *
 * @package GeChiUI
 */
class GC_AppKeys {

	/**
	 * The appkeys user meta key.
	 *
	 *
	 * @var string
	 */
	const USERMETA_KEY_APPLICATION_PASSWORDS = '_appkeys';

	/**
	 * The option name used to store whether appkeys is in use.
	 *
	 *
	 * @var string
	 */
	const OPTION_KEY_IN_USE = 'using_appkeys';

	/**
	 * The generated appkey length.
	 *
	 *
	 * @var int
	 */
	const PW_LENGTH = 24;

	/**
	 * Checks if AppKeys are being used by the site.
	 *
	 * This returns true if at least one AppKey has ever been created.
	 *
	 *
	 * @return bool
	 */
	public static function is_in_use() {
		$network_id = get_main_network_id();
		return (bool) get_network_option( $network_id, self::OPTION_KEY_IN_USE );
	}

	/**
	 * Creates a new appkey.
	 *
	 *
	 * @param int   $user_id  User ID.
	 * @param array $args     {
	 *     Arguments used to create the appkey.
	 *
	 *     @type string $name   The name of the appkey.
	 *     @type string $app_id A UUID provided by the application to uniquely identify it.
	 * }
	 * @return array|GC_Error The first key in the array is the new password, the second is its detailed information.
	 *                        A GC_Error instance is returned on error.
	 */
	public static function create_new_appkey( $user_id, $args = array() ) {
		if ( ! empty( $args['name'] ) ) {
			$args['name'] = sanitize_text_field( $args['name'] );
		}

		if ( empty( $args['name'] ) ) {
			return new GC_Error( 'appkey_empty_name', __( '创建Appkey需要应用程序名称。' ), array( 'status' => 400 ) );
		}

		if ( self::application_name_exists_for_user( $user_id, $args['name'] ) ) {
			return new GC_Error( 'appkey_duplicate_name', __( '每个应用程序名称都应该是唯一的。' ), array( 'status' => 409 ) );
		}

		$new_password    = gc_generate_password( static::PW_LENGTH, false );
		$hashed_password = gc_hash_password( $new_password );

		$new_item = array(
			'uuid'      => gc_generate_uuid4(),
			'app_id'    => empty( $args['app_id'] ) ? '' : $args['app_id'],
			'name'      => $args['name'],
			'password'  => $hashed_password,
			'created'   => time(),
			'last_used' => null,
			'last_ip'   => null,
		);

		$passwords   = static::get_user_appkeys( $user_id );
		$passwords[] = $new_item;
		$saved       = static::set_user_appkeys( $user_id, $passwords );

		if ( ! $saved ) {
			return new GC_Error( 'db_error', __( '无法保存Appkey。' ) );
		}

		$network_id = get_main_network_id();
		if ( ! get_network_option( $network_id, self::OPTION_KEY_IN_USE ) ) {
			update_network_option( $network_id, self::OPTION_KEY_IN_USE, true );
		}

		/**
		 * Fires when an appkey is created.
		 *
		 *
		 * @param int    $user_id      The user ID.
		 * @param array  $new_item     {
		 *     The details about the created password.
		 *
		 *     @type string $uuid      该Appkey的唯一标识符。
		 *     @type string $app_id    A UUID provided by the application to uniquely identify it.
		 *     @type string $name      The name of the appkey.
		 *     @type string $password  A one-way hash of the password.
		 *     @type int    $created   Unix timestamp of when the password was created.
		 *     @type null   $last_used Null.
		 *     @type null   $last_ip   Null.
		 * }
		 * @param string $new_password The unhashed generated appkey.
		 * @param array  $args         {
		 *     Arguments used to create the appkey.
		 *
		 *     @type string $name   The name of the appkey.
		 *     @type string $app_id A UUID provided by the application to uniquely identify it.
		 * }
		 */
		do_action( 'gc_create_appkey', $user_id, $new_item, $new_password, $args );

		return array( $new_password, $new_item );
	}

	/**
	 * Gets a user's appkeys.
	 *
	 *
	 * @param int $user_id User ID.
	 * @return array {
	 *     The list of app passwords.
	 *
	 *     @type array ...$0 {
	 *         @type string      $uuid      该Appkey的唯一标识符。
	 *         @type string      $app_id    A UUID provided by the application to uniquely identify it.
	 *         @type string      $name      The name of the appkey.
	 *         @type string      $password  A one-way hash of the password.
	 *         @type int         $created   Unix timestamp of when the password was created.
	 *         @type int|null    $last_used The Unix timestamp of the GMT date the appkey was last used.
	 *         @type string|null $last_ip   上次使用该Appkey的IP地址。
	 *     }
	 * }
	 */
	public static function get_user_appkeys( $user_id ) {
		$passwords = get_user_meta( $user_id, static::USERMETA_KEY_APPLICATION_PASSWORDS, true );

		if ( ! is_array( $passwords ) ) {
			return array();
		}

		$save = false;

		foreach ( $passwords as $i => $password ) {
			if ( ! isset( $password['uuid'] ) ) {
				$passwords[ $i ]['uuid'] = gc_generate_uuid4();
				$save                    = true;
			}
		}

		if ( $save ) {
			static::set_user_appkeys( $user_id, $passwords );
		}

		return $passwords;
	}

	/**
	 * Gets a user's appkey with the given UUID.
	 *
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's UUID.
	 * @return array|null The appkey if found, null otherwise.
	 */
	public static function get_user_appkey( $user_id, $uuid ) {
		$passwords = static::get_user_appkeys( $user_id );

		foreach ( $passwords as $password ) {
			if ( $password['uuid'] === $uuid ) {
				return $password;
			}
		}

		return null;
	}

	/**
	 * Checks if an appkey with the given name exists for this user.
	 *
	 *
	 * @param int    $user_id User ID.
	 * @param string $name    Application name.
	 * @return bool Whether the provided application name exists.
	 */
	public static function application_name_exists_for_user( $user_id, $name ) {
		$passwords = static::get_user_appkeys( $user_id );

		foreach ( $passwords as $password ) {
			if ( strtolower( $password['name'] ) === strtolower( $name ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Updates an appkey.
	 *
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's UUID.
	 * @param array  $update  Information about the appkey to update.
	 * @return true|GC_Error True if successful, otherwise a GC_Error instance is returned on error.
	 */
	public static function update_appkey( $user_id, $uuid, $update = array() ) {
		$passwords = static::get_user_appkeys( $user_id );

		foreach ( $passwords as &$item ) {
			if ( $item['uuid'] !== $uuid ) {
				continue;
			}

			if ( ! empty( $update['name'] ) ) {
				$update['name'] = sanitize_text_field( $update['name'] );
			}

			$save = false;

			if ( ! empty( $update['name'] ) && $item['name'] !== $update['name'] ) {
				$item['name'] = $update['name'];
				$save         = true;
			}

			if ( $save ) {
				$saved = static::set_user_appkeys( $user_id, $passwords );

				if ( ! $saved ) {
					return new GC_Error( 'db_error', __( '无法保存Appkey。' ) );
				}
			}

			/**
			 * Fires when an appkey is updated.
			 *
		
			 *
			 * @param int   $user_id The user ID.
			 * @param array $item    The updated app password details.
			 * @param array $update  The information to update.
			 */
			do_action( 'gc_update_appkey', $user_id, $item, $update );

			return true;
		}

		return new GC_Error( 'appkey_not_found', __( '找不到具有该ID的Appkey。' ) );
	}

	/**
	 * Records that an appkey has been used.
	 *
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's UUID.
	 * @return true|GC_Error True if the usage was recorded, a GC_Error if an error occurs.
	 */
	public static function record_appkey_usage( $user_id, $uuid ) {
		$passwords = static::get_user_appkeys( $user_id );

		foreach ( $passwords as &$password ) {
			if ( $password['uuid'] !== $uuid ) {
				continue;
			}

			// Only record activity once a day.
			if ( $password['last_used'] + DAY_IN_SECONDS > time() ) {
				return true;
			}

			$password['last_used'] = time();
			$password['last_ip']   = $_SERVER['REMOTE_ADDR'];

			$saved = static::set_user_appkeys( $user_id, $passwords );

			if ( ! $saved ) {
				return new GC_Error( 'db_error', __( '无法保存Appkey。' ) );
			}

			return true;
		}

		// Specified AppKey not found!
		return new GC_Error( 'appkey_not_found', __( '找不到具有该ID的Appkey。' ) );
	}

	/**
	 * Deletes an appkey.
	 *
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's UUID.
	 * @return true|GC_Error Whether the password was successfully found and deleted, a GC_Error otherwise.
	 */
	public static function delete_appkey( $user_id, $uuid ) {
		$passwords = static::get_user_appkeys( $user_id );

		foreach ( $passwords as $key => $item ) {
			if ( $item['uuid'] === $uuid ) {
				unset( $passwords[ $key ] );
				$saved = static::set_user_appkeys( $user_id, $passwords );

				if ( ! $saved ) {
					return new GC_Error( 'db_error', __( '无法删除Appkey。' ) );
				}

				/**
				 * Fires when an appkey is deleted.
				 *
			
				 *
				 * @param int   $user_id The user ID.
				 * @param array $item    The data about the appkey.
				 */
				do_action( 'gc_delete_appkey', $user_id, $item );

				return true;
			}
		}

		return new GC_Error( 'appkey_not_found', __( '找不到具有该ID的Appkey。' ) );
	}

	/**
	 * Deletes all appkeys for the given user.
	 *
	 *
	 * @param int $user_id User ID.
	 * @return int|GC_Error The number of passwords that were deleted or a GC_Error on failure.
	 */
	public static function delete_all_appkeys( $user_id ) {
		$passwords = static::get_user_appkeys( $user_id );

		if ( $passwords ) {
			$saved = static::set_user_appkeys( $user_id, array() );

			if ( ! $saved ) {
				return new GC_Error( 'db_error', __( '无法删除Appkey。' ) );
			}

			foreach ( $passwords as $item ) {
				/** This action is documented in gc-includes/class-gc-appkeys.php */
				do_action( 'gc_delete_appkey', $user_id, $item );
			}

			return count( $passwords );
		}

		return 0;
	}

	/**
	 * Sets a user's appkeys.
	 *
	 *
	 * @param int   $user_id   User ID.
	 * @param array $passwords Appkeys.
	 *
	 * @return bool
	 */
	protected static function set_user_appkeys( $user_id, $passwords ) {
		return update_user_meta( $user_id, static::USERMETA_KEY_APPLICATION_PASSWORDS, $passwords );
	}

	/**
	 * Sanitizes and then splits a password into smaller chunks.
	 *
	 *
	 * @param string $raw_password The raw appkey.
	 * @return string The chunked password.
	 */
	public static function chunk_password( $raw_password ) {
		$raw_password = preg_replace( '/[^a-z\d]/i', '', $raw_password );

		return trim( chunk_split( $raw_password, 4, ' ' ) );
	}
}
