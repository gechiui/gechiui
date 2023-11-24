<?php
/**
 * Administration API: Core Ajax handlers
 *
 * @package GeChiUI
 * @subpackage Administration
 */

//
// No-privilege Ajax handlers.
//

/**
 * Handles the Heartbeat API in the no-privilege context via AJAX .
 *
 * Runs when the user is not logged in.
 *
 */
function gc_ajax_nopriv_heartbeat() {
	$response = array();

	// 'screen_id' is the same as $current_screen->id and the JS global 'pagenow'.
	if ( ! empty( $_POST['screen_id'] ) ) {
		$screen_id = sanitize_key( $_POST['screen_id'] );
	} else {
		$screen_id = 'front';
	}

	if ( ! empty( $_POST['data'] ) ) {
		$data = gc_unslash( (array) $_POST['data'] );

		/**
		 * Filters Heartbeat Ajax response in no-privilege environments.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $response  The no-priv Heartbeat response.
		 * @param array  $data      The $_POST data sent.
		 * @param string $screen_id The screen ID.
		 */
		$response = apply_filters( 'heartbeat_nopriv_received', $response, $data, $screen_id );
	}

	/**
	 * Filters Heartbeat Ajax response in no-privilege environments when no data is passed.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $response  The no-priv Heartbeat response.
	 * @param string $screen_id The screen ID.
	 */
	$response = apply_filters( 'heartbeat_nopriv_send', $response, $screen_id );

	/**
	 * Fires when Heartbeat ticks in no-privilege environments.
	 *
	 * Allows the transport to be easily replaced with long-polling.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $response  The no-priv Heartbeat response.
	 * @param string $screen_id The screen ID.
	 */
	do_action( 'heartbeat_nopriv_tick', $response, $screen_id );

	// Send the current time according to the server.
	$response['server_time'] = time();

	gc_send_json( $response );
}

//
// GET-based Ajax handlers.
//

/**
 * Handles fetching a list table via AJAX.
 *
 */
function gc_ajax_fetch_list() {
	$list_class = $_GET['list_args']['class'];
	check_ajax_referer( "fetch-list-$list_class", '_ajax_fetch_list_nonce' );

	$gc_list_table = _get_list_table( $list_class, array( 'screen' => $_GET['list_args']['screen']['id'] ) );
	if ( ! $gc_list_table ) {
		gc_die( 0 );
	}

	if ( ! $gc_list_table->ajax_user_can() ) {
		gc_die( -1 );
	}

	$gc_list_table->ajax_response();

	gc_die( 0 );
}

/**
 * Handles tag search via AJAX.
 *
 */
function gc_ajax_ajax_tag_search() {
	if ( ! isset( $_GET['tax'] ) ) {
		gc_die( 0 );
	}

	$taxonomy        = sanitize_key( $_GET['tax'] );
	$taxonomy_object = get_taxonomy( $taxonomy );

	if ( ! $taxonomy_object ) {
		gc_die( 0 );
	}

	if ( ! current_user_can( $taxonomy_object->cap->assign_terms ) ) {
		gc_die( -1 );
	}

	$search = gc_unslash( $_GET['q'] );

	$comma = _x( ',', 'tag delimiter' );
	if ( ',' !== $comma ) {
		$search = str_replace( $comma, ',', $search );
	}

	if ( str_contains( $search, ',' ) ) {
		$search = explode( ',', $search );
		$search = $search[ count( $search ) - 1 ];
	}

	$search = trim( $search );

	/**
	 * Filters the minimum number of characters required to fire a tag search via Ajax.
	 *
	 * @since 4.0.0
	 *
	 * @param int         $characters      The minimum number of characters required. Default 2.
	 * @param GC_Taxonomy $taxonomy_object The taxonomy object.
	 * @param string      $search          The search term.
	 */
	$term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $taxonomy_object, $search );

	/*
	 * Require $term_search_min_chars chars for matching (default: 2)
	 * ensure it's a non-negative, non-zero integer.
	 */
	if ( ( 0 == $term_search_min_chars ) || ( strlen( $search ) < $term_search_min_chars ) ) {
		gc_die();
	}

	$results = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'name__like' => $search,
			'fields'     => 'names',
			'hide_empty' => false,
			'number'     => isset( $_GET['number'] ) ? (int) $_GET['number'] : 0,
		)
	);

	/**
	 * Filters the Ajax term search results.
	 *
	 * @since 6.1.0
	 *
	 * @param string[]    $results         Array of term names.
	 * @param GC_Taxonomy $taxonomy_object The taxonomy object.
	 * @param string      $search          The search term.
	 */
	$results = apply_filters( 'ajax_term_search_results', $results, $taxonomy_object, $search );

	echo implode( "\n", $results );
	gc_die();
}

/**
 * Handles compression testing via AJAX.
 *
 */
function gc_ajax_gc_compression_test() {
	if ( ! current_user_can( 'manage_options' ) ) {
		gc_die( -1 );
	}

	if ( ini_get( 'zlib.output_compression' ) || 'ob_gzhandler' === ini_get( 'output_handler' ) ) {
		// Use `update_option()` on single site to mark the option for autoloading.
		if ( is_multisite() ) {
			update_site_option( 'can_compress_scripts', 0 );
		} else {
			update_option( 'can_compress_scripts', 0, 'yes' );
		}
		gc_die( 0 );
	}

	if ( isset( $_GET['test'] ) ) {
		header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		header( 'Content-Type: application/javascript; charset=UTF-8' );
		$force_gzip = ( defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP );
		$test_str   = '"gcCompressionTest Lorem ipsum dolor sit amet consectetuer mollis sapien urna ut a. Eu nonummy condimentum fringilla tempor pretium platea vel nibh netus Maecenas. Hac molestie amet justo quis pellentesque est ultrices interdum nibh Morbi. Cras mattis pretium Phasellus ante ipsum ipsum ut sociis Suspendisse Lorem. Ante et non molestie. Porta urna Vestibulum egestas id congue nibh eu risus gravida sit. Ac augue auctor Ut et non a elit massa id sodales. Elit eu Nulla at nibh adipiscing mattis lacus mauris at tempus. Netus nibh quis suscipit nec feugiat eget sed lorem et urna. Pellentesque lacus at ut massa consectetuer ligula ut auctor semper Pellentesque. Ut metus massa nibh quam Curabitur molestie nec mauris congue. Volutpat molestie elit justo facilisis neque ac risus Ut nascetur tristique. Vitae sit lorem tellus et quis Phasellus lacus tincidunt nunc Fusce. Pharetra wisi Suspendisse mus sagittis libero lacinia Integer consequat ac Phasellus. Et urna ac cursus tortor aliquam Aliquam amet tellus volutpat Vestibulum. Justo interdum condimentum In augue congue tellus sollicitudin Quisque quis nibh."';

		if ( 1 == $_GET['test'] ) {
			echo $test_str;
			gc_die();
		} elseif ( 2 == $_GET['test'] ) {
			if ( ! isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) {
				gc_die( -1 );
			}

			if ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate' ) && function_exists( 'gzdeflate' ) && ! $force_gzip ) {
				header( 'Content-Encoding: deflate' );
				$out = gzdeflate( $test_str, 1 );
			} elseif ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && function_exists( 'gzencode' ) ) {
				header( 'Content-Encoding: gzip' );
				$out = gzencode( $test_str, 1 );
			} else {
				gc_die( -1 );
			}

			echo $out;
			gc_die();
		} elseif ( 'no' === $_GET['test'] ) {
			check_ajax_referer( 'update_can_compress_scripts' );
			// Use `update_option()` on single site to mark the option for autoloading.
			if ( is_multisite() ) {
				update_site_option( 'can_compress_scripts', 0 );
			} else {
				update_option( 'can_compress_scripts', 0, 'yes' );
			}
		} elseif ( 'yes' === $_GET['test'] ) {
			check_ajax_referer( 'update_can_compress_scripts' );
			// Use `update_option()` on single site to mark the option for autoloading.
			if ( is_multisite() ) {
				update_site_option( 'can_compress_scripts', 1 );
			} else {
				update_option( 'can_compress_scripts', 1, 'yes' );
			}
		}
	}

	gc_die( 0 );
}

/**
 * Handles image editor previews via AJAX.
 *
 */
function gc_ajax_imgedit_preview() {
	$post_id = (int) $_GET['postid'];
	if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		gc_die( -1 );
	}

	check_ajax_referer( "image_editor-$post_id" );

	require_once ABSPATH . 'gc-admin/includes/image-edit.php';

	if ( ! stream_preview_image( $post_id ) ) {
		gc_die( -1 );
	}

	gc_die();
}

/**
 * Handles oEmbed caching via AJAX.
 *
 * @global GC_Embed $gc_embed
 */
function gc_ajax_oembed_cache() {
	$GLOBALS['gc_embed']->cache_oembed( $_GET['post'] );
	gc_die( 0 );
}

/**
 * Handles user autocomplete via AJAX.
 *
 */
function gc_ajax_autocomplete_user() {
	if ( ! is_multisite() || ! current_user_can( 'promote_users' ) || gc_is_large_network( 'users' ) ) {
		gc_die( -1 );
	}

	/** This filter is documented in gc-admin/user-new.php */
	if ( ! current_user_can( 'manage_network_users' ) && ! apply_filters( 'autocomplete_users_for_site_admins', false ) ) {
		gc_die( -1 );
	}

	$return = array();

	/*
	 * Check the type of request.
	 * Current allowed values are `add` and `search`.
	 */
	if ( isset( $_REQUEST['autocomplete_type'] ) && 'search' === $_REQUEST['autocomplete_type'] ) {
		$type = $_REQUEST['autocomplete_type'];
	} else {
		$type = 'add';
	}

	/*
	 * Check the desired field for value.
	 * Current allowed values are `user_email` and `user_login`.
	 */
	if ( isset( $_REQUEST['autocomplete_field'] ) && 'user_email' === $_REQUEST['autocomplete_field'] ) {
		$field = $_REQUEST['autocomplete_field'];
	} else {
		$field = 'user_login';
	}

	// Exclude current users of this blog.
	if ( isset( $_REQUEST['site_id'] ) ) {
		$id = absint( $_REQUEST['site_id'] );
	} else {
		$id = get_current_blog_id();
	}

	$include_blog_users = ( 'search' === $type ? get_users(
		array(
			'blog_id' => $id,
			'fields'  => 'ID',
		)
	) : array() );

	$exclude_blog_users = ( 'add' === $type ? get_users(
		array(
			'blog_id' => $id,
			'fields'  => 'ID',
		)
	) : array() );

	$users = get_users(
		array(
			'blog_id'        => false,
			'search'         => '*' . $_REQUEST['term'] . '*',
			'include'        => $include_blog_users,
			'exclude'        => $exclude_blog_users,
			'search_columns' => array( 'user_login', 'user_nicename', 'user_email' ),
		)
	);

	foreach ( $users as $user ) {
		$return[] = array(
			/* translators: 1: User login, 2: User email address. */
			'label' => sprintf( _x( '%1$s（%2$s）', 'user autocomplete result' ), $user->user_login, $user->user_email ),
			'value' => $user->$field,
		);
	}

	gc_die( gc_json_encode( $return ) );
}

/**
 * Handles Ajax requests for community events
 *
 */
function gc_ajax_get_community_events() {
	require_once ABSPATH . 'gc-admin/includes/class-gc-community-events.php';

	check_ajax_referer( 'community_events' );

	$search         = isset( $_POST['location'] ) ? gc_unslash( $_POST['location'] ) : '';
	$timezone       = isset( $_POST['timezone'] ) ? gc_unslash( $_POST['timezone'] ) : '';
	$user_id        = get_current_user_id();
	$saved_location = get_user_option( 'community-events-location', $user_id );
	$events_client  = new GC_Community_Events( $user_id, $saved_location );
	$events         = $events_client->get_events( $search, $timezone );
	$ip_changed     = false;

	if ( is_gc_error( $events ) ) {
		gc_send_json_error(
			array(
				'error' => $events->get_error_message(),
			)
		);
	} else {
		if ( empty( $saved_location['ip'] ) && ! empty( $events['location']['ip'] ) ) {
			$ip_changed = true;
		} elseif ( isset( $saved_location['ip'] ) && ! empty( $events['location']['ip'] ) && $saved_location['ip'] !== $events['location']['ip'] ) {
			$ip_changed = true;
		}

		/*
		 * The location should only be updated when it changes. The API doesn't always return
		 * a full location; sometimes it's missing the description or country. The location
		 * that was saved during the initial request is known to be good and complete, though.
		 * It should be left intact until the user explicitly changes it (either by manually
		 * searching for a new location, or by changing their IP address).
		 *
		 * If the location was updated with an incomplete response from the API, then it could
		 * break assumptions that the UI makes (e.g., that there will always be a description
		 * that corresponds to a latitude/longitude location).
		 *
		 * The location is stored network-wide, so that the user doesn't have to set it on each site.
		 */
		if ( $ip_changed || $search ) {
			update_user_meta( $user_id, 'community-events-location', $events['location'] );
		}

		gc_send_json_success( $events );
	}
}

/**
 * Handles dashboard widgets via AJAX.
 *
 */
function gc_ajax_dashboard_widgets() {
	require_once ABSPATH . 'gc-admin/includes/dashboard.php';

	$pagenow = $_GET['pagenow'];
	if ( 'dashboard-user' === $pagenow || 'dashboard-network' === $pagenow || 'dashboard' === $pagenow ) {
		set_current_screen( $pagenow );
	}

	switch ( $_GET['widget'] ) {
		case 'dashboard_primary':
			gc_dashboard_primary();
			break;
	}
	gc_die();
}

/**
 * Handles Customizer preview logged-in status via AJAX.
 *
 */
function gc_ajax_logged_in() {
	gc_die( 1 );
}

//
// Ajax helpers.
//

/**
 * Sends back current comment total and new page links if they need to be updated.
 *
 * Contrary to normal success Ajax response ("1"), die with time() on success.
 *
 * @since 2.7.0
 * @access private
 *
 * @param int $comment_id
 * @param int $delta
 */
function _gc_ajax_delete_comment_response( $comment_id, $delta = -1 ) {
	$total    = isset( $_POST['_total'] ) ? (int) $_POST['_total'] : 0;
	$per_page = isset( $_POST['_per_page'] ) ? (int) $_POST['_per_page'] : 0;
	$page     = isset( $_POST['_page'] ) ? (int) $_POST['_page'] : 0;
	$url      = isset( $_POST['_url'] ) ? sanitize_url( $_POST['_url'] ) : '';

	// JS didn't send us everything we need to know. Just die with success message.
	if ( ! $total || ! $per_page || ! $page || ! $url ) {
		$time           = time();
		$comment        = get_comment( $comment_id );
		$comment_status = '';
		$comment_link   = '';

		if ( $comment ) {
			$comment_status = $comment->comment_approved;
		}

		if ( 1 === (int) $comment_status ) {
			$comment_link = get_comment_link( $comment );
		}

		$counts = gc_count_comments();

		$x = new GC_Ajax_Response(
			array(
				'what'         => 'comment',
				// Here for completeness - not used.
				'id'           => $comment_id,
				'supplemental' => array(
					'status'               => $comment_status,
					'postId'               => $comment ? $comment->comment_post_ID : '',
					'time'                 => $time,
					'in_moderation'        => $counts->moderated,
					'i18n_comments_text'   => sprintf(
						/* translators: %s: Number of comments. */
						_n( '%s条评论', '%s条评论', $counts->approved ),
						number_format_i18n( $counts->approved )
					),
					'i18n_moderation_text' => sprintf(
						/* translators: %s: Number of comments. */
						_n( '%s条评论待审', '%s条评论待审', $counts->moderated ),
						number_format_i18n( $counts->moderated )
					),
					'comment_link'         => $comment_link,
				),
			)
		);
		$x->send();
	}

	$total += $delta;
	if ( $total < 0 ) {
		$total = 0;
	}

	// Only do the expensive stuff on a page-break, and about 1 other time per page.
	if ( 0 == $total % $per_page || 1 == mt_rand( 1, $per_page ) ) {
		$post_id = 0;
		// What type of comment count are we looking for?
		$status = 'all';
		$parsed = parse_url( $url );

		if ( isset( $parsed['query'] ) ) {
			parse_str( $parsed['query'], $query_vars );

			if ( ! empty( $query_vars['comment_status'] ) ) {
				$status = $query_vars['comment_status'];
			}

			if ( ! empty( $query_vars['p'] ) ) {
				$post_id = (int) $query_vars['p'];
			}

			if ( ! empty( $query_vars['comment_type'] ) ) {
				$type = $query_vars['comment_type'];
			}
		}

		if ( empty( $type ) ) {
			// Only use the comment count if not filtering by a comment_type.
			$comment_count = gc_count_comments( $post_id );

			// We're looking for a known type of comment count.
			if ( isset( $comment_count->$status ) ) {
				$total = $comment_count->$status;
			}
		}
		// Else use the decremented value from above.
	}

	// The time since the last comment count.
	$time    = time();
	$comment = get_comment( $comment_id );
	$counts  = gc_count_comments();

	$x = new GC_Ajax_Response(
		array(
			'what'         => 'comment',
			'id'           => $comment_id,
			'supplemental' => array(
				'status'               => $comment ? $comment->comment_approved : '',
				'postId'               => $comment ? $comment->comment_post_ID : '',
				/* translators: %s: Number of comments. */
				'total_items_i18n'     => sprintf( _n( '%s个项目', '%s个项目', $total ), number_format_i18n( $total ) ),
				'total_pages'          => ceil( $total / $per_page ),
				'total_pages_i18n'     => number_format_i18n( ceil( $total / $per_page ) ),
				'total'                => $total,
				'time'                 => $time,
				'in_moderation'        => $counts->moderated,
				'i18n_moderation_text' => sprintf(
					/* translators: %s: Number of comments. */
					_n( '%s条评论待审', '%s条评论待审', $counts->moderated ),
					number_format_i18n( $counts->moderated )
				),
			),
		)
	);
	$x->send();
}

//
// POST-based Ajax handlers.
//

/**
 * Handles adding a hierarchical term via AJAX.
 *
 * @access private
 */
