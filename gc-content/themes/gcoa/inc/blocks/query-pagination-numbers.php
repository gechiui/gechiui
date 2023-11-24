<?php
/**
 * Server-side rendering of the `core/query-pagination-numbers` block.
 *
 * @package GeChiUI
 */

function gcoa_paginate_links( $args = '' ) {
	global $gc_query, $gc_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $gc_query->max_num_pages ) ? $gc_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

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
		'add_args'           => array(), // Array of query args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = gc_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_query = isset( $format[1] ) ? $format[1] : '';
		gc_parse_str( $format_query, $format_args );

		// Find the query args of the requested URL.
		gc_parse_str( $url_parts[1], $url_query_args );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_query_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
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

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<li class="page-item"><a class="page-link" href="%s">上一页</a></li>',
			// '<a class="prev page-numbers" href="%s">%s</a>',
			/**
			 * Filters the paginated links for the given archive pages.
			 *
		
			 *
			 * @param string $link The paginated link URL.
			 */
			esc_url( apply_filters( 'paginate_links', $link ) )
			// $args['prev_text']
		);
	else :
		$page_links[] = '<li class="page-item"><a class="page-link" href="#">上一页</a></li>';
	endif;

	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = sprintf(
				'<li class="page-item active"><a class="page-link" href="#">%s</a></li>',
				// '<span aria-current="%s" class="page-numbers current">%s</span>',
				// esc_attr( $args['aria_current'] ),
				$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
			);

			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link .= $args['add_fragment'];

				$page_links[] = sprintf(
					'<li class="page-item"><a class="page-link" href="%s">%s</a><li>',
					// '<a class="page-numbers" href="%s">%s</a>',
					/** This filter is documented in gc-includes/general-template.php */
					esc_url( apply_filters( 'paginate_links', $link ) ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';

				$dots = false;
			endif;
		endif;
	endfor;

	if ( $args['prev_next'] && $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<li class="page-item"><a class="page-link" href="%s">下一页</a><li>',
			// '<a class="next page-numbers" href="%s">%s</a>',
			/** This filter is documented in gc-includes/general-template.php */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['next_text']
		);
	else :
		$page_links[] = '<li class="page-item"><a class="page-link" href="#">下一页</a><li>';
	endif;

	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		// case 'list':
		// 	$r .= "<nav>\n<ul class='pagination justify-content-center'>\n\t<li>";
		// 	$r .= implode( "</li  class='page-item'>\n\t<li>", $page_links );
		// 	// $r .= "<ul class='page-numbers'>\n\t<li>";
		// 	// $r .= implode( "</li>\n\t<li>", $page_links );
		// 	$r .= "</li>\n</ul>\n</nav>\n";
		// 	break;

		default:
			$r = implode( "\n", $page_links );
			break;
	}

	$r = '<nav><ul class="pagination justify-content-center">'. $r .'</ul></nav>';

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

/**
 * Renders the `core/query-pagination-numbers` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the pagination numbers for the Query.
 */
function render_block_gcoa_core_query_pagination_numbers( $attributes, $content, $block ) {
	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];
	$max_page = isset( $block->context['query']['pages'] ) ? (int) $block->context['query']['pages'] : 0;

	$content = '';
	global $gc_query;
	if ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] ) {
		// Take into account if we have set a bigger `max page`
		// than what the query has.
		$total         = ! $max_page || $max_page > $gc_query->max_num_pages ? $gc_query->max_num_pages : $max_page;
		$paginate_args = array(
			'prev_next' => true,
			'total'     => $total,
		);
		$content       = gcoa_paginate_links( $paginate_args );
	} else {
		$block_query = new GC_Query( build_query_vars_from_query_block( $block, $page ) );
		// `paginate_links` works with the global $gc_query, so we have to
		// temporarily switch it with our custom query.
		$prev_gc_query = $gc_query;
		$gc_query      = $block_query;
		$total         = ! $max_page || $max_page > $gc_query->max_num_pages ? $gc_query->max_num_pages : $max_page;
		$paginate_args = array(
			'base'      => '%_%',
			'format'    => "?$page_key=%#%",
			'current'   => max( 1, $page ),
			'total'     => $total,
			'prev_next' => true,
		);
		if ( 1 !== $page ) {
			$paginate_args['add_args'] = array( 'cst' => '' );
		}
		// We still need to preserve `paged` query param if exists, as is used
		// for Queries that inherit from global context.
		$paged = empty( $_GET['paged'] ) ? null : (int) $_GET['paged'];
		if ( $paged ) {
			$paginate_args['add_args'] = array( 'paged' => $paged );
		}
		$content = gcoa_paginate_links( $paginate_args );
		gc_reset_postdata(); // Restore original Post Data.
		$gc_query = $prev_gc_query;
	}
	if ( empty( $content ) ) {
		return '';
	}
	return $content;
}

/**
 * Registers the `core/query-pagination-numbers` block on the server.
 */
function register_block_gcoa_core_query_pagination_numbers() {
	// 删除原有的区块
	unregister_block_type('core/query-pagination-numbers');
	// 注册新的
	register_block_type_from_metadata(
		ABSPATH . GCINC . '/blocks/query-pagination-numbers',
		array(
			'render_callback' => 'render_block_gcoa_core_query_pagination_numbers',
		)
	);
}
add_action( 'init', 'register_block_gcoa_core_query_pagination_numbers' );
