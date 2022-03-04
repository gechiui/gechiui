<?php
/**
 * GeChiUI Administration Template Header
 *
 * @package GeChiUI
 * @subpackage Administration
 */

header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
if ( ! defined( 'GC_ADMIN' ) ) {
	require_once __DIR__ . '/admin.php';
}

/**
 * In case admin-header.php is included in a function.
 *
 * @global string    $title
 * @global string    $hook_suffix
 * @global GC_Screen $current_screen     GeChiUI current screen object.
 * @global GC_Locale $gc_locale          GeChiUI date and time locale object.
 * @global string    $pagenow
 * @global string    $update_title
 * @global int       $total_update_count
 * @global string    $parent_file
 * @global string    $typenow
 */
global $title, $hook_suffix, $current_screen, $gc_locale, $pagenow,
	$update_title, $total_update_count, $parent_file, $typenow;

// Catch plugins that include admin-header.php before admin.php completes.
if ( empty( $current_screen ) ) {
	set_current_screen();
}

get_admin_page_title();
$title = strip_tags( $title );

if ( is_network_admin() ) {
	/* translators: Network admin screen title. %s: Network title. */
	$admin_title = sprintf( __( '管理网络：%s' ), get_network()->site_name );
} elseif ( is_user_admin() ) {
	/* translators: User dashboard screen title. %s: Network title. */
	$admin_title = sprintf( __( '用户仪表盘：%s' ), get_network()->site_name );
} else {
	$admin_title = get_bloginfo( 'name' );
}

if ( $admin_title === $title ) {
	/* translators: Admin screen title. %s: Admin screen name. */
	$admin_title = sprintf( __( '%s &#8212; GeChiUI' ), $title );
} else {
	$screen_title = $title;

	if ( 'post' === $current_screen->base && 'add' !== $current_screen->action ) {
		$post_title = get_the_title();
		if ( ! empty( $post_title ) ) {
			$post_type_obj = get_post_type_object( $typenow );
			$screen_title  = sprintf(
				/* translators: Editor admin screen title. 1: "Edit item" text for the post type, 2: Post title. */
				__( '%1$s “%2$s”' ),
				$post_type_obj->labels->edit_item,
				$post_title
			);
		}
	}

	/* translators: Admin screen title. 1: Admin screen name, 2: Network or site name. */
	$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; GeChiUI' ), $screen_title, $admin_title );
}

if ( gc_is_recovery_mode() ) {
	/* translators: %s: Admin screen title. */
	$admin_title = sprintf( __( '恢复模式 &#8212; %s' ), $admin_title );
}

/**
 * Filters the title tag content for an admin page.
 *
 *
 *
 * @param string $admin_title The page title, with extra context added.
 * @param string $title       The original page title.
 */
$admin_title = apply_filters( 'admin_title', $admin_title, $title );

gc_user_settings();

_gc_admin_html_begin();
?>
<title><?php echo esc_html( $admin_title ); ?></title>
<?php

gc_enqueue_style( 'colors' );
gc_enqueue_script( 'utils' );
gc_enqueue_script( 'svg-painter' );

