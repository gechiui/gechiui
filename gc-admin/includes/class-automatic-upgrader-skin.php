<?php
/**
 * Upgrader API: Automatic_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Upgrader Skin for Automatic GeChiUI Upgrades.
 *
 * This skin is designed to be used when no output is intended, all output
 * is captured and stored for the caller to process and log/email/discard.
 *
 *
 *
 *
 * @see Bulk_Upgrader_Skin
 */
class Automatic_Upgrader_Skin extends GC_Upgrader_Skin {
	protected $messages = array();

	/**
	 * Determines whether the upgrader needs FTP/SSH details in order to connect
	 * to the filesystem.
	 *
	 *
	 * @see request_filesystem_credentials()
	 *
	 * @param bool|GC_Error $error                        Optional. Whether the current request has failed to connect,
	 *                                                    or an error object. Default false.
	 * @param string        $context                      Optional. Full path to the directory that is tested
	 *                                                    for being writable. Default empty.
	 * @param bool          $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		if ( $context ) {
			$this->options['context'] = $context;
		}
		/*
		 * TODO: Fix up request_filesystem_credentials(), or split it, to allow us to request a no-output version.
		 * This will output a credentials form in event of failure. We don't want that, so just hide with a buffer.
		 */
		ob_start();
		$result = parent::request_filesystem_credentials( $error, $context, $allow_relaxed_file_ownership );
		ob_end_clean();
		return $result;
	}

	/**
	 * Retrieves the upgrade messages.
	 *
	 *
	 * @return string[] Messages during an upgrade.
	 */
	public function get_upgrade_messages() {
		return $this->messages;
	}

	/**
	 * Stores a message about the upgrade.
	 *
	 *
	 * @param string|array|GC_Error $feedback Message data.
	 * @param mixed                 ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
		if ( is_gc_error( $feedback ) ) {
			$string = $feedback->get_error_message();
		} elseif ( is_array( $feedback ) ) {
			return;
		} else {
			$string = $feedback;
		}

		if ( ! empty( $this->upgrader->strings[ $string ] ) ) {
			$string = $this->upgrader->strings[ $string ];
		}

		if ( strpos( $string, '%' ) !== false ) {
			if ( ! empty( $args ) ) {
				$string = vsprintf( $string, $args );
			}
		}

		$string = trim( $string );

		// Only allow basic HTML in the messages, as it'll be used in emails/logs rather than direct browser output.
		$string = gc_kses(
			$string,
			array(
				'a'      => array(
					'href' => true,
				),
				'br'     => true,
				'em'     => true,
				'strong' => true,
			)
		);

		if ( empty( $string ) ) {
			return;
		}

		$this->messages[] = $string;
	}

	/**
	 * Creates a new output buffer.
	 *
	 */
	public function header() {
		ob_start();
	}

	/**
	 * Retrieves the buffered content, deletes the buffer, and processes the output.
	 *
	 */
	public function footer() {
		$output = ob_get_clean();
		if ( ! empty( $output ) ) {
			$this->feedback( $output );
		}
	}
}
