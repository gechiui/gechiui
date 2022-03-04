<?php
/**
 * About page with large image and buttons
 */
return array(
	'title'      => __( '带有大图像和按钮的关于页面', 'defaultbird' ),
	'categories' => array( 'pages', 'buttons' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--gc--custom--spacing--small, 1.25rem)","bottom":"var(--gc--custom--spacing--small, 1.25rem)"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:var(--gc--custom--spacing--small, 1.25rem);padding-bottom:var(--gc--custom--spacing--small, 1.25rem)"><!-- gc:image {"align":"wide","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image alignwide size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-gray-b.jpg" alt=""/></figure>
					<!-- /gc:image -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '购买我的作品', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '支持我的工作室', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '上课', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '阅读关于我的信息', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '了解我的流程', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link">' . esc_html__( '加入我的邮件列表', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:social-links {"iconColor":"primary","iconColorValue":"var(--gc--preset--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="gc-block-social-links has-icon-color is-style-logos-only"><!-- gc:social-link {"url":"#","service":"gechiui"} /-->

					<!-- gc:social-link {"url":"#","service":"facebook"} /-->

					<!-- gc:social-link {"url":"#","service":"twitter"} /-->

					<!-- gc:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /gc:social-links --></div>
					<!-- /gc:group -->',
);
