<?php
/**
 * Exception for 511 Network Authentication Required responses
 *
 * @link https://tools.ietf.org/html/rfc6585
 *
 * @package Requests\Exceptions
 */

namespace GcOrg\Requests\Exception\Http;

use GcOrg\Requests\Exception\Http;

/**
 * Exception for 511 Network Authentication Required responses
 *
 * @link https://tools.ietf.org/html/rfc6585
 *
 * @package Requests\Exceptions
 */
final class Status511 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 511;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Network Authentication Required';
}
