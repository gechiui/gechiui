<?php
/**
 * Object Cache API functions missing from 3rd party object caches.
 *
 * @link https://codex.gechiui.com/Class_Reference/GC_Object_Cache
 *
 * @package GeChiUI
 * @subpackage Cache
 */

if ( ! function_exists( 'gc_cache_get_multiple' ) ) :
	/**
	 * Retrieves multiple values from the cache in one call.
	 *
	 * Compat function to mimic gc_cache_get_multiple().
	 *
	 * @ignore
	 *
	 * @see gc_cache_get_multiple()
	 *
	 * @param array  $keys  Array of keys under which the cache contents are stored.
	 * @param string $group Optional. Where the cache contents are grouped. Default empty.
	 * @param bool   $force Optional. Whether to force an update of the local cache
	 *                      from the persistent cache. Default false.
	 * @return array Array of values organized into groups.
	 */
	function gc_cache_get_multiple( $keys, $group = '', $force = false ) {
		$values = array();

		foreach ( $keys as $key ) {
			$values[ $key ] = gc_cache_get( $key, $group, $force );
		}

		return $values;
	}
endif;
