<?php
        list( $rp_path ) = explode( '?', gc_unslash( $_SERVER['REQUEST_URI'] ) );
		$rp_cookie       = 'gc-resetpass-' . COOKIEHASH;

		if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {
			$value = sprintf( '%s:%s', gc_unslash( $_GET['login'] ), gc_unslash( $_GET['key'] ) );
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );

			gc_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
			exit;
		}

		if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
			list( $rp_login, $rp_key ) = explode( ':', gc_unslash( $_COOKIE[ $rp_cookie ] ), 2 );

			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( isset( $_POST['pass1'] ) && ! hash_equals( $rp_key, $_POST['rp_key'] ) ) {
				$user = false;
			}
		} else {
			$user = false;
		}

		if ( ! $user || is_gc_error( $user ) ) {
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );

			if ( $user && $user->get_error_code() === 'expired_key' ) {
				gc_redirect( site_url( 'gc-login.php?action=lostpassword&error=expiredkey' ) );
			} else {
				gc_redirect( site_url( 'gc-login.php?action=lostpassword&error=invalidkey' ) );
			}

			exit;
		}

		$errors = new GC_Error();

		if ( isset( $_POST['pass1'] ) && $_POST['pass1'] !== $_POST['pass2'] ) {
			$errors->add( 'password_reset_mismatch', __( '<strong>错误</strong>：密码不匹配。' ) );
		}

		/**
		 * Fires before the password reset procedure is validated.
		 *
		 *
		 * @param GC_Error         $errors GC Error object.
		 * @param GC_User|GC_Error $user   GC_User object if the login and reset key match. GC_Error object otherwise.
		 */
		do_action( 'validate_password_reset', $errors, $user );

		if ( ( ! $errors->has_errors() ) && isset( $_POST['pass1'] ) && ! empty( $_POST['pass1'] ) ) {
			reset_password( $user, $_POST['pass1'] );
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			login_header( __( '密码重置' ), '<p class="message reset-pass">' . __( '您的密码已被重置。' ) . ' <a href="' . esc_url( gc_login_url() ) . '">' . __( '登录' ) . '</a></p>' );
			login_footer();
			exit;
		}

		gc_enqueue_script( 'utils' );
		gc_enqueue_script( 'user-profile' );

		login_header( __( '重置密码' ), '<p class="message reset-pass">' . __( '在下方输入您的新密码，或生成一个新密码。' ) . '</p>', $errors );

		?>
		<form name="resetpassform" id="resetpassform" action="<?php echo esc_url( network_site_url( 'gc-login.php?action=resetpass', 'login_post' ) ); ?>" method="post" autocomplete="off">
			<input type="hidden" id="user_login" value="<?php echo esc_attr( $rp_login ); ?>" autocomplete="off" />

			<div class="user-pass1-wrap">
				<p>
					<label for="pass1"><?php _e( '新密码' ); ?></label>
				</p>

				<div class="gc-pwd">
					<input type="password" data-reveal="1" data-pw="<?php echo esc_attr( gc_generate_password( 16 ) ); ?>" name="pass1" id="pass1" class="input password-input" size="24" value="" autocomplete="off" aria-describedby="pass-strength-result" />

					<button type="button" class="button button-secondary gc-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( '隐藏密码' ); ?>">
						<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
					</button>
					<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite"><?php _e( '强度评估' ); ?></div>
				</div>
				<div class="pw-weak">
					<input type="checkbox" name="pw_weak" id="pw-weak" class="pw-checkbox" />
					<label for="pw-weak"><?php _e( '确认使用弱密码' ); ?></label>
				</div>
			</div>

			<p class="user-pass2-wrap">
				<label for="pass2"><?php _e( '确认新密码' ); ?></label>
				<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
			</p>

			<p class="description indicator-hint"><?php echo gc_get_password_hint(); ?></p>
			<br class="clear" />

			<?php

			/**
			 * Fires following the 'Strength indicator' meter in the user password reset form.
			 *
		
			 *
			 * @param GC_User $user User object of the user whose password is being reset.
			 */
			do_action( 'resetpass_form', $user );

			?>
			<input type="hidden" name="rp_key" value="<?php echo esc_attr( $rp_key ); ?>" />
			<p class="submit reset-pass-submit">
				<button type="button" class="button gc-generate-pw hide-if-no-js" aria-expanded="true"><?php _e( '生成密码' ); ?></button>
				<input type="submit" name="gc-submit" id="gc-submit" class="button button-primary button-large" value="<?php esc_attr_e( '保存密码' ); ?>" />
			</p>
		</form>

		<p id="nav">
			<a href="<?php echo esc_url( gc_login_url() ); ?>"><?php _e( '登录' ); ?></a>
			<?php

			if ( get_option( 'users_can_register' ) ) {
				$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( gc_registration_url() ), __( '注册' ) );

				echo esc_html( $login_link_separator );

				/** This filter is documented in gc-includes/general-template.php */
				echo apply_filters( 'register', $registration_url );
			}

			?>
		</p>
		<?php
