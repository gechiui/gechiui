<?php
/**
 * Server-side rendering of the `core/navigation` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/navigation` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block_gcoa_core_navigation( $attributes, $content, $block ) {

	return get_navigation();
}

/**
 * Registers the `core/navigation` block on the server.
 */
function register_block_gcoa_core_navigation() {
	
	// 删除原有的区块
	unregister_block_type('core/navigation');
	// 注册新的
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/navigation',
		array(
			'render_callback' => 'render_block_gcoa_core_navigation',
		)
	);
}
add_action( 'init', 'register_block_gcoa_core_navigation' );

function is_active($menu_item){
	global $post;

	// 当前POST的URL对比
	$active = $menu_item->url == get_permalink($post->ID);

	// 当前分类（或POST的上级分类）对比，应对列表
	if(! $active){
		$category = get_the_category();
		$active = $menu_item->url == get_category_link($category[0]->cat_ID);
	}
	// URL父级匹配
	// page页面的父子关系
	if(! $active){
		$parents = get_post_ancestors($post->ID);
		$id = ($parents) ? $parents[count($parents)-1]: $post->ID;
		$active = $menu_item->url == get_permalink($id);
	}

	return $active;
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
	$menu_item_parents = array_column($menu_items, 'menu_item_parent');
	foreach ( (array) $menu_items as $menu_item ) {
		$arrow = '';
		if(array_search($menu_item->ID,$menu_item_parents)) {
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
 * 
 * $menu_items 菜单数据集
 * $menu_item_parent 父级菜单
 */
function get_navigation_children($menu_items, $menu_item_parent) {
	$nav_menu = '<ul class="dropdown-menu">';

	
	
	$menu_item_parents = array_column($menu_items, 'menu_item_parent');
	foreach ( (array) $menu_items as $menu_item ) {
		$arrow = ''; // 导航尾部显示的下拉三角形图标
		$children_html = ''; // 菜单子集的HTML
		// 判断父级ID
		if( $menu_item_parent == $menu_item->menu_item_parent ) {
			// 判断是否有子集数据
			if(array_search($menu_item->ID, $menu_item_parents)) { 
				$arrow = '<span class="arrow"><i class="arrow-icon"></i></span>';
				$menu_item->url = empty( $menu_item->url ) ?  'javascript:void(0);' : $menu_item->url;
				$children_html = get_navigation_children($menu_items, $menu_item->ID);
				
			}
			$class = is_active($menu_item) ? 'class="active"' : '';
			$class = $children_html == '' ? $class : 'class="nav-item dropdown"';
			$nav_menu .='<li '. $class .'><a href="'. $menu_item->url .'">'. $menu_item->title . $arrow .'</a>'. $children_html .'</li>';
		}
		unset($arrow);
		unset($children_html);
	}

	$nav_menu .= '</ul>';

	return $nav_menu;
}
