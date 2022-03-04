<?php
/**
 * Core User API
 *
 * @package GeChiUI
 * @subpackage Users
 */

/**
 * Authenticates and logs a user in with 'remember' capability.
 *
 * The credentials is an array that has 'user_login', 'user_password', and
 * 'remember' indices. If the credentials is not given, then the log in form
 * will be assumed and used if set.
 *
 * The various authentication cookies will be set by this function and will be
 * set for a longer period depending on if the 'remember' credential is set to
 * true.
 *
 * Note: gc_signon() doesn't handle setting the current user. This means that if the
 * function is called before the {@see 'init'} hook is fired, is_user_logged_in() will
 * evaluate as false until that point. If is_user_logged_in() is needed in conjunction
 * with gc_signon(), gc_set_current_user() should be called explicitly.
 *
 *
 *
 * @global string $auth_secure_cookie
 *
 * @param array       $credentials   Optional. User info in order to sign on.
 * @param string|bool $secure_cookie Optional. Whether to use secure cookie.
 * @return GC_User|GC_Error GC_User on success, GC_Error on failure.
 */
function gc_signon( $credentials = array(), $secure_cookie = '' ) {
	if ( empty( $credentials ) ) {
		$credentials = array(); // Back-compat for plugins passing an empty string.

		if ( ! empty( $_POST['log'] ) ) {
			$credentials['user_login'] = gc_unslash( $_POST['log'] );
		}
		if ( ! empty( $_POST['pwd'] ) ) {
			$credentials['user_password'] = $_POST['pwd'];
		}
		if ( ! empty( $_POST['rememberme'] ) ) {
			$credentials['remember'] = $_POST['rememberme'];
		}
	}

	if ( ! empty( $credentials['remember'] ) ) {
		$credentials['remember'] = true;
	} else {
		$credentials['remember'] = false;
	}

	/**
	 * Fires before the user is authenticated.
	 *
	 * The variables passed to the callbacks are passed by reference,
	 * and can be modified by callback functions.
	 *
	 *
	 * @todo Decide whether to deprecate the gc_authenticate action.
	 *
	 * @param string $user_login    Username (passed by reference).
	 * @param string $user_password User password (passed by reference).
	 */
	do_action_ref_array( 'gc_authenticate', array( &$credentials['user_login'], &$credentials['user_password'] ) );

	if ( '' === $secure_cookie ) {
		$secure_cookie = is_ssl();
	}

	/**
	 * Filters whether to use a secure sign-on cookie.
	 *
	 *
	 * @param bool  $secure_cookie Whether to use a secure sign-on cookie.
	 * @param array $credentials {
	 *     Array of entered sign-on data.
	 *
	 *     @type string $user_login    Username.
	 *     @type string $user_password Password entered.
	 *     @type bool   $remember      Whether to 'remember' the user. Increases the time
	 *                                 that the cookie will be kept. Default false.
	 * }
	 */
	$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );

	global $auth_secure_cookie; // XXX ugly hack to pass this to gc_authenticate_cookie().
	$auth_secure_cookie = $secure_cookie;

	add_filter( 'authenticate', 'gc_authenticate_cookie', 30, 3 );

	$user = gc_authenticate( $credentials['user_login'], $credentials['user_password'] );

	if ( is_gc_error( $user ) ) {
		return $user;
	}

	gc_set_auth_cookie( $user->ID, $credentials['remember'], $secure_cookie );
	/**
	 * Fires after the user has successfully logged in.
	 *
	 *
	 * @param string  $user_login Username.
	 * @param GC_User $user       GC_User object of the logged-in user.
	 */
	do_action( 'gc_login', $user->user_login, $user );
	return $user;
}

/*
*  使用“记住”功能对用户进行身份验证和登录。
* 凭据是一个具有“手机号”、“验证码”和“记住”索引的数组。如果未提供凭据，则将假定并使用登录表单（如果已设置）。
*/
function gc_signon_mobile( $credentials = array(), $secure_cookie = '' ) {
    
	if ( empty( $credentials ) ) {
		$credentials = array(); // Back-compat for plugins passing an empty string.

		if ( ! empty( $_POST['user_mobile'] ) ) {
			$credentials['user_mobile'] = gc_unslash( $_POST['user_mobile'] );
		}
		if ( ! empty( $_POST['sms_code'] ) ) {
			$credentials['sms_code'] = $_POST['sms_code'];
		}
		if ( ! empty( $_POST['rememberme'] ) ) {
			$credentials['remember'] = $_POST['rememberme'];
		}
	}

	if ( ! empty( $credentials['remember'] ) ) {
		$credentials['remember'] = true;
	} else {
		$credentials['remember'] = false;
	}

	/**
	 * Fires before the user is authenticated.
	 *
	 * The variables passed to the callbacks are passed by reference,
	 * and can be modified by callback functions.
	 * 
	 *
	 * @todo Decide whether to deprecate the gc_authenticate action.
	 *
	 * @param string $user_mobile    Username (passed by reference).
	 * @param string $sms_code User password (passed by reference).
	 */
	do_action_ref_array( 'gc_authenticate', array( &$credentials['user_mobile'], &$credentials['sms_code'] ) );

	if ( '' === $secure_cookie ) {
		$secure_cookie = is_ssl();
	}

	/**
	 * Filters whether to use a secure sign-on cookie.
	 * 
	 *
	 * @param bool  $secure_cookie Whether to use a secure sign-on cookie.
	 * @param array $credentials {
	 *     Array of entered sign-on data.
	 *
	 *     @type string $user_mobile    Username.
	 *     @type string $sms_code Password entered.
	 *     @type bool   $remember      Whether to 'remember' the user. Increases the time
	 *                                 that the cookie will be kept. Default false.
	 * }
	 */
	$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );

	global $auth_secure_cookie; // XXX ugly hack to pass this to gc_authenticate_cookie().
	$auth_secure_cookie = $secure_cookie;

	add_filter( 'authenticate', 'gc_authenticate_cookie', 30, 3 );

	$user = gc_authenticate_mobile( $credentials['user_mobile'], $credentials['sms_code'] );

	if ( is_gc_error( $user ) ) {
		return $user;
	}
    
	gc_set_auth_cookie( $user->ID, $credentials['remember'], $secure_cookie );
	/**
	 * Fires after the user has successfully logged in.
	 * 
	 *
	 * @param string  $user_login Username.
	 * @param GC_User $user       GC_User object of the logged-in user.
	 */
	do_action( 'gc_login', $user->user_login, $user );
	return $user;
}


/**
 * Authenticate a user, confirming the username and password are valid.
 *
 *
 *
 * @param GC_User|GC_Error|null $user     GC_User or GC_Error object from a previous callback. Default null.
 * @param string                $username Username for authentication.
 * @param string                $password Password for authentication.
 * @return GC_User|GC_Error GC_User on success, GC_Error on failure.
 */
function gc_authenticate_username_password( $user, $username, $password ) {
	if ( $user instanceof GC_User ) {
		return $user;
	}

	if ( empty( $username ) || empty( $password ) ) {
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$error = new GC_Error();

		if ( empty( $username ) ) {
			$error->add( 'empty_username', __( '<strong>错误</strong>：用户名字段为空。' ) );
		}

		if ( empty( $password ) ) {
			$error->add( 'empty_password', __( '<strong>错误</strong>：密码字段为空。' ) );
		}

		return $error;
	}

	$user = get_user_by( 'login', $username );

	if ( ! $user ) {
		return new GC_Error(
			'invalid_username',
			sprintf(
				/* translators: %s: User name. */
				__( '<strong>错误：</strong>用户名<strong>%s</strong>未在本站点注册。如果您不确定您的用户名，请改用电子邮箱进行尝试。' ),
				$username
			)
		);
	}

	/**
	 * Filters whether the given user can be authenticated with the provided $password.
	 *
	 *
	 * @param GC_User|GC_Error $user     GC_User or GC_Error object if a previous
	 *                                   callback failed authentication.
	 * @param string           $password Password to check against the user.
	 */
	$user = apply_filters( 'gc_authenticate_user', $user, $password );
	if ( is_gc_error( $user ) ) {
		return $user;
	}

	if ( ! gc_check_password( $password, $user->user_pass, $user->ID ) ) {
		return new GC_Error(
			'incorrect_password',
			sprintf(
				/* translators: %s: User name. */
				__( '<strong>错误</strong>：为用户名%s指定的密码不正确。' ),
				'<strong>' . $username . '</strong>'
			) .
			' <a href="' . gc_lostpassword_url() . '">' .
			__( '忘记密码？' ) .
			'</a>'
		);
	}

	return $user;
}

/**
 * Authenticates a user using the email and password.
 *
 *
 *
 * @param GC_User|GC_Error|null $user     GC_User or GC_Error object if a previous
 *                                        callback failed authentication.
 * @param string                $email    Email address for authentication.
 * @param string                $password Password for authentication.
 * @return GC_User|GC_Error GC_User on success, GC_Error on failure.
 */
function gc_authenticate_email_password( $user, $email, $password ) {
	if ( $user instanceof GC_User ) {
		return $user;
	}

	if ( empty( $email ) || empty( $password ) ) {
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$error = new GC_Error();

		if ( empty( $email ) ) {
			// Uses 'empty_username' for back-compat with gc_signon().
			$error->add( 'empty_username', __( '<strong>错误</strong>：电子邮箱字段为空。' ) );
		}

		if ( empty( $password ) ) {
			$error->add( 'empty_password', __( '<strong>错误</strong>：密码字段为空。' ) );
		}

		return $error;
	}

	if ( ! is_email( $email ) ) {
		return $user;
	}

	$user = get_user_by( 'email', $email );

	if ( ! $user ) {
		return new GC_Error(
			'invalid_email',
			__( '未知的电子邮箱，请检查或改用用户名。' )
		);
	}

	/** This filter is documented in gc-includes/user.php */
	$user = apply_filters( 'gc_authenticate_user', $user, $password );

	if ( is_gc_error( $user ) ) {
		return $user;
	}

	if ( ! gc_check_password( $password, $user->user_pass, $user->ID ) ) {
		return new GC_Error(
			'incorrect_password',
			sprintf(
				/* translators: %s: Email address. */
				__( '<strong>错误</strong>：您为电子邮箱%s输入的密码不正确。' ),
				'<strong>' . $email . '</strong>'
			) .
			' <a href="' . gc_lostpassword_url() . '">' .
			__( '忘记密码？' ) .
			'</a>'
		);
	}

	return $user;
}

//手机号+密码核验
function gc_authenticate_mobile_password( $user, $mobile, $password ) {
	if ( $user instanceof GC_User ) {
		return $user;
	}

	if ( empty( $mobile ) || empty( $password ) ) {
		if ( is_gc_error( $user ) ) {
			return $user;
		}

		$error = new GC_Error();

		if ( empty( $mobile ) ) {
			// Uses 'empty_username' for back-compat with gc_signon().
			$error->add( 'empty_usermobile', __('<strong>错误</strong>：请输入手机号。') );
		}

		if ( empty( $password ) ) {
			$error->add( 'empty_password', __('<strong>错误</strong>：密码字段为空。') );
		}

		return $error;
	}
    
    //验证手机号格式
    //如果不是手机号，可能是使用其他方式（用户名或邮箱）验证
    $error = validate_usermobile( $mobile );
    if (  is_gc_error( $error ) ) {
        return $user;
	} 

	$user = get_user_by( 'mobile', $mobile ); //基于手机自动找用户

	if ( ! $user ) {
		return new GC_Error(
			'user_mobile',
            __( '手机号未注册。再次检查或尝试您的电子邮箱。' )
		);
	}

	if ( ! gc_check_password( $password, $user->user_pass, $user->ID ) ) {
		return new GC_Error( 
            'incorrect_password', 
            sprintf(
				/* translators: %s: Email address. */
				__( '<strong>错误</strong>：您为手机号输入的密码 %s 不正确。' ),
				'<strong>' . $mobile . '</strong>'
			) .
			' <a href="' . gc_lostpassword_url() . '">' .
			__( '忘记密码？' ) .
			'</a>'         
        );
	}
	return $user;
}

//手机号+短信验证码核验
function gc_authenticate_mobile_smscode( $user, $mobile, $smscode ) {
	if ( $user instanceof GC_User ) {
		return $user;
	}

	if ( empty( $mobile ) || empty( $smscode ) ) {
		if ( is_gc_error( $user ) ) {
			return $user;
		}
	}
    
    //短信验证码核验
    $error = validate_smscode($mobile, $smscode);
    if(is_gc_error($error)) {
        return $error;
    }
    
    $user = get_user_by( 'mobile', $mobile ); //基于手机自动找用户

	if ( ! $user ) {
		return new GC_Error( 'invalid_usermobile',  __( '未注册的手机号。再次检查或尝试您的更换手机号。' ) );
	}

	return $user;
}

/**
 * Authenticate the user using the GeChiUI auth cookie.
 *
 *
 *
 * @global string $auth_secure_cookie
 *
 * @param GC_User|GC_Error|null $user     GC_User or GC_Error object from a previous callback. Default null.
 * @param string                $username Username. If not empty, cancels the cookie authentication.
 * @param string                $password Password. If not empty, cancels the cookie authentication.
 * @return GC_User|GC_Error GC_User on success, GC_Error on failure.
 */
function gc_authenticate_cookie( $user, $username, $password ) {
	if ( $user instanceof GC_User ) {
		return $user;
	}

	if ( empty( $username ) && empty( $password ) ) {
		$user_id = gc_validate_auth_cookie();
		if ( $user_id ) {
			return new GC_User( $user_id );
		}

		global $auth_secure_cookie;

		if ( $auth_secure_cookie ) {
			$auth_cookie = SECURE_AUTH_COOKIE;
		} else {
			$auth_cookie = AUTH_COOKIE;
		}

		if ( ! empty( $_COOKIE[ $auth_cookie ] ) ) {
			return new GC_Error( 'expired_session', __( '请重新登录。' ) );
		}

		// If the cookie is not set, be silent.
	}

	return $user;
}

/**
 * Authenticates the user using an appkey.
 *
 *
 *
 * @param GC_User|GC_Error|null $input_user GC_User or GC_Error object if a previous
 *                                          callback failed authentication.
 * @param string                $username   Username for authentication.
 * @param string                $password   Password for authentication.
 * @return GC_User|GC_Error|null GC_User on success, GC_Error on failure, null if
 *                               null is passed in and this isn't an API request.
 */
function gc_authenticate_appkey( $input_user, $username, $password ) {
	if ( $input_user instanceof GC_User ) {
		return $input_user;
	}

	if ( ! GC_AppKeys::is_in_use() ) {
		return $input_user;
	}

	$is_api_request = ( ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) );

	/**
	 * Filters whether this is an API request that AppKeys can be used on.
	 *
	 * By default, AppKeys is available for the REST API and XML-RPC.
	 *
	 *
	 * @param bool $is_api_request If this is an acceptable API request.
	 */
	$is_api_request = apply_filters( 'appkey_is_api_request', $is_api_request );

	if ( ! $is_api_request ) {
		return $input_user;
	}

	$error = null;
	$user  = get_user_by( 'login', $username );

	if ( ! $user && is_email( $username ) ) {
		$user = get_user_by( 'email', $username );
	}

	// If the login name is invalid, short circuit.
	if ( ! $user ) {
		if ( is_email( $username ) ) {
			$error = new GC_Error(
				'invalid_email',
				__( '<strong>错误：</strong>未知电子邮箱，请再次检查或改用您的用户名进行尝试。' )
			);
		} else {
			$error = new GC_Error(
				'invalid_username',
				__( '<strong>错误：</strong>未知用户名，请再次检查或改用您的电子邮箱进行尝试。' )
			);
		}
	} elseif ( ! gc_is_appkeys_available() ) {
		$error = new GC_Error(
			'appkeys_disabled',
			__( 'Appkey不可用。' )
		);
	} elseif ( ! gc_is_appkeys_available_for_user( $user ) ) {
		$error = new GC_Error(
			'appkeys_disabled_for_user',
			__( '您的账户无法使用Appkey。请联系站点管理员以获得协助。' )
		);
	}

	if ( $error ) {
		/**
		 * Fires when an appkey failed to authenticate the user.
		 *
		 *
		 * @param GC_Error $error The authentication error.
		 */
		do_action( 'appkey_failed_authentication', $error );

		return $error;
	}

	/*
	 * Strip out anything non-alphanumeric. This is so passwords can be used with
	 * or without spaces to indicate the groupings for readability.
	 *
	 * Generated appkeys are exclusively alphanumeric.
	 */
	$password = preg_replace( '/[^a-z\d]/i', '', $password );

	$hashed_passwords = GC_AppKeys::get_user_appkeys( $user->ID );

	foreach ( $hashed_passwords as $key => $item ) {
		if ( ! gc_check_password( $password, $item['password'], $user->ID ) ) {
			continue;
		}

		$error = new GC_Error();

		/**
		 * Fires when an appkey has been successfully checked as valid.
		 *
		 * This allows for plugins to add additional constraints to prevent an appkey from being used.
		 *
		 *
		 * @param GC_Error $error    The error object.
		 * @param GC_User  $user     The user authenticating.
		 * @param array    $item     The details about the appkey.
		 * @param string   $password The raw supplied password.
		 */
		do_action( 'gc_authenticate_appkey_errors', $error, $user, $item, $password );

		if ( is_gc_error( $error ) && $error->has_errors() ) {
			/** This action is documented in gc-includes/user.php */
			do_action( 'appkey_failed_authentication', $error );

			return $error;
		}

		GC_AppKeys::record_appkey_usage( $user->ID, $item['uuid'] );

		/**
		 * Fires after an appkey was used for authentication.
		 *
		 *
		 * @param GC_User $user The user who was authenticated.
		 * @param array   $item The appkey used.
		 */
		do_action( 'appkey_did_authenticate', $user, $item );

		return $user;
	}

	$error = new GC_Error(
		'incorrect_password',
		__( '提供的密码是无效的Appkey。' )
	);

	/** This action is documented in gc-includes/user.php */
	do_action( 'appkey_failed_authentication', $error );

	return $error;
}

/**
 * Validates the appkey credentials passed via Basic Authentication.
 *
 *
 *
 * @param int|false $input_user User ID if one has been determined, false otherwise.
 * @return int|false The authenticated user ID if successful, false otherwise.
 */
function gc_validate_appkey( $input_user ) {
	// Don't authenticate twice.
	if ( ! empty( $input_user ) ) {
		return $input_user;
	}

	if ( ! gc_is_appkeys_available() ) {
		return $input_user;
	}

	// Both $_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_PW'] must be set in order to attempt authentication.
	if ( ! isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
		return $input_user;
	}

	$authenticated = gc_authenticate_appkey( null, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );

	if ( $authenticated instanceof GC_User ) {
		return $authenticated->ID;
	}

	// If it wasn't a user what got returned, just pass on what we had received originally.
	return $input_user;
}

