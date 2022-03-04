<?php
/**
 * Core HTTP Request API
 *
 * Standardizes the HTTP requests for GeChiUI. Handles cookies, gzip encoding and decoding, chunk
 * decoding, if HTTP 1.1 and various other difficult HTTP protocol implementations.
 *
 * @package GeChiUI
 * @subpackage HTTP
 */

/**
 * Returns the initialized GC_Http Object
 *
 *
 * @access private
 *
 * @return GC_Http HTTP Transport object.
 */
function _gc_http_get_object() {
	static $http = null;

	if ( is_null( $http ) ) {
		$http = new GC_Http();
	}
	return $http;
}

function setlog($txt){
	$txt = $txt.'

	';
	$myfile = fopen("log.txt", "a");
	fwrite($myfile, $txt);
	fclose($myfile);
}

/**
 * Retrieve the raw response from a safe HTTP request.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_safe_remote_request( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->request( $url, $args );

}

/**
 * Retrieve the raw response from a safe HTTP request using the GET method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_safe_remote_get( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->get( $url, $args );
}

/**
 * Retrieve the raw response from a safe HTTP request using the POST method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_safe_remote_post( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->post( $url, $args );
}

/**
 * Retrieve the raw response from a safe HTTP request using the HEAD method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_safe_remote_head( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->head( $url, $args );
}

/**
 * Performs an HTTP request and returns its response.
 *
 * There are other API functions available which abstract away the HTTP method:
 *
 *  - Default 'GET'  for gc_remote_get()
 *  - Default 'POST' for gc_remote_post()
 *  - Default 'HEAD' for gc_remote_head()
 *
 *
 *
 * @see GC_Http::request() For information on default arguments.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error {
 *     The response array or a GC_Error on failure.
 *
 *     @type string[]                       $headers       Array of response headers keyed by their name.
 *     @type string                         $body          Response body.
 *     @type array                          $response      {
 *         Data about the HTTP response.
 *
 *         @type int|false    $code    HTTP response code.
 *         @type string|false $message HTTP response message.
 *     }
 *     @type GC_HTTP_Cookie[]               $cookies       Array of response cookies.
 *     @type GC_HTTP_Requests_Response|null $http_response Raw HTTP response object.
 * }
 */
function gc_remote_request( $url, $args = array() ) {
	$http = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->request( $url, $args );
}

/**
 * Performs an HTTP request using the GET method and returns its response.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_remote_get( $url, $args = array() ) {
	$http = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->get( $url, $args );
}

/**
 * Performs an HTTP request using the POST method and returns its response.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_remote_post( $url, $args = array() ) {
	$http = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->post( $url, $args );
}

/**
 * Performs an HTTP request using the HEAD method and returns its response.
 *
 *
 *
 * @see gc_remote_request() For more information on the response array format.
 * @see GC_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 * @return array|GC_Error The response or GC_Error on failure.
 */
function gc_remote_head( $url, $args = array() ) {
	$http = _gc_http_get_object();
	setlog($url);
	setlog(json_encode($args));
	return $http->head( $url, $args );
}

/**
 * Retrieve only the headers from the raw response.
 *
 *
 *
 *
 * @see \Requests_Utility_CaseInsensitiveDictionary
 *
 * @param array|GC_Error $response HTTP response.
 * @return array|\Requests_Utility_CaseInsensitiveDictionary The headers of the response. Empty array if incorrect parameter given.
 */
function gc_remote_retrieve_headers( $response ) {
	if ( is_gc_error( $response ) || ! isset( $response['headers'] ) ) {
		return array();
	}

	return $response['headers'];
}

/**
 * Retrieve a single header by name from the raw response.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @param string         $header   Header name to retrieve value from.
 * @return array|string The header(s) value(s). Array if multiple headers with the same name are retrieved.
 *                      Empty string if incorrect parameter given, or if the header doesn't exist.
 */
function gc_remote_retrieve_header( $response, $header ) {
	if ( is_gc_error( $response ) || ! isset( $response['headers'] ) ) {
		return '';
	}

	if ( isset( $response['headers'][ $header ] ) ) {
		return $response['headers'][ $header ];
	}

	return '';
}

/**
 * Retrieve only the response code from the raw response.
 *
 * Will return an empty string if incorrect parameter value is given.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @return int|string The response code as an integer. Empty string on incorrect parameter given.
 */
function gc_remote_retrieve_response_code( $response ) {
	if ( is_gc_error( $response ) || ! isset( $response['response'] ) || ! is_array( $response['response'] ) ) {
		return '';
	}

	return $response['response']['code'];
}

/**
 * Retrieve only the response message from the raw response.
 *
 * Will return an empty string if incorrect parameter value is given.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @return string The response message. Empty string on incorrect parameter given.
 */
