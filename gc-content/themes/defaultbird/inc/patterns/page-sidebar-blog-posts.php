<?php
/**
 * Blog posts with left sidebar block pattern
 */
return array(
	'title'      => __( '左边栏博客文章', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"},"blockGap":"5%"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary"} -->
					<div class="gc-block-columns alignwide has-primary-color has-text-color has-link-color" style="margin-top:0px;margin-bottom:0px"><!-- gc:column {"width":"33.33%"} -->
					<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:cover {"overlayColor":"secondary","minHeight":400,"isDark":false} -->
					<div class="gc-block-cover is-light" style="min-height:400px"><span aria-hidden="true" class="has-secondary-background-color has-background-dim-100 gc-block-cover__gradient-background has-background-dim"></span><div class="gc-block-cover__inner-container"><!-- gc:site-logo {"align":"center","width":60} /--></div></div>
					<!-- /gc:cover -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:site-tagline {"fontSize":"small"} /-->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:separator {"color":"foreground","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-foreground-background-color has-foreground-color is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:navigation {"orientation":"vertical"} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:separator {"color":"foreground","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-foreground-background-color has-foreground-color is-style-wide"/>
					<!-- /gc:separator --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"66.66%"} -->
					<div class="gc-block-column" style="flex-basis:66.66%"><!-- gc:query {"query":{"perPage":"5","pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"layout":{"inherit":true}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"1rem"}},"typography":{"fontStyle":"normal","fontWeight":"300"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

					<!-- gc:post-featured-image {"isLink":true} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:group {"layout":{"type":"flex"}} -->
					<div class="gc-block-group"><!-- gc:post-date {"format":"F j, Y","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"small"} /-->

					<!-- gc:post-terms {"term":"category","fontSize":"small"} /-->

					<!-- gc:post-terms {"term":"post_tag","fontSize":"small"} /--></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":128} -->
					<div style="height:128px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->
					<!-- /gc:post-template -->

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
