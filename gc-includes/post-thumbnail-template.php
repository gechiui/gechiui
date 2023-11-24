<?php
/**
 * GeChiUI Post Thumbnail Template Functions.
 *
 * Support for post thumbnails.
 * Theme's functions.php must call add_theme_support( 'post-thumbnails' ) to use these.
 *
 * @package GeChiUI
 * @subpackage Template
 */

/**
 * Determines whether a post has an image attached.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 * `$post` can be a post ID or GC_Post object.
 *
 * @param int|GC_Post $post Optional. Post ID or GC_Post object. Default is global `$post`.
 * @return bool Whether the post has an image attached.
 */
function has_post_thumbnail( $post = null ) {
	$thumbnail_id  = get_post_thumbnail_id( $post );
	$has_thumbnail = (bool) $thumbnail_id;

	/**
	 * Filters whether a post has a post thumbnail.
	 *
	 * @since 5.1.0
	 *
	 * @param bool             $has_thumbnail true if the post has a post thumbnail, otherwise false.
	 * @param int|GC_Post|null $post          Post ID or GC_Post object. Default is global `$post`.
	 * @param int|false        $thumbnail_id  Post thumbnail ID or false if the post does not exist.
	 */
	return (bool) apply_filters( 'has_post_thumbnail', $has_thumbnail, $post, $thumbnail_id );
}

/**
 * Retrieves the post thumbnail ID.
 * `$post` can be a post ID or GC_Post object.
 * @since 5.5.0 The return value for a non-existing post
 *              was changed to false instead of an empty string.
 *
 * @param int|GC_Post $post Optional. Post ID or GC_Post object. Default is global `$post`.
 * @return int|false Post thumbnail ID (which can be 0 if the thumbnail is not set),
 *                   or false if the post does not exist.
 */
function get_post_thumbnail_id( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$thumbnail_id = (int) get_post_meta( $post->ID, '_thumbnail_id', true );

	/**
	 * Filters the post thumbnail ID.
	 *
	 * @since 5.9.0
	 *
	 * @param int|false        $thumbnail_id Post thumbnail ID or false if the post does not exist.
	 * @param int|GC_Post|null $post         Post ID or GC_Post object. Default is global `$post`.
	 */
	return (int) apply_filters( 'post_thumbnail_id', $thumbnail_id, $post );
}

/**
 * Displays the post thumbnail.
 *
 * When a theme adds 'post-thumbnail' support, a special 'post-thumbnail' image size
 * is registered, which differs from the 'thumbnail' image size managed via the
 * Settings > Media screen.
 *
 * When using the_post_thumbnail() or related functions, the 'post-thumbnail' image
 * size is used by default, though a different size can be specified instead as needed.
 *
 * @see get_the_post_thumbnail()
 *
 * @param string|int[] $size Optional. Image size. Accepts any registered image size name, or an array of
 *                           width and height values in pixels (in that order). Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 */
function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	echo get_the_post_thumbnail( null, $size, $attr );
}

/**
 * Updates cache for thumbnails in the current loop.
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param GC_Query $gc_query Optional. A GC_Query instance. Defaults to the $gc_query global.
 */
function update_post_thumbnail_cache( $gc_query = null ) {
	if ( ! $gc_query ) {
		$gc_query = $GLOBALS['gc_query'];
	}

	if ( $gc_query->thumbnails_cached ) {
		return;
	}

	$thumb_ids = array();

	foreach ( $gc_query->posts as $post ) {
		$id = get_post_thumbnail_id( $post->ID );
		if ( $id ) {
			$thumb_ids[] = $id;
		}
	}

	if ( ! empty( $thumb_ids ) ) {
		_prime_post_caches( $thumb_ids, false, true );
	}

	$gc_query->thumbnails_cached = true;
}

/**
 * Retrieves the post thumbnail.
 *
 * When a theme adds 'post-thumbnail' support, a special 'post-thumbnail' image size
 * is registered, which differs from the 'thumbnail' image size managed via the
 * Settings > Media screen.
 *
 * When using the_post_thumbnail() or related functions, the 'post-thumbnail' image
 * size is used by default, though a different size can be specified instead as needed.
 * `$post` can be a post ID or GC_Post object.
 *
 * @param int|GC_Post  $post Optional. Post ID or GC_Post object.  Default is global `$post`.
 * @param string|int[] $size Optional. Image size. Accepts any registered image size name, or an array of
 *                           width and height values in pixels (in that order). Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 * @return string The post thumbnail image tag.
 */
