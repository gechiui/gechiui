<?php
/**
 * About page on solid color background
 */
return array(
	'title'      => __( '纯色背景的页面', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"1.25rem","right":"1.25rem","bottom":"1.25rem","left":"1.25rem"}}}} -->
					<div class="gc-block-group alignfull" style="padding-top:1.25rem;padding-right:1.25rem;padding-bottom:1.25rem;padding-left:1.25rem"><!-- gc:cover {"overlayColor":"secondary","minHeight":80,"minHeightUnit":"vh","isDark":false,"align":"full"} -->
					<div class="gc-block-cover alignfull is-light" style="min-height:80vh"><span aria-hidden="true" class="has-secondary-background-color has-background-dim-100 gc-block-cover__gradient-background has-background-dim"></span><div class="gc-block-cover__inner-container"><!-- gc:group {"layout":{"inherit":false,"contentSize":"400px"}} -->
					<div class="gc-block-group"><!-- gc:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --><!-- gc:heading {"style":{"typography":{"lineHeight":"1","textTransform":"uppercase","fontSize":"clamp(2.75rem, 6vw, 3.25rem)"}}} -->
					<h2 id="edvard-smith" style="font-size:clamp(2.75rem, 6vw, 3.25rem);line-height:1;text-transform:uppercase">' . gc_kses_post( __( '爱德华<br>史密斯', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:spacer {"height":8} -->
					<div style="height:8px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:paragraph {"fontSize":"small"} -->
					<p class="has-small-font-size">' . esc_html__( 'Oh hello. My name’s Edvard, and you’ve found your way to my website. I’m an avid bird watcher, and I also broadcast my own radio show every Tuesday evening at 11PM EDT. Listen in sometime!', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":8} -->
					<div style="height:8px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"foreground","iconColorValue":"var(--gc--preset--color--foreground)","className":"is-style-logos-only"} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --><!-- gc:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:group --></div></div>
					<!-- /gc:cover --></div>
					<!-- /gc:group -->',
);
