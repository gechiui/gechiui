<?php

/**
 * gongenlin
 *
 * @package GeChiUI
 */

// 给CSS或JS文件取个名字，建立一样的规则，可以防止重复加载
function get_customize_handle( $src, $suffix) {
	return 'gcoa-'.basename($src, $suffix);
}

// 添加自定义CSS文件
function register_custom_style( $href ) {
	$theme_version = gc_get_theme()->get( 'Version' );
	$version_string = is_string( $theme_version ) ? $theme_version : false;
	$handle = 'gcoa-css-custom-'.basename($href, ".css");
	gc_register_style(
		$handle,
		gcoa_get_assets_uri($href), 
		array(),
		$version_string,
		true
	);
	gc_enqueue_style( $handle );
	return true;
}

// 添加自定义JS文件
function register_custom_script( $src ) {
	$theme_version = gc_get_theme()->get( 'Version' );
	$version_string = is_string( $theme_version ) ? $theme_version : false;
	$handle = 'gcoa-js-custom-'.basename($src, ".js");
	gc_register_script(
		$handle,
		gcoa_get_assets_uri($customize_js) , 
		array(),
		$version_string,
		true
	);
	gc_enqueue_script( $handle );
	return true;
}

// 删除原有的样式
function remove_gc_block_template_part_inline_css(){
	gc_dequeue_style( 'gc-block-template-part' );
	gc_dequeue_style( 'gc-block-library' );
	gc_dequeue_style( 'global-styles' );
}
add_action( 'gc_enqueue_scripts', 'remove_gc_block_template_part_inline_css', 70 );

// 格式化获取资源路径
function gcoa_get_assets_uri($src){
	if( '/gc-content' === substr( $src, 0, 11 ) ){
		$src = home_url() . $src;
	}elseif( '/assets' === substr( $src, 0, 7 ) ){
		$src = get_template_directory_uri() . $src;
	}
	return $src;
}

// 页头加载的CSS和JS文件
if ( ! function_exists( 'gcoa_styles_top' ) ) :
	function gcoa_styles_top() {
		$theme_version = gc_get_theme()->get( 'Version' );
		$version_string = is_string( $theme_version ) ? $theme_version : false;
		// 加载JS到底部
		$src = get_template_directory_uri() . '/assets/js/vendors.min.js';
		$handle = get_customize_handle($src, ".js");
		gc_register_script(
			$handle,
			$src,
			array(),
			$version_string,
			true
		);
		gc_enqueue_script( $handle );


	}
endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_top', 80 );

// 加载自定义字段的CSS和JS文件
// 在区块编辑器中，可以自定义页面添加CSS和JS文件（及脚本代码）
// 添加方式如 /assets/css/*.min.css 或 /gc-content/themes/gcoa/assets/css/*.min.css
if ( ! function_exists( 'gcoa_styles_custom' ) ) :
	function gcoa_styles_custom() {
		global $post, $croe_html_custom_css, $croe_html_custom_js;

		if(!$post){
			return;
		}
		$theme_version = gc_get_theme()->get( 'Version' );
		$version_string = is_string( $theme_version ) ? $theme_version : false;

		// 加载自定义script语句
		$customize_script = get_post_meta($post->ID,"customize_script", $single = true);
		gc_add_inline_script(
			'gcoa-js-vendors',
			$customize_script
		);

		// 加载外部CSS文件
		$customizes_css = get_post_meta($post->ID,"customize_css", $single = false);
		$customizes_css = array_merge((array)$customizes_css, (array)$croe_html_custom_css);
		if(count($customizes_css)>0) {
			foreach ($customizes_css as $customize_css) {
				// 加载css到header
				$handle = get_customize_handle($customize_css, ".css");
				gc_register_style(
					$handle,
					gcoa_get_assets_uri($customize_css), 
					array(),
					$version_string
				);
				gc_enqueue_style( $handle );
			}
		}

		// 加载外部JS文件
		$customizes_js = get_post_meta($post->ID,"customize_js", $single = false);
		$customizes_js = array_merge((array)$customizes_js, (array)$croe_html_custom_js);
		if(count($customizes_js)>0) {
			foreach ($customizes_js as $customize_js) {
				// 加载JS到底部
				$handle = get_customize_handle($customize_js, ".js");
				gc_register_script(
					$handle,
					gcoa_get_assets_uri($customize_js) , 
					array(),
					$version_string,
					true
				);
				gc_enqueue_script( $handle );
			}
		}
	}

endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_custom',90 );


// 页尾加载的CSS和JS文件
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
		// gc_register_style(
		// 	'gcoa-style',
		// 	get_template_directory_uri() . '/style.css',
		// 	array(),
		// 	$version_string
		// );

		// Enqueue theme stylesheet.
		// gc_enqueue_style( 'gcoa-style' );


	    // 加载APP样式到顶部
	    $src = get_template_directory_uri() . '/assets/css/app.css';
	    $handle = get_customize_handle($src, ".css");
		gc_register_style(
			$handle,
			$src,
			array(),
			$version_string
		);
		gc_enqueue_style( $handle );
		
		// 加载JS到底部
		$src = get_template_directory_uri() . '/assets/js/app.min.js';
	    $handle = get_customize_handle($src, ".js");
		gc_register_script(
			$handle,
			$src,
			array(),
			$version_string,
			true
		);
		gc_enqueue_script( $handle );

	}

endif;

add_action( 'gc_enqueue_scripts', 'gcoa_styles_bottom', 10000 );