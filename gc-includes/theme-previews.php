<?php
/**
 * Theme previews using the Site Editor for block themes.
 *
 * @package GeChiUI
 */

/**
 * Filters the blog option to return the path for the previewed theme.
 *
 * @since 6.3.0
 *
 * @param string $current_stylesheet The current theme's stylesheet or template path.
 * @return string The previewed theme's stylesheet or template path.
 */
function gc_get_theme_preview_path( $current_stylesheet = null ) {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return $current_stylesheet;
	}

	$preview_stylesheet = ! empty( $_GET['gc_theme_preview'] ) ? sanitize_text_field( gc_unslash( $_GET['gc_theme_preview'] ) ) : null;
	$gc_theme           = gc_get_theme( $preview_stylesheet );
	if ( ! is_gc_error( $gc_theme->errors() ) ) {
		if ( current_filter() === 'template' ) {
			$theme_path = $gc_theme->get_template();
		} else {
			$theme_path = $gc_theme->get_stylesheet();
		}

		return sanitize_text_field( $theme_path );
	}

	return $current_stylesheet;
}

/**
 * Adds a middleware to `apiFetch` to set the theme for the preview.
 * This adds a `gc_theme_preview` URL parameter to API requests from the Site Editor, so they also respond as if the theme is set to the value of the parameter.
 *
 * @since 6.3.0
 */
function gc_attach_theme_preview_middleware() {
	// Don't allow non-admins to preview themes.
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	gc_add_inline_script(
		'gc-api-fetch',
		sprintf(
			'gc.apiFetch.use( gc.apiFetch.createThemePreviewMiddleware( %s ) );',
			gc_json_encode( sanitize_text_field( gc_unslash( $_GET['gc_theme_preview'] ) ) )
		),
		'after'
	);
}

/**
 * Set a JavaScript constant for theme activation.
 *
 * Sets the JavaScript global GC_BLOCK_THEME_ACTIVATE_NONCE containing the nonce
 * required to activate a theme. For use within the site editor.
 *
 * @see https://github.com/GeChiUI/gutenberg/pull/41836.
 *
 * @since 6.3.0
 * @private
 */
function gc_block_theme_activate_nonce() {
	$nonce_handle = 'switch-theme_' . gc_get_theme_preview_path();
	?>
	<script type="text/javascript">
		window.GC_BLOCK_THEME_ACTIVATE_NONCE = <?php echo gc_json_encode( gc_create_nonce( $nonce_handle ) ); ?>;
	</script>
	<?php
}

// Attaches filters to enable theme previews in the Site Editor.
if ( ! empty( $_GET['gc_theme_preview'] ) ) {
	add_filter( 'stylesheet', 'gc_get_theme_preview_path' );
	add_filter( 'template', 'gc_get_theme_preview_path' );
	add_action( 'init', 'gc_attach_theme_preview_middleware' );
	add_action( 'admin_head', 'gc_block_theme_activate_nonce' );
}
