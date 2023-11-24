<?php
/**
 * Object Cache API functions missing from 3rd party object caches.
 *
 * @link https://developer.gechiui.com/reference/classes/gc_object_cache/
 *
 * @package GeChiUI
 * @subpackage Cache
 */

if ( ! function_exists( 'gc_cache_add_multiple' ) ) :
	/**
	 * Adds multiple values to the cache in one call, if the cache keys don't already exist.
	 *
	 * Compat function to mimic gc_cache_add_multiple().
	 *
	 * @ignore
	 * @since 6.0.0
	 *
	 * @see gc_cache_add_multiple()
	 *
	 * @param array  $data   Array of keys and values to be added.
	 * @param string $group  Optional. Where the cache contents are grouped. Default empty.
	 * @param int    $expire Optional. When to expire the cache contents, in seconds.
	 *                       Default 0 (no expiration).
	 * @return bool[] Array of return values, grouped by key. Each value is either
	 *                true on success, or false if cache key and group already exist.
	 */
	function gc_cache_add_multiple( array $data, $group = '', $expire = 0 ) {
		$values = array();

		foreach ( $data as $key => $value ) {
			$values[ $key ] = gc_cache_add( $key, $value, $group, $expire );
		}

		return $values;
	}
endif;

if ( ! function_exists( 'gc_cache_set_multiple' ) ) :
	/**
	 * Sets multiple values to the cache in one call.
	 *
	 * Differs from gc_cache_add_multiple() in that it will always write data.
	 *
	 * Compat function to mimic gc_cache_set_multiple().
	 *
	 * @ignore
	 * @since 6.0.0
	 *
	 * @see gc_cache_set_multiple()
	 *
	 * @param array  $data   Array of keys and values to be set.
	 * @param string $group  Optional. Where the cache contents are grouped. Default empty.
	 * @param int    $expire Optional. When to expire the cache contents, in seconds.
	 *                       Default 0 (no expiration).
	 * @return bool[] Array of return values, grouped by key. Each value is either
	 *                true on success, or false on failure.
	 */
	function gc_cache_set_multiple( array $data, $group = '', $expire = 0 ) {
		$values = array();

		foreach ( $data as $key => $value ) {
			$values[ $key ] = gc_cache_set( $key, $value, $group, $expire );
		}

		return $values;
	}
endif;

if ( ! function_exists( 'gc_cache_get_multiple' ) ) :
	/**
	 * Retrieves multiple values from the cache in one call.
	 *
	 * Compat function to mimic gc_cache_get_multiple().
	 *
	 * @ignore
	 * @since 5.5.0
	 *
	 * @see gc_cache_get_multiple()
	 *
	 * @param array  $keys  Array of keys under which the cache contents are stored.
	 * @param string $group Optional. Where the cache contents are grouped. Default empty.
	 * @param bool   $force Optional. Whether to force an update of the local cache
	 *                      from the persistent cache. Default false.
	 * @return array Array of return values, grouped by key. Each value is either
	 *               the cache contents on success, or false on failure.
	 */
	function gc_cache_get_multiple( $keys, $group = '', $force = false ) {
		$values = array();

		foreach ( $keys as $key ) {
			$values[ $key ] = gc_cache_get( $key, $group, $force );
		}

		return $values;
	}
endif;

if ( ! function_exists( 'gc_cache_delete_multiple' ) ) :
	/**
	 * Deletes multiple values from the cache in one call.
	 *
	 * Compat function to mimic gc_cache_delete_multiple().
	 *
	 * @ignore
	 * @since 6.0.0
	 *
	 * @see gc_cache_delete_multiple()
	 *
	 * @param array  $keys  Array of keys under which the cache to deleted.
	 * @param string $group Optional. Where the cache contents are grouped. Default empty.
	 * @return bool[] Array of return values, grouped by key. Each value is either
	 *                true on success, or false if the contents were not deleted.
	 */
	function gc_cache_delete_multiple( array $keys, $group = '' ) {
		$values = array();

		foreach ( $keys as $key ) {
			$values[ $key ] = gc_cache_delete( $key, $group );
		}

		return $values;
	}
endif;

if ( ! function_exists( 'gc_cache_flush_runtime' ) ) :
	/**
	 * Removes all cache items from the in-memory runtime cache.
	 *
	 * Compat function to mimic gc_cache_flush_runtime().
	 *
	 * @ignore
	 * @since 6.0.0
	 *
	 * @see gc_cache_flush_runtime()
	 *
	 * @return bool True on success, false on failure.
	 */
	function gc_cache_flush_runtime() {
		if ( ! gc_cache_supports( 'flush_runtime' ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				__( '您的对象缓存实现不支持刷新内存中的运行时缓存。' ),
				'6.1.0'
			);

			return false;
		}

		return gc_cache_flush();
	}
endif;

if ( ! function_exists( 'gc_cache_flush_group' ) ) :
	/**
	 * Removes all cache items in a group, if the object cache implementation supports it.
	 *
	 * Before calling this function, always check for group flushing support using the
	 * `gc_cache_supports( 'flush_group' )` function.
	 *
	 * @since 6.1.0
	 *
	 * @see GC_Object_Cache::flush_group()
	 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
	 *
	 * @param string $group Name of group to remove from cache.
	 * @return bool True if group was flushed, false otherwise.
	 */
	function gc_cache_flush_group( $group ) {
		global $gc_object_cache;

		if ( ! gc_cache_supports( 'flush_group' ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				__( '您的对象缓存实现不支持刷新单个组。' ),
				'6.1.0'
			);

			return false;
		}

		return $gc_object_cache->flush_group( $group );
	}
endif;

if ( ! function_exists( 'gc_cache_supports' ) ) :
	/**
	 * Determines whether the object cache implementation supports a particular feature.
	 *
	 * @since 6.1.0
	 *
	 * @param string $feature Name of the feature to check for. Possible values include:
	 *                        'add_multiple', 'set_multiple', 'get_multiple', 'delete_multiple',
	 *                        'flush_runtime', 'flush_group'.
	 * @return bool True if the feature is supported, false otherwise.
	 */
	function gc_cache_supports( $feature ) {
		return false;
	}
endif;
