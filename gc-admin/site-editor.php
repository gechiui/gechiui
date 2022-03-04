<?php
/**
 * Site Editor administration screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

global $post, $editor_styles;

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此站点上编辑主题选项。' ) . '</p>',
		403
	);
}

if ( ! gc_is_block_theme() ) {
	gc_die( __( '您目前使用的主题不兼容全站编辑。' ) );
}

// Used in the HTML title tag.
$title       = __( '编辑器（ (beta)）' );
$parent_file = 'themes.php';

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

// Load block patterns from w.org.
_load_remote_block_patterns();
_load_remote_featured_patterns();

// Default to is-fullscreen-mode to avoid jumps in the UI.
add_filter(
	'admin_body_class',
	static function( $classes ) {
		return "$classes is-fullscreen-mode";
	}
);

$indexed_template_types = array();
foreach ( get_default_block_template_types() as $slug => $template_type ) {
	$template_type['slug']    = (string) $slug;
	$indexed_template_types[] = $template_type;
}

$block_editor_context = new GC_Block_Editor_Context();
$custom_settings      = array(
	'siteUrl'                              => site_url(),
	'postsPerPage'                         => get_option( 'posts_per_page' ),
	'styles'                               => get_block_editor_theme_styles(),
	'defaultTemplateTypes'                 => $indexed_template_types,
	'defaultTemplatePartAreas'             => get_allowed_block_template_part_areas(),
	'__experimentalBlockPatterns'          => GC_Block_Patterns_Registry::get_instance()->get_all_registered(),
	'__experimentalBlockPatternCategories' => GC_Block_Pattern_Categories_Registry::get_instance()->get_all_registered(),
);
$editor_settings      = get_block_editor_settings( $custom_settings, $block_editor_context );

if ( isset( $_GET['postType'] ) && ! isset( $_GET['postId'] ) ) {
	$post_type = get_post_type_object( $_GET['postType'] );
	if ( ! $post_type ) {
		gc_die( __( '无效的文章类型。' ) );
	}
}

$active_global_styles_id = GC_Theme_JSON_Resolver::get_user_global_styles_post_id();
$active_theme            = gc_get_theme()->get_stylesheet();
$preload_paths           = array(
	array( '/gc/v2/media', 'OPTIONS' ),
	'/',
	'/gc/v2/types?context=edit',
	'/gc/v2/types/gc_template?context=edit',
	'/gc/v2/types/gc_template-part?context=edit',
	'/gc/v2/taxonomies?context=edit',
	'/gc/v2/pages?context=edit',
	'/gc/v2/categories?context=edit',
	'/gc/v2/posts?context=edit',
	'/gc/v2/tags?context=edit',
	'/gc/v2/templates?context=edit&per_page=-1',
	'/gc/v2/template-parts?context=edit&per_page=-1',
	'/gc/v2/settings',
	'/gc/v2/themes?context=edit&status=active',
	'/gc/v2/global-styles/' . $active_global_styles_id . '?context=edit',
	'/gc/v2/global-styles/' . $active_global_styles_id,
	'/gc/v2/global-styles/themes/' . $active_theme,
);

block_editor_rest_api_preload( $preload_paths, $block_editor_context );

gc_add_inline_script(
	'gc-edit-site',
	sprintf(
		'gc.domReady( function() {
			gc.editSite.initializeEditor( "site-editor", %s );
		} );',
		gc_json_encode( $editor_settings )
	)
);

// Preload server-registered block schemas.
gc_add_inline_script(
	'gc-blocks',
	'gc.blocks.unstable__bootstrapServerSideBlockDefinitions(' . gc_json_encode( get_block_editor_server_block_settings() ) . ');'
);

gc_add_inline_script(
	'gc-blocks',
	sprintf( 'gc.blocks.setCategories( %s );', gc_json_encode( get_block_categories( $post ) ) ),
	'after'
);

gc_enqueue_script( 'gc-edit-site' );
gc_enqueue_script( 'gc-format-library' );
gc_enqueue_style( 'gc-edit-site' );
gc_enqueue_style( 'gc-format-library' );
gc_enqueue_media();

if (
	current_theme_supports( 'gc-block-styles' ) ||
	( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 )
) {
	gc_enqueue_style( 'gc-block-library-theme' );
}

/** This action is documented in gc-admin/edit-form-blocks.php */
do_action( 'enqueue_block_editor_assets' );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div id="site-editor" class="edit-site"></div>

<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
