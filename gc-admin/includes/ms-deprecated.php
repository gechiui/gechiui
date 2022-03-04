<?php
/**
 * Multisite: Deprecated admin functions from past versions and GeChiUI MU
 *
 * These functions should not be used and will be removed in a later version.
 * It is suggested to use for the alternatives instead when available.
 *
 * @package GeChiUI
 * @subpackage Deprecated
 *
 */

/**
 * Outputs the GCMU menu.
 *
 * @deprecated 3.0.0
 */
function gcmu_menu() {
	_deprecated_function( __FUNCTION__, '3.0.0' );
	// Deprecated. See #11763.
}

/**
 * Determines if the available space defined by the admin has been exceeded by the user.
 *
 * @deprecated 3.0.0 Use is_upload_space_available()
 * @see is_upload_space_available()
 */
function gcmu_checkAvailableSpace() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'is_upload_space_available()' );

	if ( ! is_upload_space_available() ) {
		gc_die( sprintf(
			/* translators: %s: Allowed space allocation. */
			__( '抱歉，您已经用尽了您的空间配额（%s）。请删除一些文件后再上传更多文件。' ),
			size_format( get_space_allowed() * MB_IN_BYTES )
		) );
	}
}

/**
 * GCMU options.
 *
 * @deprecated 3.0.0
 */
function mu_options( $options ) {
	_deprecated_function( __FUNCTION__, '3.0.0' );
	return $options;
}

/**
 * Deprecated functionality for activating a network-only plugin.
 *
 * @deprecated 3.0.0 Use activate_plugin()
 * @see activate_plugin()
 */
function activate_sitewide_plugin() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'activate_plugin()' );
	return false;
}

/**
 * Deprecated functionality for deactivating a network-only plugin.
 *
 * @deprecated 3.0.0 Use deactivate_plugin()
 * @see deactivate_plugin()
 */
function deactivate_sitewide_plugin( $plugin = false ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'deactivate_plugin()' );
}

/**
 * Deprecated functionality for determining if the current plugin is network-only.
 *
 * @deprecated 3.0.0 Use is_network_only_plugin()
 * @see is_network_only_plugin()
 */
function is_gcmu_sitewide_plugin( $file ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'is_network_only_plugin()' );
	return is_network_only_plugin( $file );
}

/**
 * Deprecated functionality for getting themes network-enabled themes.
 *
 * @deprecated 3.4.0 Use GC_Theme::get_allowed_on_network()
 * @see GC_Theme::get_allowed_on_network()
 */
function get_site_allowed_themes() {
	_deprecated_function( __FUNCTION__, '3.4.0', 'GC_Theme::get_allowed_on_network()' );
	return array_map( 'intval', GC_Theme::get_allowed_on_network() );
}

/**
 * Deprecated functionality for getting themes allowed on a specific site.
 *
 * @deprecated 3.4.0 Use GC_Theme::get_allowed_on_site()
 * @see GC_Theme::get_allowed_on_site()
 */
function gcmu_get_blog_allowedthemes( $blog_id = 0 ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'GC_Theme::get_allowed_on_site()' );
	return array_map( 'intval', GC_Theme::get_allowed_on_site( $blog_id ) );
}

/**
 * Deprecated functionality for determining whether a file is deprecated.
 *
 * @deprecated 3.5.0
 */
function ms_deprecated_blogs_file() {}
