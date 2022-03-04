<?php
/**
 * Database Repair and Optimization Script.
 *
 * @package GeChiUI
 * @subpackage Database
 */
define( 'GC_REPAIRING', true );

require_once dirname( dirname( __DIR__ ) ) . '/gc-load.php';

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title><?php _e( 'GeChiUI &rsaquo; 数据库修复' ); ?></title>
	<?php gc_admin_css( 'install', true ); ?>
</head>
<body class="gc-core-ui">
<p id="logo"><a href="<?php echo esc_url( __( 'https://www.gechiui.com/' ) ); ?>"><?php _e( 'GeChiUI' ); ?></a></p>

<?php

if ( ! defined( 'GC_ALLOW_REPAIR' ) || ! GC_ALLOW_REPAIR ) {

	echo '<h1 class="screen-reader-text">' . __( '允许自动数据库修复' ) . '</h1>';

	echo '<p>';
	printf(
		/* translators: %s: gc-config.php */
		__( '要允许本页面自动修复数据库问题，请将下面一行加入您的%s文件。完成后请刷新本页面。' ),
		'<code>gc-config.php</code>'
	);
	echo "</p><p><code>define('GC_ALLOW_REPAIR', true);</code></p>";

	$default_key     = 'put your unique phrase here';
	$missing_key     = false;
	$duplicated_keys = array();

	foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $key ) {
		if ( defined( $key ) ) {
			// Check for unique values of each key.
			$duplicated_keys[ constant( $key ) ] = isset( $duplicated_keys[ constant( $key ) ] );
		} else {
			// If a constant is not defined, it's missing.
			$missing_key = true;
		}
	}

	// If at least one key uses the default value, consider it duplicated.
	if ( isset( $duplicated_keys[ $default_key ] ) ) {
		$duplicated_keys[ $default_key ] = true;
	}

	// Weed out all unique, non-default values.
	$duplicated_keys = array_filter( $duplicated_keys );

	if ( $duplicated_keys || $missing_key ) {

		echo '<h2 class="screen-reader-text">' . __( '检查密钥' ) . '</h2>';

		/* translators: 1: gc-config.php, 2: Secret key service URL. */
		echo '<p>' . sprintf( __( '当您在编辑您的%1$s文件时，请花点时间确认您有全部8个密钥，并且他们是独一无二的。您可以用<a href="%2$s">www.GeChiUI.com密钥服务</a>来生成它们。' ), '<code>gc-config.php</code>', 'https://api.gechiui.com/secret-key/1.1/salt/' ) . '</p>';
	}
} elseif ( isset( $_GET['repair'] ) ) {

	echo '<h1 class="screen-reader-text">' . __( '数据库修复结果' ) . '</h1>';

	$optimize = 2 == $_GET['repair'];
	$okay     = true;
	$problems = array();

	$tables = $gcdb->tables();

	// Sitecategories may not exist if global terms are disabled.
	$query = $gcdb->prepare( 'SHOW TABLES LIKE %s', $gcdb->esc_like( $gcdb->sitecategories ) );
	if ( is_multisite() && ! $gcdb->get_var( $query ) ) {
		unset( $tables['sitecategories'] );
	}

	/**
	 * Filters additional database tables to repair.
	 *
	 *
	 * @param string[] $tables Array of prefixed table names to be repaired.
	 */
	$tables = array_merge( $tables, (array) apply_filters( 'tables_to_repair', array() ) );

	// Loop over the tables, checking and repairing as needed.
	foreach ( $tables as $table ) {
		$check = $gcdb->get_row( "CHECK TABLE $table" );

		echo '<p>';
		if ( 'OK' === $check->Msg_text ) {
			/* translators: %s: Table name. */
			printf( __( '%s数据表正常。' ), "<code>$table</code>" );
		} else {
			/* translators: 1: Table name, 2: Error message. */
			printf( __( '“%1$s”数据表有问题。报告的问题是：%2$s。GeChiUI正在尝试进行修复&hellip;' ), "<code>$table</code>", "<code>$check->Msg_text</code>" );

			$repair = $gcdb->get_row( "REPAIR TABLE $table" );

			echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;';
			if ( 'OK' === $repair->Msg_text ) {
				/* translators: %s: Table name. */
				printf( __( '成功修复了%s数据表。' ), "<code>$table</code>" );
			} else {
				/* translators: 1: Table name, 2: Error message. */
				printf( __( '无法修复%1$s表。错误消息：%2$s' ), "<code>$table</code>", "<code>$repair->Msg_text</code>" ) . '<br />';
				$problems[ $table ] = $repair->Msg_text;
				$okay               = false;
			}
		}

		if ( $okay && $optimize ) {
			$analyze = $gcdb->get_row( "ANALYZE TABLE $table" );

			echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;';
			if ( 'Table is already up to date' === $analyze->Msg_text ) {
				/* translators: %s: Table name. */
				printf( __( '%s数据表已优化过了。' ), "<code>$table</code>" );
			} else {
				$optimize = $gcdb->get_row( "OPTIMIZE TABLE $table" );

				echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;';
				if ( 'OK' === $optimize->Msg_text || 'Table is already up to date' === $optimize->Msg_text ) {
					/* translators: %s: Table name. */
					printf( __( '成功优化%s数据表。' ), "<code>$table</code>" );
				} else {
					/* translators: 1: Table name. 2: Error message. */
					printf( __( '无法优化%1$s表。错误消息：%2$s' ), "<code>$table</code>", "<code>$optimize->Msg_text</code>" );
				}
			}
		}
		echo '</p>';
	}

	if ( $problems ) {
		printf(
			/* translators: %s: URL to "Fixing GeChiUI" forum. */
			'<p>' . __( '部分数据库问题无法修复。请复制下列错误信息，前往<a href="%s">GeChiUI支持论坛</a>寻求帮助。' ) . '</p>',
			__( 'https://www.gechiui.com/support/forum/issues/' )
		);
		$problem_output = '';
		foreach ( $problems as $table => $problem ) {
			$problem_output .= "$table: $problem\n";
		}
		echo '<p><textarea name="errors" id="errors" rows="20" cols="60">' . esc_textarea( $problem_output ) . '</textarea></p>';
	} else {
		echo '<p>' . __( '修复完成。请移除刚刚在gc-config.php中添加的那行代码，以防他人滥用本页面。' ) . "</p><p><code>define('GC_ALLOW_REPAIR', true);</code></p>";
	}
} else {

	echo '<h1 class="screen-reader-text">' . __( 'GeChiUI数据库修复' ) . '</h1>';

	if ( isset( $_GET['referrer'] ) && 'is_blog_installed' === $_GET['referrer'] ) {
		echo '<p>' . __( '有些数据表无效。若您希望让GeChiUI尝试修复它们，请点击“修复数据库”按钮。修复过程需要一点时间，请耐心等待。' ) . '</p>';
	} else {
		echo '<p>' . __( 'GeChiUI能自动检测并修复一些常见数据库问题。修复过程需要一段时间，请耐心等待。' ) . '</p>';
	}
	?>
	<p class="step"><a class="button button-large" href="repair.php?repair=1"><?php _e( '修复数据库' ); ?></a></p>
	<p><?php _e( 'GeChiUI还可以尝试优化数据库，这在某些情况下能提高数据库性能。修复和优化数据库的过程需要一段时间，请耐心等待——在此期间我们会锁定（lock）数据表。' ); ?></p>
	<p class="step"><a class="button button-large" href="repair.php?repair=2"><?php _e( '修复并优化数据库' ); ?></a></p>
	<?php
}
?>
</body>
</html>
