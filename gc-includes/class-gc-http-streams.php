<?php
/**
 * HTTP API: GC_Http_Streams class
 *
 * @package GeChiUI
 * @subpackage HTTP
 *
 */

/**
 * Core class used to integrate PHP Streams as an HTTP transport.
 *
 *
 *
 */
class GC_Http_Streams {
	/**
	 * Send a HTTP request to a URI using PHP Streams.
	 *
	 * @see GC_Http::request For default options descriptions.
	 *
	 *
	 * @param string       $url  The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|GC_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A GC_Error instance upon error
	 */
	public function request( $url, $args = array() ) {
		$defaults = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => null,
			'cookies'     => array(),
		);

		$parsed_args = gc_parse_args( $args, $defaults );

		if ( isset( $parsed_args['headers']['User-Agent'] ) ) {
			$parsed_args['user-agent'] = $parsed_args['headers']['User-Agent'];
			unset( $parsed_args['headers']['User-Agent'] );
		} elseif ( isset( $parsed_args['headers']['user-agent'] ) ) {
			$parsed_args['user-agent'] = $parsed_args['headers']['user-agent'];
			unset( $parsed_args['headers']['user-agent'] );
		}

		// Construct Cookie: header if any cookies are set.
		GC_Http::buildCookieHeader( $parsed_args );

		$parsed_url = parse_url( $url );

		$connect_host = $parsed_url['host'];

		$secure_transport = ( 'ssl' === $parsed_url['scheme'] || 'https' === $parsed_url['scheme'] );
		if ( ! isset( $parsed_url['port'] ) ) {
			if ( 'ssl' === $parsed_url['scheme'] || 'https' === $parsed_url['scheme'] ) {
				$parsed_url['port'] = 443;
				$secure_transport   = true;
			} else {
				$parsed_url['port'] = 80;
			}
		}

		// Always pass a path, defaulting to the root in cases such as http://example.com.
		if ( ! isset( $parsed_url['path'] ) ) {
			$parsed_url['path'] = '/';
		}

		if ( isset( $parsed_args['headers']['Host'] ) || isset( $parsed_args['headers']['host'] ) ) {
			if ( isset( $parsed_args['headers']['Host'] ) ) {
				$parsed_url['host'] = $parsed_args['headers']['Host'];
			} else {
				$parsed_url['host'] = $parsed_args['headers']['host'];
			}
			unset( $parsed_args['headers']['Host'], $parsed_args['headers']['host'] );
		}

		/*
		 * Certain versions of PHP have issues with 'localhost' and IPv6, It attempts to connect
		 * to ::1, which fails when the server is not set up for it. For compatibility, always
		 * connect to the IPv4 address.
		 */
		if ( 'localhost' === strtolower( $connect_host ) ) {
			$connect_host = '127.0.0.1';
		}

		$connect_host = $secure_transport ? 'ssl://' . $connect_host : 'tcp://' . $connect_host;

		$is_local   = isset( $parsed_args['local'] ) && $parsed_args['local'];
		$ssl_verify = isset( $parsed_args['sslverify'] ) && $parsed_args['sslverify'];

