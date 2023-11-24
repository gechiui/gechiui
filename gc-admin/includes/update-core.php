<?php
/**
 * GeChiUI core upgrade functionality.
 *
 * @package GeChiUI
 * @subpackage Administration
 * @since 2.7.0
 */

/**
 * Stores files to be deleted.
 *
 * Bundled theme files should not be included in this list.
 *
 * @since 2.7.0
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
	// 2.0.8
	'gc-includes/vendors/tinymce/plugins/inlinepopups/readme.txt',
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
	'gc-includes/vendors/tinymce/plugins/autosave/langs/cs.js',
	'gc-includes/vendors/tinymce/plugins/autosave/langs/sv.js',
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
	'gc-includes/vendors/tinymce/license.html',
	// 2.5
	'gc-admin/css/upload.css',
	'assets/images/box-bg-left.gif',
	'assets/images/box-bg-right.gif',
	'assets/images/box-bg.gif',
	'assets/images/box-butt-left.gif',
	'assets/images/box-butt-right.gif',
	'assets/images/box-butt.gif',
	'assets/images/box-head-left.gif',
	'assets/images/box-head-right.gif',
	'assets/images/box-head.gif',
	'assets/images/heading-bg.gif',
	'assets/images/login-bkg-bottom.gif',
	'assets/images/login-bkg-tile.gif',
	'assets/images/notice.gif',
	'assets/images/toggle.gif',
	'gc-admin/includes/upload.php',
	'gc-admin/js/dbx-admin-key.js',
	'gc-admin/js/link-cat.js',
	'gc-admin/profile-update.php',
	'gc-admin/templates.php',
	'assets/images/wlw/GcComments.png',
	'assets/images/wlw/GcIcon.png',
	'assets/images/wlw/GcWatermark.png',
	'gc-includes/js/dbx.js',
	'gc-includes/js/fat.js',
	'gc-includes/js/list-manipulation.js',
	'gc-includes/vendors/tinymce/langs/en.js',
	'gc-includes/vendors/tinymce/plugins/autosave/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/autosave/langs',
	'gc-includes/vendors/tinymce/plugins/directionality/images',
	'gc-includes/vendors/tinymce/plugins/directionality/langs',
	'gc-includes/vendors/tinymce/plugins/inlinepopups/css',
	'gc-includes/vendors/tinymce/plugins/inlinepopups/images',
	'gc-includes/vendors/tinymce/plugins/inlinepopups/jscripts',
	'gc-includes/vendors/tinymce/plugins/paste/images',
	'gc-includes/vendors/tinymce/plugins/paste/jscripts',
	'gc-includes/vendors/tinymce/plugins/paste/langs',
	'gc-includes/vendors/tinymce/plugins/spellchecker/classes/HttpClient.class.php',
	'gc-includes/vendors/tinymce/plugins/spellchecker/classes/TinyGoogleSpell.class.php',
	'gc-includes/vendors/tinymce/plugins/spellchecker/classes/TinyPspell.class.php',
	'gc-includes/vendors/tinymce/plugins/spellchecker/classes/TinyPspellShell.class.php',
	'gc-includes/vendors/tinymce/plugins/spellchecker/css/spellchecker.css',
	'gc-includes/vendors/tinymce/plugins/spellchecker/images',
	'gc-includes/vendors/tinymce/plugins/spellchecker/langs',
	'gc-includes/vendors/tinymce/plugins/spellchecker/tinyspell.php',
	'gc-includes/vendors/tinymce/plugins/gechiui/images',
	'gc-includes/vendors/tinymce/plugins/gechiui/langs',
	'gc-includes/vendors/tinymce/plugins/gechiui/gechiui.css',
	'gc-includes/vendors/tinymce/plugins/gchelp',
	'gc-includes/vendors/tinymce/themes/advanced/css',
	'gc-includes/vendors/tinymce/themes/advanced/images',
	'gc-includes/vendors/tinymce/themes/advanced/jscripts',
	'gc-includes/vendors/tinymce/themes/advanced/langs',
	// 2.5.1
	'gc-includes/vendors/tinymce/tiny_mce_gzip.php',
	// 2.6
	'gc-admin/bookmarklet.php',
	'gc-includes/vendors/jquery/jquery.dimensions.min.js',
	'gc-includes/vendors/tinymce/plugins/gechiui/popups.css',
	'gc-includes/js/gc-ajax.js',
	// 2.7
	'gc-admin/css/press-this-ie-rtl.css',
	'gc-admin/css/press-this-ie.css',
	'gc-admin/css/upload-rtl.css',
	'gc-admin/edit-form.php',
	'assets/images/comment-pill.gif',
	'assets/images/comment-stalk-classic.gif',
	'assets/images/comment-stalk-fresh.gif',
	'assets/images/comment-stalk-rtl.gif',
	'assets/images/del.png',
	'assets/images/gear.png',
	'assets/images/media-button-gallery.gif',
	'assets/images/media-buttons.gif',
	'assets/images/postbox-bg.gif',
	'assets/images/tab.png',
	'assets/images/tail.gif',
	'gc-admin/js/forms.js',
	'gc-admin/js/upload.js',
	'gc-admin/link-import.php',
	'assets/images/audio.png',
	'assets/images/css.png',
	'assets/images/default.png',
	'assets/images/doc.png',
	'assets/images/exe.png',
	'assets/images/html.png',
	'assets/images/js.png',
	'assets/images/pdf.png',
	'assets/images/swf.png',
	'assets/images/tar.png',
	'assets/images/text.png',
	'assets/images/video.png',
	'assets/images/zip.png',
	'gc-includes/vendors/tinymce/tiny_mce_config.php',
	'gc-includes/vendors/tinymce/tiny_mce_ext.js',
	// 2.8
	'gc-admin/js/users.js',
	'gc-includes/vendors/swfupload/plugins/swfupload.documentready.js',
	'gc-includes/vendors/swfupload/plugins/swfupload.graceful_degradation.js',
	'gc-includes/vendors/swfupload/swfupload_f9.swf',
	'gc-includes/vendors/tinymce/plugins/autosave',
	'gc-includes/vendors/tinymce/plugins/paste/css',
	'gc-includes/vendors/tinymce/utils/mclayer.js',
	'gc-includes/vendors/tinymce/gechiui.css',
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
	'gc-includes/vendors/tinymce/plugins/gechiui/js',
	'gc-includes/streams.php',
	// MU
	'README.txt',
	'htaccess.dist',
	'index-install.php',
	'gc-admin/css/mu-rtl.css',
	'gc-admin/css/mu.css',
	'assets/images/site-admin.png',
	'gc-admin/includes/mu.php',
	'gc-admin/gcmu-admin.php',
	'gc-admin/gcmu-blogs.php',
	'gc-admin/gcmu-edit.php',
	'gc-admin/gcmu-options.php',
	'gc-admin/gcmu-themes.php',
	'gc-admin/gcmu-upgrade-site.php',
	'gc-admin/gcmu-users.php',
	'assets/images/gechiui-mu.png',
	'gc-includes/gcmu-default-filters.php',
	'gc-includes/gcmu-functions.php',
	'gcmu-settings.php',
	// 3.0
	'gc-admin/categories.php',
	'gc-admin/edit-category-form.php',
	'gc-admin/edit-page-form.php',
	'gc-admin/edit-pages.php',
	'assets/images/admin-header-footer.png',
	'assets/images/browse-happy.gif',
	'assets/images/ico-add.png',
	'assets/images/ico-close.png',
	'assets/images/ico-edit.png',
	'assets/images/ico-viewpage.png',
	'assets/images/fav-top.png',
	'assets/images/screen-options-left.gif',
	'assets/images/gc-logo-vs.gif',
	'assets/images/gc-logo.gif',
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
	'gc-includes/vendors/jquery/autocomplete.dev.js',
	'gc-includes/vendors/jquery/autocomplete.js',
	'gc-includes/vendors/jquery/interface.js',
	'gc-includes/js/scriptaculous/prototype.js',
	// Following file added back in 5.1, see #45645.
	//'gc-includes/vendors/tinymce/gc-tinymce.js',
	// 3.1
	'gc-admin/edit-attachment-rows.php',
	'gc-admin/edit-link-categories.php',
	'gc-admin/edit-link-category-form.php',
	'gc-admin/edit-post-rows.php',
	'assets/images/button-grad-active-vs.png',
	'assets/images/button-grad-vs.png',
	'assets/images/fav-arrow-vs-rtl.gif',
	'assets/images/fav-arrow-vs.gif',
	'assets/images/fav-top-vs.gif',
	'assets/images/list-vs.png',
	'assets/images/screen-options-right-up.gif',
	'assets/images/screen-options-right.gif',
	'assets/images/visit-site-button-grad-vs.gif',
	'assets/images/visit-site-button-grad.gif',
	'gc-admin/link-category.php',
	'gc-admin/sidebar.php',
	'gc-includes/classes.php',
	'gc-includes/vendors/tinymce/blank.htm',
	'gc-includes/vendors/tinymce/plugins/media/css/content.css',
	'gc-includes/vendors/tinymce/plugins/media/img',
	'gc-includes/vendors/tinymce/plugins/safari',
	// 3.2
	'assets/images/logo-login.gif',
	'assets/images/star.gif',
	'gc-admin/js/list-table.dev.js',
	'gc-admin/js/list-table.js',
	'gc-includes/default-embeds.php',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/help.gif',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/more.gif',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/toolbars.gif',
	'gc-includes/vendors/tinymce/themes/advanced/img/fm.gif',
	'gc-includes/vendors/tinymce/themes/advanced/img/sflogo.png',
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
	'assets/images/admin-bar-sprite-rtl.png',
	'gc-includes/vendors/jquery/ui.button.js',
	'gc-includes/vendors/jquery/ui.core.js',
	'gc-includes/vendors/jquery/ui.dialog.js',
	'gc-includes/vendors/jquery/ui.draggable.js',
	'gc-includes/vendors/jquery/ui.droppable.js',
	'gc-includes/vendors/jquery/ui.mouse.js',
	'gc-includes/vendors/jquery/ui.position.js',
	'gc-includes/vendors/jquery/ui.resizable.js',
	'gc-includes/vendors/jquery/ui.selectable.js',
	'gc-includes/vendors/jquery/ui.sortable.js',
	'gc-includes/vendors/jquery/ui.tabs.js',
	'gc-includes/vendors/jquery/ui.widget.js',
	'gc-includes/js/l10n.dev.js',
	'gc-includes/js/l10n.js',
	'gc-includes/vendors/tinymce/plugins/gclink/css',
	'gc-includes/vendors/tinymce/plugins/gclink/img',
	'gc-includes/vendors/tinymce/plugins/gclink/js',
	'gc-includes/vendors/tinymce/themes/advanced/img/gcicons.png',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/butt2.png',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/button_bg.png',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/down_arrow.gif',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/fade-butt.png',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/separator.gif',
	// Don't delete, yet: 'gc-rss.php',
	// Don't delete, yet: 'gc-rdf.php',
	// Don't delete, yet: 'gc-rss2.php',
	// Don't delete, yet: 'gc-commentsrss2.php',
	// Don't delete, yet: 'gc-atom.php',
	// Don't delete, yet: 'gc-feed.php',
	// 3.4
	'assets/images/gray-star.png',
	'assets/images/logo-login.png',
	'assets/images/star.png',
	'gc-admin/index-extra.php',
	'gc-admin/network/index-extra.php',
	'gc-admin/user/index-extra.php',
	'assets/images/screenshots/admin-flyouts.png',
	'assets/images/screenshots/coediting.png',
	'assets/images/screenshots/drag-and-drop.png',
	'assets/images/screenshots/help-screen.png',
	'assets/images/screenshots/media-icon.png',
	'assets/images/screenshots/new-feature-pointer.png',
	'assets/images/screenshots/welcome-screen.png',
	'gc-includes/css/editor-buttons.css',
	'gc-includes/css/editor-buttons.dev.css',
	'gc-includes/vendors/tinymce/plugins/paste/blank.htm',
	'gc-includes/vendors/tinymce/plugins/gechiui/css',
	'gc-includes/vendors/tinymce/plugins/gechiui/editor_plugin.dev.js',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/embedded.png',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/more_bug.gif',
	'gc-includes/vendors/tinymce/plugins/gechiui/img/page_bug.gif',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/editor_plugin.dev.js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/css/editimage-rtl.css',
	'gc-includes/vendors/tinymce/plugins/gceditimage/editor_plugin.dev.js',
	'gc-includes/vendors/tinymce/plugins/gcfullscreen/editor_plugin.dev.js',
	'gc-includes/vendors/tinymce/plugins/gcgallery/editor_plugin.dev.js',
	'gc-includes/vendors/tinymce/plugins/gcgallery/img/gallery.png',
	'gc-includes/vendors/tinymce/plugins/gclink/editor_plugin.dev.js',
	// Don't delete, yet: 'gc-pass.php',
	// Don't delete, yet: 'gc-register.php',
	// 3.5
	'gc-admin/gears-manifest.php',
	'gc-admin/includes/manifest.php',
	'assets/images/archive-link.png',
	'assets/images/blue-grad.png',
	'assets/images/button-grad-active.png',
	'assets/images/button-grad.png',
	'assets/images/ed-bg-vs.gif',
	'assets/images/ed-bg.gif',
	'assets/images/fade-butt.png',
	'assets/images/fav-arrow-rtl.gif',
	'assets/images/fav-arrow.gif',
	'assets/images/fav-vs.png',
	'assets/images/fav.png',
	'assets/images/gray-grad.png',
	'assets/images/loading-publish.gif',
	'assets/images/logo-ghost.png',
	'assets/images/logo.gif',
	'assets/images/menu-arrow-frame-rtl.png',
	'assets/images/menu-arrow-frame.png',
	'assets/images/menu-arrows.gif',
	'assets/images/menu-bits-rtl-vs.gif',
	'assets/images/menu-bits-rtl.gif',
	'assets/images/menu-bits-vs.gif',
	'assets/images/menu-bits.gif',
	'assets/images/menu-dark-rtl-vs.gif',
	'assets/images/menu-dark-rtl.gif',
	'assets/images/menu-dark-vs.gif',
	'assets/images/menu-dark.gif',
	'assets/images/required.gif',
	'assets/images/screen-options-toggle-vs.gif',
	'assets/images/screen-options-toggle.gif',
	'assets/images/toggle-arrow-rtl.gif',
	'assets/images/toggle-arrow.gif',
	'assets/images/upload-classic.png',
	'assets/images/upload-fresh.png',
	'assets/images/white-grad-active.png',
	'assets/images/white-grad.png',
	'assets/images/widgets-arrow-vs.gif',
	'assets/images/widgets-arrow.gif',
	'assets/images/gcspin_dark.gif',
	'assets/images/upload.png',
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
	'gc-includes/vendors/json2.dev.js',
	'gc-includes/js/comment-reply.dev.js',
	'gc-includes/js/customize-preview.dev.js',
	'gc-includes/js/gclink.dev.js',
	'gc-includes/vendors/tw-sack.dev.js',
	'gc-includes/js/gc-list-revisions.dev.js',
	'gc-includes/js/autosave.dev.js',
	'gc-includes/js/admin-bar.dev.js',
	'gc-includes/js/quicktags.dev.js',
	'gc-includes/js/gc-ajax-response.dev.js',
	'gc-includes/js/gc-pointer.dev.js',
	'gc-includes/vendors/hoverintent.dev.js',
	'gc-includes/vendors/colorpicker.dev.js',
	'gc-includes/js/gc-lists.dev.js',
	'gc-includes/js/customize-loader.dev.js',
	'gc-includes/vendors/jquery/jquery.table-hotkeys.dev.js',
	'gc-includes/vendors/jquery/jquery.color.dev.js',
	'gc-includes/vendors/jquery/jquery.color.js',
	'gc-includes/vendors/jquery/jquery.hotkeys.dev.js',
	'gc-includes/vendors/jquery/jquery.form.dev.js',
	'gc-includes/vendors/jquery/suggest.dev.js',
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
	'gc-includes/vendors/tinymce/plugins/gceditimage/js/editimage.dev.js',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/js/popup.dev.js',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/js/gcdialog.dev.js',
	'gc-includes/vendors/plupload/handlers.dev.js',
	'gc-includes/vendors/plupload/gc-plupload.dev.js',
	'gc-includes/vendors/swfupload/handlers.dev.js',
	'gc-includes/vendors/jcrop/jquery.Jcrop.dev.js',
	'gc-includes/vendors/jcrop/jquery.Jcrop.js',
	'gc-includes/vendors/jcrop/jquery.Jcrop.css',
	'gc-includes/vendors/imgareaselect/jquery.imgareaselect.dev.js',
	'gc-includes/css/gc-pointer.dev.css',
	'gc-includes/css/editor.dev.css',
	'gc-includes/css/jquery-ui-dialog.dev.css',
	'gc-includes/css/admin-bar-rtl.dev.css',
	'gc-includes/css/admin-bar.dev.css',
	'gc-includes/vendors/jquery/ui/jquery.effects.clip.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.scale.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.blind.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.core.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.shake.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.fade.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.explode.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.slide.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.drop.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.highlight.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.bounce.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.pulsate.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.transfer.min.js',
	'gc-includes/vendors/jquery/ui/jquery.effects.fold.min.js',
	'assets/images/screenshots/captions-1.png',
	'assets/images/screenshots/captions-2.png',
	'assets/images/screenshots/flex-header-1.png',
	'assets/images/screenshots/flex-header-2.png',
	'assets/images/screenshots/flex-header-3.png',
	'assets/images/screenshots/flex-header-media-library.png',
	'assets/images/screenshots/theme-customizer.png',
	'assets/images/screenshots/twitter-embed-1.png',
	'assets/images/screenshots/twitter-embed-2.png',
	'gc-admin/js/utils.js',
	// Added back in 5.3 [45448], see #43895.
	// 'gc-admin/options-privacy.php',
	'gc-app.php',
	'gc-includes/class-gc-atom-server.php',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/ui.css',
	// 3.5.2
	'gc-includes/vendors/swfupload/swfupload-all.js',
	// 3.6
	'gc-admin/js/revisions-js.php',
	'assets/images/screenshots',
	'gc-admin/js/categories.js',
	'gc-admin/js/categories.min.js',
	'gc-admin/js/custom-fields.js',
	'gc-admin/js/custom-fields.min.js',
	// 3.7
	'gc-admin/js/cat.js',
	'gc-admin/js/cat.min.js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/js/editimage.min.js',
	// 3.8
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/page_bug.gif',
	'gc-includes/vendors/tinymce/themes/advanced/skins/gc_theme/img/more_bug.gif',
	'gc-includes/vendors/thickbox/tb-close-2x.png',
	'gc-includes/vendors/thickbox/tb-close.png',
	'assets/images/gcmini-blue-2x.png',
	'assets/images/gcmini-blue.png',
	'gc-admin/css/colors-fresh.css',
	'gc-admin/css/colors-classic.css',
	'gc-admin/css/colors-fresh.min.css',
	'gc-admin/css/colors-classic.min.css',
	'gc-admin/js/about.min.js',
	'gc-admin/js/about.js',
	'assets/images/arrows-dark-vs-2x.png',
	'assets/images/gc-logo-vs.png',
	'assets/images/arrows-dark-vs.png',
	'assets/images/gc-logo.png',
	'assets/images/arrows-pr.png',
	'assets/images/arrows-dark.png',
	'assets/images/press-this.png',
	'assets/images/press-this-2x.png',
	'assets/images/arrows-vs-2x.png',
	'assets/images/welcome-icons.png',
	'assets/images/gc-logo-2x.png',
	'assets/images/stars-rtl-2x.png',
	'assets/images/arrows-dark-2x.png',
	'assets/images/arrows-pr-2x.png',
	'assets/images/menu-shadow-rtl.png',
	'assets/images/arrows-vs.png',
	'assets/images/about-search-2x.png',
	'assets/images/bubble_bg-rtl-2x.gif',
	'assets/images/gc-badge-2x.png',
	'assets/images/gechiui-logo-2x.png',
	'assets/images/bubble_bg-rtl.gif',
	'assets/images/gc-badge.png',
	'assets/images/menu-shadow.png',
	'assets/images/about-globe-2x.png',
	'assets/images/welcome-icons-2x.png',
	'assets/images/stars-rtl.png',
	'assets/images/gc-logo-vs-2x.png',
	'assets/images/about-updates-2x.png',
	// 3.9
	'gc-admin/css/colors.css',
	'gc-admin/css/colors.min.css',
	'gc-admin/css/colors-rtl.css',
	'gc-admin/css/colors-rtl.min.css',
	// Following files added back in 4.5, see #36083.
	// 'gc-admin/css/media-rtl.min.css',
	// 'gc-admin/css/media.min.css',
	// 'gc-admin/css/farbtastic-rtl.min.css',
	'assets/images/lock-2x.png',
	'assets/images/lock.png',
	'gc-admin/js/theme-preview.js',
	'gc-admin/js/theme-install.min.js',
	'gc-admin/js/theme-install.js',
	'gc-admin/js/theme-preview.min.js',
	'gc-includes/vendors/plupload/plupload.html4.js',
	'gc-includes/vendors/plupload/plupload.html5.js',
	'gc-includes/vendors/plupload/changelog.txt',
	'gc-includes/vendors/plupload/plupload.silverlight.js',
	'gc-includes/vendors/plupload/plupload.flash.js',
	// Added back in 4.9 [41328], see #41755.
	// 'gc-includes/vendors/plupload/plupload.js',
	'gc-includes/vendors/tinymce/plugins/spellchecker',
	'gc-includes/vendors/tinymce/plugins/inlinepopups',
	'gc-includes/vendors/tinymce/plugins/media/js',
	'gc-includes/vendors/tinymce/plugins/media/css',
	'gc-includes/vendors/tinymce/plugins/gechiui/img',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/img',
	'gc-includes/vendors/tinymce/plugins/gceditimage/js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/css',
	'gc-includes/vendors/tinymce/plugins/gcgallery/img',
	'gc-includes/vendors/tinymce/plugins/gcfullscreen/css',
	'gc-includes/vendors/tinymce/plugins/paste/js',
	'gc-includes/vendors/tinymce/themes/advanced',
	'gc-includes/vendors/tinymce/tiny_mce.js',
	'gc-includes/vendors/tinymce/mark_loaded_src.js',
	'gc-includes/vendors/tinymce/gc-tinymce-schema.js',
	'gc-includes/vendors/tinymce/plugins/media/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/media/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/media/media.htm',
	'gc-includes/vendors/tinymce/plugins/gcview/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gcview/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/directionality/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/directionality/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gechiui/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gechiui/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gcdialogs/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/editimage.html',
	'gc-includes/vendors/tinymce/plugins/gceditimage/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gceditimage/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/fullscreen/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/fullscreen/fullscreen.htm',
	'gc-includes/vendors/tinymce/plugins/fullscreen/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gclink/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gclink/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gcgallery/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gcgallery/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/tabfocus/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/tabfocus/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/gcfullscreen/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/gcfullscreen/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/paste/editor_plugin.js',
	'gc-includes/vendors/tinymce/plugins/paste/pasteword.htm',
	'gc-includes/vendors/tinymce/plugins/paste/editor_plugin_src.js',
	'gc-includes/vendors/tinymce/plugins/paste/pastetext.htm',
	'gc-includes/vendors/tinymce/langs/gc-langs.php',
	// 4.1
	'gc-includes/vendors/jquery/ui/jquery.ui.accordion.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.autocomplete.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.button.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.core.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.datepicker.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.dialog.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.draggable.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.droppable.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-blind.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-bounce.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-clip.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-drop.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-explode.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-fade.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-fold.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-highlight.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-pulsate.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-scale.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-shake.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-slide.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect-transfer.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.effect.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.menu.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.mouse.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.position.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.progressbar.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.resizable.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.selectable.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.slider.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.sortable.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.spinner.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.tabs.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.tooltip.min.js',
	'gc-includes/vendors/jquery/ui/jquery.ui.widget.min.js',
	'gc-includes/vendors/tinymce/skins/gechiui/images/dashicon-no-alt.png',
	// 4.3
	'gc-admin/js/gc-fullscreen.js',
	'gc-admin/js/gc-fullscreen.min.js',
	'gc-includes/vendors/tinymce/gc-mce-help.php',
	'gc-includes/vendors/tinymce/plugins/gcfullscreen',
	// 4.5
	'gc-includes/theme-compat/comments-popup.php',
	// 4.6
	'gc-admin/includes/class-gc-automatic-upgrader.php', // Wrong file name, see #37628.
	// 4.8
	'gc-includes/vendors/tinymce/plugins/gcembed',
	'gc-includes/vendors/tinymce/plugins/media/moxieplayer.swf',
	'gc-includes/vendors/tinymce/skins/lightgray/fonts/readme.md',
	'gc-includes/vendors/tinymce/skins/lightgray/fonts/tinymce-small.json',
	'gc-includes/vendors/tinymce/skins/lightgray/fonts/tinymce.json',
	'gc-includes/vendors/tinymce/skins/lightgray/skin.ie7.min.css',
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
	'gc-includes/vendors/mediaelement/background.png',
	'gc-includes/vendors/mediaelement/bigplay.png',
	'gc-includes/vendors/mediaelement/bigplay.svg',
	'gc-includes/vendors/mediaelement/controls.png',
	'gc-includes/vendors/mediaelement/controls.svg',
	'gc-includes/vendors/mediaelement/flashmediaelement.swf',
	'gc-includes/vendors/mediaelement/froogaloop.min.js',
	'gc-includes/vendors/mediaelement/jumpforward.png',
	'gc-includes/vendors/mediaelement/loading.gif',
	'gc-includes/vendors/mediaelement/silverlightmediaelement.xap',
	'gc-includes/vendors/mediaelement/skipback.png',
	'gc-includes/vendors/plupload/plupload.flash.swf',
	'gc-includes/vendors/plupload/plupload.full.min.js',
	'gc-includes/vendors/plupload/plupload.silverlight.xap',
	'gc-includes/vendors/swfupload/plugins',
	'gc-includes/vendors/swfupload/swfupload.swf',
	// 4.9.2
	'gc-includes/vendors/mediaelement/lang',
	'gc-includes/vendors/mediaelement/lang/ca.js',
	'gc-includes/vendors/mediaelement/lang/cs.js',
	'gc-includes/vendors/mediaelement/lang/de.js',
	'gc-includes/vendors/mediaelement/lang/es.js',
	'gc-includes/vendors/mediaelement/lang/fa.js',
	'gc-includes/vendors/mediaelement/lang/fr.js',
	'gc-includes/vendors/mediaelement/lang/hr.js',
	'gc-includes/vendors/mediaelement/lang/hu.js',
	'gc-includes/vendors/mediaelement/lang/it.js',
	'gc-includes/vendors/mediaelement/lang/ja.js',
	'gc-includes/vendors/mediaelement/lang/ko.js',
	'gc-includes/vendors/mediaelement/lang/nl.js',
	'gc-includes/vendors/mediaelement/lang/pl.js',
	'gc-includes/vendors/mediaelement/lang/pt.js',
	'gc-includes/vendors/mediaelement/lang/ro.js',
	'gc-includes/vendors/mediaelement/lang/ru.js',
	'gc-includes/vendors/mediaelement/lang/sk.js',
	'gc-includes/vendors/mediaelement/lang/sv.js',
	'gc-includes/vendors/mediaelement/lang/uk.js',
	'gc-includes/vendors/mediaelement/lang/zh-cn.js',
	'gc-includes/vendors/mediaelement/lang/zh.js',
	'gc-includes/vendors/mediaelement/mediaelement-flash-audio-ogg.swf',
	'gc-includes/vendors/mediaelement/mediaelement-flash-audio.swf',
	'gc-includes/vendors/mediaelement/mediaelement-flash-video-hls.swf',
	'gc-includes/vendors/mediaelement/mediaelement-flash-video-mdash.swf',
	'gc-includes/vendors/mediaelement/mediaelement-flash-video.swf',
	'gc-includes/vendors/mediaelement/renderers/dailymotion.js',
	'gc-includes/vendors/mediaelement/renderers/dailymotion.min.js',
	'gc-includes/vendors/mediaelement/renderers/facebook.js',
	'gc-includes/vendors/mediaelement/renderers/facebook.min.js',
	'gc-includes/vendors/mediaelement/renderers/soundcloud.js',
	'gc-includes/vendors/mediaelement/renderers/soundcloud.min.js',
	'gc-includes/vendors/mediaelement/renderers/twitch.js',
	'gc-includes/vendors/mediaelement/renderers/twitch.min.js',
	// 5.0
	'gc-includes/vendors/codemirror/jshint.js',
	// 5.1
	'gc-includes/random_compat/random_bytes_openssl.php',
	'gc-includes/vendors/tinymce/gc-tinymce.js.gz',
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
	// 5.6
	'gc-includes/vendors/jquery/ui/position.min.js',
	'gc-includes/vendors/jquery/ui/widget.min.js',
	// 5.7
	'gc-includes/blocks/classic/block.json',
	// 5.8
	'assets/images/freedoms.png',
	'assets/images/privacy.png',
	'assets/images/about-badge.svg',
	'assets/images/about-color-palette.svg',
	'assets/images/about-color-palette-vert.svg',
	'assets/images/about-header-brushes.svg',
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
	// 6.1
	'gc-includes/blocks/post-comments.php',
	'gc-includes/blocks/post-comments/block.json',
	'gc-includes/blocks/post-comments/editor.css',
	'gc-includes/blocks/post-comments/editor.min.css',
	'gc-includes/blocks/post-comments/editor-rtl.css',
	'gc-includes/blocks/post-comments/editor-rtl.min.css',
	'gc-includes/blocks/post-comments/style.css',
	'gc-includes/blocks/post-comments/style.min.css',
	'gc-includes/blocks/post-comments/style-rtl.css',
	'gc-includes/blocks/post-comments/style-rtl.min.css',
	'gc-includes/blocks/post-comments',
	'gc-includes/blocks/comments-query-loop/block.json',
	'gc-includes/blocks/comments-query-loop/editor.css',
	'gc-includes/blocks/comments-query-loop/editor.min.css',
	'gc-includes/blocks/comments-query-loop/editor-rtl.css',
	'gc-includes/blocks/comments-query-loop/editor-rtl.min.css',
	'gc-includes/blocks/comments-query-loop',
	// 6.3
	'assets/images/wlw',
	'gc-includes/wlwmanifest.xml',
	'gc-includes/random_compat',
);

/**
 * Stores Requests files to be preloaded and deleted.
 *
 * For classes/interfaces, use the class/interface name
 * as the array key.
 *
 * All other files/directories should not have a key.
 *
 * @since 6.2.0
 *
 * @global array $_old_requests_files
 * @var array
 * @name $_old_requests_files
 */
