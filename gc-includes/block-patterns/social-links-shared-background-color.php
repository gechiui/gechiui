<?php
/**
 * 具有共享背景色的社交链接.
 *
 * @package GeChiUI
 */

return array(
	'title'         => _x( '具有共享背景色的社交链接', 'Block pattern title' ),
	'categories'    => array( 'buttons' ),
	'blockTypes'    => array( 'core/social-links' ),
	'viewportWidth' => 500,
	'content'       => '<!-- gc:social-links {"customIconColor":"#ffffff","iconColorValue":"#ffffff","customIconBackgroundColor":"#3962e3","iconBackgroundColorValue":"#3962e3","className":"has-icon-color"} -->
						<ul class="gc-block-social-links has-icon-color has-icon-background-color"><!-- gc:social-link {"url":"https://www.gechiui.com","service":"gechiui"} /-->
						<!-- gc:social-link {"url":"#","service":"chain"} /-->
						<!-- gc:social-link {"url":"#","service":"mail"} /--></ul>
						<!-- /gc:social-links -->',
);
