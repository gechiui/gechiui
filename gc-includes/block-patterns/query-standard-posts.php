<?php
/**
 * Query: Standard.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '标准', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
					<div class="gc-block-query">
					<!-- gc:post-template -->
					<!-- gc:post-title {"isLink":true} /-->
					<!-- gc:post-featured-image  {"isLink":true,"align":"wide"} /-->
					<!-- gc:post-excerpt /-->
					<!-- gc:separator -->
					<hr class="gc-block-separator"/>
					<!-- /gc:separator -->
					<!-- gc:post-date /-->
					<!-- /gc:post-template -->
					</div>
					<!-- /gc:query -->',
);
