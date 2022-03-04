<?php
/**
 * Subscribe callout block pattern
 */
return array(
	'title'      => __( '订阅标注', 'defaultbird' ),
	'categories' => array( 'featured', 'buttons' ),
	'content'    => '<!-- gc:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="gc-block-columns alignwide are-vertically-aligned-center"><!-- gc:column {"verticalAlignment":"center"} -->
					<div class="gc-block-column is-vertically-aligned-center"><!-- gc:heading -->
					<h2>' . gc_kses_post( __( '观赏鸟类<br>从您的收件箱', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"fontSize":"medium"} -->
					<div class="gc-block-button has-custom-font-size has-medium-font-size"><a class="gc-block-button__link">' . esc_html__( '加入我们的邮件列表', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem"}}}} -->
					<div class="gc-block-column is-vertically-aligned-center" style="padding-top:2rem;padding-bottom:2rem"><!-- gc:separator {"color":"primary","className":"is-style-wide"} -->
					<hr class="gc-block-separator has-text-color has-background has-primary-background-color has-primary-color is-style-wide"/>
					<!-- /gc:separator --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->',
);
