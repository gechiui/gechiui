<?php
        if ( ! array_key_exists( 'post_password', $_POST ) ) {
			gc_safe_redirect( gc_get_referer() );
			exit;
		}

		require_once ABSPATH . GCINC . '/class-phpass.php';
		$hasher = new PasswordHash( 8, true );

		/**
		 * Filters the life span of the post password cookie.
		 *
		 * By default, the cookie expires 10 days from creation. To turn this
		 * into a session cookie, return 0.
		 *
		 * @param int $expires The expiry time, as passed to setcookie().
		 */
		$expire  = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
		$referer = gc_get_referer();

		if ( $referer ) {
			$secure = ( 'https' === parse_url( $referer, PHP_URL_SCHEME ) );
		} else {
			$secure = false;
		}

		setcookie( 'gc-postpass_' . COOKIEHASH, $hasher->HashPassword( gc_unslash( $_POST['post_password'] ) ), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );

		gc_safe_redirect( gc_get_referer() );