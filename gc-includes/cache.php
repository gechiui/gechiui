<?php
/**
 * Object Cache API
 *
 * @link https://codex.gechiui.com/Class_Reference/GC_Object_Cache
 *
 * @package GeChiUI
 * @subpackage Cache
 */

/** GC_Object_Cache class */
require_once ABSPATH . GCINC . '/class-gc-object-cache.php';

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 *
 *
 * @see GC_Object_Cache::add()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key    The cache key to use for retrieval later.
 * @param mixed      $data   The data to add to the cache.
 * @param string     $group  Optional. The group to add the cache to. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int        $expire Optional. When the cache data should expire, in seconds.
 *                           Default 0 (no expiration).
 * @return bool True on success, false if cache key and group already exist.
 */
function gc_cache_add( $key, $data, $group = '', $expire = 0 ) {
	global $gc_object_cache;

	return $gc_object_cache->add( $key, $data, $group, (int) $expire );
}

/**
 * Closes the cache.
 *
 * This function has ceased to do anything since GeChiUI 2.5. The
 * functionality was removed along with the rest of the persistent cache.
 *
 * This does not mean that plugins can't implement this function when they need
 * to make sure that the cache is cleaned up after GeChiUI no longer needs it.
 *
 *
 *
 * @return true Always returns true.
 */
function gc_cache_close() {
	return true;
}

/**
 * Decrements numeric cache item's value.
 *
 *
 *
 * @see GC_Object_Cache::decr()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key    The cache key to decrement.
 * @param int        $offset Optional. The amount by which to decrement the item's value. Default 1.
 * @param string     $group  Optional. The group the key is in. Default empty.
 * @return int|false The item's new value on success, false on failure.
 */
function gc_cache_decr( $key, $offset = 1, $group = '' ) {
	global $gc_object_cache;

	return $gc_object_cache->decr( $key, $offset, $group );
}

/**
 * Removes the cache contents matching key and group.
 *
 *
 *
 * @see GC_Object_Cache::delete()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key   What the contents in the cache are called.
 * @param string     $group Optional. Where the cache contents are grouped. Default empty.
 * @return bool True on successful removal, false on failure.
 */
function gc_cache_delete( $key, $group = '' ) {
	global $gc_object_cache;

	return $gc_object_cache->delete( $key, $group );
}

/**
 * Removes all cache items.
 *
 *
 *
 * @see GC_Object_Cache::flush()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @return bool True on success, false on failure.
 */
function gc_cache_flush() {
	global $gc_object_cache;

	return $gc_object_cache->flush();
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 *
 *
 * @see GC_Object_Cache::get()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key   The key under which the cache contents are stored.
 * @param string     $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool       $force Optional. Whether to force an update of the local cache
 *                          from the persistent cache. Default false.
 * @param bool       $found Optional. Whether the key was found in the cache (passed by reference).
 *                          Disambiguates a return of false, a storable value. Default null.
 * @return mixed|false The cache contents on success, false on failure to retrieve contents.
 */
function gc_cache_get( $key, $group = '', $force = false, &$found = null ) {
	global $gc_object_cache;

	return $gc_object_cache->get( $key, $group, $force, $found );
}

/**
 * Retrieves multiple values from the cache in one call.
 *
 *
 *
 * @see GC_Object_Cache::get_multiple()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param array  $keys  Array of keys under which the cache contents are stored.
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool   $force Optional. Whether to force an update of the local cache
 *                      from the persistent cache. Default false.
 * @return array Array of values organized into groups.
 */
function gc_cache_get_multiple( $keys, $group = '', $force = false ) {
	global $gc_object_cache;

	return $gc_object_cache->get_multiple( $keys, $group, $force );
}

/**
 * Increment numeric cache item's value
 *
 *
 *
 * @see GC_Object_Cache::incr()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key    The key for the cache contents that should be incremented.
 * @param int        $offset Optional. The amount by which to increment the item's value. Default 1.
 * @param string     $group  Optional. The group the key is in. Default empty.
 * @return int|false The item's new value on success, false on failure.
 */
function gc_cache_incr( $key, $offset = 1, $group = '' ) {
	global $gc_object_cache;

	return $gc_object_cache->incr( $key, $offset, $group );
}

/**
 * Sets up Object Cache Global and assigns it.
 *
 *
 *
 * @global GC_Object_Cache $gc_object_cache
 */
function gc_cache_init() {
	$GLOBALS['gc_object_cache'] = new GC_Object_Cache();
}

/**
 * Replaces the contents of the cache with new data.
 *
 *
 *
 * @see GC_Object_Cache::replace()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key    The key for the cache data that should be replaced.
 * @param mixed      $data   The new data to store in the cache.
 * @param string     $group  Optional. The group for the cache data that should be replaced.
 *                           Default empty.
 * @param int        $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 * @return bool False if original value does not exist, true if contents were replaced
 */
function gc_cache_replace( $key, $data, $group = '', $expire = 0 ) {
	global $gc_object_cache;

	return $gc_object_cache->replace( $key, $data, $group, (int) $expire );
}

/**
 * Saves the data to the cache.
 *
 * Differs from gc_cache_add() and gc_cache_replace() in that it will always write data.
 *
 *
 *
 * @see GC_Object_Cache::set()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int|string $key    The cache key to use for retrieval later.
 * @param mixed      $data   The contents to store in the cache.
 * @param string     $group  Optional. Where to group the cache contents. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int        $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 * @return bool True on success, false on failure.
 */
function gc_cache_set( $key, $data, $group = '', $expire = 0 ) {
	global $gc_object_cache;

	return $gc_object_cache->set( $key, $data, $group, (int) $expire );
}

/**
 * Switches the internal blog ID.
 *
 * This changes the blog id used to create keys in blog specific groups.
 *
 *
 *
 * @see GC_Object_Cache::switch_to_blog()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param int $blog_id Site ID.
 */
function gc_cache_switch_to_blog( $blog_id ) {
	global $gc_object_cache;

	$gc_object_cache->switch_to_blog( $blog_id );
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 *
 *
 * @see GC_Object_Cache::add_global_groups()
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 *
 * @param string|string[] $groups A group or an array of groups to add.
 */
function gc_cache_add_global_groups( $groups ) {
	global $gc_object_cache;

	$gc_object_cache->add_global_groups( $groups );
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 *
 *
 * @param string|string[] $groups A group or an array of groups to add.
 */
function gc_cache_add_non_persistent_groups( $groups ) {
	// Default cache doesn't persist so nothing to do here.
}

/**
 * Reset internal cache keys and structures.
 *
 * If the cache back end uses global blog or site IDs as part of its cache keys,
 * this function instructs the back end to reset those keys and perform any cleanup
 * since blog or site IDs have changed since cache init.
 *
 * This function is deprecated. Use gc_cache_switch_to_blog() instead of this
 * function when preparing the cache for a blog switch. For clearing the cache
 * during unit tests, consider using gc_cache_init(). gc_cache_init() is not
 * recommended outside of unit tests as the performance penalty for using it is
 * high.
 *
 *
 * @deprecated 3.5.0 GC_Object_Cache::reset()
 * @see GC_Object_Cache::reset()
 *
 * @global GC_Object_Cache $gc_object_cache Object cache global instance.
 */
function gc_cache_reset() {
	_deprecated_function( __FUNCTION__, '3.5.0', 'GC_Object_Cache::reset()' );

	global $gc_object_cache;

	$gc_object_cache->reset();
}
