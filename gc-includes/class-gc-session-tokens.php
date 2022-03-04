<?php
/**
 * Session API: GC_Session_Tokens class
 *
 * @package GeChiUI
 * @subpackage Session
 *
 */

/**
 * Abstract class for managing user session tokens.
 *
 *
 */
abstract class GC_Session_Tokens {

	/**
	 * User ID.
	 *
	 * @var int User ID.
	 */
	protected $user_id;

	/**
	 * Protected constructor. Use the `get_instance()` method to get the instance.
	 *
	 *
	 * @param int $user_id User whose session to manage.
	 */
	protected function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Retrieves a session manager instance for a user.
	 *
	 * This method contains a {@see 'session_token_manager'} filter, allowing a plugin to swap out
	 * the session manager for a subclass of `GC_Session_Tokens`.
	 *
	 *
	 * @param int $user_id User whose session to manage.
	 * @return GC_Session_Tokens The session object, which is by default an instance of
	 *                           the `GC_User_Meta_Session_Tokens` class.
	 */
	final public static function get_instance( $user_id ) {
		/**
		 * Filters the class name for the session token manager.
		 *
		 *
		 * @param string $session Name of class to use as the manager.
		 *                        Default 'GC_User_Meta_Session_Tokens'.
		 */
		$manager = apply_filters( 'session_token_manager', 'GC_User_Meta_Session_Tokens' );
		return new $manager( $user_id );
	}

	/**
	 * Hashes the given session token for storage.
	 *
	 *
	 * @param string $token Session token to hash.
	 * @return string A hash of the session token (a verifier).
	 */
	private function hash_token( $token ) {
		// If ext/hash is not present, use sha1() instead.
		if ( function_exists( 'hash' ) ) {
			return hash( 'sha256', $token );
		} else {
			return sha1( $token );
		}
	}

	/**
	 * Retrieves a user's session for the given token.
	 *
	 *
	 * @param string $token Session token.
	 * @return array|null The session, or null if it does not exist.
	 */
	final public function get( $token ) {
		$verifier = $this->hash_token( $token );
		return $this->get_session( $verifier );
	}

	/**
	 * Validates the given session token for authenticity and validity.
	 *
	 * Checks that the given token is present and hasn't expired.
	 *
	 *
	 * @param string $token Token to verify.
	 * @return bool Whether the token is valid for the user.
	 */
	final public function verify( $token ) {
		$verifier = $this->hash_token( $token );
		return (bool) $this->get_session( $verifier );
	}

	/**
	 * Generates a session token and attaches session information to it.
	 *
	 * A session token is a long, random string. It is used in a cookie
	 * to link that cookie to an expiration time and to ensure the cookie
	 * becomes invalidated when the user logs out.
	 *
	 * This function generates a token and stores it with the associated
	 * expiration time (and potentially other session information via the
	 * {@see 'attach_session_information'} filter).
	 *
	 *
	 * @param int $expiration Session expiration timestamp.
	 * @return string Session token.
	 */
	final public function create( $expiration ) {
		/**
		 * Filters the information attached to the newly created session.
		 *
		 * Can be used to attach further information to a session.
		 *
		 *
		 * @param array $session Array of extra data.
		 * @param int   $user_id User ID.
		 */
		$session               = apply_filters( 'attach_session_information', array(), $this->user_id );
		$session['expiration'] = $expiration;

		// IP address.
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$session['ip'] = $_SERVER['REMOTE_ADDR'];
		}

		// User-agent.
		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$session['ua'] = gc_unslash( $_SERVER['HTTP_USER_AGENT'] );
		}

		// Timestamp.
		$session['login'] = time();

		$token = gc_generate_password( 43, false, false );

		$this->update( $token, $session );

		return $token;
	}

	/**
	 * Updates the data for the session with the given token.
	 *
	 *
	 * @param string $token Session token to update.
	 * @param array  $session Session information.
	 */
	final public function update( $token, $session ) {
		$verifier = $this->hash_token( $token );
		$this->update_session( $verifier, $session );
	}

	/**
	 * Destroys the session with the given token.
	 *
	 *
	 * @param string $token Session token to destroy.
	 */
	final public function destroy( $token ) {
		$verifier = $this->hash_token( $token );
		$this->update_session( $verifier, null );
	}

	/**
	 * Destroys all sessions for this user except the one with the given token (presumably the one in use).
	 *
	 *
	 * @param string $token_to_keep Session token to keep.
	 */
	final public function destroy_others( $token_to_keep ) {
		$verifier = $this->hash_token( $token_to_keep );
		$session  = $this->get_session( $verifier );
		if ( $session ) {
			$this->destroy_other_sessions( $verifier );
		} else {
			$this->destroy_all_sessions();
		}
	}

	/**
	 * Determines whether a session is still valid, based on its expiration timestamp.
	 *
	 *
	 * @param array $session Session to check.
	 * @return bool Whether session is valid.
	 */
	final protected function is_still_valid( $session ) {
		return $session['expiration'] >= time();
	}

	/**
	 * Destroys all sessions for a user.
	 *
	 */
	final public function destroy_all() {
		$this->destroy_all_sessions();
	}

	/**
	 * Destroys all sessions for all users.
	 *
	 */
	final public static function destroy_all_for_all_users() {
		/** This filter is documented in gc-includes/class-gc-session-tokens.php */
		$manager = apply_filters( 'session_token_manager', 'GC_User_Meta_Session_Tokens' );
		call_user_func( array( $manager, 'drop_sessions' ) );
	}

	/**
	 * Retrieves all sessions for a user.
	 *
	 *
	 * @return array Sessions for a user.
	 */
	final public function get_all() {
		return array_values( $this->get_sessions() );
	}

	/**
	 * Retrieves all sessions of the user.
	 *
	 *
	 * @return array Sessions of the user.
	 */
	abstract protected function get_sessions();

	/**
	 * Retrieves a session based on its verifier (token hash).
	 *
	 *
	 * @param string $verifier Verifier for the session to retrieve.
	 * @return array|null The session, or null if it does not exist.
	 */
	abstract protected function get_session( $verifier );

	/**
	 * Updates a session based on its verifier (token hash).
	 *
	 * Omitting the second argument destroys the session.
	 *
	 *
	 * @param string $verifier Verifier for the session to update.
	 * @param array  $session  Optional. Session. Omitting this argument destroys the session.
	 */
	abstract protected function update_session( $verifier, $session = null );

	/**
	 * Destroys all sessions for this user, except the single session with the given verifier.
	 *
	 *
	 * @param string $verifier Verifier of the session to keep.
	 */
	abstract protected function destroy_other_sessions( $verifier );

	/**
	 * Destroys all sessions for the user.
	 *
	 */
	abstract protected function destroy_all_sessions();

	/**
	 * Destroys all sessions for all users.
	 *
	 */
	public static function drop_sessions() {}
}
