<?php
/**
 * Retrieves and creates the gc-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the gc-config.php to be created using this page.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * We are installing.
 */
define( 'GC_INSTALLING', true );

/**
 * We are blissfully unaware of anything.
 */
define( 'GC_SETUP_CONFIG', true );

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging
 */
error_reporting( 0 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require ABSPATH . 'gc-settings.php';

/** Load GeChiUI Administration Upgrade API */
require_once ABSPATH . 'gc-admin/includes/upgrade.php';

/** Load GeChiUI Translation Installation API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

nocache_headers();

// Support gc-config-sample.php one level up, for the develop repo.
if ( file_exists( ABSPATH . 'gc-config-sample.php' ) ) {
	$config_file = file( ABSPATH . 'gc-config-sample.php' );
} elseif ( file_exists( dirname( ABSPATH ) . '/gc-config-sample.php' ) ) {
	$config_file = file( dirname( ABSPATH ) . '/gc-config-sample.php' );
} else {
	gc_die(
		sprintf(
			/* translators: %s: gc-config-sample.php */
			__( '抱歉，我需要%s文件才能工作。请重新上传该文件到您的GeChiUI。' ),
			'<code>gc-config-sample.php</code>'
		)
	);
}

// Check if gc-config.php has been created.
if ( file_exists( ABSPATH . 'gc-config.php' ) ) {
	gc_die(
		'<p>' . sprintf(
			/* translators: 1: gc-config.php, 2: install.php */
			__( '文件%1$s已经存在。如果您希望重置该文件中的任何配置项目，请先删除该文件。您可以<a href="%2$s">现在安装</a>。' ),
			'<code>gc-config.php</code>',
			'install.php'
		) . '</p>',
		409
	);
}

// Check if gc-config.php exists above the root directory but is not part of another installation.
if ( @file_exists( ABSPATH . '../gc-config.php' ) && ! @file_exists( ABSPATH . '../gc-settings.php' ) ) {
	gc_die(
		'<p>' . sprintf(
			/* translators: 1: gc-config.php, 2: install.php */
			__( '文件%1$s已经存在于GeChiUI的上级目录。如果您希望重置该文件中的任何配置项目，请先删除该文件。您可以<a href="%2$s">现在安装</a>。' ),
			'<code>gc-config.php</code>',
			'install.php'
		) . '</p>',
		409
	);
}

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : -1;

/**
 * Display setup gc-config.php file header.
 *
 * @ignore
 *
 * @param string|string[] $body_classes Class attribute values for the body tag.
 */
function setup_config_display_header( $body_classes = array() ) {
	$body_classes   = (array) $body_classes;
	$body_classes[] = 'gc-core-ui';
	$dir_attr       = '';
	if ( is_rtl() ) {
		$body_classes[] = 'rtl';
		$dir_attr       = ' dir="rtl"';
	}

	header( 'Content-Type: text/html; charset=utf-8' );
	?>
<!DOCTYPE html>
<html<?php echo $dir_attr; ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title><?php _e( 'GeChiUI &rsaquo; 安装配置文件' ); ?></title>
	<?php gc_admin_css( 'install', true ); ?>
</head>
<body class="<?php echo implode( ' ', $body_classes ); ?>">
<p id="logo"><?php _e( 'GeChiUI' ); ?></p>
	<?php
} // End function setup_config_display_header();

$language = '';
if ( ! empty( $_REQUEST['language'] ) ) {
	$language = preg_replace( '/[^a-zA-Z0-9_]/', '', $_REQUEST['language'] );
} elseif ( isset( $GLOBALS['gc_local_package'] ) ) {
	$language = $GLOBALS['gc_local_package'];
}

