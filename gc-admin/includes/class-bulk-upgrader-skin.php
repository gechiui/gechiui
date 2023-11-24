<?php
/**
 * Upgrader API: Bulk_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 */

/**
 * Generic Bulk Upgrader Skin for GeChiUI Upgrades.
 * Moved to its own file from gc-admin/includes/class-gc-upgrader-skins.php.
 *
 * @see GC_Upgrader_Skin
 */
class Bulk_Upgrader_Skin extends GC_Upgrader_Skin {
	public $in_loop = false;
	/**
	 * @var string|false
	 */
	public $error = false;

	/**
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'url'   => '',
			'nonce' => '',
		);
		$args     = gc_parse_args( $args, $defaults );

		parent::__construct( $args );
	}

	/**
	 */
	public function add_strings() {
		$this->upgrader->strings['skin_upgrade_start'] = __( '正开始升级。这个过程在某些服务器上花的时间要长些，请耐心等待。' );
		/* translators: 1: Title of an update, 2: Error message. */
		$this->upgrader->strings['skin_update_failed_error'] = __( '在更新%1$s时发生了错误：%2$s' );
		/* translators: %s: Title of an update. */
		$this->upgrader->strings['skin_update_failed'] = __( '%s更新失败。' );
		/* translators: %s: Title of an update. */
		$this->upgrader->strings['skin_update_successful'] = __( '%s已成功更新。' );
		$this->upgrader->strings['skin_upgrade_end']       = __( '更新任务全部完成。' );
	}

	/**
	 * @since 5.9.0 Renamed `$string` (a PHP reserved keyword) to `$feedback` for PHP 8 named parameter support.
	 *
	 * @param string $feedback Message data.
	 * @param mixed  ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
		if ( isset( $this->upgrader->strings[ $feedback ] ) ) {
			$feedback = $this->upgrader->strings[ $feedback ];
		}

		if ( str_contains( $feedback, '%' ) ) {
			if ( $args ) {
				$args     = array_map( 'strip_tags', $args );
				$args     = array_map( 'esc_html', $args );
				$feedback = vsprintf( $feedback, $args );
			}
		}
		if ( empty( $feedback ) ) {
			return;
		}
		if ( $this->in_loop ) {
			echo "$feedback<br />\n";
		} else {
			echo "<p>$feedback</p>\n";
		}
	}

	/**
	 */
	public function header() {
		// Nothing. This will be displayed within an iframe.
	}

	/**
	 */
	public function footer() {
		// Nothing. This will be displayed within an iframe.
	}

	/**
	 * @since 5.9.0 Renamed `$error` to `$errors` for PHP 8 named parameter support.
	 *
	 * @param string|GC_Error $errors Errors.
	 */
	public function error( $errors ) {
		if ( is_string( $errors ) && isset( $this->upgrader->strings[ $errors ] ) ) {
			$this->error = $this->upgrader->strings[ $errors ];
		}

		if ( is_gc_error( $errors ) ) {
			$messages = array();
			foreach ( $errors->get_error_messages() as $emessage ) {
				if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) ) {
					$messages[] = $emessage . ' ' . esc_html( strip_tags( $errors->get_error_data() ) );
				} else {
					$messages[] = $emessage;
				}
			}
			$this->error = implode( ', ', $messages );
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
	}

	/**
	 */
	public function bulk_header() {
		$this->feedback( 'skin_upgrade_start' );
	}

	/**
	 */
	public function bulk_footer() {
		$this->feedback( 'skin_upgrade_end' );
	}

	/**
	 * @param string $title
	 */
	public function before( $title = '' ) {
		$this->in_loop = true;
		printf( '<h2>' . $this->upgrader->strings['skin_before_update_header'] . ' <span class="spinner waiting-' . $this->upgrader->update_current . '"></span></h2>', $title, $this->upgrader->update_current, $this->upgrader->update_count );
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').css("display", "inline-block");</script>';
		// This progress messages div gets moved via JavaScript when clicking on "更多详情。".
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr( $this->upgrader->update_current ) . '"><p>';
		$this->flush_output();
	}

	/**
	 * @param string $title
	 */
	public function after( $title = '' ) {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error ) {
				echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed_error'], $title, '<strong>' . $this->error . '</strong>' ) . '</p></div>';
			} else {
				echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed'], $title ) . '</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
		}
		if ( $this->result && ! is_gc_error( $this->result ) ) {
			if ( ! $this->error ) {
				echo '<div class="updated js-update-details" data-update-details="progress-' . esc_attr( $this->upgrader->update_current ) . '">' .
					'<p>' . sprintf( $this->upgrader->strings['skin_update_successful'], $title ) .
					' <button type="button" class="hide-if-no-js button-link js-update-details-toggle" aria-expanded="false">' . __( '更多详情。' ) . '<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span></button>' .
					'</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	/**
	 */
	public function reset() {
		$this->in_loop = false;
		$this->error   = false;
	}

	/**
	 */
	public function flush_output() {
		gc_ob_end_flush_all();
		flush();
	}
}
