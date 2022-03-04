<?php
/**
 * Query: Grid.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '网格', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
					<div class="gc-block-query">
					<!-- gc:post-template -->
					<!-- gc:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"inherit":false}} -->
					<div class="gc-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- gc:post-title {"isLink":true} /-->
					<!-- gc:post-excerpt /-->
					<!-- gc:post-date /--></div>
					<!-- /gc:group -->
					<!-- /gc:post-template -->
					</div>
					<!-- /gc:query -->',
);