/**
 * For Multisite blogs, check if the authenticated user has been marked as a
 * spammer, or if the user's primary blog has been marked as spam.
 *
 *
 *
 * @param GC_User|GC_Error|null $user GC_User or GC_Error object from a previous callback. Default null.
 * @return GC_User|GC_Error GC_User on success, GC_Error if the user is considered a spammer.
 */
function gc_authenticate_spam_check( $user ) {
	if ( $user instanceof GC_User && is_multisite() ) {
		/**
		 * Filters whether the user has been marked as a spammer.
		 *
		 *
		 * @param bool    $spammed Whether the user is considered a spammer.
		 * @param GC_User $user    User to check against.
		 */
		$spammed = apply_filters( 'check_is_user_spammed', is_user_spammy( $user ), $user );

		if ( $spammed ) {
			return new GC_Error( 'spammer_account', __( '<strong>错误</strong>：您的账户已被标记为垃圾账户。' ) );
		}
	}
	return $user;
}

/**
 * Validates the logged-in cookie.
 *
 * Checks the logged-in cookie if the previous auth cookie could not be
 * validated and parsed.
 *
 * This is a callback for the {@see 'determine_current_user'} filter, rather than API.
 *
 *
 *
 * @param int|false $user_id The user ID (or false) as received from
 *                           the `determine_current_user` filter.
 * @return int|false User ID if validated, false otherwise. If a user ID from
 *                   an earlier filter callback is received, that value is returned.
 */
function gc_validate_logged_in_cookie( $user_id ) {
	if ( $user_id ) {
		return $user_id;
	}

	if ( is_blog_admin() || is_network_admin() || empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
		return false;
	}

	return gc_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
}

/**
 * Number of posts user has written.
 *
 *
 *
 *
 *              of post types to `$post_type`.
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int          $userid      User ID.
 * @param array|string $post_type   Optional. Single post type or array of post types to count the number of posts for. Default 'post'.
 * @param bool         $public_only Optional. Whether to only return counts for public posts. Default false.
 * @return string Number of posts the user has written in this post type.
 */
function count_user_posts( $userid, $post_type = 'post', $public_only = false ) {
	global $gcdb;

	$where = get_posts_by_author_sql( $post_type, true, $userid, $public_only );

	$count = $gcdb->get_var( "SELECT COUNT(*) FROM $gcdb->posts $where" );

	/**
	 * Filters the number of posts a user has written.
	 *
	 *
	 * @param int          $count       The user's post count.
	 * @param int          $userid      User ID.
	 * @param string|array $post_type   Single post type or array of post types to count the number of posts for.
	 * @param bool         $public_only Whether to limit counted posts to public posts.
	 */
	return apply_filters( 'get_usernumposts', $count, $userid, $post_type, $public_only );
}

/**
 * Number of posts written by a list of users.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int[]           $users       Array of user IDs.
 * @param string|string[] $post_type   Optional. Single post type or array of post types to check. Defaults to 'post'.
 * @param bool            $public_only Optional. Only return counts for public posts.  Defaults to false.
 * @return string[] Amount of posts each user has written, as strings, keyed by user ID.
 */
function count_many_users_posts( $users, $post_type = 'post', $public_only = false ) {
	global $gcdb;

	$count = array();
	if ( empty( $users ) || ! is_array( $users ) ) {
		return $count;
	}

	$userlist = implode( ',', array_map( 'absint', $users ) );
	$where    = get_posts_by_author_sql( $post_type, true, null, $public_only );

	$result = $gcdb->get_results( "SELECT post_author, COUNT(*) FROM $gcdb->posts $where AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) ) {
			$count[ $id ] = 0;
		}
	}

	return $count;
}

//
// User option functions.
//

/**
 * Get the current user's ID
 *
 * @since MU
 *
 * @return int The current user's ID, or 0 if no user is logged in.
 */
function get_current_user_id() {
	if ( ! function_exists( 'gc_get_current_user' ) ) {
		return 0;
	}
	$user = gc_get_current_user();
	return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}

/**
 * Retrieve user option that can be either per Site or per Network.
 *
 * If the user ID is not given, then the current user will be used instead. If
 * the user ID is given, then the user data will be retrieved. The filter for
 * the result, will also pass the original option name and finally the user data
 * object as the third parameter.
 *
 * The option will first check for the per site name and then the per Network name.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $option     User option name.
 * @param int    $user       Optional. User ID.
 * @param string $deprecated Use get_option() to check for an option in the options table.
 * @return mixed User option value on success, false on failure.
 */
function get_user_option( $option, $user = 0, $deprecated = '' ) {
	global $gcdb;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '3.0.0' );
	}

	if ( empty( $user ) ) {
		$user = get_current_user_id();
	}

	$user = get_userdata( $user );
	if ( ! $user ) {
		return false;
	}

	$prefix = $gcdb->get_blog_prefix();
	if ( $user->has_prop( $prefix . $option ) ) { // Blog-specific.
		$result = $user->get( $prefix . $option );
	} elseif ( $user->has_prop( $option ) ) { // User-specific and cross-blog.
		$result = $user->get( $option );
	} else {
		$result = false;
	}

	/**
	 * Filters a specific user option value.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the user option name.
	 *
	 *
	 * @param mixed   $result Value for the user's option.
	 * @param string  $option Name of the option being retrieved.
	 * @param GC_User $user   GC_User object of the user whose option is being retrieved.
	 */
	return apply_filters( "get_user_option_{$option}", $result, $option, $user );
}

/**
 * Update user option with global blog capability.
 *
 * User options are just like user metadata except that they have support for
 * global blog options. If the 'global' parameter is false, which it is by default
 * it will prepend the GeChiUI table prefix to the option name.
 *
 * Deletes the user option if $newvalue is empty.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $user_id     User ID.
 * @param string $option_name User option name.
 * @param mixed  $newvalue    User option value.
 * @param bool   $global      Optional. Whether option name is global or blog specific.
 *                            Default false (blog specific).
 * @return int|bool User meta ID if the option didn't exist, true on successful update,
 *                  false on failure.
 */
function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $gcdb;

	if ( ! $global ) {
		$option_name = $gcdb->get_blog_prefix() . $option_name;
	}

	return update_user_meta( $user_id, $option_name, $newvalue );
}

/**
 * Delete user option with global blog capability.
 *
 * User options are just like user metadata except that they have support for
 * global blog options. If the 'global' parameter is false, which it is by default
 * it will prepend the GeChiUI table prefix to the option name.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int    $user_id     User ID
 * @param string $option_name User option name.
 * @param bool   $global      Optional. Whether option name is global or blog specific.
 *                            Default false (blog specific).
 * @return bool True on success, false on failure.
 */
function delete_user_option( $user_id, $option_name, $global = false ) {
	global $gcdb;

	if ( ! $global ) {
		$option_name = $gcdb->get_blog_prefix() . $option_name;
	}
	return delete_user_meta( $user_id, $option_name );
}

/**
 * Retrieve list of users matching criteria.
 *
 *
 *
 * @see GC_User_Query
 *
 * @param array $args Optional. Arguments to retrieve users. See GC_User_Query::prepare_query()
 *                    for more information on accepted arguments.
 * @return array List of users.
 */
function get_users( $args = array() ) {

	$args                = gc_parse_args( $args );
	$args['count_total'] = false;

	$user_search = new GC_User_Query( $args );

	return (array) $user_search->get_results();
}

/**
 * List all the users of the site, with several options available.
 *
 *
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string $orderby       How to sort the users. Accepts 'nicename', 'email', 'url', 'registered',
 *                                 'user_nicename', 'user_email', 'user_url', 'user_registered', 'name',
 *                                 'display_name', 'post_count', 'ID', 'meta_value', 'user_login'. Default 'name'.
 *     @type string $order         Sorting direction for $orderby. Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type int    $number        Maximum users to return or display. Default empty (all users).
 *     @type bool   $exclude_admin Whether to exclude the 'admin' account, if it exists. Default false.
 *     @type bool   $show_fullname Whether to show the user's full name. Default false.
 *     @type string $feed          If not empty, show a link to the user's feed and use this text as the alt
 *                                 parameter of the link. Default empty.
 *     @type string $feed_image    If not empty, show a link to the user's feed and use this image URL as
 *                                 clickable anchor. Default empty.
 *     @type string $feed_type     The feed type to link to, such as 'rss2'. Defaults to default feed type.
 *     @type bool   $echo          Whether to output the result or instead return it. Default true.
 *     @type string $style         If 'list', each user is wrapped in an `<li>` element, otherwise the users
 *                                 will be separated by commas.
 *     @type bool   $html          Whether to list the items in HTML form or plaintext. Default true.
 *     @type string $exclude       An array, comma-, or space-separated list of user IDs to exclude. Default empty.
 *     @type string $include       An array, comma-, or space-separated list of user IDs to include. Default empty.
 * }
 * @return string|null The output if echo is false. Otherwise null.
 */
function gc_list_users( $args = array() ) {
	$defaults = array(
		'orderby'       => 'name',
		'order'         => 'ASC',
		'number'        => '',
		'exclude_admin' => true,
		'show_fullname' => false,
		'feed'          => '',
		'feed_image'    => '',
		'feed_type'     => '',
		'echo'          => true,
		'style'         => 'list',
		'html'          => true,
		'exclude'       => '',
		'include'       => '',
	);

	$args = gc_parse_args( $args, $defaults );

	$return = '';

	$query_args           = gc_array_slice_assoc( $args, array( 'orderby', 'order', 'number', 'exclude', 'include' ) );
	$query_args['fields'] = 'ids';
	$users                = get_users( $query_args );

	foreach ( $users as $user_id ) {
		$user = get_userdata( $user_id );

		if ( $args['exclude_admin'] && 'admin' === $user->display_name ) {
			continue;
		}

		if ( $args['show_fullname'] && '' !== $user->first_name && '' !== $user->last_name ) {
			$name = "$user->first_name $user->last_name";
		} else {
			$name = $user->display_name;
		}

		if ( ! $args['html'] ) {
			$return .= $name . ', ';

			continue; // No need to go further to process HTML.
		}

		if ( 'list' === $args['style'] ) {
			$return .= '<li>';
		}

		$row = $name;

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$row .= ' ';
			if ( empty( $args['feed_image'] ) ) {
				$row .= '(';
			}

			$row .= '<a href="' . get_author_feed_link( $user->ID, $args['feed_type'] ) . '"';

			$alt = '';
			if ( ! empty( $args['feed'] ) ) {
				$alt  = ' alt="' . esc_attr( $args['feed'] ) . '"';
				$name = $args['feed'];
			}

			$row .= '>';

			if ( ! empty( $args['feed_image'] ) ) {
				$row .= '<img src="' . esc_url( $args['feed_image'] ) . '" style="border: none;"' . $alt . ' />';
			} else {
				$row .= $name;
			}

			$row .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$row .= ')';
			}
		}

		$return .= $row;
		$return .= ( 'list' === $args['style'] ) ? '</li>' : ', ';
	}

	$return = rtrim( $return, ', ' );

	if ( ! $args['echo'] ) {
		return $return;
	}
	echo $return;
}

/**
 * Get the sites a user belongs to.
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int  $user_id User ID
 * @param bool $all     Whether to retrieve all sites, or only sites that are not
 *                      marked as deleted, archived, or spam.
 * @return object[] A list of the user's sites. An empty array if the user doesn't exist
 *                  or belongs to no sites.
 */
function get_blogs_of_user( $user_id, $all = false ) {
	global $gcdb;

	$user_id = (int) $user_id;

	// Logged out users can't have sites.
	if ( empty( $user_id ) ) {
		return array();
	}

	/**
	 * Filters the list of a user's sites before it is populated.
	 *
	 * Returning a non-null value from the filter will effectively short circuit
	 * get_blogs_of_user(), returning that value instead.
	 *
	 *
	 * @param null|object[] $sites   An array of site objects of which the user is a member.
	 * @param int           $user_id User ID.
	 * @param bool          $all     Whether the returned array should contain all sites, including
	 *                               those marked 'deleted', 'archived', or 'spam'. Default false.
	 */
	$sites = apply_filters( 'pre_get_blogs_of_user', null, $user_id, $all );

	if ( null !== $sites ) {
		return $sites;
	}

	$keys = get_user_meta( $user_id );
	if ( empty( $keys ) ) {
		return array();
	}

	if ( ! is_multisite() ) {
		$site_id                        = get_current_blog_id();
		$sites                          = array( $site_id => new stdClass );
		$sites[ $site_id ]->userblog_id = $site_id;
		$sites[ $site_id ]->blogname    = get_option( 'blogname' );
		$sites[ $site_id ]->domain      = '';
		$sites[ $site_id ]->path        = '';
		$sites[ $site_id ]->site_id     = 1;
		$sites[ $site_id ]->siteurl     = get_option( 'siteurl' );
		$sites[ $site_id ]->archived    = 0;
		$sites[ $site_id ]->spam        = 0;
		$sites[ $site_id ]->deleted     = 0;
		return $sites;
	}

	$site_ids = array();

	if ( isset( $keys[ $gcdb->base_prefix . 'capabilities' ] ) && defined( 'MULTISITE' ) ) {
		$site_ids[] = 1;
		unset( $keys[ $gcdb->base_prefix . 'capabilities' ] );
	}

	$keys = array_keys( $keys );

	foreach ( $keys as $key ) {
		if ( 'capabilities' !== substr( $key, -12 ) ) {
			continue;
		}
		if ( $gcdb->base_prefix && 0 !== strpos( $key, $gcdb->base_prefix ) ) {
			continue;
		}
		$site_id = str_replace( array( $gcdb->base_prefix, '_capabilities' ), '', $key );
		if ( ! is_numeric( $site_id ) ) {
			continue;
		}

		$site_ids[] = (int) $site_id;
	}

	$sites = array();

	if ( ! empty( $site_ids ) ) {
		$args = array(
			'number'                 => '',
			'site__in'               => $site_ids,
			'update_site_meta_cache' => false,
		);
		if ( ! $all ) {
			$args['archived'] = 0;
			$args['spam']     = 0;
			$args['deleted']  = 0;
		}

		$_sites = get_sites( $args );

		foreach ( $_sites as $site ) {
			$sites[ $site->id ] = (object) array(
				'userblog_id' => $site->id,
				'blogname'    => $site->blogname,
				'domain'      => $site->domain,
				'path'        => $site->path,
				'site_id'     => $site->network_id,
				'siteurl'     => $site->siteurl,
				'archived'    => $site->archived,
				'mature'      => $site->mature,
				'spam'        => $site->spam,
				'deleted'     => $site->deleted,
			);
		}
	}

	/**
	 * Filters the list of sites a user belongs to.
	 *
	 * @since MU
	 *
	 * @param object[] $sites   An array of site objects belonging to the user.
	 * @param int      $user_id User ID.
	 * @param bool     $all     Whether the returned sites array should contain all sites, including
	 *                          those marked 'deleted', 'archived', or 'spam'. Default false.
	 */
	return apply_filters( 'get_blogs_of_user', $sites, $user_id, $all );
}

/**
 * Find out whether a user is a member of a given blog.
 *
 * @since MU
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $user_id Optional. The unique ID of the user. Defaults to the current user.
 * @param int $blog_id Optional. ID of the blog to check. Defaults to the current site.
 * @return bool
 */
function is_user_member_of_blog( $user_id = 0, $blog_id = 0 ) {
	global $gcdb;

	$user_id = (int) $user_id;
	$blog_id = (int) $blog_id;

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Technically not needed, but does save calls to get_site() and get_user_meta()
	// in the event that the function is called when a user isn't logged in.
	if ( empty( $user_id ) ) {
		return false;
	} else {
		$user = get_userdata( $user_id );
		if ( ! $user instanceof GC_User ) {
			return false;
		}
	}

	if ( ! is_multisite() ) {
		return true;
	}

	if ( empty( $blog_id ) ) {
		$blog_id = get_current_blog_id();
	}

	$blog = get_site( $blog_id );

	if ( ! $blog || ! isset( $blog->domain ) || $blog->archived || $blog->spam || $blog->deleted ) {
		return false;
	}

	$keys = get_user_meta( $user_id );
	if ( empty( $keys ) ) {
		return false;
	}

	// No underscore before capabilities in $base_capabilities_key.
	$base_capabilities_key = $gcdb->base_prefix . 'capabilities';
	$site_capabilities_key = $gcdb->base_prefix . $blog_id . '_capabilities';

	if ( isset( $keys[ $base_capabilities_key ] ) && 1 == $blog_id ) {
		return true;
	}

	if ( isset( $keys[ $site_capabilities_key ] ) ) {
		return true;
	}

	return false;
}

/**
 * Adds meta data to a user.
 *
 *
 *
 * @param int    $user_id    User ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function add_user_meta( $user_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'user', $user_id, $meta_key, $meta_value, $unique );
}

/**
 * Remove metadata matching criteria from a user.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 *
 *
 * @link https://developer.gechiui.com/reference/functions/delete_user_meta/
 *
 * @param int    $user_id    User ID
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. If provided,
 *                           rows will only be removed that match the value.
 *                           Must be serializable if non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'user', $user_id, $meta_key, $meta_value );
}

/**
 * Retrieve user meta field for a user.
 *
 *
 *
 * @link https://developer.gechiui.com/reference/functions/get_user_meta/
 *
 * @param int    $user_id User ID.
 * @param string $key     Optional. The meta key to retrieve. By default,
 *                        returns data for all keys.
 * @param bool   $single  Optional. Whether to return a single value.
 *                        This parameter has no effect if `$key` is not specified.
 *                        Default false.
 * @return mixed An array of values if `$single` is false.
 *               The value of meta data field if `$single` is true.
 *               False for an invalid `$user_id` (non-numeric, zero, or negative value).
 *               An empty string if a valid but non-existing user ID is passed.
 */
function get_user_meta( $user_id, $key = '', $single = false ) {
	return get_metadata( 'user', $user_id, $key, $single );
}

/**
 * Update user meta field based on user ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and user ID.
 *
 * If the meta field for the user does not exist, it will be added.
 *
 *
 *
 * @link https://developer.gechiui.com/reference/functions/update_user_meta/
 *
 * @param int    $user_id    User ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before updating.
 *                           If specified, only update existing metadata entries with
 *                           this value. Otherwise, update all entries. Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure or if the value passed to the function
 *                  is the same as the one that is already in the database.
 */
function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'user', $user_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Count number of users who have each of the user roles.
 *
 * Assumes there are neither duplicated nor orphaned capabilities meta_values.
 * Assumes role names are unique phrases. Same assumption made by GC_User_Query::prepare_query()
 * Using $strategy = 'time' this is CPU-intensive and should handle around 10^7 users.
 * Using $strategy = 'memory' this is memory-intensive and should handle around 10^5 users, but see GC Bug #12257.
 *
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string   $strategy Optional. The computational strategy to use when counting the users.
 *                           Accepts either 'time' or 'memory'. Default 'time'.
 * @param int|null $site_id  Optional. The site ID to count users for. Defaults to the current site.
 * @return array {
 *     User counts.
 *
 *     @type int   $total_users Total number of users on the site.
 *     @type int[] $avail_roles Array of user counts keyed by user role.
 * }
 */