function _gc_ajax_add_hierarchical_term() {
	$action   = $_POST['action'];
	$taxonomy = get_taxonomy( substr( $action, 4 ) );
	check_ajax_referer( $action, '_ajax_nonce-add-' . $taxonomy->name );

	if ( ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		gc_die( -1 );
	}

	$names  = explode( ',', $_POST[ 'new' . $taxonomy->name ] );
	$parent = isset( $_POST[ 'new' . $taxonomy->name . '_parent' ] ) ? (int) $_POST[ 'new' . $taxonomy->name . '_parent' ] : 0;

	if ( 0 > $parent ) {
		$parent = 0;
	}

	if ( 'category' === $taxonomy->name ) {
		$post_category = isset( $_POST['post_category'] ) ? (array) $_POST['post_category'] : array();
	} else {
		$post_category = ( isset( $_POST['tax_input'] ) && isset( $_POST['tax_input'][ $taxonomy->name ] ) ) ? (array) $_POST['tax_input'][ $taxonomy->name ] : array();
	}

	$checked_categories = array_map( 'absint', (array) $post_category );
	$popular_ids        = gc_popular_terms_checklist( $taxonomy->name, 0, 10, false );

	foreach ( $names as $cat_name ) {
		$cat_name          = trim( $cat_name );
		$category_nicename = sanitize_title( $cat_name );

		if ( '' === $category_nicename ) {
			continue;
		}

		$cat_id = gc_insert_term( $cat_name, $taxonomy->name, array( 'parent' => $parent ) );

		if ( ! $cat_id || is_gc_error( $cat_id ) ) {
			continue;
		} else {
			$cat_id = $cat_id['term_id'];
		}

		$checked_categories[] = $cat_id;

		if ( $parent ) { // Do these all at once in a second.
			continue;
		}

		ob_start();

		gc_terms_checklist(
			0,
			array(
				'taxonomy'             => $taxonomy->name,
				'descendants_and_self' => $cat_id,
				'selected_cats'        => $checked_categories,
				'popular_cats'         => $popular_ids,
			)
		);

		$data = ob_get_clean();

		$add = array(
			'what'     => $taxonomy->name,
			'id'       => $cat_id,
			'data'     => str_replace( array( "\n", "\t" ), '', $data ),
			'position' => -1,
		);
	}

	if ( $parent ) { // Foncy - replace the parent and all its children.
		$parent  = get_term( $parent, $taxonomy->name );
		$term_id = $parent->term_id;

		while ( $parent->parent ) { // Get the top parent.
			$parent = get_term( $parent->parent, $taxonomy->name );
			if ( is_gc_error( $parent ) ) {
				break;
			}
			$term_id = $parent->term_id;
		}

		ob_start();

		gc_terms_checklist(
			0,
			array(
				'taxonomy'             => $taxonomy->name,
				'descendants_and_self' => $term_id,
				'selected_cats'        => $checked_categories,
				'popular_cats'         => $popular_ids,
			)
		);

		$data = ob_get_clean();

		$add = array(
			'what'     => $taxonomy->name,
			'id'       => $term_id,
			'data'     => str_replace( array( "\n", "\t" ), '', $data ),
			'position' => -1,
		);
	}

	ob_start();

	gc_dropdown_categories(
		array(
			'taxonomy'         => $taxonomy->name,
			'hide_empty'       => 0,
			'name'             => 'new' . $taxonomy->name . '_parent',
			'orderby'          => 'name',
			'hierarchical'     => 1,
			'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;',
		)
	);

	$sup = ob_get_clean();

	$add['supplemental'] = array( 'newcat_parent' => $sup );

	$x = new GC_Ajax_Response( $add );
	$x->send();
}

/**
 * Handles deleting a comment via AJAX.
 *
 */
function gc_ajax_delete_comment() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	$comment = get_comment( $id );

	if ( ! $comment ) {
		gc_die( time() );
	}

	if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		gc_die( -1 );
	}

	check_ajax_referer( "delete-comment_$id" );
	$status = gc_get_comment_status( $comment );
	$delta  = -1;

	if ( isset( $_POST['trash'] ) && 1 == $_POST['trash'] ) {
		if ( 'trash' === $status ) {
			gc_die( time() );
		}

		$r = gc_trash_comment( $comment );
	} elseif ( isset( $_POST['untrash'] ) && 1 == $_POST['untrash'] ) {
		if ( 'trash' !== $status ) {
			gc_die( time() );
		}

		$r = gc_untrash_comment( $comment );

		// Undo trash, not in Trash.
		if ( ! isset( $_POST['comment_status'] ) || 'trash' !== $_POST['comment_status'] ) {
			$delta = 1;
		}
	} elseif ( isset( $_POST['spam'] ) && 1 == $_POST['spam'] ) {
		if ( 'spam' === $status ) {
			gc_die( time() );
		}

		$r = gc_spam_comment( $comment );
	} elseif ( isset( $_POST['unspam'] ) && 1 == $_POST['unspam'] ) {
		if ( 'spam' !== $status ) {
			gc_die( time() );
		}

		$r = gc_unspam_comment( $comment );

		// Undo spam, not in spam.
		if ( ! isset( $_POST['comment_status'] ) || 'spam' !== $_POST['comment_status'] ) {
			$delta = 1;
		}
	} elseif ( isset( $_POST['delete'] ) && 1 == $_POST['delete'] ) {
		$r = gc_delete_comment( $comment );
	} else {
		gc_die( -1 );
	}

	if ( $r ) {
		// Decide if we need to send back '1' or a more complicated response including page links and comment counts.
		_gc_ajax_delete_comment_response( $comment->comment_ID, $delta );
	}

	gc_die( 0 );
}

/**
 * Handles deleting a tag via AJAX.
 *
 */
function gc_ajax_delete_tag() {
	$tag_id = (int) $_POST['tag_ID'];
	check_ajax_referer( "delete-tag_$tag_id" );

	if ( ! current_user_can( 'delete_term', $tag_id ) ) {
		gc_die( -1 );
	}

	$taxonomy = ! empty( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : 'post_tag';
	$tag      = get_term( $tag_id, $taxonomy );

	if ( ! $tag || is_gc_error( $tag ) ) {
		gc_die( 1 );
	}

	if ( gc_delete_term( $tag_id, $taxonomy ) ) {
		gc_die( 1 );
	} else {
		gc_die( 0 );
	}
}

/**
 * Handles deleting a link via AJAX.
 *
 */
function gc_ajax_delete_link() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "delete-bookmark_$id" );

	if ( ! current_user_can( 'manage_links' ) ) {
		gc_die( -1 );
	}

	$link = get_bookmark( $id );
	if ( ! $link || is_gc_error( $link ) ) {
		gc_die( 1 );
	}

	if ( gc_delete_link( $id ) ) {
		gc_die( 1 );
	} else {
		gc_die( 0 );
	}
}

/**
 * Handles deleting meta via AJAX.
 *
 */
function gc_ajax_delete_meta() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "delete-meta_$id" );
	$meta = get_metadata_by_mid( 'post', $id );

	if ( ! $meta ) {
		gc_die( 1 );
	}

	if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'delete_post_meta', $meta->post_id, $meta->meta_key ) ) {
		gc_die( -1 );
	}

	if ( delete_meta( $meta->meta_id ) ) {
		gc_die( 1 );
	}

	gc_die( 0 );
}

/**
 * Handles deleting a post via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_delete_post( $action ) {
	if ( empty( $action ) ) {
		$action = 'delete-post';
	}

	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	check_ajax_referer( "{$action}_$id" );

	if ( ! current_user_can( 'delete_post', $id ) ) {
		gc_die( -1 );
	}

	if ( ! get_post( $id ) ) {
		gc_die( 1 );
	}

	if ( gc_delete_post( $id ) ) {
		gc_die( 1 );
	} else {
		gc_die( 0 );
	}
}

/**
 * Handles sending a post to the Trash via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_trash_post( $action ) {
	if ( empty( $action ) ) {
		$action = 'trash-post';
	}

	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	check_ajax_referer( "{$action}_$id" );

	if ( ! current_user_can( 'delete_post', $id ) ) {
		gc_die( -1 );
	}

	if ( ! get_post( $id ) ) {
		gc_die( 1 );
	}

	if ( 'trash-post' === $action ) {
		$done = gc_trash_post( $id );
	} else {
		$done = gc_untrash_post( $id );
	}

	if ( $done ) {
		gc_die( 1 );
	}

	gc_die( 0 );
}

/**
 * Handles restoring a post from the Trash via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_untrash_post( $action ) {
	if ( empty( $action ) ) {
		$action = 'untrash-post';
	}

	gc_ajax_trash_post( $action );
}

/**
 * Handles deleting a page via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_delete_page( $action ) {
	if ( empty( $action ) ) {
		$action = 'delete-page';
	}

	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	check_ajax_referer( "{$action}_$id" );

	if ( ! current_user_can( 'delete_page', $id ) ) {
		gc_die( -1 );
	}

	if ( ! get_post( $id ) ) {
		gc_die( 1 );
	}

	if ( gc_delete_post( $id ) ) {
		gc_die( 1 );
	} else {
		gc_die( 0 );
	}
}

/**
 * Handles dimming a comment via AJAX.
 *
 */
function gc_ajax_dim_comment() {
	$id      = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$comment = get_comment( $id );

	if ( ! $comment ) {
		$x = new GC_Ajax_Response(
			array(
				'what' => 'comment',
				'id'   => new GC_Error(
					'invalid_comment',
					/* translators: %d: Comment ID. */
					sprintf( __( '评论 %d 不存在' ), $id )
				),
			)
		);
		$x->send();
	}

	if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) && ! current_user_can( 'moderate_comments' ) ) {
		gc_die( -1 );
	}

	$current = gc_get_comment_status( $comment );

	if ( isset( $_POST['new'] ) && $_POST['new'] == $current ) {
		gc_die( time() );
	}

	check_ajax_referer( "approve-comment_$id" );

	if ( in_array( $current, array( 'unapproved', 'spam' ), true ) ) {
		$result = gc_set_comment_status( $comment, 'approve', true );
	} else {
		$result = gc_set_comment_status( $comment, 'hold', true );
	}

	if ( is_gc_error( $result ) ) {
		$x = new GC_Ajax_Response(
			array(
				'what' => 'comment',
				'id'   => $result,
			)
		);
		$x->send();
	}

	// Decide if we need to send back '1' or a more complicated response including page links and comment counts.
	_gc_ajax_delete_comment_response( $comment->comment_ID );
	gc_die( 0 );
}

/**
 * Handles adding a link category via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_add_link_category( $action ) {
	if ( empty( $action ) ) {
		$action = 'add-link-category';
	}

	check_ajax_referer( $action );

	$taxonomy_object = get_taxonomy( 'link_category' );

	if ( ! current_user_can( $taxonomy_object->cap->manage_terms ) ) {
		gc_die( -1 );
	}

	$names = explode( ',', gc_unslash( $_POST['newcat'] ) );
	$x     = new GC_Ajax_Response();

	foreach ( $names as $cat_name ) {
		$cat_name = trim( $cat_name );
		$slug     = sanitize_title( $cat_name );

		if ( '' === $slug ) {
			continue;
		}

		$cat_id = gc_insert_term( $cat_name, 'link_category' );

		if ( ! $cat_id || is_gc_error( $cat_id ) ) {
			continue;
		} else {
			$cat_id = $cat_id['term_id'];
		}

		$cat_name = esc_html( $cat_name );

		$x->add(
			array(
				'what'     => 'link-category',
				'id'       => $cat_id,
				'data'     => "<li id='link-category-$cat_id'><label for='in-link-category-$cat_id' class='selectit'><input value='" . esc_attr( $cat_id ) . "' type='checkbox' checked='checked' name='link_category[]' id='in-link-category-$cat_id'/> $cat_name</label></li>",
				'position' => -1,
			)
		);
	}
	$x->send();
}

/**
 * Handles adding a tag via AJAX.
 *
 */
function gc_ajax_add_tag() {
	check_ajax_referer( 'add-tag', '_gcnonce_add-tag' );

	$taxonomy        = ! empty( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : 'post_tag';
	$taxonomy_object = get_taxonomy( $taxonomy );

	if ( ! current_user_can( $taxonomy_object->cap->edit_terms ) ) {
		gc_die( -1 );
	}

	$x = new GC_Ajax_Response();

	$tag = gc_insert_term( $_POST['tag-name'], $taxonomy, $_POST );

	if ( $tag && ! is_gc_error( $tag ) ) {
		$tag = get_term( $tag['term_id'], $taxonomy );
	}

	if ( ! $tag || is_gc_error( $tag ) ) {
		$message    = __( '发生了错误，请刷新此页面并重试。' );
		$error_code = 'error';

		if ( is_gc_error( $tag ) && $tag->get_error_message() ) {
			$message = $tag->get_error_message();
		}

		if ( is_gc_error( $tag ) && $tag->get_error_code() ) {
			$error_code = $tag->get_error_code();
		}

		$x->add(
			array(
				'what' => 'taxonomy',
				'data' => new GC_Error( $error_code, $message ),
			)
		);
		$x->send();
	}

	$gc_list_table = _get_list_table( 'GC_Terms_List_Table', array( 'screen' => $_POST['screen'] ) );

	$level     = 0;
	$noparents = '';

	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$level = count( get_ancestors( $tag->term_id, $taxonomy, 'taxonomy' ) );
		ob_start();
		$gc_list_table->single_row( $tag, $level );
		$noparents = ob_get_clean();
	}

	ob_start();
	$gc_list_table->single_row( $tag );
	$parents = ob_get_clean();

	require ABSPATH . 'gc-admin/includes/edit-tag-messages.php';

	$message = '';
	if ( isset( $messages[ $taxonomy_object->name ][1] ) ) {
		$message = $messages[ $taxonomy_object->name ][1];
	} elseif ( isset( $messages['_item'][1] ) ) {
		$message = $messages['_item'][1];
	}

	$x->add(
		array(
			'what'         => 'taxonomy',
			'data'         => $message,
			'supplemental' => array(
				'parents'   => $parents,
				'noparents' => $noparents,
				'notice'    => $message,
			),
		)
	);

	$x->add(
		array(
			'what'         => 'term',
			'position'     => $level,
			'supplemental' => (array) $tag,
		)
	);

	$x->send();
}

/**
 * Handles getting a tagcloud via AJAX.
 *
 */
function gc_ajax_get_tagcloud() {
	if ( ! isset( $_POST['tax'] ) ) {
		gc_die( 0 );
	}

	$taxonomy        = sanitize_key( $_POST['tax'] );
	$taxonomy_object = get_taxonomy( $taxonomy );

	if ( ! $taxonomy_object ) {
		gc_die( 0 );
	}

	if ( ! current_user_can( $taxonomy_object->cap->assign_terms ) ) {
		gc_die( -1 );
	}

	$tags = get_terms(
		array(
			'taxonomy' => $taxonomy,
			'number'   => 45,
			'orderby'  => 'count',
			'order'    => 'DESC',
		)
	);

	if ( empty( $tags ) ) {
		gc_die( $taxonomy_object->labels->not_found );
	}

	if ( is_gc_error( $tags ) ) {
		gc_die( $tags->get_error_message() );
	}

	foreach ( $tags as $key => $tag ) {
		$tags[ $key ]->link = '#';
		$tags[ $key ]->id   = $tag->term_id;
	}

	// We need raw tag names here, so don't filter the output.
	$return = gc_generate_tag_cloud(
		$tags,
		array(
			'filter' => 0,
			'format' => 'list',
		)
	);

	if ( empty( $return ) ) {
		gc_die( 0 );
	}

	echo $return;
	gc_die();
}

/**
 * Handles getting comments via AJAX.
 *
 * @global int $post_id
 *
 * @param string $action Action to perform.
 */
function gc_ajax_get_comments( $action ) {
	global $post_id;

	if ( empty( $action ) ) {
		$action = 'get-comments';
	}

	check_ajax_referer( $action );

	if ( empty( $post_id ) && ! empty( $_REQUEST['p'] ) ) {
		$id = absint( $_REQUEST['p'] );
		if ( ! empty( $id ) ) {
			$post_id = $id;
		}
	}

	if ( empty( $post_id ) ) {
		gc_die( -1 );
	}

	$gc_list_table = _get_list_table( 'GC_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		gc_die( -1 );
	}

	$gc_list_table->prepare_items();

	if ( ! $gc_list_table->has_items() ) {
		gc_die( 1 );
	}

	$x = new GC_Ajax_Response();

	ob_start();
	foreach ( $gc_list_table->items as $comment ) {
		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) && 0 === $comment->comment_approved ) {
			continue;
		}
		get_comment( $comment );
		$gc_list_table->single_row( $comment );
	}
	$comment_list_item = ob_get_clean();

	$x->add(
		array(
			'what' => 'comments',
			'data' => $comment_list_item,
		)
	);

	$x->send();
}

/**
 * Handles replying to a comment via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_replyto_comment( $action ) {
	if ( empty( $action ) ) {
		$action = 'replyto-comment';
	}

	check_ajax_referer( $action, '_ajax_nonce-replyto-comment' );

	$comment_post_id = (int) $_POST['comment_post_ID'];
	$post            = get_post( $comment_post_id );

	if ( ! $post ) {
		gc_die( -1 );
	}

	if ( ! current_user_can( 'edit_post', $comment_post_id ) ) {
		gc_die( -1 );
	}

	if ( empty( $post->post_status ) ) {
		gc_die( 1 );
	} elseif ( in_array( $post->post_status, array( 'draft', 'pending', 'trash' ), true ) ) {
		gc_die( __( '您不能回复草稿文章上的评论。' ) );
	}

	$user = gc_get_current_user();

	if ( $user->exists() ) {
		$comment_author       = gc_slash( $user->display_name );
		$comment_author_email = gc_slash( $user->user_email );
		$comment_author_url   = gc_slash( $user->user_url );
		$user_id              = $user->ID;

		if ( current_user_can( 'unfiltered_html' ) ) {
			if ( ! isset( $_POST['_gc_unfiltered_html_comment'] ) ) {
				$_POST['_gc_unfiltered_html_comment'] = '';
			}

			if ( gc_create_nonce( 'unfiltered-html-comment' ) != $_POST['_gc_unfiltered_html_comment'] ) {
				kses_remove_filters(); // Start with a clean slate.
				kses_init_filters();   // Set up the filters.
				remove_filter( 'pre_comment_content', 'gc_filter_post_kses' );
				add_filter( 'pre_comment_content', 'gc_filter_kses' );
			}
		}
	} else {
		gc_die( __( '抱歉，回复评论需先登录。' ) );
	}

	$comment_content = trim( $_POST['content'] );

	if ( '' === $comment_content ) {
		gc_die( __( '请输入您的评论文字。' ) );
	}

	$comment_type = isset( $_POST['comment_type'] ) ? trim( $_POST['comment_type'] ) : 'comment';

	$comment_parent = 0;

	if ( isset( $_POST['comment_ID'] ) ) {
		$comment_parent = absint( $_POST['comment_ID'] );
	}

	$comment_auto_approved = false;

	$commentdata = array(
		'comment_post_ID' => $comment_post_id,
	);

	$commentdata += compact(
		'comment_author',
		'comment_author_email',
		'comment_author_url',
		'comment_content',
		'comment_type',
		'comment_parent',
		'user_id'
	);

	// Automatically approve parent comment.
	if ( ! empty( $_POST['approve_parent'] ) ) {
		$parent = get_comment( $comment_parent );

		if ( $parent && '0' === $parent->comment_approved && $parent->comment_post_ID == $comment_post_id ) {
			if ( ! current_user_can( 'edit_comment', $parent->comment_ID ) ) {
				gc_die( -1 );
			}

			if ( gc_set_comment_status( $parent, 'approve' ) ) {
				$comment_auto_approved = true;
			}
		}
	}

	$comment_id = gc_new_comment( $commentdata );

	if ( is_gc_error( $comment_id ) ) {
		gc_die( $comment_id->get_error_message() );
	}

	$comment = get_comment( $comment_id );

	if ( ! $comment ) {
		gc_die( 1 );
	}

	$position = ( isset( $_POST['position'] ) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';

	ob_start();
	if ( isset( $_REQUEST['mode'] ) && 'dashboard' === $_REQUEST['mode'] ) {
		require_once ABSPATH . 'gc-admin/includes/dashboard.php';
		_gc_dashboard_recent_comments_row( $comment );
	} else {
		if ( isset( $_REQUEST['mode'] ) && 'single' === $_REQUEST['mode'] ) {
			$gc_list_table = _get_list_table( 'GC_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );
		} else {
			$gc_list_table = _get_list_table( 'GC_Comments_List_Table', array( 'screen' => 'edit-comments' ) );
		}
		$gc_list_table->single_row( $comment );
	}
	$comment_list_item = ob_get_clean();

	$response = array(
		'what'     => 'comment',
		'id'       => $comment->comment_ID,
		'data'     => $comment_list_item,
		'position' => $position,
	);

	$counts                   = gc_count_comments();
	$response['supplemental'] = array(
		'in_moderation'        => $counts->moderated,
		'i18n_comments_text'   => sprintf(
			/* translators: %s: Number of comments. */
			_n( '%s条评论', '%s条评论', $counts->approved ),
			number_format_i18n( $counts->approved )
		),
		'i18n_moderation_text' => sprintf(
			/* translators: %s: Number of comments. */
			_n( '%s条评论待审', '%s条评论待审', $counts->moderated ),
			number_format_i18n( $counts->moderated )
		),
	);

	if ( $comment_auto_approved ) {
		$response['supplemental']['parent_approved'] = $parent->comment_ID;
		$response['supplemental']['parent_post_id']  = $parent->comment_post_ID;
	}

	$x = new GC_Ajax_Response();
	$x->add( $response );
	$x->send();
}

