<?php
/**
 * About page links
 */
return array(
	'title'      => __( '关于页面链接', 'defaultbird' ),
	'categories' => array( 'pages', 'buttons' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"10rem","bottom":"10rem"}}},"layout":{"inherit":false,"contentSize":"400px"}} -->
					<div class="gc-block-group alignfull" style="padding-top:10rem;padding-bottom:10rem;"><!-- gc:image {"align":"center","width":100,"height":100,"sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
					<div class="gc-block-image is-style-rounded"><figure class="aligncenter size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-bird.jpg" alt="' . esc_attr__( '以飞鸟为特色的标志', 'defaultbird' ) . '" width="100" height="100"/></figure></div>
					<!-- /gc:image -->

					<!-- gc:group -->
					<div class="gc-block-group">

					<!-- gc:heading {"textAlign":"center","style":{"typography":{"fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"}}} -->
					<h2 class="has-text-align-center" style="font-size:var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))">' . esc_html__( '俯冲', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph {"align":"center"} -->
					<p class="has-text-align-center">' . esc_html__( '关于鸟类的播客', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:buttons {"contentJustification":"left"} -->
					<div class="gc-block-buttons is-content-justification-left"><!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-fill"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '观看我们的视频', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-fill"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '在 iTunes 播客上收听', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-fill"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '在 Spotify 上收听', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-fill"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '展会支持', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-fill"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '关于房东', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:group -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"primary","iconColorValue":"var(--gc--preset--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"facebook"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:group -->',
);