function count_users( $strategy = 'time', $site_id = null ) {
	global $gcdb;

	// Initialize.
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	/**
	 * Filters the user count before queries are run.
	 *
	 * Return a non-null value to cause count_users() to return early.
	 *
	 *
	 * @param null|string $result   The value to return instead. Default null to continue with the query.
	 * @param string      $strategy Optional. The computational strategy to use when counting the users.
	 *                              Accepts either 'time' or 'memory'. Default 'time'.
	 * @param int|null    $site_id  Optional. The site ID to count users for. Defaults to the current site.
	 */
	$pre = apply_filters( 'pre_count_users', null, $strategy, $site_id );

	if ( null !== $pre ) {
		return $pre;
	}

	$blog_prefix = $gcdb->get_blog_prefix( $site_id );
	$result      = array();

	if ( 'time' === $strategy ) {
		if ( is_multisite() && get_current_blog_id() != $site_id ) {
			switch_to_blog( $site_id );
			$avail_roles = gc_roles()->get_names();
			restore_current_blog();
		} else {
			$avail_roles = gc_roles()->get_names();
		}

		// Build a CPU-intensive query that will return concise information.
		$select_count = array();
		foreach ( $avail_roles as $this_role => $name ) {
			$select_count[] = $gcdb->prepare( 'COUNT(NULLIF(`meta_value` LIKE %s, false))', '%' . $gcdb->esc_like( '"' . $this_role . '"' ) . '%' );
		}
		$select_count[] = "COUNT(NULLIF(`meta_value` = 'a:0:{}', false))";
		$select_count   = implode( ', ', $select_count );

		// Add the meta_value index to the selection list, then run the query.
		$row = $gcdb->get_row(
			"
			SELECT {$select_count}, COUNT(*)
			FROM {$gcdb->usermeta}
			INNER JOIN {$gcdb->users} ON user_id = ID
			WHERE meta_key = '{$blog_prefix}capabilities'
		",
			ARRAY_N
		);

		// Run the previous loop again to associate results with role names.
		$col         = 0;
		$role_counts = array();
		foreach ( $avail_roles as $this_role => $name ) {
			$count = (int) $row[ $col++ ];
			if ( $count > 0 ) {
				$role_counts[ $this_role ] = $count;
			}
		}

		$role_counts['none'] = (int) $row[ $col++ ];

		// Get the meta_value index from the end of the result set.
		$total_users = (int) $row[ $col ];

		$result['total_users'] = $total_users;
		$result['avail_roles'] =& $role_counts;
	} else {
		$avail_roles = array(
			'none' => 0,
		);

		$users_of_blog = $gcdb->get_col(
			"
			SELECT meta_value
			FROM {$gcdb->usermeta}
			INNER JOIN {$gcdb->users} ON user_id = ID
			WHERE meta_key = '{$blog_prefix}capabilities'
		"
		);

		foreach ( $users_of_blog as $caps_meta ) {
			$b_roles = maybe_unserialize( $caps_meta );
			if ( ! is_array( $b_roles ) ) {
				continue;
			}
			if ( empty( $b_roles ) ) {
				$avail_roles['none']++;
			}
			foreach ( $b_roles as $b_role => $val ) {
				if ( isset( $avail_roles[ $b_role ] ) ) {
					$avail_roles[ $b_role ]++;
				} else {
					$avail_roles[ $b_role ] = 1;
				}
			}
		}

		$result['total_users'] = count( $users_of_blog );
		$result['avail_roles'] =& $avail_roles;
	}

	return $result;
}

//
// Private helper functions.
//

/**
 * Set up global user vars.
 *
 * Used by gc_set_current_user() for back compat. Might be deprecated in the future.
 *
 *
 *
 * @global string  $user_login    The user username for logging in
 * @global GC_User $userdata      User data.
 * @global int     $user_level    The level of the user
 * @global int     $user_ID       The ID of the user
 * @global string  $user_email    The email address of the user
 * @global string  $user_url      The url in the user's profile
 * @global string  $user_identity The display name of the user
 *
 * @param int $for_user_id Optional. User ID to set up global data. Default 0.
 */
function setup_userdata( $for_user_id = 0 ) {
	global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_identity;

	if ( ! $for_user_id ) {
		$for_user_id = get_current_user_id();
	}
	$user = get_userdata( $for_user_id );

	if ( ! $user ) {
		$user_ID       = 0;
		$user_level    = 0;
		$userdata      = null;
		$user_login    = '';
		$user_email    = '';
		$user_url      = '';
		$user_identity = '';
		return;
	}

	$user_ID       = (int) $user->ID;
	$user_level    = (int) $user->user_level;
	$userdata      = $user;
	$user_login    = $user->user_login;
	$user_email    = $user->user_email;
	$user_url      = $user->user_url;
	$user_identity = $user->display_name;
}

/**
 * Create dropdown HTML content of users.
 *
 * The content can either be displayed, which it is by default or retrieved by
 * setting the 'echo' argument. The 'include' and 'exclude' arguments do not
 * need to be used; all users will be displayed in that case. Only one can be
 * used, either 'include' or 'exclude', but not both.
 *
 * The available arguments are as follows:
 *
 *
 *
 *
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a drop-down of users.
 *     See GC_User_Query::prepare_query() for additional available arguments.
 *
 *     @type string       $show_option_all         Text to show as the drop-down default (all).
 *                                                 Default empty.
 *     @type string       $show_option_none        Text to show as the drop-down default when no
 *                                                 users were found. Default empty.
 *     @type int|string   $option_none_value       Value to use for $show_option_non when no users
 *                                                 were found. Default -1.
 *     @type string       $hide_if_only_one_author Whether to skip generating the drop-down
 *                                                 if only one user was found. Default empty.
 *     @type string       $orderby                 Field to order found users by. Accepts user fields.
 *                                                 Default 'display_name'.
 *     @type string       $order                   Whether to order users in ascending or descending
 *                                                 order. Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                                 Default 'ASC'.
 *     @type int[]|string $include                 Array or comma-separated list of user IDs to include.
 *                                                 Default empty.
 *     @type int[]|string $exclude                 Array or comma-separated list of user IDs to exclude.
 *                                                 Default empty.
 *     @type bool|int     $multi                   Whether to skip the ID attribute on the 'select' element.
 *                                                 Accepts 1|true or 0|false. Default 0|false.
 *     @type string       $show                    User data to display. If the selected item is empty
 *                                                 then the 'user_login' will be displayed in parentheses.
 *                                                 Accepts any user field, or 'display_name_with_login' to show
 *                                                 the display name with user_login in parentheses.
 *                                                 Default 'display_name'.
 *     @type int|bool     $echo                    Whether to echo or return the drop-down. Accepts 1|true (echo)
 *                                                 or 0|false (return). Default 1|true.
 *     @type int          $selected                Which user ID should be selected. Default 0.
 *     @type bool         $include_selected        Whether to always include the selected user ID in the drop-
 *                                                 down. Default false.
 *     @type string       $name                    Name attribute of select element. Default 'user'.
 *     @type string       $id                      ID attribute of the select element. Default is the value of $name.
 *     @type string       $class                   Class attribute of the select element. Default empty.
 *     @type int          $blog_id                 ID of blog (Multisite only). Default is ID of the current blog.
 *     @type string       $who                     Which type of users to query. Accepts only an empty string or
 *                                                 'authors'. Default empty.
 *     @type string|array $role                    An array or a comma-separated list of role names that users must
 *                                                 match to be included in results. Note that this is an inclusive
 *                                                 list: users must match *each* role. Default empty.
 *     @type string[]     $role__in                An array of role names. Matched users must have at least one of
 *                                                 these roles. Default empty array.
 *     @type string[]     $role__not_in            An array of role names to exclude. Users matching one or more of
 *                                                 these roles will not be included in results. Default empty array.
 * }
 * @return string HTML dropdown list of users.
 */
