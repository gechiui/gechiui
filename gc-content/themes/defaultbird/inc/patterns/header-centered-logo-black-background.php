<?php
/**
 * Header with centered logo and black background
 */
return array(
	'title'      => __( 'Logo居中和深色背景的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"bottom":"var(--gc--custom--spacing--small, 1.25rem)","top":"var(--gc--custom--spacing--small, 1.25rem)"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"foreground","textColor":"background","layout":{"type":"flex","justifyContent":"center"}} -->
					<div class="gc-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:navigation-link {"isTopLevelLink":true} /-->

					<!-- gc:navigation-link {"isTopLevelLink":true} /-->

					<!-- gc:site-logo {"width":90} /-->

					<!-- gc:navigation-link {"isTopLevelLink":true} /-->

					<!-- gc:navigation-link {"isTopLevelLink":true} /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group -->',
);