$admin_body_class = preg_replace( '/[^a-z0-9_-]+/i', '-', $hook_suffix );
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!=='undefined')jQuery(function(){func();});else if(typeof gcOnload!=='function'){gcOnload=func;}else{var oldonload=gcOnload;gcOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
	pagenow = '<?php echo esc_js( $current_screen->id ); ?>',
	typenow = '<?php echo esc_js( $current_screen->post_type ); ?>',
	adminpage = '<?php echo esc_js( $admin_body_class ); ?>',
	thousandsSeparator = '<?php echo esc_js( $gc_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo esc_js( $gc_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>
<?php

/**
 * Enqueue scripts for all admin pages.
 *
 *
 *
 * @param string $hook_suffix The current admin page.
 */
do_action( 'admin_enqueue_scripts', $hook_suffix );

/**
 * Fires when styles are printed for a specific admin page based on $hook_suffix.
 *
 *
 */
do_action( "admin_print_styles-{$hook_suffix}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

/**
 * Fires when styles are printed for all admin pages.
 *
 *
 */
do_action( 'admin_print_styles' );

/**
 * Fires when scripts are printed for a specific admin page based on $hook_suffix.
 *
 *
 */
do_action( "admin_print_scripts-{$hook_suffix}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

/**
 * Fires when scripts are printed for all admin pages.
 *
 *
 */
do_action( 'admin_print_scripts' );

/**
 * Fires in head section for a specific admin page.
 *
 * The dynamic portion of the hook name, `$hook_suffix`, refers to the hook suffix
 * for the admin page.
 *
 *
 */
do_action( "admin_head-{$hook_suffix}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

/**
 * Fires in head section for all admin pages.
 *
 *
 */
do_action( 'admin_head' );

if ( 'f' === get_user_setting( 'mfold' ) ) {
	$admin_body_class .= ' folded';
}

if ( ! get_user_setting( 'unfold' ) ) {
	$admin_body_class .= ' auto-fold';
}

if ( is_admin_bar_showing() ) {
	$admin_body_class .= ' admin-bar';
}

if ( is_rtl() ) {
	$admin_body_class .= ' rtl';
}

if ( $current_screen->post_type ) {
	$admin_body_class .= ' post-type-' . $current_screen->post_type;
}

if ( $current_screen->taxonomy ) {
	$admin_body_class .= ' taxonomy-' . $current_screen->taxonomy;
}

$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', (float) get_bloginfo( 'version' ) );
$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', get_bloginfo( 'version' ) ) );
$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

if ( gc_is_mobile() ) {
	$admin_body_class .= ' mobile';
}

if ( is_multisite() ) {
	$admin_body_class .= ' multisite';
}

if ( is_network_admin() ) {
	$admin_body_class .= ' network-admin';
}

$admin_body_class .= ' no-customize-support no-svg';

if ( $current_screen->is_block_editor() ) {
	$admin_body_class .= ' block-editor-page gc-embed-responsive';
}

$error_get_last = error_get_last();

// Print a CSS class to make PHP errors visible.
if ( $error_get_last && GC_DEBUG && GC_DEBUG_DISPLAY && ini_get( 'display_errors' )
	// Don't print the class for PHP notices in gc-config.php, as they happen before GC_DEBUG takes effect,
	// and should not be displayed with the `error_reporting` level previously set in gc-load.php.
	&& ( E_NOTICE !== $error_get_last['type'] || 'gc-config.php' !== gc_basename( $error_get_last['file'] ) )
) {
	$admin_body_class .= ' php-error';
}

unset( $error_get_last );

?>
</head>
<?php
/**
 * Filters the CSS classes for the body tag in the admin.
 *
 * This filter differs from the {@see 'post_class'} and {@see 'body_class'} filters
 * in two important ways:
 *
 * 1. `$classes` is a space-separated string of class names instead of an array.
 * 2. Not all core admin classes are filterable, notably: gc-admin, gc-core-ui,
 *    and no-js cannot be removed.
 *
 *
 *
 * @param string $classes Space-separated list of CSS classes.
 */
$admin_body_classes = apply_filters( 'admin_body_class', '' );
$admin_body_classes = ltrim( $admin_body_classes . ' ' . $admin_body_class );
?>
<body class="gc-admin gc-core-ui no-js <?php echo $admin_body_classes; ?>">
<script type="text/javascript">
	document.body.className = document.body.className.replace('no-js','js');
</script>

<?php
// Make sure the customize body classes are correct as early as possible.
if ( current_user_can( 'customize' ) ) {
	gc_customize_support_script();
}
?>

<div id="gcwrap" class="app">
<?php require ABSPATH . 'gc-admin/menu-header.php'; ?>
<div id="gccontent" class="page-container">

<?php
/**
 * Fires at the beginning of the content section in an admin page.
 *
 *
 */
do_action( 'in_admin_header' );
?>

<div id="gcbody" role="main">
<?php
unset( $blog_name, $total_update_count, $update_title );

$current_screen->set_parentage( $parent_file );

?>

<div id="gcbody-content">
<?php

$current_screen->render_screen_meta();

if ( is_network_admin() ) {
	/**
	 * Prints network admin screen notices.
	 *
	 */
	do_action( 'network_admin_notices' );
} elseif ( is_user_admin() ) {
	/**
	 * Prints user admin screen notices.
	 *
	 */
	do_action( 'user_admin_notices' );
} else {
	/**
	 * Prints admin screen notices.
	 *
	 */
	do_action( 'admin_notices' );
}

/**
 * Prints generic admin screen notices.
 *
 *
 */
do_action( 'all_admin_notices' );

if ( 'options-general.php' === $parent_file ) {
	require ABSPATH . 'gc-admin/options-head.php';
}
