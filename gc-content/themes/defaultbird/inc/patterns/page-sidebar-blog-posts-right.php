<?php
/**
 * Blog posts with right sidebar block pattern
 */
return array(
	'title'      => __( '右边栏博客文章', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"2rem","top":"0px","right":"0px","left":"0px"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:0px;padding-right:0px;padding-bottom:2rem;padding-left:0px"><!-- gc:group {"layout":{"type":"flex"}} -->
					<div class="gc-block-group"><!-- gc:site-logo {"width":64} /--></div>
					<!-- /gc:group -->

					<!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- gc:page-list /-->
					<!-- /gc:navigation --></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"},"blockGap":"5%"},"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}}},"textColor":"foreground"} -->
					<div class="gc-block-columns alignwide has-foreground-color has-text-color has-link-color" style="margin-top:0px;margin-bottom:0px"><!-- gc:column {"width":"66.66%","style":{"spacing":{"padding":{"bottom":"6rem"}}}} -->
					<div class="gc-block-column" style="padding-bottom:6rem;flex-basis:66.66%"><!-- gc:query {"queryId":9,"query":{"perPage":"5","pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"list"},"layout":{"inherit":true}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"1rem"}},"typography":{"fontStyle":"normal","fontWeight":"300"},"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}}},"textColor":"foreground","fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

					<!-- gc:post-featured-image {"isLink":true} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:group {"layout":{"type":"flex"}} -->
					<div class="gc-block-group"><!-- gc:post-date {"format":"F j, Y","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"small"} /-->

					<!-- gc:post-terms {"term":"category","fontSize":"small"} /-->

					<!-- gc:post-terms {"term":"post_tag","fontSize":"small"} /--></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->
					<!-- /gc:post-template -->

					<!-- gc:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- gc:query-pagination-previous {"fontSize":"small"} /-->

					<!-- gc:query-pagination-numbers /-->

					<!-- gc:query-pagination-next {"fontSize":"small"} /-->
					<!-- /gc:query-pagination --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"33.33%"} -->
					<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:image {"sizeSlug":"large","linkDestination":"none"} -->
					<figure class="gc-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-salmon.jpg" alt="' . esc_attr__( '飞鸟的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->

					<!-- gc:spacer {"height":4} -->
					<div style="height:4px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:site-title {"isLink":false,"style":{"typography":{"fontStyle":"normal","fontWeight":"300","lineHeight":"1.2"}},"fontSize":"large","fontFamily":"source-serif-pro"} /-->

					<!-- gc:site-tagline /-->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:heading {"level":4,"fontSize":"large"} -->
					<h4 class="has-large-font-size"><em>' . esc_html__( '分类', 'defaultbird' ) . '</em></h4>
					<!-- /gc:heading -->

					<!-- gc:tag-cloud {"taxonomy":"category","showTagCounts":true} /-->

					<!-- gc:heading {"level":4,"fontSize":"large"} -->
					<h4 class="has-large-font-size"><em>' . esc_html__( '标签', 'defaultbird' ) . '</em></h4>
					<!-- /gc:heading -->

					<!-- gc:tag-cloud {"showTagCounts":true} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
