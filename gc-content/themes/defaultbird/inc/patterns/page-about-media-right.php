<?php
/**
 * About page with media on the right
 */
return array(
	'title'      => __( '右侧媒体的关于页面', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:media-text {"align":"full","mediaPosition":"right","mediaLink":"' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-black.jpg","mediaType":"image","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"foreground","textColor":"background"} -->
				<div class="gc-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile has-background-color has-foreground-background-color has-text-color has-background has-link-color"><figure class="gc-block-media-text__media"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-black.jpg" alt="' . esc_attr__( '鸟儿飞翔的影像', 'defaultbird' ) . '"/></figure><div class="gc-block-media-text__content"><!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->
					<!-- gc:site-logo {"width":60} /-->

					<!-- gc:group {"style":{"spacing":{"padding":{"right":"min(8rem, 5vw)","top":"min(20rem, 20vw)"}}}} -->
					<div class="gc-block-group" style="padding-top:min(20rem, 20vw);padding-right:min(8rem, 5vw)"><!-- gc:heading {"style":{"typography":{"fontWeight":"300","lineHeight":"1.115","fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h2 style="font-size:clamp(3rem, 6vw, 4.5rem);font-weight:300;line-height:1.115"><em>' . gc_kses_post( __( '金刚砂<br>德里斯库', 'defaultbird' ) ) . '</em></h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph {"style":{"typography":{"lineHeight":"1.6"}}} -->
					<p style="line-height:1.6">' . esc_html__( 'Oh hello. My name’s Emery, and you’ve found your way to my website. I’m an avid bird watcher, and I also broadcast my own radio show on Tuesday evenings at 11PM EDT.', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"background","iconColorValue":"var(--gc--preset--color--foreground)","iconBackgroundColor":"foreground","iconBackgroundColorValue":"var(--gc--preset--color--background)"} -->
					<ul class="gc-block-social-links has-icon-color has-icon-background-color"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:group --></div>

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:media-text -->',
);
