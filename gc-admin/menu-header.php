<?php
/**
 * Displays Administration Menu.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * The current page.
 *
 * @global string $self
 */
$self = preg_replace( '|^.*/gc-admin/network/|i', '', $_SERVER['PHP_SELF'] );
$self = preg_replace( '|^.*/gc-admin/|i', '', $self );
$self = preg_replace( '|^.*/plugins/|i', '', $self );
$self = preg_replace( '|^.*/mu-plugins/|i', '', $self );

/**
 * For when admin-header is included from within a function.
 *
 * @global array  $menu
 * @global array  $submenu
 * @global string $parent_file
 * @global string $submenu_file
 */
global $menu, $submenu, $parent_file, $submenu_file;

/**
 * Filters the parent file of an admin menu sub-menu item.
 *
 * Allows plugins to move sub-menu items around.
 *
 *
 * @param string $parent_file The parent file.
 */
$parent_file = apply_filters( 'parent_file', $parent_file );

/**
 * Filters the file of an admin menu sub-menu item.
 *
 *
 * @param string $submenu_file The submenu file.
 * @param string $parent_file  The submenu item's parent file.
 */
$submenu_file = apply_filters( 'submenu_file', $submenu_file, $parent_file );

get_admin_page_parent();

/**
 * Display menu.
 *
 * @access private
 *
 * @global string $self
 * @global string $parent_file
 * @global string $submenu_file
 * @global string $plugin_page
 * @global string $typenow
 *
 * @param array $menu
 * @param array $submenu
 * @param bool  $submenu_as_parent
 */
