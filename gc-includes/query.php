<?php
/**
 * GeChiUI Query API
 *
 * The query API attempts to get which part of GeChiUI the user is on. It
 * also provides functionality for getting URL query information.
 *
 * @link https://developer.gechiui.com/themes/basics/the-loop/ More information on The Loop.
 *
 * @package GeChiUI
 * @subpackage Query
 */

/**
 * Retrieves the value of a query variable in the GC_Query class.
 *
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string $var       The variable key to retrieve.
 * @param mixed  $default   Optional. Value to return if the query variable is not set. Default empty.
 * @return mixed Contents of the query variable.
 */
function get_query_var( $var, $default = '' ) {
	global $gc_query;
	return $gc_query->get( $var, $default );
}

/**
 * Retrieves the currently queried object.
 *
 * Wrapper for GC_Query::get_queried_object().
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return GC_Term|GC_Post_Type|GC_Post|GC_User|null The queried object.
 */
function get_queried_object() {
	global $gc_query;
	return $gc_query->get_queried_object();
}

/**
 * Retrieves the ID of the currently queried object.
 *
 * Wrapper for GC_Query::get_queried_object_id().
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return int ID of the queried object.
 */
function get_queried_object_id() {
	global $gc_query;
	return $gc_query->get_queried_object_id();
}

/**
 * Sets the value of a query variable in the GC_Query class.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string $var   Query variable key.
 * @param mixed  $value Query variable value.
 */
function set_query_var( $var, $value ) {
	global $gc_query;
	$gc_query->set( $var, $value );
}

/**
 * Sets up The Loop with query parameters.
 *
 * Note: This function will completely override the main query and isn't intended for use
 * by plugins or themes. Its overly-simplistic approach to modifying the main query can be
 * problematic and should be avoided wherever possible. In most cases, there are better,
 * more performant options for modifying the main query such as via the {@see 'pre_get_posts'}
 * action within GC_Query.
 *
 * This must not be used within the GeChiUI Loop.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param array|string $query Array or string of GC_Query arguments.
 * @return GC_Post[]|int[] Array of post objects or post IDs.
 */
function query_posts( $query ) {
	$GLOBALS['gc_query'] = new GC_Query();
	return $GLOBALS['gc_query']->query( $query );
}

/**
 * Destroys the previous query and sets up a new query.
 *
 * This should be used after query_posts() and before another query_posts().
 * This will remove obscure bugs that occur when the previous GC_Query object
 * is not destroyed properly before another is set up.
 *
 *
 *
 * @global GC_Query $gc_query     GeChiUI Query object.
 * @global GC_Query $gc_the_query Copy of the global GC_Query instance created during gc_reset_query().
 */
function gc_reset_query() {
	$GLOBALS['gc_query'] = $GLOBALS['gc_the_query'];
	gc_reset_postdata();
}

/**
 * After looping through a separate query, this function restores
 * the $post global to the current post in the main query.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 */
function gc_reset_postdata() {
	global $gc_query;

	if ( isset( $gc_query ) ) {
		$gc_query->reset_postdata();
	}
}

/*
 * Query type checks.
 */

/**
 * Determines whether the query is for an existing archive page.
 *
 * Archive pages include category, tag, author, date, custom post type,
 * and custom taxonomy based archives.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @see is_category()
 * @see is_tag()
 * @see is_author()
 * @see is_date()
 * @see is_post_type_archive()
 * @see is_tax()
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an existing archive page.
 */
function is_archive() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_archive();
}

/**
 * Determines whether the query is for an existing post type archive page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string|string[] $post_types Optional. Post type or array of posts types
 *                                    to check against. Default empty.
 * @return bool Whether the query is for an existing post type archive page.
 */
function is_post_type_archive( $post_types = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_post_type_archive( $post_types );
}

/**
 * Determines whether the query is for an existing attachment page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $attachment Optional. Attachment ID, title, slug, or array of such
 *                                              to check against. Default empty.
 * @return bool Whether the query is for an existing attachment page.
 */
function is_attachment( $attachment = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_attachment( $attachment );
}

/**
 * Determines whether the query is for an existing author archive page.
 *
 * If the $author parameter is specified, this function will additionally
 * check if the query is for one of the authors specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $author Optional. User ID, nickname, nicename, or array of such
 *                                          to check against. Default empty.
 * @return bool Whether the query is for an existing author archive page.
 */
function is_author( $author = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_author( $author );
}

/**
 * Determines whether the query is for an existing category archive page.
 *
 * If the $category parameter is specified, this function will additionally
 * check if the query is for one of the categories specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $category Optional. Category ID, name, slug, or array of such
 *                                            to check against. Default empty.
 * @return bool Whether the query is for an existing category archive page.
 */
