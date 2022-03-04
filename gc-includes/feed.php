<?php
/**
 * GeChiUI Feed API
 *
 * Many of the functions used in here belong in The Loop, or The Loop for the
 * Feeds.
 *
 * @package GeChiUI
 * @subpackage Feed
 *
 */

/**
 * RSS container for the bloginfo function.
 *
 * You can retrieve anything that you can using the get_bloginfo() function.
 * Everything will be stripped of tags and characters converted, when the values
 * are retrieved for use in the feeds.
 *
 *
 *
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 * @return string
 */
function get_bloginfo_rss( $show = '' ) {
	$info = strip_tags( get_bloginfo( $show ) );
	/**
	 * Filters the bloginfo for use in RSS feeds.
	 *
	 *
	 * @see convert_chars()
	 * @see get_bloginfo()
	 *
	 * @param string $info Converted string value of the blog information.
	 * @param string $show The type of blog information to retrieve.
	 */
	return apply_filters( 'get_bloginfo_rss', convert_chars( $info ), $show );
}

/**
 * Display RSS container for the bloginfo function.
 *
 * You can retrieve anything that you can using the get_bloginfo() function.
 * Everything will be stripped of tags and characters converted, when the values
 * are retrieved for use in the feeds.
 *
 *
 *
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 */
function bloginfo_rss( $show = '' ) {
	/**
	 * Filters the bloginfo for display in RSS feeds.
	 *
	 *
	 * @see get_bloginfo()
	 *
	 * @param string $rss_container RSS container for the blog information.
	 * @param string $show          The type of blog information to retrieve.
	 */
	echo apply_filters( 'bloginfo_rss', get_bloginfo_rss( $show ), $show );
}

/**
 * Retrieve the default feed.
 *
 * The default feed is 'rss2', unless a plugin changes it through the
 * {@see 'default_feed'} filter.
 *
 *
 *
 * @return string Default feed, or for example 'rss2', 'atom', etc.
 */
function get_default_feed() {
	/**
	 * Filters the default feed type.
	 *
	 *
	 * @param string $feed_type Type of default feed. Possible values include 'rss2', 'atom'.
	 *                          Default 'rss2'.
	 */
	$default_feed = apply_filters( 'default_feed', 'rss2' );

	return ( 'rss' === $default_feed ) ? 'rss2' : $default_feed;
}

/**
 * Retrieve the blog title for the feed title.
 *
 *
 *
 *
 * @param string $deprecated Unused..
 * @return string The document title.
 */
function get_gc_title_rss( $deprecated = '&#8211;' ) {
	if ( '&#8211;' !== $deprecated ) {
		/* translators: %s: 'document_title_separator' filter name. */
		_deprecated_argument( __FUNCTION__, '4.4.0', sprintf( __( '改用%s过滤器。' ), '<code>document_title_separator</code>' ) );
	}

	/**
	 * Filters the blog title for use as the feed title.
	 *
	 *
	 * @param string $title      The current blog title.
	 * @param string $deprecated Unused.
	 */
	return apply_filters( 'get_gc_title_rss', gc_get_document_title(), $deprecated );
}

/**
 * Display the blog title for display of the feed title.
 *
 *
 *
 *
 * @param string $deprecated Unused.
 */
function gc_title_rss( $deprecated = '&#8211;' ) {
	if ( '&#8211;' !== $deprecated ) {
		/* translators: %s: 'document_title_separator' filter name. */
		_deprecated_argument( __FUNCTION__, '4.4.0', sprintf( __( '改用%s过滤器。' ), '<code>document_title_separator</code>' ) );
	}

	/**
	 * Filters the blog title for display of the feed title.
	 *
	 *
	 * @see get_gc_title_rss()
	 *
	 * @param string $gc_title_rss The current blog title.
	 * @param string $deprecated   Unused.
	 */
	echo apply_filters( 'gc_title_rss', get_gc_title_rss(), $deprecated );
}

