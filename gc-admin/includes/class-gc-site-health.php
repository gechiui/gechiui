<?php
/**
 * Class for looking up a site's health based on a user's GeChiUI environment.
 *
 * @package GeChiUI
 * @subpackage Site_Health
 * @since 5.2.0
 */

#[AllowDynamicProperties]
class GC_Site_Health {
	private static $instance = null;

	private $is_acceptable_mysql_version;
	private $is_recommended_mysql_version;

	public $is_mariadb                   = false;
	private $mysql_server_version        = '';
	private $mysql_required_version      = '5.5';
	private $mysql_recommended_version   = '5.7';
	private $mariadb_recommended_version = '10.4';

	public $php_memory_limit;

	public $schedules;
	public $crons;
	public $last_missed_cron     = null;
	public $last_late_cron       = null;
	private $timeout_missed_cron = null;
	private $timeout_late_cron   = null;

	/**
	 * GC_Site_Health constructor.
	 *
	 * @since 5.2.0
	 */
	public function __construct() {
		$this->maybe_create_scheduled_event();

		// Save memory limit before it's affected by gc_raise_memory_limit( 'admin' ).
		$this->php_memory_limit = ini_get( 'memory_limit' );

		$this->timeout_late_cron   = 0;
		$this->timeout_missed_cron = - 5 * MINUTE_IN_SECONDS;

		if ( defined( 'DISABLE_GC_CRON' ) && DISABLE_GC_CRON ) {
			$this->timeout_late_cron   = - 15 * MINUTE_IN_SECONDS;
			$this->timeout_missed_cron = - 1 * HOUR_IN_SECONDS;
		}

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'gc_site_health_scheduled_check', array( $this, 'gc_cron_scheduled_check' ) );

