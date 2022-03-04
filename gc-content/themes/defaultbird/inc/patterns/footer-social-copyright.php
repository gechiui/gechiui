<?php
/**
 * 带有社交链接和版权的信息页脚
 */
return array(
	'title'      => __( '带有社交链接和版权的信息页脚', 'defaultbird' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}}} -->
					<div class="gc-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem"><!-- gc:social-links {"iconColor":"foreground","iconColorValue":"var(--gc--preset--color--foreground)","iconBackgroundColor":"background","iconBackgroundColorValue":"var(--gc--preset--color--background)","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="gc-block-social-links has-icon-color has-icon-background-color"><!-- gc:social-link {"url":"#","service":"facebook"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links -->

					<!-- gc:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:paragraph {"align":"center","style":{"typography":{"fontSize":"16px"}}} -->
					<p class="has-text-align-center" style="font-size:16px">' . esc_html__( '© 站点标题', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