/**
 * Retrieve the current post title for the feed.
 *
 *
 *
 * @return string Current post title.
 */
function get_the_title_rss() {
	$title = get_the_title();

	/**
	 * Filters the post title for use in a feed.
	 *
	 *
	 * @param string $title The current post title.
	 */
	return apply_filters( 'the_title_rss', $title );
}

/**
 * Display the post title in the feed.
 *
 *
 */
function the_title_rss() {
	echo get_the_title_rss();
}

/**
 * Retrieve the post content for feeds.
 *
 *
 *
 * @see get_the_content()
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 * @return string The filtered content.
 */
function get_the_content_feed( $feed_type = null ) {
	if ( ! $feed_type ) {
		$feed_type = get_default_feed();
	}

	/** This filter is documented in gc-includes/post-template.php */
	$content = apply_filters( 'the_content', get_the_content() );
	$content = str_replace( ']]>', ']]&gt;', $content );

	/**
	 * Filters the post content for use in feeds.
	 *
	 *
	 * @param string $content   The current post content.
	 * @param string $feed_type Type of feed. Possible values include 'rss2', 'atom'.
	 *                          Default 'rss2'.
	 */
	return apply_filters( 'the_content_feed', $content, $feed_type );
}

/**
 * Display the post content for feeds.
 *
 *
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 */
function the_content_feed( $feed_type = null ) {
	echo get_the_content_feed( $feed_type );
}

/**
 * Display the post excerpt for the feed.
 *
 *
 */
function the_excerpt_rss() {
	$output = get_the_excerpt();
	/**
	 * Filters the post excerpt for a feed.
	 *
	 *
	 * @param string $output The current post excerpt.
	 */
	echo apply_filters( 'the_excerpt_rss', $output );
}

/**
 * Display the permalink to the post for use in feeds.
 *
 *
 */
function the_permalink_rss() {
	/**
	 * Filters the permalink to the post for use in feeds.
	 *
	 *
	 * @param string $post_permalink The current post permalink.
	 */
	echo esc_url( apply_filters( 'the_permalink_rss', get_permalink() ) );
}

/**
 * Outputs the link to the comments for the current post in an xml safe way
 *
 *
 */
function comments_link_feed() {
	/**
	 * Filters the comments permalink for the current post.
	 *
	 *
	 * @param string $comment_permalink The current comment permalink with
	 *                                  '#comments' appended.
	 */
	echo esc_url( apply_filters( 'comments_link_feed', get_comments_link() ) );
}

/**
 * Display the feed GUID for the current comment.
 *
 *
 *
 * @param int|GC_Comment $comment_id Optional comment object or ID. Defaults to global comment object.
 */
function comment_guid( $comment_id = null ) {
	echo esc_url( get_comment_guid( $comment_id ) );
}

/**
 * Retrieve the feed GUID for the current comment.
 *
 *
 *
 * @param int|GC_Comment $comment_id Optional comment object or ID. Defaults to global comment object.
 * @return string|false GUID for comment on success, false on failure.
 */
