<?php
/**
 * Video trailer block pattern
 */
return array(
	'title'      => __( '视频预告片', 'defaultbird' ),
	'categories' => array( 'featured', 'columns' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}},"spacing":{"padding":{"top":"6rem","bottom":"4rem"}}},"backgroundColor":"secondary","textColor":"foreground","layout":{"inherit":true}} -->
				<div class="gc-block-group alignfull has-foreground-color has-secondary-background-color has-text-color has-background has-link-color" style="padding-top:6rem;padding-bottom:4rem"><!-- gc:columns {"align":"wide"} -->
				<div class="gc-block-columns alignwide"><!-- gc:column {"width":"33.33%"} -->
				<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:heading {"fontSize":"x-large"} -->
				<h2 class="has-x-large-font-size" id="extended-trailer">' . esc_html__( '加长版预告', 'defaultbird' ) . '</h2>
				<!-- /gc:heading -->

				<!-- gc:paragraph -->
				<p>' . esc_html__( '一部关于业余观鸟者的电影，一本不同鸟类的目录，以及它们发出的声音。每一种鸟都有自己的学名，所以事情看起来更正式。', 'defaultbird' ) . '</p>
				<!-- /gc:paragraph --></div>
				<!-- /gc:column -->

				<!-- gc:column {"width":"66.66%"} -->
				<div class="gc-block-column" style="flex-basis:66.66%"><!-- gc:video -->
				<figure class="gc-block-video"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
				<!-- /gc:video --></div>
				<!-- /gc:column --></div>
				<!-- /gc:columns --></div>
				<!-- /gc:group -->',
);
