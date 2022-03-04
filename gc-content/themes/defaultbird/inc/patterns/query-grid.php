<?php
/**
 * Grid of posts block pattern
 */
return array(
	'title'      => __( '瀑布流文章', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":12},"displayLayout":{"type":"flex","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template -->

					<!-- gc:separator {"align":"wide","className":"is-style-wide"} -->
					<hr class="gc-block-separator alignwide is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- gc:query-pagination-previous {"fontSize":"small"} /-->

					<!-- gc:query-pagination-numbers /-->

					<!-- gc:query-pagination-next {"fontSize":"small"} /-->
					<!-- /gc:query-pagination --></div>
					<!-- /gc:query -->',
);
