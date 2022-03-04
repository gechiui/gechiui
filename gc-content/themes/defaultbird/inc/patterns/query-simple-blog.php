<?php
/**
 * Simple blog posts block pattern
 */
return array(
	'title'      => __( '简单的博客文章', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"perPage":10},"layout":{"inherit":true}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"1rem","bottom":"1rem"}},"typography":{"fontStyle":"normal","fontWeight":"300"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

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
					<!-- /gc:query -->',
);
