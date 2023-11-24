<?php
/**
 * Server-side rendering of the `core/template-part` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/template-part` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function render_block_core_template_part( $attributes ) {
	static $seen_ids = array();

	$template_part_id = null;
	$content          = null;
	$area             = GC_TEMPLATE_PART_AREA_UNCATEGORIZED;

	if (
		isset( $attributes['slug'] ) &&
		isset( $attributes['theme'] ) &&
		get_stylesheet() === $attributes['theme']
	) {
		$template_part_id    = $attributes['theme'] . '//' . $attributes['slug'];
		$template_part_query = new GC_Query(
			array(
				'post_type'           => 'gc_template_part',
				'post_status'         => 'publish',
				'post_name__in'       => array( $attributes['slug'] ),
				'tax_query'           => array(
					array(
						'taxonomy' => 'gc_theme',
						'field'    => 'name',
						'terms'    => $attributes['theme'],
					),
				),
				'posts_per_page'      => 1,
				'no_found_rows'       => true,
				'lazy_load_term_meta' => false, // Do not lazy load term meta, as template parts only have one term.
			)
		);
		$template_part_post  = $template_part_query->have_posts() ? $template_part_query->next_post() : null;
		if ( $template_part_post ) {
			// A published post might already exist if this template part was customized elsewhere
			// or if it's part of a customized template.
			$content    = $template_part_post->post_content;
			$area_terms = get_the_terms( $template_part_post, 'gc_template_part_area' );
			if ( ! is_gc_error( $area_terms ) && false !== $area_terms ) {
				$area = $area_terms[0]->name;
			}
			/**
			 * Fires when a block template part is loaded from a template post stored in the database.
			 *
			 * @since 5.9.0
			 *
			 * @param string  $template_part_id   The requested template part namespaced to the theme.
			 * @param array   $attributes         The block attributes.
			 * @param GC_Post $template_part_post The template part post object.
			 * @param string  $content            The template part content.
			 */
			do_action( 'render_block_core_template_part_post', $template_part_id, $attributes, $template_part_post, $content );
		} else {
			$template_part_file_path = '';
			// Else, if the template part was provided by the active theme,
			// render the corresponding file content.
			if ( 0 === validate_file( $attributes['slug'] ) ) {
				$block_template_file = _get_block_template_file( 'gc_template_part', $attributes['slug'] );
				if ( $block_template_file ) {
					$template_part_file_path = $block_template_file['path'];
					$content                 = (string) file_get_contents( $template_part_file_path );
					$content                 = '' !== $content ? _inject_theme_attribute_in_block_template_content( $content ) : '';
					if ( isset( $block_template_file['area'] ) ) {
						$area = $block_template_file['area'];
					}
				}
			}

			if ( '' !== $content && null !== $content ) {
				/**
				 * Fires when a block template part is loaded from a template part in the theme.
				 *
				 * @since 5.9.0
				 *
				 * @param string $template_part_id        The requested template part namespaced to the theme.
				 * @param array  $attributes              The block attributes.
				 * @param string $template_part_file_path Absolute path to the template path.
				 * @param string $content                 The template part content.
				 */
				do_action( 'render_block_core_template_part_file', $template_part_id, $attributes, $template_part_file_path, $content );
			} else {
				/**
				 * Fires when a requested block template part does not exist in the database nor in the theme.
				 *
				 * @since 5.9.0
				 *
				 * @param string $template_part_id        The requested template part namespaced to the theme.
				 * @param array  $attributes              The block attributes.
				 * @param string $template_part_file_path Absolute path to the not found template path.
				 */
				do_action( 'render_block_core_template_part_none', $template_part_id, $attributes, $template_part_file_path );
			}
		}
	}

	// GC_DEBUG_DISPLAY must only be honored when GC_DEBUG. This precedent
	// is set in `gc_debug_mode()`.
	$is_debug = GC_DEBUG && GC_DEBUG_DISPLAY;

	if ( is_null( $content ) && $is_debug ) {
		if ( ! isset( $attributes['slug'] ) ) {
			// If there is no slug this is a placeholder and we dont want to return any message.
			return;
		}
		return sprintf(
			/* translators: %s: Template part slug. */
			__( '模板组件已被删除或不可用：%s' ),
			$attributes['slug']
		);
	}

	if ( isset( $seen_ids[ $template_part_id ] ) ) {
		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[区块渲染已停止]' ) :
			'';
	}

	// Look up area definition.
	$area_definition = null;
	$defined_areas   = get_allowed_block_template_part_areas();
	foreach ( $defined_areas as $defined_area ) {
		if ( $defined_area['area'] === $area ) {
			$area_definition = $defined_area;
			break;
		}
	}

	// If $area is not allowed, set it back to the uncategorized default.
	if ( ! $area_definition ) {
		$area = GC_TEMPLATE_PART_AREA_UNCATEGORIZED;
	}

	// Run through the actions that are typically taken on the_content.
	$content                       = shortcode_unautop( $content );
	$content                       = do_shortcode( $content );
	$seen_ids[ $template_part_id ] = true;
	$content                       = do_blocks( $content );
	unset( $seen_ids[ $template_part_id ] );
	$content = gctexturize( $content );
	$content = convert_smilies( $content );
	$content = gc_filter_content_tags( $content, "template_part_{$area}" );

	// Handle embeds for block template parts.
	global $gc_embed;
	$content = $gc_embed->autoembed( $content );

	if ( empty( $attributes['tagName'] ) ) {
		$area_tag = 'div';
		if ( $area_definition && isset( $area_definition['area_tag'] ) ) {
			$area_tag = $area_definition['area_tag'];
		}
		$html_tag = $area_tag;
	} else {
		$html_tag = esc_attr( $attributes['tagName'] );
	}
	$wrapper_attributes = get_block_wrapper_attributes();

	return "<$html_tag $wrapper_attributes>" . str_replace( ']]>', ']]&gt;', $content ) . "</$html_tag>";
}

