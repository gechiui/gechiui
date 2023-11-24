<?php
/**
 * Block template loader functions.
 *
 * @package GeChiUI
 */

/**
 * Adds necessary hooks to resolve '_gc-find-template' requests.
 *
 * @access private
 * @since 5.9.0
 */
function _add_template_loader_filters() {
	if ( isset( $_GET['_gc-find-template'] ) && current_theme_supports( 'block-templates' ) ) {
		add_action( 'pre_get_posts', '_resolve_template_for_new_post' );
	}
}

/**
 * Finds a block template with equal or higher specificity than a given PHP template file.
 *
 * Internally, this communicates the block content that needs to be used by the template canvas through a global variable.
 *
 * @since 5.8.0
 * @since 6.3.0 Added `$_gc_current_template_id` global for editing of current template directly from the admin bar.
 *
 * @global string $_gc_current_template_content
 * @global string $_gc_current_template_id
 *
 * @param string   $template  Path to the template. See locate_template().
 * @param string   $type      Sanitized filename without extension.
 * @param string[] $templates A list of template candidates, in descending order of priority.
 * @return string The path to the Site Editor template canvas file, or the fallback PHP template.
 */
function locate_block_template( $template, $type, array $templates ) {
	global $_gc_current_template_content, $_gc_current_template_id;

	if ( ! current_theme_supports( 'block-templates' ) ) {
		return $template;
	}

	if ( $template ) {
		/*
		 * locate_template() has found a PHP template at the path specified by $template.
		 * That means that we have a fallback candidate if we cannot find a block template
		 * with higher specificity.
		 *
		 * Thus, before looking for matching block themes, we shorten our list of candidate
		 * templates accordingly.
		 */

		// Locate the index of $template (without the theme directory path) in $templates.
		$relative_template_path = str_replace(
			array( get_stylesheet_directory() . '/', get_template_directory() . '/' ),
			'',
			$template
		);
		$index                  = array_search( $relative_template_path, $templates, true );

		// If the template hierarchy algorithm has successfully located a PHP template file,
		// we will only consider block templates with higher or equal specificity.
		$templates = array_slice( $templates, 0, $index + 1 );
	}

	$block_template = resolve_block_template( $type, $templates, $template );

	if ( $block_template ) {
		$_gc_current_template_id = $block_template->id;

		if ( empty( $block_template->content ) && is_user_logged_in() ) {
			$_gc_current_template_content =
			sprintf(
				/* translators: %s: Template title */
				__( '空白模板：%s' ),
				$block_template->title
			);
		} elseif ( ! empty( $block_template->content ) ) {
			$_gc_current_template_content = $block_template->content;
		}
		if ( isset( $_GET['_gc-find-template'] ) ) {
			gc_send_json_success( $block_template );
		}
	} else {
		if ( $template ) {
			return $template;
		}

		if ( 'index' === $type ) {
			if ( isset( $_GET['_gc-find-template'] ) ) {
				gc_send_json_error( array( 'message' => __( '未找到匹配的模板。' ) ) );
			}
		} else {
			return ''; // So that the template loader keeps looking for templates.
		}
	}

	// Add hooks for template canvas.
	// Add viewport meta tag.
	add_action( 'gc_head', '_block_template_viewport_meta_tag', 0 );

	// Render title tag with content, regardless of whether theme has title-tag support.
	remove_action( 'gc_head', '_gc_render_title_tag', 1 );    // Remove conditional title tag rendering...
	add_action( 'gc_head', '_block_template_render_title_tag', 1 ); // ...and make it unconditional.

	// This file will be included instead of the theme's template file.
	return ABSPATH . GCINC . '/template-canvas.php';
}

/**
 * Returns the correct 'gc_template' to render for the request template type.
 *
 * @access private
 * @since 5.8.0
 * @since 5.9.0 Added the `$fallback_template` parameter.
 *
 * @param string   $template_type      The current template type.
 * @param string[] $template_hierarchy The current template hierarchy, ordered by priority.
 * @param string   $fallback_template  A PHP fallback template to use if no matching block template is found.
 * @return GC_Block_Template|null template A template object, or null if none could be found.
 */
