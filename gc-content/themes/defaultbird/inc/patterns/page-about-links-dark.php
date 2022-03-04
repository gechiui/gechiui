<?php
/**
 * About page links (dark)
 */
return array(
	'title'      => __( '关于页面链接（黑色）', 'defaultbird' ),
	'categories' => array( 'pages', 'buttons' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"10rem","bottom":"10rem"}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":false,"contentSize":"400px"}} -->
					<div class="gc-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:10rem;padding-bottom:10rem;"><!-- gc:group -->
					<div class="gc-block-group">

					<!-- gc:image {"width":100,"height":100,"sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
					<figure class="gc-block-image size-full is-resized is-style-rounded"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-bird.jpg" alt="' . esc_attr__( '以飞鸟为特色的标志', 'defaultbird' ) . '" width="100" height="100"/></figure>
					<!-- /gc:image -->

					<!-- gc:heading {"textAlign":"left","style":{"typography":{"fontSize":"var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"}}} -->
					<h2 class="has-text-align-left" style="font-size:var(--gc--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))">' . esc_html__( '蜂鸟的烦恼', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:buttons {"contentJustification":"left"} -->
					<div class="gc-block-buttons is-content-justification-left"><!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-outline"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '观看我们的视频', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-outline"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '在 iTunes 播客上收听', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-outline"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '在 Spotify 上收听', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-outline"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '展会支持', 'defaultbird' ) . '</a></div>
					<!-- /gc:button -->

					<!-- gc:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100 is-style-outline"><a class="gc-block-button__link" style="border-radius:6px">' . esc_html__( '关于房东', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