		add_action( 'site_health_tab_content', array( $this, 'show_site_health_tab' ) );
	}

	/**
	 * Outputs the content of a tab in the Site Health screen.
	 *
	 * @since 5.8.0
	 *
	 * @param string $tab Slug of the current tab being displayed.
	 */
	public function show_site_health_tab( $tab ) {
		if ( 'debug' === $tab ) {
			require_once ABSPATH . 'gc-admin/site-health-info.php';
		}
	}

	/**
	 * Returns an instance of the GC_Site_Health class, or create one if none exist yet.
	 *
	 * @since 5.4.0
	 *
	 * @return GC_Site_Health|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new GC_Site_Health();
		}

		return self::$instance;
	}

	/**
	 * Enqueues the site health scripts.
	 *
	 * @since 5.2.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'site-health' !== $screen->id && 'dashboard' !== $screen->id ) {
			return;
		}

		$health_check_js_variables = array(
			'screen'      => $screen->id,
			'nonce'       => array(
				'site_status'        => gc_create_nonce( 'health-check-site-status' ),
				'site_status_result' => gc_create_nonce( 'health-check-site-status-result' ),
			),
			'site_status' => array(
				'direct' => array(),
				'async'  => array(),
				'issues' => array(
					'good'        => 0,
					'recommended' => 0,
					'critical'    => 0,
				),
			),
		);

		$issue_counts = get_transient( 'health-check-site-status-result' );

		if ( false !== $issue_counts ) {
			$issue_counts = json_decode( $issue_counts );

			$health_check_js_variables['site_status']['issues'] = $issue_counts;
		}

		if ( 'site-health' === $screen->id && ( ! isset( $_GET['tab'] ) || empty( $_GET['tab'] ) ) ) {
			$tests = GC_Site_Health::get_tests();

			// Don't run https test on development environments.
			if ( $this->is_development_environment() ) {
				unset( $tests['async']['https_status'] );
			}

			foreach ( $tests['direct'] as $test ) {
				if ( is_string( $test['test'] ) ) {
					$test_function = sprintf(
						'get_test_%s',
						$test['test']
					);

					if ( method_exists( $this, $test_function ) && is_callable( array( $this, $test_function ) ) ) {
						$health_check_js_variables['site_status']['direct'][] = $this->perform_test( array( $this, $test_function ) );
						continue;
					}
				}

				if ( is_callable( $test['test'] ) ) {
					$health_check_js_variables['site_status']['direct'][] = $this->perform_test( $test['test'] );
				}
			}

			foreach ( $tests['async'] as $test ) {
				if ( is_string( $test['test'] ) ) {
					$health_check_js_variables['site_status']['async'][] = array(
						'test'      => $test['test'],
						'has_rest'  => ( isset( $test['has_rest'] ) ? $test['has_rest'] : false ),
						'completed' => false,
						'headers'   => isset( $test['headers'] ) ? $test['headers'] : array(),
					);
				}
			}
		}

		gc_localize_script( 'site-health', 'SiteHealth', $health_check_js_variables );
	}

	/**
	 * Runs a Site Health test directly.
	 *
	 * @since 5.4.0
	 *
	 * @param callable $callback
	 * @return mixed|void
	 */
	private function perform_test( $callback ) {
		/**
		 * Filters the output of a finished Site Health test.
		 *
		 * @since 5.3.0
		 *
		 * @param array $test_result {
		 *     An associative array of test result data.
		 *
		 *     @type string $label       A label describing the test, and is used as a header in the output.
		 *     @type string $status      The status of the test, which can be a value of `good`, `recommended` or `critical`.
		 *     @type array  $badge {
		 *         Tests are put into categories which have an associated badge shown, these can be modified and assigned here.
		 *
		 *         @type string $label The test label, for example `Performance`.
		 *         @type string $color Default `blue`. A string representing a color to use for the label.
		 *     }
		 *     @type string $description A more descriptive explanation of what the test looks for, and why it is important for the end user.
		 *     @type string $actions     An action to direct the user to where they can resolve the issue, if one exists.
		 *     @type string $test        The name of the test being ran, used as a reference point.
		 * }
		 */
		return apply_filters( 'site_status_test_result', call_user_func( $callback ) );
	}

	/**
	 * Runs the SQL version checks.
	 *
	 * These values are used in later tests, but the part of preparing them is more easily managed
	 * early in the class for ease of access and discovery.
	 *
	 * @since 5.2.0
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 */
	private function prepare_sql_data() {
		global $gcdb;

		$mysql_server_type = $gcdb->db_server_info();

		$this->mysql_server_version = $gcdb->get_var( 'SELECT VERSION()' );

		if ( stristr( $mysql_server_type, 'mariadb' ) ) {
			$this->is_mariadb                = true;
			$this->mysql_recommended_version = $this->mariadb_recommended_version;
		}

		$this->is_acceptable_mysql_version  = version_compare( $this->mysql_required_version, $this->mysql_server_version, '<=' );
		$this->is_recommended_mysql_version = version_compare( $this->mysql_recommended_version, $this->mysql_server_version, '<=' );
	}

	/**
	 * Tests whether `gc_version_check` is blocked.
	 *
	 * It's possible to block updates with the `gc_version_check` filter, but this can't be checked
	 * during an Ajax call, as the filter is never introduced then.
	 *
	 * This filter overrides a standard page request if it's made by an admin through the Ajax call
	 * with the right query argument to check for this.
	 *
	 * @since 5.2.0
	 */
	public function check_gc_version_check_exists() {
		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'update_core' ) || ! isset( $_GET['health-check-test-gc_version_check'] ) ) {
			return;
		}

		echo ( has_filter( 'gc_version_check', 'gc_version_check' ) ? 'yes' : 'no' );

		die();
	}

	/**
	 * Tests for GeChiUI version and outputs it.
	 *
	 * Gives various results depending on what kind of updates are available, if any, to encourage
	 * the user to install security updates as a priority.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test result.
	 */
	public function get_test_gechiui_version() {
		$result = array(
			'label'       => '',
			'status'      => '',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => '',
			'actions'     => '',
			'test'        => 'gechiui_version',
		);

		$core_current_version = get_bloginfo( 'version' );
		$core_updates         = get_core_updates();

		if ( ! is_array( $core_updates ) ) {
			$result['status'] = 'recommended';

			$result['label'] = sprintf(
				/* translators: %s: Your current version of GeChiUI. */
				__( 'GeChiUI版本%s' ),
				$core_current_version
			);

			$result['description'] = sprintf(
				'<p>%s</p>',
				__( '无法检查是否有任何新版本的 GeChiUI 可用。' )
			);

			$result['actions'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'update-core.php?force-check=1' ) ),
				__( '手动检查更新' )
			);
		} else {
			foreach ( $core_updates as $core => $update ) {
				if ( 'upgrade' === $update->response ) {
					$current_version = explode( '.', $core_current_version );
					$new_version     = explode( '.', $update->version );

					$current_major = $current_version[0] . '.' . $current_version[1];
					$new_major     = $new_version[0] . '.' . $new_version[1];

					$result['label'] = sprintf(
						/* translators: %s: The latest version of GeChiUI available. */
						__( 'GeChiUI更新可用（%s）' ),
						$update->version
					);

					$result['actions'] = sprintf(
						'<a href="%s">%s</a>',
						esc_url( admin_url( 'update-core.php' ) ),
						__( '安装最新版本的GeChiUI' )
					);

					if ( $current_major !== $new_major ) {
						// This is a major version mismatch.
						$result['status']      = 'recommended';
						$result['description'] = sprintf(
							'<p>%s</p>',
							__( '新版本的GeChiUI现已可用。' )
						);
					} else {
						// This is a minor version, sometimes considered more critical.
						$result['status']         = 'critical';
						$result['badge']['label'] = __( '安全' );
						$result['description']    = sprintf(
							'<p>%s</p>',
							__( '一个新的小版本更新现已可用。因为小版本更新通常会解决安全问题，我们推荐您安装此更新。' )
						);
					}
				} else {
					$result['status'] = 'good';
					$result['label']  = sprintf(
						/* translators: %s: The current version of GeChiUI installed on this site. */
						__( '您的GeChiUI版本（%s）已是最新' ),
						$core_current_version
					);

					$result['description'] = sprintf(
						'<p>%s</p>',
						__( '您正在运行最新版本的GeChiUI，好样的！' )
					);
				}
			}
		}

		return $result;
	}

	/**
	 * Tests if plugins are outdated, or unnecessary.
	 *
	 * The test checks if your plugins are up to date, and encourages you to remove any
	 * that are not in use.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test result.
	 */
	public function get_test_plugin_version() {
		$result = array(
			'label'       => __( '您的所有插件均为最新版本' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '插件为您的系统添加新功能，如联系表单、电子商务及更多。这意味着他们对您的系统有很深的访问权，所以保证您有最新版本至关重要。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( admin_url( 'plugins.php' ) ),
				__( '管理您的插件' )
			),
			'test'        => 'plugin_version',
		);

		$plugins        = get_plugins();
		$plugin_updates = get_plugin_updates();

		$plugins_active      = 0;
		$plugins_total       = 0;
		$plugins_need_update = 0;

		// Loop over the available plugins and check their versions and active state.
		foreach ( $plugins as $plugin_path => $plugin ) {
			$plugins_total++;

			if ( is_plugin_active( $plugin_path ) ) {
				$plugins_active++;
			}

			if ( array_key_exists( $plugin_path, $plugin_updates ) ) {
				$plugins_need_update++;
			}
		}

		// Add a notice if there are outdated plugins.
		if ( $plugins_need_update > 0 ) {
			$result['status'] = 'critical';

			$result['label'] = __( '您有插件等待更新' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %d: The number of outdated plugins. */
					_n(
						'您的系统有%d个插件正等待更新。',
						'您的系统有%d个插件正等待更新。',
						$plugins_need_update
					),
					$plugins_need_update
				)
			);

			$result['actions'] .= sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( network_admin_url( 'plugins.php?plugin_status=upgrade' ) ),
				__( '更新您的插件' )
			);
		} else {
			if ( 1 === $plugins_active ) {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的系统有一个已启用的插件，其已是最新。' )
				);
			} elseif ( $plugins_active > 0 ) {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %d: The number of active plugins. */
						_n(
							'您的系统有%d个已启用的插件，这些插件已是最新。',
							'您的系统有%d个已启用的插件，这些插件已是最新。',
							$plugins_active
						),
						$plugins_active
					)
				);
			} else {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的系统没有任何已启用的插件。' )
				);
			}
		}

		// Check if there are inactive plugins.
		if ( $plugins_total > $plugins_active && ! is_multisite() ) {
			$unused_plugins = $plugins_total - $plugins_active;

			$result['status'] = 'recommended';

			$result['label'] = __( '您应该移除未启用的插件' );

			$result['description'] .= sprintf(
				'<p>%s %s</p>',
				sprintf(
					/* translators: %d: The number of inactive plugins. */
					_n(
						'您的系统有%d个未启用的插件。',
						'您的系统有%d个未启用的插件。',
						$unused_plugins
					),
					$unused_plugins
				),
				__( '未启用的插件是攻击者的垂涎目标。 若您不打算使用，则应考虑将其移除。' )
			);

			$result['actions'] .= sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( admin_url( 'plugins.php?plugin_status=inactive' ) ),
				__( '管理未启用的插件' )
			);
		}

		return $result;
	}

	/**
	 * Tests if themes are outdated, or unnecessary.
	 *
	 * Checks if your site has a default theme (to fall back on if there is a need),
	 * if your themes are up to date and, finally, encourages you to remove any themes
	 * that are not needed.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_theme_version() {
		$result = array(
			'label'       => __( '您的所有主题均为最新版本' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '主题为您的系统增添光彩。保持他们最新十分重要，这能与您的品牌保持一致，并确保您的系统安全。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( admin_url( 'themes.php' ) ),
				__( '管理您的主题' )
			),
			'test'        => 'theme_version',
		);

		$theme_updates = get_theme_updates();

		$themes_total        = 0;
		$themes_need_updates = 0;
		$themes_inactive     = 0;

		// This value is changed during processing to determine how many themes are considered a reasonable amount.
		$allowed_theme_count = 1;

		$has_default_theme   = false;
		$has_unused_themes   = false;
		$show_unused_themes  = true;
		$using_default_theme = false;

		// Populate a list of all themes available in the install.
		$all_themes   = gc_get_themes();
		$active_theme = gc_get_theme();

		// If GC_DEFAULT_THEME doesn't exist, fall back to the latest core default theme.
		$default_theme = gc_get_theme( GC_DEFAULT_THEME );
		if ( ! $default_theme->exists() ) {
			$default_theme = GC_Theme::get_core_default_theme();
		}

		if ( $default_theme ) {
			$has_default_theme = true;

			if (
				$active_theme->get_stylesheet() === $default_theme->get_stylesheet()
			||
				is_child_theme() && $active_theme->get_template() === $default_theme->get_template()
			) {
				$using_default_theme = true;
			}
		}

		foreach ( $all_themes as $theme_slug => $theme ) {
			$themes_total++;

			if ( array_key_exists( $theme_slug, $theme_updates ) ) {
				$themes_need_updates++;
			}
		}

		// If this is a child theme, increase the allowed theme count by one, to account for the parent.
		if ( is_child_theme() ) {
			$allowed_theme_count++;
		}

		// If there's a default theme installed and not in use, we count that as allowed as well.
		if ( $has_default_theme && ! $using_default_theme ) {
			$allowed_theme_count++;
		}

		if ( $themes_total > $allowed_theme_count ) {
			$has_unused_themes = true;
			$themes_inactive   = ( $themes_total - $allowed_theme_count );
		}

		// Check if any themes need to be updated.
		if ( $themes_need_updates > 0 ) {
			$result['status'] = 'critical';

			$result['label'] = __( '您有主题等待更新' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %d: The number of outdated themes. */
					_n(
						'您的系统有%d个主题正等待更新。',
						'您的系统有%d个主题正等待更新。',
						$themes_need_updates
					),
					$themes_need_updates
				)
			);
		} else {
			// Give positive feedback about the site being good about keeping things up to date.
			if ( 1 === $themes_total ) {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的系统有一个已启用的主题，其已是最新。' )
				);
			} elseif ( $themes_total > 0 ) {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %d: The number of themes. */
						_n(
							'您的系统有%d个已启用的主题，这些主题已是最新。',
							'您的系统有%d个已启用的主题，这些主题已是最新。',
							$themes_total
						),
						$themes_total
					)
				);
			} else {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的系统没有安装任何主题。' )
				);
			}
		}

		if ( $has_unused_themes && $show_unused_themes && ! is_multisite() ) {

			// This is a child theme, so we want to be a bit more explicit in our messages.
			if ( $active_theme->parent() ) {
				// Recommend removing inactive themes, except a default theme, your current one, and the parent theme.
				$result['status'] = 'recommended';

				$result['label'] = __( '您应该移除未启用的主题' );

				if ( $using_default_theme ) {
					$result['description'] .= sprintf(
						'<p>%s %s</p>',
						sprintf(
							/* translators: %d: The number of inactive themes. */
							_n(
								'您的系统有%d个未启用的主题。',
								'您的系统有%d个未启用的主题。',
								$themes_inactive
							),
							$themes_inactive
						),
						sprintf(
							/* translators: 1: The currently active theme. 2: The active theme's parent theme. */
							__( '建议移除您不使用的任何主题以提高系统安全性。 请保留当前的主题 %1$s 及其父主题 %2$s。' ),
							$active_theme->name,
							$active_theme->parent()->name
						)
					);
				} else {
					$result['description'] .= sprintf(
						'<p>%s %s</p>',
						sprintf(
							/* translators: %d: The number of inactive themes. */
							_n(
								'您的系统有%d个未启用的主题。',
								'您的系统有%d个未启用的主题。',
								$themes_inactive
							),
							$themes_inactive
						),
						sprintf(
							/* translators: 1: The default theme for GeChiUI. 2: The currently active theme. 3: The active theme's parent theme. */
							__( '建议移除您不使用的任何主题以提高系统安全性。 请保留 GeChiUI 的默认主题 %1$s 、当前的主题 %2$s 及其父主题 %3$s。' ),
							$default_theme ? $default_theme->name : GC_DEFAULT_THEME,
							$active_theme->name,
							$active_theme->parent()->name
						)
					);
				}
			} else {
				// Recommend removing all inactive themes.
				$result['status'] = 'recommended';

				$result['label'] = __( '您应该移除未启用的主题' );

				if ( $using_default_theme ) {
					$result['description'] .= sprintf(
						'<p>%s %s</p>',
						sprintf(
							/* translators: 1: The amount of inactive themes. 2: The currently active theme. */
							_n(
								'除了您当前的主题%2$s外，您的系统有%1$d个未启用的主题。',
								'除了您当前的主题%2$s外，您的系统有%1$d个未启用的主题。',
								$themes_inactive
							),
							$themes_inactive,
							$active_theme->name
						),
						__( '建议移除任何未使用的主题以增强系统的安全性。' )
					);
				} else {
					$result['description'] .= sprintf(
						'<p>%s %s</p>',
						sprintf(
							/* translators: 1: The amount of inactive themes. 2: The default theme for GeChiUI. 3: The currently active theme. */
							_n(
								'除了GeChiUI默认主题%2$s和您当前的主题%3$s外，您的系统有%1$d个未启用的主题。',
								'除了GeChiUI默认主题%2$s和您当前的主题%3$s外，您的系统有%1$d个未启用的主题。',
								$themes_inactive
							),
							$themes_inactive,
							$default_theme ? $default_theme->name : GC_DEFAULT_THEME,
							$active_theme->name
						),
						__( '建议移除任何未使用的主题以增强系统的安全性。' )
					);
				}
			}
		}

		// If no default Twenty* theme exists.
		if ( ! $has_default_theme ) {
			$result['status'] = 'recommended';

			$result['label'] = __( '有可用的默认主题' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				__( '您的系统没有任何默认主题。当您的主题遇到任何问题时，GeChiUI会自动使用默认主题。' )
			);
		}

		return $result;
	}

	/**
	 * Tests if the supplied PHP version is supported.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_php_version() {
		$response = gc_check_php_version();

		$result = array(
			'label'       => sprintf(
				/* translators: %s: The current PHP version. */
				__( '您的系统正在运行最新的PHP版本（%s）。' ),
				PHP_VERSION
			),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: The minimum recommended PHP version. */
					__( 'PHP 是用于搭建 GeChiUI 的编程语言之一。较新版本的 PHP 能接受定期安全更新，也能提升您系统的性能。PHP 的最低建议版本为 %s。' ),
					$response ? $response['recommended_version'] : ''
				)
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				esc_url( gc_get_update_php_url() ),
				__( '查阅如何更新PHP' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
			'test'        => 'php_version',
		);

		// PHP is up to date.
		if ( ! $response || version_compare( PHP_VERSION, $response['recommended_version'], '>=' ) ) {
			return $result;
		}

		// The PHP version is older than the recommended version, but still receiving active support.
		if ( $response['is_supported'] ) {
			$result['label'] = sprintf(
				/* translators: %s: The server PHP version. */
				__( '您的系统正在运行较旧版本的 PHP（%s）' ),
				PHP_VERSION
			);
			$result['status'] = 'recommended';

			return $result;
		}

		/*
		 * The PHP version is still receiving security fixes, but is lower than
		 * the expected minimum version that will be required by GeChiUI in the near future.
		 */
		if ( $response['is_secure'] && $response['is_lower_than_future_minimum'] ) {
			// The `is_secure` array key name doesn't actually imply this is a secure version of PHP. It only means it receives security updates.

			$result['label'] = sprintf(
				/* translators: %s: The server PHP version. */
				__( '您的系统正在运行过时版本的PHP （%s），其很快将不被 GeChiUI 所支持。' ),
				PHP_VERSION
			);

			$result['status']         = 'critical';
			$result['badge']['label'] = __( '环境要求' );

			return $result;
		}

		// The PHP version is only receiving security fixes.
		if ( $response['is_secure'] ) {
			$result['label'] = sprintf(
				/* translators: %s: The server PHP version. */
				__( '您的系统正在运行较旧版本的 PHP（%s），应当更新' ),
				PHP_VERSION
			);
			$result['status'] = 'recommended';

			return $result;
		}

		// No more security updates for the PHP version, and lower than the expected minimum version required by GeChiUI.
		if ( $response['is_lower_than_future_minimum'] ) {
			$message = sprintf(
				/* translators: %s: The server PHP version. */
				__( '您的系统正在运行过时版本的 PHP （%s），其无法接收安全更新，而且很快将不被 GeChiUI 所支持。' ),
				PHP_VERSION
			);
		} else {
			// No more security updates for the PHP version, must be updated.
			$message = sprintf(
				/* translators: %s: The server PHP version. */
				__( '您的系统正在运行过时版本的 PHP （%s），其无法接收安全更新，且应当被升级。' ),
				PHP_VERSION
			);
		}

		$result['label']  = $message;
		$result['status'] = 'critical';

		$result['badge']['label'] = __( '安全' );

		return $result;
	}

	/**
	 * Checks if the passed extension or function are available.
	 *
	 * Make the check for available PHP modules into a simple boolean operator for a cleaner test runner.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 The `$constant_name` and `$class_name` parameters were added.
	 *
	 * @param string $extension_name Optional. The extension name to test. Default null.
	 * @param string $function_name  Optional. The function name to test. Default null.
	 * @param string $constant_name  Optional. The constant name to test for. Default null.
	 * @param string $class_name     Optional. The class name to test for. Default null.
	 * @return bool Whether or not the extension and function are available.
	 */
	private function test_php_extension_availability( $extension_name = null, $function_name = null, $constant_name = null, $class_name = null ) {
		// If no extension or function is passed, claim to fail testing, as we have nothing to test against.
		if ( ! $extension_name && ! $function_name && ! $constant_name && ! $class_name ) {
			return false;
		}

		if ( $extension_name && ! extension_loaded( $extension_name ) ) {
			return false;
		}

		if ( $function_name && ! function_exists( $function_name ) ) {
			return false;
		}

		if ( $constant_name && ! defined( $constant_name ) ) {
			return false;
		}

		if ( $class_name && ! class_exists( $class_name ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Tests if required PHP modules are installed on the host.
	 *
	 * This test builds on the recommendations made by the GeChiUI Hosting Team
	 * as seen at https://make.gechiui.com/hosting/handbook/handbook/server-environment/#php-extensions
	 *
	 * @since 5.2.0
	 *
	 * @return array
	 */
	public function get_test_php_extensions() {
		$result = array(
			'label'       => __( '必需和推荐的模组均已安装' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'PHP模组为您的系统执行大多数任务。任何修改都需要由您的服务器管理员进行。' ),
				sprintf(
					/* translators: 1: Link to the hosting group page about recommended PHP modules. 2: Additional link attributes. 3: Accessibility text. */
					__( 'GeChiUI主机团队维护着一份必需和推荐的模组列表，列于<a href="%1$s" %2$s>团队手册%3$s</a>。' ),
					/* translators: Localized team handbook, if one exists. */
					esc_url( __( 'https://make.gechiui.com/hosting/handbook/handbook/server-environment/#php-extensions' ) ),
					'target="_blank" rel="noopener"',
					sprintf(
						'<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span>',
						/* translators: Hidden accessibility text. */
						__( '（在新窗口中打开）' )
					)
				)
			),
			'actions'     => '',
			'test'        => 'php_extensions',
		);

		$modules = array(
			'curl'      => array(
				'function' => 'curl_version',
				'required' => false,
			),
			'dom'       => array(
				'class'    => 'DOMNode',
				'required' => false,
			),
			'exif'      => array(
				'function' => 'exif_read_data',
				'required' => false,
			),
			'fileinfo'  => array(
				'function' => 'finfo_file',
				'required' => false,
			),
			'hash'      => array(
				'function' => 'hash',
				'required' => false,
			),
			'imagick'   => array(
				'extension' => 'imagick',
				'required'  => false,
			),
			'json'      => array(
				'function' => 'json_last_error',
				'required' => true,
			),
			'mbstring'  => array(
				'function' => 'mb_check_encoding',
				'required' => false,
			),
			'mysqli'    => array(
				'function' => 'mysqli_connect',
				'required' => false,
			),
			'libsodium' => array(
				'constant'            => 'SODIUM_LIBRARY_VERSION',
				'required'            => false,
				'php_bundled_version' => '7.2.0',
			),
			'openssl'   => array(
				'function' => 'openssl_encrypt',
				'required' => false,
			),
			'pcre'      => array(
				'function' => 'preg_match',
				'required' => false,
			),
			'mod_xml'   => array(
				'extension' => 'libxml',
				'required'  => false,
			),
			'zip'       => array(
				'class'    => 'ZipArchive',
				'required' => false,
			),
			'filter'    => array(
				'function' => 'filter_list',
				'required' => false,
			),
			'gd'        => array(
				'extension'    => 'gd',
				'required'     => false,
				'fallback_for' => 'imagick',
			),
			'iconv'     => array(
				'function' => 'iconv',
				'required' => false,
			),
			'intl'      => array(
				'extension' => 'intl',
				'required'  => false,
			),
			'mcrypt'    => array(
				'extension'    => 'mcrypt',
				'required'     => false,
				'fallback_for' => 'libsodium',
			),
			'simplexml' => array(
				'extension'    => 'simplexml',
				'required'     => false,
				'fallback_for' => 'mod_xml',
			),
			'xmlreader' => array(
				'extension'    => 'xmlreader',
				'required'     => false,
				'fallback_for' => 'mod_xml',
			),
			'zlib'      => array(
				'extension'    => 'zlib',
				'required'     => false,
				'fallback_for' => 'zip',
			),
		);

		/**
		 * Filters the array representing all the modules we wish to test for.
		 *
		 * @since 5.2.0
		 * @since 5.3.0 The `$constant` and `$class` parameters were added.
		 *
		 * @param array $modules {
		 *     An associative array of modules to test for.
		 *
		 *     @type array ...$0 {
		 *         An associative array of module properties used during testing.
		 *         One of either `$function` or `$extension` must be provided, or they will fail by default.
		 *
		 *         @type string $function     Optional. A function name to test for the existence of.
		 *         @type string $extension    Optional. An extension to check if is loaded in PHP.
		 *         @type string $constant     Optional. A constant name to check for to verify an extension exists.
		 *         @type string $class        Optional. A class name to check for to verify an extension exists.
		 *         @type bool   $required     Is this a required feature or not.
		 *         @type string $fallback_for Optional. The module this module replaces as a fallback.
		 *     }
		 * }
		 */
		$modules = apply_filters( 'site_status_test_php_modules', $modules );

		$failures = array();

		foreach ( $modules as $library => $module ) {
			$extension_name = ( isset( $module['extension'] ) ? $module['extension'] : null );
			$function_name  = ( isset( $module['function'] ) ? $module['function'] : null );
			$constant_name  = ( isset( $module['constant'] ) ? $module['constant'] : null );
			$class_name     = ( isset( $module['class'] ) ? $module['class'] : null );

			// If this module is a fallback for another function, check if that other function passed.
			if ( isset( $module['fallback_for'] ) ) {
				/*
				 * If that other function has a failure, mark this module as required for usual operations.
				 * If that other function hasn't failed, skip this test as it's only a fallback.
				 */
				if ( isset( $failures[ $module['fallback_for'] ] ) ) {
					$module['required'] = true;
				} else {
					continue;
				}
			}

			if ( ! $this->test_php_extension_availability( $extension_name, $function_name, $constant_name, $class_name )
				&& ( ! isset( $module['php_bundled_version'] )
					|| version_compare( PHP_VERSION, $module['php_bundled_version'], '<' ) )
			) {
				if ( $module['required'] ) {
					$result['status'] = 'critical';

					$class = 'error';
					/* translators: Hidden accessibility text. */
					$screen_reader = __( '错误' );
					$message       = sprintf(
						/* translators: %s: The module name. */
						__( '必需的模组%s未被安装或已被禁用。' ),
						$library
					);
				} else {
					$class = 'warning';
					/* translators: Hidden accessibility text. */
					$screen_reader = __( '警告' );
					$message       = sprintf(
						/* translators: %s: The module name. */
						__( '可选的模组%s未被安装或已被禁用。' ),
						$library
					);
				}

				if ( ! $module['required'] && 'good' === $result['status'] ) {
					$result['status'] = 'recommended';
				}

				$failures[ $library ] = "<span class='dashicons $class'><span class='screen-reader-text'>$screen_reader</span></span> $message";
			}
		}

		if ( ! empty( $failures ) ) {
			$output = '<ul>';

			foreach ( $failures as $failure ) {
				$output .= sprintf(
					'<li>%s</li>',
					$failure
				);
			}

			$output .= '</ul>';
		}

		if ( 'good' !== $result['status'] ) {
			if ( 'recommended' === $result['status'] ) {
				$result['label'] = __( '缺少一个或多个推荐的模组' );
			}
			if ( 'critical' === $result['status'] ) {
				$result['label'] = __( '缺少一个或多个必需的模组' );
			}

			$result['description'] .= $output;
		}

		return $result;
	}

	/**
	 * Tests if the PHP default timezone is set to UTC.
	 *
	 * @since 5.3.1
	 *
	 * @return array The test results.
	 */
	public function get_test_php_default_timezone() {
		$result = array(
			'label'       => __( 'PHP默认时区有效' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'GeChiUI载入时设置了PHP的默认时区，这是为了能够正确计算日期和时间。' )
			),
			'actions'     => '',
			'test'        => 'php_default_timezone',
		);

		if ( 'UTC' !== date_default_timezone_get() ) {
			$result['status'] = 'critical';

			$result['label'] = __( 'PHP默认时区无效' );

			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: date_default_timezone_set() */
					__( 'PHP的默认时区在GeChiUI载入后被%s函数调用修改，这可能会影响日期和时间的正确计算。' ),
					'<code>date_default_timezone_set()</code>'
				)
			);
		}

		return $result;
	}

	/**
	 * Tests if there's an active PHP session that can affect loopback requests.
	 *
	 * @since 5.5.0
	 *
	 * @return array The test results.
	 */
	public function get_test_php_sessions() {
		$result = array(
			'label'       => __( '未检测到PHP会话' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: session_start(), 2: session_write_close() */
					__( '由%1$s函数调用创建的PHP会话可能会干扰REST API和回环请求。在发出任何HTTP请求之前，活动的PHP会话应由%2$s关闭。' ),
					'<code>session_start()</code>',
					'<code>session_write_close()</code>'
				)
			),
			'test'        => 'php_sessions',
		);

		if ( function_exists( 'session_status' ) && PHP_SESSION_ACTIVE === session_status() ) {
			$result['status'] = 'critical';

			$result['label'] = __( '已检测到活动的PHP会话' );

			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: session_start(), 2: session_write_close() */
					__( '%1$s函数调用生成了一个PHP会话。该会话干扰了REST API及环回请求。在做出任何HTTP请求前，该会话必须由%2$s函数关闭。' ),
					'<code>session_start()</code>',
					'<code>session_write_close()</code>'
				)
			);
		}

		return $result;
	}

	/**
	 * Tests if the SQL server is up to date.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_sql_server() {
		if ( ! $this->mysql_server_version ) {
			$this->prepare_sql_data();
		}

		$result = array(
			'label'       => __( 'SQL服务器已是最新' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'SQL服务器是GeChiUI所必需的一份软件，用以存储您的系统的所有内容和设置。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				/* translators: Localized version of GeChiUI requirements if one exists. */
				esc_url( __( 'https://www.gechiui.com/about/requirements/' ) ),
				__( '了解GeChiUI的运行需求' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
			'test'        => 'sql_server',
		);

		$db_dropin = file_exists( GC_CONTENT_DIR . '/db.php' );

		if ( ! $this->is_recommended_mysql_version ) {
			$result['status'] = 'recommended';

			$result['label'] = __( '陈旧的SQL服务器' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: The database engine in use (MySQL or MariaDB). 2: Database server recommended version number. */
					__( '出于最佳性能和安全考虑，建议您运行 %1$s %2$s 或更高版本。 请联系您的主机提供商以更正此问题。' ),
					( $this->is_mariadb ? 'MariaDB' : 'MySQL' ),
					$this->mysql_recommended_version
				)
			);
		}

		if ( ! $this->is_acceptable_mysql_version ) {
			$result['status'] = 'critical';

			$result['label']          = __( '极度陈旧的SQL服务器' );
			$result['badge']['label'] = __( '安全' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: The database engine in use (MySQL or MariaDB). 2: Database server minimum version number. */
					__( 'GeChiUI需要%2$s及更高版本的%1$s。请联系您的主机提供商来修正此项。' ),
					( $this->is_mariadb ? 'MariaDB' : 'MySQL' ),
					$this->mysql_required_version
				)
			);
		}

		if ( $db_dropin ) {
			$result['description'] .= sprintf(
				'<p>%s</p>',
				gc_kses(
					sprintf(
						/* translators: 1: The name of the drop-in. 2: The name of the database engine. */
						__( '您正在使用增强插件%1$s，这可能意味着%2$s数据库并未被使用。' ),
						'<code>gc-content/db.php</code>',
						( $this->is_mariadb ? 'MariaDB' : 'MySQL' )
					),
					array(
						'code' => true,
					)
				)
			);
		}

		return $result;
	}

	/**
	 * Tests if the database server is capable of using utf8mb4.
	 *
	 * @since 5.2.0
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @return array The test results.
	 */
	public function get_test_utf8mb4_support() {
		global $gcdb;

		if ( ! $this->mysql_server_version ) {
			$this->prepare_sql_data();
		}

		$result = array(
			'label'       => __( '支持UTF8MB4' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'UTF8MB4是GeChiUI建议使用于数据库存储的字符集，它能安全地支持包括Emoji在内的字符和编码，并对非英文语言提供更好的支持。' )
			),
			'actions'     => '',
			'test'        => 'utf8mb4_support',
		);

		if ( ! $this->is_mariadb ) {
			if ( version_compare( $this->mysql_server_version, '5.5.3', '<' ) ) {
				$result['status'] = 'recommended';

				$result['label'] = __( 'utf8mb4需要MySQL更新' );

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %s: Version number. */
						__( 'GeChiUI的utf8mb4支持需要MySQL版本%s或更高。请联系您的服务器管理员。' ),
						'5.5.3'
					)
				);
			} else {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的MySQL版本支持utf8mb4。' )
				);
			}
		} else { // MariaDB introduced utf8mb4 support in 5.5.0.
			if ( version_compare( $this->mysql_server_version, '5.5.0', '<' ) ) {
				$result['status'] = 'recommended';

				$result['label'] = __( 'utf8mb4需要MariaDB更新' );

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %s: Version number. */
						__( 'GeChiUI的utf8mb4支持需要MariaDB版本%s或更高。请联系您的服务器管理员。' ),
						'5.5.0'
					)
				);
			} else {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的MariaDB版本支持utf8mb4。' )
				);
			}
		}

		if ( $gcdb->use_mysqli ) {
			// phpcs:ignore GeChiUI.DB.RestrictedFunctions.mysql_mysqli_get_client_info
			$mysql_client_version = mysqli_get_client_info();
		} else {
			// phpcs:ignore GeChiUI.DB.RestrictedFunctions.mysql_mysql_get_client_info,PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			$mysql_client_version = mysql_get_client_info();
		}

		/*
		 * libmysql has supported utf8mb4 since 5.5.3, same as the MySQL server.
		 * mysqlnd has supported utf8mb4 since 5.0.9.
		 */
		if ( str_contains( $mysql_client_version, 'mysqlnd' ) ) {
			$mysql_client_version = preg_replace( '/^\D+([\d.]+).*/', '$1', $mysql_client_version );
			if ( version_compare( $mysql_client_version, '5.0.9', '<' ) ) {
				$result['status'] = 'recommended';

				$result['label'] = __( 'utf8mb4需要更新的用户端库' );

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: 1: Name of the library, 2: Number of version. */
						__( 'GeChiUI的utf8mb4支持需要MySQL用户端库（%1$s）版本%2$s或更高。请联系您的服务器管理员。' ),
						'mysqlnd',
						'5.0.9'
					)
				);
			}
		} else {
			if ( version_compare( $mysql_client_version, '5.5.3', '<' ) ) {
				$result['status'] = 'recommended';

				$result['label'] = __( 'utf8mb4需要更新的用户端库' );

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: 1: Name of the library, 2: Number of version. */
						__( 'GeChiUI的utf8mb4支持需要MySQL用户端库（%1$s）版本%2$s或更高。请联系您的服务器管理员。' ),
						'libmysql',
						'5.5.3'
					)
				);
			}
		}

		return $result;
	}

	/**
	 * Tests if the site can communicate with www.GeChiUI.com.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_dotorg_communication() {
		$result = array(
			'label'       => __( '能够与www.GeChiUI.com通信' ),
			'status'      => '',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'GeChiUI 需要与服务器进行通信来检查新版本，以及安装、更新 GeChiUI 核心、主题或插件。' )
			),
			'actions'     => '',
			'test'        => 'dotorg_communication',
		);

		$gc_dotorg = gc_remote_get(
			'https://api.gechiui.com',
			array(
				'timeout' => 10,
			)
		);
		if ( ! is_gc_error( $gc_dotorg ) ) {
			$result['status'] = 'good';
		} else {
			$result['status'] = 'critical';

			$result['label'] = __( '未能与www.GeChiUI.com通信' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					'<span class="error"><span class="screen-reader-text">%s</span></span> %s',
					/* translators: Hidden accessibility text. */
					__( '错误' ),
					sprintf(
						/* translators: 1: The IP address www.GeChiUI.com resolves to. 2: The error returned by the lookup. */
						__( '您的系统无法与www.GeChiUI.com（%1$s）通信，并返回了错误：%2$s' ),
						gethostbyname( 'api.gechiui.com' ),
						$gc_dotorg->get_error_message()
					)
				)
			);

			$result['actions'] = sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				/* translators: Localized Support reference. */
				esc_url( __( 'https://www.gechiui.com/support/forums/' ) ),
				__( '获取解决此问题的帮助。' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			);
		}

		return $result;
	}

	/**
	 * Tests if debug information is enabled.
	 *
	 * When GC_DEBUG is enabled, errors and information may be disclosed to site visitors,
	 * or logged to a publicly accessible file.
	 *
	 * Debugging is also frequently left enabled after looking for errors on a site,
	 * as site owners do not understand the implications of this.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_is_in_debug_mode() {
		$result = array(
			'label'       => __( '您的系统没有被设置为输出调试信息' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '调试模式通常被用来获得关于一个错误或系统功能的详细信息，但也可能包含在一个公开系统（如网站、SaaS等）上不应泄露的敏感信息。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				/* translators: Documentation explaining debugging in GeChiUI. */
				esc_url( __( 'https://www.gechiui.com/support/debugging-in-gechiui/' ) ),
				__( '了解更多在GeChiUI中调试的信息' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
			'test'        => 'is_in_debug_mode',
		);

		if ( defined( 'GC_DEBUG' ) && GC_DEBUG ) {
			if ( defined( 'GC_DEBUG_LOG' ) && GC_DEBUG_LOG ) {
				$result['label'] = __( '您的系统已设置为将错误日志保存到可公开读写的文件中' );

				$result['status'] = str_starts_with( ini_get( 'error_log' ), ABSPATH ) ? 'critical' : 'recommended';

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %s: GC_DEBUG_LOG */
						__( '数值%s已加入系统配置文件，这意味着此系统上发生的错误会被写入一个可能公开可见的文件。' ),
						'<code>GC_DEBUG_LOG</code>'
					)
				);
			}

			if ( defined( 'GC_DEBUG_DISPLAY' ) && GC_DEBUG_DISPLAY ) {
				$result['label'] = __( '您的系统被设置为向系统访客展示错误' );

				$result['status'] = 'critical';

				// On development environments, set the status to recommended.
				if ( $this->is_development_environment() ) {
					$result['status'] = 'recommended';
				}

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: 1: GC_DEBUG_DISPLAY, 2: GC_DEBUG */
						__( '此数值，%1$s，或是已被%2$s启用，或是已被加入您的配置文件。这将会让错误被显示在您的系统前端。' ),
						'<code>GC_DEBUG_DISPLAY</code>',
						'<code>GC_DEBUG</code>'
					)
				);
			}
		}

		return $result;
	}

	/**
	 * Tests if the site is serving content over HTTPS.
	 *
	 * Many sites have varying degrees of HTTPS support, the most common of which is sites that have it
	 * enabled, but only if you visit the right site address.
	 *
	 * @since 5.2.0
	 * @since 5.7.0 Updated to rely on {@see gc_is_using_https()} and {@see gc_is_https_supported()}.
	 *
	 * @return array The test results.
	 */
	public function get_test_https_status() {
		/*
		 * Enforce fresh HTTPS detection results. This is normally invoked by using cron,
		 * but for Site Health it should always rely on the latest results.
		 */
		gc_update_https_detection_errors();

		$default_update_url = gc_get_default_update_https_url();

		$result = array(
			'label'       => __( '您的系统正在使用活跃HTTPS连接' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'HTTPS连接是更安全的网络浏览方式。许多服务都已开始要求使用HTTPS。HTTPS让您能够使用可以提高系统速度、改善搜索排名和通过保护访客隐私来赢得信任的新功能。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				esc_url( $default_update_url ),
				__( '详细了解为何您应该使用 HTTPS ' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
			'test'        => 'https_status',
		);

		if ( ! gc_is_using_https() ) {
			/*
			 * If the website is not using HTTPS, provide more information
			 * about whether it is supported and how it can be enabled.
			 */
			$result['status'] = 'recommended';
			$result['label']  = __( '您的系统没有使用HTTPS' );

			if ( gc_is_site_url_using_https() ) {
				if ( is_ssl() ) {
					$result['description'] = sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: %s: URL to Settings > General > Site Address. */
							__( '您正在通过HTTPS访问此系统，但您的<a href="%s">系统地址</a>并未设为默认使用HTTPS。' ),
							esc_url( admin_url( 'options-general.php' ) . '#home' )
						)
					);
				} else {
					$result['description'] = sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: %s: URL to Settings > General > Site Address. */
							__( '您的<a href="%s">系统地址</a>未设置为使用HTTPS。' ),
							esc_url( admin_url( 'options-general.php' ) . '#home' )
						)
					);
				}
			} else {
				if ( is_ssl() ) {
					$result['description'] = sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: 1: URL to Settings > General > GeChiUI Address, 2: URL to Settings > General > Site Address. */
							__( '您正在通过HTTPS访问此系统，但您的<a href="%1$s">GeChiUI地址</a>及<a href="%2$s">系统地址</a>并未设为默认使用HTTPS。' ),
							esc_url( admin_url( 'options-general.php' ) . '#siteurl' ),
							esc_url( admin_url( 'options-general.php' ) . '#home' )
						)
					);
				} else {
					$result['description'] = sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: 1: URL to Settings > General > GeChiUI Address, 2: URL to Settings > General > Site Address. */
							__( '您的<a href="%1$s">GeChiUI地址</a>及<a href="%2$s">系统地址</a>未设置为使用HTTPS。' ),
							esc_url( admin_url( 'options-general.php' ) . '#siteurl' ),
							esc_url( admin_url( 'options-general.php' ) . '#home' )
						)
					);
				}
			}

			if ( gc_is_https_supported() ) {
				$result['description'] .= sprintf(
					'<p>%s</p>',
					__( '您的系统已支持HTTPS。' )
				);

				if ( defined( 'GC_HOME' ) || defined( 'GC_SITEURL' ) ) {
					$result['description'] .= sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: 1: gc-config.php, 2: GC_HOME, 3: GC_SITEURL */
							__( '但是，您的GeChiUI地址当前由PHP常量控制，因此无法更新。您需要编辑%1$s并移除或更新%2$s和%3$s的定义。' ),
							'<code>gc-config.php</code>',
							'<code>GC_HOME</code>',
							'<code>GC_SITEURL</code>'
						)
					);
				} elseif ( current_user_can( 'update_https' ) ) {
					$default_direct_update_url = add_query_arg( 'action', 'update_https', gc_nonce_url( admin_url( 'site-health.php' ), 'gc_update_https' ) );
					$direct_update_url         = gc_get_direct_update_https_url();

					if ( ! empty( $direct_update_url ) ) {
						$result['actions'] = sprintf(
							'<p class="button-container"><a class="btn btn-primary" href="%1$s" target="_blank" rel="noopener">%2$s<span class="screen-reader-text"> %3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
							esc_url( $direct_update_url ),
							__( '更新系统以使用HTTPS' ),
							/* translators: Hidden accessibility text. */
							__( '（在新窗口中打开）' )
						);
					} else {
						$result['actions'] = sprintf(
							'<p class="button-container"><a class="btn btn-primary" href="%1$s">%2$s</a></p>',
							esc_url( $default_direct_update_url ),
							__( '更新系统以使用HTTPS' )
						);
					}
				}
			} else {
				// If host-specific "Update HTTPS" URL is provided, include a link.
				$update_url = gc_get_update_https_url();
				if ( $update_url !== $default_update_url ) {
					$result['description'] .= sprintf(
						'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
						esc_url( $update_url ),
						__( '请与您的主机提供商讨论如何为您的系统提供HTTPS支持。' ),
						/* translators: Hidden accessibility text. */
						__( '（在新窗口中打开）' )
					);
				} else {
					$result['description'] .= sprintf(
						'<p>%s</p>',
						__( '请与您的主机提供商讨论如何为您的系统提供HTTPS支持。' )
					);
				}
			}
		}

		return $result;
	}

	/**
	 * Checks if the HTTP API can handle SSL/TLS requests.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test result.
	 */
	public function get_test_ssl_support() {
		$result = array(
			'label'       => '',
			'status'      => '',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '服务器间的安全通讯对传输文件、进行交易等活动而言实属必需。' )
			),
			'actions'     => '',
			'test'        => 'ssl_support',
		);

		$supports_https = gc_http_supports( array( 'ssl' ) );

		if ( $supports_https ) {
			$result['status'] = 'good';

			$result['label'] = __( '您的系统能够与其他服务安全地通信' );
		} else {
			$result['status'] = 'critical';

			$result['label'] = __( '您的系统无法与其他服务安全通信' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				__( '请向您的主机提供商询问以了解对 PHP的 OpenSSL 支持。' )
			);
		}

		return $result;
	}

	/**
	 * Tests if scheduled events run as intended.
	 *
	 * If scheduled events are not running, this may indicate something with GC_Cron is not working
	 * as intended, or that there are orphaned events hanging around from older code.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_scheduled_events() {
		$result = array(
			'label'       => __( '计划事件正在运行' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '计划事件包括定期检查插件、主题和GeChiUI自身的更新，也包括让计划文章按时发布。很多插件也通过计划事件来确保动作按期执行。' )
			),
			'actions'     => '',
			'test'        => 'scheduled_events',
		);

		$this->gc_schedule_test_init();

		if ( is_gc_error( $this->has_missed_cron() ) ) {
			$result['status'] = 'critical';

			$result['label'] = __( '不能检查您的计划事件' );

			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: The error message returned while from the cron scheduler. */
					__( '在试图检查您的系统的计划事件时，遇到了此错误：%s' ),
					$this->has_missed_cron()->get_error_message()
				)
			);
		} elseif ( $this->has_missed_cron() ) {
			$result['status'] = 'recommended';

			$result['label'] = __( '一个计划事件已失败' );

			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: The name of the failed cron event. */
					__( '计划事件%s执行失败。您的系统仍然工作，但这可能意味着定时发布或自动更新不再正常运行。' ),
					$this->last_missed_cron
				)
			);
		} elseif ( $this->has_late_cron() ) {
			$result['status'] = 'recommended';

			$result['label'] = __( '一个计划事件被延迟' );

			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: The name of the late cron event. */
					__( '计划事件%s执行被延迟。您的系统仍然工作，但这可能意味着定时发布或自动更新不再正常运行。' ),
					$this->last_late_cron
				)
			);
		}

		return $result;
	}

	/**
	 * Tests if GeChiUI can run automated background updates.
	 *
	 * Background updates in GeChiUI are primarily used for minor releases and security updates.
	 * It's important to either have these working, or be aware that they are intentionally disabled
	 * for whatever reason.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_background_updates() {
		$result = array(
			'label'       => __( '后台更新正常工作' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '后台更新确保GeChiUI能够在您正运行的版本需要安全更新时自动更新GeChiUI。' )
			),
			'actions'     => '',
			'test'        => 'background_updates',
		);

		if ( ! class_exists( 'GC_Site_Health_Auto_Updates' ) ) {
			require_once ABSPATH . 'gc-admin/includes/class-gc-site-health-auto-updates.php';
		}

		/*
		 * Run the auto-update tests in a separate class,
		 * as there are many considerations to be made.
		 */
		$automatic_updates = new GC_Site_Health_Auto_Updates();
		$tests             = $automatic_updates->run_tests();

		$output = '<ul>';

		foreach ( $tests as $test ) {
			/* translators: Hidden accessibility text. */
			$severity_string = __( '通过' );

			if ( 'fail' === $test->severity ) {
				$result['label'] = __( '后台更新未能正常工作' );

				$result['status'] = 'critical';

				/* translators: Hidden accessibility text. */
				$severity_string = __( '错误' );
			}

			if ( 'warning' === $test->severity && 'good' === $result['status'] ) {
				$result['label'] = __( '后台更新可能未能正常工作' );

				$result['status'] = 'recommended';

				/* translators: Hidden accessibility text. */
				$severity_string = __( '警告' );
			}

			$output .= sprintf(
				'<li><span class="dashicons %s"><span class="screen-reader-text">%s</span></span> %s</li>',
				esc_attr( $test->severity ),
				$severity_string,
				$test->description
			);
		}

		$output .= '</ul>';

		if ( 'good' !== $result['status'] ) {
			$result['description'] .= $output;
		}

		return $result;
	}

	/**
	 * Tests if plugin and theme auto-updates appear to be configured correctly.
	 *
	 * @since 5.5.0
	 *
	 * @return array The test results.
	 */
	public function get_test_plugin_theme_auto_updates() {
		$result = array(
			'label'       => __( '插件和主题自动更新配置正确' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '插件与主题的自动更新能保证安装的总是最新版本。' )
			),
			'actions'     => '',
			'test'        => 'plugin_theme_auto_updates',
		);

		$check_plugin_theme_updates = $this->detect_plugin_theme_auto_update_issues();

		$result['status'] = $check_plugin_theme_updates->status;

		if ( 'good' !== $result['status'] ) {
			$result['label'] = __( '您的系统可能在自动更新插件和主题时遇到问题' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				$check_plugin_theme_updates->message
			);
		}

		return $result;
	}

	/**
	 * Tests available disk space for updates.
	 *
	 * @since 6.3.0
	 *
	 * @return array The test results.
	 */
	public function get_test_available_updates_disk_space() {
		$available_space = function_exists( 'disk_free_space' ) ? @disk_free_space( GC_CONTENT_DIR . '/upgrade/' ) : false;

		$result = array(
			'label'       => __( '可用于安全执行更新的磁盘空间' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				/* translators: %s: Available disk space in MB or GB. */
				'<p>' . __( '检测到%s可用磁盘空间，可以安全地执行更新例程。' ) . '</p>',
				size_format( $available_space )
			),
			'actions'     => '',
			'test'        => 'available_updates_disk_space',
		);

		if ( false === $available_space ) {
			$result['description'] = __( '无法确定可用于更新的磁盘空间。' );
			$result['status']      = 'recommended';
		} elseif ( $available_space < 20 * MB_IN_BYTES ) {
			$result['description'] = __( '可用磁盘空间非常低，可用空间不足 20 MB。请谨慎操作，更新可能会失败。' );
			$result['status']      = 'critical';
		} elseif ( $available_space < 100 * MB_IN_BYTES ) {
			$result['description'] = __( '可用磁盘空间较低，可用空间不足 100 MB。' );
			$result['status']      = 'recommended';
		}

		return $result;
	}

	/**
	 * Tests if plugin and theme temporary backup directories are writable or can be created.
	 *
	 * @since 6.3.0
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @return array The test results.
	 */
	public function get_test_update_temp_backup_writable() {
		global $gc_filesystem;

		$result = array(
			'label'       => __( '插件和主题临时备份目录可写' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				/* translators: %s: gc-content/upgrade-temp-backup */
				'<p>' . __( '用于提高插件和主题更新稳定性的%s目录是可写的。' ) . '</p>',
				'<code>gc-content/upgrade-temp-backup</code>'
			),
			'actions'     => '',
			'test'        => 'update_temp_backup_writable',
		);

		if ( ! function_exists( 'GC_Filesystem' ) ) {
			require_once ABSPATH . '/gc-admin/includes/file.php';
		}

		ob_start();
		$credentials = request_filesystem_credentials( '' );
		ob_end_clean();

		if ( false === $credentials || ! GC_Filesystem( $credentials ) ) {
			$result['status']      = 'recommended';
			$result['label']       = __( '无法访问文件系统' );
			$result['description'] = __( '无法连接至文件系统。 请确认您的凭据。' );
			return $result;
		}

		$gc_content = $gc_filesystem->gc_content_dir();

		if ( ! $gc_content ) {
			$result['status']      = 'critical';
			$result['label']       = __( '无法找到 GeChiUI 内容目录' );
			$result['description'] = sprintf(
				/* translators: %s: gc-content */
				'<p>' . __( '找不到%s目录。' ) . '</p>',
				'<code>gc-content</code>'
			);
			return $result;
		}

		$upgrade_dir_exists      = $gc_filesystem->is_dir( "$gc_content/upgrade" );
		$upgrade_dir_is_writable = $gc_filesystem->is_writable( "$gc_content/upgrade" );
		$backup_dir_exists       = $gc_filesystem->is_dir( "$gc_content/upgrade-temp-backup" );
		$backup_dir_is_writable  = $gc_filesystem->is_writable( "$gc_content/upgrade-temp-backup" );

		$plugins_dir_exists      = $gc_filesystem->is_dir( "$gc_content/upgrade-temp-backup/plugins" );
		$plugins_dir_is_writable = $gc_filesystem->is_writable( "$gc_content/upgrade-temp-backup/plugins" );
		$themes_dir_exists       = $gc_filesystem->is_dir( "$gc_content/upgrade-temp-backup/themes" );
		$themes_dir_is_writable  = $gc_filesystem->is_writable( "$gc_content/upgrade-temp-backup/themes" );

		if ( $plugins_dir_exists && ! $plugins_dir_is_writable && $themes_dir_exists && ! $themes_dir_is_writable ) {
			$result['status']      = 'critical';
			$result['label']       = __( '插件和主题临时备份目录存在但不可写' );
			$result['description'] = sprintf(
				/* translators: 1: gc-content/upgrade-temp-backup/plugins, 2: gc-content/upgrade-temp-backup/themes. */
				'<p>' . __( '%1$s和%2$s目录存在，但不可写。这些目录用于提高插件更新的稳定性。请确保服务器对这些目录有写权限。' ) . '</p>',
				'<code>gc-content/upgrade-temp-backup/plugins</code>',
				'<code>gc-content/upgrade-temp-backup/themes</code>'
			);
			return $result;
		}

		if ( $plugins_dir_exists && ! $plugins_dir_is_writable ) {
			$result['status']      = 'critical';
			$result['label']       = __( '插件临时备份目录存在但不可写' );
			$result['description'] = sprintf(
				/* translators: %s: gc-content/upgrade-temp-backup/plugins */
				'<p>' . __( '%s目录存在但不可写。该目录用于提高插件更新的稳定性。请确保服务器对此目录有写权限。' ) . '</p>',
				'<code>gc-content/upgrade-temp-backup/plugins</code>'
			);
			return $result;
		}

		if ( $themes_dir_exists && ! $themes_dir_is_writable ) {
			$result['status']      = 'critical';
			$result['label']       = __( '主题临时备份目录存在但不可写' );
			$result['description'] = sprintf(
				/* translators: %s: gc-content/upgrade-temp-backup/themes */
				'<p>' . __( '%s目录存在但不可写。该目录用于提高主题更新的稳定性。请确保服务器对此目录有写权限。' ) . '</p>',
				'<code>gc-content/upgrade-temp-backup/themes</code>'
			);
			return $result;
		}

		if ( ( ! $plugins_dir_exists || ! $themes_dir_exists ) && $backup_dir_exists && ! $backup_dir_is_writable ) {
			$result['status']      = 'critical';
			$result['label']       = __( '临时备份目录存在但不可写' );
			$result['description'] = sprintf(
				/* translators: %s: gc-content/upgrade-temp-backup */
				'<p>' . __( '%s目录存在但不可写。该目录用于提高插件和主题更新的稳定性。请确保服务器对此目录有写权限。' ) . '</p>',
				'<code>gc-content/upgrade-temp-backup</code>'
			);
			return $result;
		}

		if ( ! $backup_dir_exists && $upgrade_dir_exists && ! $upgrade_dir_is_writable ) {
			$result['status']      = 'critical';
			$result['label']       = __( '升级目录存在但不可写' );
			$result['description'] = sprintf(
				/* translators: %s: gc-content/upgrade */
				'<p>' . __( '%s 目录存在但不可写。该目录用于插件和主题更新。请确保服务器对此目录有写权限。' ) . '</p>',
				'<code>gc-content/upgrade</code>'
			);
			return $result;
		}

		if ( ! $upgrade_dir_exists && ! $gc_filesystem->is_writable( $gc_content ) ) {
			$result['status']      = 'critical';
			$result['label']       = __( '无法创建升级目录' );
			$result['description'] = sprintf(
				/* translators: 1: gc-content/upgrade, 2: gc-content. */
				'<p>' . __( '%1$s 目录不存在，服务器在 %2$s 中没有创建该目录的写入权限。该目录用于插件和主题更新。请确保服务器在 %2$s 中具有写入权限。' ) . '</p>',
				'<code>gc-content/upgrade</code>',
				'<code>gc-content</code>'
			);
			return $result;
		}

		return $result;
	}

	/**
	 * Tests if loopbacks work as expected.
	 *
	 * A loopback is when GeChiUI queries itself, for example to start a new GC_Cron instance,
	 * or when editing a plugin or theme. This has shown itself to be a recurring issue,
	 * as code can very easily break this interaction.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_loopback_requests() {
		$result = array(
			'label'       => __( '您的系统可以进行环回请求' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '环回请求被用来运行计划事件，也被内建的主题及插件编辑器使用来确保代码稳定性。' )
			),
			'actions'     => '',
			'test'        => 'loopback_requests',
		);

		$check_loopback = $this->can_perform_loopback();

		$result['status'] = $check_loopback->status;

		if ( 'good' !== $result['status'] ) {
			$result['label'] = __( '您的系统不能完成环回请求' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				$check_loopback->message
			);
		}

		return $result;
	}

	/**
	 * Tests if HTTP requests are blocked.
	 *
	 * It's possible to block all outgoing communication (with the possibility of allowing certain
	 * hosts) via the HTTP API. This may create problems for users as many features are running as
	 * services these days.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_http_requests() {
		$result = array(
			'label'       => __( 'HTTP请求似乎工作正常' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '系统维护者可以阻止全部或部分到其他系统和服务的连接。如果没有正确设置，这可能会让一些插件及主题停止工作。' )
			),
			'actions'     => '',
			'test'        => 'http_requests',
		);

		$blocked = false;
		$hosts   = array();

		if ( defined( 'GC_HTTP_BLOCK_EXTERNAL' ) && GC_HTTP_BLOCK_EXTERNAL ) {
			$blocked = true;
		}

		if ( defined( 'GC_ACCESSIBLE_HOSTS' ) ) {
			$hosts = explode( ',', GC_ACCESSIBLE_HOSTS );
		}

		if ( $blocked && 0 === count( $hosts ) ) {
			$result['status'] = 'critical';

			$result['label'] = __( 'HTTP请求被阻止' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: Name of the constant used. */
					__( 'HTTP请求已被%s常量阻止，且没有允许的主机。' ),
					'<code>GC_HTTP_BLOCK_EXTERNAL</code>'
				)
			);
		}

		if ( $blocked && 0 < count( $hosts ) ) {
			$result['status'] = 'recommended';

			$result['label'] = __( 'HTTP请求被部分阻止' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: Name of the constant used. 2: List of allowed hostnames. */
					__( 'HTTP请求已被%1$s常量阻止，并允许了一些主机：%2$s。' ),
					'<code>GC_HTTP_BLOCK_EXTERNAL</code>',
					implode( ',', $hosts )
				)
			);
		}

		return $result;
	}

	/**
	 * Tests if the REST API is accessible.
	 *
	 * Various security measures may block the REST API from working, or it may have been disabled in general.
	 * This is required for the new block editor to work, so we explicitly test for this.
	 *
	 * @since 5.2.0
	 *
	 * @return array The test results.
	 */
	public function get_test_rest_availability() {
		$result = array(
			'label'       => __( 'REST API可用' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'REST API 是 GeChiUI 及其他应用与服务器通信的一种途径。例如，区块编辑器页面就依赖 REST API 来显示及保存您的页面和文章。' )
			),
			'actions'     => '',
			'test'        => 'rest_availability',
		);

		$cookies = gc_unslash( $_COOKIE );
		$timeout = 10; // 10 seconds.
		$headers = array(
			'Cache-Control' => 'no-cache',
			'X-GC-Nonce'    => gc_create_nonce( 'gc_rest' ),
		);
		/** This filter is documented in gc-includes/class-gc-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		// Include Basic auth in loopback requests.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( gc_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . gc_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		$url = rest_url( 'gc/v2/types/post' );

		// The context for this is editing with the new block editor.
		$url = add_query_arg(
			array(
				'context' => 'edit',
			),
			$url
		);

		$r = gc_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		if ( is_gc_error( $r ) ) {
			$result['status'] = 'critical';

			$result['label'] = __( 'REST API遇到了错误' );

			$result['description'] .= sprintf(
				'<p>%s</p><p>%s<br>%s</p>',
				__( '在测试 REST API 时，发生了一个错误：' ),
				sprintf(
					// translators: %s: The REST API URL.
					__( 'REST API 端点：%s' ),
					$url
				),
				sprintf(
					// translators: 1: The GeChiUI error code. 2: The GeChiUI error message.
					__( 'REST API 响应：(%1$s) %2$s' ),
					$r->get_error_code(),
					$r->get_error_message()
				)
			);
		} elseif ( 200 !== gc_remote_retrieve_response_code( $r ) ) {
			$result['status'] = 'recommended';

			$result['label'] = __( 'REST API遇到了预料之外的结果' );

			$result['description'] .= sprintf(
				'<p>%s</p><p>%s<br>%s</p>',
				__( '当测试 REST API 时返回了预期之外的结果：' ),
				sprintf(
					// translators: %s: The REST API URL.
					__( 'REST API 端点：%s' ),
					$url
				),
				sprintf(
					// translators: 1: The GeChiUI error code. 2: The HTTP status code error message.
					__( 'REST API 响应：(%1$s) %2$s' ),
					gc_remote_retrieve_response_code( $r ),
					gc_remote_retrieve_response_message( $r )
				)
			);
		} else {
			$json = json_decode( gc_remote_retrieve_body( $r ), true );

			if ( false !== $json && ! isset( $json['capabilities'] ) ) {
				$result['status'] = 'recommended';

				$result['label'] = __( 'REST API行为不正确' );

				$result['description'] .= sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %s: The name of the query parameter being tested. */
						__( 'REST API未能正确处理%s请求参数。' ),
						'<code>context</code>'
					)
				);
			}
		}

		return $result;
	}

	/**
	 * Tests if 'file_uploads' directive in PHP.ini is turned off.
	 *
	 * @since 5.5.0
	 *
	 * @return array The test results.
	 */
	public function get_test_file_uploads() {
		$result = array(
			'label'       => __( '文件可以上传' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: file_uploads, 2: php.ini */
					__( '%2$s中的%1$s指令决定了您的系统是否能上传文件。' ),
					'<code>file_uploads</code>',
					'<code>php.ini</code>'
				)
			),
			'actions'     => '',
			'test'        => 'file_uploads',
		);

		if ( ! function_exists( 'ini_get' ) ) {
			$result['status']       = 'critical';
			$result['description'] .= sprintf(
				/* translators: %s: ini_get() */
				__( '%s函数已被禁用，因此某些媒体设置不可用。' ),
				'<code>ini_get()</code>'
			);
			return $result;
		}

		if ( empty( ini_get( 'file_uploads' ) ) ) {
			$result['status']       = 'critical';
			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: file_uploads, 2: 0 */
					__( '%1$s的值被设置为%2$s，因此您无法在这个系统上传文件。' ),
					'<code>file_uploads</code>',
					'<code>0</code>'
				)
			);
			return $result;
		}

		$post_max_size       = ini_get( 'post_max_size' );
		$upload_max_filesize = ini_get( 'upload_max_filesize' );

		if ( gc_convert_hr_to_bytes( $post_max_size ) < gc_convert_hr_to_bytes( $upload_max_filesize ) ) {
			$result['label'] = sprintf(
				/* translators: 1: post_max_size, 2: upload_max_filesize */
				__( '"%1$s"的值小于"%2$s"' ),
				'post_max_size',
				'upload_max_filesize'
			);
			$result['status'] = 'recommended';

			if ( 0 === gc_convert_hr_to_bytes( $post_max_size ) ) {
				$result['description'] = sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: 1: post_max_size, 2: upload_max_filesize */
						__( '%1$s当前被配置为0，当尝试通过依赖各种上传方法的插件或主题功能上传文件时，这可能会导致一些问题。建议将此设置配置为固定值，最好与%2$s的值匹配，因为某些上传方法将数值“0”读取为无限制或禁用。' ),
						'<code>post_max_size</code>',
						'<code>upload_max_filesize</code>'
					)
				);
			} else {
				$result['description'] = sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: 1: post_max_size, 2: upload_max_filesize */
						__( '%1$s 的设置小于 %2$s，这可能会在尝试上传文件时出现一些问题。' ),
						'<code>post_max_size</code>',
						'<code>upload_max_filesize</code>'
					)
				);
			}

			return $result;
		}

		return $result;
	}

	/**
	 * Tests if the Authorization header has the expected values.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function get_test_authorization_header() {
		$result = array(
			'label'       => __( '授权标头按预期正常运作' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '安全' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( '授权标头被由您为此系统批准的第三方应用使用。如果没有此标头，这些应用程序将无法连接到您的系统。' )
			),
			'actions'     => '',
			'test'        => 'authorization_header',
		);

		if ( ! isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
			$result['label'] = __( '授权标头丢失' );
		} elseif ( 'user' !== $_SERVER['PHP_AUTH_USER'] || 'pwd' !== $_SERVER['PHP_AUTH_PW'] ) {
			$result['label'] = __( '授权标头无效' );
		} else {
			return $result;
		}

		$result['status']       = 'recommended';
		$result['description'] .= sprintf(
			'<p>%s</p>',
			__( '如果在尝试了下面的操作之后您仍然看到了这些警告信息，您可能需要联系您的主机提供商以获取进一步的帮助。' )
		);

		if ( ! function_exists( 'got_mod_rewrite' ) ) {
			require_once ABSPATH . 'gc-admin/includes/misc.php';
		}

		if ( got_mod_rewrite() ) {
			$result['actions'] .= sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( admin_url( 'options-permalink.php' ) ),
				__( '重新整理固定链接' )
			);
		} else {
			$result['actions'] .= sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				__( 'https://developer.gechiui.com/rest-api/frequently-asked-questions/#why-is-authentication-not-working' ),
				__( '了解如何配置授权标头。' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			);
		}

		return $result;
	}

	/**
	 * Tests if a full page cache is available.
	 *
	 * @since 6.1.0
	 *
	 * @return array The test result.
	 */
	public function get_test_page_cache() {
		$description  = '<p>' . __( '页面缓存通过保存和提供静态页面使得用户访问时不需要每次都调用页面，进而改善了您系统的速度和性能。' ) . '</p>';
		$description .= '<p>' . __( '页面缓存会通过查找已启用的页面缓存插件的同时向主页发起三次请求并查找一个或多个下列的 HTTP 用户端响应标头，来确定页面缓存的存在。' ) . '</p>';
		$description .= '<code>' . implode( '</code>, <code>', array_keys( $this->get_page_cache_headers() ) ) . '.</code>';

		$result = array(
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'description' => gc_kses_post( $description ),
			'test'        => 'page_cache',
			'status'      => 'good',
			'label'       => '',
			'actions'     => sprintf(
				'<p><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span class="screen-reader-text"> %3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				__( 'https://www.gechiui.com/support/optimization/#Caching' ),
				__( '了解有关页面缓存的更多信息' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
		);

		$page_cache_detail = $this->get_page_cache_detail();

		if ( is_gc_error( $page_cache_detail ) ) {
			$result['label']  = __( '无法检测到页面缓存的存在' );
			$result['status'] = 'recommended';
			$error_info       = sprintf(
			/* translators: 1: Error message, 2: Error code. */
				__( '由于可能的环回请求问题，无法检测页面缓存的是否存在。请确认环回请求测试是否通过。错误：%1$s（代码： %2$s）' ),
				$page_cache_detail->get_error_message(),
				$page_cache_detail->get_error_code()
			);
			$result['description'] = gc_kses_post( "<p>$error_info</p>" ) . $result['description'];
			return $result;
		}

		$result['status'] = $page_cache_detail['status'];

		switch ( $page_cache_detail['status'] ) {
			case 'recommended':
				$result['label'] = __( '未检测到页面缓存，但服务器响应时间正常' );
				break;
			case 'good':
				$result['label'] = __( '检测到页面缓存，并且服务器响应时间良好' );
				break;
			default:
				if ( empty( $page_cache_detail['headers'] ) && ! $page_cache_detail['advanced_cache_present'] ) {
					$result['label'] = __( '未检测到页面缓存，且服务器响应时间缓慢' );
				} else {
					$result['label'] = __( '检测到页面缓存，但服务器响应时间仍然缓慢' );
				}
		}

		$page_cache_test_summary = array();

		if ( empty( $page_cache_detail['response_time'] ) ) {
			$page_cache_test_summary[] = '<span class="dashicons dashicons-dismiss"></span> ' . __( '无法确定服务器响应时间。请确认环回请求是否正常工作。' );
		} else {

			$threshold = $this->get_good_response_time_threshold();
			if ( $page_cache_detail['response_time'] < $threshold ) {
				$page_cache_test_summary[] = '<span class="dashicons dashicons-yes-alt"></span> ' . sprintf(
					/* translators: 1: The response time in milliseconds, 2: The recommended threshold in milliseconds. */
					__( '服务器响应时间的中位数是 %1$s 毫秒，小于推荐的 %2$s 毫秒临界值。' ),
					number_format_i18n( $page_cache_detail['response_time'] ),
					number_format_i18n( $threshold )
				);
			} else {
				$page_cache_test_summary[] = '<span class="dashicons dashicons-warning"></span> ' . sprintf(
					/* translators: 1: The response time in milliseconds, 2: The recommended threshold in milliseconds. */
					__( '服务器响应时间的中位数是 %1$s 毫秒，其应当小于推荐的 %2$s 毫秒临界值。' ),
					number_format_i18n( $page_cache_detail['response_time'] ),
					number_format_i18n( $threshold )
				);
			}

			if ( empty( $page_cache_detail['headers'] ) ) {
				$page_cache_test_summary[] = '<span class="dashicons dashicons-warning"></span> ' . __( '未检测到用户端缓存响应标头。' );
			} else {
				$headers_summary  = '<span class="dashicons dashicons-yes-alt"></span>';
				$headers_summary .= ' ' . sprintf(
					/* translators: %d: Number of caching headers. */
					_n(
						'检测到 %d 个用户端缓存响应标头：',
						'检测到 %d 个用户端缓存响应标头：',
						count( $page_cache_detail['headers'] )
					),
					count( $page_cache_detail['headers'] )
				);
				$headers_summary          .= ' <code>' . implode( '</code>, <code>', $page_cache_detail['headers'] ) . '</code>.';
				$page_cache_test_summary[] = $headers_summary;
			}
		}

		if ( $page_cache_detail['advanced_cache_present'] ) {
			$page_cache_test_summary[] = '<span class="dashicons dashicons-yes-alt"></span> ' . __( '已检测到页面缓存插件。' );
		} elseif ( ! ( is_array( $page_cache_detail ) && ! empty( $page_cache_detail['headers'] ) ) ) {
			// Note: This message is not shown if client caching response headers were present since an external caching layer may be employed.
			$page_cache_test_summary[] = '<span class="dashicons dashicons-warning"></span> ' . __( '未检测到页面缓存插件。' );
		}

		$result['description'] .= '<ul><li>' . implode( '</li><li>', $page_cache_test_summary ) . '</li></ul>';
		return $result;
	}

	/**
	 * Tests if the site uses persistent object cache and recommends to use it if not.
	 *
	 * @since 6.1.0
	 *
	 * @return array The test result.
	 */
	public function get_test_persistent_object_cache() {
		/**
		 * Filters the action URL for the persistent object cache health check.
		 *
		 * @since 6.1.0
		 *
		 * @param string $action_url Learn more link for persistent object cache health check.
		 */
		$action_url = apply_filters(
			'site_status_persistent_object_cache_url',
			/* translators: Localized Support reference. */
			__( 'https://www.gechiui.com/support/optimization/#persistent-object-cache' )
		);

		$result = array(
			'test'        => 'persistent_object_cache',
			'status'      => 'good',
			'badge'       => array(
				'label' => __( '性能' ),
				'color' => 'blue',
			),
			'label'       => __( '正在使用一个持久对象缓存' ),
			'description' => sprintf(
				'<p>%s</p>',
				__( '持久对象存储可以提升您的系统数据库的执行效率，通过让 GeChiUI 更快地获取您系统的内容和设置以实现更短的加载时间。' )
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener">%s<span class="screen-reader-text"> %s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				esc_url( $action_url ),
				__( '了解有关持久对象缓存的更多信息。' ),
				/* translators: Hidden accessibility text. */
				__( '（在新窗口中打开）' )
			),
		);

		if ( gc_using_ext_object_cache() ) {
			return $result;
		}

		if ( ! $this->should_suggest_persistent_object_cache() ) {
			$result['label'] = __( '持久对象缓存不是必须的' );

			return $result;
		}

		$available_services = $this->available_object_cache_services();

		$notes = __( '您的主机提供商可以告诉您是否可以在您的系统上启用持久对象存储。' );

		if ( ! empty( $available_services ) ) {
			$notes .= ' ' . sprintf(
				/* translators: Available object caching services. */
				__( '您的主机似乎支持下列对象缓存服务：%s。' ),
				implode( ', ', $available_services )
			);
		}

		/**
		 * Filters the second paragraph of the health check's description
		 * when suggesting the use of a persistent object cache.
		 *
		 * Hosts may want to replace the notes to recommend their preferred object caching solution.
		 *
		 * Plugin authors may want to append notes (not replace) on why object caching is recommended for their plugin.
		 *
		 * @since 6.1.0
		 *
		 * @param string   $notes              The notes appended to the health check description.
		 * @param string[] $available_services The list of available persistent object cache services.
		 */
		$notes = apply_filters( 'site_status_persistent_object_cache_notes', $notes, $available_services );

		$result['status']       = 'recommended';
		$result['label']        = __( '您应该使用持久对象缓存' );
		$result['description'] .= sprintf(
			'<p>%s</p>',
			gc_kses(
				$notes,
				array(
					'a'      => array( 'href' => true ),
					'code'   => true,
					'em'     => true,
					'strong' => true,
				)
			)
		);

		return $result;
	}

	/**
	 * Returns a set of tests that belong to the site status page.
	 *
	 * Each site status test is defined here, they may be `direct` tests, that run on page load, or `async` tests
	 * which will run later down the line via JavaScript calls to improve page performance and hopefully also user
	 * experiences.
	 *
	 * @since 5.2.0
	 * @since 5.6.0 Added support for `has_rest` and `permissions`.
	 *
	 * @return array The list of tests to run.
	 */
	public static function get_tests() {
		$tests = array(
			'direct' => array(
				'gechiui_version'            => array(
					'label' => __( 'GeChiUI版本' ),
					'test'  => 'gechiui_version',
				),
				'plugin_version'               => array(
					'label' => __( '插件版本' ),
					'test'  => 'plugin_version',
				),
				'theme_version'                => array(
					'label' => __( '主题版本' ),
					'test'  => 'theme_version',
				),
				'php_version'                  => array(
					'label' => __( 'PHP版本' ),
					'test'  => 'php_version',
				),
				'php_extensions'               => array(
					'label' => __( 'PHP扩展' ),
					'test'  => 'php_extensions',
				),
				'php_default_timezone'         => array(
					'label' => __( 'PHP默认时区' ),
					'test'  => 'php_default_timezone',
				),
				'php_sessions'                 => array(
					'label' => __( 'PHP会话' ),
					'test'  => 'php_sessions',
				),
				'sql_server'                   => array(
					'label' => __( '数据库服务器版本' ),
					'test'  => 'sql_server',
				),
				'utf8mb4_support'              => array(
					'label' => __( 'MySQL utf8mb4支持' ),
					'test'  => 'utf8mb4_support',
				),
				'ssl_support'                  => array(
					'label' => __( '安全通信' ),
					'test'  => 'ssl_support',
				),
				'scheduled_events'             => array(
					'label' => __( '计划事件' ),
					'test'  => 'scheduled_events',
				),
				'http_requests'                => array(
					'label' => __( 'HTTP请求' ),
					'test'  => 'http_requests',
				),
				'rest_availability'            => array(
					'label'     => __( 'REST API可用性' ),
					'test'      => 'rest_availability',
					'skip_cron' => true,
				),
				'debug_enabled'                => array(
					'label' => __( '调试已启用' ),
					'test'  => 'is_in_debug_mode',
				),
				'file_uploads'                 => array(
					'label' => __( '文件上传' ),
					'test'  => 'file_uploads',
				),
				'plugin_theme_auto_updates'    => array(
					'label' => __( '插件和主题自动更新' ),
					'test'  => 'plugin_theme_auto_updates',
				),
				'update_temp_backup_writable'  => array(
					'label' => __( '插件和主题临时备份目录访问' ),
					'test'  => 'update_temp_backup_writable',
				),
				'available_updates_disk_space' => array(
					'label' => __( '可用磁盘空间' ),
					'test'  => 'available_updates_disk_space',
				),
			),
			'async'  => array(
				'dotorg_communication' => array(
					'label'             => __( '与www.GeChiUI.com通讯' ),
					'test'              => rest_url( 'gc-site-health/v1/tests/dotorg-communication' ),
					'has_rest'          => true,
					'async_direct_test' => array( GC_Site_Health::get_instance(), 'get_test_dotorg_communication' ),
				),
				'background_updates'   => array(
					'label'             => __( '后台更新' ),
					'test'              => rest_url( 'gc-site-health/v1/tests/background-updates' ),
					'has_rest'          => true,
					'async_direct_test' => array( GC_Site_Health::get_instance(), 'get_test_background_updates' ),
				),
				'loopback_requests'    => array(
					'label'             => __( '环回请求' ),
					'test'              => rest_url( 'gc-site-health/v1/tests/loopback-requests' ),
					'has_rest'          => true,
					'async_direct_test' => array( GC_Site_Health::get_instance(), 'get_test_loopback_requests' ),
				),
				'https_status'         => array(
					'label'             => __( 'HTTPS状态' ),
					'test'              => rest_url( 'gc-site-health/v1/tests/https-status' ),
					'has_rest'          => true,
					'async_direct_test' => array( GC_Site_Health::get_instance(), 'get_test_https_status' ),
				),
			),
		);

		// Conditionally include Authorization header test if the site isn't protected by Basic Auth.
		if ( ! gc_is_site_protected_by_basic_auth() ) {
			$tests['async']['authorization_header'] = array(
				'label'     => __( '授权标头' ),
				'test'      => rest_url( 'gc-site-health/v1/tests/authorization-header' ),
				'has_rest'  => true,
				'headers'   => array( 'Authorization' => 'Basic ' . base64_encode( 'user:pwd' ) ),
				'skip_cron' => true,
			);
		}

		// Only check for caches in production environments.
		if ( 'production' === gc_get_environment_type() ) {
			$tests['async']['page_cache'] = array(
				'label'             => __( '页面缓存' ),
				'test'              => rest_url( 'gc-site-health/v1/tests/page-cache' ),
				'has_rest'          => true,
				'async_direct_test' => array( GC_Site_Health::get_instance(), 'get_test_page_cache' ),
			);

			$tests['direct']['persistent_object_cache'] = array(
				'label' => __( '持久对象缓存' ),
				'test'  => 'persistent_object_cache',
			);
		}

		/**
		 * Filters which site status tests are run on a site.
		 *
		 * The site health is determined by a set of tests based on best practices from
		 * both the GeChiUI Hosting Team and web standards in general.
		 *
		 * Some sites may not have the same requirements, for example the automatic update
		 * checks may be handled by a host, and are therefore disabled in core.
		 * Or maybe you want to introduce a new test, is caching enabled/disabled/stale for example.
		 *
		 * Tests may be added either as direct, or asynchronous ones. Any test that may require some time
		 * to complete should run asynchronously, to avoid extended loading periods within gc-admin.
		 *
		 * @since 5.2.0
		 * @since 5.6.0 Added the `async_direct_test` array key for asynchronous tests.
		 *              Added the `skip_cron` array key for all tests.
		 *
		 * @param array[] $tests {
		 *     An associative array of direct and asynchronous tests.
		 *
		 *     @type array[] $direct {
		 *         An array of direct tests.
		 *
		 *         @type array ...$identifier {
		 *             `$identifier` should be a unique identifier for the test. Plugins and themes are encouraged to
		 *             prefix test identifiers with their slug to avoid collisions between tests.
		 *
		 *             @type string   $label     The friendly label to identify the test.
		 *             @type callable $test      The callback function that runs the test and returns its result.
		 *             @type bool     $skip_cron Whether to skip this test when running as cron.
		 *         }
		 *     }
		 *     @type array[] $async {
		 *         An array of asynchronous tests.
		 *
		 *         @type array ...$identifier {
		 *             `$identifier` should be a unique identifier for the test. Plugins and themes are encouraged to
		 *             prefix test identifiers with their slug to avoid collisions between tests.
		 *
		 *             @type string   $label             The friendly label to identify the test.
		 *             @type string   $test              An admin-ajax.php action to be called to perform the test, or
		 *                                               if `$has_rest` is true, a URL to a REST API endpoint to perform
		 *                                               the test.
		 *             @type bool     $has_rest          Whether the `$test` property points to a REST API endpoint.
		 *             @type bool     $skip_cron         Whether to skip this test when running as cron.
		 *             @type callable $async_direct_test A manner of directly calling the test marked as asynchronous,
		 *                                               as the scheduled event can not authenticate, and endpoints
		 *                                               may require authentication.
		 *         }
		 *     }
		 * }
		 */
		$tests = apply_filters( 'site_status_tests', $tests );

		// Ensure that the filtered tests contain the required array keys.
		$tests = array_merge(
			array(
				'direct' => array(),
				'async'  => array(),
			),
			$tests
		);

		return $tests;
	}

	/**
	 * Adds a class to the body HTML tag.
	 *
	 * Filters the body class string for admin pages and adds our own class for easier styling.
	 *
	 * @since 5.2.0
	 *
	 * @param string $body_class The body class string.
	 * @return string The modified body class string.
	 */
	public function admin_body_class( $body_class ) {
		$screen = get_current_screen();
		if ( 'site-health' !== $screen->id ) {
			return $body_class;
		}

		$body_class .= ' site-health';

		return $body_class;
	}

	/**
	 * Initiates the GC_Cron schedule test cases.
	 *
	 * @since 5.2.0
	 */
	private function gc_schedule_test_init() {
		$this->schedules = gc_get_schedules();
		$this->get_cron_tasks();
	}

	/**
	 * Populates the list of cron events and store them to a class-wide variable.
	 *
	 * @since 5.2.0
	 */
	private function get_cron_tasks() {
		$cron_tasks = _get_cron_array();

		if ( empty( $cron_tasks ) ) {
			$this->crons = new GC_Error( 'no_tasks', __( '此系统不存在计划事件。' ) );
			return;
		}

		$this->crons = array();

		foreach ( $cron_tasks as $time => $cron ) {
			foreach ( $cron as $hook => $dings ) {
				foreach ( $dings as $sig => $data ) {

					$this->crons[ "$hook-$sig-$time" ] = (object) array(
						'hook'     => $hook,
						'time'     => $time,
						'sig'      => $sig,
						'args'     => $data['args'],
						'schedule' => $data['schedule'],
						'interval' => isset( $data['interval'] ) ? $data['interval'] : null,
					);

				}
			}
		}
	}

	/**
	 * Checks if any scheduled tasks have been missed.
	 *
	 * Returns a boolean value of `true` if a scheduled task has been missed and ends processing.
	 *
	 * If the list of crons is an instance of GC_Error, returns the instance instead of a boolean value.
	 *
	 * @since 5.2.0
	 *
	 * @return bool|GC_Error True if a cron was missed, false if not. GC_Error if the cron is set to that.
	 */
	public function has_missed_cron() {
		if ( is_gc_error( $this->crons ) ) {
			return $this->crons;
		}

		foreach ( $this->crons as $id => $cron ) {
			if ( ( $cron->time - time() ) < $this->timeout_missed_cron ) {
				$this->last_missed_cron = $cron->hook;
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if any scheduled tasks are late.
	 *
	 * Returns a boolean value of `true` if a scheduled task is late and ends processing.
	 *
	 * If the list of crons is an instance of GC_Error, returns the instance instead of a boolean value.
	 *
	 * @since 5.3.0
	 *
	 * @return bool|GC_Error True if a cron is late, false if not. GC_Error if the cron is set to that.
	 */
	public function has_late_cron() {
		if ( is_gc_error( $this->crons ) ) {
			return $this->crons;
		}

		foreach ( $this->crons as $id => $cron ) {
			$cron_offset = $cron->time - time();
			if (
				$cron_offset >= $this->timeout_missed_cron &&
				$cron_offset < $this->timeout_late_cron
			) {
				$this->last_late_cron = $cron->hook;
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks for potential issues with plugin and theme auto-updates.
	 *
	 * Though there is no way to 100% determine if plugin and theme auto-updates are configured
	 * correctly, a few educated guesses could be made to flag any conditions that would
	 * potentially cause unexpected behaviors.
	 *
	 * @since 5.5.0
	 *
	 * @return object The test results.
	 */
	public function detect_plugin_theme_auto_update_issues() {
		$mock_plugin = (object) array(
			'id'            => 'w.org/plugins/a-fake-plugin',
			'slug'          => 'a-fake-plugin',
			'plugin'        => 'a-fake-plugin/a-fake-plugin.php',
			'new_version'   => '9.9',
			'url'           => 'https://www.gechiui.com/plugins/a-fake-plugin/',
			'package'       => 'https://downloads.gechiui.com/plugin/a-fake-plugin.9.9.zip',
			'icons'         => array(
				'2x' => 'https://ps.w.org/a-fake-plugin/assets/icon-256x256.png',
				'1x' => 'https://ps.w.org/a-fake-plugin/assets/icon-128x128.png',
			),
			'banners'       => array(
				'2x' => 'https://ps.w.org/a-fake-plugin/assets/banner-1544x500.png',
				'1x' => 'https://ps.w.org/a-fake-plugin/assets/banner-772x250.png',
			),
			'banners_rtl'   => array(),
			'tested'        => '5.5.0',
			'requires_php'  => '5.6.20',
			'compatibility' => new stdClass(),
		);

		$mock_theme = (object) array(
			'theme'        => 'a-fake-theme',
			'new_version'  => '9.9',
			'url'          => 'https://www.gechiui.com/themes/a-fake-theme/',
			'package'      => 'https://downloads.gechiui.com/theme/a-fake-theme.9.9.zip',
			'requires'     => '5.0.0',
			'requires_php' => '5.6.20',
		);

		$test_plugins_enabled = gc_is_auto_update_forced_for_item( 'plugin', true, $mock_plugin );
		$test_themes_enabled  = gc_is_auto_update_forced_for_item( 'theme', true, $mock_theme );

		$ui_enabled_for_plugins = gc_is_auto_update_enabled_for_type( 'plugin' );
		$ui_enabled_for_themes  = gc_is_auto_update_enabled_for_type( 'theme' );
		$plugin_filter_present  = has_filter( 'auto_update_plugin' );
		$theme_filter_present   = has_filter( 'auto_update_theme' );

		if ( ( ! $test_plugins_enabled && $ui_enabled_for_plugins )
			|| ( ! $test_themes_enabled && $ui_enabled_for_themes )
		) {
			return (object) array(
				'status'  => 'critical',
				'message' => __( '插件和（或）主题的自动更新似乎已被禁用，但是界面依然被设置为显示更新。这可能导致自动更新无法如期进行。' ),
			);
		}

		if ( ( ! $test_plugins_enabled && $plugin_filter_present )
			&& ( ! $test_themes_enabled && $theme_filter_present )
		) {
			return (object) array(
				'status'  => 'recommended',
				'message' => __( '插件和主题的自动更新似乎被禁用。这将阻止您的系统在有可用更新时自动接收新版本。' ),
			);
		} elseif ( ! $test_plugins_enabled && $plugin_filter_present ) {
			return (object) array(
				'status'  => 'recommended',
				'message' => __( '插件的自动更新似乎被禁用。这将阻止您的系统在有可用更新时自动接收新版本。' ),
			);
		} elseif ( ! $test_themes_enabled && $theme_filter_present ) {
			return (object) array(
				'status'  => 'recommended',
				'message' => __( '主题的自动更新似乎被禁用。这将阻止您的系统在有可用更新时自动接收新版本。' ),
			);
		}

		return (object) array(
			'status'  => 'good',
			'message' => __( '插件和主题自动更新一切如常。' ),
		);
	}

	/**
	 * Runs a loopback test on the site.
	 *
	 * Loopbacks are what GeChiUI uses to communicate with itself to start up GC_Cron, scheduled posts,
	 * make sure plugin or theme edits don't cause site failures and similar.
	 *
	 * @since 5.2.0
	 *
	 * @return object The test results.
	 */
	public function can_perform_loopback() {
		$body    = array( 'site-health' => 'loopback-test' );
		$cookies = gc_unslash( $_COOKIE );
		$timeout = 10; // 10 seconds.
		$headers = array(
			'Cache-Control' => 'no-cache',
		);
		/** This filter is documented in gc-includes/class-gc-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		// Include Basic auth in loopback requests.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( gc_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . gc_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		$url = site_url( 'gc-cron.php' );

		/*
		 * A post request is used for the gc-cron.php loopback test to cause the file
		 * to finish early without triggering cron jobs. This has two benefits:
		 * - cron jobs are not triggered a second time on the site health page,
		 * - the loopback request finishes sooner providing a quicker result.
		 *
		 * Using a POST request causes the loopback to differ slightly to the standard
		 * GET request GeChiUI uses for gc-cron.php loopback requests but is close
		 * enough. See https://core.trac.gechiui.com/ticket/52547
		 */
		$r = gc_remote_post( $url, compact( 'body', 'cookies', 'headers', 'timeout', 'sslverify' ) );

		if ( is_gc_error( $r ) ) {
			return (object) array(
				'status'  => 'critical',
				'message' => sprintf(
					'%s<br>%s',
					__( '到您系统的环回请求失败，这意味着依赖此种请求的功能将不能正常工作。' ),
					sprintf(
						/* translators: 1: The GeChiUI error message. 2: The GeChiUI error code. */
						__( '错误：%1$s（%2$s）' ),
						$r->get_error_message(),
						$r->get_error_code()
					)
				),
			);
		}

		if ( 200 !== gc_remote_retrieve_response_code( $r ) ) {
			return (object) array(
				'status'  => 'recommended',
				'message' => sprintf(
					/* translators: %d: The HTTP response code returned. */
					__( '到您系统的环回请求返回了预期外的HTTP状态码%d，无法判断依赖此种请求的功能是否能正常工作。' ),
					gc_remote_retrieve_response_code( $r )
				),
			);
		}

		return (object) array(
			'status'  => 'good',
			'message' => __( '到您系统的环回请求成功完成。' ),
		);
	}

	/**
	 * Creates a weekly cron event, if one does not already exist.
	 *
	 * @since 5.4.0
	 */
	public function maybe_create_scheduled_event() {
		if ( ! gc_next_scheduled( 'gc_site_health_scheduled_check' ) && ! gc_installing() ) {
			gc_schedule_event( time() + DAY_IN_SECONDS, 'weekly', 'gc_site_health_scheduled_check' );
		}
	}

	/**
	 * Runs the scheduled event to check and update the latest site health status for the website.
	 *
	 * @since 5.4.0
	 */
	public function gc_cron_scheduled_check() {
		// Bootstrap gc-admin, as GC_Cron doesn't do this for us.
		require_once trailingslashit( ABSPATH ) . 'gc-admin/includes/admin.php';

		$tests = GC_Site_Health::get_tests();

		$results = array();

		$site_status = array(
			'good'        => 0,
			'recommended' => 0,
			'critical'    => 0,
		);

		// Don't run https test on development environments.
		if ( $this->is_development_environment() ) {
			unset( $tests['async']['https_status'] );
		}

		foreach ( $tests['direct'] as $test ) {
			if ( ! empty( $test['skip_cron'] ) ) {
				continue;
			}

			if ( is_string( $test['test'] ) ) {
				$test_function = sprintf(
					'get_test_%s',
					$test['test']
				);

				if ( method_exists( $this, $test_function ) && is_callable( array( $this, $test_function ) ) ) {
					$results[] = $this->perform_test( array( $this, $test_function ) );
					continue;
				}
			}

			if ( is_callable( $test['test'] ) ) {
				$results[] = $this->perform_test( $test['test'] );
			}
		}

		foreach ( $tests['async'] as $test ) {
			if ( ! empty( $test['skip_cron'] ) ) {
				continue;
			}

			// Local endpoints may require authentication, so asynchronous tests can pass a direct test runner as well.
			if ( ! empty( $test['async_direct_test'] ) && is_callable( $test['async_direct_test'] ) ) {
				// This test is callable, do so and continue to the next asynchronous check.
				$results[] = $this->perform_test( $test['async_direct_test'] );
				continue;
			}

			if ( is_string( $test['test'] ) ) {
				// Check if this test has a REST API endpoint.
				if ( isset( $test['has_rest'] ) && $test['has_rest'] ) {
					$result_fetch = gc_remote_get(
						$test['test'],
						array(
							'body' => array(
								'_gcnonce' => gc_create_nonce( 'gc_rest' ),
							),
						)
					);
				} else {
					$result_fetch = gc_remote_post(
						admin_url( 'admin-ajax.php' ),
						array(
							'body' => array(
								'action'   => $test['test'],
								'_gcnonce' => gc_create_nonce( 'health-check-site-status' ),
							),
						)
					);
				}

				if ( ! is_gc_error( $result_fetch ) && 200 === gc_remote_retrieve_response_code( $result_fetch ) ) {
					$result = json_decode( gc_remote_retrieve_body( $result_fetch ), true );
				} else {
					$result = false;
				}

				if ( is_array( $result ) ) {
					$results[] = $result;
				} else {
					$results[] = array(
						'status' => 'recommended',
						'label'  => __( '测试不可用' ),
					);
				}
			}
		}

		foreach ( $results as $result ) {
			if ( 'critical' === $result['status'] ) {
				$site_status['critical']++;
			} elseif ( 'recommended' === $result['status'] ) {
				$site_status['recommended']++;
			} else {
				$site_status['good']++;
			}
		}

		set_transient( 'health-check-site-status-result', gc_json_encode( $site_status ) );
	}

	/**
	 * Checks if the current environment type is set to 'development' or 'local'.
	 *
	 * @since 5.6.0
	 *
	 * @return bool True if it is a development environment, false if not.
	 */
	public function is_development_environment() {
		return in_array( gc_get_environment_type(), array( 'development', 'local' ), true );
	}

	/**
	 * Returns a list of headers and its verification callback to verify if page cache is enabled or not.
	 *
	 * Note: key is header name and value could be callable function to verify header value.
	 * Empty value mean existence of header detect page cache is enabled.
	 *
	 * @since 6.1.0
	 *
	 * @return array List of client caching headers and their (optional) verification callbacks.
	 */
	public function get_page_cache_headers() {

		$cache_hit_callback = static function ( $header_value ) {
			return str_contains( strtolower( $header_value ), 'hit' );
		};

		$cache_headers = array(
			'cache-control'          => static function ( $header_value ) {
				return (bool) preg_match( '/max-age=[1-9]/', $header_value );
			},
			'expires'                => static function ( $header_value ) {
				return strtotime( $header_value ) > time();
			},
			'age'                    => static function ( $header_value ) {
				return is_numeric( $header_value ) && $header_value > 0;
			},
			'last-modified'          => '',
			'etag'                   => '',
			'x-cache-enabled'        => static function ( $header_value ) {
				return 'true' === strtolower( $header_value );
			},
			'x-cache-disabled'       => static function ( $header_value ) {
				return ( 'on' !== strtolower( $header_value ) );
			},
			'x-srcache-store-status' => $cache_hit_callback,
			'x-srcache-fetch-status' => $cache_hit_callback,
		);

		/**
		 * Filters the list of cache headers supported by core.
		 *
		 * @since 6.1.0
		 *
		 * @param array $cache_headers Array of supported cache headers.
		 */
		return apply_filters( 'site_status_page_cache_supported_cache_headers', $cache_headers );
	}

	/**
	 * Checks if site has page cache enabled or not.
	 *
	 * @since 6.1.0
	 *
	 * @return GC_Error|array {
	 *     Page cache detection details or else error information.
	 *
	 *     @type bool    $advanced_cache_present        Whether a page cache plugin is present.
	 *     @type array[] $page_caching_response_headers Sets of client caching headers for the responses.
	 *     @type float[] $response_timing               Response timings.
	 * }
	 */
	private function check_for_page_caching() {

		/** This filter is documented in gc-includes/class-gc-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		$headers = array();

		/*
		 * Include basic auth in loopback requests. Note that this will only pass along basic auth when user is
		 * initiating the test. If a site requires basic auth, the test will fail when it runs in GC Cron as part of
		 * gc_site_health_scheduled_check. This logic is copied from GC_Site_Health::can_perform_loopback().
		 */
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( gc_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . gc_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		$caching_headers               = $this->get_page_cache_headers();
		$page_caching_response_headers = array();
		$response_timing               = array();

		for ( $i = 1; $i <= 3; $i++ ) {
			$start_time    = microtime( true );
			$http_response = gc_remote_get( home_url( '/' ), compact( 'sslverify', 'headers' ) );
			$end_time      = microtime( true );

			if ( is_gc_error( $http_response ) ) {
				return $http_response;
			}
			if ( gc_remote_retrieve_response_code( $http_response ) !== 200 ) {
				return new GC_Error(
					'http_' . gc_remote_retrieve_response_code( $http_response ),
					gc_remote_retrieve_response_message( $http_response )
				);
			}

			$response_headers = array();

			foreach ( $caching_headers as $header => $callback ) {
				$header_values = gc_remote_retrieve_header( $http_response, $header );
				if ( empty( $header_values ) ) {
					continue;
				}
				$header_values = (array) $header_values;
				if ( empty( $callback ) || ( is_callable( $callback ) && count( array_filter( $header_values, $callback ) ) > 0 ) ) {
					$response_headers[ $header ] = $header_values;
				}
			}

			$page_caching_response_headers[] = $response_headers;
			$response_timing[]               = ( $end_time - $start_time ) * 1000;
		}

		return array(
			'advanced_cache_present'        => (
				file_exists( GC_CONTENT_DIR . '/advanced-cache.php' )
				&&
				( defined( 'GC_CACHE' ) && GC_CACHE )
				&&
				/** This filter is documented in gc-settings.php */
				apply_filters( 'enable_loading_advanced_cache_dropin', true )
			),
			'page_caching_response_headers' => $page_caching_response_headers,
			'response_timing'               => $response_timing,
		);
	}

	/**
	 * Gets page cache details.
	 *
	 * @since 6.1.0
	 *
	 * @return GC_Error|array {
	 *    Page cache detail or else a GC_Error if unable to determine.
	 *
	 *    @type string   $status                 Page cache status. Good, Recommended or Critical.
	 *    @type bool     $advanced_cache_present Whether page cache plugin is available or not.
	 *    @type string[] $headers                Client caching response headers detected.
	 *    @type float    $response_time          Response time of site.
	 * }
	 */
	private function get_page_cache_detail() {
		$page_cache_detail = $this->check_for_page_caching();
		if ( is_gc_error( $page_cache_detail ) ) {
			return $page_cache_detail;
		}

		// Use the median server response time.
		$response_timings = $page_cache_detail['response_timing'];
		rsort( $response_timings );
		$page_speed = $response_timings[ floor( count( $response_timings ) / 2 ) ];

		// Obtain unique set of all client caching response headers.
		$headers = array();
		foreach ( $page_cache_detail['page_caching_response_headers'] as $page_caching_response_headers ) {
			$headers = array_merge( $headers, array_keys( $page_caching_response_headers ) );
		}
		$headers = array_unique( $headers );

		// Page cache is detected if there are response headers or a page cache plugin is present.
		$has_page_caching = ( count( $headers ) > 0 || $page_cache_detail['advanced_cache_present'] );

		if ( $page_speed && $page_speed < $this->get_good_response_time_threshold() ) {
			$result = $has_page_caching ? 'good' : 'recommended';
		} else {
			$result = 'critical';
		}

		return array(
			'status'                 => $result,
			'advanced_cache_present' => $page_cache_detail['advanced_cache_present'],
			'headers'                => $headers,
			'response_time'          => $page_speed,
		);
	}

	/**
	 * Gets the threshold below which a response time is considered good.
	 *
	 * @since 6.1.0
	 *
	 * @return int Threshold in milliseconds.
	 */
	private function get_good_response_time_threshold() {
		/**
		 * Filters the threshold below which a response time is considered good.
		 *
		 * The default is based on https://web.dev/time-to-first-byte/.
		 *
		 * @param int $threshold Threshold in milliseconds. Default 600.
		 *
		 * @since 6.1.0
		 */
		return (int) apply_filters( 'site_status_good_response_time_threshold', 600 );
	}

	/**
	 * Determines whether to suggest using a persistent object cache.
	 *
	 * @since 6.1.0
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @return bool Whether to suggest using a persistent object cache.
	 */
	public function should_suggest_persistent_object_cache() {
		global $gcdb;

		/**
		 * Filters whether to suggest use of a persistent object cache and bypass default threshold checks.
		 *
		 * Using this filter allows to override the default logic, effectively short-circuiting the method.
		 *
		 * @since 6.1.0
		 *
		 * @param bool|null $suggest Boolean to short-circuit, for whether to suggest using a persistent object cache.
		 *                           Default null.
		 */
		$short_circuit = apply_filters( 'site_status_should_suggest_persistent_object_cache', null );
		if ( is_bool( $short_circuit ) ) {
			return $short_circuit;
		}

		if ( is_multisite() ) {
			return true;
		}

		/**
		 * Filters the thresholds used to determine whether to suggest the use of a persistent object cache.
		 *
		 * @since 6.1.0
		 *
		 * @param int[] $thresholds The list of threshold numbers keyed by threshold name.
		 */
		$thresholds = apply_filters(
			'site_status_persistent_object_cache_thresholds',
			array(
				'alloptions_count' => 500,
				'alloptions_bytes' => 100000,
				'comments_count'   => 1000,
				'options_count'    => 1000,
				'posts_count'      => 1000,
				'terms_count'      => 1000,
				'users_count'      => 1000,
			)
		);

		$alloptions = gc_load_alloptions();

		if ( $thresholds['alloptions_count'] < count( $alloptions ) ) {
			return true;
		}

		if ( $thresholds['alloptions_bytes'] < strlen( serialize( $alloptions ) ) ) {
			return true;
		}

		$table_names = implode( "','", array( $gcdb->comments, $gcdb->options, $gcdb->posts, $gcdb->terms, $gcdb->users ) );

		// With InnoDB the `TABLE_ROWS` are estimates, which are accurate enough and faster to retrieve than individual `COUNT()` queries.
		$results = $gcdb->get_results(
			$gcdb->prepare(
				// phpcs:ignore GeChiUI.DB.PreparedSQL.InterpolatedNotPrepared -- This query cannot use interpolation.
				"SELECT TABLE_NAME AS 'table', TABLE_ROWS AS 'rows', SUM(data_length + index_length) as 'bytes' FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME IN ('$table_names') GROUP BY TABLE_NAME;",
				DB_NAME
			),
			OBJECT_K
		);

		$threshold_map = array(
			'comments_count' => $gcdb->comments,
			'options_count'  => $gcdb->options,
			'posts_count'    => $gcdb->posts,
			'terms_count'    => $gcdb->terms,
			'users_count'    => $gcdb->users,
		);

		foreach ( $threshold_map as $threshold => $table ) {
			if ( $thresholds[ $threshold ] <= $results[ $table ]->rows ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a list of available persistent object cache services.
	 *
	 * @since 6.1.0
	 *
	 * @return string[] The list of available persistent object cache services.
	 */
	private function available_object_cache_services() {
		$extensions = array_map(
			'extension_loaded',
			array(
				'APCu'      => 'apcu',
				'Redis'     => 'redis',
				'Relay'     => 'relay',
				'Memcache'  => 'memcache',
				'Memcached' => 'memcached',
			)
		);

		$services = array_keys( array_filter( $extensions ) );

		/**
		 * Filters the persistent object cache services available to the user.
		 *
		 * This can be useful to hide or add services not included in the defaults.
		 *
		 * @since 6.1.0
		 *
		 * @param string[] $services The list of available persistent object cache services.
		 */
		return apply_filters( 'site_status_available_object_cache_services', $services );
	}

}
