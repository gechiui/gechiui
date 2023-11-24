<?php
        check_admin_referer( 'log-out' );

		$user = gc_get_current_user();

		gc_logout();

		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to           = $_REQUEST['redirect_to'];
			$requested_redirect_to = $redirect_to;
		} else {
			$redirect_to = add_query_arg(
				array(
					'loggedout' => 'true',
					'gc_lang'   => get_user_locale( $user ),
				),
				gc_login_url()
			);

			$requested_redirect_to = '';
		}

		/**
		 * Filters the log out redirect URL.
		 *
		 * @param string  $redirect_to           The redirect destination URL.
		 * @param string  $requested_redirect_to The requested redirect destination URL passed as a parameter.
		 * @param GC_User $user                  The GC_User object for the user that's logging out.
		 */
		$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );

		gc_safe_redirect( $redirect_to );