/**
 * Returns an array of area variation objects for the template part block.
 *
 * @param array $instance_variations The variations for instances.
 *
 * @return array Array containing the block variation objects.
 */
function build_template_part_block_area_variations( $instance_variations ) {
	$variations    = array();
	$defined_areas = get_allowed_block_template_part_areas();

	foreach ( $defined_areas as $area ) {
		if ( 'uncategorized' !== $area['area'] ) {
			$has_instance_for_area = false;
			foreach ( $instance_variations as $variation ) {
				if ( $variation['attributes']['area'] === $area['area'] ) {
					$has_instance_for_area = true;
					break;
				}
			}

			$scope = $has_instance_for_area ? array() : array( 'inserter' );

			$variations[] = array(
				'name'        => 'area_' . $area['area'],
				'title'       => $area['label'],
				'description' => $area['description'],
				'attributes'  => array(
					'area' => $area['area'],
				),
				'scope'       => $scope,
				'icon'        => $area['icon'],
			);
		}
	}
	return $variations;
}

/**
 * Returns an array of instance variation objects for the template part block
 *
 * @return array Array containing the block variation objects.
 */
function build_template_part_block_instance_variations() {
	// Block themes are unavailable during installation.
	if ( gc_installing() ) {
		return array();
	}

	if ( ! current_theme_supports( 'block-templates' ) && ! current_theme_supports( 'block-template-parts' ) ) {
		return array();
	}

	$variations     = array();
	$template_parts = get_block_templates(
		array(
			'post_type' => 'gc_template_part',
		),
		'gc_template_part'
	);

	$defined_areas = get_allowed_block_template_part_areas();
	$icon_by_area  = array_combine( array_column( $defined_areas, 'area' ), array_column( $defined_areas, 'icon' ) );

	foreach ( $template_parts as $template_part ) {
		$variations[] = array(
			'name'        => 'instance_' . sanitize_title( $template_part->slug ),
			'title'       => $template_part->title,
			// If there's no description for the template part don't show the
			// block description. This is a bit hacky, but prevent the fallback
			// by using a non-breaking space so that the value of description
			// isn't falsey.
			'description' => $template_part->description || '&nbsp;',
			'attributes'  => array(
				'slug'  => $template_part->slug,
				'theme' => $template_part->theme,
				'area'  => $template_part->area,
			),
			'scope'       => array( 'inserter' ),
			'icon'        => isset( $icon_by_area[ $template_part->area ] ) ? $icon_by_area[ $template_part->area ] : null,
			'example'     => array(
				'attributes' => array(
					'slug'  => $template_part->slug,
					'theme' => $template_part->theme,
					'area'  => $template_part->area,
				),
			),
		);
	}
	return $variations;
}

/**
 * Returns an array of all template part block variations.
 *
 * @return array Array containing the block variation objects.
 */
function build_template_part_block_variations() {
	$instance_variations = build_template_part_block_instance_variations();
	$area_variations     = build_template_part_block_area_variations( $instance_variations );
	return array_merge( $area_variations, $instance_variations );
}

/**
 * Registers the `core/template-part` block on the server.
 */
function register_block_core_template_part() {
	register_block_type_from_metadata(
		__DIR__ . '/template-part',
		array(
			'render_callback' => 'render_block_core_template_part',
			'variations'      => build_template_part_block_variations(),
		)
	);
}
add_action( 'init', 'register_block_core_template_part' );
