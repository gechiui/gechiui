<?php

// 处理core/html块中的手工引用CSS与JS，这有利于区块编辑器的快速预览页面效果
// 虽然不处理，也不会有什么影响，但是处理后，前端执行效果会更好点
function bizboost_block_wrapper( $block_content, $block ) {
	global $croe_html_custom_css, $croe_html_custom_js;
	if ( 'core/html' === $block['blockName'] ) {
		$regex = '#<link([^<>]+)/?>#iU';
		if ( preg_match_all( $regex, $block_content, $links ) ) {
			foreach ( $links[1] as $link ) {
				$atts = shortcode_parse_atts( $link );
				$croe_html_custom_css[] = $atts['href'];

			}
			$block_content = preg_replace_callback( $regex, '_filter_preg_replace_callback', $block_content );
		}
		
		$regex = '#<script([^<>]+)/?></script>#iU';
		if ( preg_match_all( $regex, $block_content, $links ) ) {
			foreach ( $links[1] as $link ) {
				$atts = shortcode_parse_atts( $link );
				$croe_html_custom_js[] = $atts['src'];
			}
			$block_content = preg_replace_callback( $regex, '_filter_preg_replace_callback', $block_content );
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'bizboost_block_wrapper', 10, 2 );

// 返回空字符串的方法
function _filter_preg_replace_callback( $text ) {
	return '';
}

// 关闭区块编辑器的一些代码处理功能
remove_filter (  'the_content' ,  'gcautop'  ); # 关闭自动添加<p>  gcautop 将连续两个换行符替换为p
remove_filter (  'the_excerpt' ,  'gcautop'  ); # 关闭自动添加<br>
remove_filter (  'comment_text' ,  'gcautop'  );
remove_filter (  'widget_text_content' ,  'gcautop'  );
remove_filter (  'the_excerpt_embed' ,  'gcautop'  );
remove_filter( 'render_block', 'gc_render_layout_support_flag' ); # 关闭自动添加样式

/**
 * gongenlin
 * 用于重写区块编辑器的部分核心块。
 *
 * @package GeChiUI
 */
require get_template_directory() . '/inc/blocks/avatar.php';
require get_template_directory() . '/inc/blocks/comment-edit-link.php';
require get_template_directory() . '/inc/blocks/comment-reply-link.php';
require get_template_directory() . '/inc/blocks/navigation.php';
require get_template_directory() . '/inc/blocks/query-pagination-numbers.php';
require get_template_directory() . '/inc/blocks/post-comments-form.php';

/**
 * gongenlin
 * 添加简码
 */
require get_template_directory() . '/inc/shortcodes/gcforms-entries.php';
require get_template_directory() . '/inc/shortcodes/profile.php';
require get_template_directory() . '/inc/shortcodes/users.php';


/**
 * 设置主题样式支持区块编辑器。在区块编辑器中点击代码段的预览，可以查看效果。
 */
function tinymce_editor_style() {
    add_theme_support('editor-styles');
	add_editor_style( 'assets/css/app.css' );
	add_editor_style( 'assets/css/editor-app.css' );
}
add_action( 'after_setup_theme', 'tinymce_editor_style' );

function enqueue_custom_admin_style(){
	// $css_enqueues = array(
	// 	'gcoa-bootstrap' 	=> 'assets/css/editor-bootstrap.min.css',
	// 	'gcpa-app' 		=> 'assets/css/app.css' ,
	// 	'gcpa-select2' 	=> 'assets/css/select2.min.css'
	// );
	gc_enqueue_style( 'gcoa-app', get_stylesheet_directory_uri() .'/assets/css/app.css', array(), filemtime( get_template_directory() .'/assets/css/app.css' ) );
}

// add_action( 'gc_default_styles', 'enqueue_custom_admin_style' );

/**
 * 区块过滤
 *
 * 将区块进行代码过滤，增加或删除部分功能
 */

function gcoa_block_wrapper( $block_content, $block ) {

	if ( 'core/group' === $block['blockName'] ) {
		
		$block_content = str_replace( 'gc-block-group', '', $block_content );
			return $block_content;
	}

	if ( 'core/separator' === $block['blockName'] ) {
		
		$block_content = str_replace( 'gc-block-separator', '', $block_content );
			return $block_content;
	}

    return $block_content;
}
	
add_filter( 'render_block', 'gcoa_block_wrapper', 10, 2 );

/**
 * gongenlin
 * 执行位置 /gc-includes/template.php
 * 读取到模板数据后，对不同类型模板进行分别加工
 */
function gcoa_block_main( $template, $type, $templates ) {

	// 页面中有很多类型模板，默认blank不加载主框架，其它的都需要加载主框架
	if($templates[0] == 'blank'){
		return $template;
	}
	global $_gc_current_template_content;

	// 获取主框架内容
	$attributes = array( 'slug' => 'gcoa/main' );

	$main_content = render_block_core_pattern($attributes);

	$_gc_current_template_content = str_replace('##TEMPLATE_INNER_HTML##', $_gc_current_template_content, $main_content);
	return $template;
}

// 注意：前端调用类型的方式是
// add_filter( "{$type}_template", $template, $type, $templates );
add_filter( 'page_template', 'gcoa_block_main', 10, 3 );
add_filter( 'single_template', 'gcoa_block_main', 10, 3 );
add_filter( 'archive_template', 'gcoa_block_main', 10, 3 );
add_filter( 'home_template', 'gcoa_block_main', 10, 3 );
add_filter( 'index_template', 'gcoa_block_main', 10, 3 );
add_filter( 'search_template', 'gcoa_block_main', 10, 3 );
