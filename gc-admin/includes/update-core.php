<?php
/**
 * GeChiUI core upgrade functionality.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Stores files to be deleted.
 *
 *
 *
 * @global array $_old_files
 * @var array
 * @name $_old_files
 */
global $_old_files;

$_old_files = array(
	// 2.0
	'gc-admin/import-b2.php',
	'gc-admin/import-blogger.php',
	'gc-admin/import-greymatter.php',
	'gc-admin/import-livejournal.php',
	'gc-admin/import-mt.php',
	'gc-admin/import-rss.php',
	'gc-admin/import-textpattern.php',
	'gc-admin/quicktags.js',
	'gc-images/fade-butt.png',
	'gc-images/get-firefox.png',
	'gc-images/header-shadow.png',
	'gc-images/smilies',
	'gc-images/gc-small.png',
	'gc-images/gcminilogo.png',
	'gc.php',
	// 2.1
	'gc-admin/edit-form-ajax-cat.php',
	'gc-admin/execute-pings.php',
	'gc-admin/inline-uploading.php',
	'gc-admin/link-categories.php',
	'gc-admin/list-manipulation.js',
	'gc-admin/list-manipulation.php',
	'gc-includes/comment-functions.php',
	'gc-includes/feed-functions.php',
	'gc-includes/functions-compat.php',
	'gc-includes/functions-formatting.php',
	'gc-includes/functions-post.php',
	'gc-includes/js/dbx-key.js',
	'gc-includes/links.php',
	'gc-includes/pluggable-functions.php',
	'gc-includes/template-functions-author.php',
	'gc-includes/template-functions-category.php',
	'gc-includes/template-functions-general.php',
	'gc-includes/template-functions-links.php',
	'gc-includes/template-functions-post.php',
	'gc-includes/gc-l10n.php',
	// 2.2
	'gc-admin/cat-js.php',
	'gc-admin/import/b2.php',
	'gc-includes/js/autosave-js.php',
	'gc-includes/js/list-manipulation-js.php',
	'gc-includes/js/gc-ajax-js.php',
	// 2.3
	'gc-admin/admin-db.php',
	'gc-admin/cat.js',
	'gc-admin/categories.js',
	'gc-admin/custom-fields.js',
	'gc-admin/dbx-admin-key.js',
	'gc-admin/edit-comments.js',
	'gc-admin/install-rtl.css',
	'gc-admin/install.css',
	'gc-admin/upgrade-schema.php',
	'gc-admin/upload-functions.php',
	'gc-admin/upload-rtl.css',
	'gc-admin/upload.css',
	'gc-admin/upload.js',
	'gc-admin/users.js',
	'gc-admin/widgets-rtl.css',
	'gc-admin/widgets.css',
	'gc-admin/xfn.js',
	// 2.5
	'gc-admin/css/upload.css',
	'gc-admin/images/box-bg-left.gif',
	'gc-admin/images/box-bg-right.gif',
	'gc-admin/images/box-bg.gif',
	'gc-admin/images/box-butt-left.gif',
	'gc-admin/images/box-butt-right.gif',
	'gc-admin/images/box-butt.gif',
	'gc-admin/images/box-head-left.gif',
	'gc-admin/images/box-head-right.gif',
	'gc-admin/images/box-head.gif',
	'gc-admin/images/heading-bg.gif',
	'gc-admin/images/login-bkg-bottom.gif',
	'gc-admin/images/login-bkg-tile.gif',
	'gc-admin/images/notice.gif',
	'gc-admin/images/toggle.gif',
	'gc-admin/includes/upload.php',
	'gc-admin/js/dbx-admin-key.js',
	'gc-admin/js/link-cat.js',
	'gc-admin/profile-update.php',
	'gc-admin/templates.php',
	'gc-includes/images/wlw/GcComments.png',
	'gc-includes/images/wlw/GcIcon.png',
	'gc-includes/images/wlw/GcWatermark.png',
	'gc-includes/js/dbx.js',
	'gc-includes/js/fat.js',
	'gc-includes/js/list-manipulation.js',

	// 2.6
	'gc-admin/bookmarklet.php',
	'gc-includes/js/gc-ajax.js',
	// 2.7
	'gc-admin/css/press-this-ie-rtl.css',
	'gc-admin/css/press-this-ie.css',
	'gc-admin/css/upload-rtl.css',
	'gc-admin/edit-form.php',
	'gc-admin/images/comment-pill.gif',
	'gc-admin/images/comment-stalk-classic.gif',
	'gc-admin/images/comment-stalk-fresh.gif',
	'gc-admin/images/comment-stalk-rtl.gif',
	'gc-admin/images/del.png',
	'gc-admin/images/gear.png',
	'gc-admin/images/media-button-gallery.gif',
	'gc-admin/images/media-buttons.gif',
	'gc-admin/images/postbox-bg.gif',
	'gc-admin/images/tab.png',
	'gc-admin/images/tail.gif',
	'gc-admin/js/forms.js',
	'gc-admin/js/upload.js',
	'gc-admin/link-import.php',
	'gc-includes/images/audio.png',
	'gc-includes/images/css.png',
	'gc-includes/images/default.png',
	'gc-includes/images/doc.png',
	'gc-includes/images/exe.png',
	'gc-includes/images/html.png',
	'gc-includes/images/js.png',
	'gc-includes/images/pdf.png',
	'gc-includes/images/swf.png',
	'gc-includes/images/tar.png',
	'gc-includes/images/text.png',
	'gc-includes/images/video.png',
	'gc-includes/images/zip.png',
	// 2.8
	'gc-admin/js/users.js',
	// 2.8.5
	'gc-admin/import/btt.php',
	'gc-admin/import/jkw.php',
	// 2.9
	'gc-admin/js/page.dev.js',
	'gc-admin/js/page.js',
	'gc-admin/js/set-post-thumbnail-handler.dev.js',
	'gc-admin/js/set-post-thumbnail-handler.js',
	'gc-admin/js/slug.dev.js',
	'gc-admin/js/slug.js',
	'gc-includes/gettext.php',
	'gc-includes/streams.php',
	// MU
	'README.txt',
	'htaccess.dist',
	'index-install.php',
	'gc-admin/css/mu-rtl.css',
	'gc-admin/css/mu.css',
	'gc-admin/images/site-admin.png',
	'gc-admin/includes/mu.php',
	'gc-admin/gcmu-admin.php',
	'gc-admin/gcmu-blogs.php',
	'gc-admin/gcmu-edit.php',
	'gc-admin/gcmu-options.php',
	'gc-admin/gcmu-themes.php',
	'gc-admin/gcmu-upgrade-site.php',
	'gc-admin/gcmu-users.php',
	'gc-includes/images/gechiui-mu.png',
	'gc-includes/gcmu-default-filters.php',
	'gc-includes/gcmu-functions.php',
	'gcmu-settings.php',
	// 3.0
	'gc-admin/categories.php',
	'gc-admin/edit-category-form.php',
	'gc-admin/edit-page-form.php',
	'gc-admin/edit-pages.php',
	'gc-admin/images/admin-header-footer.png',
	'gc-admin/images/browse-happy.gif',
	'gc-admin/images/ico-add.png',
	'gc-admin/images/ico-close.png',
	'gc-admin/images/ico-edit.png',
	'gc-admin/images/ico-viewpage.png',
	'gc-admin/images/fav-top.png',
	'gc-admin/images/screen-options-left.gif',
	'gc-admin/images/gc-logo-vs.gif',
	'gc-admin/images/gc-logo.gif',
	'gc-admin/import',
	'gc-admin/js/gc-gears.dev.js',
	'gc-admin/js/gc-gears.js',
	'gc-admin/options-misc.php',
	'gc-admin/page-new.php',
	'gc-admin/page.php',
	'gc-admin/rtl.css',
	'gc-admin/rtl.dev.css',
	'gc-admin/update-links.php',
	'gc-admin/gc-admin.css',
	'gc-admin/gc-admin.dev.css',
	'gc-includes/js/codepress',
	'gc-includes/js/codepress/engines/khtml.js',
	'gc-includes/js/codepress/engines/older.js',
	// Following file added back in 5.1, see #45645.
	// 3.1
	'gc-admin/edit-attachment-rows.php',
	'gc-admin/edit-link-categories.php',
	'gc-admin/edit-link-category-form.php',
	'gc-admin/edit-post-rows.php',
	'gc-admin/images/button-grad-active-vs.png',
	'gc-admin/images/button-grad-vs.png',
	'gc-admin/images/fav-arrow-vs-rtl.gif',
	'gc-admin/images/fav-arrow-vs.gif',
	'gc-admin/images/fav-top-vs.gif',
	'gc-admin/images/list-vs.png',
	'gc-admin/images/screen-options-right-up.gif',
	'gc-admin/images/screen-options-right.gif',
	'gc-admin/images/visit-site-button-grad-vs.gif',
	'gc-admin/images/visit-site-button-grad.gif',
	'gc-admin/link-category.php',
	'gc-admin/sidebar.php',
	'gc-includes/classes.php',
	// 3.2
	'gc-admin/images/logo-login.gif',
	'gc-admin/images/star.gif',
	'gc-admin/js/list-table.dev.js',
	'gc-admin/js/list-table.js',
	'gc-includes/default-embeds.php',
	// 3.3
	'gc-admin/css/colors-classic-rtl.css',
	'gc-admin/css/colors-classic-rtl.dev.css',
	'gc-admin/css/colors-fresh-rtl.css',
	'gc-admin/css/colors-fresh-rtl.dev.css',
	'gc-admin/css/dashboard-rtl.dev.css',
	'gc-admin/css/dashboard.dev.css',
	'gc-admin/css/global-rtl.css',
	'gc-admin/css/global-rtl.dev.css',
	'gc-admin/css/global.css',
	'gc-admin/css/global.dev.css',
	'gc-admin/css/install-rtl.dev.css',
	'gc-admin/css/login-rtl.dev.css',
	'gc-admin/css/login.dev.css',
	'gc-admin/css/ms.css',
	'gc-admin/css/ms.dev.css',
	'gc-admin/css/nav-menu-rtl.css',
	'gc-admin/css/nav-menu-rtl.dev.css',
	'gc-admin/css/nav-menu.css',
	'gc-admin/css/nav-menu.dev.css',
	'gc-admin/css/plugin-install-rtl.css',
	'gc-admin/css/plugin-install-rtl.dev.css',
	'gc-admin/css/plugin-install.css',
	'gc-admin/css/plugin-install.dev.css',
	'gc-admin/css/press-this-rtl.dev.css',
	'gc-admin/css/press-this.dev.css',
	'gc-admin/css/theme-editor-rtl.css',
	'gc-admin/css/theme-editor-rtl.dev.css',
	'gc-admin/css/theme-editor.css',
	'gc-admin/css/theme-editor.dev.css',
	'gc-admin/css/theme-install-rtl.css',
	'gc-admin/css/theme-install-rtl.dev.css',
	'gc-admin/css/theme-install.css',
	'gc-admin/css/theme-install.dev.css',
	'gc-admin/css/widgets-rtl.dev.css',
	'gc-admin/css/widgets.dev.css',
	'gc-admin/includes/internal-linking.php',
	'gc-includes/images/admin-bar-sprite-rtl.png',
	'gc-includes/js/l10n.dev.js',
	'gc-includes/js/l10n.js',
	// Don't delete, yet: 'gc-rss.php',
	// Don't delete, yet: 'gc-rdf.php',
	// Don't delete, yet: 'gc-rss2.php',
	// Don't delete, yet: 'gc-commentsrss2.php',
	// Don't delete, yet: 'gc-atom.php',
	// Don't delete, yet: 'gc-feed.php',
	// 3.4
	'gc-admin/images/gray-star.png',
	'gc-admin/images/logo-login.png',
	'gc-admin/images/star.png',
	'gc-admin/index-extra.php',
	'gc-admin/network/index-extra.php',
	'gc-admin/user/index-extra.php',
	'gc-admin/images/screenshots/admin-flyouts.png',
	'gc-admin/images/screenshots/coediting.png',
	'gc-admin/images/screenshots/drag-and-drop.png',
	'gc-admin/images/screenshots/help-screen.png',
	'gc-admin/images/screenshots/media-icon.png',
	'gc-admin/images/screenshots/new-feature-pointer.png',
	'gc-admin/images/screenshots/welcome-screen.png',
	'gc-includes/css/editor-buttons.css',
	'gc-includes/css/editor-buttons.dev.css',
	// Don't delete, yet: 'gc-pass.php',
	// Don't delete, yet: 'gc-register.php',
	// 3.5
	'gc-admin/gears-manifest.php',
	'gc-admin/includes/manifest.php',
	'gc-admin/images/archive-link.png',
	'gc-admin/images/blue-grad.png',
	'gc-admin/images/button-grad-active.png',
	'gc-admin/images/button-grad.png',
	'gc-admin/images/ed-bg-vs.gif',
	'gc-admin/images/ed-bg.gif',
	'gc-admin/images/fade-butt.png',
	'gc-admin/images/fav-arrow-rtl.gif',
	'gc-admin/images/fav-arrow.gif',
	'gc-admin/images/fav-vs.png',
	'gc-admin/images/fav.png',
	'gc-admin/images/gray-grad.png',
	'gc-admin/images/loading-publish.gif',
	'gc-admin/images/logo-ghost.png',
	'gc-admin/images/logo.gif',
	'gc-admin/images/menu-arrow-frame-rtl.png',
	'gc-admin/images/menu-arrow-frame.png',
	'gc-admin/images/menu-arrows.gif',
	'gc-admin/images/menu-bits-rtl-vs.gif',
	'gc-admin/images/menu-bits-rtl.gif',
	'gc-admin/images/menu-bits-vs.gif',
	'gc-admin/images/menu-bits.gif',
	'gc-admin/images/menu-dark-rtl-vs.gif',
	'gc-admin/images/menu-dark-rtl.gif',
	'gc-admin/images/menu-dark-vs.gif',
	'gc-admin/images/menu-dark.gif',
	'gc-admin/images/required.gif',
	'gc-admin/images/screen-options-toggle-vs.gif',
	'gc-admin/images/screen-options-toggle.gif',
	'gc-admin/images/toggle-arrow-rtl.gif',
	'gc-admin/images/toggle-arrow.gif',
	'gc-admin/images/upload-classic.png',
	'gc-admin/images/upload-fresh.png',
	'gc-admin/images/white-grad-active.png',
	'gc-admin/images/white-grad.png',
	'gc-admin/images/widgets-arrow-vs.gif',
	'gc-admin/images/widgets-arrow.gif',
	'gc-admin/images/gcspin_dark.gif',
	'gc-includes/images/upload.png',
	'gc-includes/js/prototype.js',
	'gc-includes/js/scriptaculous',
	'gc-admin/css/gc-admin-rtl.dev.css',
	'gc-admin/css/gc-admin.dev.css',
	'gc-admin/css/media-rtl.dev.css',
	'gc-admin/css/media.dev.css',
	'gc-admin/css/colors-classic.dev.css',
	'gc-admin/css/customize-controls-rtl.dev.css',
	'gc-admin/css/customize-controls.dev.css',
	'gc-admin/css/ie-rtl.dev.css',
	'gc-admin/css/ie.dev.css',
	'gc-admin/css/install.dev.css',
	'gc-admin/css/colors-fresh.dev.css',
	'gc-includes/js/customize-base.dev.js',
	'gc-includes/js/comment-reply.dev.js',
	'gc-includes/js/customize-preview.dev.js',
	'gc-includes/js/gclink.dev.js',
	'gc-includes/js/gc-list-revisions.dev.js',
	'gc-includes/js/autosave.dev.js',
	'gc-includes/js/admin-bar.dev.js',
	'gc-includes/js/quicktags.dev.js',
	'gc-includes/js/gc-ajax-response.dev.js',
	'gc-includes/js/gc-pointer.dev.js',
	'gc-includes/js/gc-lists.dev.js',
	'gc-includes/js/customize-loader.dev.js',
	'gc-admin/js/xfn.dev.js',
	'gc-admin/js/set-post-thumbnail.dev.js',
	'gc-admin/js/comment.dev.js',
	'gc-admin/js/theme.dev.js',
	'gc-admin/js/cat.dev.js',
	'gc-admin/js/password-strength-meter.dev.js',
	'gc-admin/js/user-profile.dev.js',
	'gc-admin/js/theme-preview.dev.js',
	'gc-admin/js/post.dev.js',
	'gc-admin/js/media-upload.dev.js',
	'gc-admin/js/word-count.dev.js',
	'gc-admin/js/plugin-install.dev.js',
	'gc-admin/js/edit-comments.dev.js',
	'gc-admin/js/media-gallery.dev.js',
	'gc-admin/js/custom-fields.dev.js',
	'gc-admin/js/custom-background.dev.js',
	'gc-admin/js/common.dev.js',
	'gc-admin/js/inline-edit-tax.dev.js',
	'gc-admin/js/gallery.dev.js',
	'gc-admin/js/utils.dev.js',
	'gc-admin/js/widgets.dev.js',
	'gc-admin/js/gc-fullscreen.dev.js',
	'gc-admin/js/nav-menu.dev.js',
	'gc-admin/js/dashboard.dev.js',
	'gc-admin/js/link.dev.js',
	'gc-admin/js/user-suggest.dev.js',
	'gc-admin/js/postbox.dev.js',
	'gc-admin/js/tags.dev.js',
	'gc-admin/js/image-edit.dev.js',
	'gc-admin/js/media.dev.js',
	'gc-admin/js/customize-controls.dev.js',
	'gc-admin/js/inline-edit-post.dev.js',
	'gc-admin/js/categories.dev.js',
	'gc-admin/js/editor.dev.js',
	'gc-includes/css/gc-pointer.dev.css',
	'gc-includes/css/editor.dev.css',
	'gc-includes/css/jquery-ui-dialog.dev.css',
	'gc-includes/css/admin-bar-rtl.dev.css',
	'gc-includes/css/admin-bar.dev.css',
	'gc-admin/images/screenshots/captions-1.png',
	'gc-admin/images/screenshots/captions-2.png',
	'gc-admin/images/screenshots/flex-header-1.png',
	'gc-admin/images/screenshots/flex-header-2.png',
	'gc-admin/images/screenshots/flex-header-3.png',
	'gc-admin/images/screenshots/flex-header-media-library.png',
	'gc-admin/images/screenshots/theme-customizer.png',
	'gc-admin/images/screenshots/twitter-embed-1.png',
	'gc-admin/images/screenshots/twitter-embed-2.png',
	'gc-admin/js/utils.js',
	// Added back in 5.3 [45448], see #43895.
	// 'gc-admin/options-privacy.php',
	'gc-app.php',
	'gc-includes/class-gc-atom-server.php',
	// 3.6
	'gc-admin/js/revisions-js.php',
	'gc-admin/images/screenshots',
	'gc-admin/js/categories.js',
	'gc-admin/js/categories.min.js',
	'gc-admin/js/custom-fields.js',
	'gc-admin/js/custom-fields.min.js',
	// 3.7
	'gc-admin/js/cat.js',
	'gc-admin/js/cat.min.js',
	// 3.8
	'gc-includes/images/gcmini-blue-2x.png',
	'gc-includes/images/gcmini-blue.png',
	'gc-admin/css/colors-fresh.css',
	'gc-admin/css/colors-classic.css',
	'gc-admin/css/colors-fresh.min.css',
	'gc-admin/css/colors-classic.min.css',
	'gc-admin/js/about.min.js',
	'gc-admin/js/about.js',
	'gc-admin/images/arrows-dark-vs-2x.png',
	'gc-admin/images/gc-logo-vs.png',
	'gc-admin/images/arrows-dark-vs.png',
	'gc-admin/images/gc-logo.png',
	'gc-admin/images/arrows-pr.png',
	'gc-admin/images/arrows-dark.png',
	'gc-admin/images/press-this.png',
	'gc-admin/images/press-this-2x.png',
	'gc-admin/images/arrows-vs-2x.png',
	'gc-admin/images/welcome-icons.png',
	'gc-admin/images/gc-logo-2x.png',
	'gc-admin/images/stars-rtl-2x.png',
	'gc-admin/images/arrows-dark-2x.png',
	'gc-admin/images/arrows-pr-2x.png',
	'gc-admin/images/menu-shadow-rtl.png',
	'gc-admin/images/arrows-vs.png',
	'gc-admin/images/about-search-2x.png',
	'gc-admin/images/bubble_bg-rtl-2x.gif',
	'gc-admin/images/gc-badge-2x.png',
	'gc-admin/images/gechiui-logo-2x.png',
	'gc-admin/images/bubble_bg-rtl.gif',
	'gc-admin/images/gc-badge.png',
	'gc-admin/images/menu-shadow.png',
	'gc-admin/images/about-globe-2x.png',
	'gc-admin/images/welcome-icons-2x.png',
	'gc-admin/images/stars-rtl.png',
	'gc-admin/images/gc-logo-vs-2x.png',
	'gc-admin/images/about-updates-2x.png',
	// 3.9
	'gc-admin/css/colors.css',
	'gc-admin/css/colors.min.css',
	'gc-admin/css/colors-rtl.css',
	'gc-admin/css/colors-rtl.min.css',
	// Following files added back in 4.5, see #36083.
	// 'gc-admin/css/media-rtl.min.css',
	// 'gc-admin/css/media.min.css',
	// 'gc-admin/css/farbtastic-rtl.min.css',
	'gc-admin/images/lock-2x.png',
	'gc-admin/images/lock.png',
	'gc-admin/js/theme-preview.js',
	'gc-admin/js/theme-install.min.js',
	'gc-admin/js/theme-install.js',
	'gc-admin/js/theme-preview.min.js',
	// Added back in 4.9 [41328], see #41755.
	// 4.3
	'gc-admin/js/gc-fullscreen.js',
	'gc-admin/js/gc-fullscreen.min.js',
	// 4.5
	'gc-includes/theme-compat/comments-popup.php',
	// 4.6
	'gc-admin/includes/class-gc-automatic-upgrader.php', // Wrong file name, see #37628.
	// 4.9
	'gc-admin/css/press-this-editor-rtl.css',
	'gc-admin/css/press-this-editor-rtl.min.css',
	'gc-admin/css/press-this-editor.css',
	'gc-admin/css/press-this-editor.min.css',
	'gc-admin/css/press-this-rtl.css',
	'gc-admin/css/press-this-rtl.min.css',
	'gc-admin/css/press-this.css',
	'gc-admin/css/press-this.min.css',
	'gc-admin/includes/class-gc-press-this.php',
	'gc-admin/js/bookmarklet.js',
	'gc-admin/js/bookmarklet.min.js',
	'gc-admin/js/press-this.js',
	'gc-admin/js/press-this.min.js',
	// 5.1
	'gc-includes/random_compat/random_bytes_openssl.php',
	// 5.3
	'gc-includes/js/gc-a11y.js',     // Moved to: gc-includes/js/dist/a11y.js
	'gc-includes/js/gc-a11y.min.js', // Moved to: gc-includes/js/dist/a11y.min.js
	// 5.4
	'gc-admin/js/gc-fullscreen-stub.js',
	'gc-admin/js/gc-fullscreen-stub.min.js',
	// 5.5
	'gc-admin/css/ie.css',
	'gc-admin/css/ie.min.css',
	'gc-admin/css/ie-rtl.css',
	'gc-admin/css/ie-rtl.min.css',
	// 5.7
	'gc-includes/blocks/classic/block.json',
	// 5.8
	'gc-admin/images/freedoms.png',
	'gc-admin/images/privacy.png',
	'gc-admin/images/about-badge.svg',
	'gc-admin/images/about-color-palette.svg',
	'gc-admin/images/about-color-palette-vert.svg',
	'gc-admin/images/about-header-brushes.svg',
	'gc-includes/block-patterns/large-header.php',
	'gc-includes/block-patterns/heading-paragraph.php',
	'gc-includes/block-patterns/quote.php',
	'gc-includes/block-patterns/text-three-columns-buttons.php',
	'gc-includes/block-patterns/two-buttons.php',
	'gc-includes/block-patterns/two-images.php',
	'gc-includes/block-patterns/three-buttons.php',
	'gc-includes/block-patterns/text-two-columns-with-images.php',
	'gc-includes/block-patterns/text-two-columns.php',
	'gc-includes/block-patterns/large-header-button.php',
	'gc-includes/blocks/subhead/block.json',
	'gc-includes/blocks/subhead',
	'gc-includes/css/dist/editor/editor-styles.css',
	'gc-includes/css/dist/editor/editor-styles.min.css',
	'gc-includes/css/dist/editor/editor-styles-rtl.css',
	'gc-includes/css/dist/editor/editor-styles-rtl.min.css',
	// 5.9
	'gc-includes/blocks/heading/editor.css',
	'gc-includes/blocks/heading/editor.min.css',
	'gc-includes/blocks/heading/editor-rtl.css',
	'gc-includes/blocks/heading/editor-rtl.min.css',
	'gc-includes/blocks/post-content/editor.css',
	'gc-includes/blocks/post-content/editor.min.css',
	'gc-includes/blocks/post-content/editor-rtl.css',
	'gc-includes/blocks/post-content/editor-rtl.min.css',
	'gc-includes/blocks/query-title/editor.css',
	'gc-includes/blocks/query-title/editor.min.css',
	'gc-includes/blocks/query-title/editor-rtl.css',
	'gc-includes/blocks/query-title/editor-rtl.min.css',
	'gc-includes/blocks/tag-cloud/editor.css',
	'gc-includes/blocks/tag-cloud/editor.min.css',
	'gc-includes/blocks/tag-cloud/editor-rtl.css',
	'gc-includes/blocks/tag-cloud/editor-rtl.min.css',
);

