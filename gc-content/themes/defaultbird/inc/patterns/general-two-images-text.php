<?php
/**
 * Two images with text block pattern
 */
return array(
	'title'      => __( '两个带文字的图片', 'defaultbird' ),
	'categories' => array( 'featured', 'columns', 'gallery' ),
	'content'    => '<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"style":{"spacing":{"padding":{"top":"0rem","right":"0rem","bottom":"0rem","left":"0rem"}}}} -->
					<div class="gc-block-column" style="padding-top:0rem;padding-right:0rem;padding-bottom:0rem;padding-left:0rem"><!-- gc:image {"sizeSlug":"large"} -->
					<figure class="gc-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-salmon.jpg" alt="' . esc_attr__( '一只鸟坐在树枝上的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->

					<!-- gc:spacer {"height":30} -->
					<div style="height:30px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="screening">' . esc_html__( '筛选', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . gc_kses_post( __( '2022年5月14日晚上7:00，新罕布什尔州加登维尔雅顿路245号文塔格剧院', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":8} -->
					<div style="height:8px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:spacer {"height":10} -->
					<div style="height:10px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"foreground"} -->
					<div class="gc-block-button"><a class="gc-block-button__link has-foreground-background-color has-background">' . esc_html__( '买票', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column -->

					<!-- gc:column {"style":{"spacing":{"padding":{"top":"0rem","right":"0rem","bottom":"0rem","left":"0rem"}}}} -->
					<div class="gc-block-column" style="padding-top:0rem;padding-right:0rem;padding-bottom:0rem;padding-left:0rem"><!-- gc:image {"sizeSlug":"large"} -->
					<figure class="gc-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-green.jpg" alt="' . esc_attr__( '鸟儿飞翔的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->

					<!-- gc:spacer {"height":30} -->
					<div style="height:30px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="screening">' . esc_html__( '筛选', 'defaultbird' ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . gc_kses_post( __( '2022年5月14日晚上7:00，新罕布什尔州加登维尔雅顿路245号文塔格剧院', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":8} -->
					<div style="height:8px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:spacer {"height":10} -->
					<div style="height:10px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"foreground"} -->
					<div class="gc-block-button"><a class="gc-block-button__link has-foreground-background-color has-background">' . esc_html__( '买票', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->',
);