function get_comment_guid( $comment_id = null ) {
	$comment = get_comment( $comment_id );

	if ( ! is_object( $comment ) ) {
		return false;
	}

	return get_the_guid( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID;
}

/**
 * Display the link to the comments.
 *
 *
 *
 *
 * @param int|GC_Comment $comment Optional. Comment object or ID. Defaults to global comment object.
 */
function comment_link( $comment = null ) {
	/**
	 * Filters the current comment's permalink.
	 *
	 *
	 * @see get_comment_link()
	 *
	 * @param string $comment_permalink The current comment permalink.
	 */
	echo esc_url( apply_filters( 'comment_link', get_comment_link( $comment ) ) );
}

/**
 * Retrieve the current comment author for use in the feeds.
 *
 *
 *
 * @return string Comment Author
 */
function get_comment_author_rss() {
	/**
	 * Filters the current comment author for use in a feed.
	 *
	 *
	 * @see get_comment_author()
	 *
	 * @param string $comment_author The current comment author.
	 */
	return apply_filters( 'comment_author_rss', get_comment_author() );
}

/**
 * Display the current comment author in the feed.
 *
 *
 */
function comment_author_rss() {
	echo get_comment_author_rss();
}

/**
 * Display the current comment content for use in the feeds.
 *
 *
 */
function comment_text_rss() {
	$comment_text = get_comment_text();
	/**
	 * Filters the current comment content for use in a feed.
	 *
	 *
	 * @param string $comment_text The content of the current comment.
	 */
	$comment_text = apply_filters( 'comment_text_rss', $comment_text );
	echo $comment_text;
}

/**
 * Retrieve all of the post categories, formatted for use in feeds.
 *
 * All of the categories for the current post in the feed loop, will be
 * retrieved and have feed markup added, so that they can easily be added to the
 * RSS2, Atom, or RSS1 and RSS0.91 RDF feeds.
 *
 *
 *
 * @param string $type Optional, default is the type returned by get_default_feed().
 * @return string All of the post categories for displaying in the feed.
 */
function get_the_category_rss( $type = null ) {
	if ( empty( $type ) ) {
		$type = get_default_feed();
	}
	$categories = get_the_category();
	$tags       = get_the_tags();
	$the_list   = '';
	$cat_names  = array();

	$filter = 'rss';
	if ( 'atom' === $type ) {
		$filter = 'raw';
	}

	if ( ! empty( $categories ) ) {
		foreach ( (array) $categories as $category ) {
			$cat_names[] = sanitize_term_field( 'name', $category->name, $category->term_id, 'category', $filter );
		}
	}

	if ( ! empty( $tags ) ) {
		foreach ( (array) $tags as $tag ) {
			$cat_names[] = sanitize_term_field( 'name', $tag->name, $tag->term_id, 'post_tag', $filter );
		}
	}

	$cat_names = array_unique( $cat_names );

	foreach ( $cat_names as $cat_name ) {
		if ( 'rdf' === $type ) {
			$the_list .= "\t\t<dc:subject><![CDATA[$cat_name]]></dc:subject>\n";
		} elseif ( 'atom' === $type ) {
			$the_list .= sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( get_bloginfo_rss( 'url' ) ), esc_attr( $cat_name ) );
		} else {
			$the_list .= "\t\t<category><![CDATA[" . html_entity_decode( $cat_name, ENT_COMPAT, get_option( 'blog_charset' ) ) . "]]></category>\n";
		}
	}

	/**
	 * Filters all of the post categories for display in a feed.
	 *
	 *
	 * @param string $the_list All of the RSS post categories.
	 * @param string $type     Type of feed. Possible values include 'rss2', 'atom'.
	 *                         Default 'rss2'.
	 */
	return apply_filters( 'the_category_rss', $the_list, $type );
}

/**
 * Display the post categories in the feed.
 *
 *
 *
 * @see get_the_category_rss() For better explanation.
 *
 * @param string $type Optional, default is the type returned by get_default_feed().
 */
function the_category_rss( $type = null ) {
	echo get_the_category_rss( $type );
}

/**
 * Display the HTML type based on the blog setting.
 *
 * The two possible values are either 'xhtml' or 'html'.
 *
 *
 */
function html_type_rss() {
	$type = get_bloginfo( 'html_type' );
	if ( strpos( $type, 'xhtml' ) !== false ) {
		$type = 'xhtml';
	} else {
		$type = 'html';
	}
	echo $type;
}

/**
 * Display the rss enclosure for the current post.
 *
 * Uses the global $post to check whether the post requires a password and if
 * the user has the password for the post. If not then it will return before
 * displaying.
 *
 * Also uses the function get_post_custom() to get the post's 'enclosure'
 * metadata field and parses the value to display the enclosure(s). The
 * enclosure(s) consist of enclosure HTML tag(s) with a URI and other
 * attributes.
 *
 *
 */
