<?php
/**
 * Layout block support flag.
 *
 * @package GeChiUI
 * @since 5.8.0
 */

/**
 * Registers the layout block attribute for block types that support it.
 *
 * @since 5.8.0
 * @access private
 *
 * @param GC_Block_Type $block_type Block Type.
 */
function gc_register_layout_support( $block_type ) {
	$support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );
	if ( $support_layout ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'layout', $block_type->attributes ) ) {
			$block_type->attributes['layout'] = array(
				'type' => 'object',
			);
		}
	}
}

/**
 * Generates the CSS corresponding to the provided layout.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $selector              CSS selector.
 * @param array  $layout                Layout object. The one that is passed has already checked
 *                                      the existence of default block layout.
 * @param bool   $has_block_gap_support Whether the theme has support for the block gap.
 * @param string $gap_value             The block gap value to apply.
 * @return string CSS style.
 */
function gc_get_layout_style( $selector, $layout, $has_block_gap_support = false, $gap_value = null ) {
	$layout_type = isset( $layout['type'] ) ? $layout['type'] : 'default';

	$style = '';
	if ( 'default' === $layout_type ) {
		$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
		$wide_size    = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';

		$all_max_width_value  = $content_size ? $content_size : $wide_size;
		$wide_max_width_value = $wide_size ? $wide_size : $content_size;

		// Make sure there is a single CSS rule, and all tags are stripped for security.
		$all_max_width_value  = safecss_filter_attr( explode( ';', $all_max_width_value )[0] );
		$wide_max_width_value = safecss_filter_attr( explode( ';', $wide_max_width_value )[0] );

		$style = '';
		if ( $content_size || $wide_size ) {
			$style  = "$selector > * {";
			$style .= 'max-width: ' . esc_html( $all_max_width_value ) . ';';
			$style .= 'margin-left: auto !important;';
			$style .= 'margin-right: auto !important;';
			$style .= '}';

			$style .= "$selector > .alignwide { max-width: " . esc_html( $wide_max_width_value ) . ';}';
			$style .= "$selector .alignfull { max-width: none; }";
		}

		$style .= "$selector .alignleft { float: left; margin-right: 2em; }";
		$style .= "$selector .alignright { float: right; margin-left: 2em; }";
		if ( $has_block_gap_support ) {
			$gap_style = $gap_value ? $gap_value : 'var( --gc--style--block-gap )';
			$style    .= "$selector > * { margin-top: 0; margin-bottom: 0; }";
			$style    .= "$selector > * + * { margin-top: $gap_style;  margin-bottom: 0; }";
		}
	} elseif ( 'flex' === $layout_type ) {
		$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

		$justify_content_options = array(
			'left'   => 'flex-start',
			'right'  => 'flex-end',
			'center' => 'center',
		);

		if ( 'horizontal' === $layout_orientation ) {
			$justify_content_options += array( 'space-between' => 'space-between' );
		}

		$flex_wrap_options = array( 'wrap', 'nowrap' );
		$flex_wrap         = ! empty( $layout['flexWrap'] ) && in_array( $layout['flexWrap'], $flex_wrap_options, true ) ?
			$layout['flexWrap'] :
			'wrap';

		$style  = "$selector {";
		$style .= 'display: flex;';
		if ( $has_block_gap_support ) {
			$gap_style = $gap_value ? $gap_value : 'var( --gc--style--block-gap, 0.5em )';
			$style    .= "gap: $gap_style;";
		} else {
			$style .= 'gap: 0.5em;';
		}
		$style .= "flex-wrap: $flex_wrap;";
		$style .= 'align-items: center;';
		if ( 'horizontal' === $layout_orientation ) {
			$style .= 'align-items: center;';
			/**
			 * Add this style only if is not empty for backwards compatibility,
			 * since we intend to convert blocks that had flex layout implemented
			 * by custom css.
			 */
			if ( ! empty( $layout['justifyContent'] ) && array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
				$style .= "justify-content: {$justify_content_options[ $layout['justifyContent'] ]};";
			}
		} else {
			$style .= 'flex-direction: column;';
			if ( ! empty( $layout['justifyContent'] ) && array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
				$style .= "align-items: {$justify_content_options[ $layout['justifyContent'] ]};";
			}
		}
		$style .= '}';

		$style .= "$selector > * { margin: 0; }";
	}

	return $style;
}

