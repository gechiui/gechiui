<?php
        $redirect_to = admin_url();
		$errors      = new GC_Error();

		if ( 'confirm' === $_GET['checkemail'] ) {
			$errors->add(
				'confirm',
				sprintf(
					/* translators: %s: Link to the login page. */
					__( '请在您的电子邮箱中查收确认链接，然后访问<a href="%s">登录页面</a>。' ),
					gc_login_url()
				),
				'message'
			);
		} elseif ( 'registered' === $_GET['checkemail'] ) {
			$errors->add(
				'registered',
				sprintf(
					/* translators: %s: Link to the login page. */
					__( '注册完成。请检查您电子邮箱中收到的邮件，然后访问 <a href="%s">登录页面</a>。' ),
					gc_login_url()
				),
				'message'
			);
		}

		/** This action is documented in gc-login.php */
		$errors = apply_filters( 'gc_login_errors', $errors, $redirect_to );

		login_header( __( '检查您的电子邮箱' ), '', $errors );