function _gc_menu_output( $menu, $submenu, $submenu_as_parent = true ) {
	global $self, $parent_file, $submenu_file, $plugin_page, $typenow;

	// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes, 5 = hookname, 6 = icon_url.
	foreach ( $menu as $key => $item ) {
		$admin_is_parent = false;
		$class='';
		$aria_attributes = '';
		$aria_hidden = '';

		$submenu_items = array();
		if ( ! empty( $submenu[ $item[2] ] ) ) {
			$submenu_items = $submenu[ $item[2] ];
		}

		if ( ! empty( $item[4] ) ) {
		}

		$id = ! empty( $item[5] ) ? ' id="' . preg_replace( '|[^a-zA-Z0-9_:.]|', '-', $item[5] ) . '"' : '';
		$img = '';
		$img_style = '';
		$img_class = '';

		/*
		 * If the string 'none' (previously 'div') is passed instead of a URL, don't output
		 * the default menu image so an icon can be added to div.gc-menu-image as background
		 * with CSS. Dashicons and base64-encoded data:image/svg_xml URIs are also handled
		 * as special cases.
		 */ 
		
        if ( ! empty( $item[6] ) ) {
            $img = '<img src="' . $item[6] . '" alt="" />';

			if ( 'none' === $item[6] || 'div' === $item[6] ) {
				$img = '';
            } elseif ( 0 === strpos( $item[6], 'data:image/svg+xml;base64,' ) ) {
                $img = '';
				$img_style = ' style="background-image:url(\'' . esc_attr( $item[6] ) . '\');"';
				$img_class = 'anticon anticon-svg';
			} elseif ( 0 === strpos( $item[6], 'anticon' ) ) {
                $img = '';
				$img_class = $item[6];
			} elseif ( 0 === strpos( $item[6], 'dashicons-' ) ) {
				$img       = '';
				$img_class = ' dashicons-before ' . sanitize_html_class( $item[6] );
			}
		}
		//$arrow = '<div class="gc-menu-arrow"><div></div></div>';
        $active =  empty($submenu_items) && $self === $item[2] ? 'active' : '';
		$title = gctexturize( $item[0] );

		// Hide separators from screen readers.

        echo "<li class='nav-item dropdown $active' $id>";

		if ( $submenu_as_parent && ! empty( $submenu_items ) ) {
			$submenu_items = array_values( $submenu_items );  // Re-index.
			$menu_hook = get_plugin_page_hook( $submenu_items[0][2], $item[2] );
			$menu_file = $submenu_items[0][2];
			$pos = strpos( $menu_file, '?' );

			if ( false !== $pos ) {
				$menu_file = substr( $menu_file, 0, $pos );
			}
			if ( ! empty( $menu_hook )
				|| ( ( 'index.php' !== $submenu_items[0][2] )
					&& file_exists( GC_PLUGIN_DIR . "/$menu_file" )
					&& ! file_exists( ABSPATH . "/gc-admin/$menu_file" ) )
			) {
				$admin_is_parent = true;
                //应用级+有子菜单，应用菜单添加
                echo "<a  class='dropdown-toggle' href='javascript:void(0);' ><span class='icon-holder'><i class='gc-menu-image $img_class' $img_style>$img</i></span>
                    <span class='title'>$title</span><span class='arrow'><i class='arrow-icon'></i></span></a>";
			} else {
                //系统级+有子菜单, 正常显示下拉效果
                echo "<a  class='dropdown-toggle' href='javascript:void(0);'><span class='icon-holder'><i class='gc-menu-image $img_class' $img_style>$img</i></span>
                    <span class='title'>$title</span><span class='arrow'><i class='arrow-icon'></i></span></a>";
			}
		} elseif ( ! empty( $item[2] ) && current_user_can( $item[1] ) ) {
			$menu_hook = get_plugin_page_hook( $item[2], 'admin.php' );
			$menu_file = $item[2];
			$pos = strpos( $menu_file, '?' );

			if ( false !== $pos ) {
				$menu_file = substr( $menu_file, 0, $pos );
			}
            

			if ( ! empty( $menu_hook )
				|| ( ( 'index.php' !== $item[2] )
					&& file_exists( GC_PLUGIN_DIR . "/$menu_file" )
					&& ! file_exists( ABSPATH . "/gc-admin/$menu_file" ) )
			) {
				$admin_is_parent = true;
                //应用级+无子菜单
                echo "<a  class='dropdown-toggle' href='{$item[2]}'><span class='icon-holder'><i class='gc-menu-image $img_class' $img_style>$img</i></span>
                    <span class='title'>{$item[0]}</span></a>"; 
			} else {
                //空占行的跳过
                if( empty($item[0]) ){
                    continue;
                }
                //系统级+无子菜单
                echo "<a  class='dropdown-toggle' href='{$item[2]}'><span class='icon-holder'><i class='gc-menu-image $img_class' $img_style>$img</i></span>
                    <span class='title'>{$item[0]}</span></a>";
			}
		}

		if ( ! empty( $submenu_items ) ) {
			echo "<ul class='dropdown-menu'>";

			// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes.
			foreach ( $submenu_items as $sub_key => $sub_item ) {
				if ( ! current_user_can( $sub_item[1] ) ) {
					continue;
				}

				$class = '';
				$aria_attributes = '';
                
				$menu_file = $item[2];
				$pos = strpos( $menu_file, '?' );

				if ( false !== $pos ) {
					$menu_file = substr( $menu_file, 0, $pos );
				}

				// Handle current for post_type=post|page|foo pages, which won't match $self.
				$self_type = ! empty( $typenow ) ? $self . '?post_type=' . $typenow : 'nothing';

				if ( isset( $submenu_file ) ) {
					if ( $submenu_file === $sub_item[2] ) {
						$class = 'class="active"'; //原值current
						$aria_attributes .= ' aria-current="page"';
					}
					// If plugin_page is set the parent must either match the current page or not physically exist.
					// This allows plugin pages with the same hook to exist under different parents.
				} elseif (
					( ! isset( $plugin_page ) && $self === $sub_item[2] )
					|| ( isset( $plugin_page ) && $plugin_page === $sub_item[2]
						&& ( $item[2] === $self_type || $item[2] === $self || file_exists( $menu_file ) === false ) )
				) {
					$class = 'class="active"';//原值current
					$aria_attributes .= ' aria-current="page"';
				}

				$menu_hook = get_plugin_page_hook( $sub_item[2], $item[2] );
				$sub_file = $sub_item[2];
				$pos = strpos( $sub_file, '?' );
				if ( false !== $pos ) {
					$sub_file = substr( $sub_file, 0, $pos );
				}

				$title = gctexturize( $sub_item[0] );

				if ( ! empty( $menu_hook )
					|| ( ( 'index.php' !== $sub_item[2] )
						&& file_exists( GC_PLUGIN_DIR . "/$sub_file" )
						&& ! file_exists( ABSPATH . "/gc-admin/$sub_file" ) )
				) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin directory.
					if ( ( ! $admin_is_parent && file_exists( GC_PLUGIN_DIR . "/$menu_file" ) && ! is_dir( GC_PLUGIN_DIR . "/{$item[2]}" ) ) || file_exists( $menu_file ) ) {
						$sub_item_url = add_query_arg( array( 'page' => $sub_item[2] ), $item[2] );
					} else {
						$sub_item_url = add_query_arg( array( 'page' => $sub_item[2] ), 'admin.php' );
					}

					$sub_item_url = esc_url( $sub_item_url );
                    //应用安装的菜单
					echo "<li $class ><a href='$sub_item_url'>$title</a></li>";
				} else {
                    //系统菜单
					echo "<li $class ><a href='{$sub_item[2]}'>$title</a></li>";
				}
			}
			echo '</ul>';
		}
		echo '</li>';
	}

}

