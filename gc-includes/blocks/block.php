<?php
/**
 * Server-side rendering of the `core/block` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/block` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Rendered HTML of the referenced block.
 */
function render_block_core_block( $attributes ) {
	static $seen_refs = array();

	if ( empty( $attributes['ref'] ) ) {
		return '';
	}

	$reusable_block = get_post( $attributes['ref'] );
	if ( ! $reusable_block || 'gc_block' !== $reusable_block->post_type ) {
		return '';
	}

	if ( isset( $seen_refs[ $attributes['ref'] ] ) ) {
		// GC_DEBUG_DISPLAY must only be honored when GC_DEBUG. This precedent
		// is set in `gc_debug_mode()`.
		$is_debug = GC_DEBUG && GC_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[区块渲染已停止]' ) :
			'';
	}

	if ( 'publish' !== $reusable_block->post_status || ! empty( $reusable_block->post_password ) ) {
		return '';
	}

	$seen_refs[ $attributes['ref'] ] = true;

	// Handle embeds for reusable blocks.
	global $gc_embed;
	$content = $gc_embed->run_shortcode( $reusable_block->post_content );
	$content = $gc_embed->autoembed( $content );

	$content = do_blocks( $content );
	unset( $seen_refs[ $attributes['ref'] ] );
	return $content;
}

/**
 * Registers the `core/block` block.
 */
function register_block_core_block() {
	register_block_type_from_metadata(
		__DIR__ . '/block',
		array(
			'render_callback' => 'render_block_core_block',
		)
	);
}
add_action( 'init', 'register_block_core_block' );
