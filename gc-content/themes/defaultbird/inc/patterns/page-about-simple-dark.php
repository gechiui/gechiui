<?php
/**
 * Simple dark about page
 */
return array(
	'title'      => __( '深色的关于页面', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:cover {"overlayColor":"foreground","minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"max(1.25rem, 8vw)","right":"max(1.25rem, 8vw)","bottom":"max(1.25rem, 8vw)","left":"max(1.25rem, 8vw)"}}}} -->
					<div class="gc-block-cover alignfull has-foreground-background-color has-background-dim" style="padding-top:max(1.25rem, 8vw);padding-right:max(1.25rem, 8vw);padding-bottom:max(1.25rem, 8vw);padding-left:max(1.25rem, 8vw);min-height:100vh"><div class="gc-block-cover__inner-container"><!-- gc:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"},"overlayMenu":"always"} -->
					<!-- gc:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /gc:navigation -->

					<!-- gc:columns -->
					<div class="gc-block-columns"><!-- gc:column {"verticalAlignment":"bottom","width":"45%","style":{"spacing":{"padding":{"top":"12rem"}}}} -->
					<div class="gc-block-column is-vertically-aligned-bottom" style="padding-top:12rem;flex-basis:45%"><!-- gc:site-logo {"width":60} /-->

					<!-- gc:heading {"style":{"typography":{"fontWeight":"300","lineHeight":"1.115","fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h2 style="font-size:clamp(3rem, 6vw, 4.5rem);font-weight:300;line-height:1.115"><em>' . gc_kses_post( __( '赫苏斯<br>罗德里格斯', 'defaultbird' ) ) . '</em></h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph {"style":{"typography":{"lineHeight":"1.6"}}} -->
					<p style="line-height:1.6">' . esc_html__( 'Oh hello. My name’s Jesús, and you’ve found your way to my website. I’m an avid bird watcher, and I also broadcast my own radio show on Tuesday evenings at 11PM EDT.', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"background","iconColorValue":"var(--gc--preset--color--foreground)","iconBackgroundColor":"foreground","iconBackgroundColorValue":"var(--gc--preset--color--background)"} -->
					<ul class="gc-block-social-links has-icon-color has-icon-background-color"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"0rem","right":"0rem","bottom":"4rem","left":"0rem"}}}} -->
					<div class="gc-block-column is-vertically-aligned-center" style="padding-top:0rem;padding-right:0rem;padding-bottom:4rem;padding-left:0rem"><!-- gc:separator {"color":"background","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /gc:separator --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div></div>
					<!-- /gc:cover -->',
);
