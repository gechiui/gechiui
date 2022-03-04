<?php
/**
 * Confirms that the activation key that is sent in an email after a user signs
 * up for a new site matches the key for that user and then displays confirmation.
 *
 * @package GeChiUI
 */

define( 'GC_INSTALLING', true );

/** Sets up the GeChiUI Environment. */
require __DIR__ . '/gc-load.php';

require __DIR__ . '/gc-blog-header.php';

if ( ! is_multisite() ) {
	gc_redirect( gc_registration_url() );
	die();
}

$valid_error_codes = array( 'already_active', 'blog_taken' );

list( $activate_path ) = explode( '?', gc_unslash( $_SERVER['REQUEST_URI'] ) );
$activate_cookie       = 'gc-activate-' . COOKIEHASH;

$key    = '';
$result = null;

if ( isset( $_GET['key'] ) && isset( $_POST['key'] ) && $_GET['key'] !== $_POST['key'] ) {
	gc_die( __( '检测到不匹配的键值。请按照您收到的激活邮件中提供的链接进行操作。' ), __( '激活过程中发生了错误' ), 400 );
} elseif ( ! empty( $_GET['key'] ) ) {
	$key = $_GET['key'];
} elseif ( ! empty( $_POST['key'] ) ) {
	$key = $_POST['key'];
}

if ( $key ) {
	$redirect_url = remove_query_arg( 'key' );

	if ( remove_query_arg( false ) !== $redirect_url ) {
		setcookie( $activate_cookie, $key, 0, $activate_path, COOKIE_DOMAIN, is_ssl(), true );
		gc_safe_redirect( $redirect_url );
		exit;
	} else {
		$result = gcmu_activate_signup( $key );
	}
}

if ( null === $result && isset( $_COOKIE[ $activate_cookie ] ) ) {
	$key    = $_COOKIE[ $activate_cookie ];
	$result = gcmu_activate_signup( $key );
	setcookie( $activate_cookie, ' ', time() - YEAR_IN_SECONDS, $activate_path, COOKIE_DOMAIN, is_ssl(), true );
}

if ( null === $result || ( is_gc_error( $result ) && 'invalid_key' === $result->get_error_code() ) ) {
	status_header( 404 );
} elseif ( is_gc_error( $result ) ) {
	$error_code = $result->get_error_code();

	if ( ! in_array( $error_code, $valid_error_codes, true ) ) {
		status_header( 400 );
	}
}

nocache_headers();

if ( is_object( $gc_object_cache ) ) {
	$gc_object_cache->cache_enabled = false;
}

// Fix for page title.
$gc_query->is_404 = false;

/**
 * Fires before the Site Activation page is loaded.
 *
 *
 */
do_action( 'activate_header' );

/**
 * Adds an action hook specific to this page.
 *
 * Fires on {@see 'gc_head'}.
 *
 * @since MU
 */
function do_activate_header() {
	/**
	 * Fires before the Site Activation page is loaded.
	 *
	 * Fires on the {@see 'gc_head'} action.
	 *
	 */
	do_action( 'activate_gc_head' );
}
add_action( 'gc_head', 'do_activate_header' );

/**
 * Loads styles specific to this page.
 *
 * @since MU
 */
