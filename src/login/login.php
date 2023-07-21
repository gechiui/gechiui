<?php
        $secure_cookie   = '';
		$customize_login = isset( $_REQUEST['customize-login'] );

		if ( $customize_login ) {
			gc_enqueue_script( 'customize-base' );
		}

		// If the user wants SSL but the session is not SSL, force a secure cookie.
		if ( ! empty( $_POST['log'] ) && ! force_ssl_admin() ) {
			$user_name = sanitize_user( gc_unslash( $_POST['log'] ) );
			$user      = get_user_by( 'login', $user_name );
            
            //通过邮箱去用户信息
			if ( ! $user && strpos( $user_name, '@' ) ) {
				$user = get_user_by( 'email', $user_name );
			}
            
            //通过手机号取用户信息
            if ( ! $user && validate_usermobile( $user_name ) ) {
				$user = get_user_by( 'mobile', $user_name );
			}

			if ( $user ) {
				if ( get_user_option( 'use_ssl', $user->ID ) ) {
					$secure_cookie = true;
					force_ssl_admin( true );
				}
			}
		}

		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $_REQUEST['redirect_to'];
			// Redirect to HTTPS if user wants SSL.
			if ( $secure_cookie && false !== strpos( $redirect_to, 'gc-admin' ) ) {
				$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
			}
		} else {
			$redirect_to = admin_url();
		}

		$reauth = empty( $_REQUEST['reauth'] ) ? false : true;

		$user = gc_signon( array(), $secure_cookie );

		if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			if ( headers_sent() ) {
				$user = new GC_Error(
					'test_cookie',
					sprintf(
						/* translators: 1: Browser cookie documentation URL, 2: Support forums URL. */
						__( '<strong>错误</strong>：Cookies因预料之外的输出被阻止。要获取帮助，请参见<a href="%1$s">此文档</a>或访问<a href="%2$s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/cookies/' ),
						__( 'https://www.gechiui.com/support/forums/' )
					)
				);
			} elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
				// If cookies are disabled, we can't log in even with a valid user and password.
				$user = new GC_Error(
					'test_cookie',
					sprintf(
						/* translators: %s: Browser cookie documentation URL. */
						__( '<strong>错误</strong>：Cookies被阻止或者您的浏览器不支持。要使用GeChiUI，您必须<a href="%s">启用cookies</a>。' ),
						__( 'https://www.gechiui.com/support/cookies/#enable-cookies-in-your-browser' )
					)
				);
			}
		}

		$requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
		/**
		 * Filters the login redirect URL.
		 *
		 *
		 * @param string           $redirect_to           The redirect destination URL.
		 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
		 * @param GC_User|GC_Error $user                  GC_User object if login was successful, GC_Error object otherwise.
		 */
		$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

		if ( ! is_gc_error( $user ) && ! $reauth ) {
			// Check if it is time to add a redirect to the admin email confirmation screen.
			if ( is_a( $user, 'GC_User' ) && $user->exists() && $user->has_cap( 'manage_options' ) ) {
				$admin_email_lifespan = (int) get_option( 'admin_email_lifespan' );

				// If `0` (or anything "falsey" as it is cast to int) is returned, the user will not be redirected
				// to the admin email confirmation screen.
				/** This filter is documented in gc-login.php */
				$admin_email_check_interval = (int) apply_filters( 'admin_email_check_interval', 6 * MONTH_IN_SECONDS );

				if ( $admin_email_check_interval > 0 && time() > $admin_email_lifespan ) {
					$redirect_to = add_query_arg(
						array(
							'action'  => 'confirm_admin_email',
							'gc_lang' => get_user_locale( $user ),
						),
						gc_login_url( $redirect_to )
					);
				}
			}

			if ( ( empty( $redirect_to ) || 'gc-admin/' === $redirect_to || admin_url() === $redirect_to ) ) {
				// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
				if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
					$redirect_to = user_admin_url();
				} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
					$redirect_to = get_dashboard_url( $user->ID );
				} elseif ( ! $user->has_cap( 'edit_posts' ) ) {
					$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
				}

				gc_redirect( $redirect_to );
				exit;
			}

			gc_safe_redirect( $redirect_to );
			exit;
		}

		$errors = $user;
		// Clear errors if loggedout is set.
		if ( ! empty( $_GET['loggedout'] ) || $reauth ) {
			$errors = new GC_Error();
		}

		if ( empty( $_POST ) && $errors->get_error_codes() === array( 'empty_username', 'empty_password' ) ) {
			$errors = new GC_Error( '', '' );
		}

        // Some parts of this script use the main login form to display a message.
        if ( isset( $_GET['loggedout'] ) && $_GET['loggedout'] ) {
            $errors->add( 'loggedout', __( '您已注销。' ), 'message' );
        } elseif ( isset( $_GET['registration'] ) && 'disabled' === $_GET['registration'] ) {
            $errors->add( 'registerdisabled', __( '<strong>错误</strong>：目前不允许用户注册。' ) );
        } elseif ( strpos( $redirect_to, 'about.php?updated' ) ) {
            $errors->add( 'updated', __( '<strong>GeChiUI升级成功！</strong>请重新登录以查看更新详情。' ), 'message' );
        } elseif ( GC_Recovery_Mode_Link_Service::LOGIN_ACTION_ENTERED === $action ) {
            $errors->add( 'enter_recovery_mode', __( '恢复模式已初始化，请登录以继续。' ), 'message' );
        } elseif ( isset( $_GET['redirect_to'] ) && false !== strpos( $_GET['redirect_to'], 'gc-admin/authorize-application.php' ) ) {
            $query_component = gc_parse_url( $_GET['redirect_to'], PHP_URL_QUERY );
            parse_str( $query_component, $query );

            if ( ! empty( $query['app_name'] ) ) {
                /* translators: 1: Website name, 2: Application name. */
                $message = sprintf( '登录到%1$s以授权%2$s连接到您的帐户。', get_bloginfo( 'name', 'display' ), '<strong>' . esc_html( $query['app_name'] ) . '</strong>' );
            } else {
                /* translators: %s: Website name. */
                $message = sprintf( '请登录%s以继续授权。', get_bloginfo( 'name', 'display' ) );
            }

            $errors->add( 'authorize_application', $message, 'message' );
        }


		/**
		 * Filters the login page errors.
		 *
		 *
		 * @param GC_Error $errors      GC Error object.
		 * @param string   $redirect_to Redirect destination URL.
		 */
		$errors = apply_filters( 'gc_login_errors', $errors, $redirect_to );

		// Clear any stale cookies.
		if ( $reauth ) {
			gc_clear_auth_cookie();
		}

		login_header( __( '登录' ), '', $errors );

		if ( isset( $_POST['log'] ) ) {
			$user_login = ( 'incorrect_password' === $errors->get_error_code() || 'empty_password' === $errors->get_error_code() ) ? esc_attr( gc_unslash( $_POST['log'] ) ) : '';
		}

		$rememberme = ! empty( $_POST['rememberme'] );

		if ( $errors->has_errors() ) {
			$aria_describedby_error = ' aria-describedby="login_error"';
		} else {
			$aria_describedby_error = '';
		}

		gc_enqueue_script( 'user-profile' );
		?>
    <div class="card shadow-lg">
		<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'gc-login.php', 'login_post' ) ); ?>" method="post">
            <div class="card-body">
                <div class="align-items-center justify-content-between m-b-15 text-center">
                    <h2 class="m-b-0"><?php _e( '登录' ); ?></h2>
                </div>
                <div class="form-group">
                    <?php if( has_filter('gc_sms') ) :?>
                    <p><?php echo sprintf( __('通过你的账号密码登录, 或 <a href="%s">短信验证码</a>'), esc_url( '/gc-login.php?action=sms') )?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="user_login">  <?php _e( '用户名' ); ?>/<?php _e( '电子邮箱' ); ?><?php echo has_filter('gc_sms') ? '/'. __( '手机号') : '' ?> </label>
                    <div class="input-affix"> 
                        <i class="prefix-icon anticon anticon-user"></i>
                        <input type="text" class="form-control" name="log" id="user_login" <?php echo $aria_describedby_error; ?> value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" placeholder=" <?php _e( '用户名' ); ?>/<?php _e( '电子邮箱' ); ?><?php echo has_filter('gc_sms') ? '/'. __( '手机号') : '' ?>"/>
                    </div>
                </div>
                <div class="form-group user-pass-wrap">
                    <label for="user_pass">
                        <?php _e( '密码' ); ?>
                    </label>
                    <a class="float-right font-size-13 text-muted" href="<?php echo esc_url( gc_lostpassword_url() ); ?>"><?php _e( '忘记密码？' ); ?></a>
                    <div class="input-affix m-b-10">
                        <input type="password" class="form-control" name="pwd" id="user_pass" size="20" <?php echo $aria_describedby_error; ?> placeholder="<?php _e( '请输入您的密码' ); ?>" value="" aria-label="<?php esc_attr_e( '显示密码' ); ?>"/>
                        <i class="suffix-icon anticon anticon-eye gc-hide-pw pointer"></i>
                    </div>
                </div>
                <?php

                /**
                 * Fires following the 'Password' field in the login form.
                 *
               
                 */
                do_action( 'login_form' );

                ?>

                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between"> 
                        <div>
                            <p>
                                <div class="checkbox">
                                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked( $rememberme ); ?> /> 
                                    <label for="rememberme"><?php esc_html_e( '记住我' ); ?></label>
                                </div>
                            </p>
                            <?php
                            if (  get_option( 'users_can_register' ) ) {
                                $registration_url = sprintf( '<p>%s<a href="%s">%s</a></p>', __( '没有账号？'), esc_url( gc_registration_url() ), __( '立即注册') );

                                /** This filter is documented in gc-includes/general-template.php */
                                echo apply_filters( 'register', $registration_url );
                            }
                            ?>
                        </div>
                        <button name="gc-submit" id="gc-submit"  class="btn btn-primary"><?php esc_attr_e( '登录' ); ?></button>
                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                        <?php
                        if ( $customize_login ) {
                            ?>
                            <input type="hidden" name="customize-login" value="1" />
                            <?php
                        }

                        ?>
                        <input type="hidden" name="testcookie" value="1" />
                    </div>
                </div>
            </div>
		</form>
    </div>

		<?php
		$login_script  = 'function gc_attempt_focus() {';
		$login_script .= 'setTimeout( function() {';
		$login_script .= 'try {';

		if ( $user_login ) {
			$login_script .= 'd = document.getElementById( "user_pass" ); d.value = "";';
		} else {
			$login_script .= 'd = document.getElementById( "user_login" );';

			if ( $errors->get_error_code() === 'invalid_username' ) {
				$login_script .= 'd.value = "";';
			}
		}

		$login_script .= 'd.focus(); d.select();';
		$login_script .= '} catch( er ) {}';
		$login_script .= '}, 200);';
		$login_script .= "}\n"; // End of gc_attempt_focus().

		/**
		 * Filters whether to print the call to `gc_attempt_focus()` on the login screen.
		 *
		 *
		 * @param bool $print Whether to print the function call. Default true.
		 */
		if ( apply_filters( 'enable_login_autofocus', true ) && ! $error ) {
			$login_script .= "gc_attempt_focus();\n";
		}

		// Run `gcOnload()` if defined.
		$login_script .= "if ( typeof gcOnload === 'function' ) { gcOnload() }";

		?>
		<script type="text/javascript">
			<?php echo $login_script; ?>
		</script>
		<?php
