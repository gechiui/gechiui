<?php
/**
 * Title, navigation, and social links header block pattern
 */
return array(
	'title'      => __( '标题、导航和共享链接的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var(--gc--custom--spacing--large, 8rem)","top":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:site-title {"style":{"typography":{"fontStyle":"italic","fontWeight":"400"}}} /-->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->

					<!-- gc:social-links {"iconColor":"foreground","iconColorValue":"var(--gc--preset--color--foreground)","className":"is-style-logos-only"} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"instagram"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /--></ul>
					<!-- /gc:social-links -->
					<!-- /gc:navigation --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
