<?php
/**
 * Register the block patterns and block patterns categories
 *
 * @package GeChiUI
 *
 */

add_theme_support( 'core-block-patterns' );

/**
 * Registers the core block patterns and categories.
 *
 *
 * @private
 */
function _register_core_block_patterns_and_categories() {
	$should_register_core_patterns = get_theme_support( 'core-block-patterns' );

	if ( $should_register_core_patterns ) {
		$core_block_patterns = array(
			'query-standard-posts',
			'query-medium-posts',
			'query-small-posts',
			'query-grid-posts',
			'query-large-title-posts',
			'query-offset-posts',
			'social-links-shared-background-color',
		);

		foreach ( $core_block_patterns as $core_block_pattern ) {
			register_block_pattern(
				'core/' . $core_block_pattern,
				require __DIR__ . '/block-patterns/' . $core_block_pattern . '.php'
			);
		}
	}

	register_block_pattern_category( 'buttons', array( 'label' => _x( '按钮', 'Block pattern category' ) ) );
	register_block_pattern_category( 'columns', array( 'label' => _x( '栏目', 'Block pattern category' ) ) );
	register_block_pattern_category( 'gallery', array( 'label' => _x( '画廊', 'Block pattern category' ) ) );
	register_block_pattern_category( 'header', array( 'label' => _x( '页眉', 'Block pattern category' ) ) );
	register_block_pattern_category( 'text', array( 'label' => _x( '文本', 'Block pattern category' ) ) );
	register_block_pattern_category( 'query', array( 'label' => _x( '查询', 'Block pattern category' ) ) );
}

/**
 * Register Core's official patterns from www.gechiui.com/patterns.
 *
 *
 *
 *
 * @param GC_Screen $deprecated Unused. Formerly the screen that the current request was triggered from.
 */
function _load_remote_block_patterns( $deprecated = null ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '5.9.0' );
		$current_screen = $deprecated;
		if ( ! $current_screen->is_block_editor ) {
			return;
		}
	}

	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/**
	 * Filter to disable remote block patterns.
	 *
	 *
	 * @param bool $should_load_remote
	 */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( $supports_core_patterns && $should_load_remote ) {
		$request         = new GC_REST_Request( 'GET', '/gc/v2/pattern-directory/patterns' );
		$core_keyword_id = 11; // 11 is the ID for "core".
		$request->set_param( 'keyword', $core_keyword_id );
		$response = rest_do_request( $request );
		if ( $response->is_error() ) {
			return;
		}
		$patterns = $response->get_data();

		foreach ( $patterns as $settings ) {
			$pattern_name = 'core/' . sanitize_title( $settings['title'] );
			register_block_pattern( $pattern_name, (array) $settings );
		}
	}
}

/**
 * Register `Featured` (category) patterns from www.gechiui.com/patterns.
 *
 *
 */
function _load_remote_featured_patterns() {
	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/** This filter is documented in gc-includes/block-patterns.php */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( ! $should_load_remote || ! $supports_core_patterns ) {
		return;
	}

	if ( ! GC_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'featured' ) ) {
		register_block_pattern_category( 'featured', array( 'label' => __( '特色' ) ) );
	}

	$request         = new GC_REST_Request( 'GET', '/gc/v2/pattern-directory/patterns' );
	$featured_cat_id = 26; // This is the `Featured` category id from pattern directory.
	$request->set_param( 'category', $featured_cat_id );
	$response = rest_do_request( $request );
	if ( $response->is_error() ) {
		return;
	}
	$patterns = $response->get_data();

	foreach ( $patterns as $pattern ) {
		$pattern_name = sanitize_title( $pattern['title'] );
		$registry     = GC_Block_Patterns_Registry::get_instance();
		// Some patterns might be already registered as core patterns with the `core` prefix.
		$is_registered = $registry->is_registered( $pattern_name ) || $registry->is_registered( "core/$pattern_name" );
		if ( ! $is_registered ) {
			register_block_pattern( $pattern_name, (array) $pattern );
		}
	}
}