global $_old_requests_files;

$_old_requests_files = array(
	// Interfaces.
	'Requests_Auth'                              => 'gc-includes/Requests/Auth.php',
	'Requests_Hooker'                            => 'gc-includes/Requests/Hooker.php',
	'Requests_Proxy'                             => 'gc-includes/Requests/Proxy.php',
	'Requests_Transport'                         => 'gc-includes/Requests/Transport.php',

	// Classes.
	'Requests_Auth_Basic'                        => 'gc-includes/Requests/Auth/Basic.php',
	'Requests_Cookie_Jar'                        => 'gc-includes/Requests/Cookie/Jar.php',
	'Requests_Exception_HTTP'                    => 'gc-includes/Requests/Exception/HTTP.php',
	'Requests_Exception_Transport'               => 'gc-includes/Requests/Exception/Transport.php',
	'Requests_Exception_HTTP_304'                => 'gc-includes/Requests/Exception/HTTP/304.php',
	'Requests_Exception_HTTP_305'                => 'gc-includes/Requests/Exception/HTTP/305.php',
	'Requests_Exception_HTTP_306'                => 'gc-includes/Requests/Exception/HTTP/306.php',
	'Requests_Exception_HTTP_400'                => 'gc-includes/Requests/Exception/HTTP/400.php',
	'Requests_Exception_HTTP_401'                => 'gc-includes/Requests/Exception/HTTP/401.php',
	'Requests_Exception_HTTP_402'                => 'gc-includes/Requests/Exception/HTTP/402.php',
	'Requests_Exception_HTTP_403'                => 'gc-includes/Requests/Exception/HTTP/403.php',
	'Requests_Exception_HTTP_404'                => 'gc-includes/Requests/Exception/HTTP/404.php',
	'Requests_Exception_HTTP_405'                => 'gc-includes/Requests/Exception/HTTP/405.php',
	'Requests_Exception_HTTP_406'                => 'gc-includes/Requests/Exception/HTTP/406.php',
	'Requests_Exception_HTTP_407'                => 'gc-includes/Requests/Exception/HTTP/407.php',
	'Requests_Exception_HTTP_408'                => 'gc-includes/Requests/Exception/HTTP/408.php',
	'Requests_Exception_HTTP_409'                => 'gc-includes/Requests/Exception/HTTP/409.php',
	'Requests_Exception_HTTP_410'                => 'gc-includes/Requests/Exception/HTTP/410.php',
	'Requests_Exception_HTTP_411'                => 'gc-includes/Requests/Exception/HTTP/411.php',
	'Requests_Exception_HTTP_412'                => 'gc-includes/Requests/Exception/HTTP/412.php',
	'Requests_Exception_HTTP_413'                => 'gc-includes/Requests/Exception/HTTP/413.php',
	'Requests_Exception_HTTP_414'                => 'gc-includes/Requests/Exception/HTTP/414.php',
	'Requests_Exception_HTTP_415'                => 'gc-includes/Requests/Exception/HTTP/415.php',
	'Requests_Exception_HTTP_416'                => 'gc-includes/Requests/Exception/HTTP/416.php',
	'Requests_Exception_HTTP_417'                => 'gc-includes/Requests/Exception/HTTP/417.php',
	'Requests_Exception_HTTP_418'                => 'gc-includes/Requests/Exception/HTTP/418.php',
	'Requests_Exception_HTTP_428'                => 'gc-includes/Requests/Exception/HTTP/428.php',
	'Requests_Exception_HTTP_429'                => 'gc-includes/Requests/Exception/HTTP/429.php',
	'Requests_Exception_HTTP_431'                => 'gc-includes/Requests/Exception/HTTP/431.php',
	'Requests_Exception_HTTP_500'                => 'gc-includes/Requests/Exception/HTTP/500.php',
	'Requests_Exception_HTTP_501'                => 'gc-includes/Requests/Exception/HTTP/501.php',
	'Requests_Exception_HTTP_502'                => 'gc-includes/Requests/Exception/HTTP/502.php',
	'Requests_Exception_HTTP_503'                => 'gc-includes/Requests/Exception/HTTP/503.php',
	'Requests_Exception_HTTP_504'                => 'gc-includes/Requests/Exception/HTTP/504.php',
	'Requests_Exception_HTTP_505'                => 'gc-includes/Requests/Exception/HTTP/505.php',
	'Requests_Exception_HTTP_511'                => 'gc-includes/Requests/Exception/HTTP/511.php',
	'Requests_Exception_HTTP_Unknown'            => 'gc-includes/Requests/Exception/HTTP/Unknown.php',
	'Requests_Exception_Transport_cURL'          => 'gc-includes/Requests/Exception/Transport/cURL.php',
	'Requests_Proxy_HTTP'                        => 'gc-includes/Requests/Proxy/HTTP.php',
	'Requests_Response_Headers'                  => 'gc-includes/Requests/Response/Headers.php',
	'Requests_Transport_cURL'                    => 'gc-includes/Requests/Transport/cURL.php',
	'Requests_Transport_fsockopen'               => 'gc-includes/Requests/Transport/fsockopen.php',
	'Requests_Utility_CaseInsensitiveDictionary' => 'gc-includes/Requests/Utility/CaseInsensitiveDictionary.php',
	'Requests_Utility_FilteredIterator'          => 'gc-includes/Requests/Utility/FilteredIterator.php',
	'Requests_Cookie'                            => 'gc-includes/Requests/Cookie.php',
	'Requests_Exception'                         => 'gc-includes/Requests/Exception.php',
	'Requests_Hooks'                             => 'gc-includes/Requests/Hooks.php',
	'Requests_IDNAEncoder'                       => 'gc-includes/Requests/IDNAEncoder.php',
	'Requests_IPv6'                              => 'gc-includes/Requests/IPv6.php',
	'Requests_IRI'                               => 'gc-includes/Requests/IRI.php',
	'Requests_Response'                          => 'gc-includes/Requests/Response.php',
	'Requests_SSL'                               => 'gc-includes/Requests/SSL.php',
	'Requests_Session'                           => 'gc-includes/Requests/Session.php',

	// Directories.
	'gc-includes/Requests/Auth/',
	'gc-includes/Requests/Cookie/',
	'gc-includes/Requests/Exception/HTTP/',
	'gc-includes/Requests/Exception/Transport/',
	'gc-includes/Requests/Exception/',
	'gc-includes/Requests/Proxy/',
	'gc-includes/Requests/Response/',
	'gc-includes/Requests/Transport/',
	'gc-includes/Requests/Utility/',
);

