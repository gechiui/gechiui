<?php
/**
 * Poster with right sidebar block pattern
 */
return array(
	'title'      => __( '右边栏海报', 'defaultbird' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull"><!-- gc:columns {"align":"wide","style":{"spacing":{"blockGap":"5%"}}} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"70%"} -->
					<div class="gc-block-column" style="flex-basis:70%">

					<!-- gc:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(3rem, 6vw, 4.5rem)"},"spacing":{"margin":{"bottom":"0px"}}}} -->
				<h1 class="alignwide" style="font-size:clamp(3rem, 6vw, 4.5rem);margin-bottom:0px">' . gc_kses_post( __( '<em>Flutter</em>，与鸟类相关的短暂事件的集合', 'defaultbird' ) ) . '</h1>
					<!-- /gc:heading --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":""} -->
					<div class="gc-block-column"></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:columns {"align":"wide","style":{"spacing":{"blockGap":"5%"}}} -->
					<div class="gc-block-columns alignwide"><!-- gc:column {"width":"70%","style":{"spacing":{"padding":{"bottom":"32px"}}}} -->
					<div class="gc-block-column" style="padding-bottom:32px;flex-basis:70%"><!-- gc:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-salmon.jpg" alt="' . esc_attr__( '树枝上的鸟的图像', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image --></div>
					<!-- /gc:column -->

					<!-- gc:column {"width":""} -->
					<div class="gc-block-column"><!-- gc:image {"width":100,"height":47,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-binoculars.png" alt="' . esc_attr__( '双筒望远镜的图标。', 'defaultbird' ) . '" width="100" height="47"/></figure>
					<!-- /gc:image -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:heading {"level":3,"fontSize":"large"} -->
					<h3 class="has-large-font-size"><em>' . esc_html__( '日期', 'defaultbird' ) . '</em></h3>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '2021年2月12日', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:heading {"level":3,"fontSize":"large"} -->
					<h3 class="has-large-font-size"><em>' . esc_html__( '位置', 'defaultbird' ) . '</em></h3>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . gc_kses_post( __( '大剧院<br>154东大街<br>马里兰州， 12345', 'defaultbird' ) ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