/**
 * Stores new files in gc-content to copy
 *
 * The contents of this array indicate any new bundled plugins/themes which
 * should be installed with the GeChiUI Upgrade. These items will not be
 * re-installed in future upgrades, this behaviour is controlled by the
 * introduced version present here being older than the current installed version.
 *
 * The content of this array should follow the following format:
 * Filename (relative to gc-content) => Introduced version
 * Directories should be noted by suffixing it with a trailing slash (/)
 *
 *
 *
 *              upgrade. New themes are now installed again. To disable new
 *              themes from being installed on upgrade, explicitly define
 *              CORE_UPGRADE_SKIP_NEW_BUNDLED as true.
 * @global array $_new_bundled_files
 * @var array
 * @name $_new_bundled_files
 */
global $_new_bundled_files;

$_new_bundled_files = array(
	'themes/gechiui-book/' => '5.8',
	'themes/defaultbird/' => '5.9',
);

/**
 * Upgrades the core of GeChiUI.
 *
 * This will create a .maintenance file at the base of the GeChiUI directory
 * to ensure that people can not access the web site, when the files are being
 * copied to their locations.
 *
 * The files in the `$_old_files` list will be removed and the new files
 * copied from the zip file after the database is upgraded.
 *
 * The files in the `$_new_bundled_files` list will be added to the installation
 * if the version is greater than or equal to the old version being upgraded.
 *
 * The steps for the upgrader for after the new release is downloaded and
 * unzipped is:
 *   1. Test unzipped location for select files to ensure that unzipped worked.
 *   2. Create the .maintenance file in current GeChiUI base.
 *   3. Copy new GeChiUI directory over old GeChiUI files.
 *   4. Upgrade GeChiUI to new version.
 *     4.1. Copy all files/folders other than gc-content
 *     4.2. Copy any language files to GC_LANG_DIR (which may differ from GC_CONTENT_DIR
 *     4.3. Copy any new bundled themes/plugins to their respective locations
 *   5. Delete new GeChiUI directory path.
 *   6. Delete .maintenance file.
 *   7. Remove old files.
 *   8. Delete 'update_core' option.
 *
 * There are several areas of failure. For instance if PHP times out before step
 * 6, then you will not be able to access any portion of your site. Also, since
 * the upgrade will not continue where it left off, you will not be able to
 * automatically remove old files and remove the 'update_core' option. This
 * isn't that bad.
 *
 * If the copy of the new GeChiUI over the old fails, then the worse is that
 * the new GeChiUI directory will remain.
 *
 * If it is assumed that every file will be copied over, including plugins and
 * themes, then if you edit the default theme, you should rename it, so that
 * your changes remain.
 *
 *
 *
 * @global GC_Filesystem_Base $gc_filesystem          GeChiUI filesystem subclass.
 * @global array              $_old_files
 * @global array              $_new_bundled_files
 * @global gcdb               $gcdb                   GeChiUI database abstraction object.
 * @global string             $gc_version
 * @global string             $required_php_version
 * @global string             $required_mysql_version
 *
 * @param string $from New release unzipped path.
 * @param string $to   Path to old GeChiUI installation.
 * @return string|GC_Error New GeChiUI version on success, GC_Error on failure.
 */
