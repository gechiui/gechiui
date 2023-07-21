<?php
/**
 * Default footer
 */
return array(
	'title'      => __( '默认页脚', 'gcoa' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- gc:group {"align":"full","layout":{"inherit":true}} -->
					<footer class="footer"><!-- gc:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="footer-content"><p class="m-b-0">©2022 - 格尺科技 提供技术支持</p>

					<!-- gc:paragraph {"align":"right"} -->
					<span>
                        <a href="#" class="text-gray m-r-15">服务条款</a>' .
					sprintf(
						/* Translators: GeChiUI link. */
						'<a href="' . esc_url( __( 'https://www.gechiui.com', 'gcoa' ) ) . '" rel="nofollow">GeChiUI</a>'
					) . '
                    </span>
					<!-- /gc:paragraph --></div>
					<!-- /gc:group --></footer>
					<!-- /gc:group -->',
);
