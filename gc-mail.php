<?php
/**
 * Gets the email message from the user's mailbox to add as
 * a GeChiUI post. Mailbox connection information must be
 * configured under Settings > Writing
 *
 * @package GeChiUI
 */

/** Make sure that the GeChiUI bootstrap has run before continuing. */
require __DIR__ . '/gc-load.php';

/** This filter is documented in gc-admin/options.php */
if ( ! apply_filters( 'enable_post_by_email_configuration', true ) ) {
	gc_die( __( '此操作已被管理员禁用。' ), 403 );
}

$mailserver_url = get_option( 'mailserver_url' );

if ( 'mail.example.com' === $mailserver_url || empty( $mailserver_url ) ) {
	gc_die( __( '此操作已被管理员禁用。' ), 403 );
}

/**
 * Fires to allow a plugin to do a complete takeover of Post by Email.
 *
 *
 */
do_action( 'gc-mail.php' ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

/** Get the POP3 class with which to access the mailbox. */
require_once ABSPATH . GCINC . '/class-pop3.php';

/** Only check at this interval for new messages. */
if ( ! defined( 'GC_MAIL_INTERVAL' ) ) {
	define( 'GC_MAIL_INTERVAL', 5 * MINUTE_IN_SECONDS );
}

$last_checked = get_transient( 'mailserver_last_checked' );

if ( $last_checked ) {
	gc_die( __( '请放慢检查频率，不需要这么频繁地检查新邮件的！' ) );
}

set_transient( 'mailserver_last_checked', true, GC_MAIL_INTERVAL );

$time_difference = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

$phone_delim = '::';

$pop3 = new POP3();

if ( ! $pop3->connect( get_option( 'mailserver_url' ), get_option( 'mailserver_port' ) ) || ! $pop3->user( get_option( 'mailserver_login' ) ) ) {
	gc_die( esc_html( $pop3->ERROR ) );
}

$count = $pop3->pass( get_option( 'mailserver_pass' ) );

if ( false === $count ) {
	gc_die( esc_html( $pop3->ERROR ) );
}

if ( 0 === $count ) {
	$pop3->quit();
	gc_die( __( '没有发现新邮件。' ) );
}

for ( $i = 1; $i <= $count; $i++ ) {

	$message = $pop3->get( $i );

	$bodysignal                = false;
	$boundary                  = '';
	$charset                   = '';
	$content                   = '';
	$content_type              = '';
	$content_transfer_encoding = '';
	$post_author               = 1;
	$author_found              = false;
	$post_date                 = null;
	$post_date_gmt             = null;

	foreach ( $message as $line ) {
		// Body signal.
		if ( strlen( $line ) < 3 ) {
			$bodysignal = true;
		}
		if ( $bodysignal ) {
			$content .= $line;
		} else {
			if ( preg_match( '/Content-Type: /i', $line ) ) {
				$content_type = trim( $line );
				$content_type = substr( $content_type, 14, strlen( $content_type ) - 14 );
				$content_type = explode( ';', $content_type );
				if ( ! empty( $content_type[1] ) ) {
					$charset = explode( '=', $content_type[1] );
					$charset = ( ! empty( $charset[1] ) ) ? trim( $charset[1] ) : '';
				}
				$content_type = $content_type[0];
			}
			if ( preg_match( '/Content-Transfer-Encoding: /i', $line ) ) {
				$content_transfer_encoding = trim( $line );
				$content_transfer_encoding = substr( $content_transfer_encoding, 27, strlen( $content_transfer_encoding ) - 27 );
				$content_transfer_encoding = explode( ';', $content_transfer_encoding );
				$content_transfer_encoding = $content_transfer_encoding[0];
			}
			if ( ( 'multipart/alternative' === $content_type ) && ( false !== strpos( $line, 'boundary="' ) ) && ( '' === $boundary ) ) {
				$boundary = trim( $line );
				$boundary = explode( '"', $boundary );
				$boundary = $boundary[1];
			}
			if ( preg_match( '/Subject: /i', $line ) ) {
				$subject = trim( $line );
				$subject = substr( $subject, 9, strlen( $subject ) - 9 );
				// Captures any text in the subject before $phone_delim as the subject.
				if ( function_exists( 'iconv_mime_decode' ) ) {
					$subject = iconv_mime_decode( $subject, 2, get_option( 'blog_charset' ) );
				} else {
					$subject = gc_iso_descrambler( $subject );
				}
				$subject = explode( $phone_delim, $subject );
				$subject = $subject[0];
			}

			/*
			 * Set the author using the email address (From or Reply-To, the last used)
			 * otherwise use the site admin.
			 */
			if ( ! $author_found && preg_match( '/^(From|Reply-To): /', $line ) ) {
				if ( preg_match( '|[a-z0-9_.-]+@[a-z0-9_.-]+(?!.*<)|i', $line, $matches ) ) {
					$author = $matches[0];
				} else {
					$author = trim( $line );
				}
				$author = sanitize_email( $author );
				if ( is_email( $author ) ) {
					/* translators: %s: Post author email address. */
					echo '<p>' . sprintf( __( '作者的电子邮箱为%s' ), $author ) . '</p>';
					$userdata = get_user_by( 'email', $author );
					if ( ! empty( $userdata ) ) {
						$post_author  = $userdata->ID;
						$author_found = true;
					}
				}
			}

			if ( preg_match( '/Date: /i', $line ) ) { // Of the form '20 Mar 2002 20:32:37 +0100'.
				$ddate = str_replace( 'Date: ', '', trim( $line ) );
				// Remove parenthesised timezone string if it exists, as this confuses strtotime().
				$ddate           = preg_replace( '!\s*\(.+\)\s*$!', '', $ddate );
				$ddate_timestamp = strtotime( $ddate );
				$post_date       = gmdate( 'Y-m-d H:i:s', $ddate_timestamp + $time_difference );
				$post_date_gmt   = gmdate( 'Y-m-d H:i:s', $ddate_timestamp );
			}
		}
	}

	// Set $post_status based on $author_found and on author's publish_posts capability.
	if ( $author_found ) {
		$user        = new GC_User( $post_author );
		$post_status = ( $user->has_cap( 'publish_posts' ) ) ? 'publish' : 'pending';
	} else {
		// Author not found in DB, set status to pending. Author already set to admin.
		$post_status = 'pending';
	}

	$subject = trim( $subject );

	if ( 'multipart/alternative' === $content_type ) {
		$content = explode( '--' . $boundary, $content );
		$content = $content[2];

		// Match case-insensitive content-transfer-encoding.
		if ( preg_match( '/Content-Transfer-Encoding: quoted-printable/i', $content, $delim ) ) {
			$content = explode( $delim[0], $content );
			$content = $content[1];
		}
		$content = strip_tags( $content, '<img><p><br><i><b><u><em><strong><strike><font><span><div>' );
	}
	$content = trim( $content );

	/**
	 * Filters the original content of the email.
	 *
	 * Give Post-By-Email extending plugins full access to the content, either
	 * the raw content, or the content of the last quoted-printable section.
	 *
	 *
	 * @param string $content The original email content.
	 */
	$content = apply_filters( 'gc_mail_original_content', $content );

	if ( false !== stripos( $content_transfer_encoding, 'quoted-printable' ) ) {
		$content = quoted_printable_decode( $content );
	}

	if ( function_exists( 'iconv' ) && ! empty( $charset ) ) {
		$content = iconv( $charset, get_option( 'blog_charset' ), $content );
	}

	// Captures any text in the body after $phone_delim as the body.
	$content = explode( $phone_delim, $content );
	$content = empty( $content[1] ) ? $content[0] : $content[1];

	$content = trim( $content );

	/**
	 * Filters the content of the post submitted by email before saving.
	 *
	 *
	 * @param string $content The email content.
	 */
	$post_content = apply_filters( 'phone_content', $content );

	$post_title = xmlrpc_getposttitle( $content );

	if ( '' === trim( $post_title ) ) {
		$post_title = $subject;
	}

	$post_category = array( get_option( 'default_email_category' ) );

	$post_data = compact( 'post_content', 'post_title', 'post_date', 'post_date_gmt', 'post_author', 'post_category', 'post_status' );
	$post_data = gc_slash( $post_data );

	$post_ID = gc_insert_post( $post_data );
	if ( is_gc_error( $post_ID ) ) {
		echo "\n" . $post_ID->get_error_message();
	}

	// We couldn't post, for whatever reason. Better move forward to the next email.
	if ( empty( $post_ID ) ) {
		continue;
	}

	/**
	 * Fires after a post submitted by email is published.
	 *
	 *
	 * @param int $post_ID The post ID.
	 */
	do_action( 'publish_phone', $post_ID );

	echo "\n<p><strong>" . __( '作者：' ) . '</strong> ' . esc_html( $post_author ) . '</p>';
	echo "\n<p><strong>" . __( '文章标题：' ) . '</strong> ' . esc_html( $post_title ) . '</p>';

	if ( ! $pop3->delete( $i ) ) {
		echo '<p>' . sprintf(
			/* translators: %s: POP3 error. */
			__( '出错了：%s' ),
			esc_html( $pop3->ERROR )
		) . '</p>';
		$pop3->reset();
		exit;
	} else {
		echo '<p>' . sprintf(
			/* translators: %s: The message ID. */
			__( '任务完成。信息%s已删除。' ),
			'<strong>' . $i . '</strong>'
		) . '</p>';
	}
}

$pop3->quit();