function update_core( $from, $to ) {
	global $gc_filesystem, $_old_files, $_new_bundled_files, $gcdb;

	set_time_limit( 300 );

	/**
	 * Filters feedback messages displayed during the core update process.
	 *
	 * The filter is first evaluated after the zip file for the latest version
	 * has been downloaded and unzipped. It is evaluated five more times during
	 * the process:
	 *
	 * 1. Before GeChiUI begins the core upgrade process.
	 * 2. Before Maintenance Mode is enabled.
	 * 3. Before GeChiUI begins copying over the necessary files.
	 * 4. Before Maintenance Mode is disabled.
	 * 5. Before the database is upgraded.
	 *
	 *
	 * @param string $feedback The core update feedback messages.
	 */
	apply_filters( 'update_feedback', __( '正在校验解压的文件&#8230;' ) );

	// Sanity check the unzipped distribution.
	$distro = '';
	$roots  = array( '/gechiui/', '/gechiui-mu/' );

	foreach ( $roots as $root ) {
		if ( $gc_filesystem->exists( $from . $root . 'readme.html' )
			&& $gc_filesystem->exists( $from . $root . 'gc-includes/version.php' )
		) {
			$distro = $root;
			break;
		}
	}

	if ( ! $distro ) {
		$gc_filesystem->delete( $from, true );

		return new GC_Error( 'insane_distro', __( '无法解压升级包' ) );
	}

	/*
	 * Import $gc_version, $required_php_version, and $required_mysql_version from the new version.
	 * DO NOT globalize any variables imported from `version-current.php` in this function.
	 *
	 * BC Note: $gc_filesystem->gc_content_dir() returned unslashed pre-2.8.
	 */
	$versions_file = trailingslashit( $gc_filesystem->gc_content_dir() ) . 'upgrade/version-current.php';

	if ( ! $gc_filesystem->copy( $from . $distro . 'gc-includes/version.php', $versions_file ) ) {
		$gc_filesystem->delete( $from, true );

		return new GC_Error(
			'copy_failed_for_version_file',
			__( '因为我们不能复制一些文件，升级未被安装。这通常是因为存在不一致的文件权限。' ),
			'gc-includes/version.php'
		);
	}

	$gc_filesystem->chmod( $versions_file, FS_CHMOD_FILE );

	/*
	 * `gc_opcache_invalidate()` only exists in GeChiUI 5.5 or later,
	 * so don't run it when upgrading from older versions.
	 */
	if ( function_exists( 'gc_opcache_invalidate' ) ) {
		gc_opcache_invalidate( $versions_file );
	}

	require GC_CONTENT_DIR . '/upgrade/version-current.php';
	$gc_filesystem->delete( $versions_file );

	$php_version       = phpversion();
	$mysql_version     = $gcdb->db_version();
	$old_gc_version    = $GLOBALS['gc_version']; // The version of GeChiUI we're updating from.
	$development_build = ( false !== strpos( $old_gc_version . $gc_version, '-' ) ); // A dash in the version indicates a development release.
	$php_compat        = version_compare( $php_version, $required_php_version, '>=' );

	if ( file_exists( GC_CONTENT_DIR . '/db.php' ) && empty( $gcdb->is_mysql ) ) {
		$mysql_compat = true;
	} else {
		$mysql_compat = version_compare( $mysql_version, $required_mysql_version, '>=' );
	}

	if ( ! $mysql_compat || ! $php_compat ) {
		$gc_filesystem->delete( $from, true );
	}

	$php_update_message = '';

	if ( function_exists( 'gc_get_update_php_url' ) ) {
		$php_update_message = '</p><p>' . sprintf(
			/* translators: %s: URL to Update PHP page. */
			__( '<a href="%s">查阅如何更新PHP</a>。' ),
			esc_url( gc_get_update_php_url() )
		);

		if ( function_exists( 'gc_get_update_php_annotation' ) ) {
			$annotation = gc_get_update_php_annotation();

			if ( $annotation ) {
				$php_update_message .= '</p><p><em>' . $annotation . '</em>';
			}
		}
	}

	if ( ! $mysql_compat && ! $php_compat ) {
		return new GC_Error(
			'php_mysql_not_compatible',
			sprintf(
				/* translators: 1: GeChiUI version number, 2: Minimum required PHP version number, 3: Minimum required MySQL version number, 4: Current PHP version number, 5: Current MySQL version number. */
				__( '升级无法完成，因为GeChiUI %1$s需要PHP %2$s或更高版本和MySQL %3$s或更高版本。而您当前的PHP版本为%4$s，MySQL版本为%5$s。' ),
				$gc_version,
				$required_php_version,
				$required_mysql_version,
				$php_version,
				$mysql_version
			) . $php_update_message
		);
	} elseif ( ! $php_compat ) {
		return new GC_Error(
			'php_not_compatible',
			sprintf(
				/* translators: 1: GeChiUI version number, 2: Minimum required PHP version number, 3: Current PHP version number. */
				__( '无法安装更新，因为GeChiUI %1$s要求PHP版本%2$s或更高，而您运行的PHP版本为%3$s。' ),
				$gc_version,
				$required_php_version,
				$php_version
			) . $php_update_message
		);
	} elseif ( ! $mysql_compat ) {
		return new GC_Error(
			'mysql_not_compatible',
			sprintf(
				/* translators: 1: GeChiUI version number, 2: Minimum required MySQL version number, 3: Current MySQL version number. */
				__( '无法安装更新，因为GeChiUI %1$s要求MySQL版本%2$s或更高，而您运行的MySQL版本为%3$s。' ),
				$gc_version,
				$required_mysql_version,
				$mysql_version
			)
		);
	}

	// Add a warning when the JSON PHP extension is missing.
	if ( ! extension_loaded( 'json' ) ) {
		return new GC_Error(
			'php_not_compatible_json',
			sprintf(
				/* translators: 1: GeChiUI version number, 2: The PHP extension name needed. */
				__( '无法安装更新，因为GeChiUI %1$s要求PHP扩展%2$s。' ),
				$gc_version,
				'JSON'
			)
		);
	}

	/** This filter is documented in gc-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( '正在准备安装最新版本&#8230;' ) );

	// Don't copy gc-content, we'll deal with that below.
	// We also copy version.php last so failed updates report their old version.
	$skip              = array( 'gc-content', 'gc-includes/version.php' );
	$check_is_writable = array();

	// Check to see which files don't really need updating - only available for 3.7 and higher.
	if ( function_exists( 'get_core_checksums' ) ) {
		// Find the local version of the working directory.
		$working_dir_local = GC_CONTENT_DIR . '/upgrade/' . basename( $from ) . $distro;

		$checksums = get_core_checksums( $gc_version, isset( $gc_local_package ) ? $gc_local_package : 'zh_CN' );

		if ( is_array( $checksums ) && isset( $checksums[ $gc_version ] ) ) {
			$checksums = $checksums[ $gc_version ]; // Compat code for 3.7-beta2.
		}

		if ( is_array( $checksums ) ) {
			foreach ( $checksums as $file => $checksum ) {
				if ( 'gc-content' === substr( $file, 0, 10 ) ) {
					continue;
				}

				if ( ! file_exists( ABSPATH . $file ) ) {
					continue;
				}

				if ( ! file_exists( $working_dir_local . $file ) ) {
					continue;
				}

				if ( '.' === dirname( $file )
					&& in_array( pathinfo( $file, PATHINFO_EXTENSION ), array( 'html', 'txt' ), true )
				) {
					continue;
				}

				if ( md5_file( ABSPATH . $file ) === $checksum ) {
					$skip[] = $file;
				} else {
					$check_is_writable[ $file ] = ABSPATH . $file;
				}
			}
		}
	}

	// If we're using the direct method, we can predict write failures that are due to permissions.
	if ( $check_is_writable && 'direct' === $gc_filesystem->method ) {
		$files_writable = array_filter( $check_is_writable, array( $gc_filesystem, 'is_writable' ) );

		if ( $files_writable !== $check_is_writable ) {
			$files_not_writable = array_diff_key( $check_is_writable, $files_writable );

			foreach ( $files_not_writable as $relative_file_not_writable => $file_not_writable ) {
				// If the writable check failed, chmod file to 0644 and try again, same as copy_dir().
				$gc_filesystem->chmod( $file_not_writable, FS_CHMOD_FILE );

				if ( $gc_filesystem->is_writable( $file_not_writable ) ) {
					unset( $files_not_writable[ $relative_file_not_writable ] );
				}
			}

			// Store package-relative paths (the key) of non-writable files in the GC_Error object.
			$error_data = version_compare( $old_gc_version, '3.7-beta2', '>' ) ? array_keys( $files_not_writable ) : '';

			if ( $files_not_writable ) {
				return new GC_Error(
					'files_not_writable',
					__( '因为我们不能复制一些文件，升级未被安装。这通常是因为存在不一致的文件权限。' ),
					implode( ', ', $error_data )
				);
			}
		}
	}

	/** This filter is documented in gc-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( '正在启用维护模式&#8230;' ) );

	// Create maintenance file to signal that we are upgrading.
	$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
	$maintenance_file   = $to . '.maintenance';
	$gc_filesystem->delete( $maintenance_file );
	$gc_filesystem->put_contents( $maintenance_file, $maintenance_string, FS_CHMOD_FILE );

	/** This filter is documented in gc-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( '正在复制所需的文件&#8230;' ) );

	// Copy new versions of GC files into place.
	$result = _copy_dir( $from . $distro, $to, $skip );

	if ( is_gc_error( $result ) ) {
		$result = new GC_Error(
			$result->get_error_code(),
			$result->get_error_message(),
			substr( $result->get_error_data(), strlen( $to ) )
		);
	}

	// Since we know the core files have copied over, we can now copy the version file.
	if ( ! is_gc_error( $result ) ) {
		if ( ! $gc_filesystem->copy( $from . $distro . 'gc-includes/version.php', $to . 'gc-includes/version.php', true /* overwrite */ ) ) {
			$gc_filesystem->delete( $from, true );
			$result = new GC_Error(
				'copy_failed_for_version_file',
				__( '因为我们不能复制一些文件，升级未被安装。这通常是因为存在不一致的文件权限。' ),
				'gc-includes/version.php'
			);
		}

		$gc_filesystem->chmod( $to . 'gc-includes/version.php', FS_CHMOD_FILE );

		/*
		 * `gc_opcache_invalidate()` only exists in GeChiUI 5.5 or later,
		 * so don't run it when upgrading from older versions.
		 */
		if ( function_exists( 'gc_opcache_invalidate' ) ) {
			gc_opcache_invalidate( $to . 'gc-includes/version.php' );
		}
	}

	// Check to make sure everything copied correctly, ignoring the contents of gc-content.
	$skip   = array( 'gc-content' );
	$failed = array();

	if ( isset( $checksums ) && is_array( $checksums ) ) {
		foreach ( $checksums as $file => $checksum ) {
			if ( 'gc-content' === substr( $file, 0, 10 ) ) {
				continue;
			}

			if ( ! file_exists( $working_dir_local . $file ) ) {
				continue;
			}

			if ( '.' === dirname( $file )
				&& in_array( pathinfo( $file, PATHINFO_EXTENSION ), array( 'html', 'txt' ), true )
			) {
				$skip[] = $file;
				continue;
			}

			if ( file_exists( ABSPATH . $file ) && md5_file( ABSPATH . $file ) === $checksum ) {
				$skip[] = $file;
			} else {
				$failed[] = $file;
			}
		}
	}

	// Some files didn't copy properly.
	if ( ! empty( $failed ) ) {
		$total_size = 0;

		foreach ( $failed as $file ) {
			if ( file_exists( $working_dir_local . $file ) ) {
				$total_size += filesize( $working_dir_local . $file );
			}
		}

		// If we don't have enough free space, it isn't worth trying again.
		// Unlikely to be hit due to the check in unzip_file().
		$available_space = function_exists( 'disk_free_space' ) ? @disk_free_space( ABSPATH ) : false;

		if ( $available_space && $total_size >= $available_space ) {
			$result = new GC_Error( 'disk_full', __( '磁盘空间不足，无法执行更新。' ) );
		} else {
			$result = _copy_dir( $from . $distro, $to, $skip );

			if ( is_gc_error( $result ) ) {
				$result = new GC_Error(
					$result->get_error_code() . '_retry',
					$result->get_error_message(),
					substr( $result->get_error_data(), strlen( $to ) )
				);
			}
		}
	}

	// Custom content directory needs updating now.
	// Copy languages.
	if ( ! is_gc_error( $result ) && $gc_filesystem->is_dir( $from . $distro . 'gc-content/languages' ) ) {
		if ( GC_LANG_DIR !== ABSPATH . GCINC . '/languages' || @is_dir( GC_LANG_DIR ) ) {
			$lang_dir = GC_LANG_DIR;
		} else {
			$lang_dir = GC_CONTENT_DIR . '/languages';
		}

		// Check if the language directory exists first.
		if ( ! @is_dir( $lang_dir ) && 0 === strpos( $lang_dir, ABSPATH ) ) {
			// If it's within the ABSPATH we can handle it here, otherwise they're out of luck.
			$gc_filesystem->mkdir( $to . str_replace( ABSPATH, '', $lang_dir ), FS_CHMOD_DIR );
			clearstatcache(); // For FTP, need to clear the stat cache.
		}

		if ( @is_dir( $lang_dir ) ) {
			$gc_lang_dir = $gc_filesystem->find_folder( $lang_dir );

			if ( $gc_lang_dir ) {
				$result = copy_dir( $from . $distro . 'gc-content/languages/', $gc_lang_dir );

				if ( is_gc_error( $result ) ) {
					$result = new GC_Error(
						$result->get_error_code() . '_languages',
						$result->get_error_message(),
						substr( $result->get_error_data(), strlen( $gc_lang_dir ) )
					);
				}
			}
		}
	}

	/** This filter is documented in gc-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( '正在停用维护模式&#8230;' ) );

	// Remove maintenance file, we're done with potential site-breaking changes.
	$gc_filesystem->delete( $maintenance_file );

	/*
	 * Copy new bundled plugins & themes.
	 * This gives us the ability to install new plugins & themes bundled with
	 * future versions of GeChiUI whilst avoiding the re-install upon upgrade issue.
	 * $development_build controls us overwriting bundled themes and plugins when a non-stable release is being updated.
	 */
	if ( ! is_gc_error( $result )
		&& ( ! defined( 'CORE_UPGRADE_SKIP_NEW_BUNDLED' ) || ! CORE_UPGRADE_SKIP_NEW_BUNDLED )
	) {
		foreach ( (array) $_new_bundled_files as $file => $introduced_version ) {
			// If a $development_build or if $introduced version is greater than what the site was previously running.
			if ( $development_build || version_compare( $introduced_version, $old_gc_version, '>' ) ) {
				$directory = ( '/' === $file[ strlen( $file ) - 1 ] );

				list( $type, $filename ) = explode( '/', $file, 2 );

				// Check to see if the bundled items exist before attempting to copy them.
				if ( ! $gc_filesystem->exists( $from . $distro . 'gc-content/' . $file ) ) {
					continue;
				}

				if ( 'plugins' === $type ) {
					$dest = $gc_filesystem->gc_plugins_dir();
				} elseif ( 'themes' === $type ) {
					// Back-compat, ::gc_themes_dir() did not return trailingslash'd pre-3.2.
					$dest = trailingslashit( $gc_filesystem->gc_themes_dir() );
				} else {
					continue;
				}

				if ( ! $directory ) {
					if ( ! $development_build && $gc_filesystem->exists( $dest . $filename ) ) {
						continue;
					}

					if ( ! $gc_filesystem->copy( $from . $distro . 'gc-content/' . $file, $dest . $filename, FS_CHMOD_FILE ) ) {
						$result = new GC_Error( "copy_failed_for_new_bundled_$type", __( '无法复制文件。' ), $dest . $filename );
					}
				} else {
					if ( ! $development_build && $gc_filesystem->is_dir( $dest . $filename ) ) {
						continue;
					}

					$gc_filesystem->mkdir( $dest . $filename, FS_CHMOD_DIR );
					$_result = copy_dir( $from . $distro . 'gc-content/' . $file, $dest . $filename );

					// If a error occurs partway through this final step, keep the error flowing through, but keep process going.
					if ( is_gc_error( $_result ) ) {
						if ( ! is_gc_error( $result ) ) {
							$result = new GC_Error;
						}

						$result->add(
							$_result->get_error_code() . "_$type",
							$_result->get_error_message(),
							substr( $_result->get_error_data(), strlen( $dest ) )
						);
					}
				}
			}
		} // End foreach.
	}

	// Handle $result error from the above blocks.
	if ( is_gc_error( $result ) ) {
		$gc_filesystem->delete( $from, true );

		return $result;
	}

	// Remove old files.
	foreach ( $_old_files as $old_file ) {
		$old_file = $to . $old_file;

		if ( ! $gc_filesystem->exists( $old_file ) ) {
			continue;
		}

		// If the file isn't deleted, try writing an empty string to the file instead.
		if ( ! $gc_filesystem->delete( $old_file, true ) && $gc_filesystem->is_file( $old_file ) ) {
			$gc_filesystem->put_contents( $old_file, '' );
		}
	}

	// Remove any Genericons example.html's from the filesystem.
	_upgrade_422_remove_genericons();

	// Deactivate the REST API plugin if its version is 2.0 Beta 4 or lower.
	_upgrade_440_force_deactivate_incompatible_plugins();

	// Deactivate the Gutenberg plugin if its version is 11.8 or lower.
	_upgrade_590_force_deactivate_incompatible_plugins();

	// Upgrade DB with separate request.
	/** This filter is documented in gc-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( '正在升级数据库&#8230;' ) );

	$db_upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
	gc_remote_post( $db_upgrade_url, array( 'timeout' => 60 ) );

	// Clear the cache to prevent an update_option() from saving a stale db_version to the cache.
	gc_cache_flush();
	// Not all cache back ends listen to 'flush'.
	gc_cache_delete( 'alloptions', 'options' );

	// Remove working directory.
	$gc_filesystem->delete( $from, true );

	// Force refresh of update information.
	if ( function_exists( 'delete_site_transient' ) ) {
		delete_site_transient( 'update_core' );
	} else {
		delete_option( 'update_core' );
	}

	/**
	 * Fires after GeChiUI core has been successfully updated.
	 *
	 *
	 * @param string $gc_version The current GeChiUI version.
	 */
	do_action( '_core_updated_successfully', $gc_version );

	// Clear the option that blocks auto-updates after failures, now that we've been successful.
	if ( function_exists( 'delete_site_option' ) ) {
		delete_site_option( 'auto_core_update_failed' );
	}

	return $gc_version;
}

