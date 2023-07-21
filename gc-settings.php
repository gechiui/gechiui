<?php
/**
 * Used to set up and fix common variables and include
 * the GeChiUI procedural and class library.
 *
 * Allows for some configuration in gc-config.php (see default-constants.php)
 *
 * @package GeChiUI
 */

/**
 * Stores the location of the GeChiUI directory of functions, classes, and core content.
 *
 * @since 1.0.0
 */
define( 'GCINC', 'gc-includes' );

/**
 * Version information for the current GeChiUI release.
 *
 * These can't be directly globalized in version.php. When updating,
 * we're including version.php from another installation and don't want
 * these values to be overridden if already set.
 *
 * @global string $gc_version             The GeChiUI version string.
 * @global int    $gc_db_version          GeChiUI database version.
 * @global string $tinymce_version        TinyMCE version.
 * @global string $required_php_version   The required PHP version string.
 * @global string $required_mysql_version The required MySQL version string.
 * @global string $gc_local_package       Locale code of the package.
 */
global $gc_version, $gc_db_version, $tinymce_version, $required_php_version, $required_mysql_version, $gc_local_package;
require ABSPATH . GCINC . '/version.php';
require ABSPATH . GCINC . '/load.php';

// Check for the required PHP version and for the MySQL extension or a database drop-in.
gc_check_php_mysql_versions();

// Include files required for initialization.
require ABSPATH . GCINC . '/class-gc-paused-extensions-storage.php';
require ABSPATH . GCINC . '/class-gc-fatal-error-handler.php';
require ABSPATH . GCINC . '/class-gc-recovery-mode-cookie-service.php';
require ABSPATH . GCINC . '/class-gc-recovery-mode-key-service.php';
require ABSPATH . GCINC . '/class-gc-recovery-mode-link-service.php';
require ABSPATH . GCINC . '/class-gc-recovery-mode-email-service.php';
require ABSPATH . GCINC . '/class-gc-recovery-mode.php';
require ABSPATH . GCINC . '/error-protection.php';
require ABSPATH . GCINC . '/default-constants.php';
require_once ABSPATH . GCINC . '/plugin.php';

/**
 * If not already configured, `$blog_id` will default to 1 in a single site
 * configuration. In multisite, it will be overridden by default in ms-settings.php.
 *
 * @global int $blog_id
 * @since 2.0.0
 */
global $blog_id;

// Set initial default constants including GC_MEMORY_LIMIT, GC_MAX_MEMORY_LIMIT, GC_DEBUG, SCRIPT_DEBUG, GC_CONTENT_DIR and GC_CACHE.
gc_initial_constants();

// Make sure we register the shutdown handler for fatal errors as soon as possible.
gc_register_fatal_error_handler();

// GeChiUI calculates offsets from UTC.
// phpcs:ignore GeChiUI.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
date_default_timezone_set( 'UTC' );

// Standardize $_SERVER variables across setups.
gc_fix_server_vars();

// Check if we're in maintenance mode.
gc_maintenance();

// Start loading timer.
timer_start();

// Check if we're in GC_DEBUG mode.
gc_debug_mode();

/**
 * Filters whether to enable loading of the advanced-cache.php drop-in.
 *
 * This filter runs before it can be used by plugins. It is designed for non-web
 * run-times. If false is returned, advanced-cache.php will never be loaded.
 *
 * @since 4.6.0
 *
 * @param bool $enable_advanced_cache Whether to enable loading advanced-cache.php (if present).
 *                                    Default true.
 */
if ( GC_CACHE && apply_filters( 'enable_loading_advanced_cache_dropin', true ) && file_exists( GC_CONTENT_DIR . '/advanced-cache.php' ) ) {
	// For an advanced caching plugin to use. Uses a static drop-in because you would only want one.
	include GC_CONTENT_DIR . '/advanced-cache.php';

	// Re-initialize any hooks added manually by advanced-cache.php.
	if ( $gc_filter ) {
		$gc_filter = GC_Hook::build_preinitialized_hooks( $gc_filter );
	}
}

