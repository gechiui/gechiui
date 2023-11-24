<?php
/**
 * Error Protection API: GC_Recovery_Mode_Email_Link class
 *
 * @package GeChiUI
 * @since 5.2.0
 */

/**
 * Core class used to send an email with a link to begin Recovery Mode.
 *
 * @since 5.2.0
 */
#[AllowDynamicProperties]
final class GC_Recovery_Mode_Email_Service {

	const RATE_LIMIT_OPTION = 'recovery_mode_email_last_sent';

	/**
	 * Service to generate recovery mode URLs.
	 *
	 * @since 5.2.0
	 * @var GC_Recovery_Mode_Link_Service
	 */
	private $link_service;

	/**
	 * GC_Recovery_Mode_Email_Service constructor.
	 *
	 * @since 5.2.0
	 *
	 * @param GC_Recovery_Mode_Link_Service $link_service
	 */
	public function __construct( GC_Recovery_Mode_Link_Service $link_service ) {
		$this->link_service = $link_service;
	}

	/**
	 * Sends the recovery mode email if the rate limit has not been sent.
	 *
	 * @since 5.2.0
	 *
	 * @param int   $rate_limit Number of seconds before another email can be sent.
	 * @param array $error      Error details from `error_get_last()`.
	 * @param array $extension {
	 *     The extension that caused the error.
	 *
	 *     @type string $slug The extension slug. The plugin or theme's directory.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 * @return true|GC_Error True if email sent, GC_Error otherwise.
	 */
	public function maybe_send_recovery_mode_email( $rate_limit, $error, $extension ) {

		$last_sent = get_option( self::RATE_LIMIT_OPTION );

		if ( ! $last_sent || time() > $last_sent + $rate_limit ) {
			if ( ! update_option( self::RATE_LIMIT_OPTION, time() ) ) {
				return new GC_Error( 'storage_error', __( '未能更新邮件上次发送时间。' ) );
			}

			$sent = $this->send_recovery_mode_email( $rate_limit, $error, $extension );

			if ( $sent ) {
				return true;
			}

			return new GC_Error(
				'email_failed',
				sprintf(
					/* translators: %s: mail() */
					__( '邮件未能发送。可能的原因：您的主机禁用了%s函数。' ),
					'mail()'
				)
			);
		}

		$err_message = sprintf(
			/* translators: 1: Last sent as a human time diff, 2: Wait time as a human time diff. */
			__( '恢复链接已在%1$s前发送。请等待%2$s后再请求新邮件。' ),
			human_time_diff( $last_sent ),
			human_time_diff( $last_sent + $rate_limit )
		);

		return new GC_Error( 'email_sent_already', $err_message );
	}