function gc_dropdown_users( $args = '' ) {
	$defaults = array(
		'show_option_all'         => '',
		'show_option_none'        => '',
		'hide_if_only_one_author' => '',
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => '',
		'exclude'                 => '',
		'multi'                   => 0,
		'show'                    => 'display_name',
		'echo'                    => 1,
		'selected'                => 0,
		'name'                    => 'user',
		'class'                   => '',
		'id'                      => '',
		'blog_id'                 => get_current_blog_id(),
		'who'                     => '',
		'include_selected'        => false,
		'option_none_value'       => -1,
		'role'                    => '',
		'role__in'                => array(),
		'role__not_in'            => array(),
		'capability'              => '',
		'capability__in'          => array(),
		'capability__not_in'      => array(),
	);

	$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

	$parsed_args = gc_parse_args( $args, $defaults );

	$query_args = gc_array_slice_assoc(
		$parsed_args,
		array(
			'blog_id',
			'include',
			'exclude',
			'orderby',
			'order',
			'who',
			'role',
			'role__in',
			'role__not_in',
			'capability',
			'capability__in',
			'capability__not_in',
		)
	);

	$fields = array( 'ID', 'user_login' );

	$show = ! empty( $parsed_args['show'] ) ? $parsed_args['show'] : 'display_name';
	if ( 'display_name_with_login' === $show ) {
		$fields[] = 'display_name';
	} else {
		$fields[] = $show;
	}

	$query_args['fields'] = $fields;

	$show_option_all   = $parsed_args['show_option_all'];
	$show_option_none  = $parsed_args['show_option_none'];
	$option_none_value = $parsed_args['option_none_value'];

	/**
	 * Filters the query arguments for the list of users in the dropdown.
	 *
	 *
	 * @param array $query_args  The query arguments for get_users().
	 * @param array $parsed_args The arguments passed to gc_dropdown_users() combined with the defaults.
	 */
	$query_args = apply_filters( 'gc_dropdown_users_args', $query_args, $parsed_args );

	$users = get_users( $query_args );

	$output = '';
	if ( ! empty( $users ) && ( empty( $parsed_args['hide_if_only_one_author'] ) || count( $users ) > 1 ) ) {
		$name = esc_attr( $parsed_args['name'] );
		if ( $parsed_args['multi'] && ! $parsed_args['id'] ) {
			$id = '';
		} else {
			$id = $parsed_args['id'] ? " id='" . esc_attr( $parsed_args['id'] ) . "'" : " id='$name'";
		}
		$output = "<select name='{$name}'{$id} class='" . $parsed_args['class'] . "'>\n";

		if ( $show_option_all ) {
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$_selected = selected( $option_none_value, $parsed_args['selected'], false );
			$output   .= "\t<option value='" . esc_attr( $option_none_value ) . "'$_selected>$show_option_none</option>\n";
		}

		if ( $parsed_args['include_selected'] && ( $parsed_args['selected'] > 0 ) ) {
			$found_selected          = false;
			$parsed_args['selected'] = (int) $parsed_args['selected'];

			foreach ( (array) $users as $user ) {
				$user->ID = (int) $user->ID;
				if ( $user->ID === $parsed_args['selected'] ) {
					$found_selected = true;
				}
			}

			if ( ! $found_selected ) {
				$selected_user = get_userdata( $parsed_args['selected'] );
				if ( $selected_user ) {
					$users[] = $selected_user;
				}
			}
		}

		foreach ( (array) $users as $user ) {
			if ( 'display_name_with_login' === $show ) {
				/* translators: 1: User's display name, 2: User login. */
				$display = sprintf( _x( '%1$s（%2$s）', 'user dropdown' ), $user->display_name, $user->user_login );
			} elseif ( ! empty( $user->$show ) ) {
				$display = $user->$show;
			} else {
				$display = '(' . $user->user_login . ')';
			}

			$_selected = selected( $user->ID, $parsed_args['selected'], false );
			$output   .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		$output .= '</select>';
	}

	/**
	 * Filters the gc_dropdown_users() HTML output.
	 *
	 *
	 * @param string $output HTML output generated by gc_dropdown_users().
	 */
	$html = apply_filters( 'gc_dropdown_users', $output );

	if ( $parsed_args['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Sanitize user field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display', 'attribute' and 'js'. The
 * 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display'
 * when calling filters.
 *
 *
 *
 * @param string $field   The user Object field name.
 * @param mixed  $value   The user Object value.
 * @param int    $user_id User ID.
 * @param string $context How to sanitize user fields. Looks for 'raw', 'edit', 'db', 'display',
 *                        'attribute' and 'js'.
 * @return mixed Sanitized value.
 */
function sanitize_user_field( $field, $value, $user_id, $context ) {
	$int_fields = array( 'ID' );
	if ( in_array( $field, $int_fields, true ) ) {
		$value = (int) $value;
	}

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
		return $value;
	}

	$prefixed = false !== strpos( $field, 'user_' );

	if ( 'edit' === $context ) {
		if ( $prefixed ) {

			/** This filter is documented in gc-includes/post.php */
			$value = apply_filters( "edit_{$field}", $value, $user_id );
		} else {

			/**
			 * Filters a user field value in the 'edit' context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
			 *
		
			 *
			 * @param mixed $value   Value of the prefixed user field.
			 * @param int   $user_id User ID.
			 */
			$value = apply_filters( "edit_user_{$field}", $value, $user_id );
		}

		if ( 'description' === $field ) {
			$value = esc_html( $value ); // textarea_escaped?
		} else {
			$value = esc_attr( $value );
		}
	} elseif ( 'db' === $context ) {
		if ( $prefixed ) {
			/** This filter is documented in gc-includes/post.php */
			$value = apply_filters( "pre_{$field}", $value );
		} else {

			/**
			 * Filters the value of a user field in the 'db' context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
			 *
		
			 *
			 * @param mixed $value Value of the prefixed user field.
			 */
			$value = apply_filters( "pre_user_{$field}", $value );
		}
	} else {
		// Use display filters by default.
		if ( $prefixed ) {

			/** This filter is documented in gc-includes/post.php */
			$value = apply_filters( "{$field}", $value, $user_id, $context );
		} else {

			/**
			 * Filters the value of a user field in a standard context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
			 *
		
			 *
			 * @param mixed  $value   The user object value to sanitize.
			 * @param int    $user_id User ID.
			 * @param string $context The context to filter within.
			 */
			$value = apply_filters( "user_{$field}", $value, $user_id, $context );
		}
	}

	if ( 'user_url' === $field ) {
		$value = esc_url( $value );
	}

	if ( 'attribute' === $context ) {
		$value = esc_attr( $value );
	} elseif ( 'js' === $context ) {
		$value = esc_js( $value );
	}

	// Restore the type for integer fields after esc_attr().
	if ( in_array( $field, $int_fields, true ) ) {
		$value = (int) $value;
	}

	return $value;
}

/**
 * Update all user caches
 *
 *
 *
 * @param object|GC_User $user User object or database row to be cached
 * @return void|false Void on success, false on failure.
 */
function update_user_caches( $user ) {
	if ( $user instanceof GC_User ) {
		if ( ! $user->exists() ) {
			return false;
		}

		$user = $user->data;
	}

	gc_cache_add( $user->ID, $user, 'users' );
	gc_cache_add( $user->user_login, $user->ID, 'userlogins' );
	gc_cache_add( $user->user_email, $user->ID, 'useremail' );
	gc_cache_add( $user->user_nicename, $user->ID, 'userslugs' );
}

/**
 * Clean all user caches
 *
 *
 *
 *
 *
 * @global GC_User $current_user The current user object which holds the user data.
 *
 * @param GC_User|int $user User object or ID to be cleaned from the cache
 */
function clean_user_cache( $user ) {
	global $current_user;

	if ( is_numeric( $user ) ) {
		$user = new GC_User( $user );
	}

	if ( ! $user->exists() ) {
		return;
	}

	gc_cache_delete( $user->ID, 'users' );
	gc_cache_delete( $user->user_login, 'userlogins' );
	gc_cache_delete( $user->user_email, 'useremail' );
	gc_cache_delete( $user->user_nicename, 'userslugs' );

	/**
	 * Fires immediately after the given user's cache is cleaned.
	 *
	 *
	 * @param int     $user_id User ID.
	 * @param GC_User $user    User object.
	 */
	do_action( 'clean_user_cache', $user->ID, $user );

	// Refresh the global user instance if the cleaning current user.
	if ( get_current_user_id() === (int) $user->ID ) {
		$user_id      = (int) $user->ID;
		$current_user = null;
		gc_set_current_user( $user_id, '' );
	}
}

/**
 * Determines whether the given username exists.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @param string $username The username to check for existence.
 * @return int|false The user ID on success, false on failure.
 */
function username_exists( $username ) {
	$user = get_user_by( 'login', $username );
	if ( $user ) {
		$user_id = $user->ID;
	} else {
		$user_id = false;
	}

	/**
	 * Filters whether the given username exists.
	 *
	 *
	 * @param int|false $user_id  The user ID associated with the username,
	 *                            or false if the username does not exist.
	 * @param string    $username The username to check for existence.
	 */
	return apply_filters( 'username_exists', $user_id, $username );
}

/**
 * 判断手机号是否存在
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://docs.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @param string $usermobile The usermobile to check for existence.
 * @return int|false The user ID on success, false on failure.
 */
function mobile_exists( $usermobile ) {
	$user = get_user_by( 'mobile', $usermobile );
	if ( $user ) {
		$user_id = $user->ID;
	} else {
		$user_id = false;
	}

	/**
	 * Filters whether the given usermobile exists.
	 * 
	 *
	 * @param int|false $user_id  The user ID associated with the usermobile,
	 *                            or false if the usermobile does not exist.
	 * @param string    $usermobile The usermobile to check for existence.
	 */
	return apply_filters( 'mobile_exists', $user_id, $usermobile );
}

/**
 * Determines whether the given email exists.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @param string $email The email to check for existence.
 * @return int|false The user ID on success, false on failure.
 */
function email_exists( $email ) {
	$user = get_user_by( 'email', $email );
	if ( $user ) {
		$user_id = $user->ID;
	} else {
		$user_id = false;
	}

	/**
	 * Filters whether the given email exists.
	 *
	 *
	 * @param int|false $user_id The user ID associated with the email,
	 *                           or false if the email does not exist.
	 * @param string    $email   The email to check for existence.
	 */
	return apply_filters( 'email_exists', $user_id, $email );
}

/**
 * Checks whether a username is valid.
 *
 *
 *
 *
 * @param string $username Username.
 * @return bool Whether username given is valid.
 */
function validate_username( $username ) {
	$sanitized = sanitize_user( $username, true );
	$valid     = ( $sanitized == $username && ! empty( $sanitized ) );

	/**
	 * Filters whether the provided username is valid.
	 *
	 *
	 * @param bool   $valid    Whether given username is valid.
	 * @param string $username Username to check.
	 */
	return apply_filters( 'validate_username', $valid, $username );
}

//手机号码格式验证
function validate_usermobile($mobile){
    if(preg_match("/^1[34578]\d{9}$/", $mobile)){
        return $mobile;
    }
    return new GC_Error( 'user_mobile', __('<strong>错误</strong>： 不正确的手机号码格式。') );
}

//短信验证
function validate_smscode($mobile, $smscode) {
    $errors = new GC_Error();
    
    $sanitized_user_mobile = sanitize_user( $mobile );
	/**
	 * Filters the mobile of a user being registered.
	 * 
	 *
	 * @param string $mobile The mobile of the new user.
	 */
	$mobile = apply_filters( 'user_registration_mobile', $mobile );
    // Check the usermobile.
	if ( empty( $sanitized_user_mobile) ) {
		$errors->add( 'empty_usermobile', __( '<strong>错误</strong>： 请输入您的手机号' ) );
	} 
    // Check the SMS Code.
	if ( empty( $smscode) ) {
		$errors->add( 'empty_smscode', __( '<strong>错误</strong>： 请输入短信验证码' ) );
	} 
    if ( $errors->has_errors() ) {
        return $errors;
    }
    
    //验证手机号格式
    $error = validate_usermobile( $mobile );
    if (  is_gc_error( $error ) ) {
        return $error;
	} 
    
    $session = GC_User::get_session( $mobile );  //取短信验证码的Session
    if ( ! $session ) {
        return new GC_Error( 'invalid_smscode', __('<strong>错误</strong>： 短信验证码已过期， 请重新验证。') );
    }

    //验证码存在Sessions表里
    if ( $smscode != $session['session_value'] ) {
        return new GC_Error( 'invalid_smscode', __('<strong>错误</strong>： 短信验证码不正确。') );
    }
    
    $user =  mobile_exists( $sanitized_user_mobile ) ;
    if (  is_gc_error( $user ) ) {
		$errors->add( $user );
	} else {
		/** This filter is documented in gc-includes/user.php */
		$illegal_user_mobiles = (array) apply_filters( 'illegal_user_mobiles', array() );
		if ( in_array( strtolower( $sanitized_user_mobile ), array_map( 'strtolower', $illegal_user_mobiles ), true ) ) {
			$errors->add( 'invalid_usermobile', __( '<strong>错误</strong>：对不起，这个手机号码禁止注册。' ) );
		}
	}
    
    return $sanitized_user_mobile;
}

//密码格式验证
function validate_password($userpass){
    $pattern="/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/";
    if(preg_match($pattern, $userpass)){
        return $userpass;
    }
    return new GC_Error( 'user_pass', __('<strong>错误</strong>：密码必须是 6-16 个字母和数字的组合。') );
}

/**
 * Insert a user into the database.
 *
 * Most of the `$userdata` array fields have filters associated with the values. Exceptions are
 * 'ID', 'rich_editing', 'syntax_highlighting', 'comment_shortcuts', 'admin_color', 'use_ssl',
 * 'user_registered', 'user_activation_key', 'spam', and 'role'. The filters have the prefix
 * 'pre_user_' followed by the field name. An example using 'description' would have the filter
 * called 'pre_user_description' that can be hooked into.
 *
 *
 *
 *              methods for new installations. See gc_get_user_contact_methods().
 *
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param array|object|GC_User $userdata {
 *     An array, object, or GC_User object of user data arguments.
 *
 *     @type int    $ID                   User ID. If supplied, the user will be updated.
 *     @type string $user_pass            The plain-text user password.
 *     @type string $user_login           The user's login username.
 *     @type string $user_nicename        The URL-friendly user name.
 *     @type string $user_url             The user URL.
 *     @type string $user_email           The user email address.
 *     @type string $display_name         The user's display name.
 *                                        Default is the user's username.
 *     @type string $nickname             The user's nickname.
 *                                        Default is the user's username.
 *     @type string $first_name           The user's first name. For new users, will be used
 *                                        to build the first part of the user's display name
 *                                        if `$display_name` is not specified.
 *     @type string $last_name            The user's last name. For new users, will be used
 *                                        to build the second part of the user's display name
 *                                        if `$display_name` is not specified.
 *     @type string $description          The user's biographical description.
 *     @type string $rich_editing         Whether to enable the rich-editor for the user.
 *                                        Accepts 'true' or 'false' as a string literal,
 *                                        not boolean. Default 'true'.
 *     @type string $syntax_highlighting  Whether to enable the rich code editor for the user.
 *                                        Accepts 'true' or 'false' as a string literal,
 *                                        not boolean. Default 'true'.
 *     @type string $comment_shortcuts    Whether to enable comment moderation keyboard
 *                                        shortcuts for the user. Accepts 'true' or 'false'
 *                                        as a string literal, not boolean. Default 'false'.
 *     @type string $admin_color          Admin color scheme for the user. Default 'fresh'.
 *     @type bool   $use_ssl              Whether the user should always access the admin over
 *                                        https. Default false.
 *     @type string $user_registered      Date the user registered in UTC. Format is 'Y-m-d H:i:s'.
 *     @type string $user_activation_key  Password reset key. Default empty.
 *     @type bool   $spam                 Multisite only. Whether the user is marked as spam.
 *                                        Default false.
 *     @type string $show_admin_bar_front Whether to display the Admin Bar for the user
 *                                        on the site's front end. Accepts 'true' or 'false'
 *                                        as a string literal, not boolean. Default 'true'.
 *     @type string $role                 User's role.
 *     @type string $locale               User's locale. Default empty.
 *     @type array  $meta_input           Array of custom user meta values keyed by meta key.
 *                                        Default empty.
 * }
 * @return int|GC_Error The newly created user's ID or a GC_Error object if the user could not
 *                      be created.
 */
function gc_insert_user( $userdata ) {
	global $gcdb;

	if ( $userdata instanceof stdClass ) {
		$userdata = get_object_vars( $userdata );
	} elseif ( $userdata instanceof GC_User ) {
		$userdata = $userdata->to_array();
	}

	// Are we updating or creating?
	if ( ! empty( $userdata['ID'] ) ) {
		$user_id       = (int) $userdata['ID'];
		$update        = true;
		$old_user_data = get_userdata( $user_id );

		if ( ! $old_user_data ) {
			return new GC_Error( 'invalid_user_id', __( '用户ID无效。' ) );
		}

		// Hashed in gc_update_user(), plaintext if called directly.
		$user_pass = ! empty( $userdata['user_pass'] ) ? $userdata['user_pass'] : $old_user_data->user_pass;
	} else {
		$update = false;
		// Hash the password.
		$user_pass = gc_hash_password( $userdata['user_pass'] );
	}

	$sanitized_user_login = sanitize_user( $userdata['user_login'], true );

	/**
	 * Filters a username after it has been sanitized.
	 *
	 * This filter is called before the user is created or updated.
	 *
	 *
	 * @param string $sanitized_user_login Username after it has been sanitized.
	 */
	$pre_user_login = apply_filters( 'pre_user_login', $sanitized_user_login );

	// Remove any non-printable chars from the login string to see if we have ended up with an empty username.
	$user_login = trim( $pre_user_login );

	// user_login must be between 0 and 60 characters.
	if ( empty( $user_login ) ) {
		return new GC_Error( 'empty_user_login', __( '不能创建登录名为空的用户。' ) );
	} elseif ( mb_strlen( $user_login ) > 60 ) {
		return new GC_Error( 'user_login_too_long', __( '用户名不能超过60个字符。' ) );
	}

	if ( ! $update && username_exists( $user_login ) ) {
		return new GC_Error( 'existing_user_login', __( '抱歉，用户名已存在！' ) );
	}

	/**
	 * Filters the list of disallowed usernames.
	 *
	 *
	 * @param array $usernames Array of disallowed usernames.
	 */
	$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

	if ( in_array( strtolower( $user_login ), array_map( 'strtolower', $illegal_logins ), true ) ) {
		return new GC_Error( 'invalid_username', __( '抱歉，该用户名不可用。' ) );
	}

	/*
	 * If a nicename is provided, remove unsafe user characters before using it.
	 * Otherwise build a nicename from the user_login.
	 */
	if ( ! empty( $userdata['user_nicename'] ) ) {
		$user_nicename = sanitize_user( $userdata['user_nicename'], true );
		if ( mb_strlen( $user_nicename ) > 50 ) {
			return new GC_Error( 'user_nicename_too_long', __( '昵称不能长于50个字符。' ) );
		}
	} else {
		$user_nicename = mb_substr( $user_login, 0, 50 );
	}

	$user_nicename = sanitize_title( $user_nicename );

	/**
	 * Filters a user's nicename before the user is created or updated.
	 *
	 *
	 * @param string $user_nicename The user's nicename.
	 */
	$user_nicename = apply_filters( 'pre_user_nicename', $user_nicename );

	$user_nicename_check = $gcdb->get_var( $gcdb->prepare( "SELECT ID FROM $gcdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1", $user_nicename, $user_login ) );

	if ( $user_nicename_check ) {
		$suffix = 2;
		while ( $user_nicename_check ) {
			// user_nicename allows 50 chars. Subtract one for a hyphen, plus the length of the suffix.
			$base_length         = 49 - mb_strlen( $suffix );
			$alt_user_nicename   = mb_substr( $user_nicename, 0, $base_length ) . "-$suffix";
			$user_nicename_check = $gcdb->get_var( $gcdb->prepare( "SELECT ID FROM $gcdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1", $alt_user_nicename, $user_login ) );
			$suffix++;
		}
		$user_nicename = $alt_user_nicename;
	}

	$raw_user_email = empty( $userdata['user_email'] ) ? '' : $userdata['user_email'];

	/**
	 * Filters a user's email before the user is created or updated.
	 *
	 *
	 * @param string $raw_user_email The user's email.
	 */
	$user_email = apply_filters( 'pre_user_email', $raw_user_email );

	/*
	 * If there is no update, just check for `email_exists`. If there is an update,
	 * check if current email and new email are the same, and check `email_exists`
	 * accordingly.
	 */
	if ( ( ! $update || ( ! empty( $old_user_data ) && 0 !== strcasecmp( $user_email, $old_user_data->user_email ) ) )
		&& ! defined( 'GC_IMPORTING' )
		&& email_exists( $user_email )
	) {
		return new GC_Error( 'existing_user_email', __( '抱歉，此电子邮箱已被使用！' ) );
	}

	$raw_user_mobile = empty( $userdata['user_mobile'] ) ? '' : $userdata['user_mobile'];

	/**
	 * 在创建或更新用户之前过滤用户的手机。
	 * 
	 *
	 * @param string $raw_user_mobile 用户的手机
	 */
	$user_mobile = apply_filters( 'pre_user_mobile', $raw_user_mobile );

	/*
	 * 如果没有更新，只需检查“mobile_exists”。如果有相应的更新。
	 */
	if ( ( ! $update || ( ! empty( $old_user_data ) && 0 !== strcasecmp( $user_mobile, $old_user_data->user_mobile ) ) )
		&& ! defined( 'GC_IMPORTING' )
		&& mobile_exists( $user_mobile )
	) {
		return new GC_Error( 'existing_user_mobile', __( '对不起，那个手机已经使用了！' ) );
	}


	$raw_user_url = empty( $userdata['user_url'] ) ? '' : $userdata['user_url'];

	/**
	 * Filters a user's URL before the user is created or updated.
	 *
	 *
	 * @param string $raw_user_url The user's URL.
	 */
	$user_url = apply_filters( 'pre_user_url', $raw_user_url );

	if ( mb_strlen( $user_url ) > 100 ) {
		return new GC_Error( 'user_url_too_long', __( '用户URL不能超过100个字符。' ) );
	}

	$user_registered = empty( $userdata['user_registered'] ) ? gmdate( 'Y-m-d H:i:s' ) : $userdata['user_registered'];

	$user_activation_key = empty( $userdata['user_activation_key'] ) ? '' : $userdata['user_activation_key'];

	if ( ! empty( $userdata['spam'] ) && ! is_multisite() ) {
		return new GC_Error( 'no_spam', __( '抱歉，仅能在多站点中标记用户为垃圾。' ) );
	}

	$spam = empty( $userdata['spam'] ) ? 0 : (bool) $userdata['spam'];

	// Store values to save in user meta.
	$meta = array();

	$nickname = empty( $userdata['nickname'] ) ? $user_login : $userdata['nickname'];

	/**
	 * Filters a user's nickname before the user is created or updated.
	 *
	 *
	 * @param string $nickname The user's nickname.
	 */
	$meta['nickname'] = apply_filters( 'pre_user_nickname', $nickname );

	$first_name = empty( $userdata['first_name'] ) ? '' : $userdata['first_name'];

	/**
	 * Filters a user's first name before the user is created or updated.
	 *
	 *
	 * @param string $first_name The user's first name.
	 */
	$meta['first_name'] = apply_filters( 'pre_user_first_name', $first_name );

	$last_name = empty( $userdata['last_name'] ) ? '' : $userdata['last_name'];

	/**
	 * Filters a user's last name before the user is created or updated.
	 *
	 *
	 * @param string $last_name The user's last name.
	 */
	$meta['last_name'] = apply_filters( 'pre_user_last_name', $last_name );

	if ( empty( $userdata['display_name'] ) ) {
		if ( $update ) {
			$display_name = $user_login;
		} elseif ( $meta['first_name'] && $meta['last_name'] ) {
			/* translators: 1: User's first name, 2: Last name. */
			$display_name = sprintf( _x( '%1$s%2$s', 'Display name based on first name and last name' ), $meta['first_name'], $meta['last_name'] );
		} elseif ( $meta['first_name'] ) {
			$display_name = $meta['first_name'];
		} elseif ( $meta['last_name'] ) {
			$display_name = $meta['last_name'];
		} else {
			$display_name = $user_login;
		}
	} else {
		$display_name = $userdata['display_name'];
	}

	/**
	 * Filters a user's display name before the user is created or updated.
	 *
	 *
	 * @param string $display_name The user's display name.
	 */
	$display_name = apply_filters( 'pre_user_display_name', $display_name );

	$description = empty( $userdata['description'] ) ? '' : $userdata['description'];

	/**
	 * Filters a user's description before the user is created or updated.
	 *
	 *
	 * @param string $description The user's description.
	 */
	$meta['description'] = apply_filters( 'pre_user_description', $description );

	$meta['rich_editing'] = empty( $userdata['rich_editing'] ) ? 'true' : $userdata['rich_editing'];

	$meta['syntax_highlighting'] = empty( $userdata['syntax_highlighting'] ) ? 'true' : $userdata['syntax_highlighting'];

	$meta['comment_shortcuts'] = empty( $userdata['comment_shortcuts'] ) || 'false' === $userdata['comment_shortcuts'] ? 'false' : 'true';

	$admin_color         = empty( $userdata['admin_color'] ) ? 'fresh' : $userdata['admin_color'];
	$meta['admin_color'] = preg_replace( '|[^a-z0-9 _.\-@]|i', '', $admin_color );

	$meta['use_ssl'] = empty( $userdata['use_ssl'] ) ? 0 : (bool) $userdata['use_ssl'];

	$meta['show_admin_bar_front'] = empty( $userdata['show_admin_bar_front'] ) ? 'true' : $userdata['show_admin_bar_front'];

	$meta['locale'] = isset( $userdata['locale'] ) ? $userdata['locale'] : '';

	$compacted = compact( 'user_pass', 'user_nicename', 'user_email', 'user_mobile', 'user_url', 'user_registered', 'user_activation_key', 'display_name' );
	$data      = gc_unslash( $compacted );

	if ( ! $update ) {
		$data = $data + compact( 'user_login' );
	}

	if ( is_multisite() ) {
		$data = $data + compact( 'spam' );
	}

	/**
	 * Filters user data before the record is created or updated.
	 *
	 * It only includes data in the users table, not any user metadata.
	 *
	 *
	 * @param array    $data {
	 *     Values and keys for the user.
	 *
	 *     @type string $user_login      The user's login. Only included if $update == false
	 *     @type string $user_pass       The user's password.
	 *     @type string $user_email      The user's email.
	 *     @type string $user_url        The user's url.
	 *     @type string $user_nicename   The user's nice name. Defaults to a URL-safe version of user's login
	 *     @type string $display_name    The user's display name.
	 *     @type string $user_registered MySQL timestamp describing the moment when the user registered. Defaults to
	 *                                   the current UTC timestamp.
	 * }
	 * @param bool     $update   Whether the user is being updated rather than created.
	 * @param int|null $user_id  ID of the user to be updated, or NULL if the user is being created.
	 * @param array    $userdata The raw array of data passed to gc_insert_user().
	 */
	$data = apply_filters( 'gc_pre_insert_user_data', $data, $update, ( $update ? $user_id : null ), $userdata );

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new GC_Error( 'empty_data', __( '没有足够的数据来创建此用户。' ) );
	}

	if ( $update ) {
		if ( $user_email !== $old_user_data->user_email || $user_pass !== $old_user_data->user_pass ||  $user_mobile !== $old_user_data->user_mobile ) {
			$data['user_activation_key'] = '';
		}
		$gcdb->update( $gcdb->users, $data, array( 'ID' => $user_id ) );
	} else {
		$gcdb->insert( $gcdb->users, $data );
		$user_id = (int) $gcdb->insert_id;
	}

	$user = new GC_User( $user_id );

	/**
	 * Filters a user's meta values and keys immediately after the user is created or updated
	 * and before any user meta is inserted or updated.
	 *
	 * Does not include contact methods. These are added using `gc_get_user_contact_methods( $user )`.
	 *
	 * For custom meta fields, see the {@see 'insert_custom_user_meta'} filter.
	 *
	 *
	 * @param array $meta {
	 *     Default meta values and keys for the user.
	 *
	 *     @type string   $nickname             The user's nickname. Default is the user's username.
	 *     @type string   $first_name           The user's first name.
	 *     @type string   $last_name            The user's last name.
	 *     @type string   $description          The user's description.
	 *     @type string   $rich_editing         Whether to enable the rich-editor for the user. Default 'true'.
	 *     @type string   $syntax_highlighting  Whether to enable the rich code editor for the user. Default 'true'.
	 *     @type string   $comment_shortcuts    Whether to enable keyboard shortcuts for the user. Default 'false'.
	 *     @type string   $admin_color          The color scheme for a user's admin screen. Default 'fresh'.
	 *     @type int|bool $use_ssl              Whether to force SSL on the user's admin area. 0|false if SSL
	 *                                          is not forced.
	 *     @type string   $show_admin_bar_front Whether to show the admin bar on the front end for the user.
	 *                                          Default 'true'.
	 *     @type string   $locale               User's locale. Default empty.
	 * }
	 * @param GC_User $user     User object.
	 * @param bool    $update   Whether the user is being updated rather than created.
	 * @param array   $userdata The raw array of data passed to gc_insert_user().
	 */
	$meta = apply_filters( 'insert_user_meta', $meta, $user, $update, $userdata );

	$custom_meta = array();
	if ( array_key_exists( 'meta_input', $userdata ) && is_array( $userdata['meta_input'] ) && ! empty( $userdata['meta_input'] ) ) {
		$custom_meta = $userdata['meta_input'];
	}

	/**
	 * Filters a user's custom meta values and keys immediately after the user is created or updated
	 * and before any user meta is inserted or updated.
	 *
	 * For non-custom meta fields, see the {@see 'insert_user_meta'} filter.
	 *
	 *
	 * @param array   $custom_meta Array of custom user meta values keyed by meta key.
	 * @param GC_User $user        User object.
	 * @param bool    $update      Whether the user is being updated rather than created.
	 * @param array   $userdata    The raw array of data passed to gc_insert_user().
	 */
	$custom_meta = apply_filters( 'insert_custom_user_meta', $custom_meta, $user, $update, $userdata );

	$meta = array_merge( $meta, $custom_meta );

	// Update user meta.
	foreach ( $meta as $key => $value ) {
		update_user_meta( $user_id, $key, $value );
	}

	foreach ( gc_get_user_contact_methods( $user ) as $key => $value ) {
		if ( isset( $userdata[ $key ] ) ) {
			update_user_meta( $user_id, $key, $userdata[ $key ] );
		}
	}

	if ( isset( $userdata['role'] ) ) {
		$user->set_role( $userdata['role'] );
	} elseif ( ! $update ) {
		$user->set_role( get_option( 'default_role' ) );
	}

	clean_user_cache( $user_id );

	if ( $update ) {
		/**
		 * Fires immediately after an existing user is updated.
		 *
		 *
		 * @param int     $user_id       User ID.
		 * @param GC_User $old_user_data Object containing user's data prior to update.
		 * @param array   $userdata      The raw array of data passed to gc_insert_user().
		 */
		do_action( 'profile_update', $user_id, $old_user_data, $userdata );

		if ( isset( $userdata['spam'] ) && $userdata['spam'] != $old_user_data->spam ) {
			if ( 1 == $userdata['spam'] ) {
				/**
				 * Fires after the user is marked as a SPAM user.
				 *
			
				 *
				 * @param int $user_id ID of the user marked as SPAM.
				 */
				do_action( 'make_spam_user', $user_id );
			} else {
				/**
				 * Fires after the user is marked as a HAM user. Opposite of SPAM.
				 *
			
				 *
				 * @param int $user_id ID of the user marked as HAM.
				 */
				do_action( 'make_ham_user', $user_id );
			}
		}
	} else {
		/**
		 * Fires immediately after a new user is registered.
		 *
		 *
		 * @param int   $user_id  User ID.
		 * @param array $userdata The raw array of data passed to gc_insert_user().
		 */
		do_action( 'user_register', $user_id, $userdata );
	}

	return $user_id;
}

/**
 * Update a user in the database.
 *
 * It is possible to update a user's password by specifying the 'user_pass'
 * value in the $userdata parameter array.
 *
 * If current user's password is being updated, then the cookies will be
 * cleared.
 *
 *
 *
 * @see gc_insert_user() For what fields can be set in $userdata.
 *
 * @param array|object|GC_User $userdata An array of user data or a user object of type stdClass or GC_User.
 * @return int|GC_Error The updated user's ID or a GC_Error object if the user could not be updated.
 */
function gc_update_user( $userdata ) {
	if ( $userdata instanceof stdClass ) {
		$userdata = get_object_vars( $userdata );
	} elseif ( $userdata instanceof GC_User ) {
		$userdata = $userdata->to_array();
	}

	$user_id = isset( $userdata['ID'] ) ? (int) $userdata['ID'] : 0;
	if ( ! $user_id ) {
		return new GC_Error( 'invalid_user_id', __( '用户ID无效。' ) );
	}

	// First, get all of the original fields.
	$user_obj = get_userdata( $user_id );
	if ( ! $user_obj ) {
		return new GC_Error( 'invalid_user_id', __( '用户ID无效。' ) );
	}

	$user = $user_obj->to_array();

	// Add additional custom fields.
	foreach ( _get_additional_user_keys( $user_obj ) as $key ) {
		$user[ $key ] = get_user_meta( $user_id, $key, true );
	}

	// Escape data pulled from DB.
	$user = add_magic_quotes( $user );

	if ( ! empty( $userdata['user_pass'] ) && $userdata['user_pass'] !== $user_obj->user_pass ) {
		// If password is changing, hash it now.
		$plaintext_pass        = $userdata['user_pass'];
		$userdata['user_pass'] = gc_hash_password( $userdata['user_pass'] );

		/**
		 * Filters whether to send the password change email.
		 *
		 *
		 * @see gc_insert_user() For `$user` and `$userdata` fields.
		 *
		 * @param bool  $send     Whether to send the email.
		 * @param array $user     The original user array.
		 * @param array $userdata The updated user array.
		 */
		$send_password_change_email = apply_filters( 'send_password_change_email', true, $user, $userdata );
	}

	if ( isset( $userdata['user_email'] ) && $user['user_email'] !== $userdata['user_email'] ) {
		/**
		 * Filters whether to send the email change email.
		 *
		 *
		 * @see gc_insert_user() For `$user` and `$userdata` fields.
		 *
		 * @param bool  $send     Whether to send the email.
		 * @param array $user     The original user array.
		 * @param array $userdata The updated user array.
		 */
		$send_email_change_email = apply_filters( 'send_email_change_email', true, $user, $userdata );
	}

	clean_user_cache( $user_obj );

	// Merge old and new fields with new fields overwriting old ones.
	$userdata = array_merge( $user, $userdata );
	$user_id  = gc_insert_user( $userdata );

	if ( is_gc_error( $user_id ) ) {
		return $user_id;
	}

	$blog_name = gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	$switched_locale = false;
	if ( ! empty( $send_password_change_email ) || ! empty( $send_email_change_email ) ) {
		$switched_locale = switch_to_locale( get_user_locale( $user_id ) );
	}

	if ( ! empty( $send_password_change_email ) ) {
		/* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
		$pass_change_text = __(
			'您好，###USERNAME###：

这个通知是为了确认您在###SITENAME###上的密码已被修改。

如果您没有进行过修改密码的操作，请发送邮件至###ADMIN_EMAIL###\

来通知站点管理员。

此邮件以发送至###EMAIL###。

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
		);

		$pass_change_email = array(
			'to'      => $user['user_email'],
			/* translators: Password change notification email subject. %s: Site title. */
			'subject' => __( '[%s]密码已修改' ),
			'message' => $pass_change_text,
			'headers' => '',
		);

		/**
		 * Filters the contents of the email sent when the user's password is changed.
		 *
		 *
		 * @param array $pass_change_email {
		 *     Used to build gc_mail().
		 *
		 *     @type string $to      The intended recipients. Add emails in a comma separated string.
		 *     @type string $subject The subject of the email.
		 *     @type string $message The content of the email.
		 *         The following strings have a special meaning and will get replaced dynamically:
		 *         - ###USERNAME###    The current user's username.
		 *         - ###ADMIN_EMAIL### The admin email in case this was unexpected.
		 *         - ###EMAIL###       The user's email address.
		 *         - ###SITENAME###    The name of the site.
		 *         - ###SITEURL###     The URL to the site.
		 *     @type string $headers Headers. Add headers in a newline (\r\n) separated string.
		 * }
		 * @param array $user     The original user array.
		 * @param array $userdata The updated user array.
		 */
		$pass_change_email = apply_filters( 'password_change_email', $pass_change_email, $user, $userdata );

		$pass_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $pass_change_email['message'] );
		$pass_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $pass_change_email['message'] );
		$pass_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $pass_change_email['message'] );
		$pass_change_email['message'] = str_replace( '###SITENAME###', $blog_name, $pass_change_email['message'] );
		$pass_change_email['message'] = str_replace( '###SITEURL###', home_url(), $pass_change_email['message'] );

		gc_mail( $pass_change_email['to'], sprintf( $pass_change_email['subject'], $blog_name ), $pass_change_email['message'], $pass_change_email['headers'] );
	}

	if ( ! empty( $send_email_change_email ) ) {
		/* translators: Do not translate USERNAME, ADMIN_EMAIL, NEW_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
		$email_change_text = __(
			'您好，###USERNAME###：

此通知确认您在###SITENAME###的电子邮箱已被修改为###NEW_EMAIL###。

如果您没有进行过修改电子邮箱的操作，请联系站点管理员：
###ADMIN_EMAIL###

此邮件发送至###EMAIL###

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
		);

		$email_change_email = array(
			'to'      => $user['user_email'],
			/* translators: Email change notification email subject. %s: Site title. */
			'subject' => __( '[%s] 电子邮箱已更改' ),
			'message' => $email_change_text,
			'headers' => '',
		);

		/**
		 * Filters the contents of the email sent when the user's email is changed.
		 *
		 *
		 * @param array $email_change_email {
		 *     Used to build gc_mail().
		 *
		 *     @type string $to      The intended recipients.
		 *     @type string $subject The subject of the email.
		 *     @type string $message The content of the email.
		 *         The following strings have a special meaning and will get replaced dynamically:
		 *         - ###USERNAME###    The current user's username.
		 *         - ###ADMIN_EMAIL### The admin email in case this was unexpected.
		 *         - ###NEW_EMAIL###   The new email address.
		 *         - ###EMAIL###       The old email address.
		 *         - ###SITENAME###    The name of the site.
		 *         - ###SITEURL###     The URL to the site.
		 *     @type string $headers Headers.
		 * }
		 * @param array $user     The original user array.
		 * @param array $userdata The updated user array.
		 */
		$email_change_email = apply_filters( 'email_change_email', $email_change_email, $user, $userdata );

		$email_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $email_change_email['message'] );
		$email_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $email_change_email['message'] );
		$email_change_email['message'] = str_replace( '###NEW_EMAIL###', $userdata['user_email'], $email_change_email['message'] );
		$email_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $email_change_email['message'] );
		$email_change_email['message'] = str_replace( '###SITENAME###', $blog_name, $email_change_email['message'] );
		$email_change_email['message'] = str_replace( '###SITEURL###', home_url(), $email_change_email['message'] );

		gc_mail( $email_change_email['to'], sprintf( $email_change_email['subject'], $blog_name ), $email_change_email['message'], $email_change_email['headers'] );
	}

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	// Update the cookies if the password changed.
	$current_user = gc_get_current_user();
	if ( $current_user->ID == $user_id ) {
		if ( isset( $plaintext_pass ) ) {
			gc_clear_auth_cookie();

			// Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
			// If it's greater than this, then we know the user checked '记住我' when they logged in.
			$logged_in_cookie = gc_parse_auth_cookie( '', 'logged_in' );
			/** This filter is documented in gc-includes/pluggable.php */
			$default_cookie_life = apply_filters( 'auth_cookie_expiration', ( 2 * DAY_IN_SECONDS ), $user_id, false );
			$remember            = false;
			if ( false !== $logged_in_cookie && ( $logged_in_cookie['expiration'] - time() ) > $default_cookie_life ) {
				$remember = true;
			}

			gc_set_auth_cookie( $user_id, $remember );
		}
	}

	return $user_id;
}

