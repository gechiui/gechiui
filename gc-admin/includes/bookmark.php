<?php
/**
 * GeChiUI Bookmark Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Add a link to using values provided in $_POST.
 *
 *
 *
 * @return int|GC_Error Value 0 or GC_Error on failure. The link ID on success.
 */
function add_link() {
	return edit_link();
}

/**
 * Updates or inserts a link using values provided in $_POST.
 *
 *
 *
 * @param int $link_id Optional. ID of the link to edit. Default 0.
 * @return int|GC_Error Value 0 or GC_Error on failure. The link ID on success.
 */
function edit_link( $link_id = 0 ) {
	if ( ! current_user_can( 'manage_links' ) ) {
		gc_die(
			'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
			'<p>' . __( '抱歉，您不能在此站点上编辑链接。' ) . '</p>',
			403
		);
	}

	$_POST['link_url']   = esc_html( $_POST['link_url'] );
	$_POST['link_url']   = esc_url( $_POST['link_url'] );
	$_POST['link_name']  = esc_html( $_POST['link_name'] );
	$_POST['link_image'] = esc_html( $_POST['link_image'] );
	$_POST['link_rss']   = esc_url( $_POST['link_rss'] );
	if ( ! isset( $_POST['link_visible'] ) || 'N' !== $_POST['link_visible'] ) {
		$_POST['link_visible'] = 'Y';
	}

	if ( ! empty( $link_id ) ) {
		$_POST['link_id'] = $link_id;
		return gc_update_link( $_POST );
	} else {
		return gc_insert_link( $_POST );
	}
}

/**
 * Retrieves the default link for editing.
 *
 *
 *
 * @return stdClass Default link object.
 */
function get_default_link_to_edit() {
	$link = new stdClass;
	if ( isset( $_GET['linkurl'] ) ) {
		$link->link_url = esc_url( gc_unslash( $_GET['linkurl'] ) );
	} else {
		$link->link_url = '';
	}

	if ( isset( $_GET['name'] ) ) {
		$link->link_name = esc_attr( gc_unslash( $_GET['name'] ) );
	} else {
		$link->link_name = '';
	}

	$link->link_visible = 'Y';

	return $link;
}

/**
 * Deletes a specified link from the database.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $link_id ID of the link to delete
 * @return true Always true.
 */
function gc_delete_link( $link_id ) {
	global $gcdb;
	/**
	 * Fires before a link is deleted.
	 *
	 *
	 * @param int $link_id ID of the link to delete.
	 */
	do_action( 'delete_link', $link_id );

	gc_delete_object_term_relationships( $link_id, 'link_category' );

	$gcdb->delete( $gcdb->links, array( 'link_id' => $link_id ) );

	/**
	 * Fires after a link has been deleted.
	 *
	 *
	 * @param int $link_id ID of the deleted link.
	 */
	do_action( 'deleted_link', $link_id );

	clean_bookmark_cache( $link_id );

	return true;
}

/**
 * Retrieves the link category IDs associated with the link specified.
 *
 *
 *
 * @param int $link_id Link ID to look up.
 * @return int[] The IDs of the requested link's categories.
 */
function gc_get_link_cats( $link_id = 0 ) {
	$cats = gc_get_object_terms( $link_id, 'link_category', array( 'fields' => 'ids' ) );
	return array_unique( $cats );
}

/**
 * Retrieves link data based on its ID.
 *
 *
 *
 * @param int|stdClass $link Link ID or object to retrieve.
 * @return object Link object for editing.
 */
function get_link_to_edit( $link ) {
	return get_bookmark( $link, OBJECT, 'edit' );
}

