<?php
/**
 * GeChiUI Administration for Navigation Menus
 * Interface functions
 *
 * @version 2.0.0
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Load all the nav menu interface functions.
require_once ABSPATH . 'gc-admin/includes/nav-menu.php';

if ( ! current_theme_supports( 'menus' ) && ! current_theme_supports( 'widgets' ) ) {
	gc_die( __( '您的主题不支持导航菜单或小工具。' ) );
}

// Permissions check.
if ( ! current_user_can( 'edit_theme_options' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此站点上编辑主题选项。' ) . '</p>',
		403
	);
}

gc_enqueue_script( 'nav-menu' );

if ( gc_is_mobile() ) {
	gc_enqueue_script( 'jquery-touch-punch' );
}

// Container for any messages displayed to the user.
$messages = array();

// Container that stores the name of the active menu.
$nav_menu_selected_title = '';

// The menu id of the current menu being edited.
$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

// Get existing menu locations assignments.
$locations      = get_registered_nav_menus();
$menu_locations = get_nav_menu_locations();
$num_locations  = count( array_keys( $locations ) );

// Allowed actions: add, update, delete.
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

/*
 * If a JSON blob of navigation menu data is found, expand it and inject it
 * into `$_POST` to avoid PHP `max_input_vars` limitations. See #14134.
 */
_gc_expand_nav_menu_post_data();

