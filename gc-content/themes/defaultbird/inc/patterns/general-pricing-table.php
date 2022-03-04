<?php
/**
 * Pricing table block pattern
 */
return array(
	'title'      => __( '定价表', 'defaultbird' ),
	'categories' => array( 'featured', 'columns', 'buttons' ),
	'content'    => '<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:heading {"style":{"typography":{"fontSize":"var(--gc--custom--typography--font-size--gigantic, clamp(2.75rem, 6vw, 3.25rem))","lineHeight":"0.5"}}} -->
					<h2 id="1" style="font-size:var(--gc--custom--typography--font-size--gigantic, clamp(2.75rem, 6vw, 3.25rem));line-height:0.5">' . esc_html( _x( '1', 'First item in a numbered list.', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:heading {"level":3,"fontSize":"x-large"} -->
					<h3 class="has-x-large-font-size" id="pigeon"><em>' . esc_html__( '鸽子', 'defaultbird' ) . '</em></h3>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '通过加入鸽子级别的团队，帮助支持我们不断增长的社区。您的支持将有助于支付我们的作者费用，您还可以访问我们的独家通讯。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"foreground","width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link has-foreground-background-color has-background">' . esc_html__( '¥25元', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:heading {"style":{"typography":{"fontSize":"clamp(2.75rem, 6vw, 3.25rem)","lineHeight":"0.5"}}} -->
					<h2 id="2" style="font-size:clamp(2.75rem, 6vw, 3.25rem);line-height:0.5">' . esc_html( _x( '2', 'Second item in a numbered list.', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:heading {"level":3,"fontSize":"x-large"} -->
					<h3 class="has-x-large-font-size" id="sparrow"><meta charset="utf-8"><em>' . esc_html__( '麻雀', 'defaultbird' ) . '</em></h3>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '加入麻雀级别，成为我们羊群中的一员！你会收到我们的通讯，还有一个鸟别针，当你在大自然中时可以自豪地佩戴。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"foreground","width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link has-foreground-background-color has-background">' . esc_html__( '¥75元', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:separator {"className":"is-style-wide"} -->
					<hr class="gc-block-separator is-style-wide"/>
					<!-- /gc:separator -->

					<!-- gc:heading {"style":{"typography":{"fontSize":"clamp(2.75rem, 6vw, 3.25rem)","lineHeight":"0.5"}}} -->
					<h2 id="3" style="font-size:clamp(2.75rem, 6vw, 3.25rem);line-height:0.5">' . esc_html( _x( '3', 'Third item in a numbered list.', 'defaultbird' ) ) . '</h2>
					<!-- /gc:heading -->

					<!-- gc:heading {"level":3,"fontSize":"x-large"} -->
					<h3 class="has-x-large-font-size" id="falcon"><meta charset="utf-8"><em>' . esc_html__( '猎鹰', 'defaultbird' ) . '</em></h3>
					<!-- /gc:heading -->

					<!-- gc:paragraph -->
					<p>' . esc_html__( '通过加入猎鹰队，为我们的社区发挥领导作用。这一级别为您在我们的董事会赢得了一个席位，您可以在那里帮助规划未来的观鸟探险。', 'defaultbird' ) . '</p>
					<!-- /gc:paragraph -->

					<!-- gc:buttons -->
					<div class="gc-block-buttons"><!-- gc:button {"backgroundColor":"foreground","width":100} -->
					<div class="gc-block-button has-custom-width gc-block-button__width-100"><a class="gc-block-button__link has-foreground-background-color has-background">' . esc_html__( '¥150元', 'defaultbird' ) . '</a></div>
					<!-- /gc:button --></div>
					<!-- /gc:buttons -->

					<!-- gc:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->',
);
