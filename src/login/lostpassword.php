<?php

        if ( is_multisite() ) {
			if ( ! is_main_network() || ! is_main_site() ) {
                gc_redirect( apply_filters( 'gc_signup_location', network_site_url( 'gc-login.php?action=lostpassword' ) ) );
                exit;
            }
		}

        if ( $http_post ) {
			$errors = retrieve_password();

			if ( ! is_gc_error( $errors ) ) {
				$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'gc-login.php?checkemail=confirm';
				gc_safe_redirect( $redirect_to );
				exit;
			}
		}

		if ( isset( $_GET['error'] ) ) {
			if ( 'invalidkey' === $_GET['error'] ) {
				$errors->add( 'invalidkey', __( '<strong>错误</strong>：您的密码重置链接似乎无效，请在下方请求新的链接。' ) );
			} elseif ( 'expiredkey' === $_GET['error'] ) {
				$errors->add( 'expiredkey', __( '<strong>错误</strong>：您的密码重置链接已过期，请在下方请求新链接。' ) );
			}
		}

		$lostpassword_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
		/**
		 * Filters the URL redirected to after submitting the lostpassword/retrievepassword form.
		 *
		 * @param string $lostpassword_redirect The redirect destination URL.
		 */
		$redirect_to = apply_filters( 'lostpassword_redirect', $lostpassword_redirect );

		/**
		 * Fires before the lost password form.
		 *
		 * @param GC_Error $errors A `GC_Error` object containing any errors generated by using invalid
		 *                         credentials. Note that the error object may not contain any errors.
		 */
		do_action( 'lost_password', $errors );

		login_header( __( '忘记密码' ), '', $errors );

		$user_login = '';

		if ( isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] ) ) {
			$user_login = gc_unslash( $_POST['user_login'] );
		}

		?>
     <div class="card shadow-lg">
         <div class="card-body">
            <div class="align-items-center justify-content-between m-b-30 text-center">
                <h2 class="m-b-0"><?php _e( '忘记密码' ); ?></h2>
            </div>
            <?php if( has_filter('gc_sms') ) :?>
            <div class="form-group">
                <p><?php _e( '请输入您的用户名或电子邮箱。您会收到一封包含重设密码指引的邮件。' ); ?></p>
                <p><?php echo sprintf(__('或使用 <a href="%s">手机短信登录</a>后修改密码'), esc_url( '/gc-login.php?action=sms'))?></p>
            </div>
            <?php endif; ?>
            <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( network_site_url( 'gc-login.php?action=lostpassword', 'login_post' ) ); ?>" method="post">
                <div class="form-group">
                    <label for="user_login"><?php _e( '用户名或电子邮箱' ); ?></label>
                    <input type="text" name="user_login" id="user_login" class="form-control" value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" />
                </div>
                <?php

                /**
                 * Fires inside the lostpassword form tags, before the hidden fields.
                 *
               
                 */
                do_action( 'lostpassword_form' );

                ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                <div class="form-group">
                    <button class="btn btn-primary btn-block" name="gc-submit" id="gc-submit" class="submit" >
                    <?php esc_attr_e( '获取新密码' ); ?>
                    </button>
                </div>
            </form>

            <p id="nav">
                <a href="<?php echo esc_url( gc_login_url() ); ?>"><?php _e( '登录' ); ?></a>
                <?php

                if ( get_option( 'users_can_register' ) ) {
                    $registration_url = sprintf( '<a href="%s">%s</a>', esc_url( gc_registration_url() ), __( '注册' ) );

                    echo esc_html( ' | ' );

                    /** This filter is documented in gc-includes/general-template.php */
                    echo apply_filters( 'register', $registration_url );
                }

                ?>
            </p>
        </div>
    </div>
		<?php