switch ( $action ) {
	case 'add-menu-item':
		check_admin_referer( 'add-menu_item', 'menu-settings-column-nonce' );

		if ( isset( $_REQUEST['nav-menu-locations'] ) ) {
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_REQUEST['menu-locations'] ) );
		} elseif ( isset( $_REQUEST['menu-item'] ) ) {
			gc_save_nav_menu_items( $nav_menu_selected_id, $_REQUEST['menu-item'] );
		}

		break;

	case 'move-down-menu-item':
		// Moving down a menu item is the same as moving up the next in order.
		check_admin_referer( 'move-menu_item' );

		$menu_item_id = isset( $_REQUEST['menu-item'] ) ? (int) $_REQUEST['menu-item'] : 0;

		if ( is_nav_menu_item( $menu_item_id ) ) {
			$menus = isset( $_REQUEST['menu'] ) ? array( (int) $_REQUEST['menu'] ) : gc_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );

			if ( ! is_gc_error( $menus ) && ! empty( $menus[0] ) ) {
				$menu_id            = (int) $menus[0];
				$ordered_menu_items = gc_get_nav_menu_items( $menu_id );
				$menu_item_data     = (array) gc_setup_nav_menu_item( get_post( $menu_item_id ) );

				// Set up the data we need in one pass through the array of menu items.
				$dbids_to_orders = array();
				$orders_to_dbids = array();

				foreach ( (array) $ordered_menu_items as $ordered_menu_item_object ) {
					if ( isset( $ordered_menu_item_object->ID ) ) {
						if ( isset( $ordered_menu_item_object->menu_order ) ) {
							$dbids_to_orders[ $ordered_menu_item_object->ID ]         = $ordered_menu_item_object->menu_order;
							$orders_to_dbids[ $ordered_menu_item_object->menu_order ] = $ordered_menu_item_object->ID;
						}
					}
				}

				// Get next in order.
				if ( isset( $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] + 1 ] ) ) {
					$next_item_id   = $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] + 1 ];
					$next_item_data = (array) gc_setup_nav_menu_item( get_post( $next_item_id ) );

					// If not siblings of same parent, bubble menu item up but keep order.
					if ( ! empty( $menu_item_data['menu_item_parent'] )
						&& ( empty( $next_item_data['menu_item_parent'] )
							|| (int) $next_item_data['menu_item_parent'] !== (int) $menu_item_data['menu_item_parent'] )
					) {
						if ( in_array( (int) $menu_item_data['menu_item_parent'], $orders_to_dbids, true ) ) {
							$parent_db_id = (int) $menu_item_data['menu_item_parent'];
						} else {
							$parent_db_id = 0;
						}

						$parent_object = gc_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_gc_error( $parent_object ) ) {
							$parent_data                        = (array) $parent_object;
							$menu_item_data['menu_item_parent'] = $parent_data['menu_item_parent'];
							update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
						}

						// Make menu item a child of its next sibling.
					} else {
						$next_item_data['menu_order'] = $next_item_data['menu_order'] - 1;
						$menu_item_data['menu_order'] = $menu_item_data['menu_order'] + 1;

						$menu_item_data['menu_item_parent'] = $next_item_data['ID'];
						update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );

						gc_update_post( $menu_item_data );
						gc_update_post( $next_item_data );
					}

					// The item is last but still has a parent, so bubble up.
				} elseif ( ! empty( $menu_item_data['menu_item_parent'] )
					&& in_array( (int) $menu_item_data['menu_item_parent'], $orders_to_dbids, true )
				) {
					$menu_item_data['menu_item_parent'] = (int) get_post_meta( $menu_item_data['menu_item_parent'], '_menu_item_menu_item_parent', true );
					update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
				}
			}
		}

		break;

	case 'move-up-menu-item':
		check_admin_referer( 'move-menu_item' );

		$menu_item_id = isset( $_REQUEST['menu-item'] ) ? (int) $_REQUEST['menu-item'] : 0;

		if ( is_nav_menu_item( $menu_item_id ) ) {
			if ( isset( $_REQUEST['menu'] ) ) {
				$menus = array( (int) $_REQUEST['menu'] );
			} else {
				$menus = gc_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			}

			if ( ! is_gc_error( $menus ) && ! empty( $menus[0] ) ) {
				$menu_id            = (int) $menus[0];
				$ordered_menu_items = gc_get_nav_menu_items( $menu_id );
				$menu_item_data     = (array) gc_setup_nav_menu_item( get_post( $menu_item_id ) );

				// Set up the data we need in one pass through the array of menu items.
				$dbids_to_orders = array();
				$orders_to_dbids = array();

				foreach ( (array) $ordered_menu_items as $ordered_menu_item_object ) {
					if ( isset( $ordered_menu_item_object->ID ) ) {
						if ( isset( $ordered_menu_item_object->menu_order ) ) {
							$dbids_to_orders[ $ordered_menu_item_object->ID ]         = $ordered_menu_item_object->menu_order;
							$orders_to_dbids[ $ordered_menu_item_object->menu_order ] = $ordered_menu_item_object->ID;
						}
					}
				}

				// If this menu item is not first.
				if ( ! empty( $dbids_to_orders[ $menu_item_id ] )
					&& ! empty( $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ] )
				) {

					// If this menu item is a child of the previous.
					if ( ! empty( $menu_item_data['menu_item_parent'] )
						&& in_array( (int) $menu_item_data['menu_item_parent'], array_keys( $dbids_to_orders ), true )
						&& isset( $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ] )
						&& ( (int) $menu_item_data['menu_item_parent'] === $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ] )
					) {
						if ( in_array( (int) $menu_item_data['menu_item_parent'], $orders_to_dbids, true ) ) {
							$parent_db_id = (int) $menu_item_data['menu_item_parent'];
						} else {
							$parent_db_id = 0;
						}

						$parent_object = gc_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_gc_error( $parent_object ) ) {
							$parent_data = (array) $parent_object;

							/*
							 * If there is something before the parent and parent a child of it,
							 * make menu item a child also of it.
							 */
							if ( ! empty( $dbids_to_orders[ $parent_db_id ] )
								&& ! empty( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ] )
								&& ! empty( $parent_data['menu_item_parent'] )
							) {
								$menu_item_data['menu_item_parent'] = $parent_data['menu_item_parent'];

								/*
								* Else if there is something before parent and parent not a child of it,
								* make menu item a child of that something's parent
								*/
							} elseif ( ! empty( $dbids_to_orders[ $parent_db_id ] )
								&& ! empty( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ] )
							) {
								$_possible_parent_id = (int) get_post_meta( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ], '_menu_item_menu_item_parent', true );

								if ( in_array( $_possible_parent_id, array_keys( $dbids_to_orders ), true ) ) {
									$menu_item_data['menu_item_parent'] = $_possible_parent_id;
								} else {
									$menu_item_data['menu_item_parent'] = 0;
								}

								// Else there isn't something before the parent.
							} else {
								$menu_item_data['menu_item_parent'] = 0;
							}

							// Set former parent's [menu_order] to that of menu-item's.
							$parent_data['menu_order'] = $parent_data['menu_order'] + 1;

							// Set menu-item's [menu_order] to that of former parent.
							$menu_item_data['menu_order'] = $menu_item_data['menu_order'] - 1;

							// Save changes.
							update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
							gc_update_post( $menu_item_data );
							gc_update_post( $parent_data );
						}

						// Else this menu item is not a child of the previous.
					} elseif ( empty( $menu_item_data['menu_order'] )
						|| empty( $menu_item_data['menu_item_parent'] )
						|| ! in_array( (int) $menu_item_data['menu_item_parent'], array_keys( $dbids_to_orders ), true )
						|| empty( $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ] )
						|| $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ] !== (int) $menu_item_data['menu_item_parent']
					) {
						// Just make it a child of the previous; keep the order.
						$menu_item_data['menu_item_parent'] = (int) $orders_to_dbids[ $dbids_to_orders[ $menu_item_id ] - 1 ];
						update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
						gc_update_post( $menu_item_data );
					}
				}
			}
		}

		break;

	case 'delete-menu-item':
		$menu_item_id = (int) $_REQUEST['menu-item'];

		check_admin_referer( 'delete-menu_item_' . $menu_item_id );

		if ( is_nav_menu_item( $menu_item_id ) && gc_delete_post( $menu_item_id, true ) ) {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( '菜单项已被成功删除。' ) . '</p></div>';
		}

		break;

	case 'delete':
		check_admin_referer( 'delete-nav_menu-' . $nav_menu_selected_id );

		if ( is_nav_menu( $nav_menu_selected_id ) ) {
			$deletion = gc_delete_nav_menu( $nav_menu_selected_id );
		} else {
			// Reset the selected menu.
			$nav_menu_selected_id = 0;
			unset( $_REQUEST['menu'] );
		}

		if ( ! isset( $deletion ) ) {
			break;
		}

		if ( is_gc_error( $deletion ) ) {
			$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $deletion->get_error_message() . '</p></div>';
		} else {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( '菜单已被成功删除。' ) . '</p></div>';
		}

		break;

	case 'delete_menus':
		check_admin_referer( 'nav_menus_bulk_actions' );

		foreach ( $_REQUEST['delete_menus'] as $menu_id_to_delete ) {
			if ( ! is_nav_menu( $menu_id_to_delete ) ) {
				continue;
			}

			$deletion = gc_delete_nav_menu( $menu_id_to_delete );

			if ( is_gc_error( $deletion ) ) {
				$messages[]     = '<div id="message" class="error notice is-dismissible"><p>' . $deletion->get_error_message() . '</p></div>';
				$deletion_error = true;
			}
		}

		if ( empty( $deletion_error ) ) {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( '菜单已被成功删除。' ) . '</p></div>';
		}

		break;

	case 'update':
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Merge new and existing menu locations if any new ones are set.
		$new_menu_locations = array();
		if ( isset( $_POST['menu-locations'] ) ) {
			$new_menu_locations = array_map( 'absint', $_POST['menu-locations'] );
			$menu_locations     = array_merge( $menu_locations, $new_menu_locations );
		}

		// Add Menu.
		if ( 0 === $nav_menu_selected_id ) {
			$new_menu_title = trim( esc_html( $_POST['menu-name'] ) );

			if ( $new_menu_title ) {
				$_nav_menu_selected_id = gc_update_nav_menu_object( 0, array( 'menu-name' => $new_menu_title ) );

				if ( is_gc_error( $_nav_menu_selected_id ) ) {
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object            = gc_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id    = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;

					if ( isset( $_REQUEST['menu-item'] ) ) {
						gc_save_nav_menu_items( $nav_menu_selected_id, absint( $_REQUEST['menu-item'] ) );
					}

					if ( isset( $_REQUEST['zero-menu-state'] ) || ! empty( $_POST['auto-add-pages'] ) ) {
						// If there are menu items, add them.
						gc_nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title );
					}

					if ( isset( $_REQUEST['zero-menu-state'] ) ) {
						// Auto-save nav_menu_locations.
						$locations = get_nav_menu_locations();

						foreach ( $locations as $location => $menu_id ) {
								$locations[ $location ] = $nav_menu_selected_id;
								break; // There should only be 1.
						}

						set_theme_mod( 'nav_menu_locations', $locations );
					} elseif ( count( $new_menu_locations ) > 0 ) {
						// If locations have been selected for the new menu, save those.
						$locations = get_nav_menu_locations();

						foreach ( array_keys( $new_menu_locations ) as $location ) {
							$locations[ $location ] = $nav_menu_selected_id;
						}

						set_theme_mod( 'nav_menu_locations', $locations );
					}

					if ( isset( $_REQUEST['use-location'] ) ) {
						$locations      = get_registered_nav_menus();
						$menu_locations = get_nav_menu_locations();

						if ( isset( $locations[ $_REQUEST['use-location'] ] ) ) {
							$menu_locations[ $_REQUEST['use-location'] ] = $nav_menu_selected_id;
						}

						set_theme_mod( 'nav_menu_locations', $menu_locations );
					}

					gc_redirect( admin_url( 'nav-menus.php?menu=' . $_nav_menu_selected_id ) );
					exit;
				}
			} else {
				$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( '请输入有效的菜单名称。' ) . '</p></div>';
			}

			// Update existing menu.
		} else {
			// Remove menu locations that have been unchecked.
			foreach ( $locations as $location => $description ) {
				if ( ( empty( $_POST['menu-locations'] ) || empty( $_POST['menu-locations'][ $location ] ) )
					&& isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] === $nav_menu_selected_id
				) {
					unset( $menu_locations[ $location ] );
				}
			}

			// Set menu locations.
			set_theme_mod( 'nav_menu_locations', $menu_locations );

			$_menu_object = gc_get_nav_menu_object( $nav_menu_selected_id );

			$menu_title = trim( esc_html( $_POST['menu-name'] ) );

			if ( ! $menu_title ) {
				$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( '请输入有效的菜单名称。' ) . '</p></div>';
				$menu_title = $_menu_object->name;
			}

			if ( ! is_gc_error( $_menu_object ) ) {
				$_nav_menu_selected_id = gc_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $menu_title ) );

				if ( is_gc_error( $_nav_menu_selected_id ) ) {
					$_menu_object = $_nav_menu_selected_id;
					$messages[]   = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object            = gc_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items.
			if ( ! is_gc_error( $_menu_object ) ) {
				$messages = array_merge( $messages, gc_nav_menu_update_menu_items( $_nav_menu_selected_id, $nav_menu_selected_title ) );

				// If the menu ID changed, redirect to the new URL.
				if ( $nav_menu_selected_id !== $_nav_menu_selected_id ) {
					gc_redirect( admin_url( 'nav-menus.php?menu=' . (int) $_nav_menu_selected_id ) );
					exit;
				}
			}
		}

		break;

	case 'locations':
		if ( ! $num_locations ) {
			gc_redirect( admin_url( 'nav-menus.php' ) );
			exit;
		}

		add_filter( 'screen_options_show_screen', '__return_false' );

		if ( isset( $_POST['menu-locations'] ) ) {
			check_admin_referer( 'save-menu-locations' );

			$new_menu_locations = array_map( 'absint', $_POST['menu-locations'] );
			$menu_locations     = array_merge( $menu_locations, $new_menu_locations );
			// Set menu locations.
			set_theme_mod( 'nav_menu_locations', $menu_locations );

			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( '菜单位置已更新。' ) . '</p></div>';
		}

		break;
}