function resolve_block_template( $template_type, $template_hierarchy, $fallback_template ) {
	if ( ! $template_type ) {
		return null;
	}

	if ( empty( $template_hierarchy ) ) {
		$template_hierarchy = array( $template_type );
	}

	$slugs = array_map(
		'_strip_template_file_suffix',
		$template_hierarchy
	);

	// Find all potential templates 'gc_template' post matching the hierarchy.
	$query     = array(
		'slug__in' => $slugs,
	);
	$templates = get_block_templates( $query );

	// Order these templates per slug priority.
	// Build map of template slugs to their priority in the current hierarchy.
	$slug_priorities = array_flip( $slugs );

	usort(
		$templates,
		static function ( $template_a, $template_b ) use ( $slug_priorities ) {
			return $slug_priorities[ $template_a->slug ] - $slug_priorities[ $template_b->slug ];
		}
	);

	$theme_base_path        = get_stylesheet_directory() . DIRECTORY_SEPARATOR;
	$parent_theme_base_path = get_template_directory() . DIRECTORY_SEPARATOR;

	// Is the active theme a child theme, and is the PHP fallback template part of it?
	if (
		str_starts_with( $fallback_template, $theme_base_path ) &&
		! str_contains( $fallback_template, $parent_theme_base_path )
	) {
		$fallback_template_slug = substr(
			$fallback_template,
			// Starting position of slug.
			strpos( $fallback_template, $theme_base_path ) + strlen( $theme_base_path ),
			// Remove '.php' suffix.
			-4
		);

		// Is our candidate block template's slug identical to our PHP fallback template's?
		if (
			count( $templates ) &&
			$fallback_template_slug === $templates[0]->slug &&
			'theme' === $templates[0]->source
		) {
			// Unfortunately, we cannot trust $templates[0]->theme, since it will always
			// be set to the active theme's slug by _build_block_template_result_from_file(),
			// even if the block template is really coming from the active theme's parent.
			// (The reason for this is that we want it to be associated with the active theme
			// -- not its parent -- once we edit it and store it to the DB as a gc_template CPT.)
			// Instead, we use _get_block_template_file() to locate the block template file.
			$template_file = _get_block_template_file( 'gc_template', $fallback_template_slug );
			if ( $template_file && get_template() === $template_file['theme'] ) {
				// The block template is part of the parent theme, so we
				// have to give precedence to the child theme's PHP template.
				array_shift( $templates );
			}
		}
	}

	return count( $templates ) ? $templates[0] : null;
}

/**
 * Displays title tag with content, regardless of whether theme has title-tag support.
 *
 * @access private
 * @since 5.8.0
 *
 * @see _gc_render_title_tag()
 */
function _block_template_render_title_tag() {
	echo '<title>' . gc_get_document_title() . '</title>' . "\n";
}

/**
 * Returns the markup for the current template.
 *
 * @access private
 * @since 5.8.0
 *
 * @global string   $_gc_current_template_content
 * @global GC_Embed $gc_embed
 *
 * @return string Block template markup.
 */
function get_the_block_template_html() {
	global $_gc_current_template_content;
	global $gc_embed;

	if ( ! $_gc_current_template_content ) {
		if ( is_user_logged_in() ) {
			return '<h1>' . esc_html__( '未找到匹配的模板' ) . '</h1>';
		}
		return;
	}

	$content = $gc_embed->run_shortcode( $_gc_current_template_content );
	$content = $gc_embed->autoembed( $content );
	$content = shortcode_unautop( $content );
	$content = do_shortcode( $content );
	$content = do_blocks( $content );
	$content = gctexturize( $content );
	$content = convert_smilies( $content );
	$content = gc_filter_content_tags( $content, 'template' );
	$content = str_replace( ']]>', ']]&gt;', $content );

	// Wrap block template in .gc-site-blocks to allow for specific descendant styles
	// (e.g. `.gc-site-blocks > *`).
	return '<div class="gc-site-blocks">' . $content . '</div>';
}

/**
 * Renders a 'viewport' meta tag.
 *
 * This is hooked into {@see 'gc_head'} to decouple its output from the default template canvas.
 *
 * @access private
 * @since 5.8.0
 */
function _block_template_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Strips .php or .html suffix from template file names.
 *
 * @access private
 * @since 5.8.0
 *
 * @param string $template_file Template file name.
 * @return string Template file name without extension.
 */
function _strip_template_file_suffix( $template_file ) {
	return preg_replace( '/\.(php|html)$/', '', $template_file );
}

/**
 * Removes post details from block context when rendering a block template.
 *
 * @access private
 * @since 5.8.0
 *
 * @param array $context Default context.
 *
 * @return array Filtered context.
 */
function _block_template_render_without_post_block_context( $context ) {
	/*
	 * When loading a template directly and not through a page that resolves it,
	 * the top-level post ID and type context get set to that of the template.
	 * Templates are just the structure of a site, and they should not be available
	 * as post context because blocks like Post Content would recurse infinitely.
	 */
	if ( isset( $context['postType'] ) && 'gc_template' === $context['postType'] ) {
		unset( $context['postId'] );
		unset( $context['postType'] );
	}

	return $context;
}

/**
 * Sets the current GC_Query to return auto-draft posts.
 *
 * The auto-draft status indicates a new post, so allow the the GC_Query instance to
 * return an auto-draft post for template resolution when editing a new post.
 *
 * @access private
 * @since 5.9.0
 *
 * @param GC_Query $gc_query Current GC_Query instance, passed by reference.
 */
function _resolve_template_for_new_post( $gc_query ) {
	if ( ! $gc_query->is_main_query() ) {
		return;
	}

	remove_filter( 'pre_get_posts', '_resolve_template_for_new_post' );

	// Pages.
	$page_id = isset( $gc_query->query['page_id'] ) ? $gc_query->query['page_id'] : null;

	// Posts, including custom post types.
	$p = isset( $gc_query->query['p'] ) ? $gc_query->query['p'] : null;

	$post_id = $page_id ? $page_id : $p;
	$post    = get_post( $post_id );

	if (
		$post &&
		'auto-draft' === $post->post_status &&
		current_user_can( 'edit_post', $post->ID )
	) {
		$gc_query->set( 'post_status', 'auto-draft' );
	}
}