/**
 * A simpler way of inserting a user into the database.
 *
 * Creates a new user with just the username, password, and email. For more
 * complex user creation use gc_insert_user() to specify more information.
 *
 *
 *
 * @see gc_insert_user() More complete way to create a new user.
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email    Optional. The user's email. Default empty.
 * @return int|GC_Error The newly created user's ID or a GC_Error object if the user could not
 *                      be created.
 */
function gc_create_user( $username, $password, $email = '', $mobile = '' ) {
	$user_login = gc_slash( $username );
	$user_email = gc_slash( $email );
	$user_mobile = gc_slash( $mobile );
	$user_pass  = $password;

	$userdata = compact( 'user_login', 'user_email', 'user_mobile', 'user_pass' );
	return gc_insert_user( $userdata );
}

/**
 * Returns a list of meta keys to be (maybe) populated in gc_update_user().
 *
 * The list of keys returned via this function are dependent on the presence
 * of those keys in the user meta data to be set.
 *
 *
 * @access private
 *
 * @param GC_User $user GC_User instance.
 * @return string[] List of user keys to be populated in gc_update_user().
 */
function _get_additional_user_keys( $user ) {
	$keys = array( 'first_name', 'last_name', 'nickname', 'description', 'rich_editing', 'syntax_highlighting', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front', 'locale' );
	return array_merge( $keys, array_keys( gc_get_user_contact_methods( $user ) ) );
}

/**
 * Set up the user contact methods.
 *
 * Default contact methods were removed in 3.6. A filter dictates contact methods.
 *
 *
 *
 * @param GC_User|null $user Optional. GC_User object.
 * @return string[] Array of contact method labels keyed by contact method.
 */
function gc_get_user_contact_methods( $user = null ) {
	$methods = array();
	if ( get_site_option( 'initial_db_version' ) < 23588 ) {
		$methods = array(
			'aim'    => __( 'AIM' ),
			'yim'    => __( '雅虎通' ),
			'jabber' => __( 'Jabber或Google Talk' ),
		);
	}

	/**
	 * Filters the user contact methods.
	 *
	 *
	 * @param string[]     $methods Array of contact method labels keyed by contact method.
	 * @param GC_User|null $user    GC_User object or null if none was provided.
	 */
	return apply_filters( 'user_contactmethods', $methods, $user );
}

/**
 * The old private function for setting up user contact methods.
 *
 * Use gc_get_user_contact_methods() instead.
 *
 *
 * @access private
 *
 * @param GC_User|null $user Optional. GC_User object. Default null.
 * @return string[] Array of contact method labels keyed by contact method.
 */
function _gc_get_user_contactmethods( $user = null ) {
	return gc_get_user_contact_methods( $user );
}

/**
 * Gets the text suggesting how to create strong passwords.
 *
 *
 *
 * @return string The password hint text.
 */
function gc_get_password_hint() {
	$hint = __( '提示：密码应该至少12个字符长。要创建强密码，请使用大写和小写字母、数字、以及符号如!、"、?、$、%、^、&amp;、)等。' );

	/**
	 * Filters the text describing the site's password complexity policy.
	 *
	 *
	 * @param string $hint The password hint text.
	 */
	return apply_filters( 'password_hint', $hint );
}

/**
 * Creates, stores, then returns a password reset key for user.
 *
 *
 *
 * @global PasswordHash $gc_hasher Portable PHP password hashing framework.
 *
 * @param GC_User $user User to retrieve password reset key for.
 * @return string|GC_Error Password reset key on success. GC_Error on error.
 */
function get_password_reset_key( $user ) {
	global $gc_hasher;

	if ( ! ( $user instanceof GC_User ) ) {
		return new GC_Error( 'invalidcombo', __( '<strong>错误</strong>：没有使用该用户名或电子邮箱的账户。' ) );
	}

	/**
	 * Fires before a new password is retrieved.
	 *
	 * Use the {@see 'retrieve_password'} hook instead.
	 *
	 * @deprecated 1.5.1 Misspelled. Use {@see 'retrieve_password'} hook instead.
	 *
	 * @param string $user_login The user login name.
	 */
	do_action_deprecated( 'retreive_password', array( $user->user_login ), '1.5.1', 'retrieve_password' );

	/**
	 * Fires before a new password is retrieved.
	 *
	 *
	 * @param string $user_login The user login name.
	 */
	do_action( 'retrieve_password', $user->user_login );

	$allow = true;
	if ( is_multisite() && is_user_spammy( $user ) ) {
		$allow = false;
	}

	/**
	 * Filters whether to allow a password to be reset.
	 *
	 *
	 * @param bool $allow   Whether to allow the password to be reset. Default true.
	 * @param int  $user_id The ID of the user attempting to reset a password.
	 */
	$allow = apply_filters( 'allow_password_reset', $allow, $user->ID );

	if ( ! $allow ) {
		return new GC_Error( 'no_password_reset', __( '不能重设该用户的密码' ) );
	} elseif ( is_gc_error( $allow ) ) {
		return $allow;
	}

	// Generate something random for a password reset key.
	$key = gc_generate_password( 20, false );

	/**
	 * Fires when a password reset key is generated.
	 *
	 *
	 * @param string $user_login The username for the user.
	 * @param string $key        The generated password reset key.
	 */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $gc_hasher ) ) {
		require_once ABSPATH . GCINC . '/class-phpass.php';
		$gc_hasher = new PasswordHash( 8, true );
	}

	$hashed = time() . ':' . $gc_hasher->HashPassword( $key );

	$key_saved = gc_update_user(
		array(
			'ID'                  => $user->ID,
			'user_activation_key' => $hashed,
		)
	);

	if ( is_gc_error( $key_saved ) ) {
		return $key_saved;
	}

	return $key;
}

/**
 * Retrieves a user row based on password reset key and login
 *
 * A key is considered 'expired' if it exactly matches the value of the
 * user_activation_key field, rather than being matched after going through the
 * hashing process. This field is now hashed; old values are no longer accepted
 * but have a different GC_Error code so good user feedback can be provided.
 *
 *
 *
 * @global gcdb         $gcdb      GeChiUI database object for queries.
 * @global PasswordHash $gc_hasher Portable PHP password hashing framework instance.
 *
 * @param string $key       Hash to validate sending user's password.
 * @param string $login     The user login.
 * @return GC_User|GC_Error GC_User object on success, GC_Error object for invalid or expired keys.
 */
function check_password_reset_key( $key, $login ) {
	global $gcdb, $gc_hasher;

	$key = preg_replace( '/[^a-z0-9]/i', '', $key );

	if ( empty( $key ) || ! is_string( $key ) ) {
		return new GC_Error( 'invalid_key', __( '无效密钥。' ) );
	}

	if ( empty( $login ) || ! is_string( $login ) ) {
		return new GC_Error( 'invalid_key', __( '无效密钥。' ) );
	}

	$user = get_user_by( 'login', $login );

	if ( ! $user ) {
		return new GC_Error( 'invalid_key', __( '无效密钥。' ) );
	}

	if ( empty( $gc_hasher ) ) {
		require_once ABSPATH . GCINC . '/class-phpass.php';
		$gc_hasher = new PasswordHash( 8, true );
	}

	/**
	 * Filters the expiration time of password reset keys.
	 *
	 *
	 * @param int $expiration The expiration time in seconds.
	 */
	$expiration_duration = apply_filters( 'password_reset_expiration', DAY_IN_SECONDS );

	if ( false !== strpos( $user->user_activation_key, ':' ) ) {
		list( $pass_request_time, $pass_key ) = explode( ':', $user->user_activation_key, 2 );
		$expiration_time                      = $pass_request_time + $expiration_duration;
	} else {
		$pass_key        = $user->user_activation_key;
		$expiration_time = false;
	}

	if ( ! $pass_key ) {
		return new GC_Error( 'invalid_key', __( '无效密钥。' ) );
	}

	$hash_is_correct = $gc_hasher->CheckPassword( $key, $pass_key );

	if ( $hash_is_correct && $expiration_time && time() < $expiration_time ) {
		return $user;
	} elseif ( $hash_is_correct && $expiration_time ) {
		// Key has an expiration time that's passed.
		return new GC_Error( 'expired_key', __( '无效密钥。' ) );
	}

	if ( hash_equals( $user->user_activation_key, $key ) || ( $hash_is_correct && ! $expiration_time ) ) {
		$return  = new GC_Error( 'expired_key', __( '无效密钥。' ) );
		$user_id = $user->ID;

		/**
		 * Filters the return value of check_password_reset_key() when an
		 * old-style key is used.
		 *
		 *
		 * @param GC_Error $return  A GC_Error object denoting an expired key.
		 *                          Return a GC_User object to validate the key.
		 * @param int      $user_id The matched user ID.
		 */
		return apply_filters( 'password_reset_key_expired', $return, $user_id );
	}

	return new GC_Error( 'invalid_key', __( '无效密钥。' ) );
}

