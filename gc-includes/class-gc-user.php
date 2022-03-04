<?php
/**
 * User API: GC_User class
 *
 * @package GeChiUI
 * @subpackage Users
 *
 */

/**
 * Core class used to implement the GC_User object.
 *
 *
 *
 * @property string $nickname
 * @property string $description
 * @property string $user_description
 * @property string $first_name
 * @property string $user_firstname
 * @property string $last_name
 * @property string $user_lastname
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property string $user_status
 * @property int    $user_level
 * @property string $display_name
 * @property string $spam
 * @property string $deleted
 * @property string $locale
 * @property string $rich_editing
 * @property string $syntax_highlighting
 * @property string $use_ssl
 */
class GC_User {
	/**
	 * User data container.
	 *
	 * @var stdClass
	 */
	public $data;

	/**
	 * The user's ID.
	 *
	 * @var int
	 */
	public $ID = 0;

	/**
	 * Capabilities that the individual user has been granted outside of those inherited from their role.
	 *
	 * @var bool[] Array of key/value pairs where keys represent a capability name
	 *             and boolean values represent whether the user has that capability.
	 */
	public $caps = array();

	/**
	 * User metadata option name.
	 *
	 * @var string
	 */
	public $cap_key;

	/**
	 * The roles the user is part of.
	 *
	 * @var string[]
	 */
	public $roles = array();

	/**
	 * All capabilities the user has, including individual and role based.
	 *
	 * @var bool[] Array of key/value pairs where keys represent a capability name
	 *             and boolean values represent whether the user has that capability.
	 */
	public $allcaps = array();

	/**
	 * The filter context applied to user data fields.
	 *
	 * @var string
	 */
	public $filter = null;

	/**
	 * The site ID the capabilities of this user are initialized for.
	 *
	 * @var int
	 */
	private $site_id = 0;

	/**
	 * @var array
	 */
	private static $back_compat_keys;

	/**
	 * Constructor.
	 *
	 * Retrieves the userdata and passes it to GC_User::init().
	 *
	 *
	 * @param int|string|stdClass|GC_User $id      User's ID, a GC_User object, or a user object from the DB.
	 * @param string                      $name    Optional. User's username
	 * @param int                         $site_id Optional Site ID, defaults to current site.
	 */
	public function __construct( $id = 0, $name = '', $site_id = '' ) {
		if ( ! isset( self::$back_compat_keys ) ) {
			$prefix                 = $GLOBALS['gcdb']->prefix;
			self::$back_compat_keys = array(
				'user_firstname'             => 'first_name',
				'user_lastname'              => 'last_name',
				'user_description'           => 'description',
				'user_level'                 => $prefix . 'user_level',
				$prefix . 'usersettings'     => $prefix . 'user-settings',
				$prefix . 'usersettingstime' => $prefix . 'user-settings-time',
			);
		}

		if ( $id instanceof GC_User ) {
			$this->init( $id->data, $site_id );
			return;
		} elseif ( is_object( $id ) ) {
			$this->init( $id, $site_id );
			return;
		}

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			$name = $id;
			$id   = 0;
		}

		if ( $id ) {
			$data = self::get_data_by( 'id', $id );
		} else {
			$data = self::get_data_by( 'login', $name );
		}