/**
 * Handles editing a comment via AJAX.
 *
 */
function gc_ajax_edit_comment() {
	check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

	$comment_id = (int) $_POST['comment_ID'];

	if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
		gc_die( -1 );
	}

	if ( '' === $_POST['content'] ) {
		gc_die( __( '请输入您的评论文字。' ) );
	}

	if ( isset( $_POST['status'] ) ) {
		$_POST['comment_status'] = $_POST['status'];
	}

	$updated = edit_comment();
	if ( is_gc_error( $updated ) ) {
		gc_die( $updated->get_error_message() );
	}

	$position      = ( isset( $_POST['position'] ) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';
	$checkbox      = ( isset( $_POST['checkbox'] ) && true == $_POST['checkbox'] ) ? 1 : 0;
	$gc_list_table = _get_list_table( $checkbox ? 'GC_Comments_List_Table' : 'GC_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );

	$comment = get_comment( $comment_id );

	if ( empty( $comment->comment_ID ) ) {
		gc_die( -1 );
	}

	ob_start();
	$gc_list_table->single_row( $comment );
	$comment_list_item = ob_get_clean();

	$x = new GC_Ajax_Response();

	$x->add(
		array(
			'what'     => 'edit_comment',
			'id'       => $comment->comment_ID,
			'data'     => $comment_list_item,
			'position' => $position,
		)
	);

	$x->send();
}

/**
 * Handles adding a menu item via AJAX.
 *
 */
function gc_ajax_add_menu_item() {
	check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	require_once ABSPATH . 'gc-admin/includes/nav-menu.php';

	/*
	 * For performance reasons, we omit some object properties from the checklist.
	 * The following is a hacky way to restore them when adding non-custom items.
	 */
	$menu_items_data = array();

	foreach ( (array) $_POST['menu-item'] as $menu_item_data ) {
		if (
			! empty( $menu_item_data['menu-item-type'] ) &&
			'custom' !== $menu_item_data['menu-item-type'] &&
			! empty( $menu_item_data['menu-item-object-id'] )
		) {
			switch ( $menu_item_data['menu-item-type'] ) {
				case 'post_type':
					$_object = get_post( $menu_item_data['menu-item-object-id'] );
					break;

				case 'post_type_archive':
					$_object = get_post_type_object( $menu_item_data['menu-item-object'] );
					break;

				case 'taxonomy':
					$_object = get_term( $menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object'] );
					break;
			}

			$_menu_items = array_map( 'gc_setup_nav_menu_item', array( $_object ) );
			$_menu_item  = reset( $_menu_items );

			// Restore the missing menu item properties.
			$menu_item_data['menu-item-description'] = $_menu_item->description;
		}

		$menu_items_data[] = $menu_item_data;
	}

	$item_ids = gc_save_nav_menu_items( 0, $menu_items_data );
	if ( is_gc_error( $item_ids ) ) {
		gc_die( 0 );
	}

	$menu_items = array();

	foreach ( (array) $item_ids as $menu_item_id ) {
		$menu_obj = get_post( $menu_item_id );

		if ( ! empty( $menu_obj->ID ) ) {
			$menu_obj        = gc_setup_nav_menu_item( $menu_obj );
			$menu_obj->title = empty( $menu_obj->title ) ? __( '菜单项' ) : $menu_obj->title;
			$menu_obj->label = $menu_obj->title; // Don't show "(pending)" in ajax-added items.
			$menu_items[]    = $menu_obj;
		}
	}

	/** This filter is documented in gc-admin/includes/nav-menu.php */
	$walker_class_name = apply_filters( 'gc_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST['menu'] );

	if ( ! class_exists( $walker_class_name ) ) {
		gc_die( 0 );
	}

	if ( ! empty( $menu_items ) ) {
		$args = array(
			'after'       => '',
			'before'      => '',
			'link_after'  => '',
			'link_before' => '',
			'walker'      => new $walker_class_name(),
		);

		echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
	}

	gc_die();
}

/**
 * Handles adding meta via AJAX.
 *
 */
function gc_ajax_add_meta() {
	check_ajax_referer( 'add-meta', '_ajax_nonce-add-meta' );
	$c    = 0;
	$pid  = (int) $_POST['post_id'];
	$post = get_post( $pid );

	if ( isset( $_POST['metakeyselect'] ) || isset( $_POST['metakeyinput'] ) ) {
		if ( ! current_user_can( 'edit_post', $pid ) ) {
			gc_die( -1 );
		}

		if ( isset( $_POST['metakeyselect'] ) && '#NONE#' === $_POST['metakeyselect'] && empty( $_POST['metakeyinput'] ) ) {
			gc_die( 1 );
		}

		// If the post is an autodraft, save the post as a draft and then attempt to save the meta.
		if ( 'auto-draft' === $post->post_status ) {
			$post_data                = array();
			$post_data['action']      = 'draft'; // Warning fix.
			$post_data['post_ID']     = $pid;
			$post_data['post_type']   = $post->post_type;
			$post_data['post_status'] = 'draft';
			$now                      = time();
			/* translators: 1: Post creation date, 2: Post creation time. */
			$post_data['post_title'] = sprintf( __( '草稿在%2$s于%1$s创建' ), gmdate( __( 'Y年n月j日' ), $now ), gmdate( __( 'ag:i' ), $now ) );

			$pid = edit_post( $post_data );

			if ( $pid ) {
				if ( is_gc_error( $pid ) ) {
					$x = new GC_Ajax_Response(
						array(
							'what' => 'meta',
							'data' => $pid,
						)
					);
					$x->send();
				}

				$mid = add_meta( $pid );
				if ( ! $mid ) {
					gc_die( __( '请输入一个自定义字段值。' ) );
				}
			} else {
				gc_die( 0 );
			}
		} else {
			$mid = add_meta( $pid );
			if ( ! $mid ) {
				gc_die( __( '请输入一个自定义字段值。' ) );
			}
		}

		$meta = get_metadata_by_mid( 'post', $mid );
		$pid  = (int) $meta->post_id;
		$meta = get_object_vars( $meta );

		$x = new GC_Ajax_Response(
			array(
				'what'         => 'meta',
				'id'           => $mid,
				'data'         => _list_meta_row( $meta, $c ),
				'position'     => 1,
				'supplemental' => array( 'postid' => $pid ),
			)
		);
	} else { // Update?
		$mid   = (int) key( $_POST['meta'] );
		$key   = gc_unslash( $_POST['meta'][ $mid ]['key'] );
		$value = gc_unslash( $_POST['meta'][ $mid ]['value'] );

		if ( '' === trim( $key ) ) {
			gc_die( __( '请输入一个自定义字段的名称。' ) );
		}

		$meta = get_metadata_by_mid( 'post', $mid );

		if ( ! $meta ) {
			gc_die( 0 ); // If meta doesn't exist.
		}

		if (
			is_protected_meta( $meta->meta_key, 'post' ) || is_protected_meta( $key, 'post' ) ||
			! current_user_can( 'edit_post_meta', $meta->post_id, $meta->meta_key ) ||
			! current_user_can( 'edit_post_meta', $meta->post_id, $key )
		) {
			gc_die( -1 );
		}

		if ( $meta->meta_value != $value || $meta->meta_key != $key ) {
			$u = update_metadata_by_mid( 'post', $mid, $value, $key );
			if ( ! $u ) {
				gc_die( 0 ); // We know meta exists; we also know it's unchanged (or DB error, in which case there are bigger problems).
			}
		}

		$x = new GC_Ajax_Response(
			array(
				'what'         => 'meta',
				'id'           => $mid,
				'old_id'       => $mid,
				'data'         => _list_meta_row(
					array(
						'meta_key'   => $key,
						'meta_value' => $value,
						'meta_id'    => $mid,
					),
					$c
				),
				'position'     => 0,
				'supplemental' => array( 'postid' => $meta->post_id ),
			)
		);
	}
	$x->send();
}

/**
 * Handles adding a user via AJAX.
 *
 * @param string $action Action to perform.
 */
function gc_ajax_add_user( $action ) {
	if ( empty( $action ) ) {
		$action = 'add-user';
	}

	check_ajax_referer( $action );

	if ( ! current_user_can( 'create_users' ) ) {
		gc_die( -1 );
	}

	$user_id = edit_user();

	if ( ! $user_id ) {
		gc_die( 0 );
	} elseif ( is_gc_error( $user_id ) ) {
		$x = new GC_Ajax_Response(
			array(
				'what' => 'user',
				'id'   => $user_id,
			)
		);
		$x->send();
	}

	$user_object   = get_userdata( $user_id );
	$gc_list_table = _get_list_table( 'GC_Users_List_Table' );

	$role = current( $user_object->roles );

	$x = new GC_Ajax_Response(
		array(
			'what'         => 'user',
			'id'           => $user_id,
			'data'         => $gc_list_table->single_row( $user_object, '', $role ),
			'supplemental' => array(
				'show-link' => sprintf(
					/* translators: %s: The new user. */
					__( '用户%s已添加' ),
					'<a href="#user-' . $user_id . '">' . $user_object->user_login . '</a>'
				),
				'role'      => $role,
			),
		)
	);
	$x->send();
}

/**
 * Handles closed post boxes via AJAX.
 *
 */
function gc_ajax_closed_postboxes() {
	check_ajax_referer( 'closedpostboxes', 'closedpostboxesnonce' );
	$closed = isset( $_POST['closed'] ) ? explode( ',', $_POST['closed'] ) : array();
	$closed = array_filter( $closed );

	$hidden = isset( $_POST['hidden'] ) ? explode( ',', $_POST['hidden'] ) : array();
	$hidden = array_filter( $hidden );

	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( sanitize_key( $page ) != $page ) {
		gc_die( 0 );
	}

	$user = gc_get_current_user();
	if ( ! $user ) {
		gc_die( -1 );
	}

	if ( is_array( $closed ) ) {
		update_user_meta( $user->ID, "closedpostboxes_$page", $closed );
	}

	if ( is_array( $hidden ) ) {
		// Postboxes that are always shown.
		$hidden = array_diff( $hidden, array( 'submitdiv', 'linksubmitdiv', 'manage-menu', 'create-menu' ) );
		update_user_meta( $user->ID, "metaboxhidden_$page", $hidden );
	}

	gc_die( 1 );
}

/**
 * Handles hidden columns via AJAX.
 *
 */
function gc_ajax_hidden_columns() {
	check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );
	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( sanitize_key( $page ) != $page ) {
		gc_die( 0 );
	}

	$user = gc_get_current_user();
	if ( ! $user ) {
		gc_die( -1 );
	}

	$hidden = ! empty( $_POST['hidden'] ) ? explode( ',', $_POST['hidden'] ) : array();
	update_user_meta( $user->ID, "manage{$page}columnshidden", $hidden );

	gc_die( 1 );
}

/**
 * Handles updating whether to display the welcome panel via AJAX.
 *
 */
function gc_ajax_update_welcome_panel() {
	check_ajax_referer( 'welcome-panel-nonce', 'welcomepanelnonce' );

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	update_user_meta( get_current_user_id(), 'show_welcome_panel', empty( $_POST['visible'] ) ? 0 : 1 );

	gc_die( 1 );
}

/**
 * Handles for retrieving menu meta boxes via AJAX.
 *
 */
function gc_ajax_menu_get_metabox() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	require_once ABSPATH . 'gc-admin/includes/nav-menu.php';

	if ( isset( $_POST['item-type'] ) && 'post_type' === $_POST['item-type'] ) {
		$type     = 'posttype';
		$callback = 'gc_nav_menu_item_post_type_meta_box';
		$items    = (array) get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
	} elseif ( isset( $_POST['item-type'] ) && 'taxonomy' === $_POST['item-type'] ) {
		$type     = 'taxonomy';
		$callback = 'gc_nav_menu_item_taxonomy_meta_box';
		$items    = (array) get_taxonomies( array( 'show_ui' => true ), 'object' );
	}

	if ( ! empty( $_POST['item-object'] ) && isset( $items[ $_POST['item-object'] ] ) ) {
		$menus_meta_box_object = $items[ $_POST['item-object'] ];

		/** This filter is documented in gc-admin/includes/nav-menu.php */
		$item = apply_filters( 'nav_menu_meta_box_object', $menus_meta_box_object );

		$box_args = array(
			'id'       => 'add-' . $item->name,
			'title'    => $item->labels->name,
			'callback' => $callback,
			'args'     => $item,
		);

		ob_start();
		$callback( null, $box_args );

		$markup = ob_get_clean();

		echo gc_json_encode(
			array(
				'replace-id' => $type . '-' . $item->name,
				'markup'     => $markup,
			)
		);
	}

	gc_die();
}

/**
 * Handles internal linking via AJAX.
 *
 */
function gc_ajax_gc_link_ajax() {
	check_ajax_referer( 'internal-linking', '_ajax_linking_nonce' );

	$args = array();

	if ( isset( $_POST['search'] ) ) {
		$args['s'] = gc_unslash( $_POST['search'] );
	}

	if ( isset( $_POST['term'] ) ) {
		$args['s'] = gc_unslash( $_POST['term'] );
	}

	$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

	if ( ! class_exists( '_GC_Editors', false ) ) {
		require ABSPATH . GCINC . '/class-gc-editor.php';
	}

	$results = _GC_Editors::gc_link_query( $args );

	if ( ! isset( $results ) ) {
		gc_die( 0 );
	}

	echo gc_json_encode( $results );
	echo "\n";

	gc_die();
}

/**
 * Handles saving menu locations via AJAX.
 *
 */
function gc_ajax_menu_locations_save() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

	if ( ! isset( $_POST['menu-locations'] ) ) {
		gc_die( 0 );
	}

	set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_POST['menu-locations'] ) );
	gc_die( 1 );
}

/**
 * Handles saving the meta box order via AJAX.
 *
 */
function gc_ajax_meta_box_order() {
	check_ajax_referer( 'meta-box-order' );
	$order        = isset( $_POST['order'] ) ? (array) $_POST['order'] : false;
	$page_columns = isset( $_POST['page_columns'] ) ? $_POST['page_columns'] : 'auto';

	if ( 'auto' !== $page_columns ) {
		$page_columns = (int) $page_columns;
	}

	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( sanitize_key( $page ) != $page ) {
		gc_die( 0 );
	}

	$user = gc_get_current_user();
	if ( ! $user ) {
		gc_die( -1 );
	}

	if ( $order ) {
		update_user_meta( $user->ID, "meta-box-order_$page", $order );
	}

	if ( $page_columns ) {
		update_user_meta( $user->ID, "screen_layout_$page", $page_columns );
	}

	gc_send_json_success();
}

/**
 * Handles menu quick searching via AJAX.
 *
 */
function gc_ajax_menu_quick_search() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	require_once ABSPATH . 'gc-admin/includes/nav-menu.php';

	_gc_ajax_menu_quick_search( $_POST );

	gc_die();
}

/**
 * Handles retrieving a permalink via AJAX.
 *
 */
function gc_ajax_get_permalink() {
	check_ajax_referer( 'getpermalink', 'getpermalinknonce' );
	$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	gc_die( get_preview_post_link( $post_id ) );
}

/**
 * Handles retrieving a sample permalink via AJAX.
 *
 */
function gc_ajax_sample_permalink() {
	check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
	$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	$title   = isset( $_POST['new_title'] ) ? $_POST['new_title'] : '';
	$slug    = isset( $_POST['new_slug'] ) ? $_POST['new_slug'] : null;
	gc_die( get_sample_permalink_html( $post_id, $title, $slug ) );
}

/**
 * Handles Quick Edit saving a post from a list table via AJAX.
 *
 * @global string $mode List table view mode.
 */
function gc_ajax_inline_save() {
	global $mode;

	check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

	if ( ! isset( $_POST['post_ID'] ) || ! (int) $_POST['post_ID'] ) {
		gc_die();
	}

	$post_id = (int) $_POST['post_ID'];

	if ( 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			gc_die( __( '抱歉，您不能编辑此页面。' ) );
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			gc_die( __( '抱歉，您不能修改这篇文章。' ) );
		}
	}

	$last = gc_check_post_lock( $post_id );
	if ( $last ) {
		$last_user      = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __( '有人' );

		/* translators: %s: User's display name. */
		$msg_template = __( '无法保存：『%s』正在编辑这篇文章。' );

		if ( 'page' === $_POST['post_type'] ) {
			/* translators: %s: User's display name. */
			$msg_template = __( '无法保存：『%s』正在编辑这个页面。' );
		}

		printf( $msg_template, esc_html( $last_user_name ) );
		gc_die();
	}

	$data = &$_POST;

	$post = get_post( $post_id, ARRAY_A );

	// Since it's coming from the database.
	$post = gc_slash( $post );

	$data['content'] = $post['post_content'];
	$data['excerpt'] = $post['post_excerpt'];

	// Rename.
	$data['user_ID'] = get_current_user_id();

	if ( isset( $data['post_parent'] ) ) {
		$data['parent_id'] = $data['post_parent'];
	}

	// Status.
	if ( isset( $data['keep_private'] ) && 'private' === $data['keep_private'] ) {
		$data['visibility']  = 'private';
		$data['post_status'] = 'private';
	} else {
		$data['post_status'] = $data['_status'];
	}

	if ( empty( $data['comment_status'] ) ) {
		$data['comment_status'] = 'closed';
	}

	if ( empty( $data['ping_status'] ) ) {
		$data['ping_status'] = 'closed';
	}

	// Exclude terms from taxonomies that are not supposed to appear in Quick Edit.
	if ( ! empty( $data['tax_input'] ) ) {
		foreach ( $data['tax_input'] as $taxonomy => $terms ) {
			$tax_object = get_taxonomy( $taxonomy );
			/** This filter is documented in gc-admin/includes/class-gc-posts-list-table.php */
			if ( ! apply_filters( 'quick_edit_show_taxonomy', $tax_object->show_in_quick_edit, $taxonomy, $post['post_type'] ) ) {
				unset( $data['tax_input'][ $taxonomy ] );
			}
		}
	}

	// Hack: gc_unique_post_slug() doesn't work for drafts, so we will fake that our post is published.
	if ( ! empty( $data['post_name'] ) && in_array( $post['post_status'], array( 'draft', 'pending' ), true ) ) {
		$post['post_status'] = 'publish';
		$data['post_name']   = gc_unique_post_slug( $data['post_name'], $post['ID'], $post['post_status'], $post['post_type'], $post['post_parent'] );
	}

	// Update the post.
	edit_post();

	$gc_list_table = _get_list_table( 'GC_Posts_List_Table', array( 'screen' => $_POST['screen'] ) );

	$mode = 'excerpt' === $_POST['post_view'] ? 'excerpt' : 'list';

	$level = 0;
	if ( is_post_type_hierarchical( $gc_list_table->screen->post_type ) ) {
		$request_post = array( get_post( $_POST['post_ID'] ) );
		$parent       = $request_post[0]->post_parent;

		while ( $parent > 0 ) {
			$parent_post = get_post( $parent );
			$parent      = $parent_post->post_parent;
			$level++;
		}
	}

	$gc_list_table->display_rows( array( get_post( $_POST['post_ID'] ) ), $level );

	gc_die();
}