/**
 * 顶导航的超级菜单
 *
 * @access private
 *
 * @global string $self
 * @global string $parent_file
 * @global string $submenu_file
 * @global string $plugin_page
 * @global string $typenow
 *
 * @param array $menu
 * @param array $submenu
 * @param bool  $submenu_as_parent
 */
function _gc_menu_gcadminbar( $submenu_as_parent = true ) {
	global $menu, $submenu, $self, $parent_file, $submenu_file, $plugin_page, $typenow;

	// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes, 5 = hookname, 6 = icon_url.
	foreach ( $menu as $key => $item ) {
        if( empty($item[0]) ){
            continue;
        }
		$admin_is_parent = false;
		$class='';
		$aria_attributes = '';
		$aria_hidden = '';

		$submenu_items = array();
		if ( ! empty( $submenu[ $item[2] ] ) ) {
			$submenu_items = $submenu[ $item[2] ];
		}

		if ( ! empty( $item[4] ) ) {
		}

		$id = ! empty( $item[5] ) ? ' id="' . preg_replace( '|[^a-zA-Z0-9_:.]|', '-', $item[5] ) . '"' : '';
		$img = '';
		$img_style = '';
		$img_class = '';

		/*
		 * If the string 'none' (previously 'div') is passed instead of a URL, don't output
		 * the default menu image so an icon can be added to div.gc-menu-image as background
		 * with CSS. Dashicons and base64-encoded data:image/svg_xml URIs are also handled
		 * as special cases.
		 */ 
		
        if ( ! empty( $item[6] ) ) {
            $img = '<img src="' . $item[6] . '" alt="" />';

			if ( 'none' === $item[6] || 'div' === $item[6] ) {
				$img = '';
            } elseif ( 0 === strpos( $item[6], 'data:image/svg+xml;base64,' ) ) {
                $img = '';
				$img_style = ' style="background-image:url(\'' . esc_attr( $item[6] ) . '\');"';
				$img_class = 'anticon anticon-svg';
			} elseif ( 0 === strpos( $item[6], 'anticon' ) ) {
                $img = '';
				$img_class = $item[6];
			} elseif ( 0 === strpos( $item[6], 'dashicons-' ) ) {
				$img       = '';
				$img_class = ' dashicons-before ' . sanitize_html_class( $item[6] );
			}
		}
		$title = gctexturize( $item[0] );

		// Hide separators from screen readers.

        echo "<ul class='nav-item' $id>";

		if ( $submenu_as_parent && ! empty( $submenu_items ) ) {
			$submenu_items = array_values( $submenu_items );  // Re-index.
			$menu_hook = get_plugin_page_hook( $submenu_items[0][2], $item[2] );
			$menu_file = $submenu_items[0][2];
			$pos = strpos( $menu_file, '?' );

			if ( false !== $pos ) {
				$menu_file = substr( $menu_file, 0, $pos );
			}
			echo "<li><h5>$title</h5></li>";
		} elseif ( ! empty( $item[2] ) && current_user_can( $item[1] ) ) {
			$menu_hook = get_plugin_page_hook( $item[2], 'admin.php' );
			$menu_file = $item[2];
			$pos = strpos( $menu_file, '?' );

			if ( false !== $pos ) {
				$menu_file = substr( $menu_file, 0, $pos );
			}
            

			echo "<li><a  href='{$item[2]}'><h5>$title</h5></a></li>";
		}

		if ( ! empty( $submenu_items ) ) {
			// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes.
			foreach ( $submenu_items as $sub_key => $sub_item ) {
				if ( ! current_user_can( $sub_item[1] ) ) {
					continue;
				}

				$class = '';
				$aria_attributes = '';
                
				$menu_file = $item[2];
				$pos = strpos( $menu_file, '?' );

				if ( false !== $pos ) {
					$menu_file = substr( $menu_file, 0, $pos );
				}

				// Handle current for post_type=post|page|foo pages, which won't match $self.
				$self_type = ! empty( $typenow ) ? $self . '?post_type=' . $typenow : 'nothing';

				if ( isset( $submenu_file ) ) {
					if ( $submenu_file === $sub_item[2] ) {
						$class = 'class="active"'; //原值current
						$aria_attributes .= ' aria-current="page"';
					}
					// If plugin_page is set the parent must either match the current page or not physically exist.
					// This allows plugin pages with the same hook to exist under different parents.
				} elseif (
					( ! isset( $plugin_page ) && $self === $sub_item[2] )
					|| ( isset( $plugin_page ) && $plugin_page === $sub_item[2]
						&& ( $item[2] === $self_type || $item[2] === $self || file_exists( $menu_file ) === false ) )
				) {
					$class = 'class="active"';//原值current
					$aria_attributes .= ' aria-current="page"';
				}

				$menu_hook = get_plugin_page_hook( $sub_item[2], $item[2] );
				$sub_file = $sub_item[2];
				$pos = strpos( $sub_file, '?' );
				if ( false !== $pos ) {
					$sub_file = substr( $sub_file, 0, $pos );
				}

				$title = gctexturize( $sub_item[0] );

				if ( ! empty( $menu_hook )
					|| ( ( 'index.php' !== $sub_item[2] )
						&& file_exists( GC_PLUGIN_DIR . "/$sub_file" )
						&& ! file_exists( ABSPATH . "/gc-admin/$sub_file" ) )
				) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin directory.
					if ( ( ! $admin_is_parent && file_exists( GC_PLUGIN_DIR . "/$menu_file" ) && ! is_dir( GC_PLUGIN_DIR . "/{$item[2]}" ) ) || file_exists( $menu_file ) ) {
						$sub_item_url = add_query_arg( array( 'page' => $sub_item[2] ), $item[2] );
					} else {
						$sub_item_url = add_query_arg( array( 'page' => $sub_item[2] ), 'admin.php' );
					}

					$sub_item_url = esc_url( $sub_item_url );
                    //应用安装的菜单
					echo "<li><a href='$sub_item_url'>$title</a></li>";
				} else {
                    //系统菜单
					echo "<li><a href='{$sub_item[2]}'>$title</a></li>";
				}
			}
		}
		echo '</ul>';
	}
}

?>

<!-- Side Nav START -->
<div id="adminmenumain" class="side-nav">
    <div id="adminmenuwrap" class="side-nav-inner">
        <ul id="adminmenu" class="side-nav-menu scrollable">
<?php

_gc_menu_output( $menu, $submenu );
/**
 * Fires after the admin menu has been output.
 *
 */
do_action( 'adminmenu' );

?>
        </ul>
    </div>
</div>
<!-- Side Nav END -->
