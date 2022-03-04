<?php
/**
 * Large list of names block pattern
 */
return array(
	'title'      => __( '人员罗列', 'defaultbird' ),
	'categories' => array( 'featured', 'text' ),
	'content'    => '<!-- gc:group {"align":"full","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem"}},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"backgroundColor":"tertiary","textColor":"primary","layout":{"inherit":true}} -->
					<div class="gc-block-group alignfull has-primary-color has-tertiary-background-color has-text-color has-background has-link-color" style="padding-top:6rem;padding-bottom:6rem"><!-- gc:group {"align":"wide"} -->
					<div class="gc-block-group alignwide"><!-- gc:image {"width":175,"height":82,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-binoculars.png" alt="' . esc_attr__( '代表双筒望远镜的图标。', 'defaultbird' ) . '" width="175" height="82"/></figure>
					<!-- /gc:image --></div>
					<!-- /gc:group -->

					<!-- gc:group {"align":"wide"} -->
					<div class="gc-block-group alignwide"><!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:paragraph {"style":{"typography":{"fontWeight":"300"}},"fontSize":"x-large"} -->
					<p class="has-x-large-font-size" style="font-weight:300">' . esc_html__( 'Jesús Rodriguez, Doug Stilton, Emery Driscoll, Megan Perry, Rowan Price, Angelo Tso, Edward Stilton, Amy Jensen, Boston Bell, Shay Ford, Lee Cunningham, Evelynn Ray, Landen Reese, Ewan Hart, Jenna Chan, Phoenix Murray, Mel Saunders, Aldo Davidson, Zain Hall.', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"primary","textColor":"background"} -->
					<div class="gc-block-button"><a class="gc-block-button__link has-background-color has-primary-background-color has-text-color has-background">' . esc_html__( '阅读更多', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons --></div>
					<!-- /gc:group --></div>
					<!-- /gc:group -->',
);
