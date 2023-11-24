<?php
/**
 * Autoloader for Requests for PHP.
 *
 * Include this file if you'd like to avoid having to create your own autoloader.
 *
 * @package Requests
 * @since   2.0.0
 *
 * @codeCoverageIgnore
 */

namespace GcOrg\Requests;

/*
 * Ensure the autoloader is only declared once.
 * This safeguard is in place as this is the typical entry point for this library
 * and this file being required unconditionally could easily cause
 * fatal "Class already declared" errors.
 */
if (class_exists('GcOrg\Requests\Autoload') === false) {

	/**
	 * Autoloader for Requests for PHP.
	 *
	 * This autoloader supports the PSR-4 based Requests 2.0.0 classes in a case-sensitive manner
	 * as the most common server OS-es are case-sensitive and the file names are in mixed case.
	 *
	 * For the PSR-0 Requests 1.x BC-layer, requested classes will be treated case-insensitively.
	 *
	 * @package Requests
	 */
	final class Autoload {

		/**
		 * List of the old PSR-0 class names in lowercase as keys with their PSR-4 case-sensitive name as a value.
		 *
		 * @var array
		 */
		private static $deprecated_classes = [
			// Interfaces.
			'requests_auth'                              => '\GcOrg\Requests\Auth',
			'requests_hooker'                            => '\GcOrg\Requests\HookManager',
			'requests_proxy'                             => '\GcOrg\Requests\Proxy',
			'requests_transport'                         => '\GcOrg\Requests\Transport',

			// Classes.
			'requests_cookie'                            => '\GcOrg\Requests\Cookie',
			'requests_exception'                         => '\GcOrg\Requests\Exception',
			'requests_hooks'                             => '\GcOrg\Requests\Hooks',
			'requests_idnaencoder'                       => '\GcOrg\Requests\IdnaEncoder',
			'requests_ipv6'                              => '\GcOrg\Requests\Ipv6',
			'requests_iri'                               => '\GcOrg\Requests\Iri',
			'requests_response'                          => '\GcOrg\Requests\Response',
			'requests_session'                           => '\GcOrg\Requests\Session',
			'requests_ssl'                               => '\GcOrg\Requests\Ssl',
			'requests_auth_basic'                        => '\GcOrg\Requests\Auth\Basic',
			'requests_cookie_jar'                        => '\GcOrg\Requests\Cookie\Jar',
			'requests_proxy_http'                        => '\GcOrg\Requests\Proxy\Http',
			'requests_response_headers'                  => '\GcOrg\Requests\Response\Headers',
			'requests_transport_curl'                    => '\GcOrg\Requests\Transport\Curl',
			'requests_transport_fsockopen'               => '\GcOrg\Requests\Transport\Fsockopen',
			'requests_utility_caseinsensitivedictionary' => '\GcOrg\Requests\Utility\CaseInsensitiveDictionary',
			'requests_utility_filterediterator'          => '\GcOrg\Requests\Utility\FilteredIterator',
			'requests_exception_http'                    => '\GcOrg\Requests\Exception\Http',
			'requests_exception_transport'               => '\GcOrg\Requests\Exception\Transport',
			'requests_exception_transport_curl'          => '\GcOrg\Requests\Exception\Transport\Curl',
			'requests_exception_http_304'                => '\GcOrg\Requests\Exception\Http\Status304',
			'requests_exception_http_305'                => '\GcOrg\Requests\Exception\Http\Status305',
			'requests_exception_http_306'                => '\GcOrg\Requests\Exception\Http\Status306',
			'requests_exception_http_400'                => '\GcOrg\Requests\Exception\Http\Status400',
			'requests_exception_http_401'                => '\GcOrg\Requests\Exception\Http\Status401',
			'requests_exception_http_402'                => '\GcOrg\Requests\Exception\Http\Status402',
			'requests_exception_http_403'                => '\GcOrg\Requests\Exception\Http\Status403',
			'requests_exception_http_404'                => '\GcOrg\Requests\Exception\Http\Status404',
			'requests_exception_http_405'                => '\GcOrg\Requests\Exception\Http\Status405',
			'requests_exception_http_406'                => '\GcOrg\Requests\Exception\Http\Status406',
			'requests_exception_http_407'                => '\GcOrg\Requests\Exception\Http\Status407',
			'requests_exception_http_408'                => '\GcOrg\Requests\Exception\Http\Status408',
			'requests_exception_http_409'                => '\GcOrg\Requests\Exception\Http\Status409',
			'requests_exception_http_410'                => '\GcOrg\Requests\Exception\Http\Status410',
			'requests_exception_http_411'                => '\GcOrg\Requests\Exception\Http\Status411',
			'requests_exception_http_412'                => '\GcOrg\Requests\Exception\Http\Status412',
			'requests_exception_http_413'                => '\GcOrg\Requests\Exception\Http\Status413',
			'requests_exception_http_414'                => '\GcOrg\Requests\Exception\Http\Status414',
			'requests_exception_http_415'                => '\GcOrg\Requests\Exception\Http\Status415',
			'requests_exception_http_416'                => '\GcOrg\Requests\Exception\Http\Status416',
			'requests_exception_http_417'                => '\GcOrg\Requests\Exception\Http\Status417',
			'requests_exception_http_418'                => '\GcOrg\Requests\Exception\Http\Status418',
			'requests_exception_http_428'                => '\GcOrg\Requests\Exception\Http\Status428',
			'requests_exception_http_429'                => '\GcOrg\Requests\Exception\Http\Status429',
			'requests_exception_http_431'                => '\GcOrg\Requests\Exception\Http\Status431',
			'requests_exception_http_500'                => '\GcOrg\Requests\Exception\Http\Status500',
			'requests_exception_http_501'                => '\GcOrg\Requests\Exception\Http\Status501',
			'requests_exception_http_502'                => '\GcOrg\Requests\Exception\Http\Status502',
			'requests_exception_http_503'                => '\GcOrg\Requests\Exception\Http\Status503',
			'requests_exception_http_504'                => '\GcOrg\Requests\Exception\Http\Status504',
			'requests_exception_http_505'                => '\GcOrg\Requests\Exception\Http\Status505',
			'requests_exception_http_511'                => '\GcOrg\Requests\Exception\Http\Status511',
			'requests_exception_http_unknown'            => '\GcOrg\Requests\Exception\Http\StatusUnknown',
		];

		/**
		 * Register the autoloader.
		 *
		 * Note: the autoloader is *prepended* in the autoload queue.
		 * This is done to ensure that the Requests 2.0 autoloader takes precedence
		 * over a potentially (dependency-registered) Requests 1.x autoloader.
		 *
		 * @internal This method contains a safeguard against the autoloader being
		 * registered multiple times. This safeguard uses a global constant to
		 * (hopefully/in most cases) still function correctly, even if the
		 * class would be renamed.
		 *
		 * @return void
		 */
		public static function register() {
			if (defined('REQUESTS_AUTOLOAD_REGISTERED') === false) {
				spl_autoload_register([self::class, 'load'], true);
				define('REQUESTS_AUTOLOAD_REGISTERED', true);
			}
		}

		/**
		 * Autoloader.
		 *
		 * @param string $class_name Name of the class name to load.
		 *
		 * @return bool Whether a class was loaded or not.
		 */
		public static function load($class_name) {
			// Check that the class starts with "Requests" (PSR-0) or "GcOrg\Requests" (PSR-4).
			$psr_4_prefix_pos = strpos($class_name, 'GcOrg\\Requests\\');

			if (stripos($class_name, 'Requests') !== 0 && $psr_4_prefix_pos !== 0) {
				return false;
			}

			$class_lower = strtolower($class_name);

			if ($class_lower === 'requests') {
				// Reference to the original PSR-0 Requests class.
				$file = dirname(__DIR__) . '/library/Requests.php';
			} elseif ($psr_4_prefix_pos === 0) {
				// PSR-4 classname.
				$file = __DIR__ . '/' . strtr(substr($class_name, 15), '\\', '/') . '.php';
			}

			if (isset($file) && file_exists($file)) {
				include $file;
				return true;
			}

			/*
			 * Okay, so the class starts with "Requests", but we couldn't find the file.
			 * If this is one of the deprecated/renamed PSR-0 classes being requested,
			 * let's alias it to the new name and throw a deprecation notice.
			 */
			if (isset(self::$deprecated_classes[$class_lower])) {
				/*
				 * Integrators who cannot yet upgrade to the PSR-4 class names can silence deprecations
				 * by defining a `REQUESTS_SILENCE_PSR0_DEPRECATIONS` constant and setting it to `true`.
				 * The constant needs to be defined before the first deprecated class is requested
				 * via this autoloader.
				 */
				if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS') || REQUESTS_SILENCE_PSR0_DEPRECATIONS !== true) {
					// phpcs:ignore GeChiUI.PHP.DevelopmentFunctions.error_log_trigger_error
					trigger_error(
						'The PSR-0 `Requests_...` class names in the Requests library are deprecated.'
						. ' Switch to the PSR-4 `GcOrg\Requests\...` class names at your earliest convenience.',
						E_USER_DEPRECATED
					);

					// Prevent the deprecation notice from being thrown twice.
					if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS')) {
						define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);
					}
				}

				// Create an alias and let the autoloader recursively kick in to load the PSR-4 class.
				return class_alias(self::$deprecated_classes[$class_lower], $class_name, true);
			}

			return false;
		}
	}
}