function rss_enclosure() {
	if ( post_password_required() ) {
		return;
	}

	foreach ( (array) get_post_custom() as $key => $val ) {
		if ( 'enclosure' === $key ) {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode( "\n", $enc );

				// Only get the first element, e.g. 'audio/mpeg' from 'audio/mpeg mpga mp2 mp3'.
				$t    = preg_split( '/[ \t]/', trim( $enclosure[2] ) );
				$type = $t[0];

				/**
				 * Filters the RSS enclosure HTML link tag for the current post.
				 *
			
				 *
				 * @param string $html_link_tag The HTML link tag with a URI and other attributes.
				 */
				echo apply_filters( 'rss_enclosure', '<enclosure url="' . esc_url( trim( $enclosure[0] ) ) . '" length="' . absint( trim( $enclosure[1] ) ) . '" type="' . esc_attr( $type ) . '" />' . "\n" );
			}
		}
	}
}

/**
 * Display the atom enclosure for the current post.
 *
 * Uses the global $post to check whether the post requires a password and if
 * the user has the password for the post. If not then it will return before
 * displaying.
 *
 * Also uses the function get_post_custom() to get the post's 'enclosure'
 * metadata field and parses the value to display the enclosure(s). The
 * enclosure(s) consist of link HTML tag(s) with a URI and other attributes.
 *
 *
 */
function atom_enclosure() {
	if ( post_password_required() ) {
		return;
	}

	foreach ( (array) get_post_custom() as $key => $val ) {
		if ( 'enclosure' === $key ) {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode( "\n", $enc );

				$url    = '';
				$type   = '';
				$length = 0;

				$mimes = get_allowed_mime_types();

				// Parse URL.
				if ( isset( $enclosure[0] ) && is_string( $enclosure[0] ) ) {
					$url = trim( $enclosure[0] );
				}

				// Parse length and type.
				for ( $i = 1; $i <= 2; $i++ ) {
					if ( isset( $enclosure[ $i ] ) ) {
						if ( is_numeric( $enclosure[ $i ] ) ) {
							$length = trim( $enclosure[ $i ] );
						} elseif ( in_array( $enclosure[ $i ], $mimes, true ) ) {
							$type = trim( $enclosure[ $i ] );
						}
					}
				}

				$html_link_tag = sprintf(
					"<link href=\"%s\" rel=\"enclosure\" length=\"%d\" type=\"%s\" />\n",
					esc_url( $url ),
					esc_attr( $length ),
					esc_attr( $type )
				);

				/**
				 * Filters the atom enclosure HTML link tag for the current post.
				 *
			
				 *
				 * @param string $html_link_tag The HTML link tag with a URI and other attributes.
				 */
				echo apply_filters( 'atom_enclosure', $html_link_tag );
			}
		}
	}
}

/**
 * Determine the type of a string of data with the data formatted.
 *
 * Tell whether the type is text, HTML, or XHTML, per RFC 4287 section 3.1.
 *
 * In the case of GeChiUI, text is defined as containing no markup,
 * XHTML is defined as "well formed", and HTML as tag soup (i.e., the rest).
 *
 * Container div tags are added to XHTML values, per section 3.1.1.3.
 *
 * @link http://www.atomenabled.org/developers/syndication/atom-format-spec.php#rfc.section.3.1
 *
 *
 *
 * @param string $data Input string
 * @return array array(type, value)
 */
