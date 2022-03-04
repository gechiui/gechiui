<?php
/**
 * Grid of image posts block pattern
 */
return array(
	'title'      => __( '图片文章网格', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","inherit":false,"perPage":12},"displayLayout":{"type":"flex","columns":3},"layout":{"inherit":true}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"200px"} /-->

					<!-- gc:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":"0.5rem"}}} -->
					<div class="gc-block-columns is-not-stacked-on-mobile"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"margin":{"top":"0.2em"}}},"fontSize":"small","fontFamily":"system-font"} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"4em"} -->
					<div class="gc-block-column" style="flex-basis:4em"><!-- gc:post-date {"textAlign":"right","format":"m.d.y","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->
					<!-- /gc:post-template -->

					<!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator alignwide is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- gc:query-pagination-previous {"fontSize":"small"} /-->

					<!-- gc:query-pagination-numbers /-->

					<!-- gc:query-pagination-next {"fontSize":"small"} /-->
					<!-- /gc:query-pagination --></div>
					<!-- /gc:query -->',
);
