<?php
/**
 * Front to the GeChiUI application. This file doesn't do anything, but loads
 * gc-blog-header.php which does and tells GeChiUI to load the theme.
 *
 * @package GeChiUI
 */

/**
 * Tells GeChiUI to load the GeChiUI theme and output it.
 *
 * @var bool
 */
define( 'GC_USE_THEMES', true );

/** Loads the GeChiUI Environment and Template */
require __DIR__ . '/gc-blog-header.php';