		if ( $is_local ) {
			/**
			 * Filters whether SSL should be verified for local HTTP API requests.
			 *
		
		
			 *
			 * @param bool   $ssl_verify Whether to verify the SSL connection. Default true.
			 * @param string $url        The request URL.
			 */
			$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify, $url );
		} elseif ( ! $is_local ) {
			/** This filter is documented in gc-includes/class-gc-http.php */
			$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify, $url );
		}

		$proxy = new GC_HTTP_Proxy();

		$context = stream_context_create(
			array(
				'ssl' => array(
					'verify_peer'       => $ssl_verify,
					// 'CN_match' => $parsed_url['host'], // This is handled by self::verify_ssl_certificate().
					'capture_peer_cert' => $ssl_verify,
					'SNI_enabled'       => true,
					'cafile'            => $parsed_args['sslcertificates'],
					'allow_self_signed' => ! $ssl_verify,
				),
			)
		);

		$timeout         = (int) floor( $parsed_args['timeout'] );
		$utimeout        = $timeout == $parsed_args['timeout'] ? 0 : 1000000 * $parsed_args['timeout'] % 1000000;
		$connect_timeout = max( $timeout, 1 );

		// Store error number.
		$connection_error = null;

		// Store error string.
		$connection_error_str = null;

		if ( ! GC_DEBUG ) {
			// In the event that the SSL connection fails, silence the many PHP warnings.
			if ( $secure_transport ) {
				$error_reporting = error_reporting( 0 );
			}

			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
				// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
				$handle = @stream_socket_client(
					'tcp://' . $proxy->host() . ':' . $proxy->port(),
					$connection_error,
					$connection_error_str,
					$connect_timeout,
					STREAM_CLIENT_CONNECT,
					$context
				);
			} else {
				// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
				$handle = @stream_socket_client(
					$connect_host . ':' . $parsed_url['port'],
					$connection_error,
					$connection_error_str,
					$connect_timeout,
					STREAM_CLIENT_CONNECT,
					$context
				);
			}

			if ( $secure_transport ) {
				error_reporting( $error_reporting );
			}
		} else {
			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
				$handle = stream_socket_client(
					'tcp://' . $proxy->host() . ':' . $proxy->port(),
					$connection_error,
					$connection_error_str,
					$connect_timeout,
					STREAM_CLIENT_CONNECT,
					$context
				);
			} else {
				$handle = stream_socket_client(
					$connect_host . ':' . $parsed_url['port'],
					$connection_error,
					$connection_error_str,
					$connect_timeout,
					STREAM_CLIENT_CONNECT,
					$context
				);
			}
		}

		if ( false === $handle ) {
			// SSL connection failed due to expired/invalid cert, or, OpenSSL configuration is broken.
			if ( $secure_transport && 0 === $connection_error && '' === $connection_error_str ) {
				return new GC_Error( 'http_request_failed', __( '主机的SSL证书不能被验证。' ) );
			}

			return new GC_Error( 'http_request_failed', $connection_error . ': ' . $connection_error_str );
		}

		// Verify that the SSL certificate is valid for this request.
		if ( $secure_transport && $ssl_verify && ! $proxy->is_enabled() ) {
			if ( ! self::verify_ssl_certificate( $handle, $parsed_url['host'] ) ) {
				return new GC_Error( 'http_request_failed', __( '主机的SSL证书不能被验证。' ) );
			}
		}

		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) { // Some proxies require full URL in this field.
			$requestPath = $url;
		} else {
			$requestPath = $parsed_url['path'] . ( isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '' );
		}

		$strHeaders = strtoupper( $parsed_args['method'] ) . ' ' . $requestPath . ' HTTP/' . $parsed_args['httpversion'] . "\r\n";

		$include_port_in_host_header = (
			( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
			|| ( 'http' === $parsed_url['scheme'] && 80 != $parsed_url['port'] )
			|| ( 'https' === $parsed_url['scheme'] && 443 != $parsed_url['port'] )
		);

		if ( $include_port_in_host_header ) {
			$strHeaders .= 'Host: ' . $parsed_url['host'] . ':' . $parsed_url['port'] . "\r\n";
		} else {
			$strHeaders .= 'Host: ' . $parsed_url['host'] . "\r\n";
		}

		if ( isset( $parsed_args['user-agent'] ) ) {
			$strHeaders .= 'User-agent: ' . $parsed_args['user-agent'] . "\r\n";
		}

		if ( is_array( $parsed_args['headers'] ) ) {
			foreach ( (array) $parsed_args['headers'] as $header => $headerValue ) {
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
			}
		} else {
			$strHeaders .= $parsed_args['headers'];
		}

		if ( $proxy->use_authentication() ) {
			$strHeaders .= $proxy->authentication_header() . "\r\n";
		}

		$strHeaders .= "\r\n";

		if ( ! is_null( $parsed_args['body'] ) ) {
			$strHeaders .= $parsed_args['body'];
		}

		fwrite( $handle, $strHeaders );

		if ( ! $parsed_args['blocking'] ) {
			stream_set_blocking( $handle, 0 );
			fclose( $handle );
			return array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => false,
					'message' => false,
				),
				'cookies'  => array(),
			);
		}

		$strResponse  = '';
		$bodyStarted  = false;
		$keep_reading = true;
		$block_size   = 4096;

		if ( isset( $parsed_args['limit_response_size'] ) ) {
			$block_size = min( $block_size, $parsed_args['limit_response_size'] );
		}

		// If streaming to a file setup the file handle.
		if ( $parsed_args['stream'] ) {
			if ( ! GC_DEBUG ) {
				$stream_handle = @fopen( $parsed_args['filename'], 'w+' );
			} else {
				$stream_handle = fopen( $parsed_args['filename'], 'w+' );
			}

			if ( ! $stream_handle ) {
				return new GC_Error(
					'http_request_failed',
					sprintf(
						/* translators: 1: fopen(), 2: File name. */
						__( '未能为%1$s打开到%2$s的句柄。' ),
						'fopen()',
						$parsed_args['filename']
					)
				);
			}

			$bytes_written = 0;

			while ( ! feof( $handle ) && $keep_reading ) {
				$block = fread( $handle, $block_size );
				if ( ! $bodyStarted ) {
					$strResponse .= $block;
					if ( strpos( $strResponse, "\r\n\r\n" ) ) {
						$processed_response = GC_Http::processResponse( $strResponse );
						$bodyStarted        = true;
						$block              = $processed_response['body'];
						unset( $strResponse );
						$processed_response['body'] = '';
					}
				}

				$this_block_size = strlen( $block );

				if ( isset( $parsed_args['limit_response_size'] )
					&& ( $bytes_written + $this_block_size ) > $parsed_args['limit_response_size']
				) {
					$this_block_size = ( $parsed_args['limit_response_size'] - $bytes_written );
					$block           = substr( $block, 0, $this_block_size );
				}

				$bytes_written_to_file = fwrite( $stream_handle, $block );

				if ( $bytes_written_to_file != $this_block_size ) {
					fclose( $handle );
					fclose( $stream_handle );
					return new GC_Error( 'http_request_failed', __( '不能将请求写入临时文件。' ) );
				}

				$bytes_written += $bytes_written_to_file;

				$keep_reading = (
					! isset( $parsed_args['limit_response_size'] )
					|| $bytes_written < $parsed_args['limit_response_size']
				);
			}

			fclose( $stream_handle );

		} else {
			$header_length = 0;

			while ( ! feof( $handle ) && $keep_reading ) {
				$block        = fread( $handle, $block_size );
				$strResponse .= $block;

				if ( ! $bodyStarted && strpos( $strResponse, "\r\n\r\n" ) ) {
					$header_length = strpos( $strResponse, "\r\n\r\n" ) + 4;
					$bodyStarted   = true;
				}

				$keep_reading = (
					! $bodyStarted
					|| ! isset( $parsed_args['limit_response_size'] )
					|| strlen( $strResponse ) < ( $header_length + $parsed_args['limit_response_size'] )
				);
			}

			$processed_response = GC_Http::processResponse( $strResponse );
			unset( $strResponse );

		}

		fclose( $handle );

		$processed_headers = GC_Http::processHeaders( $processed_response['headers'], $url );

		$response = array(
			'headers'  => $processed_headers['headers'],
			// Not yet processed.
			'body'     => null,
			'response' => $processed_headers['response'],
			'cookies'  => $processed_headers['cookies'],
			'filename' => $parsed_args['filename'],
		);

		// Handle redirects.
		$redirect_response = GC_Http::handle_redirects( $url, $parsed_args, $response );
		if ( false !== $redirect_response ) {
			return $redirect_response;
		}

		// If the body was chunk encoded, then decode it.
		if ( ! empty( $processed_response['body'] )
			&& isset( $processed_headers['headers']['transfer-encoding'] )
			&& 'chunked' === $processed_headers['headers']['transfer-encoding']
		) {
			$processed_response['body'] = GC_Http::chunkTransferDecode( $processed_response['body'] );
		}

		if ( true === $parsed_args['decompress']
			&& true === GC_Http_Encoding::should_decode( $processed_headers['headers'] )
		) {
			$processed_response['body'] = GC_Http_Encoding::decompress( $processed_response['body'] );
		}

		if ( isset( $parsed_args['limit_response_size'] )
			&& strlen( $processed_response['body'] ) > $parsed_args['limit_response_size']
		) {
			$processed_response['body'] = substr( $processed_response['body'], 0, $parsed_args['limit_response_size'] );
		}

		$response['body'] = $processed_response['body'];

		return $response;
	}

	/**
	 * Verifies the received SSL certificate against its Common Names and subjectAltName fields.
	 *
	 * PHP's SSL verifications only verify that it's a valid Certificate, it doesn't verify if
	 * the certificate is valid for the hostname which was requested.
	 * This function verifies the requested hostname against certificate's subjectAltName field,
	 * if that is empty, or contains no DNS entries, a fallback to the Common Name field is used.
	 *
	 * IP Address support is included if the request is being made to an IP address.
	 *
	 *
	 * @param resource $stream The PHP Stream which the SSL request is being made over
	 * @param string   $host   The hostname being requested
	 * @return bool If the certificate presented in $stream is valid for $host
	 */
	public static function verify_ssl_certificate( $stream, $host ) {
		$context_options = stream_context_get_options( $stream );

		if ( empty( $context_options['ssl']['peer_certificate'] ) ) {
			return false;
		}

		$cert = openssl_x509_parse( $context_options['ssl']['peer_certificate'] );
		if ( ! $cert ) {
			return false;
		}

		/*
		 * If the request is being made to an IP address, we'll validate against IP fields
		 * in the cert (if they exist)
		 */
		$host_type = ( GC_Http::is_ip_address( $host ) ? 'ip' : 'dns' );

		$certificate_hostnames = array();
		if ( ! empty( $cert['extensions']['subjectAltName'] ) ) {
			$match_against = preg_split( '/,\s*/', $cert['extensions']['subjectAltName'] );
			foreach ( $match_against as $match ) {
				list( $match_type, $match_host ) = explode( ':', $match );
				if ( strtolower( trim( $match_type ) ) === $host_type ) { // IP: or DNS:
					$certificate_hostnames[] = strtolower( trim( $match_host ) );
				}
			}
		} elseif ( ! empty( $cert['subject']['CN'] ) ) {
			// Only use the CN when the certificate includes no subjectAltName extension.
			$certificate_hostnames[] = strtolower( $cert['subject']['CN'] );
		}

		// Exact hostname/IP matches.
		if ( in_array( strtolower( $host ), $certificate_hostnames, true ) ) {
			return true;
		}

		// IP's can't be wildcards, Stop processing.
		if ( 'ip' === $host_type ) {
			return false;
		}

		// Test to see if the domain is at least 2 deep for wildcard support.
		if ( substr_count( $host, '.' ) < 2 ) {
			return false;
		}

		// Wildcard subdomains certs (*.example.com) are valid for a.example.com but not a.b.example.com.
		$wildcard_host = preg_replace( '/^[^.]+\./', '*.', $host );

		return in_array( strtolower( $wildcard_host ), $certificate_hostnames, true );
	}

	/**
	 * Determines whether this class can be used for retrieving a URL.
	 *
	 *
	 * @param array $args Optional. Array of request arguments. Default empty array.
	 * @return bool False means this class can not be used, true means it can.
	 */
	public static function test( $args = array() ) {
		if ( ! function_exists( 'stream_socket_client' ) ) {
			return false;
		}

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl ) {
			if ( ! extension_loaded( 'openssl' ) ) {
				return false;
			}
			if ( ! function_exists( 'openssl_x509_parse' ) ) {
				return false;
			}
		}

		/**
		 * Filters whether streams can be used as a transport for retrieving a URL.
		 *
		 *
		 * @param bool  $use_class Whether the class can be used. Default true.
		 * @param array $args      Request arguments.
		 */
		return apply_filters( 'use_streams_transport', true, $args );
	}
}

/**
 * Deprecated HTTP Transport method which used fsockopen.
 *
 * This class is not used, and is included for backward compatibility only.
 * All code should make use of GC_Http directly through its API.
 *
 * @see GC_HTTP::request
 *
 *
 * @deprecated 3.7.0 Please use GC_HTTP::request() directly
 */
class GC_HTTP_Fsockopen extends GC_Http_Streams {
	// For backward compatibility for users who are using the class directly.
}