// Define GC_LANG_DIR if not set.
gc_set_lang_dir();

// Load early GeChiUI files.
require ABSPATH . GCINC . '/compat.php';
require ABSPATH . GCINC . '/class-gc-list-util.php';
require ABSPATH . GCINC . '/formatting.php';
require ABSPATH . GCINC . '/meta.php';
require ABSPATH . GCINC . '/functions.php';
require ABSPATH . GCINC . '/class-gc-meta-query.php';
require ABSPATH . GCINC . '/class-gc-matchesmapregex.php';
require ABSPATH . GCINC . '/class-gc.php';
require ABSPATH . GCINC . '/class-gc-error.php';
require ABSPATH . GCINC . '/pomo/mo.php';

/**
 * @global gcdb $gcdb GeChiUI database abstraction object.
 * @since 0.71
 */
global $gcdb;
// Include the gcdb class and, if present, a db.php database drop-in.
require_gc_db();

// Set the database table prefix and the format specifiers for database table columns.
$GLOBALS['table_prefix'] = $table_prefix;
gc_set_gcdb_vars();

// Start the GeChiUI object cache, or an external object cache if the drop-in is present.
gc_start_object_cache();

// Attach the default filters.
require ABSPATH . GCINC . '/default-filters.php';

// Initialize multisite if enabled.
if ( is_multisite() ) {
	require ABSPATH . GCINC . '/class-gc-site-query.php';
	require ABSPATH . GCINC . '/class-gc-network-query.php';
	require ABSPATH . GCINC . '/ms-blogs.php';
	require ABSPATH . GCINC . '/ms-settings.php';
} elseif ( ! defined( 'MULTISITE' ) ) {
	define( 'MULTISITE', false );
}

register_shutdown_function( 'shutdown_action_hook' );

// Stop most of GeChiUI from being loaded if we just want the basics.
if ( SHORTINIT ) {
	return false;
}

// Load the L10n library.
require_once ABSPATH . GCINC . '/l10n.php';
require_once ABSPATH . GCINC . '/class-gc-locale.php';
require_once ABSPATH . GCINC . '/class-gc-locale-switcher.php';

// Run the installer if GeChiUI is not installed.
gc_not_installed();

