<?php
/**
 * Server-side rendering of the `core/social-link` blocks.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/social-link` block on server.
 *
 * @param Array    $attributes The block attributes.
 * @param String   $content    InnerBlocks content of the Block.
 * @param GC_Block $block      Block object.
 *
 * @return string Rendered HTML of the referenced block.
 */
function render_block_core_social_link( $attributes, $content, $block ) {
	$open_in_new_tab = isset( $block->context['openInNewTab'] ) ? $block->context['openInNewTab'] : false;

	$service = ( isset( $attributes['service'] ) ) ? $attributes['service'] : 'Icon';
	$url     = ( isset( $attributes['url'] ) ) ? $attributes['url'] : false;
	$label   = ( isset( $attributes['label'] ) ) ? $attributes['label'] : sprintf(
		/* translators: %1$s: Social-network name. %2$s: URL. */
		__( '%1$s: %2$s' ),
		block_core_social_link_get_name( $service ),
		$url
	);
	$class_name = isset( $attributes['className'] ) ? ' ' . $attributes['className'] : false;

	// Don't render a link if there is no URL set.
	if ( ! $url ) {
		return '';
	}

	$attribute = '';
	if ( $open_in_new_tab ) {
		$attribute = 'rel="noopener nofollow" target="_blank"';
	}

	$icon               = block_core_social_link_get_icon( $service );
	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'gc-social-link gc-social-link-' . $service . $class_name,
			'style' => block_core_social_link_get_color_styles( $block->context ),
		)
	);

	return '<li ' . $wrapper_attributes . '><a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '" ' . $attribute . ' class="gc-block-social-link-anchor"> ' . $icon . '</a></li>';
}

/**
 * Registers the `core/social-link` blocks.
 */
function register_block_core_social_link() {
	register_block_type_from_metadata(
		ABSPATH . 'assets/blocks/social-link',
		array(
			'render_callback' => 'render_block_core_social_link',
		)
	);
}
add_action( 'init', 'register_block_core_social_link' );


/**
 * Returns the SVG for social link.
 *
 * @param string $service The service icon.
 *
 * @return string SVG Element for service icon.
 */
function block_core_social_link_get_icon( $service ) {
	$services = block_core_social_link_services();
	if ( isset( $services[ $service ] ) && isset( $services[ $service ]['icon'] ) ) {
		return $services[ $service ]['icon'];
	}

	return $services['share']['icon'];
}

/**
 * Returns the brand name for social link.
 *
 * @param string $service The service icon.
 *
 * @return string Brand label.
 */
function block_core_social_link_get_name( $service ) {
	$services = block_core_social_link_services();
	if ( isset( $services[ $service ] ) && isset( $services[ $service ]['name'] ) ) {
		return $services[ $service ]['name'];
	}

	return $services['share']['name'];
}

/**
 * Returns the SVG for social link.
 *
 * @param string $service The service slug to extract data from.
 * @param string $field The field ('name', 'icon', etc) to extract for a service.
 *
 * @return array|string
 */