/**
 * Handles sending a password retrieval email to a user.
 *
 *
 *
 *
 * @global gcdb         $gcdb       GeChiUI database abstraction object.
 * @global PasswordHash $gc_hasher  Portable PHP password hashing framework.
 *
 * @param string $user_login Optional. Username to send a password retrieval email for.
 *                           Defaults to `$_POST['user_login']` if not set.
 * @return true|GC_Error True when finished, GC_Error object on error.
 */
function retrieve_password( $user_login = null ) {
	$errors    = new GC_Error();
	$user_data = false;

	// Use the passed $user_login if available, otherwise use $_POST['user_login'].
	if ( ! $user_login && ! empty( $_POST['user_login'] ) ) {
		$user_login = $_POST['user_login'];
	}

	if ( empty( $user_login ) ) {
		$errors->add( 'empty_username', __( '<strong>错误</strong>：请输入用户名或电子邮箱。' ) );
	} elseif ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( gc_unslash( $user_login ) ) );
		if ( empty( $user_data ) ) {
			$errors->add( 'invalid_email', __( '<strong>错误</strong>：没有使用该用户名或电子邮箱的账户。' ) );
		}
	} else {
		$user_data = get_user_by( 'login', trim( gc_unslash( $user_login ) ) );
	}

	/**
	 * Filters the user data during a password reset request.
	 *
	 * Allows, for example, custom validation using data other than username or email address.
	 *
	 *
	 * @param GC_User|false $user_data GC_User object if found, false if the user does not exist.
	 * @param GC_Error      $errors    A GC_Error object containing any errors generated
	 *                                 by using invalid credentials.
	 */
	$user_data = apply_filters( 'lostpassword_user_data', $user_data, $errors );

	/**
	 * Fires before errors are returned from a password reset request.
	 *
	 *
	 * @param GC_Error      $errors    A GC_Error object containing any errors generated
	 *                                 by using invalid credentials.
	 * @param GC_User|false $user_data GC_User object if found, false if the user does not exist.
	 */
	do_action( 'lostpassword_post', $errors, $user_data );

	/**
	 * Filters the errors encountered on a password reset request.
	 *
	 * The filtered GC_Error object may, for example, contain errors for an invalid
	 * username or email address. A GC_Error object should always be returned,
	 * but may or may not contain errors.
	 *
	 * If any errors are present in $errors, this will abort the password reset request.
	 *
	 *
	 * @param GC_Error      $errors    A GC_Error object containing any errors generated
	 *                                 by using invalid credentials.
	 * @param GC_User|false $user_data GC_User object if found, false if the user does not exist.
	 */
	$errors = apply_filters( 'lostpassword_errors', $errors, $user_data );

	if ( $errors->has_errors() ) {
		return $errors;
	}

	if ( ! $user_data ) {
		$errors->add( 'invalidcombo', __( '<strong>错误</strong>：没有使用该用户名或电子邮箱的账户。' ) );
		return $errors;
	}

	/**
	 * Filters whether to send the retrieve password email.
	 *
	 * Return false to disable sending the email.
	 *
	 *
	 * @param bool $send Whether to send the email.
	 */
	if ( ! apply_filters( 'send_retrieve_password_email', true ) ) {
		return true;
	}

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key        = get_password_reset_key( $user_data );

	if ( is_gc_error( $key ) ) {
		return $key;
	}

	// Localize password reset message content for user.
	$locale = get_user_locale( $user_data );

	$switched_locale = switch_to_locale( $locale );

	if ( is_multisite() ) {
		$site_name = get_network()->site_name;
	} else {
		/*
		 * The blogname option is escaped with esc_html on the way into the database
		 * in sanitize_option. We want to reverse this for the plain text arena of emails.
		 */
		$site_name = gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$message = __( '有人为以下账户请求了密码重置：' ) . "\r\n\r\n";
	/* translators: %s: Site name. */
	$message .= sprintf( __( '站点名称：%s' ), $site_name ) . "\r\n\r\n";
	/* translators: %s: User login. */
	$message .= sprintf( __( '用户名：%s' ), $user_login ) . "\r\n\r\n";
	$message .= __( '若这不是您本人要求的，请忽略本邮件，一切如常。' ) . "\r\n\r\n";
	$message .= __( '要重置您的密码，请打开以下链接：' ) . "\r\n\r\n";
	$message .= network_site_url( "gc-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . '&gc_lang=' . $locale . "\r\n\r\n";

	if ( ! is_user_logged_in() ) {
		$requester_ip = $_SERVER['REMOTE_ADDR'];
		if ( $requester_ip ) {
			$message .= sprintf(
				/* translators: %s: IP address of password reset requester. */
				__( '此密码重置请求来自IP地址%s。' ),
				$requester_ip
			) . "\r\n";
		}
	}

	/* translators: Password reset notification email subject. %s: Site title. */
	$title = sprintf( __( '[%s] 密码重设' ), $site_name );

	/**
	 * Filters the subject of the password reset email.
	 *
	 *
	 * @param string  $title      Email subject.
	 * @param string  $user_login The username for the user.
	 * @param GC_User $user_data  GC_User object.
	 */
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	/**
	 * Filters the message body of the password reset mail.
	 *
	 * If the filtered message is empty, the password reset email will not be sent.
	 *
	 *
	 * @param string  $message    Email message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param GC_User $user_data  GC_User object.
	 */
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

	// Short-circuit on falsey $message value for backwards compatibility.
	if ( ! $message ) {
		return true;
	}

	/*
	 * Wrap the single notification email arguments in an array
	 * to pass them to the retrieve_password_notification_email filter.
	 */
	$defaults = array(
		'to'      => $user_email,
		'subject' => $title,
		'message' => $message,
		'headers' => '',
	);

	$data = compact( 'key', 'user_login', 'user_data' );

	/**
	 * Filters the contents of the reset password notification email sent to the user.
	 *
	 *
	 * @param array $defaults {
	 *     The default notification email arguments. Used to build gc_mail().
	 *
	 *     @type string $to      The intended recipient - user email address.
	 *     @type string $subject The subject of the email.
	 *     @type string $message The body of the email.
	 *     @type string $headers The headers of the email.
	 * }
	 * @param array $data {
	 *     Additional information for extenders.
	 *
	 *     @type string  $key        The activation key.
	 *     @type string  $user_login The username for the user.
	 *     @type GC_User $user_data  GC_User object.
	 * }
	 */
	$notification_email = apply_filters( 'retrieve_password_notification_email', $defaults, $data );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	if ( is_array( $notification_email ) ) {
		// Force key order and merge defaults in case any value is missing in the filtered array.
		$notification_email = array_merge( $defaults, $notification_email );
	} else {
		$notification_email = $defaults;
	}

	list( $to, $subject, $message, $headers ) = array_values( $notification_email );

	$subject = gc_specialchars_decode( $subject );

	if ( ! gc_mail( $to, $subject, $message, $headers ) ) {
		$errors->add(
			'retrieve_password_email_failure',
			sprintf(
				/* translators: %s: Documentation URL. */
				__( '<strong>错误</strong>：邮件未能发送。您的主机可能未获正确配置。<a href="%s">获取关于重设您的密码的帮助</a>。' ),
				esc_url( __( 'https://www.gechiui.com/support/resetting-your-password/' ) )
			)
		);
		return $errors;
	}

	return true;
}

/**
 * Handles resetting the user's password.
 *
 *
 *
 * @param GC_User $user     The user
 * @param string  $new_pass New password for the user in plaintext
 */
function reset_password( $user, $new_pass ) {
	/**
	 * Fires before the user's password is reset.
	 *
	 *
	 * @param GC_User $user     The user.
	 * @param string  $new_pass New user password.
	 */
	do_action( 'password_reset', $user, $new_pass );

	gc_set_password( $new_pass, $user->ID );
	update_user_meta( $user->ID, 'default_password_nag', false );

	/**
	 * Fires after the user's password is reset.
	 *
	 *
	 * @param GC_User $user     The user.
	 * @param string  $new_pass New user password.
	 */
	do_action( 'after_password_reset', $user, $new_pass );
}

/**
 * Handles registering a new user.
 *
 *
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|GC_Error Either user's ID or error on failure.
 */
function register_new_user( $user_login, $user_email ) {
	$errors = new GC_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	/**
	 * Filters the email address of a user being registered.
	 *
	 *
	 * @param string $user_email The email address of the new user.
	 */
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username.
	if ( '' === $sanitized_user_login ) {
		$errors->add( 'empty_username', __( '<strong>错误</strong>：请填写用户名。' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>错误</strong>：此用户名包含无效字符，请输入有效的用户名。' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>错误</strong>：该用户名已被注册，请再选择一个。' ) );

	} else {
		/** This filter is documented in gc-includes/user.php */
		$illegal_user_logins = (array) apply_filters( 'illegal_user_logins', array() );
		if ( in_array( strtolower( $sanitized_user_login ), array_map( 'strtolower', $illegal_user_logins ), true ) ) {
			$errors->add( 'invalid_username', __( '<strong>错误</strong>：此用户名不被允许。' ) );
		}
	}

	// Check the email address.
	if ( '' === $user_email ) {
		$errors->add( 'empty_email', __( '<strong>错误</strong>：请输入您的电子邮箱。' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>错误</strong>：电子邮箱不正确。' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add(
			'email_exists',
			sprintf(
				/* translators: %s: Link to the login page. */
				__( '<strong>错误：</strong>此电子邮箱已被注册，请<a href="%s">使用此地址登录</a>或选择其他地址。' ),
				gc_login_url()
			)
		);
	}

	/**
	 * Fires when submitting registration form data, before the user is created.
	 *
	 *
	 * @param string   $sanitized_user_login The submitted username after being sanitized.
	 * @param string   $user_email           The submitted email.
	 * @param GC_Error $errors               Contains any errors with submitted username and email,
	 *                                       e.g., an empty field, an invalid username or email,
	 *                                       or an existing username or email.
	 */
	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	/**
	 * Filters the errors encountered when a new user is being registered.
	 *
	 * The filtered GC_Error object may, for example, contain errors for an invalid
	 * or existing username or email address. A GC_Error object should always be returned,
	 * but may or may not contain errors.
	 *
	 * If any errors are present in $errors, this will abort the user's registration.
	 *
	 *
	 * @param GC_Error $errors               A GC_Error object containing any errors encountered
	 *                                       during registration.
	 * @param string   $sanitized_user_login User's username after it has been sanitized.
	 * @param string   $user_email           User's email.
	 */
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->has_errors() ) {
		return $errors;
	}

	$user_pass = gc_generate_password( 12, false );
	$user_id   = gc_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id || is_gc_error( $user_id ) ) {
		$errors->add(
			'registerfail',
			sprintf(
				/* translators: %s: Admin email address. */
				__( '<strong>错误</strong>：无法完成您的注册请求&hellip;请联系<a href="mailto:%s">站点管理员</a>！' ),
				get_option( 'admin_email' )
			)
		);
		return $errors;
	}

	update_user_meta( $user_id, 'default_password_nag', true ); // Set up the password change nag.

	if ( ! empty( $_COOKIE['gc_lang'] ) ) {
		$gc_lang = sanitize_text_field( $_COOKIE['gc_lang'] );
		if ( in_array( $gc_lang, get_available_languages(), true ) ) {
			update_user_meta( $user_id, 'locale', $gc_lang ); // Set user locale if defined on registration.
		}
	}

	/**
	 * Fires after a new user registration has been recorded.
	 *
	 *
	 * @param int $user_id ID of the newly registered user.
	 */
	do_action( 'register_new_user', $user_id );

	return $user_id;
}

//用户修改手机号码，短信核验
function update_mobile($user_mobile, $sms_code, $user_id){
    global $gcdb;
    $sanitized_user_mobile =  validate_smscode( $user_mobile, $sms_code ) ;
    if (  is_gc_error( $sanitized_user_mobile ) ) {
		return $sanitized_user_mobile;
	} 
    return $gcdb->query( $gcdb->prepare( "UPDATE {$gcdb->users} SET user_mobile = %s WHERE ID = %s", $sanitized_user_mobile, $user_id ) );
}

/**
* 使用手机号码注册一个新用户
* @param string user_mobile 用户手机号即用户名
* @param string sms_code 短信验证码，数据存放在表Sessions中
* @param string user_pass 用户密码
*/
function register_new_mobile( $user_mobile, $sms_code, $user_pass ){
    
    //验证手机号和验证码，如果不返回异常，则通过
    $sanitized_user_mobile =  validate_smscode( $user_mobile, $sms_code ) ;
    if (  is_gc_error( $sanitized_user_mobile ) ) {
		return $sanitized_user_mobile;
	} 

	/**
	 * Fires when submitting registration form data, before the user is created.
	 */
	do_action( 'register_mobile_post', $sanitized_user_mobile, $user_pass, $errors );

	/**
	 * Filters the errors encountered when a new user is being registered.
	 *
	 * The filtered GC_Error object may, for example, contain errors for an invalid
	 * or existing usermobile or email address. A GC_Error object should always be returned,
	 * but may or may not contain errors.
	 *
	 * If any errors are present in $errors, this will abort the user's registration.
	 * 
	 *
	 * @param GC_Error $errors               A GC_Error object containing any errors encountered
	 *                                       during registration.
	 * @param string   sanitized_user_mobile User's username after it has been sanitized.
	 */
	$errors = apply_filters( 'registration_mobile_errors', $errors, $sanitized_user_mobile, $user_pass);

	if ( is_gc_error($errors) && $errors->has_errors() ) {
		return $errors;
	}
    $strs="qwertyuiopasdfghjklzxcvbnm"; //用户名随机字符串组
    $username = substr(str_shuffle($strs),mt_rand(0,strlen($strs)-7),6).$sanitized_user_mobile; //随机6个字符串
	$user_id   = gc_create_user( $username, $user_pass, '',  $sanitized_user_mobile);
    if( is_gc_error( $user_id )) {
        return $user_id;
    }
	if ( ! $user_id ) {
		return new GC_Error(
			'registerfail',
			sprintf(
				/* translators: %s: Admin email address. */
				__( '<strong>错误</strong>：无法完成您的注册请求&hellip;请联系<a href="mailto:%s">站点管理员</a>！' ),
				get_option( 'admin_email' )
			)
		);
	}

	update_user_meta( $user_id, 'default_password_nag', true ); // Set up the password change nag.

	/**
	 * Fires after a new user registration has been recorded.
	 * 
	 *
	 * @param int $user_id ID of the newly registered user.
	 */
	do_action( 'register_new_user', $user_id );

	return $user_id;
}

/**
 * Initiates email notifications related to the creation of new users.
 *
 * Notifications are sent both to the site admin and to the newly created user.
 *
 *
 *
 *              notifications only to the user created.
 *
 * @param int    $user_id ID of the newly created user.
 * @param string $notify  Optional. Type of notification that should happen. Accepts 'admin'
 *                        or an empty string (admin only), 'user', or 'both' (admin and user).
 *                        Default 'both'.
 */
function gc_send_new_user_notifications( $user_id, $notify = 'both' ) {
	gc_new_user_notification( $user_id, null, $notify );
}

/**
 * Retrieve the current session token from the logged_in cookie.
 *
 *
 *
 * @return string Token.
 */
function gc_get_session_token() {
	$cookie = gc_parse_auth_cookie( '', 'logged_in' );
	return ! empty( $cookie['token'] ) ? $cookie['token'] : '';
}

/**
 * Retrieve a list of sessions for the current user.
 *
 *
 *
 * @return array Array of sessions.
 */
function gc_get_all_sessions() {
	$manager = GC_Session_Tokens::get_instance( get_current_user_id() );
	return $manager->get_all();
}

/**
 * Remove the current session token from the database.
 *
 *
 */
function gc_destroy_current_session() {
	$token = gc_get_session_token();
	if ( $token ) {
		$manager = GC_Session_Tokens::get_instance( get_current_user_id() );
		$manager->destroy( $token );
	}
}

/**
 * Remove all but the current session token for the current user for the database.
 *
 *
 */
function gc_destroy_other_sessions() {
	$token = gc_get_session_token();
	if ( $token ) {
		$manager = GC_Session_Tokens::get_instance( get_current_user_id() );
		$manager->destroy_others( $token );
	}
}

/**
 * Remove all session tokens for the current user from the database.
 *
 *
 */
function gc_destroy_all_sessions() {
	$manager = GC_Session_Tokens::get_instance( get_current_user_id() );
	$manager->destroy_all();
}

/**
 * Get the user IDs of all users with no role on this site.
 *
 *
 *
 *
 * @param int|null $site_id Optional. The site ID to get users with no role for. Defaults to the current site.
 * @return string[] Array of user IDs as strings.
 */
function gc_get_users_with_no_role( $site_id = null ) {
	global $gcdb;

	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	$prefix = $gcdb->get_blog_prefix( $site_id );

	if ( is_multisite() && get_current_blog_id() != $site_id ) {
		switch_to_blog( $site_id );
		$role_names = gc_roles()->get_names();
		restore_current_blog();
	} else {
		$role_names = gc_roles()->get_names();
	}

	$regex = implode( '|', array_keys( $role_names ) );
	$regex = preg_replace( '/[^a-zA-Z_\|-]/', '', $regex );
	$users = $gcdb->get_col(
		$gcdb->prepare(
			"
		SELECT user_id
		FROM $gcdb->usermeta
		WHERE meta_key = '{$prefix}capabilities'
		AND meta_value NOT REGEXP %s
	",
			$regex
		)
	);

	return $users;
}

/**
 * Retrieves the current user object.
 *
 * Will set the current user, if the current user is not set. The current user
 * will be set to the logged-in person. If no user is logged-in, then it will
 * set the current user to 0, which is invalid and won't have any permissions.
 *
 * This function is used by the pluggable functions gc_get_current_user() and
 * get_currentuserinfo(), the latter of which is deprecated but used for backward
 * compatibility.
 *
 *
 * @access private
 *
 * @see gc_get_current_user()
 * @global GC_User $current_user Checks if the current user is set.
 *
 * @return GC_User Current GC_User instance.
 */
