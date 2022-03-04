<?php
/**
 * Footer with title, tagline, and social links on a dark background
 */
return array(
	'title'      => __( '黑色背景的页脚，标题、标语和共享链接', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="gc-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem"><!-- gc:group -->
					<div class="gc-block-group"><!-- gc:site-title {"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}},"typography":{"textTransform":"uppercase"}}} /-->

					<!-- gc:site-tagline {"style":{"spacing":{"margin":{"top":"0.25em","bottom":"0px"}},"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /gc:group -->

					<!-- gc:social-links {"iconBackgroundColor":"foreground","iconBackgroundColorValue":"var(--gc--preset--color--foreground)","layout":{"type":"flex","justifyContent":"right"}} -->
					<ul class="gc-block-social-links has-icon-background-color"><!-- gc:social-link {"url":"#","service":"facebook"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
