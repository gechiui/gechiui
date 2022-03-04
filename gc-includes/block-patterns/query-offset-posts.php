<?php
/**
 * Query: Offset.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '偏移', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"inherit":false}} -->
					<div class="gc-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- gc:columns -->
					<div class="gc-block-columns"><!-- gc:column {"width":"50%"} -->
					<div class="gc-block-column" style="flex-basis:50%"><!-- gc:query {"query":{"perPage":2,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:post-featured-image /-->
					<!-- gc:post-title /-->
					<!-- gc:post-date /-->
					<!-- gc:spacer {"height":200} -->
					<div style="height:200px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->
					<!-- gc:column {"width":"50%"} -->
					<div class="gc-block-column" style="flex-basis:50%"><!-- gc:query {"query":{"perPage":2,"pages":0,"offset":2,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:spacer {"height":200} -->
					<div style="height:200px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->
					<!-- gc:post-featured-image /-->
					<!-- gc:post-title /-->
					<!-- gc:post-date /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
