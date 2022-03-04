<?php
/**
 * Page layout with image and text.
 */
return array(
	'title'      => __( '图片和文本的页面布局', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"2rem"}}},"layout":{"inherit":true}} -->
				<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:2rem"><!-- gc:heading {"align":"wide","style":{"typography":{"fontSize":"clamp(4rem, 8vw, 7.5rem)","lineHeight":"1.15","fontWeight":"300"}}} -->
					<h2 class="alignwide" style="font-size:clamp(4rem, 8vw, 7.5rem);font-weight:300;line-height:1.15">' . gc_kses_post( __( '<em>观赏鸟类 </em><br><em>在花园里</em>', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading --></div>
					<!-- /gc:group -->

					<!-- gc:image {"align":"full","style":{"color":{}}} -->
					<figure class="gc-block-image alignfull"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-b.png" alt="' . esc_attr_x( '待定', 'Short for to be determined', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->

					<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"2rem","bottom":"var(--gc--custom--spacing--large, 8rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:2rem;padding-bottom:var(--gc--custom--spacing--large, 8rem)">
					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"verticalAlignment":"bottom","style":{"spacing":{"padding":{"bottom":"1em"}}}} -->
					<div class="gc-block-column is-vertically-aligned-bottom" style="padding-bottom:1em"><!-- gc:site-logo {"width":60} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"bottom"} -->
					<div class="gc-block-column is-vertically-aligned-bottom"><!-- gc:paragraph -->
					<p>' . gc_kses_post( __( 'Oh hello. My name’s Angelo, and I operate this blog. I was born in Portland, but I currently live in upstate New York. You may recognize me from publications with names like <a href="#">Eagle Beagle</a> and <a href="#">Mourning Dive</a>. I write for a living.<br><br>I usually use this blog to catalog extensive lists of birds and other things that I find interesting. If you find an error with one of my lists, please keep it to yourself.<br><br>If that’s not your cup of tea, <a href="#">I definitely recommend this tea</a>. It’s my favorite.', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