	/**
	 * Clears the rate limit, allowing a new recovery mode email to be sent immediately.
	 *
	 * @since 5.2.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function clear_rate_limit() {
		return delete_option( self::RATE_LIMIT_OPTION );
	}

	/**
	 * Sends the Recovery Mode email to the site admin email address.
	 *
	 * @since 5.2.0
	 *
	 * @param int   $rate_limit Number of seconds before another email can be sent.
	 * @param array $error      Error details from `error_get_last()`.
	 * @param array $extension {
	 *     The extension that caused the error.
	 *
	 *     @type string $slug The extension slug. The directory of the plugin or theme.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 * @return bool Whether the email was sent successfully.
	 */
	private function send_recovery_mode_email( $rate_limit, $error, $extension ) {

		$url      = $this->link_service->generate_url();
		$blogname = gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$switched_locale = switch_to_locale( get_locale() );

		if ( $extension ) {
			$cause   = $this->get_cause( $extension );
			$details = gc_strip_all_tags( gc_get_extension_error_description( $error ) );

			if ( $details ) {
				$header  = __( '错误详情' );
				$details = "\n\n" . $header . "\n" . str_pad( '', strlen( $header ), '=' ) . "\n" . $details;
			}
		} else {
			$cause   = '';
			$details = '';
		}

		/**
		 * Filters the support message sent with the the fatal error protection email.
		 *
		 * @since 5.2.0
		 *
		 * @param string $message The Message to include in the email.
		 */
		$support = apply_filters( 'recovery_email_support_info', __( '请联系您的主机提供商来获得帮助及解决此问题。' ) );

		/**
		 * Filters the debug information included in the fatal error protection email.
		 *
		 * @since 5.3.0
		 *
		 * @param array $message An associative array of debug information.
		 */
		$debug = apply_filters( 'recovery_email_debug_info', $this->get_debug( $extension ) );

		/* translators: Do not translate LINK, EXPIRES, CAUSE, DETAILS, SITEURL, PAGEURL, SUPPORT. DEBUG: those are placeholders. */
		$message = __(
			'Howdy!

GeChiUI has a built-in feature that detects when a plugin or theme causes a fatal error on your site, and notifies you with this automated email.
###CAUSE###
First, visit your website (###SITEURL###) and check for any visible issues. Next, visit the page where the error was caught (###PAGEURL###) and check for any visible issues.

###SUPPORT###

If your site appears broken and you can\'t access your dashboard normally, GeChiUI now has a special "recovery mode". This lets you safely login to your dashboard and investigate further.

###LINK###

To keep your site safe, this link will expire in ###EXPIRES###. Don\'t worry about that, though: a new link will be emailed to you if the error occurs again after it expires.

When seeking help with this issue, you may be asked for some of the following information:
###DEBUG###

###DETAILS###'
		);
		$message = str_replace(
			array(
				'###LINK###',
				'###EXPIRES###',
				'###CAUSE###',
				'###DETAILS###',
				'###SITEURL###',
				'###PAGEURL###',
				'###SUPPORT###',
				'###DEBUG###',
			),
			array(
				$url,
				human_time_diff( time() + $rate_limit ),
				$cause ? "\n{$cause}\n" : "\n",
				$details,
				home_url( '/' ),
				home_url( $_SERVER['REQUEST_URI'] ),
				$support,
				implode( "\r\n", $debug ),
			),
			$message
		);

		$email = array(
			'to'          => $this->get_recovery_mode_email_address(),
			/* translators: %s: Site title. */
			'subject'     => __( '[%s] 您的系统正遇到技术问题' ),
			'message'     => $message,
			'headers'     => '',
			'attachments' => '',
		);

		/**
		 * Filters the contents of the Recovery Mode email.
		 *
		 * @since 5.2.0
		 * @since 5.6.0 The `$email` argument includes the `attachments` key.
		 *
		 * @param array  $email {
		 *     Used to build a call to gc_mail().
		 *
		 *     @type string|array $to          Array or comma-separated list of email addresses to send message.
		 *     @type string       $subject     Email subject
		 *     @type string       $message     Message contents
		 *     @type string|array $headers     Optional. Additional headers.
		 *     @type string|array $attachments Optional. Files to attach.
		 * }
		 * @param string $url   URL to enter recovery mode.
		 */
		$email = apply_filters( 'recovery_mode_email', $email, $url );

		$sent = gc_mail(
			$email['to'],
			gc_specialchars_decode( sprintf( $email['subject'], $blogname ) ),
			$email['message'],
			$email['headers'],
			$email['attachments']
		);

		if ( $switched_locale ) {
			restore_previous_locale();
		}

		return $sent;
	}

	/**
	 * Gets the email address to send the recovery mode link to.
	 *
	 * @since 5.2.0
	 *
	 * @return string Email address to send recovery mode link to.
	 */
	private function get_recovery_mode_email_address() {
		if ( defined( 'RECOVERY_MODE_EMAIL' ) && is_email( RECOVERY_MODE_EMAIL ) ) {
			return RECOVERY_MODE_EMAIL;
		}

		return get_option( 'admin_email' );
	}

	/**
	 * Gets the description indicating the possible cause for the error.
	 *
	 * @since 5.2.0
	 *
	 * @param array $extension {
	 *     The extension that caused the error.
	 *
	 *     @type string $slug The extension slug. The directory of the plugin or theme.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 * @return string Message about which extension caused the error.
	 */
	private function get_cause( $extension ) {

		if ( 'plugin' === $extension['type'] ) {
			$plugin = $this->get_plugin( $extension );

			if ( false === $plugin ) {
				$name = $extension['slug'];
			} else {
				$name = $plugin['Name'];
			}

			/* translators: %s: Plugin name. */
			$cause = sprintf( __( '这次，GeChiUI发现了您的插件造成的错误：%s。' ), $name );
		} else {
			$theme = gc_get_theme( $extension['slug'] );
			$name  = $theme->exists() ? $theme->display( 'Name' ) : $extension['slug'];

			/* translators: %s: Theme name. */
			$cause = sprintf( __( '这次，GeChiUI发现了您的主题造成的错误：%s。' ), $name );
		}

		return $cause;
	}

	/**
	 * Return the details for a single plugin based on the extension data from an error.
	 *
	 * @since 5.3.0
	 *
	 * @param array $extension {
	 *     The extension that caused the error.
	 *
	 *     @type string $slug The extension slug. The directory of the plugin or theme.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 * @return array|false A plugin array {@see get_plugins()} or `false` if no plugin was found.
	 */
	private function get_plugin( $extension ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'gc-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		// Assume plugin main file name first since it is a common convention.
		if ( isset( $plugins[ "{$extension['slug']}/{$extension['slug']}.php" ] ) ) {
			return $plugins[ "{$extension['slug']}/{$extension['slug']}.php" ];
		} else {
			foreach ( $plugins as $file => $plugin_data ) {
				if ( str_starts_with( $file, "{$extension['slug']}/" ) || $file === $extension['slug'] ) {
					return $plugin_data;
				}
			}
		}

		return false;
	}

	/**
	 * Return debug information in an easy to manipulate format.
	 *
	 * @since 5.3.0
	 *
	 * @param array $extension {
	 *     The extension that caused the error.
	 *
	 *     @type string $slug The extension slug. The directory of the plugin or theme.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 * @return array An associative array of debug information.
	 */
	private function get_debug( $extension ) {
		$theme      = gc_get_theme();
		$gc_version = get_bloginfo( 'version' );

		if ( $extension ) {
			$plugin = $this->get_plugin( $extension );
		} else {
			$plugin = null;
		}

		$debug = array(
			'gc'    => sprintf(
				/* translators: %s: Current GeChiUI version number. */
				__( 'GeChiUI版本%s' ),
				$gc_version
			),
			'theme' => sprintf(
				/* translators: 1: Current active theme name. 2: Current active theme version. */
				__( '目前启用的主题：%1$s（%2$s 版本）' ),
				$theme->get( 'Name' ),
				$theme->get( 'Version' )
			),
		);

		if ( null !== $plugin ) {
			$debug['plugin'] = sprintf(
				/* translators: 1: The failing plugins name. 2: The failing plugins version. */
				__( '当前插件：%1$s（版本%2$s）' ),
				$plugin['Name'],
				$plugin['Version']
			);
		}

		$debug['php'] = sprintf(
			/* translators: %s: The currently used PHP version. */
			__( 'PHP版本%s' ),
			PHP_VERSION
		);

		return $debug;
	}
}
