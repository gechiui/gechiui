<?php
/**
 * About page with media on the left
 */
return array(
	'title'      => __( '左侧媒体的关于页面', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:media-text {"align":"full","mediaType":"image","imageFill":true,"focalPoint":{"x":"0.63","y":"0.16"},"backgroundColor":"foreground","className":"alignfull is-image-fill has-background-color has-text-color has-background has-link-color"} -->
					<div class="gc-block-media-text alignfull is-stacked-on-mobile is-image-fill has-background-color has-text-color has-background has-link-color has-foreground-background-color has-background"><figure class="gc-block-media-text__media" style="background-image:url(' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-salmon.jpg);background-position:63% 16%"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-salmon.jpg" alt="' . esc_attr__( '树枝上的鸟的图像', 'defaultbird' ) . '"/></figure><div class="gc-block-media-text__content"><!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:site-logo {"width":60} /-->

					<!-- gc:group {"style":{"spacing":{"padding":{"right":"min(8rem, 5vw)","top":"min(28rem, 28vw)"}}}} -->
					<div class="gc-block-group" style="padding-top:min(28rem, 28vw);padding-right:min(8rem, 5vw)"><!-- gc:heading {"style":{"typography":{"fontWeight":"300","lineHeight":"1.115","fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h2 style="font-size:clamp(3rem, 6vw, 4.5rem);font-weight:300;line-height:1.115"><em>' . esc_html__( '道格', 'defaultbird' ) . '<br>' . esc_html__( '斯蒂尔顿', 'defaultbird' ) . '</em></h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph {"style":{"typography":{"lineHeight":"1.6"}}} -->
					<p style="line-height:1.6">' . esc_html__( 'Oh hello. My name’s Doug, and you’ve found your way to my website. I’m an avid bird watcher, and I also broadcast my own radio show on Tuesday evenings at 11PM EDT.', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"background","iconColorValue":"var(--gc--preset--color--background)","iconBackgroundColor":"foreground","iconBackgroundColorValue":"var(--gc--preset--color--foreground)"} -->
					<ul class="gc-block-social-links has-icon-color has-icon-background-color"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div></div>
					<!-- /gc:media-text -->',
);
