<?php
/**
 * Used to set up all core blocks used with the block editor.
 *
 * @package GeChiUI
 */

// Include files required for core blocks registration.
require get_template_directory() . '/blocks/gcoa-navigation.php';
// require get_template_directory() . '/blocks/archives.php';
// require get_template_directory() . '/blocks/block.php';
// require get_template_directory() . '/blocks/calendar.php';
// require get_template_directory() . '/blocks/categories.php';
// require get_template_directory() . '/blocks/file.php';
// require get_template_directory() . '/blocks/gallery.php';
// require get_template_directory() . '/blocks/get-template-directory-uri.php';
// require get_template_directory() . '/blocks/image.php';
// require get_template_directory() . '/blocks/latest-comments.php';
// require get_template_directory() . '/blocks/latest-posts.php';
// require get_template_directory() . '/blocks/legacy-widget.php';
// require get_template_directory() . '/blocks/loginout.php';
// require get_template_directory() . '/blocks/navigation-link.php';
// require get_template_directory() . '/blocks/navigation-submenu.php';
// require get_template_directory() . '/blocks/navigation.php';
// require get_template_directory() . '/blocks/page-list.php';
// require get_template_directory() . '/blocks/pattern.php';
// require get_template_directory() . '/blocks/gcoa-post-author-avatar.php';
require get_template_directory() . '/blocks/gcoa-post-author.php';
require get_template_directory() . '/blocks/gcoa-post-comment-avatar.php';
require get_template_directory() . '/blocks/gcoa-post-comment-author.php';
require get_template_directory() . '/blocks/gcoa-post-comment-content.php';
require get_template_directory() . '/blocks/gcoa-post-comment-date.php';
require get_template_directory() . '/blocks/gcoa-post-comment-replytocom-link.php';
require get_template_directory() . '/blocks/gcoa-post-comment-template.php';
require get_template_directory() . '/blocks/gcoa-post-comments-show.php';
require get_template_directory() . '/blocks/gcoa-post-comments-count.php';
require get_template_directory() . '/blocks/gcoa-post-comments-form.php';

require get_template_directory() . '/blocks/gcoa-post-id.php';
require get_template_directory() . '/blocks/gcoa-post-content.php';
// require get_template_directory() . '/blocks/gcoa-post-date.php';
// require get_template_directory() . '/blocks/gcoa-post-excerpt.php';
// require get_template_directory() . '/blocks/gcoa-post-featured-image.php';
// require get_template_directory() . '/blocks/gcoa-post-navigation-link.php';
// require get_template_directory() . '/blocks/gcoa-post-template.php';
// require get_template_directory() . '/blocks/gcoa-post-terms.php';
require get_template_directory() . '/blocks/gcoa-post-title.php';
// require get_template_directory() . '/blocks/gcoa-post-url.php';
// require get_template_directory() . '/blocks/query-pagination-next.php';
// require get_template_directory() . '/blocks/query-pagination-numbers.php';
// require get_template_directory() . '/blocks/query-pagination-previous.php';
// require get_template_directory() . '/blocks/query-pagination.php';
// require get_template_directory() . '/blocks/query-title.php';
// require get_template_directory() . '/blocks/query.php';
// require get_template_directory() . '/blocks/rss.php';
// require get_template_directory() . '/blocks/search-key.php';
// require get_template_directory() . '/blocks/search.php';
// require get_template_directory() . '/blocks/shortcode.php';
// require get_template_directory() . '/blocks/site-logo.php';
// require get_template_directory() . '/blocks/site-tagline.php';
// require get_template_directory() . '/blocks/site-title.php';
// require get_template_directory() . '/blocks/social-link.php';
// require get_template_directory() . '/blocks/tag-cloud.php';
// require get_template_directory() . '/blocks/template-part.php';
// require get_template_directory() . '/blocks/term-description.php';
// require get_template_directory() . '/blocks/widget-group.php';

/**
 * Registers core block types using metadata files.
 * Dynamic core blocks are registered separately.
 *
 *
 */
// function gcoa_register_core_block_types_from_metadata() {
// 	$block_folders = array(
// 		'gcoa-post-title',
// 		'gcoa-post-id',
// 	);

// 	foreach ( $block_folders as $block_folder ) {
// 		register_block_type(
// 			get_template_directory() . '/blocks/' . $block_folder
// 		);
// 	}
// }
// add_action( 'init', 'gcoa_register_core_block_types_from_metadata' );