function is_category( $category = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_category( $category );
}

/**
 * Determines whether the query is for an existing tag archive page.
 *
 * If the $tag parameter is specified, this function will additionally
 * check if the query is for one of the tags specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $tag Optional. Tag ID, name, slug, or array of such
 *                                       to check against. Default empty.
 * @return bool Whether the query is for an existing tag archive page.
 */
function is_tag( $tag = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_tag( $tag );
}

/**
 * Determines whether the query is for an existing custom taxonomy archive page.
 *
 * If the $taxonomy parameter is specified, this function will additionally
 * check if the query is for that specific $taxonomy.
 *
 * If the $term parameter is specified in addition to the $taxonomy parameter,
 * this function will additionally check if the query is for one of the terms
 * specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string|string[]           $taxonomy Optional. Taxonomy slug or slugs to check against.
 *                                            Default empty.
 * @param int|string|int[]|string[] $term     Optional. Term ID, name, slug, or array of such
 *                                            to check against. Default empty.
 * @return bool Whether the query is for an existing custom taxonomy archive page.
 *              True for custom taxonomy archive pages, false for built-in taxonomies
 *              (category and tag archives).
 */
function is_tax( $taxonomy = '', $term = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_tax( $taxonomy, $term );
}

/**
 * Determines whether the query is for an existing date archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an existing date archive.
 */
function is_date() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_date();
}

/**
 * Determines whether the query is for an existing day archive.
 *
 * A conditional check to test whether the page is a date-based archive page displaying posts for the current day.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an existing day archive.
 */
function is_day() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_day();
}

/**
 * Determines whether the query is for a feed.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string|string[] $feeds Optional. Feed type or array of feed types
 *                                         to check against. Default empty.
 * @return bool Whether the query is for a feed.
 */
function is_feed( $feeds = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_feed( $feeds );
}

/**
 * Is the query for a comments feed?
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a comments feed.
 */
function is_comment_feed() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_comment_feed();
}

/**
 * Determines whether the query is for the front page of the site.
 *
 * This is for what is displayed at your site's main URL.
 *
 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_on_front'.
 *
 * If you set a static page for the front page of your site, this function will return
 * true when viewing that page.
 *
 * Otherwise the same as @see is_home()
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for the front page of the site.
 */
function is_front_page() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_front_page();
}

/**
 * Determines whether the query is for the blog homepage.
 *
 * The blog homepage is the page that shows the time-based blog content of the site.
 *
 * is_home() is dependent on the site's "Front page displays" Reading Settings 'show_on_front'
 * and 'page_for_posts'.
 *
 * If a static page is set for the front page of the site, this function will return true only
 * on the page you set as the "文章页".
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @see is_front_page()
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for the blog homepage.
 */
function is_home() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_home();
}

/**
 * Determines whether the query is for the Privacy Policy page.
 *
 * The Privacy Policy page is the page that shows the Privacy Policy content of the site.
 *
 * is_privacy_policy() is dependent on the site's "更改您的隐私政策页面" Privacy Settings 'gc_page_for_privacy_policy'.
 *
 * This function will return true only on the page you set as the "Privacy Policy page".
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for the Privacy Policy page.
 */
function is_privacy_policy() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_privacy_policy();
}

/**
 * Determines whether the query is for an existing month archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an existing month archive.
 */
function is_month() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_month();
}

/**
 * Determines whether the query is for an existing single page.
 *
 * If the $page parameter is specified, this function will additionally
 * check if the query is for one of the pages specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @see is_single()
 * @see is_singular()
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $page Optional. Page ID, title, slug, or array of such
 *                                        to check against. Default empty.
 * @return bool Whether the query is for an existing single page.
 */
function is_page( $page = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_page( $page );
}

/**
 * Determines whether the query is for a paged result and not for the first page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a paged result.
 */
function is_paged() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_paged();
}

/**
 * Determines whether the query is for a post or page preview.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a post or page preview.
 */
function is_preview() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_preview();
}

/**
 * Is the query for the robots.txt file?
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for the robots.txt file.
 */
function is_robots() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_robots();
}

/**
 * Is the query for the favicon.ico file?
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for the favicon.ico file.
 */
function is_favicon() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_favicon();
}

/**
 * Determines whether the query is for a search.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a search.
 */
function is_search() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_search();
}

/**
 * Determines whether the query is for an existing single post.
 *
 * Works for any post type, except attachments and pages
 *
 * If the $post parameter is specified, this function will additionally
 * check if the query is for one of the Posts specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @see is_page()
 * @see is_singular()
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param int|string|int[]|string[] $post Optional. Post ID, title, slug, or array of such
 *                                        to check against. Default empty.
 * @return bool Whether the query is for an existing single post.
 */
