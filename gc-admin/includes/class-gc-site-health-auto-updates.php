<?php
/**
 * Class for testing automatic updates in the GeChiUI code.
 *
 * @package GeChiUI
 * @subpackage Site_Health
 *
 */

class GC_Site_Health_Auto_Updates {
	/**
	 * GC_Site_Health_Auto_Updates constructor.
	 *
	 */
	public function __construct() {
		require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	}


	/**
	 * Run tests to determine if auto-updates can run.
	 *
	 *
	 * @return array The test results.
	 */
	public function run_tests() {
		$tests = array(
			$this->test_constants( 'GC_AUTO_UPDATE_CORE', array( true, 'beta', 'rc', 'development', 'branch-development', 'minor' ) ),
			$this->test_gc_version_check_attached(),
			$this->test_filters_automatic_updater_disabled(),
			$this->test_gc_automatic_updates_disabled(),
			$this->test_if_failed_update(),
			$this->test_vcs_abspath(),
			$this->test_check_gc_filesystem_method(),
			$this->test_all_files_writable(),
			$this->test_accepts_dev_updates(),
			$this->test_accepts_minor_updates(),
		);

		$tests = array_filter( $tests );
		$tests = array_map(
			static function( $test ) {
				$test = (object) $test;

				if ( empty( $test->severity ) ) {
					$test->severity = 'warning';
				}

				return $test;
			},
			$tests
		);

		return $tests;
	}

