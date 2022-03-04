<?php
/**
 * GeChiUI Diff bastard child of old MediaWiki Diff Formatter.
 *
 * Basically all that remains is the table structure and some method names.
 *
 * @package GeChiUI
 * @subpackage Diff
 */

if ( ! class_exists( 'Text_Diff', false ) ) {
	/** Text_Diff class */
	require ABSPATH . GCINC . '/Text/Diff.php';
	/** Text_Diff_Renderer class */
	require ABSPATH . GCINC . '/Text/Diff/Renderer.php';
	/** Text_Diff_Renderer_inline class */
	require ABSPATH . GCINC . '/Text/Diff/Renderer/inline.php';
}

require ABSPATH . GCINC . '/class-gc-text-diff-renderer-table.php';
require ABSPATH . GCINC . '/class-gc-text-diff-renderer-inline.php';