function is_single( $post = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_single( $post );
}

/**
 * Determines whether the query is for an existing single post of any post type
 * (post, attachment, page, custom post types).
 *
 * If the $post_types parameter is specified, this function will additionally
 * check if the query is for one of the Posts Types specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @see is_page()
 * @see is_single()
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param string|string[] $post_types Optional. Post type or array of post types
 *                                    to check against. Default empty.
 * @return bool Whether the query is for an existing single post
 *              or any of the given post types.
 */
function is_singular( $post_types = '' ) {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_singular( $post_types );
}

/**
 * Determines whether the query is for a specific time.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a specific time.
 */
function is_time() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_time();
}

/**
 * Determines whether the query is for a trackback endpoint call.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for a trackback endpoint call.
 */
function is_trackback() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_trackback();
}

/**
 * Determines whether the query is for an existing year archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an existing year archive.
 */
function is_year() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_year();
}

/**
 * Determines whether the query has resulted in a 404 (returns no results).
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is a 404 error.
 */
function is_404() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_404();
}

/**
 * Is the query for an embedded post?
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is for an embedded post.
 */
function is_embed() {
	global $gc_query;

	if ( ! isset( $gc_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( '条件标签在运行查询之前使用是无效的。这样做的话，返回值只会为false。' ), '3.1.0' );
		return false;
	}

	return $gc_query->is_embed();
}

/**
 * Determines whether the query is the main query.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool Whether the query is the main query.
 */
function is_main_query() {
	global $gc_query;

	if ( 'pre_get_posts' === current_filter() ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: pre_get_posts, 2: GC_Query->is_main_query(), 3: is_main_query(), 4: Documentation URL. */
				__( '在%1$s中，请使用%2$s方法，而不是%3$s函数。请参见%4$s。' ),
				'<code>pre_get_posts</code>',
				'<code>GC_Query->is_main_query()</code>',
				'<code>is_main_query()</code>',
				__( 'https://developer.gechiui.com/reference/functions/is_main_query/' )
			),
			'3.7.0'
		);
	}

	return $gc_query->is_main_query();
}

/*
 * The Loop. Post loop control.
 */

/**
 * Determines whether current GeChiUI query has posts to loop over.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool True if posts are available, false if end of the loop.
 */
function have_posts() {
	global $gc_query;
	return $gc_query->have_posts();
}

/**
 * Determines whether the caller is in the Loop.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool True if caller is within loop, false if loop hasn't started or ended.
 */
function in_the_loop() {
	global $gc_query;
	return $gc_query->in_the_loop;
}

/**
 * Rewind the loop posts.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 */
function rewind_posts() {
	global $gc_query;
	$gc_query->rewind_posts();
}

/**
 * Iterate the post index in the loop.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 */
function the_post() {
	global $gc_query;
	$gc_query->the_post();
}

/*
 * Comments loop.
 */

/**
 * Determines whether current GeChiUI query has comments to loop over.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return bool True if comments are available, false if no more comments.
 */
function have_comments() {
	global $gc_query;
	return $gc_query->have_comments();
}

/**
 * Iterate comment index in the comment loop.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @return null
 */
function the_comment() {
	global $gc_query;
	return $gc_query->the_comment();
}

/**
 * Redirect old slugs to the correct permalink.
 *
 * Attempts to find the current slug from the past slugs.
 *
 *
 */
function gc_old_slug_redirect() {
	if ( is_404() && '' !== get_query_var( 'name' ) ) {
		// Guess the current post type based on the query vars.
		if ( get_query_var( 'post_type' ) ) {
			$post_type = get_query_var( 'post_type' );
		} elseif ( get_query_var( 'attachment' ) ) {
			$post_type = 'attachment';
		} elseif ( get_query_var( 'pagename' ) ) {
			$post_type = 'page';
		} else {
			$post_type = 'post';
		}

		if ( is_array( $post_type ) ) {
			if ( count( $post_type ) > 1 ) {
				return;
			}
			$post_type = reset( $post_type );
		}

		// Do not attempt redirect for hierarchical post types.
		if ( is_post_type_hierarchical( $post_type ) ) {
			return;
		}

		$id = _find_post_by_old_slug( $post_type );

		if ( ! $id ) {
			$id = _find_post_by_old_date( $post_type );
		}

		/**
		 * Filters the old slug redirect post ID.
		 *
		 *
		 * @param int $id The redirect post ID.
		 */
		$id = apply_filters( 'old_slug_redirect_post_id', $id );

		if ( ! $id ) {
			return;
		}

		$link = get_permalink( $id );

		if ( get_query_var( 'paged' ) > 1 ) {
			$link = user_trailingslashit( trailingslashit( $link ) . 'page/' . get_query_var( 'paged' ) );
		} elseif ( is_embed() ) {
			$link = user_trailingslashit( trailingslashit( $link ) . 'embed' );
		}

		/**
		 * Filters the old slug redirect URL.
		 *
		 *
		 * @param string $link The redirect URL.
		 */
		$link = apply_filters( 'old_slug_redirect_url', $link );

		if ( ! $link ) {
			return;
		}

		gc_redirect( $link, 301 ); // Permanent redirect.
		exit;
	}
}