/**
 * Handles Quick Edit saving for a term via AJAX.
 *
 */
function gc_ajax_inline_save_tax() {
	check_ajax_referer( 'taxinlineeditnonce', '_inline_edit' );

	$taxonomy        = sanitize_key( $_POST['taxonomy'] );
	$taxonomy_object = get_taxonomy( $taxonomy );

	if ( ! $taxonomy_object ) {
		gc_die( 0 );
	}

	if ( ! isset( $_POST['tax_ID'] ) || ! (int) $_POST['tax_ID'] ) {
		gc_die( -1 );
	}

	$id = (int) $_POST['tax_ID'];

	if ( ! current_user_can( 'edit_term', $id ) ) {
		gc_die( -1 );
	}

	$gc_list_table = _get_list_table( 'GC_Terms_List_Table', array( 'screen' => 'edit-' . $taxonomy ) );

	$tag                  = get_term( $id, $taxonomy );
	$_POST['description'] = $tag->description;

	$updated = gc_update_term( $id, $taxonomy, $_POST );

	if ( $updated && ! is_gc_error( $updated ) ) {
		$tag = get_term( $updated['term_id'], $taxonomy );
		if ( ! $tag || is_gc_error( $tag ) ) {
			if ( is_gc_error( $tag ) && $tag->get_error_message() ) {
				gc_die( $tag->get_error_message() );
			}
			gc_die( __( '项目未更新。' ) );
		}
	} else {
		if ( is_gc_error( $updated ) && $updated->get_error_message() ) {
			gc_die( $updated->get_error_message() );
		}
		gc_die( __( '项目未更新。' ) );
	}

	$level  = 0;
	$parent = $tag->parent;

	while ( $parent > 0 ) {
		$parent_tag = get_term( $parent, $taxonomy );
		$parent     = $parent_tag->parent;
		$level++;
	}

	$gc_list_table->single_row( $tag, $level );
	gc_die();
}

/**
 * Handles querying posts for the Find Posts modal via AJAX.
 *
 * @see window.findPosts
 *
 */
function gc_ajax_find_posts() {
	check_ajax_referer( 'find-posts' );

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	unset( $post_types['attachment'] );

	$args = array(
		'post_type'      => array_keys( $post_types ),
		'post_status'    => 'any',
		'posts_per_page' => 50,
	);

	$search = gc_unslash( $_POST['ps'] );

	if ( '' !== $search ) {
		$args['s'] = $search;
	}

	$posts = get_posts( $args );

	if ( ! $posts ) {
		gc_send_json_error( __( '找不到条目。' ) );
	}

	$html = '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>' . __( '标题' ) . '</th><th class="no-break">' . __( '类型' ) . '</th><th class="no-break">' . __( '日期' ) . '</th><th class="no-break">' . __( '状态' ) . '</th></tr></thead><tbody>';
	$alt  = '';
	foreach ( $posts as $post ) {
		$title = trim( $post->post_title ) ? $post->post_title : __( '（无标题）' );
		$alt   = ( 'alternate' === $alt ) ? '' : 'alternate';

		switch ( $post->post_status ) {
			case 'publish':
			case 'private':
				$stat = __( '已发布' );
				break;
			case 'future':
				$stat = __( '已计划' );
				break;
			case 'pending':
				$stat = __( '等待复审' );
				break;
			case 'draft':
				$stat = __( '草稿' );
				break;
		}

		if ( '0000-00-00 00:00:00' === $post->post_date ) {
			$time = '';
		} else {
			/* translators: Date format in table columns, see https://www.php.net/manual/datetime.format.php */
			$time = mysql2date( __( 'Y-m-d' ), $post->post_date );
		}

		$html .= '<tr class="' . trim( 'found-posts ' . $alt ) . '"><td class="found-radio"><input type="radio" id="found-' . $post->ID . '" name="found_post_id" value="' . esc_attr( $post->ID ) . '"></td>';
		$html .= '<td><label for="found-' . $post->ID . '">' . esc_html( $title ) . '</label></td><td class="no-break">' . esc_html( $post_types[ $post->post_type ]->labels->singular_name ) . '</td><td class="no-break">' . esc_html( $time ) . '</td><td class="no-break">' . esc_html( $stat ) . ' </td></tr>' . "\n\n";
	}

	$html .= '</tbody></table>';

	gc_send_json_success( $html );
}

/**
 * Handles saving the widgets order via AJAX.
 *
 */
function gc_ajax_widgets_order() {
	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	unset( $_POST['savewidgets'], $_POST['action'] );

	// Save widgets order for all sidebars.
	if ( is_array( $_POST['sidebars'] ) ) {
		$sidebars = array();

		foreach ( gc_unslash( $_POST['sidebars'] ) as $key => $val ) {
			$sb = array();

			if ( ! empty( $val ) ) {
				$val = explode( ',', $val );

				foreach ( $val as $k => $v ) {
					if ( ! str_contains( $v, 'widget-' ) ) {
						continue;
					}

					$sb[ $k ] = substr( $v, strpos( $v, '_' ) + 1 );
				}
			}
			$sidebars[ $key ] = $sb;
		}

		gc_set_sidebars_widgets( $sidebars );
		gc_die( 1 );
	}

	gc_die( -1 );
}

/**
 * Handles saving a widget via AJAX.
 *
 * @global array $gc_registered_widgets
 * @global array $gc_registered_widget_controls
 * @global array $gc_registered_widget_updates
 */
function gc_ajax_save_widget() {
	global $gc_registered_widgets, $gc_registered_widget_controls, $gc_registered_widget_updates;

	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST['id_base'] ) ) {
		gc_die( -1 );
	}

	unset( $_POST['savewidgets'], $_POST['action'] );

	/**
	 * Fires early when editing the widgets displayed in sidebars.
	 *
	 */
	do_action( 'load-widgets.php' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

	/**
	 * Fires early when editing the widgets displayed in sidebars.
	 *
	 */
	do_action( 'widgets.php' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

	/** This action is documented in gc-admin/widgets.php */
	do_action( 'sidebar_admin_setup' );

	$id_base      = gc_unslash( $_POST['id_base'] );
	$widget_id    = gc_unslash( $_POST['widget-id'] );
	$sidebar_id   = $_POST['sidebar'];
	$multi_number = ! empty( $_POST['multi_number'] ) ? (int) $_POST['multi_number'] : 0;
	$settings     = isset( $_POST[ 'widget-' . $id_base ] ) && is_array( $_POST[ 'widget-' . $id_base ] ) ? $_POST[ 'widget-' . $id_base ] : false;
	$error        = '<p>' . __( '发生了错误，请刷新此页面并重试。' ) . '</p>';

	$sidebars = gc_get_sidebars_widgets();
	$sidebar  = isset( $sidebars[ $sidebar_id ] ) ? $sidebars[ $sidebar_id ] : array();

	// Delete.
	if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] ) {

		if ( ! isset( $gc_registered_widgets[ $widget_id ] ) ) {
			gc_die( $error );
		}

		$sidebar = array_diff( $sidebar, array( $widget_id ) );
		$_POST   = array(
			'sidebar'            => $sidebar_id,
			'widget-' . $id_base => array(),
			'the-widget-id'      => $widget_id,
			'delete_widget'      => '1',
		);

		/** This action is documented in gc-admin/widgets.php */
		do_action( 'delete_widget', $widget_id, $sidebar_id, $id_base );

	} elseif ( $settings && preg_match( '/__i__|%i%/', key( $settings ) ) ) {
		if ( ! $multi_number ) {
			gc_die( $error );
		}

		$_POST[ 'widget-' . $id_base ] = array( $multi_number => reset( $settings ) );
		$widget_id                     = $id_base . '-' . $multi_number;
		$sidebar[]                     = $widget_id;
	}
	$_POST['widget-id'] = $sidebar;

	foreach ( (array) $gc_registered_widget_updates as $name => $control ) {

		if ( $name == $id_base ) {
			if ( ! is_callable( $control['callback'] ) ) {
				continue;
			}

			ob_start();
				call_user_func_array( $control['callback'], $control['params'] );
			ob_end_clean();
			break;
		}
	}

	if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] ) {
		$sidebars[ $sidebar_id ] = $sidebar;
		gc_set_sidebars_widgets( $sidebars );
		echo "deleted:$widget_id";
		gc_die();
	}

	if ( ! empty( $_POST['add_new'] ) ) {
		gc_die();
	}

	$form = $gc_registered_widget_controls[ $widget_id ];
	if ( $form ) {
		call_user_func_array( $form['callback'], $form['params'] );
	}

	gc_die();
}

/**
 * Handles updating a widget via AJAX.
 *
 * @since 3.9.0
 *
 * @global GC_Customize_Manager $gc_customize
 */
function gc_ajax_update_widget() {
	global $gc_customize;
	$gc_customize->widgets->gc_ajax_update_widget();
}

/**
 * Handles removing inactive widgets via AJAX.
 *
 */
function gc_ajax_delete_inactive_widgets() {
	check_ajax_referer( 'remove-inactive-widgets', 'removeinactivewidgets' );

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		gc_die( -1 );
	}

	unset( $_POST['removeinactivewidgets'], $_POST['action'] );
	/** This action is documented in gc-admin/includes/ajax-actions.php */
	do_action( 'load-widgets.php' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	/** This action is documented in gc-admin/includes/ajax-actions.php */
	do_action( 'widgets.php' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
	/** This action is documented in gc-admin/widgets.php */
	do_action( 'sidebar_admin_setup' );

	$sidebars_widgets = gc_get_sidebars_widgets();

	foreach ( $sidebars_widgets['gc_inactive_widgets'] as $key => $widget_id ) {
		$pieces       = explode( '-', $widget_id );
		$multi_number = array_pop( $pieces );
		$id_base      = implode( '-', $pieces );
		$widget       = get_option( 'widget_' . $id_base );
		unset( $widget[ $multi_number ] );
		update_option( 'widget_' . $id_base, $widget );
		unset( $sidebars_widgets['gc_inactive_widgets'][ $key ] );
	}

	gc_set_sidebars_widgets( $sidebars_widgets );

	gc_die();
}

/**
 * Handles creating missing image sub-sizes for just uploaded images via AJAX.
 *
 * @since 5.3.0
 */
function gc_ajax_media_create_image_subsizes() {
	check_ajax_referer( 'media-form' );

	if ( ! current_user_can( 'upload_files' ) ) {
		gc_send_json_error( array( 'message' => __( '抱歉，您不能上传文件。' ) ) );
	}

	if ( empty( $_POST['attachment_id'] ) ) {
		gc_send_json_error( array( 'message' => __( '上传失败，请刷新后再试。' ) ) );
	}

	$attachment_id = (int) $_POST['attachment_id'];

	if ( ! empty( $_POST['_gc_upload_failed_cleanup'] ) ) {
		// Upload failed. Cleanup.
		if ( gc_attachment_is_image( $attachment_id ) && current_user_can( 'delete_post', $attachment_id ) ) {
			$attachment = get_post( $attachment_id );

			// Created at most 10 min ago.
			if ( $attachment && ( time() - strtotime( $attachment->post_date_gmt ) < 600 ) ) {
				gc_delete_attachment( $attachment_id, true );
				gc_send_json_success();
			}
		}
	}

	/*
	 * Set a custom header with the attachment_id.
	 * Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
	 */
	if ( ! headers_sent() ) {
		header( 'X-GC-Upload-Attachment-ID: ' . $attachment_id );
	}

	/*
	 * This can still be pretty slow and cause timeout or out of memory errors.
	 * The js that handles the response would need to also handle HTTP 500 errors.
	 */
	gc_update_image_subsizes( $attachment_id );

	if ( ! empty( $_POST['_legacy_support'] ) ) {
		// The old (inline) uploader. Only needs the attachment_id.
		$response = array( 'id' => $attachment_id );
	} else {
		// Media modal and Media Library grid view.
		$response = gc_prepare_attachment_for_js( $attachment_id );

		if ( ! $response ) {
			gc_send_json_error( array( 'message' => __( '上传失败。' ) ) );
		}
	}

	// At this point the image has been uploaded successfully.
	gc_send_json_success( $response );
}

/**
 * Handles uploading attachments via AJAX.
 *
 */
function gc_ajax_upload_attachment() {
	check_ajax_referer( 'media-form' );
	/*
	 * This function does not use gc_send_json_success() / gc_send_json_error()
	 * as the html4 Plupload handler requires a text/html Content-Type for older IE.
	 * See https://core.trac.gechiui.com/ticket/31037
	 */

	if ( ! current_user_can( 'upload_files' ) ) {
		echo gc_json_encode(
			array(
				'success' => false,
				'data'    => array(
					'message'  => __( '抱歉，您不能上传文件。' ),
					'filename' => esc_html( $_FILES['async-upload']['name'] ),
				),
			)
		);

		gc_die();
	}

	if ( isset( $_REQUEST['post_id'] ) ) {
		$post_id = $_REQUEST['post_id'];

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			echo gc_json_encode(
				array(
					'success' => false,
					'data'    => array(
						'message'  => __( '抱歉，您不能添加附件到此文章。' ),
						'filename' => esc_html( $_FILES['async-upload']['name'] ),
					),
				)
			);

			gc_die();
		}
	} else {
		$post_id = null;
	}

	$post_data = ! empty( $_REQUEST['post_data'] ) ? _gc_get_allowed_postdata( _gc_translate_postdata( false, (array) $_REQUEST['post_data'] ) ) : array();

	if ( is_gc_error( $post_data ) ) {
		gc_die( $post_data->get_error_message() );
	}

	// If the context is custom header or background, make sure the uploaded file is an image.
	if ( isset( $post_data['context'] ) && in_array( $post_data['context'], array( 'custom-header', 'custom-background' ), true ) ) {
		$gc_filetype = gc_check_filetype_and_ext( $_FILES['async-upload']['tmp_name'], $_FILES['async-upload']['name'] );

		if ( ! gc_match_mime_types( 'image', $gc_filetype['type'] ) ) {
			echo gc_json_encode(
				array(
					'success' => false,
					'data'    => array(
						'message'  => __( '上传的文件不是有效的图片。请重试。' ),
						'filename' => esc_html( $_FILES['async-upload']['name'] ),
					),
				)
			);

			gc_die();
		}
	}

	$attachment_id = media_handle_upload( 'async-upload', $post_id, $post_data );

	if ( is_gc_error( $attachment_id ) ) {
		echo gc_json_encode(
			array(
				'success' => false,
				'data'    => array(
					'message'  => $attachment_id->get_error_message(),
					'filename' => esc_html( $_FILES['async-upload']['name'] ),
				),
			)
		);

		gc_die();
	}

	if ( isset( $post_data['context'] ) && isset( $post_data['theme'] ) ) {
		if ( 'custom-background' === $post_data['context'] ) {
			update_post_meta( $attachment_id, '_gc_attachment_is_custom_background', $post_data['theme'] );
		}

		if ( 'custom-header' === $post_data['context'] ) {
			update_post_meta( $attachment_id, '_gc_attachment_is_custom_header', $post_data['theme'] );
		}
	}

	$attachment = gc_prepare_attachment_for_js( $attachment_id );
	if ( ! $attachment ) {
		gc_die();
	}

	echo gc_json_encode(
		array(
			'success' => true,
			'data'    => $attachment,
		)
	);

	gc_die();
}

/**
 * Handles image editing via AJAX.
 *
 */
function gc_ajax_image_editor() {
	$attachment_id = (int) $_POST['postid'];

	if ( empty( $attachment_id ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
		gc_die( -1 );
	}

	check_ajax_referer( "image_editor-$attachment_id" );
	require_once ABSPATH . 'gc-admin/includes/image-edit.php';

	$msg = false;

	switch ( $_POST['do'] ) {
		case 'save':
			$msg = gc_save_image( $attachment_id );
			if ( ! empty( $msg->error ) ) {
				gc_send_json_error( $msg );
			}

			gc_send_json_success( $msg );
			break;
		case 'scale':
			$msg = gc_save_image( $attachment_id );
			break;
		case 'restore':
			$msg = gc_restore_image( $attachment_id );
			break;
	}

	ob_start();
	gc_image_editor( $attachment_id, $msg );
	$html = ob_get_clean();

	if ( ! empty( $msg->error ) ) {
		gc_send_json_error(
			array(
				'message' => $msg,
				'html'    => $html,
			)
		);
	}

	gc_send_json_success(
		array(
			'message' => $msg,
			'html'    => $html,
		)
	);
}

/**
 * Handles setting the featured image via AJAX.
 *
 */
function gc_ajax_set_post_thumbnail() {
	$json = ! empty( $_REQUEST['json'] ); // New-style request.

	$post_id = (int) $_POST['post_id'];
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		gc_die( -1 );
	}

	$thumbnail_id = (int) $_POST['thumbnail_id'];

	if ( $json ) {
		check_ajax_referer( "update-post_$post_id" );
	} else {
		check_ajax_referer( "set_post_thumbnail-$post_id" );
	}

	if ( '-1' == $thumbnail_id ) {
		if ( delete_post_thumbnail( $post_id ) ) {
			$return = _gc_post_thumbnail_html( null, $post_id );
			$json ? gc_send_json_success( $return ) : gc_die( $return );
		} else {
			gc_die( 0 );
		}
	}

	if ( set_post_thumbnail( $post_id, $thumbnail_id ) ) {
		$return = _gc_post_thumbnail_html( $thumbnail_id, $post_id );
		$json ? gc_send_json_success( $return ) : gc_die( $return );
	}

	gc_die( 0 );
}

/**
 * Handles retrieving HTML for the featured image via AJAX.
 *
 */
function gc_ajax_get_post_thumbnail_html() {
	$post_id = (int) $_POST['post_id'];

	check_ajax_referer( "update-post_$post_id" );

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		gc_die( -1 );
	}

	$thumbnail_id = (int) $_POST['thumbnail_id'];

	// For backward compatibility, -1 refers to no featured image.
	if ( -1 === $thumbnail_id ) {
		$thumbnail_id = null;
	}

	$return = _gc_post_thumbnail_html( $thumbnail_id, $post_id );
	gc_send_json_success( $return );
}

/**
 * Handles setting the featured image for an attachment via AJAX.
 *
 * @since 4.0.0
 *
 * @see set_post_thumbnail()
 */
