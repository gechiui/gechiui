<?php
/**
 * Error Protection API: GC_Recovery_Mode_Cookie_Service class
 *
 * @package GeChiUI
 *
 */

/**
 * Core class used to set, validate, and clear cookies that identify a Recovery Mode session.
 *
 *
 */
final class GC_Recovery_Mode_Cookie_Service {

	/**
	 * Checks whether the recovery mode cookie is set.
	 *
	 *
	 * @return bool True if the cookie is set, false otherwise.
	 */
	public function is_cookie_set() {
		return ! empty( $_COOKIE[ RECOVERY_MODE_COOKIE ] );
	}

	/**
	 * Sets the recovery mode cookie.
	 *
	 * This must be immediately followed by exiting the request.
	 *
	 */
	public function set_cookie() {

		$value = $this->generate_cookie();

		/**
		 * Filters the length of time a Recovery Mode cookie is valid for.
		 *
		 *
		 * @param int $length Length in seconds.
		 */
		$length = apply_filters( 'recovery_mode_cookie_length', WEEK_IN_SECONDS );

		$expire = time() + $length;

		setcookie( RECOVERY_MODE_COOKIE, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

		if ( COOKIEPATH !== SITECOOKIEPATH ) {
			setcookie( RECOVERY_MODE_COOKIE, $value, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}
	}

	/**
	 * Clears the recovery mode cookie.
	 *
	 */
	public function clear_cookie() {
		setcookie( RECOVERY_MODE_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( RECOVERY_MODE_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Validates the recovery mode cookie.
	 *
	 *
	 * @param string $cookie Optionally specify the cookie string.
	 *                       If omitted, it will be retrieved from the super global.
	 * @return true|GC_Error True on success, error object on failure.
	 */
	public function validate_cookie( $cookie = '' ) {

		if ( ! $cookie ) {
			if ( empty( $_COOKIE[ RECOVERY_MODE_COOKIE ] ) ) {
				return new GC_Error( 'no_cookie', __( '没有cookie存在。' ) );
			}

			$cookie = $_COOKIE[ RECOVERY_MODE_COOKIE ];
		}

		$parts = $this->parse_cookie( $cookie );

		if ( is_gc_error( $parts ) ) {
			return $parts;
		}

		list( , $created_at, $random, $signature ) = $parts;

		if ( ! ctype_digit( $created_at ) ) {
			return new GC_Error( 'invalid_created_at', __( '无效的cookie格式。' ) );
		}

		/** This filter is documented in gc-includes/class-gc-recovery-mode-cookie-service.php */
		$length = apply_filters( 'recovery_mode_cookie_length', WEEK_IN_SECONDS );

		if ( time() > $created_at + $length ) {
			return new GC_Error( 'expired', __( 'Cookie已过期。' ) );
		}

		$to_sign = sprintf( 'recovery_mode|%s|%s', $created_at, $random );
		$hashed  = $this->recovery_mode_hash( $to_sign );

		if ( ! hash_equals( $signature, $hashed ) ) {
			return new GC_Error( 'signature_mismatch', __( '无效cookie。' ) );
		}

		return true;
	}

	/**
	 * Gets the session identifier from the cookie.
	 *
	 * The cookie should be validated before calling this API.
	 *
	 *
	 * @param string $cookie Optionally specify the cookie string.
	 *                       If omitted, it will be retrieved from the super global.
	 * @return string|GC_Error Session ID on success, or error object on failure.
	 */
	public function get_session_id_from_cookie( $cookie = '' ) {
		if ( ! $cookie ) {
			if ( empty( $_COOKIE[ RECOVERY_MODE_COOKIE ] ) ) {
				return new GC_Error( 'no_cookie', __( '没有cookie存在。' ) );
			}

			$cookie = $_COOKIE[ RECOVERY_MODE_COOKIE ];
		}

		$parts = $this->parse_cookie( $cookie );
		if ( is_gc_error( $parts ) ) {
			return $parts;
		}

		list( , , $random ) = $parts;

		return sha1( $random );
	}

	/**
	 * Parses the cookie into its four parts.
	 *
	 *
	 * @param string $cookie Cookie content.
	 * @return array|GC_Error Cookie parts array, or error object on failure.
	 */
	private function parse_cookie( $cookie ) {
		$cookie = base64_decode( $cookie );
		$parts  = explode( '|', $cookie );

		if ( 4 !== count( $parts ) ) {
			return new GC_Error( 'invalid_format', __( '无效的cookie格式。' ) );
		}

		return $parts;
	}

	/**
	 * Generates the recovery mode cookie value.
	 *
	 * The cookie is a base64 encoded string with the following format:
	 *
	 * recovery_mode|iat|rand|signature
	 *
	 * Where "recovery_mode" is a constant string,
	 * iat is the time the cookie was generated at,
	 * rand is a randomly generated password that is also used as a session identifier
	 * and signature is an hmac of the preceding 3 parts.
	 *
	 *
	 * @return string Generated cookie content.
	 */
	private function generate_cookie() {
		$to_sign = sprintf( 'recovery_mode|%s|%s', time(), gc_generate_password( 20, false ) );
		$signed  = $this->recovery_mode_hash( $to_sign );

		return base64_encode( sprintf( '%s|%s', $to_sign, $signed ) );
	}

	/**
	 * Gets a form of `gc_hash()` specific to Recovery Mode.
	 *
	 * We cannot use `gc_hash()` because it is defined in `pluggable.php` which is not loaded until after plugins are loaded,
	 * which is too late to verify the recovery mode cookie.
	 *
	 * This tries to use the `AUTH` salts first, but if they aren't valid specific salts will be generated and stored.
	 *
	 *
	 * @param string $data Data to hash.
	 * @return string|false The hashed $data, or false on failure.
	 */
	private function recovery_mode_hash( $data ) {
		if ( ! defined( 'AUTH_KEY' ) || AUTH_KEY === 'put your unique phrase here' ) {
			$auth_key = get_site_option( 'recovery_mode_auth_key' );

			if ( ! $auth_key ) {
				if ( ! function_exists( 'gc_generate_password' ) ) {
					require_once ABSPATH . GCINC . '/pluggable.php';
				}

				$auth_key = gc_generate_password( 64, true, true );
				update_site_option( 'recovery_mode_auth_key', $auth_key );
			}
		} else {
			$auth_key = AUTH_KEY;
		}

		if ( ! defined( 'AUTH_SALT' ) || AUTH_SALT === 'put your unique phrase here' || AUTH_SALT === $auth_key ) {
			$auth_salt = get_site_option( 'recovery_mode_auth_salt' );

			if ( ! $auth_salt ) {
				if ( ! function_exists( 'gc_generate_password' ) ) {
					require_once ABSPATH . GCINC . '/pluggable.php';
				}

				$auth_salt = gc_generate_password( 64, true, true );
				update_site_option( 'recovery_mode_auth_salt', $auth_salt );
			}
		} else {
			$auth_salt = AUTH_SALT;
		}

		$secret = $auth_key . $auth_salt;

		return hash_hmac( 'sha1', $data, $secret );
	}
}
