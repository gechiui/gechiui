<?php
/**
 * Network API: GC_Network class
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/**
 * Core class used for interacting with a multisite network.
 *
 * This class is used during load to populate the `$current_site` global and
 * setup the current network.
 *
 * This class is most useful in GeChiUI multi-network installations where the
 * ability to interact with any network of sites is required.
 *
 *
 *
 * @property int $id
 * @property int $site_id
 */
class GC_Network {

	/**
	 * Network ID.
	 *
	 *              access via magic methods. As part of the access change, the type was
	 *              also changed from `string` to `int`.
	 * @var int
	 */
	private $id;

	/**
	 * Domain of the network.
	 *
	 * @var string
	 */
	public $domain = '';

	/**
	 * Path of the network.
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * The ID of the network's main site.
	 *
	 * Named "blog" vs. "site" for legacy reasons. A main site is mapped to
	 * the network when the network is created.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	private $blog_id = '0';

	/**
	 * Domain used to set cookies for this network.
	 *
	 * @var string
	 */
	public $cookie_domain = '';

	/**
	 * Name of this network.
	 *
	 * Named "site" vs. "network" for legacy reasons.
	 *
	 * @var string
	 */
	public $site_name = '';

	/**
	 * Retrieve a network from the database by its ID.
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param int $network_id The ID of the network to retrieve.
	 * @return GC_Network|false The network's object if found. False if not.
	 */
	public static function get_instance( $network_id ) {
		global $gcdb;

		$network_id = (int) $network_id;
		if ( ! $network_id ) {
			return false;
		}

		$_network = gc_cache_get( $network_id, 'networks' );

		if ( false === $_network ) {
			$_network = $gcdb->get_row( $gcdb->prepare( "SELECT * FROM {$gcdb->site} WHERE id = %d LIMIT 1", $network_id ) );

			if ( empty( $_network ) || is_gc_error( $_network ) ) {
				$_network = -1;
			}

			gc_cache_add( $network_id, $_network, 'networks' );
		}

		if ( is_numeric( $_network ) ) {
			return false;
		}

		return new GC_Network( $_network );
	}

	/**
	 * Create a new GC_Network object.
	 *
	 * Will populate object properties from the object provided and assign other
	 * default properties based on that information.
	 *
	 *
	 * @param GC_Network|object $network A network object.
	 */
	public function __construct( $network ) {
		foreach ( get_object_vars( $network ) as $key => $value ) {
			$this->$key = $value;
		}

		$this->_set_site_name();
		$this->_set_cookie_domain();
	}

	/**
	 * Getter.
	 *
	 * Allows current multisite naming conventions when getting properties.
	 *
	 *
	 * @param string $key Property to get.
	 * @return mixed Value of the property. Null if not available.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'id':
				return (int) $this->id;
			case 'blog_id':
				return (string) $this->get_main_site_id();
			case 'site_id':
				return $this->get_main_site_id();
		}

		return null;
	}

	/**
	 * Isset-er.
	 *
	 * Allows current multisite naming conventions when checking for properties.
	 *
	 *
	 * @param string $key Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $key ) {
		switch ( $key ) {
			case 'id':
			case 'blog_id':
			case 'site_id':
				return true;
		}

		return false;
	}

	/**
	 * Setter.
	 *
	 * Allows current multisite naming conventions while setting properties.
	 *
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 */
	public function __set( $key, $value ) {
		switch ( $key ) {
			case 'id':
				$this->id = (int) $value;
				break;
			case 'blog_id':
			case 'site_id':
				$this->blog_id = (string) $value;
				break;
			default:
				$this->$key = $value;
		}
	}