function prep_atom_text_construct( $data ) {
	if ( strpos( $data, '<' ) === false && strpos( $data, '&' ) === false ) {
		return array( 'text', $data );
	}

	if ( ! function_exists( 'xml_parser_create' ) ) {
		trigger_error( __( "PHP的XML扩展不可用。请联系您的主机提供商来启用PHP的XML扩展。" ) );

		return array( 'html', "<![CDATA[$data]]>" );
	}

	$parser = xml_parser_create();
	xml_parse( $parser, '<div>' . $data . '</div>', true );
	$code = xml_get_error_code( $parser );
	xml_parser_free( $parser );
	unset( $parser );

	if ( ! $code ) {
		if ( strpos( $data, '<' ) === false ) {
			return array( 'text', $data );
		} else {
			$data = "<div xmlns='http://www.w3.org/1999/xhtml'>$data</div>";
			return array( 'xhtml', $data );
		}
	}

	if ( strpos( $data, ']]>' ) === false ) {
		return array( 'html', "<![CDATA[$data]]>" );
	} else {
		return array( 'html', htmlspecialchars( $data ) );
	}
}

/**
 * Displays Site Icon in atom feeds.
 *
 *
 *
 * @see get_site_icon_url()
 */
function atom_site_icon() {
	$url = get_site_icon_url( 32 );
	if ( $url ) {
		echo '<icon>' . convert_chars( $url ) . "</icon>\n";
	}
}

/**
 * Displays Site Icon in RSS2.
 *
 *
 */
function rss2_site_icon() {
	$rss_title = get_gc_title_rss();
	if ( empty( $rss_title ) ) {
		$rss_title = get_bloginfo_rss( 'name' );
	}

	$url = get_site_icon_url( 32 );
	if ( $url ) {
		echo '
<image>
	<url>' . convert_chars( $url ) . '</url>
	<title>' . $rss_title . '</title>
	<link>' . get_bloginfo_rss( 'url' ) . '</link>
	<width>32</width>
	<height>32</height>
</image> ' . "\n";
	}
}

/**
 * Returns the link for the currently displayed feed.
 *
 *
 *
 * @return string Correct link for the atom:self element.
 */
function get_self_link() {
	$host = parse_url( home_url() );
	return set_url_scheme( 'http://' . $host['host'] . gc_unslash( $_SERVER['REQUEST_URI'] ) );
}

/**
 * Display the link for the currently displayed feed in a XSS safe way.
 *
 * Generate a correct link for the atom:self element.
 *
 *
 */
function self_link() {
	/**
	 * Filters the current feed URL.
	 *
	 *
	 * @see set_url_scheme()
	 * @see gc_unslash()
	 *
	 * @param string $feed_link The link for the feed with set URL scheme.
	 */
	echo esc_url( apply_filters( 'self_link', get_self_link() ) );
}

/**
 * Get the UTC time of the most recently modified post from GC_Query.
 *
 * If viewing a comment feed, the time of the most recently modified
 * comment will be returned.
 *
 * @global GC_Query $gc_query GeChiUI Query object.
 *
 *
 *
 * @param string $format Date format string to return the time in.
 * @return string|false The time in requested format, or false on failure.
 */
function get_feed_build_date( $format ) {
	global $gc_query;

	$datetime          = false;
	$max_modified_time = false;
	$utc               = new DateTimeZone( 'UTC' );

	if ( ! empty( $gc_query ) && $gc_query->have_posts() ) {
		// Extract the post modified times from the posts.
		$modified_times = gc_list_pluck( $gc_query->posts, 'post_modified_gmt' );

		// If this is a comment feed, check those objects too.
		if ( $gc_query->is_comment_feed() && $gc_query->comment_count ) {
			// Extract the comment modified times from the comments.
			$comment_times = gc_list_pluck( $gc_query->comments, 'comment_date_gmt' );

			// Add the comment times to the post times for comparison.
			$modified_times = array_merge( $modified_times, $comment_times );
		}

		// Determine the maximum modified time.
		$datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', max( $modified_times ), $utc );
	}

	if ( false === $datetime ) {
		// Fall back to last time any post was modified or published.
		$datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', get_lastpostmodified( 'GMT' ), $utc );
	}

	if ( false !== $datetime ) {
		$max_modified_time = $datetime->format( $format );
	}

	/**
	 * Filters the date the last post or comment in the query was modified.
	 *
	 *
	 * @param string|false $max_modified_time Date the last post or comment was modified in the query, in UTC.
	 *                                        False on failure.
	 * @param string       $format            The date format requested in get_feed_build_date().
	 */
	return apply_filters( 'get_feed_build_date', $max_modified_time, $format );
}

