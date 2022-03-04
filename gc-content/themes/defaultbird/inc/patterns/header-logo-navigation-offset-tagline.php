<?php
/**
 * Logo, navigation, and offset tagline Header block pattern
 */
return array(
	'title'      => __( 'Logo、导航和偏移标语的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var(--gc--custom--spacing--large, 8rem)"}}}} -->
					<div class="gc-block-group alignwide" style="padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:site-logo {"width":64} /-->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group -->

					<!-- gc:columns {"isStackedOnMobile":false,"align":"wide"} -->
					<div class="gc-block-columns alignwide is-not-stacked-on-mobile"><!-- gc:column {"width":"64px"} -->
					<div class="gc-block-column" style="flex-basis:64px"></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"380px"} -->
					<div class="gc-block-column" style="flex-basis:380px"><!-- gc:site-tagline {"fontSize":"small"} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