/**
 * Copies a directory from one location to another via the GeChiUI Filesystem Abstraction.
 *
 * Assumes that GC_Filesystem() has already been called and setup.
 *
 * This is a standalone copy of the `copy_dir()` function that is used to
 * upgrade the core files. It is placed here so that the version of this
 * function from the *new* GeChiUI version will be called.
 *
 * It was initially added for the 3.1 -> 3.2 upgrade.
 *
 * @ignore
 *
 *
 *
 * @see copy_dir()
 * @link https://core.trac.gechiui.com/ticket/17173
 *
 * @global GC_Filesystem_Base $gc_filesystem
 *
 * @param string   $from      Source directory.
 * @param string   $to        Destination directory.
 * @param string[] $skip_list Array of files/folders to skip copying.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function _copy_dir( $from, $to, $skip_list = array() ) {
	global $gc_filesystem;

	$dirlist = $gc_filesystem->dirlist( $from );

	if ( false === $dirlist ) {
		return new GC_Error( 'dirlist_failed__copy_dir', __( '无法显示目录列表。' ), basename( $to ) );
	}

	$from = trailingslashit( $from );
	$to   = trailingslashit( $to );

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list, true ) ) {
			continue;
		}

		if ( 'f' === $fileinfo['type'] ) {
			if ( ! $gc_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
				// If copy failed, chmod file to 0644 and try again.
				$gc_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );

				if ( ! $gc_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
					return new GC_Error( 'copy_failed__copy_dir', __( '无法复制文件。' ), $to . $filename );
				}
			}

			/*
			 * `gc_opcache_invalidate()` only exists in GeChiUI 5.5 or later,
			 * so don't run it when upgrading from older versions.
			 */
			if ( function_exists( 'gc_opcache_invalidate' ) ) {
				gc_opcache_invalidate( $to . $filename );
			}
		} elseif ( 'd' === $fileinfo['type'] ) {
			if ( ! $gc_filesystem->is_dir( $to . $filename ) ) {
				if ( ! $gc_filesystem->mkdir( $to . $filename, FS_CHMOD_DIR ) ) {
					return new GC_Error( 'mkdir_failed__copy_dir', __( '无法创建目录。' ), $to . $filename );
				}
			}

			/*
			 * Generate the $sub_skip_list for the subdirectory as a sub-set
			 * of the existing $skip_list.
			 */
			$sub_skip_list = array();

			foreach ( $skip_list as $skip_item ) {
				if ( 0 === strpos( $skip_item, $filename . '/' ) ) {
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
				}
			}

			$result = _copy_dir( $from . $filename, $to . $filename, $sub_skip_list );

			if ( is_gc_error( $result ) ) {
				return $result;
			}
		}
	}

	return true;
}

