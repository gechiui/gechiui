<?php
/**
 * Error Protection API: GC_Recovery_Mode class
 *
 * @package GeChiUI
 *
 */

/**
 * Core class used to implement Recovery Mode.
 *
 *
 */
class GC_Recovery_Mode {

	const EXIT_ACTION = 'exit_recovery_mode';

	/**
	 * Service to handle cookies.
	 *
	 * @var GC_Recovery_Mode_Cookie_Service
	 */
	private $cookie_service;

	/**
	 * Service to generate a recovery mode key.
	 *
	 * @var GC_Recovery_Mode_Key_Service
	 */
	private $key_service;

	/**
	 * Service to generate and validate recovery mode links.
	 *
	 * @var GC_Recovery_Mode_Link_Service
	 */
	private $link_service;

	/**
	 * Service to handle sending an email with a recovery mode link.
	 *
	 * @var GC_Recovery_Mode_Email_Service
	 */
	private $email_service;

	/**
	 * Is recovery mode initialized.
	 *
	 * @var bool
	 */
	private $is_initialized = false;

	/**
	 * Is recovery mode active in this session.
	 *
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * Get an ID representing the current recovery mode session.
	 *
	 * @var string
	 */
	private $session_id = '';

	/**
	 * GC_Recovery_Mode constructor.
	 *
	 */
	public function __construct() {
		$this->cookie_service = new GC_Recovery_Mode_Cookie_Service();
		$this->key_service    = new GC_Recovery_Mode_Key_Service();
		$this->link_service   = new GC_Recovery_Mode_Link_Service( $this->cookie_service, $this->key_service );
		$this->email_service  = new GC_Recovery_Mode_Email_Service( $this->link_service );
	}

	/**
	 * Initialize recovery mode for the current request.
	 *
	 */
	public function initialize() {
		$this->is_initialized = true;

		add_action( 'gc_logout', array( $this, 'exit_recovery_mode' ) );
		add_action( 'login_form_' . self::EXIT_ACTION, array( $this, 'handle_exit_recovery_mode' ) );
		add_action( 'recovery_mode_clean_expired_keys', array( $this, 'clean_expired_keys' ) );

		if ( ! gc_next_scheduled( 'recovery_mode_clean_expired_keys' ) && ! gc_installing() ) {
			gc_schedule_event( time(), 'daily', 'recovery_mode_clean_expired_keys' );
		}

		if ( defined( 'GC_RECOVERY_MODE_SESSION_ID' ) ) {
			$this->is_active  = true;
			$this->session_id = GC_RECOVERY_MODE_SESSION_ID;

			return;
		}

		if ( $this->cookie_service->is_cookie_set() ) {
			$this->handle_cookie();

			return;
		}

		$this->link_service->handle_begin_link( $this->get_link_ttl() );
	}

	/**
	 * Checks whether recovery mode is active.
	 *
	 * This will not change after recovery mode has been initialized. {@see GC_Recovery_Mode::run()}.
	 *
	 *
	 * @return bool True if recovery mode is active, false otherwise.
	 */
	public function is_active() {
		return $this->is_active;
	}

	/**
	 * Gets the recovery mode session ID.
	 *
	 *
	 * @return string The session ID if recovery mode is active, empty string otherwise.
	 */
	public function get_session_id() {
		return $this->session_id;
	}

	/**
	 * Checks whether recovery mode has been initialized.
	 *
	 * Recovery mode should not be used until this point. Initialization happens immediately before loading plugins.
	 *
	 *
	 * @return bool
	 */
	public function is_initialized() {
		return $this->is_initialized;
	}

