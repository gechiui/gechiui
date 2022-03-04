<?php
/**
 * Large post titles block pattern
 */
return array(
	'title'      => __( '大标题', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:query {"query":{"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"perPage":8},"align":"wide"} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template -->
					<!-- gc:columns -->
					<div class="gc-block-columns"><!-- gc:column {"verticalAlignment":"top","width":"4em"} -->
					<div class="gc-block-column is-vertically-aligned-top" style="flex-basis:4em"><!-- gc:post-date {"format":"M j","fontSize":"small"} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center","width":""} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}},"typography":{"fontSize":"clamp(2.75rem, 6vw, 3.25rem)"}}} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator -->
					<!-- /gc:post-template --></div>
					<!-- /gc:query -->',
);
