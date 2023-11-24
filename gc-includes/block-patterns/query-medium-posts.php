<?php
/**
 * Query: Image at left.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '图片在左', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
					<div class="gc-block-query">
					<!-- gc:post-template -->
					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"66.66%"} -->
					<div class="gc-block-column" style="flex-basis:66.66%"><!-- gc:post-featured-image {"isLink":true} /--></div>
					<!-- /gc:column -->
					<!-- gc:column {"width":"33.33%"} -->
					<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:post-title {"isLink":true} /-->
					<!-- gc:post-excerpt /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->
					<!-- /gc:post-template -->
					</div>
					<!-- /gc:query -->',
);
