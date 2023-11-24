<?php
/**
 * GeChiUI User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package GeChiUI
 */

/** Make sure that the GeChiUI bootstrap has run before continuing. */
require __DIR__ . '/gc-load.php';

// Redirect to HTTPS login if forced to use SSL.
if ( force_ssl_admin() && ! is_ssl() ) {
	if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
		gc_safe_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
		exit;
	} else {
		gc_safe_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		exit;
	}
}

/**
 * Output the login page header.
 *
 *
 * @global string      $error         Login error message set by deprecated pluggable gc_login() function
 *                                    or plugins replacing it.
 * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
 *                                    upon successful login.
 * @global string      $action        The action that brought the visitor to the login page.
 *
 * @param string   $title    Optional. GeChiUI login Page title to display in the `<title>` element.
 *                           Default 'Log In'.
 * @param string   $message  Optional. Message to display in header. Default empty.
 * @param GC_Error $gc_error Optional. The error to pass. Default is a GC_Error instance.
 */
function login_header( $title = '登录', $message = '', $gc_error = null ) {
	global $error, $interim_login, $action;

	// Don't index any of these forms.
	add_filter( 'gc_robots', 'gc_robots_sensitive_page' );
	add_action( 'login_head', 'gc_strict_cross_origin_referrer' );

	add_action( 'login_head', 'gc_login_viewport_meta' );

	if ( ! is_gc_error( $gc_error ) ) {
		$gc_error = new GC_Error();
	}

	// Shake it!
	$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'empty_usermobile', 'invalid_usermobile', 'empty_smscode', 'invalid_smscode', 'incorrect_password', 'retrieve_password_email_failure' );
	/**
	 * Filters the error codes array for shaking the login form.
	 *
	 *
	 * @param string[] $shake_error_codes Error codes that shake the login form.
	 */
	$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

	if ( $shake_error_codes && $gc_error->has_errors() && in_array( $gc_error->get_error_code(), $shake_error_codes, true ) ) {
		add_action( 'login_footer', 'gc_shake_js', 12 );
	}

	$login_title = get_bloginfo( 'name', 'display' );

	/* translators: Login screen title. 1: Login screen name, 2: Network or site name. */
	$login_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; GeChiUI' ), $title, $login_title );

	if ( gc_is_recovery_mode() ) {
		/* translators: %s: Login screen title. */
		$login_title = sprintf( __( '恢复模式 &#8212; %s' ), $login_title );
	}

	/**
	 * Filters the title tag content for login page.
	 *
	 *
	 * @param string $login_title The page title, with extra context added.
	 * @param string $title       The original page title.
	 */
	$login_title = apply_filters( 'login_title', $login_title, $title );

	?><!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo $login_title; ?></title>
	<?php

	gc_enqueue_style( 'app' );

	/*
	 * Remove all stored post data on logging out.
	 * This could be added by add_action('login_head'...) like gc_shake_js(),
	 * but maybe better if it's not removable by plugins.
	 */
	if ( 'loggedout' === $gc_error->get_error_code() ) {
		?>
		<script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("gc-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
		<?php
	}

	/**
	 * Enqueue scripts and styles for the login page.
	 *
	 */
	do_action( 'login_enqueue_scripts' );

	/**
	 * Fires in the login page header after scripts are enqueued.
	 *
	 */
	do_action( 'login_head' );

	$classes = array( 'login-action-' . $action, 'gc-core-ui' );

	if ( is_rtl() ) {
		$classes[] = 'rtl';
	}

	if ( $interim_login ) {
		$classes[] = 'interim-login';

		?>
		<style type="text/css">html{background-color: transparent;}</style>
		<?php

		if ( 'success' === $interim_login ) {
			$classes[] = 'interim-login-success';
		}
	}

	$classes[] = ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

	/**
	 * Filters the login page body classes.
	 *
	 *
	 * @param string[] $classes An array of body classes.
	 * @param string   $action  The action that brought the visitor to the login page.
	 */
	$classes = apply_filters( 'login_body_class', $classes, $action );

	?>
	</head>
	<body class="login no-js <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<script type="text/javascript">
		document.body.className = document.body.className.replace('no-js','js');
	</script>
	<?php
	/**
	 * Fires in the login page header after the body tag is opened.
	 *
	 */
	do_action( 'login_header' );

	?>
	<div class="app">
        <div class="container-fluid p-h-0 bg full-height d-flex">
            <div class="d-flex flex-column justify-content-between w-100">
                <div class="container d-flex h-100">
                    <div class="row align-items-center w-100">
                        <div class="col-md-7 col-lg-6 m-h-auto">
		                    <div class="text-center m-b-20">
                            <?php
                                //系统LOGO，默认GeChiUI的LOGO
                                if ( has_custom_logo() ) {
                                    the_custom_logo();
                                }else{
                                	echo '<img class="img" src="/assets/images/logo/logo.png">';
                                }
                            ?>
                            </div>
	<?php
	/**
	 * Filters the message to display above the login form.
	 *
	 *
	 * @param string $message Login message text.
	 */
	$message = apply_filters( 'login_message', $message );

	if ( ! empty( $message ) ) {
		echo $message . "\n";
	}

	// In case a plugin uses $error rather than the $gc_errors object.
	if ( ! empty( $error ) ) {
		$gc_error->add( 'error', $error );
		unset( $error );
	}

	if ( $gc_error->has_errors() ) {
		$errors   = '';
		$messages = '';

		foreach ( $gc_error->get_error_codes() as $code ) {
			$severity = $gc_error->get_error_data( $code );
			foreach ( $gc_error->get_error_messages( $code ) as $error_message ) {
				if ( 'message' === $severity ) {
					$messages .= '	' . $error_message . "<br />\n";
				} else {
					$errors .= '	' . $error_message . "<br />\n";
				}
			}
		}

		if ( ! empty( $errors ) ) {
			/**
			 * Filters the error messages displayed above the login form.
			 *
		
			 *
			 * @param string $errors Login error message.
			 */
			echo '<div id="login_error" class="alert alert-danger">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
		}

		if ( ! empty( $messages ) ) {
			/**
			 * Filters instructional messages displayed above the login form.
			 *
		
			 *
			 * @param string $messages Login messages.
			 */
			echo '<p class="message">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
		}
	}
} // End of login_header().