// Load most of GeChiUI.
require ABSPATH . GCINC . '/class-gc-walker.php';
require ABSPATH . GCINC . '/class-gc-ajax-response.php';
require ABSPATH . GCINC . '/capabilities.php';
require ABSPATH . GCINC . '/class-gc-roles.php';
require ABSPATH . GCINC . '/class-gc-role.php';
require ABSPATH . GCINC . '/class-gc-user.php';
require ABSPATH . GCINC . '/class-gc-query.php';
require ABSPATH . GCINC . '/query.php';
require ABSPATH . GCINC . '/class-gc-date-query.php';
require ABSPATH . GCINC . '/theme.php';
require ABSPATH . GCINC . '/class-gc-theme.php';
require ABSPATH . GCINC . '/class-gc-theme-json-schema.php';
require ABSPATH . GCINC . '/class-gc-theme-json.php';
require ABSPATH . GCINC . '/class-gc-theme-json-resolver.php';
require ABSPATH . GCINC . '/global-styles-and-settings.php';
require ABSPATH . GCINC . '/class-gc-block-template.php';
require ABSPATH . GCINC . '/block-template-utils.php';
require ABSPATH . GCINC . '/block-template.php';
require ABSPATH . GCINC . '/theme-templates.php';
require ABSPATH . GCINC . '/template.php';
require ABSPATH . GCINC . '/https-detection.php';
require ABSPATH . GCINC . '/https-migration.php';
require ABSPATH . GCINC . '/class-gc-user-request.php';
require ABSPATH . GCINC . '/user.php';
require ABSPATH . GCINC . '/class-gc-user-query.php';
require ABSPATH . GCINC . '/class-gc-session-tokens.php';
require ABSPATH . GCINC . '/class-gc-user-meta-session-tokens.php';
require ABSPATH . GCINC . '/class-gc-metadata-lazyloader.php';
require ABSPATH . GCINC . '/general-template.php';
require ABSPATH . GCINC . '/link-template.php';
require ABSPATH . GCINC . '/author-template.php';
require ABSPATH . GCINC . '/robots-template.php';
require ABSPATH . GCINC . '/post.php';
require ABSPATH . GCINC . '/class-walker-page.php';
require ABSPATH . GCINC . '/class-walker-page-dropdown.php';
require ABSPATH . GCINC . '/class-gc-post-type.php';
require ABSPATH . GCINC . '/class-gc-post.php';
require ABSPATH . GCINC . '/post-template.php';
require ABSPATH . GCINC . '/revision.php';
require ABSPATH . GCINC . '/post-formats.php';
require ABSPATH . GCINC . '/post-thumbnail-template.php';
require ABSPATH . GCINC . '/category.php';
require ABSPATH . GCINC . '/class-walker-category.php';
require ABSPATH . GCINC . '/class-walker-category-dropdown.php';
require ABSPATH . GCINC . '/category-template.php';
require ABSPATH . GCINC . '/comment.php';
require ABSPATH . GCINC . '/class-gc-comment.php';
require ABSPATH . GCINC . '/class-gc-comment-query.php';
require ABSPATH . GCINC . '/class-walker-comment.php';
require ABSPATH . GCINC . '/comment-template.php';
require ABSPATH . GCINC . '/rewrite.php';
require ABSPATH . GCINC . '/class-gc-rewrite.php';
require ABSPATH . GCINC . '/feed.php';
require ABSPATH . GCINC . '/bookmark.php';
require ABSPATH . GCINC . '/bookmark-template.php';
require ABSPATH . GCINC . '/kses.php';
require ABSPATH . GCINC . '/cron.php';
require ABSPATH . GCINC . '/deprecated.php';
require ABSPATH . GCINC . '/script-loader.php';
require ABSPATH . GCINC . '/taxonomy.php';
require ABSPATH . GCINC . '/class-gc-taxonomy.php';
require ABSPATH . GCINC . '/class-gc-term.php';
require ABSPATH . GCINC . '/class-gc-term-query.php';
require ABSPATH . GCINC . '/class-gc-tax-query.php';
require ABSPATH . GCINC . '/update.php';
require ABSPATH . GCINC . '/canonical.php';
require ABSPATH . GCINC . '/shortcodes.php';
require ABSPATH . GCINC . '/embed.php';
require ABSPATH . GCINC . '/class-gc-embed.php';
require ABSPATH . GCINC . '/class-gc-oembed.php';
require ABSPATH . GCINC . '/class-gc-oembed-controller.php';
require ABSPATH . GCINC . '/media.php';
require ABSPATH . GCINC . '/http.php';
require ABSPATH . GCINC . '/class-gc-http.php';
require ABSPATH . GCINC . '/class-gc-http-streams.php';
require ABSPATH . GCINC . '/class-gc-http-curl.php';
require ABSPATH . GCINC . '/class-gc-http-proxy.php';
require ABSPATH . GCINC . '/class-gc-http-cookie.php';
require ABSPATH . GCINC . '/class-gc-http-encoding.php';
require ABSPATH . GCINC . '/class-gc-http-response.php';
require ABSPATH . GCINC . '/class-gc-http-requests-response.php';
require ABSPATH . GCINC . '/class-gc-http-requests-hooks.php';
require ABSPATH . GCINC . '/widgets.php';
require ABSPATH . GCINC . '/class-gc-widget.php';
require ABSPATH . GCINC . '/class-gc-widget-factory.php';
require ABSPATH . GCINC . '/nav-menu-template.php';
require ABSPATH . GCINC . '/nav-menu.php';
require ABSPATH . GCINC . '/admin-bar.php';
require ABSPATH . GCINC . '/class-gc-appkeys.php';
require ABSPATH . GCINC . '/rest-api.php';
require ABSPATH . GCINC . '/rest-api/class-gc-rest-server.php';
require ABSPATH . GCINC . '/rest-api/class-gc-rest-response.php';
require ABSPATH . GCINC . '/rest-api/class-gc-rest-request.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-posts-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-attachments-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-global-styles-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-post-types-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-post-statuses-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-revisions-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-autosaves-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-taxonomies-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-terms-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-menu-items-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-menus-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-menu-locations-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-users-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-comments-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-search-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-blocks-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-block-types-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-block-renderer-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-settings-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-themes-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-plugins-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-block-directory-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-edit-site-export-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-pattern-directory-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-appkeys-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-site-health-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-sidebars-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-widget-types-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-widgets-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-templates-controller.php';
require ABSPATH . GCINC . '/rest-api/endpoints/class-gc-rest-url-details-controller.php';
require ABSPATH . GCINC . '/rest-api/fields/class-gc-rest-meta-fields.php';
require ABSPATH . GCINC . '/rest-api/fields/class-gc-rest-comment-meta-fields.php';
require ABSPATH . GCINC . '/rest-api/fields/class-gc-rest-post-meta-fields.php';
require ABSPATH . GCINC . '/rest-api/fields/class-gc-rest-term-meta-fields.php';
require ABSPATH . GCINC . '/rest-api/fields/class-gc-rest-user-meta-fields.php';
require ABSPATH . GCINC . '/rest-api/search/class-gc-rest-search-handler.php';
require ABSPATH . GCINC . '/rest-api/search/class-gc-rest-post-search-handler.php';
require ABSPATH . GCINC . '/rest-api/search/class-gc-rest-term-search-handler.php';
require ABSPATH . GCINC . '/rest-api/search/class-gc-rest-post-format-search-handler.php';
require ABSPATH . GCINC . '/sitemaps.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps-index.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps-provider.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps-registry.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps-renderer.php';
require ABSPATH . GCINC . '/sitemaps/class-gc-sitemaps-stylesheet.php';
require ABSPATH . GCINC . '/sitemaps/providers/class-gc-sitemaps-sites.php';
require ABSPATH . GCINC . '/sitemaps/providers/class-gc-sitemaps-posts.php';
require ABSPATH . GCINC . '/sitemaps/providers/class-gc-sitemaps-taxonomies.php';
require ABSPATH . GCINC . '/sitemaps/providers/class-gc-sitemaps-users.php';
require ABSPATH . GCINC . '/class-gc-block-editor-context.php';
require ABSPATH . GCINC . '/class-gc-block-type.php';
require ABSPATH . GCINC . '/class-gc-block-pattern-categories-registry.php';
require ABSPATH . GCINC . '/class-gc-block-patterns-registry.php';
require ABSPATH . GCINC . '/class-gc-block-styles-registry.php';
require ABSPATH . GCINC . '/class-gc-block-type-registry.php';
require ABSPATH . GCINC . '/class-gc-block.php';
require ABSPATH . GCINC . '/class-gc-block-list.php';
require ABSPATH . GCINC . '/class-gc-block-parser.php';
require ABSPATH . GCINC . '/blocks.php';
require ABSPATH . GCINC . '/blocks/index.php';
require ABSPATH . GCINC . '/block-editor.php';
require ABSPATH . GCINC . '/block-patterns.php';
require ABSPATH . GCINC . '/class-gc-block-supports.php';
require ABSPATH . GCINC . '/block-supports/utils.php';
require ABSPATH . GCINC . '/block-supports/align.php';
require ABSPATH . GCINC . '/block-supports/border.php';
require ABSPATH . GCINC . '/block-supports/colors.php';
require ABSPATH . GCINC . '/block-supports/custom-classname.php';
require ABSPATH . GCINC . '/block-supports/dimensions.php';
require ABSPATH . GCINC . '/block-supports/duotone.php';
require ABSPATH . GCINC . '/block-supports/elements.php';
require ABSPATH . GCINC . '/block-supports/generated-classname.php';
require ABSPATH . GCINC . '/block-supports/layout.php';
require ABSPATH . GCINC . '/block-supports/spacing.php';
require ABSPATH . GCINC . '/block-supports/typography.php';