function gc_ajax_set_attachment_thumbnail() {
	if ( empty( $_POST['urls'] ) || ! is_array( $_POST['urls'] ) ) {
		gc_send_json_error();
	}

	$thumbnail_id = (int) $_POST['thumbnail_id'];
	if ( empty( $thumbnail_id ) ) {
		gc_send_json_error();
	}

	if ( false === check_ajax_referer( 'set-attachment-thumbnail', '_ajax_nonce', false ) ) {
		gc_send_json_error();
	}

	$post_ids = array();
	// For each URL, try to find its corresponding post ID.
	foreach ( $_POST['urls'] as $url ) {
		$post_id = attachment_url_to_postid( $url );
		if ( ! empty( $post_id ) ) {
			$post_ids[] = $post_id;
		}
	}

	if ( empty( $post_ids ) ) {
		gc_send_json_error();
	}

	$success = 0;
	// For each found attachment, set its thumbnail.
	foreach ( $post_ids as $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			continue;
		}

		if ( set_post_thumbnail( $post_id, $thumbnail_id ) ) {
			$success++;
		}
	}

	if ( 0 === $success ) {
		gc_send_json_error();
	} else {
		gc_send_json_success();
	}

	gc_send_json_error();
}

/**
 * Handles formatting a date via AJAX.
 *
 */
function gc_ajax_date_format() {
	gc_die( date_i18n( sanitize_option( 'date_format', gc_unslash( $_POST['date'] ) ) ) );
}

/**
 * Handles formatting a time via AJAX.
 *
 */
function gc_ajax_time_format() {
	gc_die( date_i18n( sanitize_option( 'time_format', gc_unslash( $_POST['date'] ) ) ) );
}

/**
 * Handles saving posts from the fullscreen editor via AJAX.
 *
 * @deprecated 4.3.0
 */
function gc_ajax_gc_fullscreen_save_post() {
	$post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

	$post = null;

	if ( $post_id ) {
		$post = get_post( $post_id );
	}

	check_ajax_referer( 'update-post_' . $post_id, '_gcnonce' );

	$post_id = edit_post();

	if ( is_gc_error( $post_id ) ) {
		gc_send_json_error();
	}

	if ( $post ) {
		$last_date = mysql2date( __( 'Y年n月j日' ), $post->post_modified );
		$last_time = mysql2date( __( 'ag:i' ), $post->post_modified );
	} else {
		$last_date = date_i18n( __( 'Y年n月j日' ) );
		$last_time = date_i18n( __( 'ag:i' ) );
	}

	$last_id = get_post_meta( $post_id, '_edit_last', true );
	if ( $last_id ) {
		$last_user = get_userdata( $last_id );
		/* translators: 1: User's display name, 2: Date of last edit, 3: Time of last edit. */
		$last_edited = sprintf( __( '最后由 %1$s 编辑于 %2$s%3$s' ), esc_html( $last_user->display_name ), $last_date, $last_time );
	} else {
		/* translators: 1: Date of last edit, 2: Time of last edit. */
		$last_edited = sprintf( __( '最后编辑于 %1$s %2$s' ), $last_date, $last_time );
	}

	gc_send_json_success( array( 'last_edited' => $last_edited ) );
}

/**
 * Handles removing a post lock via AJAX.
 *
 */
function gc_ajax_gc_remove_post_lock() {
	if ( empty( $_POST['post_ID'] ) || empty( $_POST['active_post_lock'] ) ) {
		gc_die( 0 );
	}

	$post_id = (int) $_POST['post_ID'];
	$post    = get_post( $post_id );

	if ( ! $post ) {
		gc_die( 0 );
	}

	check_ajax_referer( 'update-post_' . $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		gc_die( -1 );
	}

	$active_lock = array_map( 'absint', explode( ':', $_POST['active_post_lock'] ) );

	if ( get_current_user_id() != $active_lock[1] ) {
		gc_die( 0 );
	}

	/**
	 * Filters the post lock window duration.
	 *
	 *
	 * @param int $interval The interval in seconds the post lock duration
	 *                      should last, plus 5 seconds. Default 150.
	 */
	$new_lock = ( time() - apply_filters( 'gc_check_post_lock_window', 150 ) + 5 ) . ':' . $active_lock[1];
	update_post_meta( $post_id, '_edit_lock', $new_lock, implode( ':', $active_lock ) );
	gc_die( 1 );
}

/**
 * Handles dismissing a GeChiUI pointer via AJAX.
 *
 */
function gc_ajax_dismiss_gc_pointer() {
	$pointer = $_POST['pointer'];

	if ( sanitize_key( $pointer ) != $pointer ) {
		gc_die( 0 );
	}

	//  check_ajax_referer( 'dismiss-pointer_' . $pointer );

	$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_gc_pointers', true ) ) );

	if ( in_array( $pointer, $dismissed, true ) ) {
		gc_die( 0 );
	}

	$dismissed[] = $pointer;
	$dismissed   = implode( ',', $dismissed );

	update_user_meta( get_current_user_id(), 'dismissed_gc_pointers', $dismissed );
	gc_die( 1 );
}

/**
 * Handles getting an attachment via AJAX.
 *
 */
function gc_ajax_get_attachment() {
	if ( ! isset( $_REQUEST['id'] ) ) {
		gc_send_json_error();
	}

	$id = absint( $_REQUEST['id'] );
	if ( ! $id ) {
		gc_send_json_error();
	}

	$post = get_post( $id );
	if ( ! $post ) {
		gc_send_json_error();
	}

	if ( 'attachment' !== $post->post_type ) {
		gc_send_json_error();
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		gc_send_json_error();
	}

	$attachment = gc_prepare_attachment_for_js( $id );
	if ( ! $attachment ) {
		gc_send_json_error();
	}

	gc_send_json_success( $attachment );
}

/**
 * Handles querying attachments via AJAX.
 *
 */
function gc_ajax_query_attachments() {
	if ( ! current_user_can( 'upload_files' ) ) {
		gc_send_json_error();
	}

	$query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();
	$keys  = array(
		's',
		'order',
		'orderby',
		'posts_per_page',
		'paged',
		'post_mime_type',
		'post_parent',
		'author',
		'post__in',
		'post__not_in',
		'year',
		'monthnum',
	);

	foreach ( get_taxonomies_for_attachments( 'objects' ) as $t ) {
		if ( $t->query_var && isset( $query[ $t->query_var ] ) ) {
			$keys[] = $t->query_var;
		}
	}

	$query              = array_intersect_key( $query, array_flip( $keys ) );
	$query['post_type'] = 'attachment';

	if (
		MEDIA_TRASH &&
		! empty( $_REQUEST['query']['post_status'] ) &&
		'trash' === $_REQUEST['query']['post_status']
	) {
		$query['post_status'] = 'trash';
	} else {
		$query['post_status'] = 'inherit';
	}

	if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) ) {
		$query['post_status'] .= ',private';
	}

	// Filter query clauses to include filenames.
	if ( isset( $query['s'] ) ) {
		add_filter( 'gc_allow_query_attachment_by_filename', '__return_true' );
	}

	/**
	 * Filters the arguments passed to GC_Query during an Ajax
	 * call for querying attachments.
	 *
	 * @since 3.7.0
	 *
	 * @see GC_Query::parse_query()
	 *
	 * @param array $query An array of query variables.
	 */
	$query             = apply_filters( 'ajax_query_attachments_args', $query );
	$attachments_query = new GC_Query( $query );
	update_post_parent_caches( $attachments_query->posts );

	$posts       = array_map( 'gc_prepare_attachment_for_js', $attachments_query->posts );
	$posts       = array_filter( $posts );
	$total_posts = $attachments_query->found_posts;

	if ( $total_posts < 1 ) {
		// Out-of-bounds, run the query again without LIMIT for total count.
		unset( $query['paged'] );

		$count_query = new GC_Query();
		$count_query->query( $query );
		$total_posts = $count_query->found_posts;
	}

	$posts_per_page = (int) $attachments_query->get( 'posts_per_page' );

	$max_pages = $posts_per_page ? ceil( $total_posts / $posts_per_page ) : 0;

	header( 'X-GC-Total: ' . (int) $total_posts );
	header( 'X-GC-TotalPages: ' . (int) $max_pages );

	gc_send_json_success( $posts );
}

/**
 * Handles updating attachment attributes via AJAX.
 *
 */
function gc_ajax_save_attachment() {
	if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['changes'] ) ) {
		gc_send_json_error();
	}

	$id = absint( $_REQUEST['id'] );
	if ( ! $id ) {
		gc_send_json_error();
	}

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) ) {
		gc_send_json_error();
	}

	$changes = $_REQUEST['changes'];
	$post    = get_post( $id, ARRAY_A );

	if ( 'attachment' !== $post['post_type'] ) {
		gc_send_json_error();
	}

	if ( isset( $changes['parent'] ) ) {
		$post['post_parent'] = $changes['parent'];
	}

	if ( isset( $changes['title'] ) ) {
		$post['post_title'] = $changes['title'];
	}

	if ( isset( $changes['caption'] ) ) {
		$post['post_excerpt'] = $changes['caption'];
	}

	if ( isset( $changes['description'] ) ) {
		$post['post_content'] = $changes['description'];
	}

	if ( MEDIA_TRASH && isset( $changes['status'] ) ) {
		$post['post_status'] = $changes['status'];
	}

	if ( isset( $changes['alt'] ) ) {
		$alt = gc_unslash( $changes['alt'] );
		if ( get_post_meta( $id, '_gc_attachment_image_alt', true ) !== $alt ) {
			$alt = gc_strip_all_tags( $alt, true );
			update_post_meta( $id, '_gc_attachment_image_alt', gc_slash( $alt ) );
		}
	}

	if ( gc_attachment_is( 'audio', $post['ID'] ) ) {
		$changed = false;
		$id3data = gc_get_attachment_metadata( $post['ID'] );

		if ( ! is_array( $id3data ) ) {
			$changed = true;
			$id3data = array();
		}

		foreach ( gc_get_attachment_id3_keys( (object) $post, 'edit' ) as $key => $label ) {
			if ( isset( $changes[ $key ] ) ) {
				$changed         = true;
				$id3data[ $key ] = sanitize_text_field( gc_unslash( $changes[ $key ] ) );
			}
		}

		if ( $changed ) {
			gc_update_attachment_metadata( $id, $id3data );
		}
	}

	if ( MEDIA_TRASH && isset( $changes['status'] ) && 'trash' === $changes['status'] ) {
		gc_delete_post( $id );
	} else {
		gc_update_post( $post );
	}

	gc_send_json_success();
}

/**
 * Handles saving backward compatible attachment attributes via AJAX.
 *
 */
function gc_ajax_save_attachment_compat() {
	if ( ! isset( $_REQUEST['id'] ) ) {
		gc_send_json_error();
	}

	$id = absint( $_REQUEST['id'] );
	if ( ! $id ) {
		gc_send_json_error();
	}

	if ( empty( $_REQUEST['attachments'] ) || empty( $_REQUEST['attachments'][ $id ] ) ) {
		gc_send_json_error();
	}

	$attachment_data = $_REQUEST['attachments'][ $id ];

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) ) {
		gc_send_json_error();
	}

	$post = get_post( $id, ARRAY_A );

	if ( 'attachment' !== $post['post_type'] ) {
		gc_send_json_error();
	}

	/** This filter is documented in gc-admin/includes/media.php */
	$post = apply_filters( 'attachment_fields_to_save', $post, $attachment_data );

	if ( isset( $post['errors'] ) ) {
		$errors = $post['errors']; // @todo return me and display me!
		unset( $post['errors'] );
	}

	gc_update_post( $post );

	foreach ( get_attachment_taxonomies( $post ) as $taxonomy ) {
		if ( isset( $attachment_data[ $taxonomy ] ) ) {
			gc_set_object_terms( $id, array_map( 'trim', preg_split( '/,+/', $attachment_data[ $taxonomy ] ) ), $taxonomy, false );
		}
	}

	$attachment = gc_prepare_attachment_for_js( $id );

	if ( ! $attachment ) {
		gc_send_json_error();
	}

	gc_send_json_success( $attachment );
}

/**
 * Handles saving the attachment order via AJAX.
 *
 */
function gc_ajax_save_attachment_order() {
	if ( ! isset( $_REQUEST['post_id'] ) ) {
		gc_send_json_error();
	}

	$post_id = absint( $_REQUEST['post_id'] );
	if ( ! $post_id ) {
		gc_send_json_error();
	}

	if ( empty( $_REQUEST['attachments'] ) ) {
		gc_send_json_error();
	}

	check_ajax_referer( 'update-post_' . $post_id, 'nonce' );

	$attachments = $_REQUEST['attachments'];

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		gc_send_json_error();
	}

	foreach ( $attachments as $attachment_id => $menu_order ) {
		if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
			continue;
		}

		$attachment = get_post( $attachment_id );

		if ( ! $attachment ) {
			continue;
		}

		if ( 'attachment' !== $attachment->post_type ) {
			continue;
		}

		gc_update_post(
			array(
				'ID'         => $attachment_id,
				'menu_order' => $menu_order,
			)
		);
	}

	gc_send_json_success();
}

/**
 * Handles sending an attachment to the editor via AJAX.
 *
 * Generates the HTML to send an attachment to the editor.
 * Backward compatible with the {@see 'media_send_to_editor'} filter
 * and the chain of filters that follow.
 *
 */
function gc_ajax_send_attachment_to_editor() {
	check_ajax_referer( 'media-send-to-editor', 'nonce' );

	$attachment = gc_unslash( $_POST['attachment'] );

	$id = (int) $attachment['id'];

	$post = get_post( $id );
	if ( ! $post ) {
		gc_send_json_error();
	}

	if ( 'attachment' !== $post->post_type ) {
		gc_send_json_error();
	}

	if ( current_user_can( 'edit_post', $id ) ) {
		// If this attachment is unattached, attach it. Primarily a back compat thing.
		$insert_into_post_id = (int) $_POST['post_id'];

		if ( 0 == $post->post_parent && $insert_into_post_id ) {
			gc_update_post(
				array(
					'ID'          => $id,
					'post_parent' => $insert_into_post_id,
				)
			);
		}
	}

	$url = empty( $attachment['url'] ) ? '' : $attachment['url'];
	$rel = ( str_contains( $url, 'attachment_id' ) || get_attachment_link( $id ) === $url );

	remove_filter( 'media_send_to_editor', 'image_media_send_to_editor' );

	if ( str_starts_with( $post->post_mime_type, 'image' ) ) {
		$align = isset( $attachment['align'] ) ? $attachment['align'] : 'none';
		$size  = isset( $attachment['image-size'] ) ? $attachment['image-size'] : 'medium';
		$alt   = isset( $attachment['image_alt'] ) ? $attachment['image_alt'] : '';

		// No whitespace-only captions.
		$caption = isset( $attachment['post_excerpt'] ) ? $attachment['post_excerpt'] : '';
		if ( '' === trim( $caption ) ) {
			$caption = '';
		}

		$title = ''; // We no longer insert title tags into <img> tags, as they are redundant.
		$html  = get_image_send_to_editor( $id, $caption, $title, $align, $url, $rel, $size, $alt );
	} elseif ( gc_attachment_is( 'video', $post ) || gc_attachment_is( 'audio', $post ) ) {
		$html = stripslashes_deep( $_POST['html'] );
	} else {
		$html = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
		$rel  = $rel ? ' rel="attachment gc-att-' . $id . '"' : ''; // Hard-coded string, $id is already sanitized.

		if ( ! empty( $url ) ) {
			$html = '<a href="' . esc_url( $url ) . '"' . $rel . '>' . $html . '</a>';
		}
	}

	/** This filter is documented in gc-admin/includes/media.php */
	$html = apply_filters( 'media_send_to_editor', $html, $id, $attachment );

	gc_send_json_success( $html );
}

/**
 * Handles sending a link to the editor via AJAX.
 *
 * Generates the HTML to send a non-image embed link to the editor.
 *
 * Backward compatible with the following filters:
 * - file_send_to_editor_url
 * - audio_send_to_editor_url
 * - video_send_to_editor_url
 *
 * @global GC_Post  $post     Global post object.
 * @global GC_Embed $gc_embed
 */
function gc_ajax_send_link_to_editor() {
	global $post, $gc_embed;

	check_ajax_referer( 'media-send-to-editor', 'nonce' );

	$src = gc_unslash( $_POST['src'] );
	if ( ! $src ) {
		gc_send_json_error();
	}

	if ( ! strpos( $src, '://' ) ) {
		$src = 'http://' . $src;
	}

	$src = sanitize_url( $src );
	if ( ! $src ) {
		gc_send_json_error();
	}

	$link_text = trim( gc_unslash( $_POST['link_text'] ) );
	if ( ! $link_text ) {
		$link_text = gc_basename( $src );
	}

	$post = get_post( isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0 );

	// Ping GeChiUI for an embed.
	$check_embed = $gc_embed->run_shortcode( '[embed]' . $src . '[/embed]' );

	// Fallback that GeChiUI creates when no oEmbed was found.
	$fallback = $gc_embed->maybe_make_link( $src );

	if ( $check_embed !== $fallback ) {
		// TinyMCE view for [embed] will parse this.
		$html = '[embed]' . $src . '[/embed]';
	} elseif ( $link_text ) {
		$html = '<a href="' . esc_url( $src ) . '">' . $link_text . '</a>';
	} else {
		$html = '';
	}

	// Figure out what filter to run:
	$type = 'file';
	$ext  = preg_replace( '/^.+?\.([^.]+)$/', '$1', $src );
	if ( $ext ) {
		$ext_type = gc_ext2type( $ext );
		if ( 'audio' === $ext_type || 'video' === $ext_type ) {
			$type = $ext_type;
		}
	}

	/** This filter is documented in gc-admin/includes/media.php */
	$html = apply_filters( "{$type}_send_to_editor_url", $html, $src, $link_text );

	gc_send_json_success( $html );
}

/**
 * Handles the Heartbeat API via AJAX.
 *
 * Runs when the user is logged in.
 *
 */
function gc_ajax_heartbeat() {
	if ( empty( $_POST['_nonce'] ) ) {
		gc_send_json_error();
	}

	$response    = array();
	$data        = array();
	$nonce_state = gc_verify_nonce( $_POST['_nonce'], 'heartbeat-nonce' );

	// 'screen_id' is the same as $current_screen->id and the JS global 'pagenow'.
	if ( ! empty( $_POST['screen_id'] ) ) {
		$screen_id = sanitize_key( $_POST['screen_id'] );
	} else {
		$screen_id = 'front';
	}

	if ( ! empty( $_POST['data'] ) ) {
		$data = gc_unslash( (array) $_POST['data'] );
	}

	if ( 1 !== $nonce_state ) {
		/**
		 * Filters the nonces to send to the New/Edit Post screen.
		 *
		 * @since 4.3.0
		 *
		 * @param array  $response  The Heartbeat response.
		 * @param array  $data      The $_POST data sent.
		 * @param string $screen_id The screen ID.
		 */
		$response = apply_filters( 'gc_refresh_nonces', $response, $data, $screen_id );

		if ( false === $nonce_state ) {
			// User is logged in but nonces have expired.
			$response['nonces_expired'] = true;
			gc_send_json( $response );
		}
	}

	if ( ! empty( $data ) ) {
		/**
		 * Filters the Heartbeat response received.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $response  The Heartbeat response.
		 * @param array  $data      The $_POST data sent.
		 * @param string $screen_id The screen ID.
		 */
		$response = apply_filters( 'heartbeat_received', $response, $data, $screen_id );
	}

	/**
	 * Filters the Heartbeat response sent.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $response  The Heartbeat response.
	 * @param string $screen_id The screen ID.
	 */
	$response = apply_filters( 'heartbeat_send', $response, $screen_id );

	/**
	 * Fires when Heartbeat ticks in logged-in environments.
	 *
	 * Allows the transport to be easily replaced with long-polling.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $response  The Heartbeat response.
	 * @param string $screen_id The screen ID.
	 */
	do_action( 'heartbeat_tick', $response, $screen_id );

	// Send the current time according to the server.
	$response['server_time'] = time();

	gc_send_json( $response );
}

