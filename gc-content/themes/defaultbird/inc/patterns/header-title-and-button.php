<?php
/**
 * Title and button header block pattern
 */
return array(
	'title'      => __( '标题和按钮的页眉', 'defaultbird' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var(--gc--custom--spacing--large, 8rem)","top":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:site-title {"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}},"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"},"overlayMenu":"always"} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
