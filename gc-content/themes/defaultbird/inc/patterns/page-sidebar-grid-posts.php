<?php
/**
 * Grid of posts with left sidebar block pattern
 */
return array(
	'title'      => __( '左边栏文章网格', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}}}} -->
					<div class="gc-block-columns alignwide" style="margin-top:0px;margin-bottom:0px"><!-- gc:column {"width":"30%"} -->
					<div class="gc-block-column" style="flex-basis:30%"><!-- gc:site-title {"isLink":false,"style":{"spacing":{"margin":{"top":"0px","bottom":"1rem"}},"typography":{"fontStyle":"italic","fontWeight":"300","lineHeight":"1.1"}},"fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))","fontFamily":"source-serif-pro"} /-->

					<!-- gc:site-tagline {"fontSize":"small"} /-->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:separator {"color":"foreground","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-foreground-background-color has-foreground-color is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:navigation {"orientation":"vertical"} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:separator {"color":"foreground","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-foreground-background-color has-foreground-color is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:site-logo {"width":60} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"70%"} -->
					<div class="gc-block-column" style="flex-basis:70%"><!-- gc:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","inherit":false,"perPage":12},"displayLayout":{"type":"flex","columns":3},"layout":{"inherit":true}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"200px"} /-->

					<!-- gc:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group"><!-- gc:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"small","fontFamily":"system-font"} /-->

					<!-- gc:post-date {"format":"m.d.y","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /gc:group -->
					<!-- /gc:post-template -->

					<!-- gc:separator {"className":"alignwide is-style-wide"} -->
					<hr class="gc-block-separator alignwide is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- gc:query-pagination-previous {"fontSize":"small"} /-->

					<!-- gc:query-pagination-numbers /-->

					<!-- gc:query-pagination-next {"fontSize":"small"} /-->
					<!-- /gc:query-pagination --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