function gc_remote_retrieve_response_message( $response ) {
	if ( is_gc_error( $response ) || ! isset( $response['response'] ) || ! is_array( $response['response'] ) ) {
		return '';
	}

	return $response['response']['message'];
}

/**
 * Retrieve only the body from the raw response.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function gc_remote_retrieve_body( $response ) {
	if ( is_gc_error( $response ) || ! isset( $response['body'] ) ) {
		return '';
	}

	return $response['body'];
}

/**
 * Retrieve only the cookies from the raw response.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @return GC_Http_Cookie[] An array of `GC_Http_Cookie` objects from the response. Empty array if there are none, or the response is a GC_Error.
 */
function gc_remote_retrieve_cookies( $response ) {
	if ( is_gc_error( $response ) || empty( $response['cookies'] ) ) {
		return array();
	}

	return $response['cookies'];
}

/**
 * Retrieve a single cookie by name from the raw response.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @param string         $name     The name of the cookie to retrieve.
 * @return GC_Http_Cookie|string The `GC_Http_Cookie` object. Empty string if the cookie isn't present in the response.
 */
function gc_remote_retrieve_cookie( $response, $name ) {
	$cookies = gc_remote_retrieve_cookies( $response );

	if ( empty( $cookies ) ) {
		return '';
	}

	foreach ( $cookies as $cookie ) {
		if ( $cookie->name === $name ) {
			return $cookie;
		}
	}

	return '';
}

/**
 * Retrieve a single cookie's value by name from the raw response.
 *
 *
 *
 * @param array|GC_Error $response HTTP response.
 * @param string         $name     The name of the cookie to retrieve.
 * @return string The value of the cookie. Empty string if the cookie isn't present in the response.
 */
function gc_remote_retrieve_cookie_value( $response, $name ) {
	$cookie = gc_remote_retrieve_cookie( $response, $name );

	if ( ! is_a( $cookie, 'GC_Http_Cookie' ) ) {
		return '';
	}

	return $cookie->value;
}

/**
 * Determines if there is an HTTP Transport that can process this request.
 *
 *
 *
 * @param array  $capabilities Array of capabilities to test or a gc_remote_request() $args array.
 * @param string $url          Optional. If given, will check if the URL requires SSL and adds
 *                             that requirement to the capabilities array.
 *
 * @return bool
 */
function gc_http_supports( $capabilities = array(), $url = null ) {
	$http = _gc_http_get_object();

	$capabilities = gc_parse_args( $capabilities );

	$count = count( $capabilities );

	// If we have a numeric $capabilities array, spoof a gc_remote_request() associative $args array.
	if ( $count && count( array_filter( array_keys( $capabilities ), 'is_numeric' ) ) == $count ) {
		$capabilities = array_combine( array_values( $capabilities ), array_fill( 0, $count, true ) );
	}

	if ( $url && ! isset( $capabilities['ssl'] ) ) {
		$scheme = parse_url( $url, PHP_URL_SCHEME );
		if ( 'https' === $scheme || 'ssl' === $scheme ) {
			$capabilities['ssl'] = true;
		}
	}

	return (bool) $http->_get_first_available_transport( $capabilities );
}

/**
 * Get the HTTP Origin of the current request.
 *
 *
 *
 * @return string URL of the origin. Empty string if no origin.
 */
function get_http_origin() {
	$origin = '';
	if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		$origin = $_SERVER['HTTP_ORIGIN'];
	}

	/**
	 * Change the origin of an HTTP request.
	 *
	 *
	 * @param string $origin The original origin for the request.
	 */
	return apply_filters( 'http_origin', $origin );
}

/**
 * Retrieve list of allowed HTTP origins.
 *
 *
 *
 * @return string[] Array of origin URLs.
 */
function get_allowed_http_origins() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );

	// @todo Preserve port?
	$allowed_origins = array_unique(
		array(
			'http://' . $admin_origin['host'],
			'https://' . $admin_origin['host'],
			'http://' . $home_origin['host'],
			'https://' . $home_origin['host'],
		)
	);

	/**
	 * Change the origin types allowed for HTTP requests.
	 *
	 *
	 * @param string[] $allowed_origins {
	 *     Array of default allowed HTTP origins.
	 *
	 *     @type string $0 Non-secure URL for admin origin.
	 *     @type string $1 Secure URL for admin origin.
	 *     @type string $2 Non-secure URL for home origin.
	 *     @type string $3 Secure URL for home origin.
	 * }
	 */
	return apply_filters( 'allowed_http_origins', $allowed_origins );
}

