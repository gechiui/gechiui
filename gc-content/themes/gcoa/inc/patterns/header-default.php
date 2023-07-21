<?php
global $gc_roles;
/**
 * Default header block pattern
 */
$user_id      = get_current_user_id();
$current_user = gc_get_current_user();

//用户角色名称
$role_name = translate_user_role( $gc_roles->roles[ array_shift($current_user->roles) ]['name'] );

// 编辑个人资料的URL地址
if ( current_user_can( 'read' ) ) {
		$profile_url = get_edit_profile_url( $user_id );
	} elseif ( is_multisite() ) {
		$profile_url = get_dashboard_url( $user_id, 'profile.php' );
	} else {
		$profile_url = false;
	}

return array(
	'title'      => __( '默认页眉', 'gcoa' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '
<div class="header">
    <div class="logo logo-dark">
        <a href="index.html">
            <img src="'.get_template_directory_uri().'/assets/images/logo/logo.png" alt="Logo">
            <img class="logo-fold" src="'.get_template_directory_uri().'/assets/images/logo/logo-fold.png" alt="Logo">
        </a>
    </div>
    <div class="logo logo-white">
        <a href="index.html">
            <img src="'.get_template_directory_uri().'/assets/images/logo/logo-white.png" alt="Logo">
            <img class="logo-fold" src="'.get_template_directory_uri().'/assets/images/logo/logo-fold-white.png" alt="Logo">
        </a>
    </div>
    <div class="nav-wrap">
        <ul class="nav-left">
            <li class="desktop-toggle">
                <a href="javascript:void(0);">
                    <i class="anticon"></i>
                </a>
            </li>
            <li class="mobile-toggle">
                <a href="javascript:void(0);">
                    <i class="anticon"></i>
                </a>
            </li>
        </ul>
        <ul class="nav-right">
            <li>
                <form role="search" method="get" class="search-form" action="/">
                    <input type="search" class="form-control" placeholder="搜索 …" name="s">
                </form>
            </li>
            <li class="dropdown dropdown-animated scale-left">
                <div class="pointer" data-toggle="dropdown">
                    <div class="avatar avatar-image  m-h-10 m-r-15">
                        '. get_avatar( $current_user->user_email, 32) .'
                    </div>
                </div>
                <div class="p-b-15 p-t-20 dropdown-menu pop-profile">
                    <div class="p-h-20 p-b-15 m-b-10 border-bottom">
                        <div class="d-flex m-r-50">
                            <div class="avatar avatar-lg avatar-image">
                                '. get_avatar( $current_user->user_email, 32) .'
                            </div>
                            <div class="m-l-10">
                                <p class="m-b-0 text-dark font-weight-semibold">' . $current_user->display_name . '</p>
                                <p class="m-b-0 opacity-07">'. $role_name .'</p>
                            </div>
                        </div>
                    </div>
                    '. get_admin_menu() .'
                    '. get_header_user_menu() .'
                    <a href="'. gc_logout_url() .'" class="dropdown-item d-block p-h-15 p-v-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="anticon opacity-04 font-size-16 anticon-logout"></i>
                                <span class="m-l-10">退出</span>
                            </div>
                            <i class="anticon font-size-10 anticon-right"></i>
                        </div>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
',
);

function get_admin_menu(){
    if(! current_user_can('level_2')){
        return '';
    }
    return '<a href="/gc-admin/" class="dropdown-item d-block p-h-15 p-v-10">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="anticon opacity-04 font-size-16 anticon-tool"></i>
                        <span class="m-l-10">超级管理后台</span>
                    </div>
                    <i class="anticon font-size-10 anticon-right"></i>
                </div>
            </a>';
}

// 顶部个人头像弹出的菜单
function get_header_user_menu() {

    $locations = get_nav_menu_locations();
    $menus = gc_get_nav_menu_object($locations['header-user-menu']); // 获取菜单组基本信息
    if(!$menus) {
        return;
    }
    $menu_items = gc_get_nav_menu_items( $menus->term_id, array( 'update_post_term_cache' => false ) );


    // _gc_menu_item_classes_by_context( $menu_items );

    $nav_menu = '';
    foreach ( (array) $menu_items as $menu_item ) {
        $menu_item->url = empty( $menu_item->url ) ?  'javascript:void(0);' : $menu_item->url;
        // 只处理顶级循环
        if ( $menu_item->menu_item_parent == 0 ) {
            $nav_menu .= '
                <a href="'. $menu_item->url .'" class="dropdown-item d-block p-h-15 p-v-10">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="anticon opacity-04 font-size-16 '. implode(" ",$menu_item->classes) .'"></i>
                            <span class="m-l-10">'. $menu_item->title .'</span>
                        </div>
                        <i class="anticon font-size-10 anticon-right"></i>
                    </div>
                </a>';
        }
    }

    return $nav_menu;
}