/**
 * Redirect to the About GeChiUI page after a successful upgrade.
 *
 * This function is only needed when the existing installation is older than 3.4.0.
 *
 *
 *
 * @global string $gc_version The GeChiUI version string.
 * @global string $pagenow
 * @global string $action
 *
 * @param string $new_version
 */
function _redirect_to_about_gechiui( $new_version ) {
	global $gc_version, $pagenow, $action;

	if ( version_compare( $gc_version, '3.4-RC1', '>=' ) ) {
		return;
	}

	// Ensure we only run this on the update-core.php page. The Core_Upgrader may be used in other contexts.
	if ( 'update-core.php' !== $pagenow ) {
		return;
	}

	if ( 'do-core-upgrade' !== $action && 'do-core-reinstall' !== $action ) {
		return;
	}

	// Load the updated default text localization domain for new strings.
	load_default_textdomain();

	// See do_core_upgrade().
	show_message( __( 'GeChiUI升级成功。' ) );

	// self_admin_url() won't exist when upgrading from <= 3.0, so relative URLs are intentional.
	show_message(
		'<span class="hide-if-no-js">' . sprintf(
			/* translators: 1: GeChiUI version, 2: URL to About screen. */
			__( '欢迎使用GeChiUI %1$s。我们将带您到“关于GeChiUI”页面。如果没有自动跳转，请<a href="%2$s">点击这里</a>。' ),
			$new_version,
			'about.php?updated'
		) . '</span>'
	);
	show_message(
		'<span class="hide-if-js">' . sprintf(
			/* translators: 1: GeChiUI version, 2: URL to About screen. */
			__( '欢迎使用GeChiUI %1$s。<a href="%2$s">了解更多</a>。' ),
			$new_version,
			'about.php?updated'
		) . '</span>'
	);
	echo '</div>';
	?>
<script type="text/javascript">
window.location = 'about.php?updated';
</script>
	<?php

	// Include admin-footer.php and exit.
	require_once ABSPATH . 'gc-admin/admin-footer.php';
	exit;
}