/**
 * Renders the layout config to the block wrapper.
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function gc_render_layout_support_flag( $block_content, $block ) {
	$block_type     = GC_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );

	if ( ! $support_layout ) {
		return $block_content;
	}

	$block_gap             = gc_get_global_settings( array( 'spacing', 'blockGap' ) );
	$default_layout        = gc_get_global_settings( array( 'layout' ) );
	$has_block_gap_support = isset( $block_gap ) ? null !== $block_gap : false;
	$default_block_layout  = _gc_array_get( $block_type->supports, array( '__experimentalLayout', 'default' ), array() );
	$used_layout           = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $default_block_layout;
	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] ) {
		if ( ! $default_layout ) {
			return $block_content;
		}
		$used_layout = $default_layout;
	}

	$class_name = gc_unique_id( 'gc-container-' );
	$gap_value  = _gc_array_get( $block, array( 'attrs', 'style', 'spacing', 'blockGap' ) );
	// Skip if gap value contains unsupported characters.
	// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
	// because we only want to match against the value, not the CSS attribute.
	$gap_value = preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null : $gap_value;
	$style     = gc_get_layout_style( ".$class_name", $used_layout, $has_block_gap_support, $gap_value );
	// This assumes the hook only applies to blocks with a single wrapper.
	// I think this is a reasonable limitation for that particular hook.
	$content = preg_replace(
		'/' . preg_quote( 'class="', '/' ) . '/',
		'class="' . esc_attr( $class_name ) . ' ',
		$block_content,
		1
	);

	gc_enqueue_block_support_styles( $style );

	return $content;
}

// Register the block support.
GC_Block_Supports::get_instance()->register(
	'layout',
	array(
		'register_attribute' => 'gc_register_layout_support',
	)
);
add_filter( 'render_block', 'gc_render_layout_support_flag', 10, 2 );

/**
 * For themes without theme.json file, make sure
 * to restore the inner div for the group block
 * to avoid breaking styles relying on that div.
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function gc_restore_group_inner_container( $block_content, $block ) {
	$tag_name                         = isset( $block['attrs']['tagName'] ) ? $block['attrs']['tagName'] : 'div';
	$group_with_inner_container_regex = sprintf(
		'/(^\s*<%1$s\b[^>]*gc-block-group(\s|")[^>]*>)(\s*<div\b[^>]*gc-block-group__inner-container(\s|")[^>]*>)((.|\S|\s)*)/U',
		preg_quote( $tag_name, '/' )
	);

	if (
		'core/group' !== $block['blockName'] ||
		GC_Theme_JSON_Resolver::theme_has_support() ||
		1 === preg_match( $group_with_inner_container_regex, $block_content ) ||
		( isset( $block['attrs']['layout']['type'] ) && 'default' !== $block['attrs']['layout']['type'] )
	) {
		return $block_content;
	}

	$replace_regex   = sprintf(
		'/(^\s*<%1$s\b[^>]*gc-block-group[^>]*>)(.*)(<\/%1$s>\s*$)/ms',
		preg_quote( $tag_name, '/' )
	);
	$updated_content = preg_replace_callback(
		$replace_regex,
		static function( $matches ) {
			return $matches[1] . '<div class="gc-block-group__inner-container">' . $matches[2] . '</div>' . $matches[3];
		},
		$block_content
	);
	return $updated_content;
}

add_filter( 'render_block', 'gc_restore_group_inner_container', 10, 2 );
