<?php
/**
 * Upgrader API: GC_Ajax_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Upgrader Skin for Ajax GeChiUI upgrades.
 *
 * This skin is designed to be used for Ajax updates.
 *
 *
 *
 * @see Automatic_Upgrader_Skin
 */
class GC_Ajax_Upgrader_Skin extends Automatic_Upgrader_Skin {

	/**
	 * Holds the GC_Error object.
	 *
	 *
	 * @var null|GC_Error
	 */
	protected $errors = null;

	/**
	 * Constructor.
	 *
	 * Sets up the GeChiUI Ajax upgrader skin.
	 *
	 *
	 * @see GC_Upgrader_Skin::__construct()
	 *
	 * @param array $args Optional. The GeChiUI Ajax upgrader skin arguments to
	 *                    override default options. See GC_Upgrader_Skin::__construct().
	 *                    Default empty array.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( $args );

		$this->errors = new GC_Error();
	}

	/**
	 * Retrieves the list of errors.
	 *
	 *
	 * @return GC_Error Errors during an upgrade.
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Retrieves a string for error messages.
	 *
	 *
	 * @return string Error messages during an upgrade.
	 */
	public function get_error_messages() {
		$messages = array();

		foreach ( $this->errors->get_error_codes() as $error_code ) {
			$error_data = $this->errors->get_error_data( $error_code );

			if ( $error_data && is_string( $error_data ) ) {
				$messages[] = $this->errors->get_error_message( $error_code ) . ' ' . esc_html( strip_tags( $error_data ) );
			} else {
				$messages[] = $this->errors->get_error_message( $error_code );
			}
		}

		return implode( ', ', $messages );
	}

	/**
	 * Stores an error message about the upgrade.
	 *
	 *              to the function signature.
	 *
	 * @param string|GC_Error $errors  Errors.
	 * @param mixed           ...$args Optional text replacements.
	 */
	public function error( $errors, ...$args ) {
		if ( is_string( $errors ) ) {
			$string = $errors;
			if ( ! empty( $this->upgrader->strings[ $string ] ) ) {
				$string = $this->upgrader->strings[ $string ];
			}

			if ( false !== strpos( $string, '%' ) ) {
				if ( ! empty( $args ) ) {
					$string = vsprintf( $string, $args );
				}
			}

			// Count existing errors to generate a unique error code.
			$errors_count = count( $this->errors->get_error_codes() );
			$this->errors->add( 'unknown_upgrade_error_' . ( $errors_count + 1 ), $string );
		} elseif ( is_gc_error( $errors ) ) {
			foreach ( $errors->get_error_codes() as $error_code ) {
				$this->errors->add( $error_code, $errors->get_error_message( $error_code ), $errors->get_error_data( $error_code ) );
			}
		}

		parent::error( $errors, ...$args );
	}

	/**
	 * Stores a message about the upgrade.
	 *
	 *              to the function signature.
	 *
	 * @param string|array|GC_Error $feedback Message data.
	 * @param mixed                 ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
		if ( is_gc_error( $feedback ) ) {
			foreach ( $feedback->get_error_codes() as $error_code ) {
				$this->errors->add( $error_code, $feedback->get_error_message( $error_code ), $feedback->get_error_data( $error_code ) );
			}
		}

		parent::feedback( $feedback, ...$args );
	}
}
