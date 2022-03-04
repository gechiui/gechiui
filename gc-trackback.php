<?php
/**
 * Handle Trackbacks and Pingbacks Sent to GeChiUI
 *
 *
 *
 * @package GeChiUI
 * @subpackage Trackbacks
 */

if ( empty( $gc ) ) {
	require_once __DIR__ . '/gc-load.php';
	gc( array( 'tb' => '1' ) );
}

/**
 * Response to a trackback.
 *
 * Responds with an error or success XML message.
 *
 *
 *
 * @param int|bool $error         Whether there was an error.
 *                                Default '0'. Accepts '0' or '1', true or false.
 * @param string   $error_message Error message if an error occurred.
 */
function trackback_response( $error = 0, $error_message = '' ) {
	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );
	if ( $error ) {
		echo '<?xml version="1.0" encoding="utf-8"?' . ">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo '</response>';
		die();
	} else {
		echo '<?xml version="1.0" encoding="utf-8"?' . ">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo '</response>';
	}
}

// Trackback is done by a POST.
$request_array = 'HTTP_POST_VARS';

if ( ! isset( $_GET['tb_id'] ) || ! $_GET['tb_id'] ) {
	$tb_id = explode( '/', $_SERVER['REQUEST_URI'] );
	$tb_id = (int) $tb_id[ count( $tb_id ) - 1 ];
}

$tb_url  = isset( $_POST['url'] ) ? $_POST['url'] : '';
$charset = isset( $_POST['charset'] ) ? $_POST['charset'] : '';

// These three are stripslashed here so they can be properly escaped after mb_convert_encoding().
$title     = isset( $_POST['title'] ) ? gc_unslash( $_POST['title'] ) : '';
$excerpt   = isset( $_POST['excerpt'] ) ? gc_unslash( $_POST['excerpt'] ) : '';
$blog_name = isset( $_POST['blog_name'] ) ? gc_unslash( $_POST['blog_name'] ) : '';

if ( $charset ) {
	$charset = str_replace( array( ',', ' ' ), '', strtoupper( trim( $charset ) ) );
} else {
	$charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS';
}

// No valid uses for UTF-7.
if ( false !== strpos( $charset, 'UTF-7' ) ) {
	die;
}

// For international trackbacks.
if ( function_exists( 'mb_convert_encoding' ) ) {
	$title     = mb_convert_encoding( $title, get_option( 'blog_charset' ), $charset );
	$excerpt   = mb_convert_encoding( $excerpt, get_option( 'blog_charset' ), $charset );
	$blog_name = mb_convert_encoding( $blog_name, get_option( 'blog_charset' ), $charset );
}

// Now that mb_convert_encoding() has been given a swing, we need to escape these three.
$title     = gc_slash( $title );
$excerpt   = gc_slash( $excerpt );
$blog_name = gc_slash( $blog_name );

if ( is_single() || is_page() ) {
	$tb_id = $posts[0]->ID;
}

if ( ! isset( $tb_id ) || ! (int) $tb_id ) {
	trackback_response( 1, __( '我需要ID来执行操作。' ) );
}

if ( empty( $title ) && empty( $tb_url ) && empty( $blog_name ) ) {
	// If it doesn't look like a trackback at all.
	gc_redirect( get_permalink( $tb_id ) );
	exit;
}

if ( ! empty( $tb_url ) && ! empty( $title ) ) {
	/**
	 * Fires before the trackback is added to a post.
	 *
	 *
	 * @param int    $tb_id     Post ID related to the trackback.
	 * @param string $tb_url    Trackback URL.
	 * @param string $charset   Character Set.
	 * @param string $title     Trackback Title.
	 * @param string $excerpt   Trackback Excerpt.
	 * @param string $blog_name Blog Name.
	 */
	do_action( 'pre_trackback_post', $tb_id, $tb_url, $charset, $title, $excerpt, $blog_name );

	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );

	if ( ! pings_open( $tb_id ) ) {
		trackback_response( 1, __( '抱歉，此项目未开放trackback。' ) );
	}

	$title   = gc_html_excerpt( $title, 250, '&#8230;' );
	$excerpt = gc_html_excerpt( $excerpt, 252, '&#8230;' );

	$comment_post_ID      = (int) $tb_id;
	$comment_author       = $blog_name;
	$comment_author_email = '';
	$comment_author_url   = $tb_url;
	$comment_content      = "<strong>$title</strong>\n\n$excerpt";
	$comment_type         = 'trackback';

	$dupe = $gcdb->get_results( $gcdb->prepare( "SELECT * FROM $gcdb->comments WHERE comment_post_ID = %d AND comment_author_url = %s", $comment_post_ID, $comment_author_url ) );
	if ( $dupe ) {
		trackback_response( 1, __( '我们已经收到了来自该URL对此文章的ping。' ) );
	}

	$commentdata = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type' );

	$result = gc_new_comment( $commentdata );

	if ( is_gc_error( $result ) ) {
		trackback_response( 1, $result->get_error_message() );
	}

	$trackback_id = $gcdb->insert_id;

	/**
	 * Fires after a trackback is added to a post.
	 *
	 *
	 * @param int $trackback_id Trackback ID.
	 */
	do_action( 'trackback_post', $trackback_id );
	trackback_response( 0 );
}