		if ( $data ) {
			$this->init( $data, $site_id );
		} else {
			$this->data = new stdClass;
		}
	}

	/**
	 * Sets up object properties, including capabilities.
	 *
	 *
	 * @param object $data    User DB row object.
	 * @param int    $site_id Optional. The site ID to initialize for.
	 */
	public function init( $data, $site_id = '' ) {
		if ( ! isset( $data->ID ) ) {
			$data->ID = 0;
		}
		$this->data = $data;
		$this->ID   = (int) $data->ID;

		$this->for_site( $site_id );
	}

	/**
	 * Return only the main user fields
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param string     $field The field to query against: 'id', 'ID', 'slug', 'email' or 'login'.
	 * @param string|int $value The field value
	 * @return object|false Raw user object
	 */
	public static function get_data_by( $field, $value ) {
		global $gcdb;

		// 'ID' is an alias of 'id'.
		if ( 'ID' === $field ) {
			$field = 'id';
		}

		if ( 'id' === $field ) {
			// Make sure the value is numeric to avoid casting objects, for example,
			// to int 1.
			if ( ! is_numeric( $value ) ) {
				return false;
			}
			$value = (int) $value;
			if ( $value < 1 ) {
				return false;
			}
		} else {
			$value = trim( $value );
		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id':
				$user_id  = $value;
				$db_field = 'ID';
				break;
			case 'slug':
				$user_id  = gc_cache_get( $value, 'userslugs' );
				$db_field = 'user_nicename';
				break;
			case 'email':
				$user_id  = gc_cache_get( $value, 'useremail' );
				$db_field = 'user_email';
				break;
			case 'mobile':
				$user_id  = gc_cache_get( $value, 'usermobile' );
				$db_field = 'user_mobile';
				break;
			case 'login':
				$value    = sanitize_user( $value );
				$user_id  = gc_cache_get( $value, 'userlogins' );
				$db_field = 'user_login';
				break;
			default:
				return false;
		}

		if ( false !== $user_id ) {
			$user = gc_cache_get( $user_id, 'users' );
			if ( $user ) {
				return $user;
			}
		}

		$user = $gcdb->get_row(
			$gcdb->prepare(
				"SELECT * FROM $gcdb->users WHERE $db_field = %s LIMIT 1",
				$value
			)
		);
		if ( ! $user ) {
			return false;
		}

		update_user_caches( $user );

		return $user;
	}


	//获取有效的Session
    public static function get_session($key){
    	return get_option( '_session_'.$key, array() );
    }
    
    //写入短信验证码的Session
    //键+值+过期时间点
    //GeChiUI本身不实现这个方法，而是给插件开发使用，例如短信验证码插件
    public static function set_session($key, $value, $expiry=null){
    	if(empty($expiry)) {
    		//过期时间
            $expiry = date('Y-m-d H:i:s',  strtotime(current_time( 'mysql' )) + (60*15));
        }
    	$session = array(
                'session_key'         => $key,
                'session_value'           => $value,
                'session_expiry'          => $expiry,
                'session_date' => current_time( 'mysql' )
            );

    	update_option('_session_'.$key, $session);
    }

	/**
	 * Magic method for checking the existence of a certain custom field.
	 *
	 *
	 * @param string $key User meta key to check if set.
	 * @return bool Whether the given user meta key is set.
	 */
	public function __isset( $key ) {
		if ( 'id' === $key ) {
			_deprecated_argument(
				'GC_User->id',
				'2.1.0',
				sprintf(
					/* translators: %s: GC_User->ID */
					__( '请改用%s。' ),
					'<code>GC_User->ID</code>'
				)
			);
			$key = 'ID';
		}

		if ( isset( $this->data->$key ) ) {
			return true;
		}

		if ( isset( self::$back_compat_keys[ $key ] ) ) {
			$key = self::$back_compat_keys[ $key ];
		}

		return metadata_exists( 'user', $this->ID, $key );
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 *
	 * @param string $key User meta key to retrieve.
	 * @return mixed Value of the given user meta key (if set). If `$key` is 'id', the user ID.
	 */
	public function __get( $key ) {
		if ( 'id' === $key ) {
			_deprecated_argument(
				'GC_User->id',
				'2.1.0',
				sprintf(
					/* translators: %s: GC_User->ID */
					__( '请改用%s。' ),
					'<code>GC_User->ID</code>'
				)
			);
			return $this->ID;
		}

		if ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		} else {
			if ( isset( self::$back_compat_keys[ $key ] ) ) {
				$key = self::$back_compat_keys[ $key ];
			}
			$value = get_user_meta( $this->ID, $key, true );
		}

		if ( $this->filter ) {
			$value = sanitize_user_field( $key, $value, $this->ID, $this->filter );
		}

		return $value;
	}

	/**
	 * Magic method for setting custom user fields.
	 *
	 * This method does not update custom fields in the database. It only stores
	 * the value on the GC_User instance.
	 *
	 *
	 * @param string $key   User meta key.
	 * @param mixed  $value User meta value.
	 */
	public function __set( $key, $value ) {
		if ( 'id' === $key ) {
			_deprecated_argument(
				'GC_User->id',
				'2.1.0',
				sprintf(
					/* translators: %s: GC_User->ID */
					__( '请改用%s。' ),
					'<code>GC_User->ID</code>'
				)
			);
			$this->ID = $value;
			return;
		}

		$this->data->$key = $value;
	}

	/**
	 * Magic method for unsetting a certain custom field.
	 *
	 *
	 * @param string $key User meta key to unset.
	 */
	public function __unset( $key ) {
		if ( 'id' === $key ) {
			_deprecated_argument(
				'GC_User->id',
				'2.1.0',
				sprintf(
					/* translators: %s: GC_User->ID */
					__( '请改用%s。' ),
					'<code>GC_User->ID</code>'
				)
			);
		}

		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}

		if ( isset( self::$back_compat_keys[ $key ] ) ) {
			unset( self::$back_compat_keys[ $key ] );
		}
	}

	/**
	 * Determine whether the user exists in the database.
	 *
	 *
	 * @return bool True if user exists in the database, false if not.
	 */
	public function exists() {
		return ! empty( $this->ID );
	}

	/**
	 * Retrieve the value of a property or meta key.
	 *
	 * Retrieves from the users and usermeta table.
	 *
	 *
	 * @param string $key Property
	 * @return mixed
	 */
	public function get( $key ) {
		return $this->__get( $key );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the users and usermeta tables.
	 *
	 *
	 * @param string $key Property
	 * @return bool
	 */
	public function has_prop( $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Return an array representation.
	 *
	 *
	 * @return array Array representation.
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}

	/**
	 * Makes private/protected methods readable for backward compatibility.
	 *
	 *
	 * @param string $name      Method to call.
	 * @param array  $arguments Arguments to pass when calling.
	 * @return mixed|false Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( '_init_caps' === $name ) {
			return $this->_init_caps( ...$arguments );
		}
		return false;
	}

	/**
	 * Set up capability object properties.
	 *
	 * Will set the value for the 'cap_key' property to current database table
	 * prefix, followed by 'capabilities'. Will then check to see if the
	 * property matching the 'cap_key' exists and is an array. If so, it will be
	 * used.
	 *
	 * @deprecated 4.9.0 Use GC_User::for_site()
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param string $cap_key Optional capability key
	 */
	protected function _init_caps( $cap_key = '' ) {
		global $gcdb;

		_deprecated_function( __METHOD__, '4.9.0', 'GC_User::for_site()' );

		if ( empty( $cap_key ) ) {
			$this->cap_key = $gcdb->get_blog_prefix( $this->site_id ) . 'capabilities';
		} else {
			$this->cap_key = $cap_key;
		}

		$this->caps = $this->get_caps_data();

		$this->get_role_caps();
	}

	/**
	 * Retrieves all of the capabilities of the user's roles, and merges them with
	 * individual user capabilities.
	 *
	 * All of the capabilities of the user's roles are merged with the user's individual
	 * capabilities. This means that the user can be denied specific capabilities that
	 * their role might have, but the user is specifically denied.
	 *
	 *
	 * @return bool[] Array of key/value pairs where keys represent a capability name
	 *                and boolean values represent whether the user has that capability.
	 */
	public function get_role_caps() {
		$switch_site = false;
		if ( is_multisite() && get_current_blog_id() != $this->site_id ) {
			$switch_site = true;

			switch_to_blog( $this->site_id );
		}

		$gc_roles = gc_roles();

		// Filter out caps that are not role names and assign to $this->roles.
		if ( is_array( $this->caps ) ) {
			$this->roles = array_filter( array_keys( $this->caps ), array( $gc_roles, 'is_role' ) );
		}

		// Build $allcaps from role caps, overlay user's $caps.
		$this->allcaps = array();
		foreach ( (array) $this->roles as $role ) {
			$the_role      = $gc_roles->get_role( $role );
			$this->allcaps = array_merge( (array) $this->allcaps, (array) $the_role->capabilities );
		}
		$this->allcaps = array_merge( (array) $this->allcaps, (array) $this->caps );

		if ( $switch_site ) {
			restore_current_blog();
		}

		return $this->allcaps;
	}

	/**
	 * Add role to user.
	 *
	 * Updates the user's meta data option with capabilities and roles.
	 *
	 *
	 * @param string $role Role name.
	 */
	public function add_role( $role ) {
		if ( empty( $role ) ) {
			return;
		}

		$this->caps[ $role ] = true;
		update_user_meta( $this->ID, $this->cap_key, $this->caps );
		$this->get_role_caps();
		$this->update_user_level_from_caps();

		/**
		 * Fires immediately after the user has been given a new role.
		 *
		 *
		 * @param int    $user_id The user ID.
		 * @param string $role    The new role.
		 */
		do_action( 'add_user_role', $this->ID, $role );
	}

	/**
	 * Remove role from user.
	 *
	 *
	 * @param string $role Role name.
	 */
	public function remove_role( $role ) {
		if ( ! in_array( $role, $this->roles, true ) ) {
			return;
		}
		unset( $this->caps[ $role ] );
		update_user_meta( $this->ID, $this->cap_key, $this->caps );
		$this->get_role_caps();
		$this->update_user_level_from_caps();

		/**
		 * Fires immediately after a role as been removed from a user.
		 *
		 *
		 * @param int    $user_id The user ID.
		 * @param string $role    The removed role.
		 */
		do_action( 'remove_user_role', $this->ID, $role );
	}

	/**
	 * Set the role of the user.
	 *
	 * This will remove the previous roles of the user and assign the user the
	 * new one. You can set the role to an empty string and it will remove all
	 * of the roles from the user.
	 *
	 *
	 * @param string $role Role name.
	 */
	public function set_role( $role ) {
		if ( 1 === count( $this->roles ) && current( $this->roles ) == $role ) {
			return;
		}

		foreach ( (array) $this->roles as $oldrole ) {
			unset( $this->caps[ $oldrole ] );
		}

		$old_roles = $this->roles;
		if ( ! empty( $role ) ) {
			$this->caps[ $role ] = true;
			$this->roles         = array( $role => true );
		} else {
			$this->roles = false;
		}
		update_user_meta( $this->ID, $this->cap_key, $this->caps );
		$this->get_role_caps();
		$this->update_user_level_from_caps();

		/**
		 * Fires after the user's role has changed.
		 *
		 *
		 * @param int      $user_id   The user ID.
		 * @param string   $role      The new role.
		 * @param string[] $old_roles An array of the user's previous roles.
		 */
		do_action( 'set_user_role', $this->ID, $role, $old_roles );
	}

	/**
	 * Choose the maximum level the user has.
	 *
	 * Will compare the level from the $item parameter against the $max
	 * parameter. If the item is incorrect, then just the $max parameter value
	 * will be returned.
	 *
	 * Used to get the max level based on the capabilities the user has. This
	 * is also based on roles, so if the user is assigned the Administrator role
	 * then the capability 'level_10' will exist and the user will get that
	 * value.
	 *
	 *
	 * @param int    $max  Max level of user.
	 * @param string $item Level capability name.
	 * @return int Max Level.
	 */
	public function level_reduction( $max, $item ) {
		if ( preg_match( '/^level_(10|[0-9])$/i', $item, $matches ) ) {
			$level = (int) $matches[1];
			return max( $max, $level );
		} else {
			return $max;
		}
	}

	/**
	 * Update the maximum user level for the user.
	 *
	 * Updates the 'user_level' user metadata (includes prefix that is the
	 * database table prefix) with the maximum user level. Gets the value from
	 * the all of the capabilities that the user has.
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 */
	public function update_user_level_from_caps() {
		global $gcdb;
		$this->user_level = array_reduce( array_keys( $this->allcaps ), array( $this, 'level_reduction' ), 0 );
		update_user_meta( $this->ID, $gcdb->get_blog_prefix() . 'user_level', $this->user_level );
	}

	/**
	 * Add capability and grant or deny access to capability.
	 *
	 *
	 * @param string $cap   Capability name.
	 * @param bool   $grant Whether to grant capability to user.
	 */
	public function add_cap( $cap, $grant = true ) {
		$this->caps[ $cap ] = $grant;
		update_user_meta( $this->ID, $this->cap_key, $this->caps );
		$this->get_role_caps();
		$this->update_user_level_from_caps();
	}

	/**
	 * Remove capability from user.
	 *
	 *
	 * @param string $cap Capability name.
	 */
	public function remove_cap( $cap ) {
		if ( ! isset( $this->caps[ $cap ] ) ) {
			return;
		}
		unset( $this->caps[ $cap ] );
		update_user_meta( $this->ID, $this->cap_key, $this->caps );
		$this->get_role_caps();
		$this->update_user_level_from_caps();
	}

	/**
	 * Remove all of the capabilities of the user.
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 */
	public function remove_all_caps() {
		global $gcdb;
		$this->caps = array();
		delete_user_meta( $this->ID, $this->cap_key );
		delete_user_meta( $this->ID, $gcdb->get_blog_prefix() . 'user_level' );
		$this->get_role_caps();
	}

	/**
	 * Returns whether the user has the specified capability.
	 *
	 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
	 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     $user->has_cap( 'edit_posts' );
	 *     $user->has_cap( 'edit_post', $post->ID );
	 *     $user->has_cap( 'edit_post_meta', $post->ID, $meta_key );
	 *
	 * While checking against a role in place of a capability is supported in part, this practice is discouraged as it
	 * may produce unreliable results.
	 *
	 *              by adding it to the function signature.
	 *
	 * @see map_meta_cap()
	 *
	 * @param string $cap     Capability name.
	 * @param mixed  ...$args Optional further parameters, typically starting with an object ID.
	 * @return bool Whether the user has the given capability, or, if an object ID is passed, whether the user has
	 *              the given capability for that object.
	 */
	public function has_cap( $cap, ...$args ) {
		if ( is_numeric( $cap ) ) {
			_deprecated_argument( __FUNCTION__, '2.0.0', __( '用户级别已被废弃，请改用能力。' ) );
			$cap = $this->translate_level_to_cap( $cap );
		}

		$caps = map_meta_cap( $cap, $this->ID, ...$args );

		// Multisite super admin has all caps by definition, Unless specifically denied.
		if ( is_multisite() && is_super_admin( $this->ID ) ) {
			if ( in_array( 'do_not_allow', $caps, true ) ) {
				return false;
			}
			return true;
		}

		// Maintain BC for the argument passed to the "user_has_cap" filter.
		$args = array_merge( array( $cap, $this->ID ), $args );

		/**
		 * Dynamically filter a user's capabilities.
		 *
		 *
		 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name
		 *                          and boolean values represent whether the user has that capability.
		 * @param string[] $caps    Required primitive capabilities for the requested capability.
		 * @param array    $args {
		 *     Arguments that accompany the requested capability check.
		 *
		 *     @type string    $0 Requested capability.
		 *     @type int       $1 Concerned user ID.
		 *     @type mixed  ...$2 Optional second and further parameters, typically object ID.
		 * }
		 * @param GC_User  $user    The user object.
		 */
		$capabilities = apply_filters( 'user_has_cap', $this->allcaps, $caps, $args, $this );

		// Everyone is allowed to exist.
		$capabilities['exist'] = true;

		// Nobody is allowed to do things they are not allowed to do.
		unset( $capabilities['do_not_allow'] );

		// Must have ALL requested caps.
		foreach ( (array) $caps as $cap ) {
			if ( empty( $capabilities[ $cap ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Convert numeric level to level capability name.
	 *
	 * Prepends 'level_' to level number.
	 *
	 *
	 * @param int $level Level number, 1 to 10.
	 * @return string
	 */
	public function translate_level_to_cap( $level ) {
		return 'level_' . $level;
	}

	/**
	 * Set the site to operate on. Defaults to the current site.
	 *
	 * @deprecated 4.9.0 Use GC_User::for_site()
	 *
	 * @param int $blog_id Optional. Site ID, defaults to current site.
	 */
	public function for_blog( $blog_id = '' ) {
		_deprecated_function( __METHOD__, '4.9.0', 'GC_User::for_site()' );

		$this->for_site( $blog_id );
	}

	/**
	 * Sets the site to operate on. Defaults to the current site.
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param int $site_id Site ID to initialize user capabilities for. Default is the current site.
	 */
	public function for_site( $site_id = '' ) {
		global $gcdb;

		if ( ! empty( $site_id ) ) {
			$this->site_id = absint( $site_id );
		} else {
			$this->site_id = get_current_blog_id();
		}

		$this->cap_key = $gcdb->get_blog_prefix( $this->site_id ) . 'capabilities';

		$this->caps = $this->get_caps_data();

		$this->get_role_caps();
	}

	/**
	 * Gets the ID of the site for which the user's capabilities are currently initialized.
	 *
	 *
	 * @return int Site ID.
	 */
	public function get_site_id() {
		return $this->site_id;
	}

	/**
	 * Gets the available user capabilities data.
	 *
	 *
	 * @return bool[] List of capabilities keyed by the capability name,
	 *                e.g. array( 'edit_posts' => true, 'delete_posts' => false ).
	 */
	private function get_caps_data() {
		$caps = get_user_meta( $this->ID, $this->cap_key, true );

		if ( ! is_array( $caps ) ) {
			return array();
		}

		return $caps;
	}
}
