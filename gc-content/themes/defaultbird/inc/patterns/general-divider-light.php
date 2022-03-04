<?php
/**
 * Divider with image and color (light) block pattern
 */
return array(
	'title'      => __( '带图像和颜色的分隔线（亮色）', 'defaultbird' ),
	'categories' => array( 'featured' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"1rem","right":"0px","bottom":"1rem","left":"0px"}}},"backgroundColor":"secondary"} -->
					<div class="gc-block-group alignfull has-secondary-background-color has-background" style="padding-top:1rem;padding-right:0px;padding-bottom:1rem;padding-left:0px"><!-- gc:image {"id":473,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/divider-black.png" alt="" class="gc-image-473"/></figure>
					<!-- /gc:image --></div>
					<!-- /gc:group -->',
);
