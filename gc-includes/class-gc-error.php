<?php
/**
 * GeChiUI Error API.
 *
 * @package GeChiUI
 */

/**
 * GeChiUI Error class.
 *
 * Container for checking for GeChiUI errors and error messages. Return
 * GC_Error and use is_gc_error() to check if this class is returned. Many
 * core GeChiUI functions pass this class in the event of an error and
 * if not handled properly will result in code errors.
 *
 *
 */
class GC_Error {
	/**
	 * Stores the list of errors.
	 *
	 * @var array
	 */
	public $errors = array();

	/**
	 * Stores the most recently added data for each error code.
	 *
	 * @var array
	 */
	public $error_data = array();

	/**
	 * Stores previously added data added for error codes, oldest-to-newest by code.
	 *
	 * @var array[]
	 */
	protected $additional_data = array();

	/**
	 * Initializes the error.
	 *
	 * If `$code` is empty, the other parameters will be ignored.
	 * When `$code` is not empty, `$message` will be used even if
	 * it is empty. The `$data` parameter will be used only if it
	 * is not empty.
	 *
	 * Though the class is constructed with a single error code and
	 * message, multiple codes can be added using the `add()` method.
	 *
	 *
	 * @param string|int $code    Error code.
	 * @param string     $message Error message.
	 * @param mixed      $data    Optional. Error data.
	 */
	public function __construct( $code = '', $message = '', $data = '' ) {
		if ( empty( $code ) ) {
			return;
		}

		$this->add( $code, $message, $data );
	}

	/**
	 * Retrieves all error codes.
	 *
	 *
	 * @return array List of error codes, if available.
	 */
	public function get_error_codes() {
		if ( ! $this->has_errors() ) {
			return array();
		}

		return array_keys( $this->errors );
	}

	/**
	 * Retrieves the first error code available.
	 *
	 *
	 * @return string|int Empty string, if no error codes.
	 */
	public function get_error_code() {
		$codes = $this->get_error_codes();

		if ( empty( $codes ) ) {
			return '';
		}

		return $codes[0];
	}

	/**
	 * Retrieves all error messages, or the error messages for the given error code.
	 *
	 *
	 * @param string|int $code Optional. Retrieve messages matching code, if exists.
	 * @return array Error strings on success, or empty array if there are none.
	 */
	public function get_error_messages( $code = '' ) {
		// Return all messages if no code specified.
		if ( empty( $code ) ) {
			$all_messages = array();
			foreach ( (array) $this->errors as $code => $messages ) {
				$all_messages = array_merge( $all_messages, $messages );
			}

			return $all_messages;
		}

		if ( isset( $this->errors[ $code ] ) ) {
			return $this->errors[ $code ];
		} else {
			return array();
		}
	}

	/**
	 * Gets a single error message.
	 *
	 * This will get the first message available for the code. If no code is
	 * given then the first code available will be used.
	 *
	 *
	 * @param string|int $code Optional. Error code to retrieve message.
	 * @return string The error message.
	 */
	public function get_error_message( $code = '' ) {
		if ( empty( $code ) ) {
			$code = $this->get_error_code();
		}
		$messages = $this->get_error_messages( $code );
		if ( empty( $messages ) ) {
			return '';
		}
		return $messages[0];
	}

	/**
	 * Retrieves the most recently added error data for an error code.
	 *
	 *
	 * @param string|int $code Optional. Error code.
	 * @return mixed Error data, if it exists.
	 */
	public function get_error_data( $code = '' ) {
		if ( empty( $code ) ) {
			$code = $this->get_error_code();
		}

		if ( isset( $this->error_data[ $code ] ) ) {
			return $this->error_data[ $code ];
		}
	}

	/**
	 * Verifies if the instance contains errors.
	 *
	 *
	 * @return bool If the instance contains errors.
	 */
	public function has_errors() {
		if ( ! empty( $this->errors ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Adds an error or appends an additional message to an existing error.
	 *
	 *
	 * @param string|int $code    Error code.
	 * @param string     $message Error message.
	 * @param mixed      $data    Optional. Error data.
	 */
	public function add( $code, $message, $data = '' ) {
		$this->errors[ $code ][] = $message;

		if ( ! empty( $data ) ) {
			$this->add_data( $data, $code );
		}

		/**
		 * Fires when an error is added to a GC_Error object.
		 *
		 *
		 * @param string|int $code     Error code.
		 * @param string     $message  Error message.
		 * @param mixed      $data     Error data. Might be empty.
		 * @param GC_Error   $gc_error The GC_Error object.
		 */
		do_action( 'gc_error_added', $code, $message, $data, $this );
	}

	/**
	 * Adds data to an error with the given code.
	 *
	 *
	 * @param mixed      $data Error data.
	 * @param string|int $code Error code.
	 */
	public function add_data( $data, $code = '' ) {
		if ( empty( $code ) ) {
			$code = $this->get_error_code();
		}

		if ( isset( $this->error_data[ $code ] ) ) {
			$this->additional_data[ $code ][] = $this->error_data[ $code ];
		}

		$this->error_data[ $code ] = $data;
	}

	/**
	 * Retrieves all error data for an error code in the order in which the data was added.
	 *
	 *
	 * @param string|int $code Error code.
	 * @return mixed[] Array of error data, if it exists.
	 */
	public function get_all_error_data( $code = '' ) {
		if ( empty( $code ) ) {
			$code = $this->get_error_code();
		}

		$data = array();

		if ( isset( $this->additional_data[ $code ] ) ) {
			$data = $this->additional_data[ $code ];
		}

		if ( isset( $this->error_data[ $code ] ) ) {
			$data[] = $this->error_data[ $code ];
		}

		return $data;
	}

	/**
	 * Removes the specified error.
	 *
	 * This function removes all error messages associated with the specified
	 * error code, along with any error data for that code.
	 *
	 *
	 * @param string|int $code Error code.
	 */
	public function remove( $code ) {
		unset( $this->errors[ $code ] );
		unset( $this->error_data[ $code ] );
		unset( $this->additional_data[ $code ] );
	}

	/**
	 * Merges the errors in the given error object into this one.
	 *
	 *
	 * @param GC_Error $error Error object to merge.
	 */
	public function merge_from( GC_Error $error ) {
		static::copy_errors( $error, $this );
	}

	/**
	 * Exports the errors in this object into the given one.
	 *
	 *
	 * @param GC_Error $error Error object to export into.
	 */
	public function export_to( GC_Error $error ) {
		static::copy_errors( $this, $error );
	}

	/**
	 * Copies errors from one GC_Error instance to another.
	 *
	 *
	 * @param GC_Error $from The GC_Error to copy from.
	 * @param GC_Error $to   The GC_Error to copy to.
	 */
	protected static function copy_errors( GC_Error $from, GC_Error $to ) {
		foreach ( $from->get_error_codes() as $code ) {
			foreach ( $from->get_error_messages( $code ) as $error_message ) {
				$to->add( $code, $error_message );
			}

			foreach ( $from->get_all_error_data( $code ) as $data ) {
				$to->add_data( $data, $code );
			}
		}
	}
}
