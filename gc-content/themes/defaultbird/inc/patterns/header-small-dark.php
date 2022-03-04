<?php
/**
 * Small header with dark background block pattern
 */
return array(
	'title'      => __( '深色背景的小页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"0px","bottom":"0px"}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:0px;padding-bottom:0px;"><!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:0px;padding-bottom:0px;"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:group {"layout":{"type":"flex"}} -->
					<div class="gc-block-group"><!-- gc:site-logo {"width":64} /-->

					<!-- gc:site-title {"style":{"typography":{"fontStyle":"italic","fontWeight":"400"}}} /--></div>
					<!-- /gc:group -->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->

					<!-- gc:image {"align":"wide","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image alignwide size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-d.png" alt="' . esc_attr__( '鸟儿飞翔的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image --></div>
					<!-- /gc:group -->
					<!-- gc:spacer {"height":66} -->
					<div style="height:66px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->',
);
