<?php
/**
 * 带文字、标题和 logo 的页脚
 */
return array(
	'title'      => __( '带文字、标题和 logo 的页脚', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--large, 8rem)","bottom":"6rem"}}},"backgroundColor":"secondary","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-secondary-background-color has-background" style="padding-top:var(--gc--custom--spacing--large, 8rem);padding-bottom:6rem"><!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"33%"} -->
					<div class="gc-block-column" style="flex-basis:33%"><!-- gc:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( '关于我们', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:paragraph {"style":{"fontSize":"small"} -->
					<p class="has-small-font-size">' . esc_html__( '我们是一群无聊的观鸟者。 众所周知，为了观察最稀有的鸟类，我们会偷偷穿过栅栏，爬上围墙，通常，我们会擅自闯入。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":180} -->
					<div style="height:180px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:site-title {"level":0} /--></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"bottom"} -->
					<div class="gc-block-column is-vertically-aligned-bottom"><!-- gc:site-logo {"align":"right","width":60} /--></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
