<?php
/**
 * GeChiUI Installer
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Sanity check.
if ( false ) {
	?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error: PHP is not running</title>
</head>
<body class="gc-core-ui">
	<p id="logo"><a href="https://www.gechiui.com/">GeChiUI</a></p>
	<h1>Error: PHP is not running</h1>
	<p>GeChiUI requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
</body>
</html>
	<?php
}

/**
 * We are installing GeChiUI.
 *
 * @var bool
 */
define( 'GC_INSTALLING', true );

/** Load GeChiUI Bootstrap */
require_once dirname( __DIR__ ) . '/gc-load.php';

/** Load GeChiUI Administration Upgrade API */
require_once ABSPATH . 'gc-admin/includes/upgrade.php';

/** Load GeChiUI Translation Install API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

/** Load gcdb */
require_once ABSPATH . GCINC . '/gc-db.php';

nocache_headers();

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

/**
 * Display installation header.
 *
 *
 * @param string $body_classes
 */
function display_header( $body_classes = '' ) {
	header( 'Content-Type: text/html; charset=utf-8' );
	if ( is_rtl() ) {
		$body_classes .= 'rtl';
	}
	if ( $body_classes ) {
		$body_classes = ' ' . $body_classes;
	}
	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title><?php _e( 'GeChiUI &rsaquo; 安装' ); ?></title>
	<?php gc_admin_css( 'install', true ); ?>
</head>
<body class="gc-core-ui<?php echo $body_classes; ?>">
<p id="logo"><?php _e( 'GeChiUI' ); ?></p>

	<?php
} // End display_header().

/**
 * Display installer setup form.
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string|null $error
 */