/**
 * Stores new files in gc-content to copy
 *
 * The contents of this array indicate any new bundled plugins/themes which
 * should be installed with the GeChiUI Upgrade. These items will not be
 * re-installed in future upgrades, this behavior is controlled by the
 * introduced version present here being older than the current installed version.
 *
 * The content of this array should follow the following format:
 * Filename (relative to gc-content) => Introduced version
 * Directories should be noted by suffixing it with a trailing slash (/)
 * New themes were not automatically installed for 4.4-4.6 on
 *              upgrade. New themes are now installed again. To disable new
 *              themes from being installed on upgrade, explicitly define
 *              CORE_UPGRADE_SKIP_NEW_BUNDLED as true.
 * @global array $_new_bundled_files
 * @var array
 * @name $_new_bundled_files
 */
global $_new_bundled_files;

$_new_bundled_files = array(
	'plugins/akismet/'          => '2.0',
	'themes/gcoa/' => '1.1',
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
 * @since 2.7.0
 *
 * @global GC_Filesystem_Base $gc_filesystem          GeChiUI filesystem subclass.
 * @global array              $_old_files
 * @global array              $_old_requests_files
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
	global $gc_filesystem, $_old_files, $_old_requests_files, $_new_bundled_files, $gcdb;

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}

	/*
	 * Merge the old Requests files and directories into the `$_old_files`.
	 * Then preload these Requests files first, before the files are deleted
	 * and replaced to ensure the code is in memory if needed.
	 */
	$_old_files = array_merge( $_old_files, array_values( $_old_requests_files ) );
	_preload_old_requests_classes_and_interfaces( $to );

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
			__( '由于某些文件无法被复制，更新无法进行。此问题通常是由于文件权限不一致造成的。' ),
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

	$php_version    = PHP_VERSION;
	$mysql_version  = $gcdb->db_version();
	$old_gc_version = $GLOBALS['gc_version']; // The version of GeChiUI we're updating from.
	/*
	 * Note: str_contains() is not used here, as this file is included
	 * when updating from older GeChiUI versions, in which case
	 * the polyfills from gc-includes/compat.php may not be available.
	 */
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

	/*
	 * Don't copy gc-content, we'll deal with that below.
	 * We also copy version.php last so failed updates report their old version.
	 */
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
				/*
				 * Note: str_starts_with() is not used here, as this file is included
				 * when updating from older GeChiUI versions, in which case
				 * the polyfills from gc-includes/compat.php may not be available.
				 */
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
					__( '因为您的系统不能复制一些文件，更新不能被安装。这通常是因为存在不一致的文件权限。' ),
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
	$result = copy_dir( $from . $distro, $to, $skip );

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
				__( '因为您的系统不能复制一些文件，更新不能被安装。这通常是因为存在不一致的文件权限。' ),
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
			/*
			 * Note: str_starts_with() is not used here, as this file is included
			 * when updating from older GeChiUI versions, in which case
			 * the polyfills from gc-includes/compat.php may not be available.
			 */
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

		/*
		 * If we don't have enough free space, it isn't worth trying again.
		 * Unlikely to be hit due to the check in unzip_file().
		 */
		$available_space = function_exists( 'disk_free_space' ) ? @disk_free_space( ABSPATH ) : false;

		if ( $available_space && $total_size >= $available_space ) {
			$result = new GC_Error( 'disk_full', __( '磁盘空间不足，无法执行更新。' ) );
		} else {
			$result = copy_dir( $from . $distro, $to, $skip );

			if ( is_gc_error( $result ) ) {
				$result = new GC_Error(
					$result->get_error_code() . '_retry',
					$result->get_error_message(),
					substr( $result->get_error_data(), strlen( $to ) )
				);
			}
		}
	}

	/*
	 * Custom content directory needs updating now.
	 * Copy languages.
	 */
	if ( ! is_gc_error( $result ) && $gc_filesystem->is_dir( $from . $distro . 'gc-content/languages' ) ) {
		if ( GC_LANG_DIR !== ABSPATH . GCINC . '/languages' || @is_dir( GC_LANG_DIR ) ) {
			$lang_dir = GC_LANG_DIR;
		} else {
			$lang_dir = GC_CONTENT_DIR . '/languages';
		}
		/*
		 * Note: str_starts_with() is not used here, as this file is included
		 * when updating from older GeChiUI versions, in which case
		 * the polyfills from gc-includes/compat.php may not be available.
		 */
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
	 * 3.5 -> 3.5+ - an empty twentytwelve directory was created upon upgrade to 3.5 for some users,
	 * preventing installation of Twenty Twelve.
	 */
	if ( '3.5' === $old_gc_version ) {
		if ( is_dir( GC_CONTENT_DIR . '/themes/twentytwelve' )
			&& ! file_exists( GC_CONTENT_DIR . '/themes/twentytwelve/style.css' )
		) {
			$gc_filesystem->delete( $gc_filesystem->gc_themes_dir() . 'twentytwelve/' );
		}
	}

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

					/*
					 * If an error occurs partway through this final step,
					 * keep the error flowing through, but keep the process going.
					 */
					if ( is_gc_error( $_result ) ) {
						if ( ! is_gc_error( $result ) ) {
							$result = new GC_Error();
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

	// Deactivate incompatible plugins.
	_upgrade_core_deactivate_incompatible_plugins();

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
 * Preloads old Requests classes and interfaces.
 *
 * This function preloads the old Requests code into memory before the
 * upgrade process deletes the files. Why? Requests code is loaded into
 * memory via an autoloader, meaning when a class or interface is needed
 * If a request is in process, Requests could attempt to access code. If
 * the file is not there, a fatal error could occur. If the file was
 * replaced, the new code is not compatible with the old, resulting in
 * a fatal error. Preloading ensures the code is in memory before the
 * code is updated.
 *
 * @since 6.2.0
 *
 * @global array              $_old_requests_files Requests files to be preloaded.
 * @global GC_Filesystem_Base $gc_filesystem       GeChiUI filesystem subclass.
 * @global string             $gc_version          The GeChiUI version string.
 *
 * @param string $to Path to old GeChiUI installation.
 */
function _preload_old_requests_classes_and_interfaces( $to ) {
	global $_old_requests_files, $gc_filesystem, $gc_version;

	/*
	 * Requests was introduced in GeChiUI 4.6.
	 *
	 * Skip preloading if the website was previously using
	 * an earlier version of GeChiUI.
	 */
	if ( version_compare( $gc_version, '4.6', '<' ) ) {
		return;
	}

	if ( ! defined( 'REQUESTS_SILENCE_PSR0_DEPRECATIONS' ) ) {
		define( 'REQUESTS_SILENCE_PSR0_DEPRECATIONS', true );
	}

	foreach ( $_old_requests_files as $name => $file ) {
		// Skip files that aren't interfaces or classes.
		if ( is_int( $name ) ) {
			continue;
		}

		// Skip if it's already loaded.
		if ( class_exists( $name ) || interface_exists( $name ) ) {
			continue;
		}

		// Skip if the file is missing.
		if ( ! $gc_filesystem->is_file( $to . $file ) ) {
			continue;
		}

		require_once $to . $file;
	}
}

/**
 * Redirect to the About GeChiUI page after a successful upgrade.
 *
 * This function is only needed when the existing installation is older than 3.4.0.
 *
 * @global string $gc_version The GeChiUI version string.
 * @global string $pagenow    The filename of the current screen.
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
 * @since 4.2.2
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
 * @since 4.2.2
 *
 * @param string $directory Directory path. Expects trailingslashed.
 * @return array
 */
function _upgrade_422_find_genericons_files_in_folder( $directory ) {
	$directory = trailingslashit( $directory );
	$files     = array();

	if ( file_exists( "{$directory}example.html" )
		/*
		 * Note: str_contains() is not used here, as this file is included
		 * when updating from older GeChiUI versions, in which case
		 * the polyfills from gc-includes/compat.php may not be available.
		 */
		&& false !== strpos( file_get_contents( "{$directory}example.html" ), '<title>Genericons</title>' )
	) {
		$files[] = "{$directory}example.html";
	}

	$dirs = glob( $directory . '*', GLOB_ONLYDIR );
	$dirs = array_filter(
		$dirs,
		static function( $dir ) {
			/*
			 * Skip any node_modules directories.
			 *
			 * Note: str_contains() is not used here, as this file is included
			 * when updating from older GeChiUI versions, in which case
			 * the polyfills from gc-includes/compat.php may not be available.
			 */
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
 */
function _upgrade_440_force_deactivate_incompatible_plugins() {
	if ( defined( 'REST_API_VERSION' ) && version_compare( REST_API_VERSION, '2.0-beta4', '<=' ) ) {
		deactivate_plugins( array( 'rest-api/plugin.php' ), true );
	}
}

/**
 * @access private
 * @ignore
 * @since 5.8.0
 * @since 5.9.0 The minimum compatible version of Gutenberg is 11.9.
 * @since 6.1.1 The minimum compatible version of Gutenberg is 14.1.
 */
function _upgrade_core_deactivate_incompatible_plugins() {
	if ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '14.1', '<' ) ) {
		$deactivated_gutenberg['gutenberg'] = array(
			'plugin_name'         => 'Gutenberg',
			'version_deactivated' => GUTENBERG_VERSION,
			'version_compatible'  => '14.1',
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
