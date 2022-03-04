<?php
/**
 * Header with centered logo block pattern
 */
return array(
	'title'      => __( 'Logo居中的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem);"><!-- gc:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="gc-block-columns alignwide are-vertically-aligned-center"><!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"400","textTransform":"uppercase"}}} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"64px"} -->
					<div class="gc-block-column" style="flex-basis:64px"><!-- gc:site-logo {"width":64} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