function display_setup_form( $error = null ) {
	global $gcdb;

	$user_table = ( $gcdb->get_var( $gcdb->prepare( 'SHOW TABLES LIKE %s', $gcdb->esc_like( $gcdb->users ) ) ) !== null );

	// Ensure that sites appear in search engines by default.
	$blog_public = 1;
	if ( isset( $_POST['weblog_title'] ) ) {
		$blog_public = isset( $_POST['blog_public'] );
	}

	$weblog_title = isset( $_POST['weblog_title'] ) ? trim( gc_unslash( $_POST['weblog_title'] ) ) : '';
	$user_name    = isset( $_POST['user_name'] ) ? trim( gc_unslash( $_POST['user_name'] ) ) : '';
	$admin_email  = isset( $_POST['admin_email'] ) ? trim( gc_unslash( $_POST['admin_email'] ) ) : '';

	if ( ! is_null( $error ) ) {
		?>
<h1><?php _ex( '欢迎', 'Howdy' ); ?></h1>
<p class="message"><?php echo $error; ?></p>
<?php } ?>
<form id="setup" method="post" action="install.php?step=2" novalidate="novalidate">
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="weblog_title"><?php _e( '系统标题' ); ?></label></th>
			<td><input name="weblog_title" type="text" id="weblog_title" size="25" value="<?php echo esc_attr( $weblog_title ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="user_login"><?php _e( '用户名' ); ?></label></th>
			<td>
			<?php
			if ( $user_table ) {
				_e( '用户已存在。' );
				echo '<input name="user_name" type="hidden" value="admin" />';
			} else {
				?>
				<input name="user_name" type="text" id="user_login" size="25" value="<?php echo esc_attr( sanitize_user( $user_name, true ) ); ?>" />
				<p><?php _e( '用户名只能含有字母、数字、空格、下划线、连字符、句号和“@”符号。' ); ?></p>
				<?php
			}
			?>
			</td>
		</tr>
		<?php if ( ! $user_table ) : ?>
		<tr class="form-field form-required user-pass1-wrap">
			<th scope="row">
				<label for="pass1">
					<?php _e( '密码' ); ?>
				</label>
			</th>
			<td>
				<div class="gc-pwd">
					<?php $initial_password = isset( $_POST['admin_password'] ) ? stripslashes( $_POST['admin_password'] ) : gc_generate_password( 18 ); ?>
					<span class="password-input-wrapper">
						<input type="password" name="admin_password" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
						<div id="pass-strength-result" aria-live="polite"></div>
					</span>
					<button type="button" class="button gc-hide-pw hide-if-no-js" data-start-masked="<?php echo (int) isset( $_POST['admin_password'] ); ?>" data-toggle="0" aria-label="<?php esc_attr_e( '隐藏密码' ); ?>">
						<span class="dashicons dashicons-hidden"></span>
						<span class="text"><?php _e( '隐藏' ); ?></span>
					</button>
					
				</div>
				<p><span class="description important hide-if-no-js">
				<strong><?php _e( '重要：' ); ?></strong>
				<?php /* translators: The non-breaking space prevents 1Password from thinking the text "log in" should trigger a password save prompt. */ ?>
				<?php _e( '您将需要此密码来登录，请将其保存在安全的位置。' ); ?></span></p>
			</td>
		</tr>
		<tr class="form-field form-required user-pass2-wrap hide-if-js">
			<th scope="row">
				<label for="pass2"><?php _e( '重复密码' ); ?>
					<span class="description"><?php _e( '（必填）' ); ?></span>
				</label>
			</th>
			<td>
				<input name="admin_password2" type="password" id="pass2" autocomplete="off" />
			</td>
		</tr>
		<tr class="pw-weak">
			<th scope="row"><?php _e( '确认密码' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="pw_weak" class="pw-checkbox" />
					<?php _e( '确认使用弱密码' ); ?>
				</label>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th scope="row"><label for="admin_email"><?php _e( '您的电子邮箱' ); ?></label></th>
			<td><input name="admin_email" type="email" id="admin_email" size="25" value="<?php echo esc_attr( $admin_email ); ?>" />
			<p><?php _e( '请仔细检查电子邮箱后再继续。' ); ?></p></td>
		</tr>
		<tr>
			<th scope="row"><?php has_action( 'blog_privacy_selector' ) ? _e( '系统可见性' ) : _e( '搜索引擎' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php has_action( 'blog_privacy_selector' ) ? _e( '系统可见性' ) : _e( '搜索引擎' ); ?> </span></legend>
					<?php
					if ( has_action( 'blog_privacy_selector' ) ) {
						?>
						<input id="blog-public" type="radio" name="blog_public" value="1" <?php checked( 1, $blog_public ); ?> />
						<label for="blog-public"><?php _e( '允许搜索引擎索引本系统' ); ?></label><br/>
						<input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked( 0, $blog_public ); ?> />
						<label for="blog-norobots"><?php _e( '建议搜索引擎不索引本系统' ); ?></label>
						<p class="description"><?php _e( '注意：这些设置并不能彻底防止搜索引擎访问您的系统——具体行为还取决于它们是否遵循您的要求。' ); ?></p>
						<?php
						/** This action is documented in gc-admin/options-reading.php */
						do_action( 'blog_privacy_selector' );
					} else {
						?>
						<label for="blog_public"><input name="blog_public" type="checkbox" id="blog_public" value="0" <?php checked( 0, $blog_public ); ?> />
						<?php _e( '建议搜索引擎不索引本系统' ); ?></label>
						<p class="description"><?php _e( '搜索引擎将本着自觉自愿的原则对待GeChiUI提出的请求。并不是所有搜索引擎都会遵守这类请求。' ); ?></p>
					<?php } ?>
				</fieldset>
			</td>
		</tr>
	</table>
	<p class="step"><?php submit_button( __( '安装GeChiUI' ), 'primary', 'Submit', false, array( 'id' => 'submit' ) ); ?></p>
	<input type="hidden" name="language" value="<?php echo isset( $_REQUEST['language'] ) ? esc_attr( $_REQUEST['language'] ) : ''; ?>" />
</form>
	<?php
} // End display_setup_form().

// Let's check to make sure GC isn't already installed.
if ( is_blog_installed() ) {
	display_header();
	die(
		'<h1>' . __( '已安装过' ) . '</h1>' .
		'<p>' . __( '您的GeChiUI看起来已经安装妥当。如果想重新安装，请删除数据库中的旧数据表。' ) . '</p>' .
		'<p class="step"><a href="' . esc_url( gc_login_url() ) . '" class="btn btn-primary">' . __( '登录' ) . '</a></p>' .
		'</body></html>'
	);
}

/**
 * @global string $gc_version             The GeChiUI version string.
 * @global string $required_php_version   The required PHP version string.
 * @global string $required_mysql_version The required MySQL version string.
 */
global $gc_version, $required_php_version, $required_mysql_version;

$php_version   = phpversion();
$mysql_version = $gcdb->db_version();
$php_compat    = version_compare( $php_version, $required_php_version, '>=' );
$mysql_compat  = version_compare( $mysql_version, $required_mysql_version, '>=' ) || file_exists( GC_CONTENT_DIR . '/db.php' );

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
	$compat = sprintf(
		/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Minimum required MySQL version number, 5: Current PHP version number, 6: Current MySQL version number. */
		__( '您不能安装<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高及MySQL版本%4$s或更高。您正在运行PHP %5$s和MySQL %6$s。' ),
		$version_url,
		$gc_version,
		$required_php_version,
		$required_mysql_version,
		$php_version,
		$mysql_version
	) . $php_update_message;
} elseif ( ! $php_compat ) {
	$compat = sprintf(
		/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required PHP version number, 4: Current PHP version number. */
		__( '您不能安装<a href="%1$s">GeChiUI %2$s</a>，因其需要PHP版本%3$s或更高。您正在运行版本%4$s。' ),
		$version_url,
		$gc_version,
		$required_php_version,
		$php_version
	) . $php_update_message;
} elseif ( ! $mysql_compat ) {
	$compat = sprintf(
		/* translators: 1: URL to GeChiUI release notes, 2: GeChiUI version number, 3: Minimum required MySQL version number, 4: Current MySQL version number. */
		__( '您不能安装<a href="%1$s">GeChiUI %2$s</a>，因其需要MySQL版本%3$s或更高。您正在运行版本%4$s。' ),
		$version_url,
		$gc_version,
		$required_mysql_version,
		$mysql_version
	);
}

if ( ! $mysql_compat || ! $php_compat ) {
	display_header();
	die( '<h1>' . __( '未满足要求' ) . '</h1><p>' . $compat . '</p></body></html>' );
}

if ( ! is_string( $gcdb->base_prefix ) || '' === $gcdb->base_prefix ) {
	display_header();
	die(
		'<h1>' . __( '配置有误' ) . '</h1>' .
		'<p>' . sprintf(
			/* translators: %s: gc-config.php */
			__( '您的%s文件有一个空的数据库表前缀，这不被支持。' ),
			'<code>gc-config.php</code>'
		) . '</p></body></html>'
	);
}

// Set error message if DO_NOT_UPGRADE_GLOBAL_TABLES isn't set as it will break install.
if ( defined( 'DO_NOT_UPGRADE_GLOBAL_TABLES' ) ) {
	display_header();
	die(
		'<h1>' . __( '配置有误' ) . '</h1>' .
		'<p>' . sprintf(
			/* translators: %s: DO_NOT_UPGRADE_GLOBAL_TABLES */
			__( '常量%s不能在安装GeChiUI时被定义。' ),
			'<code>DO_NOT_UPGRADE_GLOBAL_TABLES</code>'
		) . '</p></body></html>'
	);
}

/**
 * @global string    $gc_local_package Locale code of the package.
 * @global GC_Locale $gc_locale        GeChiUI date and time locale object.
 */
$language = '';
if ( ! empty( $_REQUEST['language'] ) ) {
	$language = preg_replace( '/[^a-zA-Z0-9_]/', '', $_REQUEST['language'] );
} elseif ( isset( $GLOBALS['gc_local_package'] ) ) {
	$language = $GLOBALS['gc_local_package'];
}

$scripts_to_print = array( 'jquery' );

switch ( $step ) {
	case 0: // Step 0.
		if ( gc_can_install_language_pack() && empty( $language ) ) {
			$languages = gc_get_available_translations();
			if ( $languages ) {
				$scripts_to_print[] = 'language-chooser';
				display_header( 'language-chooser' );
				echo '<form id="setup" method="post" action="?step=1">';
				gc_install_language_form( $languages );
				echo '</form>';
				break;
			}
		}

		// Deliberately fall through if we can't reach the translations API.

	case 1: // Step 1, direct link or from language chooser.
		if ( ! empty( $language ) ) {
			$loaded_language = gc_download_language_pack( $language );
			if ( $loaded_language ) {
				load_default_textdomain( $loaded_language );
				$GLOBALS['gc_locale'] = new GC_Locale();
			}
		}

		$scripts_to_print[] = 'user-profile';

		display_header();
		?>
<h1><?php _ex( '欢迎', 'Howdy' ); ?></h1>
<p><?php _e( '欢迎使用GeChiUI五分钟安装程序！请简单地填写下面的表单，来开始使用格尺・后台开发框架。' ); ?></p>

<h4><?php _e( '需要信息' ); ?></h4>
<p><?php _e( '您需要填写一些基本信息。无需担心填错，这些信息以后可以再次修改。' ); ?></p>

		<?php
		display_setup_form();
		break;
	case 2:
		if ( ! empty( $language ) && load_default_textdomain( $language ) ) {
			$loaded_language      = $language;
			$GLOBALS['gc_locale'] = new GC_Locale();
		} else {
			$loaded_language = 'zh_CN';
		}

		if ( ! empty( $gcdb->error ) ) {
			gc_die( $gcdb->error->get_error_message() );
		}

		$scripts_to_print[] = 'user-profile';

		display_header();
		// Fill in the data we gathered.
		$weblog_title         = isset( $_POST['weblog_title'] ) ? trim( gc_unslash( $_POST['weblog_title'] ) ) : '';
		$user_name            = isset( $_POST['user_name'] ) ? trim( gc_unslash( $_POST['user_name'] ) ) : '';
		$admin_password       = isset( $_POST['admin_password'] ) ? gc_unslash( $_POST['admin_password'] ) : '';
		$admin_password_check = isset( $_POST['admin_password2'] ) ? gc_unslash( $_POST['admin_password2'] ) : '';
		$admin_email          = isset( $_POST['admin_email'] ) ? trim( gc_unslash( $_POST['admin_email'] ) ) : '';
		$public               = isset( $_POST['blog_public'] ) ? (int) $_POST['blog_public'] : 1;

		// Check email address.
		$error = false;
		if ( empty( $user_name ) ) {
			// TODO: Poka-yoke.
			display_setup_form( __( '请提供有效的用户名。' ) );
			$error = true;
		} elseif ( sanitize_user( $user_name, true ) !== $user_name ) {
			display_setup_form( __( '您提供的用户名包含非法字符。' ) );
			$error = true;
		} elseif ( $admin_password !== $admin_password_check ) {
			// TODO: Poka-yoke.
			display_setup_form( __( '您两次输入的密码不符，请重试。' ) );
			$error = true;
		} elseif ( empty( $admin_email ) ) {
			// TODO: Poka-yoke.
			display_setup_form( __( '您必须提供电子邮箱。' ) );
			$error = true;
		} elseif ( ! is_email( $admin_email ) ) {
			// TODO: Poka-yoke.
			display_setup_form( __( '抱歉，电子邮箱无效。形如<code>username@example.com</code>的才是电子邮箱。' ) );
			$error = true;
		}

		if ( false === $error ) {
			$gcdb->show_errors();
			$result = gc_install( $weblog_title, $user_name, $admin_email, $public, '', gc_slash( $admin_password ), $loaded_language );
			?>

<h1><?php _e( '成功！' ); ?></h1>

<p><?php _e( 'GeChiUI安装完成。谢谢！' ); ?></p>

<table class="form-table install-success">
	<tr>
		<th><?php _e( '用户名' ); ?></th>
		<td><?php echo esc_html( sanitize_user( $user_name, true ) ); ?></td>
	</tr>
	<tr>
		<th><?php _e( '密码' ); ?></th>
		<td>
			<?php if ( ! empty( $result['password'] ) && empty( $admin_password_check ) ) : ?>
				<code><?php echo esc_html( $result['password'] ); ?></code><br />
			<?php endif; ?>
			<p><?php echo $result['password_message']; ?></p>
		</td>
	</tr>
</table>

<p class="step"><a href="<?php echo esc_url( gc_login_url() ); ?>" class="btn btn-primary"><?php _e( '登录' ); ?></a></p>

			<?php
		}
		break;
}

if ( ! gc_is_mobile() ) {
	?>
<script type="text/javascript">var t = document.getElementById('weblog_title'); if (t){ t.focus(); }</script>
	<?php
}

gc_print_scripts( $scripts_to_print );
?>
<script type="text/javascript">
jQuery( function( $ ) {
	$( '.hide-if-no-js' ).removeClass( 'hide-if-no-js' );
} );
</script>
</body>
</html>
