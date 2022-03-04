<?php
/**
 * Dependencies API: Styles functions
 *
 *
 *
 * @package GeChiUI
 * @subpackage Dependencies
 */

/**
 * Initialize $gc_styles if it has not been set.
 *
 * @global GC_Styles $gc_styles
 *
 *
 *
 * @return GC_Styles GC_Styles instance.
 */
function gc_styles() {
	global $gc_styles;

	if ( ! ( $gc_styles instanceof GC_Styles ) ) {
		$gc_styles = new GC_Styles();
	}

	return $gc_styles;
}

/**
 * Display styles that are in the $handles queue.
 *
 * Passing an empty array to $handles prints the queue,
 * passing an array with one string prints that style,
 * and passing an array of strings prints those styles.
 *
 * @global GC_Styles $gc_styles The GC_Styles object for printing styles.
 *
 *
 *
 * @param string|bool|array $handles Styles to be printed. Default 'false'.
 * @return string[] On success, an array of handles of processed GC_Dependencies items; otherwise, an empty array.
 */
function gc_print_styles( $handles = false ) {
	global $gc_styles;

	if ( '' === $handles ) { // For 'gc_head'.
		$handles = false;
	}

	if ( ! $handles ) {
		/**
		 * Fires before styles in the $handles queue are printed.
		 *
		 */
		do_action( 'gc_print_styles' );
	}

	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	if ( ! ( $gc_styles instanceof GC_Styles ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return gc_styles()->do_items( $handles );
}

/**
 * Add extra CSS styles to a registered stylesheet.
 *
 * Styles will only be added if the stylesheet is already in the queue.
 * Accepts a string $data containing the CSS. If two or more CSS code blocks
 * are added to the same stylesheet $handle, they will be printed in the order
 * they were added, i.e. the latter added styles can redeclare the previous.
 *
 * @see GC_Styles::add_inline_style()
 *
 *
 *
 * @param string $handle Name of the stylesheet to add the extra styles to.
 * @param string $data   String containing the CSS styles to be added.
 * @return bool True on success, false on failure.
 */
function gc_add_inline_style( $handle, $data ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	if ( false !== stripos( $data, '</style>' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: <style>, 2: gc_add_inline_style() */
				__( '不要传递%1$s标签给%2$s。' ),
				'<code>&lt;style&gt;</code>',
				'<code>gc_add_inline_style()</code>'
			),
			'3.7.0'
		);
		$data = trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $data ) );
	}

	return gc_styles()->add_inline_style( $handle, $data );
}

/**
 * Register a CSS stylesheet.
 *
 * @see GC_Dependencies::add()
 * @link https://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 *
 *
 *
 * @param string           $handle Name of the stylesheet. Should be unique.
 * @param string|bool      $src    Full URL of the stylesheet, or path of the stylesheet relative to the GeChiUI root directory.
 *                                 If source is set to false, stylesheet is an alias of other stylesheets it depends on.
 * @param string[]         $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
 * @param string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
 *                                 as a query string for cache busting purposes. If version is set to false, a version
 *                                 number is automatically added equal to current installed GeChiUI version.
 *                                 If set to null, no version is added.
 * @param string           $media  Optional. The media for which this stylesheet has been defined.
 *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
 *                                 '(orientation: portrait)' and '(max-width: 640px)'.
 * @return bool Whether the style has been registered. True on success, false on failure.
 */
function gc_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	return gc_styles()->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered stylesheet.
 *
 * @see GC_Dependencies::remove()
 *
 *
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function gc_deregister_style( $handle ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	gc_styles()->remove( $handle );
}

/**
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 *
 * @see GC_Dependencies::add()
 * @see GC_Dependencies::enqueue()
 * @link https://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 *
 *
 * @param string           $handle Name of the stylesheet. Should be unique.
 * @param string           $src    Full URL of the stylesheet, or path of the stylesheet relative to the GeChiUI root directory.
 *                                 Default empty.
 * @param string[]         $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
 * @param string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
 *                                 as a query string for cache busting purposes. If version is set to false, a version
 *                                 number is automatically added equal to current installed GeChiUI version.
 *                                 If set to null, no version is added.
 * @param string           $media  Optional. The media for which this stylesheet has been defined.
 *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
 *                                 '(orientation: portrait)' and '(max-width: 640px)'.
 */
function gc_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	$gc_styles = gc_styles();

	if ( $src ) {
		$_handle = explode( '?', $handle );
		$gc_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}

	$gc_styles->enqueue( $handle );
}

/**
 * Remove a previously enqueued CSS stylesheet.
 *
 * @see GC_Dependencies::dequeue()
 *
 *
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function gc_dequeue_style( $handle ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	gc_styles()->dequeue( $handle );
}

/**
 * Check whether a CSS stylesheet has been added to the queue.
 *
 *
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list   Optional. Status of the stylesheet to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether style is queued.
 */
function gc_style_is( $handle, $list = 'enqueued' ) {
	_gc_scripts_maybe_doing_it_wrong( __FUNCTION__, $handle );

	return (bool) gc_styles()->query( $handle, $list );
}

/**
 * Add metadata to a CSS stylesheet.
 *
 * Works only if the stylesheet has already been registered.
 *
 * Possible values for $key and $value:
 * 'conditional' string      Comments for IE 6, lte IE 7 etc.
 * 'rtl'         bool|string To declare an RTL stylesheet.
 * 'suffix'      string      Optional suffix, used in combination with RTL.
 * 'alt'         bool        For rel="alternate stylesheet".
 * 'title'       string      For preferred/alternate stylesheets.
 * 'path'        string      The absolute path to a stylesheet. Stylesheet will
 *                           load inline when 'path'' is set.
 *
 * @see GC_Dependencies::add_data()
 *
 *
 *
 *              See {@see gc_maybe_inline_styles()}.
 *
 * @param string $handle Name of the stylesheet.
 * @param string $key    Name of data point for which we're storing a value.
 *                       Accepts 'conditional', 'rtl' and 'suffix', 'alt', 'title' and 'path'.
 * @param mixed  $value  String containing the CSS data to be added.
 * @return bool True on success, false on failure.
 */
function gc_style_add_data( $handle, $key, $value ) {
	return gc_styles()->add_data( $handle, $key, $value );
}
