<?php
/**
 * Page layout with image, text and video.
 */
return array(
	'title'      => __( '图像、文本和视频的页面布局', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"var(--gc--custom--spacing--large, 8rem)"}}},"backgroundColor":"primary","textColor":"background"} -->
					<div class="gc-block-group alignfull has-background-color has-primary-background-color has-text-color has-background" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:var(--gc--custom--spacing--large, 8rem)"><!-- gc:group {"layout":{"inherit":true}} -->
					<div class="gc-block-group"><!-- gc:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h1 class="alignwide" style="font-size:clamp(3rem, 6vw, 4.5rem)">' . gc_kses_post( __( '<em>Warble</em>，一部关于 <br>业余观鸟者。', 'defaultbird' ) ) . '</h1>
					<!-- /gc:heading -->

					<!-- gc:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"33.33%"} -->
					<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size">' . esc_html__( '筛选', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . gc_kses_post( __( '2022年5月14日晚上7:00，新罕布什尔州加登维尔雅顿路245号文塔格剧院', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"secondary","textColor":"primary"} -->
					<div class="gc-block-button"><a class="gc-block-button__link has-primary-color has-secondary-background-color has-text-color has-background">' . esc_html__( '买票', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"66.66%"} -->
					<div class="gc-block-column" style="flex-basis:66.66%"></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->

					<!-- gc:image {"align":"full","style":{"color":{}}} -->
					<figure class="gc-block-image alignfull"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-a.png" alt="' . esc_attr__( '飞行中的鸟的插图', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->

					<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"33.33%"} -->
					<div class="gc-block-column" style="flex-basis:33.33%"><!-- gc:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size">' . esc_html__( '加长版预告', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '哦，你好。我叫安吉洛，你找到了我的博客。我写了一系列的话题，但最近我一直在分享我对明年的希望。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":"66.66%"} -->
					<div class="gc-block-column" style="flex-basis:66.66%"><!-- gc:video {"id":181} -->
					<figure class="gc-block-video"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
					<!-- /gc:video --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
