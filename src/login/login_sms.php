<?php
        $secure_cookie = '';
        $customize_login = isset( $_REQUEST['customize-login'] );

        if ( $customize_login ) {
			gc_enqueue_script( 'customize-base' );
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

        //登录验证的核心方法
        $user = gc_signon_mobile( array(), $secure_cookie );

        if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
            if ( headers_sent() ) {
                $errors =  new GC_Error(
                    'generic',
                    __('<strong>错误</strong>: Cookies因预料之外的输出被阻止。要获取帮助，请参见<a href=\"%1$s\">此文档</a>或访问<a href=\"%2$s\">支持论坛</a>。')
                );
                return;
            } elseif ( isset( $_POST[ 'testcookie' ] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
                // If cookies are disabled, we can't log in even with a valid user and password.
                $errors =  new GC_Error(
                    'generic',
                    __('<strong>错误</strong>: Cookies被阻止或者您的浏览器不支持。要使用GeChiUI，您必须<a href=\"%s\">启用cookies</a>。')
                );
                return;
            }
        }
        
        //登录成功，跳转
        $requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
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

        //当同时出现'empty_usermobile', 'empty_smscode'，说明还没登录
		if ( empty( $_POST ) && $errors->get_error_codes() === array( 'empty_usermobile', 'empty_smscode' ) ) {
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
                $message = sprintf( 'Please log in to %1$s to authorize %2$s to connect to your account.', get_bloginfo( 'name', 'display' ), '<strong>' . esc_html( $query['app_name'] ) . '</strong>' );
            } else {
                /* translators: %s: Website name. */
                $message = sprintf( 'Please log in to %s to proceed with authorization.', get_bloginfo( 'name', 'display' ) );
            }

            $errors->add( 'authorize_application', $message, 'message' );
        }


		/**
		 * Filters the login page errors.
		 *
		 * @param GC_Error $errors      GC Error object.
		 * @param string   $redirect_to Redirect destination URL.
		 */
		$errors = apply_filters( 'gc_login_errors', $errors, $redirect_to );

		// Clear any stale cookies.
		if ( $reauth ) {
			gc_clear_auth_cookie();
		}

        login_header( __( '短信验证码登录' ), '', $errors );

        $user_mobile = '';
        $sms_code = '';
        if ( isset( $_POST[ 'user_mobile' ] ) ) {
            $user_mobile = $_POST['user_mobile'];
        }
        if ( isset( $_POST[ 'sms_code' ] ) ) {
            $sms_code = $_POST['sms_code'];
        }
        gc_enqueue_script( 'pages-login-sms' );
?>
<script>admin_ajax_url = '<?php echo html_entity_decode(admin_url(gc_nonce_url('admin-ajax.php?action=smscode', 'smscode' ))) ; ?>'</script>
<div class="card shadow-lg">
    <form id="smsform" method="post">
        <div class="card-body">
            <div class="align-items-center justify-content-between m-b-15 text-center">
                <h2 class="m-b-0"><?php _e( '短信验证码登录' ); ?></h2>
            </div>
            <div class="form-group">
                <p><?php echo sprintf( __('通过你的短信验证码登录, 或 <a href="%s">账号密码</a>'), esc_url( '/gc-login.php') )?></p>
            </div>
            <div class="form-group">
                <label for="user_mobile"><?php _e( '手机号' ); ?></label>
                <div class="input-affix"> <i class="prefix-icon anticon anticon-mobile"></i>
                    <input type="text" id="user_mobile" class="form-control" name="user_mobile" placeholder="<?php _e( '请输入您的手机号' ); ?>" value="<?php echo esc_attr( $user_mobile ); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="sms_code"><?php _e( '短信验证码' ); ?></label>
                <div class="input-affix m-b-10">
                    <input type="text" id="sms_code" class="form-control" name="sms_code" placeholder="<?php _e( '请输入短信验证码' ); ?>" value="<?php echo esc_attr( $sms_code ); ?>">
                    <button  id="verify" class="btn btn-primary btn-sm suffix-icon" style="right: 3px; height: 34px" disabled><?php _e( '获取短信验证码' ); ?></button>
                </div>
            </div>
            <?php

            // Fires following the 'Password' field in the login form.
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
            <?php

            if ( $interim_login ) {
                ?>
            <input type="hidden" name="interim-login" value="1" />
            <?php
            } else {
                ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
            <?php
            }

            ?>
            <input type="hidden" name="testcookie" value="1" />
        </div>
    </form>
</div>
<?php
