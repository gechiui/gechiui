<?php
/**
 * Default footer
 */
return array(
	'title'      => __( '默认页脚', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem"><!-- gc:site-title {"level":0} /-->

					<!-- gc:paragraph {"align":"right"} -->
					<p class="has-text-align-right">' .
					sprintf(
						/* Translators: GeChiUI link. */
						esc_html__( '自豪地采用 %s', 'defaultbird' ),
						'<a href="' . esc_url( __( 'https://www.gechiui.com', 'defaultbird' ) ) . '" rel="nofollow">GeChiUI</a>'
					) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
