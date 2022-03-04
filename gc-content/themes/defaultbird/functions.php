<?php
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.gechiui.com/themes/basics/theme-functions/
 *
 * @package GeChiUI
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */


if ( ! function_exists( 'defaultbird_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various GeChiUI features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function defaultbird_support() {

		// Add support for block styles.
		add_theme_support( 'gc-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'defaultbird_support' );

if ( ! function_exists( 'defaultbird_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function defaultbird_styles() {
		// Register theme stylesheet.
		$theme_version = gc_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		gc_register_style(
			'defaultbird-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Add styles inline.
		gc_add_inline_style( 'defaultbird-style', defaultbird_get_font_face_styles() );

		// Enqueue theme stylesheet.
		gc_enqueue_style( 'defaultbird-style' );

	}

endif;

add_action( 'gc_enqueue_scripts', 'defaultbird_styles' );

if ( ! function_exists( 'defaultbird_editor_styles' ) ) :

	/**
	 * Enqueue editor styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function defaultbird_editor_styles() {

		// Add styles inline.
		gc_add_inline_style( 'gc-block-library', defaultbird_get_font_face_styles() );

	}

endif;

add_action( 'admin_init', 'defaultbird_editor_styles' );


if ( ! function_exists( 'defaultbird_get_font_face_styles' ) ) :

	/**
	 * Get font face styles.
	 * Called by functions defaultbird_styles() and defaultbird_editor_styles() above.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return string
	 */
	function defaultbird_get_font_face_styles() {

		return "
		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) . "') format('woff2');
		}

		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: italic;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Italic.ttf.woff2' ) . "') format('woff2');
		}
		";

	}

endif;

if ( ! function_exists( 'defaultbird_preload_webfonts' ) ) :

	/**
	 * Preloads the main web font to improve performance.
	 *
	 * Only the main web font (font-style: normal) is preloaded here since that font is always relevant (it is used
	 * on every heading, for example). The other font is only needed if there is any applicable content in italic style,
	 * and therefore preloading it would in most cases regress performance when that font would otherwise not be loaded
	 * at all.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function defaultbird_preload_webfonts() {
		?>
		<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
		<?php
	}

endif;

add_action( 'gc_head', 'defaultbird_preload_webfonts' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';
