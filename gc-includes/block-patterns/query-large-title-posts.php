<?php
/**
 * Query: Large title.
 *
 * @package GeChiUI
 */

return array(
	'title'      => _x( '大标题', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"100px","right":"100px","bottom":"100px","left":"100px"}},"color":{"text":"#ffffff","background":"#000000"}}} -->
					<div class="gc-block-group alignfull has-text-color has-background" style="background-color:#000000;color:#ffffff;padding-top:100px;padding-right:100px;padding-bottom:100px;padding-left:100px"><!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
					<div class="gc-block-query"><!-- gc:post-template -->
					<!-- gc:separator {"customColor":"#ffffff","align":"wide","className":"is-style-wide"} -->
					<hr class="gc-block-separator alignwide has-text-color has-background is-style-wide" style="background-color:#ffffff;color:#ffffff"/>
					<!-- /gc:separator -->

					<!-- gc:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="gc-block-columns alignwide are-vertically-aligned-center"><!-- gc:column {"verticalAlignment":"center","width":"20%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:20%"><!-- gc:post-date {"style":{"color":{"text":"#ffffff"}},"fontSize":"extra-small"} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center","width":"80%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- gc:post-title {"isLink":true,"style":{"typography":{"fontSize":"72px","lineHeight":"1.1"},"color":{"text":"#ffffff","link":"#ffffff"}}} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:group -->',
);