/**
 * Handles getting revision diffs via AJAX.
 *
 */
function gc_ajax_get_revision_diffs() {
	require ABSPATH . 'gc-admin/includes/revision.php';

	$post = get_post( (int) $_REQUEST['post_id'] );
	if ( ! $post ) {
		gc_send_json_error();
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		gc_send_json_error();
	}

	// Really just pre-loading the cache here.
	$revisions = gc_get_post_revisions( $post->ID, array( 'check_enabled' => false ) );
	if ( ! $revisions ) {
		gc_send_json_error();
	}

	$return = array();

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}

	foreach ( $_REQUEST['compare'] as $compare_key ) {
		list( $compare_from, $compare_to ) = explode( ':', $compare_key ); // from:to

		$return[] = array(
			'id'     => $compare_key,
			'fields' => gc_get_revision_ui_diff( $post, $compare_from, $compare_to ),
		);
	}
	gc_send_json_success( $return );
}

/**
 * Handles auto-saving the selected color scheme for
 * a user's own profile via AJAX.
 *
 * @since 3.8.0
 *
 * @global array $_gc_admin_css_colors
 */
function gc_ajax_save_user_color_scheme() {
	global $_gc_admin_css_colors;

	check_ajax_referer( 'save-color-scheme', 'nonce' );

	$color_scheme = sanitize_key( $_POST['color_scheme'] );

	if ( ! isset( $_gc_admin_css_colors[ $color_scheme ] ) ) {
		gc_send_json_error();
	}

	$previous_color_scheme = get_user_meta( get_current_user_id(), 'admin_color', true );
	update_user_meta( get_current_user_id(), 'admin_color', $color_scheme );

	gc_send_json_success(
		array(
			'previousScheme' => 'admin-color-' . $previous_color_scheme,
			'currentScheme'  => 'admin-color-' . $color_scheme,
		)
	);
}

/**
 * Handles getting themes from themes_api() via AJAX.
 *
 * @since 3.9.0
 *
 * @global array $themes_allowedtags
 * @global array $theme_field_defaults
 */
function gc_ajax_query_themes() {
	global $themes_allowedtags, $theme_field_defaults;

	if ( ! current_user_can( 'install_themes' ) ) {
		gc_send_json_error();
	}

	$args = gc_parse_args(
		gc_unslash( $_REQUEST['request'] ),
		array(
			'per_page' => 20,
			'fields'   => array_merge(
				(array) $theme_field_defaults,
				array(
					'reviews_url' => true, // Explicitly request the reviews URL to be linked from the Add Themes screen.
				)
			),
		)
	);

	if ( isset( $args['browse'] ) && 'favorites' === $args['browse'] && ! isset( $args['user'] ) ) {
		$user = get_user_option( 'gcorg_favorites' );
		if ( $user ) {
			$args['user'] = $user;
		}
	}

	$old_filter = isset( $args['browse'] ) ? $args['browse'] : 'search';

	/** This filter is documented in gc-admin/includes/class-gc-theme-install-list-table.php */
	$args = apply_filters( 'install_themes_table_api_args_' . $old_filter, $args );

	$api = themes_api( 'query_themes', $args );

	if ( is_gc_error( $api ) ) {
		gc_send_json_error();
	}

	$update_php = network_admin_url( 'update.php?action=install-theme' );

	$installed_themes = search_theme_directories();

	if ( false === $installed_themes ) {
		$installed_themes = array();
	}

	foreach ( $installed_themes as $theme_slug => $theme_data ) {
		// Ignore child themes.
		if ( str_contains( $theme_slug, '/' ) ) {
			unset( $installed_themes[ $theme_slug ] );
		}
	}

	foreach ( $api->themes as &$theme ) {
		$theme->install_url = add_query_arg(
			array(
				'theme'    => $theme->slug,
				'_gcnonce' => gc_create_nonce( 'install-theme_' . $theme->slug ),
			),
			$update_php
		);

		if ( current_user_can( 'switch_themes' ) ) {
			if ( is_multisite() ) {
				$theme->activate_url = add_query_arg(
					array(
						'action'   => 'enable',
						'_gcnonce' => gc_create_nonce( 'enable-theme_' . $theme->slug ),
						'theme'    => $theme->slug,
					),
					network_admin_url( 'themes.php' )
				);
			} else {
				$theme->activate_url = add_query_arg(
					array(
						'action'     => 'activate',
						'_gcnonce'   => gc_create_nonce( 'switch-theme_' . $theme->slug ),
						'stylesheet' => $theme->slug,
					),
					admin_url( 'themes.php' )
				);
			}
		}

		$is_theme_installed = array_key_exists( $theme->slug, $installed_themes );

		// We only care about installed themes.
		$theme->block_theme = $is_theme_installed && gc_get_theme( $theme->slug )->is_block_theme();

		if ( ! is_multisite() && current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			$customize_url = $theme->block_theme ? admin_url( 'site-editor.php' ) : gc_customize_url( $theme->slug );

			$theme->customize_url = add_query_arg(
				array(
					'return' => urlencode( network_admin_url( 'theme-install.php', 'relative' ) ),
				),
				$customize_url
			);
		}

		$theme->name        = gc_kses( $theme->name, $themes_allowedtags );
		$theme->author      = gc_kses( $theme->author['display_name'], $themes_allowedtags );
		$theme->version     = gc_kses( $theme->version, $themes_allowedtags );
		$theme->description = gc_kses( $theme->description, $themes_allowedtags );

		$theme->stars = gc_star_rating(
			array(
				'rating' => $theme->rating,
				'type'   => 'percent',
				'number' => $theme->num_ratings,
				'echo'   => false,
			)
		);

		$theme->num_ratings    = number_format_i18n( $theme->num_ratings );
		$theme->preview_url    = set_url_scheme( $theme->preview_url );
		$theme->compatible_gc  = is_gc_version_compatible( $theme->requires );
		$theme->compatible_php = is_php_version_compatible( $theme->requires_php );
	}

	gc_send_json_success( $api );
}

/**
 * Applies [embed] Ajax handlers to a string.
 *
 * @since 4.0.0
 *
 * @global GC_Post    $post       Global post object.
 * @global GC_Embed   $gc_embed   Embed API instance.
 * @global GC_Scripts $gc_scripts
 * @global int        $content_width
 */
function gc_ajax_parse_embed() {
	global $post, $gc_embed, $content_width;

	if ( empty( $_POST['shortcode'] ) ) {
		gc_send_json_error();
	}

	$post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

	if ( $post_id > 0 ) {
		$post = get_post( $post_id );

		if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
			gc_send_json_error();
		}
		setup_postdata( $post );
	} elseif ( ! current_user_can( 'edit_posts' ) ) { // See GC_oEmbed_Controller::get_proxy_item_permissions_check().
		gc_send_json_error();
	}

	$shortcode = gc_unslash( $_POST['shortcode'] );

	preg_match( '/' . get_shortcode_regex() . '/s', $shortcode, $matches );
	$atts = shortcode_parse_atts( $matches[3] );

	if ( ! empty( $matches[5] ) ) {
		$url = $matches[5];
	} elseif ( ! empty( $atts['src'] ) ) {
		$url = $atts['src'];
	} else {
		$url = '';
	}

	$parsed                         = false;
	$gc_embed->return_false_on_fail = true;

	if ( 0 === $post_id ) {
		/*
		 * Refresh oEmbeds cached outside of posts that are past their TTL.
		 * Posts are excluded because they have separate logic for refreshing
		 * their post meta caches. See GC_Embed::cache_oembed().
		 */
		$gc_embed->usecache = false;
	}

	if ( is_ssl() && str_starts_with( $url, 'http://' ) ) {
		/*
		 * Admin is ssl and the user pasted non-ssl URL.
		 * Check if the provider supports ssl embeds and use that for the preview.
		 */
		$ssl_shortcode = preg_replace( '%^(\\[embed[^\\]]*\\])http://%i', '$1https://', $shortcode );
		$parsed        = $gc_embed->run_shortcode( $ssl_shortcode );

		if ( ! $parsed ) {
			$no_ssl_support = true;
		}
	}

	// Set $content_width so any embeds fit in the destination iframe.
	if ( isset( $_POST['maxwidth'] ) && is_numeric( $_POST['maxwidth'] ) && $_POST['maxwidth'] > 0 ) {
		if ( ! isset( $content_width ) ) {
			$content_width = (int) $_POST['maxwidth'];
		} else {
			$content_width = min( $content_width, (int) $_POST['maxwidth'] );
		}
	}

	if ( $url && ! $parsed ) {
		$parsed = $gc_embed->run_shortcode( $shortcode );
	}

	if ( ! $parsed ) {
		gc_send_json_error(
			array(
				'type'    => 'not-embeddable',
				/* translators: %s: URL that could not be embedded. */
				'message' => sprintf( __( '嵌入%s失败。' ), '<code>' . esc_html( $url ) . '</code>' ),
			)
		);
	}

	if ( has_shortcode( $parsed, 'audio' ) || has_shortcode( $parsed, 'video' ) ) {
		$styles     = '';
		$mce_styles = gcview_media_sandbox_styles();

		foreach ( $mce_styles as $style ) {
			$styles .= sprintf( '<link rel="stylesheet" href="%s" />', $style );
		}

		$html = do_shortcode( $parsed );

		global $gc_scripts;

		if ( ! empty( $gc_scripts ) ) {
			$gc_scripts->done = array();
		}

		ob_start();
		gc_print_scripts( array( 'mediaelement-vimeo', 'gc-mediaelement' ) );
		$scripts = ob_get_clean();

		$parsed = $styles . $html . $scripts;
	}

	if ( ! empty( $no_ssl_support ) || ( is_ssl() && ( preg_match( '%<(iframe|script|embed) [^>]*src="http://%', $parsed ) ||
		preg_match( '%<link [^>]*href="http://%', $parsed ) ) ) ) {
		// Admin is ssl and the embed is not. Iframes, scripts, and other "active content" will be blocked.
		gc_send_json_error(
			array(
				'type'    => 'not-ssl',
				'message' => __( '此预览在编辑器中不可用。' ),
			)
		);
	}

	$return = array(
		'body' => $parsed,
		'attr' => $gc_embed->last_attr,
	);

	if ( str_contains( $parsed, 'class="gc-embedded-content' ) ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$script_src = assets_url( 'js/gc-embed.js' );
		} else {
			$script_src = assets_url( 'js/gc-embed.min.js' );
		}

		$return['head']    = '<script src="' . $script_src . '"></script>';
		$return['sandbox'] = true;
	}

	gc_send_json_success( $return );
}

/**
 * @since 4.0.0
 *
 * @global GC_Post    $post       Global post object.
 * @global GC_Scripts $gc_scripts
 */
function gc_ajax_parse_media_shortcode() {
	global $post, $gc_scripts;

	if ( empty( $_POST['shortcode'] ) ) {
		gc_send_json_error();
	}

	$shortcode = gc_unslash( $_POST['shortcode'] );

	if ( ! empty( $_POST['post_ID'] ) ) {
		$post = get_post( (int) $_POST['post_ID'] );
	}

	// The embed shortcode requires a post.
	if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
		if ( 'embed' === $shortcode ) {
			gc_send_json_error();
		}
	} else {
		setup_postdata( $post );
	}

	$parsed = do_shortcode( $shortcode );

	if ( empty( $parsed ) ) {
		gc_send_json_error(
			array(
				'type'    => 'no-items',
				'message' => __( '找不到条目。' ),
			)
		);
	}

	$head   = '';
	$styles = gcview_media_sandbox_styles();

	foreach ( $styles as $style ) {
		$head .= '<link type="text/css" rel="stylesheet" href="' . $style . '">';
	}

	if ( ! empty( $gc_scripts ) ) {
		$gc_scripts->done = array();
	}

	ob_start();

	echo $parsed;

	if ( 'playlist' === $_REQUEST['type'] ) {
		gc_underscore_playlist_templates();

		gc_print_scripts( 'gc-playlist' );
	} else {
		gc_print_scripts( array( 'mediaelement-vimeo', 'gc-mediaelement' ) );
	}

	gc_send_json_success(
		array(
			'head' => $head,
			'body' => ob_get_clean(),
		)
	);
}

/**
 * Handles destroying multiple open sessions for a user via AJAX.
 *
 * @since 4.1.0
 */
function gc_ajax_destroy_sessions() {
	$user = get_userdata( (int) $_POST['user_id'] );

	if ( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			$user = false;
		} elseif ( ! gc_verify_nonce( $_POST['nonce'], 'update-user_' . $user->ID ) ) {
			$user = false;
		}
	}

	if ( ! $user ) {
		gc_send_json_error(
			array(
				'message' => __( '无法注销用户会话，请重试。' ),
			)
		);
	}

	$sessions = GC_Session_Tokens::get_instance( $user->ID );

	if ( get_current_user_id() === $user->ID ) {
		$sessions->destroy_others( gc_get_session_token() );
		$message = __( '您已成功注销除此之外的所有会话。' );
	} else {
		$sessions->destroy_all();
		/* translators: %s: User's display name. */
		$message = sprintf( __( '已成功注销%s的会话。' ), $user->display_name );
	}

	gc_send_json_success( array( 'message' => $message ) );
}

/**
 * Handles cropping an image via AJAX.
 *
 * @since 4.3.0
 */
function gc_ajax_crop_image() {
	$attachment_id = absint( $_POST['id'] );

	check_ajax_referer( 'image_editor-' . $attachment_id, 'nonce' );

	if ( empty( $attachment_id ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
		gc_send_json_error();
	}

	$context = str_replace( '_', '-', $_POST['context'] );
	$data    = array_map( 'absint', $_POST['cropDetails'] );
	$cropped = gc_crop_image( $attachment_id, $data['x1'], $data['y1'], $data['width'], $data['height'], $data['dst_width'], $data['dst_height'] );

	if ( ! $cropped || is_gc_error( $cropped ) ) {
		gc_send_json_error( array( 'message' => __( '图片不能被处理。' ) ) );
	}

	switch ( $context ) {
		case 'site-icon':
			require_once ABSPATH . 'gc-admin/includes/class-gc-site-icon.php';
			$gc_site_icon = new GC_Site_Icon();

			// Skip creating a new attachment if the attachment is a Site Icon.
			if ( get_post_meta( $attachment_id, '_gc_attachment_context', true ) == $context ) {

				// Delete the temporary cropped file, we don't need it.
				gc_delete_file( $cropped );

				// Additional sizes in gc_prepare_attachment_for_js().
				add_filter( 'image_size_names_choose', array( $gc_site_icon, 'additional_sizes' ) );
				break;
			}

			/** This filter is documented in gc-admin/includes/class-custom-image-header.php */
			$cropped    = apply_filters( 'gc_create_file_in_uploads', $cropped, $attachment_id ); // For replication.
			$attachment = $gc_site_icon->create_attachment_object( $cropped, $attachment_id );
			unset( $attachment['ID'] );

			// Update the attachment.
			add_filter( 'intermediate_image_sizes_advanced', array( $gc_site_icon, 'additional_sizes' ) );
			$attachment_id = $gc_site_icon->insert_attachment( $attachment, $cropped );
			remove_filter( 'intermediate_image_sizes_advanced', array( $gc_site_icon, 'additional_sizes' ) );

			// Additional sizes in gc_prepare_attachment_for_js().
			add_filter( 'image_size_names_choose', array( $gc_site_icon, 'additional_sizes' ) );
			break;

		default:
			/**
			 * Fires before a cropped image is saved.
			 *
			 * Allows to add filters to modify the way a cropped image is saved.
			 *
			 * @since 4.3.0
			 *
			 * @param string $context       The Customizer control requesting the cropped image.
			 * @param int    $attachment_id The attachment ID of the original image.
			 * @param string $cropped       Path to the cropped image file.
			 */
			do_action( 'gc_ajax_crop_image_pre_save', $context, $attachment_id, $cropped );

			/** This filter is documented in gc-admin/includes/class-custom-image-header.php */
			$cropped = apply_filters( 'gc_create_file_in_uploads', $cropped, $attachment_id ); // For replication.

			$parent_url      = gc_get_attachment_url( $attachment_id );
			$parent_basename = gc_basename( $parent_url );
			$url             = str_replace( $parent_basename, gc_basename( $cropped ), $parent_url );

			$size       = gc_getimagesize( $cropped );
			$image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

			// Get the original image's post to pre-populate the cropped image.
			$original_attachment  = get_post( $attachment_id );
			$sanitized_post_title = sanitize_file_name( $original_attachment->post_title );
			$use_original_title   = (
				( '' !== trim( $original_attachment->post_title ) ) &&
				/*
				 * Check if the original image has a title other than the "filename" default,
				 * meaning the image had a title when originally uploaded or its title was edited.
				 */
				( $parent_basename !== $sanitized_post_title ) &&
				( pathinfo( $parent_basename, PATHINFO_FILENAME ) !== $sanitized_post_title )
			);
			$use_original_description = ( '' !== trim( $original_attachment->post_content ) );

			$attachment = array(
				'post_title'     => $use_original_title ? $original_attachment->post_title : gc_basename( $cropped ),
				'post_content'   => $use_original_description ? $original_attachment->post_content : $url,
				'post_mime_type' => $image_type,
				'guid'           => $url,
				'context'        => $context,
			);

			// Copy the image caption attribute (post_excerpt field) from the original image.
			if ( '' !== trim( $original_attachment->post_excerpt ) ) {
				$attachment['post_excerpt'] = $original_attachment->post_excerpt;
			}

			// Copy the image alt text attribute from the original image.
			if ( '' !== trim( $original_attachment->_gc_attachment_image_alt ) ) {
				$attachment['meta_input'] = array(
					'_gc_attachment_image_alt' => gc_slash( $original_attachment->_gc_attachment_image_alt ),
				);
			}

			$attachment_id = gc_insert_attachment( $attachment, $cropped );
			$metadata      = gc_generate_attachment_metadata( $attachment_id, $cropped );

			/**
			 * Filters the cropped image attachment metadata.
			 *
			 * @since 4.3.0
			 *
			 * @see gc_generate_attachment_metadata()
			 *
			 * @param array $metadata Attachment metadata.
			 */
			$metadata = apply_filters( 'gc_ajax_cropped_attachment_metadata', $metadata );
			gc_update_attachment_metadata( $attachment_id, $metadata );

			/**
			 * Filters the attachment ID for a cropped image.
			 *
			 * @since 4.3.0
			 *
			 * @param int    $attachment_id The attachment ID of the cropped image.
			 * @param string $context       The Customizer control requesting the cropped image.
			 */
			$attachment_id = apply_filters( 'gc_ajax_cropped_attachment_id', $attachment_id, $context );
	}

	gc_send_json_success( gc_prepare_attachment_for_js( $attachment_id ) );
}

/**
 * Handles generating a password via AJAX.
 *
 */
function gc_ajax_generate_password() {
	gc_send_json_success( gc_generate_password( 24 ) );
}

/**
 * Handles generating a password in the no-privilege context via AJAX.
 *
 * @since 5.7.0
 */
function gc_ajax_nopriv_generate_password() {
	gc_send_json_success( gc_generate_password( 24 ) );
}

/**
 * Handles saving the user's www.GeChiUI.com username via AJAX.
 *
 */
function gc_ajax_save_gcorg_username() {
	if ( ! current_user_can( 'install_themes' ) && ! current_user_can( 'install_plugins' ) ) {
		gc_send_json_error();
	}

	check_ajax_referer( 'save_gcorg_username_' . get_current_user_id() );

	$username = isset( $_REQUEST['username'] ) ? gc_unslash( $_REQUEST['username'] ) : false;

	if ( ! $username ) {
		gc_send_json_error();
	}

	gc_send_json_success( update_user_meta( get_current_user_id(), 'gcorg_favorites', $username ) );
}

/**
 * Handles installing a theme via AJAX.
 *
 * @see Theme_Upgrader
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_install_theme() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['slug'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_theme_specified',
				'errorMessage' => __( '未指定主题。' ),
			)
		);
	}

	$slug = sanitize_key( gc_unslash( $_POST['slug'] ) );

	$status = array(
		'install' => 'theme',
		'slug'    => $slug,
	);

	if ( ! current_user_can( 'install_themes' ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上安装主题。' );
		gc_send_json_error( $status );
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	require_once ABSPATH . 'gc-admin/includes/theme.php';

	$api = themes_api(
		'theme_information',
		array(
			'slug'   => $slug,
			'fields' => array( 'sections' => false ),
		)
	);

	if ( is_gc_error( $api ) ) {
		$status['errorMessage'] = $api->get_error_message();
		gc_send_json_error( $status );
	}

	$skin     = new GC_Ajax_Upgrader_Skin();
	$upgrader = new Theme_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );

	if ( defined( 'GC_DEBUG' ) && GC_DEBUG ) {
		$status['debug'] = $skin->get_upgrade_messages();
	}

	if ( is_gc_error( $result ) ) {
		$status['errorCode']    = $result->get_error_code();
		$status['errorMessage'] = $result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( is_gc_error( $skin->result ) ) {
		$status['errorCode']    = $skin->result->get_error_code();
		$status['errorMessage'] = $skin->result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( $skin->get_errors()->has_errors() ) {
		$status['errorMessage'] = $skin->get_error_messages();
		gc_send_json_error( $status );
	} elseif ( is_null( $result ) ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	$status['themeName'] = gc_get_theme( $slug )->get( 'Name' );

	if ( current_user_can( 'switch_themes' ) ) {
		if ( is_multisite() ) {
			$status['activateUrl'] = add_query_arg(
				array(
					'action'   => 'enable',
					'_gcnonce' => gc_create_nonce( 'enable-theme_' . $slug ),
					'theme'    => $slug,
				),
				network_admin_url( 'themes.php' )
			);
		} else {
			$status['activateUrl'] = add_query_arg(
				array(
					'action'     => 'activate',
					'_gcnonce'   => gc_create_nonce( 'switch-theme_' . $slug ),
					'stylesheet' => $slug,
				),
				admin_url( 'themes.php' )
			);
		}
	}

	$theme                = gc_get_theme( $slug );
	$status['blockTheme'] = $theme->is_block_theme();

	if ( ! is_multisite() && current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
		$status['customizeUrl'] = add_query_arg(
			array(
				'return' => urlencode( network_admin_url( 'theme-install.php', 'relative' ) ),
			),
			gc_customize_url( $slug )
		);
	}

	/*
	 * See GC_Theme_Install_List_Table::_get_theme_status() if we wanted to check
	 * on post-installation status.
	 */
	gc_send_json_success( $status );
}