/**
 * Outputs the footer for the login page.
 *
 *
 * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
 *                                    upon successful login.
 *
 * @param string $input_id Which input to auto-focus.
 */
function login_footer( $input_id = '' ) {
	global $interim_login;

	// Don't allow interim logins to navigate away from the page.
	if ( ! $interim_login ) {
		?>
		<p id="backtoblog">
			<?php
			$html_link = sprintf(
				'<a href="%s">%s</a>',
				esc_url( home_url( '/' ) ),
				sprintf(
					/* translators: %s: Site title. */
					_x( '&larr; 返回到%s', 'site' ),
					get_bloginfo( 'title', 'display' )
				)
			);
			/**
			 * Filter the "Go to site" link displayed in the login page footer.
			 *
		
			 *
			 * @param string $link HTML link to the home URL of the current site.
			 */
			echo apply_filters( 'login_site_html_link', $html_link );
			?>
		</p>
		<?php

		the_privacy_policy_link( '<div class="privacy-policy-page-link">', '</div>' );
	}

	?>
	                    </div>
                </div>
            </div>
            <!-- Footer START -->
            <!-- Footer END --> 
        </div>
    </div>
</div>

	<?php
	if (
		! $interim_login &&
		/**
		 * Filters the Languages select input activation on the login screen.
		 *
		 * @param bool Whether to display the Languages select input on the login screen.
		 */
		apply_filters( 'login_display_language_dropdown', true )
	) {
		$languages = get_available_languages();

		if ( ! empty( $languages ) ) {
			?>
			<div class="language-switcher">
				<form id="language-switcher" action="" method="get">

					<label for="language-switcher-locales">
						<span class="dashicons dashicons-translation" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php _e( '语言' ); ?></span>
					</label>

					<?php
					$args = array(
						'id'                          => 'language-switcher-locales',
						'name'                        => 'gc_lang',
						'selected'                    => determine_locale(),
						'show_available_translations' => false,
						'explicit_option_zh_cn'       => true,
						'languages'                   => $languages,
					);

					/**
					 * Filters default arguments for the Languages select input on the login screen.
					 *
				
					 *
					 * @param array $args Arguments for the Languages select input on the login screen.
					 */
					gc_dropdown_languages( apply_filters( 'login_language_dropdown_args', $args ) );
					?>

					<?php if ( $interim_login ) { ?>
						<input type="hidden" name="interim-login" value="1" />
					<?php } ?>

					<?php if ( isset( $_GET['redirect_to'] ) && '' !== $_GET['redirect_to'] ) { ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url_raw( $_GET['redirect_to'] ); ?>" />
					<?php } ?>

					<?php if ( isset( $_GET['action'] ) && '' !== $_GET['action'] ) { ?>
						<input type="hidden" name="action" value="<?php echo esc_attr( $_GET['action'] ); ?>" />
					<?php } ?>

						<input type="submit" class="btn btn-primary btn-tone btn-sm" value="<?php esc_attr_e( '更改' ); ?>">

					</form>
				</div>
		<?php } ?>
	<?php } ?>
	<?php

	if ( ! empty( $input_id ) ) {
		?>
		<script type="text/javascript">
		try{document.getElementById('<?php echo $input_id; ?>').focus();}catch(e){}
		if(typeof gcOnload==='function')gcOnload();
		</script>
		<?php
	}

	/**
	 * Fires in the login page footer.
	 *
	 */
	do_action( 'login_footer' );

	?>
	<div class="clear"></div>
	</body>
	</html>
	<?php
}