function _gc_get_current_user() {
	global $current_user;

	if ( ! empty( $current_user ) ) {
		if ( $current_user instanceof GC_User ) {
			return $current_user;
		}

		// Upgrade stdClass to GC_User.
		if ( is_object( $current_user ) && isset( $current_user->ID ) ) {
			$cur_id       = $current_user->ID;
			$current_user = null;
			gc_set_current_user( $cur_id );
			return $current_user;
		}

		// $current_user has a junk value. Force to GC_User with ID 0.
		$current_user = null;
		gc_set_current_user( 0 );
		return $current_user;
	}

	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		gc_set_current_user( 0 );
		return $current_user;
	}

	/**
	 * Filters the current user.
	 *
	 * The default filters use this to determine the current user from the
	 * request's cookies, if available.
	 *
	 * Returning a value of false will effectively short-circuit setting
	 * the current user.
	 *
	 *
	 * @param int|false $user_id User ID if one has been determined, false otherwise.
	 */
	$user_id = apply_filters( 'determine_current_user', false );
	if ( ! $user_id ) {
		gc_set_current_user( 0 );
		return $current_user;
	}

	gc_set_current_user( $user_id );

	return $current_user;
}

/**
 * Send a confirmation request email when a change of user email address is attempted.
 *
 *
 *
 *
 * @global GC_Error $errors GC_Error object.
 */
function send_confirmation_on_profile_email() {
	global $errors;

	$current_user = gc_get_current_user();
	if ( ! is_object( $errors ) ) {
		$errors = new GC_Error();
	}

	if ( $current_user->ID != $_POST['user_id'] ) {
		return false;
	}

	if ( $current_user->user_email != $_POST['email'] ) {
		if ( ! is_email( $_POST['email'] ) ) {
			$errors->add(
				'user_email',
				__( '<strong>错误</strong>：电子邮箱不正确。' ),
				array(
					'form-field' => 'email',
				)
			);

			return;
		}

		if ( email_exists( $_POST['email'] ) ) {
			$errors->add(
				'user_email',
				__( '<strong>错误</strong>：此电子邮箱已被使用。' ),
				array(
					'form-field' => 'email',
				)
			);
			delete_user_meta( $current_user->ID, '_new_email' );

			return;
		}

		$hash           = md5( $_POST['email'] . time() . gc_rand() );
		$new_user_email = array(
			'hash'     => $hash,
			'newemail' => $_POST['email'],
		);
		update_user_meta( $current_user->ID, '_new_email', $new_user_email );

		$sitename = gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		/* translators: Do not translate USERNAME, ADMIN_URL, EMAIL, SITENAME, SITEURL: those are placeholders. */
		$email_text = __(
			'您好，###USERNAME###：

您最近请求修改您账户的电子邮箱。

如果您确实要修改，请点击以下链接：
###ADMIN_URL###

如果您并未请求修改，则可安全地忽视并删除此邮件。

此邮件被发送至###EMAIL###

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
		);

		/**
		 * Filters the text of the email sent when a change of user email address is attempted.
		 *
		 * The following strings have a special meaning and will get replaced dynamically:
		 * - ###USERNAME###  The current user's username.
		 * - ###ADMIN_URL### The link to click on to confirm the email change.
		 * - ###EMAIL###     The new email.
		 * - ###SITENAME###  The name of the site.
		 * - ###SITEURL###   The URL to the site.
		 *
		 * @since MU
		 *
		 * @param string $email_text     Text in the email.
		 * @param array  $new_user_email {
		 *     Data relating to the new user email address.
		 *
		 *     @type string $hash     The secure hash used in the confirmation link URL.
		 *     @type string $newemail The proposed new email address.
		 * }
		 */
		$content = apply_filters( 'new_user_email_content', $email_text, $new_user_email );

		$content = str_replace( '###USERNAME###', $current_user->user_login, $content );
		$content = str_replace( '###ADMIN_URL###', esc_url( admin_url( 'profile.php?newuseremail=' . $hash ) ), $content );
		$content = str_replace( '###EMAIL###', $_POST['email'], $content );
		$content = str_replace( '###SITENAME###', $sitename, $content );
		$content = str_replace( '###SITEURL###', home_url(), $content );

		/* translators: New email address notification email subject. %s: Site title. */
		gc_mail( $_POST['email'], sprintf( __( '[%s] 电子邮箱变更请求' ), $sitename ), $content );

		$_POST['email'] = $current_user->user_email;
	}
}

/**
 * Adds an admin notice alerting the user to check for confirmation request email
 * after email address change.
 *
 *
 *
 *
 * @global string $pagenow
 */
function new_user_email_admin_notice() {
	global $pagenow;

	if ( 'profile.php' === $pagenow && isset( $_GET['updated'] ) ) {
		$email = get_user_meta( get_current_user_id(), '_new_email', true );
		if ( $email ) {
			/* translators: %s: New email address. */
			echo '<div class="notice notice-info"><p>' . sprintf( __( '您的电子邮箱尚未更新，请在%s的收件箱中检查确认邮件。' ), '<code>' . esc_html( $email['newemail'] ) . '</code>' ) . '</p></div>';
		}
	}
}

/**
 * Get all personal data request types.
 *
 *
 * @access private
 *
 * @return array List of core privacy action types.
 */
function _gc_privacy_action_request_types() {
	return array(
		'export_personal_data',
		'remove_personal_data',
	);
}

/**
 * Registers the personal data exporter for users.
 *
 *
 *
 * @param array $exporters  An array of personal data exporters.
 * @return array An array of personal data exporters.
 */
function gc_register_user_personal_data_exporter( $exporters ) {
	$exporters['gechiui-user'] = array(
		'exporter_friendly_name' => __( 'GeChiUI用户' ),
		'callback'               => 'gc_user_personal_data_exporter',
	);

	return $exporters;
}

/**
 * Finds and exports personal data associated with an email address from the user and user_meta table.
 *
 *
 *
 *
 *
 * @param string $email_address  The user's email address.
 * @return array An array of personal data.
 */
function gc_user_personal_data_exporter( $email_address ) {
	$email_address = trim( $email_address );

	$data_to_export = array();

	$user = get_user_by( 'email', $email_address );

	if ( ! $user ) {
		return array(
			'data' => array(),
			'done' => true,
		);
	}

	$user_meta = get_user_meta( $user->ID );

	$user_props_to_export = array(
		'ID'              => __( '用户ID' ),
		'user_login'      => __( '用户登录名' ),
		'user_nicename'   => __( '用户友好名称' ),
		'user_email'      => __( '用户电邮' ),
		'user_url'        => __( '用户URL' ),
		'user_registered' => __( '用户注册日期' ),
		'display_name'    => __( '用户显示名称' ),
		'nickname'        => __( '用户昵称' ),
		'first_name'      => __( '用户名字' ),
		'last_name'       => __( '用户姓氏' ),
		'description'     => __( '用户描述' ),
	);

	$user_data_to_export = array();

	foreach ( $user_props_to_export as $key => $name ) {
		$value = '';

		switch ( $key ) {
			case 'ID':
			case 'user_login':
			case 'user_nicename':
			case 'user_email':
			case 'user_url':
			case 'user_registered':
			case 'display_name':
				$value = $user->data->$key;
				break;
			case 'nickname':
			case 'first_name':
			case 'last_name':
			case 'description':
				$value = $user_meta[ $key ][0];
				break;
		}

		if ( ! empty( $value ) ) {
			$user_data_to_export[] = array(
				'name'  => $name,
				'value' => $value,
			);
		}
	}

	// Get the list of reserved names.
	$reserved_names = array_values( $user_props_to_export );

	/**
	 * Filter to extend the user's profile data for the privacy exporter.
	 *
	 *
	 * @param array    $additional_user_profile_data {
	 *     An array of name-value pairs of additional user data items. Default empty array.
	 *
	 *     @type string $name  The user-facing name of an item name-value pair,e.g. 'IP Address'.
	 *     @type string $value The user-facing value of an item data pair, e.g. '50.60.70.0'.
	 * }
	 * @param GC_User  $user           The user whose data is being exported.
	 * @param string[] $reserved_names An array of reserved names. Any item in `$additional_user_data`
	 *                                 that uses one of these for its `name` will not be included in the export.
	 */
	$_extra_data = apply_filters( 'gc_privacy_additional_user_profile_data', array(), $user, $reserved_names );

	if ( is_array( $_extra_data ) && ! empty( $_extra_data ) ) {
		// Remove items that use reserved names.
		$extra_data = array_filter(
			$_extra_data,
			static function( $item ) use ( $reserved_names ) {
				return ! in_array( $item['name'], $reserved_names, true );
			}
		);

		if ( count( $extra_data ) !== count( $_extra_data ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: %s: gc_privacy_additional_user_profile_data */
					__( '过滤器%s返回了包含保留名称的项目。' ),
					'<code>gc_privacy_additional_user_profile_data</code>'
				),
				'5.4.0'
			);
		}

		if ( ! empty( $extra_data ) ) {
			$user_data_to_export = array_merge( $user_data_to_export, $extra_data );
		}
	}

	$data_to_export[] = array(
		'group_id'          => 'user',
		'group_label'       => __( '用户' ),
		'group_description' => __( '用户的个人资料数据。' ),
		'item_id'           => "user-{$user->ID}",
		'data'              => $user_data_to_export,
	);

	if ( isset( $user_meta['community-events-location'] ) ) {
		$location = maybe_unserialize( $user_meta['community-events-location'][0] );

		$location_props_to_export = array(
			'description' => __( '城市' ),
			'country'     => __( '国家/地区' ),
			'latitude'    => __( '纬度' ),
			'longitude'   => __( '经度' ),
			'ip'          => __( 'IP' ),
		);

		$location_data_to_export = array();

		foreach ( $location_props_to_export as $key => $name ) {
			if ( ! empty( $location[ $key ] ) ) {
				$location_data_to_export[] = array(
					'name'  => $name,
					'value' => $location[ $key ],
				);
			}
		}

		$data_to_export[] = array(
			'group_id'          => 'community-events-location',
			'group_label'       => __( '社区活动位置' ),
			'group_description' => __( '用户的位置数据，用于GeChiUI活动及新闻挂件中的社区活动。' ),
			'item_id'           => "community-events-location-{$user->ID}",
			'data'              => $location_data_to_export,
		);
	}

	if ( isset( $user_meta['session_tokens'] ) ) {
		$session_tokens = maybe_unserialize( $user_meta['session_tokens'][0] );

		$session_tokens_props_to_export = array(
			'expiration' => __( '过期时间' ),
			'ip'         => __( 'IP' ),
			'ua'         => __( '用户代理' ),
			'login'      => __( '上次登录' ),
		);

		foreach ( $session_tokens as $token_key => $session_token ) {
			$session_tokens_data_to_export = array();

			foreach ( $session_tokens_props_to_export as $key => $name ) {
				if ( ! empty( $session_token[ $key ] ) ) {
					$value = $session_token[ $key ];
					if ( in_array( $key, array( 'expiration', 'login' ), true ) ) {
						$value = date_i18n( 'F d, Y H:i A', $value );
					}
					$session_tokens_data_to_export[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}

			$data_to_export[] = array(
				'group_id'          => 'session-tokens',
				'group_label'       => __( '会话令牌' ),
				'group_description' => __( '用户的会话令牌数据。' ),
				'item_id'           => "session-tokens-{$user->ID}-{$token_key}",
				'data'              => $session_tokens_data_to_export,
			);
		}
	}

	return array(
		'data' => $data_to_export,
		'done' => true,
	);
}

/**
 * Update log when privacy request is confirmed.
 *
 *
 * @access private
 *
 * @param int $request_id ID of the request.
 */
function _gc_privacy_account_request_confirmed( $request_id ) {
	$request = gc_get_user_request( $request_id );

	if ( ! $request ) {
		return;
	}

	if ( ! in_array( $request->status, array( 'request-pending', 'request-failed' ), true ) ) {
		return;
	}

	update_post_meta( $request_id, '_gc_user_request_confirmed_timestamp', time() );
	gc_update_post(
		array(
			'ID'          => $request_id,
			'post_status' => 'request-confirmed',
		)
	);
}

/**
 * Notify the site administrator via email when a request is confirmed.
 *
 * Without this, the admin would have to manually check the site to see if any
 * action was needed on their part yet.
 *
 *
 *
 * @param int $request_id The ID of the request.
 */
function _gc_privacy_send_request_confirmation_notification( $request_id ) {
	$request = gc_get_user_request( $request_id );

	if ( ! is_a( $request, 'GC_User_Request' ) || 'request-confirmed' !== $request->status ) {
		return;
	}

	$already_notified = (bool) get_post_meta( $request_id, '_gc_admin_notified', true );

	if ( $already_notified ) {
		return;
	}

	if ( 'export_personal_data' === $request->action_name ) {
		$manage_url = admin_url( 'export-personal-data.php' );
	} elseif ( 'remove_personal_data' === $request->action_name ) {
		$manage_url = admin_url( 'erase-personal-data.php' );
	}
	$action_description = gc_user_request_action_description( $request->action_name );

	/**
	 * Filters the recipient of the data request confirmation notification.
	 *
	 * In a Multisite environment, this will default to the email address of the
	 * network admin because, by default, single site admins do not have the
	 * capabilities required to process requests. Some networks may wish to
	 * delegate those capabilities to a single-site admin, or a dedicated person
	 * responsible for managing privacy requests.
	 *
	 *
	 * @param string          $admin_email The email address of the notification recipient.
	 * @param GC_User_Request $request     The request that is initiating the notification.
	 */
	$admin_email = apply_filters( 'user_request_confirmed_email_to', get_site_option( 'admin_email' ), $request );

	$email_data = array(
		'request'     => $request,
		'user_email'  => $request->email,
		'description' => $action_description,
		'manage_url'  => $manage_url,
		'sitename'    => gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		'siteurl'     => home_url(),
		'admin_email' => $admin_email,
	);

	$subject = sprintf(
		/* translators: Privacy data request confirmed notification email subject. 1: Site title, 2: Name of the confirmed action. */
		__( '[%1$s] 操作已确认：%2$s' ),
		$email_data['sitename'],
		$action_description
	);

	/**
	 * Filters the subject of the user request confirmation email.
	 *
	 *
	 * @param string $subject    The email subject.
	 * @param string $sitename   The name of the site.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $user_email  The email address confirming a request
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $manage_url  The link to click manage privacy requests of this type.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 *     @type string          $admin_email The administrator email receiving the mail.
	 * }
	 */
	$subject = apply_filters( 'user_request_confirmed_email_subject', $subject, $email_data['sitename'], $email_data );

	/* translators: Do not translate SITENAME, USER_EMAIL, DESCRIPTION, MANAGE_URL, SITEURL; those are placeholders. */
	$content = __(
		'您好：

站点###SITENAME###上有了新的已确认的用户数据隐私请求：

用户：###USER_EMAIL###
请求：###DESCRIPTION###

您可以在此查看和管理这些数据隐私请求：

###MANAGE_URL###

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
	);

	/**
	 * Filters the body of the user request confirmation email.
	 *
	 * The email is sent to an administrator when a user request is confirmed.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 *
	 * ###SITENAME###    The name of the site.
	 * ###USER_EMAIL###  The user email for the request.
	 * ###DESCRIPTION### Description of the action being performed so the user knows what the email is for.
	 * ###MANAGE_URL###  The URL to manage requests.
	 * ###SITEURL###     The URL to the site.
	 *
	 * @deprecated 5.8.0 Use {@see 'user_request_confirmed_email_content'} instead.
	 *                   For user erasure fulfillment email content
	 *                   use {@see 'user_erasure_fulfillment_email_content'} instead.
	 *
	 * @param string $content    The email content.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $user_email  The email address confirming a request
	 *     @type string          $description Description of the action being performed
	 *                                        so the user knows what the email is for.
	 *     @type string          $manage_url  The link to click manage privacy requests of this type.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 *     @type string          $admin_email The administrator email receiving the mail.
	 * }
	 */
	$content = apply_filters_deprecated(
		'user_confirmed_action_email_content',
		array( $content, $email_data ),
		'5.8.0',
		sprintf(
			/* translators: 1 & 2: Deprecation replacement options. */
			__( '%1$s 或 %2$s' ),
			'user_request_confirmed_email_content',
			'user_erasure_fulfillment_email_content'
		)
	);

	/**
	 * Filters the body of the user request confirmation email.
	 *
	 * The email is sent to an administrator when a user request is confirmed.
	 * The following strings have a special meaning and will get replaced dynamically:
	 *
	 * ###SITENAME###    The name of the site.
	 * ###USER_EMAIL###  The user email for the request.
	 * ###DESCRIPTION### Description of the action being performed so the user knows what the email is for.
	 * ###MANAGE_URL###  The URL to manage requests.
	 * ###SITEURL###     The URL to the site.
	 *
	 *
	 * @param string $content    The email content.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $user_email  The email address confirming a request
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $manage_url  The link to click manage privacy requests of this type.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 *     @type string          $admin_email The administrator email receiving the mail.
	 * }
	 */
	$content = apply_filters( 'user_request_confirmed_email_content', $content, $email_data );

	$content = str_replace( '###SITENAME###', $email_data['sitename'], $content );
	$content = str_replace( '###USER_EMAIL###', $email_data['user_email'], $content );
	$content = str_replace( '###DESCRIPTION###', $email_data['description'], $content );
	$content = str_replace( '###MANAGE_URL###', esc_url_raw( $email_data['manage_url'] ), $content );
	$content = str_replace( '###SITEURL###', esc_url_raw( $email_data['siteurl'] ), $content );

	$headers = '';

	/**
	 * Filters the headers of the user request confirmation email.
	 *
	 *
	 * @param string|array $headers    The email headers.
	 * @param string       $subject    The email subject.
	 * @param string       $content    The email content.
	 * @param int          $request_id The request ID.
	 * @param array        $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $user_email  The email address confirming a request
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $manage_url  The link to click manage privacy requests of this type.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 *     @type string          $admin_email The administrator email receiving the mail.
	 * }
	 */
	$headers = apply_filters( 'user_request_confirmed_email_headers', $headers, $subject, $content, $request_id, $email_data );

	$email_sent = gc_mail( $email_data['admin_email'], $subject, $content, $headers );

	if ( $email_sent ) {
		update_post_meta( $request_id, '_gc_admin_notified', true );
	}
}

/**
 * Notify the user when their erasure request is fulfilled.
 *
 * Without this, the user would never know if their data was actually erased.
 *
 *
 *
 * @param int $request_id The privacy request post ID associated with this request.
 */
function _gc_privacy_send_erasure_fulfillment_notification( $request_id ) {
	$request = gc_get_user_request( $request_id );

	if ( ! is_a( $request, 'GC_User_Request' ) || 'request-completed' !== $request->status ) {
		return;
	}

	$already_notified = (bool) get_post_meta( $request_id, '_gc_user_notified', true );

	if ( $already_notified ) {
		return;
	}

	// Localize message content for user; fallback to site default for visitors.
	if ( ! empty( $request->user_id ) ) {
		$locale = get_user_locale( $request->user_id );
	} else {
		$locale = get_locale();
	}

	$switched_locale = switch_to_locale( $locale );

	/**
	 * Filters the recipient of the data erasure fulfillment notification.
	 *
	 *
	 * @param string          $user_email The email address of the notification recipient.
	 * @param GC_User_Request $request    The request that is initiating the notification.
	 */
	$user_email = apply_filters( 'user_erasure_fulfillment_email_to', $request->email, $request );

	$email_data = array(
		'request'            => $request,
		'message_recipient'  => $user_email,
		'privacy_policy_url' => get_privacy_policy_url(),
		'sitename'           => gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		'siteurl'            => home_url(),
	);

	$subject = sprintf(
		/* translators: Erasure request fulfilled notification email subject. %s: Site title. */
		__( '[%s] 抹除请求已完成' ),
		$email_data['sitename']
	);

	/**
	 * Filters the subject of the email sent when an erasure request is completed.
	 *
	 * @deprecated 5.8.0 Use {@see 'user_erasure_fulfillment_email_subject'} instead.
	 *
	 * @param string $subject    The email subject.
	 * @param string $sitename   The name of the site.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$subject = apply_filters_deprecated(
		'user_erasure_complete_email_subject',
		array( $subject, $email_data['sitename'], $email_data ),
		'5.8.0',
		'user_erasure_fulfillment_email_subject'
	);

	/**
	 * Filters the subject of the email sent when an erasure request is completed.
	 *
	 *
	 * @param string $subject    The email subject.
	 * @param string $sitename   The name of the site.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$subject = apply_filters( 'user_erasure_fulfillment_email_subject', $subject, $email_data['sitename'], $email_data );

	/* translators: Do not translate SITENAME, SITEURL; those are placeholders. */
	$content = __(
		'您好：

您在###SITENAME###的个人数据抹除请求已被完成。

如果您有更多问题或担忧，请联系站点管理员。

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
	);

	if ( ! empty( $email_data['privacy_policy_url'] ) ) {
		/* translators: Do not translate SITENAME, SITEURL, PRIVACY_POLICY_URL; those are placeholders. */
		$content = __(
			'您好：

您在###SITENAME###的个人数据抹除请求已完成。

如果您有更多问题或担忧，请联系站点管理员。

要了解更多信息，您也可以阅读我们的隐私政策：###PRIVACY_POLICY_URL###

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
		);
	}

	/**
	 * Filters the body of the data erasure fulfillment notification.
	 *
	 * The email is sent to a user when their data erasure request is fulfilled
	 * by an administrator.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 *
	 * ###SITENAME###           The name of the site.
	 * ###PRIVACY_POLICY_URL### Privacy policy page URL.
	 * ###SITEURL###            The URL to the site.
	 *
	 * @deprecated 5.8.0 Use {@see 'user_erasure_fulfillment_email_content'} instead.
	 *                   For user request confirmation email content
	 *                   use {@see 'user_request_confirmed_email_content'} instead.
	 *
	 * @param string $content The email content.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$content = apply_filters_deprecated(
		'user_confirmed_action_email_content',
		array( $content, $email_data ),
		'5.8.0',
		sprintf(
			/* translators: 1 & 2: Deprecation replacement options. */
			__( '%1$s 或 %2$s' ),
			'user_erasure_fulfillment_email_content',
			'user_request_confirmed_email_content'
		)
	);

	/**
	 * Filters the body of the data erasure fulfillment notification.
	 *
	 * The email is sent to a user when their data erasure request is fulfilled
	 * by an administrator.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 *
	 * ###SITENAME###           The name of the site.
	 * ###PRIVACY_POLICY_URL### Privacy policy page URL.
	 * ###SITEURL###            The URL to the site.
	 *
	 *
	 * @param string $content The email content.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$content = apply_filters( 'user_erasure_fulfillment_email_content', $content, $email_data );

	$content = str_replace( '###SITENAME###', $email_data['sitename'], $content );
	$content = str_replace( '###PRIVACY_POLICY_URL###', $email_data['privacy_policy_url'], $content );
	$content = str_replace( '###SITEURL###', esc_url_raw( $email_data['siteurl'] ), $content );

	$headers = '';

	/**
	 * Filters the headers of the data erasure fulfillment notification.
	 *
	 * @deprecated 5.8.0 Use {@see 'user_erasure_fulfillment_email_headers'} instead.
	 *
	 * @param string|array $headers    The email headers.
	 * @param string       $subject    The email subject.
	 * @param string       $content    The email content.
	 * @param int          $request_id The request ID.
	 * @param array        $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$headers = apply_filters_deprecated(
		'user_erasure_complete_email_headers',
		array( $headers, $subject, $content, $request_id, $email_data ),
		'5.8.0',
		'user_erasure_fulfillment_email_headers'
	);

	/**
	 * Filters the headers of the data erasure fulfillment notification.
	 *
	 *
	 * @param string|array $headers    The email headers.
	 * @param string       $subject    The email subject.
	 * @param string       $content    The email content.
	 * @param int          $request_id The request ID.
	 * @param array        $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request            User request object.
	 *     @type string          $message_recipient  The address that the email will be sent to. Defaults
	 *                                               to the value of `$request->email`, but can be changed
	 *                                               by the `user_erasure_fulfillment_email_to` filter.
	 *     @type string          $privacy_policy_url Privacy policy URL.
	 *     @type string          $sitename           The site name sending the mail.
	 *     @type string          $siteurl            The site URL sending the mail.
	 * }
	 */
	$headers = apply_filters( 'user_erasure_fulfillment_email_headers', $headers, $subject, $content, $request_id, $email_data );

	$email_sent = gc_mail( $user_email, $subject, $content, $headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	if ( $email_sent ) {
		update_post_meta( $request_id, '_gc_user_notified', true );
	}
}

