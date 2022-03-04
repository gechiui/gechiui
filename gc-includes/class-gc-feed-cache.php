<?php
/**
 * Feed API: GC_Feed_Cache class
 *
 * @package GeChiUI
 * @subpackage Feed
 *
 * @deprecated 5.6.0
 */

_deprecated_file(
	basename( __FILE__ ),
	'5.6.0',
	'',
	__( '加载此文件只是为了向后兼容SimplePie 1.2.x。请考虑切换至最新的SimplePie版本。' )
);

/**
 * Core class used to implement a feed cache.
 *
 *
 *
 * @see SimplePie_Cache
 */
class GC_Feed_Cache extends SimplePie_Cache {

	/**
	 * Creates a new SimplePie_Cache object.
	 *
	 *
	 * @param string $location  URL location (scheme is used to determine handler).
	 * @param string $filename  Unique identifier for cache object.
	 * @param string $extension 'spi' or 'spc'.
	 * @return GC_Feed_Cache_Transient Feed cache handler object that uses transients.
	 */
	public function create( $location, $filename, $extension ) {
		return new GC_Feed_Cache_Transient( $location, $filename, $extension );
	}
}
