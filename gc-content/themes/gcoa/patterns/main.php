<?php
/**
 * Title: 主框架
 * Slug: gcoa/main
 * Categories: featured
 * Description: 主框架
 */
$gcoa_user_id = get_current_user_id();
$gcoa_gc_roles = new GC_Roles();
$gcoa_current_user = get_userdata($gcoa_user_id);

//用户角色名称
$gcoa_role_name = translate_user_role( $gcoa_gc_roles->roles[ array_shift($gcoa_current_user->roles) ]['name'] );

// 顶部个人头像弹出的菜单
function get_main_header_user_menu() {

    $locations = get_nav_menu_locations();
    if(Empty($locations)){
        return;
    }
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

?>
<div class="app">
	<div class="layout">
		<div class="header">
		    <div class="logo logo-dark">
		        <a href="/">
		            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo.png" alt="Logo">
		            <img class="logo-fold" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo-fold.png" alt="Logo">
		        </a>
		    </div>
		    <div class="logo logo-white">
		        <a href="/">
		            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo-white.png" alt="Logo">
		            <img class="logo-fold" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo-fold-white.png" alt="Logo">
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
		                    <div class="avatar avatar-lg avatar-image bg-primary  m-h-10 m-r-15">
		                        <?php 
		                        	echo get_avatar( $gcoa_current_user->ID, 48); 
		                        ?>
		                        <i class="anticon anticon-user"></i>
		                    </div>
		                </div>
		                <div class="p-b-15 p-t-20 dropdown-menu pop-profile">
		                    <div class="p-h-20 p-b-15 m-b-10 border-bottom">
		                        <div class="d-flex m-r-50">
		                            <div class="avatar avatar-lg avatar-image bg-primary">

		                                <?php
		                                 	echo get_avatar( $gcoa_current_user->ID, 48); 
		                                ?>
		                                <i class="anticon anticon-user"></i>
		                            </div>
		                            <div class="m-l-10">
		                                <p class="m-b-0 text-dark font-weight-semibold"><?php echo $gcoa_current_user->display_name; ?></p>
		                                <p class="m-b-0 opacity-07"><?php echo $gcoa_role_name; ?></p>
		                            </div>
		                        </div>
		                    </div>
		                    <?php if( current_user_can('level_2')) : // 是否具有登录后台的权限 ?>
	                    	<a href="/gc-admin/" class="dropdown-item d-block p-h-15 p-v-10">
				                <div class="d-flex align-items-center justify-content-between">
				                    <div>
				                        <i class="anticon opacity-04 font-size-16 anticon-tool"></i>
				                        <span class="m-l-10">超级管理后台</span>
				                    </div>
				                    <i class="anticon font-size-10 anticon-right"></i>
				                </div>
				            </a>
		                    <?php endif;?>
		                    <?php echo  get_main_header_user_menu(); ?>
		                    <a href="<?php echo gc_logout_url(); ?>" class="dropdown-item d-block p-h-15 p-v-10">
		                        <div class="d-flex align-items-center justify-content-between">
		                            <div class="gc-block-group">
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
		<div class="side-nav">
		    <div class="side-nav-inner">
		        <!-- gc:navigation /-->
		    </div>
		</div>
        <div class="page-container">
        	<div class="main-content">
        			##TEMPLATE_INNER_HTML##
        	</div>
			<footer class="gc-block-group footer">
				<div class="gc-block-group footer-content">
					<p class="m-b-0">©2022 - 格尺科技 提供技术支持</p>
					<span>
					    <a href="#" class="text-gray m-r-15">服务条款</a>
						<a href="<?php echo esc_url( __( 'https://www.gechiui.com', 'gcoa' ) ); ?>" rel="nofollow">GeChiUI</a>
					</span>
				</div>
			</footer>
		</div>
	</div>
</div>