	/**
	 * Handles a fatal error occurring.
	 *
	 * The calling API should immediately die() after calling this function.
	 *
	 *
	 * @param array $error Error details from {@see error_get_last()}
	 * @return true|GC_Error True if the error was handled and headers have already been sent.
	 *                       Or the request will exit to try and catch multiple errors at once.
	 *                       GC_Error if an error occurred preventing it from being handled.
	 */
	public function handle_error( array $error ) {

		$extension = $this->get_extension_for_error( $error );

		if ( ! $extension || $this->is_network_plugin( $extension ) ) {
			return new GC_Error( 'invalid_source', __( '错误不是由插件或主题引起的。' ) );
		}

		if ( ! $this->is_active() ) {
			if ( ! is_protected_endpoint() ) {
				return new GC_Error( 'non_protected_endpoint', __( '错误在未受保护的端点发生。' ) );
			}

			if ( ! function_exists( 'gc_generate_password' ) ) {
				require_once ABSPATH . GCINC . '/pluggable.php';
			}

			return $this->email_service->maybe_send_recovery_mode_email( $this->get_email_rate_limit(), $error, $extension );
		}

		if ( ! $this->store_error( $error ) ) {
			return new GC_Error( 'storage_error', __( '未能存储此错误。' ) );
		}

		if ( headers_sent() ) {
			return true;
		}

		$this->redirect_protected();
	}

	/**
	 * Ends the current recovery mode session.
	 *
	 *
	 * @return bool True on success, false on failure.
	 */
	public function exit_recovery_mode() {
		if ( ! $this->is_active() ) {
			return false;
		}

		$this->email_service->clear_rate_limit();
		$this->cookie_service->clear_cookie();

		gc_paused_plugins()->delete_all();
		gc_paused_themes()->delete_all();

		return true;
	}

	/**
	 * Handles a request to exit Recovery Mode.
	 *
	 */
	public function handle_exit_recovery_mode() {
		$redirect_to = gc_get_referer();

		// Safety check in case referrer returns false.
		if ( ! $redirect_to ) {
			$redirect_to = is_user_logged_in() ? admin_url() : home_url();
		}

		if ( ! $this->is_active() ) {
			gc_safe_redirect( $redirect_to );
			die;
		}

		if ( ! isset( $_GET['action'] ) || self::EXIT_ACTION !== $_GET['action'] ) {
			return;
		}

		if ( ! isset( $_GET['_gcnonce'] ) || ! gc_verify_nonce( $_GET['_gcnonce'], self::EXIT_ACTION ) ) {
			gc_die( __( '退出恢复模式链接已过期。' ), 403 );
		}

		if ( ! $this->exit_recovery_mode() ) {
			gc_die( __( '未能退出恢复模式。请稍后再试。' ) );
		}

		gc_safe_redirect( $redirect_to );
		die;
	}

	/**
	 * Cleans any recovery mode keys that have expired according to the link TTL.
	 *
	 * Executes on a daily cron schedule.
	 *
	 */
	public function clean_expired_keys() {
		$this->key_service->clean_expired_keys( $this->get_link_ttl() );
	}

	/**
	 * Handles checking for the recovery mode cookie and validating it.
	 *
	 */
	protected function handle_cookie() {
		$validated = $this->cookie_service->validate_cookie();

		if ( is_gc_error( $validated ) ) {
			$this->cookie_service->clear_cookie();

			$validated->add_data( array( 'status' => 403 ) );
			gc_die( $validated );
		}

		$session_id = $this->cookie_service->get_session_id_from_cookie();
		if ( is_gc_error( $session_id ) ) {
			$this->cookie_service->clear_cookie();

			$session_id->add_data( array( 'status' => 403 ) );
			gc_die( $session_id );
		}

		$this->is_active  = true;
		$this->session_id = $session_id;
	}

	/**
	 * Gets the rate limit between sending new recovery mode email links.
	 *
	 *
	 * @return int Rate limit in seconds.
	 */
	protected function get_email_rate_limit() {
		/**
		 * Filters the rate limit between sending new recovery mode email links.
		 *
		 *
		 * @param int $rate_limit Time to wait in seconds. Defaults to 1 day.
		 */
		return apply_filters( 'recovery_mode_email_rate_limit', DAY_IN_SECONDS );
	}