	/**
	 * Returns the main site ID for the network.
	 *
	 * Internal method used by the magic getter for the 'blog_id' and 'site_id'
	 * properties.
	 *
	 *
	 * @return int The ID of the main site.
	 */
	private function get_main_site_id() {
		/**
		 * Filters the main site ID.
		 *
		 * Returning a positive integer will effectively short-circuit the function.
		 *
		 *
		 * @param int|null   $main_site_id If a positive integer is returned, it is interpreted as the main site ID.
		 * @param GC_Network $network      The network object for which the main site was detected.
		 */
		$main_site_id = (int) apply_filters( 'pre_get_main_site_id', null, $this );
		if ( 0 < $main_site_id ) {
			return $main_site_id;
		}

		if ( 0 < (int) $this->blog_id ) {
			return (int) $this->blog_id;
		}

		if ( ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) && DOMAIN_CURRENT_SITE === $this->domain && PATH_CURRENT_SITE === $this->path )
			|| ( defined( 'SITE_ID_CURRENT_SITE' ) && SITE_ID_CURRENT_SITE == $this->id ) ) {
			if ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
				$this->blog_id = (string) BLOG_ID_CURRENT_SITE;

				return (int) $this->blog_id;
			}

			if ( defined( 'BLOGID_CURRENT_SITE' ) ) { // Deprecated.
				$this->blog_id = (string) BLOGID_CURRENT_SITE;

				return (int) $this->blog_id;
			}
		}

		$site = get_site();
		if ( $site->domain === $this->domain && $site->path === $this->path ) {
			$main_site_id = (int) $site->id;
		} else {
			$cache_key = 'network:' . $this->id . ':main_site';

			$main_site_id = gc_cache_get( $cache_key, 'site-options' );
			if ( false === $main_site_id ) {
				$_sites       = get_sites(
					array(
						'fields'     => 'ids',
						'number'     => 1,
						'domain'     => $this->domain,
						'path'       => $this->path,
						'network_id' => $this->id,
					)
				);
				$main_site_id = ! empty( $_sites ) ? array_shift( $_sites ) : 0;

				gc_cache_add( $cache_key, $main_site_id, 'site-options' );
			}
		}

		$this->blog_id = (string) $main_site_id;

		return (int) $this->blog_id;
	}

	/**
	 * Set the site name assigned to the network if one has not been populated.
	 *
	 */
	private function _set_site_name() {
		if ( ! empty( $this->site_name ) ) {
			return;
		}

		$default         = ucfirst( $this->domain );
		$this->site_name = get_network_option( $this->id, 'site_name', $default );
	}

	/**
	 * Set the cookie domain based on the network domain if one has
	 * not been populated.
	 *
	 * @todo What if the domain of the network doesn't match the current site?
	 *
	 */
	private function _set_cookie_domain() {
		if ( ! empty( $this->cookie_domain ) ) {
			return;
		}

		$this->cookie_domain = $this->domain;
		if ( 'www.' === substr( $this->cookie_domain, 0, 4 ) ) {
			$this->cookie_domain = substr( $this->cookie_domain, 4 );
		}
	}

	/**
	 * Retrieve the closest matching network for a domain and path.
	 *
	 * This will not necessarily return an exact match for a domain and path. Instead, it
	 * breaks the domain and path into pieces that are then used to match the closest
	 * possibility from a query.
	 *
	 * The intent of this method is to match a network during bootstrap for a
	 * requested site address.
	 *
	 *
	 * @param string   $domain   Domain to check.
	 * @param string   $path     Path to check.
	 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
	 * @return GC_Network|false Network object if successful. False when no network is found.
	 */
	public static function get_by_path( $domain = '', $path = '', $segments = null ) {
		$domains = array( $domain );
		$pieces  = explode( '.', $domain );

		/*
		 * It's possible one domain to search is 'com', but it might as well
		 * be 'localhost' or some other locally mapped domain.
		 */
		while ( array_shift( $pieces ) ) {
			if ( ! empty( $pieces ) ) {
				$domains[] = implode( '.', $pieces );
			}
		}

		/*
		 * If we've gotten to this function during normal execution, there is
		 * more than one network installed. At this point, who knows how many
		 * we have. Attempt to optimize for the situation where networks are
		 * only domains, thus meaning paths never need to be considered.
		 *
		 * This is a very basic optimization; anything further could have
		 * drawbacks depending on the setup, so this is best done per-installation.
		 */
		$using_paths = true;
		if ( gc_using_ext_object_cache() ) {
			$using_paths = gc_cache_get( 'networks_have_paths', 'site-options' );
			if ( false === $using_paths ) {
				$using_paths = get_networks(
					array(
						'number'       => 1,
						'count'        => true,
						'path__not_in' => '/',
					)
				);
				gc_cache_add( 'networks_have_paths', $using_paths, 'site-options' );
			}
		}

		$paths = array();
		if ( $using_paths ) {
			$path_segments = array_filter( explode( '/', trim( $path, '/' ) ) );

			/**
			 * Filters the number of path segments to consider when searching for a site.
			 *
		
			 *
			 * @param int|null $segments The number of path segments to consider. GeChiUI by default looks at
			 *                           one path segment. The function default of null only makes sense when you
			 *                           know the requested path should match a network.
			 * @param string   $domain   The requested domain.
			 * @param string   $path     The requested path, in full.
			 */
			$segments = apply_filters( 'network_by_path_segments_count', $segments, $domain, $path );

			if ( ( null !== $segments ) && count( $path_segments ) > $segments ) {
				$path_segments = array_slice( $path_segments, 0, $segments );
			}

			while ( count( $path_segments ) ) {
				$paths[] = '/' . implode( '/', $path_segments ) . '/';
				array_pop( $path_segments );
			}

			$paths[] = '/';
		}

		/**
		 * Determine a network by its domain and path.
		 *
		 * This allows one to short-circuit the default logic, perhaps by
		 * replacing it with a routine that is more optimal for your setup.
		 *
		 * Return null to avoid the short-circuit. Return false if no network
		 * can be found at the requested domain and path. Otherwise, return
		 * an object from gc_get_network().
		 *
		 *
		 * @param null|false|GC_Network $network  Network value to return by path. Default null
		 *                                       to continue retrieving the network.
		 * @param string               $domain   The requested domain.
		 * @param string               $path     The requested path, in full.
		 * @param int|null             $segments The suggested number of paths to consult.
		 *                                       Default null, meaning the entire path was to be consulted.
		 * @param string[]             $paths    Array of paths to search for, based on `$path` and `$segments`.
		 */
		$pre = apply_filters( 'pre_get_network_by_path', null, $domain, $path, $segments, $paths );
		if ( null !== $pre ) {
			return $pre;
		}

		if ( ! $using_paths ) {
			$networks = get_networks(
				array(
					'number'     => 1,
					'orderby'    => array(
						'domain_length' => 'DESC',
					),
					'domain__in' => $domains,
				)
			);

			if ( ! empty( $networks ) ) {
				return array_shift( $networks );
			}

			return false;
		}

		$networks = get_networks(
			array(
				'orderby'    => array(
					'domain_length' => 'DESC',
					'path_length'   => 'DESC',
				),
				'domain__in' => $domains,
				'path__in'   => $paths,
			)
		);

		/*
		 * Domains are sorted by length of domain, then by length of path.
		 * The domain must match for the path to be considered. Otherwise,
		 * a network with the path of / will suffice.
		 */
		$found = false;
		foreach ( $networks as $network ) {
			if ( ( $network->domain === $domain ) || ( "www.{$network->domain}" === $domain ) ) {
				if ( in_array( $network->path, $paths, true ) ) {
					$found = true;
					break;
				}
			}
			if ( '/' === $network->path ) {
				$found = true;
				break;
			}
		}

		if ( true === $found ) {
			return $network;
		}

		return false;
	}
}
