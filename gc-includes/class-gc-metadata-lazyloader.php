<?php
/**
 * Meta API: GC_Metadata_Lazyloader class
 *
 * @package GeChiUI
 * @subpackage Meta
 *
 */

/**
 * Core class used for lazy-loading object metadata.
 *
 * When loading many objects of a given type, such as posts in a GC_Query loop, it often makes
 * sense to prime various metadata caches at the beginning of the loop. This means fetching all
 * relevant metadata with a single database query, a technique that has the potential to improve
 * performance dramatically in some cases.
 *
 * In cases where the given metadata may not even be used in the loop, we can improve performance
 * even more by only priming the metadata cache for affected items the first time a piece of metadata
 * is requested - ie, by lazy-loading it. So, for example, comment meta may not be loaded into the
 * cache in the comments section of a post until the first time get_comment_meta() is called in the
 * context of the comment loop.
 *
 * GC uses the GC_Metadata_Lazyloader class to queue objects for metadata cache priming. The class
 * then detects the relevant get_*_meta() function call, and queries the metadata of all queued objects.
 *
 * Do not access this class directly. Use the gc_metadata_lazyloader() function.
 *
 *
 */
class GC_Metadata_Lazyloader {
	/**
	 * Pending objects queue.
	 *
	 * @var array
	 */
	protected $pending_objects;

	/**
	 * Settings for supported object types.
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->settings = array(
			'term'    => array(
				'filter'   => 'get_term_metadata',
				'callback' => array( $this, 'lazyload_term_meta' ),
			),
			'comment' => array(
				'filter'   => 'get_comment_metadata',
				'callback' => array( $this, 'lazyload_comment_meta' ),
			),
		);
	}

	/**
	 * Adds objects to the metadata lazy-load queue.
	 *
	 *
	 * @param string $object_type Type of object whose meta is to be lazy-loaded. Accepts 'term' or 'comment'.
	 * @param array  $object_ids  Array of object IDs.
	 * @return void|GC_Error GC_Error on failure.
	 */
	public function queue_objects( $object_type, $object_ids ) {
		if ( ! isset( $this->settings[ $object_type ] ) ) {
			return new GC_Error( 'invalid_object_type', __( '无效对象类型。' ) );
		}

		$type_settings = $this->settings[ $object_type ];

		if ( ! isset( $this->pending_objects[ $object_type ] ) ) {
			$this->pending_objects[ $object_type ] = array();
		}

		foreach ( $object_ids as $object_id ) {
			// Keyed by ID for faster lookup.
			if ( ! isset( $this->pending_objects[ $object_type ][ $object_id ] ) ) {
				$this->pending_objects[ $object_type ][ $object_id ] = 1;
			}
		}

		add_filter( $type_settings['filter'], $type_settings['callback'] );

		/**
		 * Fires after objects are added to the metadata lazy-load queue.
		 *
		 *
		 * @param array                  $object_ids  Array of object IDs.
		 * @param string                 $object_type Type of object being queued.
		 * @param GC_Metadata_Lazyloader $lazyloader  The lazy-loader object.
		 */
		do_action( 'metadata_lazyloader_queued_objects', $object_ids, $object_type, $this );
	}

	/**
	 * Resets lazy-load queue for a given object type.
	 *
	 *
	 * @param string $object_type Object type. Accepts 'comment' or 'term'.
	 * @return void|GC_Error GC_Error on failure.
	 */
	public function reset_queue( $object_type ) {
		if ( ! isset( $this->settings[ $object_type ] ) ) {
			return new GC_Error( 'invalid_object_type', __( '无效对象类型。' ) );
		}

		$type_settings = $this->settings[ $object_type ];

		$this->pending_objects[ $object_type ] = array();
		remove_filter( $type_settings['filter'], $type_settings['callback'] );
	}

	/**
	 * Lazy-loads term meta for queued terms.
	 *
	 * This method is public so that it can be used as a filter callback. As a rule, there
	 * is no need to invoke it directly.
	 *
	 *
	 * @param mixed $check The `$check` param passed from the 'get_term_metadata' hook.
	 * @return mixed In order not to short-circuit `get_metadata()`. Generally, this is `null`, but it could be
	 *               another value if filtered by a plugin.
	 */
	public function lazyload_term_meta( $check ) {
		if ( ! empty( $this->pending_objects['term'] ) ) {
			update_termmeta_cache( array_keys( $this->pending_objects['term'] ) );

			// No need to run again for this set of terms.
			$this->reset_queue( 'term' );
		}

		return $check;
	}

	/**
	 * Lazy-loads comment meta for queued comments.
	 *
	 * This method is public so that it can be used as a filter callback. As a rule, there is no need to invoke it
	 * directly, from either inside or outside the `GC_Query` object.
	 *
	 *
	 * @param mixed $check The `$check` param passed from the {@see 'get_comment_metadata'} hook.
	 * @return mixed The original value of `$check`, so as not to short-circuit `get_comment_metadata()`.
	 */
	public function lazyload_comment_meta( $check ) {
		if ( ! empty( $this->pending_objects['comment'] ) ) {
			update_meta_cache( 'comment', array_keys( $this->pending_objects['comment'] ) );

			// No need to run again for this set of comments.
			$this->reset_queue( 'comment' );
		}

		return $check;
	}
}
