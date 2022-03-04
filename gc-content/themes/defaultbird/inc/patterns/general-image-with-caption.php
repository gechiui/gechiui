<?php
/**
 * Image with caption block pattern
 */
return array(
	'title'      => __( '图片和说明', 'defaultbird' ),
	'categories' => array( 'featured', 'columns', 'gallery' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:6rem;padding-bottom:6rem"><!-- gc:media-text {"mediaId":202,"mediaLink":"' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-gray.jpg","mediaType":"image","verticalAlignment":"bottom","imageFill":false} -->
					<div class="gc-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-bottom"><figure class="gc-block-media-text__media"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-gray.jpg" alt="' . esc_attr__( '蜂鸟图', 'defaultbird' ) . '" class="gc-image-202 size-full"/></figure><div class="gc-block-media-text__content"><!-- gc:paragraph -->
					<p><strong>' . esc_html__( '蜂鸟', 'defaultbird' ) . '</strong></p>
					<!-- /gc:paragraph -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '这是一只美丽的鸟，羽毛颜色惊人。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div></div>
					<!-- /gc:media-text --></div>
					<!-- /gc:group -->',
);
