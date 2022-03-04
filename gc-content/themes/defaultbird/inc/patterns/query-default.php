<?php
/**
 * Default posts block pattern
 */
return array(
	'title'      => __( '默认文章', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":""},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:group {"layout":{"inherit":true}} -->
					<div class="gc-block-group"><!-- gc:post-title {"isLink":true,"align":"wide","fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

					<!-- gc:post-featured-image {"isLink":true,"align":"wide","style":{"spacing":{"margin":{"top":"calc(1.75 * var(--gc--style--block-gap))"}}}} /-->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"650px"} -->
					<div class="gc-block-column" style="flex-basis:650px"><!-- gc:post-excerpt /-->

					<!-- gc:post-date {"isLink":true,"format":"F j, Y","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":""} -->
					<div class="gc-block-column"></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:separator {"align":"wide","className":"is-style-wide"} -->
					<hr class="gc-block-separator alignwide is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:group -->
					<!-- /gc:post-template -->

					<!-- gc:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- gc:query-pagination-previous {"fontSize":"small"} /-->

					<!-- gc:query-pagination-numbers /-->

					<!-- gc:query-pagination-next {"fontSize":"small"} /-->
					<!-- /gc:query-pagination --></div>
					<!-- /gc:query -->',
);
