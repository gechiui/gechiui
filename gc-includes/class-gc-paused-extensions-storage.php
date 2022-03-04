<?php
/**
 * Error Protection API: GC_Paused_Extensions_Storage class
 *
 * @package GeChiUI
 *
 */

/**
 * Core class used for storing paused extensions.
 *
 *
 */
class GC_Paused_Extensions_Storage {

	/**
	 * Type of extension. Used to key extension storage.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $extension_type Extension type. Either 'plugin' or 'theme'.
	 */
	public function __construct( $extension_type ) {
		$this->type = $extension_type;
	}

	/**
	 * Records an extension error.
	 *
	 * Only one error is stored per extension, with subsequent errors for the same extension overriding the
	 * previously stored error.
	 *
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @param array  $error     {
	 *     Error that was triggered.
	 *
	 *     @type string $type    The error type.
	 *     @type string $file    The name of the file in which the error occurred.
	 *     @type string $line    The line number in which the error occurred.
	 *     @type string $message The error message.
	 * }
	 * @return bool True on success, false on failure.
	 */
	public function set( $extension, $error ) {
		if ( ! $this->is_api_loaded() ) {
			return false;
		}

		$option_name = $this->get_option_name();

		if ( ! $option_name ) {
			return false;
		}

		$paused_extensions = (array) get_option( $option_name, array() );

		// Do not update if the error is already stored.
		if ( isset( $paused_extensions[ $this->type ][ $extension ] ) && $paused_extensions[ $this->type ][ $extension ] === $error ) {
			return true;
		}

		$paused_extensions[ $this->type ][ $extension ] = $error;

		return update_option( $option_name, $paused_extensions );
	}

	/**
	 * Forgets a previously recorded extension error.
	 *
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $extension ) {
		if ( ! $this->is_api_loaded() ) {
			return false;
		}

		$option_name = $this->get_option_name();

		if ( ! $option_name ) {
			return false;
		}

		$paused_extensions = (array) get_option( $option_name, array() );

		// Do not delete if no error is stored.
		if ( ! isset( $paused_extensions[ $this->type ][ $extension ] ) ) {
			return true;
		}

		unset( $paused_extensions[ $this->type ][ $extension ] );

		if ( empty( $paused_extensions[ $this->type ] ) ) {
			unset( $paused_extensions[ $this->type ] );
		}

		// Clean up the entire option if we're removing the only error.
		if ( ! $paused_extensions ) {
			return delete_option( $option_name );
		}

		return update_option( $option_name, $paused_extensions );
	}

	/**
	 * Gets the error for an extension, if paused.
	 *
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @return array|null Error that is stored, or null if the extension is not paused.
	 */
	public function get( $extension ) {
		if ( ! $this->is_api_loaded() ) {
			return null;
		}

		$paused_extensions = $this->get_all();

		if ( ! isset( $paused_extensions[ $extension ] ) ) {
			return null;
		}

		return $paused_extensions[ $extension ];
	}

	/**
	 * Gets the paused extensions with their errors.
	 *
	 *
	 * @return array Associative array of extension slugs to the error recorded.
	 */
	public function get_all() {
		if ( ! $this->is_api_loaded() ) {
			return array();
		}

		$option_name = $this->get_option_name();

		if ( ! $option_name ) {
			return array();
		}

		$paused_extensions = (array) get_option( $option_name, array() );

		return isset( $paused_extensions[ $this->type ] ) ? $paused_extensions[ $this->type ] : array();
	}

	/**
	 * Remove all paused extensions.
	 *
	 *
	 * @return bool
	 */
	public function delete_all() {
		if ( ! $this->is_api_loaded() ) {
			return false;
		}

		$option_name = $this->get_option_name();

		if ( ! $option_name ) {
			return false;
		}

		$paused_extensions = (array) get_option( $option_name, array() );

		unset( $paused_extensions[ $this->type ] );

		if ( ! $paused_extensions ) {
			return delete_option( $option_name );
		}

		return update_option( $option_name, $paused_extensions );
	}

	/**
	 * Checks whether the underlying API to store paused extensions is loaded.
	 *
	 *
	 * @return bool True if the API is loaded, false otherwise.
	 */
	protected function is_api_loaded() {
		return function_exists( 'get_option' );
	}

	/**
	 * Get the option name for storing paused extensions.
	 *
	 *
	 * @return string
	 */
	protected function get_option_name() {
		if ( ! gc_recovery_mode()->is_active() ) {
			return '';
		}

		$session_id = gc_recovery_mode()->get_session_id();
		if ( empty( $session_id ) ) {
			return '';
		}

		return "{$session_id}_paused_extensions";
	}
}
