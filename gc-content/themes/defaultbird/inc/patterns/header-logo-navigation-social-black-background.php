<?php
/**
 * Logo, navigation, and social links header with black background block pattern
 */
return array(
	'title'      => __( '深色背景、Logo、导航和共享链接的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"0rem","top":"0px","right":"0px","left":"0px"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:0px;padding-right:0px;padding-bottom:0rem;padding-left:0px"><!-- gc:site-logo {"width":64} /-->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->

					<!-- gc:social-links {"iconColor":"background","iconColorValue":"var(--gc--preset--color--background)","className":"is-style-logos-only"} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"instagram"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /--></ul>
					<!-- /gc:social-links -->
					<!-- /gc:navigation --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