/**
 * Cleans up Genericons example files.
 *
 *
 *
 * @global array              $gc_theme_directories
 * @global GC_Filesystem_Base $gc_filesystem
 */
function _upgrade_422_remove_genericons() {
	global $gc_theme_directories, $gc_filesystem;

	// A list of the affected files using the filesystem absolute paths.
	$affected_files = array();

	// Themes.
	foreach ( $gc_theme_directories as $directory ) {
		$affected_theme_files = _upgrade_422_find_genericons_files_in_folder( $directory );
		$affected_files       = array_merge( $affected_files, $affected_theme_files );
	}

	// Plugins.
	$affected_plugin_files = _upgrade_422_find_genericons_files_in_folder( GC_PLUGIN_DIR );
	$affected_files        = array_merge( $affected_files, $affected_plugin_files );

	foreach ( $affected_files as $file ) {
		$gen_dir = $gc_filesystem->find_folder( trailingslashit( dirname( $file ) ) );

		if ( empty( $gen_dir ) ) {
			continue;
		}

		// The path when the file is accessed via GC_Filesystem may differ in the case of FTP.
		$remote_file = $gen_dir . basename( $file );

		if ( ! $gc_filesystem->exists( $remote_file ) ) {
			continue;
		}

		if ( ! $gc_filesystem->delete( $remote_file, false, 'f' ) ) {
			$gc_filesystem->put_contents( $remote_file, '' );
		}
	}
}

