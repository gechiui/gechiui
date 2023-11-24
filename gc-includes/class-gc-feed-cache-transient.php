<?php
/**
 * Feed API: GC_Feed_Cache_Transient class
 *
 * @package GeChiUI
 * @subpackage Feed
 */

/**
 * Core class used to implement feed cache transients.
 *
 */
#[AllowDynamicProperties]
class GC_Feed_Cache_Transient {

	/**
	 * Holds the transient name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Holds the transient mod name.
	 *
	 * @var string
	 */
	public $mod_name;

	/**
	 * Holds the cache duration in seconds.
	 *
	 * Defaults to 43200 seconds (12 hours).
	 *
	 * @var int
	 */
	public $lifetime = 43200;

	/**
	 * Constructor.
	 *
	 * @since 3.2.0 Updated to use a PHP5 constructor.
	 *
	 * @param string $location  URL location (scheme is used to determine handler).
	 * @param string $filename  Unique identifier for cache object.
	 * @param string $extension 'spi' or 'spc'.
	 */
	public function __construct( $location, $filename, $extension ) {
		$this->name     = 'feed_' . $filename;
		$this->mod_name = 'feed_mod_' . $filename;

		$lifetime = $this->lifetime;
		/**
		 * Filters the transient lifetime of the feed cache.
		 *
		 * @since 2.8.0
		 *
		 * @param int    $lifetime Cache duration in seconds. Default is 43200 seconds (12 hours).
		 * @param string $filename Unique identifier for the cache object.
		 */
		$this->lifetime = apply_filters( 'gc_feed_cache_transient_lifetime', $lifetime, $filename );
	}

	/**
	 * Sets the transient.
	 *
	 *
	 * @param SimplePie $data Data to save.
	 * @return true Always true.
	 */
	public function save( $data ) {
		if ( $data instanceof SimplePie ) {
			$data = $data->data;
		}

		set_transient( $this->name, $data, $this->lifetime );
		set_transient( $this->mod_name, time(), $this->lifetime );
		return true;
	}

	/**
	 * Gets the transient.
	 *
	 *
	 * @return mixed Transient value.
	 */
	public function load() {
		return get_transient( $this->name );
	}

	/**
	 * Gets mod transient.
	 *
	 *
	 * @return mixed Transient value.
	 */
	public function mtime() {
		return get_transient( $this->mod_name );
	}

	/**
	 * Sets mod transient.
	 *
	 *
	 * @return bool False if value was not set and true if value was set.
	 */
	public function touch() {
		return set_transient( $this->mod_name, time(), $this->lifetime );
	}

	/**
	 * Deletes transients.
	 *
	 *
	 * @return true Always true.
	 */
	public function unlink() {
		delete_transient( $this->name );
		delete_transient( $this->mod_name );
		return true;
	}
}