/**
 * Inserts a link into the database, or updates an existing link.
 *
 * Runs all the necessary sanitizing, provides default values if arguments are missing,
 * and finally saves the link.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param array $linkdata {
 *     Elements that make up the link to insert.
 *
 *     @type int    $link_id          Optional. The ID of the existing link if updating.
 *     @type string $link_url         The URL the link points to.
 *     @type string $link_name        The title of the link.
 *     @type string $link_image       Optional. A URL of an image.
 *     @type string $link_target      Optional. The target element for the anchor tag.
 *     @type string $link_description Optional. A short description of the link.
 *     @type string $link_visible     Optional. 'Y' means visible, anything else means not.
 *     @type int    $link_owner       Optional. A user ID.
 *     @type int    $link_rating      Optional. A rating for the link.
 *     @type string $link_rel         Optional. A relationship of the link to you.
 *     @type string $link_notes       Optional. An extended description of or notes on the link.
 *     @type string $link_rss         Optional. A URL of an associated RSS feed.
 *     @type int    $link_category    Optional. The term ID of the link category.
 *                                    If empty, uses default link category.
 * }
 * @param bool  $gc_error Optional. Whether to return a GC_Error object on failure. Default false.
 * @return int|GC_Error Value 0 or GC_Error on failure. The link ID on success.
 */
function gc_insert_link( $linkdata, $gc_error = false ) {
	global $gcdb;

	$defaults = array(
		'link_id'     => 0,
		'link_name'   => '',
		'link_url'    => '',
		'link_rating' => 0,
	);

	$parsed_args = gc_parse_args( $linkdata, $defaults );
	$parsed_args = gc_unslash( sanitize_bookmark( $parsed_args, 'db' ) );

	$link_id   = $parsed_args['link_id'];
	$link_name = $parsed_args['link_name'];
	$link_url  = $parsed_args['link_url'];

	$update = false;
	if ( ! empty( $link_id ) ) {
		$update = true;
	}

	if ( '' === trim( $link_name ) ) {
		if ( '' !== trim( $link_url ) ) {
			$link_name = $link_url;
		} else {
			return 0;
		}
	}

	if ( '' === trim( $link_url ) ) {
		return 0;
	}

	$link_rating      = ( ! empty( $parsed_args['link_rating'] ) ) ? $parsed_args['link_rating'] : 0;
	$link_image       = ( ! empty( $parsed_args['link_image'] ) ) ? $parsed_args['link_image'] : '';
	$link_target      = ( ! empty( $parsed_args['link_target'] ) ) ? $parsed_args['link_target'] : '';
	$link_visible     = ( ! empty( $parsed_args['link_visible'] ) ) ? $parsed_args['link_visible'] : 'Y';
	$link_owner       = ( ! empty( $parsed_args['link_owner'] ) ) ? $parsed_args['link_owner'] : get_current_user_id();
	$link_notes       = ( ! empty( $parsed_args['link_notes'] ) ) ? $parsed_args['link_notes'] : '';
	$link_description = ( ! empty( $parsed_args['link_description'] ) ) ? $parsed_args['link_description'] : '';
	$link_rss         = ( ! empty( $parsed_args['link_rss'] ) ) ? $parsed_args['link_rss'] : '';
	$link_rel         = ( ! empty( $parsed_args['link_rel'] ) ) ? $parsed_args['link_rel'] : '';
	$link_category    = ( ! empty( $parsed_args['link_category'] ) ) ? $parsed_args['link_category'] : array();

	// Make sure we set a valid category.
	if ( ! is_array( $link_category ) || 0 === count( $link_category ) ) {
		$link_category = array( get_option( 'default_link_category' ) );
	}

	if ( $update ) {
		if ( false === $gcdb->update( $gcdb->links, compact( 'link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_rel', 'link_notes', 'link_rss' ), compact( 'link_id' ) ) ) {
			if ( $gc_error ) {
				return new GC_Error( 'db_update_error', __( '无法在数据库中更新链接。' ), $gcdb->last_error );
			} else {
				return 0;
			}
		}
	} else {
		if ( false === $gcdb->insert( $gcdb->links, compact( 'link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_rel', 'link_notes', 'link_rss' ) ) ) {
			if ( $gc_error ) {
				return new GC_Error( 'db_insert_error', __( '无法在数据库中插入链接。' ), $gcdb->last_error );
			} else {
				return 0;
			}
		}
		$link_id = (int) $gcdb->insert_id;
	}

	gc_set_link_cats( $link_id, $link_category );

	if ( $update ) {
		/**
		 * Fires after a link was updated in the database.
		 *
		 *
		 * @param int $link_id ID of the link that was updated.
		 */
		do_action( 'edit_link', $link_id );
	} else {
		/**
		 * Fires after a link was added to the database.
		 *
		 *
		 * @param int $link_id ID of the link that was added.
		 */
		do_action( 'add_link', $link_id );
	}
	clean_bookmark_cache( $link_id );

	return $link_id;
}