/**
 * Recursively find Genericons example files in a given folder.
 *
 * @ignore
 *
 *
 * @param string $directory Directory path. Expects trailingslashed.
 * @return array
 */
function _upgrade_422_find_genericons_files_in_folder( $directory ) {
	$directory = trailingslashit( $directory );
	$files     = array();

	if ( file_exists( "{$directory}example.html" )
		&& false !== strpos( file_get_contents( "{$directory}example.html" ), '<title>Genericons</title>' )
	) {
		$files[] = "{$directory}example.html";
	}

	$dirs = glob( $directory . '*', GLOB_ONLYDIR );
	$dirs = array_filter(
		$dirs,
		static function( $dir ) {
			// Skip any node_modules directories.
			return false === strpos( $dir, 'node_modules' );
		}
	);

	if ( $dirs ) {
		foreach ( $dirs as $dir ) {
			$files = array_merge( $files, _upgrade_422_find_genericons_files_in_folder( $dir ) );
		}
	}

	return $files;
}

/**
 * @ignore
 *
 */
function _upgrade_440_force_deactivate_incompatible_plugins() {
	if ( defined( 'REST_API_VERSION' ) && version_compare( REST_API_VERSION, '2.0-beta4', '<=' ) ) {
		deactivate_plugins( array( 'rest-api/plugin.php' ), true );
	}
}

/**
 * @access private
 * @ignore
 *
 */
function _upgrade_590_force_deactivate_incompatible_plugins() {
	if ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '11.9', '<' ) ) {
		$deactivated_gutenberg['gutenberg'] = array(
			'plugin_name'         => 'Gutenberg',
			'version_deactivated' => GUTENBERG_VERSION,
			'version_compatible'  => '11.9',
		);
		if ( is_plugin_active_for_network( 'gutenberg/gutenberg.php' ) ) {
			$deactivated_plugins = get_site_option( 'gc_force_deactivated_plugins', array() );
			$deactivated_plugins = array_merge( $deactivated_plugins, $deactivated_gutenberg );
			update_site_option( 'gc_force_deactivated_plugins', $deactivated_plugins );
		} else {
			$deactivated_plugins = get_option( 'gc_force_deactivated_plugins', array() );
			$deactivated_plugins = array_merge( $deactivated_plugins, $deactivated_gutenberg );
			update_option( 'gc_force_deactivated_plugins', $deactivated_plugins );
		}
		deactivate_plugins( array( 'gutenberg/gutenberg.php' ), true );
	}
}
