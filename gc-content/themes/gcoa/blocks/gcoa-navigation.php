<?php
/**
 * Server-side rendering of the `core/gcoa-navigation` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/gcoa-navigation` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block_core_gcoa_navigation( $attributes, $content, $block ) {

	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];
	return get_navigation();
}

/**
 * Registers the `core/gcoa-navigation` block on the server.
 */
function register_block_core_gcoa_navigation() {
	register_block_type_from_metadata(
		__DIR__ . '/gcoa-navigation',
		array(
			'render_callback' => 'render_block_core_gcoa_navigation',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_navigation' );

function is_active($menu_item){
	global $post;
	// echo 'AA'.json_encode($post);
	// if( is_multisite() ){
	// 	return false;
	// }
	// URL完全相同
	return $menu_item->url == get_permalink($post->ID);

	// 文章的分类相同
}

function get_navigation() {

	$locations = get_nav_menu_locations();
    $menus = gc_get_nav_menu_object($locations['nav-left']); // 获取菜单组基本信息
    if(!$menus) {
        return;
    }
    $menu_items = gc_get_nav_menu_items( $menus->term_id, array( 'update_post_term_cache' => false ) );

	// _gc_menu_item_classes_by_context( $menu_items );

	$nav_menu = '';
	foreach ( (array) $menu_items as $menu_item ) {
		$arrow = '';
		if(array_search($menu_item->ID, array_column($menu_items, 'menu_item_parent'))) {
			$arrow = '<span class="arrow"><i class="arrow-icon"></i></span>'; // 导航尾部显示的下拉三角形图标
			$menu_item->url = empty( $menu_item->url ) ?  'javascript:void(0);' : $menu_item->url;
		}
		// 先处理顶级循环
		if ( $menu_item->menu_item_parent == 0 ) {
			$nav_menu .= '<li class="nav-item dropdown"><a class="dropdown-toggle" href="'. $menu_item->url .'"><span class="icon-holder"><i class="anticon '. implode(" ",$menu_item->classes) .'"></i></span><span class="title">'. $menu_item->title .'</span>'. $arrow .'</a>';
			$nav_menu .= get_navigation_children( $menu_items, $menu_item->ID );
			$nav_menu .= '</li>';
		}
	}

	return '<ul class="side-nav-menu scrollable">'. $nav_menu .'<ul>';
}

/**
 * 迭代获取树形菜单的子集结构
 */
function get_navigation_children($menu_items, $menu_item_parent) {
	$nav_menu = '<ul class="dropdown-menu">';

	$arrow = ''; // 导航尾部显示的下拉三角形图标
	$children_html = ''; // 菜单子集的HTML

	foreach ( (array) $menu_items as $menu_item ) {
		if( $menu_item_parent == $menu_item->menu_item_parent ) {
			if(array_search($menu_item->ID, array_column($menu_items, 'menu_item_parent'))) { // 判断是否有子集数据
				$arrow = '<span class="arrow"><i class="arrow-icon"></i></span>';
				$menu_item->url = empty( $menu_item->url ) ?  'javascript:void(0);' : $menu_item->url;
				$children_html = get_navigation_children($menu_items, $menu_item->ID);
				
			}
			$class = is_active($menu_item) ? 'class="active"' : '';
			$class = $children_html == '' ? $class : 'class="nav-item dropdown"';
			$nav_menu .='<li '. $class .'><a href="'. $menu_item->url .'">'. $menu_item->title . $arrow .'</a>'. $children_html .'</li>';
		}
	}

	$nav_menu .= '</ul>';

	return $nav_menu;
}