/**
 * Determines if the HTTP origin is an authorized one.
 *
 *
 *
 * @param null|string $origin Origin URL. If not provided, the value of get_http_origin() is used.
 * @return string Origin URL if allowed, empty string if not.
 */
function is_allowed_http_origin( $origin = null ) {
	$origin_arg = $origin;

	if ( null === $origin ) {
		$origin = get_http_origin();
	}

	if ( $origin && ! in_array( $origin, get_allowed_http_origins(), true ) ) {
		$origin = '';
	}

	/**
	 * Change the allowed HTTP origin result.
	 *
	 *
	 * @param string $origin     Origin URL if allowed, empty string if not.
	 * @param string $origin_arg Original origin string passed into is_allowed_http_origin function.
	 */
	return apply_filters( 'allowed_http_origin', $origin, $origin_arg );
}

/**
 * Send Access-Control-Allow-Origin and related headers if the current request
 * is from an allowed origin.
 *
 * If the request is an OPTIONS request, the script exits with either access
 * control headers sent, or a 403 response if the origin is not allowed. For
 * other request methods, you will receive a return value.
 *
 *
 *
 * @return string|false Returns the origin URL if headers are sent. Returns false
 *                      if headers are not sent.
 */
function send_origin_headers() {
	$origin = get_http_origin();

	if ( is_allowed_http_origin( $origin ) ) {
		header( 'Access-Control-Allow-Origin: ' . $origin );
		header( 'Access-Control-Allow-Credentials: true' );
		if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
			exit;
		}
		return $origin;
	}

	if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
		status_header( 403 );
		exit;
	}

	return false;
}

/**
 * Validate a URL for safe use in the HTTP API.
 *
 *
 *
 * @param string $url Request URL.
 * @return string|false URL or false on failure.
 */
function gc_http_validate_url( $url ) {
	if ( ! is_string( $url ) || '' === $url || is_numeric( $url ) ) {
		return false;
	}

	$original_url = $url;
	$url          = gc_kses_bad_protocol( $url, array( 'http', 'https' ) );
	if ( ! $url || strtolower( $url ) !== strtolower( $original_url ) ) {
		return false;
	}

	$parsed_url = parse_url( $url );
	if ( ! $parsed_url || empty( $parsed_url['host'] ) ) {
		return false;
	}

	if ( isset( $parsed_url['user'] ) || isset( $parsed_url['pass'] ) ) {
		return false;
	}

	if ( false !== strpbrk( $parsed_url['host'], ':#?[]' ) ) {
		return false;
	}

	$parsed_home = parse_url( get_option( 'home' ) );
	$same_host   = isset( $parsed_home['host'] ) && strtolower( $parsed_home['host'] ) === strtolower( $parsed_url['host'] );
	$host        = trim( $parsed_url['host'], '.' );

	if ( ! $same_host ) {
		if ( preg_match( '#^(([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)\.){3}([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)$#', $host ) ) {
			$ip = $host;
		} else {
			$ip = gethostbyname( $host );
			if ( $ip === $host ) { // Error condition for gethostbyname().
				return false;
			}
		}
		if ( $ip ) {
			$parts = array_map( 'intval', explode( '.', $ip ) );
			if ( 127 === $parts[0] || 10 === $parts[0] || 0 === $parts[0]
				|| ( 172 === $parts[0] && 16 <= $parts[1] && 31 >= $parts[1] )
				|| ( 192 === $parts[0] && 168 === $parts[1] )
			) {
				// If host appears local, reject unless specifically allowed.
				/**
				 * Check if HTTP request is external or not.
				 *
				 * Allows to change and allow external requests for the HTTP request.
				 *
			
				 *
				 * @param bool   $external Whether HTTP request is external or not.
				 * @param string $host     Host name of the requested URL.
				 * @param string $url      Requested URL.
				 */
				if ( ! apply_filters( 'http_request_host_is_external', false, $host, $url ) ) {
					return false;
				}
			}
		}
	}

	if ( empty( $parsed_url['port'] ) ) {
		return $url;
	}

	$port = $parsed_url['port'];

	/**
	 * Controls the list of ports considered safe in HTTP API.
	 *
	 * Allows to change and allow external requests for the HTTP request.
	 *
	 *
	 * @param array  $allowed_ports Array of integers for valid ports.
	 * @param string $host          Host name of the requested URL.
	 * @param string $url           Requested URL.
	 */
	$allowed_ports = apply_filters( 'http_allowed_safe_ports', array( 80, 443, 8080 ), $host, $url );
	if ( is_array( $allowed_ports ) && in_array( $port, $allowed_ports, true ) ) {
		return $url;
	}

	if ( $parsed_home && $same_host && isset( $parsed_home['port'] ) && $parsed_home['port'] === $port ) {
		return $url;
	}

	return false;
}