$GLOBALS['gc_embed'] = new GC_Embed();

// Load multisite-specific files.
if ( is_multisite() ) {
	require ABSPATH . GCINC . '/ms-functions.php';
	require ABSPATH . GCINC . '/ms-default-filters.php';
	require ABSPATH . GCINC . '/ms-deprecated.php';
}

// Define constants that rely on the API to obtain the default value.
// Define must-use plugin directory constants, which may be overridden in the sunrise.php drop-in.
gc_plugin_directory_constants();

$GLOBALS['gc_plugin_paths'] = array();

// Load must-use plugins.
foreach ( gc_get_mu_plugins() as $mu_plugin ) {
	$_gc_plugin_file = $mu_plugin;
	include_once $mu_plugin;
	$mu_plugin = $_gc_plugin_file; // Avoid stomping of the $mu_plugin variable in a plugin.

	/**
	 * Fires once a single must-use plugin has loaded.
	 *
	 * @since 5.1.0
	 *
	 * @param string $mu_plugin Full path to the plugin's main file.
	 */
	do_action( 'mu_plugin_loaded', $mu_plugin );
}
unset( $mu_plugin, $_gc_plugin_file );

// Load network activated plugins.
if ( is_multisite() ) {
	foreach ( gc_get_active_network_plugins() as $network_plugin ) {
		gc_register_plugin_realpath( $network_plugin );

		$_gc_plugin_file = $network_plugin;
		include_once $network_plugin;
		$network_plugin = $_gc_plugin_file; // Avoid stomping of the $network_plugin variable in a plugin.

		/**
		 * Fires once a single network-activated plugin has loaded.
		 *
		 * @since 5.1.0
		 *
		 * @param string $network_plugin Full path to the plugin's main file.
		 */
		do_action( 'network_plugin_loaded', $network_plugin );
	}
	unset( $network_plugin, $_gc_plugin_file );
}