// Get all nav menus.
$nav_menus  = gc_get_nav_menus();
$menu_count = count( $nav_menus );

// Are we on the add new screen?
$add_new_screen = ( isset( $_GET['menu'] ) && 0 === (int) $_GET['menu'] ) ? true : false;

$locations_screen = ( isset( $_GET['action'] ) && 'locations' === $_GET['action'] ) ? true : false;

$page_count = gc_count_posts( 'page' );

/*
 * If we have one theme location, and zero menus, we take them right
 * into editing their first menu.
 */
if ( 1 === count( get_registered_nav_menus() ) && ! $add_new_screen
	&& empty( $nav_menus ) && ! empty( $page_count->publish )
) {
	$one_theme_location_no_menus = true;
} else {
	$one_theme_location_no_menus = false;
}

$nav_menus_l10n = array(
	'oneThemeLocationNoMenus' => $one_theme_location_no_menus,
	'moveUp'                  => __( '上移一项' ),
	'moveDown'                => __( '下移一项' ),
	'moveToTop'               => __( '移动至顶端' ),
	/* translators: %s: Previous item name. */
	'moveUnder'               => __( '移至%s下' ),
	/* translators: %s: Previous item name. */
	'moveOutFrom'             => __( '从%s移出' ),
	/* translators: %s: Previous item name. */
	'under'                   => __( '%s下' ),
	/* translators: %s: Previous item name. */
	'outFrom'                 => __( '%s之外' ),
	/* translators: 1: Item name, 2: Item position, 3: Total number of items. */
	'menuFocus'               => __( '%1$s。%3$s个菜单项之%2$s。' ),
	/* translators: 1: Item name, 2: Item position, 3: Parent item name. */
	'subMenuFocus'            => __( '%1$s。%3$s菜单中的第%2$d项。' ),
	/* translators: %s: Item name. */
	'menuItemDeletion'        => __( '项目%s' ),
	/* translators: %s: Item name. */
	'itemsDeleted'            => __( '已删除菜单项：%s。' ),
	'itemAdded'               => __( '菜单项已添加' ),
	'itemRemoved'             => __( '菜单项已移除' ),
	'movedUp'                 => __( '菜单项已上移' ),
	'movedDown'               => __( '菜单项已下移' ),
	'movedTop'                => __( '菜单项已移至顶部' ),
	'movedLeft'               => __( '菜单项已移动至子菜单外' ),
	'movedRight'              => __( '菜单项已是子项目' ),
);
gc_localize_script( 'nav-menu', 'menus', $nav_menus_l10n );

