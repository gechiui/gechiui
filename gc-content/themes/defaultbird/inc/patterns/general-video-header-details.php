<?php
/**
 * Video with header and details block pattern
 */
return array(
	'title'      => __( '标题和详情的视频', 'defaultbird' ),
	'categories' => array( 'featured', 'columns' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}},"elements":{"link":{"color":{"text":"var:preset|color|secondary"}}}},"backgroundColor":"foreground","textColor":"secondary"} -->
					<div class="gc-block-group alignfull has-secondary-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h1 class="alignwide" id="warble-a-film-about-hobbyist-bird-watchers-1" style="font-size:clamp(3rem, 6vw, 4.5rem)">' . gc_kses_post( __( '<em>Warble</em>，一部关于 <br>业余观鸟者。', 'defaultbird' ) ) . '</h1>
					<!-- /gc:heading -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:video {"align":"wide"} -->
					<figure class="gc-block-video alignwide"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
					<!-- /gc:video -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"50%"} -->
					<div class="gc-block-column" style="flex-basis:50%"><!-- gc:paragraph -->
					<p><strong>' . esc_html__( '推荐日期', 'defaultbird' ) . '</strong></p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph -->
					<p>' . gc_kses_post( __( 'Jesús Rodriguez<br>Doug Stilton<br>Emery Driscoll<br>Megan Perry<br>Rowan Price', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:paragraph -->
					<p>' . gc_kses_post( __( '安杰洛·措<br>爱德华·斯蒂尔顿<br>艾米·詹森<br>波士顿贝尔<br>谢伊·福特', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
