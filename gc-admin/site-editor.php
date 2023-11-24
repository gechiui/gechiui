<?php
/**
 * Site Editor administration screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

global $editor_styles;

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此站点上编辑主题选项。' ) . '</p>',
		403
	);
}

if ( ! ( current_theme_supports( 'block-template-parts' ) || gc_is_block_theme() ) ) {
	gc_die( __( '您当前使用的主题与网站编辑器不兼容。' ) );
}

$is_template_part = isset( $_GET['postType'] ) && 'gc_template_part' === sanitize_key( $_GET['postType'] );
$is_template_part_path = isset( $_GET['path'] ) && 'gc_template_partall' === sanitize_key( $_GET['path'] );
$is_template_part_editor = $is_template_part || $is_template_part_path;

if ( ! gc_is_block_theme() && ! $is_template_part_editor ) {
	gc_die( __( '您当前使用的主题与网站编辑器不兼容。' ) );
}

// Used in the HTML title tag.
$title       = _x( '编辑器', 'site editor title tag' );
$parent_file = 'themes.php';

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

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

$block_editor_context = new GC_Block_Editor_Context( array( 'name' => 'core/edit-site' ) );
$custom_settings      = array(
	'siteUrl'                   => site_url(),
	'postsPerPage'              => get_option( 'posts_per_page' ),
	'styles'                    => get_block_editor_theme_styles(),
	'defaultTemplateTypes'      => $indexed_template_types,
	'defaultTemplatePartAreas'  => get_allowed_block_template_part_areas(),
	'supportsLayout'            => gc_theme_has_theme_json(),
	'supportsTemplatePartsMode' => ! gc_is_block_theme() && current_theme_supports( 'block-template-parts' ),
);

// Add additional back-compat patterns registered by `current_screen` et al.
$custom_settings['__experimentalAdditionalBlockPatterns']          = GC_Block_Patterns_Registry::get_instance()->get_all_registered( true );
$custom_settings['__experimentalAdditionalBlockPatternCategories'] = GC_Block_Pattern_Categories_Registry::get_instance()->get_all_registered( true );

$editor_settings = get_block_editor_settings( $custom_settings, $block_editor_context );

if ( isset( $_GET['postType'] ) && ! isset( $_GET['postId'] ) ) {
	$post_type = get_post_type_object( $_GET['postType'] );
	if ( ! $post_type ) {
		gc_die( __( '无效的文章类型。' ) );
	}
}

$active_global_styles_id = GC_Theme_JSON_Resolver::get_user_global_styles_post_id();
$active_theme            = get_stylesheet();

$navigation_rest_route = rest_get_route_for_post_type_items(
	'gc_navigation'
);

$preload_paths = array(
	array( '/gc/v2/media', 'OPTIONS' ),
	'/gc/v2/types?context=view',
	'/gc/v2/types/gc_template?context=edit',
	'/gc/v2/types/gc_template-part?context=edit',
	'/gc/v2/templates?context=edit&per_page=-1',
	'/gc/v2/template-parts?context=edit&per_page=-1',
	'/gc/v2/themes?context=edit&status=active',
	'/gc/v2/global-styles/' . $active_global_styles_id . '?context=edit',
	'/gc/v2/global-styles/' . $active_global_styles_id,
	'/gc/v2/global-styles/themes/' . $active_theme,
	array( $navigation_rest_route, 'OPTIONS' ),
	array(
		add_query_arg(
			array(
				'context'   => 'edit',
				'per_page'  => 100,
				'order'     => 'desc',
				'orderby'   => 'date',
				// array indices are required to avoid query being encoded and not matching in cache.
				'status[0]' => 'publish',
				'status[1]' => 'draft',
			),
			$navigation_rest_route
		),
		'GET',
	),
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
	sprintf( 'gc.blocks.setCategories( %s );', gc_json_encode( isset( $editor_settings['blockCategories'] ) ? $editor_settings['blockCategories'] : array() ) ),
	'after'
);

gc_enqueue_script( 'gc-edit-site' );
gc_enqueue_script( 'gc-format-library' );
gc_enqueue_style( 'gc-edit-site' );
gc_enqueue_style( 'gc-format-library' );
gc_enqueue_media();

if (
	current_theme_supports( 'gc-block-styles' ) &&
	( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 )
) {
	gc_enqueue_style( 'gc-block-library-theme' );
}

/** This action is documented in gc-admin/edit-form-blocks.php */
do_action( 'enqueue_block_editor_assets' );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="edit-site" id="site-editor">
	<?php // JavaScript is disabled. ?>
	<div class="wrap hide-if-js site-editor-no-js">
		<div class="page-header"><h2 class="header-title"><?php esc_html_e( '编辑系统' ); ?></h2></div>
		<?php
			/**
			 * Filters the message displayed in the site editor interface when JavaScript is
			 * not enabled in the browser.
			 *
			 * @since 6.3.0
			 *
			 * @param string  $message The message being displayed.
			 * @param GC_Post $post    The post being edited.
			 */
			$message = apply_filters( 'site_editor_no_javascript_message', __( '网站编辑器需要JavaScript。请在浏览器设置中启用JavaScript。' ), $post );
			echo setting_error( $message, 'danger' );
		?>
	</div>
</div>

<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