/**
 * Mark allowed redirect hosts safe for HTTP requests as well.
 *
 * Attached to the {@see 'http_request_host_is_external'} filter.
 *
 *
 *
 * @param bool   $is_external
 * @param string $host
 * @return bool
 */
function allowed_http_request_hosts( $is_external, $host ) {
	if ( ! $is_external && gc_validate_redirect( 'http://' . $host ) ) {
		$is_external = true;
	}
	return $is_external;
}

/**
 * Adds any domain in a multisite installation for safe HTTP requests to the
 * allowed list.
 *
 * Attached to the {@see 'http_request_host_is_external'} filter.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param bool   $is_external
 * @param string $host
 * @return bool
 */
function ms_allowed_http_request_hosts( $is_external, $host ) {
	global $gcdb;
	static $queried = array();
	if ( $is_external ) {
		return $is_external;
	}
	if ( get_network()->domain === $host ) {
		return true;
	}
	if ( isset( $queried[ $host ] ) ) {
		return $queried[ $host ];
	}
	$queried[ $host ] = (bool) $gcdb->get_var( $gcdb->prepare( "SELECT domain FROM $gcdb->blogs WHERE domain = %s LIMIT 1", $host ) );
	return $queried[ $host ];
}

/**
 * A wrapper for PHP's parse_url() function that handles consistency in the return
 * values across PHP versions.
 *
 * PHP 5.4.7 expanded parse_url()'s ability to handle non-absolute url's, including
 * schemeless and relative url's with :// in the path. This function works around
 * those limitations providing a standard output on PHP 5.2~5.4+.
 *
 * Secondly, across various PHP versions, schemeless URLs starting containing a ":"
 * in the query are being handled inconsistently. This function works around those
 * differences as well.
 *
 *
 *
 *
 * @link https://www.php.net/manual/en/function.parse-url.php
 *
 * @param string $url       The URL to parse.
 * @param int    $component The specific component to retrieve. Use one of the PHP
 *                          predefined constants to specify which one.
 *                          Defaults to -1 (= return all parts as an array).
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function gc_parse_url( $url, $component = -1 ) {
	$to_unset = array();
	$url      = (string) $url;

	if ( '//' === substr( $url, 0, 2 ) ) {
		$to_unset[] = 'scheme';
		$url        = 'placeholder:' . $url;
	} elseif ( '/' === substr( $url, 0, 1 ) ) {
		$to_unset[] = 'scheme';
		$to_unset[] = 'host';
		$url        = 'placeholder://placeholder' . $url;
	}

	$parts = parse_url( $url );

	if ( false === $parts ) {
		// Parsing failure.
		return $parts;
	}

	// Remove the placeholder values.
	foreach ( $to_unset as $key ) {
		unset( $parts[ $key ] );
	}

	return _get_component_from_parsed_url_array( $parts, $component );
}

/**
 * Retrieve a specific component from a parsed URL array.
 *
 * @internal
 *
 *
 * @access private
 *
 * @link https://www.php.net/manual/en/function.parse-url.php
 *
 * @param array|false $url_parts The parsed URL. Can be false if the URL failed to parse.
 * @param int         $component The specific component to retrieve. Use one of the PHP
 *                               predefined constants to specify which one.
 *                               Defaults to -1 (= return all parts as an array).
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function _get_component_from_parsed_url_array( $url_parts, $component = -1 ) {
	if ( -1 === $component ) {
		return $url_parts;
	}

	$key = _gc_translate_php_url_constant_to_key( $component );
	if ( false !== $key && is_array( $url_parts ) && isset( $url_parts[ $key ] ) ) {
		return $url_parts[ $key ];
	} else {
		return null;
	}
}

/**
 * Translate a PHP_URL_* constant to the named array keys PHP uses.
 *
 * @internal
 *
 *
 * @access private
 *
 * @link https://www.php.net/manual/en/url.constants.php
 *
 * @param int $constant PHP_URL_* constant.
 * @return string|false The named key or false.
 */
function _gc_translate_php_url_constant_to_key( $constant ) {
	$translation = array(
		PHP_URL_SCHEME   => 'scheme',
		PHP_URL_HOST     => 'host',
		PHP_URL_PORT     => 'port',
		PHP_URL_USER     => 'user',
		PHP_URL_PASS     => 'pass',
		PHP_URL_PATH     => 'path',
		PHP_URL_QUERY    => 'query',
		PHP_URL_FRAGMENT => 'fragment',
	);

	if ( isset( $translation[ $constant ] ) ) {
		return $translation[ $constant ];
	} else {
		return false;
	}
}
