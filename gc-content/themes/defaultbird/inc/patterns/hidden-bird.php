<?php
/**
 * Bird image
 *
 * This pattern is used only to reference a dynamic image URL.
 * It does not appear in the inserter.
 */
return array(
	'title'    => __( '页眉与鸟图', 'defaultbird' ),
	'inserter' => false,
	'content'  => '<!-- gc:image {"align":"wide","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="gc-block-image alignwide size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-d.png" alt="' . esc_attr__( '鸟儿飞翔的插图。', 'defaultbird' ) . '"/></figure>
					<!-- /gc:image -->',
);