/*
 * Redirect to add screen if there are no menus and this users has either zero,
 * or more than 1 theme locations.
 */
if ( 0 === $menu_count && ! $add_new_screen && ! $one_theme_location_no_menus ) {
	gc_redirect( admin_url( 'nav-menus.php?action=edit&menu=0' ) );
}

// Get recently edited nav menu.
$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) ) {
	$recently_edited = $nav_menu_selected_id;
}

// Use $recently_edited if none are selected.
if ( empty( $nav_menu_selected_id ) && ! isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) ) {
	$nav_menu_selected_id = $recently_edited;
}

// On deletion of menu, if another menu exists, show it.
if ( ! $add_new_screen && $menu_count > 0 && isset( $_GET['action'] ) && 'delete' === $_GET['action'] ) {
	$nav_menu_selected_id = $nav_menus[0]->term_id;
}

// Set $nav_menu_selected_id to 0 if no menus.
if ( $one_theme_location_no_menus ) {
	$nav_menu_selected_id = 0;
} elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
	// If we have no selection yet, and we have menus, set to the first one in the list.
	$nav_menu_selected_id = $nav_menus[0]->term_id;
}

// Update the user's setting.
if ( $nav_menu_selected_id !== $recently_edited && is_nav_menu( $nav_menu_selected_id ) ) {
	update_user_meta( $current_user->ID, 'nav_menu_recently_edited', $nav_menu_selected_id );
}

// If there's a menu, get its name.
if ( ! $nav_menu_selected_title && is_nav_menu( $nav_menu_selected_id ) ) {
	$_menu_object            = gc_get_nav_menu_object( $nav_menu_selected_id );
	$nav_menu_selected_title = ! is_gc_error( $_menu_object ) ? $_menu_object->name : '';
}

// Generate truncated menu names.
foreach ( (array) $nav_menus as $key => $_nav_menu ) {
	$nav_menus[ $key ]->truncated_name = gc_html_excerpt( $_nav_menu->name, 40, '&hellip;' );
}

// Retrieve menu locations.
if ( current_theme_supports( 'menus' ) ) {
	$locations      = get_registered_nav_menus();
	$menu_locations = get_nav_menu_locations();
}

/*
 * Ensure the user will be able to scroll horizontally
 * by adding a class for the max menu depth.
 *
 * @global int $_gc_nav_menu_max_depth
 */
global $_gc_nav_menu_max_depth;
$_gc_nav_menu_max_depth = 0;

// Calling gc_get_nav_menu_to_edit generates $_gc_nav_menu_max_depth.
if ( is_nav_menu( $nav_menu_selected_id ) ) {
	$menu_items  = gc_get_nav_menu_items( $nav_menu_selected_id, array( 'post_status' => 'any' ) );
	$edit_markup = gc_get_nav_menu_to_edit( $nav_menu_selected_id );
}

/**
 * @global int $_gc_nav_menu_max_depth
 *
 * @param string $classes
 * @return string
 */
function gc_nav_menu_max_depth( $classes ) {
	global $_gc_nav_menu_max_depth;
	return "$classes menu-max-depth-$_gc_nav_menu_max_depth";
}

add_filter( 'admin_body_class', 'gc_nav_menu_max_depth' );

gc_nav_menu_setup();
gc_initial_nav_menu_meta_boxes();

if ( ! current_theme_supports( 'menus' ) && ! $num_locations ) {
	$messages[] = '<div id="message" class="updated"><p>' . sprintf(
		/* translators: %s: URL to Widgets screen. */
		__( '当前的主题未提供原生的菜单支持，您可以通过在<a href="%s">小工具</a>添加“导航菜单”小工具来在侧栏上显示。' ),
		admin_url( 'widgets.php' )
	) . '</p></div>';
}

