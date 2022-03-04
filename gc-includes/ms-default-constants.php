<?php
/**
 * Defines constants and global variables that can be overridden, generally in gc-config.php.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/**
 * Defines Multisite upload constants.
 *
 * Exists for backward compatibility with legacy file-serving through
 * gc-includes/ms-files.php (gc-content/blogs.php in MU).
 *
 *
 */
function ms_upload_constants() {
	// This filter is attached in ms-default-filters.php but that file is not included during SHORTINIT.
	add_filter( 'default_site_option_ms_files_rewriting', '__return_true' );

	if ( ! get_site_option( 'ms_files_rewriting' ) ) {
		return;
	}

	// Base uploads dir relative to ABSPATH.
	if ( ! defined( 'UPLOADBLOGSDIR' ) ) {
		define( 'UPLOADBLOGSDIR', 'gc-content/blogs.dir' );
	}

	// Note, the main site in a post-MU network uses gc-content/uploads.
	// This is handled in gc_upload_dir() by ignoring UPLOADS for this case.
	if ( ! defined( 'UPLOADS' ) ) {
		$site_id = get_current_blog_id();

		define( 'UPLOADS', UPLOADBLOGSDIR . '/' . $site_id . '/files/' );

		// Uploads dir relative to ABSPATH.
		if ( 'gc-content/blogs.dir' === UPLOADBLOGSDIR && ! defined( 'BLOGUPLOADDIR' ) ) {
			define( 'BLOGUPLOADDIR', GC_CONTENT_DIR . '/blogs.dir/' . $site_id . '/files/' );
		}
	}
}

/**
 * Defines Multisite cookie constants.
 *
 *
 */
function ms_cookie_constants() {
	$current_network = get_network();

	/**
	 */
	if ( ! defined( 'COOKIEPATH' ) ) {
		define( 'COOKIEPATH', $current_network->path );
	}

	/**
	 */
	if ( ! defined( 'SITECOOKIEPATH' ) ) {
		define( 'SITECOOKIEPATH', $current_network->path );
	}

	/**
	 */
	if ( ! defined( 'ADMIN_COOKIE_PATH' ) ) {
		$site_path = parse_url( get_option( 'siteurl' ), PHP_URL_PATH );
		if ( ! is_subdomain_install() || is_string( $site_path ) && trim( $site_path, '/' ) ) {
			define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH );
		} else {
			define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'gc-admin' );
		}
	}

	/**
	 */
	if ( ! defined( 'COOKIE_DOMAIN' ) && is_subdomain_install() ) {
		if ( ! empty( $current_network->cookie_domain ) ) {
			define( 'COOKIE_DOMAIN', '.' . $current_network->cookie_domain );
		} else {
			define( 'COOKIE_DOMAIN', '.' . $current_network->domain );
		}
	}
}

/**
 * Defines Multisite file constants.
 *
 * Exists for backward compatibility with legacy file-serving through
 * gc-includes/ms-files.php (gc-content/blogs.php in MU).
 *
 *
 */
function ms_file_constants() {
	/**
	 * Optional support for X-Sendfile header
	 *
	 */
	if ( ! defined( 'GCMU_SENDFILE' ) ) {
		define( 'GCMU_SENDFILE', false );
	}

	/**
	 * Optional support for X-Accel-Redirect header
	 *
	 */
	if ( ! defined( 'GCMU_ACCEL_REDIRECT' ) ) {
		define( 'GCMU_ACCEL_REDIRECT', false );
	}
}

/**
 * Defines Multisite subdomain constants and handles warnings and notices.
 *
 * VHOST is deprecated in favor of SUBDOMAIN_INSTALL, which is a bool.
 *
 * On first call, the constants are checked and defined. On second call,
 * we will have translations loaded and can trigger warnings easily.
 *
 *
 */
function ms_subdomain_constants() {
	static $subdomain_error      = null;
	static $subdomain_error_warn = null;

	if ( false === $subdomain_error ) {
		return;
	}

	if ( $subdomain_error ) {
		$vhost_deprecated = sprintf(
			/* translators: 1: VHOST, 2: SUBDOMAIN_INSTALL, 3: gc-config.php, 4: is_subdomain_install() */
			__( '选项%1$s已被<strong>弃用</strong>。请改用%3$s中的%2$s布尔型常量来启用子域名配置。使用%4$s来检查子域名配置是否已启用。' ),
			'<code>VHOST</code>',
			'<code>SUBDOMAIN_INSTALL</code>',
			'<code>gc-config.php</code>',
			'<code>is_subdomain_install()</code>'
		);
		if ( $subdomain_error_warn ) {
			trigger_error( __( '<strong>常量VHOST与SUBDOMAIN_INSTALL的值相冲突。</strong>GeChiUI将使用SUBDOMAIN_INSTALL的值来运行。' ) . ' ' . $vhost_deprecated, E_USER_WARNING );
		} else {
			_deprecated_argument( 'define()', '3.0.0', $vhost_deprecated );
		}
		return;
	}

	if ( defined( 'SUBDOMAIN_INSTALL' ) && defined( 'VHOST' ) ) {
		$subdomain_error = true;
		if ( SUBDOMAIN_INSTALL !== ( 'yes' === VHOST ) ) {
			$subdomain_error_warn = true;
		}
	} elseif ( defined( 'SUBDOMAIN_INSTALL' ) ) {
		$subdomain_error = false;
		define( 'VHOST', SUBDOMAIN_INSTALL ? 'yes' : 'no' );
	} elseif ( defined( 'VHOST' ) ) {
		$subdomain_error = true;
		define( 'SUBDOMAIN_INSTALL', 'yes' === VHOST );
	} else {
		$subdomain_error = false;
		define( 'SUBDOMAIN_INSTALL', false );
		define( 'VHOST', 'no' );
	}
}
