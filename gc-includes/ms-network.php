<?php
/**
 * Network API
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/**
 * Retrieves network data given a network ID or network object.
 *
 * Network data will be cached and returned after being passed through a filter.
 * If the provided network is empty, the current network global will be used.
 *
 *
 *
 * @global GC_Network $current_site
 *
 * @param GC_Network|int|null $network Optional. Network to retrieve. Default is the current network.
 * @return GC_Network|null The network object or null if not found.
 */
function get_network( $network = null ) {
	global $current_site;
	if ( empty( $network ) && isset( $current_site ) ) {
		$network = $current_site;
	}

	if ( $network instanceof GC_Network ) {
		$_network = $network;
	} elseif ( is_object( $network ) ) {
		$_network = new GC_Network( $network );
	} else {
		$_network = GC_Network::get_instance( $network );
	}

	if ( ! $_network ) {
		return null;
	}

	/**
	 * Fires after a network is retrieved.
	 *
	 *
	 * @param GC_Network $_network Network data.
	 */
	$_network = apply_filters( 'get_network', $_network );

	return $_network;
}

/**
 * Retrieves a list of networks.
 *
 *
 *
 * @param string|array $args Optional. Array or string of arguments. See GC_Network_Query::parse_query()
 *                           for information on accepted arguments. Default empty array.
 * @return array|int List of GC_Network objects, a list of network IDs when 'fields' is set to 'ids',
 *                   or the number of networks when 'count' is passed as a query var.
 */
function get_networks( $args = array() ) {
	$query = new GC_Network_Query();

	return $query->query( $args );
}

/**
 * Removes a network from the object cache.
 *
 *
 *
 * @global bool $_gc_suspend_cache_invalidation
 *
 * @param int|array $ids Network ID or an array of network IDs to remove from cache.
 */
function clean_network_cache( $ids ) {
	global $_gc_suspend_cache_invalidation;

	if ( ! empty( $_gc_suspend_cache_invalidation ) ) {
		return;
	}

	foreach ( (array) $ids as $id ) {
		gc_cache_delete( $id, 'networks' );

		/**
		 * Fires immediately after a network has been removed from the object cache.
		 *
		 *
		 * @param int $id Network ID.
		 */
		do_action( 'clean_network_cache', $id );
	}

	gc_cache_set( 'last_changed', microtime(), 'networks' );
}

/**
 * Updates the network cache of given networks.
 *
 * Will add the networks in $networks to the cache. If network ID already exists
 * in the network cache then it will not be updated. The network is added to the
 * cache using the network group with the key using the ID of the networks.
 *
 *
 *
 * @param array $networks Array of network row objects.
 */
function update_network_cache( $networks ) {
	foreach ( (array) $networks as $network ) {
		gc_cache_add( $network->id, $network, 'networks' );
	}
}

/**
 * Adds any networks from the given IDs to the cache that do not already exist in cache.
 *
 *
 * @access private
 *
 * @see update_network_cache()
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param array $network_ids Array of network IDs.
 */
function _prime_network_caches( $network_ids ) {
	global $gcdb;

	$non_cached_ids = _get_non_cached_ids( $network_ids, 'networks' );
	if ( ! empty( $non_cached_ids ) ) {
		$fresh_networks = $gcdb->get_results( sprintf( "SELECT $gcdb->site.* FROM $gcdb->site WHERE id IN (%s)", implode( ',', array_map( 'intval', $non_cached_ids ) ) ) ); // phpcs:ignore GeChiUI.DB.PreparedSQL.NotPrepared

		update_network_cache( $fresh_networks );
	}
}