	/**
	 * Gets the number of seconds the recovery mode link is valid for.
	 *
	 *
	 * @return int Interval in seconds.
	 */
	protected function get_link_ttl() {

		$rate_limit = $this->get_email_rate_limit();
		$valid_for  = $rate_limit;

		/**
		 * Filters the amount of time the recovery mode email link is valid for.
		 *
		 * The ttl must be at least as long as the email rate limit.
		 *
		 *
		 * @param int $valid_for The number of seconds the link is valid for.
		 */
		$valid_for = apply_filters( 'recovery_mode_email_link_ttl', $valid_for );

		return max( $valid_for, $rate_limit );
	}

	/**
	 * Gets the extension that the error occurred in.
	 *
	 *
	 * @global array $gc_theme_directories
	 *
	 * @param array $error Error that was triggered.
	 * @return array|false {
	 *     Extension details.
	 *
	 *     @type string $slug The extension slug. This is the plugin or theme's directory.
	 *     @type string $type The extension type. Either 'plugin' or 'theme'.
	 * }
	 */
	protected function get_extension_for_error( $error ) {
		global $gc_theme_directories;

		if ( ! isset( $error['file'] ) ) {
			return false;
		}

		if ( ! defined( 'GC_PLUGIN_DIR' ) ) {
			return false;
		}

		$error_file    = gc_normalize_path( $error['file'] );
		$gc_plugin_dir = gc_normalize_path( GC_PLUGIN_DIR );

		if ( 0 === strpos( $error_file, $gc_plugin_dir ) ) {
			$path  = str_replace( $gc_plugin_dir . '/', '', $error_file );
			$parts = explode( '/', $path );

			return array(
				'type' => 'plugin',
				'slug' => $parts[0],
			);
		}

		if ( empty( $gc_theme_directories ) ) {
			return false;
		}

		foreach ( $gc_theme_directories as $theme_directory ) {
			$theme_directory = gc_normalize_path( $theme_directory );

			if ( 0 === strpos( $error_file, $theme_directory ) ) {
				$path  = str_replace( $theme_directory . '/', '', $error_file );
				$parts = explode( '/', $path );

				return array(
					'type' => 'theme',
					'slug' => $parts[0],
				);
			}
		}

		return false;
	}

	/**
	 * Checks whether the given extension a network activated plugin.
	 *
	 *
	 * @param array $extension Extension data.
	 * @return bool True if network plugin, false otherwise.
	 */
	protected function is_network_plugin( $extension ) {
		if ( 'plugin' !== $extension['type'] ) {
			return false;
		}

		if ( ! is_multisite() ) {
			return false;
		}

		$network_plugins = gc_get_active_network_plugins();

		foreach ( $network_plugins as $plugin ) {
			if ( 0 === strpos( $plugin, $extension['slug'] . '/' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stores the given error so that the extension causing it is paused.
	 *
	 *
	 * @param array $error Error that was triggered.
	 * @return bool True if the error was stored successfully, false otherwise.
	 */
	protected function store_error( $error ) {
		$extension = $this->get_extension_for_error( $error );

		if ( ! $extension ) {
			return false;
		}

		switch ( $extension['type'] ) {
			case 'plugin':
				return gc_paused_plugins()->set( $extension['slug'], $error );
			case 'theme':
				return gc_paused_themes()->set( $extension['slug'], $error );
			default:
				return false;
		}
	}

	/**
	 * Redirects the current request to allow recovering multiple errors in one go.
	 *
	 * The redirection will only happen when on a protected endpoint.
	 *
	 * It must be ensured that this method is only called when an error actually occurred and will not occur on the
	 * next request again. Otherwise it will create a redirect loop.
	 *
	 */
	protected function redirect_protected() {
		// Pluggable is usually loaded after plugins, so we manually include it here for redirection functionality.
		if ( ! function_exists( 'gc_safe_redirect' ) ) {
			require_once ABSPATH . GCINC . '/pluggable.php';
		}

		$scheme = is_ssl() ? 'https://' : 'http://';

		$url = "{$scheme}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		gc_safe_redirect( $url );
		exit;
	}
}
