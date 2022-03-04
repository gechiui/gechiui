<?php
/**
 * Feed API: GC_SimplePie_File class
 *
 * @package GeChiUI
 * @subpackage Feed
 *
 */

/**
 * Core class for fetching remote files and reading local files with SimplePie.
 *
 * This uses Core's HTTP API to make requests, which gives plugins the ability
 * to hook into the process.
 *
 *
 *
 * @see SimplePie_File
 */
class GC_SimplePie_File extends SimplePie_File {

	/**
	 * Constructor.
	 *
	 *              rather than remaining an array.
	 *
	 * @param string       $url             Remote file URL.
	 * @param int          $timeout         Optional. How long the connection should stay open in seconds.
	 *                                      Default 10.
	 * @param int          $redirects       Optional. The number of allowed redirects. Default 5.
	 * @param string|array $headers         Optional. Array or string of headers to send with the request.
	 *                                      Default null.
	 * @param string       $useragent       Optional. User-agent value sent. Default null.
	 * @param bool         $force_fsockopen Optional. Whether to force opening internet or unix domain socket
	 *                                      connection or not. Default false.
	 */
	public function __construct( $url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false ) {
		$this->url       = $url;
		$this->timeout   = $timeout;
		$this->redirects = $redirects;
		$this->headers   = $headers;
		$this->useragent = $useragent;

		$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE;

		if ( preg_match( '/^http(s)?:\/\//i', $url ) ) {
			$args = array(
				'timeout'     => $this->timeout,
				'redirection' => $this->redirects,
			);

			if ( ! empty( $this->headers ) ) {
				$args['headers'] = $this->headers;
			}

			if ( SIMPLEPIE_USERAGENT != $this->useragent ) { // Use default GC user agent unless custom has been specified.
				$args['user-agent'] = $this->useragent;
			}

			$res = gc_safe_remote_request( $url, $args );

			if ( is_gc_error( $res ) ) {
				$this->error   = 'GC HTTP Error: ' . $res->get_error_message();
				$this->success = false;

			} else {
				$this->headers = gc_remote_retrieve_headers( $res );

				/*
				 * SimplePie expects multiple headers to be stored as a comma-separated string,
				 * but `gc_remote_retrieve_headers()` returns them as an array, so they need
				 * to be converted.
				 *
				 * The only exception to that is the `content-type` header, which should ignore
				 * any previous values and only use the last one.
				 *
				 * @see SimplePie_HTTP_Parser::new_line().
				 */
				foreach ( $this->headers as $name => $value ) {
					if ( ! is_array( $value ) ) {
						continue;
					}

					if ( 'content-type' === $name ) {
						$this->headers[ $name ] = array_pop( $value );
					} else {
						$this->headers[ $name ] = implode( ', ', $value );
					}
				}

				$this->body        = gc_remote_retrieve_body( $res );
				$this->status_code = gc_remote_retrieve_response_code( $res );
			}
		} else {
			$this->error   = '';
			$this->success = false;
		}
	}
}
