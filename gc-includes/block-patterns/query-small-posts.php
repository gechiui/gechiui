<?php
/**
 * Query: Small image and title.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '小图片和标题', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
					<div class="gc-block-query">
					<!-- gc:post-template -->
					<!-- gc:columns {"verticalAlignment":"center"} -->
					<div class="gc-block-columns are-vertically-aligned-center"><!-- gc:column {"verticalAlignment":"center","width":"25%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:25%"><!-- gc:post-featured-image {"isLink":true} /--></div>
					<!-- /gc:column -->
					<!-- gc:column {"verticalAlignment":"center","width":"75%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:75%"><!-- gc:post-title {"isLink":true} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->
					<!-- /gc:post-template -->
					</div>
					<!-- /gc:query -->',
);
