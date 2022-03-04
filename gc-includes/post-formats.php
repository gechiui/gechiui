<?php
/**
 * Post format functions.
 *
 * @package GeChiUI
 * @subpackage Post
 */

/**
 * Retrieve the format slug for a post
 *
 *
 *
 * @param int|GC_Post|null $post Optional. Post ID or post object. Defaults to the current post in the loop.
 * @return string|false The format if successful. False otherwise.
 */
function get_post_format( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	if ( ! post_type_supports( $post->post_type, 'post-formats' ) ) {
		return false;
	}

	$_format = get_the_terms( $post->ID, 'post_format' );

	if ( empty( $_format ) ) {
		return false;
	}

	$format = reset( $_format );

	return str_replace( 'post-format-', '', $format->slug );
}

/**
 * Check if a post has any of the given formats, or any format.
 *
 *
 *
 * @param string|string[]  $format Optional. The format or formats to check.
 * @param GC_Post|int|null $post   Optional. The post to check. Defaults to the current post in the loop.
 * @return bool True if the post has any of the given formats (or any format, if no format specified),
 *              false otherwise.
 */
function has_post_format( $format = array(), $post = null ) {
	$prefixed = array();

	if ( $format ) {
		foreach ( (array) $format as $single ) {
			$prefixed[] = 'post-format-' . sanitize_key( $single );
		}
	}

	return has_term( $prefixed, 'post_format', $post );
}

/**
 * Assign a format to a post
 *
 *
 *
 * @param int|object $post   The post for which to assign a format.
 * @param string     $format A format to assign. Use an empty string or array to remove all formats from the post.
 * @return array|GC_Error|false Array of affected term IDs on success. GC_Error on error.
 */
function set_post_format( $post, $format ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return new GC_Error( 'invalid_post', __( '无效文章。' ) );
	}

	if ( ! empty( $format ) ) {
		$format = sanitize_key( $format );
		if ( 'standard' === $format || ! in_array( $format, get_post_format_slugs(), true ) ) {
			$format = '';
		} else {
			$format = 'post-format-' . $format;
		}
	}

	return gc_set_post_terms( $post->ID, $format, 'post_format' );
}

/**
 * Returns an array of post format slugs to their translated and pretty display versions
 *
 *
 *
 * @return string[] Array of post format labels keyed by format slug.
 */
function get_post_format_strings() {
	$strings = array(
		'standard' => _x( '标准', 'Post format' ), // Special case. Any value that evals to false will be considered standard.
		'aside'    => _x( '日志', 'Post format' ),
		'chat'     => _x( '聊天', 'Post format' ),
		'gallery'  => _x( '画廊', 'Post format' ),
		'link'     => _x( '链接', 'Post format' ),
		'image'    => _x( '图片', 'Post format' ),
		'quote'    => _x( '引语', 'Post format' ),
		'status'   => _x( '状态', 'Post format' ),
		'video'    => _x( '视频', 'Post format' ),
		'audio'    => _x( '音频', 'Post format' ),
	);
	return $strings;
}

/**
 * Retrieves the array of post format slugs.
 *
 *
 *
 * @return string[] The array of post format slugs as both keys and values.
 */
function get_post_format_slugs() {
	$slugs = array_keys( get_post_format_strings() );
	return array_combine( $slugs, $slugs );
}

/**
 * Returns a pretty, translated version of a post format slug
 *
 *
 *
 * @param string $slug A post format slug.
 * @return string The translated post format name.
 */
function get_post_format_string( $slug ) {
	$strings = get_post_format_strings();
	if ( ! $slug ) {
		return $strings['standard'];
	} else {
		return ( isset( $strings[ $slug ] ) ) ? $strings[ $slug ] : '';
	}
}

/**
 * Returns a link to a post format index.
 *
 *
 *
 * @param string $format The post format slug.
 * @return string|GC_Error|false The post format term link.
 */
function get_post_format_link( $format ) {
	$term = get_term_by( 'slug', 'post-format-' . $format, 'post_format' );
	if ( ! $term || is_gc_error( $term ) ) {
		return false;
	}
	return get_term_link( $term );
}

/**
 * Filters the request to allow for the format prefix.
 *
 * @access private
 *
 *
 * @param array $qvs
 * @return array
 */
function _post_format_request( $qvs ) {
	if ( ! isset( $qvs['post_format'] ) ) {
		return $qvs;
	}
	$slugs = get_post_format_slugs();
	if ( isset( $slugs[ $qvs['post_format'] ] ) ) {
		$qvs['post_format'] = 'post-format-' . $slugs[ $qvs['post_format'] ];
	}
	$tax = get_taxonomy( 'post_format' );
	if ( ! is_admin() ) {
		$qvs['post_type'] = $tax->object_type;
	}
	return $qvs;
}

/**
 * Filters the post format term link to remove the format prefix.
 *
 * @access private
 *
 *
 * @global GC_Rewrite $gc_rewrite GeChiUI rewrite component.
 *
 * @param string  $link
 * @param GC_Term $term
 * @param string  $taxonomy
 * @return string
 */
function _post_format_link( $link, $term, $taxonomy ) {
	global $gc_rewrite;
	if ( 'post_format' !== $taxonomy ) {
		return $link;
	}
	if ( $gc_rewrite->get_extra_permastruct( $taxonomy ) ) {
		return str_replace( "/{$term->slug}", '/' . str_replace( 'post-format-', '', $term->slug ), $link );
	} else {
		$link = remove_query_arg( 'post_format', $link );
		return add_query_arg( 'post_format', str_replace( 'post-format-', '', $term->slug ), $link );
	}
}

/**
 * Remove the post format prefix from the name property of the term object created by get_term().
 *
 * @access private
 *
 *
 * @param object $term
 * @return object
 */
function _post_format_get_term( $term ) {
	if ( isset( $term->slug ) ) {
		$term->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
	}
	return $term;
}

/**
 * Remove the post format prefix from the name property of the term objects created by get_terms().
 *
 * @access private
 *
 *
 * @param array        $terms
 * @param string|array $taxonomies
 * @param array        $args
 * @return array
 */
function _post_format_get_terms( $terms, $taxonomies, $args ) {
	if ( in_array( 'post_format', (array) $taxonomies, true ) ) {
		if ( isset( $args['fields'] ) && 'names' === $args['fields'] ) {
			foreach ( $terms as $order => $name ) {
				$terms[ $order ] = get_post_format_string( str_replace( 'post-format-', '', $name ) );
			}
		} else {
			foreach ( (array) $terms as $order => $term ) {
				if ( isset( $term->taxonomy ) && 'post_format' === $term->taxonomy ) {
					$terms[ $order ]->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
				}
			}
		}
	}
	return $terms;
}

/**
 * Remove the post format prefix from the name property of the term objects created by gc_get_object_terms().
 *
 * @access private
 *
 *
 * @param array $terms
 * @return array
 */
function _post_format_gc_get_object_terms( $terms ) {
	foreach ( (array) $terms as $order => $term ) {
		if ( isset( $term->taxonomy ) && 'post_format' === $term->taxonomy ) {
			$terms[ $order ]->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
		}
	}
	return $terms;
}
