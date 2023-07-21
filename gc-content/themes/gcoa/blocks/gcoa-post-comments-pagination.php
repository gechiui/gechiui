<?php
/**
 * Server-side rendering of the `core/comments-pagination-numbers` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/comments-pagination-numbers` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the pagination numbers for the Query.
 */
function render_block_core_gcoa_post_comments_pagination( $attributes, $content, $block ) {
	$page_key = isset( $block->context['postId'] ) ? 'comments-' . $block->context['postId'] . '-page' : 'comments-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];
	$max_page = isset( $block->context['comments']['pages'] ) ? (int) $block->context['comments']['pages'] : 0;

	// $wrapper_attributes = get_block_wrapper_attributes();
	$content            = '';
	global $post_comments;
	if ( isset( $block->context['comments']['inherit'] ) && $block->context['comments']['inherit'] ) {
		// Take into account if we have set a bigger `max page`
		// than what the comments has.
		$total         = ! $max_page || $max_page > $post_comments->max_num_pages ? $post_comments->max_num_pages : $max_page;
		$paginate_args = array(
			'prev_next' => false,
			'total'     => $total,
		);
		$content       = gcoa_paginate_links( $paginate_args );
	} else {
		$block_comments = new GC_Query( build_comments_vars_from_comments_block( $block, $page ) );
		// `paginate_links` works with the global $post_comments, so we have to
		// temporarily switch it with our custom comments.
		$prev_gc_comments = $post_comments;
		$post_comments      = $block_comments;
		$total         = ! $max_page || $max_page > $post_comments->max_num_pages ? $post_comments->max_num_pages : $max_page;
		$paginate_args = array(
			'base'      => '%_%',
			'format'    => "?$page_key=%#%",
			'current'   => max( 1, $page ),
			'total'     => $total,
			'prev_next' => false,
		);
		if ( 1 !== $page ) {
			/**
			 * `paginate_links` doesn't use the provided `format` when the page is `1`.
			 * This is great for the main comments as it removes the extra comments params
			 * making the URL shorter, but in the case of multiple custom queries is
			 * problematic. It results in returning an empty link which ends up with
			 * a link to the current page.
			 *
			 * A way to address this is to add a `fake` comments arg with no value that
			 * is the same for all custom queries. This way the link is not empty and
			 * preserves all the other existent comments args.
			 *
			 * @see https://developer.gechiui.com/reference/functions/paginate_links/
			 *
			 * The proper fix of this should be in core. Track Ticket:
			 * @see https://core.trac.gechiui.com/ticket/53868
			 *
			 * TODO: After two GC versions (starting from the GC version the core patch landed),
			 * we should remove this and call `paginate_links` with the proper new arg.
			 */
			$paginate_args['add_args'] = array( 'cst' => '' );
		}
		// We still need to preserve `paged` comments param if exists, as is used
		// for Queries that inherit from global context.
		$paged = empty( $_GET['paged'] ) ? null : (int) $_GET['paged'];
		if ( $paged ) {
			$paginate_args['add_args'] = array( 'paged' => $paged );
		}
		$content = gcoa_paginate_links( $paginate_args );
		gc_reset_postdata(); // Restore original Post Data.
		$post_comments = $prev_gc_comments;
	}
	if ( empty( $content ) ) {
		return '';
	}
	return $content;
}

/**
 * Registers the `core/comments-pagination-numbers` block on the server.
 */
function register_block_core_gcoa_post_comments_pagination() {
	register_block_type_from_metadata(
		get_template_directory() . '/blocks/gcoa-post-comments-pagination',
		array(
			'render_callback' => 'render_block_core_gcoa_post_comments_pagination',
		)
	);
}
add_action( 'init', 'register_block_core_gcoa_post_comments_pagination' );

function gcoa_paginate_links( $args = '' ) {
	global $post_comments, $gc_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current comments, if available.
	$total   = isset( $post_comments->max_num_pages ) ? $post_comments->max_num_pages : 1;
	$current = get_comments_var( 'paged' ) ? (int) get_comments_var( 'paged' ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $gc_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $gc_rewrite->using_permalinks() ? user_trailingslashit( $gc_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => __( '&laquo; 上一页' ),
		'next_text'          => __( '下一页 &raquo;' ),
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // Array of comments args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = gc_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional comments vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_comments = isset( $format[1] ) ? $format[1] : '';
		gc_parse_str( $format_comments, $format_args );

		// Find the comments args of the requested URL.
		gc_parse_str( $url_parts[1], $url_comments_args );

		// Remove the format argument from the array of comments arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_comments_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_comments_args ) );
	}

	// Who knows what else people pass in $args.
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds? Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}

	$add_args   = $args['add_args'];
	$r          = '';
	$page_links = array();
	$dots       = false;

	if ( $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_comments_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<li class="page-item"><a class="page-link" href="%s">上一页</a></li>',
			/**
			 * Filters the paginated links for the given archive pages.
			 *
		
			 *
			 * @param string $link The paginated link URL.
			 */
			esc_url( apply_filters( 'paginate_links', $link ) )
		);
	endif;

	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = sprintf(
				'<li class="page-item active"><a class="page-link" href="#">%s</a></li>',
				$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
			);

			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args ) {
					$link = add_comments_arg( $add_args, $link );
				}
				$link .= $args['add_fragment'];

				$page_links[] = sprintf(
					'<li class="page-item"><a class="page-link" href="%s">%s</a><li>',
					/** This filter is documented in gc-includes/general-template.php */
					esc_url( apply_filters( 'paginate_links', $link ) ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="page-link dots">' . __( '&hellip;' ) . '</span>';

				$dots = false;
			endif;
		endif;
	endfor;

	if ( $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_comments_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<li class="page-item"><a class="page-link" href="%s">下一页</a><li>',
			/** This filter is documented in gc-includes/general-template.php */
			esc_url( apply_filters( 'paginate_links', $link ) ),
		);
	endif;

	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		case 'list':
			$r .= "<ul class='page-link'>\n\t<li>";
			$r .= implode( "</li  class='page-item'>\n\t<li>", $page_links );
			$r .= "</li>\n</ul>\n";
			break;

		default:
			$r = implode( "\n", $page_links );
			break;
	}

	/**
	 * Filters the HTML output of paginated links for archives.
	 *
	 *
	 * @param string $r    HTML output.
	 * @param array  $args An array of arguments. See paginate_links()
	 *                     for information on accepted arguments.
	 */
	$r = apply_filters( 'paginate_links_output', $r, $args );

	return $r;
}
