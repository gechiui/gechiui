<?php
        if ( is_multisite() ) {
			if ( ! is_main_network() || ! is_main_site() ) {
                gc_redirect( apply_filters( 'gc_signup_location', network_site_url( 'gc-login.php?action=register' ) ) );
                exit;
            }
		}

		if ( ! get_option( 'users_can_register' ) ) {
			gc_redirect( site_url( 'gc-login.php?registration=disabled' ) );
			exit;
		}

		$user_login = '';
		$user_email = '';

		if ( $http_post ) {
			if ( isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] ) ) {
				$user_login = gc_unslash( $_POST['user_login'] );
			}

			if ( isset( $_POST['user_email'] ) && is_string( $_POST['user_email'] ) ) {
				$user_email = gc_unslash( $_POST['user_email'] );
			}

			$errors = register_new_user( $user_login, $user_email );

			if ( ! is_gc_error( $errors ) ) {
				$redirect_to = ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'gc-login.php?checkemail=registered';
				gc_safe_redirect( $redirect_to );
				exit;
			}
		}

		$registration_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

		/**
		 * Filters the registration redirect URL.
		 *
		 *
		 * @param string $registration_redirect The redirect destination URL.
		 */
		$redirect_to = apply_filters( 'registration_redirect', $registration_redirect );

		login_header( __( '注册表单' ), '', $errors );

		?>
    <div class="card shadow-lg">
		<form name="registerform" id="registerform" action="<?php echo esc_url( site_url( 'gc-login.php?action=register', 'login_post' ) ); ?>" method="post" novalidate="novalidate">
            <div class="card-body">
                <div class="align-items-center justify-content-between m-b-15 text-center">
                    <h2 class="m-b-0"><?php _e( '电子邮箱注册' ); ?></h2>
                </div>
                <?php if( has_filter('gc_sms') ) :?>
                <div class="form-group">
                    <p><?php echo sprintf( __('通过你的电子邮箱注册,  或使用 <a href="%s">手机号</a>'), esc_url( '/gc-login.php?action=register-sms')); ?></p>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="user_login"><?php _e( '用户名' ); ?></label>
                    <input type="text" name="user_login" id="user_login" class="form-control" value="<?php echo esc_attr( gc_unslash( $user_login ) ); ?>" size="20" autocapitalize="off" />
                </div>
                <div class="form-group">
                    <label for="user_email"><?php _e( '电子邮箱' ); ?></label>
                    <input type="email" name="user_email" id="user_email" class="form-control" value="<?php echo esc_attr( gc_unslash( $user_email ) ); ?>" size="25" />
                </div>
                <?php

                /**
                 * Fires following the 'Email' field in the user registration form.
                 *
               
                 */
                do_action( 'register_form' );

                ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between"> 
                        <div>
                            <p id="reg_passmail">
                                <?php _e( '注册确认信息将通过电子邮件发送给您。' ); ?>
                            </p>
                            <p id="nav">
                                <a href="<?php echo esc_url( gc_login_url() ); ?>"><?php _e( '登录' ); ?></a>
                                    | 
                                <a href="<?php echo esc_url( gc_lostpassword_url() ); ?>"><?php _e( '忘记密码？' ); ?></a>
                            </p>
                        </div>
                        <input type="submit" name="gc-submit" id="gc-submit" class="btn btn-primary" value="<?php esc_attr_e( '注册' ); ?>" />
                    </div>
                </div>
            </div>
		</form>
    </div>
		<?php