/**
 * Handles updating a theme via AJAX.
 *
 * @see Theme_Upgrader
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_update_theme() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['slug'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_theme_specified',
				'errorMessage' => __( '未指定主题。' ),
			)
		);
	}

	$stylesheet = preg_replace( '/[^A-z0-9_\-]/', '', gc_unslash( $_POST['slug'] ) );
	$status     = array(
		'update'     => 'theme',
		'slug'       => $stylesheet,
		'oldVersion' => '',
		'newVersion' => '',
	);

	if ( ! current_user_can( 'update_themes' ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上升级主题。' );
		gc_send_json_error( $status );
	}

	$theme = gc_get_theme( $stylesheet );
	if ( $theme->exists() ) {
		$status['oldVersion'] = $theme->get( 'Version' );
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	$current = get_site_transient( 'update_themes' );
	if ( empty( $current ) ) {
		gc_update_themes();
	}

	$skin     = new GC_Ajax_Upgrader_Skin();
	$upgrader = new Theme_Upgrader( $skin );
	$result   = $upgrader->bulk_upgrade( array( $stylesheet ) );

	if ( defined( 'GC_DEBUG' ) && GC_DEBUG ) {
		$status['debug'] = $skin->get_upgrade_messages();
	}

	if ( is_gc_error( $skin->result ) ) {
		$status['errorCode']    = $skin->result->get_error_code();
		$status['errorMessage'] = $skin->result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( $skin->get_errors()->has_errors() ) {
		$status['errorMessage'] = $skin->get_error_messages();
		gc_send_json_error( $status );
	} elseif ( is_array( $result ) && ! empty( $result[ $stylesheet ] ) ) {

		// Theme is already at the latest version.
		if ( true === $result[ $stylesheet ] ) {
			$status['errorMessage'] = $upgrader->strings['up_to_date'];
			gc_send_json_error( $status );
		}

		$theme = gc_get_theme( $stylesheet );
		if ( $theme->exists() ) {
			$status['newVersion'] = $theme->get( 'Version' );
		}

		gc_send_json_success( $status );
	} elseif ( false === $result ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	// An unhandled error occurred.
	$status['errorMessage'] = __( '主题升级失败。' );
	gc_send_json_error( $status );
}

/**
 * Handles deleting a theme via AJAX.
 *
 * @see delete_theme()
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_delete_theme() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['slug'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_theme_specified',
				'errorMessage' => __( '未指定主题。' ),
			)
		);
	}

	$stylesheet = preg_replace( '/[^A-z0-9_\-]/', '', gc_unslash( $_POST['slug'] ) );
	$status     = array(
		'delete' => 'theme',
		'slug'   => $stylesheet,
	);

	if ( ! current_user_can( 'delete_themes' ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上删除主题。' );
		gc_send_json_error( $status );
	}

	if ( ! gc_get_theme( $stylesheet )->exists() ) {
		$status['errorMessage'] = __( '请求的主题不存在。' );
		gc_send_json_error( $status );
	}

	// Check filesystem credentials. `delete_theme()` will bail otherwise.
	$url = gc_nonce_url( 'themes.php?action=delete&stylesheet=' . urlencode( $stylesheet ), 'delete-theme_' . $stylesheet );

	ob_start();
	$credentials = request_filesystem_credentials( $url );
	ob_end_clean();

	if ( false === $credentials || ! GC_Filesystem( $credentials ) ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	require_once ABSPATH . 'gc-admin/includes/theme.php';

	$result = delete_theme( $stylesheet );

	if ( is_gc_error( $result ) ) {
		$status['errorMessage'] = $result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( false === $result ) {
		$status['errorMessage'] = __( '主题未能被删除。' );
		gc_send_json_error( $status );
	}

	gc_send_json_success( $status );
}

/**
 * Handles installing a plugin via AJAX.
 *
 * @see Plugin_Upgrader
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_install_plugin() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['slug'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_plugin_specified',
				'errorMessage' => __( '未指定插件。' ),
			)
		);
	}

	$status = array(
		'install' => 'plugin',
		'slug'    => sanitize_key( gc_unslash( $_POST['slug'] ) ),
	);

	if ( ! current_user_can( 'install_plugins' ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上安装插件。' );
		gc_send_json_error( $status );
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	require_once ABSPATH . 'gc-admin/includes/plugin-install.php';

	$api = plugins_api(
		'plugin_information',
		array(
			'slug'   => sanitize_key( gc_unslash( $_POST['slug'] ) ),
			'fields' => array(
				'sections' => false,
			),
		)
	);

	if ( is_gc_error( $api ) ) {
		$status['errorMessage'] = $api->get_error_message();
		gc_send_json_error( $status );
	}

	$status['pluginName'] = $api->name;

	$skin     = new GC_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );

	if ( defined( 'GC_DEBUG' ) && GC_DEBUG ) {
		$status['debug'] = $skin->get_upgrade_messages();
	}

	if ( is_gc_error( $result ) ) {
		$status['errorCode']    = $result->get_error_code();
		$status['errorMessage'] = $result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( is_gc_error( $skin->result ) ) {
		$status['errorCode']    = $skin->result->get_error_code();
		$status['errorMessage'] = $skin->result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( $skin->get_errors()->has_errors() ) {
		$status['errorMessage'] = $skin->get_error_messages();
		gc_send_json_error( $status );
	} elseif ( is_null( $result ) ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	$install_status = install_plugin_install_status( $api );
	$pagenow        = isset( $_POST['pagenow'] ) ? sanitize_key( $_POST['pagenow'] ) : '';

	// If installation request is coming from import page, do not return network activation link.
	$plugins_url = ( 'import' === $pagenow ) ? admin_url( 'plugins.php' ) : network_admin_url( 'plugins.php' );

	if ( current_user_can( 'activate_plugin', $install_status['file'] ) && is_plugin_inactive( $install_status['file'] ) ) {
		$status['activateUrl'] = add_query_arg(
			array(
				'_gcnonce' => gc_create_nonce( 'activate-plugin_' . $install_status['file'] ),
				'action'   => 'activate',
				'plugin'   => $install_status['file'],
			),
			$plugins_url
		);
	}

	if ( is_multisite() && current_user_can( 'manage_network_plugins' ) && 'import' !== $pagenow ) {
		$status['activateUrl'] = add_query_arg( array( 'networkwide' => 1 ), $status['activateUrl'] );
	}

	gc_send_json_success( $status );
}

/**
 * Handles updating a plugin via AJAX.
 *
 * @see Plugin_Upgrader
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_update_plugin() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['plugin'] ) || empty( $_POST['slug'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_plugin_specified',
				'errorMessage' => __( '未指定插件。' ),
			)
		);
	}

	$plugin = plugin_basename( sanitize_text_field( gc_unslash( $_POST['plugin'] ) ) );

	$status = array(
		'update'     => 'plugin',
		'slug'       => sanitize_key( gc_unslash( $_POST['slug'] ) ),
		'oldVersion' => '',
		'newVersion' => '',
	);

	if ( ! current_user_can( 'update_plugins' ) || 0 !== validate_file( $plugin ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上升级插件。' );
		gc_send_json_error( $status );
	}

	$plugin_data          = get_plugin_data( GC_PLUGIN_DIR . '/' . $plugin );
	$status['plugin']     = $plugin;
	$status['pluginName'] = $plugin_data['Name'];

	if ( $plugin_data['Version'] ) {
		/* translators: %s: Plugin version. */
		$status['oldVersion'] = sprintf( __( '%s版本' ), $plugin_data['Version'] );
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	gc_update_plugins();

	$skin     = new GC_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->bulk_upgrade( array( $plugin ) );

	if ( defined( 'GC_DEBUG' ) && GC_DEBUG ) {
		$status['debug'] = $skin->get_upgrade_messages();
	}

	if ( is_gc_error( $skin->result ) ) {
		$status['errorCode']    = $skin->result->get_error_code();
		$status['errorMessage'] = $skin->result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( $skin->get_errors()->has_errors() ) {
		$status['errorMessage'] = $skin->get_error_messages();
		gc_send_json_error( $status );
	} elseif ( is_array( $result ) && ! empty( $result[ $plugin ] ) ) {

		/*
		 * Plugin is already at the latest version.
		 *
		 * This may also be the return value if the `update_plugins` site transient is empty,
		 * e.g. when you update two plugins in quick succession before the transient repopulates.
		 *
		 * Preferably something can be done to ensure `update_plugins` isn't empty.
		 * For now, surface some sort of error here.
		 */
		if ( true === $result[ $plugin ] ) {
			$status['errorMessage'] = $upgrader->strings['up_to_date'];
			gc_send_json_error( $status );
		}

		$plugin_data = get_plugins( '/' . $result[ $plugin ]['destination_name'] );
		$plugin_data = reset( $plugin_data );

		if ( $plugin_data['Version'] ) {
			/* translators: %s: Plugin version. */
			$status['newVersion'] = sprintf( __( '%s版本' ), $plugin_data['Version'] );
		}

		gc_send_json_success( $status );
	} elseif ( false === $result ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	// An unhandled error occurred.
	$status['errorMessage'] = __( '插件升级失败。' );
	gc_send_json_error( $status );
}

/**
 * Handles deleting a plugin via AJAX.
 *
 * @see delete_plugins()
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 */
function gc_ajax_delete_plugin() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['slug'] ) || empty( $_POST['plugin'] ) ) {
		gc_send_json_error(
			array(
				'slug'         => '',
				'errorCode'    => 'no_plugin_specified',
				'errorMessage' => __( '未指定插件。' ),
			)
		);
	}

	$plugin = plugin_basename( sanitize_text_field( gc_unslash( $_POST['plugin'] ) ) );

	$status = array(
		'delete' => 'plugin',
		'slug'   => sanitize_key( gc_unslash( $_POST['slug'] ) ),
	);

	if ( ! current_user_can( 'delete_plugins' ) || 0 !== validate_file( $plugin ) ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上删除插件。' );
		gc_send_json_error( $status );
	}

	$plugin_data          = get_plugin_data( GC_PLUGIN_DIR . '/' . $plugin );
	$status['plugin']     = $plugin;
	$status['pluginName'] = $plugin_data['Name'];

	if ( is_plugin_active( $plugin ) ) {
		$status['errorMessage'] = __( '您不能删除主系统正在使用的插件。' );
		gc_send_json_error( $status );
	}

	// Check filesystem credentials. `delete_plugins()` will bail otherwise.
	$url = gc_nonce_url( 'plugins.php?action=delete-selected&verify-delete=1&checked[]=' . $plugin, 'bulk-plugins' );

	ob_start();
	$credentials = request_filesystem_credentials( $url );
	ob_end_clean();

	if ( false === $credentials || ! GC_Filesystem( $credentials ) ) {
		global $gc_filesystem;

		$status['errorCode']    = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __( '无法连接至文件系统。 请确认您的凭据。' );

		// Pass through the error from GC_Filesystem if one was raised.
		if ( $gc_filesystem instanceof GC_Filesystem_Base && is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html( $gc_filesystem->errors->get_error_message() );
		}

		gc_send_json_error( $status );
	}

	$result = delete_plugins( array( $plugin ) );

	if ( is_gc_error( $result ) ) {
		$status['errorMessage'] = $result->get_error_message();
		gc_send_json_error( $status );
	} elseif ( false === $result ) {
		$status['errorMessage'] = __( '插件未能被删除。' );
		gc_send_json_error( $status );
	}

	gc_send_json_success( $status );
}

/**
 * Handles searching plugins via AJAX.
 *
 * @global string $s Search term.
 */
function gc_ajax_search_plugins() {
	check_ajax_referer( 'updates' );

	// Ensure after_plugin_row_{$plugin_file} gets hooked.
	gc_plugin_update_rows();

	$pagenow = isset( $_POST['pagenow'] ) ? sanitize_key( $_POST['pagenow'] ) : '';
	if ( 'plugins-network' === $pagenow || 'plugins' === $pagenow ) {
		set_current_screen( $pagenow );
	}

	/** @var GC_Plugins_List_Table $gc_list_table */
	$gc_list_table = _get_list_table(
		'GC_Plugins_List_Table',
		array(
			'screen' => get_current_screen(),
		)
	);

	$status = array();

	if ( ! $gc_list_table->ajax_user_can() ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上管理插件。' );
		gc_send_json_error( $status );
	}

	// Set the correct requester, so pagination works.
	$_SERVER['REQUEST_URI'] = add_query_arg(
		array_diff_key(
			$_POST,
			array(
				'_ajax_nonce' => null,
				'action'      => null,
			)
		),
		network_admin_url( 'plugins.php', 'relative' )
	);

	$GLOBALS['s'] = gc_unslash( $_POST['s'] );

	$gc_list_table->prepare_items();

	ob_start();
	$gc_list_table->display();
	$status['count'] = count( $gc_list_table->items );
	$status['items'] = ob_get_clean();

	gc_send_json_success( $status );
}

/**
 * Handles searching plugins to install via AJAX.
 *
 */
function gc_ajax_search_install_plugins() {
	check_ajax_referer( 'updates' );

	$pagenow = isset( $_POST['pagenow'] ) ? sanitize_key( $_POST['pagenow'] ) : '';
	if ( 'plugin-install-network' === $pagenow || 'plugin-install' === $pagenow ) {
		set_current_screen( $pagenow );
	}

	/** @var GC_Plugin_Install_List_Table $gc_list_table */
	$gc_list_table = _get_list_table(
		'GC_Plugin_Install_List_Table',
		array(
			'screen' => get_current_screen(),
		)
	);

	$status = array();

	if ( ! $gc_list_table->ajax_user_can() ) {
		$status['errorMessage'] = __( '抱歉，您不能在此系统上管理插件。' );
		gc_send_json_error( $status );
	}

	// Set the correct requester, so pagination works.
	$_SERVER['REQUEST_URI'] = add_query_arg(
		array_diff_key(
			$_POST,
			array(
				'_ajax_nonce' => null,
				'action'      => null,
			)
		),
		network_admin_url( 'plugin-install.php', 'relative' )
	);

	$gc_list_table->prepare_items();

	ob_start();
	$gc_list_table->display();
	$status['count'] = (int) $gc_list_table->get_pagination_arg( 'total_items' );
	$status['items'] = ob_get_clean();

	gc_send_json_success( $status );
}

/**
 * Handles editing a theme or plugin file via AJAX.
 *
 * @see gc_edit_theme_plugin_file()
 */
function gc_ajax_edit_theme_plugin_file() {
	$r = gc_edit_theme_plugin_file( gc_unslash( $_POST ) ); // Validation of args is done in gc_edit_theme_plugin_file().

	if ( is_gc_error( $r ) ) {
		gc_send_json_error(
			array_merge(
				array(
					'code'    => $r->get_error_code(),
					'message' => $r->get_error_message(),
				),
				(array) $r->get_error_data()
			)
		);
	} else {
		gc_send_json_success(
			array(
				'message' => __( '文件修改成功。' ),
			)
		);
	}
}

/**
 * Handles exporting a user's personal data via AJAX.
 *
 */