/**
 * Find the post ID for redirecting an old slug.
 *
 *
 * @access private
 *
 * @see gc_old_slug_redirect()
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $post_type The current post type based on the query vars.
 * @return int The Post ID.
 */
function _find_post_by_old_slug( $post_type ) {
	global $gcdb;

	$query = $gcdb->prepare( "SELECT post_id FROM $gcdb->postmeta, $gcdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = '_gc_old_slug' AND meta_value = %s", $post_type, get_query_var( 'name' ) );

	// If year, monthnum, or day have been specified, make our query more precise
	// just in case there are multiple identical _gc_old_slug values.
	if ( get_query_var( 'year' ) ) {
		$query .= $gcdb->prepare( ' AND YEAR(post_date) = %d', get_query_var( 'year' ) );
	}
	if ( get_query_var( 'monthnum' ) ) {
		$query .= $gcdb->prepare( ' AND MONTH(post_date) = %d', get_query_var( 'monthnum' ) );
	}
	if ( get_query_var( 'day' ) ) {
		$query .= $gcdb->prepare( ' AND DAYOFMONTH(post_date) = %d', get_query_var( 'day' ) );
	}

	$id = (int) $gcdb->get_var( $query );

	return $id;
}

/**
 * Find the post ID for redirecting an old date.
 *
 *
 * @access private
 *
 * @see gc_old_slug_redirect()
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $post_type The current post type based on the query vars.
 * @return int The Post ID.
 */
function _find_post_by_old_date( $post_type ) {
	global $gcdb;

	$date_query = '';
	if ( get_query_var( 'year' ) ) {
		$date_query .= $gcdb->prepare( ' AND YEAR(pm_date.meta_value) = %d', get_query_var( 'year' ) );
	}
	if ( get_query_var( 'monthnum' ) ) {
		$date_query .= $gcdb->prepare( ' AND MONTH(pm_date.meta_value) = %d', get_query_var( 'monthnum' ) );
	}
	if ( get_query_var( 'day' ) ) {
		$date_query .= $gcdb->prepare( ' AND DAYOFMONTH(pm_date.meta_value) = %d', get_query_var( 'day' ) );
	}

	$id = 0;
	if ( $date_query ) {
		$id = (int) $gcdb->get_var( $gcdb->prepare( "SELECT post_id FROM $gcdb->postmeta AS pm_date, $gcdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = '_gc_old_date' AND post_name = %s" . $date_query, $post_type, get_query_var( 'name' ) ) );

		if ( ! $id ) {
			// Check to see if an old slug matches the old date.
			$id = (int) $gcdb->get_var( $gcdb->prepare( "SELECT ID FROM $gcdb->posts, $gcdb->postmeta AS pm_slug, $gcdb->postmeta AS pm_date WHERE ID = pm_slug.post_id AND ID = pm_date.post_id AND post_type = %s AND pm_slug.meta_key = '_gc_old_slug' AND pm_slug.meta_value = %s AND pm_date.meta_key = '_gc_old_date'" . $date_query, $post_type, get_query_var( 'name' ) ) );
		}
	}

	return $id;
}

/**
 * Set up global post data.
 *
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param GC_Post|object|int $post GC_Post instance or Post ID/object.
 * @return bool True when finished.
 */
function setup_postdata( $post ) {
	global $gc_query;

	if ( ! empty( $gc_query ) && $gc_query instanceof GC_Query ) {
		return $gc_query->setup_postdata( $post );
	}

	return false;
}

/**
 * Generates post data.
 *
 *
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 * @param GC_Post|object|int $post GC_Post instance or Post ID/object.
 * @return array|false Elements of post, or false on failure.
 */
function generate_postdata( $post ) {
	global $gc_query;

	if ( ! empty( $gc_query ) && $gc_query instanceof GC_Query ) {
		return $gc_query->generate_postdata( $post );
	}

	return false;
}