/**
 * Update link with the specified link categories.
 *
 *
 *
 * @param int   $link_id         ID of the link to update.
 * @param int[] $link_categories Array of link category IDs to add the link to.
 */
function gc_set_link_cats( $link_id = 0, $link_categories = array() ) {
	// If $link_categories isn't already an array, make it one:
	if ( ! is_array( $link_categories ) || 0 === count( $link_categories ) ) {
		$link_categories = array( get_option( 'default_link_category' ) );
	}

	$link_categories = array_map( 'intval', $link_categories );
	$link_categories = array_unique( $link_categories );

	gc_set_object_terms( $link_id, $link_categories, 'link_category' );

	clean_bookmark_cache( $link_id );
}

/**
 * Updates a link in the database.
 *
 *
 *
 * @param array $linkdata Link data to update. See gc_insert_link() for accepted arguments.
 * @return int|GC_Error Value 0 or GC_Error on failure. The updated link ID on success.
 */
function gc_update_link( $linkdata ) {
	$link_id = (int) $linkdata['link_id'];

	$link = get_bookmark( $link_id, ARRAY_A );

	// Escape data pulled from DB.
	$link = gc_slash( $link );

	// Passed link category list overwrites existing category list if not empty.
	if ( isset( $linkdata['link_category'] ) && is_array( $linkdata['link_category'] )
		&& count( $linkdata['link_category'] ) > 0
	) {
		$link_cats = $linkdata['link_category'];
	} else {
		$link_cats = $link['link_category'];
	}

	// Merge old and new fields with new fields overwriting old ones.
	$linkdata                  = array_merge( $link, $linkdata );
	$linkdata['link_category'] = $link_cats;

	return gc_insert_link( $linkdata );
}

/**
 * Outputs the 'disabled' message for the GeChiUI Link Manager.
 *
 *
 * @access private
 *
 * @global string $pagenow
 */
function gc_link_manager_disabled_message() {
	global $pagenow;

	if ( ! in_array( $pagenow, array( 'link-manager.php', 'link-add.php', 'link.php' ), true ) ) {
		return;
	}

	add_filter( 'pre_option_link_manager_enabled', '__return_true', 100 );
	$really_can_manage_links = current_user_can( 'manage_links' );
	remove_filter( 'pre_option_link_manager_enabled', '__return_true', 100 );

	if ( $really_can_manage_links ) {
		$plugins = get_plugins();

		if ( empty( $plugins['link-manager/link-manager.php'] ) ) {
			if ( current_user_can( 'install_plugins' ) ) {
				$install_url = gc_nonce_url(
					self_admin_url( 'update.php?action=install-plugin&plugin=link-manager' ),
					'install-plugin_link-manager'
				);

				gc_die(
					sprintf(
						/* translators: %s: A link to install the Link Manager plugin. */
						__( '如果您需要使用链接管理器，请安装<a href="%s">链接管理器插件</a>。' ),
						esc_url( $install_url )
					)
				);
			}
		} elseif ( is_plugin_inactive( 'link-manager/link-manager.php' ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				$activate_url = gc_nonce_url(
					self_admin_url( 'plugins.php?action=activate&plugin=link-manager/link-manager.php' ),
					'activate-plugin_link-manager/link-manager.php'
				);

				gc_die(
					sprintf(
						/* translators: %s: A link to activate the Link Manager plugin. */
						__( '请启用<a href="%s">链接管理器插件</a>以使用链接管理器。' ),
						esc_url( $activate_url )
					)
				);
			}
		}
	}

	gc_die( __( '抱歉，您不能在此站点上编辑链接。' ) );
}