function gc_ajax_gc_privacy_export_personal_data() {

	if ( empty( $_POST['id'] ) ) {
		gc_send_json_error( __( '缺少请求ID。' ) );
	}

	$request_id = (int) $_POST['id'];

	if ( $request_id < 1 ) {
		gc_send_json_error( __( '无效的请求ID。' ) );
	}

	if ( ! current_user_can( 'export_others_personal_data' ) ) {
		gc_send_json_error( __( '抱歉，您不能进行此操作。' ) );
	}

	check_ajax_referer( 'gc-privacy-export-personal-data-' . $request_id, 'security' );

	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		gc_send_json_error( __( '无效的请求类型。' ) );
	}

	$email_address = $request->email;
	if ( ! is_email( $email_address ) ) {
		gc_send_json_error( __( '必须提供有效的电子邮箱。' ) );
	}

	if ( ! isset( $_POST['exporter'] ) ) {
		gc_send_json_error( __( '缺少导出器索引。' ) );
	}

	$exporter_index = (int) $_POST['exporter'];

	if ( ! isset( $_POST['page'] ) ) {
		gc_send_json_error( __( '缺少页面索引。' ) );
	}

	$page = (int) $_POST['page'];

	$send_as_email = isset( $_POST['sendAsEmail'] ) ? ( 'true' === $_POST['sendAsEmail'] ) : false;

	/**
	 * Filters the array of exporter callbacks.
	 *
	 * @since 4.9.6
	 *
	 * @param array $args {
	 *     An array of callable exporters of personal data. Default empty array.
	 *
	 *     @type array ...$0 {
	 *         Array of personal data exporters.
	 *
	 *         @type callable $callback               Callable exporter function that accepts an
	 *                                                email address and a page and returns an array
	 *                                                of name => value pairs of personal data.
	 *         @type string   $exporter_friendly_name Translated user facing friendly name for the
	 *                                                exporter.
	 *     }
	 * }
	 */
	$exporters = apply_filters( 'gc_privacy_personal_data_exporters', array() );

	if ( ! is_array( $exporters ) ) {
		gc_send_json_error( __( '有导出器不当使用了注册过滤器。' ) );
	}

	// Do we have any registered exporters?
	if ( 0 < count( $exporters ) ) {
		if ( $exporter_index < 1 ) {
			gc_send_json_error( __( '导出器索引不能为负。' ) );
		}

		if ( $exporter_index > count( $exporters ) ) {
			gc_send_json_error( __( '导出器索引越界。' ) );
		}

		if ( $page < 1 ) {
			gc_send_json_error( __( '页面索引不能小于1。' ) );
		}

		$exporter_keys = array_keys( $exporters );
		$exporter_key  = $exporter_keys[ $exporter_index - 1 ];
		$exporter      = $exporters[ $exporter_key ];

		if ( ! is_array( $exporter ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter array index. */
				sprintf( __( '未找到描述位于索引%s的导出器的数组。' ), $exporter_key )
			);
		}

		if ( ! array_key_exists( 'exporter_friendly_name', $exporter ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter array index. */
				sprintf( __( '位于索引%s的导出器数组未包含友好名称。' ), $exporter_key )
			);
		}

		$exporter_friendly_name = $exporter['exporter_friendly_name'];

		if ( ! array_key_exists( 'callback', $exporter ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器未包含回调函数：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}

		if ( ! is_callable( $exporter['callback'] ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器回调函数不是合法的回调函数：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}

		$callback = $exporter['callback'];
		$response = call_user_func( $callback, $email_address, $page );

		if ( is_gc_error( $response ) ) {
			gc_send_json_error( $response );
		}

		if ( ! is_array( $response ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器返回的不是数组：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}

		if ( ! array_key_exists( 'data', $response ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器返回的数组中未包含data：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}

		if ( ! is_array( $response['data'] ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器返回的数组中未包含data数组：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}

		if ( ! array_key_exists( 'done', $response ) ) {
			gc_send_json_error(
				/* translators: %s: Exporter friendly name. */
				sprintf( __( '导出器返回的数组中未包含done（布尔值）：%s。' ), esc_html( $exporter_friendly_name ) )
			);
		}
	} else {
		// No exporters, so we're done.
		$exporter_key = '';

		$response = array(
			'data' => array(),
			'done' => true,
		);
	}

	/**
	 * Filters a page of personal data exporter data. Used to build the export report.
	 *
	 * Allows the export response to be consumed by destinations in addition to Ajax.
	 *
	 * @since 4.9.6
	 *
	 * @param array  $response        The personal data for the given exporter and page.
	 * @param int    $exporter_index  The index of the exporter that provided this data.
	 * @param string $email_address   The email address associated with this personal data.
	 * @param int    $page            The page for this response.
	 * @param int    $request_id      The privacy request post ID associated with this request.
	 * @param bool   $send_as_email   Whether the final results of the export should be emailed to the user.
	 * @param string $exporter_key    The key (slug) of the exporter that provided this data.
	 */
	$response = apply_filters( 'gc_privacy_personal_data_export_page', $response, $exporter_index, $email_address, $page, $request_id, $send_as_email, $exporter_key );

	if ( is_gc_error( $response ) ) {
		gc_send_json_error( $response );
	}

	gc_send_json_success( $response );
}

/**
 * Handles erasing personal data via AJAX.
 *
 */
function gc_ajax_gc_privacy_erase_personal_data() {

	if ( empty( $_POST['id'] ) ) {
		gc_send_json_error( __( '缺少请求ID。' ) );
	}

	$request_id = (int) $_POST['id'];

	if ( $request_id < 1 ) {
		gc_send_json_error( __( '无效的请求ID。' ) );
	}

	// Both capabilities are required to avoid confusion, see `_gc_personal_data_removal_page()`.
	if ( ! current_user_can( 'erase_others_personal_data' ) || ! current_user_can( 'delete_users' ) ) {
		gc_send_json_error( __( '抱歉，您不能进行此操作。' ) );
	}

	check_ajax_referer( 'gc-privacy-erase-personal-data-' . $request_id, 'security' );

	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'remove_personal_data' !== $request->action_name ) {
		gc_send_json_error( __( '无效的请求类型。' ) );
	}

	$email_address = $request->email;

	if ( ! is_email( $email_address ) ) {
		gc_send_json_error( __( '请求中包含无效的电子邮箱。' ) );
	}

	if ( ! isset( $_POST['eraser'] ) ) {
		gc_send_json_error( __( '缺少抹除器索引。' ) );
	}

	$eraser_index = (int) $_POST['eraser'];

	if ( ! isset( $_POST['page'] ) ) {
		gc_send_json_error( __( '缺少页面索引。' ) );
	}

	$page = (int) $_POST['page'];

	/**
	 * Filters the array of personal data eraser callbacks.
	 *
	 * @since 4.9.6
	 *
	 * @param array $args {
	 *     An array of callable erasers of personal data. Default empty array.
	 *
	 *     @type array ...$0 {
	 *         Array of personal data exporters.
	 *
	 *         @type callable $callback               Callable eraser that accepts an email address and
	 *                                                a page and returns an array with boolean values for
	 *                                                whether items were removed or retained and any messages
	 *                                                from the eraser, as well as if additional pages are
	 *                                                available.
	 *         @type string   $exporter_friendly_name Translated user facing friendly name for the eraser.
	 *     }
	 * }
	 */
	$erasers = apply_filters( 'gc_privacy_personal_data_erasers', array() );

	// Do we have any registered erasers?
	if ( 0 < count( $erasers ) ) {

		if ( $eraser_index < 1 ) {
			gc_send_json_error( __( '抹除器索引不能小于1。' ) );
		}

		if ( $eraser_index > count( $erasers ) ) {
			gc_send_json_error( __( '抹除器索引超出范围。' ) );
		}

		if ( $page < 1 ) {
			gc_send_json_error( __( '页面索引不能小于1。' ) );
		}

		$eraser_keys = array_keys( $erasers );
		$eraser_key  = $eraser_keys[ $eraser_index - 1 ];
		$eraser      = $erasers[ $eraser_key ];

		if ( ! is_array( $eraser ) ) {
			/* translators: %d: Eraser array index. */
			gc_send_json_error( sprintf( __( '未找到描述位于索引%d的抹除器的数组。' ), $eraser_index ) );
		}

		if ( ! array_key_exists( 'eraser_friendly_name', $eraser ) ) {
			/* translators: %d: Eraser array index. */
			gc_send_json_error( sprintf( __( '位于索引%d的抹除器数组未包含友好名称。' ), $eraser_index ) );
		}

		$eraser_friendly_name = $eraser['eraser_friendly_name'];

		if ( ! array_key_exists( 'callback', $eraser ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: %s: Eraser friendly name. */
					__( '抹除器未包含回调：%s。' ),
					esc_html( $eraser_friendly_name )
				)
			);
		}

		if ( ! is_callable( $eraser['callback'] ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: %s: Eraser friendly name. */
					__( '抹除器回调无效：%s。' ),
					esc_html( $eraser_friendly_name )
				)
			);
		}

		$callback = $eraser['callback'];
		$response = call_user_func( $callback, $email_address, $page );

		if ( is_gc_error( $response ) ) {
			gc_send_json_error( $response );
		}

		if ( ! is_array( $response ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '未从抹除器%1$s（索引%2$d）处收到数组。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}

		if ( ! array_key_exists( 'items_removed', $response ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '从抹除器%1$s（索引%2$d）处收到的数组未包含items_removed键。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}

		if ( ! array_key_exists( 'items_retained', $response ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '从抹除器%1$s（索引%2$d）处收到的数组未包含items_retained键。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}

		if ( ! array_key_exists( 'messages', $response ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '从抹除器%1$s（索引%2$d）处收到的数组未包含messages键。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}

		if ( ! is_array( $response['messages'] ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '从抹除器%1$s（索引%2$d）处收到的数组包含的messages键未指代数组。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}

		if ( ! array_key_exists( 'done', $response ) ) {
			gc_send_json_error(
				sprintf(
					/* translators: 1: Eraser friendly name, 2: Eraser array index. */
					__( '从抹除器%1$s（索引%2$d）处收到的数组未包含done旗标。' ),
					esc_html( $eraser_friendly_name ),
					$eraser_index
				)
			);
		}
	} else {
		// No erasers, so we're done.
		$eraser_key = '';

		$response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Filters a page of personal data eraser data.
	 *
	 * Allows the erasure response to be consumed by destinations in addition to Ajax.
	 *
	 * @since 4.9.6
	 *
	 * @param array  $response        The personal data for the given exporter and page.
	 * @param int    $eraser_index    The index of the eraser that provided this data.
	 * @param string $email_address   The email address associated with this personal data.
	 * @param int    $page            The page for this response.
	 * @param int    $request_id      The privacy request post ID associated with this request.
	 * @param string $eraser_key      The key (slug) of the eraser that provided this data.
	 */
	$response = apply_filters( 'gc_privacy_personal_data_erasure_page', $response, $eraser_index, $email_address, $page, $request_id, $eraser_key );

	if ( is_gc_error( $response ) ) {
		gc_send_json_error( $response );
	}

	gc_send_json_success( $response );
}

/**
 * Handles site health checks on server communication via AJAX.
 *
 * @since 5.2.0
 * @deprecated 5.6.0 Use GC_REST_Site_Health_Controller::test_dotorg_communication()
 * @see GC_REST_Site_Health_Controller::test_dotorg_communication()
 */
function gc_ajax_health_check_dotorg_communication() {
	_doing_it_wrong(
		'gc_ajax_health_check_dotorg_communication',
		sprintf(
		// translators: 1: The Site Health action that is no longer used by core. 2: The new function that replaces it.
			__( '系统健康检查的%1$s已被%2$s代替。' ),
			'gc_ajax_health_check_dotorg_communication',
			'GC_REST_Site_Health_Controller::test_dotorg_communication'
		),
		'5.6.0'
	);

	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		gc_send_json_error();
	}

	if ( ! class_exists( 'GC_Site_Health' ) ) {
		require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
	}

	$site_health = GC_Site_Health::get_instance();
	gc_send_json_success( $site_health->get_test_dotorg_communication() );
}

/**
 * Handles site health checks on background updates via AJAX.
 *
 * @since 5.2.0
 * @deprecated 5.6.0 Use GC_REST_Site_Health_Controller::test_background_updates()
 * @see GC_REST_Site_Health_Controller::test_background_updates()
 */
function gc_ajax_health_check_background_updates() {
	_doing_it_wrong(
		'gc_ajax_health_check_background_updates',
		sprintf(
		// translators: 1: The Site Health action that is no longer used by core. 2: The new function that replaces it.
			__( '系统健康检查的%1$s已被%2$s代替。' ),
			'gc_ajax_health_check_background_updates',
			'GC_REST_Site_Health_Controller::test_background_updates'
		),
		'5.6.0'
	);

	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		gc_send_json_error();
	}

	if ( ! class_exists( 'GC_Site_Health' ) ) {
		require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
	}

	$site_health = GC_Site_Health::get_instance();
	gc_send_json_success( $site_health->get_test_background_updates() );
}

/**
 * Handles site health checks on loopback requests via AJAX.
 *
 * @since 5.2.0
 * @deprecated 5.6.0 Use GC_REST_Site_Health_Controller::test_loopback_requests()
 * @see GC_REST_Site_Health_Controller::test_loopback_requests()
 */
function gc_ajax_health_check_loopback_requests() {
	_doing_it_wrong(
		'gc_ajax_health_check_loopback_requests',
		sprintf(
		// translators: 1: The Site Health action that is no longer used by core. 2: The new function that replaces it.
			__( '系统健康检查的%1$s已被%2$s代替。' ),
			'gc_ajax_health_check_loopback_requests',
			'GC_REST_Site_Health_Controller::test_loopback_requests'
		),
		'5.6.0'
	);

	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		gc_send_json_error();
	}

	if ( ! class_exists( 'GC_Site_Health' ) ) {
		require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
	}

	$site_health = GC_Site_Health::get_instance();
	gc_send_json_success( $site_health->get_test_loopback_requests() );
}

/**
 * Handles site health check to update the result status via AJAX.
 *
 * @since 5.2.0
 */
function gc_ajax_health_check_site_status_result() {
	check_ajax_referer( 'health-check-site-status-result' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		gc_send_json_error();
	}

	set_transient( 'health-check-site-status-result', gc_json_encode( $_POST['counts'] ) );

	gc_send_json_success();
}

/**
 * Handles site health check to get directories and database sizes via AJAX.
 *
 * @since 5.2.0
 * @deprecated 5.6.0 Use GC_REST_Site_Health_Controller::get_directory_sizes()
 * @see GC_REST_Site_Health_Controller::get_directory_sizes()
 */
function gc_ajax_health_check_get_sizes() {
	_doing_it_wrong(
		'gc_ajax_health_check_get_sizes',
		sprintf(
		// translators: 1: The Site Health action that is no longer used by core. 2: The new function that replaces it.
			__( '系统健康检查的%1$s已被%2$s代替。' ),
			'gc_ajax_health_check_get_sizes',
			'GC_REST_Site_Health_Controller::get_directory_sizes'
		),
		'5.6.0'
	);

	check_ajax_referer( 'health-check-site-status-result' );

	if ( ! current_user_can( 'view_site_health_checks' ) || is_multisite() ) {
		gc_send_json_error();
	}

	if ( ! class_exists( 'GC_Debug_Data' ) ) {
		require_once ABSPATH . 'gc-admin/includes/class-gc-debug-data.php';
	}

	$sizes_data = GC_Debug_Data::get_sizes();
	$all_sizes  = array( 'raw' => 0 );

	foreach ( $sizes_data as $name => $value ) {
		$name = sanitize_text_field( $name );
		$data = array();

		if ( isset( $value['size'] ) ) {
			if ( is_string( $value['size'] ) ) {
				$data['size'] = sanitize_text_field( $value['size'] );
			} else {
				$data['size'] = (int) $value['size'];
			}
		}

		if ( isset( $value['debug'] ) ) {
			if ( is_string( $value['debug'] ) ) {
				$data['debug'] = sanitize_text_field( $value['debug'] );
			} else {
				$data['debug'] = (int) $value['debug'];
			}
		}

		if ( ! empty( $value['raw'] ) ) {
			$data['raw'] = (int) $value['raw'];
		}

		$all_sizes[ $name ] = $data;
	}

	if ( isset( $all_sizes['total_size']['debug'] ) && 'not available' === $all_sizes['total_size']['debug'] ) {
		gc_send_json_error( $all_sizes );
	}

	gc_send_json_success( $all_sizes );
}

/**
 * Handles renewing the REST API nonce via AJAX.
 *
 * @since 5.3.0
 */
function gc_ajax_rest_nonce() {
	exit( gc_create_nonce( 'gc_rest' ) );
}

/**
 * Handles enabling or disable plugin and theme auto-updates via AJAX.
 *
 * @since 5.5.0
 */
function gc_ajax_toggle_auto_updates() {
	check_ajax_referer( 'updates' );

	if ( empty( $_POST['type'] ) || empty( $_POST['asset'] ) || empty( $_POST['state'] ) ) {
		gc_send_json_error( array( 'error' => __( '无效数据。未选择项目。' ) ) );
	}

	$asset = sanitize_text_field( urldecode( $_POST['asset'] ) );

	if ( 'enable' !== $_POST['state'] && 'disable' !== $_POST['state'] ) {
		gc_send_json_error( array( 'error' => __( '无效数据。 未知状态。' ) ) );
	}
	$state = $_POST['state'];

	if ( 'plugin' !== $_POST['type'] && 'theme' !== $_POST['type'] ) {
		gc_send_json_error( array( 'error' => __( '无效数据。 未知类型。' ) ) );
	}
	$type = $_POST['type'];

	switch ( $type ) {
		case 'plugin':
			if ( ! current_user_can( 'update_plugins' ) ) {
				$error_message = __( '抱歉，您不能修改插件。' );
				gc_send_json_error( array( 'error' => $error_message ) );
			}

			$option = 'auto_update_plugins';
			/** This filter is documented in gc-admin/includes/class-gc-plugins-list-table.php */
			$all_items = apply_filters( 'all_plugins', get_plugins() );
			break;
		case 'theme':
			if ( ! current_user_can( 'update_themes' ) ) {
				$error_message = __( '抱歉，您不能编辑评论。' );
				gc_send_json_error( array( 'error' => $error_message ) );
			}

			$option    = 'auto_update_themes';
			$all_items = gc_get_themes();
			break;
		default:
			gc_send_json_error( array( 'error' => __( '无效数据。 未知类型。' ) ) );
	}

	if ( ! array_key_exists( $asset, $all_items ) ) {
		$error_message = __( '无效数据。该项目不存在。' );
		gc_send_json_error( array( 'error' => $error_message ) );
	}

	$auto_updates = (array) get_site_option( $option, array() );

	if ( 'disable' === $state ) {
		$auto_updates = array_diff( $auto_updates, array( $asset ) );
	} else {
		$auto_updates[] = $asset;
		$auto_updates   = array_unique( $auto_updates );
	}

	// Remove items that have been deleted since the site option was last updated.
	$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

	update_site_option( $option, $auto_updates );

	gc_send_json_success();
}

/**
 * Handles sending a password reset link via AJAX.
 *
 * @since 5.7.0
 */
function gc_ajax_send_password_reset() {

	// Validate the nonce for this action.
	$user_id = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
	check_ajax_referer( 'reset-password-for-' . $user_id, 'nonce' );

	// Verify user capabilities.
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		gc_send_json_error( __( '无法发送密码重置邮件，权限不足。' ) );
	}

	// Send the password reset link.
	$user    = get_userdata( $user_id );
	$results = retrieve_password( $user->user_login );

	if ( true === $results ) {
		gc_send_json_success(
			/* translators: %s: User's display name. */
			sprintf( __( '密码重置链接已通过邮件发送至%s。' ), $user->display_name )
		);
	} else {
		gc_send_json_error( $results->get_error_message() );
	}
}
