<?php
	
show_admin_bar(false); # 禁止在浏览站点时显示工具栏

remove_filter (  'the_content' ,  'gcautop'  ); # 关闭自动添加<p>  gcautop 将连续两个换行符替换为p
remove_filter (  'the_excerpt' ,  'gcautop'  ); # 关闭自动添加<br>
remove_filter (  'comment_text' ,  'gcautop'  );
remove_filter (  'widget_text_content' ,  'gcautop'  );
remove_filter (  'the_excerpt_embed' ,  'gcautop'  );
remove_filter( 'render_block', 'gc_render_layout_support_flag' ); # 关闭自动添加样式

// 删除原有的样式
function remove_gc_block_template_part_inline_css(){
	gc_dequeue_style( 'gc-block-template-part' );
}
add_action( 'gc_enqueue_scripts', 'remove_gc_block_template_part_inline_css' );

function remove_gc_block_library_inline_css(){
	gc_dequeue_style( 'gc-block-library' );
}
add_action( 'gc_enqueue_scripts', 'remove_gc_block_library_inline_css' );

function remove_global_styles_inline_css(){
	gc_dequeue_style( 'global-styles' );
}
add_action( 'gc_enqueue_scripts', 'remove_global_styles_inline_css' );

// 加载自定义的block
require get_template_directory() .  '/blocks/index.php';

// 修改登录跳转地址
 function my_login_redirect($redirect_to, $request){
    if( empty( $redirect_to ) || $redirect_to == 'gc-admin/' || $redirect_to == admin_url() )
    return home_url('');
    else
    return $redirect_to;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3);

// 登录验证
function require_login(){

	// 判断当前自定义模板是不是“空白”，而不用验证登录
	if ( get_page_template_slug() =='blank' ){
		return;
	}
 
	// Require login for site
	get_currentuserinfo();
	global $user_ID;
	if ($user_ID == '') {
	    header('Location: /gc-login.php'); 
	    exit();
	}
}

add_action( 'gc_enqueue_scripts', 'require_login' );

if ( ! function_exists( 'gcoa_support' ) ) :

	/**
	 * 设置主题默认值并注册对各种GeChiUI功能的支持。
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function gcoa_support() {

		// 添加对区块样式的支持
		add_theme_support( 'gc-block-styles' );

		// 加入编辑器样式
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'gcoa_support' );

// 优先加载的CSS和JS文件
if ( ! function_exists( 'gcoa_styles_top' ) ) :
	function gcoa_styles_top() {
		// 加载JS到底部
		gc_register_script(
			'gcoa-js-vendors',
			get_template_directory_uri() . '/assets/js/vendors.min.js',
			array(),
			$version_string,
			true
		);
		gc_enqueue_script( 'gcoa-js-vendors' );


	}
endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_top' );

// 加载自定义字段的CSS和JS文件
if ( ! function_exists( 'gcoa_styles_custom' ) ) :
	function gcoa_styles_custom() {
		global $post;

		// 加载自定义script语句
		$customize_script = get_post_meta($post->ID,"customize_script", $single = true);
		gc_add_inline_script(
			'gcoa-js-vendors',
			$customize_script
		);

		// 加载外部CSS文件
		$customizes_css = get_post_meta($post->ID,"customize_css", $single = false);

		foreach ($customizes_css as $customize_css) {
			// 加载css到header
			gc_register_style(
				'gcoa-css-custom-'.basename($customize_css, ".css"),
				gcoa_get_assets_uri($customize_css), 
				array(),
				$version_string
			);
			gc_enqueue_style( 'gcoa-css-custom-'.basename($customize_css, ".css") );

		}

		// 加载外部JS文件
		$customizes_js = get_post_meta($post->ID,"customize_js", $single = false);

		foreach ($customizes_js as $customize_js) {
			// 加载JS到底部
			gc_register_script(
				'gcoa-js-custom-'.basename($customize_js, ".js"),
				gcoa_get_assets_uri($customize_js) , 
				array(),
				$version_string,
				true
			);
			gc_enqueue_script( 'gcoa-js-custom-'.basename($customize_js, ".js") );

		}

	}

endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_custom' );


// 最后加载的CSS和JS文件
if ( ! function_exists( 'gcoa_styles_bottom' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function gcoa_styles_bottom() {
		// 注册主题样式表
		$theme_version = gc_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		gc_register_style(
			'gcoa-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		gc_enqueue_style( 'gcoa-style' );


	    // 加载APP样式到顶部
		gc_register_style(
			'gcoa-style-app',
			get_template_directory_uri() . '/assets/css/app.css',
			array(),
			$version_string
		);
		gc_enqueue_style( 'gcoa-style-app' );
		
		// 加载JS到底部
		gc_register_script(
			'gcoa-js-app',
			get_template_directory_uri() . '/assets/js/app.min.js',
			array(),
			$version_string,
			true
		);
		gc_enqueue_script( 'gcoa-js-app' );

	}

endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_bottom', 100 );


if ( ! function_exists( 'navigation_left' ) ) :

function navigation_left() {
 
    $locations = array(
        'nav-left'  => '左导航',
        'header-user-menu' => '顶部用户菜单',
    );
 
    register_nav_menus( $locations );
}
endif;
 
add_action( 'init', 'navigation_left' );


// 添加区块样板
require get_template_directory() . '/inc/block-patterns.php';
// 添加简码
require get_template_directory() . '/inc/block-shortcodes.php';

function gcoa_get_assets_uri($src){
	if( '/gc-content' === substr( $src, 0, 11 ) ){
		$src = home_url() . $src;
	}elseif( '/assets' === substr( $src, 0, 7 ) ){
		$src = get_template_directory_uri() . $src;
	}
	return $src;

}