if ( ! $locations_screen ) : // Main tab.
	$overview  = '<p>' . __( '此界面用于管理您的导航菜单。' ) . '</p>';
	$overview .= '<p>' . sprintf(
		/* translators: 1: URL to Widgets screen, 2 and 3: The names of the default themes. */
		__( '菜单可在您的主题指定的位置显示，或通过<a href="%1$s">小工具</a>中的“导航菜单”小工具显示在侧栏中。如果您的主题不支持导航菜单功能（默认主题%2$s和%3$s均支持），您可以查阅侧面的文档链接了解如何加入这一支持。' ),
		admin_url( 'widgets.php' ),
		'Twenty Twenty',
		'Twenty Twenty-One'
	) . '</p>';
	$overview .= '<p>' . __( '在此界面中，您可以：' ) . '</p>';
	$overview .= '<ul><li>' . __( '创建、编辑和删除菜单' ) . '</li>';
	$overview .= '<li>' . __( '添加、管理和修改某一菜单项' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' => $overview,
		)
	);

	$menu_management  = '<p>' . __( '顶部的菜单管理框可用来控制在下方编辑器中打开哪个菜单。' ) . '</p>';
	$menu_management .= '<ul><li>' . __( '要编辑已存在的菜单，<strong>在下拉菜单中选择菜单并点击“选择”</strong>' ) . '</li>';
	$menu_management .= '<li>' . __( '如果您还没有创建任何菜单，请<strong>点击“创建新菜单”链接</strong>来开始' ) . '</li></ul>';
	$menu_management .= '<p>' . __( '要指派单独菜单的主题位置，您可在菜单编辑器的底部<strong>选择想要的设置</strong>。要指派菜单到所有主题位置，请在顶部<strong>访问“管理位置”页</strong>。' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'menu-management',
			'title'   => __( '菜单管理' ),
			'content' => $menu_management,
		)
	);

	$editing_menus  = '<p>' . __( '每个导航菜单都可包含页面、链接、自定义URL或其他内容类型。在下方左栏中展开的框中选择项目可添加菜单链接。' ) . '</p>';
	$editing_menus .= '<p>' . __( '在编辑器中<strong>点击任一菜单项目标题右侧的箭头</strong>会显示一组标准设置。附加设置（如链接目标、CSS类、链接关系和链接描述等）可在“显示选项”页中设置显示或隐藏。' ) . '</p>';
	$editing_menus .= '<ul><li>' . __( '要添加一个或多个项目，请<strong>选择每个项目前的复选框，并点击“添加到菜单”</strong>' ) . '</li>';
	$editing_menus .= '<li>' . __( '要添加自定义链接，<strong>展开自定义链接小节，输入URL和链接文本，然后点击添加到菜单</strong>' ) . '</li>';
	$editing_menus .= '<li>' . __( '要重排菜单项目，请<strong>用鼠标或键盘拖动项目</strong>。将项目稍稍拖到右侧可将其变为子菜单' ) . '</li>';
	$editing_menus .= '<li>' . __( '要删除菜单项目，请<strong>将其展开并点击“移除”链接</strong>' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'editing-menus',
			'title'   => __( '编辑菜单' ),
			'content' => $editing_menus,
		)
	);
else : // Locations tab.
	$locations_overview  = '<p>' . __( '在本页面调整主题中菜单的位置。' ) . '</p>';
	$locations_overview .= '<ul><li>' . __( '要将菜单指派到主题位置，<strong>在每个位置的下拉菜单中选择菜单</strong>。当您完成后，<strong>点击“保存修改”</strong>' ) . '</li>';
	$locations_overview .= '<li>' . __( '要编辑已被指派到主题位置的菜单，<strong>点击相应的“编辑”链接</strong>' ) . '</li>';
	$locations_overview .= '<li>' . __( '要添加菜单而不是指派已存在的菜单，<strong>点击“使用新菜单”链接</strong>。您的新菜单会被自动指派到相应的主题位置' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'locations-overview',
			'title'   => __( '概述' ),
			'content' => $locations_overview,
		)
	);
endif;

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/appearance-menus-screen/">菜单文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

