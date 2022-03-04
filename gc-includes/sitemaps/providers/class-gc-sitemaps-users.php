<?php
/**
 * Sitemaps: GC_Sitemaps_Users class
 *
 * Builds the sitemaps for the 'user' object type.
 *
 * @package GeChiUI
 * @subpackage Sitemaps
 *
 */

/**
 * Users XML sitemap provider.
 *
 *
 */
class GC_Sitemaps_Users extends GC_Sitemaps_Provider {
	/**
	 * GC_Sitemaps_Users constructor.
	 *
	 */
	public function __construct() {
		$this->name        = 'users';
		$this->object_type = 'user';
	}

	/**
	 * Gets a URL list for a user sitemap.
	 *
	 *
	 * @param int    $page_num       Page of results.
	 * @param string $object_subtype Optional. Not applicable for Users but
	 *                               required for compatibility with the parent
	 *                               provider class. Default empty.
	 * @return array[] Array of URL information for a sitemap.
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		/**
		 * Filters the users URL list before it is generated.
		 *
		 * Returning a non-null value will effectively short-circuit the generation,
		 * returning that value instead.
		 *
		 *
		 * @param array[]|null $url_list The URL list. Default null.
		 * @param int        $page_num Page of results.
		 */
		$url_list = apply_filters(
			'gc_sitemaps_users_pre_url_list',
			null,
			$page_num
		);

		if ( null !== $url_list ) {
			return $url_list;
		}

		$args          = $this->get_users_query_args();
		$args['paged'] = $page_num;

		$query    = new GC_User_Query( $args );
		$users    = $query->get_results();
		$url_list = array();

		foreach ( $users as $user ) {
			$sitemap_entry = array(
				'loc' => get_author_posts_url( $user->ID ),
			);

			/**
			 * Filters the sitemap entry for an individual user.
			 *
		
			 *
			 * @param array   $sitemap_entry Sitemap entry for the user.
			 * @param GC_User $user          User object.
			 */
			$sitemap_entry = apply_filters( 'gc_sitemaps_users_entry', $sitemap_entry, $user );
			$url_list[]    = $sitemap_entry;
		}

		return $url_list;
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 *
	 * @see GC_Sitemaps_Provider::max_num_pages
	 *
	 * @param string $object_subtype Optional. Not applicable for Users but
	 *                               required for compatibility with the parent
	 *                               provider class. Default empty.
	 * @return int Total page count.
	 */
	public function get_max_num_pages( $object_subtype = '' ) {
		/**
		 * Filters the max number of pages for a user sitemap before it is generated.
		 *
		 * Returning a non-null value will effectively short-circuit the generation,
		 * returning that value instead.
		 *
		 *
		 * @param int|null $max_num_pages The maximum number of pages. Default null.
		 */
		$max_num_pages = apply_filters( 'gc_sitemaps_users_pre_max_num_pages', null );

		if ( null !== $max_num_pages ) {
			return $max_num_pages;
		}

		$args  = $this->get_users_query_args();
		$query = new GC_User_Query( $args );

		$total_users = $query->get_total();

		return (int) ceil( $total_users / gc_sitemaps_get_max_urls( $this->object_type ) );
	}

	/**
	 * Returns the query args for retrieving users to list in the sitemap.
	 *
	 *
	 * @return array Array of GC_User_Query arguments.
	 */
	protected function get_users_query_args() {
		$public_post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		// We're not supporting sitemaps for author pages for attachments.
		unset( $public_post_types['attachment'] );

		/**
		 * Filters the query arguments for authors with public posts.
		 *
		 * Allows modification of the authors query arguments before querying.
		 *
		 * @see GC_User_Query for a full list of arguments
		 *
		 *
		 * @param array $args Array of GC_User_Query arguments.
		 */
		$args = apply_filters(
			'gc_sitemaps_users_query_args',
			array(
				'has_published_posts' => array_keys( $public_post_types ),
				'number'              => gc_sitemaps_get_max_urls( $this->object_type ),
			)
		);

		return $args;
	}
}
