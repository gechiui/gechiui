<?php
/**
 * Upgrade GeChiUI Page.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * We are upgrading GeChiUI.
 *
 *
 * @var bool
 */
define( 'GC_INSTALLING', true );

/** Load GeChiUI Bootstrap */
require dirname( __DIR__ ) . '/gc-load.php';

nocache_headers();

require_once ABSPATH . 'gc-admin/includes/upgrade.php';

delete_site_transient( 'update_core' );

if ( isset( $_GET['step'] ) ) {
	$step = $_GET['step'];
} else {
	$step = 0;
}

// Do it. No output.
if ( 'upgrade_db' === $step ) {
	gc_upgrade();
	die( '0' );
}

/**
 * @global string $gc_version             The GeChiUI version string.
 * @global string $required_php_version   The required PHP version string.
 * @global string $required_mysql_version The required MySQL version string.
 */
global $gc_version, $required_php_version, $required_mysql_version;

$step = (int) $step;

$php_version   = phpversion();
$mysql_version = $gcdb->db_version();
$php_compat    = version_compare( $php_version, $required_php_version, '>=' );
if ( file_exists( GC_CONTENT_DIR . '/db.php' ) && empty( $gcdb->is_mysql ) ) {
	$mysql_compat = true;
} else {
	$mysql_compat = version_compare( $mysql_version, $required_mysql_version, '>=' );
}

header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<meta name="robots" content="noindex,nofollow" />
	<title><?php _e( 'GeChiUI &rsaquo; 升级' ); ?></title>
	<?php gc_admin_css( 'install', true ); ?>
</head>
<body class="gc-core-ui">
<p id="logo"><a href="<?php echo esc_url( __( 'https://www.gechiui.com/' ) ); ?>"><?php _e( 'GeChiUI' ); ?></a></p>

<?php if ( (int) get_option( 'db_version' ) === $gc_db_version || ! is_blog_installed() ) : ?>

<h1><?php _e( '无需升级' ); ?></h1>
<p><?php _e( '您的GeChiUI数据库已经是最新的了！' ); ?></p>
<p class="step"><a class="button button-large" href="<?php echo get_option( 'home' ); ?>/"><?php _e( '继续' ); ?></a></p>

	<?php
elseif ( ! $php_compat || ! $mysql_compat ) :
	$version_url = sprintf(
		/* translators: %s: GeChiUI version. */
		esc_url( __( 'https://www.gechiui.com/support/gechiui-version/version-%s/' ) ),
		sanitize_title( $gc_version )
	);

	$php_update_message = '</p><p>' . sprintf(
		/* translators: %s: URL to Update PHP page. */
		__( '<a href="%s">查阅如何更新PHP</a>。' ),
		esc_url( gc_get_update_php_url() )
	);

	$annotation = gc_get_update_php_annotation();

	if ( $annotation ) {
		$php_update_message .= '</p><p><em>' . $annotation . '</em>';
	}

	if ( ! $mysql_compat && ! $php_compat ) {
		$message = sprintf(
			/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Minimum required MySQL version number, 5: Current PHP version number, 6: Current MySQL version number. */
			__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高及MySQL版本%4$s或更高。您正在运行PHP %5$s和MySQL %6$s。' ),
			$version_url,
			$gc_version,
			$required_php_version,
			$required_mysql_version,
			$php_version,
			$mysql_version
		) . $php_update_message;
	} elseif ( ! $php_compat ) {
		$message = sprintf(
			/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Current PHP version number. */
			__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高。您正在运行版本%4$s。' ),
			$version_url,
			$gc_version,
			$required_php_version,
			$php_version
		) . $php_update_message;
	} elseif ( ! $mysql_compat ) {
		$message = sprintf(
			/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required MySQL version number, 4: Current MySQL version number. */
			__( '您不能更新至<a href="%1$s">GeChiUI %2$s</a>，因其需要MySQL版本%3$s或更高。您正在运行版本%4$s。' ),
			$version_url,
			$gc_version,
			$required_mysql_version,
			$mysql_version
		);
	}

	echo '<p>' . $message . '</p>';
	?>
	<?php
else :
	switch ( $step ) :
		case 0:
			$goback = gc_get_referer();
			if ( $goback ) {
				$goback = esc_url_raw( $goback );
				$goback = urlencode( $goback );
			}
			?>
	<h1><?php _e( '需要升级数据库' ); ?></h1>
<p><?php _e( 'GeChiUI已升级，我们需要接着升级您的数据库。' ); ?></p>
<p><?php _e( '数据库升级过程需要一点时间，请耐心等候。' ); ?></p>
<p class="step"><a class="button button-large button-primary" href="upgrade.php?step=1&amp;backto=<?php echo $goback; ?>"><?php _e( '升级GeChiUI数据库' ); ?></a></p>
			<?php
			break;
		case 1:
			gc_upgrade();

			$backto = ! empty( $_GET['backto'] ) ? gc_unslash( urldecode( $_GET['backto'] ) ) : __get_option( 'home' ) . '/';
			$backto = esc_url( $backto );
			$backto = gc_validate_redirect( $backto, __get_option( 'home' ) . '/' );
			?>
	<h1><?php _e( '升级完成' ); ?></h1>
	<p><?php _e( '您的GeChiUI数据库已成功升级！' ); ?></p>
	<p class="step"><a class="button button-large" href="<?php echo $backto; ?>"><?php _e( '继续' ); ?></a></p>
			<?php
			break;
endswitch;
endif;
?>
</body>
</html>