// Get the admin header.
require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap">
	<h1 class="gc-heading-inline"><?php esc_html_e( '菜单' ); ?></h1>
	<?php
	if ( current_user_can( 'customize' ) ) :
		$focus = $locations_screen ? array( 'section' => 'menu_locations' ) : array( 'panel' => 'nav_menus' );
		printf(
			' <a class="page-title-action hide-if-no-customize" href="%1$s">%2$s</a>',
			esc_url(
				add_query_arg(
					array(
						array( 'autofocus' => $focus ),
						'return' => urlencode( remove_query_arg( gc_removable_query_args(), gc_unslash( $_SERVER['REQUEST_URI'] ) ) ),
					),
					admin_url( 'customize.php' )
				)
			),
			__( '使用实时预览管理' )
		);
	endif;

	$nav_tab_active_class = '';
	$nav_aria_current     = '';

	if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'locations' !== $_GET['action'] ) {
		$nav_tab_active_class = ' nav-tab-active';
		$nav_aria_current     = ' aria-current="page"';
	}
	?>

	<hr class="gc-header-end">

	<nav class="nav-tab-wrapper gc-clearfix" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="nav-tab<?php echo $nav_tab_active_class; ?>"<?php echo $nav_aria_current; ?>><?php esc_html_e( '编辑菜单' ); ?></a>
		<?php
		if ( $num_locations && $menu_count ) {
			$active_tab_class = '';
			$aria_current     = '';

			if ( $locations_screen ) {
				$active_tab_class = ' nav-tab-active';
				$aria_current     = ' aria-current="page"';
			}
			?>
			<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'locations' ), admin_url( 'nav-menus.php' ) ) ); ?>" class="nav-tab<?php echo $active_tab_class; ?>"<?php echo $aria_current; ?>><?php esc_html_e( '管理位置' ); ?></a>
			<?php
		}
		?>
	</nav>
	<?php
	foreach ( $messages as $message ) :
		echo $message . "\n";
	endforeach;
	?>
	<?php
	if ( $locations_screen ) :
		if ( 1 === $num_locations ) {
			echo '<p>' . __( '您的主题支持一个菜单，请选择您希望使用的菜单。' ) . '</p>';
		} else {
			echo '<p>' . sprintf(
				/* translators: %s: Number of menus. */
				_n(
					'您的主题支持%s个菜单，请分别选择您希望在每一处出现的菜单。',
					'您的主题支持%s个菜单，请分别选择您希望在每一处出现的菜单。',
					$num_locations
				),
				number_format_i18n( $num_locations )
			) . '</p>';
		}
		?>
	<div id="menu-locations-wrap">
		<form method="post" action="<?php echo esc_url( add_query_arg( array( 'action' => 'locations' ), admin_url( 'nav-menus.php' ) ) ); ?>">
			<table class="widefat fixed" id="menu-locations-table">
				<thead>
				<tr>
					<th scope="col" class="manage-column column-locations"><?php _e( '主题位置' ); ?></th>
					<th scope="col" class="manage-column column-menus"><?php _e( '已指派的菜单' ); ?></th>
				</tr>
				</thead>
				<tbody class="menu-locations">
				<?php foreach ( $locations as $_location => $_name ) { ?>
					<tr class="menu-locations-row">
						<td class="menu-location-title"><label for="locations-<?php echo $_location; ?>"><?php echo $_name; ?></label></td>
						<td class="menu-location-menus">
							<select name="menu-locations[<?php echo $_location; ?>]" id="locations-<?php echo $_location; ?>">
								<option value="0"><?php printf( '&mdash; %s &mdash;', esc_html__( '选择菜单' ) ); ?></option>
								<?php
								foreach ( $nav_menus as $menu ) :
									$data_orig = '';
									$selected  = isset( $menu_locations[ $_location ] ) && $menu_locations[ $_location ] === $menu->term_id;

									if ( $selected ) {
										$data_orig = 'data-orig="true"';
									}
									?>
									<option <?php echo $data_orig; ?> <?php selected( $selected ); ?> value="<?php echo $menu->term_id; ?>">
										<?php echo gc_html_excerpt( $menu->name, 40, '&hellip;' ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="locations-row-links">
								<?php if ( isset( $menu_locations[ $_location ] ) && 0 !== $menu_locations[ $_location ] ) : ?>
								<span class="locations-edit-menu-link">
									<a href="
									<?php
									echo esc_url(
										add_query_arg(
											array(
												'action' => 'edit',
												'menu'   => $menu_locations[ $_location ],
											),
											admin_url( 'nav-menus.php' )
										)
									);
									?>
									">
										<span aria-hidden="true"><?php _ex( '编辑', 'menu' ); ?></span><span class="screen-reader-text"><?php _e( '编辑选中的菜单' ); ?></span>
									</a>
								</span>
								<?php endif; ?>
								<span class="locations-add-menu-link">
									<a href="
									<?php
									echo esc_url(
										add_query_arg(
											array(
												'action' => 'edit',
												'menu'   => 0,
												'use-location' => $_location,
											),
											admin_url( 'nav-menus.php' )
										)
									);
									?>
									">
										<?php _ex( '使用新菜单', 'menu' ); ?>
									</a>
								</span>
							</div><!-- .locations-row-links -->
						</td><!-- .menu-location-menus -->
					</tr><!-- .menu-locations-row -->
				<?php } // End foreach. ?>
				</tbody>
			</table>
			<p class="button-controls gc-clearfix"><?php submit_button( __( '保存更改' ), 'primary left', 'nav-menu-locations', false ); ?></p>
			<?php gc_nonce_field( 'save-menu-locations' ); ?>
			<input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
		</form>
	</div><!-- #menu-locations-wrap -->
		<?php
		/**
		 * Fires after the menu locations table is displayed.
		 *
		 */
		do_action( 'after_menu_locations_table' );
		?>
	<?php else : ?>
	<div class="manage-menus">
		<?php if ( $menu_count < 1 ) : ?>
		<span class="first-menu-message">
			<?php _e( '在此创建您的第一个菜单。' ); ?>
			<span class="screen-reader-text"><?php _e( '填写菜单名称并点击创建菜单按钮来创建您的第一个菜单。' ); ?></span>
		</span><!-- /first-menu-message -->
		<?php elseif ( $menu_count < 2 ) : ?>
		<span class="add-edit-menu-action">
			<?php
			printf(
				/* translators: %s: URL to create a new menu. */
				__( '在此编辑您的菜单，或<a href="%s">创建新菜单</a>。不要忘了保存您的修改！' ),
				esc_url(
					add_query_arg(
						array(
							'action' => 'edit',
							'menu'   => 0,
						),
						admin_url( 'nav-menus.php' )
					)
				)
			);
			?>
			<span class="screen-reader-text"><?php _e( '点击保存菜单按钮来保存您的修改。' ); ?></span>
		</span><!-- /add-edit-menu-action -->
		<?php else : ?>
			<form method="get" action="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
			<input type="hidden" name="action" value="edit" />
			<label for="select-menu-to-edit" class="selected-menu"><?php _e( '选择要编辑的菜单：' ); ?></label>
			<select name="menu" id="select-menu-to-edit">
				<?php if ( $add_new_screen ) : ?>
					<option value="0" selected="selected"><?php _e( '&mdash;选择&mdash;' ); ?></option>
				<?php endif; ?>
				<?php foreach ( (array) $nav_menus as $_nav_menu ) : ?>
					<option value="<?php echo esc_attr( $_nav_menu->term_id ); ?>" <?php selected( $_nav_menu->term_id, $nav_menu_selected_id ); ?>>
						<?php
						echo esc_html( $_nav_menu->truncated_name );

						if ( ! empty( $menu_locations ) && in_array( $_nav_menu->term_id, $menu_locations, true ) ) {
							$locations_assigned_to_this_menu = array();

							foreach ( array_keys( $menu_locations, $_nav_menu->term_id, true ) as $menu_location_key ) {
								if ( isset( $locations[ $menu_location_key ] ) ) {
									$locations_assigned_to_this_menu[] = $locations[ $menu_location_key ];
								}
							}

							/**
							 * Filters the number of locations listed per menu in the drop-down select.
							 *
						
							 *
							 * @param int $locations Number of menu locations to list. Default 3.
							 */
							$locations_listed_per_menu = absint( apply_filters( 'gc_nav_locations_listed_per_menu', 3 ) );

							$assigned_locations = array_slice( $locations_assigned_to_this_menu, 0, $locations_listed_per_menu );

							// Adds ellipses following the number of locations defined in $assigned_locations.
							if ( ! empty( $assigned_locations ) ) {
								printf(
									' (%1$s%2$s)',
									implode( ', ', $assigned_locations ),
									count( $locations_assigned_to_this_menu ) > count( $assigned_locations ) ? ' &hellip;' : ''
								);
							}
						}
						?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="submit-btn"><input type="submit" class="button" value="<?php esc_attr_e( '选择' ); ?>"></span>
			<span class="add-new-menu-action">
				<?php
				printf(
					/* translators: %s: URL to create a new menu. */
					__( '或<a href="%s">创建新菜单</a>。不要忘了保存您的修改！' ),
					esc_url(
						add_query_arg(
							array(
								'action' => 'edit',
								'menu'   => 0,
							),
							admin_url( 'nav-menus.php' )
						)
					)
				);
				?>
				<span class="screen-reader-text"><?php _e( '点击保存菜单按钮来保存您的修改。' ); ?></span>
			</span><!-- /add-new-menu-action -->
		</form>
			<?php
		endif;

		$metabox_holder_disabled_class = '';

		if ( isset( $_GET['menu'] ) && 0 === (int) $_GET['menu'] ) {
			$metabox_holder_disabled_class = ' metabox-holder-disabled';
		}
		?>
	</div><!-- /manage-menus -->
	<div id="nav-menus-frame" class="gc-clearfix">
	<div id="menu-settings-column" class="metabox-holder<?php echo $metabox_holder_disabled_class; ?>">

		<div class="clear"></div>

		<form id="nav-menu-meta" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<input type="hidden" name="action" value="add-menu-item" />
			<?php gc_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); ?>
			<h2><?php _e( '添加菜单项' ); ?></h2>
			<?php do_accordion_sections( 'nav-menus', 'side', null ); ?>
		</form>

	</div><!-- /#menu-settings-column -->
	<div id="menu-management-liquid">
		<div id="menu-management">
			<form id="update-nav-menu" method="post" enctype="multipart/form-data">
				<h2><?php _e( '菜单结构' ); ?></h2>
				<div class="menu-edit">
					<input type="hidden" name="nav-menu-data">
					<?php
					gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					gc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
					gc_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );

					$menu_name_aria_desc = $add_new_screen ? ' aria-describedby="menu-name-desc"' : '';

					if ( $one_theme_location_no_menus ) {
						$menu_name_val = 'value="' . esc_attr( 'Menu 1' ) . '"';
						?>
						<input type="hidden" name="zero-menu-state" value="true" />
						<?php
					} else {
						$menu_name_val = 'value="' . esc_attr( $nav_menu_selected_title ) . '"';
					}
					?>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="menu" id="menu" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
					<div id="nav-menu-header">
						<div class="major-publishing-actions gc-clearfix">
							<label class="menu-name-label" for="menu-name"><?php _e( '菜单名称' ); ?></label>
							<input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox form-required" required="required" <?php echo $menu_name_val . $menu_name_aria_desc; ?> />
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( '创建菜单' ) : __( '保存菜单' ), 'primary large menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- END .nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content" class="gc-clearfix">
							<?php if ( ! $add_new_screen ) : ?>
								<?php
								$hide_style = '';

								if ( isset( $menu_items ) && 0 === count( $menu_items ) ) {
									$hide_style = 'style="display: none;"';
								}

								if ( $one_theme_location_no_menus ) {
									$starter_copy = __( '您可以通过加入或删除项目来编辑默认菜单，并将项目拖动到您喜欢的顺序。点击“创建菜单”按钮保存您的修改。' );
								} else {
									$starter_copy = __( '将各个项目拖到您喜欢的顺序。点击项目右边的箭头可显示其他配置选项。' );
								}
								?>
								<div class="drag-instructions post-body-plain" <?php echo $hide_style; ?>>
									<p><?php echo $starter_copy; ?></p>
								</div>

								<?php if ( ! $add_new_screen ) : ?>
									<div id="nav-menu-bulk-actions-top" class="bulk-actions">
										<label class="bulk-select-button" for="bulk-select-switcher-top">
											<input type="checkbox" id="bulk-select-switcher-top" name="bulk-select-switcher-top" class="bulk-select-switcher">
											<span class="bulk-select-button-label"><?php _e( '批量选择' ); ?></span>
										</label>
									</div>
								<?php endif; ?>

								<?php
								if ( isset( $edit_markup ) && ! is_gc_error( $edit_markup ) ) {
									echo $edit_markup;
								} else {
									?>
									<ul class="menu" id="menu-to-edit"></ul>
								<?php } ?>

							<?php endif; ?>

							<?php if ( $add_new_screen ) : ?>
								<p class="post-body-plain" id="menu-name-desc"><?php _e( '给菜单命名，然后点击“创建菜单”。' ); ?></p>
								<?php if ( isset( $_GET['use-location'] ) ) : ?>
									<input type="hidden" name="use-location" value="<?php echo esc_attr( $_GET['use-location'] ); ?>" />
								<?php endif; ?>

								<?php
							endif;

							$no_menus_style = '';

							if ( $one_theme_location_no_menus ) {
								$no_menus_style = 'style="display: none;"';
							}
							?>

							<?php if ( ! $add_new_screen ) : ?>
								<div id="nav-menu-bulk-actions-bottom" class="bulk-actions">
									<label class="bulk-select-button" for="bulk-select-switcher-bottom">
										<input type="checkbox" id="bulk-select-switcher-bottom" name="bulk-select-switcher-top" class="bulk-select-switcher">
										<span class="bulk-select-button-label"><?php _e( '批量选择' ); ?></span>
									</label>
									<input type="button" class="deletion menu-items-delete disabled" value="<?php _e( '移除所选项目' ); ?>">
									<div id="pending-menu-items-to-delete">
										<p><?php _e( '选择要删除的菜单项列表：' ); ?></p>
										<ul></ul>
									</div>
								</div>
							<?php endif; ?>

							<div class="menu-settings" <?php echo $no_menus_style; ?>>
								<h3><?php _e( '菜单设置' ); ?></h3>
								<?php
								if ( ! isset( $auto_add ) ) {
									$auto_add = get_option( 'nav_menu_options' );

									if ( ! isset( $auto_add['auto_add'] ) ) {
										$auto_add = false;
									} elseif ( false !== array_search( $nav_menu_selected_id, $auto_add['auto_add'], true ) ) {
										$auto_add = true;
									} else {
										$auto_add = false;
									}
								}
								?>

								<fieldset class="menu-settings-group auto-add-pages">
									<legend class="menu-settings-group-name howto"><?php _e( '自动添加页面' ); ?></legend>
									<div class="menu-settings-input checkbox-input">
										<input type="checkbox"<?php checked( $auto_add ); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf( __( '自动将新的顶级页面添加至此菜单' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></label>
									</div>
								</fieldset>

								<?php if ( current_theme_supports( 'menus' ) ) : ?>

									<fieldset class="menu-settings-group menu-theme-locations">
										<legend class="menu-settings-group-name howto"><?php _e( '显示位置' ); ?></legend>
										<?php
										foreach ( $locations as $location => $description ) :
											$checked = false;

											if ( isset( $menu_locations[ $location ] )
													&& 0 !== $nav_menu_selected_id
													&& $menu_locations[ $location ] === $nav_menu_selected_id
											) {
													$checked = true;
											}
											?>
											<div class="menu-settings-input checkbox-input">
												<input type="checkbox"<?php checked( $checked ); ?> name="menu-locations[<?php echo esc_attr( $location ); ?>]" id="locations-<?php echo esc_attr( $location ); ?>" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
												<label for="locations-<?php echo esc_attr( $location ); ?>"><?php echo $description; ?></label>
												<?php if ( ! empty( $menu_locations[ $location ] ) && $menu_locations[ $location ] !== $nav_menu_selected_id ) : ?>
													<span class="theme-location-set">
													<?php
														printf(
															/* translators: %s: Menu name. */
															_x( '（当前设置为：%s）', 'menu location' ),
															gc_get_nav_menu_object( $menu_locations[ $location ] )->name
														);
													?>
													</span>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</fieldset>

								<?php endif; ?>

							</div>
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer">
						<div class="major-publishing-actions gc-clearfix">
							<?php if ( $menu_count > 0 ) : ?>

								<?php if ( $add_new_screen ) : ?>
								<span class="cancel-action">
									<a class="submitcancel cancellation menu-cancel" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php _e( '取消' ); ?></a>
								</span><!-- END .cancel-action -->
								<?php else : ?>
								<span class="delete-action">
									<a class="submitdelete deletion menu-delete" href="
									<?php
									echo esc_url(
										gc_nonce_url(
											add_query_arg(
												array(
													'action' => 'delete',
													'menu' => $nav_menu_selected_id,
												),
												admin_url( 'nav-menus.php' )
											),
											'delete-nav_menu-' . $nav_menu_selected_id
										)
									);
									?>
									"><?php _e( '删除菜单' ); ?></a>
								</span><!-- END .delete-action -->
								<?php endif; ?>

							<?php endif; ?>
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( '创建菜单' ) : __( '保存菜单' ), 'primary large menu-save', 'save_menu', false, array( 'id' => 'save_menu_footer' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#nav-menu-footer -->
				</div><!-- /.menu-edit -->
			</form><!-- /#update-nav-menu -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
	<?php endif; ?>
</div><!-- /.wrap-->
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
