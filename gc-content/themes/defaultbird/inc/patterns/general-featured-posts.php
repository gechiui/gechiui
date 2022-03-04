<?php
/**
 * Featured posts block pattern
 */
return array(
	'title'      => __( '特色文章', 'defaultbird' ),
	'categories' => array( 'featured', 'query' ),
	'content'    => '<!-- gc:group {"align":"wide","layout":{"inherit":false}} -->
					<div class="gc-block-group alignwide"><!-- gc:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( '最新文章', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-featured-image {"isLink":true,"width":"","height":"310px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:group -->',
);
