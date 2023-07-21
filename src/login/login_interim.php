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

			if ( ! $user && strpos( $user_name, '@' ) ) {
				$user = get_user_by( 'email', $user_name );
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

            $message       = '<p class="message">' . __( '登录成功。' ) . '</p>';
            $interim_login = 'success';
            login_header( '', $message );

            ?>
            </div>
            <?php

            /** This action is documented in gc-login.php */
            do_action( 'login_footer' );

            if ( $customize_login ) {
                ?>
                <script type="text/javascript">setTimeout( function(){ new gc.customize.Messenger({ url: '<?php echo gc_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
                <?php
            }

            ?>
            </body></html>
            <?php

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

		if ( ! $errors->has_errors() ) {
            $errors->add( 'expired', __( '您的会话已过期，请登录来继续您的工作。' ), 'message' );
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
                <div class="form-group">
                    <label for="user_login"> <?php _e( '用户名' ); ?>/<?php _e( '电子邮箱' ); ?><?php echo has_filter('gc_sms') ? '/'. __( '手机号') : '' ?> </label>
                    <div class="input-affix"> 
                        <i class="prefix-icon anticon anticon-user"></i>
                        <input type="text" class="form-control" name="log" id="user_login" <?php echo $aria_describedby_error; ?> value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" placeholder="<?php _e( '用户名' ); ?>/<?php _e( '电子邮箱' ); ?><?php echo has_filter('gc_sms') ? '/'. __( '手机号') : '' ?>"/>
                    </div>
                </div>
                <div class="form-group user-pass-wrap">
                    <label for="user_pass">
                        <?php _e( '密码' ); ?>
                    </label>
                    <a class="float-right font-size-13 text-muted" href="<?php echo esc_url( gc_lostpassword_url() ); ?>"><?php _e( '忘记密码？' ); ?></a>
                    <div class="input-affix m-b-10">
                        <input type="password" class="form-control" name="pwd" id="user_pass" size="20" <?php echo $aria_describedby_error; ?> placeholder="<?php esc_attr_e( '请输入密码。' ); ?>" value="" aria-label="<?php esc_attr_e( '显示密码' ); ?>"/>
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
                            <div class="forgetmenot">
                                <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked( $rememberme ); ?> /> 
                                <label for="rememberme"><?php esc_html_e( '记住我' ); ?></label>
                            </div>
                        </div>
                        <button name="gc-submit" id="gc-submit"  class="btn btn-primary"><?php esc_attr_e( '登录' ); ?></button>
                        <input type="hidden" name="interim-login" value="1" />
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

        <script type="text/javascript">
			( function() {
				try {
					var i, links = document.getElementsByTagName( 'a' );
					for ( i in links ) {
						if ( links[i].href ) {
							links[i].target = '_blank';
							links[i].rel = 'noopener';
						}
					}
				} catch( er ) {}
			}());
        </script>
			<?php