function gcmu_activate_stylesheet() {
	?>
	<style type="text/css">
		form { margin-top: 2em; }
		#submit, #key { width: 90%; font-size: 24px; }
		#language { margin-top: .5em; }
		.error { background: #f66; }
		span.h3 { padding: 0 8px; font-size: 1.3em; font-weight: 600; }
	</style>
	<?php
}
add_action( 'gc_head', 'gcmu_activate_stylesheet' );
add_action( 'gc_head', 'gc_strict_cross_origin_referrer' );
add_filter( 'gc_robots', 'gc_robots_sensitive_page' );

get_header( 'gc-activate' );

$blog_details = get_blog_details();
?>

<div id="signup-content" class="widecolumn">
	<div class="gc-activate-container">
	<?php if ( ! $key ) { ?>

		<h2><?php _e( '需要激活密钥' ); ?></h2>
		<form name="activateform" id="activateform" method="post" action="<?php echo network_site_url( $blog_details->path . 'gc-activate.php' ); ?>">
			<p>
				<label for="key"><?php _e( '激活密钥：' ); ?></label>
				<br /><input type="text" name="key" id="key" value="" size="50" />
			</p>
			<p class="submit">
				<input id="submit" type="submit" name="Submit" class="submit" value="<?php esc_attr_e( '启用' ); ?>" />
			</p>
		</form>

		<?php
	} else {
		if ( is_gc_error( $result ) && in_array( $result->get_error_code(), $valid_error_codes, true ) ) {
			$signup = $result->get_error_data();
			?>
			<h2><?php _e( '您的账户现已激活！' ); ?></h2>
			<?php
			echo '<p class="lead-in">';
			if ( '' === $signup->domain . $signup->path ) {
				printf(
					/* translators: 1: Login URL, 2: Username, 3: User email address, 4: Lost password URL. */
					__( '您在%1$s的站点已激活。请使用您选择的用户名%2$s和%3$s邮箱中收到的密码来登录您的站点。如果您找不到我们发送的邮件，请检查“垃圾邮件”文件夹。如果您一小时之后仍不能收到邮件，请考虑<a href=\"%4$s\">重置您的密码</a>。' ),
					network_site_url( $blog_details->path . 'gc-login.php', 'login' ),
					$signup->user_login,
					$signup->user_email,
					gc_lostpassword_url()
				);
			} else {
				printf(
					/* translators: 1: Site URL, 2: Username, 3: User email address, 4: Lost password URL. */
					__( '您的账户已激活。请使用您选择的用户名（%2$s）和%3$s邮箱中收到的密码来<a href=\"%1$s\">登录</a>您的站点。如果您找不到我们发送的邮件，请检查“垃圾邮件”文件夹。如果您一小时之后仍不能收到邮件，请考虑<a href=\"%4$s\">重置您的密码</a>。' ),
					sprintf( '<a href="http://%1$s%2$s">%1$s%2$s</a>', $signup->domain, $blog_details->path ),
					$signup->user_login,
					$signup->user_email,
					gc_lostpassword_url()
				);
			}
			echo '</p>';
		} elseif ( null === $result || is_gc_error( $result ) ) {
			?>
			<h2><?php _e( '激活过程中发生了错误' ); ?></h2>
			<?php if ( is_gc_error( $result ) ) : ?>
				<p><?php echo $result->get_error_message(); ?></p>
			<?php endif; ?>
			<?php
		} else {
			$url  = isset( $result['blog_id'] ) ? get_home_url( (int) $result['blog_id'] ) : '';
			$user = get_userdata( (int) $result['user_id'] );
			?>
			<h2><?php _e( '您的帐户现在处于活动状态！' ); ?></h2>

			<div id="signup-welcome">
			<p><span class="h3"><?php _e( '用户名：' ); ?></span> <?php echo $user->user_login; ?></p>
			<p><span class="h3"><?php _e( '密码：' ); ?></span> <?php echo $result['password']; ?></p>
			</div>

			<?php
			if ( $url && network_home_url( '', 'http' ) !== $url ) :
				switch_to_blog( (int) $result['blog_id'] );
				$login_url = gc_login_url();
				restore_current_blog();
				?>
				<p class="view">
				<?php
					/* translators: 1: Site URL, 2: Login URL. */
					printf( __( '您的账户已激活。请<a href=\"%1$s\">浏览您的站点</a>或<a href=\"%2$s\">登录</a>' ), $url, esc_url( $login_url ) );
				?>
				</p>
			<?php else : ?>
				<p class="view">
				<?php
					printf(
						/* translators: 1: Login URL, 2: Network home URL. */
						__( 'Y您的账户已激活。您可<a href=\"%1$s\">登录</a>或<a href=\"%2$s\">回首页</a>。' ),
						network_site_url( $blog_details->path . 'gc-login.php', 'login' ),
						network_home_url( $blog_details->path )
					);
				?>
				</p>
				<?php
				endif;
		}
	}
	?>
	</div>
</div>
<script type="text/javascript">
	var key_input = document.getElementById('key');
	key_input && key_input.focus();
</script>
<?php
get_footer( 'gc-activate' );
