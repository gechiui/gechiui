<?php
/**
 * Dependencies API: _GC_Dependency class
 *
 *
 *
 * @package GeChiUI
 * @subpackage Dependencies
 */

/**
 * Class _GC_Dependency
 *
 * Helper class to register a handle and associated data.
 *
 * @access private
 *
 */
class _GC_Dependency {
	/**
	 * The handle name.
	 *
	 * @var string
	 */
	public $handle;

	/**
	 * The handle source.
	 *
	 * @var string
	 */
	public $src;

	/**
	 * An array of handle dependencies.
	 *
	 * @var string[]
	 */
	public $deps = array();

	/**
	 * The handle version.
	 *
	 * Used for cache-busting.
	 *
	 * @var bool|string
	 */
	public $ver = false;

	/**
	 * Additional arguments for the handle.
	 *
	 * @var array
	 */
	public $args = null;  // Custom property, such as $in_footer or $media.

	/**
	 * Extra data to supply to the handle.
	 *
	 * @var array
	 */
	public $extra = array();

	/**
	 * Translation textdomain set for this dependency.
	 *
	 * @var string
	 */
	public $textdomain;

	/**
	 * Translation path set for this dependency.
	 *
	 * @var string
	 */
	public $translations_path;

	/**
	 * Setup dependencies.
	 *
	 *              to the function signature.
	 *
	 * @param mixed ...$args Dependency information.
	 */
	public function __construct( ...$args ) {
		list( $this->handle, $this->src, $this->deps, $this->ver, $this->args ) = $args;
		if ( ! is_array( $this->deps ) ) {
			$this->deps = array();
		}
	}

	/**
	 * Add handle data.
	 *
	 *
	 * @param string $name The data key to add.
	 * @param mixed  $data The data value to add.
	 * @return bool False if not scalar, true otherwise.
	 */
	public function add_data( $name, $data ) {
		if ( ! is_scalar( $name ) ) {
			return false;
		}
		$this->extra[ $name ] = $data;
		return true;
	}

	/**
	 * Sets the translation domain for this dependency.
	 *
	 *
	 * @param string $domain The translation textdomain.
	 * @param string $path   Optional. The full file path to the directory containing translation files.
	 * @return bool False if $domain is not a string, true otherwise.
	 */
	public function set_translations( $domain, $path = null ) {
		if ( ! is_string( $domain ) ) {
			return false;
		}
		$this->textdomain        = $domain;
		$this->translations_path = $path;
		return true;
	}
}
