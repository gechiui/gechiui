<?php
/**
 * Centered header with navigation, social links, and salmon background block pattern
 */
return array(
	'title'      => __( '带导航、共享链接和橙红背景的居中页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"backgroundColor":"secondary","textColor":"primary","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-primary-color has-secondary-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem);"><!-- gc:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="gc-block-columns alignwide are-vertically-aligned-center"><!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"left"}} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":""} -->
					<div class="gc-block-column"><!-- gc:site-title {"textAlign":"center","style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"}}} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:social-links {"iconColor":"primary","iconColorValue":"var(--gc--custom--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"right"}} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