switch ( $step ) {
	case -1:
		if ( gc_can_install_language_pack() && empty( $language ) ) {
			$languages = gc_get_available_translations();
			if ( $languages ) {
				setup_config_display_header( 'language-chooser' );
				echo '<h1 class="screen-reader-text">Select a default language</h1>';
				echo '<form id="setup" method="post" action="?step=0">';
				gc_install_language_form( $languages );
				echo '</form>';
				break;
			}
		}

		// Deliberately fall through if we can't reach the translations API.

	case 0:
		if ( ! empty( $language ) ) {
			$loaded_language = gc_download_language_pack( $language );
			if ( $loaded_language ) {
				load_default_textdomain( $loaded_language );
				$GLOBALS['gc_locale'] = new GC_Locale();
			}
		}

		setup_config_display_header();
		$step_1 = 'setup-config.php?step=1';
		if ( isset( $_REQUEST['noapi'] ) ) {
			$step_1 .= '&amp;noapi';
		}
		if ( ! empty( $loaded_language ) ) {
			$step_1 .= '&amp;language=' . $loaded_language;
		}
		?>
<h1 class="screen-reader-text"><?php _e( '开始之前' ); ?></h1>
<p><?php _e( '欢迎使用GeChiUI。在开始前，我们需要您数据库的一些信息。请准备好如下信息。' ); ?></p>
<ol>
	<li><?php _e( '数据库名' ); ?></li>
	<li><?php _e( '数据库用户名' ); ?></li>
	<li><?php _e( '数据库密码' ); ?></li>
	<li><?php _e( '数据库主机' ); ?></li>
	<li><?php _e( '数据表前缀（table prefix，特别是当您要在一个数据库中安装多个GeChiUI时）' ); ?></li>
</ol>
<p>
		<?php
		printf(
			/* translators: %s: gc-config.php */
			__( '我们会使用这些信息来创建一个%s文件。' ),
			'<code>gc-config.php</code>'
		);
		?>
	<strong>
		<?php
		printf(
			/* translators: 1: gc-config-sample.php, 2: gc-config.php */
			__( '如果自动创建未能成功，不用担心，您要做的只是将数据库信息填入配置文件。您也可以在文本编辑器中打开%1$s，填入您的信息，并将其另存为%2$s。' ),
			'<code>gc-config-sample.php</code>',
			'<code>gc-config.php</code>'
		);
		?>
	</strong>
		<?php
		printf(
			/* translators: %s: Documentation URL. */
			__( '需要更多帮助？<a href="%s">看这里</a>。' ),
			__( 'https://www.gechiui.com/support/editing-gc-config-php/' )
		);
		?>
</p>
<p><?php _e( '绝大多数时候，您的主机服务提供商会告诉您这些信息。如果您没有这些信息，在继续之前您将需要联系他们。如果您准备好了...'  ); ?></p>

<p class="step"><a href="<?php echo $step_1; ?>" class="btn btn-primary"><?php _e( '现在就开始！' ); ?></a></p>
		<?php
		break;

	case 1:
		load_default_textdomain( $language );
		$GLOBALS['gc_locale'] = new GC_Locale();

		setup_config_display_header();

		$autofocus = gc_is_mobile() ? '' : ' autofocus';
		?>
<h1 class="screen-reader-text"><?php _e( '配置您的数据库连接' ); ?></h1>
<form method="post" action="setup-config.php?step=2">
	<p><?php _e( '请在下方填写您的数据库连接信息。如果您不确定，请联系您的主机提供商。' ); ?></p>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="dbname"><?php _e( '数据库名' ); ?></label></th>
			<td><input name="dbname" id="dbname" type="text" aria-describedby="dbname-desc" size="25" value="gechiui"<?php echo $autofocus; ?>/></td>
			<td id="dbname-desc"><?php _e( '希望将GeChiUI安装到的数据库名称。' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="uname"><?php _e( '用户名' ); ?></label></th>
			<td><input name="uname" id="uname" type="text" aria-describedby="uname-desc" size="25" value="<?php echo htmlspecialchars( _x( '用户名', 'example username' ), ENT_QUOTES ); ?>" /></td>
			<td id="uname-desc"><?php _e( '您的数据库用户名。' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd"><?php _e( '密码' ); ?></label></th>
			<td><input name="pwd" id="pwd" type="text" aria-describedby="pwd-desc" size="25" value="<?php echo htmlspecialchars( _x( '密码', 'example password' ), ENT_QUOTES ); ?>" autocomplete="off" /></td>
			<td id="pwd-desc"><?php _e( '您的数据库密码。' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost"><?php _e( '数据库主机' ); ?></label></th>
			<td><input name="dbhost" id="dbhost" type="text" aria-describedby="dbhost-desc" size="25" value="localhost" /></td>
			<td id="dbhost-desc">
			<?php
				/* translators: %s: localhost */
				printf( __( '如果%s不能用，您通常可以从主机提供商处得到正确的信息。' ), '<code>localhost</code>' );
			?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix"><?php _e( '表前缀' ); ?></label></th>
			<td><input name="prefix" id="prefix" type="text" aria-describedby="prefix-desc" value="gc_" size="25" /></td>
			<td id="prefix-desc"><?php _e( '如果您希望在同一个数据库安装多个GeChiUI，请修改前缀。' ); ?></td>
		</tr>
	</table>
		<?php
		if ( isset( $_GET['noapi'] ) ) {
			?>
<input name="noapi" type="hidden" value="1" /><?php } ?>
	<input type="hidden" name="language" value="<?php echo esc_attr( $language ); ?>" />
	<p class="step"><input name="submit" type="submit" value="<?php echo htmlspecialchars( __( '提交' ), ENT_QUOTES ); ?>" class="btn btn-primary" /></p>
</form>
		<?php
		break;

	case 2:
		load_default_textdomain( $language );
		$GLOBALS['gc_locale'] = new GC_Locale();

		$dbname = trim( gc_unslash( $_POST['dbname'] ) );
		$uname  = trim( gc_unslash( $_POST['uname'] ) );
		$pwd    = trim( gc_unslash( $_POST['pwd'] ) );
		$dbhost = trim( gc_unslash( $_POST['dbhost'] ) );
		$prefix = trim( gc_unslash( $_POST['prefix'] ) );

		$step_1  = 'setup-config.php?step=1';
		$install = 'install.php';
		if ( isset( $_REQUEST['noapi'] ) ) {
			$step_1 .= '&amp;noapi';
		}

		if ( ! empty( $language ) ) {
			$step_1  .= '&amp;language=' . $language;
			$install .= '?language=' . $language;
		} else {
			$install .= '?language=zh_CN';
		}

		$tryagain_link = '</p><p class="step"><a href="' . $step_1 . '" onclick="javascript:history.go(-1);return false;" class="btn btn-primary">' . __( '重试' ) . '</a>';

		if ( empty( $prefix ) ) {
			gc_die( __( '<strong>错误</strong>: "表前缀"不能为空。' ) . $tryagain_link );
		}

		// Validate $prefix: it can only contain letters, numbers and underscores.
		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ) {
			gc_die( __( '<strong>错误</strong>: "表前缀"只能包含数字、字母和下划线。' ) . $tryagain_link );
		}

		// Test the DB connection.
		/**#@+
		 *
		 * @ignore
		 */
		define( 'DB_NAME', $dbname );
		define( 'DB_USER', $uname );
		define( 'DB_PASSWORD', $pwd );
		define( 'DB_HOST', $dbhost );
		/**#@-*/

		// Re-construct $gcdb with these new values.
		unset( $gcdb );
		require_gc_db();

		/*
		* The gcdb constructor bails when GC_SETUP_CONFIG is set, so we must
		* fire this manually. We'll fail here if the values are no good.
		*/
		$gcdb->db_connect();

		if ( ! empty( $gcdb->error ) ) {
			gc_die( $gcdb->error->get_error_message() . $tryagain_link );
		}

		$errors = $gcdb->hide_errors();
		$gcdb->query( "SELECT $prefix" );
		$gcdb->show_errors( $errors );
		if ( ! $gcdb->last_error ) {
			// MySQL was able to parse the prefix as a value, which we don't want. Bail.
			gc_die( __( '<strong>错误</strong>: "表前缀"无效。' ) );
		}

		// Generate keys and salts using secure CSPRNG; fallback to API if enabled; further fallback to original gc_generate_password().
		try {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
			$max   = strlen( $chars ) - 1;
			for ( $i = 0; $i < 8; $i++ ) {
				$key = '';
				for ( $j = 0; $j < 64; $j++ ) {
					$key .= substr( $chars, random_int( 0, $max ), 1 );
				}
				$secret_keys[] = $key;
			}
		} catch ( Exception $ex ) {
			$no_api = isset( $_POST['noapi'] );

			if ( ! $no_api ) {
				$secret_keys = gc_remote_get( 'https://api.gechiui.com/secret-key/1.1/salt/' );
			}

			if ( $no_api || is_gc_error( $secret_keys ) ) {
				$secret_keys = array();
				for ( $i = 0; $i < 8; $i++ ) {
					$secret_keys[] = gc_generate_password( 64, true, true );
				}
			} else {
				$secret_keys = explode( "\n", gc_remote_retrieve_body( $secret_keys ) );
				foreach ( $secret_keys as $k => $v ) {
					$secret_keys[ $k ] = substr( $v, 28, 64 );
				}
			}
		}

		$key = 0;
		foreach ( $config_file as $line_num => $line ) {
			if ( '$table_prefix =' === substr( $line, 0, 15 ) ) {
				$config_file[ $line_num ] = '$table_prefix = \'' . addcslashes( $prefix, "\\'" ) . "';\r\n";
				continue;
			}

			if ( ! preg_match( '/^define\(\s*\'([A-Z_]+)\',([ ]+)/', $line, $match ) ) {
				continue;
			}

			$constant = $match[1];
			$padding  = $match[2];

			switch ( $constant ) {
				case 'DB_NAME':
				case 'DB_USER':
				case 'DB_PASSWORD':
				case 'DB_HOST':
					$config_file[ $line_num ] = "define( '" . $constant . "'," . $padding . "'" . addcslashes( constant( $constant ), "\\'" ) . "' );\r\n";
					break;
				case 'DB_CHARSET':
					if ( 'utf8mb4' === $gcdb->charset || ( ! $gcdb->charset && $gcdb->has_cap( 'utf8mb4' ) ) ) {
						$config_file[ $line_num ] = "define( '" . $constant . "'," . $padding . "'utf8mb4' );\r\n";
					}
					break;
				case 'AUTH_KEY':
				case 'SECURE_AUTH_KEY':
				case 'LOGGED_IN_KEY':
				case 'NONCE_KEY':
				case 'AUTH_SALT':
				case 'SECURE_AUTH_SALT':
				case 'LOGGED_IN_SALT':
				case 'NONCE_SALT':
					$config_file[ $line_num ] = "define( '" . $constant . "'," . $padding . "'" . $secret_keys[ $key++ ] . "' );\r\n";
					break;
			}
		}
		unset( $line );

		if ( ! is_writable( ABSPATH ) ) :
			setup_config_display_header();
			?>
	<p>
			<?php
			/* translators: %s: gc-config.php */
			printf( __( '无法写入%s文件。' ), '<code>gc-config.php</code>' );
			?>
</p>
<p>
			<?php
			/* translators: %s: gc-config.php */
			printf( __( '您可以手工创建%s文件，并将以下文字粘贴于其中。' ), '<code>gc-config.php</code>' );

			$config_text = '';

			foreach ( $config_file as $line ) {
				$config_text .= htmlentities( $line, ENT_COMPAT, 'UTF-8' );
			}
			?>
</p>
<textarea id="gc-config" cols="98" rows="15" class="code" readonly="readonly"><?php echo $config_text; ?></textarea>
<p><?php _e( '完成这些后，请点击“运行安装程序”。' ); ?></p>
<p class="step"><a href="<?php echo $install; ?>" class="btn btn-primary"><?php _e( '运行安装程序' ); ?></a></p>
<script>
(function(){
if ( ! /iPad|iPod|iPhone/.test( navigator.userAgent ) ) {
	var el = document.getElementById('gc-config');
	el.focus();
	el.select();
}
})();
</script>
			<?php
		else :
			/*
			 * If this file doesn't exist, then we are using the gc-config-sample.php
			 * file one level up, which is for the develop repo.
			 */
			if ( file_exists( ABSPATH . 'gc-config-sample.php' ) ) {
				$path_to_gc_config = ABSPATH . 'gc-config.php';
			} else {
				$path_to_gc_config = dirname( ABSPATH ) . '/gc-config.php';
			}

			$error_message = '';
			$handle        = fopen( $path_to_gc_config, 'w' );
			/*
			 * Why check for the absence of false instead of checking for resource with is_resource()?
			 * To future-proof the check for when fopen returns object instead of resource, i.e. a known
			 * change coming in PHP.
			 */
			if ( false !== $handle ) {
				foreach ( $config_file as $line ) {
					fwrite( $handle, $line );
				}
				fclose( $handle );
			} else {
				$gc_config_perms = fileperms( $path_to_gc_config );
				if ( ! empty( $gc_config_perms ) && ! is_writable( $path_to_gc_config ) ) {
					$error_message = sprintf(
						/* translators: 1: gc-config.php, 2: Documentation URL. */
						__( '在保存更改之前，您需要使文件 %1$s 可写。请参阅<a href="%2$s">更改文件权限</a>以了解更多信息。' ),
						'<code>gc-config.php</code>',
						__( 'https://www.gechiui.com/support/changing-file-permissions/' )
					);
				} else {
					$error_message = sprintf(
						/* translators: %s: gc-config.php */
						__( '无法写入%s文件。' ),
						'<code>gc-config.php</code>'
					);
				}
			}

			chmod( $path_to_gc_config, 0666 );
			setup_config_display_header();

			if ( false !== $handle ) :
				?>
<h1 class="screen-reader-text"><?php _e( '数据库连接成功' ); ?></h1>
<p><?php _e( '不错。您完成了安装过程中重要的一步，GeChiUI现在已经可以连接数据库了。如果您准备好了的话，现在就&hellip;' ); ?></p>

<p class="step"><a href="<?php echo $install; ?>" class="btn btn-primary"><?php _e( '运行安装程序' ); ?></a></p>
				<?php
			else :
				printf( '<p>%s</p>', $error_message );
			endif;
		endif;
		break;
} // End of the steps switch.
?>
<?php gc_print_scripts( 'language-chooser' ); ?>
</body>
</html>
