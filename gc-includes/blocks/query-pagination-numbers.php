<?php
/**
 * Server-side rendering of the `core/query-pagination-numbers` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/query-pagination-numbers` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param GC_Block $block      Block instance.
 *
 * @return string Returns the pagination numbers for the Query.
 */
function render_block_core_query_pagination_numbers( $attributes, $content, $block ) {
	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];
	$max_page = isset( $block->context['query']['pages'] ) ? (int) $block->context['query']['pages'] : 0;

	$wrapper_attributes = get_block_wrapper_attributes();
	$content            = '';
	global $gc_query;
	if ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] ) {
		// Take into account if we have set a bigger `max page`
		// than what the query has.
		$total         = ! $max_page || $max_page > $gc_query->max_num_pages ? $gc_query->max_num_pages : $max_page;
		$paginate_args = array(
			'prev_next' => false,
			'total'     => $total,
		);
		$content       = paginate_links( $paginate_args );
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
			'prev_next' => false,
		);
		if ( 1 !== $page ) {
			/**
			 * `paginate_links` doesn't use the provided `format` when the page is `1`.
			 * This is great for the main query as it removes the extra query params
			 * making the URL shorter, but in the case of multiple custom queries is
			 * problematic. It results in returning an empty link which ends up with
			 * a link to the current page.
			 *
			 * A way to address this is to add a `fake` query arg with no value that
			 * is the same for all custom queries. This way the link is not empty and
			 * preserves all the other existent query args.
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
		// We still need to preserve `paged` query param if exists, as is used
		// for Queries that inherit from global context.
		$paged = empty( $_GET['paged'] ) ? null : (int) $_GET['paged'];
		if ( $paged ) {
			$paginate_args['add_args'] = array( 'paged' => $paged );
		}
		$content = paginate_links( $paginate_args );
		gc_reset_postdata(); // Restore original Post Data.
		$gc_query = $prev_gc_query;
	}
	if ( empty( $content ) ) {
		return '';
	}
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/query-pagination-numbers` block on the server.
 */
function register_block_core_query_pagination_numbers() {
	register_block_type_from_metadata(
		ABSPATH . 'assets/blocks/query-pagination-numbers',
		array(
			'render_callback' => 'render_block_core_query_pagination_numbers',
		)
	);
}
add_action( 'init', 'register_block_core_query_pagination_numbers' );
