<?php
/**
 * Wide image with introduction and buttons block pattern
 */
return array(
	'title'      => __( '带有介绍和按钮的宽图像', 'defaultbird' ),
	'categories' => array( 'featured', 'columns' ),
	'content'    => '<!-- gc:group {"align":"wide"} -->
				<div class="gc-block-group alignwide"><!-- gc:image {"sizeSlug":"large"} -->
				<figure class="gc-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-gray-a.jpg" alt="' . esc_attr__( '鸟儿飞翔的插图。', 'defaultbird' ) . '"/></figure>
				<!-- /gc:image -->

				<!-- gc:columns {"verticalAlignment":null} -->
				<div class="gc-block-columns"><!-- gc:column {"verticalAlignment":"bottom"} -->
				<div class="gc-block-column is-vertically-aligned-bottom"><!-- gc:heading {"style":{"typography":{"fontSize":"clamp(3.25rem, 8vw, 6.25rem)","lineHeight":"1.15"}}} -->
				<h2 style="font-size:clamp(3.25rem, 8vw, 6.25rem);line-height:1.15"><em>' . gc_kses_post( __( '欢迎来到<br>鸟舍', 'defaultbird' ) ) . '</em></h2>
				<!-- /gc:heading --></div>
				<!-- /gc:column -->

				<!-- gc:column {"verticalAlignment":"bottom","style":{"spacing":{"padding":{"bottom":"6rem"}}}} -->
				<div class="gc-block-column is-vertically-aligned-bottom" style="padding-bottom:6rem"><!-- gc:paragraph -->
				<p>' . esc_html__( '一部关于业余观鸟者的电影，一本不同鸟类的目录，以及它们发出的声音。每一种鸟都有自己的学名，所以事情看起来更正式。', 'defaultbird' ) . '</p>
				<!-- /gc:paragraph -->

				<!-- gc:spacer {"height":20} -->
				<div style="height:20px" aria-hidden="true" class="gc-block-spacer"></div>
				<!-- /gc:spacer -->

				<!-- gc:buttons -->
				<div class="gc-block-buttons"><!-- gc:button {"className":"is-style-outline"} -->
				<div class="gc-block-button is-style-outline"><a class="gc-block-button__link">' . esc_html__( '了解更多', 'defaultbird' ) . '</a></div>
				<!-- /gc:button -->

				<!-- gc:button {"className":"is-style-outline"} -->
				<div class="gc-block-button is-style-outline"><a class="gc-block-button__link">' . esc_html__( '买票', 'defaultbird' ) . '</a></div>
				<!-- /gc:button --></div>
				<!-- /gc:buttons --></div>
				<!-- /gc:column --></div>
				<!-- /gc:columns --></div>
				<!-- /gc:group -->',
);
