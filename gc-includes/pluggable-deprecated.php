<?php
/**
 * Deprecated pluggable functions from past GeChiUI versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed in a
 * later version.
 *
 * Deprecated warnings are also thrown if one of these functions is being defined by a plugin.
 *
 * @package GeChiUI
 * @subpackage Deprecated
 * @see pluggable.php
 */

/*
 * Deprecated functions come here to die.
 */

if ( !function_exists('set_current_user') ) :
/**
 * Changes the current user by ID or name.
 *
 * Set $id to null and specify a name if you do not know a user's ID.
 *
 * @since 2.0.1
 * @deprecated 3.0.0 Use gc_set_current_user()
 * @see gc_set_current_user()
 *
 * @param int|null $id User ID.
 * @param string $name Optional. The user's username
 * @return GC_User returns gc_set_current_user()
 */
function set_current_user($id, $name = '') {
	_deprecated_function( __FUNCTION__, '3.0.0', 'gc_set_current_user()' );
	return gc_set_current_user($id, $name);
}
endif;

if ( !function_exists('get_currentuserinfo') ) :
/**
 * Populate global variables with information about the currently logged in user.
 *
 * @deprecated 4.5.0 Use gc_get_current_user()
 * @see gc_get_current_user()
 *
 * @return bool|GC_User False on XMLRPC Request and invalid auth cookie, GC_User instance otherwise.
 */
function get_currentuserinfo() {
	_deprecated_function( __FUNCTION__, '4.5.0', 'gc_get_current_user()' );

	return _gc_get_current_user();
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
/**
 * Retrieve user info by login name.
 *
 * @deprecated 3.3.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $user_login User's username
 * @return bool|object False on failure, User DB row object
 */
function get_userdatabylogin($user_login) {
	_deprecated_function( __FUNCTION__, '3.3.0', "get_user_by('login')" );
	return get_user_by('login', $user_login);
}
endif;

if ( !function_exists('get_user_by_email') ) :
/**
 * Retrieve user info by email.
 *
 * @deprecated 3.3.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $email User's email address
 * @return bool|object False on failure, User DB row object
 */
function get_user_by_email($email) {
	_deprecated_function( __FUNCTION__, '3.3.0', "get_user_by('email')" );
	return get_user_by('email', $email);
}
endif;

if ( !function_exists('gc_setcookie') ) :
/**
 * Sets a cookie for a user who just logged in. This function is deprecated.
 *
 * @deprecated 2.5.0 Use gc_set_auth_cookie()
 * @see gc_set_auth_cookie()
 *
 * @param string $username The user's username
 * @param string $password Optional. The user's password
 * @param bool $already_md5 Optional. Whether the password has already been through MD5
 * @param string $home Optional. Will be used instead of COOKIEPATH if set
 * @param string $siteurl Optional. Will be used instead of SITECOOKIEPATH if set
 * @param bool $remember Optional. Remember that the user is logged in
 */
function gc_setcookie($username, $password = '', $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'gc_set_auth_cookie()' );
	$user = get_user_by('login', $username);
	gc_set_auth_cookie($user->ID, $remember);
}
else :
	_deprecated_function( 'gc_setcookie', '2.5.0', 'gc_set_auth_cookie()' );
endif;

if ( !function_exists('gc_clearcookie') ) :
/**
 * Clears the authentication cookie, logging the user out. This function is deprecated.
 *
 * @deprecated 2.5.0 Use gc_clear_auth_cookie()
 * @see gc_clear_auth_cookie()
 */
function gc_clearcookie() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'gc_clear_auth_cookie()' );
	gc_clear_auth_cookie();
}
else :
	_deprecated_function( 'gc_clearcookie', '2.5.0', 'gc_clear_auth_cookie()' );
endif;

if ( !function_exists('gc_get_cookie_login') ):
/**
 * Gets the user cookie login. This function is deprecated.
 *
 * This function is deprecated and should no longer be extended as it won't be
 * used anywhere in GeChiUI. Also, plugins shouldn't use it either.
 *
 * @since 2.0.3
 * @deprecated 2.5.0
 *
 * @return bool Always returns false
 */
function gc_get_cookie_login() {
	_deprecated_function( __FUNCTION__, '2.5.0' );
	return false;
}
else :
	_deprecated_function( 'gc_get_cookie_login', '2.5.0' );
endif;

if ( !function_exists('gc_login') ) :
/**
 * Checks a users login information and logs them in if it checks out. This function is deprecated.
 *
 * Use the global $error to get the reason why the login failed. If the username
 * is blank, no error will be set, so assume blank username on that case.
 *
 * Plugins extending this function should also provide the global $error and set
 * what the error is, so that those checking the global for why there was a
 * failure can utilize it later.
 *
 * @deprecated 2.5.0 Use gc_signon()
 * @see gc_signon()
 *
 * @global string $error Error when false is returned
 *
 * @param string $username   User's username
 * @param string $password   User's password
 * @param string $deprecated Not used
 * @return bool True on successful check, false on login failure.
 */
function gc_login($username, $password, $deprecated = '') {
	_deprecated_function( __FUNCTION__, '2.5.0', 'gc_signon()' );
	global $error;

	$user = gc_authenticate($username, $password);

	if ( ! is_gc_error($user) )
		return true;

	$error = $user->get_error_message();
	return false;
}
else :
	_deprecated_function( 'gc_login', '2.5.0', 'gc_signon()' );
endif;

/**
 * GeChiUI AtomPub API implementation.
 *
 * Originally stored in gc-app.php, and later gc-includes/class-gc-atom-server.php.
 * It is kept here in case a plugin directly referred to the class.
 *
 * @deprecated 3.5.0
 *
 * @link https://www.gechiui.com/plugins/atom-publishing-protocol/
 */
if ( ! class_exists( 'gc_atom_server', false ) ) {
	class gc_atom_server {
		public function __call( $name, $arguments ) {
			_deprecated_function( __CLASS__ . '::' . $name, '3.5.0', 'the Atom Publishing Protocol plugin' );
		}

		public static function __callStatic( $name, $arguments ) {
			_deprecated_function( __CLASS__ . '::' . $name, '3.5.0', 'the Atom Publishing Protocol plugin' );
		}
	}
}