/**
 * Outputs the JavaScript to handle the form shaking on the login page.
 *
 */
function gc_shake_js() {
	?>
	<script type="text/javascript">
	document.querySelector('form').classList.add('shake');
	</script>
	<?php
}

/**
 * Outputs the viewport meta tag for the login page.
 *
 */
function gc_login_viewport_meta() {
	?>
	<meta name="viewport" content="width=device-width" />
	<?php
}

//
// Main.
//

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
$errors = new GC_Error();

if ( isset( $_GET['key'] ) ) {
	$action = 'resetpass';
}

if ( isset( $_GET['checkemail'] ) ) {
	$action = 'checkemail';
}

$default_actions = array(
	'confirm_admin_email',
	'postpass',
	'logout',
	'lostpassword',
	'retrievepassword',
	'resetpass',
	'rp',
	'register-sms',
	'register',
	'checkemail',
	'confirmaction',
	'sms',
	'login',
	GC_Recovery_Mode_Link_Service::LOGIN_ACTION_ENTERED,
);

// Validate action so as to default to the login screen.
if ( ! in_array( $action, $default_actions, true ) && false === has_filter( 'login_form_' . $action ) ) {
	$action = 'login';
}

nocache_headers();

header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );

if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set.
	if ( isset( $_SERVER['PATH_INFO'] ) && ( $_SERVER['PATH_INFO'] !== $_SERVER['PHP_SELF'] ) ) {
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );
	}

	$url = dirname( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );

	if ( get_option( 'siteurl' ) !== $url ) {
		update_option( 'siteurl', $url );
	}
}

// Set a cookie now to see if they are supported by the browser.
$secure = ( 'https' === parse_url( gc_login_url(), PHP_URL_SCHEME ) );
setcookie( TEST_COOKIE, 'GC Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure );

if ( SITECOOKIEPATH !== COOKIEPATH ) {
	setcookie( TEST_COOKIE, 'GC Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
}

if ( isset( $_GET['gc_lang'] ) ) {
	setcookie( 'gc_lang', sanitize_text_field( $_GET['gc_lang'] ), 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
}

/**
 * Fires when the login form is initialized.
 *
 */
do_action( 'login_init' );

/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form.
 *
 * Possible hook names include:
 *
 *  - `login_form_checkemail`
 *  - `login_form_confirm_admin_email`
 *  - `login_form_confirmaction`
 *  - `login_form_entered_recovery_mode`
 *  - `login_form_login`
 *  - `login_form_logout`
 *  - `login_form_lostpassword`
 *  - `login_form_postpass`
 *  - `login_form_register`
 *  - `login_form_resetpass`
 *  - `login_form_retrievepassword`
 *  - `login_form_rp`
 *
 */
do_action( "login_form_{$action}" );

$http_post     = ( 'POST' === $_SERVER['REQUEST_METHOD'] );
$interim_login = isset( $_REQUEST['interim-login'] );

/**
 * Filters the separator used between login form navigation links.
 *
 *
 * @param string $login_link_separator The separator used between login form navigation links.
 */
$login_link_separator = apply_filters( 'login_link_separator', ' | ' );

if(  isset( $_REQUEST['interim-login'] ) ) {
    $action = 'interim-login';
}

switch ( $action ) {

	case 'confirm_admin_email':
		require_once 'src/login/confirm_admin_email.php';
		login_footer();
		break;

	case 'postpass':
		require_once 'src/login/post_password.php';
		exit;

	case 'logout':
		// 退出登录
		require_once 'src/login/logout.php';
		exit;

	case 'lostpassword':
	case 'retrievepassword':
		//忘记密码
       require_once 'src/login/lostpassword.php';
		login_footer( 'user_login' );
		break;

	case 'resetpass':
	case 'rp':
		//重置密码
        require_once 'src/login/resetpass.php';
		login_footer( 'user_pass' );
		break;

	case 'register-sms':
        //短信注册
         require_once 'src/login/register_sms.php';
		login_footer( 'user_login' );
		break;
        
    case 'register':
        //邮箱注册
         require_once 'src/login/register.php';
		login_footer( 'user_login' );
		break;

	case 'checkemail':
        //发送邮件通知
		require_once 'src/login/checkemail.php';
		login_footer();
		break;

	case 'confirmaction':
        require_once 'src/login/confirmaction.php';
		login_footer();
		exit;

	case 'sms':
        //短信验证码登录
		require_once 'src/login/login_sms.php';
        login_footer();
		break;
        
    case 'interim-login':
        require_once 'src/login/login_interim.php';
        login_footer();
		break;
        
    case 'login':
	default:
        //账号登录
		require_once 'src/login/login.php';
        login_footer();
		break;
} // End action switch.
