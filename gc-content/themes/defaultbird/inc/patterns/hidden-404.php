<?php
/**
 * 404 content.
 */
return array(
	'title'    => __( '404 内容', 'defaultbird' ),
	'inserter' => false,
	'content'  => '<!-- gc:heading {"style":{"typography":{"fontSize":"clamp(4rem, 40vw, 20rem)","fontWeight":"200","lineHeight":"1"}},"className":"has-text-align-center"} -->
					<h2 class="has-text-align-center" style="font-size:clamp(4rem, 40vw, 20rem);font-weight:200;line-height:1">' . esc_html( _x( '404', 'Error code for a webpage that is not found.', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->
					<!-- gc:paragraph {"align":"center"} -->
					<p class="has-text-align-center">' . esc_html__( '找不到此页面。也许尝试搜索？', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->
					<!-- gc:search {"label":"' . esc_html_x( '搜索', 'label', 'defaultbird' ) . '","showLabel":false,"width":50,"widthUnit":"%","buttonText":"' . esc_html__( '搜索', 'defaultbird' ) . '","buttonUseIcon":true,"align":"center"} /-->',
);
