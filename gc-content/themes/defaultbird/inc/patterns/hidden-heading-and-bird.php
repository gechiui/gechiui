<?php
/**
 * Heading and bird image
 *
 * This pattern is used only for translation
 * and to reference a dynamic image URL. It does
 * not appear in the inserter.
 */
return array(
	'title'    => __( '页眉与鸟图', 'defaultbird' ),
	'inserter' => false,
	'content'  => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}}},"layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull" style="padding-top:0px;padding-bottom:0px;"><!-- gc:heading {"align":"wide","style":{"typography":{"fontSize":"var(--gc--custom--typography--font-size--colossal, clamp(3.25rem, 8vw, 6.25rem))","lineHeight":"1.15"}}} -->
					<h2 class="alignwide" style="font-size:var(--gc--custom--typography--font-size--colossal, clamp(3.25rem, 8vw, 6.25rem));line-height:1.15">' . gc_kses_post( __( '<em>孵化场</em>：关于我观鸟冒险的博客', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading --></div>
					<!-- /gc:group -->

					<!-- gc:image {"align":"full","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image alignfull size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-c.png" alt="' . esc_attr__( '鸟儿飞翔的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->',
);
