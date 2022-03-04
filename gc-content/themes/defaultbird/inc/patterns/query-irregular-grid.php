<?php
/**
 * Irregular grid of posts block pattern
 */
return array(
	'title'      => __( '不规则文章网格', 'defaultbird' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- gc:group {"align":"wide"} -->
					<div class="gc-block-group alignwide"><!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"1","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"2","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":128} -->
					<div style="height:128px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"3","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"4","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":96} -->
					<div style="height:96px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"5","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":160} -->
					<div style="height:160px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns -->

					<!-- gc:columns {"align":"wide"} -->
					<div class="gc-block-columns alignwide"><!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"6","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"7","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":160} -->
					<div style="height:160px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column -->

					<!-- gc:column -->
					<div class="gc-block-column"><!-- gc:query {"query":{"offset":"8","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="gc-block-query alignwide"><!-- gc:post-template {"align":"wide"} -->
					<!-- gc:spacer {"height":96} -->
					<div style="height:96px" aria-hidden="true" class="gc-block-spacer"></div>
					<!-- /gc:spacer -->

					<!-- gc:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- gc:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- gc:post-excerpt /-->

					<!-- gc:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /gc:post-template --></div>
					<!-- /gc:query --></div>
					<!-- /gc:column --></div>
					<!-- /gc:columns --></div>
					<!-- /gc:group -->',
);