/**
 * Return request confirmation message HTML.
 *
 *
 * @access private
 *
 * @param int $request_id The request ID being confirmed.
 * @return string The confirmation message.
 */
function _gc_privacy_account_request_confirmed_message( $request_id ) {
	$request = gc_get_user_request( $request_id );

	$message  = '<p class="success">' . __( '操作已被确认。' ) . '</p>';
	$message .= '<p>' . __( '站点管理员已获通知，并会尽快处理您的请求。' ) . '</p>';

	if ( $request && in_array( $request->action_name, _gc_privacy_action_request_types(), true ) ) {
		if ( 'export_personal_data' === $request->action_name ) {
			$message  = '<p class="success">' . __( '感谢您确认您的导出请求。' ) . '</p>';
			$message .= '<p>' . __( '站点管理员已获通知。在您的请求获处理后，您将收到一封包含您的个人导出数据的下载链接的邮件。' ) . '</p>';
		} elseif ( 'remove_personal_data' === $request->action_name ) {
			$message  = '<p class="success">' . __( '感谢您确认您的抹除请求。' ) . '</p>';
			$message .= '<p>' . __( '站点管理员已获通知。在您的数据被抹除后，您将收到一封确认邮件。' ) . '</p>';
		}
	}

	/**
	 * Filters the message displayed to a user when they confirm a data request.
	 *
	 *
	 * @param string $message    The message to the user.
	 * @param int    $request_id The ID of the request being confirmed.
	 */
	$message = apply_filters( 'user_request_action_confirmed_message', $message, $request_id );

	return $message;
}

/**
 * Create and log a user request to perform a specific action.
 *
 * Requests are stored inside a post type named `user_request` since they can apply to both
 * users on the site, or guests without a user account.
 *
 *
 *
 *
 * @param string $email_address           User email address. This can be the address of a registered
 *                                        or non-registered user.
 * @param string $action_name             Name of the action that is being confirmed. Required.
 * @param array  $request_data            Misc data you want to send with the verification request and pass
 *                                        to the actions once the request is confirmed.
 * @param string $status                  Optional request status (pending or confirmed). Default 'pending'.
 * @return int|GC_Error                   Returns the request ID if successful, or a GC_Error object on failure.
 */
function gc_create_user_request( $email_address = '', $action_name = '', $request_data = array(), $status = 'pending' ) {
	$email_address = sanitize_email( $email_address );
	$action_name   = sanitize_key( $action_name );

	if ( ! is_email( $email_address ) ) {
		return new GC_Error( 'invalid_email', __( '电子邮箱无效。' ) );
	}

	if ( ! in_array( $action_name, _gc_privacy_action_request_types(), true ) ) {
		return new GC_Error( 'invalid_action', __( '无效的动作名称。' ) );
	}

	if ( ! in_array( $status, array( 'pending', 'confirmed' ), true ) ) {
		return new GC_Error( 'invalid_status', __( '无效的请求状态。' ) );
	}

	$user    = get_user_by( 'email', $email_address );
	$user_id = $user && ! is_gc_error( $user ) ? $user->ID : 0;

	// Check for duplicates.
	$requests_query = new GC_Query(
		array(
			'post_type'     => 'user_request',
			'post_name__in' => array( $action_name ), // Action name stored in post_name column.
			'title'         => $email_address,        // Email address stored in post_title column.
			'post_status'   => array(
				'request-pending',
				'request-confirmed',
			),
			'fields'        => 'ids',
		)
	);

	if ( $requests_query->found_posts ) {
		return new GC_Error( 'duplicate_request', __( '此电子邮箱已存在未完成的个人数据请求。' ) );
	}

	$request_id = gc_insert_post(
		array(
			'post_author'   => $user_id,
			'post_name'     => $action_name,
			'post_title'    => $email_address,
			'post_content'  => gc_json_encode( $request_data ),
			'post_status'   => 'request-' . $status,
			'post_type'     => 'user_request',
			'post_date'     => current_time( 'mysql', false ),
			'post_date_gmt' => current_time( 'mysql', true ),
		),
		true
	);

	return $request_id;
}

/**
 * Get action description from the name and return a string.
 *
 *
 *
 * @param string $action_name Action name of the request.
 * @return string Human readable action name.
 */
function gc_user_request_action_description( $action_name ) {
	switch ( $action_name ) {
		case 'export_personal_data':
			$description = __( '导出个人数据' );
			break;
		case 'remove_personal_data':
			$description = __( '抹除个人数据' );
			break;
		default:
			/* translators: %s: Action name. */
			$description = sprintf( __( '确认“%s”操作' ), $action_name );
			break;
	}

	/**
	 * Filters the user action description.
	 *
	 *
	 * @param string $description The default description.
	 * @param string $action_name The name of the request.
	 */
	return apply_filters( 'user_request_action_description', $description, $action_name );
}

/**
 * Send a confirmation request email to confirm an action.
 *
 * If the request is not already pending, it will be updated.
 *
 *
 *
 * @param string $request_id ID of the request created via gc_create_user_request().
 * @return true|GC_Error True on success, `GC_Error` on failure.
 */
function gc_send_user_request( $request_id ) {
	$request_id = absint( $request_id );
	$request    = gc_get_user_request( $request_id );

	if ( ! $request ) {
		return new GC_Error( 'invalid_request', __( '无效的个人数据请求。' ) );
	}

	// Localize message content for user; fallback to site default for visitors.
	if ( ! empty( $request->user_id ) ) {
		$locale = get_user_locale( $request->user_id );
	} else {
		$locale = get_locale();
	}

	$switched_locale = switch_to_locale( $locale );

	$email_data = array(
		'request'     => $request,
		'email'       => $request->email,
		'description' => gc_user_request_action_description( $request->action_name ),
		'confirm_url' => add_query_arg(
			array(
				'action'      => 'confirmaction',
				'request_id'  => $request_id,
				'confirm_key' => gc_generate_user_request_key( $request_id ),
			),
			gc_login_url()
		),
		'sitename'    => gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		'siteurl'     => home_url(),
	);

	/* translators: Confirm privacy data request notification email subject. 1: Site title, 2: Name of the action. */
	$subject = sprintf( __( '[%1$s] 确认操作：%2$s' ), $email_data['sitename'], $email_data['description'] );

	/**
	 * Filters the subject of the email sent when an account action is attempted.
	 *
	 *
	 * @param string $subject    The email subject.
	 * @param string $sitename   The name of the site.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $email       The email address this is being sent to.
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $confirm_url The link to click on to confirm the account action.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 * }
	 */
	$subject = apply_filters( 'user_request_action_email_subject', $subject, $email_data['sitename'], $email_data );

	/* translators: Do not translate DESCRIPTION, CONFIRM_URL, SITENAME, SITEURL: those are placeholders. */
	$content = __(
		'您好：

我们收到了在您的账户上进行如下操作的请求：

     ###DESCRIPTION###

要确认此操作，请点击以下链接：
###CONFIRM_URL###

如果您并未请求修改，则可安全地忽视并删除此邮件。

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
	);

	/**
	 * Filters the text of the email sent when an account action is attempted.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 *
	 * ###DESCRIPTION### Description of the action being performed so the user knows what the email is for.
	 * ###CONFIRM_URL### The link to click on to confirm the account action.
	 * ###SITENAME###    The name of the site.
	 * ###SITEURL###     The URL to the site.
	 *
	 *
	 * @param string $content Text in the email.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $email       The email address this is being sent to.
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $confirm_url The link to click on to confirm the account action.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 * }
	 */
	$content = apply_filters( 'user_request_action_email_content', $content, $email_data );

	$content = str_replace( '###DESCRIPTION###', $email_data['description'], $content );
	$content = str_replace( '###CONFIRM_URL###', esc_url_raw( $email_data['confirm_url'] ), $content );
	$content = str_replace( '###EMAIL###', $email_data['email'], $content );
	$content = str_replace( '###SITENAME###', $email_data['sitename'], $content );
	$content = str_replace( '###SITEURL###', esc_url_raw( $email_data['siteurl'] ), $content );

	$headers = '';

	/**
	 * Filters the headers of the email sent when an account action is attempted.
	 *
	 *
	 * @param string|array $headers    The email headers.
	 * @param string       $subject    The email subject.
	 * @param string       $content    The email content.
	 * @param int          $request_id The request ID.
	 * @param array        $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request     User request object.
	 *     @type string          $email       The email address this is being sent to.
	 *     @type string          $description Description of the action being performed so the user knows what the email is for.
	 *     @type string          $confirm_url The link to click on to confirm the account action.
	 *     @type string          $sitename    The site name sending the mail.
	 *     @type string          $siteurl     The site URL sending the mail.
	 * }
	 */
	$headers = apply_filters( 'user_request_action_email_headers', $headers, $subject, $content, $request_id, $email_data );

	$email_sent = gc_mail( $email_data['email'], $subject, $content, $headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	if ( ! $email_sent ) {
		return new GC_Error( 'privacy_email_error', __( '无法发送个人数据导出确认邮件。' ) );
	}

	return true;
}

/**
 * Returns a confirmation key for a user action and stores the hashed version for future comparison.
 *
 *
 *
 * @param int $request_id Request ID.
 * @return string Confirmation key.
 */
function gc_generate_user_request_key( $request_id ) {
	global $gc_hasher;

	// Generate something random for a confirmation key.
	$key = gc_generate_password( 20, false );

	// Return the key, hashed.
	if ( empty( $gc_hasher ) ) {
		require_once ABSPATH . GCINC . '/class-phpass.php';
		$gc_hasher = new PasswordHash( 8, true );
	}

	gc_update_post(
		array(
			'ID'            => $request_id,
			'post_status'   => 'request-pending',
			'post_password' => $gc_hasher->HashPassword( $key ),
		)
	);

	return $key;
}

/**
 * Validate a user request by comparing the key with the request's key.
 *
 *
 *
 * @param string $request_id ID of the request being confirmed.
 * @param string $key        Provided key to validate.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function gc_validate_user_request_key( $request_id, $key ) {
	global $gc_hasher;

	$request_id       = absint( $request_id );
	$request          = gc_get_user_request( $request_id );
	$saved_key        = $request->confirm_key;
	$key_request_time = $request->modified_timestamp;

	if ( ! $request || ! $saved_key || ! $key_request_time ) {
		return new GC_Error( 'invalid_request', __( '无效的个人数据请求。' ) );
	}

	if ( ! in_array( $request->status, array( 'request-pending', 'request-failed' ), true ) ) {
		return new GC_Error( 'expired_request', __( '此个人数据请求已过期。' ) );
	}

	if ( empty( $key ) ) {
		return new GC_Error( 'missing_key', __( '此个人数据请求中缺少确认密钥。' ) );
	}

	if ( empty( $gc_hasher ) ) {
		require_once ABSPATH . GCINC . '/class-phpass.php';
		$gc_hasher = new PasswordHash( 8, true );
	}

	/**
	 * Filters the expiration time of confirm keys.
	 *
	 *
	 * @param int $expiration The expiration time in seconds.
	 */
	$expiration_duration = (int) apply_filters( 'user_request_key_expiration', DAY_IN_SECONDS );
	$expiration_time     = $key_request_time + $expiration_duration;

	if ( ! $gc_hasher->CheckPassword( $key, $saved_key ) ) {
		return new GC_Error( 'invalid_key', __( '此个人数据请求的确认密钥是无效的。' ) );
	}

	if ( ! $expiration_time || time() > $expiration_time ) {
		return new GC_Error( 'expired_key', __( '此个人数据请求的确认密钥已过期。' ) );
	}

	return true;
}

/**
 * Return the user request object for the specified request ID.
 *
 *
 *
 * @param int $request_id The ID of the user request.
 * @return GC_User_Request|false
 */
function gc_get_user_request( $request_id ) {
	$request_id = absint( $request_id );
	$post       = get_post( $request_id );

	if ( ! $post || 'user_request' !== $post->post_type ) {
		return false;
	}

	return new GC_User_Request( $post );
}

/**
 * Checks if AppKeys is supported.
 *
 * AppKeys is supported only by sites using SSL or local environments
 * but may be made available using the {@see 'gc_is_appkeys_available'} filter.
 *
 *
 *
 * @return bool
 */
function gc_is_appkeys_supported() {
	return is_ssl() || 'local' === gc_get_environment_type();
}

/**
 * Checks if AppKeys is globally available.
 *
 * By default, AppKeys is available to all sites using SSL or to local environments.
 * Use the {@see 'gc_is_appkeys_available'} filter to adjust its availability.
 *
 *
 *
 * @return bool
 */
function gc_is_appkeys_available() {
	/**
	 * Filters whether AppKeys is available.
	 *
	 *
	 * @param bool $available True if available, false otherwise.
	 */
	return apply_filters( 'gc_is_appkeys_available', gc_is_appkeys_supported() );
}

/**
 * Checks if AppKeys is available for a specific user.
 *
 * By default all users can use AppKeys. Use {@see 'gc_is_appkeys_available_for_user'}
 * to restrict availability to certain users.
 *
 *
 *
 * @param int|GC_User $user The user to check.
 * @return bool
 */
function gc_is_appkeys_available_for_user( $user ) {
	if ( ! gc_is_appkeys_available() ) {
		return false;
	}

	if ( ! is_object( $user ) ) {
		$user = get_userdata( $user );
	}

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	/**
	 * Filters whether AppKeys is available for a specific user.
	 *
	 *
	 * @param bool    $available True if available, false otherwise.
	 * @param GC_User $user      The user to check.
	 */
	return apply_filters( 'gc_is_appkeys_available_for_user', true, $user );
}
