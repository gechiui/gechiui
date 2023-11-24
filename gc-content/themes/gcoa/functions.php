<?php

show_admin_bar(false); # 禁止在浏览站点时显示工具栏

// CSS样式 与 JS脚本
require get_template_directory() . '/inc/script-loader.php';
// 区块管理
require get_template_directory() . '/inc/block-filters.php';

// 修改登录跳转地址
 function my_login_redirect($redirect_to, $request){
    if( empty( $redirect_to ) || $redirect_to == 'gc-admin/' || $redirect_to == admin_url() )
    return home_url('');
    else
    return $redirect_to;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3);

// 前端用户身份验证验证规则
function require_login(){

	// 判断当前自定义模板是不是“空白”，而不用验证登录
	if ( get_page_template_slug() =='blank' ){
		return;
	}
 
	// Require login for site
	gc_get_current_user();
	global $user_ID;
	if ($user_ID == '') {
	    header('Location: /gc-login.php'); 
	    exit();
	}
}
add_action( 'gc_enqueue_scripts', 'require_login' );

// 注册导航块名称
function navigation_left() {
 
    $locations = array(
        'nav-left'  => '左导航',
        'header-user-menu' => '顶部用户菜单',
    );
 
    register_nav_menus( $locations );
}
add_action( 'init', 'navigation_left' );

/**
 *  添加区块编辑器->样板的自定义分类
 * 
 */
function register_core_block_gcoa_patterns_and_categories() {
	register_block_pattern_category(
		'dashboard',
		array(
			'label'       => _x( '仪表盘', 'Block pattern category' ),
			'description' => __( '系统首页、指挥舱类型页面等' ),
		)
	);
	register_block_pattern_category(
		'ui-elements',
		array(
			'label'       => _x( 'UI元素', 'Block pattern category' ),
			'description' => __( '按钮、图标、卡片、列表、排版等' ),
		)
	);
	register_block_pattern_category(
		'components',
		array(
			'label'       => _x( '控件', 'Block pattern category' ),
			'description' => __( '折叠菜单、下拉菜单、轮播图、模态框等' ),
		)
	);
	register_block_pattern_category(
		'forms',
		array(
			'label'       => _x( '表单', 'Block pattern category' ),
			'description' => __( '文本框、下拉框等表单元素，及表单布局和验证等' ),
		)
	);
	register_block_pattern_category(
		'tables',
		array(
			'label'       => _x( '表格', 'Block pattern category' ),
			'description' => __( '基础表格与JS表格' ),
		)
	);
	register_block_pattern_category(
		'charts',
		array(
			'label'       => _x( '图表', 'Block pattern category' ),
			'description' => __( '数据图表与JS图表' ),
		)
	);
	register_block_pattern_category(
		'pages',
		array(
			'label'       => _x( '页面', 'Block pattern category' ),
			'description' => __( '页面使用场景案例' ),
		)
	);
}
add_action( 'init', 'register_core_block_gcoa_patterns_and_categories' );