	/**
	 * Test if auto-updates related constants are set correctly.
	 *
	 *
	 * @param string $constant         The name of the constant to check.
	 * @param bool|string|array $value The value that the constant should be, if set,
	 *                                 or an array of acceptable values.
	 * @return array The test results.
	 */
	public function test_constants( $constant, $value ) {
		$acceptable_values = (array) $value;

		if ( defined( $constant ) && ! in_array( constant( $constant ), $acceptable_values, true ) ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the constant used. */
					__( '%s常量已被定义及启用。' ),
					"<code>$constant</code>"
				),
				'severity'    => 'fail',
			);
		}
	}

	/**
	 * Check if updates are intercepted by a filter.
	 *
	 *
	 * @return array The test results.
	 */
	public function test_gc_version_check_attached() {
		if ( ( ! is_multisite() || is_main_site() && is_network_admin() )
			&& ! has_filter( 'gc_version_check', 'gc_version_check' )
		) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the filter used. */
					__( '一个插件已通过禁用%s阻止了更新。' ),
					'<code>gc_version_check()</code>'
				),
				'severity'    => 'fail',
			);
		}
	}

	/**
	 * Check if automatic updates are disabled by a filter.
	 *
	 *
	 * @return array The test results.
	 */
	public function test_filters_automatic_updater_disabled() {
		/** This filter is documented in gc-admin/includes/class-gc-automatic-updater.php */
		if ( apply_filters( 'automatic_updater_disabled', false ) ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the filter used. */
					__( '%s过滤器已启用。' ),
					'<code>automatic_updater_disabled</code>'
				),
				'severity'    => 'fail',
			);
		}
	}

	/**
	 * Check if automatic updates are disabled.
	 *
	 *
	 * @return array|false The test results. False if auto-updates are enabled.
	 */
	public function test_gc_automatic_updates_disabled() {
		if ( ! class_exists( 'GC_Automatic_Updater' ) ) {
			require_once ABSPATH . 'gc-admin/includes/class-gc-automatic-updater.php';
		}

		$auto_updates = new GC_Automatic_Updater();

		if ( ! $auto_updates->is_disabled() ) {
			return false;
		}

		return array(
			'description' => __( '所有自动更新都已禁用。' ),
			'severity'    => 'fail',
		);
	}

	/**
	 * Check if automatic updates have tried to run, but failed, previously.
	 *
	 *
	 * @return array|false The test results. False if the auto-updates failed.
	 */
	public function test_if_failed_update() {
		$failed = get_site_option( 'auto_core_update_failed' );

		if ( ! $failed ) {
			return false;
		}

		if ( ! empty( $failed['critical'] ) ) {
			$description  = __( '较早前的后台更新遇到了致命错误，更新现已被禁用。' );
			$description .= ' ' . __( '因此，您会收到一封邮件。' );
			$description .= ' ' . __( "当您能在仪表盘→更新中点击“立即更新”按钮来更新时，我们将清除此错误，以便将来尝试更新。" );
			$description .= ' ' . sprintf(
				/* translators: %s: Code of error shown. */
				__( '错误代码为%s。' ),
				'<code>' . $failed['error_code'] . '</code>'
			);
			return array(
				'description' => $description,
				'severity'    => 'warning',
			);
		}

		$description = __( '较早前的自动后台更新未能发生。' );
		if ( empty( $failed['retry'] ) ) {
			$description .= ' ' . __( '因此，您会收到一封邮件。' );
		}

		$description .= ' ' . __( "我们将在下一次发布后重试。" );
		$description .= ' ' . sprintf(
			/* translators: %s: Code of error shown. */
			__( '错误代码为%s。' ),
			'<code>' . $failed['error_code'] . '</code>'
		);
		return array(
			'description' => $description,
			'severity'    => 'warning',
		);
	}

	/**
	 * Check if GeChiUI is controlled by a VCS (Git, Subversion etc).
	 *
	 *
	 * @return array The test results.
	 */
	public function test_vcs_abspath() {
		$context_dirs = array( ABSPATH );
		$vcs_dirs     = array( '.svn', '.git', '.hg', '.bzr' );
		$check_dirs   = array();

		foreach ( $context_dirs as $context_dir ) {
			// Walk up from $context_dir to the root.
			do {
				$check_dirs[] = $context_dir;

				// Once we've hit '/' or 'C:\', we need to stop. dirname will keep returning the input here.
				if ( dirname( $context_dir ) === $context_dir ) {
					break;
				}

				// Continue one level at a time.
			} while ( $context_dir = dirname( $context_dir ) );
		}

		$check_dirs = array_unique( $check_dirs );

		// Search all directories we've found for evidence of version control.
		foreach ( $vcs_dirs as $vcs_dir ) {
			foreach ( $check_dirs as $check_dir ) {
				// phpcs:ignore GeChiUI.CodeAnalysis.AssignmentInCondition,Squiz.PHP.DisallowMultipleAssignments
				if ( $checkout = @is_dir( rtrim( $check_dir, '\\/' ) . "/$vcs_dir" ) ) {
					break 2;
				}
			}
		}

		/** This filter is documented in gc-admin/includes/class-gc-automatic-updater.php */
		if ( $checkout && ! apply_filters( 'automatic_updates_is_vcs_checkout', true, ABSPATH ) ) {
			return array(
				'description' => sprintf(
					/* translators: 1: Folder name. 2: Version control directory. 3: Filter name. */
					__( '检测到目录%1$s受到版本控制（%2$s），但%3$s允许了更新。' ),
					'<code>' . $check_dir . '</code>',
					"<code>$vcs_dir</code>",
					'<code>automatic_updates_is_vcs_checkout</code>'
				),
				'severity'    => 'info',
			);
		}

		if ( $checkout ) {
			return array(
				'description' => sprintf(
					/* translators: 1: Folder name. 2: Version control directory. */
					__( '检测到目录%1$s受到版本控制（%2$s）。' ),
					'<code>' . $check_dir . '</code>',
					"<code>$vcs_dir</code>"
				),
				'severity'    => 'warning',
			);
		}

		return array(
			'description' => __( '没有检测到版本控制系统。' ),
			'severity'    => 'pass',
		);
	}

	/**
	 * Check if we can access files without providing credentials.
	 *
	 *
	 * @return array The test results.
	 */
	public function test_check_gc_filesystem_method() {
		// Make sure the `request_filesystem_credentials()` function is available during our REST API call.
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . '/gc-admin/includes/file.php';
		}

		$skin    = new Automatic_Upgrader_Skin;
		$success = $skin->request_filesystem_credentials( false, ABSPATH );

		if ( ! $success ) {
			$description  = __( '您的GeChiUI安装需要FTP凭据来进行更新。' );
			$description .= ' ' . __( '（由于文件所有权，您的站点现在通过FTP进行更新。请与您的主机提供商联系。）' );

			return array(
				'description' => $description,
				'severity'    => 'fail',
			);
		}

		return array(
			'description' => __( "您的GeChiUI更新时不需要进行FTP认证。" ),
			'severity'    => 'pass',
		);
	}

	/**
	 * Check if core files are writable by the web user/group.
	 *
	 *
	 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
	 *
	 * @return array|false The test results. False if they're not writeable.
	 */
	public function test_all_files_writable() {
		global $gc_filesystem;

		require ABSPATH . GCINC . '/version.php'; // $gc_version; // x.y.z

		$skin    = new Automatic_Upgrader_Skin;
		$success = $skin->request_filesystem_credentials( false, ABSPATH );

		if ( ! $success ) {
			return false;
		}

		GC_Filesystem();

		if ( 'direct' !== $gc_filesystem->method ) {
			return false;
		}

		// Make sure the `get_core_checksums()` function is available during our REST API call.
		if ( ! function_exists( 'get_core_checksums' ) ) {
			require_once ABSPATH . '/gc-admin/includes/update.php';
		}

		$checksums = get_core_checksums( $gc_version, 'zh_CN' );
		$dev       = ( false !== strpos( $gc_version, '-' ) );
		// Get the last stable version's files and test against that.
		if ( ! $checksums && $dev ) {
			$checksums = get_core_checksums( (float) $gc_version - 0.1, 'zh_CN' );
		}

		// There aren't always checksums for development releases, so just skip the test if we still can't find any.
		if ( ! $checksums && $dev ) {
			return false;
		}

		if ( ! $checksums ) {
			$description = sprintf(
				/* translators: %s: GeChiUI version. */
				__( "未能获取GeChiUI %s的校验和列表。" ),
				$gc_version
			);
			$description .= ' ' . __( '这可能意味着到www.GeChiUI.com的连接失败。' );
			return array(
				'description' => $description,
				'severity'    => 'warning',
			);
		}

		$unwritable_files = array();
		foreach ( array_keys( $checksums ) as $file ) {
			if ( 'gc-content' === substr( $file, 0, 10 ) ) {
				continue;
			}
			if ( ! file_exists( ABSPATH . $file ) ) {
				continue;
			}
			if ( ! is_writable( ABSPATH . $file ) ) {
				$unwritable_files[] = $file;
			}
		}

		if ( $unwritable_files ) {
			if ( count( $unwritable_files ) > 20 ) {
				$unwritable_files   = array_slice( $unwritable_files, 0, 20 );
				$unwritable_files[] = '...';
			}
			return array(
				'description' => __( '一些文件不能被GeChiUI写入：' ) . ' <ul><li>' . implode( '</li><li>', $unwritable_files ) . '</li></ul>',
				'severity'    => 'fail',
			);
		} else {
			return array(
				'description' => __( '所有GeChiUI文件都可写。' ),
				'severity'    => 'pass',
			);
		}
	}

	/**
	 * Check if the install is using a development branch and can use nightly packages.
	 *
	 *
	 * @return array|false The test results. False if it isn't a development version.
	 */
	public function test_accepts_dev_updates() {
		require ABSPATH . GCINC . '/version.php'; // $gc_version; // x.y.z
		// Only for dev versions.
		if ( false === strpos( $gc_version, '-' ) ) {
			return false;
		}

		if ( defined( 'GC_AUTO_UPDATE_CORE' ) && ( 'minor' === GC_AUTO_UPDATE_CORE || false === GC_AUTO_UPDATE_CORE ) ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the constant used. */
					__( 'GeChiUI开发更新被%s常量阻止。' ),
					'<code>GC_AUTO_UPDATE_CORE</code>'
				),
				'severity'    => 'fail',
			);
		}

		/** This filter is documented in gc-admin/includes/class-core-upgrader.php */
		if ( ! apply_filters( 'allow_dev_auto_core_updates', $gc_version ) ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the filter used. */
					__( 'GeChiUI开发更新被%s过滤器阻止。' ),
					'<code>allow_dev_auto_core_updates</code>'
				),
				'severity'    => 'fail',
			);
		}
	}

	/**
	 * Check if the site supports automatic minor updates.
	 *
	 *
	 * @return array The test results.
	 */
	public function test_accepts_minor_updates() {
		if ( defined( 'GC_AUTO_UPDATE_CORE' ) && false === GC_AUTO_UPDATE_CORE ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the constant used. */
					__( 'GeChiUI安全和维护更新被%s阻止。' ),
					"<code>define( 'GC_AUTO_UPDATE_CORE', false );</code>"
				),
				'severity'    => 'fail',
			);
		}

		/** This filter is documented in gc-admin/includes/class-core-upgrader.php */
		if ( ! apply_filters( 'allow_minor_auto_core_updates', true ) ) {
			return array(
				'description' => sprintf(
					/* translators: %s: Name of the filter used. */
					__( 'GeChiUI安全和维护更新被%s过滤器阻止。' ),
					'<code>allow_minor_auto_core_updates</code>'
				),
				'severity'    => 'fail',
			);
		}
	}
}