/**
 * Fires once all must-use and network-activated plugins have loaded.
 *
 * @since 2.8.0
 */
do_action( 'muplugins_loaded' );

if ( is_multisite() ) {
	ms_cookie_constants();
}

// Define constants after multisite is loaded.
gc_cookie_constants();

// Define and enforce our SSL constants.
gc_ssl_constants();

// Create common globals.
require ABSPATH . GCINC . '/vars.php';

// Make taxonomies and posts available to plugins and themes.
// @plugin authors: warning: these get registered again on the init hook.
create_initial_taxonomies();
create_initial_post_types();

gc_start_scraping_edited_file_errors();

// Register the default theme directory root.
register_theme_directory( get_theme_root() );

if ( ! is_multisite() ) {
	// Handle users requesting a recovery mode link and initiating recovery mode.
	gc_recovery_mode()->initialize();
}

// Load active plugins.
foreach ( gc_get_active_and_valid_plugins() as $plugin ) {
	gc_register_plugin_realpath( $plugin );

	$_gc_plugin_file = $plugin;
	include_once $plugin;
	$plugin = $_gc_plugin_file; // Avoid stomping of the $plugin variable in a plugin.

	/**
	 * Fires once a single activated plugin has loaded.
	 *
	 * @since 5.1.0
	 *
	 * @param string $plugin Full path to the plugin's main file.
	 */
	do_action( 'plugin_loaded', $plugin );
}
unset( $plugin, $_gc_plugin_file );

// Load pluggable functions.
require ABSPATH . GCINC . '/pluggable.php';
require ABSPATH . GCINC . '/pluggable-deprecated.php';

// Set internal encoding.
gc_set_internal_encoding();

// Run gc_cache_postload() if object cache is enabled and the function exists.
if ( GC_CACHE && function_exists( 'gc_cache_postload' ) ) {
	gc_cache_postload();
}

/**
 * Fires once activated plugins have loaded.
 *
 * Pluggable functions are also available at this point in the loading order.
 *
 * @since 1.5.0
 */
do_action( 'plugins_loaded' );

// Define constants which affect functionality if not already defined.
gc_functionality_constants();

// Add magic quotes and set up $_REQUEST ( $_GET + $_POST ).
gc_magic_quotes();