/**
 * Return the content type for specified feed type.
 *
 *
 *
 * @param string $type Type of feed. Possible values include 'rss', rss2', 'atom', and 'rdf'.
 */
function feed_content_type( $type = '' ) {
	if ( empty( $type ) ) {
		$type = get_default_feed();
	}

	$types = array(
		'rss'      => 'application/rss+xml',
		'rss2'     => 'application/rss+xml',
		'rss-http' => 'text/xml',
		'atom'     => 'application/atom+xml',
		'rdf'      => 'application/rdf+xml',
	);

	$content_type = ( ! empty( $types[ $type ] ) ) ? $types[ $type ] : 'application/octet-stream';

	/**
	 * Filters the content type for a specific feed type.
	 *
	 *
	 * @param string $content_type Content type indicating the type of data that a feed contains.
	 * @param string $type         Type of feed. Possible values include 'rss', rss2', 'atom', and 'rdf'.
	 */
	return apply_filters( 'feed_content_type', $content_type, $type );
}

/**
 * Build SimplePie object based on RSS or Atom feed from URL.
 *
 *
 *
 * @param string|string[] $url URL of feed to retrieve. If an array of URLs, the feeds are merged
 *                             using SimplePie's multifeed feature.
 *                             See also {@link http://simplepie.org/wiki/faq/typical_multifeed_gotchas}
 * @return SimplePie|GC_Error SimplePie object on success or GC_Error object on failure.
 */
function fetch_feed( $url ) {
	if ( ! class_exists( 'SimplePie', false ) ) {
		require_once ABSPATH . GCINC . '/class-simplepie.php';
	}

	require_once ABSPATH . GCINC . '/class-gc-feed-cache-transient.php';
	require_once ABSPATH . GCINC . '/class-gc-simplepie-file.php';
	require_once ABSPATH . GCINC . '/class-gc-simplepie-sanitize-kses.php';

	$feed = new SimplePie();

	$feed->set_sanitize_class( 'GC_SimplePie_Sanitize_KSES' );
	// We must manually overwrite $feed->sanitize because SimplePie's constructor
	// sets it before we have a chance to set the sanitization class.
	$feed->sanitize = new GC_SimplePie_Sanitize_KSES();

	// Register the cache handler using the recommended method for SimplePie 1.3 or later.
	if ( method_exists( 'SimplePie_Cache', 'register' ) ) {
		SimplePie_Cache::register( 'gc_transient', 'GC_Feed_Cache_Transient' );
		$feed->set_cache_location( 'gc_transient' );
	} else {
		// Back-compat for SimplePie 1.2.x.
		require_once ABSPATH . GCINC . '/class-gc-feed-cache.php';
		$feed->set_cache_class( 'GC_Feed_Cache' );
	}

	$feed->set_file_class( 'GC_SimplePie_File' );

	$feed->set_feed_url( $url );
	/** This filter is documented in gc-includes/class-gc-feed-cache-transient.php */
	$feed->set_cache_duration( apply_filters( 'gc_feed_cache_transient_lifetime', 12 * HOUR_IN_SECONDS, $url ) );

	/**
	 * Fires just before processing the SimplePie feed object.
	 *
	 *
	 * @param SimplePie       $feed SimplePie feed object (passed by reference).
	 * @param string|string[] $url  URL of feed or array of URLs of feeds to retrieve.
	 */
	do_action_ref_array( 'gc_feed_options', array( &$feed, $url ) );

	$feed->init();
	$feed->set_output_encoding( get_option( 'blog_charset' ) );

	if ( $feed->error() ) {
		return new GC_Error( 'simplepie-error', $feed->error() );
	}

	return $feed;
}
