<?php
/**
 * 带有查询、特色图片、标题和引文的页脚
 */
return array(
	'title'      => __( '带有查询、特色图片、标题和引文的页脚', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:4rem;padding-bottom:4rem"><!-- gc:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"flex","columns":3},"align":"wide"} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query -->

					<!-- gc:spacer -->
					<div style="height:100px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem"><!-- gc:site-title {"level":0} /-->
					<!-- gc:group {"layout":{"type":"flex","justifyContent":"right"}} -->
					<div class="gc-block-group">
					<!-- gc:paragraph -->
					<p>' .
					sprintf(
						/* Translators: GeChiUI link. */
						esc_html__( '自豪地采用 %s', 'defaultbird' ),
						'<a href="' . esc_url( __( 'https://www.gechiui.com', 'defaultbird' ) ) . '" rel="nofollow">GeChiUI</a>'
					) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