/**
 * Fires when comment cookies are sanitized.
 *
 * @since 2.0.11
 */
do_action( 'sanitize_comment_cookies' );

/**
 * GeChiUI Query object
 *
 * @global GC_Query $gc_the_query GeChiUI Query object.
 * @since 2.0.0
 */
$GLOBALS['gc_the_query'] = new GC_Query();

/**
 * Holds the reference to @see $gc_the_query
 * Use this global for GeChiUI queries
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 * @since 1.5.0
 */
$GLOBALS['gc_query'] = $GLOBALS['gc_the_query'];

/**
 * Holds the GeChiUI Rewrite object for creating pretty URLs
 *
 * @global GC_Rewrite $gc_rewrite GeChiUI rewrite component.
 * @since 1.5.0
 */
$GLOBALS['gc_rewrite'] = new GC_Rewrite();

/**
 * GeChiUI Object
 *
 * @global GC $gc Current GeChiUI environment instance.
 * @since 2.0.0
 */
$GLOBALS['gc'] = new GC();

/**
 * GeChiUI Widget Factory Object
 *
 * @global GC_Widget_Factory $gc_widget_factory
 * @since 2.8.0
 */
$GLOBALS['gc_widget_factory'] = new GC_Widget_Factory();

/**
 * GeChiUI User Roles
 *
 * @global GC_Roles $gc_roles GeChiUI role management object.
 * @since 2.0.0
 */
$GLOBALS['gc_roles'] = new GC_Roles();

/**
 * Fires before the theme is loaded.
 *
 * @since 2.6.0
 */
do_action( 'setup_theme' );

// Define the template related constants.
gc_templating_constants();

// Load the default text localization domain.
load_default_textdomain();

$locale      = get_locale();
$locale_file = GC_LANG_DIR . "/$locale.php";
if ( ( 0 === validate_file( $locale ) ) && is_readable( $locale_file ) ) {
	require $locale_file;
}
unset( $locale_file );

/**
 * GeChiUI Locale object for loading locale domain date and various strings.
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 * @since 2.1.0
 */
$GLOBALS['gc_locale'] = new GC_Locale();

/**
 * GeChiUI Locale Switcher object for switching locales.
 *
 * @since 4.7.0
 *
 * @global GC_Locale_Switcher $gc_locale_switcher GeChiUI locale switcher object.
 */
$GLOBALS['gc_locale_switcher'] = new GC_Locale_Switcher();
$GLOBALS['gc_locale_switcher']->init();

// Load the functions for the active theme, for both parent and child theme if applicable.
foreach ( gc_get_active_and_valid_themes() as $theme ) {
	if ( file_exists( $theme . '/functions.php' ) ) {
		include $theme . '/functions.php';
	}
}
unset( $theme );

/**
 * Fires after the theme is loaded.
 *
 * @since 3.0.0
 */
do_action( 'after_setup_theme' );

// Create an instance of GC_Site_Health so that Cron events may fire.
if ( ! class_exists( 'GC_Site_Health' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
}
GC_Site_Health::get_instance();

// Set up current user.
$GLOBALS['gc']->init();

/**
 * Fires after GeChiUI has finished loading but before any headers are sent.
 *
 * Most of GC is loaded at this stage, and the user is authenticated. GC continues
 * to load on the {@see 'init'} hook that follows (e.g. widgets), and many plugins instantiate
 * themselves on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
 *
 * If you wish to plug an action once GC is loaded, use the {@see 'gc_loaded'} hook below.
 *
 * @since 1.5.0
 */
do_action( 'init' );

// Check site status.
if ( is_multisite() ) {
	$file = ms_site_check();
	if ( true !== $file ) {
		require $file;
		die();
	}
	unset( $file );
}

/**
 * This hook is fired once GC, all plugins, and the theme are fully loaded and instantiated.
 *
 * Ajax requests should use gc-admin/admin-ajax.php. admin-ajax.php can handle requests for
 * users not logged in.
 *
 * @link https://codex.gechiui.com/AJAX_in_Plugins
 *
 * @since 3.0.0
 */
do_action( 'gc_loaded' );
