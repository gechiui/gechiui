<?php
/**
 * Page layout with two columns.
 */
return array(
	'title'      => __( '页面布局-两列', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:var(--gc--custom--spacing--large, 8rem);"><!-- gc:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(4rem, 15vw, 12.5rem)","lineHeight":"1","fontWeight":"200"}}} -->
					<h1 class="alignwide" style="font-size:clamp(4rem, 15vw, 12.5rem);font-weight:200;line-height:1">' . gc_kses_post( __( '<em>金翅雀 </em><br><em>&amp; 麻雀</em>', 'defaultbird' ) ) . '</h1>
					<!-- /gc:heading -->

					<!-- gc:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:group {"align":"wide","layout":{"inherit":false}} -->
					<div class="gc-block-group alignwide"><!-- gc:columns -->
					<div class="gc-block-columns"><!-- gc:column {"verticalAlignment":"center","width":"20%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:20%"><!-- gc:paragraph -->
					<p>' . esc_html__( '欢迎', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center","width":"80%"} -->
					<div class="gc-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph -->
					<p>' . gc_kses_post( __( 'Oh hello. My name’s Angelo, and I operate this blog. I was born in Portland, but I currently live in upstate New York. You may recognize me from publications with names like <a href="#">Eagle Beagle</a> and <a href="#">Mourning Dive</a>. I write for a living.<br><br>I usually use this blog to catalog extensive lists of birds and other things that I find interesting. If you find an error with one of my lists, please keep it to yourself.<br><br>If that’s not your cup of tea, <a href="#">I definitely recommend this tea</a>. It’s my favorite.', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:spacer -->
					<div style="height:100px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator --></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:paragraph -->
					<p>' . esc_html__( '文章', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:latest-posts /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
