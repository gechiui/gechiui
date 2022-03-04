<?php
        if ( ! isset( $_GET['request_id'] ) ) {
			gc_die( __( '缺少请求ID。' ) );
		}

		if ( ! isset( $_GET['confirm_key'] ) ) {
			gc_die( __( '缺少确认密钥。' ) );
		}

		$request_id = (int) $_GET['request_id'];
		$key        = sanitize_text_field( gc_unslash( $_GET['confirm_key'] ) );
		$result     = gc_validate_user_request_key( $request_id, $key );

		if ( is_gc_error( $result ) ) {
			gc_die( $result );
		}

		/**
		 * Fires an action hook when the account action has been confirmed by the user.
		 *
		 * Using this you can assume the user has agreed to perform the action by
		 * clicking on the link in the confirmation email.
		 *
		 * After firing this action hook the page will redirect to gc-login a callback
		 * redirects or exits first.
		 *
		 *
		 * @param int $request_id Request ID.
		 */
		do_action( 'user_request_action_confirmed', $request_id );

		$message = _gc_privacy_account_request_confirmed_message( $request_id );

		login_header( __( '用户操作已确认。' ), $message );