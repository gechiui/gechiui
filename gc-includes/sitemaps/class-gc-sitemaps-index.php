<?php
/**
 * Sitemaps: GC_Sitemaps_Index class.
 *
 * Generates the sitemap index.
 *
 * @package GeChiUI
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class GC_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 * @since 5.5.0
 */
#[AllowDynamicProperties]
class GC_Sitemaps_Index {
	/**
	 * The main registry of supported sitemaps.
	 *
	 * @since 5.5.0
	 * @var GC_Sitemaps_Registry
	 */
	protected $registry;

	/**
	 * Maximum number of sitemaps to include in an index.
	 *
	 * @since 5.5.0
	 *
	 * @var int Maximum number of sitemaps.
	 */
	private $max_sitemaps = 50000;

	/**
	 * GC_Sitemaps_Index constructor.
	 *
	 * @since 5.5.0
	 *
	 * @param GC_Sitemaps_Registry $registry Sitemap provider registry.
	 */
	public function __construct( GC_Sitemaps_Registry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Gets a sitemap list for the index.
	 *
	 * @since 5.5.0
	 *
	 * @return array[] Array of all sitemaps.
	 */
	public function get_sitemap_list() {
		$sitemaps = array();

		$providers = $this->registry->get_providers();
		/* @var GC_Sitemaps_Provider $provider */
		foreach ( $providers as $name => $provider ) {
			$sitemap_entries = $provider->get_sitemap_entries();

			// Prevent issues with array_push and empty arrays on PHP < 7.3.
			if ( ! $sitemap_entries ) {
				continue;
			}

			// Using array_push is more efficient than array_merge in a loop.
			array_push( $sitemaps, ...$sitemap_entries );
			if ( count( $sitemaps ) >= $this->max_sitemaps ) {
				break;
			}
		}

		return array_slice( $sitemaps, 0, $this->max_sitemaps, true );
	}

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @global GC_Rewrite $gc_rewrite GeChiUI rewrite component.
	 *
	 * @return string The sitemap index URL.
	 */
	public function get_index_url() {
		global $gc_rewrite;

		if ( ! $gc_rewrite->using_permalinks() ) {
			return home_url( '/?sitemap=index' );
		}

		return home_url( '/gc-sitemap.xml' );
	}
}
