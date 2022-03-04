<?php
/**
 * Layered images with duotone block pattern
 */
return array(
	'title'      => __( '双色分层图像', 'defaultbird' ),
	'categories' => array( 'featured', 'gallery' ),
	'content'    => '<!-- gc:cover {"url":"' . esc_url( get_template_directory_uri() ) . '/assets/images/ducks.jpg","dimRatio":0,"minHeight":666,"contentPosition":"center center","isDark":false,"align":"wide","style":{"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}},"color":{"duotone":["#000000","#FFFFFF"]}}} -->
					<div class="gc-block-cover alignwide is-light" style="padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em;min-height:666px"><span aria-hidden="true" class="has-background-dim-0 gc-block-cover__gradient-background has-background-dim"></span><img class="gc-block-cover__image-background" alt="' . esc_attr__( '画水中的鸭子。', 'defaultbird' ) . '" src="' . esc_url( get_template_directory_uri() ) . '/assets/images/ducks.jpg" data-object-fit="cover"/><div class="gc-block-cover__inner-container"><!-- gc:image {"align":"center","width":384,"height":580,"sizeSlug":"large"} -->
					<div class="gc-block-image"><figure class="aligncenter size-large is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-salmon.jpg" alt="' . esc_attr__( '一只飞鸟的插图。', 'defaultbird' ) . '" width="384" height="580"/></figure></div>
					<!-- /gc:image --></div></div>
					<!-- /gc:cover -->',
);
