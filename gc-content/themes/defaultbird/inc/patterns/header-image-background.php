<?php
/**
 * Header with image background block pattern
 */
return array(
	'title'      => __( '图片背景页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:cover {"url":"' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-gray-c.jpg","dimRatio":0,"focalPoint":{"x":"0.58","y":"0.58"},"minHeight":400,"contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}},"color":{}}} -->
					<div class="gc-block-cover alignfull" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--large, 8rem);min-height:400px"><span aria-hidden="true" class="has-background-dim-0 gc-block-cover__gradient-background has-background-dim"></span><img class="gc-block-cover__image-background" alt="' . esc_attr__( '一只飞鸟的插图', 'defaultbird' ) . '" src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-gray-c.jpg" style="object-position:58% 58%" data-object-fit="cover" data-object-position="58% 58%"/><div class="gc-block-cover__inner-container"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"0rem","top":"0px","right":"0px","left":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}}},"textColor":"foreground","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide has-foreground-color has-text-color has-link-color" style="padding-top:0px;padding-right:0px;padding-bottom:0rem;padding-left:0px"><!-- gc:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":500} -->
					<div style="height:500px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div></div>
					<!-- /gc:cover --></div>
					<!-- /gc:group -->',
);
