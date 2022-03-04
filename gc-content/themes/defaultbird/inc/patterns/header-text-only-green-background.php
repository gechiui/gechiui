<?php
/**
 * Text-only header with green background block pattern
 */
return array(
	'title'      => __( '绿色背景的纯文字页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide"><!-- gc:group -->
					<div class="gc-block-group"><!-- gc:site-title {"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}},"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

					<!-- gc:site-tagline {"style":{"spacing":{"margin":{"top":"0.25em","bottom":"0px"}},"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /gc:group -->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
