<?php
/**
 * Conditionally declares a `readonly()` function, which was renamed
 * to `gc_readonly()` in GeChiUI 5.9.0.
 *
 * In order to avoid PHP parser errors, this function was extracted
 * to this separate file and is only included conditionally on PHP 8.1.
 *
 * Including this file on PHP >= 8.1 results in a fatal error.
 *
 * @package GeChiUI
 * @since 5.9.0
 */

/**
 * Outputs the HTML readonly attribute.
 *
 * Compares the first two arguments and if identical marks as readonly.
 *
 * This function is deprecated, and cannot be used on PHP >= 8.1.
 *
 * @deprecated 5.9.0 Use gc_readonly() introduced in 5.9.0.
 *
 * @see gc_readonly()
 *
 * @param mixed $readonly_value One of the values to compare.
 * @param mixed $current        Optional. The other value to compare if not just true.
 *                              Default true.
 * @param bool  $display        Optional. Whether to echo or just return the string.
 *                              Default true.
 * @return string HTML attribute or empty string.
 */
function readonly( $readonly_value, $current = true, $display = true ) {
	_deprecated_function( __FUNCTION__, '5.9.0', 'gc_readonly()' );
	return gc_readonly( $readonly_value, $current, $display );
}
