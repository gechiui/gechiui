<?php
/**
 * Used to set up all core blocks used with the block editor.
 *
 * @package GeChiUI
 */

// Include files required for core blocks registration.
require ABSPATH . GCINC . '/blocks/archives.php';
require ABSPATH . GCINC . '/blocks/block.php';
require ABSPATH . GCINC . '/blocks/calendar.php';
require ABSPATH . GCINC . '/blocks/categories.php';
require ABSPATH . GCINC . '/blocks/file.php';
require ABSPATH . GCINC . '/blocks/gallery.php';
require ABSPATH . GCINC . '/blocks/image.php';
require ABSPATH . GCINC . '/blocks/latest-comments.php';
require ABSPATH . GCINC . '/blocks/latest-posts.php';
require ABSPATH . GCINC . '/blocks/legacy-widget.php';
require ABSPATH . GCINC . '/blocks/loginout.php';
require ABSPATH . GCINC . '/blocks/navigation-link.php';
require ABSPATH . GCINC . '/blocks/navigation-submenu.php';
require ABSPATH . GCINC . '/blocks/navigation.php';
require ABSPATH . GCINC . '/blocks/page-list.php';
require ABSPATH . GCINC . '/blocks/pattern.php';
require ABSPATH . GCINC . '/blocks/post-author.php';
require ABSPATH . GCINC . '/blocks/post-comments.php';
require ABSPATH . GCINC . '/blocks/post-content.php';
require ABSPATH . GCINC . '/blocks/post-date.php';
require ABSPATH . GCINC . '/blocks/post-excerpt.php';
require ABSPATH . GCINC . '/blocks/post-featured-image.php';
require ABSPATH . GCINC . '/blocks/post-navigation-link.php';
require ABSPATH . GCINC . '/blocks/post-template.php';
require ABSPATH . GCINC . '/blocks/post-terms.php';
require ABSPATH . GCINC . '/blocks/post-title.php';
require ABSPATH . GCINC . '/blocks/query-pagination-next.php';
require ABSPATH . GCINC . '/blocks/query-pagination-numbers.php';
require ABSPATH . GCINC . '/blocks/query-pagination-previous.php';
require ABSPATH . GCINC . '/blocks/query-pagination.php';
require ABSPATH . GCINC . '/blocks/query-title.php';
require ABSPATH . GCINC . '/blocks/query.php';
require ABSPATH . GCINC . '/blocks/rss.php';
require ABSPATH . GCINC . '/blocks/search.php';
require ABSPATH . GCINC . '/blocks/shortcode.php';
require ABSPATH . GCINC . '/blocks/site-logo.php';
require ABSPATH . GCINC . '/blocks/site-tagline.php';
require ABSPATH . GCINC . '/blocks/site-title.php';
require ABSPATH . GCINC . '/blocks/social-link.php';
require ABSPATH . GCINC . '/blocks/tag-cloud.php';
require ABSPATH . GCINC . '/blocks/template-part.php';
require ABSPATH . GCINC . '/blocks/term-description.php';

/**
 * Registers core block types using metadata files.
 * Dynamic core blocks are registered separately.
 *
 *
 */
function register_core_block_types_from_metadata() {
	$block_folders = array(
		'audio',
		'button',
		'buttons',
		'code',
		'column',
		'columns',
		'cover',
		'embed',
		'freeform',
		'group',
		'heading',
		'html',
		'list',
		'media-text',
		'missing',
		'more',
		'nextpage',
		'paragraph',
		'preformatted',
		'pullquote',
		'quote',
		'separator',
		'social-links',
		'spacer',
		'table',
		'text-columns',
		'verse',
		'video',
	);

	foreach ( $block_folders as $block_folder ) {
		register_block_type(
			ABSPATH . GCINC . '/blocks/' . $block_folder
		);
	}
}
add_action( 'init', 'register_core_block_types_from_metadata' );