function get_the_post_thumbnail( $post = null, $size = 'post-thumbnail', $attr = '' ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$post_thumbnail_id = get_post_thumbnail_id( $post );

	/**
	 * Filters the post thumbnail size.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$post_id` parameter.
	 *
	 * @param string|int[] $size    Requested image size. Can be any registered image size name, or
	 *                              an array of width and height values in pixels (in that order).
	 * @param int          $post_id The post ID.
	 */
	$size = apply_filters( 'post_thumbnail_size', $size, $post->ID );

	if ( $post_thumbnail_id ) {

		/**
		 * Fires before fetching the post thumbnail HTML.
		 *
		 * Provides "just in time" filtering of all filters in gc_get_attachment_image().
		 *
		 * @param int          $post_id           The post ID.
		 * @param int          $post_thumbnail_id The post thumbnail ID.
		 * @param string|int[] $size              Requested image size. Can be any registered image size name, or
		 *                                        an array of width and height values in pixels (in that order).
		 */
		do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );

		if ( in_the_loop() ) {
			update_post_thumbnail_cache();
		}

		$html = gc_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

		/**
		 * Fires after fetching the post thumbnail HTML.
		 *
		 * @param int          $post_id           The post ID.
		 * @param int          $post_thumbnail_id The post thumbnail ID.
		 * @param string|int[] $size              Requested image size. Can be any registered image size name, or
		 *                                        an array of width and height values in pixels (in that order).
		 */
		do_action( 'end_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );

	} else {
		$html = '';
	}

	/**
	 * Filters the post thumbnail HTML.
	 *
	 * @since 2.9.0
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param int          $post_thumbnail_id The post thumbnail ID, or 0 if there isn't one.
	 * @param string|int[] $size              Requested image size. Can be any registered image size name, or
	 *                                        an array of width and height values in pixels (in that order).
	 * @param string|array $attr              Query string or array of attributes.
	 */
	return apply_filters( 'post_thumbnail_html', $html, $post->ID, $post_thumbnail_id, $size, $attr );
}

/**
 * Returns the post thumbnail URL.
 *
 * @param int|GC_Post  $post Optional. Post ID or GC_Post object.  Default is global `$post`.
 * @param string|int[] $size Optional. Registered image size to retrieve the source for or a flat array
 *                           of height and width dimensions. Default 'post-thumbnail'.
 * @return string|false Post thumbnail URL or false if no image is available. If `$size` does not match
 *                      any registered image size, the original image URL will be returned.
 */
function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	if ( ! $post_thumbnail_id ) {
		return false;
	}

	$thumbnail_url = gc_get_attachment_image_url( $post_thumbnail_id, $size );

	/**
	 * Filters the post thumbnail URL.
	 *
	 * @since 5.9.0
	 *
	 * @param string|false     $thumbnail_url Post thumbnail URL or false if the post does not exist.
	 * @param int|GC_Post|null $post          Post ID or GC_Post object. Default is global `$post`.
	 * @param string|int[]     $size          Registered image size to retrieve the source for or a flat array
	 *                                        of height and width dimensions. Default 'post-thumbnail'.
	 */
	return apply_filters( 'post_thumbnail_url', $thumbnail_url, $post, $size );
}

/**
 * Displays the post thumbnail URL.
 *
 * @param string|int[] $size Optional. Image size to use. Accepts any valid image size,
 *                           or an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 */
function the_post_thumbnail_url( $size = 'post-thumbnail' ) {
	$url = get_the_post_thumbnail_url( null, $size );

	if ( $url ) {
		echo esc_url( $url );
	}
}

/**
 * Returns the post thumbnail caption.
 *
 * @param int|GC_Post $post Optional. Post ID or GC_Post object. Default is global `$post`.
 * @return string Post thumbnail caption.
 */
function get_the_post_thumbnail_caption( $post = null ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	if ( ! $post_thumbnail_id ) {
		return '';
	}

	$caption = gc_get_attachment_caption( $post_thumbnail_id );

	if ( ! $caption ) {
		$caption = '';
	}

	return $caption;
}

/**
 * Displays the post thumbnail caption.
 *
 * @param int|GC_Post $post Optional. Post ID or GC_Post object. Default is global `$post`.
 */
function the_post_thumbnail_caption( $post = null ) {
	/**
	 * Filters the displayed post thumbnail caption.
	 *
	 * @since 4.6.0
	 *
	 * @param string $caption Caption for the given attachment.
	 */
	echo apply_filters( 'the_post_thumbnail_caption', get_the_post_thumbnail_caption( $post ) );
}
