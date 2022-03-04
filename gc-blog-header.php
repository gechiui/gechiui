<?php
/**
 * Loads the GeChiUI environment and template.
 *
 * @package GeChiUI
 */

if ( ! isset( $gc_did_header ) ) {

	$gc_did_header = true;

	// Load the GeChiUI library.
	require_once __DIR__ . '/gc-load.php';

	// Set up the GeChiUI query.
	gc();

	// Load the theme template.
	require_once ABSPATH . GCINC . '/template-loader.php';

}
