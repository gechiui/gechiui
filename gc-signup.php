<?php

/** Sets up the GeChiUI Environment. */
require __DIR__ . '/gc-load.php';

add_filter( 'gc_robots', 'gc_robots_no_robots' );

require __DIR__ . '/gc-blog-header.php';

nocache_headers();

if ( is_array( get_site_option( 'illegal_names' ) ) && isset( $_GET['new'] ) && in_array( $_GET['new'], get_site_option( 'illegal_names' ), true ) ) {
	gc_redirect( network_home_url() );
	die();
}

/**
 * Prints signup_header via gc_head.
 *
 * @since MU
 */
function do_signup_header() {
	/**
	 * Fires within the head section of the site sign-up screen.
	 *
	 */
	do_action( 'signup_header' );
}

/**
 * Validates the new site sign-up.
 *
 * @since MU
 *
 * @return array Contains the new site data and error messages.
 *               See gcmu_validate_blog_signup() for details.
 */
function validate_blog_form() {
	$user = '';
	if ( is_user_logged_in() ) {
		$user = gc_get_current_user();
	}

	return gcmu_validate_blog_signup( $_POST['blogname'], $_POST['blog_title'], $user );
}

/**
 * Retrieves languages available during the site/user sign-up process.
 *
 *
 *
 * @see get_available_languages()
 *
 * @return string[] Array of available language codes. Language codes are formed by
 *                  stripping the .mo extension from the language file names.
 */
function signup_get_available_languages() {
	/**
	 * Filters the list of available languages for front-end site sign-ups.
	 *
	 * Passing an empty array to this hook will disable output of the setting on the
	 * sign-up form, and the default language will be used when creating the site.
	 *
	 * Languages not already installed will be stripped.
	 *
	 *
	 * @param string[] $languages Array of available language codes. Language codes are formed by
	 *                            stripping the .mo extension from the language file names.
	 */
	$languages = (array) apply_filters( 'signup_get_available_languages', get_available_languages() );

	/*
	 * Strip any non-installed languages and return.
	 *
	 * Re-call get_available_languages() here in case a language pack was installed
	 * in a callback hooked to the 'signup_get_available_languages' filter before this point.
	 */
	return array_intersect_assoc( $languages, get_available_languages() );
}

add_action( 'gc_head', 'do_signup_header' );

if ( !is_multisite() ) {
    gc_redirect( gc_registration_url() );
    die();
}

// Fix for page title.
$gc_query->is_404 = false;

/**
 * Fires before the Site Signup page is loaded.
 *
 */
do_action( 'before_signup_header' );

//get_header( 'gc-signup' );
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'mobile';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php _e( '注册' ); ?></title>
<?php

gc_enqueue_style( 'app' );

/**
 * Enqueue scripts and styles for the login page.
 *
 */
do_action( 'login_enqueue_scripts' );

/**
 * Fires in the login page header after scripts are enqueued.
 */
do_action( 'login_head' );


?>
</head>

<body>
<div class="app">
    <div class="container-fluid p-h-0 p-v-20 bg full-height d-flex">
        <div class="d-flex flex-column justify-content-between w-100">
            <div class="container d-flex h-100">
                <div class="row align-items-center w-100">
                    <div class="col-md-7 col-lg-6 m-h-auto">
                        <div class="text-center m-b-20">
                            <a href="<?php echo esc_url( site_url() ); ?>">
                                <?php
                                    if ( function_exists( 'the_custom_logo' ) ) {
                                        the_custom_logo();
                                    }
                                ?>
                            </a>
                        </div>
                        <div class="card shadow-lg">
                            <?php
                            do_action( 'before_signup_form' );

                            // Main.
                            $active_signup = get_site_option( 'registration', 'none' );

                            /**
                             * Filters the type of site sign-up.
                             * @param string $active_signup String that returns registration type. The value can be
                             *                              'all', 'none', 'blog', or 'user'.
                             */
                            $active_signup = apply_filters( 'gcmu_active_signup', $active_signup );

                            $current_user = gc_get_current_user();
                            //if ( 'none' === $active_signup ) {
                            switch ( $active_signup ) {
                                case 'user'://该网络目前只允许用户注册。The network currently allows user registrations.
                                case 'none'://网络目前不允许注册站点和用户。
                                    //不可以注册站点
                                    echo '<h2 class="font-weight-light font-size-30 text-center m-t-30 m-b-30">'. __('该网络目前禁止创建新站点。') .'</h2>';
                                    break;
                                    
                                case 'blog': //该网络目前允许站点注册 , 不可以注册新用户。The network currently allows site registrations.
                                default:
                                    //该网络目前允许站点和用户注册。
                                    //The network currently allows both site and user registrations.
                                    if(is_user_logged_in()){
                                        require_once 'src/signup/signup_another_blog.php';
                                    }else{
                                        gc_redirect( apply_filters( 'gc_signup_location', network_site_url( 'gc-login.php' ) ) );
                                        exit;
                                    }
                                    break;
                            }
                            ?>
                            <?php
                            /**
                             * Fires after the sign-up forms, before gc_footer.
                             *
                             * @since 3.0.0
                             */
                            do_action( 'after_signup_form' );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer START -->
            <footer class="footer">
                <div class="footer-content">
                    <?php 
                    $footbanquan = get_option('footbanquan'); 
                    if($footbanquan ){
                        echo $footbanquan;
                    }
                    //显示隐私政策
                    the_privacy_policy_link( '<span>', '</span>' );
                    ?>
                    
                </div>
            </footer>
            <!-- Footer END --> 
        </div>
    </div>
</div>
<?php do_action( 'signup_footer' ); ?>
</body>
</html>