function block_core_social_link_services( $service = '', $field = '' ) {
	$services_data = array(
		'chain'         => array(
			'name' => 'Link',
			'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M19.647,16.706a1.134,1.134,0,0,0-.343-.833l-2.549-2.549a1.134,1.134,0,0,0-.833-.343,1.168,1.168,0,0,0-.883.392l.233.226q.2.189.264.264a2.922,2.922,0,0,1,.184.233.986.986,0,0,1,.159.312,1.242,1.242,0,0,1,.043.337,1.172,1.172,0,0,1-1.176,1.176,1.237,1.237,0,0,1-.337-.043,1,1,0,0,1-.312-.159,2.76,2.76,0,0,1-.233-.184q-.073-.068-.264-.264l-.226-.233a1.19,1.19,0,0,0-.4.895,1.134,1.134,0,0,0,.343.833L15.837,19.3a1.13,1.13,0,0,0,.833.331,1.18,1.18,0,0,0,.833-.318l1.8-1.789a1.12,1.12,0,0,0,.343-.821Zm-8.615-8.64a1.134,1.134,0,0,0-.343-.833L8.163,4.7a1.134,1.134,0,0,0-.833-.343,1.184,1.184,0,0,0-.833.331L4.7,6.473a1.12,1.12,0,0,0-.343.821,1.134,1.134,0,0,0,.343.833l2.549,2.549a1.13,1.13,0,0,0,.833.331,1.184,1.184,0,0,0,.883-.38L8.728,10.4q-.2-.189-.264-.264A2.922,2.922,0,0,1,8.28,9.9a.986.986,0,0,1-.159-.312,1.242,1.242,0,0,1-.043-.337A1.172,1.172,0,0,1,9.254,8.079a1.237,1.237,0,0,1,.337.043,1,1,0,0,1,.312.159,2.761,2.761,0,0,1,.233.184q.073.068.264.264l.226.233a1.19,1.19,0,0,0,.4-.895ZM22,16.706a3.343,3.343,0,0,1-1.042,2.488l-1.8,1.789a3.536,3.536,0,0,1-4.988-.025l-2.525-2.537a3.384,3.384,0,0,1-1.017-2.488,3.448,3.448,0,0,1,1.078-2.561l-1.078-1.078a3.434,3.434,0,0,1-2.549,1.078,3.4,3.4,0,0,1-2.5-1.029L3.029,9.794A3.4,3.4,0,0,1,2,7.294,3.343,3.343,0,0,1,3.042,4.806l1.8-1.789A3.384,3.384,0,0,1,7.331,2a3.357,3.357,0,0,1,2.5,1.042l2.525,2.537a3.384,3.384,0,0,1,1.017,2.488,3.448,3.448,0,0,1-1.078,2.561l1.078,1.078a3.551,3.551,0,0,1,5.049-.049l2.549,2.549A3.4,3.4,0,0,1,22,16.706Z"></path></svg>',
		),
		'feed'          => array(
			'name' => 'RSS Feed',
			'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M2,8.667V12c5.515,0,10,4.485,10,10h3.333C15.333,14.637,9.363,8.667,2,8.667z M2,2v3.333 c9.19,0,16.667,7.477,16.667,16.667H22C22,10.955,13.045,2,2,2z M4.5,17C3.118,17,2,18.12,2,19.5S3.118,22,4.5,22S7,20.88,7,19.5 S5.882,17,4.5,17z"></path></svg>',
		),
		'mail'          => array(
			'name' => 'Mail',
			'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M20,4H4C2.895,4,2,4.895,2,6v12c0,1.105,0.895,2,2,2h16c1.105,0,2-0.895,2-2V6C22,4.895,21.105,4,20,4z M20,8.236l-8,4.882 L4,8.236V6h16V8.236z"></path></svg>',
		),
		'gechiui'     => array(
			'name' => 'GeChiUI',
			'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M12.158,12.786L9.46,20.625c0.806,0.237,1.657,0.366,2.54,0.366c1.047,0,2.051-0.181,2.986-0.51 c-0.024-0.038-0.046-0.079-0.065-0.124L12.158,12.786z M3.009,12c0,3.559,2.068,6.634,5.067,8.092L3.788,8.341 C3.289,9.459,3.009,10.696,3.009,12z M18.069,11.546c0-1.112-0.399-1.881-0.741-2.48c-0.456-0.741-0.883-1.368-0.883-2.109 c0-0.826,0.627-1.596,1.51-1.596c0.04,0,0.078,0.005,0.116,0.007C16.472,3.904,14.34,3.009,12,3.009 c-3.141,0-5.904,1.612-7.512,4.052c0.211,0.007,0.41,0.011,0.579,0.011c0.94,0,2.396-0.114,2.396-0.114 C7.947,6.93,8.004,7.642,7.52,7.699c0,0-0.487,0.057-1.029,0.085l3.274,9.739l1.968-5.901l-1.401-3.838 C9.848,7.756,9.389,7.699,9.389,7.699C8.904,7.67,8.961,6.93,9.446,6.958c0,0,1.484,0.114,2.368,0.114 c0.94,0,2.397-0.114,2.397-0.114c0.485-0.028,0.542,0.684,0.057,0.741c0,0-0.488,0.057-1.029,0.085l3.249,9.665l0.897-2.996 C17.841,13.284,18.069,12.316,18.069,11.546z M19.889,7.686c0.039,0.286,0.06,0.593,0.06,0.924c0,0.912-0.171,1.938-0.684,3.22 l-2.746,7.94c2.673-1.558,4.47-4.454,4.47-7.771C20.991,10.436,20.591,8.967,19.889,7.686z M12,22C6.486,22,2,17.514,2,12 C2,6.486,6.486,2,12,2c5.514,0,10,4.486,10,10C22,17.514,17.514,22,12,22z"></path></svg>',
		),
	);

	if ( ! empty( $service )
		&& ! empty( $field )
		&& isset( $services_data[ $service ] )
		&& ( 'icon' === $field || 'name' === $field )
	) {
		return $services_data[ $service ][ $field ];
	} elseif ( ! empty( $service ) && isset( $services_data[ $service ] ) ) {
		return $services_data[ $service ];
	}

	return $services_data;
}

/**
 * Returns CSS styles for icon and icon background colors.
 *
 * @param array $context Block context passed to Social Link.
 *
 * @return string Inline CSS styles for link's icon and background colors.
 */
function block_core_social_link_get_color_styles( $context ) {
	$styles = array();

	if ( array_key_exists( 'iconColorValue', $context ) ) {
		$styles[] = 'color: ' . $context['iconColorValue'] . '; ';
	}

	if ( array_key_exists( 'iconBackgroundColorValue', $context ) ) {
		$styles[] = 'background-color: ' . $context['iconBackgroundColorValue'] . '; ';
	}

	return implode( '', $styles );
}
