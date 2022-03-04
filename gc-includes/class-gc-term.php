<?php
/**
 * Taxonomy API: GC_Term class
 *
 * @package GeChiUI
 * @subpackage Taxonomy
 *
 */

/**
 * Core class used to implement the GC_Term object.
 *
 *
 *
 * @property-read object $data Sanitized term data.
 */
final class GC_Term {

	/**
	 * Term ID.
	 *
	 * @var int
	 */
	public $term_id;

	/**
	 * The term's name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The term's slug.
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * The term's term_group.
	 *
	 * @var int
	 */
	public $term_group = '';

	/**
	 * Term Taxonomy ID.
	 *
	 * @var int
	 */
	public $term_taxonomy_id = 0;

	/**
	 * The term's taxonomy name.
	 *
	 * @var string
	 */
	public $taxonomy = '';

	/**
	 * The term's description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * ID of a term's parent term.
	 *
	 * @var int
	 */
	public $parent = 0;

	/**
	 * Cached object count for this term.
	 *
	 * @var int
	 */
	public $count = 0;

	/**
	 * Stores the term object's sanitization level.
	 *
	 * Does not correspond to a database field.
	 *
	 * @var string
	 */
	public $filter = 'raw';

	/**
	 * Retrieve GC_Term instance.
	 *
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Optional. Limit matched terms to those matching `$taxonomy`. Only used for
	 *                         disambiguating potentially shared terms.
	 * @return GC_Term|GC_Error|false Term object, if found. GC_Error if `$term_id` is shared between taxonomies and
	 *                                there's insufficient data to distinguish which term is intended.
	 *                                False for other failures.
	 */
	public static function get_instance( $term_id, $taxonomy = null ) {
		global $gcdb;

		$term_id = (int) $term_id;
		if ( ! $term_id ) {
			return false;
		}

		$_term = gc_cache_get( $term_id, 'terms' );

		// If there isn't a cached version, hit the database.
		if ( ! $_term || ( $taxonomy && $taxonomy !== $_term->taxonomy ) ) {
			// Any term found in the cache is not a match, so don't use it.
			$_term = false;

			// Grab all matching terms, in case any are shared between taxonomies.
			$terms = $gcdb->get_results( $gcdb->prepare( "SELECT t.*, tt.* FROM $gcdb->terms AS t INNER JOIN $gcdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE t.term_id = %d", $term_id ) );
			if ( ! $terms ) {
				return false;
			}

			// If a taxonomy was specified, find a match.
			if ( $taxonomy ) {
				foreach ( $terms as $match ) {
					if ( $taxonomy === $match->taxonomy ) {
						$_term = $match;
						break;
					}
				}

				// If only one match was found, it's the one we want.
			} elseif ( 1 === count( $terms ) ) {
				$_term = reset( $terms );

				// Otherwise, the term must be shared between taxonomies.
			} else {
				// If the term is shared only with invalid taxonomies, return the one valid term.
				foreach ( $terms as $t ) {
					if ( ! taxonomy_exists( $t->taxonomy ) ) {
						continue;
					}

					// Only hit if we've already identified a term in a valid taxonomy.
					if ( $_term ) {
						return new GC_Error( 'ambiguous_term_id', __( '项目ID在多个分类法间共享' ), $term_id );
					}

					$_term = $t;
				}
			}

			if ( ! $_term ) {
				return false;
			}

			// Don't return terms from invalid taxonomies.
			if ( ! taxonomy_exists( $_term->taxonomy ) ) {
				return new GC_Error( 'invalid_taxonomy', __( '无效分类法。' ) );
			}

			$_term = sanitize_term( $_term, $_term->taxonomy, 'raw' );

			// Don't cache terms that are shared between taxonomies.
			if ( 1 === count( $terms ) ) {
				gc_cache_add( $term_id, $_term, 'terms' );
			}
		}

		$term_obj = new GC_Term( $_term );
		$term_obj->filter( $term_obj->filter );

		return $term_obj;
	}

	/**
	 * Constructor.
	 *
	 *
	 * @param GC_Term|object $term Term object.
	 */
	public function __construct( $term ) {
		foreach ( get_object_vars( $term ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Sanitizes term fields, according to the filter type provided.
	 *
	 *
	 * @param string $filter Filter context. Accepts 'edit', 'db', 'display', 'attribute', 'js', 'rss', or 'raw'.
	 */
	public function filter( $filter ) {
		sanitize_term( $this, $this->taxonomy, $filter );
	}

	/**
	 * Converts an object to array.
	 *
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/**
	 * Getter.
	 *
	 *
	 * @param string $key Property to get.
	 * @return mixed Property value.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'data':
				$data    = new stdClass();
				$columns = array( 'term_id', 'name', 'slug', 'term_group', 'term_taxonomy_id', 'taxonomy', 'description', 'parent', 'count' );
				foreach ( $columns as $column ) {
					$data->{$column} = isset( $this->{$column} ) ? $this->{$column} : null;
				}

				return sanitize_term( $data, $data->taxonomy, 'raw' );
		}
	}
}
