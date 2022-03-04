<?php
/**
 * Blog footer
 */
return array(
	'title'      => __( '博客页脚', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( '关于我们', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '我们是一群无聊的观鸟者。 众所周知，为了观察最稀有的鸟类，我们会偷偷穿过栅栏，爬上围墙，通常，我们会擅自闯入。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( '最新文章', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:latest-posts /--></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( '分类', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:categories /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
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
